<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS (see changelog) */

error_reporting( E_ALL );

/** REQUIRE SETTINGS */

include 'settings.php';

include IDIR . '/site/db.php';

/** CONNECT TO DB */

if( $db->connect_errno ) {
  header( 'Location: index.php' );
  die;
}

$db->set_charset( DB_CHARSET );

/** */

spl_autoload_register(function ( $cn ) {
    include IDIR . '/' . str_replace( '\\', '/', $cn ) . '.php';
});

/** */

include ( new \main\load )->language['location'];

if( isset( $_POST['action'] ) &&  $_POST['action'] == 'set_cjimg' ) {
    
    $data = $_POST['data'];
    $cjimginfo = json_decode($data,true);
    if(!$cjimginfo){
        echo "Data error:".$data;
        
        die;
    }

    $addcount = 0;
  $stmt = $db->stmt_init();
    foreach($cjimginfo as $info){
    $stmt->prepare( "INSERT INTO cj_img (advertiserId, logoId) VALUES (?, ?)" );
  $stmt->bind_param( "ii", $info[0], $info[1] );
        if($stmt->execute()){
            $addcount++;
            $stmt->prepare( "UPDATE stores SET image = '"."https://members.cj.com/member/publisher/logo/".$info[1].".gif"."' where (image IS NULL or image = '') and cjID = ".$info[0] );
            $stmt->execute();
        }
    }
  @$stmt->close();

  echo 'SUCCESS! '.$addcount.' added';

}else if( isset( $_GET['action'] ) &&  $_GET['action'] == 'get_deal' ) {
    if(isset( $_GET['csrf'] ) && $_GET['csrf'] == $_SESSION['slider_csrf'] ){
        $id = $_GET['id'];
        $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
        $seo_link_coupon = \query\main::get_option( 'seo_link_coupon' );
        $seo_link_product = \query\main::get_option( 'seo_link_product' );
        $seo_link_store = \query\main::get_option( 'seo_link_store' );
        $stmt = $db->stmt_init();
        $search_coupons = "SELECT c.id, c.title, c.link, c.description, c.tags, s.image, s.name, s.link, s.id, c.expiration FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE c.visible > 0 AND s.visible > 0 AND c.id in (".$id.")";
        $stmt->prepare($search_coupons);
        $stmt->execute();
        $stmt->bind_result( $c_id, $c_title, $c_link, $c_description, $c_tags, $s_image, $s_name, $s_link, $s_id, $c_expiration );
        $ret = array();
        while($stmt->fetch()){
            $coupon_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $c_title, $c_id ) : $GLOBALS['siteURL'] . '?id=' . $c_id );
            $store_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $s_name, $s_id ) : $GLOBALS['siteURL'] . '?store=' . $s_id );
            $c_expiration = str_replace(' 00:00:00','',$c_expiration);
            array_push($ret, array('title'=>$c_title,'image'=>$s_image, 'name'=>$s_name, 'expiration'=>'Expires on '.$c_expiration, 'link'=>$coupon_link, 's_link'=>$store_link));
        }
        if(count($ret) > 0){
            echo json_encode($ret);
        }
    }
}else if( isset( $_GET['action'] ) &&  $_GET['action'] == 'get_sale' ) {
    if(isset( $_GET['csrf'] ) && $_GET['csrf'] == $_SESSION['slider_csrf'] ){
        $id = $_GET['id'];
        $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
        $seo_link_coupon = \query\main::get_option( 'seo_link_coupon' );
        $seo_link_product = \query\main::get_option( 'seo_link_product' );
        $seo_link_store = \query\main::get_option( 'seo_link_store' );
        $stmt = $db->stmt_init();
        $search_products = "SELECT p.id, p.title, p.link, p.description, p.tags, p.image, p.currency, p.price, p.old_price, s.name, s.link, s.id, p.expiration FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE p.visible > 0 AND s.visible > 0 AND p.id in (".$id.")";
        $stmt->prepare($search_products);
        $stmt->execute();
        $stmt->bind_result( $p_id, $p_title, $p_link, $p_description, $p_tags, $p_image, $p_currency, $p_price, $p_old_price, $s_name, $s_link, $s_id, $p_expiration );
        $ret = array();
        while($stmt->fetch()){
            $product_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $p_title, $p_id ) : $GLOBALS['siteURL'] . '?product=' . $p_id );
            $store_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $s_name, $s_id ) : $GLOBALS['siteURL'] . '?store=' . $s_id );
            $p_expiration = str_replace(' 00:00:00','',$p_expiration);
            array_push($ret, array('title'=>$p_title,'image'=>$p_image, 'name'=>$s_name, 'expiration'=>'Expires on '.$p_expiration, 'link'=>$product_link, 's_link'=>$store_link, 'price'=>$p_currency.$p_price, 'old_price'=>$p_currency.$p_old_price));
        }
        if(count($ret) > 0){
            echo json_encode($ret);
        }
    }
}

$db->close();