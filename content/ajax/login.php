<?php

if( $_SERVER['REQUEST_METHOD'] && isset( $_POST['csrf'] ) == $_SESSION['csrf']['ajax_login'] ) {

  $response = array();

  $pd = \site\utils::validate_user_data( $_POST['login'] );

  try {

    $session = \user\main::login( $pd );
    $response['state'] = 'success';
    $response['message'] = $LANG['login_success'];
    $response['session'] = $GLOBALS['siteURL'] . '/setSession.php?session=' . $session;

    unset( $_SESSION['csrf']['ajax_login'] );

  }

  catch( Exception $e ){
    $response['state'] = 'error';
    $response['message'] = $e->getMessage();
  }

  echo json_encode( $response );

}