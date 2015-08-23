<?php

namespace payment;

/** */

class main {

/*

INTENT ACTION

*/

public $intent = 'sale';

/*

PAYMENT DESCRIPTION

*/

public $description;

/*

CREDIT CARD TYPE

*/

public $cc_type;

/*

CREDIT CARD FIRST NAME

*/

public $cc_first_name;

/*

CREDIT CARD LAST NAME

*/

public $cc_last_name;

/*

CREDIT CARD NUMBER

*/

public $cc_number;

/*

CREDIT CARD EXPIRATON MONTH

*/

public $cc_emonth;

/*

CREDIT CARD EXPIRATON YER

*/

public $cc_eyear;

/*

CREDIT CARD CVV

*/

public $cc_cvv;

/*

LIST OF ITEMS

*/

public $items;

/*

SUCCESS URL - User returns here when transaction was finished successfully

*/

public $success_url;

/*

CANCEL URL - User returns here when transaction was canceled

*/

public $cancel_url;


/*

Construct class

*/

function __construct( $gateway = '' ) {

  $gateway = strtolower( $gateway );

  if( empty( $gateway ) ) {
    $gateway = 'paypal';
  }

  $gateways = \site\payment::gateways();

  if( !array_key_exists( $gateway, $gateways ) ) {
    throw new \Exception( 'Gateway error.' );
  } else if( !file_exists( $gateways[$gateway]['adapter'] ) ) {
    throw new \Exception( 'Gateway error.' );
  } else {

  $this->gateway_info = $gateways[$gateway];

  require_once $this->gateway_info['adapter'];

  $this->gateway = new \Payment_Gateway;
  $this->gateway_name = $gateway;

  }

}

/*

Check if this gateway accept payments throug their website

*/

public function do_direct() {

  if( method_exists( $this->gateway, 'do_direct' ) ) {
    return $this->gateway->do_direct();
  }

  return false;

}

/*

Execute payment through gateway website

*/

public function direct() {

  $this->gateway->intent = $this->intent;
  $this->gateway->total = $this->total();
  $this->gateway->items = $this->items();
  $this->gateway->description = $this->description;
  $this->gateway->success_url = $this->success_url();
  $this->gateway->cancel_url = $this->cancel_url();

  return $this->gateway->direct();

}

/*

Check if this gateway accept payments with credit cards

*/

public function do_credit_card() {

  if( method_exists( $this->gateway, 'do_credit_card' ) ) {
    return $this->gateway->do_credit_card();
  }

  return false;

}

/*

Execute payment through credit card

*/

public function credit_card() {

  $this->gateway->intent = $this->intent;
  $this->gateway->total = $this->total();
  $this->gateway->items = $this->items();
  $this->gateway->description = $this->description;
  $this->gateway->card_type = $this->cc_type;
  $this->gateway->card_fname = $this->cc_first_name;
  $this->gateway->card_lname = $this->cc_last_name;
  $this->gateway->card_number = $this->cc_number;
  $this->gateway->card_month = $this->cc_emonth;
  $this->gateway->card_year = $this->cc_eyear;
  $this->gateway->card_cvv = $this->cc_cvv;

  return $this->gateway->credit_card();

}

/*

Check if this gateway it's ready to execute a direct payment

*/

public function execute_direct_payment() {

  if( method_exists( $this->gateway, 'execute_direct_payment' ) ) {
    return $this->gateway->execute_direct_payment();
  }

  return true;

}

/*

Execute a direct payment

*/

public function execute_payment() {

  if( method_exists( $this->gateway, 'execute_payment' ) ) {
    return $this->gateway->execute_payment();
  }

  throw new \Exception( 'Gateway error.' );

}

/*

Credit cards accepted

*/

public function cards() {

  $cards = array();

  $cards['amex'] = array( 'name' => 'American Express', 'value' => 'emex', 'image' => DEFAULT_IMAGES_LOC . '/card_amexp.png' );
  $cards['visa'] = array( 'name' => 'Visa', 'value' => 'visa', 'image' => DEFAULT_IMAGES_LOC . '/card_visa.png' );
  $cards['discover'] = array( 'name' => 'Discover', 'value' => 'discover', 'image' => DEFAULT_IMAGES_LOC . '/card_discover.png' );
  $cards['mastercard'] = array( 'name' => 'Mastercard', 'value' => 'mastercard', 'image' => DEFAULT_IMAGES_LOC . '/card_mastercard.png' );

  if( method_exists( $this->gateway, 'cards_accepted' ) ) {
    $cards = $this->gateway->cards_accepted( $cards );
  }

  return $cards;

}

/*

Total amount that should be paid

*/

private function total() {
  $price = 0;
  foreach( $this->items() as $item ) {
    $price += $item[2] * $item[3];
  }
  return $price;
}

/*

All items that should be paid

*/

private function items() {

  $good_list = array();

  if( is_array( $this->items ) ) {
    foreach( $this->items as $item ) {

      if( empty( $item[0] ) || empty( $item[1] ) || empty( $item[2] ) || empty( $item[3] ) ) {
        continue;
      }
      $good_list[] = $item;
    }
  }
  return $good_list;

}

/*

Success url, return on a specific page when the transaction were finished successfully, paypal uses it

*/

private function success_url() {

  if( empty( $this->success_url ) ) {
    return $GLOBALS['siteURL'] . 'payment.php';
  }
  return $this->success_url;

}

/*

Success url, return on a specific page when the transaction were finished by cancellation, paypal uses it

*/

private function cancel_url() {

  if( empty( $this->cancel_url ) ) {
    return $GLOBALS['siteURL'] . 'payment.php';
  }
  return $this->cancel_url;

}

}