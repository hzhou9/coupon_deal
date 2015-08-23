<?php

/*

PUT THE OBJECT INTO A GLOBAL VARIABLE

*/

$GLOBALS['item'] = \query\main::page_infos( 0, array( 'update_views' => '' ) );
$GLOBALS['exists'] = \query\main::page_exists( 0, array( 'user_view' => '' ) );

/*

CHECK IF PAGE EXISTS

*/

function exists() {
  return $GLOBALS['exists'];
}

/*

INFORMATIONS ABOUT PAGE

*/

function the_page() {
  return $GLOBALS['item'];
}

/*

METATAGS - TITLE

*/

function meta_title() {

if( $GLOBALS['exists'] > 0 && !empty( $GLOBALS['item']->meta_title ) ) {

  $repl = array( '%YEAR%' => date('Y'), '%MONTH%' => date('F') );
  return str_replace( array_keys( $repl ), array_values( $repl ), $GLOBALS['item']->meta_title );

} else

  return htmlspecialchars( \query\main::get_option( 'sitetitle' ) );

}

/*

METATAGS - DESCRIPTION

*/

function meta_description() {

if( $GLOBALS['exists'] > 0 && !empty( $GLOBALS['item']->meta_description ) ) {

  $repl = array( '%YEAR%' => date('Y'), '%MONTH%' => date('F') );
  return str_replace( array_keys( $repl ), array_values( $repl ), $GLOBALS['item']->meta_description );

} else

  return meta_default( '', \query\main::get_option( 'meta_description' ) );

}