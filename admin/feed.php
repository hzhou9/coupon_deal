<?php

if( !ab_to( array( 'feed' => 'view' ) ) ) die;

switch( $_GET['action'] ) {

/** IMPORT COUPONS */

case 'import':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . $LANG['feed_istores_title'] . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=feed.php&amp;action=stores" class="btn">' . $LANG['stores_view'] . '</a>
<a href="?route=feed.php&amp;action=coupons" class="btn">' . $LANG['coupons_view'] . '</a>
</div>';

if( !empty( $LANG['feed_istores_subtitle'] ) ) {
  echo '<span>' . $LANG['feed_istores_subtitle'] . '</span>';
}

echo '</div>';

if( ( $feeds = \query\main::stores( array( 'show' => 'feed' ) ) ) === 0 ) {

echo '<div class="a-error">' . $LANG['feed_importe1']. '</div>';

} else {

echo '<div class="a-alert">' . sprintf( $LANG['feed_importnr_stores'], $feeds ) . '</div>';

echo '<div class="form-table">

<form action="#" method="GET" autocomplete="off">

<input type="hidden" name="route" value="feed.php" />
<input type="hidden" name="action" value="import2" />
<div class="row"><span>' . $LANG['feed_from_addfrom'] . ':</span><div><input type="date" name="date" value="' . date( 'Y-m-d', \query\main::get_option('lfeed_check') ) . '" style="width: 80%" /><input type="time" name="hour" value="' . date( 'H:i', \query\main::get_option('lfeed_check') ) . '" style="width: 20%" /></div></div>
<div class="row"><span></span><div><input type="checkbox" name="import_expired" value="yes" id="import_expired"' . ( \query\main::get_option('feed_iexpc') ? ' checked' : '' ) . ' /> <label for="import_expired">' . $LANG['feed_msg1'] . '</label></div></div>
<button class="btn">' . $LANG['feed_icoupons_button'] . '</button>

</form>

</div>';

}

break;

/** IMPORT COUPONS AUTOMATICALLY */

case 'import2':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && check_csrf( $_POST['token'], 'feed_import_csrf' ) ) {

include 'includes/feed.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  $ids = array();
  foreach( \query\main::while_stores( array( 'max' => 0, 'show' => 'feed' ) ) as $store ) {
    $ids[] = $store->feedID;
  }

  $csuc = $cusuc = $cerr = $cuerr = 0;

  if( !empty( $ids ) ) {

  $last_check = \query\main::get_option( 'lfeed_check' );

  /*

  UPDATE COUPONS

  */

  if( (int) \query\main::get_option( 'feed_moddt' ) !== 0 ) {

  try {

    $coupons = $feed->coupons( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'update' => \site\utils::timeconvert( ( $_GET['date'] . ', ' . $_GET['hour'] ), 'America/New_York' ) ) );

    if( !empty( $coupons['Count'] ) ) {

    for( $cp = 1; $cp <= ceil( $coupons['Count'] / 10 ); $cp++ ) {

    if( $cp != 1 ) {
      $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
    }

    foreach( $coupons['List'] as $coupon ) {

    if( ( $couponi = admin_query::coupon_imported( $coupon->ID ) ) && actions::edit_item2( $couponi->ID, array( 'name' => $coupon->Title, 'link' => $coupon->URL, 'code' => $coupon->Code, 'description' => $coupon->Description, 'tags' => $coupon->Tags, 'start' => $coupon->Start_Date, 'end' => $coupon->End_Date ) ) ) {
      $cusuc++;
    } else {
      $cuerr++;
    }

    }

    usleep( 500000 ); // let's put a break after every page, 500 000 microseconds. that means a half of a second

    }

    }

  }

  catch( Exception $e ) { }

  }

  /*

  IMPORT COUPONS

  */

  try {

    $coupons = $feed->coupons( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'view' => (!isset( $_GET['import_expired'] ) || $_GET['import_expired'] !== 'yes' ? 'active' : ''), 'date' => \site\utils::timeconvert( ( $_GET['date'] . ', ' . $_GET['hour'] ), 'America/New_York' ) ) );

    if( !empty( $coupons['Count'] ) ) {

    for( $cp = 1; $cp <= ceil( $coupons['Count'] / 10 ); $cp++ ) {

    if( $cp != 1 ) {
      $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
    }

    foreach( $coupons['List'] as $coupon ) {

    if( !admin_query::coupon_imported( $coupon->ID ) && ( $store_id = admin_query::store_imported( $coupon->Store_ID ) ) && actions::add_item( array( 'feedID' => $coupon->ID, 'store' => $store->ID, 'category' => $store->catID, 'popular' => 0, 'exclusive' => 0, 'name' => $coupon->Title, 'link' => $coupon->URL, 'code' => $coupon->Code, 'description' => $coupon->Description, 'tags' => $coupon->Tags, 'cashback' => 0, 'start' => $coupon->Start_Date, 'end' => $coupon->End_Date, 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) ) {
      $csuc++;
    } else {
      $cerr++;
    }

    }

    usleep( 500000 ); // let's put a break after every page, 500 000 microseconds. that means a half of a second

    }

    }

    actions::set_option( array( 'lfeed_check' => time() ) ); // update time for last feed check

  }

  catch( Exception $e ) { }

  echo '<div class="a-message">' . $LANG['msg_feed_finished'] . '</div>';

  echo '<ul class="announce-box">
  <li>' . $LANG['feed_coupons_sucimp'] . ':<b>' . $csuc . '</b></li>
  <li>' . $LANG['feed_coupons_errimp'] . ':<b>' . $cerr . '</b></li>
  <li>' . $LANG['feed_coupons_sucupt'] . ':<b>' . $cusuc . '</b></li>
  <li>' . $LANG['feed_coupons_errupt'] . ':<b>' . $cuerr . '</b></li>
  </ul>';

  }

}

catch ( Exception $e ) {
  echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

}

$csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {

echo '<script>
window.onload = function() {
setTimeout(function(){
  document.forms[\'import_now\'].submit();
}, 1000);
}
</script>';

echo '<div style="text-align: center;">
  <h2>' . $LANG['feed_import_dleave'] . '</h2>
</div>

<form id="import_now" action="#" method="POST">
  <input type="hidden" name="token" value="' . $csrf . '" />
</form>';

} else {
  echo '<a href="#" class="btn" onclick="window.history.go(-2)">' . $LANG['back'] . '</a>';
}

break;

/** PREVIEW COUPON */

case 'preview_coupon':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

include 'includes/feed.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  $feed->export_as = 'object';

  try {

    $coupon = $feed->coupon( $_GET['id'] );

    echo '<div class="title">

    <h2>' . $LANG['feed_picoupon_title'] . '</h2>

    <div style="float:right; margin: 0 2px 0 0;">
    <a href="?route=feed.php&amp;action=coupons" class="btn">' . $LANG['coupons_view'] . '</a>
    </div>';

    if( !empty( $LANG['feed_picoupon_subtitle'] ) ) {
      echo '<span>' . $LANG['feed_picoupon_subtitle'] . '</span>';
    }

    echo '</div>';

    if( !( $store = admin_query::store_imported( $coupon->Store_ID ) ) ) {
      echo '<div class="a-error">' . $LANG['msg_feed_snotimp'] . '</div>';
    } else if( admin_query::coupon_imported( $coupon->ID ) ) {
      echo '<div class="a-alert">' . $LANG['msg_feed_cimported'] . '</div>';
    }

    $csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

    echo '<div class="form-table">

    <form action="?route=feed.php&amp;action=add_coupon" method="POST" autocomplete="off">

    <div class="row"><span>' . $LANG['form_category'] . ':</span>
    <div><select name="category">';
    foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
      echo '<optgroup label="' . $cat['infos']->name . '">';
      echo '<option value="' . $cat['infos']->ID . '"' . ( $store->catID == $cat['infos']->ID ? ' selected' : '' ) . '>' . $cat['infos']->name . '</option>';
      if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
          echo '<option value="' . $subcat->ID . '"' . ( $store->catID == $subcat->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
        }
      }
      echo '</optgroup>';
    }
    echo '</select></div></div>

    <div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $coupon->Title . '" required /></div></div>
    <div class="row"><span>' . $LANG['form_code'] . ':</span><div><input type="text" name="code" value="' . $coupon->Code . '" /></div></div>
    <div class="row"><span>' . $LANG['form_coupon_url'] . ':</span><div><input type="checkbox" name="coupon_ownlink" value="1" id="ownlink"' . ( empty( $coupon->URL ) ? ' checked' : '' ) . ' /> <label for="ownlink">' . $LANG['coupons_use_link'] . '</label> <br />
    <input type="text" name="link" value="' . ( !empty( $coupon->URL ) ? $coupon->URL : 'http://' ) . '"' . ( empty( $coupon->URL ) ? ' style="display: none;"' : '' ) . ' />
    </div></div>
    <div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="description">' . $coupon->Description . '</textarea></div></div>
    <div class="row"><span>' . $LANG['form_tags'] . ':</span><div><input type="text" name="tags" value="' . $coupon->Tags . '" /></div></div>
    <div class="row"><span>' . $LANG['form_start_date'] . ':</span><div><input type="date" name="start[date]" value="' . date( 'Y-m-d', strtotime( $coupon->Start_Date ) ) . '" style="width: 80%" /><input type="time" name="start[hour]" value="' . date( 'H:i', strtotime( $coupon->Start_Date ) ) . '" style="width: 20%" /></div></div>
    <div class="row"><span>' . $LANG['form_end_date'] . ':</span><div><input type="date" name="end[date]" value="' . date( 'Y-m-d', strtotime( $coupon->End_Date ) ) . '" style="width: 80%" /><input type="time" name="end[hour]" value="' . date( 'H:i', strtotime( $coupon->End_Date ) ) . '" style="width: 20%" /></div></div>
    <div class="row"><span>' . $LANG['form_addto'] . ':</span><div>
    <input type="checkbox" name="popular" id="popular" /> <label for="popular">' . $LANG['coupons_addpopular'] . '</label> <br />
    <input type="checkbox" name="exclusive" id="exclusive" /> <label for="exclusive">' . $LANG['coupons_addexclusive'] . '</label></div></div>

    <input type="hidden" name="csrf" value="' . $csrf . '" />
    <input type="hidden" name="storeID" value="' . $coupon->Store_ID . '" />
    <input type="hidden" name="feedID" value="' . $coupon->ID . '" />
    <button class="btn">' . $LANG['import'] . '</button>

    </form>

    </div>';

  }

  catch ( Exception $e ){
    echo '<div class="a-alert">' . $e->getMessage() . '</div>';
  }

}

catch ( Exception $e ) {
  echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

break;

/** ADD COUPON */

case 'add_coupon':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( !isset( $_POST['feedID'] ) || !isset( $_POST['storeID'] ) ) {
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';
} else if( admin_query::coupon_imported( $_POST['feedID'] ) ) {
  echo '<div class="a-error">' . $LANG['msg_feed_cimported'] . '</div>';
} else if( ! ( $store = admin_query::store_imported( $_POST['storeID'] ) ) ) {
  echo '<div class="a-error">' . $LANG['msg_feed_snotimp'] . '</div>';
}

else

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

  if( isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['code'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_POST['start'] ) && isset( $_POST['end'] ) )
  if( actions::add_item( array( 'feedID' => $_POST['feedID'], 'store' => $store->ID, 'category' => $_POST['category'], 'popular' => ( isset( $_POST['popular'] ) ? true : false ), 'exclusive' => ( isset( $_POST['exclusive'] ) ? true : false ), 'name' => $_POST['name'], 'link' => ( !isset( $_POST['coupon_ownlink'] ) && isset( $_POST['link'] ) && filter_var( $_POST['link'], FILTER_VALIDATE_URL ) ? $_POST['link'] : '' ), 'code' => $_POST['code'], 'description' => $_POST['description'], 'tags' => $_POST['tags'], 'cashback' => 0, 'start' => $_POST['start']['date'] . ', ' . $_POST['start']['hour'], 'end' => $_POST['end']['date'] . ', ' . $_POST['end']['hour'], 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

echo '<a href="#" class="btn" onclick="window.history.go(-2)">' . $LANG['back'] . '</a>';

break;

/** IMPORT COUPONS */

case 'import_coupons':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( !isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
  echo '<div class="a-error">' . $LANG['msg_feed_seltoimp'] . '</div>';
  echo '<a href="#" class="btn" onclick="window.history.go(-1)">' . $LANG['back'] . '</a>';

} else {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && check_csrf( $_POST['token'], 'feed_import_csrf' ) ) {

include 'includes/feed.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  $csuc = $cerr = 0;

  try {

    $coupons = $feed->coupons( $coupon_ids = array( 'ids' => implode( ',', array_keys( $_GET['id'] ) ) ) );

    if( !empty( $coupons['Count'] ) ) {

    for( $cp = 1; $cp <= ceil( $coupons['Count'] / 10 ); $cp++ ) {

    if( $cp != 1 ) {
      $coupons = $feed->stores( array_merge( array( 'page' => $cp ), $coupon_ids ) );
    }

    foreach( $coupons['List'] as $coupon ) {

    if( !admin_query::coupon_imported( $coupon->ID ) && ( $store = admin_query::store_imported( $coupon->Store_ID ) ) && actions::add_item( array( 'feedID' => $coupon->ID, 'store' => $store->ID, 'category' => $store->catID, 'popular' => 0, 'exclusive' => 0, 'name' => $coupon->Title, 'link' => $coupon->URL, 'code' => $coupon->Code, 'description' => $coupon->Description, 'tags' => $coupon->Tags, 'cashback' => 0, 'start' => $coupon->Start_Date, 'end' => $coupon->End_Date, 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) ) {
      $csuc++;
    } else {
      $cerr++;
    }

    }

    }

    }

  }

  catch ( Exception $e ){ }

  echo '<div class="a-message">' . $LANG['msg_feed_finished'] . '</div>';

  echo '<ul class="announce-box">
  <li>' . $LANG['feed_coupons_sucimp'] . ':<b>' . $csuc . '</b></li>
  <li>' . $LANG['feed_coupons_errimp'] . ':<b>' . $cerr . '</b></li>
  </ul>';

}

catch ( Exception $e ) {
  echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

}

$csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {

echo '<script>
window.onload = function() {
setTimeout(function(){
  document.forms[\'import_now\'].submit();
}, 1000);
}
</script>';

echo '<div style="text-align: center;">
  <h2>' . $LANG['feed_import_dleave'] . '</h2>
</div>

<form id="import_now" action="#" method="POST">
  <input type="hidden" name="token" value="' . $csrf . '" />
</form>';

} else {

echo '<a href="#" class="btn" onclick="window.history.go(-2)">' . $LANG['back'] . '</a>';

}

}

break;

/** PREVIEW STORE */

case 'preview_store':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

include 'includes/feed.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  $feed->export_as = 'object';

  try {

    $store = $feed->store( $_GET['id'] );

    echo '<div class="title">

    <h2>' . $LANG['feed_pistore_title'] . '</h2>

    <div style="float:right; margin: 0 2px 0 0;">
    <a href="?route=feed.php&amp;action=stores" class="btn">' . $LANG['stores_view'] . '</a>
    </div>';

    if( !empty( $LANG['feed_pistore_subtitle'] ) ) {
      echo '<span>' . $LANG['feed_pistore_subtitle'] . '</span>';
    }

    echo '</div>';

    if( admin_query::store_imported( $store->ID ) ) {
      echo '<div class="a-alert">' . $LANG['msg_feed_simported'] . '</div>';
    }

    $csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

    echo '<div class="form-table">

    <form action="?route=feed.php&amp;action=add_store" method="POST" enctype="multipart/form-data" autocomplete="off">

    <div class="row"><span>' . $LANG['form_user_id'] . ':</span><div data-search="user"><input type="text" name="user" value="' . ( !empty( $_GET['user'] ) ? (int)$_GET['user'] : $GLOBALS['me']->ID ) . '" required /><a href="#">S</a></div></div>

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

    <div class="row"><span>' . $LANG['form_name'] . ':</span><div><input type="text" name="name" value="' . $store->Name . '" required /></div></div>
    <div class="row"><span>' . $LANG['form_store_url'] . ':</span><div><input type="text" name="url" value="' . $store->URL . '" /></div></div>
    <div class="row"><span>' . $LANG['form_description'] . ':</span><div><textarea name="description">' . $store->Description . '</textarea></div></div>
    <div class="row"><span>' . $LANG['form_tags'] . ':</span><div><input type="text" name="tags" value="' . $store->Tags . '" /></div></div>

    <div class="row"><span>' . $LANG['form_logo'] . ':</span>

    <div>
    <div style="display: table; margin-bottom: 2px;"><img src="' . $store->Image . '" class="avt" alt="" style="display: table-cell; width:100px; height:50px; margin: 0 20px 5px 0;" />
    <div style="display: table-cell; vertical-align: middle; margin-left: 25px;"><input type="checkbox" name="import_logo" value="1" id="impimg"' . ( \query\main::get_option('feed_uppics') ? ' checked' : '' ) . ' /> <label for="impimg">' . $LANG['msg_feed_upload_timg'] . '</label></div>
    </div>

    <input type="file" name="logo" />
    </div> </div>

    <div class="row"><span>' . $LANG['form_addto'] . ':</span><div><input type="checkbox" name="popular" id="popular" /> <label for="popular">' . $LANG['coupons_addpopular'] . '</label></div></div>
    <div class="row"><span>' . $LANG['form_publish'] . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish">' . $LANG['msg_pubstore'] . '</label></div></div>

    <input type="hidden" name="csrf" value="' . $csrf . '" />
    <input type="hidden" name="feedID" value="' . $store->ID . '" />
    <input type="hidden" name="logo_url" value="' . $store->Image . '" />

    <button class="btn">' . $LANG['import'] . '</button>

    </form>

    </div>';

  }

  catch ( Exception $e ){
    echo '<div class="a-alert">' . $e->getMessage() . '</div>';
  }

}

catch ( Exception $e ) {
  echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

break;

/** ADD STORE */

case 'add_store':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( !isset( $_POST['feedID'] ) ) {
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';
} else if( admin_query::store_imported( $_POST['feedID'] ) ) {
  echo '<div class="a-error">' . $LANG['msg_feed_simported'] . '</div>';
}

else

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

  if( isset( $_POST['user'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['url'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_POST['logo_url'] ) )
  if( actions::add_store( array( 'feedID' => $_POST['feedID'], 'user' => $_POST['user'], 'category' => $_POST['category'], 'popular' => ( isset( $_POST['popular'] ) ? true : false ), 'name' => $_POST['name'], 'url' => $_POST['url'], 'description' => $_POST['description'], 'tags' => $_POST['tags'], 'import_logo' => ( isset( $_POST['import_logo'] ) ? true : false ), 'logo_url' => $_POST['logo_url'], 'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 ), 'meta_title' => '', 'meta_desc' => '' ) ) )
  echo '<div class="a-success">' . $LANG['msg_added'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['msg_error'] . '</div>';

}

echo '<a href="#" class="btn" onclick="window.history.go(-2)">' . $LANG['back'] . '</a>';

break;

/** IMPORT STORES */

case 'import_stores':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( !isset( $_GET['category'] ) || !\query\main::category_exists( $_GET['category'] ) ) {
  echo '<div class="a-error">' . $LANG['msg_category_dontexist'] . '</div>';
  echo '<a href="#" class="btn" onclick="window.history.go(-1)">' . $LANG['back'] . '</a>';

} else if( !isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
  echo '<div class="a-error">' . $LANG['msg_feed_seltoimp'] . '</div>';
  echo '<a href="#" class="btn" onclick="window.history.go(-1)">' . $LANG['back'] . '</a>';

} else {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && check_csrf( $_POST['token'], 'feed_import_csrf' ) ) {

include 'includes/feed.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  $ssuc = $csuc = $serr = $cerr = 0;

  try {

    $stores = $feed->stores( $store_ids = array( 'ids' => implode( ',', array_keys( $_GET['id'] ) ) ) );

    if( !empty( $stores['Count'] ) ) {

    $upload_logo = \query\main::get_option( 'feed_uppics' );
    $imp_expired = \query\main::get_option( 'feed_iexpc' );

    // Let's split everything in pages, because of ggcoupon.com, them allow only 10 results per page.

    for( $sp = 1; $sp <= ceil( $stores['Count'] / 10 ); $sp++ ) {

    if( $sp != 1 ) {
      $stores = $feed->stores( array_merge( array( 'page' => $sp ), $store_ids ) );
    }

    foreach( $stores['List'] as $store ) {

    if( !admin_query::store_imported( $store->ID ) && ( $new_store_id = actions::add_store( array( 'feedID' => $store->ID, 'user' => $GLOBALS['me']->ID, 'popular' => 0, 'category' => $_GET['category'], 'name' => $store->Name, 'url' => $store->URL, 'description' => $store->Description, 'tags' => $store->Tags, 'import_logo' => $upload_logo, 'logo_url' => $store->Image, 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) ) ) {
      $ssuc++;

    if( isset( $_GET['coupons'] ) && $_GET['coupons'] == 'yes' ) {

    try {

    $coupons = $feed->coupons( $coupon_ids = array( 'store' => $store->ID, 'view' => (!$imp_expired ? 'active' : '') ) );

    if( !empty( $coupons['Count'] ) ) {

    for( $cp = 1; $cp <= ceil( $coupons['Count'] / 10 ); $cp++ ) {

    if( $cp != 1 ) {
      $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $coupon_ids ) );
    }

    foreach( $coupons['List'] as $coupon ) {

    if( !admin_query::coupon_imported( $coupon->ID ) && actions::add_item( array( 'feedID' => $coupon->ID, 'store' => $new_store_id, 'popular' => 0, 'exclusive' => 0,  'category' => $_GET['category'], 'name' => $coupon->Title, 'link' => $coupon->URL, 'code' => $coupon->Code, 'description' => $coupon->Description, 'tags' => $coupon->Tags, 'cashback' => 0, 'start' => $coupon->Start_Date, 'end' => $coupon->End_Date, 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) ) {
      $csuc++;
    } else {
      $cerr++;
    }

    }

    usleep( 500000 ); // let's put a break after every page, 500 000 microseconds. that means a half of a second

    }

    }

    }

    catch( Exception $e ) { }

    }

    } else {
      $serr++;
    }

    }

    usleep( 200000 ); // a break again

    }

    }

  }

  catch ( Exception $e ){ }

  echo '<div class="a-message">' . $LANG['msg_feed_finished'] . '</div>';

  echo '<ul class="announce-box">
  <li>' . $LANG['feed_stores_sucimp'] . ':<b>' . $ssuc . '</b></li>
  <li>' . $LANG['feed_stores_errimp'] . ':<b>' . $serr . '</b></li>
  <li>' . $LANG['feed_coupons_sucimp'] . ':<b>' . $csuc . '</b></li>
  <li>' . $LANG['feed_coupons_errimp'] . ':<b>' . $cerr . '</b></li>
  </ul>';

}

catch ( Exception $e ) {
  echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

}

$csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {

echo '<script>
window.onload = function() {
setTimeout(function(){
  document.forms[\'import_now\'].submit();
}, 1000);
}
</script>';

echo '<div style="text-align: center;">
  <h2>' . $LANG['feed_import_dleave'] . '</h2>
</div>

<form id="import_now" action="#" method="POST">
  <input type="hidden" name="token" value="' . $csrf . '" />
</form>';

} else {

  echo '<a href="#" class="btn" onclick="window.history.go(-2)">' . $LANG['back'] . '</a>';

}

}

break;

/** LIST OF FEED COUPONS */

case 'coupons':

if( !ab_to( array( 'feed' => 'view' ) ) ) die;

include 'includes/feed.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  try {

    $coupons = $feed->coupons( array( 'page' => (isset( $_GET['page'] ) ? $_GET['page'] : 1), 'per_page' => 10, 'orderby' => (isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'date desc'), 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'category' => (isset( $_GET['category'] ) ? $_GET['category'] : ''), 'search' => (isset( $_GET['search'] ) ? $_GET['search'] : '') ) );

    echo '<div class="title">

    <h2>' . $LANG['coupons_title'] . '</h2>

    <div style="float:right; margin: 0 2px 0 0;">';
    if( ab_to( array( 'feed' => 'import' ) ) ) echo '<a href="?route=feed.php&amp;action=import" class="btn">' . $LANG['feed_icoupons'] . '</a>';
    echo '</div>';

    if( !empty( $LANG['feed_coupons_subtitle'] ) ) {
      echo '<span>' . $LANG['feed_coupons_subtitle'] . '</span>';
    }

    echo '</div>';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

    if( isset( $_POST['delete'] ) ) {

      if( isset( $_POST['id'] ) )
      if( actions::delete_page( array_keys( $_POST['id'] ) ) )
      echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
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

    }

    }

    $csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

    echo '<div class="page-toolbar">

    <form action="#" method="GET" autocomplete="off">
    <input type="hidden" name="route" value="feed.php" />
    <input type="hidden" name="action" value="coupons" />

    ' . $LANG['order_by'] . ':
    <select name="orderby">';
    foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'update' => $LANG['order_last_update'], 'update desc' => $LANG['order_last_update_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
    echo '</select> ';

    try {

      $category = $feed->categories();

      echo '<select name="category">
      <option value="">' . $LANG['all_categories'] . '</option>';
      foreach( $category['List'] as $k => $v ) {
        echo '<optgroup label="' . $v->name[0] . '">';
        echo '<option value="' . $k . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k ? ' selected' : '' ) . '>' . $v->name[0] . '</option>';
        if( isset( $v->subcategories ) ) {
          foreach( $v->subcategories as $k1 => $v1 ) {
            echo '<option value="' . $k1 . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k1 ? ' selected' : '' ) . '>' . $v1 . '</option>';
          }
        }
        echo '</optgroup>';
      }
      echo '</select>';

    }

    catch( Exception $e ) { }

    if( isset( $_GET['search'] ) ) {
    echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
    }

    echo ' <button class="btn">' . $LANG['view'] . '</button>

    </form>

    <form action="#" method="GET" autocomplete="off">
    <input type="hidden" name="route" value="feed.php" />
    <input type="hidden" name="action" value="coupons" />';

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

    echo '<div class="results">' . ( (int) $coupons['Count'] === 1 ? sprintf( $LANG['result'], $coupons['Count'] ) : sprintf( $LANG['results'], $coupons['Count'] ) );
    if( !empty( $_GET['store'] ) || !empty( $_GET['category'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=feed.php&amp;action=coupons">' . $LANG['reset_view'] . '</a>';
    echo '</div>';

    if( $coupons['Count'] ) {

    echo '<form action="#" method="GET">
    <input type="hidden" name="route" value="feed.php" />
    <input type="hidden" name="action" value="import_coupons" />

    <ul class="elements-list">

    <li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

    $feed_im  = ab_to( array( 'feed' => 'import' ) );

    if( $feed_im ) {

    echo '<div class="bulk_options">
    <button class="btn">' . $LANG['import_all'] . '</button>';
    echo '</div>';
    }

    foreach( $coupons['List'] as $item ) {

      $imported = admin_query::coupon_imported( $item->ID );
      $store_imported = admin_query::store_imported( $item->Store_ID );

      echo '<li>
      <input type="checkbox" name="id[' . $item->ID . ']" value=""' . ( $imported || !$store_imported ? ' disabled' : '' ) . '/>

      <div style="display: table;">

      <img src="' . $item->Store_Image . '" alt="" style="width: 80px;" />
      <div class="info-div"><h2>' . ( $imported ? '<span class="msg-alert" title="' . $LANG['added_through_feed_msg'] . '">' . $LANG['added_through_feed'] . '</span> ' : '' ) . ( $item->is_active ? '<span class="msg-success">' . $LANG['active'] . '</span> ' : '<span class="msg-error">' . $LANG['expired'] . '</span> ' ) . $item->Title . '</h2>
      ' . ( !$store_imported ? '<span class="msg-error">' . $LANG['notadded_through_feed'] . '</span> ' : '' ) . '<a href="?route=feed.php&amp;action=coupons&amp;store=' . $item->Store_ID . '">' . $item->Store_Name . '</a>' . ( !$store_imported ? ' / <a href="?route=feed.php&amp;action=preview_store&amp;id=' . $item->Store_ID . '">' . $LANG['preview_import'] . '</a>' : '' ) . '</div>

      </div>

      <div style="clear:both;"></div>

      <div class="options">';
      if( ! $imported && $store_imported && $feed_im ) {
        echo '<a href="javascript:void(0)" onclick="$(this).parents(\'li\').children(\'input\').click(); return false;">' . $LANG['checkun'] . '</a>';
        echo '<a href="?route=feed.php&amp;action=preview_coupon&amp;id=' . $item->ID . '">' . $LANG['preview_import'] . '</a>';
      }

      if( !empty( $item->Description ) ) {
      echo '<a href="javascript:void(0)" onclick="$(this).show_next( { after_action: \'\', element: \'div\' } ); return false;">' . $LANG['description'] . '</a>';
      echo '<div style="display: none; margin: 10px 0; font-size: 12px;">' . nltobr( $item->Description ) . '</div>';
      }

      echo '</div>

      </li>';

    }

    echo '</ul>

    <input type="hidden" name="csrf" value="' . $csrf . '" />

    </form>';

    if( ( $pages = ceil( $coupons['Count'] / 10 ) ) > 1 ) {

    $page = isset( $_GET['page'] ) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
    $page = $page > $pages ? $pages : $page;

    echo '<div class="pagination">';

    if( $page > 1 )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page - 1 ) ) . '" class="btn">' . $LANG['prev_page'] . '</a>';
    if( $page < $pages )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page + 1 ) ) . '" class="btn">' . $LANG['next_page'] . '</a>';

    if( $pages > 1 ) {
    echo '<div class="pag_goto">' . sprintf( $LANG['pageofpages'], $page, $pages ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . $LANG['go'] . '</button>
    </form>
    </div>';
    }

    echo '</div>';

    }

    } else
      echo '<div class="a-alert">' . $LANG['no_coupons_yet'] . '</div>';

  }

  catch ( Exception $e ){
    echo '<div class="a-alert">' . $e->getMessage() . '</div>';
  }

}

catch ( Exception $e ) {
  echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

break;

/** LIST OF FEED STORES */

default:

if( !ab_to( array( 'feed' => 'view' ) ) ) die;

include 'includes/feed.php';

try {

  $feed = new feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  try {

    $stores = $feed->stores( array( 'orderby' => (isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'date desc'), 'page' => (isset( $_GET['page'] ) ? $_GET['page'] : 1), 'per_page' => 10, 'category' => (isset( $_GET['category'] ) ? $_GET['category'] : ''), 'search' => (isset( $_GET['search'] ) ? $_GET['search'] : '') ) );

    echo '<div class="title">

    <h2>' . $LANG['stores_title'] . '</h2>';

    if( !empty( $LANG['feed_stores_subtitle'] ) ) {
      echo '<span>' . $LANG['feed_stores_subtitle'] . '</span>';
    }

    echo '</div>';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

    if( isset( $_POST['delete'] ) ) {

      if( isset( $_POST['id'] ) )
      if( actions::delete_page( array_keys( $_POST['id'] ) ) )
      echo '<div class="a-success">' . $LANG['msg_deleted'] . '</div>';
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

    }

    }

    $csrf = $_SESSION['feed_csrf'] = \site\utils::str_random(10);

    echo '<div class="page-toolbar">

    <form action="#" method="GET" autocomplete="off">
    <input type="hidden" name="route" value="feed.php" />
    <input type="hidden" name="action" value="list" />

    ' . $LANG['order_by'] . ':
    <select name="orderby">';
    foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'update' => $LANG['order_last_update'], 'update desc' => $LANG['order_last_update_desc'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
    echo '</select> ';

    try {

      $category = $feed->categories();

      echo '<select name="category">
      <option value="">' . $LANG['all_categories'] . '</option>';
      foreach( $category['List'] as $k => $v ) {
        echo '<optgroup label="' . $v->name[0] . '">';
        echo '<option value="' . $k . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k ? ' selected' : '' ) . '>' . $v->name[0] . '</option>';
        if( isset( $v->subcategories ) ) {
          foreach( $v->subcategories as $k1 => $v1 ) {
            echo '<option value="' . $k1 . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k1 ? ' selected' : '' ) . '>' . $v1 . '</option>';
          }
        }
        echo '</optgroup>';
      }
      echo '</select>';

    }

    catch( Exception $e ) { }

    if( isset( $_GET['search'] ) ) {
    echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
    }

    echo ' <button class="btn">' . $LANG['view'] . '</button>

    </form>

    <form action="#" method="GET" autocomplete="off">
    <input type="hidden" name="route" value="feed.php" />
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

    echo '<div class="results">' . ( (int) $stores['Count'] === 1 ? sprintf( $LANG['result'], $stores['Count'] ) : sprintf( $LANG['results'], $stores['Count'] ) );
    if( !empty( $_GET['category'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=feed.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
    echo '</div>';

    if( $stores['Count'] ) {

    echo '<form action="#" method="GET">
    <input type="hidden" name="route" value="feed.php" />
    <input type="hidden" name="action" value="import_stores" />

    <ul class="elements-list">

    <li class="head"><input type="checkbox" checkall /> ' . $LANG['name'] . '</li>';

    $feed_im  = ab_to( array( 'feed' => 'import' ) );

    if( $feed_im ) {

    echo '<div class="bulk_options">';

    echo $LANG['category'] . ':
    <select name="category">';
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
    echo '</select>

    <input type="checkbox" name="coupons" value="yes" checked> ' . $LANG['feed_icouponstoo'] . '

    <button class="btn">' . $LANG['import_all'] . '</button>';

    echo '</div>';

    }

    foreach( $stores['List'] as $item ) {

      echo '<li>
      <input type="checkbox" name="id[' . $item->ID . ']" value=""' . ( $imported = admin_query::store_imported( $item->ID ) ? ' disabled' : '' ) . ' />

      <div style="display: table;">

      <img src="' . \query\main::store_avatar( $item->Image ) . '" alt="" style="width: 80px;" />
      <div class="info-div"><h2>' . ( $imported ? '<span class="msg-alert" title="' . $LANG['added_through_feed_msg'] . '">' . $LANG['added_through_feed'] . '</span> ' : '' ) . $item->Name . '</h2>
      ' . ( empty( $item->Coupons ) ? $LANG['no_coupons_store'] : '<a href="?route=feed.php&amp;action=coupons&amp;store=' . $item->ID . '">' . sprintf( $LANG['nr_coupons_store'], $item->Coupons ) . '</a>' ) . '</div>

      </div>

      <div style="clear:both;"></div>

      <div class="options">';
      if( ! $imported && $feed_im ) {
        echo '<a href="javasript:void(0)" onclick="$(this).parents(\'li\').children(\'input\').click(); return false;">' . $LANG['checkun'] . '</a>';
        echo '<a href="?route=feed.php&amp;action=preview_store&amp;id=' . $item->ID . '">' . $LANG['preview_import'] . '</a>';
      }

      if( !empty( $item->Description ) ) {
        echo '<a href="javascript:void(0)" onclick="$(this).show_next( { after_action: \'\', element: \'div\' } ); return false;">' . $LANG['description'] . '</a>';
        echo '<div style="display: none; margin: 10px 0; font-size: 12px;">' . nltobr( $item->Description ) . '</div>';
      }

      echo '</div>

      </li>';

    }

    echo '</ul>

    </form>';

    if( ( $pages = ceil( $stores['Count'] / 10 ) ) > 1 ) {

    $page = isset( $_GET['page'] ) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
    $page = $page > $pages ? $pages : $page;

    echo '<div class="pagination">';

    if( $page > 1 )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page - 1 ) ) . '" class="btn">' . $LANG['prev_page'] . '</a>';
    if( $page < $pages )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page + 1 ) ) . '" class="btn">' . $LANG['next_page'] . '</a>';

    if( $pages > 1 ) {
    echo '<div class="pag_goto">' . sprintf( $LANG['pageofpages'], $page, $pages ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . $LANG['go'] . '</button>
    </form>
    </div>';
    }

    echo '</div>';

    }

    } else
      echo '<div class="a-alert">' . $LANG['no_stores_yet'] . '</div>';

  }

  catch ( Exception $e ) {
    echo '<div class="a-alert">' . $e->getMessage() . '</div>';
  }

}

catch ( Exception $e ) {
  echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

break;

}