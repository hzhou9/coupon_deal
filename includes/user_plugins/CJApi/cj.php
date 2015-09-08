<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** ASSIGN AN CJ.com ID TO A STORE */

case 'store_assign':

echo '<div class="title">
<h2>Assign ID</h2>
<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=advertisers">Advertisers</a></li>
<li><a href="?plugin=CJApi/cj.php&amp;action=links">Links</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
<li><a href="?plugin=CJApi/options.php">Settings</a></li>
</ul>
</div>

</div>

<span>Assign a CJ.com ID to an existing store</span>

</div>';

$imported = \plugin\CJApi\inc\import::store_imported();

if( !empty( $_GET['id'] ) ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'cjapi_csrf' ) ) {

  if( isset( $_POST['store'] ) )
  if( \plugin\CJApi\inc\actions::assign( array( 'cjID' => ( (int) $_POST['store'] === 0 ? 0 : $_GET['id'] ), 'storeID' => ( (int) $_POST['store'] === 0 && $imported ? $imported->ID : $_POST['store'] ) ) ) ) {
  echo '<div class="a-success">Saved!</div>';
  $imported = (object) array( 'ID' => ( !empty( $_POST['store'] ) ? (int) $_POST['store'] : '' ) );
  } else
  echo '<div class="a-error">Error!</div>';

}

$csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>Assign Store ID:</span><div data-search="store"><input type="text" name="store" value="' . ( $imported ? $imported->ID : '' ) . '" /><a href="#">S</a></div></div>
<div class="row"><span>To CJ.com ID:</span><div><input type="text" name="cjid" value="' . (int) $_GET['id'] . '" disabled /></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>

</form>

</div>';

} else echo '<div class="a-error">ID not set.</div>';

break;

/** PREVIEW STORE */

case 'store_preview':

echo '<div class="title">

<h2>Preview & Import</h2>
<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=advertisers">Advertisers</a></li>
<li><a href="?plugin=CJApi/cj.php&amp;action=links">Links</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
<li><a href="?plugin=CJApi/options.php">Settings</a></li>
</ul>
</div>

</div>

<span>Here you can edit the details of this store before the import</span>

</div>';

if( isset( $_GET['store'] ) ) {

  $store = json_decode( urldecode( $_GET['store'] ), true );

  $id = key( $store );
  $store = array_map( 'htmlspecialchars', current( $store ) );

} else {
  $store = array();
}

if( isset( $id ) && \plugin\CJApi\inc\import::store_imported( $id ) ) {
  echo '<div class="a-error">Sorry, this store is already imported.</div>';
} else {
    
    $imgdisp = \plugin\CJApi\inc\actions::find_cj_img($id);
    
if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['store'] ) ) {

$store = array_map( 'htmlspecialchars', $_POST['store'] );

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'cjapi_csrf' ) ) {

    if( \plugin\CJApi\inc\actions::add_store( array( 'cjID' => $id, 'logo'=>$imgdisp, 'user' => $GLOBALS['me']->ID, 'popular' => ( isset( $_POST['store']['Popular'] ) ? true : false ), 'category' => $_POST['store']['Category'], 'name' => $_POST['store']['Name'], 'url' => $_POST['store']['Link'], 'description' => $_POST['store']['Description'], 'tags' => $_POST['store']['Tags'], 'publish' => ( isset( $_POST['store']['Publish'] ) ? true : false ), 'meta_title' => $_POST['store']['MTitle'], 'meta_desc' => $_POST['store']['MDesc'] ) ) ){
  echo '<div class="a-success">Added!</div><button class="btn" onclick="window.history.go(-2);">Back</button>';
        return;
    }else
  echo '<div class="a-error">Error!</div>';

}

}

$csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>User ID:</span><div data-search="user"><input type="text" name="user" value="' . ( !empty( $_GET['user'] ) ? (int)$_GET['user'] : $GLOBALS['me']->ID ) . '" required /><a href="#">S</a></div></div>

<div class="row"><span>Category:</span>
<div><select name="store[Category]">';
$categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
foreach( $categories_while as $cat )echo '<option value=\"' . $cat->ID . '\">' . $cat->name . '</option>';
echo '</select></div></div>';
    
echo '<div class="row"><span>Name:</span><div><input type="text" name="store[Name]" value="' . ( isset( $store['Name'] ) ? $store['Name'] : '' ) . '" required /></div></div>
<div class="row"><span>Store URL:</span><div><input type="text" name="store[Link]" value="' . ( isset( $store['Link'] ) ? $store['Link'] : '' ) . '" /></div></div>
<div class="row"><span>Description:</span><div><textarea name="store[Description]">' . ( isset( $store['Description'] ) ? $store['Description'] : '' ) . '</textarea></div></div>
<div class="row"><span>Tags:</span><div><input type="text" name="store[Tags]" value="' . ( isset( $store['Tags'] ) ? $store['Tags'] : '' ) . '" /></div></div>
<div class="row"><span>Logo:</span><div><input type="file" name="logo" value="" />
    <img src="'.$imgdisp.'" style="width:80px;"/>
    
    </div></div>
<div class="row"><span>Add to:</span><div><input type="checkbox" name="store[Popular]" id="popular"' . ( isset( $store['Popular'] ) ? ' checked' : '' ) . ' /> <label for="popular">Populars</label></div></div>
<div class="row"><span>Publish:</span><div><input type="checkbox" name="store[Publish]" id="publish"' . ( $_SERVER['REQUEST_METHOD'] == 'POST' && !isset( $store['Publish'] ) ? '' : ' checked' ) . ' /> <label for="publish">Publish this store</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>Personalized Meta-Tags</h2>
</div>

<div class="row"><span>Title <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><input type="text" name="store[MTitle]" value="' . ( isset( $store['MTitle'] ) ? $store['MTitle'] : '' ) . '" /></div></div>
<div class="row"><span>Description <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><textarea name="store[MDesc]">' . ( isset( $store['MDesc'] ) ? $store['MDesc'] : '' ) . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Import</button>

<a href="#" id="modify_mt_but">Meta Tags</a>

</form>

</div>';

}

break;

/** IMPORT STORES */

case 'import_stores':

if( !isset( $_POST['category'] ) || ! \query\main::category_exists( $_POST['category'] ) ) {
  echo '<div class="a-error">Invalid category!</div>';
  echo '<a href="#" class="btn" onclick="window.history.go(-1)">Back</a>';

} else if( empty( $_POST['id'] ) || !isset( $_POST['store'] ) ) {
  echo '<div class="a-error">Select brands that you want to import.</div>';
  echo '<a href="#" class="btn" onclick="window.history.go(-1)">Back</a>';

} else {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && check_csrf( $_POST['token'], 'cjapi_csrf' ) ) {

  $success = $error = 0;

  foreach( $_POST['store'] as $store ) {

  $store = json_decode( urldecode( $store ), true );

  $id = key( $store );
  $store = current( $store );

      if( isset( $_POST['id'][$id] ) ){
          $imgdisp = \plugin\CJApi\inc\actions::find_cj_img($id);
  if( ! \plugin\CJApi\inc\import::store_imported( $id ) && \plugin\CJApi\inc\actions::add_store( array( 'cjID' => $id, 'logo'=>$imgdisp, 'user' => $GLOBALS['me']->ID, 'popular' => 0, 'category' => $_POST['category'], 'name' => $store['Name'], 'url' => $store['Link'], 'description' => '', 'tags' => '', 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) ) {
    $success++;
  } else {
    $error++;

  }
      }

  }

  echo '<div class="a-message">Import procedure has been successfully finished.</div>';

  echo '<ul class="announce-box">
  <li>Imported:<b>' . $success . '</b></li>
  <li>Errors:<b>' . $error . '</b></li>
  </ul>';

}

echo '<a href="#" class="btn" onclick="window.history.go(-1)">Back</a>';

}

break;

/** PREVIEW COUPON */

case 'coupon_preview':

echo '<div class="title">

<h2>Preview & Import</h2>
<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=advertisers">Advertisers</a></li>
<li><a href="?plugin=CJApi/cj.php&amp;action=links">Links</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
<li><a href="?plugin=CJApi/options.php">Settings</a></li>
</ul>
</div>

</div>

<span>Here you can edit the details of this coupon before the import</span>

</div>';

if( isset( $_GET['coupon'] ) ) {

  $coupon = json_decode( urldecode( $_GET['coupon'] ), true );

  $id = key( $coupon );
  $coupon = array_map( 'htmlspecialchars', current( $coupon ) );

} else {
  $coupon = array();
}

if( !isset( $coupon['Advertiser'] ) || !( $store = \plugin\CJApi\inc\import::store_imported( $coupon['Advertiser'] ) ) ) {
  echo '<div class="a-error">Sorry, the store is not imported.</div>';
} else if( \plugin\CJApi\inc\import::coupon_imported( $id ) ) {
  echo '<div class="a-alert">Sorry, the coupon is already imported.</div>';
} else {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['coupon'] ) ) {

$coupon = \site\utils::array_map_recursive( 'htmlspecialchars', $_POST['coupon'] );

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'cjapi_csrf' ) ) {

    if( \plugin\CJApi\inc\actions::add_item( array( 'cjID' => $id, 'store' => $store->ID, 'category' => $_POST['coupon']['Category'], 'popular' => ( isset( $_POST['coupon']['Popular'] ) ? true : false ), 'exclusive' => ( isset( $_POST['coupon']['Exclusive'] ) ? true : false ), 'name' => $_POST['coupon']['Title'], 'link' => ( !isset( $_POST['coupon']['Ownlink'] ) && isset( $_POST['coupon']['Link'] ) && filter_var( $_POST['coupon']['Link'], FILTER_VALIDATE_URL ) ? $_POST['coupon']['Link'] : '' ), 'code' => $_POST['coupon']['Code'], 'description' => $_POST['coupon']['Description'], 'tags' => $_POST['coupon']['Tags'], 'start' => implode( $_POST['coupon']['SD'], ', ' ), 'end' => implode( $_POST['coupon']['ED'], ', ' ), 'publish' => ( isset( $_POST['coupon']['Publish'] ) ? true : false ), 'meta_title' => $_POST['coupon']['MTitle'], 'meta_desc' => $_POST['coupon']['MDesc'] ) ) ){
        echo '<div class="a-success">Added!</div><button class="btn" onclick="window.history.go(-2);">Back</button>';return;
    }else
  echo '<div class="a-error">Error!</div>';

}

}

$csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>Category:</span>
<div><select name="coupon[Category]">';
    $categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
    foreach( $categories_while as $cat )echo '<option value=\"' . $cat->ID . '\"' . ( $store->catID == $cat->ID ? ' selected' : '' ) . '>' . $cat->name . '</option>';
echo '</select></div></div>

<div class="row"><span>Name:</span><div><input type="text" name="coupon[Title]" value="' . ( isset( $coupon['Title'] ) ? $coupon['Title'] : '' ) . '" required /></div></div>
<div class="row"><span>Code:</span><div><input type="text" name="coupon[Code]" value="' . ( isset( $coupon['Code'] ) ? $coupon['Code'] : '' ) . '" /></div></div>
<div class="row"><span>Coupon URL:</span><div><input type="checkbox" name="coupon[Ownlink]" value="1" id="ownlink" onclick="$(this).show_next({element:\'#link\', type:\'next\'});"' . ( ( isset( $coupon['Ownlink'] ) && $coupon['Ownlink'] ) || empty( $coupon['Link'] ) ? ' checked' : '' ) . ' /> <label for="ownlink">Use store address</label> <br />
<input type="text" name="coupon[Link]" value="' . ( isset( $coupon['Link'] ) ? $coupon['Link'] : 'http://' ) . '" id="link"' . ( ( isset( $coupon['Ownlink'] ) && $coupon['Ownlink'] ) || empty( $coupon['Link'] ) ? ' style="display: none;"' : '' ) . ' />
</div></div>
<div class="row"><span>Description:</span><div><textarea name="coupon[Description]">' . ( isset( $coupon['Description'] ) ? $coupon['Description'] : '' ) . '</textarea></div></div>
<div class="row"><span>Tags:</span><div><input type="text" name="coupon[Tags]" value="' . ( isset( $coupon['Tags'] ) ? $coupon['Tags'] : '' ) . '" /></div></div>
<div class="row"><span>Start Date:</span><div><input type="date" name="coupon[SD][]" value="' . ( isset( $coupon['SD'] ) ? date( 'Y-m-d', strtotime( implode( ' ', (array) $coupon['SD'] ) ) ) : '' ) . '" style="width: 80%" /><input type="time" name="coupon[SD][]" value="' . ( isset( $coupon['SD'] ) ? date( 'H:i', strtotime( implode( ' ', (array) $coupon['SD'] ) ) ) : '00:00' ) . '" style="width: 20%" /></div></div>
<div class="row"><span>End Date:</span><div><input type="date" name="coupon[ED][]" value="' . ( isset( $coupon['ED'] ) ? date( 'Y-m-d', strtotime( implode( ' ', (array) $coupon['ED'] ) ) ) : date( 'Y-m-d', strtotime( \query\main::get_option( 'cj_exp' ) . ' days' ) ) ) . '" style="width: 80%" /><input type="time" name="coupon[ED][]" value="' . ( isset( $coupon['ED'] ) ? date( 'H:i', strtotime( implode( ' ', (array) $coupon['ED'] ) ) ) : '00:00' ) . '" style="width: 20%" /></div></div>
<div class="row"><span>Add to:</span><div>
<input type="checkbox" name="coupon[Popular]" id="popular"' . ( isset( $coupon['Popular'] ) ? ' checked' : '' ) . ' /> <label for="popular">Populars</label> <br />
<input type="checkbox" name="coupon[Exclusive]" id="exclusive"' . ( isset( $coupon['Exclusive'] ) ? ' checked' : '' ) . ' /> <label for="exclusive">Exclusive</label></div></div>
<div class="row"><span>Publish:</span><div><input type="checkbox" name="coupon[Publish]" id="publish"' . ( $_SERVER['REQUEST_METHOD'] == 'POST' && !isset( $coupon['Publish'] ) ? '' : ' checked' ) . ' /> <label for="publish">Publish this coupon</label></div></div>

<div id="modify_mt" style="display: none; margin-top: 20px;">

<div class="title">
  <h2>Personalized Meta-Tags</h2>
</div>

<div class="row"><span>Title <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><input type="text" name="coupon[MTitle]" value="' . ( isset( $coupon['MTitle'] ) ? $coupon['MTitle'] : '' ) . '" /></div></div>
<div class="row"><span>Description <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><textarea name="coupon[MDesc]">' . ( isset( $coupon['MDesc'] ) ? $coupon['MDesc'] : '' ) . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Import</button>

<a href="#" id="modify_mt_but">Meta Tags</a>

</form>

</div>';

}

break;

/** IMPORT COUPONS */

case 'import_coupons':

if( empty( $_POST['id'] ) ) {
  echo '<div class="a-error">Select coupons that you want to import.</div>';
  echo '<a href="#" class="btn" onclick="window.history.go(-1)">Back</a>';

} else {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && check_csrf( $_POST['token'], 'cjapi_csrf' ) ) {

  $success = $error = 0;

  foreach( $_POST['coupon'] as $coupon ) {

  $coupon = json_decode( urldecode( $coupon ), true );

  $id = key( $coupon );
  $coupon = current( $coupon );

  if( isset( $_POST['id'][$id] ) )
  if( ( $store = \plugin\CJApi\inc\import::store_imported( $coupon['Advertiser'] ) ) && \plugin\CJApi\inc\actions::add_item( array( 'cjID' => $id, 'store' => $store->ID, 'category' => $store->catID, 'popular' => 0, 'exclusive' => 0, 'name' => $coupon['Title'], 'link' => ( isset( $coupon['Link'] ) && filter_var( $coupon['Link'], FILTER_VALIDATE_URL ) ? $coupon['Link'] : '' ), 'code' => ( isset( $coupon['Code'] ) ? $coupon['Code'] : '' ), 'description' => '', 'tags' => '', 'start' => ( isset( $coupon['SD'] ) ? $coupon['SD'] : '' ), 'end' => ( isset( $coupon['ED'] ) ? $coupon['ED'] : date( 'Y-m-d', strtotime( \query\main::get_option( 'cj_exp' ) . ' days' ) ) ), 'publish' => 1, 'meta_title' => '', 'meta_desc' => '' ) ) ) {
    $success++;
  } else {
    $error++;

  }

  }

  echo '<div class="a-message">Import procedure has been successfully finished.</div>';

  echo '<ul class="announce-box">
  <li>Imported:<b>' . $success . '</b></li>
  <li>Errors:<b>' . $error . '</b></li>
  </ul>';

}

echo '<a href="#" class="btn" onclick="window.history.go(-1)">Back</a>';

}

break;

/** LIST OF LINKS */

case 'links':

echo '<div class="title">

<h2>Links</h2>

<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=advertisers">Advertisers</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
<li><a href="?plugin=CJApi/options.php">Settings</a></li>
</ul>
</div>

</div>

<span>List of advertisher links</span>

</div>';

$csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);

/** New Cj Client */

$cj = new \plugin\CJApi\inc\client( \query\main::get_option( 'cj_key' ) );

/** */

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="plugin" value="CJApi/cj.php" />
<input type="hidden" name="action" value="links" />

View: <select name="view">';
foreach( ( $views = array( 'joined' => 'Joined', 'notjoined' => 'Not Joined' ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( isset( $_GET['view'] ) && urldecode( $_GET['view'] ) == $k || !isset( $_GET['view'] ) && $k == 'joined' ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select> ';

echo 'Type: <select name="type">';
foreach( ( $types = array( '' => 'All', 'coupon' => 'Coupon', 'sweepstakes' => 'Sweepstakes', 'product' => 'Product', 'sale/discount' => 'Sale/Discount', 'free shipping' => 'Free Shipping', 'seasonal link' => 'Seasonal' ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( isset( $_GET['type'] ) && urldecode( $_GET['type'] ) == $k ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select> ';

try {

$categories = $cj->categories();

echo 'Category: <select name="category" style="max-width: 160px;">
<option value="">All</option>';
foreach( $categories as $v ) echo '<option value="' . $v . '"' . (isset( $_GET['category'] ) && urldecode( $_GET['category'] ) == $v ? ' selected' : '') . '>' . $v . '</option>';
echo '</select> ';

}

catch( Exception $e ) { $categories = array(); }

if( isset( $_GET['search'] ) ) echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
if( isset( $_GET['ids'] ) ) echo '<input type="hidden" name="ids" value="' . htmlspecialchars( $_GET['ids'] ) . '" />';
echo '<button class="btn">View</button>
</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="plugin" value="CJApi/cj.php" />
<input type="hidden" name="action" value="links" />';
if( isset( $_GET['view'] ) ) echo '<input type="hidden" name="view" value="' . htmlspecialchars( $_GET['view'] ) . '" />';
if( isset( $_GET['type'] ) ) echo '<input type="hidden" name="type" value="' . htmlspecialchars( $_GET['type'] ) . '" />';
if( isset( $_GET['category'] ) ) echo '<input type="hidden" name="category" value="' . htmlspecialchars( $_GET['category'] ) . '" />';
if( isset( $_GET['ids'] ) ) echo '<input type="hidden" name="ids" value="' . htmlspecialchars( $_GET['ids'] ) . '" />';
echo '<input type="search" name="search" value="' . (!isset( $_GET['search'] ) ? '' : htmlspecialchars( $_GET['search'] )) . '" placeholder="Search links" />
<button class="btn">Search</button>
</form>

</div>';

try {

/* view after relationship */
$view = isset( $_GET['view'] ) && array_key_exists( $_GET['view'], $views ) ? $_GET['view'] : 'joined';
if( isset( $_GET['ids'] ) ) {
  $view = $_GET['ids'];
}

/* view after type */
$type = isset( $_GET['type'] ) && array_key_exists( $_GET['type'], $types ) ? $_GET['type'] : '';
/* */

/* view after category */
$category = isset( $_GET['category'] ) && in_array( $_GET['category'], $categories ) ? $_GET['category'] : '';
/* */

/* pagination */
$per_page = \query\main::get_option( 'cj_ipp' );
$page = isset( $_GET['page'] ) && $_GET['page'] > 0 ? $_GET['page'] : 1;
/* */

$lookup = array( 'website-id' => \query\main::get_option( 'cj_site-id' ), 'advertiser-ids' => $view, 'records-per-page' => $per_page, 'page-number' => $page, 'promotion-type' => $type, 'category' => $category );
if( !empty( $_GET['search'] ) ) {
  $lookup['keywords'] = $_GET['search'];
}

$links = $cj->linkSearch( $lookup );

//var_dump($links);
    
$attributes = $links['links']['@attributes'];

/* pagination */
$pages = ceil( $attributes['total-matched'] / $per_page );
if( $page > $pages ) $page = $pages;
/* */

echo '<div class="results"><a href="'.$GLOBALS['siteURL'].'_tools/cj_store_img_import.html" target="_blank">[Bind CJ Store Images]</a> ' . $attributes['total-matched'] . ' results';
if( !empty( $_GET['type'] ) || !empty( $_GET['category'] ) || !empty( $_GET['ids'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?plugin=CJApi/cj.php&amp;action=links' . (isset( $_GET['view'] ) ? '&amp;view=' . htmlspecialchars( $_GET['view'] ) : '') . '">Reset view</a>';
echo '</div>';

if( $attributes['records-returned'] ) {

echo '<form action="?plugin=CJApi/cj.php&amp;action=import_coupons#" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> Name</li>

<div class="bulk_options">

<button class="btn">Import all</button>

</div>';

if( isset( $links['links']['link'][0] ) ) {
  $advs = $links['links']['link'];
} else {
  $advs[] = (array) $links['links']['link'];
}

foreach( $advs as $item ) {

  // check first if this store is imported on your website
  $store_imported = \plugin\CJApi\inc\import::store_imported( $item['advertiser-id'] );

  // check first if this coupon is imported on your website
  $coupon_imported = \plugin\CJApi\inc\import::coupon_imported( $item['link-id'] );

  echo '<li>
  <input type="checkbox" name="id[' . $item['link-id'] . ']"' . ( !$store_imported || ( $store_imported && $coupon_imported ) ? ' disabled' : '' ) . ' />

  <div style="display: table;">

  <img src="' . \query\main::store_avatar( ( $store_imported ? $store_imported->image : '' ) ) . '" alt="" style="width: 80px;" />
  <div class="info-div"><h2>' . ( $store_imported && $coupon_imported ? '<span class="msg-alert" title="Local name: ' . $coupon_imported->title . '">Imported</span> ' : '' ) . htmlspecialchars( $item['link-name'] ) . '</h2>
  Advertiser: <b>' . ( $item['relationship-status'] !== 'joined' ? '<span class="msg-error">Not Joined</span> ' : '<span class="msg-success">Joined</span> ' ) . '<a href="?plugin=CJApi/cj.php&amp;action=links&amp;ids=' . $item['advertiser-id'] . '">' . htmlspecialchars( $item['advertiser-name'] ) . '</a></b> <br />
  URL: <a href="' . $item['destination'] . '" target="_blank">' . ( ( $url = urldecode( $item['destination'] ) ) && strlen( $url ) > 50 ? substr( $url, 0, 50 ) . '...' : $url ) . '</a> <br />
  Category: <b>' . $item['category'] . '</b> <br />
  <a href="javascript:void(0)" onclick="$(this).show_next({element:\'.store_more\', type:\'rightnext\'}); return false;">More/Less</a>

  <div class="store_more" style="display: none; margin-top: 8px;">';

  $jsdt = array();
  $jsdt[$item['link-id']]['Advertiser'] = $item['advertiser-id'];
  $jsdt[$item['link-id']]['Title'] = $item['link-name'];

  if( $item['relationship-status'] === 'joined' ) {
    $jsdt[$item['link-id']]['Link'] = $item['clickUrl'] . '?sid=type={TYPE},id={ID},user={UID}';
  }

  if( !empty( $item['language'] ) ) {
    echo 'Language: <b>' . $item['language'] . '</b> <br />';
  }
  if( !empty( $item['promotion-type'] ) ) {
    echo 'Type: <b>' . $item['promotion-type'] . '</b> <br />';
  }
  if( !empty( $item['coupon-code'] ) ) {
    $jsdt[$item['link-id']]['Code'] = $item['coupon-code'];
    echo 'Coupon Code: <b>' . $item['coupon-code'] . '</b><br />';
  }
  if( !empty( $item['promotion-start-date'] ) ) {
    $jsdt[$item['link-id']]['SD'] = $item['promotion-start-date'];
    echo 'Start date: <b>' . $item['promotion-start-date'] . '</b><br />';
  }
  if( !empty( $item['promotion-end-date'] ) ) {
    $jsdt[$item['link-id']]['ED'] = $item['promotion-end-date'];
    echo 'End date: <b>' . $item['promotion-end-date'] . '</b><br />';
  }
  if( !empty( $item['click-commission'] ) && $item['click-commission'] > 0 ) {
    echo 'Click Commission: <b>' . $item['click-commission'] . '</b><br />';
  }
  if( !empty( $item['lead-commission'] ) ) {
    echo 'Lead Commission: <b>' . $item['lead-commission'] . '</b><br />';
  }
  if( !empty( $item['sale-commission'] ) ) {
    echo 'Sale Commission: <b>' . $item['sale-commission'] . '</b><br />';
  }

  if( $item['relationship-status'] === 'joined' ) {
  echo '<br /><b>Click Link:</b><br />
  <input type="text" value="' . htmlspecialchars( $item['clickUrl'] ) . '" style="width: 100%; box-sizing: border-box;" />';
  if( !empty( $item['link-code-html'] ) ) {
    echo '<br /><b>Javascript Link:</b><br />
    <textarea style="width: 100%; height: 60px; box-sizing: border-box;">' . htmlspecialchars( $item['link-code-javascript'] ) . '"</textarea>';
  }
  }

  echo '</div>

  </div></div>

  <div style="clear:both;"></div>

  <div class="options">';

  if( $store_imported && !$coupon_imported ) {
    echo '<a href="javasript:void(0)" onclick="$(this).parents(\'li\').children(\'input\').click(); return false;">Check/Uncheck</a>';
    echo '<a href="?plugin=CJApi/cj.php&amp;action=coupon_preview&amp;coupon=' . ( $cdata = urlencode( json_encode( $jsdt ) ) ) . '">Preview & Import</a>';
    echo '<input type="hidden" name="coupon[]" value="' . $cdata . '" />';
  }

  echo '</div>
  </li>';

}

echo '</ul>

<input type="hidden" name="token" value="' . $csrf . '" />

</form>';


if( $pages > 1 ) {

  echo '<div class="pagination">';
  if( $page > 1 ) echo '<a href="' . \site\utils::update_uri( '', array( 'page' => ($page-1) ) ) . '" class="btn">← Prev</a>';
  if( $pages > $page ) echo '<a href="' . \site\utils::update_uri( '', array( 'page' => ($page+1) ) ) . '" class="btn">Next →</a>';
  if( $pages > 1 ) {
  echo '<div class="pag_goto">' . sprintf( 'Page %s of %s', $page, $pages ) . '
  <form action="#" method="GET">';
  foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
  echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
  <button class="btn">Go</button>
  </form>
  </div>';
  }
  echo '</div>';

}

} else {

  echo '<div class="a-alert">No links.</div>';

}

}

catch( Exception $e ) {

  echo '<div class="a-error">' . $e->getMessage() . '</div>';

}

break;

/** LIST OF ADVERTISERS */

default:

echo '<div class="title">

<h2>Advertisers</h2>

<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=links">Links</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
<li><a href="?plugin=CJApi/options.php">Settings</a></li>
</ul>
</div>

</div>

<span>List of advertisers</span>

</div>';

$csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);

/** New Cj Client */

$cj = new \plugin\CJApi\inc\client( \query\main::get_option( 'cj_key' ) );

/** */

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="plugin" value="CJApi/cj.php" />
<input type="hidden" name="action" value="list" />

View: <select name="view">';
foreach( ( $views = array( 'joined' => 'Joined', 'notjoined' => 'Not Joined' ) ) as $k => $v ) echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && urldecode( $_GET['view'] ) == $k || !isset( $_GET['view'] ) && $k == 'joined' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select> ';
if( isset( $_GET['search'] ) ) echo '<input type="hidden" name="search" value="' . htmlspecialchars( $_GET['search'] ) . '" />';
echo '<button class="btn">View</button>
</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="plugin" value="CJApi/cj.php" />
<input type="hidden" name="action" value="list" />';
if( isset( $_GET['view'] ) ) echo '<input type="hidden" name="view" value="' . htmlspecialchars( $_GET['view'] ) . '" />';
echo '<input type="search" name="search" value="' . (!isset( $_GET['search'] ) ? '' : htmlspecialchars( $_GET['search'] )) . '" placeholder="Search advertisers" />
<button class="btn">Search</button>
</form>

</div>';

try {

/* view after relationship */
$view = isset( $_GET['view'] ) && array_key_exists( $_GET['view'], $views ) ? $_GET['view'] : 'joined';
/* */

/* pagination */
$per_page = \query\main::get_option( 'cj_ipp' );
$page = isset( $_GET['page'] ) && $_GET['page'] > 0 ? $_GET['page'] : 1;
/* */

$lookup = array( 'advertiser-ids' => $view, 'records-per-page' => $per_page, 'page-number' => $page );
if( !empty( $_GET['search'] ) ) {
  $lookup['keywords'] = $_GET['search'];
}

$advertisers = $cj->advertiserLookup( $lookup );
$attributes = $advertisers['advertisers']['@attributes'];

/* pagination */
$pages = ceil( $attributes['total-matched'] / $per_page );
if( $page > $pages ) $page = $pages;
/* */

echo '<div class="results"><a href="'.$GLOBALS['siteURL'].'_tools/cj_store_img_import.html" target="_blank">[Bind CJ Store Images]</a> ' . $attributes['total-matched'] . ' results';
if( !empty( $_GET['search'] ) ) echo ' / <a href="?plugin=CJApi/cj.php&amp;action=lists' . (!isset( $_GET['view'] ) ? '' : '&amp;view=' . htmlspecialchars( $_GET['view'] )) . '">Reset view</a>';
echo '</div>';

if( $attributes['records-returned'] ) {

echo '<form action="?plugin=CJApi/cj.php&amp;action=import_stores#" method="POST" autocomplete="off">

<ul class="elements-list">

<li class="head"><input type="checkbox" checkall /> Name</li>

<div class="bulk_options">

Category: ';
echo '<select name="category">';
    $categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
    foreach( $categories_while as $cat )echo '<option value=\"' . $cat->ID . '\"' . ( $store->catID == $cat->ID ? ' selected' : '' ) . '>' . $cat->name . '</option>';
echo '</select>

<button class="btn">Import all</button>';

echo '</div>';

if( isset( $advertisers['advertisers']['advertiser'][0] ) ) {
  $advs = $advertisers['advertisers']['advertiser'];
} else {
  $advs[] = (array) $advertisers['advertisers']['advertiser'];
}

foreach( $advs as $item ) {

  // check first if this store is imported on your website
  $imported = \plugin\CJApi\inc\import::store_imported( $item['advertiser-id'] );
    $imgdisp = NULL;
    if($imported){
        $imgdisp = \query\main::store_avatar( $imported->image );
    }else{
        $imgdisp = \plugin\CJApi\inc\actions::find_cj_img($item['advertiser-id']);
        if($imgdisp == ''){
            $imgdisp = \query\main::store_avatar( '' );
        }
    }

  echo '<li>
  <input type="checkbox" name="id[' . $item['advertiser-id'] . ']"' . ( $imported ? ' disabled' : '' ) . ' />

  <div style="display: table;">

  <img src="' . $imgdisp . '" alt="" style="width: 80px;" />
  <div class="info-div"><h2>' . ( $item['relationship-status'] !== 'joined' ? '<span class="msg-error">Not Joined</span> ' : '<span class="msg-success">Joined</span> ' ) . ( $imported ? '<span class="msg-alert" title="Local name: ' . $imported->name . '">Imported</span> ' : '' ) . htmlspecialchars( $item['advertiser-name'] ) . '</h2>

  URL: <a href="' . $item['program-url'] . '" target="_blank">' . ( ( $url = urldecode( $item['program-url'] ) ) && strlen( $url ) > 50 ? substr( $url, 0, 50 ) . '...' : $url ) . '</a> <br />
  Category: <b>' . implode( ' &#65515; ', array_merge( (array) $item['primary-category']['parent'], (array) $item['primary-category']['child'] ) ) . '</b> <br />
  <a href="javascript:void(0)" onclick="$(this).show_next({element:\'.store_more\', type:\'rightnext\'}); return false;">More/Less</a>

  <div class="store_more" style="display: none; margin-top: 8px;">';

  $jsdt = array();
  $jsdt[$item['advertiser-id']]['Name'] = $item['advertiser-name'];
  $jsdt[$item['advertiser-id']]['Link'] = $item['program-url'];

  echo 'Network Rank: <b>' . $item['network-rank'] . '</b> <br />
  Language: <b>' . $item['language'] . '</b> <br />
  7 days EPC: <b>' . $item['seven-day-epc'] . '</b> <br />
  3 months EPC: <b>' . $item['three-month-epc'] . '</b> <br />
  Mobile traffic certified: <b>' . ($item['mobile-tracking-certified'] ? 'Yes' : 'No') . '</b>';
  if( isset( $item['actions']['action'] ) ) {
    echo '<br /><br /><b>Commissions:</b><br />';

    if( isset( $item['actions']['action'][0] ) ) {
      $commision = $item['actions']['action'];
    } else {
      $commision[] = (array) $item['actions']['action'];
    }

    foreach( $commision as $action ) {
      echo $action['name'] . ' (' . $action['type'] . '): <b>' . ( isset( $action['commision']['itemlist'] ) ? implode( ', ', $action['commission']['itemlist'] ) : $action['commission']['default'] ) . '</b> <br />';
    }

  }
  echo '</div>

  </div></div>

  <div style="clear:both;"></div>

  <div class="options">';
  if( !$imported ) {
    echo '<a href="javasript:void(0)" onclick="$(this).parents(\'li\').children(\'input\').click(); return false;">Check/Uncheck</a>';
    echo '<a href="?plugin=CJApi/cj.php&amp;action=store_preview&amp;store=' . ( $cdata = urlencode( json_encode( $jsdt ) ) ) . '">Preview & Import</a>';
    echo '<input type="hidden" name="store[]" value="' . $cdata . '" />';
  }
  echo '<a href="?plugin=CJApi/cj.php&amp;action=links&amp;ids=' . $item['advertiser-id'] . '">View Links</a>';
  echo '<a href="?plugin=CJApi/cj.php&amp;action=store_assign&amp;id=' . $item['advertiser-id'] . '">' . ($imported ? 'Change ID' : 'Assign ID') . '</a>';
  echo '</div>
  </li>';

}

echo '</ul>

<input type="hidden" name="token" value="' . $csrf . '" />

</form>';


if( $pages > 1 ) {

  echo '<div class="pagination">';
  if( $page > 1 ) echo '<a href="?plugin=CJApi/cj.php&amp;action=list&amp;page=' . ($page-1) . '" class="btn">← Prev</a>';
  if( $pages > $page ) echo '<a href="?plugin=CJApi/cj.php&amp;action=list&amp;page=' . ($page+1) . '" class="btn">Next →</a>';
  if( $pages > 1 ) {
  echo '<div class="pag_goto">' . sprintf( 'Page %s of %s', $page, $pages ) . '
  <form action="#" method="GET">';
  foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
  echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
  <button class="btn">Go</button>
  </form>
  </div>';
  }
  echo '</div>';

}

} else {

  echo '<div class="a-alert">No advertisers.</div>';

}

}

catch( Exception $e ) {

  echo '<div class="a-error">' . $e->getMessage() . '</div>';

}

break;

}