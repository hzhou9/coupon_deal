<?php

if( !isset( $_GET['secret'] ) || $_GET['secret'] !== \query\main::get_option( 'cron_secret' ) ) {
  die( 'Unauthorized' );
}

include ADMINDIR . '/includes/feed.php';
include ADMINDIR . '/includes/admin.php';
include ADMINDIR . '/includes/query.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  $ids = array();
  foreach( \query\main::while_stores( array( 'max' => 0, 'show' => 'feed' ) ) as $store ) {
    $ids[] = $store->feedID;
  }

  $csuc = $cusuc = $cerr = $cuerr = 0;

  if( !empty( $ids ) ) {

  $last_check = \query\main::get_option( 'lfeed_check' );

  /*

  UPDATE COUPONS

  */

  if( (int) \query\main::get_option( 'feed_moddt' ) !== 0 ) {

  try {

    $coupons = $feed->coupons( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'update' => \site\utils::timeconvert( date( 'Y-m-d, H:i:s', $last_check ), $feed->timezone ) ) );

    if( !empty( $coupons['Count'] ) ) {

    for( $cp = 1; $cp <= ceil( $coupons['Count'] / 10 ); $cp++ ) {

    if( $cp != 1 ) {
      $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
    }

    foreach( $coupons['List'] as $coupon ) {

    if( ( $couponi = admin_query::coupon_imported( $coupon->ID ) ) && actions::edit_item2( $couponi->ID, array( 'name' => $coupon->Title, 'link' => $coupon->URL, 'code' => $coupon->Code, 'description' => $coupon->Description, 'tags' => $coupon->Tags, 'start' => $coupon->Start_Date, 'end' => $coupon->End_Date ) ) ) {
      $cusuc++;
    } else {
      $cuerr++;
    }

    }

    usleep( 500000 ); // let's put a break after every page, 500 000 microseconds. that means a half of a second

    }

    }

  }

  catch( Exception $e ) { }

  }

  /*

  IMPORT COUPONS

  */

  try {

    $coupons = $feed->coupons( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'view' => (!isset( $_GET['import_expired'] ) || $_GET['import_expired'] !== 'yes' ? 'active' : ''), 'date' => \site\utils::timeconvert( date( 'Y-m-d, H:i:s', $last_check ), $feed->timezone ) ) );

    if( !empty( $coupons['Count'] ) ) {

    for( $cp = 1; $cp <= ceil( $coupons['Count'] / 10 ); $cp++ ) {

    if( $cp != 1 ) {
      $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
    }

    foreach( $coupons['List'] as $coupon ) {

    if( !admin_query::coupon_imported( $coupon->ID ) && ( $store = admin_query::store_imported( $coupon->Store_ID ) ) && actions::add_item( array( 'feedID' => $coupon->ID, 'store' => $store->ID, 'category' => $store->catID, 'popular' => 0, 'exclusive' => 0, 'name' => $coupon->Title, 'link' => $coupon->URL, 'code' => $coupon->Code, 'description' => $coupon->Description, 'tags' => $coupon->Tags, 'cashback' => 0, 'start' => $coupon->Start_Date, 'end' => $coupon->End_Date, 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) ) {
      $csuc++;
    } else {
      $cerr++;
    }

    }

    usleep( 500000 ); // let's put a break after every page, 500 000 microseconds. that means a half of a second

    }

    }

    actions::set_option( array( 'lfeed_check' => time() ) ); // update time for last feed check

  }

  catch( Exception $e ) { }

  }

  // you can use $csuc, $cusuc, $cerr, $cuerr variables to create logs or something ...

  echo 'OK';

}

catch( Exception $e ) {
  echo $e->getMessage();
}