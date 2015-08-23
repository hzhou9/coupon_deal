<?php

namespace plugins;

/** */

class click {

public static function view( $t, $intval ) {

global $db;

  switch( $intval ) {
    case 'hours': $where = 'DATE_FORMAT(date, "%d/%m/%Y,%H") = DATE_FORMAT(?, "%d/%m/%Y,%H")'; break;
    case 'weeks': $where = 'DATE_FORMAT(date, "%u/%Y") = DATE_FORMAT(?, "%u/%Y")'; break;
    case 'months': $where = 'DATE_FORMAT(date, "%m/%Y") = DATE_FORMAT(?, "%m/%Y")'; break;
    default: $where = 'DATE_FORMAT(date, "%d/%m/%Y") = DATE_FORMAT(?, "%d/%m/%Y")'; break;
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT date, COUNT(*), SUM(CASE WHEN user = 0 THEN 1 ELSE 0 END), SUM(CASE WHEN user > 0 THEN 1 ELSE 0 END) FROM " . DB_TABLE_PREFIX . "click WHERE " . $where);
  $stmt->bind_param( "s", $t );
  $stmt->execute();
  $stmt->bind_result( $date, $count, $visitor, $user );
  $stmt->fetch();
  $stmt->close();

  return (object)array( 'date' => $date, 'count' => $count, 'visitors' => $visitor, 'users' => $user );;

}

}