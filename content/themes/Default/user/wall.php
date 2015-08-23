<?php

if( me() ) {

echo '<div class="left">';

echo '<ul class="letters">';
foreach( array_merge(range('A', 'Z'), array('0-9')) as $l ) {
  echo '<li' . ( isset( $_GET['firstchar'] ) && $_GET['firstchar'] == $l ? ' class="active"' : '' ) . '><a href="' . tlink( 'user/wall', 'firstchar=' . $l . ( isset( $_GET['type'] ) && $_GET['type'] === 'products' ? '&amp;type=products' : '' ) ) . '">' . $l . '</a></li>';
}
echo '<li><a href="' . tlink( 'user/wall' ) . ( isset( $_GET['type'] ) && $_GET['type'] === 'products' ? '&amp;type=products' : '' ) . '">ALL</a></li>';
echo '</ul>';

if( isset( $_GET['type'] ) && $_GET['type'] === 'products' ) {

$pagination = have_wall_products( array( 'firstchar' => isset( $_GET['firstchar'] ) ? $_GET['firstchar'] : '' ) );

echo '<div class="title">My Wall <span style="float: right;">' . ( $pagination['results'] ?  $pagination['results'] . ' ' . ( is_first( $pagination['results'] ) ? 'product' : 'products' ) : 'Nothing found yet' ) . '</span></div>';

echo '<div style="margin-bottom: 20px;">

<ul class="category">
<li><a href="' . get_remove( array( 'type', 'page' ) ) . '">Coupons</a></li>';
if( theme_has_products() ) {
echo '<li class="active">Products</li>';
}
echo '</ul>

</div>';

if( $pagination['results'] ) {

foreach( wall_products( array( 'firstchar' => isset( $_GET['firstchar'] ) ? $_GET['firstchar'] : '' ) ) as $item ) {

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

if( isset( $pagination['prev_page'] ) || isset( $pagination['next_page'] ) ) {

  echo '<div class="pagination">';
  echo ( isset( $pagination['prev_page'] ) ? '<span><a href="' . $pagination['prev_page'] . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( isset( $pagination['next_page'] ) ? '<span><a href="' . $pagination['next_page'] . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . $pagination['page'] . ' / ' . $pagination['pages'] . '</span>';
  echo '</div>';

}

} else {
  echo '<div style="text-align:center; margin-top: 20px;">On the wall appear only products from your favorite stores/brands. <br />
  <a href="' . tlink( 'stores' ) . '" class="nice_a">Choose from the list</a></div>';
}

} else {

$pagination = have_wall( array( 'firstchar' => isset( $_GET['firstchar'] ) ? $_GET['firstchar'] : '' ) );

echo '<div class="title">My Wall <span style="float: right;">' . ( $pagination['results'] ?  $pagination['results'] . ' ' . ( is_first( $pagination['results'] ) ? 'coupon' : 'coupons' ) : 'Nothing found yet' ) . '</span></div>';

echo '<div style="margin-bottom: 20px;">

<ul class="category">
<li class="active">Coupons</li>';
if( theme_has_products() ) {
echo '<li><a href="' . get_update( array( 'type' => 'products', 'page' => 1 ) ) . '">Products</a></li>';
}
echo '</ul>

</div>';

if( $pagination['results'] ) {

foreach( wall( array( 'firstchar' => isset( $_GET['firstchar'] ) ? $_GET['firstchar'] : '' ) ) as $item ) {

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

if( isset( $pagination['prev_page'] ) || isset( $pagination['next_page'] ) ) {

  echo '<div class="pagination">';
  echo ( isset( $pagination['prev_page'] ) ? '<span><a href="' . $pagination['prev_page'] . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( isset( $pagination['next_page'] ) ? '<span><a href="' . $pagination['next_page'] . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . $pagination['page'] . ' / ' . $pagination['pages'] . '</span>';
  echo '</div>';

}

} else {
  echo '<div style="text-align:center; margin-top: 20px;">On the wall appear only coupons from your favorite stores/brands. <br />
  <a href="' . tlink( 'stores' ) . '" class="nice_a">Choose from the list</a></div>';
}

}

echo '</div>

<div class="right">';

echo show_widgets( 'right' );

echo '</div>';

} else

    echo read_template_part( '404' );

?>