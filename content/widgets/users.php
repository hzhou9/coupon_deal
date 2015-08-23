<?php

echo '<div class="widget widget_users' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

echo '<ul class="list">';
foreach( users_custom( array( 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) ) as $id ) {
echo '<li>' . $id->name;
echo '</li>';
}

echo '</ul>
</div>';