<?php

if( !isset( $_GET['secret'] ) || $_GET['secret'] !== \query\main::get_option( 'cron_secret' ) ) {
  die( 'Unauthorized' );
}

include ADMINDIR . '/includes/admin.php';

actions::cleardata( ( isset( $_GET['coupons'] ) && $_GET['coupons'] == 'true' ? true : false ), ( isset( $_GET['days'] ) && (int) $_GET['days'] > 0 ? (int) $_GET['days'] : 30 ) );

echo 'OK';