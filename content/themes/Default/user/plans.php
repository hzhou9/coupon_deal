<?php if( $me = me() ) { ?>

<div class="gtitle">Choose a plan</div>

<?php

if( ( $pagination = have_payment_plans() ) && $pagination['results'] > 0 ) {

echo '<div>';

foreach( payment_plans( array( 'show' => 'active', 'orderby' => 'price' ) ) as $item ) {

echo '<section class="array_item twopl">

<div class="table">

<div class="left">
<img src="' . payment_plan_avatar( $item->image ) . '" alt="" style="height: 60px; width: 60px;">
</div>

<div class="right">
<div class="title">' . $item->name . '</div>
<div class="info"><b style="color: #0086CE; font-weight: 900;">' . $item->credits . '</b> Credits / <b>' . $item->price_format . '</b></div>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : '-' ) . '</div>
</div>

</div>

<div class="bottom" style="text-align: right; display: table; width: 100%; box-sizing: border-box;">';;
  echo '<a href="' . tlink( 'pay', 'plan=' . $item->ID ) . '" class="btn" style="margin: 0 0 0 5px; display: inline-block; vertical-align: middle;">Purchase</a>';
echo '</div>

</section>';

}

echo '</div>';

} else {

  echo '<div class="message">No payment plans for the moment, please check a little later.</div>';

}

}

else

    echo read_template_part( '404' );

?>