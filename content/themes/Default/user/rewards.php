<?php if( ( $me = me() ) && theme_has_rewards() ) { ?>

<div class="gtitle">Rewards</div>

<div style="text-align: right; margin-bottom: 10px;">
  <a href="<?php echo tlink( 'user/claim-history' ); ?>" class="btn">Claims History</a>
</div>



<?php

if( ( $pagination = have_rewards( array( 'show' => 'active' ) ) ) && $pagination['results'] > 0 ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && \site\utils::check_csrf( $_POST['csrf'], 'claim_reward' ) ) {
  echo create_reward_request(); // without this function rewards can't be claimed
}

$csrf = $_SESSION['claim_reward'] = \site\utils::str_random(12);

echo '<div>';

foreach( rewards( array( 'show' => 'active', 'orderby' => 'points' ) ) as $item ) {

echo '<section class="array_item twopl">

<div class="table">

<div class="left">
<img src="' . reward_avatar( $item->image ) . '" alt="" style="height: 60px; width: 60px;">
</div>

<div class="right">
<div class="title">' . $item->title . '</div>
<div class="info">Requires: <b style="color: #0086CE; font-weight: 900;">' . $item->points . '</b> Points</div>
<div class="description">' . ( !empty( $item->description ) ? nl2br( $item->description ) : '-' ) . '</div>
</div>

</div>

<div class="bottom" style="text-align: right;">';

if( $me->Points >= $item->points ) {

echo '<form method="POST" action="#" class="redeem-form">';

  if( !empty( $item->fields ) ) {

  echo '<div class="fields">';

    foreach( $item->fields as $v ) {
      echo '<div' . ( $v['type'] == 'hidden' ? ' style="display: none;"' : '' ) . '><label for="' . $v['name'] . '">' . $v['name'] . ( $v['require'] ? '*' : '' ) . ':</label> <input id="' . $v['name'] . '" name="Reward[' . $item->ID . '][' . $v['name'] . ']" type="' . $v['type'] . '" value="' . $v['value'] . '"' . ( $v['require'] ? ' required' : '' ) . ' /></div>';
    }

  echo '</div>';

  }

  echo '<a href="#" class="cancel">Cancel</a>
  <input type="hidden" name="Reward-ID" value="' . $item->ID . '" />
  <input type="hidden" name="csrf" value="' . $csrf . '" />
  <button class="btn">Redeem Now !</button>

  </form>

  <a href="#" class="redeem-btn btn">Redeem</a>';

} else {

  echo 'You need ' . ( ( $item->points - $me->Points ) ) . ' more points to redeem this.';

}

echo '</div>

</section>';

}

echo '</div>';

} else {

  echo '<div class="message">No rewards yet, please check a little later.</div>';

}

}

else

    echo read_template_part( '404' );

?>