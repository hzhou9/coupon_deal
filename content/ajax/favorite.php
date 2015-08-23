<?php

if( isset( $_GET['action'] ) && $GLOBALS['me'] ) {

  switch( $_GET['action'] ) {

    case 'addFavorite':

    $answer = \user\main::favorite( $GLOBALS['me']->ID, $_GET['id'], 'add' );

    if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
      echo json_encode( array( 'answer' => ( $answer ? true : false ) ) );
      die;
    } else {
      header( 'Location: ' . ( isset( $_GET['backto'] ) ? htmlspecialchars( $_GET['backto'] ) : $GLOBALS['siteURL'] ) );
      die;
    }

    break;

    case 'remFavorite':

    $answer = \user\main::favorite( $GLOBALS['me']->ID, $_GET['id'], 'remove' );

    if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
      echo json_encode( array( 'answer' => ( $answer ? true : false ) ) );
      die;
    } else {
      header( 'Location: ' . ( isset( $_GET['backto'] ) ? htmlspecialchars( $_GET['backto'] ) : $GLOBALS['siteURL'] ) );
      die;

    }

    break;

  }

}