<?php

echo '<div class="widget widget_contact' . ( !$mobile_view ? ' mobile_view' : '' ) . '" id="widget_contact">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

if( !empty( $content ) )echo '<div class="text">' . $content . '</div>';
echo contact_form( '_widget' );

echo '</div>';