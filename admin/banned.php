<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** ADD ENTRY */

case 'add':

echo '<div class="title">

<h2>' . $LANG['banned_add_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=banned.php&amp;action=list" class="btn">' . $LANG['banned_view'] . '</a>
</div>';

if( !empty( $LANG['banned_add_subtitle'] ) ) {
  echo '<span>' . $LANG['banned_add_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'banned_csrf' ) ) {

  if( isset( $_POST['ip'] ) && valid_ip( $_POST['ip'] ) )
  if( actions::add_banned( array( 'ipaddr' => $_POST['ip'], 'registration' => ( isset( $_POST['register'] ) ? 1 : 0 ), 'login' => ( isset( $_POST['login'] ) ? 1 : 0 ), 'site' => ( isset( $_POST['shn-site'] ) ? 1 : 0 ), 'redirect' => ( isset( $_POST['redirect'] ) ? $_POST['redirect'] : '' ), 'expiration' => ( !isset( $_POST['shn-expiration'] ) ? 1 : 0 ), 'expiration_date' => ( !isset( $_POST['shn-expiration'] ) && isset( $_POST['expiration'] ) ? $_POST['expiration']['date'] . ', ' . $_POST['expiration']['hour'] : '' ) ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['banned_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . $LANG['form_ip'] . ':</span><div><input type="text" name="ip" value="' . ( $_SERVER['REQUEST_METHOD'] == 'GET' && !empty( $_GET['ip'] ) ? htmlspecialchars( $_GET['ip'] ) : '' ) . '" required /></div></div>
<div class="row"><span>' . $LANG['bann_form_block'] . ':</span><div>
<input type="checkbox" name="register" id="register" checked /> <label for="register">' . $LANG['bann_registrations'] . '</label> <br />
<input type="checkbox" name="login" id="login" checked /> <label for="login">' . $LANG['bann_login'] . '</label> <br />
<input type="checkbox" name="shn-site" id="site" /> <label for="site">' . $LANG['bann_site'] . '</label>
</div></div>
<div class="row" style="display: none;"><span>' . $LANG['bann_form_redirect'] . ':</span><div><input type="text" name="redirect" value="http://" /></div></div>
<div class="row"><span>' . $LANG['bann_form_expiration'] . ':</span><div>
<input type="checkbox" name="shn-expiration" checked id="expiration" /> <label for="expiration">' . $LANG['bann_neverexp'] . '</label>
</div></div>
<div class="row" style="display: none;"><span>' . $LANG['form_expiration_date'] . ':</span><div><input type="date" name="expiration[date]" value="' . date( 'Y-m-d', strtotime( '+1 week' ) ) . '" style="width: 80%" /><input type="time" name="expiration[hour]" value="00:00" style="width: 20%" /></div>
</div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['banned_add_button'] . '</button>

</form>

</div>';

break;

/** EDIT ENTRY */

case 'edit':

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['banned_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $banned_exists = admin_query::banned_exists( $_GET['id'] ) ) ) {

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>
<li><a href="?route=banned.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=banned.php&amp;action=list" class="btn">' . $LANG['banned_view'] . '</a>
</div>';

if( !empty( $LANG['banned_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['banned_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $banned_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'banned_csrf' ) ) {

  if( isset( $_POST['ip'] ) && valid_ip( $_POST['ip'] ) )
  if( actions::edit_banned( $_GET['id'], array( 'ipaddr' => $_POST['ip'], 'registration' => ( isset( $_POST['register'] ) ? 1 : 0 ), 'login' => ( isset( $_POST['login'] ) ? 1 : 0 ), 'site' => ( isset( $_POST['shn-site'] ) ? 1 : 0 ), 'redirect' => ( isset( $_POST['redirect'] ) ? $_POST['redirect'] : '' ), 'expiration' => ( !isset( $_POST['shn-expiration'] ) ? 1 : 0 ), 'expiration_date' => ( !isset( $_POST['shn-expiration'] ) && isset( $_POST['expiration'] ) ? $_POST['expiration']['date'] . ', ' . $_POST['expiration']['hour'] : '' ) ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$_SESSION['banned_csrf'] = $csrf;

$info = admin_query::banned_infos( $_GET['id'] );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_ip'] . ':</span><div><input type="text" name="ip" value="' . $info->IP . '" required /></div></div>
<div class="row"><span>' . $LANG['bann_form_block'] . ':</span><div>
<input type="checkbox" name="register" id="register"' . ( $info->registration ? ' checked' : '' ) . ' /> <label for="register">' . $LANG['bann_registrations'] . '</label> <br />
<input type="checkbox" name="login" id="login"' . ( $info->login ? ' checked' : '' ) . ' /> <label for="login">' . $LANG['bann_login'] . '</label> <br />
<input type="checkbox" name="shn-site" id="site"' . ( $info->site ? ' checked' : '' ) . ' /> <label for="site">' . $LANG['bann_site'] . '</label>
</div></div>
<div class="row"' . ( !$info->site ? ' style="display: none;"' : '' ) . '><span>' . $LANG['bann_form_redirect'] . ':</span><div><input type="text" name="redirect" value="' . $info->redirect_to . '" /></div></div>
<div class="row" style="display: none;"><span>' . $LANG['bann_form_redirect'] . ':</span><div><input type="text" name="redirect" value="http://" /></div></div>
<div class="row"><span>' . $LANG['bann_form_expiration'] . ':</span><div>
<input type="checkbox" name="shn-expiration" id="expiration" ' . ( !$info->expiration ? ' checked' : '' ) . ' /> <label for="expiration">' . $LANG['bann_neverexp'] . '</label>
</div></div>
<div class="row" ' . ( !$info->expiration ? ' style="display: none;"' : '' ) . '><span>' . $LANG['form_expiration_date'] . ':</span><div><input type="date" name="expiration[date]" value="' .  date( 'Y-m-d', ( $info->expiration ? strtotime( $info->expiration_date ) : strtotime( '+1 week' ) ) ) . '" style="width: 80%" /><input type="time" name="expiration[hour]" value="' . date( 'H:i', strtotime( $info->expiration_date ) ) . '" style="width: 20%" /></div>
</div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['banned_edit_button'] . '</button>

</form>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF BANNED IP's */

default:

echo '<div class="title">

<h2>' . $LANG['banned_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=banned.php&amp;action=add" class="btn">' . $LANG['banned_add'] . '</a>
</div>';

if( !empty( $LANG['banned_subtitle'] ) ) {
  echo '<span>' . $LANG['banned_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'banned_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_banned( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'banned_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_banned( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['banned_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="banned.php" />
<input type="hidden" name="action" value="list" />

Order by:
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="banned.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['bann_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = admin_query::have_banned( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=banned.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=banned.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>

<div class="bulk_options">
<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button>

</div>';

foreach( admin_query::while_banned( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li><input type="checkbox" name="id[' . $item->ID . ']" />
  <div class="info-div"><h2>' . $item->IP . '</h2></div>
  <div class="options">
  <a href="?route=banned.php&action=edit&id=' . $item->ID . '">' . $LANG['edit'] . '</a>
  <a href="?route=banned.php&action=delete&id=' . $item->ID . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>
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

  echo '<div class="a-alert">' . $LANG['no_banned_yet'] . '</div>';

}

break;

}