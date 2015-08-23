<?php

namespace site;

/** */

class payment {

public static function gateways() {

  $gateways = array();
  // built-in payment gateways
  $gateways['paypal']['name'] = 'PayPal';
  $gateways['paypal']['image'] = $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/paypal.png';
  $gateways['paypal']['adapter'] = IDIR . '/paygateways/Paypal.php';
  // user plugins
  foreach( \query\main::user_plugins( 'pay_gateway' ) as $pgateway ) {
  $gateways[strtolower( $pgateway->name )]['name'] = $pgateway->name;
  $gateways[strtolower( $pgateway->name )]['image'] = $GLOBALS['siteURL'] . $pgateway->image;
  $gateways[strtolower( $pgateway->name )]['adapter'] = UPDIR . '/'. $pgateway->main_file;
  }

  return $gateways;

}

}