<?php

namespace plugin\CJApi\inc;

class actions {
    
    public static function find_cj_img( $advertiserId ) {
        global $db;
        $ret = '';
        $stmt = $db->stmt_init();
        $stmt->prepare( "SELECT logoId FROM cj_img where advertiserId = ".$advertiserId );
        $stmt->execute();
        $stmt->bind_result( $id );
        if($stmt->fetch()){
            $ret = "https://members.cj.com/member/publisher/logo/".$id.".gif";
        }
        $stmt->close();
        
        return $ret;
        
    }

/* ASSIGN ID */

public static function assign( $opt = array() ) {

global $db;

  $stmt = $db->stmt_init();

  // delete stores assigned
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET cjID = 0 WHERE cjID = ?" );
  $stmt->bind_param( "i", $opt['cjID'] );
  $stmt->execute();

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET cjID = ? WHERE id = ?" );
  $stmt->bind_param( "ii", $opt['cjID'], $opt['storeID'] );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/* IMPORT STORE */

public static function add_store( $opt = array() ) {

global $db;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) || empty( $opt['url'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (cjID, user, category, popular, name, link, description, tags, image, visible, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $logo = isset( $_FILES['logo'] ) ? \site\images::upload( $_FILES['logo'], 'logo_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 600, 'max_height' => 400 ) ) : $opt['logo'];
    if($logo == ''){
        $logo = $opt['logo'];
    }

  $stmt->bind_param( "iiiisssssissi", $opt['cjID'], $opt['user'], $opt['category'], $opt['popular'], $opt['name'], $opt['url'], $opt['description'], $opt['tags'], $logo, $opt['publish'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID );
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

/* ADD COUPON */

public static function add_item( $opt = array() ) {

global $db;

  $opt = array_map( 'trim', $opt );

  if( empty( $opt['name'] ) ) {
    return false;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (cjID, user, store, category, popular, exclusive, title, link, description, tags, code, visible, start, expiration, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

  $stmt->bind_param( "iiiiiisssssissssi", $opt['cjID'], $GLOBALS['me']->ID, $opt['store'], $opt['category'], $opt['popular'], $opt['exclusive'], $opt['name'],  $opt['link'], $opt['description'], $opt['tags'], $opt['code'], $opt['publish'], $opt['start'], $opt['end'], $opt['meta_title'], $opt['meta_desc'], $GLOBALS['me']->ID );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

    
    public static function listCategoryMapping( $category = null ){
        global $db;
        $stmt = $db->stmt_init();
        $search = "SELECT catID, category FROM " . DB_TABLE_PREFIX . "cj_category_mapping";
        if($category && $category != ''){
            $search.=" where category = '".$category."'";
        }
        //echo $search;
        $stmt->prepare($search);
        $stmt->execute();
        $stmt->bind_result( $catID, $category );
        $data = array();
        while ( $stmt->fetch() ) {
            $data[$category] = $catID;
        }
        
        $stmt->close();
        
        return $data;
    }
    
    public static function setCategoryMapping( $category, $catid, $catid_old ){
        global $db;
        $stmt = $db->stmt_init();
        $search = ($catid_old && $catid_old > 0)?"update cj_category_mapping set catID = ? where category = ?":"insert into cj_category_mapping (catID,category) values (?,?)";
        //echo $search;
        $stmt->prepare($search);
        $stmt->bind_param( "is", $catid, $category);
        $ret = $stmt->execute();
        
        $stmt->close();
        
        return $ret;
    }
    
}