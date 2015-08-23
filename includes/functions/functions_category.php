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
$GLOBALS['item'] = \query\main::category_infos();
$GLOBALS['exists'] = \query\main::category_exists();

/*

CHECK IF CATEGORY EXISTS

*/

function exists() {
    return $GLOBALS['exists'];
}

/*

INFORMATIONS ABOUT CATEGORY

*/

function the_item() {
    return $GLOBALS['item'];
}

/*

CHECK IF HAVE COUPONS/PRODUCTS

*/

function have_items( $category = array() ) {

    if( $GLOBALS['searched_type'] === 'products' ) {
      $GLOBALS['have_items'] = \query\main::have_products( $category, 'category' );
    } else {
      $GLOBALS['have_items'] = \query\main::have_items( $category, 'category' );
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

SHPW COUPONS/PRODUCTS

*/

function items( $category = array() ) {
  if( $GLOBALS['searched_type'] === 'products' ) {
    return \query\main::while_products( $category, 'category' );
  } else {
    return \query\main::while_items( $category, 'category' );
  }
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

    $desc = \query\main::get_option( 'meta_category_title' );
    $repl = array( '%NAME%' => $GLOBALS['item']->name, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), htmlspecialchars( $desc ) );

  }

} else

  return htmlspecialchars( \query\main::get_option( 'sitetitle' ) );

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

    $desc = \query\main::get_option( 'meta_category_desc' );
    $repl = array( '%NAME%' => $GLOBALS['item']->name, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), htmlspecialchars( $desc ) );

  }

} else

  return htmlspecialchars( \query\main::get_option( 'sitetitle' ) );

}