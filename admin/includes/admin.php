<?php

class actions {


/* SET OPTION */

public static function set_option( $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "options SET option_value = ? WHERE option_name = ?" );

  foreach( $opt as $k => $v ) {

  $stmt->bind_param( "ss", $v, $k );
  $stmt->execute();

  $cache = new cache\main;
  $cache->update( 'options_' . $k, $v );

  }

  $stmt->close();

  return true;

}

/* ADD CATEGORY */

public static function add_category( $opt = array() ) {

global $db;

if( !ab_to( array( 'categories' => 'add' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }
  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "categories (subcategory, user, name, description, meta_title, meta_desc, date) VALUES (?, ?, ?, ?, ?, ?, NOW())" );
  $stmt->bind_param( "iissss", $opt['category'], $GLOBALS['me']->ID, $opt['name'], $opt['description'], $opt['meta_title'], $opt['meta_desc'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT CATEGORY */

public static function edit_category( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'categories' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "categories SET subcategory = ?, name = ?, description = ?, meta_title = ?, meta_desc = ? WHERE id = ?" );
  $stmt->bind_param( "issssi", $opt['category'], $opt['name'], $opt['description'], $opt['meta_title'], $opt['meta_desc'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* DELETE CATEGORY */

public static function delete_category( $id ) {

global $db;

if( !ab_to( array( 'categories' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "categories WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* ADD STORE */

public static function add_store( $opt = array() ) {

global $db;

// if( !ab_to( array( 'stores' => 'add' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) || empty( $opt['url'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (feedID, user, category, popular, name, link, description, tags, image, visible, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $feedID = isset( $opt['feedID'] ) ? $opt['feedID'] : 0;
  $logo = \site\images::upload( ( !empty( $opt['import_logo'] ) && !empty( $opt['logo_url'] ) && empty( $_FILES['logo']['name'] ) ? $opt['logo_url'] : @$_FILES['logo'] ), 'logo_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 600, 'max_height' => 400, 'current' => ( !empty( $opt['logo_url'] ) ? $opt['logo_url'] : '' ) ) );

  $stmt->bind_param( "iiiisssssissi", $feedID, $opt['user'], $opt['category'], $opt['popular'], $opt['name'], $opt['url'], $opt['description'], $opt['tags'], $logo, $opt['publish'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID );
  $execute = $stmt->execute();

  if( $execute ) {

  $stmt->prepare( "SELECT LAST_INSERT_ID() FROM " . DB_TABLE_PREFIX . "stores" );
  $stmt->execute();
  $stmt->bind_result( $id );
  $stmt->fetch();
  $stmt->close();

  return $id;

  }

  $stmt->close();

  return false;

}

/* IMPORT STORES */

public static function import_stores( $opt = array() ) {

global $db;

if( !ab_to( array( 'stores' => 'import' ) ) ) return false;

  $opt = \site\utils::array_map_recursive( 'trim', $opt );

  if( empty( $opt['file'] ) || !\site\utils::file_has_extension( $opt['file']['name'], '.csv' ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (user, category, name, link, description, tags, image, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $cat = !empty( $opt['category'] ) ? $opt['category'] : 0;

  $success = $error = $line = 0;

  if ( ( $handle = fopen( $opt['file']['tmp_name'], 'r' ) ) !== false ) {

    while( ( $data = fgetcsv( $handle, 3000, ',' ) ) !== false ) {

    if( $line === 0 && $opt['omit_first_line'] ) {
      $line++;
      continue;
    }

    /*

    If store URL isn't valid, omit that row.

    */

    if( empty( $data[0] ) || count( $data ) < 5 ) {
      $error++;
      continue;
    }

      $stmt2 = $db->stmt_init();
      $stmt2->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE link = ?");
      $stmt2->bind_param( "s", $data[1] );
      $stmt2->execute();
      $stmt2->bind_result( $count );
      $stmt2->fetch();
      $stmt2->close();

      if( $count > 0 ) {
        $error++;
        continue;
      }

      $stmt->bind_param( "iisssssi", $GLOBALS['me']->ID, $cat, $data[0], $data[1], $data[2], $data[3], $data[4], $GLOBALS['me']->ID );
      $execute = $stmt->execute();

      if( !$execute ) {
        $error++;
      } else {
        $success++;
      }

    }

    fclose( $handle );

  }

  @$stmt->close();

  return array( $success, $error );

}

/* EDIT STORE */

public static function edit_store( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) || empty( $opt['url'] ) ) {
    return false;
  }

  $store = \query\main::store_infos( $id );

  $logo = \site\images::upload( @$_FILES['logo'], 'logo_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 600, 'max_height' => 400, 'current' => $store->image ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET user = ?, category = ?, popular = ?, name = ?, link = ?, description = ?, tags = ?, image = ?, visible = ?, meta_title = ?, meta_desc = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
  $stmt->bind_param( "iiisssssissii", $opt['user'], $opt['category'], $opt['popular'], $opt['name'], $opt['url'], $opt['description'], $opt['tags'], $logo, $opt['publish'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* SET ACTION TO STORE */

public static function action_store( $action, $id ) {

global $db;

if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'publish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET visible = 1 WHERE id = ?" );
    break;

    case 'unpublish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET visible = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE STORE */

public static function delete_store( $id ) {

global $db;

if( !ab_to( array( 'stores' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  foreach( $id as $ID ) {

  if( \query\main::store_exists( $ID ) ) {

  $store = \query\main::store_infos( $ID );

  // delete the store
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "stores WHERE id = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  // remove coupons of this store
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "coupons WHERE store = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  // remove this store from favorites
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "favorite WHERE store = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  // remove reviews for this store
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "reviews WHERE store = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $store->image ) ) {
    @unlink( DIR . '/' . $store->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* DELETE STORE IMAGE */

public static function delete_store_image( $id ) {

global $db;

if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  foreach( $id as $ID ) {

  if( \query\main::store_exists( $ID ) ) {

  $store = \query\main::store_infos( $ID );

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET image = '' WHERE id = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $store->image ) ) {
    @unlink( DIR . '/' . $store->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* MOVE STORE */

public static function change_store_category( $id, $newcat ) {

global $db;

if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET category = ? WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "ii", $newcat, $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* ADD COUPON */

public static function add_item( $opt = array() ) {

global $db;

// if( !ab_to( array( 'coupons' => 'add' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (feedID, user, store, category, popular, exclusive, title, link, description, tags, code, visible, start, expiration, cashback, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $feedID = isset( $opt['feedID'] ) ? $opt['feedID'] : 0;

  $stmt->bind_param( "iiiiiisssssississi", $feedID, $GLOBALS['me']->ID, $opt['store'], $opt['category'], $opt['popular'], $opt['exclusive'], $opt['name'],  $opt['link'], $opt['description'], $opt['tags'], $opt['code'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* IMPORT COUPONS */

public static function import_items( $opt = array() ) {

global $db;

if( !ab_to( array( 'coupons' => 'import' ) ) ) return false;

  $opt = \site\utils::array_map_recursive( 'trim', $opt );

  if( empty( $opt['file'] ) || !\site\utils::file_has_extension( $opt['file']['name'], '.csv' ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (user, store, category, title, link, description, tags, code, start, expiration, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $cat = !empty( $opt['category'] ) ? $opt['category'] : 0;

  $success = $error = $line = 0;

  if ( ( $handle = fopen( $opt['file']['tmp_name'], 'r' ) ) !== false ) {

    while( ( $data = fgetcsv( $handle, 3000, ',' ) ) !== false ) {

    if( $line === 0 && $opt['omit_first_line'] ) {
      $line++;
      continue;
    }

    /*

    If store URL isn't valid, omit that row.

    */

    if( empty( $data[0] ) || count( $data ) < 8 ) {
      $error++;
      continue;
    }

      $stmt2 = $db->stmt_init();
      $stmt2->prepare("SELECT COUNT(*), id, category FROM " . DB_TABLE_PREFIX . "stores WHERE link = ?");
      $stmt2->bind_param( "s", $data[7] );
      $stmt2->execute();
      $stmt2->bind_result( $count, $store, $store_cat );
      $stmt2->fetch();
      $stmt2->close();

      if( $count === 0 ) {
        $error++;
        continue;
      }

      if( $cat === 0 ) {
        $cat = $store_cat;
      }

      $stmt->bind_param( "iiisssssssi", $GLOBALS['me']->ID, $store, $cat, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $GLOBALS['me']->ID );
      $execute = $stmt->execute();

      if( !$execute ) {
        $error++;
      } else {
        $success++;
      }

    }

    fclose( $handle );

  }

  @$stmt->close();

  return array( $success, $error );

}

/* EDIT COUPON */

public static function edit_item( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET store = ?, category = ?, popular = ?, exclusive = ?, title = ?, link = ?, description = ?, tags = ?, code = ?, visible = ?, start = ?, expiration = ?, cashback = ?, meta_title = ?, meta_desc = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
  $stmt->bind_param( "iiiisssssississsi", $opt['store'], $opt['category'], $opt['popular'], $opt['exclusive'], $opt['name'],  $opt['link'], $opt['description'], $opt['tags'], $opt['code'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT COUPON - LIMITED INFORMATIONS */

public static function edit_item2( $id, $opt = array() ) {

global $db;

// if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET title = ?, link = ?, description = ?, tags = ?, code = ?, start = ?, expiration = ?, lastupdate_by = 0, lastupdate = NOW() WHERE id = ?" );
  $stmt->bind_param( "sssssssi", $opt['name'], $opt['link'], $opt['description'], $opt['tags'], $opt['code'], $opt['start'], $opt['end'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* SET ACTION TO COUPON */

public static function action_item( $action, $id ) {

global $db;

if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'publish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET visible = 1 WHERE id = ?" );
    break;

    case 'unpublish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET visible = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE COUPON */

public static function delete_item( $id ) {

global $db;

if( !ab_to( array( 'coupons' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "coupons WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* ADD PRODUCT */

public static function add_product( $opt = array() ) {

global $db;

// if( !ab_to( array( 'products' => 'add' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "products (feedID, user, store, category, popular, title, link, description, tags, image, price, old_price, currency, visible, start, expiration, cashback, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $feedID = isset( $opt['feedID'] ) ? $opt['feedID'] : 0;
  $image = \site\images::upload( @$_FILES['image'], 'product_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 1000, 'max_height' => 1000, 'current' => '' ) );
  $opt['price'] = \site\utils::make_money_format( $opt['price'] );
  $opt['old_price'] = \site\utils::make_money_format( $opt['old_price'] );

  $stmt->bind_param( "iiiiisssssddsississi", $feedID, $GLOBALS['me']->ID, $opt['store'], $opt['category'], $opt['popular'], $opt['name'],  $opt['link'], $opt['description'], $opt['tags'], $image, $opt['price'], $opt['old_price'], $opt['currency'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* IMPORT PRODUCTS */

public static function import_products( $opt = array() ) {

global $db;

if( !ab_to( array( 'products' => 'import' ) ) ) return false;

  $opt = \site\utils::array_map_recursive( 'trim', $opt );

  if( empty( $opt['file'] ) || !\site\utils::file_has_extension( $opt['file']['name'], '.csv' ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "products (user, store, category, title, link, description, tags, image, price, old_price, currency, start, expiration, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $cat = !empty( $opt['category'] ) ? $opt['category'] : 0;

  $success = $error = $line = 0;

  if ( ( $handle = fopen( $opt['file']['tmp_name'], 'r' ) ) !== false ) {

    while( ( $data = fgetcsv( $handle, 3000, ',' ) ) !== false ) {

    if( $line === 0 && $opt['omit_first_line'] ) {
      $line++;
      continue;
    }

    /*

    If store URL isn't valid, omit that row.

    */

    if( empty( $data[0] ) || count( $data ) < 11 ) {
      $error++;
      continue;
    }

      $stmt2 = $db->stmt_init();
      $stmt2->prepare("SELECT COUNT(*), id, category FROM " . DB_TABLE_PREFIX . "stores WHERE link = ?");
      $stmt2->bind_param( "s", $data[10] );
      $stmt2->execute();
      $stmt2->bind_result( $count, $store, $store_cat );
      $stmt2->fetch();
      $stmt2->close();

      if( $count === 0 ) {
        $error++;
        continue;
      }

      if( $cat === 0 ) {
        $cat = $store_cat;
      }

      $stmt->bind_param( "iiisssssddsssi", $GLOBALS['me']->ID, $store, $cat, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $GLOBALS['me']->ID );
      $execute = $stmt->execute();

      if( !$execute ) {
        $error++;
      } else {
        $success++;
      }

    }

    fclose( $handle );

  }

  @$stmt->close();

  return array( $success, $error );

}

/* EDIT PRODUCT */

public static function edit_product( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'products' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $product = \query\main::product_infos( $id );

  $image = \site\images::upload( @$_FILES['image'], 'product_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 1000, 'max_height' => 1000, 'current' => $product->image ) );
  $opt['price'] = \site\utils::make_money_format( $opt['price'] );
  $opt['old_price'] = \site\utils::make_money_format( $opt['old_price'] );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET store = ?, category = ?, popular = ?, title = ?, link = ?, description = ?, tags = ?, image = ?, price = ?, old_price = ?, currency = ?, visible = ?, start = ?, expiration = ?, cashback = ?, meta_title = ?, meta_desc = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
  $stmt->bind_param( "iiisssssddsississii", $opt['store'], $opt['category'], $opt['popular'], $opt['name'],  $opt['link'], $opt['description'], $opt['tags'], $image, $opt['price'], $opt['old_price'], $opt['currency'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* SET ACTION TO PRODUCT */

public static function action_product( $action, $id ) {

global $db;

if( !ab_to( array( 'products' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'publish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET visible = 1 WHERE id = ?" );
    break;

    case 'unpublish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET visible = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE PRODUCT */

public static function delete_product( $id ) {

global $db;

if( !ab_to( array( 'products' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "products WHERE id = ?" );

  foreach( $id as $ID ) {

  $product = \query\main::product_infos( $ID );

  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $product->image ) ) {
    @unlink( DIR . '/' . $product->image );
  }

  }

  @$stmt->close();

  return true;

}

/* DELETE PRODUCT IMAGE */

public static function delete_product_image( $id ) {

global $db;

if( !ab_to( array( 'products' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  foreach( $id as $ID ) {

  if( \query\main::product_exists( $ID ) ) {

  $product = \query\main::product_infos( $ID );

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET image = '' WHERE id = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $product->image ) ) {
    @unlink( DIR . '/' . $product->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* ADD PAGE */

public static function add_page( $opt = array() ) {

global $db;

if( !ab_to( array( 'pages' => 'add' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "pages (user, name, text, visible, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );
  $stmt->bind_param( "ississi", $GLOBALS['me']->ID, $opt['name'], $opt['text'], $opt['publish'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT PAGE */

public static function edit_page( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'pages' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET name = ?, text = ?, visible = ?, meta_title = ?, meta_desc = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
  $stmt->bind_param( "ssissii", $opt['name'],  $opt['text'], $opt['publish'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* SET ACTION TO A PAGE */

public static function action_page( $action, $id ) {

global $db;

if( !ab_to( array( 'pages' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'publish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET visible = 1 WHERE id = ?" );
    break;

    case 'unpublish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET visible = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE PAGE */

public static function delete_page( $id ) {

global $db;

if( !ab_to( array( 'pages' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "pages WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* ADD USER */

public static function add_user( $opt = array() ) {

global $db, $LANG;

if( !ab_to( array( 'users' => 'add' ) ) ) return false;

  $opt = \site\utils::array_map_recursive( 'trim', $opt );

  if( empty( $opt['name'] ) || empty( $opt['email'] ) || empty( $opt['password'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();

  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "users (name, email, password, avatar, points, credits, privileges, erole, subscriber, valid, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())" );

  $avatar = \site\images::upload( @$_FILES['logo'], 'avatar_', array(  'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 500, 'max_height' => 600, 'current' => '' ) );
  $password = md5( $opt['password'] );

  $stmt->bind_param( "ssssiiisii", $opt['name'], $opt['email'], $password, $avatar, $opt['points'], $opt['credits'], $opt['privileges'], @serialize( $opt['erole'] ), $opt['subscriber'], $opt['confirm'] );

  if( $stmt->execute() ) {

  if( !$opt['confirm'] ) {

  $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "users WHERE email = ?" );
  $stmt->bind_param( "s", $opt['email'] );
  $stmt->execute();
  $stmt->bind_result( $id );
  $stmt->fetch();
  $stmt->close();

  $cofirm_session = md5( \site\utils::str_random(15) );

  if( \user\mail_sessions::insert( 'confirmation', array( 'user' => $id, 'session' => $cofirm_session ) ) ) {
    \site\mail::send( $opt['email'], $LANG['email_acc_title'] . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'account_confirmation', 'path' => '../' ), array( 'hello_name' => sprintf( $LANG['email_text_hello'], $opt['name'] ), 'confirmation_main_text' => $LANG['email_acc_maintext'], 'confirmation_button' => $LANG['email_acc_button'], 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'user' => $id, 'token' => $cofirm_session ) ) ) );
  }

  }

    return true;

  }

  $stmt->close();

  return false;

}

/* EDIT USER */

public static function edit_user( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

  $opt = \site\utils::array_map_recursive( 'trim', $opt );

  if( empty( $opt['name'] ) || empty( $opt['email'] ) ) {
    return false;
  }

  $user = \query\main::user_infos( $id );

  $avatar = \site\images::upload( @$_FILES['logo'], 'avatar_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 500, 'max_height' => 600, 'current' => $user->avatar ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET name = ?, email = ?, avatar = ?, points = ?, credits = ?, privileges = ?, erole = ?, subscriber = ?, valid = ? WHERE id = ?" );
  $stmt->bind_param( "sssiiisiii", $opt['name'], $opt['email'], $avatar, $opt['points'], $opt['credits'], $opt['privileges'], @serialize( $opt['erole'] ), $opt['subscriber'], $opt['confirm'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* SET ACTION TO USER */

public static function action_user( $action, $id ) {

global $db;

if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'verify':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET valid = 1 WHERE id = ?" );
    break;

    case 'unverify':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET valid = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* CHANGE USER PASSWORD */

public static function change_user_password( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['password'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET password = ? WHERE id = ?" );

  $pass = md5( $opt['password'] );

  $stmt->bind_param( "si", $pass, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* BAN USER */

public static function ban_user( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'users' => 'ban' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['date'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET ban = FROM_UNIXTIME(?) WHERE id = ?" );
  $stmt->bind_param( "si", $opt['date'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* DELETE USER */

public static function delete_user( $id ) {

global $db;

if( !ab_to( array( 'users' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  foreach( $id as $ID ) {

  if( \query\main::user_exists( $ID ) ) {

  $user = \query\main::user_infos( $ID );

  // don't delete administrators
  if( !$user->is_admin ) {

  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "users WHERE id = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  // delete his session
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE user = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  // clear his favorites
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "favorite WHERE user = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $user->avatar ) ) {
    @unlink( DIR . '/' . $user->avatar );
  }

  }

  }

  }

  @$stmt->close();

  return true;

}

/* DELETE USER AVATAR */

public static function delete_user_avatar( $id ) {

global $db;

if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  foreach( $id as $ID ) {

  if( \query\main::user_exists( $ID ) ) {

  $user = \query\main::user_infos( $ID );

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET avatar = '' WHERE id = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $user->avatar ) ) {
    @unlink( DIR . '/' . $user->avatar );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* ADD WIDGET */

public static function add_widget( $zone, $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "widgets (user, theme, widget_id, sidebar, location, title, stop, text, last_update, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $myid = $GLOBALS['me']->ID;
  $theme = \query\main::get_option( 'theme' );

  $stmt->bind_param( "isisssis", $myid, $theme, $id, $zone, $opt['file'], $opt['title'], $opt['limit'], $opt['text'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT WIDGET */

public static function edit_widget( $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "widgets SET title = ?, stop = ?, type = ?, orderby = ?, position = ?, text = ?, html = ?, mobile_view = ?, last_update = NOW() WHERE id = ?" );
  $stmt->bind_param( "sissisiii", $opt['title'], $opt['limit'], $opt['type'], $opt['order'], $opt['position'], $opt['text'], $opt['allow_html'], $opt['mobi_view'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* DELETE WIDGET */

public static function delete_widget( $zone, $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "widgets WHERE id = ? AND theme = ? AND sidebar = ?" );

  $theme = \query\main::get_option( 'theme' );

  $stmt->bind_param( "iss",  $id, $theme, $zone);
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return true;

}

/* SET SUGGESTION AS READ */

public static function action_suggestions( $action, $id ) {

global $db;

if( !ab_to( array( 'suggestions' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'read':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "suggestions SET viewed = 1 WHERE id = ?" );
    break;

    case 'unread':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "suggestions SET viewed = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE SUGGESTION */

public static function delete_suggestion( $id ) {

global $db;

if( !ab_to( array( 'suggestions' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "suggestions WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* ADD REVIEW */

public static function add_review( $opt = array() ) {

global $db;

if( !ab_to( array( 'reviews' => 'add' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['text'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "reviews (user, store, text, stars, valid, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())" );
  $stmt->bind_param( "iisiii", $opt['user'], $opt['store'], $opt['text'], $opt['stars'], $opt['publish'], $GLOBALS['me']->ID );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT REVIEW */

public static function edit_review( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'reviews' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['text'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "reviews SET user = ?, store = ?, text = ?, stars = ?, valid = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
  $stmt->bind_param( "iisiiii", $opt['user'], $opt['store'], $opt['text'], $opt['stars'], $opt['publish'], $GLOBALS['me']->ID, $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* SET ACTION TO REVIEW */

public static function action_review( $action, $id ) {

global $db;

if( !ab_to( array( 'reviews' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'publish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "reviews SET valid = 1 WHERE id = ?" );
    break;

    case 'unpublish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "reviews SET valid = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE REVIEW */

public static function delete_review( $id ) {

global $db;

if( !ab_to( array( 'reviews' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "reviews WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* EDIT A PAGE IN THEME */

public static function edit_theme_page( $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  if( file_exists( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'] ) ) {
    if( !@chmod( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'], 0777 ) ) {
      return false;
    }

    if( @file_put_contents( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'], $opt['text'] ) ) {
      @chmod( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'], 0644 );
      return true;
    }
  }

  return false;

}

/* EXTRACT THEME */

public static function extract_theme( $theme = '', $location = '' ) {

global $LANG;

if( !$GLOBALS['me']->is_admin ) return false;

  if( \site\utils::get_extension( basename( $theme ) ) !== '.zip' ) {
    throw new Exception( $LANG['themes_only_zip'] );
  }

  if( empty( $location ) ) {

  if( !$file = @file_put_contents( ( $temploc = DIR . '/' . TEMP_LOCATION . '/theme-' . time() . '.zip' ), file_get_contents( $theme )) ) {
    throw new Exception( $LANG['themes_wrongurl'] );
  }

  $location = $uplocation = $temploc;

  }

  $zip = new ZipArchive;

  if ( $zip->open( $location ) ) {

    $files_map['tfiles'] = $files_map['main_dirs'] = array();

    for( $i = 0; $i < $zip->numFiles; $i++ ) {
      if( preg_match( '/^([^\/]*)\/$/', $zip->getNameIndex( $i ) ) )
      $files_map['main_dirs'][] = $zip->getNameIndex( $i );
      else
      $files_map['tfiles'][] = $zip->getNameIndex( $i );
    }

    if( count( $files_map['main_dirs'] ) === 0 ) {
      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( 'directory missing' );
    }

    if( count( $files_map['main_dirs'] ) > 1 ) {
      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( 'too many directories' );
    }

    if( is_dir( DIR . '/' . THEMES_LOC .'/' . $files_map['main_dirs'][0] ) ) {
      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( sprintf( $LANG['themes_theme_exists'], rtrim( $files_map['main_dirs'][0], '/' ) ) );
    }

    // all files inside theme
    $tfiles = array();
    foreach( $files_map['tfiles'] as $file ) {
      if( preg_match( '/^([^\/]*)\//', $file ) )
      $tfiles[] = $file;
    }

    if( !template::theme_have_min( array_map( 'basename', $tfiles ) ) ) {
      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( $LANG['msg_invalid_theme'] );
    }

    $extract = $zip->extractTo( DIR . '/' . THEMES_LOC, array_merge( $files_map['main_dirs'], $tfiles ) );

    $zip->close();

    if( !$extract ) {
      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( $LANG['themes_extracting_error'] );
    }

  } else {

    // delete the temporary file
    if( isset( $uplocation ) ) @unlink( $uplocation );
    throw new Exception( $LANG['themes_cantunzip'] );

  }

  if( isset( $uplocation ) ) @unlink( $uplocation );

  return true;

}

/* DELETE THEME */

public static function delete_theme( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  foreach( $id as $ID ) {

    if( \query\main::get_option( 'theme' ) !== $ID )
    \site\files::delete_directory( DIR . '/' . THEMES_LOC . '/' . $ID );

  }

  return true;

}

/* EDIT A PAGE IN A PLUGIN */

public static function edit_plugin_page( $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  $page = DIR . '/' . UPDIR . '/' . $id . '/' . $opt['page'];

  if( file_exists( $page ) ) {
    if( !is_writable( $page ) && !@chmod( $page, 0777 ) ) {
      return false;
    }

    if( @file_put_contents( $page, $opt['text'] ) ) {
      @chmod( $page, 0644 );
      return true;
    }
  }

  return false;

}

/* EXTRACT PLUGIN */

public static function extract_plugin( $plugin = '', $location = '' ) {

global $LANG;

if( !$GLOBALS['me']->is_admin ) return false;

  if( \site\utils::get_extension( basename( $plugin ) ) !== '.zip' ) {
    throw new Exception( $LANG['plugins_only_zip'] );
  }

  if( empty( $location ) ) {

  if( !$file = @file_put_contents( ( $temploc = DIR . '/' . TEMP_LOCATION . '/plugin-' . time() . '.zip' ), file_get_contents( $plugin )) ) {
    throw new Exception( $LANG['plugins_wrongurl'] );
  }

  $location = $uplocation = $temploc;

  }

  $zip = new ZipArchive;

  if ( $zip->open( $location ) ) {

    $files_map['pfiles'] = $files_map['main_dirs'] = array();

    for( $i = 0; $i < $zip->numFiles; $i++ ) {
      if( preg_match( '/^([^\/]*)\/$/', $zip->getNameIndex( $i ) ) )
      $files_map['main_dirs'][] = $zip->getNameIndex( $i );
      else
      $files_map['pfiles'][] = $zip->getNameIndex( $i );
    }

    if( count( $files_map['main_dirs'] ) === 0 ) {
      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( $LANG['plugins_err_dirmiss'] );
    }

    if( count( $files_map['main_dirs'] ) > 1 ) {
      // delete the temporary file
        var_dump($files_map);
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( $LANG['plugins_err_manydirs'] );
    }

    if( is_dir( DIR . '/' . UPDIR .'/' . $files_map['main_dirs'][0] ) ) {
      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( sprintf( $LANG['plugins_plugin_exists'], rtrim( $files_map['main_dirs'][0], '/' ) ) );
    }

    // all files inside plugin
    $pfiles = array();
    foreach( $files_map['pfiles'] as $file ) {
      if( preg_match( '/^([^\/]*)\//', $file ) )
      $pfiles[] = $file;
    }

    $extract = $zip->extractTo( DIR . '/' . UPDIR, array_merge( $files_map['main_dirs'], $pfiles ) );

    $zip->close();

    if( !$extract ) {

      // delete the temporary file
      if( isset( $uplocation ) ) @unlink( $uplocation );
      throw new Exception( $LANG['themes_extracting_error'] );

    } else {

      /*

      Without errors until installation,
      Then try to install it.

      */

      require_once 'includes/plugin_installer.php';

      try {
        $install = (new plugin_installer( $files_map['main_dirs'][0] ))->install();
        if( isset( $uplocation ) ) @unlink( $uplocation );
      }

      catch( Exception $e ){
        // delete the temporary files
        if( isset( $uplocation ) ) @unlink( $uplocation );
        \site\files::delete_directory( DIR . '/' . UPDIR . '/' . $files_map['main_dirs'][0] );
        throw new Exception( $e->getMessage() );
      }

    }

  } else {

    // delete the temporary file
    if( isset( $uplocation ) ) @unlink( $uplocation );
    throw new Exception( $LANG['themes_cantunzip'] );

  }

  if( isset( $uplocation ) ) @unlink( $uplocation );

  return true;

}

/* EDIT PLUGIN */

public static function edit_plugin( $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  $plugin = admin_query::plugin_infos( $id );

  $image = \site\images::upload( @$_FILES['image'], 'plugin_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 600, 'max_height' => 400, 'current' => $plugin->image ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET image = ?, menu = ?, menu_icon = ?, description = ?, visible = ? WHERE id = ?" );
  $stmt->bind_param( "siisii", $image, $opt['menu'], $opt['icon'],  $opt['description'], $opt['publish'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* SET ACTION TO PLUGIN */

public static function action_plugin( $action, $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'publish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET visible = 1 WHERE id = ?" );
    break;

    case 'unpublish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET visible = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE PLUGIN */

public static function delete_plugin( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "plugins WHERE id = ?" );

  foreach( $id as $ID ) {

  $plugin = admin_query::plugin_infos( $ID );

  // delete plugin
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  // directory
  $dir = rtrim( dirname( $plugin->main_file ), '/' );

  // delete tables
  if( isset( $plugin->uninstall_preview['delete']['tables'] ) ) {
    $tables = explode( ',', $plugin->uninstall_preview['delete']['tables'] );
    foreach( array_map( 'trim', $tables ) as $table ) {
      $table = \site\plugin::replace_constant( $table );
      $db->query( "DROP TABLE `{$table}`" );
    }
  }

  // delete options
  if( isset( $plugin->uninstall_preview['delete']['options'] ) ) {
    $rows = explode( ',', $plugin->uninstall_preview['delete']['options'] );
    foreach( array_map( 'trim', $rows ) as $row ) {
      $db->query( "DELETE FROM `" . DB_TABLE_PREFIX . "options` WHERE `option_name` = '{$row}'" );
    }
  }

  // delete table columns
  if( isset( $plugin->uninstall_preview['delete']['columns'] ) ) {
    $columns = explode( ',', $plugin->uninstall_preview['delete']['columns'] );
    foreach( array_map( 'trim', $columns ) as $column ) {
      $coltab = explode( '/', $column );
      if( count( $coltab ) === 2 ) {
        $table = \site\plugin::replace_constant( $coltab[1] );
        $db->query( "ALTER TABLE `{$table}` DROP {$coltab[0]}" );
      }
    }
  }

  // delete head lines
  $db->query( "DELETE FROM `" . DB_TABLE_PREFIX . "head` WHERE `plugin` = '{$dir}'" );

  /*

  Resolve possible problems caused by uninstalling

  */

  switch( $plugin->scope ) {
    case 'language':
    if( \query\main::get_option( 'sitelang' ) == 'up_' . strtolower( $plugin->name ) ) {
      actions::set_option( array( 'sitelang' => 'english' ) );
    }
    if( \query\main::get_option( 'adminpanel_lang' ) == 'up_' . strtolower( $plugin->name ) ) {
      actions::set_option( array( 'adminpanel_lang' => 'english' ) );
    }
    break;
   }


  // delete plugin directory
  \site\files::delete_directory( DIR . '/' . UPDIR . '/' . $dir );

  // delete image, if plugins has an image
  @unlink( DIR . '/' . $plugin->image );

  }

  @$stmt->close();

  return true;

}

/* DELETE PLUGIN IMAGE */

public static function delete_plugin_image( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  foreach( $id as $ID ) {

  if( admin_query::plugin_exists( $ID ) ) {

  $plugin = admin_query::plugin_infos( $ID );

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET image = '' WHERE id = ?" );
  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $plugin->image ) ) {
    @unlink( DIR . '/' . $plugin->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* DELETE BANNED IP */

public static function delete_banned( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "banned WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* ADD BANNED IP */

public static function add_banned( $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['ipaddr'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "banned (ipaddr, registration, login, site, redirect_to, expiration, expiration_date, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())" );
  $stmt->bind_param( "siiisss", $opt['ipaddr'], $opt['registration'], $opt['login'], $opt['site'], $opt['redirect'], $opt['expiration'], $opt['expiration_date'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT BANNED IP */

public static function edit_banned( $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['ipaddr'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "banned SET ipaddr = ?, registration = ?, login = ?, site = ?, redirect_to = ?, expiration = ?, expiration_date = ? WHERE id = ?" );
  $stmt->bind_param( "siiisssi", $opt['ipaddr'], $opt['registration'], $opt['login'], $opt['site'], $opt['redirect'], $opt['expiration'], $opt['expiration_date'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* DELETE NEWS */

public static function delete_news( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "news WHERE newsID = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* DELETE USER SESSIONS */

public static function delete_sessions( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* EDIT SUBSCRIBER */

public static function edit_subscriber( $id, $opt = array() ) {

global $db;

if( !ab_to( array( 'subscribers' => 'edit' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  if( !filter_var( $opt['email'], FILTER_VALIDATE_EMAIL ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET email = ?, econf = ? WHERE id = ?" );
  $stmt->bind_param( "sii", $opt['email'], $opt['confirm'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* IMPORT SUBSCRIBERS */

public static function import_subscribers( $opt = array() ) {

global $db;

if( !ab_to( array( 'subscribers' => 'import' ) ) ) return false;

  $opt = array_map( 'trim', $opt );

  preg_match_all( '/([a-z0-9-_.]+)\@([a-z0-9-_]+)\.([a-z]+)/i', $opt['emails'], $email );

  $emails = array_map( 'strtolower', $email[0] );

  if( empty( $emails ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "newsletter (email, econf, date) VALUES (?, ?, NOW())" );

  foreach( $emails as $email ) {
  $stmt->bind_param( "si", $email, $opt['confirm'] );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* SET ACTION TO SUBSCRIBER */

public static function action_subscriber( $action, $id ) {

global $db;

if( !ab_to( array( 'subscribers' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'verify':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 1 WHERE id = ?" );
    break;

    case 'unverify':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE SUBSCRIBER */

public static function delete_subscriber( $id ) {

global $db;

if( !ab_to( array( 'subscribers' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* ADD REWARD */

public static function add_reward( $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( function( $w ) {
    if( !is_array( $w ) ) return trim( $w );
    return $w;
  }, $opt );

  if( empty( $opt['name'] ) || $opt['points'] <= 0 ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "rewards (user, points, title, description, image, fields, lastupdate_by, lastupdate, visible, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

  $image = \site\images::upload( @$_FILES['logo'], 'reward_', array(  'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 500, 'max_height' => 600, 'current' => '' ) );

  $fields = array();
  for( $i = 0; $i < count( $opt['fields']['name'] ); $i++ ) {
    if( !empty( $opt['fields']['name'][$i] ) )
    $fields[] = array( 'name' => $opt['fields']['name'][$i], 'type' => $opt['fields']['type'][$i], 'value' => $opt['fields']['value'][$i], 'require' => ( isset( $opt['fields']['require'][$i] ) && in_array( $opt['fields']['require'][$i], array( 1, 2 ) ) ? $opt['fields']['require'][$i] : 0 ) );
  }

  $fields = @serialize( $fields );

  $stmt->bind_param( "iissssii", $GLOBALS['me']->ID, $opt['points'], $opt['name'], $opt['description'], $image, $fields, $GLOBALS['me']->ID, $opt['publish'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT REWARD */

public static function edit_reward( $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( function( $w ) {
    if( !is_array( $w ) ) return trim( $w );
    return $w;
  }, $opt );

  if( empty( $opt['name'] ) || $opt['points'] <= 0 ) {
    return false;
  }

  $reward = \query\main::reward_infos( $id );

  $avatar = \site\images::upload( @$_FILES['logo'], 'reward_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 500, 'max_height' => 600, 'current' => $reward->image ) );

  $fields = array();
  for( $i = 0; $i < count( $opt['fields']['name'] ); $i++ ) {
    if( !empty( $opt['fields']['name'][$i] ) )
    $fields[] = array( 'name' => $opt['fields']['name'][$i], 'type' => $opt['fields']['type'][$i], 'value' => $opt['fields']['value'][$i], 'require' => ( isset( $opt['fields']['require'][$i] ) && in_array( $opt['fields']['require'][$i], array( 1, 2 ) ) ? $opt['fields']['require'][$i] : 0 ) );
  }

  $fields = @serialize( $fields );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards SET points = ?, title = ?, description = ?, image = ?, fields = ?, lastupdate_by = ?, lastupdate = NOW(), visible = ? WHERE id = ?" );
  $stmt->bind_param( "issssiii", $opt['points'], $opt['name'], $opt['description'], $avatar, $fields, $GLOBALS['me']->ID, $opt['publish'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* DELETE REWARD */

public static function delete_reward( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "rewards WHERE id = ?" );

  foreach( $id as $ID ) {

  if( \query\main::reward_exists( $ID ) ) {

  $reward = \query\main::reward_infos( $ID );

  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $reward->image ) ) {
    @unlink( DIR . '/' . $reward->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* DELETE REWARD IMAGE */

public static function delete_reward_image( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards SET image = '' WHERE id = ?" );

  foreach( $id as $ID ) {

  if( \query\main::reward_exists( $ID ) ) {

  $reward = \query\main::reward_infos( $ID );

  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $reward->image ) ) {
    @unlink( DIR . '/' . $reward->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* ADD PAYMENT PLAN */

public static function add_payment_plan( $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  $opt['price'] = \site\utils::make_money_format( $opt['price'] );

  if( empty( $opt['name'] ) || $opt['price'] < 0 || $opt['credits'] <= 0 ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "p_plans (user, name, description, price, credits, image, lastupdate_by, lastupdate, visible, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

  $image = \site\images::upload( @$_FILES['logo'], 'payment_plan_', array(  'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 500, 'max_height' => 600, 'current' => '' ) );

  $stmt->bind_param( "issdisii", $GLOBALS['me']->ID, $opt['name'], $opt['description'], $opt['price'], $opt['credits'], $image, $GLOBALS['me']->ID, $opt['publish'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* EDIT REWARD */

public static function edit_payment_plan( $id, $opt = array() ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $opt = array_map( 'trim', $opt );

  $opt['price'] = \site\utils::make_money_format( $opt['price'] );

  if( empty( $opt['name'] ) || $opt['price'] < 0 || $opt['credits'] <= 0 ) {
    return false;
  }

  $plan = \query\payments::plan_infos( $id );

  $avatar = \site\images::upload( @$_FILES['logo'], 'payment_plan_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 500, 'max_height' => 600, 'current' => $plan->image ) );

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET name = ?, description = ?, price = ?, credits = ?, image = ?, lastupdate_by = ?, lastupdate = NOW(), visible = ? WHERE id = ?" );
  $stmt->bind_param( "ssdisiii", $opt['name'], $opt['description'], $opt['price'], $opt['credits'], $avatar, $GLOBALS['me']->ID, $opt['publish'], $id );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* DELETE REWARD */

public static function delete_payment_plan( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "p_plans WHERE id = ?" );

  foreach( $id as $ID ) {

  if( \query\payments::plan_exists( $ID ) ) {

  $plan = \query\payments::plan_infos( $ID );

  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $plan->image ) ) {
    @unlink( DIR . '/' . $plan->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* DELETE PAYMENT PLAN IMAGE */

public static function delete_payment_plan_image( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET image = '' WHERE id = ?" );

  foreach( $id as $ID ) {

  if( \query\payments::plan_exists( $ID ) ) {

  $plan = \query\payments::plan_infos( $ID );

  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  if( !empty( $plan->image ) ) {
    @unlink( DIR . '/' . $plan->image );
  }

  }

  }

  @$stmt->close();

  return true;

}

/* SET ACTION TO PAYMANT PLAN */

public static function payment_plan_action( $action, $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'publish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET visible = 1 WHERE id = ?" );
    break;

    case 'unpublish':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET visible = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE PAYMENT - INVOICE */

public static function delete_payment( $id ) {

global $db;

if( !$GLOBALS['me']->is_admin ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "p_transactions WHERE id = ?" );

  foreach( $id as $ID ) {

  $stmt->bind_param( "i", $ID );
  $stmt->execute();

  }

  @$stmt->close();

  return true;

}

/* SET ACTION TO A PAYMENT TRANSACTION - INVOICE */

public static function action_payment( $action, $id ) {

global $db;

if( !ab_to( array( 'payments' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'paid':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET paid = 1 WHERE id = ?" );
    break;

    case 'unpaid':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET paid = 0 WHERE id = ?" );
    break;
    case 'delivered':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET delivered = 1 WHERE id = ?" );
    break;

    case 'undelivered':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET delivered = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* SET ACTION TO REWARD REQUEST */

public static function action_reward_req( $action, $id ) {

global $db;

if( !ab_to( array( 'claim_reqs' => 'edit' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();

  switch( $action ) {
    case 'claim':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards_reqs SET claimed = 1 WHERE id = ?" );
    break;

    case 'unclaim':
      $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards_reqs SET claimed = 0 WHERE id = ?" );
    break;

    default:
      return false;
    break;
  }

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  $stmt->close();

  return true;

}

/* DELETE REWARD REQUEST */

public static function delete_reward_req( $id ) {

global $db;

if( !ab_to( array( 'claim_reqs' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "rewards_reqs WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* POST CHAT MESSAGE */

public static function post_chat_message( $msg ) {

global $db;

if( !ab_to( array( 'chat' => 'add' ) ) ) return false;

  if( trim( $msg ) == '' ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "chat (user, text, date) VALUES (?, ?, NOW())" );
  $stmt->bind_param( "is", $GLOBALS['me']->ID, $msg );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* DELETE CHAT MESSAGE */

public static function delete_chat_message( $id ) {

global $db;

if( !ab_to( array( 'chat' => 'delete' ) ) ) return false;

  $id = (array) $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "chat WHERE id = ?" );

  foreach( $id as $ID ) {
  $stmt->bind_param( "i", $ID );
  $stmt->execute();
  }

  @$stmt->close();

  return true;

}

/* CLEAR EXPIRED INFORMATIONS */

public static function cleardata( $coupons = false, $after_days = 0 ) {

global $db;

  $stmt = $db->stmt_init();

  // clear all expired email sessions
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE expiration < NOW()" );
  $stmt->execute();

  // clear all expired banned IPs
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "banned WHERE expiration = 1 AND expiration_date < NOW()" );
  $stmt->execute();

  // clear all expired sessions
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE expiration < NOW()" );
  $stmt->execute();

  // delete expired coupons
  if( $coupons && $after_days > 0 ) {
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "coupons WHERE DATE_ADD(expiration, INTERVAL " . $after_days . " DAY) < NOW()" );
  $stmt->execute();
  }

  $stmt->close();

}


}