<?php

namespace plugin\CJApi\inc;

/** */

class import {

/*

CHECK IF A STORE HAS BEEN IMPORTED

*/

public static function store_imported( $id = 0 ) {

global $db;

$id = empty( $id ) ? $_GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*), id, category, name, image FROM " . DB_TABLE_PREFIX . "stores WHERE cjID = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count, $id, $cat, $name, $image );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return (object) array( 'ID' => $id, 'catID' => $cat, 'name' => htmlspecialchars( $name ), 'image' => $image );
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
  $stmt->prepare("SELECT COUNT(*), id, title FROM " . DB_TABLE_PREFIX . "coupons WHERE cjID = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count, $id, $title );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return (object) array( 'ID' => $id, 'title' => htmlspecialchars( $title ) );
  }

  return false;

}

}