<?php

switch( $_GET['action'] ) {

/** SEND EMAIL */

case 'sendmail':

if( !ab_to( array( 'mail' => 'send' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['users_sendmail_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=list" class="btn">' . $LANG['users_view'] . '</a>
</div>';

if( !empty( $LANG['users_sendmail_subtitle'] ) ) {
  echo '<span>' . $LANG['users_sendmail_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'sendmail_csrf' ) ) {

  if( !empty( $_POST['fname'] ) && !empty( $_POST['femail'] ) && !empty( $_POST['temails'] ) && !empty( $_POST['subject'] ) && !empty( $_POST['text'] ) ) {

  $suc = $err = 0;

  foreach( array_unique( array_filter( explode( ',', $_POST['temails'] ) ) ) as $email ) {

  if( \site\mail::send( trim( $email ), $_POST['subject'], array( 'template' => 'sendmail', 'path' => '../', 'from_email' => $_POST['femail'], 'from_name' => $_POST['fname'], 'reply_to' => $_POST['femail'], 'reply_name' => $_POST['fname'] ), array( 'text' => nltobr( $_POST['text'] ) ) ) ) {
    $suc++;
  } else $err++;

  }

  if( $suc > $err )
    echo '<div class="a-success">' . sprintf( $LANG['msg_mailssent'], $suc, $err ) . '</div>';
  else
    echo '<div class="a-error">' . sprintf( $LANG['msg_mailssent'], $suc, $err ) . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['sendmail_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . $LANG['users_sendmail_fname'] . ':</span><div><input type="text" name="fname" value="' . \query\main::get_option( 'email_from_name' ) . '" required /></div></div>
<div class="row"><span>' . $LANG['users_sendmail_femail'] . ':</span><div><input type="text" name="femail" value="' . \query\main::get_option( 'email_answer_to' ) . '" required /></div></div>
<div class="row"><span>' . $LANG['users_sendmail_temails'] . ' <span class="info"><span>' . $LANG['users_sendmail_itemails'] . '</span></span>:</span><div><input type="text" name="temails" value="' . ( isset( $_GET['email'] ) ? htmlspecialchars( $_GET['email'] ) : '' ) . '" required /></div></div>
<div class="row"><span>' . $LANG['users_sendmail_subject'] . ':</span><div><input type="text" name="subject" value="" required /></div></div>
<div class="row"><span>' . $LANG['users_sendmail_text'] . ':</span><div><textarea name="text" style="min-height: 200px;">' . htmlspecialchars( \query\main::get_option( 'mail_signature' ) ) . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['users_sendmail_button'] . '</button>

</form>

</div>';

break;

/** ADD USER */

case 'add':

if( !ab_to( array( 'users' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['users_add_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=list" class="btn">' . $LANG['users_view'] . '</a>
</div>';

if( !empty( $LANG['users_add_subtitle'] ) ) {
  echo '<span>' . $LANG['users_add_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

  if( isset( $_POST['name'] ) && isset( $_POST['email'] ) && isset( $_POST['password'] ) && isset( $_POST['points'] ) && ( ! $GLOBALS['me']->is_admin || isset( $_POST['privileges'] ) && in_array( $_POST['privileges'], array( 0, 1, 2 ) ) ) )
  if( actions::add_user( array( 'name' => $_POST['name'], 'email' => $_POST['email'], 'password' => $_POST['password'], 'points' => $_POST['points'], 'credits' => ( $GLOBALS['me']->is_admin && isset( $_POST['credits'] ) ? $_POST['credits'] : 0 ), 'privileges' => ( $GLOBALS['me']->is_admin ? $_POST['privileges'] : '' ), 'erole' => ( $GLOBALS['me']->is_admin ? ( isset( $_POST['erole'] ) && (int) $_POST['privileges'] === 1 ? $_POST['erole'] : '' ) : '' ), 'subscriber' => ( isset( $_POST['subscriber'] ) ? 1 : 0 ), 'confirm' => ( isset( $_POST['confirm'] ) ? 1 : 0 ) ) ) ) {
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';

  if( isset( $_POST['send_copy'] ) ) {
    \site\mail::send( $_POST['email'], $LANG['email_ac_title'] . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'account_creation', 'path' => '../' ), array( 'ac_main_text' => sprintf( $LANG['email_ac_maintext'], \query\main::get_option( 'sitename' ) ), 'form_email' => $LANG['email_ac_email'], 'form_password' => $LANG['email_ac_password'], 'email' => $_POST['email'], 'password' => $_POST['password'], 'link' => \query\main::get_option( 'siteurl' ) ) );
  }

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="" /></div></div>
<div class="row"><span>' . $LANG['form_email'] . ':</span><div><input type="email" name="email" value="" /></div></div>
<div class="row"><div><input type="checkbox" name="send_copy" id="send_copy" checked /> <label for="send_copy">' . $LANG['msg_sendcacc'] . '</label></div></div>
<div class="row"><span>' . $LANG['form_password'] . ':</span><div><input type="password" name="password" value="" /></div></div>
<div class="row"><span>' . $LANG['form_avatar'] . ':</span> <div><input type="file" name="logo" /></div> </div>

<div class="row"><span>' . $LANG['form_points'] . ':</span><div><input type="number" name="points" value="0" min="0" /></div></div>';

if( $GLOBALS['me']->is_admin ) {

echo '<div class="row"><span>' . $LANG['form_credits'] . ':</span><div><input type="number" name="credits" value="0" min="0" /></div></div>';

echo '<div class="row"><span>' . $LANG['form_role'] . ':</span><div><select name="privileges">';
foreach( array( 0 => $LANG['form_role_member'], 1 => $LANG['form_role_subadmin'], 2 => $LANG['form_role_admin'] ) as $k => $v )echo '<option value="' . $k . '"' . ( $k == 0 ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div>

<div id="privileges_scope" style="display: none;">

<div> <h2>' . $LANG['stores'] . ':</h2> <div> <div><input type="checkbox" name="erole[stores][view]" id="erole[stores][view]" value="1"' . ( isset( $_POST['erole']['stores']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[stores][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[stores][add]" id="erole[stores][add]" value="1"' . ( isset( $_POST['erole']['stores']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[stores][add]">' . $LANG['add'] . '</label> <br /> <input type="checkbox" name="erole[stores][import]" id="erole[stores][import]" value="1"' . ( isset( $_POST['erole']['stores']['import'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[stores][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[stores][export]" id="erole[stores][export]" value="1"' . ( isset( $_POST['erole']['stores']['export'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[stores][export]">' . $LANG['export'] . '</label> </div> <div><input type="checkbox" name="erole[stores][edit]" id="erole[stores][edit]" value="1"' . ( isset( $_POST['erole']['stores']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[stores][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[stores][delete]" id="erole[stores][delete]" value="1"' . ( isset( $_POST['erole']['stores']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[stores][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['categories'] . ':</h2> <div> <div><input type="checkbox" name="erole[categories][view]" id="erole[categories][view]" value="1"' . ( isset( $_POST['erole']['categories']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[categories][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[categories][add]" id="erole[categories][add]" value="1"' . ( isset( $_POST['erole']['categories']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[categories][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[categories][edit]" id="erole[categories][edit]" value="1"' . ( isset( $_POST['erole']['categories']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[categories][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[categories][delete]" id="erole[categories][delete]" value="1"' . ( isset( $_POST['erole']['categories']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[categories][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['coupons'] . ':</h2> <div> <div><input type="checkbox" name="erole[coupons][view]" id="erole[coupons][view]" value="1"' . ( isset( $_POST['erole']['coupons']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[coupons][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[coupons][add]" id="erole[coupons][add]" value="1"' . ( isset( $_POST['erole']['coupons']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[coupons][add]">' . $LANG['add'] . '</label> <br /> <input type="checkbox" name="erole[coupons][import]" id="erole[coupons][import]" value="1"' . ( isset( $_POST['erole']['coupons']['import'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[coupons][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[coupons][export]" id="erole[coupons][export]" value="1"' . ( isset( $_POST['erole']['coupons']['export'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[coupons][export]">' . $LANG['export'] . '</label></div> <div><input type="checkbox" name="erole[coupons][edit]" id="erole[coupons][edit]" value="1"' . ( isset( $_POST['erole']['coupons']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[coupons][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[coupons][delete]" id="erole[coupons][delete]" value="1"' . ( isset( $_POST['erole']['coupons']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[coupons][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['products'] . ':</h2> <div> <div><input type="checkbox" name="erole[products][view]" id="erole[products][view]" value="1"' . ( isset( $_POST['erole']['products']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[products][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[products][add]" id="erole[products][add]" value="1"' . ( isset( $_POST['erole']['products']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[products][add]">' . $LANG['add'] . '</label> <br /> <input type="checkbox" name="erole[products][import]" id="erole[products][import]" value="1"' . ( isset( $_POST['erole']['products']['import'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[products][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[products][export]" id="erole[products][export]" value="1"' . ( isset( $_POST['erole']['products']['export'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[products][export]">' . $LANG['export'] . '</label></div> <div><input type="checkbox" name="erole[products][edit]" id="erole[products][edit]" value="1"' . ( isset( $_POST['erole']['products']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[products][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[products][delete]" id="erole[products][delete]" value="1"' . ( isset( $_POST['erole']['products']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[products][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['users'] . ':</h2> <div> <div><input type="checkbox" name="erole[users][view]" id="erole[users][view]" value="1"' . ( isset( $_POST['erole']['users']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[users][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[users][add]" id="erole[users][add]" value="1"' . ( isset( $_POST['erole']['users']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[users][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[users][edit]" id="erole[users][edit]" value="1"' . ( isset( $_POST['erole']['users']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[users][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[users][delete]" id="erole[users][delete]" value="1"' . ( isset( $_POST['erole']['users']['delete'] ) ? ' checked' : '' ) . ' /> <label for="erole[users][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['users_subscribers'] . ':</h2> <div> <div><input type="checkbox" name="erole[subscribers][view]" id="erole[subscribers][view]" value="1"' . ( isset( $_POST['erole']['subscribers']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[subscribers][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[subscribers][import]" id="erole[subscribers][import]" value="1"' . ( isset( $_POST['erole']['subscribers']['import'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[subscribers][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[subscribers][export]" id="erole[subscribers][export]" value="1"' . ( isset( $_POST['erole']['subscribers']['export'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[subscribers][export]">' . $LANG['export'] . '</label></div> <div><input type="checkbox" name="erole[subscribers][edit]" id="erole[subscribers][edit]" value="1"' . ( isset( $_POST['erole']['subscribers']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[subscribers][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[subscribers][delete]" id="erole[subscribers][delete]" value="1"' . ( isset( $_POST['erole']['subscribers']['delete'] ) ? ' checked' : '' ) . ' /> <label for="erole[subscribers][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['pages'] . ':</h2> <div> <div><input type="checkbox" name="erole[pages][view]" id="erole[pages][view]" value="1"' . ( isset( $_POST['erole']['pages']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[pages][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[pages][add]" id="erole[pages][add]" value="1"' . ( isset( $_POST['erole']['pages']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[pages][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[pages][edit]" id="erole[pages][edit]" value="1"' . ( isset( $_POST['erole']['pages']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[pages][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[pages][delete]" id="erole[pages][delete]" value="1"' . ( isset( $_POST['erole']['pages']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[pages][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['reviews'] . ':</h2> <div> <div><input type="checkbox" name="erole[reviews][view]" id="erole[reviews][view]" value="1"' . ( isset( $_POST['erole']['reviews']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[reviews][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[reviews][add]" id="erole[reviews][add]" value="1"' . ( isset( $_POST['erole']['reviews']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[reviews][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[reviews][edit]" id="erole[reviews][edit]" value="1"' . ( isset( $_POST['erole']['reviews']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[reviews][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[reviews][delete]" id="erole[reviews][delete]" value="1"' . ( isset( $_POST['erole']['reviews']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[reviews][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['rewards_claimr'] . ':</h2> <div> <div><input type="checkbox" name="erole[claim_reqs][view]" id="erole[claim_reqs][view]" value="1"' . ( isset( $_POST['erole']['claim_reqs']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[claim_reqs][edit]" id="erole[claim_reqs][edit]" value="1"' . ( isset( $_POST['erole']['claim_reqs']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[claim_reqs][delete]" id="erole[claim_reqs][delete]" value="1"' . ( isset( $_POST['erole']['claim_reqs']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['suggestions'] . ':</h2> <div> <div><input type="checkbox" name="erole[suggestions][view]" id="erole[suggestions][view]" value="1"' . (isset( $_POST['erole']['suggestions']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[suggestions][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[suggestions][edit]" id="erole[suggestions][edit]" value="1"' . ( isset( $_POST['erole']['suggestions']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[suggestions][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[suggestions][delete]" id="erole[suggestions][delete]" value="1"' . ( isset( $_POST['erole']['suggestions']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[suggestions][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['chat_title'] . ':</h2> <div> <div><input type="checkbox" name="erole[chat][view]" id="erole[chat][view]" value="1"' . ( isset( $_POST['erole']['chat']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[chat][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[chat][add]" id="erole[chat][add]" value="1"' . ( isset( $_POST['erole']['chat']['add'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[chat][add]">' . $LANG['chat_write_button'] . '</label></div> <div><input type="checkbox" name="erole[chat][delete]" id="erole[chat][delete]" value="1"' . ( isset( $_POST['erole']['chat']['delete'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[chat][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['ratings'] . ':</h2> <div> <div><input type="checkbox" name="erole[raports][view]" id="erole[raports][view]" value="1"' . ( isset( $_POST['erole']['raports']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[raports][view]">' . $LANG['view'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['feed'] . ':</h2> <div> <div><input type="checkbox" name="erole[feed][view]" id="erole[feed][view]" value="1"' . ( isset( $_POST['erole']['feed']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[feed][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[feed][import]" id="erole[feed][import]" value="1"' . ( isset( $_POST['erole']['feed']['import'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[feed][import]">' . $LANG['import'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['payments'] . ':</h2> <div> <div><input type="checkbox" name="erole[payments][view]" id="erole[payments][view]" value="1"' . ( isset( $_POST['erole']['payments']['view'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[payments][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[payments][edit]" id="erole[payments][edit]" value="1"' . ( isset( $_POST['erole']['payments']['edit'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[payments][edit]">' . $LANG['edit'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['others'] . ':</h2> <div> <div><input type="checkbox" name="erole[mail][send]" id="erole[mail][send]" value="1"' . ( isset( $_POST['erole']['mail']['send'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[mail][send]">' . $LANG['send_email'] . '</label></div> <div><input type="checkbox" name="erole[users][ban]" id="erole[users][ban]" value="1"' . ( isset( $_POST['erole']['users']['ban'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? ' checked' : '' ) . ' /> <label for="erole[users][ban]">' . $LANG['ban'] . '</label></div> </div> </div>

</div>

</div>';

}

echo '<div class="row"><span>' . $LANG['form_subscriber'] . ':</span><div><input type="checkbox" name="subscriber" id="subscriber" checked /> <label for="subscriber">' . $LANG['msg_setsub'] . '</label></div></div>
<div class="row"><span>' . $LANG['form_confirm'] . ':</span><div><input type="checkbox" name="confirm" id="confirm" checked /> <label for="confirm">' . $LANG['msg_setconf'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['users_add_button'] . '</button>

</form>

</div>';

break;

/** EDIT USER */

case 'edit':

if( !( ab_to( array( 'users' => 'edit' ) ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['users_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $user_exists = \query\main::user_exists( $_GET['id'] ) ) ) {

$info = \query\main::user_infos( $_GET['id'] );

echo '<div class="options">

<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( ab_to( array( 'users' => 'delete' ) ) ) echo '<li><a href="?route=users.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
if( $info->is_confirmed ) {
  echo '<li><a href="?route=users.php&amp;action=list&amp;type=unverify&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['unverify'] . '</a></li>';
} else {
  echo '<li><a href="?route=users.php&amp;action=list&amp;type=verify&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['verify'] . '</a></li>';
}
if( ab_to( array( 'mail' => 'send' ) ) )   echo '<li><a href="?route=users.php&amp;action=sendmail&amp;email=' . $info->email . '">' . $LANG['send_email'] . '</a></li>';
echo '</ul>
</div>';

}

echo '<a href="?route=users.php&amp;action=list" class="btn">' . $LANG['users_view'] . '</a>
</div>';

if( !empty( $LANG['users_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['users_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $user_exists && ( ! $GLOBALS['me']->is_admin && $info->is_admin ) ) {

  echo '<div class="a-alert">' . $LANG['can_edit_infos'] . '</div>';

} else if( $user_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['ban_user'] ) || isset( $_POST['unban_user'] ) ) {

  if( actions::ban_user( $_GET['id'], array( 'date' => isset( $_POST['unban_user'] ) ? strtotime( '1 second ago' ) : ( isset( $_POST['expiration']['date'] ) && isset( $_POST['expiration']['hour'] ) ? strtotime( $_POST['expiration']['date'] . ', ' . $_POST['expiration']['hour'] ) : strtotime( '1 second ago' ) ) ) ) ) {

  $info = \query\main::user_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['change_password'] ) ) {

  if( isset( $_POST['password'] ) )
  if( actions::change_user_password( $_GET['id'], array( 'password' => $_POST['password'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_changed'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else {

  if( isset( $_POST['name'] ) && isset( $_POST['email'] ) && isset( $_POST['points'] ) && ( ! $GLOBALS['me']->is_admin || isset( $_POST['privileges'] ) && in_array( $_POST['privileges'], array( 0, 1, 2 ) ) ) )
  if( actions::edit_user( $_GET['id'], array( 'name' => $_POST['name'], 'email' => $_POST['email'], 'points' => $_POST['points'], 'credits' => ( $GLOBALS['me']->is_admin && isset( $_POST['credits'] ) ? $_POST['credits'] : $info->credits ), 'privileges' => ( $GLOBALS['me']->is_admin ? $_POST['privileges'] : $info->privileges ), 'erole' => ( $GLOBALS['me']->is_admin && isset( $_POST['erole'] ) && (int) $_POST['privileges'] === 1 ? $_POST['erole'] : $info->erole ), 'subscriber' => ( isset( $_POST['subscriber'] ) ? 1 : 0 ), 'confirm' => ( isset( $_POST['confirm'] ) ? 1 : 0 ) ) ) ) {

  $info = \query\main::user_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['type'] == 'delete_avatar' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_user_avatar( $_GET['id'] ) ) {

  $info->avatar = '';

  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$_SESSION['users_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $info->name . '" /></div></div>
<div class="row"><span>' . $LANG['form_email'] . ':</span><div><input type="email" name="email" value="' . $info->email . '" /></div></div>
<div class="row"><span>' . $LANG['form_avatar'] . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . \query\main::user_avatar( $info->avatar ) . '" class="avt" alt="" style="display: table-cell; width:80px; height:80px; margin: 0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->avatar ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_avatar', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
echo '</div>
</div>

<input type="file" name="logo" />
</div> </div>

<div class="row"><span>' . $LANG['form_points'] . ':</span><div><input type="number" name="points" value="' . $info->points . '" min="0" /></div></div>';

if( $GLOBALS['me']->is_admin ) {

echo '<div class="row"><span>' . $LANG['form_credits'] . ':</span><div><input type="number" name="credits" value="' . $info->credits . '" min="0" /></div></div>';

echo '<div class="row"><span>' . $LANG['form_role'] . ':</span><div><select name="privileges">';
foreach( array( 0 => $LANG['form_role_member'], 1 => $LANG['form_role_subadmin'], 2 => $LANG['form_role_admin'] ) as $k => $v )echo '<option value="' . $k . '"' . ( $info->privileges == $k ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div>

<div id="privileges_scope"' . ( $info->privileges !== 1 ? ' style="display: none;"' : '' ) . '>

<div> <h2>' . $LANG['stores'] . ':</h2> <div> <div><input type="checkbox" name="erole[stores][view]" id="erole[stores][view]" value="1"' . ( isset( $info->erole['stores']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[stores][add]" id="erole[stores][add]" value="1"' . ( isset( $info->erole['stores']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][add]">' . $LANG['add'] . '</label> <br /> <input type="checkbox" name="erole[stores][import]" id="erole[stores][import]" value="1"' . ( isset( $info->erole['stores']['import'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[stores][export]" id="erole[stores][export]" value="1"' . ( isset( $info->erole['stores']['export'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][export]">' . $LANG['export'] . '</label></div> <div><input type="checkbox" name="erole[stores][edit]" id="erole[stores][edit]" value="1"' . ( isset( $info->erole['stores']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[stores][delete]" id="erole[stores][delete]" value="1"' . ( isset( $info->erole['stores']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['categories'] . ':</h2> <div> <div><input type="checkbox" name="erole[categories][view]" id="erole[categories][view]" value="1"' . ( isset( $info->erole['categories']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[categories][add]" id="erole[categories][add]" value="1"' . ( isset( $info->erole['categories']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[categories][edit]" id="erole[categories][edit]" value="1"' . ( isset( $info->erole['categories']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[categories][delete]" id="erole[categories][delete]" value="1"' . ( isset( $info->erole['categories']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['coupons'] . ':</h2> <div> <div><input type="checkbox" name="erole[coupons][view]" id="erole[coupons][view]" value="1"' . ( isset( $info->erole['coupons']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[coupons][add]" id="erole[coupons][add]" value="1"' . ( isset( $info->erole['coupons']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][add]">' . $LANG['add'] . '</label> <br /> <input type="checkbox" name="erole[coupons][import]" id="erole[coupons][import]" value="1"' . ( isset( $info->erole['coupons']['import'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[coupons][export]" id="erole[coupons][export]" value="1"' . ( isset( $info->erole['coupons']['export'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][export]">' . $LANG['export'] . '</label></div> <div><input type="checkbox" name="erole[coupons][edit]" id="erole[coupons][edit]" value="1"' . ( isset( $info->erole['coupons']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[coupons][delete]" id="erole[coupons][delete]" value="1"' . ( isset( $info->erole['coupons']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['products'] . ':</h2> <div> <div><input type="checkbox" name="erole[products][view]" id="erole[products][view]" value="1"' . ( isset( $info->erole['products']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[products][add]" id="erole[products][add]" value="1"' . ( isset( $info->erole['products']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][add]">' . $LANG['add'] . '</label> <br /> <input type="checkbox" name="erole[products][import]" id="erole[products][import]" value="1"' . ( isset( $info->erole['products']['import'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[products][export]" id="erole[products][export]" value="1"' . ( isset( $info->erole['products']['export'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][export]">' . $LANG['export'] . '</label></div> <div><input type="checkbox" name="erole[products][edit]" id="erole[products][edit]" value="1"' . ( isset( $info->erole['products']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[products][delete]" id="erole[products][delete]" value="1"' . ( isset( $info->erole['products']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['users'] . ':</h2> <div> <div><input type="checkbox" name="erole[users][view]" id="erole[users][view]" value="1"' . ( isset( $info->erole['users']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[users][add]" id="erole[users][add]" value="1"' . ( isset( $info->erole['users']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[users][edit]" id="erole[users][edit]" value="1"' . ( isset( $info->erole['users']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[users][delete]" id="erole[users][delete]" value="1"' . ( isset( $info->erole['users']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['users_subscribers'] . ':</h2> <div> <div><input type="checkbox" name="erole[subscribers][view]" id="erole[subscribers][view]" value="1"' . ( isset( $info->erole['subscribers']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[subscribers][import]" id="erole[subscribers][import]" value="1"' . ( isset( $info->erole['subscribers']['import'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][import]">' . $LANG['import'] . '</label> <br /> <input type="checkbox" name="erole[subscribers][export]" id="erole[subscribers][export]" value="1"' . ( isset( $info->erole['subscribers']['export'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][export]">' . $LANG['export'] . '</label></div> <div><input type="checkbox" name="erole[subscribers][edit]" id="erole[subscribers][edit]" value="1"' . ( isset( $info->erole['subscribers']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[subscribers][delete]" id="erole[subscribers][delete]" value="1"' . ( isset( $info->erole['subscribers']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['pages'] . ':</h2> <div> <div><input type="checkbox" name="erole[pages][view]" id="erole[pages][view]" value="1"' . ( isset( $info->erole['pages']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[pages][add]" id="erole[pages][add]" value="1"' . ( isset( $info->erole['pages']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[pages][edit]" id="erole[pages][edit]" value="1"' . ( isset( $info->erole['pages']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[pages][delete]" id="erole[pages][delete]" value="1"' . ( isset( $info->erole['pages']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['reviews'] . ':</h2> <div> <div><input type="checkbox" name="erole[reviews][view]" id="erole[reviews][view]" value="1"' . ( isset( $info->erole['reviews']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[reviews][add]" id="erole[reviews][add]" value="1"' . ( isset( $info->erole['reviews']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][add]">' . $LANG['add'] . '</label></div> <div><input type="checkbox" name="erole[reviews][edit]" id="erole[reviews][edit]" value="1"' . ( isset( $info->erole['reviews']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[reviews][delete]" id="erole[reviews][delete]" value="1"' . ( isset( $info->erole['reviews']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['rewards_claimr'] . ':</h2> <div> <div><input type="checkbox" name="erole[claim_reqs][view]" id="erole[claim_reqs][view]" value="1"' . ( isset( $info->erole['claim_reqs']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[claim_reqs][edit]" id="erole[claim_reqs][edit]" value="1"' . ( isset( $info->erole['claim_reqs']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[claim_reqs][delete]" id="erole[claim_reqs][delete]" value="1"' . ( isset( $info->erole['claim_reqs']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['suggestions'] . ':</h2> <div> <div><input type="checkbox" name="erole[suggestions][view]" id="erole[suggestions][view]" value="1"' . ( isset( $info->erole['suggestions']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[suggestions][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[suggestions][edit]" id="erole[suggestions][edit]" value="1"' . ( isset( $info->erole['suggestions']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[suggestions][edit]">' . $LANG['edit'] . '</label></div> <div><input type="checkbox" name="erole[suggestions][delete]" id="erole[suggestions][delete]" value="1"' . ( isset( $info->erole['suggestions']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[suggestions][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['chat_title'] . ':</h2> <div> <div><input type="checkbox" name="erole[chat][view]" id="erole[chat][view]" value="1"' . ( isset( $info->erole['chat']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[chat][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[chat][add]" id="erole[chat][add]" value="1"' . ( isset( $info->erole['chat']['add'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[chat][add]">' . $LANG['chat_write_button'] . '</label></div> <div><input type="checkbox" name="erole[chat][delete]" id="erole[chat][delete]" value="1"' . ( isset( $info->erole['chat']['delete'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[chat][delete]">' . $LANG['delete'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['ratings'] . ':</h2> <div> <div><input type="checkbox" name="erole[raports][view]" id="erole[raports][view]" value="1"' . ( isset( $info->erole['raports']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[raports][view]">' . $LANG['view'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['feed'] . ':</h2> <div> <div><input type="checkbox" name="erole[feed][view]" id="erole[feed][view]" value="1"' . ( isset( $info->erole['feed']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[feed][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[feed][import]" id="erole[feed][import]" value="1"' . ( isset( $info->erole['feed']['import'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[feed][import]">' . $LANG['import'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['payments'] . ':</h2> <div> <div><input type="checkbox" name="erole[payments][view]" id="erole[payments][view]" value="1"' . ( isset( $info->erole['payments']['view'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[payments][view]">' . $LANG['view'] . '</label></div> <div><input type="checkbox" name="erole[payments][edit]" id="erole[payments][edit]" value="1"' . ( isset( $info->erole['payments']['edit'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[payments][edit]">' . $LANG['edit'] . '</label></div> </div> </div>
<div> <h2>' . $LANG['others'] . ':</h2> <div> <div><input type="checkbox" name="erole[mail][send]" id="erole[mail][send]" value="1"' . ( isset( $info->erole['mail']['send'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[mail][send]">' . $LANG['send_email'] . '</label></div> <div><input type="checkbox" name="erole[users][ban]" id="erole[users][ban]" value="1"' . ( isset( $info->erole['users']['ban'] ) || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][ban]">' . $LANG['ban'] . '</label></div> </div> </div>

</div>

</div>';

}

echo '<div class="row"><span>' . $LANG['form_subscriber'] . ':</span><div><input type="checkbox" name="subscriber" id="subscriber"' . ( $info->is_subscribed ? ' checked' : '' ) . ' /> <label for="subscriber">' . $LANG['msg_setsub'] . '</label></div></div>
<div class="row"><span>' . $LANG['form_confirm'] . ':</span><div><input type="checkbox" name="confirm" id="confirm"' . ( $info->is_confirmed ? ' checked' : '' ) . ' /> <label for="confirm">' . $LANG['msg_setconf'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['users_edit_button'] . '</button>

</form>

</div>';

if( ab_to( array( 'users' => 'ban' ) ) && !$info->is_admin ) {

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['users_ban_title'] . '</h2>

</div>';

if( $info->is_banned ) echo '<div class="a-message">' . sprintf( $LANG['msg_banned_until'], $info->ban ) . '</div>';

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . $LANG['form_expiration_date'] . ':</span><div><input type="date" name="expiration[date]" value="' .  date( 'Y-m-d', ( $info->is_banned ? strtotime( $info->ban ) : strtotime( '+1 week' ) ) ) . '" style="width: 80%" /><input type="time" name="expiration[hour]" value="' . ( $info->is_banned ? date( 'H:i', strtotime( $info->ban ) ) : date( 'H:i' ) ) . '" style="width: 20%" /></div></div>';

if( !$info->is_banned ) echo '<div class="row"><span>' . $LANG['form_fastchoice'] . ':</span><div id="ban_fast_choice"><a href="#" data=\'{"interval":"day","nr":1}\'>1 ' . $LANG['day'] . '</a> / <a href="#" data=\'{"interval":"day","nr":2}\'>2 ' . $LANG['days'] . '</a> / <a href="#" data=\'{"interval":"day","nr":3}\'>3 ' . $LANG['days'] . '</a> / <a href="#" data=\'{"interval":"week","nr":1}\'>1 ' . $LANG['week'] . '</a> / <a href="#" data=\'{"interval":"month","nr":1}\'>1 ' . $LANG['month'] . '</a> / <a href="#" data=\'{"interval":"month","nr":3}\'>3 ' . $LANG['months'] . '</a> / <a href="#" data=\'{"interval":"month","nr":6}\'>6 ' . $LANG['months'] . '</a> / <a href="#" data=\'{"interval":"year","nr":1}\'>1 ' . $LANG['year'] . '</a></div></div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn" name="ban_user">' . ( $info->is_banned ? $LANG['users_ban_updatebutton'] : $LANG['users_ban_button'] ) . '</button>';
if( $info->is_banned ) echo ' <button class="btn" name="unban_user">' . $LANG['users_unban_button'] . '</button>';

echo '</form>

</div>';

}

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['users_cp_title'] . '</h2>

</div>

<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . $LANG['form_new_password'] . ':</span><div><input type="password" name="password" value="" /></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn" name="change_password">' . $LANG['users_cp_button'] . '</button>

</form>

</div>


<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['users_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['reffered'] . ':</span> <div>';
if( empty( $info->refid ) ) echo $LANG['no'];
else {
  $ref_user = \query\main::user_infos( $info->refid );
  echo ( empty( $ref_user->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->refid . '">' . $ref_user->name . '</a>' : $ref_user->name ) );
}
echo '</div></div>
<div class="row"><span>' . $LANG['referrers'] . ':</span> <div>';
if( ( $referrers = \query\main::users( array( 'referrer' => $info->ID ) ) ) > 0 )
  echo ( ab_to( array( 'users' => 'view' ) ) ? '<a href="?route=users.php&amp;action=list&amp;referrer=' . $info->ID . '">' . $referrers . '</a>' : $referrers );
else {
  echo 0;
}
echo '</div></div>
<div class="row"><span>' . $LANG['visits'] . ':</span> <div>' . $info->visits . '</div></div>
<div class="row"><span>' . $LANG['registered_on'] . ':</span> <div>' . $info->date . '</div></div>
<div class="row"><span>' . $LANG['last_visit'] . ':</span> <div>' . $info->last_login . '</div></div>
<div class="row"><span>' . $LANG['last_action'] . ':</span> <div>' . $info->last_action . '</div></div>
<div class="row"><span>' . $LANG['reviews'] . ':</span> <div>' . ( ab_to( array( 'reviews' => 'view' ) ) ? '<a href="?route=reviews.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->reviews . '</a>' : $info->reviews ) . ( ab_to( array( 'reviews' => 'add' ) ) ? ' / <a href="?route=reviews.php&amp;action=add&amp;user=' . $info->ID . '">' . $LANG['reviews_add_button'] . '</a>' : '' ) . '</div></div>
<div class="row"><span>' . $LANG['stores'] . ':</span> <div>' . ( ab_to( array( 'stores' => 'view' ) ) ? '<a href="?route=stores.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->stores . '</a>' : $info->stores ) . ( ab_to( array( 'stores' => 'add' ) ) ? ' / <a href="?route=stores.php&amp;action=add&amp;user=' . $info->ID . '">' . $LANG['stores_add_button'] . '</a>' : '' ) . '</div></div>
<div class="row"><span>' . $LANG['coupons'] . ':</span> <div>' . ( ab_to( array( 'coupons' => 'view' ) ) ? '<a href="?route=coupons.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->coupons . '</a>' : $info->coupons ) . '</div></div>
<div class="row"><span>' . $LANG['products'] . ':</span> <div>' . ( ab_to( array( 'products' => 'view' ) ) ? '<a href="?route=products.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->products . '</a>' : $info->products ) . '</div></div>';

if( $GLOBALS['me']->is_admin ) {
  echo '<div class="row"><span>' . $LANG['form_ip'] . ':</span> <div><a href="?route=users.php&amp;action=list&amp;search=' . $info->IP . '">' . $info->IP . '</a> / <a href="?route=banned.php&amp;action=add&amp;ip=' . $info->IP . '">' . $LANG['bann_ip'] . '</a></div></div>';
}

echo '</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF USER SESSIONS */

case 'sessions':

if( ! $GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . $LANG['sessions_title'] . '</h2>';

if( !empty( $LANG['sessions_subtitle'] ) ) {
  echo '<span>' . $LANG['sessions_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_sessions( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_sessions( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="sessions" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="sessions" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['users_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = admin_query::have_usessions( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=users.php&amp;action=sessions">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=users.php&amp;action=sessions" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>

<div class="bulk_options">
  <button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button>
</div>';

foreach( admin_query::while_usessions( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::user_avatar( $item->avatar ) . '" alt="" />
  <div class="info-div"><h2>' . htmlspecialchars( $item->name ) . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2></div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">
  <a href="?route=users.php&amp;action=edit&amp;id=' . $item->userID . '">' . $LANG['sessions_edit_user'] . '</a>
  <a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>
  </div>
  </li>';

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
  echo '<div class="pagination">';

  if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . $LANG['prev_page'] . '</a>';
  if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . $LANG['next_page'] . '</a>';

  if( $p['pages'] > 1 ) {
  echo '<div class="pag_goto">' . sprintf( $LANG['pageofpages'], $page = $p['page'], $pages = $p['pages'] ) . '
  <form action="#" method="GET">';
  foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
  echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
  <button class="btn">' . $LANG['go'] . '</button>
  </form>
  </div>';
  }

  echo '</div>';
}

} else {

  echo '<div class="a-alert">' . $LANG['no_users_yet'] . '</div>';

}

break;

/** IMPORT SUBSCRIBERS */

case 'importsub':

if( !ab_to( array( 'subscribers' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['subscribers_import_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=subscribers" class="btn">' . $LANG['subscribers_view'] . '</a>
</div>';

if( !empty( $LANG['subscribers_import_subtitle'] ) ) {
  echo '<span>' . $LANG['subscribers_import_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

  if( isset( $_POST['emails'] ) )
  if( actions::import_subscribers( array( 'emails' => $_POST['emails'], 'confirm' => ( isset( $_POST['confirm'] ) ? 1 : 0 ) ) ) ) {

  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="#" method="POST">
<div class="row"><span>' . $LANG['form_emails'] . ':</span><div><textarea name="emails" style="min-height:200px;"></textarea></div></div>
<div class="row"><span>' . $LANG['form_confirm'] . ':</span><div><input type="checkbox" name="confirm" id="confirm" checked /> <label for="confirm">' . $LANG['msg_setallconf'] . '</label></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['button_import'] . '</button>
</form>';

echo '</div>';

break;

/** EXPORT SUBSCRIBERS */

case 'exportsub':

if( !ab_to( array( 'subscribers' => 'export' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['subscribers_export_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=subscribers" class="btn">' . $LANG['subscribers_view'] . '</a>
</div>';

if( !empty( $LANG['subscribers_export_subtitle'] ) ) {
  echo '<span>' . $LANG['subscribers_export_subtitle'] . '</span>';
}

echo '</div>';

$csrf = $_SESSION['subscribers_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="?download=export_subscribers_csv.php" method="POST">
<div class="row"><span>' . $LANG['subscribers_form_exporttype'] . ':</span><div><select name="view"><option value="" selected>' . $LANG['subscribers_option_all'] . '</option><option value="verified">' . $LANG['subscribers_option_verified'] . '</option><option value="notverified">' . $LANG['subscribers_option_unverified'] . '</option></select></div></div>
<div class="row"><span>' . $LANG['form_datefrom'] . ':</span><div><input type="date" name="date[from]" value="' . date( 'Y-m-d', \query\main::get_option( 'siteinstalled' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['from_dateto'] . ':</span><div><input type="date" name="date[to]" value="' . date( 'Y-m-d', strtotime( 'tomorrow' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['subscribers_form_exportfields'] . ':</span><div>
<input type="checkbox" name="fields[name]" id="name" /> <label for="name">' . $LANG['name'] . '</label>
<input type="checkbox" name="fields[email]" id="email" checked disabled /> <label for="email">' . $LANG['email'] . '</label></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['subscribers_export_button'] . '</button>
</form>';

echo '</div>';

break;

/** EDIT SUBSCRIBER */

case 'editsub':

if( !ab_to( array( 'subscribers' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['subscribers_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $subscriber_exists = admin_query::subscriber_exists( $_GET['id'] ) ) ) {

$info = admin_query::subscriber_infos( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>
<li><a href="?route=users.php&amp;action=subscribers&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=users.php&amp;action=subscribers" class="btn">' . $LANG['subscribers_view'] . '</a>
</div>';

if( !empty( $LANG['subscribers_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['subscribers_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $subscriber_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

  if( isset( $_POST['email'] ) )
  if( actions::edit_subscriber( $_GET['id'], array( 'email' => $_POST['email'], 'confirm' => ( isset( $_POST['confirm'] ) ? 1 : 0 ) ) ) ) {

  $info = admin_query::subscriber_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$_SESSION['users_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['form_email'] . ':</span><div><input type="email" name="email" value="' . htmlspecialchars( $info->email ) . '" /></div></div>
<div class="row"><span>' . $LANG['form_confirm'] . ':</span><div><input type="checkbox" name="confirm" id="confirm"' . ( $info->verified ? ' checked' : '' ) . ' /> <label for="confirm">' . $LANG['msg_setconfe'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['subscribers_edit_button'] . '</button>

</form>

</div>

<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['subscribers_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['form_ip'] . ':</span> <div>' . ( !empty( $info->IP ) ? '<a href="?route=users.php&amp;action=subscribers&amp;search=' . $info->IP . '">' . $info->IP . '</a> / <a href="?route=banned.php&amp;action=add&amp;ip=' . $info->IP . '">' . $LANG['bann_ip'] . '</a>' : '' ) .'</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span><div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF SUBSCRIBERS */

case 'subscribers':

if( !ab_to( array( 'subscribers' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['newsletter_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'subscribers' => 'import' ) ) ) echo ' <a href="?route=users.php&amp;action=importsub" class="btn">' . $LANG['subscribers_import'] . '</a>';
if( ab_to( array( 'subscribers' => 'export' ) ) ) echo ' <a href="?route=users.php&amp;action=exportsub" class="btn">' . $LANG['subscribers_export'] . '</a>';
echo '</div>';

if( !empty( $LANG['newsletter_subtitle'] ) ) {
  echo '<span>' . $LANG['newsletter_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_subscriber( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_subscriber( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_subscriber( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'verify' || $_GET['type'] == 'unverify' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_subscriber( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="subscribers" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'email' => $LANG['order_email'], 'email desc' => $LANG['order_email_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">
<option value="">' . $LANG['all_subscribers'] . '</option>';
foreach( array( 'verified' => $LANG['view_verified'], 'notverified' => $LANG['view_notverified'] ) as $kt => $kv )echo '<option value="' . $kt . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $kt ? ' selected' : '') . '>' . $kv . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="subscribers" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . htmlspecialchars( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['subscribers_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = admin_query::have_subscribers( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( isset( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=users.php&amp;action=subscribers">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=users.php&amp;action=subscribers" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'subscribers' => 'edit' ) );
$ab_del  = ab_to( array( 'subscribers' => 'delete' ) );
$ab_sm = ab_to( array( 'mail' => 'send' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

  if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button> ';

  if( $ab_edt ) {
    echo $LANG['action'] . ':
    <select name="action">';
    foreach( array( 'verify' => $LANG['verify'], 'unverify' => $LANG['unverify'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>

    <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>';
  }

echo '</div>';

}

foreach( admin_query::while_subscribers( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']"' . ( $item->is_user ? ' disabled' : '' ) . ' />';

  if( $item->is_user ) {

  echo '<div style="display: table;">

  <img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />

  <div class="info-div"><h2>' . ( $item->verified ? '<span class="msg-success">' . $LANG['verified'] . '</span> ' : '<span class="msg-error">' . $LANG['notverified'] . '</span> ' ) . htmlspecialchars( $item->user_name ) . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>
  ' . htmlspecialchars( $item->email ) . '</div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">
  <a href="?route=users.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['sessions_edit_user'] . '</a>
  </div>';

  } else {

  echo '<div style="display: table;">

  <div style="display: table-cell; content: \' \'; width:10px;"></div>

  <div class="info-div"><h2>' . ( $item->verified ? '<span class="msg-success">' . $LANG['verified'] . '</span> ' : '<span class="msg-error">' . $LANG['notverified'] . '</span> ' ) . htmlspecialchars( $item->email ) . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2></div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">';
  if( $ab_edt ) {
  echo '<a href="?route=users.php&amp;action=editsub&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->verified ? 'unverify' : 'verify' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->verified ? $LANG['unverify'] : $LANG['verify'] ) . '</a>';
  }
  if( $ab_sm ) echo '<a href="' . \site\utils::update_uri( '', array( 'action' => 'sendmail', 'email' => $item->email ) ) . '">' . $LANG['send_email'] . '</a>';
  if( $ab_del ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
  echo '</div>';

  }

  echo '</li>';

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
  echo '<div class="pagination">';

  if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . $LANG['prev_page'] . '</a>';
  if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . $LANG['next_page'] . '</a>';

  if( $p['pages'] > 1 ) {
  echo '<div class="pag_goto">' . sprintf( $LANG['pageofpages'], $page = $p['page'], $pages = $p['pages'] ) . '
  <form action="#" method="GET">';
  foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
  echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
  <button class="btn">' . $LANG['go'] . '</button>
  </form>
  </div>';
  }

  echo '</div>';
}

} else {

  echo '<div class="a-alert">' . $LANG['no_subsribers_yet'] . '</div>';

}

break;

/** LIST OF USERS */

default:

if( !ab_to( array( 'users' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['users_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'users' => 'add' ) ) ) echo '<a href="?route=users.php&amp;action=add" class="btn">' . $LANG['users_add'] . '</a>';
echo '</div>';

if( !empty( $LANG['users_subtitle'] ) ) {
  echo '<span>' . $LANG['users_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_user( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_user( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_user( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'verify' || $_GET['type'] == 'unverify' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_user( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'action' => $LANG['order_action'], 'action desc' => $LANG['order_action_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'visits' => $LANG['order_visits'], 'visits desc' => $LANG['order_visits_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">
<option value="">' . $LANG['all_users'] . '</option>';
foreach( array( 'subadmins' => $LANG['view_subadmins'], 'admins' => $LANG['view_admins'], 'verified' => $LANG['view_verified'], 'notverified' => $LANG['view_notverified'], 'banned' => $LANG['view_banned'] ) as $kt => $kv )echo '<option value="' . $kt . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $kt ? ' selected' : '') . '>' . $kv . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . htmlspecialchars( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['users_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = \query\main::have_users( $options = array( 'per_page' => 10, 'referrer' => (isset( $_GET['referrer'] ) ? $_GET['referrer'] : ''), 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( isset( $_GET['referrer'] ) || isset( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=users.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=users.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'users' => 'edit' ) );
$ab_del  = ab_to( array( 'users' => 'delete' ) );
$ab_sm = ab_to( array( 'mail' => 'send' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

  if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button> ';

  if( $ab_edt ) {
    echo $LANG['action'] . ':
    <select name="action">';
    foreach( array( 'verify' => $LANG['verify'], 'unverify' => $LANG['unverify'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>';
  }

echo '</div>';

}

foreach( \query\main::while_users( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::user_avatar( $item->avatar ) . '" alt="" />
  <div class="info-div"><h2>' . ( $item->is_confirmed ? '<span class="msg-success">' . $LANG['verified'] . '</span> ' : '<span class="msg-error">' . $LANG['notverified'] . '</span> ' ) . ( $item->is_banned ? '<span class="msg-alert" title="' . sprintf( $LANG['msg_banned_until'], $item->ban ) . '">' . $LANG['banned'] . '</span> ' : '' ) . htmlspecialchars( $item->name ) . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2></div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">';
  if( $ab_edt ) {
  echo '<a href="?route=users.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->is_confirmed ? 'unverify' : 'verify' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->is_confirmed ? $LANG['unverify'] : $LANG['verify'] ) . '</a>';
  }
  if( $ab_sm ) echo '<a href="' . \site\utils::update_uri( '', array( 'action' => 'sendmail', 'email' => $item->email ) ) . '">' . $LANG['send_email'] . '</a>';
  if( $ab_del ) echo '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>
  </div>
  </li>';

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
  echo '<div class="pagination">';

  if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . $LANG['prev_page'] . '</a>';
  if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . $LANG['next_page'] . '</a>';

  if( $p['pages'] > 1 ) {
  echo '<div class="pag_goto">' . sprintf( $LANG['pageofpages'], $page = $p['page'], $pages = $p['pages'] ) . '
  <form action="#" method="GET">';
  foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
  echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
  <button class="btn">' . $LANG['go'] . '</button>
  </form>
  </div>';
  }

  echo '</div>';
}

} else {

  echo '<div class="a-alert">' . $LANG['no_users_yet'] . '</div>';

}

break;

}