<?php if( $me = me() ) { ?>

<div class="left">

<div class="title">Edit Your Profile</div>

<?php echo edit_profile_form(); ?>

<div class="title" style="margin-top: 15px;">Change Your Password</div>

<?php echo change_password_form(); ?>

</div>

<div class="right pointsinfo">

<?php if( theme_has_rewards() ) { ?>

<div class="points">
Your balance is: <span><?php echo $me->Points; ?></span> points
</div>

<div class="link">
<a href="<?php echo tlink( 'user/rewards' ); ?>" class="btn">Claim Now</a>
<a href="<?php echo tlink( 'user/claim-history' ); ?>" class="btn">History</a>
</div>

<div class="faq">
<a href="#">How do I gain points?</a>

<?php

$rewards = array();

if( ( $ppr = option( 'u_points_davisit' ) ) > 0 ) {
  $rewards[] = $ppr . ' points for your daily visit';
}

if( ( $ppdv = option( 'u_points_review' ) ) > 0 ) {
  $rewards[] = $ppdv . ' points for every review you write';
}

if( ( $pref = option( 'u_points_refer' ) ) > 0 ) {
  $rewards[] = $pref . ' points for every friend referred';
}

?>

<div class="answer">We always try to reward our members for their activity, so you get <?php echo implode( $rewards, ', ' ); ?>.</div>
<a href="#">How do I redeem points?</a>
<div class="answer">Click on <a href="<?php echo tlink( 'user/rewards' ); ?>">'Claim Now'</a> and choose a reward from the list.</div>
</div>

<?php if( $pref > 0 ) { ?>

<section style="margin-top: 15px;">

<h2>Refer a friend</h2>

<div style="margin: 3px 0 13px 0; line-height: normal; text-align: justify;">Share on social networks or copy the link and share anywhere and you'll get <?php echo $pref; ?> points for each friend referred through your personal invite link !</div>

<div class="shareto">

<ul>
<li class="share-fb" data-ttip="Share this on Facebook"><a href="#" onclick="openwindow('https://www.facebook.com/sharer/sharer.php?u=<?php echo ( $reflink = $GLOBALS['siteURL'] . '?ref=' . $me->ID ); ?>', 500, 500, 'yes');"></a></li>
<li class="share-tw" data-ttip="Share this on Twitter"><a href="#" onclick="openwindow('https://twitter.com/home?status=<?php echo $reflink; ?>', 500, 500, 'yes');"></a></li>
<li class="share-go" data-ttip="Share this on Google+"><a href="#" onclick="openwindow('https://plus.google.com/share?url=<?php echo $reflink; ?>', 500, 500, 'yes');"></a></li>
<li class="share-li" data-ttip="Share this on Linkedin"><a href="#" onclick="openwindow('https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $reflink; ?>&amp;title=Take 10% OFF&amp;summary=&amp;source=', 500, 500, 'yes');"></a></li>
</ul>

<input type="text" value="<?php echo $reflink; ?>" onclick="$(this).select();" />

</div>

</section>

<?php } } ?>

</div>

<?php

} else {

    echo read_template_part( '404' );

}