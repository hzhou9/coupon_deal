<?php

echo '<div class="widget widget_coupons_v2' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

echo '<ul class="list">';

foreach( items_custom( array( 'show' => ( !empty( $type ) ? $type : '' ), 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) ) as $id ) {
  echo '<li><a href="' . $id->link . '"><img src="' . store_avatar( $id->store_img ) . '" alt="" />  <span>' . $id->title . '</span></a></li>';
}
echo '</ul>
</div>';