<?php

if( $_SERVER['REQUEST_METHOD'] && isset( $_POST['csrf'] ) == $_SESSION['csrf']['ajax_subscribe'] ) {

  $response = array();

  $pd = \site\utils::validate_user_data( $_POST['subscribe'] );

  try {

    $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

    $type = \user\main::subscribe( $id, $pd );
    $response['state'] = 'success';
    $response['message'] = ( $type == 1 ? sprintf( $LANG['newsletter_reqconfirm'], $pd['email'] ) : $LANG['newsletter_success'] );

    unset( $_SESSION['csrf']['ajax_subscribe'] );

  }

  catch( Exception $e ){
    $response['state'] = 'error';
    $response['message'] = $e->getMessage();
  }

  echo json_encode( $response );

}