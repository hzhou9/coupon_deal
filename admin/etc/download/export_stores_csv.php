<?php

if( !ab_to( array( 'stores' => 'export' ) ) || $_SERVER["REQUEST_METHOD"] != 'POST' || !isset( $_POST['csrf'] ) || !isset( $_SESSION['stores_csrf'] ) || $_POST['csrf'] != $_SESSION['stores_csrf'] ) {

// redirect to the last page if this can't be downloaded for any reason from above

echo '<script type="text/javascript">

window.onload = function(){
  window.history.go(-1);
}

</script>';

die;

}

$from = isset( $_POST['date']['from'] ) ? strtotime( $_POST['date']['from'] ) : strtotime( '2000-01-01' );
$to = isset( $_POST['date']['to'] ) ? strtotime( $_POST['date']['to'] ) : strtotime( 'tomorrow' );

// disable caching

header( "Expires: Tue, 03 Jul 2001 06:00:00 GMT" );
header( "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );

// force download

header( "Content-Type: application/force-download" );
header( "Content-Type: application/octet-stream" );
header( "Content-Type: application/download" );

// disposition / encoding on response body

header( "Content-Disposition: attachment; filename=stores_" . date( 'dMy', $from ) . "-" . date( 'dMy', $to ) . ".csv" );
header( "Content-Transfer-Encoding: binary" );

$file = fopen( 'php://output', 'w' );

$head = array();
$head[] = 'Name';
$head[] = 'Link';
$head[] = 'Description';
$head[] = 'Tags';
$head[] = 'Image';

fputcsv( $file, array_values( $head ) );

foreach( \query\main::while_stores( array( 'max' => 0, 'categories' => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ), 'date' => $from . ',' . $to ) ) as $store ) {

$line = array();

$line[] = $store->name;
$line[] = $store->url;
$line[] = $store->description;
$line[] = $store->tags;
if( empty( $store->image ) ) {
  $line[] = '';
} else {
  $line[] = filter_var( $store->image, FILTER_VALIDATE_URL ) ? $store->image : $GLOBALS['siteURL'] . $store->image;
}

fputcsv( $file, $line );

}

fclose( $file );