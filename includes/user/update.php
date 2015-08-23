<?php

namespace user;

/** */

class update {

/*

ADD POINTS

*/

public static function add_points( $user, $credits = 0) {

global $db;

  if( $credits === 0 ) {
    return true;
  }

  $stmt = $db->stmt_init();

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET points = points + ? WHERE id = ?" );
  $stmt->bind_param( "ii", $credits, $user );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

/*

ADD CREDITS

*/

public static function add_credits( $user, $credits = 0) {

global $db;

  if( $credits === 0 ) {
    return true;
  }

  $stmt = $db->stmt_init();

  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET credits = credits + ? WHERE id = ?" );
  $stmt->bind_param( "ii", $credits, $user );
  $execute = $stmt->execute();
  $stmt->close();

  if( $execute ) {
    return true;
  }

  return false;

}

}