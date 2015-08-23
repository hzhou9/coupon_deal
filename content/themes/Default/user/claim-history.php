<?php if( ( $me = me() ) && theme_has_rewards() ) { ?>

<div class="gtitle">Claims History</div>

<div style="text-align: right; margin-bottom: 10px;">
  <a href="<?php echo tlink( 'user/rewards' ); ?>" class="btn">Rewards</a>
</div>

<?php

if( ( $pagination = have_claim_reqs( array( 'per_page' => 10, 'user' => $me->ID ) ) ) && $pagination['results'] > 0 ) {

echo '<ul class="rewalist">';

echo '<li>
<span>Reward</span>
<span>Points</span>
<span>Status</span>
<span>Date</span>
</li>';

foreach( claim_reqs( array( 'per_page' => 10, 'user' => $me->ID, 'orderby' => 'date DESC' ) ) as $item ) {

echo '<li>
<span>' . $item->name . '</span>
<span>' . $item->points . '</span>
<span>' . ( $item->claimed ? 'completed' : 'pending' ) . '</span>
<span>' . date( 'd F Y', strtotime( $item->date ) ) . '</span>
</li>';

}

echo '</ul>';

if( isset( $pagination['prev_page'] ) || isset( $pagination['next_page'] ) ) {

  echo '<div class="pagination">';
  echo ( isset( $pagination['prev_page'] ) ? '<span><a href="' . $pagination['prev_page'] . '" class="btn">&#8592; Prev</a></span>' : '<span class="btn" style="opacity: 0.2;">&#8592; Prev</span>' );
  echo ( isset( $pagination['next_page'] ) ? '<span><a href="' . $pagination['next_page'] . '" class="btn">Next &#8594;</a></span>' : '<span class="btn" style="opacity: 0.2;">Next &#8594;</span>' );
  echo '<span>Page ' . $pagination['page'] . ' / ' . $pagination['pages'] . '</span>';
  echo '</div>';

}

} else

  echo '<div class="message">You never claimed rewards.</div>';

} else

    echo read_template_part( '404' );


?>