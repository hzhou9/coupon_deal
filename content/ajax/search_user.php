<?php

$answer = array();

foreach( \query\main::while_users( array( 'max' => 50, 'orderby' => 'name', 'search' => (isset( $_POST['search'] ) ? urldecode( $_POST['search'] ) : '') ) ) as $item ) {

$answer[$item->ID] = array( 'name' => $item->name );

}

echo json_encode( $answer );