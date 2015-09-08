<?php

switch( $_GET['action'] ) {

/** EXPORT COUPONS */

case 'export':

if( !ab_to( array( 'coupons' => 'export' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['coupons_export_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=coupons.php&amp;action=list" class="btn">' . $LANG['coupons_view'] . '</a>
</div>';

if( !empty( $LANG['coupons_export_subtitle'] ) ) {
  echo '<span>' . $LANG['coupons_export_subtitle'] . '</span>';
}

echo '</div>';

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="?download=export_coupons_csv.php" method="POST">

<div class="row"><span>' . $LANG['form_category'] . ':</span>
<div><select name="category">
<option value="0">' . $LANG['stores_option_all'] . '</option>';
$categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
echo '</select></div></div>

<div class="row"><span>' . $LANG['form_datefrom'] . ':</span><div><input type="date" name="date[from]" value="2000-01-01" /></div></div>
<div class="row"><span>' . $LANG['from_dateto'] . ':</span><div><input type="date" name="date[to]" value="' . date( 'Y-m-d', strtotime( 'tomorrow' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['subscribers_form_exportfields'] . ':</span><div>
<input type="checkbox" name="fields[name]" id="name" checked disabled /> <label for="name">' . $LANG['name'] . '</label>
<input type="checkbox" name="fields[link]" id="link" checked disabled /> <label for="link">' . $LANG['link'] . '</label>
<input type="checkbox" name="fields[description]" id="description" checked disabled /> <label for="description">' . $LANG['description'] . '</label>
<input type="checkbox" name="fields[tags]" id="tags" checked disabled /> <label for="tags">' . $LANG['tags'] . '</label>
<input type="checkbox" name="fields[code]" id="code" checked disabled /> <label for="code">' . $LANG['code'] . '</label>
<input type="checkbox" name="fields[start_date]" id="start_date" checked disabled /> <label for="start_date">' . $LANG['start_date'] . '</label>
<input type="checkbox" name="fields[end_date]" id="end_date" checked disabled /> <label for="end_date">' . $LANG['end_date'] . '</label>
<input type="checkbox" name="fields[store_url]" id="store_url" checked disabled /> <label for="store_url">' . $LANG['store_url'] . '</label>
<input type="checkbox" name="fields[store_name]" id="store_name" /> <label for="store_name">' . $LANG['store_name'] . '</label>
</div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['subscribers_export_button'] . '</button>
</form>';

echo '</div>';

break;

/** IMPORT COUPONS */

case 'import':

if( !ab_to( array( 'coupons' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['coupons_import_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=coupons.php&amp;action=list" class="btn">' . $LANG['coupons_view'] . '</a>
</div>';

if( !empty( $LANG['coupons_import_subtitle'] ) ) {
  echo '<span>' . $LANG['coupons_import_subtitle'] . '</span>';
}

echo '</div>';

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

  if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) )
  if( $import = actions::import_items( array( 'category' => $_POST['category'], 'file' => $_FILES['file'], 'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ) ) ) ) {
    echo '<div class="a-success">' . sprintf( $LANG['msg_couponsimported'], $import[0], $import[1] ) . '</div>';
  } else {
    echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';
  }

} else {

  echo '<div class="a-alert">' . $LANG['msg_import_coupons'] . '</div>';
  echo '<div class="a-message">' . $LANG['msg_import_coupons_n'] . '</div>';
}

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_in_category'] . ':</span>
<div><select name="category">
<option value="0">' . $LANG['option_no_category'] . '</option>';
        $categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
        foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
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

/** ADD COUPON */

case 'add':

if( !ab_to( array( 'coupons' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['coupons_add_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>
<li><a href="#" class="more_fields">' . $LANG['more'] . '</a></li>
</ul>
</div>

<a href="?route=coupons.php&amp;action=list" class="btn">' . $LANG['coupons_view'] . '</a>
</div>';

if( !empty( $LANG['coupons_add_subtitle'] ) ) {
  echo '<span>' . $LANG['coupons_add_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

  if( isset( $_POST['store'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['code'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_POST['reward_points'] ) && isset( $_POST['start'] ) && isset( $_POST['end'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) )
  if( actions::add_item( array( 'store' => $_POST['store'], 'category' => $_POST['category'], 'name' => $_POST['name'], 'link' => ( !isset( $_POST['coupon_ownlink'] ) && isset( $_POST['link'] ) && filter_var( $_POST['link'], FILTER_VALIDATE_URL ) ? $_POST['link'] : '' ), 'code' => $_POST['code'], 'description' => $_POST['description'], 'tags' => $_POST['tags'], 'cashback' => $_POST['reward_points'], 'start' => $_POST['start']['date'] . ', ' . $_POST['start']['hour'], 'end' => $_POST['end']['date'] . ', ' . $_POST['end']['hour'], 'popular' => ( isset( $_POST['popular'] ) ? 1 : 0 ), 'exclusive' => ( isset( $_POST['exclusive'] ) ? 1 : 0 ), 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ), 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . $LANG['form_store_id'] . ':</span><div data-search="store"><input type="text" name="store" value="' . ( isset( $_POST['store'] ) ? (int) $_POST['store'] : ( !empty( $_GET['store'] ) ? (int) $_GET['store'] : '' ) ) . '" required /><a href="#">S</a></div></div>

<div class="row"><span>' . $LANG['form_category'] . ':</span>
<div><select name="category">
<option value="0">' . $LANG['option_no_category'] . '</option>';
        $categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
        foreach( $categories_while as $cat )echo '<option value="' . $cat->ID .'"'. ( isset( $_POST['category'] ) && $_POST['category'] == $cat->ID ? ' selected' : ( !isset( $_POST['category'] ) && !empty( $_GET['category'] ) && $_GET['category'] == $cat->ID ? ' selected' : '' ) ) .'>' . $cat->name . '</option>';
echo '</select></div></div>

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="" required /></div></div>
<div class="row"><span>' . $LANG['form_code'] . ':</span><div><input type="text" name="code" value="" /></div></div>
<div class="row"><span>' . $LANG['form_coupon_url'] . ':</span><div><input type="checkbox" name="coupon_ownlink" value="1" id="ownlink" checked /> <label for="ownlink">' . $LANG['coupons_use_link'] . '</label> <br />
<input type="text" name="link" value="http://" style="display: none;" />
</div></div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="description"></textarea></div></div>
<div class="row"><span>' . $LANG['form_tags'] . ':</span><div><input type="text" name="tags" value="" /></div></div>
<div class="row" style="display: none;"><span>' . $LANG['form_reward_points'] . ' <span class="info"><span>' . $LANG['coupons_form_ireward_points'] . '</span></span>:</span><div><input type="numer" name="reward_points" value="0" /></div></div>
<div class="row"><span>' . $LANG['form_start_date'] . ':</span><div><input type="date" name="start[date]" value="" style="width: 80%" /><input type="time" name="start[hour]" value="00:00" style="width: 20%" /></div></div>
<div class="row"><span>' . $LANG['form_end_date'] . ':</span><div><input type="date" name="end[date]" value="" style="width: 80%" /><input type="time" name="end[hour]" value="00:00" style="width: 20%" /></div></div>
<div class="row"><span>' . $LANG['form_addto'] . ':</span><div>
<input type="checkbox" name="popular" id="popular" /> <label for="popular">' . $LANG['coupons_addpopular'] . '</label> <br />
<input type="checkbox" name="exclusive" id="exclusive" /> <label for="exclusive">' . $LANG['coupons_addexclusive'] . '</label></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish">' . $LANG['msg_pubcoupon'] . '</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc"></textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['coupons_add_button'] . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

break;

/** EDIT COUPON */

case 'edit':

if( !ab_to( array( 'coupons' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['coupons_edit_title'] . '</h2>

<div style="float: right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $item_exists = \query\main::item_exists( $_GET['id'] ) ) ) {

$info = \query\main::item_infos( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $info->cashback ===0 ) echo '<li><a href="#" class="more_fields">' . $LANG['more'] . '</a></li>';
if( ab_to( array( 'stores' => 'delete' ) ) ) echo '<li><a href="?route=coupons.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
if( $info->visible ) {
  echo '<li><a href="?route=coupons.php&amp;action=list&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['unpublish'] . '</a></li>';
} else {
  echo '<li><a href="?route=coupons.php&amp;action=list&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['publish'] . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=coupons.php&amp;action=list" class="btn">' . $LANG['coupons_view'] . '</a>

</div>';

if( !empty( $LANG['coupons_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['coupons_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $item_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' )) {

  if( isset( $_POST['store'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['code'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_POST['reward_points'] ) && isset( $_POST['start'] ) && isset( $_POST['end'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) )
  if( actions::edit_item( $_GET['id'], array( 'store' => $_POST['store'], 'category' => $_POST['category'], 'popular' => ( isset( $_POST['popular'] ) ? 1 : 0 ), 'exclusive' => ( isset( $_POST['exclusive'] ) ? 1 : 0 ), 'name' => $_POST['name'], 'link' => ( !isset( $_POST['coupon_ownlink'] ) && isset( $_POST['link'] ) && filter_var( $_POST['link'], FILTER_VALIDATE_URL ) ? $_POST['link'] : '' ), 'code' => $_POST['code'], 'description' => $_POST['description'], 'tags' => $_POST['tags'], 'cashback' => $_POST['reward_points'], 'start' => $_POST['start']['date'] . ', ' . $_POST['start']['hour'], 'end' => $_POST['end']['date'] . ', ' . $_POST['end']['hour'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ), 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) ) {

  $info = \query\main::item_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$_SESSION['coupons_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . $LANG['form_store_id'] . ':</span><div data-search="store"><input type="text" name="store" value="' . $info->storeID . '" required /><a href="#">S</a></div></div>

<div class="row"><span>' . $LANG['form_category'] . ':</span>
<div><select name="category">';
    $categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
    foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
echo '</select></div></div>

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $info->title . '" /></div></div>
<div class="row"><span>' . $LANG['form_code'] . ':</span><div><input type="text" name="code" value="' . $info->code . '" /></div></div>
<div class="row"><span>' . $LANG['form_coupon_url'] . ':</span><div><input type="checkbox" name="coupon_ownlink" value="1" id="ownlink"' . ( empty( $info->original_url ) ? ' checked' : '' ) . ' /> <label for="ownlink">' . $LANG['coupons_use_link'] . '</label> <br />
<input type="text" name="link" value="' . ( !empty( $info->original_url ) ? $info->original_url : 'http://' ) . '"' . ( empty( $info->original_url ) ? ' style="display: none;"' : '' ) . ' />
</div></div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="description">' . $info->description . '</textarea></div></div>
<div class="row"><span>' . $LANG['form_tags'] . ':</span><div><input type="text" name="tags" value="' . $info->tags . '" /></div></div>
<div class="row"' . ( $info->cashback !== 0 ?: 'style="display: none;"' ) . '><span>' . $LANG['form_reward_points'] . ' <span class="info"><span>' . $LANG['coupons_form_ireward_points'] . '</span></span>:</span><div><input type="numer" name="reward_points" value="' . $info->cashback . '" /></div></div>
<div class="row"><span>' . $LANG['form_start_date'] . ':</span><div><input type="date" name="start[date]" value="' . date( 'Y-m-d', strtotime( $info->start_date ) ) . '" style="width: 80%" /><input type="time" name="start[hour]" value="' . date( 'H:i', strtotime( $info->start_date ) ) . '" style="width: 20%" /></div></div>
<div class="row"><span>' . $LANG['form_end_date'] . ':</span><div><input type="date" name="end[date]" value="' . date( 'Y-m-d', strtotime( $info->expiration_date ) ) . '" style="width: 80%" /><input type="time" name="end[hour]" value="' . date( 'H:i', strtotime( $info->expiration_date ) ) . '" style="width: 20%" /></div></div>
<div class="row"><span>' . $LANG['form_addto'] . ':</span><div>
<input type="checkbox" name="popular" id="popular"' . ( $info->is_popular ? ' checked' : '' ) . ' /> <label for="popular">' . $LANG['coupons_addpopular'] . '</label> <br />
<input type="checkbox" name="exclusive" id="exclusive"' . ( $info->is_exclusive ? ' checked' : '' ) . ' /> <label for="exclusive">' . $LANG['coupons_addexclusive'] . '</label></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish">' . $LANG['msg_pubcoupon'] . '</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="' . $info->meta_title . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc">' . $info->meta_description . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['coupons_edit_button'] . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['coupons_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['views'] . ':</span> <div>' . $info->views . '</div></div>
<div class="row"><span>' . $LANG['page_url'] . ':</span> <div><a href="' . $info->link . '" target="_blank">' . $info->link . '</a></div></div>
<div class="row"><span>' . $LANG['store_name'] . ':</span> <div>' . ( empty( $info->store_name ) ? '-' : ( ab_to( array( 'stores' => 'edit' ) ) ? '<a href="?route=stores.php&amp;action=edit&amp;id=' . $info->storeID . '">' . $info->store_name . '</a>' : $info->store_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_by']  . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_on']  . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . $LANG['added_by'] . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->userID . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['added_on']  . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF STORES */

default:

if( !ab_to( array( 'coupons' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['coupons_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

$ab_exp = ab_to( array( 'coupons' => 'export' ) );
$ab_imp = ab_to( array( 'coupons' => 'import' ) );

if( $ab_exp || $ab_imp ) {

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $ab_imp ) echo '<li><a href="?route=coupons.php&amp;action=import">' . $LANG['import'] . '</a></li>';
if( $ab_exp ) echo '<li><a href="?route=coupons.php&amp;action=export">' . $LANG['export'] . '</a></li>';

echo '</ul>
</div>';

}

if( ab_to( array( 'coupons' => 'add' ) ) ) echo '<a href="?route=coupons.php&amp;action=add" class="btn">' . $LANG['coupons_add'] . '</a>';
echo '</div>';

if( !empty( $LANG['coupons_subtitle'] ) ) {
  echo '<span>' . $LANG['coupons_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_item( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_item( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'coupons_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_item( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_item( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="coupons.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'], 'update' => $LANG['order_last_update'], 'update desc' => $LANG['order_last_update_desc'], 'active' => $LANG['order_expiration'], 'active DESC' => $LANG['order_expiration_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="category">
<option value="">' . $LANG['all_categories'] . '</option>';
        $categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
        foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '"'. ( isset( $_GET['category'] ) && $_GET['category'] == $cat->ID ? ' selected' : '' ) .'>' . $cat->name . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="coupons.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['category'] ) ) {
echo '<input type="hidden" name="category" value="' . htmlspecialchars( $_GET['category'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['coupons_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = \query\main::have_items( $options = array( 'per_page' => 10, 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'user' => (isset( $_GET['user'] ) ? $_GET['user'] : ''), 'categories' => (isset( $_GET['category'] ) ? $_GET['category'] : ''), 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : 'all'), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['store'] ) || !empty( $_GET['user'] ) || !empty( $_GET['category'] ) || !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=coupons.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=coupons.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'coupons' => 'edit' ) );
$ab_del  = ab_to( array( 'coupons' => 'delete' ) );

if( $ab_del ) {

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

foreach( \query\main::while_items( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::store_avatar( $item->store_img ) . '" alt="" style="width: 80px;" />
  <div class="info-div"><h2>' . ( !$item->visible ? '<span class="msg-error">' . $LANG['notpublished'] . '</span> ' : '' ) . ( $item->feedID !== 0 ? '<span class="msg-alert" title="' . $LANG['added_through_feed_msg'] . '">' . $LANG['added_through_feed'] . '</span> ' : '' ) . ( !$item->is_expired ? '<span class="msg-success">' . $LANG['active'] . '</span> ' : '<span class="msg-error">' . $LANG['expired'] . '</span> ' ) . $item->title . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>
  <a href="?route=coupons.php&amp;store=' . $item->storeID . '"">' . $item->store_name . '</a></div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">';
  if( $ab_edt ) {
  echo '<a href="?route=coupons.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
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

  echo '<div class="a-alert">' . $LANG['no_coupons_yet'] . '</div>';

}

break;

}