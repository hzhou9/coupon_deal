<?php

echo '<div class="widget widget_stores' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

echo '<ul class="list">';
foreach( stores_custom( array( 'show' => ( !empty( $type ) ? $type : '' ), 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) ) as $id )echo '<li><a href="' . $id->link . '">' . $id->name . '</a></li>';
echo '</ul>
</div>';