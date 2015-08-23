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

if( !$GLOBALS['me'] || ! \query\payments::plan_exists( $_GET['plan'], array( 'user_view' => '' ) ) ) {
  header( 'Location: index.php' );
  die;
}

$plan = \query\payments::plan_infos( $_GET['plan'] );

try {

$gateway = ( isset( $_GET['gateway'] ) ? $_GET['gateway'] : '' );

$payment = new \payment\main( $gateway );

$thegateway = $payment->gateway_name;

$payment->description = 'Purchase plan';
$payment->items[] = array( $plan->name, $plan->description, 1, $plan->price );

  echo '<!DOCTYPE html>

  <html>
      <head>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>' . $LANG['payments_metatitle'] . '</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="' . MISCDIR . '/pay.js"></script>
        <link href="//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
        <link href="' . MISCDIR . '/pay.css" media="all" rel="stylesheet" />

      </head>

  <body>

  <div class="msg">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && \site\utils::check_csrf( $_POST['token'], 'payment_csrf' ) ) {

  if( isset( $_POST['pay_direct'] ) ) {

  try {

  // redirect URLs, used for PayPal, but can be used for other other gateways also

  $payment->success_url = $GLOBALS['siteURL'] . "payment.php?gateway={$payment->gateway_name}&plan={$_GET['plan']}";
  $payment->cancel_url = $GLOBALS['siteURL'] . "payment.php?gateway={$payment->gateway_name}&plan={$_GET['plan']}";

  $answer = $payment->direct();

  // save transaction

  \query\payments::inset_payment( array( $GLOBALS['me']->ID, $payment->gateway_name, $answer['total'], $answer['id'], $answer['state'], @serialize( $answer['items'] ), $answer['details'], 0, 0 ) );

  // save token

  $_SESSION['payment_direct_token'] = $answer['id'];

  if( isset( $answer['href'] ) ) {
    header( 'Location: ' . $answer['href'] );
    die;
  }

  }

  catch( Exception $e ) {
    echo '<div class="error">' . $e->getMessage() . '</div>';
  }

  } else if( $payment->do_credit_card() && isset( $_POST['pay_credit_card'] ) ) {

  if( empty( $_POST['card']['type'] ) ) {
    echo '<div class="error">' . $LANG['payments_msg_invseltype'] . '</div>';
  } else if( !( isset( $_POST['card']['name'] ) && ( $card_name = $_POST['card']['name'] ) &&  preg_match( '/^([a-zA-Z\']{2,})\s+([a-zA-Z\']{2,})(\s+([a-zA-Z\' ]+))?/', $card_name, $card_name_a ) ) ) {
    unset( $_POST['card']['name'] );
    echo '<div class="error">' . $LANG['payments_msg_invnamecard'] . '</div>';
  } else if( !( isset( $_POST['card']['number'] ) && ( $card_number = preg_replace( '/\s+/', '' , $_POST['card']['number'] ) ) && preg_match( '/^([0-9]{14,16})$/', $card_number ) ) ) {
    unset( $_POST['card']['number'] );
    echo '<div class="error">' . $LANG['payments_msg_invnumber'] . '</div>';
  } else if( empty( $_POST['card']['month'] ) || empty( $_POST['card']['year'] ) ) {
    echo '<div class="error">' . $LANG['payments_msg_invexp'] . '</div>';
  } else if( !( isset( $_POST['card']['cvv'] ) && preg_match( '/^([0-9]{3,4})$/', $_POST['card']['cvv'] ) ) ) {
    unset( $_POST['card']['cvv'] );
    echo '<div class="error">' . $LANG['payments_msg_invcvv'] . '</div>';
  } else {

  $payment->cc_type = $_POST['card']['type'];
  $payment->cc_first_name = $card_name_a[1];
  $payment->cc_last_name = $card_name_a[2];
  $payment->cc_number = $card_number;
  $payment->cc_emonth = $_POST['card']['month'];
  $payment->cc_eyear = $_POST['card']['year'];
  $payment->cc_cvv = $_POST['card']['cvv'];

  try {

  $answer = $payment->credit_card();

  unset( $_POST );

  echo '<div class="success">' . $LANG['payments_msg_confirmed'] . '</div>';

  /*

  Action after purchase, add credits or something ...

  */

  // add user credits

  $delivered = \user\update::add_credits( $GLOBALS['me']->ID, $plan->credits );

  // save transaction

  // userID, gateway, amount paid, transcationID, state, items on invoice, details, paid, delivered

  \query\payments::inset_payment( array( $GLOBALS['me']->ID, $payment->gateway_name, $answer['total'], $answer['id'], $answer['state'], @serialize( $answer['items'] ), $answer['details'], 1, $delivered ) );

  }

  catch( Exception $e ) {

    // show getMessage() or just show an error message for all exceptions

    echo '<div class="error">' . $LANG['payments_msg_error_cc'] . '<br />' . $e->getMessage() . '</div>';

  }

  }

  }

  } else if( ( $payment_direct_token = $payment->execute_direct_payment() ) && isset( $_SESSION['payment_direct_token'] ) && $_SESSION['payment_direct_token'] = $payment_direct_token ) {

  unset( $_SESSION['payment_direct_token'] );

  try {

  $answer = $payment->execute_payment();

  echo '<div class="success">' . $LANG['payments_msg_confirmed'] . '</div>';

  /*

  Action after purchase, add credits or something ...

  */

  // add user credits

  $delivered = \user\update::add_credits( $GLOBALS['me']->ID, $plan->credits );

  // update transaction

  // state, userID, paid, delivered, transactionID

  \query\payments::update_payment( array( $answer['state'], $GLOBALS['me']->ID, 1, $delivered, $answer['id'] ) );

  }

  catch( Exception $e ) {
    echo '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['payment_csrf'] = \site\utils::str_random(10);

  echo '<div class="table">';

  echo '<section>

  <h2>' . $LANG['payments_title_infos'] . '</h2>

  <ul class="table2">
  <li><span>' . $LANG['form_price'] . ':</span> <b>' . $plan->price_format . '</b></li>
  <li><span>' . $LANG['form_plan'] . ':</span> <b>' . $plan->name . '</b></li>
  <li><span>' . $LANG['form_credits'] . ':</span> <b>' . $plan->credits . '</b></li>
  <li><span>' . $LANG['form_description'] . ':</span> ' . $plan->description . '</li>
  </ul>

  </section>

  <section>';

  if( $docc = $payment->do_credit_card() ) {

  echo '<div class="pay-credt-card-form"' . ( isset( $_POST['credit_card'] ) ? ' style="display: block;"' : '' ) . '>

  <form method="POST" action="#" autocomplete="off">

  <ul class="table2">
  <li class="cardtype"><span>' . $LANG['payments_form_cardtype'] . ':</span>';
  $sctd_type = isset( $_POST['card']['type'] ) ? $_POST['card']['type'] : 'visa';
  foreach( $payment->cards() as $id => $card ) {
    echo '<input type="radio" name="card[type]" value="' . $card['value'] . '" id="' . $id . '"' . ( $sctd_type == $card['value'] ? ' checked' : '' ) . ' /> <label for="' . $id . '"><img src="' .  $card['image'] . '" alt="*" style="height: 25px; width: 35px;" /></label> ';
  }
  echo '</li>
  <li><span>' . $LANG['payments_form_nameoncard'] . ':</span> <input type="text" name="card[name]" value="' . ( isset( $_POST['card']['name'] ) ? htmlspecialchars( $_POST['card']['name'] ) : '' ) . '" placeholder="' . $LANG['payments_nameoncard_ph'] . '" required /></li>
  <li><span>' . $LANG['payments_form_cardnumber'] . ':</span> <input type="text" name="card[number]" value="' . ( isset( $_POST['card']['number'] ) ? htmlspecialchars( $_POST['card']['number'] ) : '' ) . '" required /></li>
  <li><span>' . $LANG['payments_form_cardexp'] . ':</span>
  <select name="card[month]" style="width: 47%;">
  <option value="0">' . $LANG['month'] . '</option>';
  $sctd_month = isset( $_POST['card']['month'] ) ? $_POST['card']['month'] : '';
  for( $i = 1; $i <= 12; $i++ ) {
    echo '<option value="' . $i . '"' . ( $i == $sctd_month ? ' selected' : '' ) . '>' . sprintf( '%02d', $i ) . '</option>';
  }
  echo '</select>
  <select name="card[year]" style="width: 46%; margin-left: 2%;">
  <option value="0">' . $LANG['year'] . '</option>';
  $sctd_year = isset( $_POST['card']['year'] ) ? $_POST['card']['year'] : '';
  for( $i = date( 'Y' ); $i < date( 'Y' ) + 15; $i++ ) {
    echo '<option value="' . $i . '"' . ( $i == $sctd_year ? ' selected' : '' ) . '>' . $i . '</option>';
  }
  echo '</select>
  </li>
  <li><span>' . $LANG['payments_form_cvv'] . ':</span> <input type="text" name="card[cvv]" value="' . ( isset( $_POST['card']['cvv'] ) ? htmlspecialchars( $_POST['card']['cvv'] ) : '' ) . '" maxlength="4" required /></li>

  <li><span></span><button>' . $LANG['payments_paynow_button'] . '</button></li>

  </ul>

  <input type="hidden" name="credit_card" />
  <input type="hidden" name="pay_credit_card" />
  <input type="hidden" name="token" value="' . $csrf . '" />

  </form>

  </div>';

  }

  echo '<div class="pay-buttons"' . ( isset( $_POST['credit_card'] ) && $docc ? ' style="display: none;"' : '' ) . '>

  <h2>' . $LANG['payments_choosetopay'] . ':</h2>

  <form method="POST" action="#" class="buttons">';
  if( $docc ) {
    echo '<button name="credit_card"><img src="' . DEFAULT_IMAGES_LOC . '/cards.png" alt="" style="width: 50px; max-height: 30px; display: block; padding: 10px 0;" /></button>';
  }
  if( $payment->do_direct() ) {
    echo '<button name="pay_direct"><img src="' . $payment->gateway_info['image'] . '" alt="" style="width: 50px; max-height: 30px; display: block; padding: 10px 0;" /></button>';
  }
  echo '<input type="hidden" name="token" value="' . $csrf . '" />
  </form>

  <div class="choose-gateway">
  ' . $LANG['payments_choosetopay'] . ':
  <select name="gateway" style="width: auto;">';
  foreach( \site\payment::gateways() as $id => $gateway ) {
    echo '<option value="payment.php?gateway=' . $id . '&amp;plan=' . $_GET['plan'] . '"' . ( $thegateway == $id ? ' selected' : '' ) . '>' . $gateway['name'] . '</option>';
  }
  echo '</select>
  </div>

  </div>';

  echo '</section>

  </div>

  <a href="index.php">' . $LANG['cancel'] . '</a>

  </div>

  </body>
  </html>';

}

catch( Exception $e ) {
  echo $e->getMessage();
}

$db->close();