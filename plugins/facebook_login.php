<?php

if( \user\main::banned( 'login' ) || \user\main::banned( 'register' ) ) {
  header( 'Location: ' . $GLOBALS['siteURL'] );
  die;
} else if( \query\main::get_option( 'facebook_appID' ) === '' || \query\main::get_option( 'facebook_secret' ) === '' ) {
  die( 'This service it\'s unavailable for the moment.' );
}

include DIR . '/' . LBDIR . '/facebook-sdk-4.0/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

FacebookSession::setDefaultApplication( \query\main::get_option( 'facebook_appID' ), \query\main::get_option( 'facebook_secret' ) );

$helper = new FacebookRedirectLoginHelper( $GLOBALS['siteURL'] . '?plugin=' . $_GET['plugin'] );

try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  echo $ex->getMessage();
} catch( Exception $ex ) {
  echo $ex->getMessage();
}

if ( isset( $session ) ) {

  $me = (new FacebookRequest( $session, 'GET', '/me' ))->execute()->getGraphObject(GraphUser::className())->asArray();

  if( !isset( $me['email'] ) || !filter_var( $me['email'], FILTER_VALIDATE_EMAIL ) ) {

    echo 'Your facebook account it\'s not associated with a valid email address.';

  die;

  }

  header( 'Location: ' . $GLOBALS['siteURL'] . 'setSession.php?session=' . \user\main::insert_user( array( 'username' => $me['name'], 'email' => $me['email'] ), true, true ) );

} else if( empty( $_GET['code'] ) ) {
  header( 'Location:' . $helper->getLoginUrl( array('scope' => 'email') ) );
}