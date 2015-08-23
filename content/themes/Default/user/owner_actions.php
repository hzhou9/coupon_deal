<?php if( $GLOBALS['me'] && isset( $_GET['action'] ) ) {

switch( $_GET['action'] ) {

/*

MY COUPONS

*/

case 'my_coupons':

echo '<div class="left">';

if( ( $pagination = have_coupons( array( 'show' => 'all' ) ) ) && $pagination['results'] > 0 ) {

foreach( coupons( array( 'orderby' => 'date desc', 'show' => 'all' ) ) as $item ) {

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
<a href="' . tlink( 'user/owner_actions', 'action=edit_coupon&amp;id=' . (int)$item->ID ) . '" class="btn">Edit coupon</a>
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

if( isset( $pagination['prev_page'] ) || isset( $pagination['next_page'] ) ) {

  echo '<div class="pagination">';
  echo ( isset( $pagination['prev_page'] ) ? '<span><a href="' . $pagination['prev_page'] . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( isset( $pagination['next_page'] ) ? '<span><a href="' . $pagination['next_page'] . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . $pagination['page'] . ' / ' . $pagination['pages'] . '</span>';
  echo '</div>';

}

} else {
  echo '<div class="info_form">You don\'t have any coupon yet.</div>';
}

echo '</div>

<div class="right" style="text-align: center;">
  <a href="' . tlink( 'user/owner_actions', 'action=add_coupon' ) . '" class="btn">Add new coupon</a>
</div>';


break;

/*

ADD COUPON

*/

case 'add_coupon':

$prices = prices( 'object' );
$my_credits = $GLOBALS['me']->Credits;

echo '<div class="left">
<div class="title">Add New Coupon</div>';
if( $my_credits < $prices->coupon ) {
  echo '<div class="mask-form"></div>';
}
  echo submit_coupon_form();
echo '</div>

<div class="right">';
if( $my_credits < $prices->coupon ) {
  echo '<div class="error">You don\'t have enough credits to add new coupons.</div>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
  } else {
  echo '<section class="cost-sect">
  <h2>Cost (credits): <span>' . $prices->coupon . ' / ' . $prices->coupon_max_days . ' days</span></h2>
  <h3>For example: If you want to add a coupon valid until ' . date( 'm.d.Y', strtotime( "+ {$prices->coupon_max_days} days" ) ) . ' you have to pay ' . $prices->coupon . ' credits. If the expiration date is greater you have to pay ' . $prices->coupon . ' credits for every ' . $prices->coupon_max_days . ' day(s). <br /> Your balance is: <span>' . $my_credits . '</span> credits.</h3>
  </section>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
}
echo '</div>';

break;

/*

EDIT COUPON

*/

case 'edit_coupon':

$prices = prices( 'object' );
$my_credits = $GLOBALS['me']->Credits;

echo '<div class="left">
<div class="title">Edit Coupon</div>';
if( $my_credits < $prices->coupon ) {
  echo '<div class="mask-form"></div>';
}
  echo edit_coupon_form( ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
echo '</div>

<div class="right">';
if( $my_credits < $prices->coupon ) {
  echo '<div class="error">You don\'t have enough credits to add new coupons.</div>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
  } else {
  echo '<section class="cost-sect">
  <h2>Cost (credits): <span>' . $prices->coupon . ' / ' . $prices->coupon_max_days . ' days</span></h2>
  <h3>For example: If you want to add a coupon valid until ' . date( 'm.d.Y', strtotime( "+ {$prices->coupon_max_days} days" ) ) . ' you have to pay ' . $prices->coupon . ' credits. If the expiration date is greater you have to pay ' . $prices->coupon . ' credits for every ' . $prices->coupon_max_days . ' day(s) <br /> Your balance is: <span>' . $my_credits . '</span> credits.</h3>
  </section>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
}
echo '</div>';

break;

/*

MY PRODUCTS

*/

case 'my_products':

echo '<div class="left">';

if( ( $pagination = have_products( array( 'show' => 'all' ) ) ) && $pagination['results'] > 0 ) {

foreach( products( array( 'orderby' => 'date desc', 'show' => 'all' ) ) as $item ) {

echo '<article class="array_item product">

<div class="table">

<div class="left">
<img src="' . product_avatar( $item->image ) . '" alt="">
<span class="rating"><span style="width:' . ( $item->stars * 16 ) . 'px;"></span></span>
<a href="' . $item->store_reviews_link . '">' . $item->reviews . ' reviews</a>
</div>

<div class="right">
<a href="' . $item->link . '" class="title">' . $item->title . '</a>';
if( !empty( $item->price ) ) {
  echo '<div class="price_info">Price: <span class="price">' . ( empty( $item->old_price ) ? '' : '<span>' . price_format( $item->old_price ) . '</span>' ) . price_format( $item->price ) . ' ' . $item->currency . '</span></div>';
}
echo 'More coupons for <a href="' . $item->store_link . '">' . $item->store_name . '</a>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : 'no description' ) . '</div>

<ul class="details">
<li>Coupon Added: <b>' . timeago( strtotime( $item->date ), 'seconds' ) . ' ago</b></li>';
if( $item->is_expired ) echo '<li>Expired on: <span class="expired">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else if( ! $item->is_started ) echo '<li>Starts on: <span class="notstarted">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
else echo '<li>Expiration: <span class="active">' . date( 'm/d/y', strtotime( $item->expiration_date ) ) . '</span></li>';
echo '</ul>

<div class="links">
<a href="' . tlink( 'user/owner_actions', 'action=edit_product&amp;id=' . (int)$item->ID ) . '" class="btn">Edit product</a>
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

if( isset( $pagination['prev_page'] ) || isset( $pagination['next_page'] ) ) {

  echo '<div class="pagination">';
  echo ( isset( $pagination['prev_page'] ) ? '<span><a href="' . $pagination['prev_page'] . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( isset( $pagination['next_page'] ) ? '<span><a href="' . $pagination['next_page'] . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . $pagination['page'] . ' / ' . $pagination['pages'] . '</span>';
  echo '</div>';

}

} else {
  echo '<div class="info_form">You don\'t have any product yet.</div>';
}

echo '</div>

<div class="right" style="text-align: center;">
  <a href="' . tlink( 'user/owner_actions', 'action=add_product' ) . '" class="btn">Add new product</a>
</div>';

break;

/*

ADD PRODUCT

*/

case 'add_product':

$prices = prices( 'object' );
$my_credits = $GLOBALS['me']->Credits;

echo '<div class="left">
<div class="title">Add New Product</div>';
if( $my_credits < $prices->product ) {
  echo '<div class="mask-form"></div>';
}
  echo submit_product_form();
echo '</div>

<div class="right">';
if( $my_credits < $prices->product ) {
  echo '<div class="error">You don\'t have enough credits to add new coupons.</div>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
  } else {
  echo '<section class="cost-sect">
  <h2>Cost (credits): <span>' . $prices->product . ' / ' . $prices->product_max_days . ' days</span></h2>
  <h3>For example: If you want to add a product valid until ' . date( 'm.d.Y', strtotime( "+ {$prices->product_max_days} days" ) ) . ' you have to pay ' . $prices->product . ' credits. If the expiration date is greater you have to pay ' . $prices->product . ' credits for every ' . $prices->product_max_days . ' day(s). <br /> Your balance is: <span>' . $my_credits . '</span> credits.</h3>
  </section>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
}
echo '</div>';

break;

/*

EDIT PRODUCT

*/

case 'edit_product':

$prices = prices( 'object' );
$my_credits = $GLOBALS['me']->Credits;

echo '<div class="left">
<div class="title">Edit Product</div>';
if( $my_credits < $prices->product ) {
  echo '<div class="mask-form"></div>';
}
  echo edit_product_form( ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
echo '</div>

<div class="right">';
if( $my_credits < $prices->product ) {
  echo '<div class="error">You don\'t have enough credits to add new coupons.</div>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
  } else {
  echo '<section class="cost-sect">
  <h2>Cost (credits): <span>' . $prices->product . ' / ' . $prices->product_max_days . ' days</span></h2>
  <h3>For example: If you want to add a product valid until ' . date( 'm.d.Y', strtotime( "+ {$prices->product_max_days} days" ) ) . ' you have to pay ' . $prices->product . ' credits. If the expiration date is greater you have to pay ' . $prices->product . ' credits for every ' . $prices->product_max_days . ' day(s) <br /> Your balance is: <span>' . $my_credits . '</span> credits.</h3>
  </section>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
}
echo '</div>';

break;

break;

/*

MY STORES

*/

case 'my_stores':

echo '<div class="left">';

if( ( $pagination = have_stores( array( 'show' => 'all' ) ) ) && $pagination['results'] > 0 ) {

foreach( stores( array( 'orderby' => 'date desc', 'show' => 'all' ) ) as $item ) {

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
echo '</ul>

<div class="links">
<a href="' . tlink( 'user/owner_actions', 'action=edit_store&amp;id=' . (int)$item->ID ) . '" class="btn">Edit store</a>
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
<li class="share-li"><a href="#" onclick="openwindow(\'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $item->link . '&amp;title=' . $item->name . '&amp;summary=&amp;source=\', 500, 500, \'yes\');"></a></li>
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

}

echo '</div>

<div class="right" style="text-align:center;">
  <a href="' . tlink( 'user/owner_actions', 'action=add_store' ) . '" class="btn">Add new store</a>
</div>';

break;

/*

ADD STORE

*/

case 'add_store':

$prices = prices( 'object' );
$my_credits = $GLOBALS['me']->Credits;

echo '<div class="left">
<div class="title">Add New Store</div>';
if( $my_credits < $prices->store ) {
  echo '<div class="mask-form"></div>';
}
  echo submit_store_form();

echo '</div>

<div class="right">';
if( $my_credits < $prices->store ) {
  echo '<div class="error">You don\'t have enough credits to add new stores.</div>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
  } else {
  echo '<section class="cost-sect">
  <h2>Cost (credits): <span>' . $prices->store . '</span></h2>
  <h3>One-time fee. <br /> Your balance is: <span>' . $my_credits . '</span> credits.</h3>
  </section>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>';
}
echo '</div>';

break;

/*

EDIT STORE

*/

case 'edit_store':

$my_credits = $GLOBALS['me']->Credits;

echo '<div class="left">
<div class="title">Edit Store/Brand</div>';
echo edit_store_form( ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
echo '</div>

<div class="right">';
  echo '<section class="cost-sect">
  <h2>Cost (credits): <span>0</span></h2>
  <h3>It\'s always free to edit your stores. <br /> Your balance is: <span>' . $my_credits . '</span> credits.</h3>
  </section>
  <div style="margin-top: 20px; text-align: center;"><a href="' . tlink( 'user/plans' ) . '" class="btn">Add Credits</a></div>
</div>';

break;

/*

ERROR 404

*/

default: echo read_template_part( '404' ); break;

}

} else

    echo read_template_part( '404' );

?>