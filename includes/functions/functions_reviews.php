<?php

/*

PUT THE OBJECT INTO A GLOBAL VARIABLE

*/

$GLOBALS['item'] = \query\main::store_infos();
$GLOBALS['exists'] = \query\main::store_exists();

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

CHECK IF HAVE ITEMS

*/

function have_items( $category = array() ) {

    $GLOBALS['have_items'] = \query\main::have_reviews( $category, 'store' );

    /*

    ACTIVATE PAGES INFORMATIONS IF FUNCTION have_items() IS CALLED

    */

      /*

      NUMBER OF RESULTS

      */

      function results() {
        return $GLOBALS['have_items']['results'];
      }

      /*

      THIS PAGE IS

      */

      function page() {
        return $GLOBALS['have_items']['page'];
      }

      /*

      NUMBER OF PAGES

      */

      function pages() {
        return $GLOBALS['have_items']['pages'];
      }

      /*

      NEXT PAGE

      */

      function next_page() {
        if( !empty( $GLOBALS['have_items']['next_page'] ) ) {
          return $GLOBALS['have_items']['next_page'];
        }
        return false;
      }

      /*

      PREVIEW PAGE

      */

      function prev_page() {
        if( !empty( $GLOBALS['have_items']['prev_page'] ) ) {
          return $GLOBALS['have_items']['prev_page'];
        }
        return false;
      }

      return $GLOBALS['have_items']['results'];

}

/*

SHOW REVIEWS

*/

function items( $category = array() ) {
  return \query\main::while_reviews( $category, 'store' );
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

    $desc = \query\main::get_option( 'meta_reviews_title' );
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

    $desc = \query\main::get_option( 'meta_reviews_desc' );
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