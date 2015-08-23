<?php

namespace plugin\Popshop\inc;

class actions {

    
public static function importDealType( $dealTypes = array() ) {

global $db;
    $num_dups = 0;
    $num_imports = 0;

  $stmt = $db->stmt_init();

    foreach($dealTypes as $deal_type){
        $stmt->prepare( "INSERT INTO popshop_deal_type (id, name) VALUES (?, ?)" );
        $stmt->bind_param( "is", $deal_type['id'], $deal_type['name']);
        $execute = $stmt->execute();
        if( $execute ) {
            $num_dups=0;
            $num_imports++;
        }else{
            $num_dups++;
        }
    }
    
  $stmt->close();

    return array('num_dups'=>$num_dups,'num_imports'=>$num_imports);

}
    public static function importMerchantType( $merchantTypes = array() ) {
        
        global $db;
        $num_dups = 0;
        $num_imports = 0;
        
        $stmt = $db->stmt_init();
        
        foreach($merchantTypes as $merchant_type){
            $stmt->prepare( "INSERT INTO popshop_merchant_type (id, name) VALUES (?, ?)" );
            $stmt->bind_param( "is", $merchant_type['id'], $merchant_type['name']);
            $execute = $stmt->execute();
            if( $execute ) {
                $num_dups=0;
                $num_imports++;
            }else{
                $num_dups++;
            }
        }
        
        $stmt->close();
        
        return array('num_dups'=>$num_dups,'num_imports'=>$num_imports);
        
    }
    
    public static function importMerchant( $merchants = array() ) {
        
        global $db;
        $num_dups = 0;
        $num_imports = 0;
        
        $stmt = $db->stmt_init();
        
        foreach($merchants as $merchant){
            $stmt->prepare( "INSERT INTO popshop_merchant (id, name, logo_url, url, merchant_type) VALUES (?, ?, ?, ?, ?)" );
            $stmt->bind_param( "isssi", $merchant['id'], $merchant['name'], $merchant['logo_url'], $merchant['url'], $merchant['merchant_type']);
            $execute = $stmt->execute();
            if( $execute ) {
                $num_dups=0;
                $num_imports++;
            }else{
                $num_dups++;
            }
        }
        
        $stmt->close();
        
        return array('num_dups'=>$num_dups,'num_imports'=>$num_imports);
        
    }
    
    public static function hasNewMerchant( $merchants = array() ) {
        
        global $db;
        $ids = '';
        
        $stmt = $db->stmt_init();
        foreach($merchants as $merchant){
            if($ids == ''){
                $ids .= $merchant['id'];
            }else{
                $ids .= ','.$merchant['id'];
            }
        }
        $search = "SELECT count(id) FROM popshop_merchant WHERE id IN (".$ids.")";
        $stmt->prepare($search);
        $stmt->execute();
        $stmt->bind_result( $count );
        $stmt->fetch();
        
        $stmt->close();
        
        return count($merchants) - $count;
        
    }
    
    public static function importDeal( $deals = array() ) {
        
        global $db;
        $num_dups = 0;
        $num_imports = 0;
        
        $stmt = $db->stmt_init();
        
        foreach($deals as $deal){
            //var_dump($deal);
            $stmt->prepare( "INSERT INTO popshop_coupon (id, merchant, code, deal_type, start_on, end_on, name, site_wide, url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)" );
            $site_wide = ($deal['site_wide']=='yes')?1:0;
            $start_on = date('Y-m-d', strtotime($deal['start_on']));
            $end_on = date('Y-m-d', strtotime($deal['end_on']));
            $code = isset($deal['code'])?$deal['code']:'';
            $deal_type = isset($deal['deal_type'])?$deal['deal_type']:'';
            $stmt->bind_param( "iisssssis", $deal['id'], $deal['merchant'], $code, $deal_type, $start_on, $end_on, $deal['name'], $site_wide, $deal['url']);
            $execute = $stmt->execute();
            if( $execute ) {
                $num_dups=0;
                $num_imports++;
            }else{
                $num_dups++;
            }
        }
        
        $stmt->close();
        
        return array('num_dups'=>$num_dups,'num_imports'=>$num_imports);
        
    }
    
    public static function listMerchants( $lookup =array() ){
        global $db;
        $stmt = $db->stmt_init();
        $search = "SELECT id,name,logo_url,url,storeID,merchant_type FROM popshop_merchant WHERE";
        if($lookup['status'] == 'joined'){
            $search.=" storeID > 0";
        }else{
            $search.=" storeID = 0";
        }
        if(isset($lookup['name'])){
            $search.=" and name like '%".$lookup['name']."%'";
        }
        if(isset($lookup['type']) && $lookup['type'] > 0){
            $search.=" and merchant_type = ".$lookup['type'];
        }
        $search.=" LIMIT ".($lookup['per_page']*$lookup['page']).",".$lookup['per_page'];
        //echo $search;
        $stmt->prepare($search);
        $stmt->execute();
        $stmt->bind_result( $id,$name,$logo_url,$url,$storeID,$merchant_type );
        $data = array();
        while ( $stmt->fetch() ) {
            $data[] = array('id'=>$id,'name'=>$name,'logo_url'=>$logo_url,'url'=>$url,'storeID'=>$storeID,'merchant_type'=>$merchant_type);
        }
        
        $stmt->close();
        
        return $data;
    }
    
    public static function listDealTypes( $ids = '' ){
        global $db;
        $stmt = $db->stmt_init();
        $search = "SELECT id,name FROM popshop_deal_type";
        if($ids && $ids != ''){
            $search.=" where id in (".$ids.")";
        }
        //echo $search;
        $stmt->prepare($search);
        $stmt->execute();
        $stmt->bind_result( $id,$name );
        $data = array();
        while ( $stmt->fetch() ) {
            $data[$id] = $name;
        }
        
        $stmt->close();
        
        return $data;
    }
    
    public static function listMerchantTypes( $id = 0 ){
        global $db;
        $stmt = $db->stmt_init();
        $search = "SELECT id,name FROM popshop_merchant_type";
        if($id){
            $search.=" where id = ".$id;
        }
        //echo $search;
        $stmt->prepare($search);
        $stmt->execute();
        $stmt->bind_result( $id,$name );
        $data = array();
        while ( $stmt->fetch() ) {
            $data[$id] = $name;
        }
        
        $stmt->close();
        
        return $data;
    }
    
    public static function listDeals( $lookup =array() ){
        global $db;
        $stmt = $db->stmt_init();
        $search = "SELECT id,merchant,code,deal_type,start_on,end_on,name,site_wide,url,couponID FROM popshop_coupon WHERE";
        if($lookup['$merchant']){
            $search.=" merchant = ".$lookup['$merchant'];
        }else{
            if($lookup['status'] == 'joined'){
                $search.=" couponID > 0";
            }else{
                $search.=" couponID = 0";
            }
            $deal_type = intval($lookup['deal_type']);
            if($deal_type > 0){
                $search.=" and deal_type like '%".$deal_type."%'";
            }
        }
        $search.=" LIMIT ".($lookup['per_page']*$lookup['page']).",".$lookup['per_page'];
        //echo $search;
        $stmt->prepare($search);
        $stmt->execute();
        $stmt->bind_result( $id,$merchant,$code,$deal_type,$start_on,$end_on,$name,$site_wide,$url,$couponID );
        $data = array();
        while ( $stmt->fetch() ) {
            $data[] = array('id'=>$id,'name'=>$name,'merchant'=>$merchant,'url'=>$url,'couponID'=>$couponID,'code'=>$code,'deal_type'=>$deal_type,'start_on'=>$start_on,'end_on'=>$end_on,'site_wide'=>$site_wide);
        }
        
        $stmt->close();
        
        return $data;
    }



/* IMPORT STORE */

public static function add_store( $catID, $storedata ) {

global $db;
    $importnum = 0;

  $stmt = $db->stmt_init();
    
foreach($storedata as $data){
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (popshopID, user, category, popular, name, link, description, tags, image, visible, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

    $popular = isset($data['popular'])?$data['popular']:0;
    $description = isset($data['description'])?$data['description']:'';
    $tags = isset($data['tags'])?$data['tags']:'';
    $publish = isset($data['publish'])?$data['publish']:0;
    $meta_title = isset($data['meta_title'])?$data['meta_title']:'';
    $meta_desc = isset($data['meta_desc'])?$data['meta_desc']:'';
  $stmt->bind_param( "iiiisssssissi", $data['id'], $GLOBALS['me']->ID, $catID, $popular, $data['name'], $data['url'], $description, $tags, $data['logo_url'], $publish, $meta_title, $meta_desc, $GLOBALS['me']->ID );
  $execute = $stmt->execute();

  if( $execute ) {//update popshop table
      $stmt->prepare( "SELECT LAST_INSERT_ID() FROM " . DB_TABLE_PREFIX . "stores" );
      $stmt->execute();
      $stmt->bind_result( $id );
      $stmt->fetch();

      $stmt->prepare( "UPDATE popshop_merchant SET storeID = ? WHERE id = ?" );
      $stmt->bind_param( "ii", $id, $data['id'] );
      $execute = $stmt->execute();

      $importnum++;
  }
}

  $stmt->close();

  return $importnum;

}
    
public static function check_store( $id ) {
    global $db;
    $ret = NULL;
    $search = "SELECT id,name,logo_url,url,storeID FROM popshop_merchant WHERE id = ".$id;
    $stmt = $db->stmt_init();
    $stmt->prepare( $search );
    $stmt->execute();
    $stmt->bind_result( $id,$name,$logo_url,$url,$storeID );
    if($stmt->fetch()){
        $ret = array('id'=>$id,'name'=>$name,'logo_url'=>$logo_url,'url'=>$url,'storeID'=>$storeID);
    }
    $stmt->close();
    return $ret;
}

    public static function get_import_store( $id ) {
        global $db;
        $ret = NULL;
        $search = "SELECT category,name,popular,link,description,visible FROM stores WHERE id = ".$id;
        $stmt = $db->stmt_init();
        $stmt->prepare( $search );
        $stmt->execute();
        $stmt->bind_result( $category,$name,$popular,$link,$description,$visible );
        if($stmt->fetch()){
            $ret = array('category'=>$category,'name'=>$name,'popular'=>$popular,'link'=>$link,'description'=>$description,'visible'=>$visible);
        }
        $stmt->close();
        return $ret;
    }

/* ADD COUPON */

public static function add_item( $coupons = array() ) {

global $db;
    $importnum = 0;
    $stores = array();
    $deal_types = NULL;
  $stmt = $db->stmt_init();
foreach($coupons as $data){
    $store = NULL;
if(!isset($data['store']) || !isset($data['category']) || !isset($data['popular']) || !isset($data['publish'])){
    if(isset($stores[$data['merchant']])){
        $store = $stores[$data['merchant']];
    }else{
        $search = "SELECT id,category,popular,visible FROM " . DB_TABLE_PREFIX . "stores WHERE popshopID = ".$data['merchant'];
        $stmt->prepare( $search );
        $stmt->execute();
        $stmt->bind_result( $id,$category,$popular,$visible );
        if($stmt->fetch()){
            $store = array('id'=>$id,'category'=>$category,'popular'=>$popular,'visible'=>$visible);
            $stores[$data['merchant']] = $store;
        }
    }
}
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (popshopID, user, store, category, popular, exclusive, title, link, description, tags, code, visible, start, expiration, meta_title, meta_desc, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );
    
    $storeid = isset($data['store'])?$data['store']:$store['id'];
    $category = isset($data['category'])?$data['category']:$store['category'];
    $popular = isset($data['popular'])?$data['popular']:$store['popular'];
    $exclusive = isset($data['exclusive'])?$data['exclusive']:0;
    $description = isset($data['description'])?$data['description']:($data['site_wide']?'Applicable across the entire merchant website':'Applicable to the specific product or category');
    $tags = '';
    if(isset($data['tags'])){
        $tags = $data['tags'];
    }else{
        if($data['deal_type'] != ''){
            if(!$deal_types){
                $deal_types = actions::listDealTypes();
            }
            $arrs = explode(',',$data['deal_type']);
            foreach($arrs as $arr){
                if(isset($deal_types[intval($arr)])){
                    if($tags == ''){
                        $tags = $deal_types[intval($arr)];
                    }else{
                        $tags .= ', '.$deal_types[intval($arr)];
                    }
                }
            }
        }
    }
    $publish = isset($data['publish'])?$data['publish']:$store['visible'];
    $meta_title = isset($data['meta_title'])?$data['meta_title']:'';
    $meta_desc = isset($data['meta_desc'])?$data['meta_desc']:'';

  $stmt->bind_param( "iiiiiisssssissssi", $data['id'], $GLOBALS['me']->ID, $storeid, $category, $popular, $exclusive, $data['name'],  $data['url'], $description, $tags, $data['code'], $publish, $data['start_on'], $data['end_on'], $meta_title, $meta_desc, $GLOBALS['me']->ID );
  $execute = $stmt->execute();
    if( $execute ) {
        $stmt->prepare( "SELECT LAST_INSERT_ID() FROM " . DB_TABLE_PREFIX . "stores" );
        $stmt->execute();
        $stmt->bind_result( $id );
        $stmt->fetch();
        
        $stmt->prepare( "UPDATE popshop_coupon SET couponID = ? WHERE id = ?" );
        $stmt->bind_param( "ii", $id, $data['id'] );
        $execute = $stmt->execute();
        
        $importnum++;
    }
}
  $stmt->close();


  return $importnum;

}

}