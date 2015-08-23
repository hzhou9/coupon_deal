<?php

class admin_query {


/*

GET NUMBER OF SUGGETSIONS

*/

public static function suggestions( $categories = array() ) {
  return admin_query::have_suggestions( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF BANNED IP'S

*/

public static function banned( $categories = array() ) {
  return admin_query::have_banned( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF NEWS

*/

public static function news( $categories = array() ) {
  return admin_query::have_news( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF USER SESSIONS

*/

public static function user_sessions( $categories = array() ) {
  return admin_query::have_usessions( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF SUBSCRIBERS

*/

public static function subscribers( $categories = array() ) {
  return admin_query::have_subscribers( $categories, array( 'only_count' => '' ) );
}


/*

GET NUMBER OF CHAT MESSAGES

*/

public static function chat_messages( $categories = array() ) {
  return admin_query::have_chat_messages( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF INSTALLED PLUGINS

*/

public static function plugins( $categories = array() ) {
  return admin_query::have_plugins( $categories, array( 'only_count' => '' ) );
}

/*

CHECK IF SUGGESTON EXISTS

*/

public static function suggestion_exists( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "suggestions WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT SUGGESTION

*/

public static function suggestion_infos( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, user, type, viewed, name, url, description, message, date FROM " . DB_TABLE_PREFIX . "suggestions WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $type, $read, $name, $url, $description, $message, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'user' => $user, 'type' => $type, 'read' => $read, 'name' => htmlspecialchars( $name ), 'url' => htmlspecialchars( $url ), 'description' => htmlspecialchars( $description ), 'message' => htmlspecialchars( $message ), 'date' => $date );

}

/*

CHECK IF SUBSCRIBER EXISTS

*/

public static function subscriber_exists( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "newsletter WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT SUBSCRIBER

*/

public static function subscriber_infos( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, email, ipaddr, econf, date FROM " . DB_TABLE_PREFIX . "newsletter WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $email, $ipaddr, $verified, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'email' => htmlspecialchars( $email ), 'IP' => htmlspecialchars( $ipaddr ), 'verified' => $verified, 'date' => $date );

}

/*

CHECK IF BANNED IP EXISTS

*/

public static function banned_exists( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT A BANNED IP

*/

public static function banned_infos( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, ipaddr, registration, login, site, redirect_to, expiration, expiration_date, date FROM " . DB_TABLE_PREFIX . "banned WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $ip, $regs, $login, $site, $redirect, $expiration, $expiration_date, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'IP' => $ip, 'registration' => $regs, 'login' => $login, 'site' => $site, 'redirect_to' => $redirect, 'expiration' => $expiration, 'expiration_date' => $expiration_date, 'date' => $date );

}

/*

CHECK IF A STORE HAS BEEN IMPORTED

*/

public static function store_imported( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*), id, category FROM " . DB_TABLE_PREFIX . "stores WHERE feedID = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count, $id, $cat );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return (object) array( 'ID' => $id, 'catID' => $cat );
  }

  return false;

}

/*

CHECK IF A COUPON HAS BEEN IMPORTED

*/

public static function coupon_imported( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*), id, category FROM " . DB_TABLE_PREFIX . "coupons WHERE feedID = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count, $id, $cat );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return (object) array( 'ID' => $id, 'catID' => $cat );
  }

  return false;

}

/*

CHECK IF PLUGIN EXISTS

*/

public static function plugin_exists( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "plugins WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT A PLUGIN

*/

public static function plugin_infos( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, user, name, image, scope, main, options, menu, menu_ready, menu_icon, extend_vars, description, visible, version, update_checker, uninstall, date FROM " . DB_TABLE_PREFIX . "plugins WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $name, $image, $scope, $main_file, $options_file, $menu, $menu_ready, $menu_icon, $vars, $description, $visible, $version, $update_checker, $uninstall, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'user' => $user, 'name' => htmlspecialchars( $name ), 'image' => htmlspecialchars( $image ), 'scope' => htmlspecialchars( $scope ), 'main_file' => htmlspecialchars( $main_file ), 'options_file' => htmlspecialchars( $options_file ), 'menu' => $menu, 'menu_ready' => $menu_ready, 'menu_icon' => $menu_icon, 'vars' => @unserialize( $vars ), 'description' => htmlspecialchars( $description ), 'update_checker' => htmlspecialchars( $update_checker ), 'version' => $version, 'uninstall_preview' => @unserialize( $uninstall ), 'visible' => $visible, 'date' => $date );

}

/*

NUMBER OF SUGGESTIONS

*/

public static function have_suggestions( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, url, description, message) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'read': $where[] = 'viewed = 1'; break;
      case 'notread': $where[] = 'viewed = 0'; break;
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "suggestions" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE SUGGESTIONS

*/

public static function while_suggestions( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, url, description, message) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'read': $where[] = 'viewed = 1'; break;
      case 'notread': $where[] = 'viewed = 0'; break;
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, user, type, viewed, name, url, description, message, date FROM " . DB_TABLE_PREFIX . "suggestions" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $type, $read, $name, $url, $description, $message, $date );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'user' => $user, 'type' => $type, 'read' => $read, 'name' => htmlspecialchars( $name ), 'url' => htmlspecialchars( $url ), 'description' => htmlspecialchars( $description ), 'message' => htmlspecialchars( $message ), 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF BANNED IP's

*/

public static function have_banned( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'ipaddr REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {

    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }

  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE BANNED IP's

*/

public static function while_banned( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'ipaddr REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, ipaddr, registration, login, site, redirect_to, date FROM " . DB_TABLE_PREFIX . "banned" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $ip, $regs, $login, $site, $redirect, $date );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'IP' => $ip, 'registration' => $regs, 'login' => $login, 'site' => $site, 'redirect_to' => $redirect, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF NEWS

*/

public static function have_news( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'title REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "news" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE NEWS

*/

public static function while_news( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'title REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT newsID, title, url, date FROM " . DB_TABLE_PREFIX . "news" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $title, $url, $date );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'title' => htmlspecialchars( $title ), 'url' => htmlspecialchars( $url ), 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF USER SESSIONS

*/

public static function have_usessions( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'u.name REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "sessions s LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = s.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE USER SESSIONS

*/

public static function while_usessions( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'u.name REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'date': $orderby[] = 's.date'; break;
      case 'date desc': $orderby[] = 's.date DESC'; break;
      case 'name': $orderby[] = 'u.name'; break;
      case 'name desc': $orderby[] = 'u.name DESC'; break;    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT s.id, s.user, u.name, u.avatar, s.expiration, s.date FROM " . DB_TABLE_PREFIX . "sessions s LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = s.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $name, $avatar, $expiration, $date );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'userID' => $user, 'name' => $name, 'avatar' => $avatar, 'expiration' => $expiration, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF SUBSRBERS

*/

public static function have_subscribers( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $w_user = $w_newsletter = '';

  /*

  WHERE / ORDER BY

  */

  $where['users'][] = 'subscriber = 1';

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where['users'][] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
    $where['newsletter'][] = 'CONCAT(email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'verified': $where['users'][] = 'valid >= 1'; $where['newsletter'][] = 'econf >= 1'; break;
      case 'notverified': $where['users'][] = 'valid = 0'; $where['newsletter'][] = 'econf = 0'; break;
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM (SELECT id FROM " . DB_TABLE_PREFIX . "users " . ( empty( $where['users'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['users'] ) ) . " UNION ALL SELECT id FROM " . DB_TABLE_PREFIX . "newsletter " . ( empty( $where['newsletter'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['newsletter'] ) ) . ") AS count" );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE SUBSCRIBERS

*/

public static function while_subscribers( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  $where['users'][] = 'subscriber = 1';

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where['users'][] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
    $where['newsletter'][] = 'CONCAT(email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'verified': $where['users'][] = 'valid >= 1'; $where['newsletter'][] = 'econf >= 1'; break;
      case 'notverified': $where['users'][] = 'valid = 0'; $where['newsletter'][] = 'econf = 0'; break;
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where['users'][] = $where['newsletter'][] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where['users'][] = $where['newsletter'][] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
      case 'email': $orderby[] = 'email'; break;
      case 'email desc': $orderby[] = 'email DESC'; break;    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "(SELECT 1, id, email, name, avatar, valid, date FROM " . DB_TABLE_PREFIX . "users" . ( empty( $where['users'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['users'] ) ) . ") UNION ALL (SELECT 0, id, email, '', '', econf, date FROM " . DB_TABLE_PREFIX . "newsletter" . ( empty( $where['newsletter'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['newsletter'] ) ) . ")" . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $user, $id, $email, $name, $avatar, $verified, $date );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'email' => $email, 'is_user' => $user, 'verified' => $verified, 'user_name' => $name, 'user_avatar' => $avatar, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF CLICKS

*/

public static function have_clicks( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['store'] ) ) {
    $where[] = 'c.store = "' . (int) $categories['store'] . '"';
  }

  if( !empty( $categories['coupon'] ) ) {
    $where[] = 'c.coupon = "' . (int) $categories['coupon'] . '"';
  }

  if( !empty( $categories['product'] ) ) {
    $where[] = 'c.product = "' . (int) $categories['product'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(c.country1, c.country2, c.browser, c.ipaddr, s.name) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {

    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }

  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "click c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE CLICKS

*/

public static function while_clicks( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['store'] ) ) {
    $where[] = 'c.store = "' . (int) $categories['store'] . '"';
  }

  if( !empty( $categories['coupon'] ) ) {
    $where[] = 'c.coupon = "' . (int) $categories['coupon'] . '"';
  }

  if( !empty( $categories['product'] ) ) {
    $where[] = 'c.product = "' . (int) $categories['product'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(c.country1, c.country2, c.browser, c.ipaddr, s.name) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'date': $orderby[] = 'c.date'; break;
      case 'date desc': $orderby[] = 'c.date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT c.id, c.store, c.coupon, c.product, c.user, c.ipaddr, c.browser, c.country1, c.country2, c.date, s.name, s.image FROM " . DB_TABLE_PREFIX . "click c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $store, $coupon, $product, $user, $IP, $browser, $country_code, $country_name, $date, $store_name, $store_img );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'storeID' => $store, 'couponID' => $coupon, 'productID' => $product, 'user' => $user, 'IP' => htmlspecialchars( $IP ), 'browser' => htmlspecialchars( $browser ), 'country' => htmlspecialchars( $country_code ), 'country_full' => htmlspecialchars( $country_name ), 'date' => $date, 'store_name' => htmlspecialchars( $store_name ), 'store_img' => htmlspecialchars( $store_img ) );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF CHAT MESSAGES

*/

public static function have_chat_messages( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'text REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "chat" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE CLICKS

*/

public static function while_chat_messages( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '|', explode( ',', trim( $categories['search'] ) ) );
    $where[] = 'c.text REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );

  foreach( $order as $v ) {
    switch( $v ) {
      case 'date': $orderby[] = 'c.date'; break;
      case 'date desc': $orderby[] = 'c.date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT c.id, c.user, u.name, u.avatar, c.text, c.date FROM " . DB_TABLE_PREFIX . "chat c LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = c.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $user_name, $user_avatar, $text, $date );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'userID' => $user, 'user_name' => htmlspecialchars( $user_name ), 'user_avatar' => htmlspecialchars( $user_avatar ), 'text' => htmlspecialchars( $text ), 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF PLUGINS

*/

public static function have_plugins( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'languages': $where[] = 'scope = "language"'; break;
      case 'payment_gateways': $where[] = 'scope = "pay_gateway"'; break;
      case 'feed_servers': $where[] = 'scope = "feed_server"'; break;
      case 'applications': $where[] = 'scope = ""'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "plugins" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE PLUGINS

*/

public static function while_plugins( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'languages': $where[] = 'scope = "language"'; break;
      case 'payment_gateways': $where[] = 'scope = "pay_gateway"'; break;
      case 'feed_servers': $where[] = 'scope = "feed_server"'; break;
      case 'applications': $where[] = 'scope = ""'; break;
    }
  }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'name': $orderby[] = 'name'; break;
      case 'name desc': $orderby[] = 'name DESC'; break;
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, user, name, image, scope, main, options, menu, menu_ready, menu_icon, extend_vars, description, version, update_checker, uninstall, visible, date FROM " . DB_TABLE_PREFIX . "plugins" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $name, $image, $scope, $main_file, $options_file, $menu, $menu_ready, $menu_icon, $vars, $description, $version, $update_checker, $uninstall, $visible, $date );

  $data = array();
  while( $info = $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => htmlspecialchars( $name ), 'image' => htmlspecialchars( $image ), 'scope' => htmlspecialchars( $scope ), 'main_file' => htmlspecialchars( $main_file ), 'options_file' => htmlspecialchars( $options_file ), 'menu' => $menu, 'menu_ready' => $menu_ready, 'menu_icon' => $menu_icon, 'vars' => @unserialize( $vars ), 'description' => htmlspecialchars( $description ), 'update_checker' => htmlspecialchars( $update_checker ), 'version' => $version, 'uninstall_preview' => @unserialize( $uninstall ), 'visible' => $visible, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

}