<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS */

error_reporting( E_ALL );

/** REQUIRE SETTINGS */

include 'settings.php';

/** CONNECT TO DB */

include IDIR . '/site/db.php';

if( $db_conn = $db->connect_errno && is_dir( 'install' ) ) {

  include 'install/index.php';
  die;

} else if( $db_conn ) {

  die('Failed to connect to MySQL (' . $db->connect_errno . ') ' . $db->connect_error);

}

$db->set_charset( DB_CHARSET );

/** */

spl_autoload_register(function ( $cn ) {

  $type = strstr( $cn, '\\', true );
  if( $type == 'plugin' ) {
    $cn = str_replace( '\\', '/', $cn );
    include DIR . '/' . UPDIR . '/' . substr( $cn, strpos( $cn, '/' )+1 ) . '.php';
  } else {
    include DIR . '/' . IDIR . '/' . str_replace( '\\', '/', $cn ) . '.php';
  }

});

/** */

if( !empty( $_GET ) ) {

if( defined( 'SEO_LINKS' ) && SEO_LINKS ) {

$sp = array(

'pages' => array( 'p' => current( $_GET ) ),
'tpage' => array( 'tpage' => current( $_GET ) ),
'ajax' => array( 'ajax' => current( $_GET ) ),
'cron' => array( 'cron' => current( $_GET ) ),
\query\main::get_option( 'seo_link_coupon' ) => array( 'id' => current( $_GET ) ),
\query\main::get_option( 'seo_link_product' ) => array( 'product' => current( $_GET ) ),
\query\main::get_option( 'seo_link_category' ) => array( 'cat' => current( $_GET ) ),
\query\main::get_option( 'seo_link_search' ) => array( 's' => isset( $_GET['s'] ) ? $_GET['s'] : '' ),
\query\main::get_option( 'seo_link_store' ) => array( 'store' => current( $_GET ) ),
\query\main::get_option( 'seo_link_stores' ) => array( 'stores' => current( $_GET ) ),
\query\main::get_option( 'seo_link_reviews' ) => array( 'reviews' => current( $_GET ) ),
\query\main::get_option( 'seo_link_user' ) => array( 'user' => current( $_GET ) ),
\query\main::get_option( 'seo_link_plugin' ) => array( 'plugin' => current( $_GET ) )

);

if( in_array( key( $_GET ), array_keys( $sp ) ) ) {

  $k = key( $sp[key( $_GET )] );
  $v = $sp[key( $_GET )][$k];

}

} else {

  $k = key( $_GET );
  $v = current( $_GET );

}

if( !empty( $k ) )

 switch( $k ) {

   case 'p':
   $GET['loc'] = 'page';
   $GET['id'] = $v;
   break;

   case 'id':
   $GET['loc'] = 'single';
   $GET['id'] = $v;
   break;

   case 'product':
   $GET['loc'] = 'product';
   $GET['id'] = $v;
   break;

   case 'cat':
   $GET['loc'] = 'category';
   $GET['id'] = $v;
   break;

   case 's':
   $GET['loc'] = 'search';
   $GET['id'] = $v;
   break;

   case 'store':
   $GET['loc'] = 'store';
   $GET['id'] = $v;
   break;

   case 'stores':
   $GET['loc'] = 'stores';
   break;

   case 'reviews':
   $GET['loc'] = 'reviews';
   $GET['id'] = $v;
   break;

   case 'user':
   $GET['loc'] = 'user';
   $GET['id'] = $v;
   break;

   // This will read a page from themes location
   case 'tpage':
   $GET['loc'] = 'tpage';
   $GET['id'] = $v;
   break;

   case 'ajax':
   $GET['loc'] = 'ajax';
   $GET['id'] = $v;
   break;

   case 'cron':
   $GET['loc'] = 'cron';
   $GET['id'] = $v;
   break;

   case 'plugin':
   $GET['loc'] = 'plugin';
   $GET['id'] = $v;
   break;

 }

}

$load = new \main\load;
$LANG = $load->get_language();
$load->execute();

$db->close();