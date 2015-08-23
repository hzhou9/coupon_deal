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

$GLOBALS['searched_type'] = searched_type();

/*

CHECK IF HAVE ITEMS

*/

function have_items( $category = array() ) {

    if( $GLOBALS['searched_type'] === 'products' ) {
      $GLOBALS['have_items'] = \query\main::have_products( $category, 'search' );
    } else {
      $GLOBALS['have_items'] = \query\main::have_items( $category, 'search' );
    }

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

SHOW COUPONS/PRODUCTS

*/

function items( $category = array() ) {
  if( $GLOBALS['searched_type'] === 'products' ) {
    return \query\main::while_products( $category, 'search' );
  } else {
    return \query\main::while_items( $category, 'search' );
  }
}

/*

SEARCHED TEXT

*/

function searched( $v = 'text' ) {

global $GET;

$text = '';

  switch( $v ) {
      case 'text':
      if( gettype( $GET['id'] ) === 'string' ) {
      $text = substr( htmlspecialchars( $GET['id'] ), 0, 50 );
      }
      break;
  }

    return $text;

}