<?php

namespace cache;

/** */

class Apc {

function __construct() {

  $this->installed = $this->installed();

}

public function installed() {

  if(extension_loaded('apc') && ini_get('apc.enabled')) {
    return true;
  }
  return false;

}

public function exists( $key ) {

  if( !$this->installed ) {
    return false;
  }
  if( apc_fetch( $key ) ) {
    return true;
  }
  return false;

}

public function add( $key, $value, $expiration = 0 ) {

  if( !$this->installed ) {
    return false;
  }
  if( apc_add( $key, $value, $expiration ) ) {
    return true;
  }
  return false;

}

public function store( $key, $value, $expiration = 0 ) {

  if( !$this->installed ) {
    return false;
  }
  if( apc_add( $key, $value, $expiration ) ){
    return true;
  }
  return false;

}

public function update( $key, $value ) {
  if( !$this->installed ) {
    return false;
  }
  if( $this->exists( $key ) ) {
    $this->remove( $key );
  }
  $this->add( $key, $value );
}


public function remove( $key ) {

  if( !$this->installed ) {
    return false;
  }
  if( apc_delete( $key ) ) {
    return true;
  }
  return false;

}

public function check( $key ) {

  if( !$this->installed ) {
    return false;
  }
  $data = apc_fetch( $key );
  if( !$data ) {
    return false;
  }
  return $data;

}

}