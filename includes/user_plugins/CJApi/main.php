<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

default:

echo '<div class="title">

<h2>Sales</h2>

<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=links">Links</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
<li><a href="?plugin=CJApi/options.php">Settings</a></li>
</ul>
</div>

</div>

<span>Sales generated by this website or all your websites</span>

</div>';

/** New Cj Client */

$cj = new \plugin\CJApi\inc\client( \query\main::get_option( 'cj_key' ) );

/** */

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="plugin" value="CJApi/main.php" />
<input type="hidden" name="action" value="sales" />

<select name="interval">';
for( $i = 0; $i <= 12; $i++ ) echo '<option value="' . ( $date = date( 'Y-m-01', ( $time = strtotime( '-' . $i . ' month' ) ) ) . ',' . date( 'Y-m-d', ( $time = strtotime( 'last day of -' . $i . ' month' ) ) ) ) . '"' . ( isset( $_GET['interval'] ) && urldecode( $_GET['interval'] ) == $date ? ' selected' : '' ) . '>' . date( 'F, Y', $time ) . '</option>';
echo '</select>
Website: ';
$websites = array( 0 => 'All' );
if( ( $website = \query\main::get_option( 'cj_site-id' ) ) && !empty( $website ) ) {
  $websites[$website] = $website;
}
foreach( $websites as $site => $sitename ) {
  echo ' <input type="radio" name="website" value="' . $site . '" id="' . $sitename . '"' . ( isset( $_GET['website'] ) && $_GET['website'] == $site ? ' checked' : ( $site == 0 ? ' checked' : '' ) ) . ' /> <label for="' . $sitename . '">' . $sitename . '</label>';
}
echo ' <button class="btn">View</button>
</form>

</div>';

try {

$lookup = array( 'date-type' => 'event' );

if( !empty( $_GET['interval'] ) ) {
  $fromto = array_map( 'trim', explode( ',', urldecode( $_GET['interval'] ) ) );
}

if( isset( $fromto[0] ) && isset( $fromto[1] ) ) {
  $lookup['start-date'] = $fromto[0];
  $lookup['end-date'] = $fromto[1];
} else {
  $lookup['start-date'] = date( 'Y-m-01' );
  $lookup['end-date'] = date( 'Y-m-d', strtotime( 'last day of this month' ) );
}
if( !empty( $_GET['website'] ) ) {
  $lookup['website-ids'] = $_GET['website'];
}

$commissions = $cj->commissionDetails( $lookup );
$attributes = $commissions['commissions']['@attributes'];

echo '<div class="results">' . $attributes['total-matched'] . ' results</div>';

if( $attributes['total-matched'] ) {

echo '<ul class="elements-list">

<li class="head">Name</li>';

if( isset( $commissions['commissions']['commission'][0] ) ) {
  $cmsons = $commissions['commissions']['commission'];
} else {
  $cmsons[] = (array) $commissions['commissions']['commission'];
}

foreach( $cmsons as $item ) {

  echo '<li>
  <div style="display: table; width: 100%;">

  <div class="info-div"><h2>' . htmlspecialchars( $item['advertiser-name'] ) . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item['posting-date'] ) ) . '</span></h2>

  <a href="javascript:void(0)" onclick="$(this).show_next({element:\'.infos\', type:\'rightnext\'}); return false;">More/Less</a>

  <div class="infos" style="display: none;">
  Type: <b>' . $item['action-type'] . '</b><br />
  Amount: <b>' . $item['commission-amount'] . '</b><br />
  <span style="color: #980000;">SID: <b>' . ( !empty( $item['sid'] ) ? $item['sid'] : 'not set' ) . '</b></span><br />
  AID: <b>' . $item['aid'] . '</b><br />
  Comission ID: <b>' . $item['commission-id'] . '</b><br />
  Country: <b>' . ( !empty( $item['country'] ) ? $item['country'] : 'not set' ) . '</b><br />
  Order ID: <b>' . $item['order-id'] . '</b><br />
  Website ID: <b>' . $item['website-id'] . '</b><br />
  Event Date: <b>' . $item['event-date'] . '</b><br />

  </div>

  </div></div>

  <div style="clear:both;"></div>

  </li>';

}

echo '</ul>';

} else {

  echo '<div class="a-alert">No sales for this period.</div>';

}

}

catch( Exception $e ) {

  echo '<div class="a-error">' . $e->getMessage() . '</div>';

}

break;

}