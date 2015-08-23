<?php

echo '<div class="widget widget_reviews' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

echo '<ul class="list">';

foreach( reviews_custom( array( 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ), 'show' => '' ) ) as $id ) {

  echo '<li><img src="' . user_avatar( $id->user_avatar ) . '" alt="" />
  <div>By <b>' . htmlsc( $id->user_name ) . '</b> for <a href="' . $id->store_link . '">' . $id->store_name . '</a>
  <p>' . $id->text . '</p>
  </div>
  </li>';

}

echo '</ul>
</div>';