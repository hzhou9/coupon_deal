<?php

switch( $_GET['action'] ) {

/** ADD REVIEW */

case 'add':

if( !ab_to( array( 'reviews' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['reviews_add_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=reviews.php&amp;action=list" class="btn">' . $LANG['reviews_view'] . '</a>
</div>';

if( !empty( $LANG['reviews_add_subtitle'] ) ) {
  echo '<span>' . $LANG['reviews_add_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'reviews_csrf' ) ) {

  if( isset( $_POST['store'] ) && isset( $_POST['user'] ) && isset( $_POST['stars'] ) && isset( $_POST['text'] ) )
  if( actions::add_review( array( 'user' => $_POST['user'], 'store' => $_POST['store'], 'text' => $_POST['text'], 'stars' => $_POST['stars'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ) ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['reviews_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . $LANG['form_store_id'] . ':</span><div data-search="store"><input type="text" name="store" value="' . ( isset( $_POST['store'] ) ? (int) $_POST['store'] : ( !empty( $_GET['store'] ) ? (int) $_GET['store'] : '' ) ) . '" required /><a href="#">S</a></div></div>
<div class="row"><span>' . $LANG['form_user_id'] . ':</span><div data-search="user"><input type="text" name="user" value="' . ( isset( $_POST['user'] ) ? (int) $_POST['user'] : ( !empty( $_GET['user'] ) ? (int) $_GET['user'] : $GLOBALS['me']->ID ) ) . '" required /><a href="#">S</a></div></div>

<div class="row"><span>' . $LANG['form_stars'] . ':</span>
<div>
<select name="stars">';
foreach( array( 1, 2, 3, 4, 5 ) as $note )echo '<option value="' . $note . '"' . ( $note == 5 ? ' selected' : '' ) . '>' . $note . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['form_text'] . ':</span><div><textarea name="text" style="min-height:200px;"></textarea></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish">' . $LANG['msg_pubreview'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['reviews_add_button'] . '</button>

</form>

</div>';

break;

/** EDIT REVIEW */

case 'edit':

if( !ab_to( array( 'reviews' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['reviews_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $review_exists = \query\main::review_exists( $_GET['id'] ) ) ) {

$info = \query\main::review_infos( $_GET['id'] );

$ab_edt  = ab_to( array( 'pages' => 'edit' ) );
$ab_del = ab_to( array( 'pages' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=reviews.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
if( $info->valid ) {
  if( $ab_edt )echo '<li><a href="?route=reviews.php&amp;action=list&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['unpublish'] . '</a></li>';
} else {
  if( $ab_edt )echo '<li><a href="?route=reviews.php&amp;action=list&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['publish'] . '</a></li>';
}
echo '</ul>
</div>';

}

}

echo '<a href="?route=reviews.php&amp;action=list" class="btn">' . $LANG['reviews_view'] . '</a>
</div>';

if( !empty( $LANG['reviews_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['reviews_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $review_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'reviews_csrf' ) ) {

  if( isset( $_POST['store'] ) && isset( $_POST['user'] ) && isset( $_POST['stars'] ) && isset( $_POST['text'] ) )
  if( actions::edit_review( $_GET['id'], array( 'user' => $_POST['user'], 'store' => $_POST['store'], 'text' => $_POST['text'], 'stars' => $_POST['stars'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ) ) ) ) {

  $info = \query\main::review_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$_SESSION['reviews_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['form_store_id'] . ':</span><div data-search="store"><input type="text" name="store" value="' . $info->storeID . '" required /><a href="#" data-search="store">S</a></div></div>
<div class="row"><span>' . $LANG['form_user_id'] . ':</span><div data-search="user"><input type="text" name="user" value="' . $info->user . '" required /><a href="#">S</a></div></div>

<div class="row"><span>' . $LANG['form_stars'] . ':</span>
<div>
<select name="stars">';
foreach( array( 1, 2, 3, 4, 5 ) as $note )echo '<option value="' . $note . '"' . ( $note == $info->stars ? ' selected' : '' ) . '>' . $note . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['form_text'] . ':</span><div><textarea name="text" style="min-height:200px;">' . $info->text . '</textarea></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->valid ? ' checked' : '' ) . ' /> <label for="publish">' . $LANG['msg_pubreview'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['reviews_edit_button'] . '</button>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['reviews_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['store_name'] . ':</span> <div>' . ( empty( $info->store_name ) ? '-' : ( ab_to( array( 'stores' => 'edit' ) ) ? '<a href="?route=stores.php&amp;action=edit&amp;id=' . $info->storeID . '">' . $info->store_name . '</a>' : $info->store_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_by'] . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_on'] . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . $LANG['added_by'] . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '.</div>';

}

break;

/** LIST OF REVIEWS */

default:

if( !ab_to( array( 'reviews' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['reviews_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'reviews' => 'add' ) ) ) echo '<a href="?route=reviews.php&amp;action=add" class="btn">' . $LANG['reviews_add'] . '</a>';
echo '</div>';

if( !empty( $LANG['reviews_subtitle'] ) ) {
  echo '<span>' . $LANG['reviews_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'reviews_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_review( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_review( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'reviews_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_review( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_review( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['reviews_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="reviews.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">';
foreach( array( 'all' => $LANG['all_reviews'], '' => $LANG['view_published'], 'notvalid' => $LANG['view_notpublished'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="reviews.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . htmlspecialchars( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['reviews_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = \query\main::have_reviews( $options = array( 'per_page' => 10, 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'user' => (isset( $_GET['user'] ) ? $_GET['user'] : ''), 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : 'all'), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['store'] ) || !empty( $_GET['user'] ) || isset( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=reviews.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=reviews.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'reviews' => 'edit' ) );
$ab_del  = ab_to( array( 'reviews' => 'delete' ) );

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

foreach( \query\main::while_reviews( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />

  <div class="info-div">

  <h2>' . ( $item->valid ? '<span class="msg-success">' . $LANG['published'] . '</span> ' : '<span class="msg-error">' . $LANG['notpublished'] . '</span> ' ) . sprintf( $LANG['reviews_byto'], '<a href="?route=reviews.php&amp;action=list&amp;user=' . $item->user . '">' . $item->user_name . '</a>', '<a href="?route=reviews.php&amp;action=list&amp;store=' . $item->storeID . '">' . ( \query\main::store_infos( $item->storeID )->name ) . '</a>' ) . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>

  <div class="info-bar">' . $item->text . '</div>

  </div></div>

  <div class="options">';
  if( $ab_edt ) {
  echo '<a href="?route=reviews.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->valid ? 'unpublish' : 'publish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->valid ? $LANG['unpublish'] : $LANG['publish'] ). '</a>';
  }
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

  echo '<div class="a-alert">' . $LANG['no_reviews_yet'] . '</div>';

}

break;

}