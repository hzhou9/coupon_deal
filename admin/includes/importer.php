<?php

class importer extends connector {

function __construct() {

  global $db;

  $this->db = $db;

  // load import
  $this->import( 'news' );

}


private function parse_answer( $type, $msg ) {

switch( $type ) {

case 'news':

$jdec = json_decode( $msg );

if( count( $jdec ) > 0 )

foreach( $jdec as $ln ) {

  $stmt = $this->db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "news (newsID, title, url, date) VALUES (?, ?, ?, NOW())" );
  $stmt->bind_param( "iss", $ln->ID, $ln->title, $ln->URL );
  $execute = $stmt->execute();
  $stmt->close();

}

break;

}

}


public function import( $type ) {

$data = array();

switch( $type ) {

case 'news':

// check interval for update

if( defined( 'CHECK_NEWS_TIME' ) ) {
  $lup = CHECK_NEWS_TIME > 1440 || CHECK_NEWS_TIME < 1 ? 3600 : CHECK_NEWS_TIME * 60;
} else {
  $lup = 3600; // 1 hour
}

$last_check = \query\main::get_option( 'check_news' );

if( ( $last_check + $lup ) > time() ) {
  return false;
}

$data['last_check'] = $last_check;

actions::set_option( array( 'check_news' => time() ) );

break;

default: return false; break;

}

try {

  $answer = $this->connect( $type, $data );

  $this->parse_answer( $type, $answer );

  return true;

}

catch( Exception $e ) {

return $e->getMessage();

}

}

}