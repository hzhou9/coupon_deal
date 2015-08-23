<?php

namespace plugin\CJApi\inc;

/** */

class client {


    public $cjurl = 'https://%s.api.cj.com/%s/%s';

    public $timeout = 10;


    public function __construct( $key = '' ) {

        $this->key = $key;

    }

    // not used in this plugin

    public function productSearch( $params = array() ) {

        return $this->Api( 'product-search', 'product-search', $params );

    }

    public function commissionDetails( $params = array() ) {

        return $this->Api( 'commission-detail', 'commissions', $params, 'v3' );

    }

    public function linkSearch( $params = array() ) {

        return $this->Api( 'linksearch', 'link-search', $params);

    }

    public function advertiserLookup( $params = array() ) {

        return $this->Api( 'advertiser-lookup', 'advertiser-lookup', $params, 'v3' );

    }

    public function supportLookup( $resource, $params = array() ) {

        return $this->Api( 'support-services', $resource, $params );

    }

    public function categories() {

      $categories = $this->Api( 'support-services', 'categories', array( 'advertiser-ids' => 0 ) );
      $categories = $categories['categories']['category'];

      asort($categories);

      return array_unique ( $categories );

    }

    public function Api( $subdomain, $resource, $params = array(), $version = 'v2' ) {

    if( empty( $this->key ) ) {
      throw new \Exception( 'Developer key is not set, check your <a href="?plugin=CJApi/options.php">settings</a> and try again.' );
    }

    $url = sprintf( $this->cjurl, $subdomain, $version, $resource ) . ( empty( $params ) ?: '?' . http_build_query( $params ) );

        $opts = array(
                'http' =>
                  array(
                      'method'  => 'GET',
                      'timeout' => $this->timeout,
                      'header' => array( 'Accept: application/xml', 'Authorization: ' . $this->key )
                      )
                );

        $result = @file_get_contents( $url, false, stream_context_create( $opts ) );

        $matches = array();
        preg_match( '#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches );

        switch( $matches[1] ) {
          case 200:
              return json_decode( json_encode( (array) simplexml_load_string( $result ) ), true );
          break;

          case 401:
              throw new \Exception( 'Unauthorized !' );
          break;

          default:
              throw new \Exception( 'Unexpected.' );
          break;
        }

    }

}