<?php

namespace site;

/** */

class feed {

public static function servers() {

  $servers = array();
  // built-in servers
  $servers['ggcoupon.com']['name'] = 'ggCoupon.com';
  $servers['ggcoupon.com']['config'] = IDIR . '/feedservers/ggCoupon.com.php';
  // user plugins
  foreach( \query\main::user_plugins( 'feed_server' ) as $server ) {
  $servers['up_' .strtolower( $server->name )]['name'] = $server->name;
  $servers['up_' .strtolower( $server->name )]['config'] = UPDIR . '/' . $server->main_file;
  }
  return $servers;

}

public static function server( $server ) {

  $server = strtolower( $server );
  $servers = feed::servers();
  if( in_array( $server, array_keys( $servers ) ) ) {
    return $servers[$server];
  }
  return false;

}

}