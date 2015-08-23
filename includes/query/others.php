<?php

namespace query;

/** */

class others {

/*

GET NUMBER OF HEAD LINES

*/

public static function head_lines( $categories = array() ) {
  return payments::have_plans( $categories, array( 'only_count' => '' ) );
}

/*

/*

CHECK IF HEAD LINE EXISTS

*/

public static function head_line( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "head WHERE id = ?");
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

GET INFORMATIONS ABOUT A HEAD LINE

*/

public static function head_line_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, text, admin, theme, plugin, date FROM " . DB_TABLE_PREFIX . "head WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $text, $admin, $theme, $plugin, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'text' => $text, 'admin' => $admin, 'theme' => $theme, 'plugin' => $plugin, 'date' => $date );

}

/*

NUMBER OF HEAD LINES

*/

public static function have_head_lines( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(text, plugin) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    switch( $categories['show'] ) {
      case 'admin':  $where[] = 'admin > 0'; break;
      case 'theme':  $where[] = 'theme > 0'; break;
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "head" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
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

WHILE THE HEAD LINES

*/

public static function while_head_lines( $category = array() ) {

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
    $where[] = 'CONCAT(text, plugin) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    switch( $categories['show'] ) {
      case 'admin':  $where[] = 'admin > 0'; break;
      case 'theme':  $where[] = 'theme > 0'; break;
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
  $stmt->prepare( "SELECT id, text, admin, theme, plugin, date FROM " . DB_TABLE_PREFIX . "head" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $text, $admin, $theme, $plugin, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'text' => $text, 'admin' => $admin, 'theme' => $theme, 'plugin' => $plugin, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

}