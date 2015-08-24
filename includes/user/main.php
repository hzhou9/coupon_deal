<?php

namespace user;

/** */

class main {

/*

USER LOGIN

*/

public static function is_logged() {

global $db;

if( !isset( $_COOKIE['user-session'] ) ) {

  return false;

} else {

  $stmt = $db->stmt_init();

  $stmt->prepare( "SELECT COUNT(*), user FROM " . DB_TABLE_PREFIX . "sessions WHERE session = ?" );
  $stmt->bind_param( "s", $_COOKIE['user-session'] );
  $stmt->bind_result( $count, $id );
  $stmt->execute();
  $stmt->fetch();

if( $count > 0 ) {

  $stmt->prepare( "SELECT name, email, avatar, points, credits, ipaddr, privileges, erole, subscriber, last_login, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE user = u.id), visits, valid, ban, date FROM " . DB_TABLE_PREFIX . "users u WHERE id = ?" );
  $stmt->bind_param( "i", $id );
  $stmt->bind_result( $name, $email, $avatar, $points, $credits, $ip, $privileges, $erole, $subscriber, $last_login, $stores, $visits, $valid, $ban, $date );
  $stmt->execute();
  $stmt->fetch();

  // update action
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET points = IF(last_action < DATE(NOW()), points + ?, points), last_action = NOW() WHERE id = ?" );
  $daily_points = \query\main::get_option( 'u_points_davisit' );
  $stmt->bind_param( "ii", $daily_points, $id );
  $stmt->execute();

  $stmt->close();

  return (object) array( 'ID' => $id, 'Name' => $name, 'Email' => $email, 'Avatar' => $avatar, 'Points' => $points, 'Credits' => $credits, 'IP' => $ip, 'Privileges' => $privileges, 'Erole' => @unserialize( $erole ), 'Last_login' => $last_login, 'Stores' => $stores, 'Visits' => $visits, 'Date' => $date, 'is_subscribed' => $subscriber, 'is_confirmed' => $valid, 'is_banned' => (strtotime( $ban ) > time() ? true : false), 'is_subadmin' => ( $privileges >= 1 ? true : false ), 'is_admin' => ( $privileges > 1 ? true : false ) );

} else {

  $stmt->close();

  return false;

}

}

}

/*

BANNED

*/

public static function banned( $type = '', $IP = '' ) {

global $db;

switch( $type ) {

  case 'registration':
  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned WHERE ipaddr = ? AND registration = 1" );
  $userip = empty( $IP ) ? \site\utils::getIP() : $IP;
  $stmt->bind_param( "s", $userip );
  $stmt->bind_result( $count );
  $stmt->execute();
  $stmt->fetch();
  $stmt->close();
  if( $count > 0 )return true;
  return false;
  break;

  case 'login':
  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned WHERE ipaddr = ? AND login = 1" );
  $userip = empty( $IP ) ? \site\utils::getIP() : $IP;
  $stmt->bind_param( "s", $userip );
  $stmt->bind_result( $count );
  $stmt->execute();
  $stmt->fetch();
  $stmt->close();
  if( $count > 0 )return true;
  return false;
  break;

  default:
  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*), redirect_to FROM " . DB_TABLE_PREFIX . "banned WHERE ipaddr = ? AND site = 1 AND ( expiration = 0 OR ( expiration = 1 AND expiration_date > NOW() ) )" );
  $userip = empty( $IP ) ? \site\utils::getIP() : $IP;
  $stmt->bind_param( "s", $userip );
  $stmt->bind_result( $count, $new_location );
  $stmt->execute();
  $stmt->fetch();
  $stmt->close();
  if( $count > 0 )return $new_location;
  return false;
  break;

}

  return false;

}

/*

USER LOGOUT

*/

public static function logout() {

global $db;

if( !isset( $_COOKIE['user-session'] ) ) {

  return false;

} else {

  $stmt = $db->stmt_init();

  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE session = ?" );
  $stmt->bind_param( "s", $_COOKIE['user-session'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

}

/*

USER LOGIN

*/

public static function login( $post, $privileges = 0 ) {

global $db, $LANG;

$session = '';

if( \user\main::banned( 'login' ) ) {
  throw new \Exception( $LANG['msg_banned'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*), id, password, ban FROM " . DB_TABLE_PREFIX . "users WHERE email = ? AND privileges >= ?" );
  $stmt->bind_param( "si", $post['username'], $privileges );
  $stmt->bind_result( $count, $id, $password, $ban );
  $stmt->execute();
  $stmt->fetch();

  if( (int)$count === 0 ) {

  // user does not even exist

  throw new \Exception( $LANG['login_invalid'] );

  } else if( strtotime( $ban ) > time() ) {

  // banned user

  throw new \Exception( $LANG['login_banaccount'] );

  } else if( (string)$password !== (string) md5( $post['password'] ) ) {

  // wrong password

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET fail_attempts = IF(fail_attempts >= " . BAN_AFTER_ATTEMPTS . ", 1, fail_attempts + 1), ban = IF(fail_attempts >= " . BAN_AFTER_ATTEMPTS . ", DATE_ADD(NOW(), INTERVAL " . BAN_AFTER_FAIL . " MINUTE), ban) WHERE email = ?" );
  $stmt->bind_param( "s", $post['username'] );
  $stmt->execute();
  $stmt->close();

  throw new \Exception( $LANG['login_invalid'] );

  } else {

  $session = md5( \site\utils::str_random(15) );

  // delete old sessions
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE user = ?" );
  $stmt->bind_param( "i", $id );
  $stmt->execute();

  // insert new session
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "sessions SET user = ?, session = ?, expiration = DATE_ADD(NOW(), INTERVAL " . ( isset( $post['keep_logged'] ) ? DEF_USER_SESSION_KL : DEF_USER_SESSION ) . " MINUTE), date = NOW()" );
  $stmt->bind_param( "is", $id, $session );

  if( !$stmt->execute() ) {

  $stmt->close();
  throw new \Exception( $LANG['msg_error'] );

  } else {

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET ipaddr = ?, last_login = NOW(), visits = visits + 1, fail_attempts = 0 WHERE id = ?" );

  $userip = \site\utils::getIP();

  $stmt->bind_param( "si", $userip, $id );
  $stmt->execute();
  $stmt->close();

  }

  }

}

  return $session;

}

/*

USER REGISTER

*/

public static function register( $post ) {

global $db, $LANG;

$session = '';

$max_acc = (int) \query\main::get_option( 'accounts_per_ip' );

if( $max_acc !== 0 && (int) \query\main::users( array( 'ip' => \site\utils::getIP() ) ) >= $max_acc ) {
  throw new \Exception( $LANG['msg_error'] ); // administrator don't allow that manny accounts
} else if( \user\main::banned( 'registration' ) ) {
  throw new \Exception( $LANG['msg_banned'] );
} else if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
  throw new \Exception( $LANG['register_usevalide'] );
} else if( !isset( $post['username'] ) ) {
  throw new \Exception( $LANG['register_complete_name'] );
} else if( !preg_match( '/(^[a-zA-Z0-9 ]{3,25}$)/', $post['username'] ) ) {
  throw new \Exception( $LANG['register_invalid_name'] );
} else if( !isset( $post['password'] ) || !isset( $post['password2'] ) ) {
  throw new \Exception( $LANG['register_paswdreq'] );
} else if( !preg_match( '/(^[a-zA-Z0-9-_]{5,40}$)/', $post['password'] ) ) {
  throw new \Exception( $LANG['register_invalid_paswd'] );
} else if( $post['password'] != $post['password2'] ) {
  throw new \Exception( $LANG['register_passwdnm'] );
} else {

  if( !$session = \user\main::insert_user( $post ) ) {
  throw new \Exception( $LANG['register_accexists'] );
  }

  return $session;

  }

}

/*

INSERT USER

*/

public static function insert_user( $infos = array(), $autologin = false, $autovalid = false ) {

/*

** ATTENTION

If $autologin is set to true, login don't require password !

*/

global $db, $LANG;

  $stmt = $db->stmt_init();

  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "users (name, email, password, points, ipaddr, last_action, valid, refid, date) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, NOW())" );

  $passwd = isset( $infos['password'] ) ? md5( $infos['password'] ) : md5( \site\utils::str_random(15) );
  $points = (int) \query\main::get_option( 'u_def_points' );
  $IPaddr = \site\utils::getIP();
  $valid = (int) ( $autovalid ? 1 : (boolean) \query\main::get_option( 'u_confirm_req' ) );
  $refid = isset( $_COOKIE['referrer'] ) ? (int) $_COOKIE['referrer'] : 0;

  $stmt->bind_param( "sssssii", $infos['username'], $infos['email'], $passwd, $points, $IPaddr, $valid, $refid );
  $execute = $stmt->execute();

  if( !$execute && !$autologin ) {

    return false;

  } else {

  $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "users WHERE email = ?" );
  $stmt->bind_param( "s", $infos['email'] );
  $stmt->execute();
  $stmt->bind_result( $id );
  $stmt->fetch();

  $session = md5( \site\utils::str_random(15) );

  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "sessions SET user = ?, session = ?, expiration = DATE_ADD(NOW(), INTERVAL " . DEF_USER_SESSION . " MINUTE), date = NOW()" );
  $stmt->bind_param( "is", $id, $session );
  $stmt->execute();

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET last_login = NOW(), visits = 1 WHERE id = ?" );
  $stmt->bind_param( "i", $id );
  $stmt->execute();

  if( !$valid ) {

  $cofirm_session = md5( \site\utils::str_random(15) );
  if( \user\mail_sessions::insert( 'confirmation', array( 'user' => $id, 'session' => $cofirm_session ) ) )
    \site\mail::send( $infos['email'], $LANG['email_acc_title'] . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'account_confirmation' ), array( 'hello_name' => sprintf( $LANG['email_text_hello'], $infos['username'] ), 'confirmation_main_text' => $LANG['email_acc_maintext'], 'confirmation_button' => $LANG['email_acc_button'], 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'user' => $id, 'token' => $cofirm_session ) ) ) );

  } else if( $valid && $refid !== 0 ) {

  // add points to user who referred the new user
  \user\update::add_points( $refid, \query\main::get_option( 'u_points_refer' ) );

  }

  $stmt->close();

  return $session;

  }

}

/*

USER RECOVERY PASSWORD

*/

public static function recovery_password( $post, $path = '', $privileges = 0 ) {

global $db, $LANG;

if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
  throw new \Exception( $LANG['register_usevalide'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "users WHERE email = ? AND privileges >= ?" );
  $stmt->bind_param( "si", $post['email'], $privileges );
  $stmt->bind_result( $user );
  $execute = $stmt->execute();
  $stmt->fetch();
  $stmt->close();

  if( !$execute || empty( $user ) ) {
  throw new \Exception( $LANG['fp_unkwacc'] );
  } else {

  $session = md5( \site\utils::str_random(15) );

  if( \user\mail_sessions::insert( 'password_recovery', array( 'user' => $user, 'session' => $session ) ) ) {

    // send email
    if( \site\mail::send( $post['email'], $LANG['email_reset_title'] . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'password_reset', 'path' => $path ), array( 'reset_main_text' => $LANG['email_reset_maintext'], 'reset_button' => $LANG['email_reset_button'], 'link' => \site\utils::update_uri( '', array( 'uid' => $user, 'session' => $session ) ) ) ) )

    return true;

  }

  throw new \Exception( $LANG['msg_error'] );

  }

  }

}

/*

RESET PASSWORD

*/

public static function reset_password( $id, $post ) {

global $db, $LANG;

if( !isset( $post['password1'] ) || !preg_match( '/(^[a-zA-Z0-9-_]{5,40}$)/', $post['password1'] ) ) {
  throw new \Exception( $LANG['reset_pwd_wrong_np'] );
} else if( !isset( $post['password1'] ) || !isset( $post['password2'] ) || $post['password1'] != $post['password2'] ) {
  throw new \Exception( $LANG['reset_pwd_pwddm'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET password = ? WHERE id = ?" );
  $stmt->bind_param( "si", md5( $post['password1'] ), $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( !$execute ) throw new \Exception( $LANG['msg_error'] );

  }

}

/*

CHANGE PASSWORD

*/

public static function change_password( $id, $post ) {

global $db, $LANG;

if( !isset( $post['new'] ) || !preg_match( '/(^[a-zA-Z0-9-_]{5,40}$)/', $post['new'] ) ) {
  throw new \Exception( $LANG['change_pwd_wrong_np'] );
} else if( !isset( $post['new'] ) || !isset( $post['new2'] ) || $post['new'] != $post['new2'] ) {
  throw new \Exception( $LANG['change_pwd_pwddm'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT password FROM " . DB_TABLE_PREFIX . "users WHERE id = ?" );
  $stmt->bind_param( "i", $id );
  $stmt->bind_result( $password );
  $stmt->execute();
  $stmt->fetch();

  if( md5( $post['old'] ) == $password ) {

  $stmt->prepare( "UPDATE users SET password = ? WHERE id = ?" );

  $new = md5( $post['new'] );

  $stmt->bind_param( "si", $new, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  return true;

  } else {

  throw new \Exception( $LANG['msg_error'] );

  }

  } else {

  $stmt->close();

  throw new \Exception( $LANG['change_pwd_wrongpwd'] );

  }

  }

}

/*

EDIT PROFILE

*/

public static function edit_profile( $id, $post ) {

global $db, $LANG;

if( !isset( $post['username'] ) ) {
  throw new \Exception( $LANG['profile_complete_name'] );
} else if( !preg_match( '/(^[a-zA-Z0-9 ]{3,25}$)/', $post['username'] ) ) {
  throw new \Exception( $LANG['profile_invalid_name'] );
} else {

  $avatar = \site\images::upload( $_FILES['edit_profile_form_avatar'], 'avatar_', array( 'path' => '', 'max_size' => 400, 'max_width' => 300, 'max_height' => 300, 'current' => $GLOBALS['me']->Avatar ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET name = ?, avatar = ?, subscriber = ? WHERE id = ?" );

  $subscriber = ( isset( $post['subscriber'] ) ? 1 : 0 );

  $stmt->bind_param( "ssii", $post['username'], $avatar, $subscriber, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  return (object) array( 'avatar' => $avatar );

  } else {

  throw new \Exception( $LANG['msg_error'] );

  }

}

}

/*

WRITE REVIEW

*/

public static function write_review( $id, $user, $post ) {

global $db, $LANG;

if( !( $allow = (int) \query\main::get_option( 'allow_reviews' ) ) || !isset( $post['stars'] ) || !in_array( $post['stars'], array( 1,2,3,4,5 ) )  ) {
  throw new \Exception( $LANG['msg_error'] ); // this error can appear only when the user try to modify post data OR administrator don't allow new reviews
} else if( $allow === 2 && !$GLOBALS['me']->is_confirmed ) {
  throw new \Exception( $LANG['review_write_notv'] );
} else if( !isset( $post['text'] ) || trim( $post['text'] ) == '' ) {
  throw new \Exception( $LANG['review_write_text'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "reviews (user, store, text, stars, valid, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $valid = (boolean) \query\main::get_option( 'review_validate' );

  $stmt->bind_param( "iisiii", $user, $id, $post['text'], $post['stars'], $valid, $user );
  $execute = $stmt->execute();

  if( $execute ) {

  if( ( $ppr = \query\main::get_option( 'u_points_review' ) ) > 0 ) {

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET points = points + ? WHERE id = ?" );
  $stmt->bind_param( "ii", $ppr, $user );
  $stmt->execute();

  }

  $stmt->close();

  return true;

  } else {

  throw new \Exception( $LANG['msg_error'] );

  }

}

}
    
    /*
     
     ADD PRODUCT TO FAVORITES
     
     */
    
    public static function favorite_product( $id, $product, $type = 'add' ) {
        
        global $db;
        
        if( $type == 'add' ) {
            
            if( !\user\main::check_favorite_product( $id, $product['id'] ) ) {
                
                $stmt = $db->stmt_init();
                $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "favorite_product (user, product, start, expiration, date) VALUES (?, ?, ?, ?, NOW())" );
                $stmt->bind_param( "iiss", $id, $product['id'], $product['start'], $product['expiration'] );
                $execute = $stmt->execute();
                $stmt->close();
                
                if( $execute ) {
                    
                    return true;
                    
                } else {
                    
                    return false;
                    
                }
                
            }
            
        } else if( $type == 'remove' ) {
            
            $stmt = $db->stmt_init();
            $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "favorite_product WHERE user = ? AND product = ?" );
            $stmt->bind_param( "ii", $id, $product['id'] );
            $execute = $stmt->execute();
            $stmt->close();
            
            if( $execute ) {
                
                return true;
                
            } else {
                
                return false;
                
            }
            
        }
        
        return false;
        
    }
    
    /*
     
     CHECK IF A PRODUCT IT'S FAVORITE
     
     */
    
    public static function check_favorite_product( $id, $productid ) {
        
        global $db;
        
        $stmt = $db->stmt_init();
        $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "favorite_product WHERE user = ? and product = ?" );
        $stmt->bind_param( "ii", $id, $productid );
        $stmt->bind_result( $count );
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
        
        if( $count > 0 ) {
            return true;
        } else {
            return false;
        }
        
    }
    

/*

ADD STORE TO FAVORITES

*/

public static function favorite( $id, $store, $type = 'add' ) {

global $db;

if( $type == 'add' ) {

if( !\user\main::check_favorite( $id, $store ) ) {

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "favorite (user, store, date) VALUES (?, ?, NOW())" );
  $stmt->bind_param( "ii", $id, $store );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  return true;

  } else {

  return false;

  }

}

} else if( $type == 'remove' ) {

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "favorite WHERE user = ? AND store = ?" );
  $stmt->bind_param( "ii", $id, $store );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  return true;

  } else {

  return false;

  }

}

  return false;

}

/*

CHECK IF A STORE IT'S FAVORITE

*/

public static function check_favorite( $id, $store ) {

global $db;

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "favorite WHERE user = ? and store = ?" );
  $stmt->bind_param( "ii", $id, $store );
  $stmt->bind_result( $count );
  $stmt->execute();
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  } else {
    return false;
  }

}

/*

SUGGEST STORE

*/

public static function suggest_store( $id, $post, $intent ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['intent'] ) || !in_array( $post['intent'], array_keys( $intent ) ) ) {
  throw new \Exception( $LANG['msg_error'] ); // this error can appear only when user try to modify post data
} else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
  throw new \Exception( $LANG['suggestion_pwn'] );
} else if( !isset( $post['url'] ) || !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,8})$/', $post['url'] ) ) {
  throw new \Exception( $LANG['suggestion_wrong_url'] );
} else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
  throw new \Exception( $LANG['suggestion_shdesc'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "suggestions (user, type, name, url, description, message, date) VALUES (?, ?, ?, ?, ?, ?, NOW())" );

  $stmt->bind_param( "iissss", $id, $post['intent'], $post['name'], $post['url'], $post['description'], $post['message'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( !$execute ) throw new \Exception( $LANG['msg_error'] );

}

}

/*

SUBMIT COUPON

*/

public static function submit_coupon( $id, $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['store'] ) || !\query\main::have_store( $post['store'], $id ) ) {
  throw new \Exception( $LANG['msg_error'] );  // this error can appear only when user try to modify post data
} else if( !isset( $post['name'] ) || trim( $post['name'] ) == ''  ) {
  throw new \Exception( $LANG['submit_cou_writename'] );
} else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) {
  throw new \Exception( $LANG['submit_cou_writeurl'] );
} else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
  throw new \Exception( $LANG['submit_cou_writedesc'] );
} else if( !isset( $post['end'] ) || !isset( $post['end_hour'] ) || strtotime( $post['end'] ) < strtotime( 'today' ) ) {
  throw new \Exception( $LANG['submit_cou_wrong_ed'] );
} else {

  $end = $post['end'] . ', ' . $post['end_hour'];

  $prices = prices( 'object' );

  // cost for this coupon

  $cost = (int) $prices->coupon * ceil( ( $days = max( ceil( ( strtotime( $end ) - strtotime( 'now' ) ) / 86400 ), 1 ) ) / (int) $prices->coupon_max_days );

  if( $GLOBALS['me']->Credits < $cost ) {
    throw new \Exception( sprintf( $LANG['msg_notenoughpoints'], $cost, $GLOBALS['me']->Credits ) );
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (user, store, title, link, description, tags, code, visible, start, expiration, lastupdate_by, lastupdate, paid_until, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), FROM_UNIXTIME(?), NOW())" );

  $start = $post['start'] . ', ' . $post['start_hour'];

  // save cost until
  $paid_until = ( $cost > 0 ? strtotime( "+$days days 00:00" ) : '' );
  $visible = (boolean) \query\main::get_option( 'coupon_validate' );

  $stmt->bind_param( "iisssssissis", $id, $post['store'], $post['name'], $post['url'], $post['description'], $post['tags'], $post['code'], $visible, $start, $end, $id, $paid_until );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  // deduct credits

  \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

  return true;

  }

  throw new \Exception( $LANG['msg_error'] );

}

}

/*

EDIT COUPON

*/

public static function edit_coupon( $id, $user, $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['store'] ) || ! \query\main::have_store( $post['store'], $user ) ) {
  throw new \Exception( $LANG['msg_error'] );  // this error can appear only when user try to modify post data
} else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
  throw new \Exception( $LANG['edit_cou_writename'] );
} else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) {
  throw new \Exception( $LANG['edit_cou_writeurl'] );
} else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
  throw new \Exception( $LANG['edit_cou_writedesc'] );
} else {

  $end = $post['end'] . ', ' . $post['end_hour'];

  $info = \query\main::item_infos( $id );

  if( ( $end_unix = strtotime( $post['end'] ) ) > ( $paid_until = strtotime( $info->paid_until ) ) ) {

  $prices = prices( 'object' );

  $now_unix = strtotime( 'today 00:00' );

  // cost for this coupon

  $cost = (int) $prices->coupon * ceil( max( ceil( ( $end_unix - ( $paid_until > $now_unix ? $paid_until : $now_unix ) ) / 86400 ), 1 ) / (int) $prices->coupon_max_days );

  // save cost until

  $paid_until = $end_unix;

  } else {

  // cost for this coupon

  $cost = 0;

  }

  if( $GLOBALS['me']->Credits < $cost ) {
    throw new \Exception( sprintf( $LANG['msg_notenoughpoints'], $cost, $GLOBALS['me']->Credits ) );
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET store = ?, title = ?, link = ?, description = ?, tags = ?, code = ?, start = ?, expiration = ?, lastupdate_by = ?, lastupdate = NOW(), paid_until = FROM_UNIXTIME(?) WHERE id = ?" );

  $start = $post['start'] . ', ' . $post['start_hour'];
  if( $cost <= 0 ) {
    $paid_until =  strtotime( $info->paid_until );
  }

  $stmt->bind_param( "isssssssisi", $post['store'], $post['name'], $post['url'], $post['description'], $post['tags'], $post['code'], $start, $end, $user, $paid_until, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  // deduct credits

  \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

  return true;

  }

  throw new \Exception( $LANG['msg_error'] );

}

}

/*

SUBMIT PRODUCT

*/

public static function submit_product( $id, $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['store'] ) || !\query\main::have_store( $post['store'], $id ) ) {
  throw new \Exception( $LANG['msg_error'] );  // this error can appear only when user try to modify post data
} else if( !isset( $post['name'] ) || trim( $post['name'] ) == ''  ) {
  throw new \Exception( $LANG['submit_prod_writename'] );
} else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) {
  throw new \Exception( $LANG['submit_prod_writeurl'] );
} else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
  throw new \Exception( $LANG['submit_prod_writedesc'] );
} else if( !isset( $post['end'] ) || !isset( $post['end_hour'] ) || strtotime( $post['end'] ) < strtotime( 'today' ) ) {
  throw new \Exception( $LANG['submit_prod_wrong_ed'] );
} else {

  $end = $post['end'] . ', ' . $post['end_hour'];

  $prices = prices( 'object' );

  // cost for this product

  $cost = (int) $prices->product * ceil( ( $days = max( ceil( ( strtotime( $end ) - strtotime( 'now' ) ) / 86400 ), 1 ) ) / (int) $prices->product_max_days );

  if( $GLOBALS['me']->Credits < $cost ) {
    throw new \Exception( sprintf( $LANG['msg_notenoughpoints'], $cost, $GLOBALS['me']->Credits ) );
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "products (user, store, title, link, description, tags, image, price, old_price, currency, visible, start, expiration, lastupdate_by, lastupdate, paid_until, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), FROM_UNIXTIME(?), NOW())" );

  $start = $post['start'] . ', ' . $post['start_hour'];

  // save cost until
  $paid_until = ( $cost > 0 ? strtotime( "+$days days 00:00" ) : '' );
  $image = \site\images::upload( $_FILES['submit_product_form_image'], 'product_', array( 'path' => '', 'max_size' => 1024, 'max_width' => 800, 'max_height' => 800, 'current' => '' ) );
  $visible = (boolean) \query\main::get_option( 'product_validate' );

  $stmt->bind_param( "iisssssddsissis", $id, $post['store'], $post['name'], $post['url'], $post['description'], $post['tags'], $image, $post['price'], $post['old_price'], $post['currency'], $visible, $start, $end, $id, $paid_until );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  // deduct credits

  \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

  return true;

  }

  throw new \Exception( $LANG['msg_error'] );

}

}

/*

EDIT PRODUCT

*/

public static function edit_product( $id, $user, $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['store'] ) || ! \query\main::have_store( $post['store'], $user ) ) {
  throw new \Exception( $LANG['msg_error'] );  // this error can appear only when user try to modify post data
} else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
  throw new \Exception( $LANG['edit_prod_writename'] );
} else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) {
  throw new \Exception( $LANG['edit_prod_writeurl'] );
} else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
  throw new \Exception( $LANG['edit_prod_writedesc'] );
} else {

  $end = $post['end'] . ', ' . $post['end_hour'];

  $info = \query\main::product_infos( $id );

  if( ( $end_unix = strtotime( $post['end'] ) ) > ( $paid_until = strtotime( $info->paid_until ) ) ) {

  $prices = prices( 'object' );

  $now_unix = strtotime( 'today 00:00' );

  // cost for this product

  $cost = (int) $prices->product * ceil( max( ceil( ( $end_unix - ( $paid_until > $now_unix ? $paid_until : $now_unix ) ) / 86400 ), 1 ) / (int) $prices->product_max_days );

  // save cost until

  $paid_until = $end_unix;

  } else {

  // cost for this product

  $cost = 0;

  }

  if( $GLOBALS['me']->Credits < $cost ) {
    throw new \Exception( sprintf( $LANG['msg_notenoughpoints'], $cost, $GLOBALS['me']->Credits ) );
  }

  $image = \site\images::upload( $_FILES['edit_product_form_image'], 'product_', array( 'path' => '', 'max_size' => 1024, 'max_width' => 800, 'max_height' => 800, 'current' => $info->image ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET store = ?, title = ?, link = ?, description = ?, tags = ?, image = ?, price = ?, old_price = ?, currency = ?, start = ?, expiration = ?, lastupdate_by = ?, lastupdate = NOW(), paid_until = FROM_UNIXTIME(?) WHERE id = ?" );

  $start = $post['start'] . ', ' . $post['start_hour'];
  if( $cost <= 0 ) {
    $paid_until =  strtotime( $info->paid_until );
  }

  $stmt->bind_param( "isssssddsssisi", $post['store'], $post['name'], $post['url'], $post['description'], $post['tags'], $image, $post['price'], $post['old_price'], $post['currency'], $start, $end, $user, $paid_until, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  // deduct credits

  \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

  return (object) array( 'image' => $image );

  }

  throw new \Exception( $LANG['msg_error'] );

}

}

/*

SUBMIT STORE

*/

public static function submit_store( $id, $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
  throw new \Exception( $LANG['submit_store_writename'] );
} else if( !isset( $post['url'] ) || !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) {
  throw new \Exception( $LANG['submit_store_wrongweb'] );
} else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
  throw new \Exception( $LANG['submit_store_writedesc'] );
} else {

  if( $GLOBALS['me']->Credits < ( $cost = (int) \query\main::get_option( 'price_store' ) ) ) {
    throw new \Exception( sprintf( $LANG['msg_notenoughpoints'], $cost, $GLOBALS['me']->Credits ) );
  }

  $logo = \site\images::upload( $_FILES['submit_store_form_logo'], 'logo_', array( 'path' => '', 'max_size' => 400, 'max_width' => 600, 'max_height' => 400, 'current' => '' ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (user, category, name, link, description, tags, image, visible, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $tags = ( isset( $post['tags'] ) ? $post['tags'] : '' );

  // autovalidate this store?

  $valid = \query\main::get_option( 'store_validate' );

  $stmt->bind_param( "iisssssii", $GLOBALS['me']->ID, $post['category'], $post['name'], $post['url'], $post['description'], $tags, $logo, $valid, $GLOBALS['me']->ID );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  // deduct credits

  \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

  return (object) array( 'image' => $logo );

  }

  throw new \Exception( $LANG['msg_error'] );

}

}

/*

EDIT STORE

*/

public static function edit_store( $id, $user, $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !\query\main::have_store( $id, $user ) ) {
  throw new \Exception( $LANG['msg_error'] );  // this error can appear only when user try to modify post data
} else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
  throw new \Exception( $LANG['edit_store_writename'] );
} else if( !isset( $post['url'] ) || !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) {
  throw new \Exception( $LANG['edit_store_wrongweb'] );
} else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
  throw new \Exception( $LANG['edit_store_writedesc'] );
} else {

  $store = \query\main::store_infos( $id );

  $logo = \site\images::upload( $_FILES['edit_store_form_logo'], 'logo_', array( 'path' => '', 'max_size' => 400, 'max_width' => 600, 'max_height' => 400, 'current' => $store->image ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET category = ?, name = ?, link = ?, description = ?, tags = ?, image = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );

  $tags = ( isset( $post['tags'] ) ? $post['tags'] : '' );

  $stmt->bind_param( "isssssii", $post['category'], $post['name'], $post['url'], $post['description'], $tags, $logo, $user, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {

  return (object) array( 'image' => $logo );

  } else {

  throw new \Exception( $LANG['msg_error'] );

  }

}

}

/*

SUBSCRIBE

*/

public static function subscribe( $id, $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
  throw new \Exception( $LANG['newsletter_usevalide'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "newsletter (email, ipaddr, date) VALUES (?, ?, NOW())" );

  $IP = \site\utils::getIP();

  $stmt->bind_param( "ss", $post['email'], $IP );
  $execute = $stmt->execute();
  $stmt->close();

  if( !$execute ) throw new \Exception( $LANG['newsletter_subscribed'] );

  if( \query\main::get_option( 'subscr_confirm_req' ) ) {

  $session = md5( \site\utils::str_random(15) );

  if( \user\mail_sessions::insert( 'subscription', array( 'email' => $post['email'], 'session' => $session ) ) && \site\mail::send( $post['email'], $LANG['email_sub_title'] . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'confirm_subscription' ), array( 'confirmation_main_text' => $LANG['email_sub_maintext'], 'confirmation_button' => $LANG['email_sub_button'], 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'action' => 'subscribe', 'email' => $post['email'], 'token' => $session ) ) ) ) )

  return 1;

  else {

  // the email could not be sent, so delete him from the database

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
  $stmt->bind_param( "s", $post['email'] );
  $stmt->execute();
  $stmt->close();

  throw new \Exception( $LANG['msg_error'] );

  }

  } else {

  // auto-validate the subscription

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 1 WHERE email = ?" );
  $stmt->bind_param( "s", $post['email'] );
  $stmt->execute();
  $stmt->close();

  if( $execute ) return 2;
  else
  throw new \Exception( $LANG['msg_error'] );

  }

  }

}

/*

SUBSCRIBE

*/

public static function unsubscribe( $post ) {

global $db, $LANG;

$post = array_map( 'trim', $post );

if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
  throw new \Exception( $LANG['newsletter_usevalide'] );
} else {

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
  $stmt->bind_param( "s", $post['email'] );
  $stmt->bind_result( $count );
  $stmt->execute();
  $stmt->fetch();
  $stmt->close();

  if( $count == 0 ) {

  throw new \Exception( $LANG['uunsubscr_notsubscr'] );

  }

  if( \query\main::get_option( 'unsubscr_confirm_req' ) ) {

  $session = md5( \site\utils::str_random(15) );

  if( \user\mail_sessions::insert( 'unsubscription', array( 'email' => $post['email'], 'session' => $session ) ) &&
  \site\mail::send( $post['email'], $LANG['email_unsub_title'] . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'confirm_unsubscription' ), array( 'confirmation_main_text' => $LANG['email_unsub_maintext'], 'confirmation_button' => $LANG['email_unsub_button'], 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'action' => 'unsubscribe2', 'email' => $post['email'], 'token' => $session ) ) ) ) )

  return 1;

  else
  throw new \Exception( $LANG['msg_error'] );

  } else {

  // auto-unsubscribe

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
  $stmt->bind_param( "s", $post['email'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) return 2;
  else
  throw new \Exception( $LANG['msg_error'] );

  }

}

}

/*

CLAIM A REWARD

*/

public static function get_reward( $id, $post ) {

global $db, $LANG;

if( !$GLOBALS['me'] ) {
  throw new \Exception( $LANG['msg_error'] );
}

if( !\query\main::reward_exists( $id, array( 'user_view' ) ) ) {
  throw new \Exception( $LANG['claim_reward_dontexist'] );
} else if( ( $reward = \query\main::reward_infos( $id ) ) && $reward->points > $GLOBALS['me']->Points ) {
  throw new \Exception( $LANG['claim_reward_mrepts'] );
} else {

// check required fields

foreach( $reward->fields as $field ) {
  if( (boolean) $field['require'] ) {

    switch( $field['type'] ) {

      case 'email':
      if( !isset( $post[$field['name']] ) || !filter_var( $post[$field['name']], FILTER_VALIDATE_EMAIL ) )
      throw new \Exception( $LANG['claim_reward_reqinv'] );
      break;

      case 'number':
      if( !isset( $post[$field['name']] ) || !filter_var( $post[$field['name']], FILTER_VALIDATE_INT ) )
      throw new \Exception( $LANG['claim_reward_reqinv'] );
      break;

      default:
      if( empty( $post[$field['name']] ) )
      throw new \Exception( $LANG['claim_reward_reqinv'] );
      break;

    }
  }
}

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "rewards_reqs (name, user, points, reward, fields, lastupdate_by, lastupdate, claimed, date) VALUES (?, ?, ?, ?, ?, ?, NOW(), 0, NOW())" );

  $fields = @serialize( $post );

  $stmt->bind_param( "siiisi", $reward->title, $GLOBALS['me']->ID, $reward->points, $reward->ID, $fields, $GLOBALS['me']->ID );

  if( $stmt->execute() ) {

  // deduct points from this user
  \user\update::add_points( $GLOBALS['me']->ID, -$reward->points );

  $stmt->close();

  return true;

  } else {

  $stmt->close();

  throw new \Exception( $LANG['msg_error'] );

  }

  }

}

/*

USER SEND MESSAGE VIA CONTACT FORM

*/

public static function send_contact( $post ) {

global $db, $LANG;

if( empty( $post['name'] ) ) {
  throw new \Exception( $LANG['sendcontact_complete_name'] );
} else if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
  throw new \Exception( $LANG['sendcontact_usevalide'] );
} else if( !isset( $post['message'] ) || strlen( $post['message'] ) < 10 ) {
  throw new \Exception( $LANG['sendcontact_writemsg'] );
} else {

    // send email
    if( \site\mail::send( \query\main::get_option( 'email_contact' ), $LANG['email_sec_title'] . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'contact_form', 'reply_name' => $post['name'], 'reply_to' => $post['email'] ), array( 'name' => $LANG['email_sec_name'], 'c_name' => $post['name'], 'email' => $LANG['email_sec_email'], 'c_email' => $post['email'], 'c_msg' => $post['message'] ) ) ) {

    return true;

    }

  throw new \Exception( $LANG['msg_error'] );

  }

}

}