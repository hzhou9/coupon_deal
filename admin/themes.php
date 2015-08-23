<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** UPLOAD THEME */

case 'upload':

echo '<div class="title">

<h2>' . $LANG['themes_upload_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=themes.php&amp;action=list" class="btn">' . $LANG['themes_view'] . '</a>
</div>';

if( !empty( $LANG['themes_upload_subtitle'] ) ) {
  echo '<span>' . $LANG['themes_upload_subtitle'] . '</span>';
}

echo '</div>';

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

  if( isset( $_FILES['file'] ) ) {

  try {
    actions::extract_theme( $_FILES['file']['name'], $_FILES['file']['tmp_name'] );
    echo '<div class="a-success">' . $LANG['themes_installed'] . '</div>';
  }

  catch( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
  }

  } else if( isset( $_POST['URL'] ) ) {

  try {
    actions::extract_theme( $_POST['URL'] );
    echo '<div class="a-success">' . $LANG['themes_installed'] . '</div>';
  }

  catch( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
  }

  }

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

/* */

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="a-alert">' . $LANG['themes_upload_msg'] . '</div>';

echo '<div class="form-table">';

echo '<form action="#" method="POST" enctype="multipart/form-data">
<div class="row"><span>' . $LANG['themes_select_theme'] . ':</span><div><input type="file" name="file" value="" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['themes_upload_button'] . '</button>
</form>

<div style="margin: 10px 0; text-align: center;">
  <h2>' . $LANG['themes_orviaurl'] . '</h2>
</div>

<form action="#" method="POST">
<div class="row"><span>' . $LANG['themes_url_theme'] . ':</span><div><input type="text" name="URL" value="" placeholder="' . $LANG['themes_urlph'] . '" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['themes_install_button'] . '</button>
</form>';

echo '</div></div>';

echo '<div id="process-theme">
  <h2>' . $LANG['themes_upload_dleave'] . '</h2>
</div>';

break;

/** THEME EDITOR */

case 'editor':

echo '<div class="title">

<h2>' . $LANG['themes_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=themes.php&amp;action=common' . ( isset( $_GET['id'] ) ? '&id=' . $_GET['id'] : '' ) . '" class="btn">' . $LANG['themes_common'] . '</a>
<a href="?route=themes.php&amp;action=list" class="btn">' . $LANG['themes_view'] . '</a>
</div>';

if( !empty( $LANG['themes_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['themes_edit_subtitle'] . '</span>';
}

echo '</div>';

if( isset( $_GET['id'] ) && is_dir( DIR . '/' . THEMES_LOC . '/' . str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['id'] ) ) ) {

if( empty( $_GET['page'] ) )
  $page = DIRECTORY_SEPARATOR . 'index.php';

else {

if( file_exists( DIR . '/' . THEMES_LOC . '/' . $_GET['id'] . '/' . str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['page'] ) ) ) {
  $page = $_GET['page'];
} else {
  $page = DIRECTORY_SEPARATOR . 'index.php';
}

}

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

  if( isset( $_POST['text'] ) )
  if( actions::edit_theme_page( $_GET['id'], array( 'page' => $page, 'text' => $_POST['text'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

' . sprintf( $LANG['theme_edit_title'], htmlspecialchars( $_GET['id'] ) ) . '

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="themes.php" />
<input type="hidden" name="action" value="editor" />
<input type="hidden" name="id" value="' . htmlspecialchars( $_GET['id'] ) . '" />
<select name="page">';
foreach( template::theme_editor_map( $_GET['id'] ) as $p )echo '<option value="' . $p . '"' . ( $p == $page ? ' selected' : '' ) . '>' . $p . '</option>';
echo '</select>
<button class="btn">' . $LANG['view'] . '</button>
</form>

</div>';

echo '<div class="form-table">

<form action="#" method="POST">

<textarea name="text" style="min-height: 450px; width: 100%; box-sizing: border-box; margin-bottom: 10px;">' . htmlspecialchars( file_get_contents( DIR . '/' . THEMES_LOC . '/' . $_GET['id'] . '/' . $page ) ) . '</textarea>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['save'] . '</button>

</form>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** COMMPON PARTS EDITOR */

case 'common':

echo '<div class="title">

<h2>' . $LANG['themes_common_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
' . ( isset( $_GET['id'] ) ? '<a href="?route=themes.php&amp;action=editor&id=' . $_GET['id'] . '" class="btn">' . $LANG['themes_editor'] . '</a>' : '' ) . '
<a href="?route=themes.php&amp;action=list" class="btn">' . $LANG['themes_view'] . '</a>
</div>';

if( !empty( $LANG['themes_common_subtitle'] ) ) {
  echo '<span>' . $LANG['themes_common_subtitle'] . '</span>';
}

echo '</div>';

if( empty( $_GET['page'] ) )
  $page = 'head.html';

else {

if( file_exists( DIR . '/' . COMMON_LOCATION . '/' . str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['page'] ) ) ) {
  $page = $_GET['page'];
} else {
  $page = 'head.html';
}

}

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

  if( isset( $_POST['text'] ) )
  if( @file_put_contents( DIR . '/' . COMMON_LOCATION . '/' . $page, trim( $_POST['text'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="themes.php" />
<input type="hidden" name="action" value="common" />
' . ( isset( $_GET['id'] ) ? '<input type="hidden" name="id" value="' . $_GET['id'] . '" />' : '' ) . '
<select name="page">';
foreach( array( 'add_extra_head()' => 'head.html' ) as $k => $v )echo '<option value="' . $v . '"' . ( $v == $page ? ' selected' : '' ) . '>' . $k . '</option>';
echo '</select>
<button class="btn">' . $LANG['view'] . '</button>
</form>

</div>';

echo '<div class="form-table">

<form action="#" method="POST">

<textarea name="text" style="min-height: 450px; width: 100%; box-sizing: border-box; margin-bottom: 10px;">' . htmlspecialchars( file_get_contents( DIR . '/' . COMMON_LOCATION . '/' . $page ) ) . '</textarea>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['save'] . '</button>

</form>

</div>';

break;

/** LIST OF THEMES */

default:

echo '<div class="title">

<h2>' . $LANG['themes_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=themes.php&amp;action=upload" class="btn">' . $LANG['themes_upload'] . '</a>
</div>';

if( !empty( $LANG['themes_subtitle'] ) ) {
  echo '<span>' . $LANG['themes_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_theme( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'themes_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_theme( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['action'] == 'activate' ) {

  if( isset( $_GET['id'] ) )
  if( !template::theme_have_min( template::theme_editor_map( $_GET['id'] ) ) )
  echo '<div class="a-error">' . $LANG['msg_invalid_theme'] . '</div>';
  else if( actions::set_option( array( 'theme' => $_GET['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

$themes = template::read_dirs();
$current = \query\main::get_option( 'theme' );

if( count( $themes > 0 ) ) {

echo '<div class="form-table">

<form action="?route=themes.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>

<div class="bulk_options">
  <button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button>
</div>';

$per_page = 5;
$page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
$pages = ceil( count( $themes['dirs'] ) / $per_page );

if( ( $page * $per_page ) > $pages ) {
  $page = $pages;
}

$start = ( $page - 1 ) * $per_page;

foreach( array_slice( $themes['dirs'], $start, $per_page ) as $item ) {

  echo '<li><input type="checkbox" name="id[' . $item . ']"' . ( $current == $item ? ' disabled' : '' ) . ' />

  <div style="display: table;">';

  echo '<img src="' . \query\main::theme_avatar( str_replace( DIR . '/', '', current(glob( DIR . '/' . THEMES_LOC . '/' . $item . '/preview.[jpg][pni][gf]' )) ) ) . '" alt="" style="width: 85px; height: 85px;" />';

  echo '<div class="info-div"><h2>' . ( $current == $item ? '<span class="msg-success">' . $LANG['themes_used'] . '</span> ' : '' ) . $item . '</h2>

  <div class="info-bar">';

  if( $info = template::read_theme_info_file( $item ) ) {

  $infos = array();

  if( isset( $info['version'] ) ) {
    $infos[] = $LANG['themes_version'] . ': <b>' . htmlspecialchars( $info['version'] ) . '</b>';
  }

  if( isset( $info['published_by'] ) ) {
    $infos[] = $LANG['themes_published_by'] . ': <b>' . htmlspecialchars( $info['published_by'] ) . '</b>';
  }

  if( isset( $info['publisher_url'] ) ) {
    $infos[] = $LANG['themes_publisher_url'] . ': <a href="' . htmlspecialchars( $info['publisher_url'] ) . '" target="_blank">' . htmlspecialchars( $info['publisher_url'] ) . '</a>';
  }

  if( isset( $info['description'] ) ) {
    $infos[] = '<a href="#" class="show_theme_desc"><span>&#8601;</span> ' . $LANG['description'] . '</a>';
  }

  if( empty( $infos ) )echo $LANG['themes_no_infos'];
  else

  echo implode( ', ', $infos );

  if( isset( $info['description'] ) ) {
    echo '<div class="theme-desc">' . htmlspecialchars( $info['description'] ) . '</div>';
  }

  } else {

    echo $LANG['themes_no_infos'];

  }

  echo '</div></div></div>

  <div style="clear:both"></div>

  <div class="options">';

  if( $current == $item )
  echo '<a href="?route=themes.php&amp;action=editor&amp;id=' . $item . '">' . $LANG['editor'] . '</a>';
  else {
  echo '<a href="?route=themes.php&amp;action=activate&amp;id=' . $item . '&amp;token=' . $csrf . '">' . $LANG['activate'] . '</a>
  <a href="?route=themes.php&amp;action=editor&amp;id=' . $item . '">' . $LANG['editor'] . '</a>
  <a href="?route=themes.php&amp;action=delete&amp;id=' . $item . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
  }

  echo '</div>
  </li>';

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>

</div>';

if( $pages > 1 ) {
echo '<div class="pagination">';
if( $page > 1 )echo '<a href="?route=themes.php&amp;action=list&amp;page=' . ($page-1) . '" class="btn">? Prev</a>';
if( $pages > $page )echo '<a href="?route=themes.php&amp;action=list&amp;page=' . ($page+1) . '" class="btn">Next ?</a>';
echo '</div>';
}

} else {

  echo '<div class="a-alert">' . $LANG['no_themes_yet'] . '</div>';

}

break;

}