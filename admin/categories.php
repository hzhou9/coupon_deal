<?php

switch( $_GET['action'] ) {

/** ADD CATEGORY */

case 'add':

if( !ab_to( array( 'categories' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . ( isset( $_GET['subcat'] ) ? $LANG['subcategories_add_title'] : $LANG['categories_add_title'] ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=categories.php&amp;action=list" class="btn">' . $LANG['categories_view'] . '</a>
</div>';

if( !empty( $LANG['categories_add_subtitle'] ) || isset( $_GET['subcat'] ) && !empty( $LANG['subcategories_add_subtitle'] ) ) {
  echo '<span>' . ( isset( $_GET['subcat'] ) ? $LANG['subcategories_add_subtitle'] : $LANG['categories_add_subtitle'] ) . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'categories_csrf' ) ) {

if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) ){
    $connect = '|';
    $connectids = array();
    if(isset($_POST['category'])){
    foreach($_POST['category'] as $index=>$val){
        $v = intval($val);
        if($v > 0){
            $connect .= $v.'|';
            $connectids[] = $v;
        }
    }
    }

    if( $id = actions::add_category( array( 'istop'=>isset( $_GET['subcat'] )?0:1, 'connect'=>$connect, 'name' => $_POST['name'], 'description' => $_POST['text'], 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) ){
        //add new connections
        foreach($connectids as $connectid){
            $info2 = \query\main::category_infos( $connectid );
            $connect = $info2->connect.$id."|";
            actions::edit_category( $connectid, array( 'connect' => $connect ) );
        }
        
        echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
    }
    else{
        echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';
    }

}
    
}

$csrf = $_SESSION['categories_csrf'] = \site\utils::str_random(10);
$categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'cats' ) );
echo '<script>
        var lastnum = 0;
        function addSubcategory(){
            lastnum++;
            var categorysel = $("<select name=\"category["+lastnum+"]\"><option value=\"0\" selected>' . $LANG['no_category_select'] . '</option>';
foreach( $categories_while as $cat )echo '<option value=\"' . $cat->ID . '\">' . $cat->name . '</option>';
echo '</select>");
            $("[name=div_category]").append(categorysel);
        }
</script><div class="form-table">

<form action="#" method="POST" autocomplete="off">';

if( isset( $_GET['subcat'] ) ) {
  echo '<div class="row"><span>' . $LANG['form_subcategoryfor'] . ':</span>
  <div name="div_category"><input type="button" value="Add" onclick="addSubcategory();"><select name="category[0]">';
  echo '<option value="0">' . $LANG['no_category_select'] . '</option>';
  foreach( $categories_while as $cat ) echo '<option value="' . $cat->ID . '"' . ( isset( $_GET['cat'] ) && (int) $_GET['cat'] === $cat->ID ? ' selected' : '' ) . '>' . $cat->name . '</option>';
  echo '</select></div></div>';
}

echo '<div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" /></div></div>

<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="text" style="min-height:100px;"></textarea></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc"></textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . ( isset( $_GET['subcat'] ) ? $LANG['subcategories_add_button'] : $LANG['categories_add_button'] ) . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

break;

/** EDIT CATEGORY */

case 'edit':

if( !ab_to( array( 'categories' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . $LANG['categories_edit_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $category_exists = \query\main::category_exists( $_GET['id'] ) ) ) {

$info = \query\main::category_infos( $_GET['id'] );

$ab_del = ab_to( array( 'categories' => 'delete' ) );

if( $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . $LANG['options'] . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=categories.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a></li>';
echo '</ul>
</div>';

}

}

echo '<a href="?route=categories.php&amp;action=list" class="btn">' . $LANG['categories_view'] . '</a>
</div>';

if( !empty( $LANG['categories_edit_subtitle'] ) ) {
  echo '<span>' . $LANG['categories_edit_subtitle'] . '</span>';
}

echo '</div>';

if( $category_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'categories_csrf' ) ) {

if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_desc'] ) ){
    $connect = '|';
    $connectids = array();
    foreach($_POST['category'] as $index=>$val){
        $v = intval($val);
        if($v > 0){
            $connect .= $v.'|';
            $connectids[] = $v;
        }
    }
    
  if( actions::edit_category( $_GET['id'], array( 'name' => $_POST['name'], 'description' => $_POST['text'], 'connect' => $connect, 'meta_title' => $_POST['meta_title'], 'meta_desc' => $_POST['meta_desc'] ) ) ) {

      $connectids_old = $info->connectids;
      //remove these non-exist connections
      foreach($connectids_old as $connectid){
          if (!in_array($connectid, $connectids)){
              $info2 = \query\main::category_infos( $connectid );
              $connect = str_replace("|".$_GET['id']."|", "|", $info2->connect);
              actions::edit_category( $connectid, array( 'connect' => $connect ) );
          }
      }
      //add new connections
      foreach($connectids as $connectid){
          if (!in_array($connectid, $connectids_old)){
              $info2 = \query\main::category_infos( $connectid );
              if(!in_array(intval($_GET['id']), $info2->connectids)){
              $connect = $info2->connect.$_GET['id']."|";
              actions::edit_category( $connectid, array( 'connect' => $connect ) );
              }
          }
      }
      
  $info = \query\main::category_infos( $_GET['id'] );

  echo '<div class="a-success">' . $LANG['msg_saved'] . '&nbsp;<input type="button" value="Back" onclick="history.go(-2);"></div>';

  } else{
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';
  }
}

}

$_SESSION['categories_csrf'] = $csrf;
$categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => ($info->is_subcat?'cats':'subcats') ) );

echo '<script>
var lastnum = '.count($info->connectids).';
    function addSubcategory(){
        lastnum++;
        var categorysel = $("<select name=\"category["+lastnum+"]\"><option value=\"0\" selected>' . $LANG['no_category_select'] . '</option>';
foreach( $categories_while as $cat )echo '<option value=\"' . $cat->ID . '\">' . $cat->name . '</option>';
echo '</select>");
        $("[name=div_category]").append(categorysel);
    }

</script><div class="form-table">

<form action="#" method="POST">';

    //var_dump($info);
    
    echo '<div class="row"><span>' . ($info->is_subcat?$LANG['form_subcategoryfor']:$LANG['form_subcategoryown']) . ':</span><div name="div_category"><input type="button" value="Add" onclick="addSubcategory();">';
    
    for($i=1; $i<=count($info->connectids); $i++){
    
echo '<select name="category['.$i.']"><option value="0">' . $LANG['no_category_select'] . '</option>';
foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '"' . ( $info->connectids[$i-1] === $cat->ID ? ' selected' : '' ) . '>' . $cat->name . '</option>';
echo '</select>';

    }

    echo '<select name="category[0]"><option value="0" selected>' . $LANG['no_category_select'] . '</option>';
    foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
    echo '</select>';
    
echo '</div></div><div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $info->name . '" /></div></div>

<div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="text" style="min-height:100px;">' . $info->description . '</textarea></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>' . $LANG['pages_title_meta'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="' . $info->meta_title . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc">' . $info->meta_description . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['categories_edit_button'] . '</button>

<a href="#" id="modify_mt_but">' . $LANG['pages_editmt_button'] . '</a>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . $LANG['categories_infos_title'] . '</h2>

</div>';

echo '<div class="infos-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . $LANG['page_url'] . ':</span> <div><a href="' . $info->link . '" target="_blank">' . $info->link . '</a></div></div>
<div class="row"><span>' . $LANG['added_by'] . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . $LANG['added_on'] . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else {

  echo '<div class="a-error">Invalid ID.</div>';

}

break;

/** LIST OF CATEGORIES */

default:

if( !ab_to( array( 'categories' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['categories_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( $ab_add = ab_to( array( 'categories' => 'add' ) ) )echo '<a href="?route=categories.php&amp;action=add&subcat" class="btn">' . $LANG['subcategories_add'] . '</a>';
if( $ab_add )echo ' <a href="?route=categories.php&amp;action=add" class="btn">' . $LANG['categories_add'] . '</a>';
echo '</div>';

if( !empty( $LANG['categories_subtitle'] ) ) {
  echo '<span>' . $LANG['categories_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'categories_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( actions::delete_category( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'categories_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( actions::delete_category( $_GET['id'] ) )
  echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

}

$csrf = $_SESSION['categories_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off" novalidate>

<input type="hidden" name="route" value="categories.php" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'name' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

<input type="hidden" name="action" value="list" />

<button class="btn">' . $LANG['view'] . '</button>

</form>

</div>';


$p = \query\main::have_categories( $options = array( 'per_page' => 10 ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=categories.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

$ab_edt  = ab_to( array( 'categories' => 'edit' ) );
$ab_del  = ab_to( array( 'categories' => 'delete' ) );

if( $ab_del ) {
echo '<div class="bulk_options">
  <button class="btn" name="delete" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete_all'] . '</button>
</div>';
}

foreach( \query\main::while_categories( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <input type="checkbox" name="id[' . $item->ID . ']" />
<div class="info-div"><h2>[' . ($item->is_subcat?'Sub':'Top') .']&nbsp;'. $item->name . '&nbsp;('.count($item->connectids).')</h2></div>';

  echo '<div class="options">';
  if( $ab_edt ) echo '<a href="?route=categories.php&amp;action=edit&amp;id=' . $item->ID . '">' . $LANG['edit'] . '</a>';
  if( $ab_add && !$item->is_subcat ) echo '<a href="?route=categories.php&amp;action=add&amp;subcat&amp;cat=' . $item->ID . '">' . $LANG['subcategories_add'] . '</a>';
  if( $ab_del ) echo '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . $LANG['delete_msg'] . '">' . $LANG['delete'] . '</a>';
  echo '</div>';

  echo '</li>';

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

  echo '<div class="a-alert">' . $LANG['no_categories_yet'] . '</div>';

}

break;

}