<div class="left">

<?php

if( exists() ) {

$item = the_item();

echo '<article class="array_item">

<div class="table" itemscope itemtype="http://data-vocabulary.org/Review-aggregate">

<div class="left">
<img src="' . store_avatar( $item->store_img ) . '" alt="" itemprop="photo" />
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<div itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating">
<span itemprop="average" style="display: none;">' . number_format( $item->stars, 2 ) . '</span>
<span itemprop="best" style="display: none;">5</span>
</div>
<a href="' . $item->store_reviews_link . '"><span itemprop="votes">' . $item->reviews . '</span> reviews</a>';

echo ( !empty( $item->cashback ) ? '<span class="cashback-points" data-ttip="Great! Use it and you\'ll receive ' . $item->cashback . ' points.">' . $item->cashback . '</span> ' : '' );

echo '</div>

<div class="right">
<div class="title">' . $item->title . '</div>
More coupons for <a href="' . $item->store_link . '"><span itemprop="itemreviewed">' . $item->store_name . '</span></a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<ul class="details" style="display: block;">
<li>Coupon Added: <b>' . timeago( strtotime( $item->date ), 'seconds' ) . ' ago</b></li>';
if( $item->is_expired ) echo '<li>Expired on: <span class="expired">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else if( ! $item->is_started ) echo '<li>Starts on: <span class="notstarted">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else echo '<li>Expiration: <span class="active">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
echo '</ul>';

if( $item->is_coupon ) {

echo '<div class="code">
<span><span class="codeviewanim" data-vw-goto="' . tlink( 'plugin/click.html', 'coupon=' . $item->ID ) . '"></span>' . $item->code . '</span>
<div class="infos">

<a href="#">How to use this coupon?</a>
<ul><li><span>K</span> Copy the code</li>
<li><span>J</span> Open website (' . $item->store_name . ')</li>
<li><span>L</span> Apply code on the cart (when checkout)</li></ul>';

} else {

echo '<div class="code">
<span class="nocode">Activated ! No coupon needed.</span>
<div class="infos">

<a href="#">How to redeem this deal?</a>
<ul><li><span>J</span> Open website (' . $item->store_name . ')</li>
<li><span>L</span> No other action required, prices include discounts automatically</li></ul>';

}

echo '</div>
</div>

<div class="links">';

if( $item->is_running ) {
  if( $item->is_coupon ) echo '<a href="' . tlink( 'plugin/click.html', 'coupon=' . $item->ID ) . '" target="_blank" class="btn" style="width: 50%;" id="copy-button" data-clipboard-text="' . $item->code . '">Copy and Use Now</a>';
  else echo '<a href="' . tlink( 'plugin/click.html', 'coupon=' . $item->ID ) . '" target="_blank" class="btn">Redeem this deal</a>';
}

echo '<a href="' . tlink( 'plugin/click.html', 'id=' . $item->storeID ) . '" target="_blank" class="btn open_site" style="width: 50%;">Open website</a>';
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

  echo '<div class="info_form">Oops. This coupon is no longer available !</div>';

}

echo '<div class="text-tit" style="margin-top: 15px;">See other coupons:</div>';

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

</div>

</article>';

}

?>

</div>

<div class="right">

<?php show_widgets( 'right' ); ?>

</div>