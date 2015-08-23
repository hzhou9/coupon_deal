<?php

//

if( !( $template_widgets = template::have_widgets() ) || !$GLOBALS['me']->is_admin ) {
  die;
}

switch( $_GET['action'] ) {

/** EDIT WIDGET */

case 'edit':

echo '<div class="title">

<h2>' . $LANG['widgets_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=widgets.php&amp;action=list" class="btn">' . $LANG['widgets_view'] . '</a>
</div>';

if( !empty( $LANG['widgets_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['widgets_edit_subtitle'] . '</span>';
}

echo '</div>';

if( isset( $_GET['id'] ) && \query\main::widget_exists( $_GET['id'] ) ) {

$info = \query\main::widget_infos( $_GET['id'] );

if( $widget = widgets::widget_from_id( $info->widget_id ) ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && check_csrf( $_POST['csrf'], 'widgets2_csrf' ) ) {

  if( isset( $_POST['title'] ) && isset( $_POST['position'] ) )
  if( actions::edit_widget( $_GET['id'], array( 'title' => $_POST['title'], 'position' => $_POST['position'], 'text' => ( isset( $_POST['text'] ) ? $_POST['text'] : '' ), 'type' => ( isset( $_POST['type'] ) ? $_POST['type'] : '' ), 'order' => ( isset( $_POST['orderby'] ) ? $_POST['orderby'] : '' ), 'limit' => ( isset( $_POST['limit'] ) ? $_POST['limit'] : '' ), 'allow_html' => ( !empty( $widget->allow_html ) && isset( $_POST['html'] ) ? 1 : 0 ), 'mobi_view' => ( isset( $_POST['mobi_view'] ) ? 1 : 0 ) ) ) ) {

  $info = \query\main::widget_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';

  } else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

$csrf = $_SESSION['widgets2_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . $LANG['form_title'] . ':</span><div><input type="text" name="title" value="' . $info->title . '" /></div></div>
<div class="row"><span>' . $LANG['form_position'] . ':</span><div><input type="number" name="position" value="' . $info->position . '" min="1" /></div></div>';

if( !empty( $widget->allow_text ) ) echo '<div class="row"><span>' . $LANG['form_text'] . ':</span><div><textarea name="text">' . $info->text . '</textarea></div></div>';

if( !empty( $widget->allow_html ) ) echo '<div class="row"><span></span><div><input type="checkbox" name="html" id="allow_html"' . ( $info->html ? ' checked' : '' ) . ' /> <label for="allow_html">' . $LANG['widget_allow_html'] . '</label></div></div>';

if( $type = $widget->allow_show ) {
echo '<div class="row"><span>' . $LANG['form_show_only'] . ':</span><div><select name="type">';
foreach( $type as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $info->type ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div></div>';
}

if( $orderby = $widget->allow_orderby ) {
echo '<div class="row"><span>' . $LANG['form_orderby'] . ':</span><div><select name="orderby">';
foreach( $orderby as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $info->orderby ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div></div>';
}

if( !empty( $widget->allow_limit ) ) echo '<div class="row"><span>' . $LANG['form_limit'] . ':</span><div><input type="number" name="limit" value="' . $info->limit . '" min="1"' . ( !empty( $widget->max_limit ) ? ' max="' . $widget->max_limit . '"' : '' ) . ' /></div></div>';

echo '<div class="row"><span>' . $LANG['form_mobilev'] . ':</span><div><input type="checkbox" name="mobi_view" id="mobi_view"' . ( $info->mobile_view ? ' checked' : '' ) . ' /> <label for="mobi_view">' . $LANG['msg_widget_mobilev'] . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['widgets_edit_button'] . '</button>

</form>

</div>';

} else {

  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else {

  echo '<div class="a-error">' . $LANG['invalid_id'] . '</div>';

}

break;

/** LIST OF WIDGETS */

default:

if( isset( $_GET['zone'] ) && in_array( $_GET['zone'], array_keys( $template_widgets ) ) ) {
  list( $zone_id, $zone ) = array( $_GET['zone'], $template_widgets[$_GET['zone']] );
} else {
  list( $zone_id, $zone ) = array( key( $template_widgets ), current( $template_widgets ) );
}

echo '<div class="title">

<h2>' . $LANG['widgets_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="widgets.php" />
' . $LANG['widgets_zones'] . ': <select name="zone">';
foreach( $template_widgets as $ID => $widgets )echo '<option value="' . $ID . '"' . ( $ID == $zone_id ? ' selected' : '' ) . '>' . $widgets['name'] . '</option>';
echo '</select>
<button class="btn">' . $LANG['widgets_viewzone'] . '</button>
</form>

</div>';

if( !empty( $LANG['widgets_subtitle'] ) ) {
  echo '<span>' . $LANG['widgets_subtitle'] . '</span>';
}

echo '</div>';

if( isset( $_GET['token'] ) && isset( $_GET['id'] ) && check_csrf( $_GET['token'], 'widgets_csrf' ) ) {

  if( isset( $_GET['add'] ) ) {

  if( $widget_info = widgets::widget_from_id( $_GET['id'] ) )

  if( actions::add_widget( $zone_id, $_GET['id'], array( 'title' => $widget_info->name, 'file' => $widget_info->file, 'limit' => ( isset( $widget_info->def_limit ) ? $widget_info->def_limit : 10 ), 'text' => ( isset( $widget_info->text ) ? $widget_info->text : '' ) ) ) )
  echo '<div class="a-success">' . $LANG['msg_added']  . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error']  . '</div>';

  } else if( isset( $_GET['delete'] ) ) {

  if( actions::delete_widget( $zone_id, $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted']  . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error']  . '</div>';

  }

}

$token = $_SESSION['widgets_csrf'] = \site\utils::str_random(10);

/* */

$zone_widgets = \query\main::show_widgets( $zone_id, '../' );

/* */

echo '<div class="form-table">

<ul class="elements-list el-two">

<li class="head">' . $LANG['widgets_available'] . '</li>';

$available = widgets::available_list();

foreach( $available as $ID => $widget ) {

  echo '<li>
  <div class="info-div">' . htmlspecialchars( $widget['name'] ) . '</div>
  <div class="options">
  <a href="?route=widgets.php&amp;zone=' . $zone_id . '&amp;id=' . $ID . '&amp;add&amp;token=' . $token . '">' . $LANG['add'] . '</a>
  </div>';
  if( !empty( $widget['description'] ) ) {
    echo '<div style="color: #000; font-size: 13px; margin-top: 10px;">' . $widget['description'] . '</div>';
  }
  echo '</li>';

}

echo '</ul>

<ul class="elements-list el-two">

<li class="head">' . htmlspecialchars( $zone['name'] ) . '
<span>' . ( empty( $zone['description'] ) ? $LANG['widgets_no_description'] : htmlspecialchars( $zone['description'] ) ) . '</span></li>';

if( empty( $zone_widgets ) ) {
  echo '<li>' . $LANG['widgets_no_widgets'] . '</li>';
} else {

foreach( $zone_widgets as $widget ) {

  echo '<li>
  <div class="info-div">' . widgets::widget_from_id( $widget['widget_id'] )->name . '</div>
  <div class="options">
  <a href="?route=widgets.php&amp;action=edit&amp;id=' . $widget['ID'] . '">' . $LANG['edit'] . '</a>
  <a href="?route=widgets.php&amp;zone=' . $zone_id . '&amp;id=' . $widget['ID'] . '&amp;delete&amp;token=' . $token . '">' . $LANG['delete'] . '</a>
  </div>
  </li>';

}

}

echo '</ul>

</div>';

break;

}