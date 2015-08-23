<?php

  // this is not a valid click. if not, redirect to your website
  if( !isset( $_GET['id'] ) && !isset( $_GET['coupon'] ) && !isset( $_GET['product'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
  }

  // check if store exists. if not, redirect to your website
  if( isset( $_GET['id'] ) && !\query\main::store_exists( $_GET['id'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
  }

  // check if coupon exists. if not, redirect to your website
  if( isset( $_GET['coupon'] ) && !\query\main::item_exists( $_GET['coupon'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
  }

  // check if product exists. if not, redirect to your website
  if( isset( $_GET['product'] ) && !\query\main::product_exists( $_GET['product'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
  }

  include LBDIR . '/iptocountry/class.php';

  $myIP = \site\utils::getIP();

  $aIP = new IpToCountry;

  $aIP->IP = $myIP;
  $IPinfos = $aIP->infos();

  //
  $coupon = $product = 0;

  if( isset( $_GET['id'] ) ) {

  $infos = \query\main::store_infos( $_GET['id'] );

  $store = $infos->ID;
  $url = $infos->url;
  $type = 'Store';
  $typeID = (int) $_GET['id'];

  } else if( isset( $_GET['coupon'] ) ) {

  $infos = \query\main::item_infos( $_GET['coupon'] );

  $store = $infos->storeID;
  $coupon = $infos->ID;
  $url = $infos->url;
  $type = 'Coupon';
  $typeID = (int) $_GET['coupon'];

  } else if( isset( $_GET['product'] ) ) {

  $infos = \query\main::product_infos( $_GET['product'] );

  $store = $infos->storeID;
  $product = $infos->ID;
  $url = $infos->url;
  $type = 'Coupon';
  $typeID = (int) $_GET['product'];

  }

  // prepare URL for traking

  $url = str_ireplace( array( '{TYPE}', '{UID}', '{ID}' ), array( $type, ( $GLOBALS['me'] ? $GLOBALS['me']->ID : 'UNL' ), $typeID ), $url );

  $stmt = $db->stmt_init();

  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "click WHERE store = ? AND coupon = ? AND product = ? AND ipaddr = ? AND date > DATE_ADD(NOW(), INTERVAL -5 MINUTE)");
  $stmt->bind_param( "iiis", $store, $coupon, $product, $myIP );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  if( (int) $count === 0 ) {
  $stmt->prepare("INSERT INTO " . DB_TABLE_PREFIX . "click (store, coupon, product, user, ipaddr, browser, country1, country2, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

  $user = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

  $stmt->bind_param( "iiiissss", $store, $coupon, $product, $user, $myIP, $_SERVER['HTTP_USER_AGENT'], $IPinfos->country, $IPinfos->country_full );
  $stmt->execute();
  }

  $stmt->close();

  header( 'Location: ' . htmlspecialchars_decode( $url ) );