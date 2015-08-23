<?php

/*

PUT THE OBJECT INTO A GLOBAL VARIABLE

*/

$GLOBALS['item'] = \query\main::product_infos( 0, array( 'update_views' => '' ) );
$GLOBALS['exists'] = \query\main::product_exists( 0, array( 'user_view' => '' ) );

/*

CHECK IF PRODUCT EXISTS

*/

function exists() {
  return $GLOBALS['exists'];
}

/*

INFORMATIONS ABOUT PRODUCT

*/

function the_item() {
  return $GLOBALS['item'];
}

/*

METATAGS - TITLE

*/

function meta_title() {

if( $GLOBALS['exists'] > 0 ) {

  if( !empty( $GLOBALS['item']->meta_title ) ) {

    $repl = array( '%YEAR%' => date('Y'), '%MONTH%' => date('F') );
    return str_replace( array_keys( $repl ), array_values( $repl ), $GLOBALS['item']->meta_title );

  } else {

    $desc = \query\main::get_option( 'meta_product_title' );
    $repl = array( '%NAME%' => $GLOBALS['item']->title, '%STORE_NAME%' => $GLOBALS['item']->store_name, '%EXPIRATION%' => date( 'Y/m/d', strtotime( $GLOBALS['item']->expiration_date ) ), '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), htmlspecialchars( $desc ) );

  }

} else

  return meta_default( '', \query\main::get_option( 'sitetitle' ) );

}

/*

METATAGS - DESCRIPTION

*/

function meta_description() {

if( $GLOBALS['exists'] > 0 ) {

  if( !empty( $GLOBALS['item']->meta_description ) ) {

    $repl = array( '%YEAR%' => date('Y'), '%MONTH%' => date('F') );
    return str_replace( array_keys( $repl ), array_values( $repl ), $GLOBALS['item']->meta_description );

  } else {

    $desc = \query\main::get_option( 'meta_product_desc' );
    $repl = array( '%NAME%' => $GLOBALS['item']->title, '%STORE_NAME%' => $GLOBALS['item']->store_name, '%EXPIRATION%' => date( 'Y/m/d', strtotime( $GLOBALS['item']->expiration_date ) ), '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), htmlspecialchars( $desc ) );

  }

} else

  return meta_default( '', \query\main::get_option( 'meta_description' ) );

}

/*

METATAGS - IMAGE

*/

function meta_image( $image = '' ) {

if( $GLOBALS['exists'] > 0 ) {

  return \query\main::store_avatar( $GLOBALS['item']->store_img );

} else

  return $image;

}