<?php

/*

CHECK IF AN USER IT'S ABLE TO ...

*/

function ab_to( $action = array() ) {

  // check if this logged administrator it's able to do ...

  if( $GLOBALS['me']->is_admin ) {
    return true;
  }

  $urole = $GLOBALS['me']->Erole;

  foreach( $action as $k => $v ) {

    if( !in_array( $k, array_keys( $urole ) ) || !in_array( $v, array_keys( $urole[$k] ) ) ) {
      return false;
    }
  }

  return true;

}

/*

CHECK CSRF

*/

function check_csrf( $post, $session ) {

  return \site\utils::check_csrf( $post, $session );

}

/*

CHECK IF AN IP ADDRESS IS VALID

*/

function valid_ip( $ip ) {

  if( preg_match( '/^([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})$/', $ip ) ) {
    return true;
  }
  return false;

}

/*

ADD COMMON HEAD

*/

function add_extra_head() {

  $cache = new \cache\main;

  if( $show_from_cache = $cache->check( 'admin_head' ) ) {

    return $show_from_cache;

  } else {

  $head = '';

  foreach( \query\others::while_head_lines( array_merge( array( 'max' => 0, 'show' => 'admin', 'orderby' => 'date desc' ) ) ) as $line ) {
    $head .= \site\plugin::replace_constant( $line->text ) . "\n";
  }

  $cache->add( 'admin_head', $head );

  return $head;

  }

}