<div class="sign_in">

<div class="wrapper">

<?php

$form = '';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['login_form'] ) && isset( $_POST['login_form']['csrf'] ) && isset( $_SESSION['csrf']['login'] ) && $_POST['login_form']['csrf'] == $_SESSION['csrf']['login'] ) {

  $pd = \site\utils::validate_user_data( $_POST['login_form'] );

  try {

    $session = \user\main::login( $pd, 1 );
    $form .= '<div class="success">' . $LANG['login_success'] . '</div>';
    $form .= '<meta http-equiv="refresh" content="1; url='. $GLOBALS['siteURL'] . '/setSession.php?session=' . $session . '&back=' . rtrim( $GLOBALS['siteURL'], '/' ) . '/' . ADMINDIR . '">';

  }

  catch( Exception $e ){

    $form .= '<div class="error">' . $e->getMessage() . '</div>';

    }

}

$csrf = $_SESSION['csrf']['login'] = \site\utils::str_random(12);

echo $form;

?>

<form action="#" method="POST">
<input type="text" name="login_form[username]" value="<?php echo (isset( $pd['username'] ) ? htmlspecialchars( $pd['username'] ) : ''); ?>" placeholder="<?php echo $LANG['form_email']; ?>" required />
<input type="password" name="login_form[password]" placeholder="<?php echo $LANG['form_password']; ?>" required />
<button><?php echo $LANG['login']; ?></button>
<span style="line-height: 40px; float: right"><input type="checkbox" name="login_form[keep_logged]" id="login_form[keep_logged]" /> <label for="login_form[keep_logged]"><?php echo $LANG['msg_keep_log']; ?></label></span>
<input type="hidden" name="login_form[csrf]" value="<?php echo $csrf; ?>" />
</form>

<div style="margin: 20px 0 0 0; text-align: center;">
<a href="?action=password_recovery"><?php echo $LANG['forgot_password']; ?></a>
</div>

</div>

<div class="links">
<a href="../">&#8592; <?php echo sprintf( $LANG['visit_site'], \query\main::get_option( 'sitename' ) ); ?></a>
<a href="http://couponsCMS.com">CouponsCMS.com</a>

</div>

</div>