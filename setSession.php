<?php

if( isset( $_GET['session'] ) ) {

  setcookie( 'user-session', $_GET['session'], time() + 3600 * (24 * 60), '/' );

  header( 'Location: ' . ( !empty( $_GET['back'] ) ? $_GET['back'] : '../index.php' ) );

  die;

}