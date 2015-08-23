<?php

require DIR . '/' . LBDIR . '/PayPal/autoload.php';

/** */

class Payment_Gateway {

/*

INTENT ACTION

*/

public $intent = 'sale';

/*

AMOUNT TO BE PAID

*/

public $total;

/*

PAYMENT DESCRIPTION

*/

public $description;

/*

CREDIT CARD TYPE

*/

public $card_type;

/*

CREDIT CARD FIRST NAME

*/

public $card_fname;

/*

CREDIT CARD LAST NAME

*/

public $card_lname;

/*

CREDIT CARD NUMBER

*/

public $card_number;

/*

CREDIT CARD EXPIRATON MONTH

*/

public $card_month;

/*

CREDIT CARD EXPIRATON YER

*/

public $card_year;

/*

CREDIT CARD CVV

*/

public $card_cvv;

/*

ITEMS

*/

public $items;

/*

SUCCESS URL

*/

public $success_url;

/*

CANCEL URL

*/

public $cancel_url;


/*

Construct class

*/

function __construct() {

  $client = \query\main::get_option( 'paypal_ID' );
  $secret = \query\main::get_option( 'paypal_secret' );

  $config = array( 'mode' => 'live' );

  if( strtolower( \query\main::get_option( 'paypal_mode' ) ) == 'sandbox' ) {

    $config['mode'] = 'sandbox';

  }

  $this->apiContext = new \PayPal\Rest\ApiContext(

      new \PayPal\Auth\OAuthTokenCredential( $client, $secret )

  );

  $this->apiContext->setConfig( $config );

}

public function do_direct() {

  return true;

}

public function direct() {

  $payer = new \PayPal\Api\Payer();
  $payer->setPaymentMethod( 'paypal' );

  $items = array();

  foreach( $this->items as $item ) {

  $item2 = new \PayPal\Api\Item();
  $item2->setName( $item[0] )
  ->setDescription( $item[1] )
  ->setCurrency( CURRENCY )
  ->setQuantity( $item[2] )
  ->setPrice( $item[3] );

  $items[] = $item2;

  }

  $itemList = new \PayPal\Api\ItemList();
  $itemList->setItems( $items );

  $amount = new \PayPal\Api\Amount();
  $amount->setCurrency( CURRENCY )
  ->setTotal( $this->total );

  $transaction = new \PayPal\Api\Transaction();
  $transaction->setDescription( $this->description )
  ->setItemList( $itemList )
  ->setAmount( $amount );

  $redirectUrls = new \PayPal\Api\RedirectUrls();
  $redirectUrls->setReturnUrl( $this->success_url )
  ->setCancelUrl( $this->cancel_url );

  $payment = new \PayPal\Api\Payment();
  $payment->setIntent( $this->intent )
  ->setPayer( $payer )
  ->setRedirectUrls( $redirectUrls )
  ->setTransactions(array( $transaction ));

  try {

  $payment->create( $this->apiContext );

  return array( 'id' => $payment->getId(), 'total' => $this->total , 'items' => $this->items, 'details' => $this->description, 'state' => $payment->getstate(), 'href' => $payment->getApprovalLink() );

  } catch ( Exception $e ) {

  throw new \Exception( 'PayPal error: ' . $e->getMessage() );

  }

}

public function do_credit_card() {

  return true;

}

public function credit_card() {

  $card = new \PayPal\Api\CreditCard();
  $card->setType( $this->card_type )
  ->setNumber( $this->card_number )
  ->setExpireMonth( $this->card_month )
  ->setExpireYear( $this->card_year )
  ->setCvv2( $this->card_cvv )
  ->setFirstName( $this->card_fname )
  ->setLastName( $this->card_lname );

  $fi = new \PayPal\Api\FundingInstrument();
  $fi->setCreditCard( $card );

  $payer = new \PayPal\Api\Payer();
  $payer->setPaymentMethod( 'credit_card' )
  ->setFundingInstruments( array( $fi ) );

  $items = array();

  foreach( $this->items as $item ) {

  $item2 = new \PayPal\Api\Item();
  $item2->setName( $item[0] )
  ->setDescription( $item[1] )
  ->setCurrency( CURRENCY )
  ->setQuantity( $item[2] )
  ->setPrice( $item[3] );

  $items[] = $item2;

  }

  $itemList = new \PayPal\Api\ItemList();
  $itemList->setItems( $items );

  $amount = new \PayPal\Api\Amount();
  $amount->setCurrency( CURRENCY )
  ->setTotal( $this->total );

  $transaction = new \PayPal\Api\Transaction();
  $transaction->setAmount( $amount )
  ->setItemList( $itemList )
  ->setDescription( $this->description );

  $payment = new \PayPal\Api\Payment();
  $payment->setIntent( $this->intent )
  ->setPayer( $payer )
  ->setTransactions(array( $transaction ));

  try {

  $payment->create( $this->apiContext );

  return array( 'id' => $payment->getId(), 'total' => $this->total , 'items' => $this->items, 'details' => $this->description, 'state' => $payment->getstate() );

  } catch ( Exception $e ) {

  throw new \Exception( 'PayPal error: ' . $e->getMessage() );

  }

}

public function execute_direct_payment() {

  // this function should return a token, payment id

  if( isset( $_GET['paymentId'] ) ) {
    return $_GET['paymentId'];
  }

  return false;

}

public function execute_payment() {

  $payment = \PayPal\Api\Payment::get( $_GET['paymentId'], $this->apiContext );

  $execution = new \PayPal\Api\PaymentExecution();
  $execution->setPayerId( $_GET['PayerID'] );

  try {

  $payment->execute( $execution, $this->apiContext );

  return array( 'id' => $payment->getId(), 'state' => $payment->getstate() );

  } catch ( Exception $e ) {

  throw new \Exception( 'PayPal error: ' . $e->getMessage() );

  }

}

}