<?php

$json = array();

foreach( admin_query::while_chat_messages( array( 'max' => 5, 'orderby' => 'date DESC' ) ) as $item ) {
  $json[] = array( 'id' => $item->ID, 'avatar' => \query\main::user_avatar( $item->user_avatar )  , 'name' => $item->user_name, 'text' => \site\utils::bbcodes( $item->text ), 'date' => $item->date, 'gfdate' => date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) );
}

echo json_encode( $json );