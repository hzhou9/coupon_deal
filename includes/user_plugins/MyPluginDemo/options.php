<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

default:

echo '<div class="title">

<h2>MyPluginDemo settings</h2>

<span>Modify MyPluginDemo settings</span>

</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'slider_csrf' ) ) {

  if( isset( $_POST['mpd_param1'] ) && isset( $_POST['mpd_param2'] ))
  if( actions::set_option( array( 'mpd_param1' => $_POST['mpd_param1'], 'mpd_param2' => $_POST['mpd_param2'] ) ) )
  echo '<div class="a-success">Saved!</div>';
  else
  echo '<div class="a-error">Error!</div>';

}

$csrf = $_SESSION['slider_csrf'] = \site\utils::str_random(10);

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>Demo Param1: <span class="info"><span>info text here</span></span></span><div><input type="text" name="mpd_param1" value="' . htmlspecialchars( \query\main::get_option( 'mpd_param1' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>Demo Param2: <span class="info"><span>info text here</span></span></span><div><input type="text" name="mpd_param2" value="' . htmlspecialchars( \query\main::get_option( 'mpd_param2' ) ) . '" style="background: #F8E0E0;" required /></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>

</form>';

break;

}