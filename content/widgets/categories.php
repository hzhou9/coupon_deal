<?php

echo '<div class="widget widget_categories' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

echo '<ul class="list">';

$cats = categories_custom( array( 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) );

foreach( $cats as $cat ) {

  $storesh = 0;

  $cline = '<li><a href="' . $cat['infos']->link . '">' . $cat['infos']->name . '</a>';

  $storesh += $cat['infos']->stores;

  if( isset( $cat['subcats'] ) ) {
    $cline .= '<ul>';
    foreach( $cat['subcats'] as $subcat ) {
      if( $subcat->stores > 0 )
      $cline .= '<li><a href="' . $subcat->link . '">' . $subcat->name . '</a></li>';

      $storesh += $subcat->stores;
    }
    $cline .= '</ul>';
  }

  $cline .= '</li>';

  if( $storesh > 0 ) echo $cline;

}

echo '</ul>
</div>';