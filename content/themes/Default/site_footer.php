</div>

</div>

<footer>

<?php

$socnet = social_networds();
if( !empty( $socnet ) ) {
  echo '<ul class="social_icons">';
  foreach( $socnet as $k => $v ) {
    echo '<li><a href="' . $v . '" class="' . $k . '"></a></li>';
  }
  echo '</ul>';
}

?>

<div class="counsub">

<ul>
<li><?php echo site_count( 'stores' ); ?></li>
<li><?php echo site_count( 'coupons' ); ?></li>
<?php if( theme_has_products() ) echo '<li>'. site_count( 'products' ) . '</li>'; ?>
</ul>

<section class="fo-newsletter">
<?php $csrf = $_SESSION['csrf']['ajax_subscribe'] = rand(1,999999); ?>
<h2>Don't miss a thing !</h2>
<form action="#" method="POST" data-ajax="<?php echo $GLOBALS['siteURL']; ?>index.php?ajax=subscribe" data-submit="footer-ajax" autocomplete="off">
<input type="email" name="subscribe[email]" placeholder="Your email address" required />
<input type="hidden" name="csrf" value="<?php echo $csrf; ?>" />
<button class="btn">Subscribe</button>
</form>
</section>

</div>

<div class="container">

<div class="left">

<ul>
<li><a href="<?php echo tlink( 'stores' ); ?>">Stores/Brands</a></li>
<li><a href="<?php echo tlink( 'tpage/top-stores' ); ?>">Top Stores/Brands</a></li>
<li><a href="<?php echo tlink( 'tpage/most-rated' ); ?>">Most Voted</a></li>
<li><a href="<?php echo tlink( 'user/suggest' ); ?>">Make a suggestion</a></li>
</ul>

<ul>
<li><a href="<?php echo tlink( 'tpage/contact' ); ?>">Contact</a></li>
<li><a href="<?php echo tlink( 'page', array( 'seo' => 'About_Us-1.html', 'notseo' => 'p=1' ) ); ?>">About Us</a></li>
<li><a href="<?php echo tlink( 'user/register' ); ?>">Register</a></li>
<li><a href="<?php echo tlink( 'user/login' ); ?>">Sign In</a></li>
</ul>

</div>

<div class="right">

<div class="site_desc"><?php echo description(); ?></div>

Powered by <a href="//couponscms.com">CouponsCMS.com</a>

</div>

</div>

<div style="text-align: center;">(c) <?php echo site_name(); ?></div>

</footer>

</body>

</html>