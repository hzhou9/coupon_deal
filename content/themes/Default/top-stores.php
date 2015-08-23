<div class="left">

<?php

$pagination = have_stores_custom( array( 'firstchar' => ( isset( $_GET['firstchar'] ) ? $_GET['firstchar'] : '' ), 'orderby' => 'rating DESC' ) );

echo '<ul class="letters">';
foreach( array_merge(range('A', 'Z'), array('0-9')) as $l ) {
  echo '<li' . ( isset( $_GET['firstchar'] ) && $_GET['firstchar'] == $l ? ' class="active"' : '' ) . '><a href="' . tlink( 'tpage/top-stores', 'firstchar=' . $l ) . '">' . $l . '</a></li>';
}
echo '<li><a href="' . tlink( 'tpage/top-stores' ) . '">ALL</a></li>';
echo '</ul>';

echo '<div class="title">
  Top Stores/Brands
</div>';

if( $pagination['results'] ) {

foreach( stores_custom( array( 'firstchar' => ( isset( $_GET['firstchar'] ) ? $_GET['firstchar'] : '' ), 'orderby' => 'rating DESC' ) ) as $item ) {

echo '<article class="array_item">

<div class="table">

<div class="left">
<img src="' . store_avatar( $item->image ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->reviews_link . '">' . $item->reviews . ' reviews</a>
</div>

<div class="right">
<a href="' . $item->link . '" class="title">' . $item->name . '</a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<ul class="details">
<li>Coupons/Deals: <b>' . $item->coupons . '</b></li>';
if( theme_has_products() ) {
  echo '<li>Products: <b>' . $item->products . '</b></li>';
}
echo '</ul>

<div class="links">
<a href="' . $item->link . '" class="btn">Profile</a>
<a href="#" class="more_details">More details</a>
<a href="#" class="share">Share</a>
</div>

</div>

</div>

<div class="share-coupon">

Share on social networks or copy the link and share anywhere:

<div class="shareto">

<ul>
<li class="share-fb" data-ttip="Share this on Facebook"><a href="#" onclick="openwindow(\'https://www.facebook.com/sharer/sharer.php?u=' . $item->link . '\', 500, 500, \'yes\');"></a></li>
<li class="share-tw" data-ttip="Share this on Twitter"><a href="#" onclick="openwindow(\'https://twitter.com/home?status=' . $item->link . '\', 500, 500, \'yes\');"></a></li>
<li class="share-go" data-ttip="Share this on Google+"><a href="#" onclick="openwindow(\'https://plus.google.com/share?url=' . $item->link . '\', 500, 500, \'yes\');"></a></li>
<li class="share-li" data-ttip="Share this on Linkedin"><a href="#" onclick="openwindow(\'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $item->link . '&amp;title=' . $item->name . '&amp;summary=&amp;source=\', 500, 500, \'yes\');"></a></li>
<li class="share-rss" data-ttip="RSS Feed"><a href="' . tlink( 'plugin/rss2.xml', 'store=' . $item->ID ) . '"></a></li>
</ul>

<input type="text" value="' . $item->link . '" onclick="$(this).select();" />

</div>

</div>

</article>';

}

if( isset( $pagination['prev_page'] ) || isset( $pagination['next_page'] ) ) {

  echo '<div class="pagination">';
  echo ( isset( $pagination['prev_page'] ) ? '<span><a href="' . $pagination['prev_page'] . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( isset( $pagination['next_page'] ) ? '<span><a href="' . $pagination['next_page'] . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . $pagination['page'] . ' / ' . $pagination['pages'] . '</span>';  
  echo '</div>';

}

} else {

    echo '<div style="text-align:center;">Sorry, no stores/brands yet :(</div>';

}

?>

</div>

<div class="right">

<?php show_widgets( 'right' ); ?>

</div>