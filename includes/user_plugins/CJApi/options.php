<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

default:

echo '<div class="title">

<h2>CJ.com Options</h2>

<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=advertisers">Advertisers</a></li>
<li><a href="?plugin=CJApi/cj.php&amp;action=links">Links</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
</ul>
</div>

</div>

<span>Modify CJ.com API settings</span>

</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'slider_csrf' ) ) {

  if( isset( $_POST['key'] ) && isset( $_POST['site-id'] ) && isset( $_POST['exp'] ) && isset( $_POST['ipp'] ) )
  if( actions::set_option( array( 'cj_key' => $_POST['key'], 'cj_site-id' => $_POST['site-id'], 'cj_exp' => $_POST['exp'], 'cj_ipp' => $_POST['ipp'] ) ) )
  echo '<div class="a-success">Saved!</div>';
  else
  echo '<div class="a-error">Error!</div>';

}

$csrf = $_SESSION['slider_csrf'] = \site\utils::str_random(10);

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>Developer Key: <span class="info"><span>Developer keys can be generated at <a href="https://api.cj.com" target="_blank" style="color: #FFF;">api.cj.com</a></span></span></span><div><input type="text" name="key" value="' . htmlspecialchars( \query\main::get_option( 'cj_key' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>Site ID: <span class="info"><span>To see your website ID, log into your <a href="//cj.com" target="_blank" style="color: #FFF;">cj.com</a> account, then go in section <i>Account</i> and select <i>Websites.</i></span></span></span><div><input type="text" name="site-id" value="' . htmlspecialchars( \query\main::get_option( 'cj_site-id' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>Deals Expiration (days) <span class="info"><span>Deals and some coupons do have not set an expiration date. This will be set automatically after a number of days that you can define here. It can be changed in preview mode.</span></span>:</span><div><input type="number" name="exp" value="' . (int) \query\main::get_option( 'cj_exp' ) . '" min="1" max="1000" required /></div></div>
<div class="row"><span>Items Per Page:</span><div><input type="number" name="ipp" value="' . (int) \query\main::get_option( 'cj_ipp' ) . '" min="1" max="100" required /></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>

</form>';

break;

}