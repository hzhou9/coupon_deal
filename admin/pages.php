<?php

switch( $_GET['action'] ) {

/** ADD PAGE */

case 'add':

if( !ab_to( array( 'pages' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['pages_add_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=pages.php&amp;action=list" class="btn">' . $LANG['pages_view'] . '</a>
</div>';

if( !empty( $LANG['pages_add_subtitle'] ) ) {
  echo '<span>' . $LANG['pages_add_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

  if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) )
  if( actions::add_page( array( 'name' => $_POST['name'], 'text' => $_POST['text'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ), 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['pages_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="" required /></div></div>
<div class="row"><span>' . $LANG['form_text'] . ' (HTML):</span><div><textarea name="text" style="min-height:400px;"></textarea></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish">' . $LANG['msg_pubpage'] . '</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc"></textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['pages_add_button'] . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

break;

/** EDIT STORE */

case 'edit':

if( !ab_to( array( 'pages' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['pages_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $page_exists = \query\main::page_exists( $_GET['id'] ) ) ) {

$info = \query\main::page_infos( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( ab_to( array( 'pages' => 'delete' ) ) ) echo '<li><a href="?route=pages.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
if( $info->visible ) {
  echo '<li><a href="?route=pages.php&amp;action=plans&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['unpublish'] . '</a></li>';
} else {
  echo '<li><a href="?route=pages.php&amp;action=plans&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['publish'] . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=pages.php&amp;action=list" class="btn">' . $LANG['pages_view'] . '</a>
</div>';

if( !empty( $LANG['pages_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['pages_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $page_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

  if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) )
  if( actions::edit_page( $_GET['id'], array( 'name' => $_POST['name'], 'text' => $_POST['text'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ), 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) ) {

  $info = \query\main::page_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$_SESSION['pages_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $info->name . '" required /></div></div>
<div class="row"><span>' . $LANG['form_text'] . ' (HTML):</span><div><textarea name="text" style="min-height:400px;">' . htmlspecialchars( $info->text ) . '</textarea></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish">' . $LANG['msg_pubpage'] . '</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="' . $info->meta_title . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc">' . $info->meta_description . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['pages_edit_button'] . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['pages_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['views'] . ':</span> <div>' . $info->views . '</div></div>
<div class="row"><span>' . $LANG['page_url'] . ':</span> <div><a href="' . $info->link . '" target="_blank">' . $info->link . '</a></div></div>
<div class="row"><span>' . $LANG['last_update_by'] . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_on'] . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . $LANG['added_by'] . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF PAGES */

default:

if( !ab_to( array( 'pages' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['pages_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'pages' => 'add' ) ) ) echo '<a href="?route=pages.php&amp;action=add" class="btn">' . $LANG['pages_add'] . '</a>';
echo '</div>';

if( !empty( $LANG['pages_subtitle'] ) ) {
  echo '<span>' . $LANG['pages_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_page( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_page( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'pages_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_page( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_page( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['pages_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="pages.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'update' => $LANG['order_last_update'], 'update desc' => $LANG['order_last_update_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'name' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="pages.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['pages_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = \query\main::have_pages( $options = array( 'per_page' => 10, 'show' => 'all', 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=pages.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=pages.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'pages' => 'edit' ) );
$ab_del  = ab_to( array( 'pages' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

  if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button> ';

  if( $ab_edt ) {
    echo $LANG['action'] . ':
    <select name="action">';
    foreach( array( 'publish' => $LANG['publish'], 'unpublish' => $LANG['unpublish'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>';
  }

echo '</div>';

}

foreach( \query\main::while_pages( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li><input type="checkbox" name="id[' . $item->ID . ']" />
  <div class="info-div"><h2>' . ( $item->visible ? '<span class="msg-success">' . $LANG['published'] . '</span> ' : '<span class="msg-error">' . $LANG['notpublished'] . '</span> ' ) . $item->name . '</h2></div>
  <div class="options">';
  if( $ab_edt ) {
  echo '<a href="?route=pages.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? $LANG['publish'] : $LANG['unpublish'] ) . '</a>';
  }
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

  echo '<div class="a-alert">' . $LANG['no_pages_yet'] . '</div>';

}

break;

}