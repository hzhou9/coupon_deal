<?php

switch( $_GET['action'] ) {

/** VIEW SUGGESTION */

case 'view':

if( !ab_to( array( 'suggestions' => 'view' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['suggestions_view_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $sugestion_exists = admin_query::suggestion_exists( $_GET['id'] ) ) ) {

$ab_edt  = ab_to( array( 'suggestions' => 'edit' ) );
$ab_del = ab_to( array( 'suggestions' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=suggestions.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
if( $ab_edt ) echo '<li><a href="?route=suggestions.php&amp;action=list&amp;type=unread&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['set_as_unread'] . '</a></li>';
echo '</ul>
</div>';

}

}

echo '<a href="?route=suggestions.php&amp;action=list" class="btn">' . $LANG['suggestions_view'] . '</a>
</div>';

if( !empty( $LANG['suggestions_view_subtitle'] ) ) {
  echo '<span>' . $LANG['suggestions_view_subtitle'] . '</span>';
}

echo '</div>';

if( $sugestion_exists ) {

// Set automaticaly read this suggestion
actions::action_suggestions( 'read', $_GET['id'] );

$_SESSION['suggestions_csrf'] = $csrf;

$info = admin_query::suggestion_infos( $_GET['id'] );

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div>' . $info->name . '</div></div>
<div class="row"><span>' . $LANG['form_store_url'] . ':</span><div><a href="' . $info->url . '">' . $info->url . '</a></div></div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div>' . $info->description . '</div></div>
<div class="row"><span>' . $LANG['form_message_for_us'] . ':</span><div>' . $info->message . '</div></div>';

if( $info->user == 0 ) {

  $addby = '-';

} else {

  $info_user = \query\main::user_infos( $info->user );

  $addby = empty( $info_user ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info_user->ID . '">' . $info_user->name . '</a>' : $info_user->name );

}

echo '<div class="row"><span>' . $LANG['added_by'] . ':</span><div>' . $addby . '</div></div>

<div class="row"><span>' . $LANG['added_on'] . ':</span><div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF SUGGESTIONS */

default:

if( !ab_to( array( 'suggestions' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['suggestions_title'] . '</h2>';

if( !empty( $LANG['suggestions_subtitle'] ) ) {
  echo '<span>' . $LANG['suggestions_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'suggestions_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_suggestion( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_suggestions( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'suggestions_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_suggestion( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'read' || $_GET['type'] == 'unread' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_suggestions( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['suggestions_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="suggestions.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">';
foreach( array( '' => $LANG['all_suggestions'], 'read' => $LANG['view_read'], 'notread' => $LANG['view_unread'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="suggestions.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . htmlspecialchars( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['suggestions_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = admin_query::have_suggestions( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=suggestions.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=suggestions.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'suggestions' => 'edit' ) );
$ab_del  = ab_to( array( 'suggestions' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button> ';

  if( $ab_edt ) {
    echo $LANG['action'] . ':
    <select name="action">';
    foreach( array( 'read' => $LANG['read'], 'unread' => $LANG['unread'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>';
  }

echo '</div>';

}

foreach( admin_query::while_suggestions( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <div style="display: table-cell; content: \' \'; width:10px;"></div>

  <div class="info-div">

  <h2>' . ( $item->read ? '<span class="msg-error">' . $LANG['read'] . '</span> ' : '<span class="msg-success">' . $LANG['unread'] . '</span> ' ) . $item->name . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>

  <div class="info-bar">' . template::suggestion_intent( $item->type ) . '</div>

  </div></div>

  <div class="options">
  <a href="?route=suggestions.php&amp;action=view&amp;id=' . $item->ID . '">' . $LANG['view'] . '</a>';
  if( $ab_edt ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->read ? 'unread' : 'read' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->read ? $LANG['set_as_unread'] : $LANG['set_as_read'] ) . '</a>';
  if( $ab_del ) echo '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
  echo '</div>

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

  echo '<div class="a-alert">' . $LANG['no_suggestions_yet'] . '</div>';

}

break;

}