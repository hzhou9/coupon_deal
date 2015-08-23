<?php

switch( $_GET['action'] ) {

/** ADD PLAN */

case 'plan_add':

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . $LANG['pmts_addplan_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=payments.php&amp;action=plan_view" class="btn">' . $LANG['payments_plan_view'] . '</a>
</div>';

if( !empty( $LANG['pmts_addplan_subtitle'] ) ) {
  echo '<span>' . $LANG['pmts_addplan_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

  if( isset( $_POST['name'] ) && isset( $_POST['price'] ) && isset( $_POST['credits'] ) && isset( $_POST['text'] ) )
  if( actions::add_payment_plan( array( 'name' => $_POST['name'], 'price' => $_POST['price'], 'credits' => $_POST['credits'], 'description' => $_POST['text'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ) ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['payments_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="" /></div></div>
<div class="row"><span>' . $LANG['form_price'] . ':</span><div><input type="text" name="price" value="" placeholder="' . sprintf( PRICE_FORMAT, \site\utils::money_format( 0.00 ) ) . '" /></div> </div>
<div class="row"><span>' . $LANG['form_credits'] . ':</span><div><input type="number" name="credits" min="0" value="10" /></div></div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="text" style="min-height:100px;"></textarea></div></div>
<div class="row"><span>' . $LANG['form_image'] . ':</span> <div><input type="file" name="logo" /></div></div>

<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish">' . $LANG['msg_pubpplan'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['pmts_plans_add'] . '</button>

</form>

</div>';

break;

/** EDIT PLAN */

case 'plan_edit':

if( !$GLOBALS['me']->is_admin ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['pmts_editplan_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $plan_exists = \query\payments::plan_exists( $_GET['id'] ) ) ) {

$info = \query\payments::plan_infos( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>
<li><a href="?route=payments.php&amp;action=plan_view&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=payments.php&amp;action=plan_view" class="btn">' . $LANG['payments_plan_view'] . '</a>
</div>';

if( !empty( $LANG['pmts_editplan_subtitle'] ) ) {
  echo '<span>' . $LANG['pmts_editplan_subtitle'] . '</span>';
}

echo '</div>';

if( $plan_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

  if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['price'] ) && isset( $_POST['credits'] ) )
  if( actions::edit_payment_plan( $_GET['id'], array( 'name' => $_POST['name'], 'description' => $_POST['text'], 'price' => $_POST['price'], 'credits' => $_POST['credits'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ) ) ) ) {

  $info = \query\payments::plan_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'payments_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_payment_plan_image( $_GET['id'] ) ) {

  $info->image = '';

  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$_SESSION['payments_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $info->name . '" /></div></div>
<div class="row"><span>' . $LANG['form_price'] . ':</span><div><input type="text" name="price" value="' . $info->price_format . '" placeholder="' . sprintf( PRICE_FORMAT, \site\utils::money_format( 0.00 ) ) . '" /></div> </div>
<div class="row"><span>' . $LANG['form_credits'] . ':</span><div><input type="number" name="credits" min="0" value="' . $info->credits . '" /></div> </div>

<div class="row"><span>' . $LANG['form_image'] . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . \query\main::payment_plan_avatar( $info->image ) . '" class="avt" alt="" style="display: table-cell; width:80px; height:80px; margin: 0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->image ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
echo '</div>
</div>

<input type="file" name="logo" /></div> </div>
<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="text" style="min-height:100px;">' . $info->description . '</textarea></div></div>
<div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" ' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish">' . $LANG['msg_pubpplan'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['pmts_editplan_button'] . '</button>

</form>

</div>


<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['pmts_plan_infos_title'] . '</h2>

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

/** LIST OF PLANS */

case 'plan_view':

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . $LANG['pmts_plans_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=payments.php&amp;action=plan_add" class="btn">' . $LANG['payments_plan_add'] . '</a>';
echo '</div>';

if( !empty( $LANG['pmts_plans_subtitle'] ) ) {
  echo '<span>' . $LANG['pmts_plans_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_payment_plan( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::payment_plan_action( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'payments_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_payment_plan( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

  if( isset( $_GET['id'] ) )
  if( actions::payment_plan_action( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['payments_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="plan_view" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'price' => $LANG['order_price'], 'price desc' => $LANG['order_price_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="plan_view" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['pmts_plans_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = \query\payments::have_plans( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=payments.php&amp;action=plan_view">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=payments.php&amp;action=plan_view" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>

<div class="bulk_options">

  <button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button> ';

  echo $LANG['action'] . ': ';
  echo '<select name="action">';
  foreach( array( 'publish' => $LANG['publish'], 'unpublish' => $LANG['unpublish'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
  echo '</select>
  <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>

</div>';

foreach( \query\payments::while_plans( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::payment_plan_avatar( $item->image ) . '" alt="" />
  <div class="info-div"><h2>' . ( $item->visible ? '<span class="msg-success">' . $LANG['published'] . '</span>' : '<span class="msg-error">' . $LANG['notpublished'] . '</span>' ) . ' ' . $item->name . ' (' . $item->price_format . ')</h2>
  ' . $LANG['form_credits'] . ': <b>' . $item->credits . '</b>
  </div></div>

  <div style="clear:both;"></div>

  <div class="options">
  <a href="?route=payments.php&amp;action=plan_edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>
  <a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? $LANG['publish'] : $LANG['unpublish'] ) . '</a>
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

  echo '<div class="a-alert">' . $LANG['no_pmtsplans_yet'] . '</div>';

}

break;

/** VIEW INVOICE */

case 'invoice_view':

if( !ab_to( array( 'payments' => 'view' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['pmts_viewinv_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $invoice_exists = \query\payments::invoice_exists( $_GET['id'] ) ) ) {

$info = \query\payments::invoice_infos( $_GET['id'] );

if( ab_to( array( 'payments' => 'edit' ) ) ) {
echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $GLOBALS['me']->is_admin ) echo '<li><a href="?route=suggestions.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
if( $info->paid ) {
  echo '<li><a href="?route=payments.php&amp;action=list&amp;type=unpaid&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['set_as_unpaid'] . '</a></li>';
} else {
  echo '<li><a href="?route=payments.php&amp;action=list&amp;type=paid&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['set_as_paid'] . '</a></li>';
}
if( $info->delivered ) {
  echo '<li><a href="?route=payments.php&amp;action=list&amp;type=undelivered&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['set_as_undelivered'] . '</a></li>';
} else {
  echo '<li><a href="?route=payments.php&amp;action=list&amp;type=delivered&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . $LANG['set_as_delivered'] . '</a></li>';
}
echo '</ul>
</div>';
}

}

echo '<a href="?route=payments.php&amp;action=list" class="btn">' . $LANG['payments_invoices'] . '</a>
</div>';

if( !empty( $LANG['pmts_viewinv_subtitle'] ) ) {
  echo '<span>' . $LANG['pmts_viewinv_subtitle'] . '</span>';
}

echo '</div>';

if( $invoice_exists ) {

$_SESSION['payments_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['pmts_form_gateway'] . ':</span><div>' . $info->gateway . '</div></div>
<div class="row"><span>' . $LANG['pmts_form_transid'] . ':</span><div>' . $info->transaction_id . '</div></div>
<div class="row"><span>' . $LANG['pmts_form_state'] . ':</span><div>' . $info->state . '</div></div>
<div class="row"><span>' . $LANG['pmts_form_paid'] . ':</span><div>' . ( $info->paid ? $LANG['yes'] : $LANG['no'] ). '</div></div>
<div class="row"><span>' . $LANG['pmts_form_delivered'] . ':</span><div>' . ( $info->delivered ? $LANG['yes'] : $LANG['no'] ). '</div></div>
<div class="row"><span>' . $LANG['form_details'] . ':</span><div>' . $info->details . '</div></div>
<div class="row"><span>' . $LANG['pmts_form_items'] . ':</span><div>

<ul style="list-style-type: none;">';

foreach( $info->items as $line ) {
  echo '<li>' . ( is_array( $line ) ? htmlspecialchars( implode( ' / ', $line ) ) : htmlspecialchars( $line ) ) . '</li>';
}
echo '</ul>

</div></div>

<div class="row"><span>' . $LANG['last_update_by']  . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['last_update_on'] . ':</span><div>' . $info->last_update . '</div></div>
<div class="row"><span>' . $LANG['owner'] . ':</span><div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span><div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF TRANSACTIONS */

default:

if( !ab_to( array( 'payments' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['pmts_invoices_title'] . '</h2>';

if( !empty( $LANG['pmts_invoices_subtitle'] ) ) {
  echo '<span>' . $LANG['pmts_invoices_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_payment( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

  if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
  if( actions::action_payment( $_POST['action'], array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'payments_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_payment( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

} else if( in_array( $_GET['type'], array( 'paid', 'unpaid', 'delivered', 'undelivered' ) ) ) {

  if( isset( $_GET['id'] ) )
  if( actions::action_payment( $_GET['type'], $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['payments_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="list" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'price' => $LANG['order_price'], 'price desc' => $LANG['order_price_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select> ';

echo '<select name="view">';
foreach( array( '' => $LANG['all_invoices'], 'paid' => $LANG['view_paid'], 'unpaid' => $LANG['view_unpaid'], 'delivered' => $LANG['view_delivered'], 'undelivered' => $LANG['view_undelivered'], 'undeliveredpayments' => $LANG['view_paidandundelivered'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['pmts_trans_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = \query\payments::have_invoices( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=payments.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=payments.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'payments' => 'edit' ) );
$ab_del = $GLOBALS['me']->is_admin;

if( $ab_edt ) {

echo '<div class="bulk_options">';

  if( $GLOBALS['me']->is_admin ) echo '<button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button> ';

    echo $LANG['action'] . ':
    <select name="action">';
    foreach( array( 'paid' => $LANG['paid'], 'unpaid' => $LANG['unpaid'], 'delivered' => $LANG['delivered'], 'undelivered' => $LANG['undelivered'] ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . $LANG['set_all'] . '</button>';

echo '</div>';

}

foreach( \query\payments::while_invoices( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />

  <div style="display: table;">

  <img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />

  <div class="info-div">

  <h2>' . ( $item->paid ? '<span class="msg-success">' . $item->state . '</span>' : '<span class="msg-alert">' . $item->state . '</span>' ) . ' ' . $item->user_name . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>

  <div class="info-bar">' . $LANG['form_amount'] . ': ' . $item->price_format . ' <span class="info"><span>' . $LANG['pmts_form_gateway'] . ': ' . $item->gateway . ' <br /> ' . $item->details . '</span></span> / ' . $LANG['pmts_form_delivered'] . ': ' . ( $item->delivered ? '<span class="msg-success">' . $LANG['yes'] . '</span>' : '<span class="msg-error">' . $LANG['no'] . '</span>' ) . '</div>

  </div></div>

  <div style="clear:both;"></div>

  <div class="options">
  <a href="?route=payments.php&amp;action=invoice_view&amp;id=' . $item->ID . '">' . $LANG['view'] . '</a>';
  if( $ab_edt ) {
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->paid ? 'unpaid' : 'paid' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->paid ? $LANG['unpaid'] : $LANG['paid'] ). '</a>';
  echo '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->delivered ? 'undelivered' : 'delivered' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->delivered ? $LANG['undelivered'] : $LANG['delivered'] ). '</a>';
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

  echo '<div class="a-alert">' . $LANG['no_pmtstrans_yet'] . '</div>';

}

break;

}