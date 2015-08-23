<div class="left">

<?php

if( exists() ) {

$item = the_item();

echo '<article class="array_item product">

<div class="table" itemscope itemtype="http://data-vocabulary.org/Review-aggregate">

<div class="left">
<img src="' . product_avatar( $item->image ) . '" alt="" itemprop="photo" />
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<div itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating">
<span itemprop="average" style="display: none;">' . number_format( $item->stars, 2 ) . '</span>
<span itemprop="best" style="display: none;">5</span>
</div>
<a href="' . $item->store_reviews_link . '"><span itemprop="votes">' . $item->reviews . '</span> reviews</a>';

echo ( !empty( $item->cashback ) ? '<span class="cashback-points" data-ttip="Great! Purchase this product and you\'ll receive ' . $item->cashback . ' points.">' . $item->cashback . '</span> ' : '' );

echo '</div>

<div class="right">
<div class="title">' . $item->title . '</div>';
if( !empty( $item->price ) ) {
  echo '<div class="price_info">Price: <span class="price">' . ( empty( $item->old_price ) ? '' : '<span>' . price_format( $item->old_price ) . '</span>' ) . price_format( $item->price ) . ' ' . $item->currency . '</span></div>';
}
echo 'More products for <a href="' . $item->store_link . '"><span itemprop="itemreviewed">' . $item->store_name . '</span></a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<ul class="details" style="display: block;">
<li>Product Added: <b>' . timeago( strtotime( $item->date ), 'seconds' ) . ' ago</b></li>';
echo '<li>Expiration: <span class="active">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
echo '</ul>

<div class="links">';

echo '<a href="' . tlink( 'plugin/click.html', 'product=' . $item->ID ) . '" target="_blank" class="btn" style="width: 50%;">Purchase</a>
<a href="' . tlink( 'plugin/click.html', 'id=' . $item->storeID ) . '" target="_blank" class="btn open_site" style="width: 50%;">Open website</a>';
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
<li class="share-li" data-ttip="Share this on Linkedin"><a href="#" onclick="openwindow(\'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $item->link . '&amp;title=' . $item->title . '&amp;summary=&amp;source=\', 500, 500, \'yes\');"></a></li>
</ul>

<input type="text" value="' . $item->link . '" onclick="$(this).select();" />

</div>

</div>

</article>';

} else {

  echo '<div class="info_form">Oops. This product is no longer available !</div>';

}

echo '<div class="text-tit" style="margin-top: 15px;">See other products:</div>';

foreach( products_custom( array( 'show' => ',active', 'orderby' => 'rand', 'max' => option('items_per_page') ) ) as $item ) {

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
echo 'More products for <a href="' . get_update( array( 'type' => 'products' ), $item->store_link ) . '">' . $item->store_name . '</a>
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

?>

</div>

<div class="right">

<?php show_widgets( 'right' ); ?>

</div>