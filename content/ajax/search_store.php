<?php

$answer = array();

foreach( \query\main::while_stores( array( 'max' => 50, 'orderby' => 'name', 'show' => ( isset( $_GET['show'] ) ? $_GET['show'] : '' ), 'search' => (isset( $_POST['search'] ) ? urldecode( $_POST['search'] ) : '') ) ) as $item ) {

$answer[$item->ID] = array( 'catID' => $item->catID, 'name' => $item->name );

}

echo json_encode( $answer );