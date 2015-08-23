<?php

class feed {

/*

FEED SERVER

*/

public $server = '';

/*

TIMEOUT, TIME UNTIL STOP TRYING TO CONNECT TO FEED SERVER

*/

public $timeout = 5;

/*

EXPORT RESULTS AS OBJECT OR ARRAY

*/

public $export_as = 'array';


/*

Construct class

*/

function __construct( $user = '', $pass = '' ) {

global $LANG;

  $this->user = $user;
  $this->pass = $pass;
  $this->lang = $LANG;

  $server = $this->checkserver();

  $this->urls = $server;
  $this->timezone = isset( $server['TIMEZONE'] ) ? $server['TIMEZONE'] : date('e');

}

/*

Check if this is a valid Feed server and ready to be used

*/

private function checkserver() {

  if( empty( $this->server ) ) {
    $this->server = \query\main::get_option( 'feedserver' );
  }
  $server = \site\feed::server( $this->server );
  if( !$server ) {
    throw new Exception( $this->lang( 'feed_e_invalid' ) );
  }
  if( !file_exists( DIR . '/' . $server['config'] ) ) {
    throw new Exception( $this->lang['feed_e_configmiss'] );
  }

  @include DIR . '/' . $server['config'];

  if( !isset( $server['COUPON_URL'] ) ||
  !isset( $server['COUPONS_URL'] ) ||
  !isset( $server['STORE_URL'] ) ||
  !isset( $server['STORES_URL'] ) ||
  !isset( $server['CATEGORIES_URL'] ) ) {
    throw new Exception( $this->lang['feed_e_serverr'] );
  }

  return $server;

}

/*

Connect to Feed server

*/

private function connect( $url = '', $method = 'GET', $getdata = array(), $postdata = array() ) {

  if( !$this->urls ) {
    throw new Exception( $this->lang['feed_e_serverr'] );
  }

 $opts = array('http' =>
      array(
          'method'  => $method,
          'content' => http_build_query( $postdata ),
          'timeout' => $this->timeout
      )
  );

  $result = @file_get_contents( trim( $url ) . '?' .  http_build_query( array_merge( array( 'UserID' => $this->user, 'Key' => $this->pass ), $getdata ) ), false, stream_context_create( $opts ) );

  if( (boolean) $result === false ) {
    throw new Exception( $this->lang['feed_e_servtout'] );
  }

  switch( current( $http_response_header ) ) {
    case 'HTTP/1.1 200 OK':
        return $this->parse( $result );
    break;

    case 'HTTP/1.1 204 No Content':
        // throw new Exception( 'No Content' );
    break;

    case 'HTTP/1.1 401 Unauthorized':
        throw new Exception( 'Unauthorized !' );
    break;

    case 'HTTP/1.1 402 Payment Required':
        throw new Exception( 'You have reached the limit ! Please check your limits.' );
    break;

    case 'HTTP/1.1 404 Not Found':
        throw new Exception( 'The content that you tried to get wasn\'t found.' );
    break;

    case 'HTTP/1.1 405 Method Not Allowed':
        throw new Exception( 'Method not allowed.' );
    break;

    default:
        throw new Exception( 'Unexpected.' );
    break;
  }

}

/*

Parse answer

*/

public function parse( $content ) {

  $content = json_decode( $content );

  switch( $this->export_as ) {
    case 'object':
        return (object) $content;
    break;
    default:
        return (array) $content;
    break;
  }

}

/*

Get informations about a store from source

*/

public function store( $ID = 0 ) {

  return $this->connect( $this->urls['STORE_URL'], 'GET', array( 'ID' => $ID ) );

}

/*

Get informations about stores from source

*/

public function stores( $getdata = array(), $postdata = array() ) {

  return $this->connect( $this->urls['STORES_URL'], 'GET', (array) $getdata, (array) $postdata );

}

/*

Get informations about a coupon from source

*/

public function coupon( $ID = 0, $postdata = array() ) {

  return $this->connect( $this->urls['COUPON_URL'], 'GET', array( 'ID' => $ID ) );

}

/*

Get informations about coupons from source

*/

public function coupons( $getdata = array(), $postdata = array() ) {

  return $this->connect( $this->urls['COUPONS_URL'], 'GET', (array) $getdata, (array) $postdata );

}

/*

Get informations about categories from source

*/

public function categories( $getdata = array() ) {

  return $this->connect( $this->urls['CATEGORIES_URL'], 'GET', (array) $getdata );

}

}