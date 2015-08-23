<?php

class connector {

public function connect( $source = '', $data = array() ) {

  $Send = array( 'key' => ( defined( 'KEY' ) ? KEY : 'undefined' ), 'host' => $_SERVER['HTTP_HOST'], 'php_version' => phpversion(), 'version' => ( defined( 'VERSION' ) ? VERSION : 'undefined' ), 'last_check' => ( isset( $data['last_check'] ) ? $data['last_check'] : 'error' ) );

  $Send_data = http_build_query( $Send );

  $opts = array('http' =>
      array(
          'method'  => 'POST',
          'content' => $Send_data,
          'timeout' => ( isset( $data['timeout'] ) ? (int) $data['timeout'] : 4 )
      )
  );

  switch( $source ) {

    case 'news':
      $url = 'http://couponscms.com/import/news/';
    break;

    default: $url = ''; break;

  }

  if( empty( $url ) ) {
    throw new Exception( 'Unknown source.' );
  }

  $result = @file_get_contents( $url, false, stream_context_create( $opts ) );

  if( !$result ) {

    throw new Exception( 'Connection error.' );

  }

    return $result;

  }

}