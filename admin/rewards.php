<?php

switch( $_GET['action'] ) {

/** ADD REWARD */

case 'add':

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . $LANG['rewards_add_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=rewards.php&amp;action=list" class="btn">' . $LANG['rewards_view'] . '</a>
</div>';

if( !empty( $LANG['rewards_add_subtitle'] ) ) {
  echo '<span>' . $LANG['rewards_add_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

  if( isset( $_POST['name'] )  && isset( $_POST['points'] ) && isset( $_POST['text'] ) && isset( $_POST['fields'] ) )
  if( actions::add_reward( array( 'name' => $_POST['name'], 'points' => $_POST['points'], 'description' => $_POST['text'], 'fields' => $_POST['fields'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ) ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['rewards_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="" /></div></div>
<div class="row"><span>' . $LANG['form_image'] . ':</span> <div><input type="file" name="logo" /></div> </div>
<div class="row"><span>' . $LANG['rewards_form_points'] . ' <span class="info"><span>' . $LANG['rewards_form_ipoints'] . '</span></span>:</span><div><input type="number" name="points" value="100" /></div> </div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="text" style="min-height:100px;"></textarea></div></div>
<div class="row fields"><span>' . $LANG['rewards_form_fields'] . ' <span class="info"><span>' . $LANG['rewards_form_ifields'] . '</span></span>:</span><div>

<ul id="fileds_table">
<li class="head" style="display: none;">
<label>' . $LANG['rewards_table_name'] . '</label>
<label>' . $LANG['rewards_table_type'] . '</label>
<label>' . $LANG['rewards_table_value'] . '</label>
</li>

<li id="fileds_table_new" style="display: none;">

<input type="input" name="fields[name][]" />
<select name="fields[type][]">';
foreach( array( 'text' => $LANG['rewards_type_text'], 'number' => $LANG['rewards_type_number'], 'email' => $LANG['rewards_type_email'], 'hidden' => $LANG['rewards_type_hidden'] ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select>
<input type="text" name="fields[value][]" />
<select name="fields[require][]">';
foreach( array( 0 => $LANG['rewards_notrequired'], 1 => $LANG['rewards_required'] ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select>

<a href="#">V</a>

</li>
</ul>

<a href="#">' . $LANG['rewards_addfield_button'] . '</a>

</div></div>

<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish">' . $LANG['msg_pubreward'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['rewards_add_button'] . '</button>

</form>

</div>';

break;

/** EDIT REWARD */

case 'edit':

if( !$GLOBALS['me']->is_admin ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['rewards_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $reward_exists = \query\main::reward_exists( $_GET['id'] ) ) ) {

$info = \query\main::reward_infos( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>
<li><a href="?route=rewards.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=rewards.php&amp;action=list" class="btn">' . $LANG['rewards_view'] . '</a>
</div>';

if( !empty( $LANG['rewards_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['rewards_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $reward_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

  if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['points'] ) && isset( $_POST['fields'] ) )
  if( actions::edit_reward( $_GET['id'], array( 'points' => $_POST['points'], 'name' => $_POST['name'], 'description' => $_POST['text'], 'fields' => $_POST['fields'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ) ) ) ) {

  $info = \query\main::reward_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'rewards_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_reward_image( $_GET['id'] ) ) {

  $info->image = '';

  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$_SESSION['rewards_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $info->title . '" /></div></div>
<div class="row"><span>' . $LANG['form_image'] . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . \query\main::reward_avatar( $info->image ) . '" class="avt" alt="" style="display: table-cell; width:80px; height:80px; margin: 0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->image ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
echo '</div>
</div>

<input type="file" name="logo" /></div> </div>

<div class="row"><span>' . $LANG['rewards_form_points'] . ' <span class="info"><span>' . $LANG['rewards_form_ipoints'] . '</span></span>:</span><div><input type="number" name="points" value="' . $info->points . '" /></div> </div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="text" style="min-height:100px;">' . $info->description . '</textarea></div></div>

<div class="row fields"><span>' . $LANG['rewards_form_fields'] . ' <span class="info"><span>' . $LANG['rewards_form_ifields'] . '</span></span>:</span><div>';

echo '<ul id="fileds_table">
<li class="head"' . ( empty( $info->fields ) ? ' style="display: none;"' : '' ) . '>
<label>' . $LANG['rewards_table_name'] . '</label>
<label>' . $LANG['rewards_table_type'] . '</label>
<label>' . $LANG['rewards_table_value'] . '</label>
</li>

<li id="fileds_table_new" style="display: none;">

<input type="input" name="fields[name][]" />
<select name="fields[type][]">';
foreach( array( 'text' => $LANG['rewards_type_text'], 'number' => $LANG['rewards_type_number'], 'email' => $LANG['rewards_type_email'], 'hidden' => $LANG['rewards_type_hidden'] ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select>
<input type="text" name="fields[value][]" />
<select name="fields[require][]">';
foreach( array( 0 => $LANG['rewards_notrequired'], 1 => $LANG['rewards_required'] ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select>

<a href="#">V</a>

</li>';

if( !empty( $info->fields ) ) {

foreach( $info->fields as $v ) {

echo '<li class="added_field">

<input type="input" name="fields[name][]" value="' . htmlspecialchars( $v['name'] ) . '" />
<select name="fields[type][]">';
foreach( array( 'text' => $LANG['rewards_type_text'], 'number' => $LANG['rewards_type_number'], 'email' => $LANG['rewards_type_email'], 'hidden' => $LANG['rewards_type_hidden'] ) as $k => $t ) echo '<option value="' . $k . '"' . ( $k == htmlspecialchars( $v['type'] ) ? ' selected' : '' ) . '>' . $t . '</option>';
echo '</select>
<input type="text" name="fields[value][]" value="' . htmlspecialchars( $v['value'] ) . '" />
<select name="fields[require][]">';
foreach( array( 0 => $LANG['rewards_notrequired'], 1 => $LANG['rewards_required'] ) as $k => $t ) echo '<option value="' . $k . '"' . ( $k == htmlspecialchars( $v['require'] ) ? ' selected' : '' ) . '>' . $t . '</option>';
echo '</select>

<a href="#">V</a>

</li>';

}

}

echo '</ul>

<a href="#">' . $LANG['rewards_addfield_button'] . '</a>

</div></div>

<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" ' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish">' . $LANG['msg_pubreward'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['rewards_edit_button'] . '</button>

</form>

</div>


<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['rewards_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['last_update_by'] . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_on'] . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . $LANG['added_by'] . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span> <div>' . $info->date . '</div></div>
</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** VIEW CLAIM REQUEST */

case 'view_rewardreq':

if( !ab_to( array( 'claim_reqs' => 'view' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['rewards_viewcr_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $request_exists = \query\main::reward_req_exists( $_GET['id'] ) ) ) {

$info = \query\main::reward_req_infos( $_GET['id'] );

$ab_edt  = ab_to( array( 'claim_reqs' => 'edit' ) );
$ab_del = ab_to( array( 'claim_reqs' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=rewards.php&amp;action=requests&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
if( $info->claimed ) {
  if( $ab_edt ) echo '<li><a href="?route=rewards.php&amp;action=requests&amp;type=unclaim&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['unclaim'] . '</a></li>';
} else {
  if( $ab_edt ) echo '<li><a href="?route=rewards.php&amp;action=requests&amp;type=claim&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['claim'] . '</a></li>';
}
echo '</ul>
</div>';

}

}

echo '<a href="?route=rewards.php&amp;action=requests" class="btn">' . $LANG['rewards_viewcr_button'] . '</a>
</div>';

if( !empty( $LANG['rewards_viewcr_subtitle'] ) ) {
  echo '<span>' . $LANG['rewards_viewcr_subtitle'] . '</span>';
}

echo '</div>';

if( $request_exists ) {

$_SESSION['rewards_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['rewards_req_form_pused'] . ':</span> <div>' . $info->points . '</div></div>
<div class="row"><span>' . $LANG['rewards_form_reward'] . ':</span> <div>' . ( $info->reward_exists ? '<a href="?route=rewards.php&amp;action=requests&amp;reward=' . $info->reward . '">' . $info->name . '</a>' . ( $GLOBALS['me']->is_admin ? ' / <a href="?route=rewards.php&amp;action=edit&amp;id=' . $info->reward . '">' . $LANG['rewards_edit_button'] . '</a>' : '' ) : $info->name ) . '</div></div>';

if( !empty( $info->fields ) ) {
foreach( $info->fields as $k => $v )
  echo '<div class="row"><span>' . htmlspecialchars( $k ) . ':</span> <div>' . ( !empty( $v ) ? htmlspecialchars( $v ) : '-' ) . '</div></div>';
}

echo '<div class="row"><span>' . $LANG['last_update_by'] . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_on'] . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . $LANG['added_by'] . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF CLAIM REQUESTS */

case 'requests':

if( !ab_to( array( 'claim_reqs' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['rewards_req_title'] . '</h2>';

if( !empty( $LANG['rewards_req_subtitle'] ) ) {
  echo '<span>' . $LANG['rewards_req_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_reward_req( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::action_reward_req( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'rewards_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_reward_req( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'claim' || $_GET['type'] == 'unclaim' ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_reward_req( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['rewards_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="requests" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'points' => $LANG['order_points'], 'points desc' => $LANG['order_points_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">';
foreach( array( '' => $LANG['all_requests'], 'valid' => $LANG['view_claimed'], 'notvalid' => $LANG['view_unclaimed'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="requests" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . htmlspecialchars( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['rewards_req_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';


$p = \query\main::have_rewards_reqs( $options = array( 'per_page' => 10, 'user' => (isset( $_GET['user'] ) ? $_GET['user'] : ''), 'reward' => (isset( $_GET['reward'] ) ? $_GET['reward'] : ''), 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['user'] ) || !empty( $_GET['reward'] ) || !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=rewards.php&amp;action=requests">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=rewards.php&amp;action=requests" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'claim_reqs' => 'edit' ) );
$ab_del  = ab_to( array( 'claim_reqs' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

  if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button> ';

  if( $ab_edt ) {
    echo $LANG['action'] . ':
    <select name="action">';
    foreach( array( 'claim' => $LANG['claim'], 'unclaim' => $LANG['unclaim'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>';
  }

echo '</div>';

}

foreach( \query\main::while_rewards_reqs( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  $user = \query\main::user_infos( $item->user );

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::user_avatar( $user->avatar ) . '" alt="" />

  <div class="info-div">

  <h2>' . ( $item->claimed ? '<span class="msg-success">' . $LANG['claimed'] . '</span>' : '<span class="msg-error">' . $LANG['notclaimed'] . '</span>' ) . ( empty( $user->name ) ? ' -' : ' <a href="?route=rewards.php&amp;action=requests&amp;user=' . $item->user . '">' . $user->name . '</a>' ) . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>

  ' . ( $item->reward_exists ? '<a href="?route=rewards.php&amp;action=requests&amp;reward=' . $item->reward . '">' . $item->name . '</a>' : $item->name ) . ' / ' . $LANG['rewards_req_form_pused'] . ': <b>' . $item->points . '</b>

  </div></div>

  <div style="clear:both;"></div>

  <div class="options">
  <a href="?route=rewards.php&amp;action=view_rewardreq&amp;id=' . $item->ID . '">' . $LANG['view'] . '</a>';
  if( $ab_edt ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->claimed ? 'unclaim' : 'claim' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->claimed ? $LANG['unclaim'] : $LANG['claim'] ) . '</a>';
  if( $ab_del ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>
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

  echo '<div class="a-alert">' . $LANG['no_claimreq_yet'] . '</div>';

}

break;

/** LIST OF REWARDS */

default:

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . $LANG['rewards_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=rewards.php&amp;action=add" class="btn">' . $LANG['rewards_add'] . '</a>
</div>';

if( !empty( $LANG['rewards_subtitle'] ) ) {
  echo '<span>' . $LANG['rewards_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_reward( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'rewards_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_reward( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['rewards_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'points' => $LANG['order_points'], 'points desc' => $LANG['order_points_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['rewards_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = \query\main::have_rewards( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=rewards.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=rewards.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>

<div class="bulk_options">
  <button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button>
</div>';

foreach( \query\main::while_rewards( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::reward_avatar( $item->image ) . '" alt="" />

  <div class="info-div">

  <h2>' . ( $item->visible ? '<span class="msg-success">' . $LANG['published'] . '</span>' : '<span class="msg-error">' . $LANG['notpublished'] . '</span>' ) . ' ' . $item->title . '</h2>

  </div></div>

  <div style="clear:both;"></div>

  <div class="options">
  <a href="?route=rewards.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>
  <a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>
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

  echo '<div class="a-alert">' . $LANG['no_rewards_yet'] . '</div>';

}

break;

}