<?php

@session_start();

if( !ab_to( array( 'raports' => 'view' ) ) ) die;

$cols = $rows = array();

$cols[] = array( 'type' => 'string', 'id' => 'Interval', 'label' => 'Interval' );
$cols[] = array( 'type' => 'number', 'id' => 'Total', 'label' => 'Total' );
$cols[] = array( 'type' => 'number', 'id' => 'Visitors', 'label' => 'Visitors' );
$cols[] = array( 'type' => 'number', 'id' => 'Users', 'label' => 'Users' );

// intervals

$intvals = array( 'hours', 'days', 'weeks', 'months' );
if( !isset( $_GET['view'] ) || !in_array( $_GET['view'], $intvals ) ) {
  $intval = 'days';
} else {
  $intval = $_GET['view'];
}

// set in a session

$_SESSION['ses_set']['lgcl'] = $intval;

$limit = isset( $_GET['limit'] ) && $_GET['limit'] > 1 && $_GET['limit'] < 25 ? $_GET['limit'] : 10;
$limit = $limit - 1;

switch( $intval ) {

  case 'hours':
  $time[] = date( 'Y-m-d, H:00', strtotime( 'this hour' ) );
  foreach( range(1, $limit) as $i ) {
  $time[] = date( 'Y-m-d, H:00', strtotime( '-' . $i . ' hour' ) );
  }
  $sdate = \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i';
  break;

  case 'weeks':
  $time[] = date( 'Y-m-d', strtotime( 'this week' ) );
  foreach( range(1, $limit) as $i ) {
  $time[] = date( 'Y-m-d', strtotime( '-' . $i . ' week' ) );
  }
  $sdate = 'd M';
  break;

  case 'months':
  $time[] = date( 'Y-m-d', strtotime( 'this month' ) );
  foreach( range(1, $limit) as $i ) {
  $time[] = date( 'Y-m-d', strtotime( '-' . $i . ' month' ) );
  }
  $sdate = 'M';
  break;

  default:
  $time[] = date( 'Y-m-d', strtotime( 'today' ) );
  foreach( range(1, $limit) as $i ) {
  $time[] = date( 'Y-m-d', strtotime( '-' . $i . ' day' ) );
  }
  $sdate = 'd M';
  break;

}

foreach( $time as $t ) {

  $item = \plugins\click::view( $t, $intval );

  $rows[] = array( 'c' => array( array( 'v' => date( $sdate, strtotime( $t ) ) . ( $intval == 'weeks' ? " - " . date( $sdate, strtotime( '+ 7 days', strtotime( $t ) ) ) : '' ) ), array( 'v' => $item->count ),  array( 'v' => $item->visitors ),  array( 'v' => $item->users ) ) );

}

echo json_encode( array( 'cols' => $cols, 'rows' => $rows ) );