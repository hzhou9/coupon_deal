<?php

namespace cache;

/** */

class main {

/*

PREFERRED ADAPTER

*/

protected $pref_adapter = PREF_CACHE;

/*

CACHE LIFETIME

*/

protected $lifetime = DEF_CACHE;


/*

Construct class

*/

function __construct() {

  $adapter = strtolower( $this->pref_adapter );

  switch( $adapter ) {
    default:
    $this->adapter = new Apc;
    break;
  }

}

/*

Check if a key it's stored in cache

*/

public function exists( $key ) {

  $this->adapter->exist( $key );

}

/*

Add a new entry in cache

*/

public function add( $key, $value ) {

  $this->adapter->add( $key, $value, $this->lifetime );

}

/*

Store a new entry in cache

*/

public function store( $key, $value ) {

  $this->adapter->store( $key, $value, $this->lifetime );

}

/*

Update a key in cache

*/

public function update( $key, $value ) {

  $this->adapter->update( $key, $value );

}

/*

Remove a key from cache

*/

public function remove( $key ) {

  $this->adapter->remove( $key );

}

/*

Check if a key exists in cache

*/

public function check( $key ) {

  $this->adapter->check( $key );

}

}