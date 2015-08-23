<?php

echo '<div class="widget widget_newsletter' . ( !$mobile_view ? ' mobile_view' : '' ) . '" id="widget_newsletter">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

if( !empty( $content ) )echo '<div class="text">' . $content . '</div>';
echo newsletter_form( '_widget' );

echo '</div>';