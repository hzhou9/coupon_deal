<div class="left">

<?php

$category = the_item();
have_items();

if( searched_type() === 'products' ) {

if( exists() ) {

echo '<div class="title">
  <span style="color: #efefef;">' . $category->name . '</span> Products <div class="shareico" style="position: relative; float: right;" data-ttip="RSS Feed"><a href="' . tlink( 'plugin/rss2-products.xml', 'cat=' . $category->ID ) . '"></a></div>
</div>

<div style="margin-bottom: 20px;">

<ul class="category">
<li><a href="' . get_remove( array( 'type', 'page' ) ) . '">Coupons</a></li>';
if( theme_has_products() ) {
echo '<li class="active">Products</li>';
}
echo '</ul>';

if( !empty( $category->description ) ) {
  echo '<article class="array_item category_box" style="border-top: 0;">' . $category->description . '</article>';
}

echo '</div>';

if( results() > 0 ) {

foreach( items( array( 'orderby' => 'active desc' ) ) as $item ) {

echo '<article class="array_item product">

<div class="table">

<div class="left">
<img src="' . product_avatar( $item->image ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->store_reviews_link . '">' . $item->reviews . ' reviews</a>';

echo ( !empty( $item->cashback ) ? '<span class="cashback-points" data-ttip="Great! Purchase this product and you\'ll receive ' . $item->cashback . ' points.">' . $item->cashback . '</span> ' : '' );

echo '</div>

<div class="right">
<a href="' . $item->link . '" class="title">' . $item->title . '</a>';
if( !empty( $item->price ) ) {
  echo '<div class="price_info">Price: <span class="price">' . ( empty( $item->old_price ) ? '' : '<span>' . price_format( $item->old_price ) . '</span>' ) . price_format( $item->price ) . ' ' . $item->currency . '</span></div>';
}
echo 'More products for <a href="' . get_update( array('type' => 'products'), $item->store_link ) . '">' . $item->store_name . '</a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

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

if( ( $pp = prev_page() ) || next_page() ) {

  echo '<div class="pagination">';
  echo ( $pp ? '<span><a href="' . $pp . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( ( $np = next_page() ) ? '<span><a href="' . $np . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . page() . ' / ' . pages() . '</span>';
  echo '</div>';

}

} else {

echo '<div class="text-tit">See some products:</div>';

foreach( products_custom( array( 'show' => ',active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {

echo '<article class="array_item product">

<div class="table">

<div class="left">
<img src="' . product_avatar( $item->image ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->store_reviews_link . '">' . $item->reviews . ' reviews</a>';

echo ( !empty( $item->cashback ) ? '<span class="cashback-points" data-ttip="Great! Purchase this product and you\'ll receive ' . $item->cashback . ' points.">' . $item->cashback . '</span> ' : '' );

echo '</div>

<div class="right">
<a href="' . $item->link . '" class="title">' . $item->title . '</a>';
if( !empty( $item->price ) ) {
  echo '<div class="price_info">Price: <span class="price">' . ( empty( $item->old_price ) ? '' : '<span>' . price_format( $item->old_price ) . '</span>' ) . price_format( $item->price ) . ' ' . $item->currency . '</span></div>';
}
echo 'More products for <a href="' . get_update( array('type' => 'products'), $item->store_link ) . '">' . $item->store_name . '</a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

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

}

} else {

echo '<div class="info_form">This category is no longer available !</div>';

echo '<div class="text-tit" style="margin-top: 15px;">See some products:</div>';

foreach( products_custom( array( 'show' => ',active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {

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
echo 'More products for <a href="' . get_update( array('type' => 'products'), $item->store_link ) . '">' . $item->store_name . '</a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

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

}

} else {

if( exists() ) {

echo '<div class="title">
  <span style="color: #efefef;">' . $category->name . '</span> Coupons <div class="shareico" style="position: relative; float: right;" data-ttip="RSS Feed"><a href="' . tlink( 'plugin/rss2.xml', 'cat=' . $category->ID ) . '"></a></div>
</div>

<div style="margin-bottom: 20px;">

<ul class="category">
<li class="active">Coupons</li>';
if( theme_has_products() ) {
echo '<li><a href="' . get_update( array( 'page' => 1, 'type' => 'products' ) ) . '">Products</a></li>';
}
echo '</ul>';

if( !empty( $category->description ) ) {
  echo '<article class="array_item category_box" style="border-top: 0;">' . $category->description . '</article>';
}

echo '</div>';

if( results() ) {

foreach( items( array( 'orderby' => 'active desc' ) ) as $item ) {

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

</div>

</article>';

}

if( ( $pp = prev_page() ) || next_page() ) {

  echo '<div class="pagination">';
  echo ( $pp ? '<span><a href="' . $pp . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( ( $np = next_page() ) ? '<span><a href="' . $np . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . page() . ' / ' . pages() . '</span>';
  echo '</div>';

}

} else {

echo '<div class="text-tit">See some coupons:</div>';

foreach( items_custom( array( 'show' => ',active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {

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

</div>

</article>';

}

}

} else {

echo '<div class="info_form">This category is no longer available !</div>';

echo '<div class="text-tit" style="margin-top: 15px;">See some coupons:</div>';

foreach( items_custom( array( 'show' => ',active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {

echo '<article class="array_item">

<div class="table">

<div class="left">
<img src="' . store_avatar( $item->store_img ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->store_reviews_link . '">' . $item->reviews . ' reviews</a>
</div>

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
<li class="share-fb"><a href="#" onclick="openwindow(\'https://www.facebook.com/sharer/sharer.php?u=' . $item->link . '\', 500, 500, \'yes\');"></a></li>
<li class="share-tw"><a href="#" onclick="openwindow(\'https://twitter.com/home?status=' . $item->link . '\', 500, 500, \'yes\');"></a></li>
<li class="share-go"><a href="#" onclick="openwindow(\'https://plus.google.com/share?url=' . $item->link . '\', 500, 500, \'yes\');"></a></li>
<li class="share-li"><a href="#" onclick="openwindow(\'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $item->link . '&amp;title=' . $item->title . '&amp;summary=&amp;source=\', 500, 500, \'yes\');"></a></li>
</ul>

<input type="text" value="' . $item->link . '" onclick="$(this).select();" />

</div>

</div>

</article>';

}

}

}

?>

</div>

<div class="right">

<?php show_widgets( 'right' ); ?>

</div>