<?php

echo '<div class="widget widget_suggest' . ( !$mobile_view ? ' mobile_view' : '' ) . '" id="widget_suggest">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

if( !empty( $content ) )echo '<div class="text">' . $content . '</div>';
echo suggest_store_form( array('intent' => 2), '_widget' );

echo '</div>';