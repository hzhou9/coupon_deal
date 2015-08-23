<?php

/*

OPTIONS

*/

function option( $option = '' ) {
  return \query\main::get_option( $option );
}

/*

Informations about the logged user. can be used as me() or $GLOBALS['me']

*/

function me() {
  if( !$GLOBALS['me'] ) {
    return false;
  }

  return $GLOBALS['me'];
}

/*

CHECK IF HAVE ITEMS

*/

function have_items_cat( $cat ) {
  return \query\main::have_items( $cat );
}

/*

IT'S NUMBER ONE?

*/

function is_First( $num ) {
  if( (int)$num === 1 )return true;
    return false;
}

/*

HTMLSPECIALCHARS

*/

function htmlsc( $str ) {
  return htmlspecialchars( $str );
}

/*

TIME AGO

*/

function timeago( $time, $type = '' ) {

global $LANG;

  if( $type == 'seconds' ) {
    $time =  time() - $time;
  } else {
    $time = $time - time();
  }

  if( $time > 31536000 ) {
    $y = floor( $time / 31536000 );
    return $y . ' ' . ( is_First( $y ) ? strtolower( $LANG['year'] ) : strtolower( $LANG['years'] ) );
  } else if( $time > 2592000 ) {
    $m = floor( $time / 2592000 );
    return $m  . ' ' . ( is_First( $m ) ? strtolower( $LANG['month'] ) : strtolower( $LANG['months'] ) );
  } else if( $time > 86400 ) {
    $d = floor( $time / 86400 );
    return $d  . ' ' . ( is_First( $d ) ? strtolower( $LANG['day'] ) : strtolower( $LANG['days'] ) );
  } else if( $time > 3600 ) {
    $h = floor( $time / 3600 );
    return $h . ' ' . ( is_First( $h ) ? strtolower( $LANG['hour'] ) : strtolower( $LANG['hours'] ) );
  } else if( $time > 60 ) {
    $m = floor( $time / 60 );
    return $m . ' ' . ( is_First( $m ) ? strtolower( $LANG['minute'] ) : strtolower( $LANG['minutes'] ) );
  } else {
    return $time . ' ' . ( is_First( $time ) ? strtolower( $LANG['second'] ) : strtolower( $LANG['seconds'] ) );
  }

}

/*

READ A PART OF TEMPLATE

*/

function read_template_part( $part ) {

$theme_location = rtrim( THEMES_LOC, '/' ) . '/' . \query\main::get_option( 'theme' );

  switch( $part ) {
    case '404': include( $theme_location . '/404.php' ); break;
  }

}

/*

SHOW CATEGORIES

*/

function all_categories() {
  return \query\main::while_categories( array( 'max' => 0 ) );
}

/*

SHOW GROUPED CATEGORIES

*/

function all_grouped_categories() {
  return \query\main::group_categories( array( 'max' => 0 ) );
}

/*

SHOW PAGES

*/

function all_pages() {
  return \query\main::while_pages( array( 'max' => 0 ) );
}

/*

CHECK IF HAVE CUSTOM ITEMS

*/

function have_items_custom( $category = array() ) {
  return \query\main::have_items( $category );
}

/*

SHOW CUSTOM ITEMS

*/

function items_custom( $category = array() ) {
  return \query\main::while_items( $category );
}

/*

CHECK IF HAVE CUSTOM PRODUCTS

*/

function have_products_custom( $category = array() ) {
  return \query\main::have_products( $category );
}

/*

SHOW CUSTOM PRODUCTS

*/

function products_custom( $category = array() ) {
  return \query\main::while_products( $category );
}

/*

CHECK IF HAVE CUSTOM CATEGORIES

*/

function have_categories_custom( $category = array() ) {
  return \query\main::have_categories( $category );
}

/*

SHOW CUSTOM CATEGORIES

*/

function categories_custom( $category = array() ) {
  return \query\main::group_categories( $category );
}

/*

CHECK IF HAVE CUSTOM PAGES

*/

function have_pages_custom( $category = array() ) {
  return \query\main::have_pages( $category );
}

/*

SHOW CUSTOM PAGES

*/

function pages_custom( $category = array() ) {
  return \query\main::while_pages( $category );
}

/*

CHECK IF HAVE CUSTOM STORES

*/

function have_stores_custom( $category = array()  ) {
  return \query\main::have_stores( $category );
}

/*

SHOW CUSTOM STORES

*/

function stores_custom( $category = array() ) {
  return \query\main::while_stores( $category );
}

/*

CHECK IF HAVE CUSTOM REVIEWS

*/

function have_reviews_custom( $category = array()  ) {
  return \query\main::have_reviews( $category );
}

/*

SHOW CUSTOM REVIEWS

*/

function reviews_custom( $category = array() ) {
  return \query\main::while_reviews( $category );
}

/*

CHECK IF HAVE CUSTOM USERS

*/

function have_users_custom( $category = array()  ) {
  return \query\main::have_users( $category );
}

/*

SHOW CUSTOM USERS

*/

function users_custom( $category = array() ) {
  return \query\main::while_users( $category );
}

/*

CHECK IF HAVE REWARDS

*/

function have_rewards( $category = array()  ) {
  return \query\main::have_rewards( $category );
}

/*

SHOW REWARDS

*/

function rewards( $category = array() ) {
  return \query\main::while_rewards( $category );
}

/*

CHECK IF HAVE REWARD REQUESTS

*/

function have_claim_reqs( $category = array()  ) {
  return \query\main::have_rewards_reqs( $category );
}

/*

SHOW REWARD REQUESTS

*/

function claim_reqs( $category = array() ) {
  return \query\main::while_rewards_reqs( $category );
}

/*

CHECK IF HAVE PAYMENT PLANS

*/

function have_payment_plans( $category = array()  ) {
  return \query\payments::have_plans( $category );
}

/*

SHOW PAYMENT PLANS

*/

function payment_plans( $category = array() ) {
  return \query\payments::while_plans( $category );
}


/*

SETTINGS: META CHARSET

*/

function meta_charset() {
  return \query\main::get_option( 'meta_charset' );
}

/*

SETTINGS: SITE NAME

*/

function site_name() {
  return \query\main::get_option( 'sitename' );
}

/*

SETTINGS: SITE DESCRIPTION

*/

function description() {
  return \query\main::get_option( 'sitedescription' );
}

/*

SETTINGS: ITEMS PER PAGE

*/

function items_per_page() {
  return \query\main::get_option( 'items_per_page' );
}

/*

SETTINGS: ALLOW REVIEWS

*/

function allow_reviews() {
  return (boolean) \query\main::get_option( 'allow_reviews' );
}

/*

SETTINGS: SITE THEME

*/

function theme() {
  return \query\main::get_option( 'theme' );
}

/*

THEME LOCATION

*/

function theme_location() {
  return $GLOBALS['siteURL'] . rtrim( THEMES_LOC, '/' ) . '/' . \query\main::get_option( 'theme' );
}

/*

SHOW USER AVATAR

*/

function user_avatar( $text ) {
  return \query\main::user_avatar( $text );
}

/*

SHOW STORE AVATAR

*/

function store_avatar( $text ) {
  return \query\main::store_avatar( $text );
}

/*

SHOW PRODUCT AVATAR

*/

function product_avatar( $text ) {
  return \query\main::product_avatar( $text );
}


/*

SHOW REWARD AVATAR

*/

function reward_avatar( $text ) {
  return \query\main::reward_avatar( $text );
}

/*

PAYMENT PLAN AVATAR

*/

function payment_plan_avatar( $text ) {
  return \query\main::payment_plan_avatar( $text );
}

/*

PERMALINK

*/

function tlink( $place, $q = array(), $backTo = '' ) {

$seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

$page =  '';

if( preg_match( '/([a-z0-9-_.]+)[\/]([a-z0-9-_.]+)/i', $place, $sl ) ) {
  list( $place, $page ) = array( $sl[1], $sl[2] );
}

if( !empty( $backTo ) ) {
  if( $backTo == 'this' ) {
    $backTo = $_SERVER['REQUEST_URI'];
  }
  $q = !empty( $q ) ? $q . '&amp;backto=' . $backTo : 'backto=' . $backTo;
}

switch( $place ) {
  case 'index': return $GLOBALS['siteURL']; break;
  case 'page': return ( $seo_link ? $GLOBALS['siteURL'] . ( !empty( $q['seo'] ) ? $q['seo'] : '' ) : $GLOBALS['siteURL'] . ( !empty( $q['notseo'] ) ? '?' . $q['notseo'] : '' ) ); break;
  case 'user': return ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_user' ) ) . $page . '.html' . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?user=' . $page . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) ); break;
  case 'stores': return ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_stores' ) ) . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?stores' . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) ); break;
  case 'search': return ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_search' ) ) : $GLOBALS['siteURL'] ); break;
  case 'ajax': return ( $seo_link ? \site\utils::make_seo_link( 'ajax' ) . $page . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?ajax=' . strtok( $page, '.' ) . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) ); break;
  case 'plugin': return ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_plugin' ) ) . $page . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?plugin=' . strtok( $page, '.' ) . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) ); break;
  case 'tpage': return ( $seo_link ? $GLOBALS['siteURL'] . $page . '.html' . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?tpage=' . $page . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) ); break;
  case 'pay': return $GLOBALS['siteURL'] . 'payment.php' . ( !empty( $q ) ? '?' . (string) $q : '' ); break;
}

}

/*

ADD EXTRA HEAD

*/

function add_extra_head() {

  $cache = new \cache\main;

  if( $show_from_cache = $cache->check( 'theme_head' ) ) {

    return $show_from_cache;

  } else {

  $head = '';

  foreach( \query\others::while_head_lines( array_merge( array( 'max' => 0, 'show' => 'theme', 'orderby' => 'date desc' ) ) ) as $line ) {
    $head .= \site\plugin::replace_constant( $line->text ) . "\n";
  }

  if( file_exists( COMMON_LOCATION . '/head.html' ) ) {
    $head .= @file_get_contents( COMMON_LOCATION . '/head.html' );
    $head .= "\n";
  }

  $cache->add( 'theme_head', $head );

  return $head;

  }

}

/*

METATAGS - TITLE

*/

if( !function_exists( 'meta_title' ) ) {

function meta_title() {
  return meta_default( '', \query\main::get_option( 'sitetitle' ) );
}

}

/*

METATAGS - KEYWORDS

*/

if( !function_exists( 'meta_keywords' ) ) {

function meta_keywords() {
  return meta_default( '', \query\main::get_option( 'meta_keywords' ) );
}

}

/*

METATAGS - DESCRIPTION

*/

if( !function_exists( 'meta_description' ) ) {

function meta_description() {
  return meta_default( '', \query\main::get_option( 'meta_description' ) );
}

}

/*

METATAGS - IMAGE

*/

if( !function_exists( 'meta_image' ) ) {

function meta_image( $image = '' ) {
  return theme_location() . '/' . $image;
}

}

/*

METATAGS - DEFAULT

*/

function meta_default( $list = array(), $text = '' ) {
  if( empty( $list ) ) {
    $list = array( '%YEAR%' => date('Y'), '%MONTH%' => date('F') );
  }
  return str_replace( array_keys( $list ), array_values( $list ), htmlspecialchars( $text ) );
}

/*

CHECK IF THERE ARE FILLED INFORMATIONS TO LOGIN WITH GOOGLE+

*/

function google_login() {

if( \query\main::get_option( 'google_clientID' ) === '' || \query\main::get_option( 'google_secret' ) === '' || \query\main::get_option( 'google_ruri' ) === '' ) {
  return false;
}

return true;

}

/*

CHECK IF THERE ARE FILLED INFORMATIONS TO LOGIN WITH FACEBOOK

*/

function facebook_login() {
  
if( \query\main::get_option( 'facebook_appID' ) === '' || \query\main::get_option( 'facebook_secret' ) === '' ) {
  return false;
}

return true;

}

/*

SHOW PRICE AS SPECIFIED FORMAT

*/

function price( $price ) {
  return sprintf( PRICE_FORMAT, $price );
}

/*

SHOW PRICE IN DESIRED FORMAT

*/

function price_format( $price ) {
  return \site\utils::money_format( $price );
}

/*

LANGUAGES

*/

function languages() {
  return \site\language::languages();
}

/*

PAYMENT GATEWAYS

*/

function payment_gateways() {
  return \site\payment::gateways();
}

/*

PRICES

*/

function prices( $out = 'array' ) {
  $prices = array( 'store' => \query\main::get_option( 'price_store' ), 'coupon' => \query\main::get_option( 'price_coupon' ), 'coupon_max_days' => \query\main::get_option( 'price_max_days' ), 'product' => \query\main::get_option( 'price_product' ), 'product_max_days' => \query\main::get_option( 'price_product_max_days' ) ); ;
  if( $out == 'object' ) {
    return (object) $prices;
  }
  return $prices;
}

/*

REMOVE PARAMETERS FROM GET QUERY STRING

*/

function get_update( $array, $url = '' ) {
  return \site\utils::update_uri( $url, $array );
}

/*

REMOVE PARAMETER FROM GET QUERY STRING

*/

function get_remove( $array, $url = '' ) {
  return \site\utils::update_uri( $url, $array, 'remove' );
}

/*

CHECK IF USER IT'S LOGGED

*/

function logout() {
  return \user\main::logout();
}

/*

CHECK IF A STORE IS FAVORITE

*/

function is_favorite( $id = 0 ) {

global $GET;

$id = empty( $id ) && isset( $GET['id'] ) ? $GET['id'] : $id;
if( empty( $id ) ) {
  return false;
}

if( $GLOBALS['me'] ) {
  return \user\main::check_favorite( $GLOBALS['me']->ID, $GET['id'] );
} else {
  return false;
}

}

/*

NUMBER OF ITEMS

*/

function site_count( $type = '', $category = array() ) {

  switch( $type ) {
    case 'sotre':
    case 'stores':
    return \query\main::stores( $category );
    break;
    case 'coupon':
    case 'coupons':
    return \query\main::coupons( $category );
    break;
    case 'product':
    case 'products':
    return \query\main::products( $category );
    break;
    case 'review':
    case 'reviews':
    return \query\main::reviews( $category );
    break;
    case 'user':
    case 'users':
    return \query\main::users( $category );
    break;
    case 'category':
    case 'categories':
    return \query\main::categories( $category );
    break;
    default:
    return 'NaN';
    break;
  }

}

/*

PROFILES ON SOCIAL NETWORKS

*/

function social_networds() {

  $profile = array();
  if( ( $facebook = \query\main::get_option( 'social_facebook' ) ) && !empty( $facebook ) ) {
    $profile['facebook'] = $facebook;
  }
  if( ( $google = \query\main::get_option( 'social_google' ) ) && !empty( $google ) ) {
    $profile['google'] = $google;
  }
  if( ( $twitter = \query\main::get_option( 'social_twitter' ) ) && !empty( $twitter ) ) {
    $profile['twitter'] = $twitter;
  }
  if( ( $flickr = \query\main::get_option( 'social_flickr' ) ) && !empty( $flickr ) ) {
    $profile['flickr'] = $flickr;
  }
  if( ( $linkedin = \query\main::get_option( 'social_linkedin' ) ) && !empty( $linkedin ) ) {
    $profile['linkedin'] = $linkedin;
  }
  if( ( $vimeo = \query\main::get_option( 'social_vimeo' ) ) && !empty( $vimeo ) ) {
    $profile['vimeo'] = $vimeo;
  }
  if( ( $youtube = \query\main::get_option( 'social_youtube' ) ) && !empty( $youtube ) ) {
    $profile['youtube'] = $youtube;
  }
  if( ( $myspace = \query\main::get_option( 'social_myspace' ) ) && !empty( $myspace ) ) {
    $profile['myspace'] = $myspace;
  }
  if( ( $reddit = \query\main::get_option( 'social_reddit' ) ) && !empty( $reddit ) ) {
    $profile['reddit'] = $reddit;
  }
  if( ( $pinterest = \query\main::get_option( 'social_pinterest' ) ) && !empty( $pinterest ) ) {
    $profile['pinterest'] = $pinterest;
  }
  return $profile;

}

/*

BBCODES

*/

function bbcodes( $text ) {

  return \site\utils::bbcodes( $text );

}

/*

SHOW WIDGET

*/

function show_widgets( $id ) {

global $LANG;

  if( function_exists( 'register_widgets' ) ) {
    if( in_array( $id, array_keys( register_widgets() ) ) ) {

    $data = \query\main::show_widgets( $id );

      foreach( $data as $k => $v ) {
          list( $title, $limit, $type, $order, $content, $mobile_view ) = array( $v['title'], $v['limit'], $v['type'], $v['orderby'], $v['content'], $v['mobile_view'] );
            @include $v['file'];
        }

    }
  }

  return false;

}

/*

LOGIN FORM

*/

function login_form() {

global $LANG;

  $form = '<div class="login_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['login_form'] ) && \site\utils::check_csrf( $_POST['login_form']['csrf'], 'login_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['login_form'] );

  try {

    $session = \user\main::login( $pd );
    $form .= '<div class="success">' . $LANG['login_success'] . '</div>';
    $form .= '<meta http-equiv="refresh" content="2; url='. $GLOBALS['siteURL'] . '/setSession.php?session=' . $session . '">';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['login_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="login_form[username]">' . $LANG['form_email'] . ':</label> <div><input type="email" name="login_form[username]" id="login_form[username]" value="' . ( isset( $pd['username'] ) ? $pd['username'] : '' ) . '" required /></div></div>
  <div class="form_field"><label for="login_form[password]">' . $LANG['form_password'] . ':</label> <div><input type="password" name="login_form[password]" id="login_form[password]" value="" required /></div></div>
  <input type="hidden" name="login_form[csrf]" value="' . $csrf . '" />
  <div class="form_field no-label"><input type="checkbox" name="login_form[keep_logged]" id="keep_logged" /> <label for="keep_logged">' . $LANG['msg_keep_log'] . '</label></div>

  <button>' . $LANG['login'] . '</button>
  </form>

  </div>';

  return $form;

}

/*

REGISTER FORM

*/

function register_form() {

global $LANG;

if( \query\main::get_option( 'registrations' ) == 'opened' ) {

  $form = '<div class="register_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['register_form'] ) && \site\utils::check_csrf( $_POST['register_form']['csrf'], 'register_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['register_form'] );

  try {

    $session = \user\main::register( $pd );
    $form .= '<div class="success">' . $LANG['register_success'] . '</div>';
    $form .= '<meta http-equiv="refresh" content="2; url='. $GLOBALS['siteURL'] . '/setSession.php?session=' . $session . '">';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['register_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="register_form[username]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="register_form[username]" id="register_form[username]" value="' . ( isset( $pd['username'] ) ? $pd['username'] : '' ) . '" required /></div></div>
  <div class="form_field"><label for="register_form[email]">' . $LANG['form_email'] . ':</label> <div><input type="email" name="register_form[email]" id="register_form[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" required /></div></div>
  <div class="form_field"><label for="register_form[password]">' . $LANG['form_password'] . ':</label> <div><input type="password" name="register_form[password]" id="register_form[password]" value="" required /></div></div>
  <div class="form_field"><label for="register_form[password2]">' . $LANG['form_password_again'] . ':</label> <div><input type="password" name="register_form[password2]" id="register_form[password2]" value="" required /></div></div>
  <input type="hidden" name="register_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['register'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['register_not_allowed'] . '</div>';

}

}

/*

FORGOT PASSWORD FORM

*/

function forgot_password_form() {

global $_GET, $LANG;

  $form = '<div class="forgot_password other_form">';

  if( isset( $_GET['uid'] ) && isset( $_GET['session'] ) && \user\mail_sessions::check( 'password_recovery', array( 'user' => $_GET['uid'], 'session' => $_GET['session'] ) )) {

  /* RESET PASSWORD FORM */

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['forgot_password'] ) && \site\utils::check_csrf( $_POST['forgot_password']['csrf'], 'forgot_password_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['forgot_password'] );

  try {

    \user\main::reset_password( $_GET['uid'], $pd );
    $form .= '<div class="success">' . $LANG['reset_pwd_success'] . '</div>';

    \user\mail_sessions::clear( 'password_recovery', array( 'user' => $_GET['uid'] ) );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['forgot_password_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="forgot_password[email]">' . $LANG['change_pwd_form_new'] . ':</label> <div><input type="password" name="forgot_password[password1]" id="forgot_password[password1]" value="" required /></div></div>
  <div class="form_field"><label for="forgot_password[email]">' . $LANG['change_pwd_form_new2'] . ':</label> <div><input type="password" name="forgot_password[password2]" id="forgot_password[password2]" value="" required /></div></div>
  <input type="hidden" name="forgot_password[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['reset_pwd_button'] . '</button>
  </form>';

  } else {

  /* SEND A SESSION TO HIS EMAIL ADDRESS FORM */

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['forgot_password'] ) && \site\utils::check_csrf( $_POST['forgot_password']['csrf'], 'forgot_password_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['forgot_password'] );

  try {

    \user\main::recovery_password( $_POST['forgot_password'] );
    $form .= '<div class="success">' . $LANG['fp_success'] . '</div>';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['forgot_password_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="forgot_password[email]">' . $LANG['form_email'] . ':</label> <div><input type="email" name="forgot_password[email]" id="forgot_password[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" required /></div></div>
  <input type="hidden" name="forgot_password[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['recovery'] . '</button>
  </form>';

  }

  $form .= '</div>';

  return $form;

}

/*

POST REVIEW FORM

*/

function write_review_form( $id = 0 ) {

global $GET, $LANG;

if( isset( $GET['id'] ) ) {
  $id = $GET['id'];
}

if( $GLOBALS['me'] && !empty( $id ) ) {

  if( ! (boolean) \query\main::get_option( 'allow_reviews' ) ) {

    return '<div class="info_form">' . $LANG['review_not_allowed'] . '</div>';

  }

  $form = '<div class="write_review_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['write_review_form'] ) && \site\utils::check_csrf( $_POST['write_review_form']['csrf'], 'write_review_form_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['write_review_form'] );

  try {

    \user\main::write_review( $id, $GLOBALS['me']->ID, $pd );
    $form .= '<div class="success">' . $LANG['review_sent'] . '</div>';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['write_review_form_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="write_review_form[stars]">' . $LANG['form_stars']  . ':</label> <div><select name="write_review_form[stars]" id="write_review_form[stars]">
  <option value="5">5</option>
  <option value="4">4</option>
  <option value="3">3</option>
  <option value="2">2</option>
  <option value="1">1</option>
  </select></div></div>
  <div class="form_field"><label for="write_review_form[text]">' . $LANG['form_text']  . ':</label> <div><textarea name="write_review_form[text]" id="write_review_form[text]" required></textarea></div></div>
  <input type="hidden" name="write_review_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['post_review']  . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

SUGGEST STORE FORM

*/

function suggest_store_form( $auto_select = array( 'intent' => 1 ), $loc = '' ) {

global $LANG;

  // id is important only for auto select (intent), please read the documentation
  $intent = array( 1 => $LANG['suggestion_store_owner'], 2 => $LANG['suggestion_just_suggestion'] );

  $form = '<div class="suggest_store_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['suggest_store_form' . $loc] ) && \site\utils::check_csrf( $_POST['suggest_store_form' . $loc]['csrf'], 'suggest_store' . $loc . '_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['suggest_store_form' . $loc] );

  try {

    $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

    \user\main::suggest_store( $id, $pd, $intent );
    $form .= '<div class="success">' . $LANG['suggestion_sent'] . '</div>';

    unset( $pd );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['suggest_store' . $loc . '_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#widget_suggest">
  <div class="form_field"><label for="suggest_store_form' . $loc . '[intent]"></label>
  <div><select name="suggest_store_form' . $loc . '[intent]" id="suggest_store_form' . $loc . '[intent]">';
  foreach( $intent as $k => $v )$form .= '<option value="' . $k . '"' . ( ( $_SERVER['REQUEST_METHOD'] != 'POST' && !empty( $auto_select['intent'] ) && ( $auto_select['intent'] == $k || $auto_select['intent'] == $v ) ) || ( isset( $pd['intent'] ) && $pd['intent'] == $k ) ? ' selected' : '' ) . '>' . $v . '</option>';
  $form .= '</select></div>
  </div>
  <div class="form_field"><label for="suggest_store_form' . $loc . '[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="suggest_store_form' . $loc . '[name]" id="suggest_store_form[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" placeholder="' . $LANG['suggestion_name_ph'] . '" required /></div></div>
  <div class="form_field"><label for="suggest_store_form' . $loc . '[url]">' . $LANG['form_store_url'] . ':</label> <div><input type="text" name="suggest_store_form' . $loc . '[url]" id="suggest_store_form[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : 'http://' ) . '" placeholder="http://" required /></div></div>
  <div class="form_field"><label for="suggest_store_form' . $loc . '[description]">' . $LANG['form_description'] . ':</label> <div><textarea name="suggest_store_form' . $loc . '[description]" id="suggest_store_form[description]">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>
  <div class="form_field"><label for="suggest_store_form' . $loc . '[message]">' . $LANG['form_message_for_us'] . ':</label> <div><textarea name="suggest_store_form' . $loc . '[message]" id="suggest_store_form[message]">' . ( isset( $pd['message'] ) ? $pd['message'] : '' ) . '</textarea></div></div>
  <input type="hidden" name="suggest_store_form' . $loc . '[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['send'] . '</button>
  </form>

  </div>';

  return $form;

}

/*

NEWSLETTER FORM

*/

function newsletter_form( $loc = '' ) {

global $LANG;

  $form = '';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['newsletter_form' . $loc] ) && \site\utils::check_csrf( $_POST['newsletter_form' . $loc]['csrf'], 'newsletter_form' . $loc . '_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['newsletter_form' . $loc] );

  try {

    $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

    $type = \user\main::subscribe( $id, $pd );
    if( $type == 1 ) $form .= '<div class="success">' . sprintf( $LANG['newsletter_reqconfirm'], $pd['email'] ) . '</div>';
    else $form .= '<div class="success">' . $LANG['newsletter_success'] . '</div>';

    unset( $pd );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['newsletter_form' . $loc . '_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#widget_newsletter">
  <input type="email" name="newsletter_form' . $loc . '[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" placeholder="' . $LANG['form_email'] . '" required />
  <input type="hidden" name="newsletter_form' . $loc . '[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['subscribe'] . '</button>
  </form>';

  return $form;

}

/*

CONTACT FORM

*/

function contact_form( $loc = '' ) {

global $LANG;

  $form = '<div class="contact_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['contact_form' . $loc] ) && \site\utils::check_csrf( $_POST['contact_form' . $loc]['csrf'], 'contact_form' . $loc . '_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['contact_form' . $loc] );

  try {

    $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

    \user\main::send_contact( $pd );
    $form .= '<div class="success">' . $LANG['sendcontact_success'] . '</div>';

    unset( $pd );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['contact_form' . $loc . '_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#widget_contact">
  <div class="form_field"><label for="contact_form' . $loc . '[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="contact_form' . $loc . '[name]" id="contact_form' . $loc . '[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" required /></div></div>
  <div class="form_field"><label for="contact_form' . $loc . '[email]">' . $LANG['form_email'] . ':</label> <div><input type="email" name="contact_form' . $loc . '[email]" id="contact_form' . $loc . '[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" required /></div></div>
  <div class="form_field"><label for="contact_form' . $loc . '[message]">' . $LANG['form_message'] . ':</label> <div><textarea name="contact_form' . $loc . '[message]" id="contact_form' . $loc . '[message]">' . ( isset( $pd['message'] ) ? $pd['message'] : '' ) . '</textarea></div></div>
  <input type="hidden" name="contact_form' . $loc . '[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['send'] . '</button>
  </form>

  </div>';

  return $form;

}