<?php

if( !ab_to( array( 'subscribers' => 'export' ) ) || $_SERVER["REQUEST_METHOD"] != 'POST' || !isset( $_POST['csrf'] ) || !isset( $_SESSION['subscribers_csrf'] ) || $_POST['csrf'] != $_SESSION['subscribers_csrf'] ) {

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

header( "Content-Disposition: attachment; filename=subscribers_" . date( 'dMy', $from ) . "-" . date( 'dMy', $to ) . ".csv" );
header( "Content-Transfer-Encoding: binary" );

$file = fopen( 'php://output', 'w' );

$head = array();
if( $name = isset( $_POST['fields']['name'] ) ) {
  $head[] = 'Name';
}
$head[] = 'Email';

fputcsv( $file, array_values( $head ) );

foreach( admin_query::while_subscribers( array( 'max' => 0, 'show' => (isset( $_POST['view'] ) ? urldecode( $_POST['view'] ) : ''), 'date' => "" . $from . ',' . $to . "" ) ) as $subscriber ) {

$line = array();
if( $name ) {
  $line[] = $subscriber->user_name;
}
$line[] = $subscriber->email;

fputcsv( $file, $line );

}

fclose( $file );