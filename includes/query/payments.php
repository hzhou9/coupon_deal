<?php

namespace query;

/** */

class payments {

/*

GET NUMBER OF PAYMENT PLANS

*/

public static function plans( $categories = array() ) {
  return payments::have_plans( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF INVOICES

*/

public static function invoices( $categories = array() ) {
  return payments::have_invoices( $categories, array( 'only_count' => '' ) );
}

/*

GET PAYMENT STATISTICS

*/

public static function payments( $categories = array() ) {
  return payments::have_invoices( $categories, array( 'statistics' => '' ) );
}

/*

INSERT TRANSACTION / PAYMENT

*/

public static function inset_payment( $details = array() ) {

global $db;

  if( count( $details ) !== 9 ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("INSERT INTO " . DB_TABLE_PREFIX . "p_transactions (user, gateway, price, transaction_id, state, items, details, lastupdate_by, lastupdate, paid, delivered, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, NOW())");
  $stmt->bind_param( "isdssssiii", $details[0], $details[1], $details[2], $details[3], $details[4], $details[5], $details[6], $details[0], $details[7], $details[8] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/*

UPDATE TRANSACTION / PAYMENT

*/

public static function update_payment( $details = array() ) {

global $db;

  if( count( $details ) !== 5 ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("UPDATE " . DB_TABLE_PREFIX . "p_transactions SET state = ?, lastupdate_by = ?, lastupdate = NOW(), paid = ?, delivered = ? WHERE transaction_id = ?");
  $stmt->bind_param( "siiis", $details[0], $details[1], $details[2], $details[3], $details[4] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/*

CHECK IF A PAYMENT PLAN EXISTS

*/

public static function plan_exists( $id = 0, $special = array() ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;


  $where = array();

  if( isset( $special['user_view'] ) ) {
    $where[] = 'visible > 0';
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "p_plans WHERE id = ?" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', $where ) ) );
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

GET INFORMATIONS ABOUT A PAYMENT PLAN

*/

public static function plan_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = pp.user), name, description, price, credits, image, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = pp.lastupdate_by), lastupdate, visible, date FROM " . DB_TABLE_PREFIX . "p_plans pp WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $user_name, $name, $description, $price, $credits, $image, $lastupdate_by, $lastupdate_by_name, $last_update, $visible, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'user' => $user, 'user_name' => htmlspecialchars( $user_name ), 'name' => htmlspecialchars( $name ), 'price' => $price, 'price_format' => sprintf( PRICE_FORMAT, \site\utils::money_format( $price ) ), 'credits' => $credits, 'image' => htmlspecialchars( $image ), 'description' => htmlspecialchars( $description ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'visible' => $visible, 'date' => $date );

}

/*

CHECK IF AN INVOICE EXISTS

*/

public static function invoice_exists( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "p_transactions WHERE id = ?");
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

GET INFORMATIONS ABOUT AN INVOICE

*/

public static function invoice_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = t.user), gateway, price, transaction_id, state, items, details, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = t.lastupdate_by), lastupdate, paid, delivered, date FROM " . DB_TABLE_PREFIX . "p_transactions t WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $user_name, $gateway, $price, $transaction_id, $state, $items, $details, $lastupdate_by, $lastupdate_by_name, $last_update, $paid, $delivered, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'user' => $user, 'user_name' => htmlspecialchars( $user_name ), 'gateway' => htmlspecialchars( $gateway ), 'price' => $price, 'price_format' => sprintf( PRICE_FORMAT, \site\utils::money_format( $price ) ), 'transaction_id' => htmlspecialchars( $transaction_id ), 'state' => htmlspecialchars( $state ), 'items' => @unserialize( $items ), 'details' => htmlspecialchars( $details ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'paid' => $paid, 'delivered' => $delivered, 'date' => $date );

}

/*

NUMBER OF PAYMENT PLANS

*/

public static function have_plans( $category = array(), $special = array() ) {

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
    switch( $categories['show'] ) {
      case 'active':  $where[] = 'visible > 0'; break;
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "p_plans" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
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

WHILE THE PAYMANT PLANS

*/

public static function while_plans( $category = array() ) {

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
    switch( $categories['show'] ) {
      case 'active':  $where[] = 'visible > 0'; break;
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 'name'; break;
      case 'name desc': $orderby[] = 'name DESC'; break;
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
      case 'price': $orderby[] = 'price'; break;
      case 'price desc': $orderby[] = 'price DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, user, name, description, price, credits, image, visible, date FROM " . DB_TABLE_PREFIX . "p_plans" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $name, $description, $price, $credits, $image, $visible, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => htmlspecialchars( $name ), 'description' => htmlspecialchars( $description ), 'price' => $price, 'price_format' => sprintf( PRICE_FORMAT, \site\utils::money_format( $price ) ), 'credits' => $credits, 'image' => htmlspecialchars( $image ), 'visible' => $visible, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF INVOICES

*/

public static function have_invoices( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(gateway, transaction_id, details) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    switch( $categories['show'] ) {
      case 'paid':  $where[] = 'paid > 0'; break;
      case 'unpaid':  $where[] = 'paid = 0'; break;
      case 'delivered':  $where[] = 'delivered > 0'; break;
      case 'undelivered':  $where[] = 'delivered = 0'; break;
      case 'undeliveredpayments': $where[] = 'paid > 0 AND delivered = 0';
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
  $stmt->prepare( "SELECT COUNT(*), SUM(price) FROM " . DB_TABLE_PREFIX . "p_transactions" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count, $sum_inv );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }

  if( isset( $special['statistics'] ) ) {
    return array( 'count' => $count, 'sum' => $sum_inv );
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

WHILE THE TRANSACTIONS

*/

public static function while_invoices( $category = array() ) {

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
    $where[] = 'CONCAT(t.gateway, t.transaction_id, t.details) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    switch( $categories['show'] ) {
      case 'paid':  $where[] = 'paid > 0'; break;
      case 'unpaid':  $where[] = 'paid = 0'; break;
      case 'delivered':  $where[] = 'delivered > 0'; break;
      case 'undelivered':  $where[] = 'delivered = 0'; break;
      case 'undeliveredpayments': $where[] = 'paid > 0 AND delivered = 0';
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 't.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 't.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'date': $orderby[] = 't.date'; break;
      case 'date desc': $orderby[] = 't.date DESC'; break;
      case 'price': $orderby[] = 't.price'; break;
      case 'price desc': $orderby[] = 't.price DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT t.id, t.user, u.name, u.avatar, t.gateway, t.price, t.transaction_id, t.state, t.details, t.lastupdate, t.paid, t.delivered, t.date FROM " . DB_TABLE_PREFIX . "p_transactions t LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = t.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $user_name, $user_avatar, $gateway, $price, $transaction_id, $state, $details, $last_update, $paid, $delivered, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'user' => $user, 'user_name' => $user_name, 'user_avatar' => $user_avatar, 'gateway' => htmlspecialchars( $gateway ), 'price' => $price, 'price_format' => sprintf( PRICE_FORMAT, \site\utils::money_format( $price ) ), 'transaction_id' => htmlspecialchars( $transaction_id ), 'state' => htmlspecialchars( $state ), 'details' => htmlspecialchars( $details ), 'last_update' => $last_update, 'paid' => $paid, 'delivered' => $delivered, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

}