<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** DEFAULT IMAGES AND OTHERS */

case 'default':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_default_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_default_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  if( isset( $_POST['user_avatar'] ) && isset( $_POST['store_avatar'] ) && isset( $_POST['def_user_points'] ) && isset( $_POST['points_per_review'] ) && isset( $_POST['points_per_dailyv'] ) && isset( $_POST['points_per_refer'] ) && isset( $_POST['refer_cookie_duration'] ) )
  if( actions::set_option( array( 'default_user_avatar' => $_POST['user_avatar'], 'default_store_avatar' => $_POST['store_avatar'], 'default_reward_avatar' => $_POST['reward_avatar'], 'u_def_points' => (int) $_POST['def_user_points'], 'u_confirm_req' => ( isset( $_POST['def_user_confirmation'] ) ? 0 : 1 ), 'subscr_confirm_req' => ( isset( $_POST['def_subscr_conf'] ) ? 1 : 0 ), 'unsubscr_confirm_req' => ( isset( $_POST['def_unsubscr_conf'] ) ? 1 : 0 ), 'u_points_review' => (int) $_POST['points_per_review'], 'u_points_davisit' => (int) $_POST['points_per_dailyv'], 'u_points_refer' => (int) $_POST['points_per_refer'], 'refer_cookie' => (int) $_POST['refer_cookie_duration'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['settings_form_defua'] . ':</span><div class="images-list">';
$def_user_avatar = \query\main::get_option( 'default_user_avatar' );
foreach( glob( '../' . DEFAULT_IMAGES_LOC . '/avatar*.[pjg][npi][gf]' ) as $k ) echo '<input type="radio" name="user_avatar" value="' . basename( $k ) . '"' . ( basename( $k ) == $def_user_avatar ? ' checked' : '' ) . ' /> <img src="' . $k . '" alt="" />';
echo '</div></div>

<div class="row"><span>' . $LANG['settings_form_defsa'] . ':</span><div class="images-list">';
$def_store_avatar = \query\main::get_option( 'default_store_avatar' );
foreach( glob( '../' . DEFAULT_IMAGES_LOC . '/store_avatar*.[pjg][npi][gf]' ) as $k ) echo '<input type="radio" name="store_avatar" value="' . basename( $k ) . '" ' . ( basename( $k ) == $def_store_avatar ? ' checked' : '' ) . ' /> <img src="' . $k . '" alt="" style="width: 60px;" />';
echo '</div></div>

<div class="row"><span>' . $LANG['settings_form_defra'] . ':</span><div class="images-list">';
$def_reward_avatar = \query\main::get_option( 'default_reward_avatar' );
foreach( glob( '../' . DEFAULT_IMAGES_LOC . '/reward_avatar*.[pjg][npi][gf]' ) as $k ) echo '<input type="radio" name="reward_avatar" value="' . basename( $k ) . '" ' . ( basename( $k ) == $def_reward_avatar ? ' checked' : '' ) . ' /> <img src="' . $k . '" alt="" />';
echo '</div></div>

<div class="row"><span>' . $LANG['settings_form_defup'] . ' <span class="info"><span>' . $LANG['settings_form_idefup'] . '</span></span>:</span><div><input type="number" name="def_user_points" value="' . (int) \query\main::get_option( 'u_def_points' ) . '" min="0" /></div></div>
<div class="row"><span>' . $LANG['settings_form_defcr'] . ':</span><div><input type="checkbox" name="def_user_confirmation" id="def_user_confirmation"' . ( ! \query\main::get_option( 'u_confirm_req' ) ? ' checked' : '' ) . ' /> <label for="def_user_confirmation">' . $LANG['msg_require_user_conf'] . '</label></div></div>
<div class="row"><span>' . $LANG['settings_form_defsubscr'] . ':</span><div><input type="checkbox" name="def_subscr_conf" id="def_subscr_confirmation"' . ( \query\main::get_option( 'subscr_confirm_req' ) ? ' checked' : '' ) . ' /> <label for="def_subscr_confirmation">' . $LANG['msg_require_subscr_conf'] . '</label></div></div>
<div class="row"><span>' . $LANG['settings_form_defunsubscr'] . ':</span><div><input type="checkbox" name="def_unsubscr_conf" id="def_unsubscr_confirmation"' . ( \query\main::get_option( 'unsubscr_confirm_req' ) ? ' checked' : '' ) . ' /> <label for="def_unsubscr_confirmation">' . $LANG['msg_require_unsubscr_conf'] . '</label></div></div>

<div class="title" style="margin-top: 40px;">
<h2>' . $LANG['settings_userrew_title'] . '</h2>
</div>

<div class="row"><span>' . $LANG['settings_form_pprev'] . ':</span><div><input type="number" name="points_per_review" value="' . (int) \query\main::get_option( 'u_points_review' ) . '" min="0" /></div></div>
<div class="row"><span>' . $LANG['settings_form_ppdv'] . ' <span class="info"><span>' . $LANG['settings_form_ippdv'] . '</span></span>:</span><div><input type="number" name="points_per_dailyv" value="' . (int) \query\main::get_option( 'u_points_davisit' ) . '" min="0" /></div></div>
<div class="row"><span>' . $LANG['settings_form_ppref'] . ' <span class="info"><span>' . $LANG['settings_form_ippref'] . '</span></span>:</span><div><input type="number" name="points_per_refer" value="' . (int) \query\main::get_option( 'u_points_refer' ) . '" min="0" /></div></div>
<div class="row"><span>' . $LANG['settings_form_cokref'] . ':</span>
<div>
<select name="refer_cookie_duration">';
$refcookie_duration = \query\main::get_option( 'refer_cookie' );
foreach( array( 15, 30, 60, 90 ) as $k => $v ) echo '<option value="' . (int) $k . '"' . ( $k == $refcookie_duration ? ' selected' : '' ) . '>' . $v . ' ' . $LANG['days'] . '</option>';
echo '</select>
</div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>

</div>';

break;

/** META TAGS */

case 'meta':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_meta_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_meta_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  if( isset( $_POST['sitetitle'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_description'] ) )
  if( actions::set_option( array( 'sitetitle' => $_POST['sitetitle'], 'meta_keywords' => $_POST['meta_keywords'], 'meta_description' => $_POST['meta_description'], 'meta_coupon_title' => $_POST['meta_coupon_title'], 'meta_coupon_desc' => $_POST['meta_coupon_desc'], 'meta_product_title' => $_POST['meta_product_title'], 'meta_product_desc' => $_POST['meta_product_desc'], 'meta_store_title' => $_POST['meta_store_title'], 'meta_store_desc' => $_POST['meta_store_desc'], 'meta_reviews_title' => $_POST['meta_reviews_title'], 'meta_reviews_desc' => $_POST['meta_reviews_desc'], 'meta_category_title' => $_POST['meta_category_title'], 'meta_category_desc' => $_POST['meta_category_desc'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="sitetitle" value="' . htmlspecialchars( \query\main::get_option( 'sitetitle' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metakeywords'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_keywords">' . \query\main::get_option( 'meta_keywords' ) . '</textarea></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_description">' . \query\main::get_option( 'meta_description' ) . '</textarea></div></div>

</div>';

echo '<div class="title" style="margin-top: 30px;">

<h2>' . $LANG['settings_meta_pcoupon'] . '</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_coupon_title" value="' . htmlspecialchars( \query\main::get_option( 'meta_coupon_title' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_coupon_desc">' . \query\main::get_option( 'meta_coupon_desc' ) . '</textarea></div></div>

</div>';

echo '<div class="title" style="margin-top: 30px;">

<h2>' . $LANG['settings_meta_pproduct'] . '</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_product_title" value="' . htmlspecialchars( \query\main::get_option( 'meta_product_title' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_product_desc">' . \query\main::get_option( 'meta_product_desc' ) . '</textarea></div></div>

</div>';

echo '<div class="title" style="margin-top: 30px;">

<h2>' . $LANG['settings_meta_pstore'] . '</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %DESCRIPTION%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_store_title" value="' . htmlspecialchars( \query\main::get_option( 'meta_store_title' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %DESCRIPTION%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_store_desc">' . \query\main::get_option( 'meta_store_desc' ) . '</textarea></div></div>

</div>';

echo '<div class="title" style="margin-top: 30px;">

<h2>' . $LANG['settings_meta_previews'] . '</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_reviews_title" value="' . htmlspecialchars( \query\main::get_option( 'meta_reviews_title' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_reviews_desc">' . \query\main::get_option( 'meta_reviews_desc' ) . '</textarea></div></div>

</div>';

echo '<div class="title" style="margin-top: 30px;">

<h2>' . $LANG['settings_meta_pcategory'] . '</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_metatitle'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_category_title" value="' . htmlspecialchars( \query\main::get_option( 'meta_category_title' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_metadesc'] . ' <span class="info"><span>' . sprintf( $LANG['settings_form_imetatitle'], '%NAME%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_category_desc">' . \query\main::get_option( 'meta_category_desc' ) . '</textarea></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>';

break;

/** APIs AND EXTERNAL ACCOUNTS */

case 'api':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_api_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_api_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  if( isset( $_POST['fbid'] ) && isset( $_POST['fbsecret'] ) &&  isset( $_POST['ggclient'] ) &&  isset( $_POST['ggsecret'] ) &&  isset( $_POST['gguri'] ) && isset( $_POST['ppmode'] ) && isset( $_POST['ppid'] ) && isset( $_POST['ppsecret'] ) && isset( $_POST['feed_server'] ) && isset( $_POST['ggcid'] ) && isset( $_POST['ggcsecret'] ) )
  if( actions::set_option( array( 'facebook_appID' => $_POST['fbid'], 'facebook_secret' => $_POST['fbsecret'], 'google_clientID' => $_POST['ggclient'], 'google_secret' => $_POST['ggsecret'], 'google_ruri' => $_POST['gguri'], 'paypal_mode' => $_POST['ppmode'], 'paypal_ID' => $_POST['ppid'], 'paypal_secret' => $_POST['ppsecret'], 'feedserver' => $_POST['feed_server'], 'feedserver_ID' => $_POST['ggcid'], 'feedserver_secret' => $_POST['ggcsecret'] ) ) )
  echo '<div class="a-success" style="margin-bottom: 15px;">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error" style="margin-bottom: 15px;">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<form action="#" method="POST">

<div class="title">

<h2>Facebook</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_fbid'] . ':</span><div><input type="text" name="fbid" value="' . htmlspecialchars( \query\main::get_option( 'facebook_appID' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_fbsecret'] . ':</span><div><input type="text" name="fbsecret" value="' . htmlspecialchars( \query\main::get_option( 'facebook_secret' ) ) . '" /></div></div>

</div>

<div class="title" style="margin-top: 15px;">

<h2>Google</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_ggclid'] . ':</span><div><input type="text" name="ggclient" value="' . htmlspecialchars( \query\main::get_option( 'google_clientID' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_ggsec'] . ':</span><div><input type="text" name="ggsecret" value="' . htmlspecialchars( \query\main::get_option( 'google_secret' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_ggruri'] . ':</span><div><input type="text" name="gguri" value="' . htmlspecialchars( \query\main::get_option( 'google_ruri' ) ) . '" /></div></div>

</div>

<div class="title" style="margin-top: 15px;">

<h2>PayPal</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_paypmode'] . ':</span>
<div>
<select name="ppmode">';
$paypal_mode = strtolower( \query\main::get_option( 'paypal_mode' ) );
foreach( array( 'sandbox' => 'Sandbox', 'live' => 'Live' ) as $k => $v )echo '<option value="' . $k . '"' . ( $k == $paypal_mode ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_paypid'] . ':</span><div><input type="text" name="ppid" value="' . htmlspecialchars( \query\main::get_option( 'paypal_ID' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_paypsecret'] . ':</span><div><input type="text" name="ppsecret" value="' . htmlspecialchars( \query\main::get_option( 'paypal_secret' ) ) . '" /></div></div>

</div>

<div class="title" style="margin-top: 15px;">

<h2>Feed Server</h2>

</div>

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_feedserver'] . ':</span>
<div>
<select name="feed_server">';
$myserver = strtolower( \query\main::get_option( 'feedserver' ) );
foreach( \site\feed::servers() as $k => $v )echo '<option value="' . $k . '"' . ( $k == $myserver ? ' selected' : '' ) . '>' . htmlspecialchars( $v['name'] ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_ggid'] . ':</span><div><input type="text" name="ggcid" value="' . htmlspecialchars( \query\main::get_option( 'feedserver_ID' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_ggsecret'] . ':</span><div><input type="text" name="ggcsecret" value="' . htmlspecialchars( \query\main::get_option( 'feedserver_secret' ) ) . '" /></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>';

break;

/** SEO LINKS */

case 'seolinks':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_seolinks_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_seolinks_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['post'] ) && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  $post = array_map( function( $w ) {
    return preg_replace( '/[^a-z0-9$]/i', '', strtolower( $w ) );
  }, $_POST['post'] );

  if( actions::set_option( array( 'seo_link_coupon' => $post['coupon'], 'seo_link_product' => $post['product'], 'seo_link_store' => $post['store'], 'seo_link_reviews' => $post['reviews'], 'seo_link_category' => $post['category'], 'seo_link_stores' => $post['stores'], 'seo_link_search' => $post['search'], 'seo_link_user' => $post['user'], 'seo_link_plugin' => $post['plugin'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>' . $LANG['settings_form_lcoupon'] . ':</span><div><input type="text" name="post[coupon]" value="' . htmlspecialchars( ( $seo_coupon = \query\main::get_option( 'seo_link_coupon' ) ) ) . '" maxlength="32" class="sinspan" required />
' . ( $site_url = rtrim( $GLOBALS['siteURL'], '/' ) ) . '/<span style="color: #7D0000;">' . $seo_coupon . '</span>/example_coupon-1.html</div></div>

<div class="row"><span>' . $LANG['settings_form_lproduct'] . ':</span><div><input type="text" name="post[product]" value="' . htmlspecialchars( ( $seo_product = \query\main::get_option( 'seo_link_product' ) ) ) . '" maxlength="32" class="sinspan" required />
' . ( $site_url = rtrim( $GLOBALS['siteURL'], '/' ) ) . '/<span style="color: #7D0000;">' . $seo_product . '</span>/example_product-1.html</div></div>

<div class="row"><span>' . $LANG['settings_form_lstore'] . ':</span><div><input type="text" name="post[store]" value="' . htmlspecialchars( ( $seo_store = \query\main::get_option( 'seo_link_store' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_store . '</span>/example_store-1.html</div></div>

<div class="row"><span>' . $LANG['settings_form_lreviews'] . ':</span><div><input type="text" name="post[reviews]" value="' . htmlspecialchars( ( $seo_reviews = \query\main::get_option( 'seo_link_reviews' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_reviews . '</span>/example_store-1.html</div></div>

<div class="row"><span>' . $LANG['settings_form_lcategory'] . ':</span><div><input type="text" name="post[category]" value="' . htmlspecialchars( ( $seo_category = \query\main::get_option( 'seo_link_category' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_category . '</span>/example_category-1.html</div></div>

<div class="row"><span>' . $LANG['settings_form_lstores'] . ':</span><div><input type="text" name="post[stores]" value="' . htmlspecialchars( ( $seo_stores = \query\main::get_option( 'seo_link_stores' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_stores . '</span>/</div></div>

<div class="row"><span>' . $LANG['settings_form_lsearch'] . ':</span><div><input type="text" name="post[search]" value="' . htmlspecialchars( ( $seo_search = \query\main::get_option( 'seo_link_search' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_search . '</span>/?s=example</div></div>

<div class="row"><span>' . $LANG['settings_form_luser'] . ':</span><div><input type="text" name="post[user]" value="' . htmlspecialchars( ( $seo_user = \query\main::get_option( 'seo_link_user' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_user . '</span>/example.html</div></div>

<div class="row"><span>' . $LANG['settings_form_lplugin'] . ':</span><div><input type="text" name="post[plugin]" value="' . htmlspecialchars( ( $seo_plugin = \query\main::get_option( 'seo_link_plugin' ) ) ) . '" maxlength="32"  class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_plugin . '</span>/example_plugin.html</div></div>


</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>';

break;

/** PRICES */

case 'prices':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_prices_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_prices_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  if( isset( $_POST['store'] ) && isset( $_POST['coupon'] ) && isset( $_POST['max_days'] ) )
  if( actions::set_option( array( 'price_store' => $_POST['store'], 'price_coupon' => $_POST['coupon'], 'price_max_days' => $_POST['max_days'], 'price_product' => $_POST['product'], 'price_product_max_days' => $_POST['max_days_p'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['settings_from_pricstore'] . ' <span class="info"><span>' . $LANG['settings_from_ipricstore'] . '</span></span>:</span><div><input type="number" name="store" value="' . (int) \query\main::get_option( 'price_store' ) . '" min="0" /></div></div>
<div class="row"><span>' . $LANG['settings_from_priccoupon'] . ' <span class="info"><span>' . $LANG['settings_from_ipriccoupon'] . '</span></span>:</span><div><input type="number" name="coupon" value="' . (int) \query\main::get_option( 'price_coupon' ) . '" min="0" /></div></div>
<div class="row"><span>' . $LANG['settings_from_priccouponmd'] . ' <span class="info"><span>' . $LANG['settings_from_ipriccouponmd'] . '</span></span>:</span><div><input type="number" name="max_days" value="' . (int) \query\main::get_option( 'price_max_days' ) . '" min="1" /></div></div>
<div class="row"><span>' . $LANG['settings_from_pricproduct'] . ' <span class="info"><span>' . $LANG['settings_from_ipricproduct'] . '</span></span>:</span><div><input type="number" name="product" value="' . (int) \query\main::get_option( 'price_product' ) . '" min="0" /></div></div>
<div class="row"><span>' . $LANG['settings_from_pricproductmd'] . ' <span class="info"><span>' . $LANG['settings_from_ipricproductmd'] . '</span></span>:</span><div><input type="number" name="max_days_p" value="' . (int) \query\main::get_option( 'price_product_max_days' ) . '" min="1" /></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>

</div>';

break;

/** FEED SETTINGS */

case 'feed':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_feed_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_feed_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  if( actions::set_option( array( 'feed_uppics' => ( isset( $_POST['uphotos'] ) ? 1 : 0 ), 'feed_iexpc' => ( isset( $_POST['feed_iexpc'] ) ? 1 : 0 ), 'feed_moddt' => ( isset( $_POST['feed_moddt'] ) ? 1 : 0 ) ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . $LANG['settings_form_fimimg'] . ':</span><div><input type="checkbox" name="uphotos" id="uphotos"' . ( \query\main::get_option( 'feed_uppics' ) ? ' checked' : '' ) . '/> <label for="uphotos">' . $LANG['msg_feed_upload_imgs'] . '</label></div></div>
<div class="row"><span>' . $LANG['settings_form_prefc'] . ':</span><div>
<input type="checkbox" name="feed_iexpc" id="feed_iexpc"' . ( \query\main::get_option( 'feed_iexpc' ) ? ' checked' : '' ) . '/> <label for="feed_iexpc">' . $LANG['msg_feed_cpnpref_impexp'] . '</label> <br />
<input type="checkbox" name="feed_moddt" id="feed_moddt"' . ( \query\main::get_option( 'feed_moddt' ) ? ' checked' : '' ) . '/> <label for="feed_moddt">' . $LANG['msg_feed_cpnpref_moddt'] . '</label>
</div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>

</div>';

break;

/** CRON LINKS */

case 'cron':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_cron_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_cron_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  if( actions::set_option( array( 'cron_secret' => md5( \site\utils::str_random(10) ) ) ) )
  echo '<div class="a-success" style="margin-bottom: 15px;">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error" style="margin-bottom: 15px;">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

$secret = \query\main::get_option( 'cron_secret' );

echo $LANG['settings_cron_d'];

echo '<div class="title" style="margin-top: 20px;">
  <h2>' . $LANG['settings_cron_clrdat'] . '</h2>
</div>';

echo '<div class="a-message">' . $LANG['settings_cron_clrdat_d'] . '</div>
<div class="page-toolbar hideinput"><input type="text" value="*/5 * * * * wget -O - ' . $GLOBALS['siteURL'] . ( defined( 'SEO_LINKS' ) && SEO_LINKS ? 'cron/cleardata.php?secret=' . $secret : '?cron=cleardata&amp;secret=' . $secret ) . ' >/dev/null 2>&amp;1" onfocus="$(this).select();" readonly /></div>';

echo '<div class="title" style="margin-top: 20px;">
  <h2>' . $LANG['settings_cron_feedi'] . '</h2>
</div>';

echo '<div class="a-message">' . $LANG['settings_cron_feedi_d'] . '</div>
<div class="page-toolbar hideinput"><input type="text" value="* */6 * * * wget -O - ' . $GLOBALS['siteURL'] . ( defined( 'SEO_LINKS' ) && SEO_LINKS ? 'cron/feed.php?secret=' . $secret : '?cron=feed&amp;secret=' . $secret ) . ' >/dev/null 2>&amp;1" onfocus="$(this).select();" readonly /></div>';
echo '<form action="#" method="POST">

<div style="margin: 20px 0;">' . $LANG['settings_cron_sec_d'] . '</div>

<button class="btn">' . $LANG['settings_cron_sec_cbtn'] . '</button>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

break;

/** SOCIAL NETWORKS */

case 'socialacc':

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_socnet_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_socnet_subtitle'] . '</span>';
}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['post'] ) && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

  $post = array_map( function( $w ) {
    if( preg_match( '/^http(s)?:\/\//i', $w ) ) {
      return substr( $w, 0, 200 );
    }
  }, $_POST['post'] );

  if( actions::set_option( array( 'social_facebook' => $post['facebook'], 'social_google' => $post['google'], 'social_twitter' => $post['twitter'], 'social_flickr' => $post['flickr'], 'social_linkedin' => $post['linkedin'], 'social_vimeo' => $post['videmo'], 'social_youtube' => $post['youtube'], 'social_myspace' => $post['myspace'], 'social_reddit' => $post['reddit'], 'social_pinterest' => $post['pinterest'] ) ) )
  echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else
  echo '<div class="a-error">' . $LANG['settings_save_error'] . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>Facebook:</span><div><input type="text" name="post[facebook]" value="' . htmlspecialchars( \query\main::get_option( 'social_facebook' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Google+:</span><div><input type="text" name="post[google]" value="' . htmlspecialchars( \query\main::get_option( 'social_google' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Twitter:</span><div><input type="text" name="post[twitter]" value="' . htmlspecialchars( \query\main::get_option( 'social_twitter' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Flickr:</span><div><input type="text" name="post[flickr]" value="' . htmlspecialchars( \query\main::get_option( 'social_flickr' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Linkedin:</span><div><input type="text" name="post[linkedin]" value="' . htmlspecialchars( \query\main::get_option( 'social_linkedin' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Vimeo:</span><div><input type="text" name="post[videmo]" value="' . htmlspecialchars( \query\main::get_option( 'social_vimeo' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Youtube:</span><div><input type="text" name="post[youtube]" value="' . htmlspecialchars( \query\main::get_option( 'social_youtube' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>MySpace:</span><div><input type="text" name="post[myspace]" value="' . htmlspecialchars( \query\main::get_option( 'social_myspace' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Reddit:</span><div><input type="text" name="post[reddit]" value="' . htmlspecialchars( \query\main::get_option( 'social_reddit' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Pinterest:</span><div><input type="text" name="post[pinterest]" value="' . htmlspecialchars( \query\main::get_option( 'social_pinterest' ) ) . '" maxlength="200" /></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>';

break;

/** GENERAL SETTINGS */

default:

include DIR . '/' . IDIR . '/others/GMT_list.php';

echo '<div class="title">

<h2>' . $LANG['settings_general_title'] . '</h2>';

if( !empty( $LANG['settings_general_subtitle'] ) ) {
  echo '<span>' . $LANG['settings_general_subtitle'] . '</span>';
}

echo '</div>';

if( isset( $_SESSION['js_settings'] ) ) {

  if( isset( $_GET['success'] ) && $_GET['success'] == 'true' )
    echo '<div class="a-success">' . $LANG['msg_saved'] . '</div>';
  else {
    echo '<div class="a-error">' . $LANG['settings_save_error'] . '</div>';
  }

  unset( $_SESSION['js_settings'] );

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="?route=post-actions.php&amp;action=general-settings" method="POST">

<div class="row"><span>' . $LANG['settings_form_sitename'] . ':</span><div><input type="text" name="sitename" value="' . htmlspecialchars( \query\main::get_option( 'sitename' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_siteurl'] . ':</span><div><input type="text" name="siteurl" value="' . htmlspecialchars( \query\main::get_option( 'siteurl' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_sitedesc'] . ':</span><div><textarea name="description">' . \query\main::get_option( 'sitedescription' ) . '</textarea></div></div>
<div class="row"><span>' . $LANG['settings_form_itemspp'] . ':</span><div><input type="number" name="ipp" value="' . (int) \query\main::get_option( 'items_per_page' ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_userregs'] . ':</span>

<div>
<select name="registrations"><option value="opened">' . $LANG['settings_select_opened'] . '</option><option value="closed"' . ( \query\main::get_option( 'registrations' ) != 'opened' ? ' selected' : '' ) . '>' . $LANG['settings_select_closed'] . '</option></select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_accpip'] . ':</span>
<div>
<select name="accounts_per_ip">';
$accounts_per_ip = \query\main::get_option( 'accounts_per_ip' );
foreach( array( 0 => $LANG['unlimited'], 1 => '1 / IP', 2 => '2 / IP', 3 => '3 / IP', 4 => '4 / IP', 5 => '5 / IP', 7 => '7 / IP', 10 => '10 / IP' ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $accounts_per_ip ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_deleteoldc'] . ':</span>
<div>
<select name="delete_old_coupons">';
$delete_old_coupons = \query\main::get_option( 'delete_old_coupons' );
foreach( array_merge( array( 0 => $LANG['never'], 1 => '1 ' . $LANG['day'] ), range( 2,15 ), array( 30, 45, 60, 120, 365 ) ) as $k => $v ) echo '<option value="' . (int) $k . '"' . ( $k == $delete_old_coupons ? ' selected' : '' ) . '>' . ( (int) $k > 1 ? $v . ' ' . $LANG['days'] : $v ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_allowrev'] . ':</span>
<div>
<select name="allow_revs">';
$allow_reviews = \query\main::get_option( 'allow_reviews' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'], 2 => $LANG['settings_option_onlyvalid'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_reviews ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_autovalrev'] . ':</span>
<div>
<select name="auvalid_revs">';
$review_validate = (boolean) \query\main::get_option( 'review_validate' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $review_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_allowsto'] . ':</span>
<div>
<select name="allow_stores">';
$allow_stores = (boolean) \query\main::get_option( 'allow_stores' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_stores ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_autovalsto'] . ':</span>
<div>
<select name="auvalid_stos">';
$store_validate = (boolean) \query\main::get_option( 'store_validate' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $store_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_allowcou'] . ':</span>
<div>
<select name="allow_coupons">';
$allow_coupons = (boolean) \query\main::get_option( 'allow_coupons' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_coupons ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_autovalcou'] . ':</span>
<div>
<select name="auvalid_cous">';
$coupon_validate = (boolean) \query\main::get_option( 'coupon_validate' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $coupon_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_allowprod'] . ':</span>
<div>
<select name="allow_products">';
$allow_coupons = (boolean) \query\main::get_option( 'allow_products' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_coupons ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_autovalprod'] . ':</span>
<div>
<select name="auvalid_prods">';
$coupon_validate = (boolean) \query\main::get_option( 'product_validate' );
foreach( array( 0 => $LANG['no'], 1 => $LANG['yes'] ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $coupon_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_sitelang'] . ':</span>
<div>
<select name="site_lang">';
$sitelang = \query\main::get_option( 'sitelang' );
foreach( ( $languages = \site\language::languages() ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $sitelang ? ' selected' : '' ) . '>' . htmlspecialchars( $v['name'] ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_adminlang'] . ':</span>
<div>
<select name="adminpanel_lang">';
$adminlang = \query\main::get_option( 'adminpanel_lang' );
foreach( $languages as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $adminlang ? ' selected' : '' ) . '>' . htmlspecialchars( $v['name'] ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_form_adminthm'] . ':</span>
<div>
<select name="admin_theme">';
$admintheme = \query\main::get_option( 'admintheme' );
foreach( array( 'Default' => 'theme/default.css', 'Blue' => 'theme/blue.css', 'Green' => 'theme/green.css' ) as $k => $v )echo '<option value="' . $v . '"' . ( $v == $admintheme ? ' selected' : '' ) . '>' . htmlspecialchars( $k ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . $LANG['settings_timezone'] . ':</span><div><select name="timezone">';
$timezone = \query\main::get_option( 'timezone' );    ;
foreach( $gmt as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $timezone ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div></div>

<div class="row"><span>' . $LANG['settings_hour_format'] . ':</span><div><select name="hour_format">';
$hourformat = \query\main::get_option( 'hour_format' );
foreach( array( 12, 24 ) as $k ) echo '<option value="' . $k . '"' . ( $k == $hourformat ? ' selected' : '' ) . '>' . $k . (' ' . strtolower( $LANG['hours'] )) . '</option>';
echo '</select></div></div>

<div class="row"><span>' . $LANG['settings_form_emailfn'] . ' <span class="info"><span>' . $LANG['settings_form_iemailfn'] . '</span></span>:</span><div><input type="text" name="email_from_name" value="' . htmlspecialchars( \query\main::get_option( 'email_from_name' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_emailas'] . ' <span class="info"><span>' . $LANG['settings_form_iemailas'] . '</span></span>:</span><div><input type="email" name="email_answer_to" value="' . htmlspecialchars( \query\main::get_option( 'email_answer_to' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_emailcntct'] . ' <span class="info"><span>' . $LANG['settings_form_iemailcntct'] . '</span></span>:</span><div><input type="email" name="email_contact" value="' . htmlspecialchars( \query\main::get_option( 'email_contact' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_form_mailmeth'] . ':</span><div><select name="mail_meth">';
foreach( array( 'PHP Mail', 'sendmail', 'SMTP' ) as $meth ) echo '<option value="' . $meth . '"' . ( \query\main::get_option( 'mail_method' ) == $meth ? ' selected' : '' ) . '>' . $meth . '</option>';
echo '</select></div></div>

<div' . ( \query\main::get_option( 'mail_method' ) != 'SMTP' ? ' style="display: none;"' : '' ) . '>
<div class="row"><span>' . $LANG['settings_from_smtpauth'] . ':</span><div><input type="checkbox" name="smtp_auth"' . ( \query\main::get_option( 'smtp_auth' ) ? ' checked' : '' ) . ' /></div></div>
<div class="row"><span>' . $LANG['settings_from_smtphost'] . ':</span><div><input type="text" name="smtp_host" value="' . htmlspecialchars( \query\main::get_option( 'smtp_host' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_from_smtpport'] . ':</span><div><input type="text" name="smtp_port" value="' . htmlspecialchars( \query\main::get_option( 'smtp_port' ) ) . '" /></div></div>
<div class="row"><span>' . $LANG['settings_from_smtpuser'] . ':</span><div><input type="text" name="smtp_user" value="' . htmlspecialchars( \query\main::get_option( 'smtp_user' ) ). '" /></div></div>
<div class="row"><span>' . $LANG['settings_from_smtppass'] . ':</span><div><input type="text" name="smtp_pass" value="' . htmlspecialchars( \query\main::get_option( 'smtp_password' ) ) . '" /></div></div>
</div>

<div' . ( \query\main::get_option( 'mail_method' ) != 'sendmail' ? ' style="display: none;"' : '' ) . '>
<div class="row"><span>' . $LANG['settings_from_snmapath'] . ':</span><div><input type="text" name="sendmail_path" value="' . htmlspecialchars( \query\main::get_option( 'sendmail_path' ) ) . '" /></div></div>
</div>

<div class="row"><span>' . $LANG['settings_form_mailsign'] . ':</span><div><textarea name="mailsign">' . \query\main::get_option( 'mail_signature' ) . '</textarea></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . $LANG['settings_save_button'] . '</button>

</form>

</div>';

break;

}