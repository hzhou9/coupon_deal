<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** INSTALL PLUGIN */

case 'install':

echo '<div class="title">

<h2>' . $LANG['plugins_istl_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=plugins.php&amp;action=list" class="btn">' . $LANG['plugins_view'] . '</a>
</div>';

if( !empty( $LANG['plugins_istl_subtitle'] ) ) {
  echo '<span>' . $LANG['plugins_istl_subtitle'] . '</span>';
}

echo '</div>';

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

  if( isset( $_FILES['file'] ) ) {

  try {
    actions::extract_plugin( $_FILES['file']['name'], $_FILES['file']['tmp_name'] );
    echo '<div class="a-success">' . $LANG['plugins_installed'] . '</div>';
  }

  catch( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
  }

  } else if( isset( $_POST['URL'] ) ) {

  try {
    actions::extract_plugin( $_POST['URL'] );
    echo '<div class="a-success">' . $LANG['plugins_installed'] . '</div>';
  }

  catch( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
  }

  }

}

$csrf = $_SESSION['plugins_csrf'] = \site\utils::str_random(10);

/* */

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="a-alert">' . $LANG['plugins_install_msg'] . '</div>';

echo '<div class="form-table">';

echo '<form action="#" method="POST" enctype="multipart/form-data">
<div class="row"><span>' . $LANG['plugins_select_plugin'] . ':</span><div><input type="file" name="file" value="" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['plugins_upload_button'] . '</button>
</form>

<div style="margin: 10px 0; text-align: center;">
  <h2>' . $LANG['plugins_orviaurl'] . '</h2>
</div>

<form action="#" method="POST">
<div class="row"><span>' . $LANG['plugins_url_plugin'] . ':</span><div><input type="text" name="URL" value="" placeholder="' . $LANG['plugins_urlph'] . '" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['plugins_install_button'] . '</button>
</form>';

echo '</div></div>';

echo '<div id="process-theme">
  <h2>' . $LANG['plugins_upload_dleave'] . '</h2>
</div>';

break;

/** PLUGIN EDITOR */

case 'editor':

echo '<div class="title">

<h2>' . $LANG['plugins_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=plugins.php&amp;action=list" class="btn">' . $LANG['plugins_view'] . '</a>
</div>';

if( !empty( $LANG['plugins_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['plugins_edit_subtitle'] . '</span>';
}

echo '</div>';

if( isset( $_GET['id'] ) && admin_query::plugin_exists( $_GET['id'] ) ) {

$info = admin_query::plugin_infos( $_GET['id'] );

$directory = dirname( $info->main_file );

if( empty( $_GET['page'] ) )
  $page = DIRECTORY_SEPARATOR . basename( $info->main_file );

else {

if( file_exists( DIR . '/' . UPDIR . '/' . $directory . '/' . str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['page'] ) ) ) {
  $page = $_GET['page'];
} else {
  $page = DIRECTORY_SEPARATOR . basename( $info->main_file );
}

}

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

  if( isset( $_POST['text'] ) )
  if( actions::edit_plugin_page( $directory, array( 'page' => $page, 'text' => $_POST['text'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['plugins_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

' . sprintf( $LANG['plugin_editor_title'], $info->name ) . '

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="editor" />
<input type="hidden" name="id" value="' . htmlspecialchars( $_GET['id'] ) . '" />
<select name="page">';
foreach( template::plugin_editor_map( $directory ) as $p ) echo '<option value="' . $p . '"' . ( $p == $page ? ' selected' : '' ) . '>' . $p . '</option>';
echo '</select>
<button class="btn">' . $LANG['view'] . '</button>
</form>

</div>';

echo '<div class="form-table">

<form action="#" method="POST">

<textarea name="text" style="min-height: 450px; width: 100%; box-sizing: border-box; margin-bottom: 10px;">' . htmlspecialchars( file_get_contents( DIR . '/' . UPDIR . '/' . $directory . '/' . $page ) ) . '</textarea>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['save'] . '</button>

</form>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** EDIT PLUGIN */

case 'edit':

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['plugins_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $plugin_exists = admin_query::plugin_exists( $_GET['id'] ) ) ) {

$info = admin_query::plugin_infos( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
echo '<li><a href="?route=plugins.php&amp;action=editor&amp;id=' . $_GET['id'] . '">' . $LANG['editor'] . '</a></li>';
echo '<li><a href="?route=plugins.php&amp;action=uninstall&amp;id=' . $_GET['id'] . '">' . $LANG['plugins_uninstall'] . '</a></li>';

echo '</ul>
</div>';

}

echo '<a href="?route=plugins.php&amp;action=list" class="btn">' . $LANG['plugins_view'] . '</a>
</div>';

if( !empty( $LANG['plugins_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['plugins_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $plugin_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

  if( isset( $_POST['description'] ) )
  if( actions::edit_plugin( $_GET['id'], array( 'description' => $_POST['description'], 'menu' => ( isset( $_POST['in_menu'] ) ? 1 : 0 ), 'icon' => ( isset( $_POST['menu_ico'] ) ? $_POST['menu_ico'] : 1 ), 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ) ) ) ) {

  $info = admin_query::plugin_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'plugins_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_plugin_image( $_GET['id'] ) ) {

  $info->image = '';

  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$_SESSION['plugins_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_publish'] . ':</span>

<div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish">' . $LANG['msg_pubplugin'] . '</label></div>';

if( $info->menu_ready ) {

echo '<div><input type="checkbox" name="in_menu" id="in_menu"' . ( $info->menu ? ' checked' : '' ) . ' /> <label for="in_menu">' . $LANG['msg_pubplugmenu'] . '</label>

<div>';
foreach( array( 1 => 'c', 2 => 'd', 3 => 'e', 4 => 'f', 5 => 'g', 6 => 'h', 7 => 'i', 8 => 'j', 9 => 'k', 10 => 'l', 11 => 'm', 12 => 'n', 13 => 'o' ) as $k => $v ) {
  echo '<input type="radio" name="menu_ico" value="' . $k . '"' . ( $info->menu_icon == $k ? ' checked' : '' ) . '> <span class="couponscms-font">' . $v . '</span>';
}
echo '</div>
</div>';

}

echo '</div>

<div class="row"><span>' . $LANG['form_image'] . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . ( empty( $info->image ) ? '../' . DEFAULT_IMAGES_LOC . '/plugin_ico.png' : '../' . $info->image ) . '" class="avt" alt="" style="display: table-cell; width:100px; height: 50px; margin: 0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->image ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
echo '</div>
</div>

<input type="file" name="image" />
</div> </div>

<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="description">' . $info->description . '</textarea></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['plugins_edit_button'] . '</button>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['plugin_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">';

$uploader = \query\main::user_infos( $info->user )->name;

echo '<div class="row"><span>' . $LANG['uploader'] . ':</span> <div>' . ( empty( $uploader ) ? '-' : '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $uploader . '</a>' ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** UNINSTALLING */

case 'uninstall':

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['plugins_uninstall_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
echo '<a href="?route=plugins.php&amp;action=list" class="btn">' . $LANG['plugins_view'] . '</a>
</div>';

echo '</div>';

if( isset( $_GET['id'] ) && ( $plugin_exists = admin_query::plugin_exists( $_GET['id'] ) ) ) {

$info = admin_query::plugin_infos( $_GET['id'] );

echo '<div class="a-message">' . $LANG['delete_plugin'] . '</div>';

echo '<div class="title" style="margin-top: 40px;">
  <h2>' . $LANG['plugins_unist_files'] . '</h2>
</div>

<ul class="list-of-items">';
echo '<li>' . UPDIR . '/' . dirname( $info->main_file ) . '</li>';
echo '</ul>';

if( isset( $info->uninstall_preview['delete']['tables'] ) ) {
echo '<div class="title" style="margin-top: 40px;">
  <h2>' . $LANG['plugins_unist_tables'] . '</h2>
</div>

<ul class="list-of-items">';
foreach( explode( ',', $info->uninstall_preview['delete']['tables'] ) as $table ) {
  echo '<li>' . \site\plugin::replace_constant( $table ) . '</li>';
}
echo '</ul>';

}

if( isset( $info->uninstall_preview['delete']['columns'] ) ) {
echo '<div class="title" style="margin-top: 40px;">
  <h2>' . $LANG['plugins_unist_columns'] . '</h2>
</div>

<ul class="list-of-items">';
foreach( explode( ',', $info->uninstall_preview['delete']['columns'] ) as $column ) {
  $coltab = explode( '/', $column );
  if( count( $coltab ) === 2 ) {
  echo '<li>' . htmlspecialchars( $coltab[0] ) . ' from ' . \site\plugin::replace_constant( trim( $coltab[1] ) ) . '</li>';
  }
}
echo '</ul>';

}

if( isset( $info->uninstall_preview['delete']['options'] ) ) {
echo '<div class="title" style="margin-top: 40px;">
  <h2>' . $LANG['plugins_unist_options'] . '</h2>
</div>

<ul class="list-of-items">';
foreach( explode( ',', $info->uninstall_preview['delete']['options'] ) as $option ) {
  echo '<li>' . htmlspecialchars( $option ) . '</li>';
}
echo '</ul>';

}

$_SESSION['plugins_csrf'] = $csrf;

echo '<h3 style="text-align: center;">' . sprintf( $LANG['plugins_unist_confq'], $info->name ) . '</h3>';

echo '<form method="GET" style="text-align: center;">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="id" value="' . $_GET['id'] . '" />
<input type="hidden" name="token" value="' . $csrf . '" />
<button class="btn">' . $LANG['plugins_cuinstall_button'] . '</button>
</form>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF PLUGINS */

default:

echo '<div class="title">

<h2>' . $LANG['plugins_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
echo '<a href="?route=plugins.php&amp;action=install" class="btn">' . $LANG['plugins_install'] . '</a>';
echo '</div>';

if( !empty( $LANG['plugins_subtitle'] ) ) {
  echo '<span>' . $LANG['plugins_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_plugin( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_plugin( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'plugins_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_plugin( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_plugin( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['plugins_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="type">
<option value="">' . $LANG['all_plugins'] . '</option>';
foreach( array( 'languages' => $LANG['view_languages'], 'payment_gateways' => $LANG['view_payment_gateways'], 'feed_servers' => $LANG['view_feed_servers'], 'applications' => $LANG['view_applications'] ) as $k => $type ) {
  echo '<option value="' . $k . '"' . ( isset( $_GET['type'] ) && $_GET['type'] == $k ? ' selected' : '' ) . '>' . $type . '</option>';
}

echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['type'] ) ) {
echo '<input type="hidden" name="type" value="' . htmlspecialchars( $_GET['type'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['plugins_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = admin_query::have_plugins( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['type'] ) ? $_GET['type'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['type'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=plugins.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=plugins.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

echo '<div class="bulk_options">';

  echo $LANG['action'] . ': ';
  echo '<select name="action">';
  foreach( array( 'publish' => $LANG['publish'], 'unpublish' => $LANG['unpublish'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
  echo '</select>
  <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>';

echo '</div>';

foreach( admin_query::while_plugins( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . ( empty( $item->image ) ? '../' . DEFAULT_IMAGES_LOC . '/plugin_ico.png' : '../' . $item->image ) . '" alt="" style="width: 70px;" />
  <div class="info-div"><h2>' . ( $item->visible !== 1 ? '<span class="msg-error">' . $LANG['notpublished'] . '</span> ' : '' ) . $item->name . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>
  v ' . sprintf( '%0.2f', $item->version ). '
  </div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">';

  if( empty( $item->scope ) ) {
    echo '<a href="?plugin=' . $item->main_file . '">' . $LANG['open'] . '</a>';
  }
  echo '<a href="?route=plugins.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? $LANG['publish'] : $LANG['unpublish'] ) . '</a>';

  if( !empty( $item->options_file ) ) {
    echo '<a href="?plugin=' . $item->options_file . '">' . $LANG['options'] . '</a>';
  }

  echo '<a href="?route=plugins.php&amp;action=uninstall&amp;id=' . $item->ID . '">' . $LANG['plugins_uninstall'] . '</a>';

  if( !empty( $item->description ) ) {
  echo '<a href="javascript:void(0)" onclick="$(this).show_next( { after_action: \'\', element: \'div\' } ); return false;">' . $LANG['description'] . '</a>';
  echo '<div style="display: none; margin: 10px 0; font-size: 12px;">' . nltobr( $item->description ) . '</div>';
  }

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

  echo '<div class="a-alert">' . $LANG['no_plugins_yet'] . '</div>';

}

break;

}