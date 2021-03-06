<div class="left">

<?php

if( exists() ) {

$item = the_item();

echo '<article class="array_item">

<div class="table" itemscope itemtype="http://data-vocabulary.org/Review-aggregate">

<div class="left">
<img src="' . store_avatar( $item->image ) . '" alt="" itemprop="photo" />
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<div itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating">
<span itemprop="average" style="display: none;">' . number_format( $item->stars, 2 ) . '</span>
<span itemprop="best" style="display: none;">5</span>
</div>
<a href="' . $item->reviews_link . '"><span itemprop="votes">' . $item->reviews . '</span> reviews</a>
</div>

<div class="right">
<div class="title" itemprop="itemreviewed">' . $item->name . '</div>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<div class="links">
<a href="' . tlink( 'plugin/click.html', 'id=' . $item->ID ) . '" target="_blank" class="btn open_site">Open website</a>';
if( me() ) {
  if( is_favorite() )
  echo '<a href="' . tlink( 'ajax/favorite.html', 'action=remFavorite&amp;id=' . $item->ID, 'this') . '" class="btn remove_favorite">-Favorite</a>';
  else
  echo '<a href="' . tlink( 'ajax/favorite.html', 'action=addFavorite&amp;id=' . $item->ID, 'this') . '" class="btn add_favorite">+Favorite</a>';
}
echo '</div>
</div>

</div>

<div class="share-coupon" style="display: block;">

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

if( me() ) {
  echo '<a href="#" class="btn write_review"' . ( $_SERVER['REQUEST_METHOD'] == 'POST' ? ' style="display: none;"' : '' ) . '>Write Review</a>';
  echo '<div' . ( $_SERVER['REQUEST_METHOD'] != 'POST' ? ' style="display: none;"' : '' ) . '>' . write_review_form() . '</div>';
}

if( $item->reviews > 0 ) {

echo '<div class="text-tit" style="margin-top: 15px;">Reviews: ' . ( ( ( $rev_rest = ( $item->reviews - 3 ) ) > 0 ) ? '<a href="' . $item->reviews_link . '" class="nice_a">(see other ' . $rev_rest . ' reviews)</a>' : '' ) . '</div>';

foreach( reviews_custom( array( 'store' => $item->ID, 'show' => '', 'orderby' => 'date desc', 'max' => 3 ) ) as $review ) {

echo '<article class="array_item">

<div class="table">

<div class="left">
<img src="' . user_avatar( $review->user_avatar ) . '" alt="" style="width: 60px; height: 60px;" />
<span class="rating"><span style="width:' . ( $review->stars * 16 ) . 'px;"></span></span>
</div>

<div class="right">
<div class="title">' . $review->user_name . '</div>
<div class="time">Added ' . date( 'm/d/Y', ( $wdate = strtotime( $review->date ) ) ) . ', ' . timeago( $wdate, 'seconds' ) . ' ago</div>
<div class="description">' .  $review->text . '</div>
</div>

</div>

</article>';

}

}

if( searched_type() === 'products' ) {

echo '<ul class="category" style="margin: 10px 0 5px 0;">
<li><a href="' . get_remove( array( 'type' ) ) . '">Coupons</a></li>';
if( theme_has_products() ) {
echo '<li class="active">Products</li>';
}
echo '</ul>';

echo '<div class="text-tit">Products for ' . $item->name . ':</div>';

$pagination = have_products_custom( array( 'store' => $item->ID ) );

if( $pagination['results'] ) {

foreach( products_custom( array( 'store' => $item->ID, 'orderby' => 'active desc' ) ) as $item ) {

echo '<article class="array_item product">

<div class="table">

<div class="left">
<img src="' . store_avatar( $item->image ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->store_reviews_link . '">' . $item->reviews . ' reviews</a>';

echo ( !empty( $item->cashback ) ? '<span class="cashback-points" data-ttip="Great! Purchase this product and you\'ll receive ' . $item->cashback . ' points.">' . $item->cashback . '</span> ' : '' );

echo '</div>

<div class="right">
<a href="' . $item->link . '" class="title">' . $item->title . '</a>';
if( !empty( $item->price ) ) {
  echo '<div class="price_info">Price: <span class="price">' . ( empty( $item->old_price ) ? '' : '<span>' . price_format( $item->old_price ) . '</span>' ) . price_format( $item->price ) . ' ' . $item->currency . '</span></div>';
}
echo '<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<ul class="details">
<li>Product Added: <b>' . timeago( strtotime( $item->date ), 'seconds' ) . ' ago</b></li>';
if( $item->is_expired ) echo '<li>Expired on: <span class="expired">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else if( ! $item->is_started ) echo '<li>Starts on: <span class="notstarted">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else echo '<li>Expiration: <span class="active">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';

echo '</ul>

<div class="links">
<a href="' . $item->link . '" class="btn">View Product</a>
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
<li class="share-li" data-ttip="Share this on Linkedin"><a href="#" onclick="openwindow(\'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $item->link . '&amp;title=' . $item->title . '&amp;summary=&amp;source=\', 500, 500, \'yes\');"></a></li>
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

echo '<div style="text-align:center;">No products, yet! :(</div>';

}

} else {

echo '<ul class="category" style="margin: 10px 0 5px 0;">
<li class="active">Coupons</li>';
if( theme_has_products() ) {
echo '<li><a href="' . get_update( array( 'type' => 'products' ) ) . '">Products</a></li>';
}
echo '</ul>';

echo '<div class="text-tit">Coupons for ' . $item->name . ':</div>';

$pagination = have_items_custom( array( 'store' => $item->ID ) );

if( $pagination['results'] ) {

foreach( items_custom( array( 'store' => $item->ID, 'orderby' => 'active desc' ) ) as $item ) {

echo '<article class="array_item">

<div class="table">

<div class="left">
<img src="' . store_avatar( $item->store_img ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->store_reviews_link . '">' . $item->reviews . ' reviews</a>';

echo ( !empty( $item->cashback ) ? '<span class="cashback-points" data-ttip="Great! Use it and you\'ll receive ' . $item->cashback . ' points.">' . $item->cashback . '</span> ' : '' );

echo '</div>

<div class="right">
<a href="' . $item->link . '" class="title">' . $item->title . '</a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<ul class="details">
<li>Coupon Added: <b>' . timeago( strtotime( $item->date ), 'seconds' ) . ' ago</b></li>';
if( $item->is_expired ) echo '<li>Expired on: <span class="expired">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else if( ! $item->is_started ) echo '<li>Starts on: <span class="notstarted">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else echo '<li>Expiration: <span class="active">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';

echo '</ul>

<div class="links">
<a href="' . $item->link . '" class="btn">View Code</a>
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
<li class="share-li" data-ttip="Share this on Linkedin"><a href="#" onclick="openwindow(\'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $item->link . '&amp;title=' . $item->title . '&amp;summary=&amp;source=\', 500, 500, \'yes\');"></a></li>
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

echo '<div style="text-align:center;">No coupons, yet! :(</div>';

}

}

} else {

echo '<div class="info_form">Oops. This store/brand is no longer available !</div>';

echo '<div class="text-tit" style="margin-top: 15px;">See some coupons:</div>';

foreach( items_custom( array( 'show' => ',active', 'orderby' => 'rand', 'max' => option('items_per_page') ) ) as $item ) {

echo '<article class="array_item">

<div class="table">

<div class="left">
<img src="' . store_avatar( $item->store_img ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->store_reviews_link . '">' . $item->reviews . ' reviews</a>';

echo ( !empty( $item->cashback ) ? '<span class="cashback-points" data-ttip="Great! Use it and you\'ll receive ' . $item->cashback . ' points.">' . $item->cashback . '</span> ' : '' );

echo '</div>

<div class="right">
<a href="' . $item->link . '" class="title">' . $item->title . '</a>
More coupons for <a href="' . $item->store_link . '">' . $item->store_name . '</a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<ul class="details">
<li>Coupon Added: <b>' . timeago( strtotime( $item->date ), 'seconds' ) . ' ago</b></li>';
if( $item->is_expired ) echo '<li>Expired on: <span class="expired">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else if( ! $item->is_started ) echo '<li>Starts on: <span class="notstarted">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else echo '<li>Expiration: <span class="active">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';

echo '</ul>

<div class="links">
<a href="' . $item->link . '" class="btn">View Code</a>
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
<li class="share-li" data-ttip="Share this on Linkedin"><a href="#" onclick="openwindow(\'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $item->link . '&amp;title=' . $item->title . '&amp;summary=&amp;source=\', 500, 500, \'yes\');"></a></li>
</ul>

<input type="text" value="' . $item->link . '" onclick="$(this).select();" />

</div>

</article>';

}

}

?>

</div>

<div class="right">

<?php show_widgets( 'right' ); ?>

</div>