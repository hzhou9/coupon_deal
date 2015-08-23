<div class="left">

<div class="title">Login</div>

<?php echo login_form(); ?>

</div>

<div class="right">

<div style="margin:0 auto; text-align: center; width: 90%;">

<div class="ul-tit">Forgot your password?
<a href="<?php echo tlink( 'user/password_recovery' ); ?>" class="btn">Recovery password</a>
</div>

<div class="ul-tit" style="margin: 15px 0 30px 0;">Don't you have an account?
<a href="<?php echo tlink( 'user/register' ); ?>" class="btn">Register new account</a>
</div>

<div class="social_share">
<?php if( facebook_login() ) { ?>
  <a href="<?php echo tlink( 'plugin/facebook_login.html' ); ?>" class="facebook" style="width: 100%;">Login with Facebook</a>
<?php } if( google_login() ) { ?>
  <a href="<?php echo tlink( 'plugin/google_login.html' ); ?>" class="google" style="width: 100%;">Login with Google+</a>
<?php } ?>
</div>

</div>

</div>