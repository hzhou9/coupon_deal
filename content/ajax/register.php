<?php

if( $_SERVER['REQUEST_METHOD'] && isset( $_POST['csrf'] ) == $_SESSION['csrf']['ajax_register'] ) {

  $response = array();

  $pd = \site\utils::validate_user_data( $_POST['register'] );

  try {

    $session = \user\main::register( $pd );
    $response['state'] = 'success';
    $response['message'] = $LANG['register_success'];
    $response['session'] = $GLOBALS['siteURL'] . '/setSession.php?session=' . $session;

    unset( $_SESSION['csrf']['ajax_register'] );

  }

  catch( Exception $e ){
    $response['state'] = 'error';
    $response['message'] = $e->getMessage();
  }

  echo json_encode( $response );

}