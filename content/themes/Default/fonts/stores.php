<?php

switch( $_GET['action'] ) {

/** EXPORT STORES */

case 'export':

if( !ab_to( array( 'stores' => 'export' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['stores_export_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=stores.php&amp;action=list" class="btn">' . $LANG['stores_view'] . '</a>
</div>';

if( !empty( $LANG['stores_export_subtitle'] ) ) {
  echo '<span>' . $LANG['stores_export_subtitle'] . '</span>';
}

echo '</div>';

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="?download=export_stores_csv.php" method="POST">

<div class="row"><span>' . $LANG['form_category'] . ':</span>
<div><select name="category">
<option value="0">' . $LANG['stores_option_all'] . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
  echo '<optgroup label="' . $cat['infos']->name . '">';
  echo '<option value="' . $cat['infos']->ID . '">' . $cat['infos']->name . '</option>';
  if( isset( $cat['subcats'] ) ) {
    foreach( $cat['subcats'] as $subcat ) {
      echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
    }
  }
  echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . $LANG['form_datefrom'] . ':</span><div><input type="date" name="date[from]" value="2000-01-01" /></div></div>
<div class="row"><span>' . $LANG['from_dateto'] . ':</span><div><input type="date" name="date[to]" value="' . date( 'Y-m-d', strtotime( 'tomorrow' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['subscribers_form_exportfields'] . ':</span><div>
<input type="checkbox" name="fields[name]" id="name" checked disabled /> <label for="name">' . $LANG['name'] . '</label>
<input type="checkbox" name="fields[link]" id="link" checked disabled /> <label for="link">' . $LANG['link'] . '</label>
<input type="checkbox" name="fields[description]" id="description" checked disabled /> <label for="description">' . $LANG['description'] . '</label>
<input type="checkbox" name="fields[tags]" id="tags" checked disabled /> <label for="tags">' . $LANG['tags'] . '</label>
<input type="checkbox" name="fields[image]" id="image" checked disabled /> <label for="image">' . $LANG['image'] . '</label></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['subscribers_export_button'] . '</button>
</form>';

echo '</div>';

break;

/** IMPORT STORES */

case 'import':

if( !ab_to( array( 'stores' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['stores_import_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=stores.php&amp;action=list" class="btn">' . $LANG['stores_view'] . '</a>
</div>';

if( !empty( $LANG['stores_import_subtitle'] ) ) {
  echo '<span>' . $LANG['stores_import_subtitle'] . '</span>';
}

echo '</div>';

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

  if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) )
  if( $import = actions::import_stores( array( 'category' => $_POST['category'], 'file' => $_FILES['file'], 'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ) ) ) ) {
    echo '<div class="a-success">' . sprintf( $LANG['msg_storesimported'], $import[0], $import[1] ) . '</div>';
  } else {
    echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';
  }

} else {

  echo '<div class="a-alert">' . $LANG['msg_import_stores'] . '</div>';

}

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_in_category'] . ':</span>
<div><select name="category">
<option value="0">' . $LANG['option_no_category'] . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
  echo '<optgroup label="' . $cat['infos']->name . '">';
  echo '<option value="' . $cat['infos']->ID . '">' . $cat['infos']->name . '</option>';
  if( isset( $cat['subcats'] ) ) {
    foreach( $cat['subcats'] as $subcat ) {
      echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
    }
  }
  echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . $LANG['form_csv_file'] . ':</span><div><input type="file" name="file" value="" /></div></div>
<div class="row"><span></span><div><input type="checkbox" name="omitfirst" id="omitfirst" value="1" checked /> <label for="omitfirst">' . $LANG['msg_csvomitfirst'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['button_import'] . '</button>
</form>

</div></div>';

echo '<div id="process-theme">
  <h2>' . $LANG['msg_upload_dleave'] . '</h2>
</div>';

break;

/** ADD STORE */

case 'add':

if( !ab_to( array( 'stores' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['stores_add_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=stores.php&amp;action=list" class="btn">' . $LANG['stores_view'] . '</a>
</div>';

if( !empty( $LANG['stores_add_subtitle'] ) ) {
  echo '<span>' . $LANG['stores_add_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

  if( isset( $_POST['user'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['url'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_FILES['logo'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) )
  if( actions::add_store( array( 'user' => $_POST['user'],  'category' => $_POST['category'], 'name' => $_POST['name'], 'url' => $_POST['url'], 'description' => $_POST['description'], 'tags' => $_POST['tags'], 'popular' => ( isset( $_POST['popular'] ) ? 1 : 0 ), 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ), 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) ) echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . $LANG['form_user_id'] . ':</span><div data-search="user"><input type="text" name="user" value="' . ( isset( $_POST['user'] ) ? (int) $_POST['user'] : ( !empty( $_GET['user'] ) ? (int) $_GET['user'] : $GLOBALS['me']->ID ) ) . '" required /><a href="#">S</a></div></div>

<div class="row"><span>' . $LANG['form_category'] . ':</span>
<div><select name="category">';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
  echo '<optgroup label="' . $cat['infos']->name . '">';
  echo '<option value="' . $cat['infos']->ID . '">' . $cat['infos']->name . '</option>';
  if( isset( $cat['subcats'] ) ) {
    foreach( $cat['subcats'] as $subcat ) {
      echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
    }
  }
  echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="" required /></div></div>
<div class="row"><span>' . $LANG['form_store_url'] . ':</span><div><input type="text" name="url" value="http://" /></div></div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="description"></textarea></div></div>
<div class="row"><span>' . $LANG['form_tags'] . ':</span><div><input type="text" name="tags" value="" /></div></div>
<div class="row"><span>' . $LANG['form_logo'] . ':</span><div><input type="file" name="logo" /></div></div>
<div class="row"><span>' . $LANG['form_addto'] . ':</span><div><input type="checkbox" name="popular" id="popular" /> <label for="popular">' . $LANG['coupons_addpopular'] . '</label></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish">' . $LANG['msg_pubstore'] . '</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc"></textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['stores_add_button'] . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

break;

/** EDIT STORE */

case 'edit':

if( !ab_to( array( 'stores' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['stores_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $store_exists = \query\main::store_exists( $_GET['id'] ) ) ) {

$info = \query\main::store_infos( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( ab_to( array( 'coupons' => 'add' ) ) ) echo '<li><a href="?route=coupons.php&amp;action=add&amp;store=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['coupons_add_button'] . '</a></li>';
if( ab_to( array( 'stores' => 'delete' ) ) ) echo '<li><a href="?route=stores.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_store'] . '">' . $LANG['delete'] . '</a></li>';
if( $info->visible ) {
  echo '<li><a href="?route=stores.php&amp;action=list&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['unpublish'] . '</a></li>';
} else {
  echo '<li><a href="?route=stores.php&amp;action=list&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['publish'] . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=stores.php&amp;action=list" class="btn">' . $LANG['stores_view'] . '</a>
</div>';

if( !empty( $LANG['stores_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['stores_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $store_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

  if( isset( $_POST['user'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['url'] ) && isset( $_POST['tags'] ) && isset( $_POST['description'] ) && isset( $_FILES['logo'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) )
  if( actions::edit_store( $_GET['id'], array( 'user' => $_POST['user'], 'category' => $_POST['category'], 'name' => $_POST['name'], 'url' => $_POST['url'], 'tags' => $_POST['tags'], 'description' => $_POST['description'], 'popular' => ( isset( $_POST['popular'] ) ? 1 : 0 ), 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ), 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) ) {

  $info = \query\main::store_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'stores_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_store_image( $_GET['id'] ) ) {

  $info->image = '';

  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$_SESSION['stores_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_user_id'] . ':</span><div data-search="user"><input type="text" name="user" value="' . $info->userID . '" required /><a href="#">S</a></div></div>

<div class="row"><span>' . $LANG['form_category'] . ':</span>
<div><select name="category">';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
  echo '<optgroup label="' . $cat['infos']->name . '">';
  echo '<option value="' . $cat['infos']->ID . '"' . ( $info->catID == $cat['infos']->ID ? ' selected' : '' ) . '>' . $cat['infos']->name . '</option>';
  if( isset( $cat['subcats'] ) ) {
    foreach( $cat['subcats'] as $subcat ) {
      echo '<option value="' . $subcat->ID . '"' . ( $info->catID == $subcat->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
    }
  }
  echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $info->name . '" required /></div></div>
<div class="row"><span>' . $LANG['form_store_url'] . ':</span><div><input type="text" name="url" value="' . $info->url . '" /></div></div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="description">' . $info->description . '</textarea></div></div>
<div class="row"><span>' . $LANG['form_tags'] . ':</span><div><input type="text" name="tags" value="' . $info->tags . '" /></div></div>
<div class="row"><span>' . $LANG['form_logo'] . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . \query\main::store_avatar( $info->image ) . '" class="avt" alt="" style="display: table-cell; width:100px; height:50px; margin: 0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->image ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
echo '</div>
</div>

<input type="file" name="logo" />
</div> </div>

<div class="row"><span>' . $LANG['form_addto'] . ':</span><div><input type="checkbox" name="popular" id="popular"' . ( $info->is_popular ? ' checked' : '' ) . ' /> <label for="popular">' . $LANG['coupons_addpopular'] . '</label></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish">' . $LANG['msg_pubstore'] . '</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="' . $info->meta_title . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc">' . $info->meta_description . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['stores_edit_button'] . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['stores_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['views'] . ':</span> <div>' . $info->views . '</div></div>
<div class="row"><span>' . $LANG['page_url'] . ':</span> <div><a href="' . $info->link . '" target="_blank">' . $info->link . '</a></div></div>
<div class="row"><span>' . $LANG['owner'] . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->userID . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_by'] . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_on'] . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . $LANG['coupons'] . ':</span> <div>' . ( ab_to( array( 'coupons' => 'view' ) ) ? '<a href="?route=coupons.php&amp;action=list&amp;store=' . $info->ID . '">' . $info->coupons . '</a>' : $info->coupons ) . ( ab_to( array( 'coupons' => 'add' ) ) ? ' / <a href="?route=coupons.php&amp;action=add&amp;store=' . $info->ID . '&amp;category=' . $info->catID . '">' . $LANG['coupons_add_button'] . '</a>' : '' ) . '</div></div>
<div class="row"><span>' . $LANG['reviews'] . ':</span> <div>' . ( ab_to( array( 'reviews' => 'view' ) ) ? '<a href="?route=reviews.php&amp;action=list&amp;store=' . $info->ID . '">' . $info->reviews . '</a>' : $info->reviews ) . ( ab_to( array( 'reviews' => 'add' ) ) ? ' / <a href="?route=reviews.php&amp;action=add&amp;store=' . $info->ID . '">' . $LANG['reviews_add_button'] . '</a>' : '' ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF STORES */

default:

if( !ab_to( array( 'stores' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['stores_title'] . '</h2>';

echo '<div style="float:right; margin: 0 2px 0 0;">';

$ab_exp = ab_to( array( 'stores' => 'export' ) );
$ab_imp = ab_to( array( 'stores' => 'import' ) );

if( $ab_exp || $ab_imp ) {

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $ab_imp ) echo '<li><a href="?route=stores.php&amp;action=import">' . $LANG['import'] . '</a></li>';
if( $ab_exp ) echo '<li><a href="?route=stores.php&amp;action=export">' . $LANG['export'] . '</a></li>';

echo '</ul>
</div>';

}

if( $ab_add = ab_to( array( 'stores' => 'add' ) ) ) echo '<a href="?route=stores.php&amp;action=add" class="btn">' . $LANG['stores_add'] . '</a>';
echo '</div>';

if( !empty( $LANG['stores_subtitle'] ) ) {
  echo '<span>' . $LANG['stores_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_store( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['change_cat'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['category'] ) )
  if( actions::change_store_category( array_keys( $_POST['id'] ), $_POST['category'] ) )
  echo '<div class="a-success">' . $LANG['msg_changed'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_store( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'stores_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_store( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_store( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="stores.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'votes' => $LANG['order_votes'], 'votes desc' => $LANG['order_votes_desc'], 'rating' => $LANG['order_rating'], 'rating desc' => $LANG['order_rating_desc'], 'update' => $LANG['order_last_update'], 'update desc' => $LANG['order_last_update_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="category">
<option value="">' . $LANG['all_categories'] . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
  echo '<optgroup label="' . $cat['infos']->name . '">';
  echo '<option value="' . $cat['infos']->ID . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $cat['infos']->ID ? ' selected' : '' ) . '>' . $cat['infos']->name . '</option>';
  if( isset( $cat['subcats'] ) ) {
    foreach( $cat['subcats'] as $subcat ) {
      echo '<option value="' . $subcat->ID . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $subcat->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
    }
  }
  echo '</optgroup>';
}
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="stores.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['category'] ) ) {
echo '<input type="hidden" name="category" value="' . htmlspecialchars( $_GET['category'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['stores_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';


$p = \query\main::have_stores( $options = array( 'per_page' => 10, 'user' => (isset( $_GET['user'] ) ? $_GET['user'] : ''), 'categories' => (isset( $_GET['category'] ) ? $_GET['category'] : ''), 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : 'all'), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['user'] ) || !empty( $_GET['category'] ) || !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=stores.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=stores.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'stores' => 'edit' ) );
$ab_del  = ab_to( array( 'stores' => 'delete' ) );
$feed_view = ab_to( array( 'feed' => 'view' ) );

if( $ab_edt || $ab_del ) {
echo '<div class="bulk_options">';

  if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_store'] . '">' . $LANG['delete_all'] . '</button> ';

  if( $ab_edt ) {

    echo $LANG['action'] . ':
    <select name="action">';
    foreach( array( 'publish' => $LANG['publish'], 'unpublish' => $LANG['unpublish'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . $LANG['set_all'] . '</button> ';

    echo $LANG['category'] . ':
    <select name="category">';
    foreach( \query\main::while_categories( array( 'max' => 0 ) ) as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
    echo '</select>
    <button class="btn" name="change_cat">' . $LANG['move_all'] . '</button>';
  }

echo '</div>';
}

foreach( \query\main::while_stores( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::store_avatar( $item->image ) . '" alt="" style="width: 80px;" />
  <div class="info-div"><h2>' . ( !$item->visible ? '<span class="msg-error">' . $LANG['notpublished'] . '</span> ' : '' ) . ( $item->feedID !== 0 ? '<span class="msg-alert" title="' . $LANG['added_through_feed_msg'] . '">' . $LANG['added_through_feed'] . '</span> ' : '' ) . $item->name . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>
  ' . ( empty( $item->coupons ) ? $LANG['no_coupons_store'] : '<a href="?route=coupons.php&amp;store=' . $item->ID . '">' . sprintf( $LANG['nr_coupons_store'], $item->coupons ) . '</a>' ) . '</div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">';
  if( $ab_edt ) {
  echo '<a href="?route=stores.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? $LANG['publish'] : $LANG['unpublish'] ) . '</a>';
  }
  if( $ab_add ) echo '<a href="?route=coupons.php&amp;action=add&amp;store=' . $item->ID . '&amp;category=' . $item->catID . '">' . $LANG['coupons_add_button'] . '</a>';
  if( $ab_del ) echo '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_store'] . '">' . $LANG['delete'] . '</a>';
  if( $feed_view && $item->feedID !== 0 ) echo '<a href="?route=feed.php&amp;action=coupons&amp;store=' . $item->feedID . '">' . $LANG['feed_coupons_link'] . '</a>';
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

  echo '<div class="a-alert">' . $LANG['no_stores_yet'] . '</div>';

}

break;

}