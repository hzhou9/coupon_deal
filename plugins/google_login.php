<?php

if( \user\main::banned( 'login' ) || \user\main::banned( 'register' ) ) {
  header( 'Location: ' . $GLOBALS['siteURL'] );
  die;
} else if( \query\main::get_option( 'google_clientID' ) === '' || \query\main::get_option( 'google_secret' ) === '' || \query\main::get_option( 'google_ruri' ) === '' ) {
  die( 'This service it\'s unavailable for the moment.' );
}

include DIR . '/' . LBDIR . '/google-api-php-client-master/autoload.php';

$client = new Google_Client();
$client->setApplicationName('Login to ' . \query\main::get_option( 'sitename' ));
$client->setClientId( \query\main::get_option( 'google_clientID' ) );
$client->setClientSecret( \query\main::get_option( 'google_secret' ) );
$client->setRedirectUri( \query\main::get_option( 'google_ruri' ) );
$client->setScopes( 'https://www.googleapis.com/auth/plus.profile.emails.read' );

if ( !empty( $_GET['code'] ) ) {

try {

  $client->authenticate( $_GET['code'] );
  $_SESSION['access_token'] = $client->getAccessToken();

  header('Location: ' . filter_var( $GLOBALS['siteURL'] , FILTER_SANITIZE_URL ));

} catch( Exception $e ) {
  echo $e->getMessage();
  die;
}

}

if ( isset( $_SESSION['access_token'] ) ) {
  $client->setAccessToken( $_SESSION['access_token'] );
}

if ( $client->getAccessToken() ) {

  $_SESSION['access_token'] = $client->getAccessToken();
  $token_data = $client->verifyIdToken()->getAttributes();

}


if( isset( $token_data ) ) {

  $me = (new Google_Service_Plus( $client ))->people->get('me');

  if( !isset( $me['emails'][0]['value'] ) || !filter_var( $me['emails'][0]['value'], FILTER_VALIDATE_EMAIL ) ) {

    echo 'Your Google+ account it\'s not associated with a valid email address.';

  die;

  }

  header( 'Location: ' . $GLOBALS['siteURL'] . 'setSession.php?session=' . \user\main::insert_user( array( 'username' => $me['displayName'], 'email' => $me['emails'][0]['value'] ), true, true ) );

} else {
  header( 'Location: ' . $client->createAuthUrl() );
}