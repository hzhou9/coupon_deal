<?php

/*

SHOWING COUPONS OR PRODUCTS

*/

function searched_type() {
    if( isset( $_GET['type'] ) && strtolower( $_GET['type'] ) === 'products' ) {
      return 'products';
    }
    return 'coupons';
}

/*

PUT THE OBJECT INTO A GLOBAL VARIABLE

*/

$GLOBALS['searched_type'] = searched_type();
$GLOBALS['item'] = \query\main::store_infos( 0, array( 'update_views' => '' ) );
$GLOBALS['exists'] = \query\main::store_exists( 0, array( 'user_view' => '' ) );

/*

CHECK IF STORE EXISTS

*/

function exists() {
  return $GLOBALS['exists'];
}

/*

INFORMATIONS ABOUT STORE

*/

function the_item() {
  return $GLOBALS['item'];
}

/*

CHECK IF STORE HAVE REVIEWS

*/

function have_reviews( $category = array() ) {
  return \query\main::have_reviews( $category, 'store' );
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

    $desc = \query\main::get_option( 'meta_store_title' );
    $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

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

    $desc = \query\main::get_option( 'meta_store_desc' );
    $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

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

  return \query\main::store_avatar( $GLOBALS['item']->image );

} else

  return $image;

}

/*

ADD TO HISTORY

*/

$_SESSION['history'][$_GET['store']] = time();
arsort( $_SESSION['history'] );

if( count( $_SESSION['history'] ) > 30 ) {
  foreach( array_slice( array_keys( $_SESSION['history'] ), 30 ) as $id ) {
    unset( $_SESSION['history'][$id] );
  }
}