<?php

/*

CHECK IF HAVE FAVORITE STOES

*/

function have_favorites( $category = array() ) {
    return \query\main::have_favorites( $category );
}

/*

INFORMATIONS ABOUT FAVORITE STORES

*/

function favorites( $category = array() ) {
    return \query\main::while_favorites( $category );
}

/*

CHECK IF HAVE SOMETHING ON THE WALL - COUPONS

*/

function have_wall( $category = array() ) {
    return \query\main::have_wall( $category );
}

/*

INFORMATIONS ABOUT THE WALL  - COUPONS

*/

function wall( $category = array() ) {
    return \query\main::while_wall( $category );
}

/*

CHECK IF HAVE SOMETHING ON THE WALL - PRODUCTS

*/

function have_wall_products( $category = array() ) {
    return \query\main::have_wall_products( $category );
}

/*

INFORMATIONS ABOUT THE WALL - PRODUCTS

*/

function wall_products( $category = array() ) {
    return \query\main::while_wall_products( $category );
}

/*

CHECK IF HAVE STORES

*/

function have_stores( $category = array() ) {
if( !$GLOBALS['me'] ) {
    return false;
}
    return \query\main::have_stores( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}


/*

INFORMATIONS ABOUT STORES

*/

function stores( $category = array() ) {
if( !$GLOBALS['me'] ) {
    return false;
}
    return \query\main::while_stores( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}

/*

CHECK IF HAVE COUPONS

*/

function have_coupons( $category = array() ) {
if( !$GLOBALS['me'] ) {
    return false;
}
    return \query\main::have_items( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}


/*

INFORMATIONS ABOUT COUPONS

*/

function coupons( $category = array() ) {
if( !$GLOBALS['me'] ) {
    return false;
}
    return \query\main::while_items( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}

/*

CHECK IF HAVE PRODUCTS

*/

function have_products( $category = array() ) {
if( !$GLOBALS['me'] ) {
    return false;
}
    return \query\main::have_products( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}


/*

INFORMATIONS ABOUT PRODUCTS

*/

function products( $category = array() ) {
if( !$GLOBALS['me'] ) {
    return false;
}
    return \query\main::while_products( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}

/*

GET REWARD

*/

function create_reward_request( $id = 0, $post = array() ) {

global $LANG;

/* This is not protected to CSRF attacks, just protect it where you use it */

  $form = '';

  if( !empty( $id ) || $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['Reward-ID'] ) ) {

  $form = '<div class="other_form">';

  $id = empty( $id ) ? (int) $_POST['Reward-ID'] : $id;
  $post = empty( $post ) ? ( isset( $_POST['Reward'][$id] ) ? (array) $_POST['Reward'][$id] : '' ) : $post;

  try {

    \user\main::get_reward( $id, $post );

    $form .= '<div class="success">' . $LANG['claim_reward_success'] . '</div>';

    unset( $_POST );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  $form .= '</div>';

  }

  return $form;

}

/*

EDIT PROFILE FORM

*/

function edit_profile_form() {

global $LANG;

if( $GLOBALS['me'] ) {

  $form = '<div class="edit_profile_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['edit_profile_form'] ) && \site\utils::check_csrf( $_POST['edit_profile_form']['csrf'], 'edit_profile_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['edit_profile_form'] );

  try {

    $user_info = \user\main::edit_profile( $GLOBALS['me']->ID, $pd );

    $GLOBALS['me']->Avatar = $user_info->avatar;

    $form .= '<div class="success">' . $LANG['profile_success'] . '</div>';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['edit_profile_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#" enctype="multipart/form-data">
  <div class="form_field"><label for="edit_profile_form[username]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="edit_profile_form[username]" id="edit_profile_form[username]" value="' . ( isset( $pd['username'] ) ? $pd['username'] : $GLOBALS['me']->Name ) . '" required /></div></div>
  <div class="form_field"><label for="edit_profile_form[email]">' . $LANG['form_email'] . ':</label> <div><input type="text" name="edit_profile_form[email]" id="edit_profile_form[email]" value="' . $GLOBALS['me']->Email . '" disabled /></div></div>
  <div class="form_field"><label for="edit_profile_form_avatar">' . $LANG['form_avatar'] . ':</label> <div><img src="' . user_avatar( $GLOBALS['me']->Avatar ) . '" alt="" style="width:80px; height:80px;" /> <input type="file" name="edit_profile_form_avatar" id="edit_profile_form_avatar" />
  <span>Note:* max width: 300px, max height: 300px.</span></div></div>
  <div class="form_field"><label for="edit_profile_form[subscriber]">' . $LANG['form_subscriber'] . ':</label> <div><input type="checkbox" name="edit_profile_form[subscriber]" id="edit_profile_form[subscriber]" ' . ( isset( $pd['subscriber'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' && $GLOBALS['me']->is_subscribed ? 'checked' : '' ) . ' /> ' . $LANG['msg_subscribe'] . '</div></div>
  <input type="hidden" name="edit_profile_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['profile_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

CHANGE PASSWORD FORM

*/

function change_password_form() {

global $LANG;

if( $GLOBALS['me'] ) {

  $form = '<div class="change_password_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['change_password_form'] ) && \site\utils::check_csrf( $_POST['change_password_form']['csrf'], 'change_password_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['change_password_form'] );

  try {

    \user\main::change_password( $GLOBALS['me']->ID, $pd );
    $form .= '<div class="success">' . $LANG['change_pwd_success'] . '</div>';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['change_password_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="change_password_form[old]">' . $LANG['change_pwd_form_old'] . ':</label> <div><input type="password" name="change_password_form[old]" id="change_password_form[old]" value="" required /></div></div>
  <div class="form_field"><label for="change_password_form[new]">' . $LANG['change_pwd_form_new'] . ':</label> <div><input type="password" name="change_password_form[new]" id="change_password_form[new]" value="" required /></div></div>
  <div class="form_field"><label for="change_password_form[new2]">' . $LANG['change_pwd_form_new2'] . ':</label> <div><input type="password" name="change_password_form[new2]" id="change_password_form[new2]" value="" required /></div></div>
  <input type="hidden" name="change_password_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['change_pwd_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

SUBMIT NEW COUPON FORM

*/

function submit_coupon_form( $auto_select = array( 'store' => '' ) ) {

global $LANG;

if( $GLOBALS['me'] ) {

if( $GLOBALS['me']->Stores > 0 ) {

  if( ! (boolean) \query\main::get_option( 'allow_coupons' ) ) {

    return '<div class="info_form">' . $LANG['submit_cou_not_allowed'] . '</div>';

  }

  $form = '<div class="submit_coupon_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['submit_coupon_form'] ) && \site\utils::check_csrf( $_POST['submit_coupon_form']['csrf'], 'submit_coupon_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['submit_coupon_form'] );

  try {

    \user\main::submit_coupon( $GLOBALS['me']->ID, $pd );
    $form .= '<div class="success">' . $LANG['submit_cou_success'] . '</div>';

    unset( $pd );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['submit_coupon_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="submit_coupon_form[store]">' . $LANG['submit_cou_addto'] . '</label>
  <div><select name="submit_coupon_form[store]" id="submit_coupon_form[store]">';
  foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ) ) as $v )$form .= '<option value="' . $v->ID . '"' . ( ( !isset( $pd['store'] ) && !empty( $auto_select['store'] ) && ( $auto_select['store'] == $v->ID || $auto_select['store'] == $v->name ) ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . $v->name . '</option>';
  $form .= '</select></div>
  </div>
  <div class="form_field"><label for="submit_coupon_form[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="submit_coupon_form[name]" id="submit_coupon_form[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" placeholder="' . $LANG['submit_cou_name_ph'] . '" required /></div></div>
  <div class="form_field"><label for="submit_coupon_form[code]">' . $LANG['form_code'] . ':</label> <div><input type="text" name="submit_coupon_form[code]" id="submit_coupon_form[code]" value="' . ( isset( $pd['code'] ) ? $pd['code'] : '' ) . '" placeholder="' . $LANG['submit_cou_code_ph'] . '" /></div></div>
  <div class="form_field"><label for="submit_coupon_form[url]">' . $LANG['form_coupon_url'] . ':</label> <div><input type="text" name="submit_coupon_form[url]" id="submit_coupon_form[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : '' ) . '" placeholder="' . $LANG['submit_cou_url_ph'] . '" /></div></div>
  <div class="form_field"><label for="submit_coupon_form[description]">' . $LANG['form_description'] . ':</label> <div><textarea name="submit_coupon_form[description]" id="submit_coupon_form[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>
  <div class="form_field"><label for="submit_coupon_form[tags]">' . $LANG['form_tags'] . ':</label> <div><input type="text" name="submit_coupon_form[tags]" id="submit_coupon_form[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : '' ) . '" /></div></div>
  <div class="form_field"><label for="submit_coupon_form[start]">' . $LANG['form_start_date'] . ':</label> <div><input type="date" name="submit_coupon_form[start]" id="submit_coupon_form[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : '' ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="submit_coupon_form[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : '00:00' ) . '" style="width: 20%" /></div></div>
  <div class="form_field"><label for="submit_coupon_form[end]">' . $LANG['form_end_date'] . ':</label> <div><input type="date" name="submit_coupon_form[end]" id="submit_coupon_form[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : '' ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="submit_coupon_form[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : '00:00' ) . '" style="width: 20%" /></div></div>
  <input type="hidden" name="submit_coupon_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['submit_cou_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form2'] . '</div>';

}

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

EDIT COUPON FORM

*/

function edit_coupon_form( $id ) {

global $LANG;

if( $GLOBALS['me'] ) {

if( $GLOBALS['me']->Stores > 0 ) {

$coupon = \query\main::item_infos( $id );

  if( $coupon->userID !== $GLOBALS['me']->ID ) {

    return '<div class="info_form">' . $LANG['edit_cou_cant'] . '</div>';

  }

  $form = '<div class="edit_coupon_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['edit_coupon_form'] ) && \site\utils::check_csrf( $_POST['edit_coupon_form']['csrf'], 'edit_coupon_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['edit_coupon_form'] );

  try {

    \user\main::edit_coupon( $id, $GLOBALS['me']->ID, $pd );
    $form .= '<div class="success">' . $LANG['edit_cou_success'] . '</div>';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['edit_coupon_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#">
  <div class="form_field"><label for="edit_coupon_form[store]">' . $LANG['submit_cou_addto'] . ':</label>
  <div><select name="edit_coupon_form[store]" id="edit_coupon_form[store]">';
  foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ) ) as $v )$form .= '<option value="' . $v->ID . '"' . ( ( !isset( $pd['store'] ) && $coupon->storeID == $v->ID ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . $v->name . '</option>';
  $form .= '</select></div>
  </div>
  <div class="form_field"><label for="edit_coupon_form[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="edit_coupon_form[name]" id="edit_coupon_form[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : $coupon->title ) . '" placeholder="' . $LANG['submit_cou_name_ph'] . '" required /></div></div>
  <div class="form_field"><label for="edit_coupon_form[code]">' . $LANG['form_code'] . ':</label> <div><input type="text" name="edit_coupon_form[code]" id="edit_coupon_form[code]" value="' . ( isset( $pd['code'] ) ? $pd['code'] : $coupon->code ) . '" placeholder="' . $LANG['submit_cou_code_ph'] . '" /></div></div>
  <div class="form_field"><label for="edit_coupon_form[url]">' . $LANG['form_coupon_url'] . ':</label> <div><input type="text" name="edit_coupon_form[url]" id="edit_coupon_form[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : $coupon->url ) . '" placeholder="' . $LANG['submit_cou_url_ph'] . '" /></div></div>
  <div class="form_field"><label for="edit_coupon_form[description]">' . $LANG['form_description'] . ':</label> <div><textarea name="edit_coupon_form[description]" id="edit_coupon_form[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : $coupon->description ) . '</textarea></div></div>
  <div class="form_field"><label for="edit_coupon_form[tags]">' . $LANG['form_tags'] . ':</label> <div><input type="text" name="edit_coupon_form[tags]" id="edit_coupon_form[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : $coupon->tags ) . '" /></div></div>
  <div class="form_field"><label for="edit_coupon_form[start]">' . $LANG['form_start_date'] . ':</label> <div><input type="date" name="edit_coupon_form[start]" id="edit_coupon_form[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : date( 'Y-m-d', strtotime( $coupon->start_date ) ) ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="edit_coupon_form[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : date( 'H:i', strtotime( $coupon->start_date ) ) ) . '" style="width: 20%" /></div></div>
  <div class="form_field"><label for="edit_coupon_form[end]">' . $LANG['form_end_date'] . ':</label> <div><input type="date" name="edit_coupon_form[end]" id="edit_coupon_form[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : date( 'Y-m-d', strtotime( $coupon->expiration_date ) ) ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="edit_coupon_form[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : date( 'H:i', strtotime( $coupon->expiration_date ) ) ) . '" style="width: 20%" /></div></div>
  <input type="hidden" name="edit_coupon_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['edit_cou_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form2'] . '</div>';

}

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

SUBMIT NEW PRODUCT FORM

*/

function submit_product_form( $auto_select = array( 'store' => '' ) ) {

global $LANG;

if( $GLOBALS['me'] ) {

if( $GLOBALS['me']->Stores > 0 ) {

  if( ! (boolean) \query\main::get_option( 'allow_products' ) ) {

    return '<div class="info_form">' . $LANG['submit_prod_not_allowed'] . '</div>';

  }

  $form = '<div class="submit_product_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['submit_product_form'] ) && \site\utils::check_csrf( $_POST['submit_product_form']['csrf'], 'submit_coupon_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['submit_product_form'] );

  try {

    \user\main::submit_product( $GLOBALS['me']->ID, $pd );
    $form .= '<div class="success">' . $LANG['submit_cou_success'] . '</div>';

    unset( $pd );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['submit_coupon_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#" enctype="multipart/form-data">
  <div class="form_field"><label for="submit_product_form[store]">' . $LANG['submit_prod_addto'] . '</label>
  <div><select name="submit_product_form[store]" id="submit_product_form[store]">';
  foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ) ) as $v )$form .= '<option value="' . $v->ID . '"' . ( ( !isset( $pd['store'] ) && !empty( $auto_select['store'] ) && ( $auto_select['store'] == $v->ID || $auto_select['store'] == $v->name ) ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . $v->name . '</option>';
  $form .= '</select></div>
  </div>
  <div class="form_field"><label for="submit_product_form[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="submit_product_form[name]" id="submit_product_form[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" placeholder="' . $LANG['submit_prod_name_ph'] . '" required /></div></div>
  <div class="form_field"><label for="submit_product_form[price]">' . $LANG['form_price'] . ':</label> <div><input type="text" name="submit_product_form[price]" id="submit_product_form[price]" value="' . ( isset( $pd['price'] ) ? $pd['price'] : '' ) . '" placeholder="' . $LANG['submit_prod_price_ph'] . '" /></div></div>
  <div class="form_field"><label for="submit_product_form[old_price]">' . $LANG['form_old_price'] . ':</label> <div><input type="text" name="submit_product_form[old_price]" id="submit_product_form[old_price]" value="' . ( isset( $pd['old_price'] ) ? $pd['old_price'] : '' ) . '" placeholder="' . $LANG['submit_prod_oldprice_ph'] . '" /></div></div>
  <div class="form_field"><label for="submit_product_form[currency]">' . $LANG['currency'] . ':</label> <div><input type="text" name="submit_product_form[currency]" id="submit_product_form[currency]" value="' . ( isset( $pd['currency'] ) ? $pd['currency'] : CURRENCY ) . '" /></div></div>
  <div class="form_field"><label for="submit_product_form[url]">' . $LANG['form_product_url'] . ':</label> <div><input type="text" name="submit_product_form[url]" id="submit_product_form[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : '' ) . '" /></div></div>
  <div class="form_field"><label for="submit_product_form[description]">' . $LANG['form_description'] . ':</label> <div><textarea name="submit_product_form[description]" id="submit_product_form[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>
  <div class="form_field"><label for="submit_product_form[tags]">' . $LANG['form_tags'] . ':</label> <div><input type="text" name="submit_product_form[tags]" id="submit_product_form[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : '' ) . '" /></div></div>
  <div class="form_field"><label for="submit_product_form_image">' . $LANG['form_image'] . ':</label> <div><img src="' . product_avatar('') . '" alt="" style="width:90px; height:90px;" /> <input type="file" name="submit_product_form_image" id="submit_product_form_image" />
  <span>Note:* max width: 800px, max height: 800px.</span></div></div>
  <div class="form_field"><label for="submit_product_form[start]">' . $LANG['form_start_date'] . ':</label> <div><input type="date" name="submit_product_form[start]" id="submit_product_form[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : '' ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="submit_product_form[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : '00:00' ) . '" style="width: 20%" /></div></div>
  <div class="form_field"><label for="submit_product_form[end]">' . $LANG['form_end_date'] . ':</label> <div><input type="date" name="submit_product_form[end]" id="submit_product_form[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : '' ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="submit_product_form[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : '00:00' ) . '" style="width: 20%" /></div></div>
  <input type="hidden" name="submit_product_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['submit_prod_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form2'] . '</div>';

}

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

EDIT PRODUCT FORM

*/

function edit_product_form( $id ) {

global $LANG;

if( $GLOBALS['me'] ) {

if( $GLOBALS['me']->Stores > 0 ) {

$product = \query\main::product_infos( $id );

  if( $product->userID !== $GLOBALS['me']->ID ) {

    return '<div class="info_form">' . $LANG['edit_prod_cant'] . '</div>';

  }

  /* */
  $product_image = $product->image;

  $form = '<div class="edit_product_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['edit_product_form'] ) && \site\utils::check_csrf( $_POST['edit_product_form']['csrf'], 'edit_coupon_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['edit_product_form'] );

  try {

    $post_info = \user\main::edit_product( $id, $GLOBALS['me']->ID, $pd );
    $product_image = $post_info->image;

    $form .= '<div class="success">' . $LANG['edit_cou_success'] . '</div>';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['edit_coupon_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#" enctype="multipart/form-data">
  <div class="form_field"><label for="edit_product_form[store]">' . $LANG['submit_prod_addto'] . ':</label>
  <div><select name="edit_product_form[store]" id="edit_product_form[store]">';
  foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ) ) as $v )$form .= '<option value="' . $v->ID . '"' . ( ( !isset( $pd['store'] ) && $product->storeID == $v->ID ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . $v->name . '</option>';
  $form .= '</select></div>
  </div>
  <div class="form_field"><label for="edit_product_form[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="edit_product_form[name]" id="edit_product_form[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : $product->title ) . '" placeholder="' . $LANG['submit_prod_name_ph'] . '" required /></div></div>
  <div class="form_field"><label for="edit_product_form[price]">' . $LANG['form_price'] . ':</label> <div><input type="text" name="edit_product_form[price]" id="edit_product_form[price]" value="' . ( isset( $pd['price'] ) ? $pd['price'] : ( empty( $product->price ) ? '' : $product->price ) ) . '" placeholder="' . $LANG['submit_prod_price_ph'] . '" /></div></div>
  <div class="form_field"><label for="edit_product_form[old_price]">' . $LANG['form_old_price'] . ':</label> <div><input type="text" name="edit_product_form[old_price]" id="edit_product_form[old_price]" value="' . ( isset( $pd['old_price'] ) ? $pd['old_price'] : ( empty( $product->old_price ) ? '' : $product->old_price ) ) . '" placeholder="' . $LANG['submit_prod_oldprice_ph'] . '" /></div></div>
  <div class="form_field"><label for="edit_product_form[currency]">' . $LANG['form_currency'] . ':</label> <div><input type="text" name="edit_product_form[currency]" id="edit_product_form[currency]" value="' . ( isset( $pd['currency'] ) ? $pd['currency'] : $product->currency ) . '" /></div></div>
  <div class="form_field"><label for="edit_product_form[url]">' . $LANG['form_product_url'] . ':</label> <div><input type="text" name="edit_product_form[url]" id="edit_product_form[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : $product->url ) . '" placeholder="' . $LANG['submit_cou_url_ph'] . '" /></div></div>
  <div class="form_field"><label for="edit_product_form[description]">' . $LANG['form_description'] . ':</label> <div><textarea name="edit_product_form[description]" id="edit_product_form[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : $product->description ) . '</textarea></div></div>
  <div class="form_field"><label for="edit_product_form[tags]">' . $LANG['form_tags'] . ':</label> <div><input type="text" name="edit_product_form[tags]" id="edit_product_form[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : $product->tags ) . '" /></div></div>
  <div class="form_field"><label for="edit_product_form_image">' . $LANG['form_image'] . ':</label> <div><img src="' . product_avatar( $product_image ) . '" alt="" style="width:90px; height:90px;" /> <input type="file" name="edit_product_form_image" id="edit_product_form_image" />
  <span>Note:* max width: 800px, max height: 800px.</span></div></div>
  <div class="form_field"><label for="edit_product_form[start]">' . $LANG['form_start_date'] . ':</label> <div><input type="date" name="edit_product_form[start]" id="edit_product_form[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : date( 'Y-m-d', strtotime( $product->start_date ) ) ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="edit_product_form[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : date( 'H:i', strtotime( $product->start_date ) ) ) . '" style="width: 20%" /></div></div>
  <div class="form_field"><label for="edit_product_form[end]">' . $LANG['form_end_date'] . ':</label> <div><input type="date" name="edit_product_form[end]" id="edit_product_form[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : date( 'Y-m-d', strtotime( $product->expiration_date ) ) ) . '" style="width: 79%; margin-right: 1%;" /><input type="time" name="edit_product_form[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : date( 'H:i', strtotime( $product->expiration_date ) ) ) . '" style="width: 20%" /></div></div>
  <input type="hidden" name="edit_product_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['edit_prod_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form2'] . '</div>';

}

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

SUBMIT NEW STORE FORM

*/

function submit_store_form( $auto_select = array( 'store' => '' ) ) {

global $LANG;

if( $GLOBALS['me'] ) {

  if( ! (boolean) \query\main::get_option( 'allow_stores' ) ) {

    return '<div class="info_form">' . $LANG['submit_store_not_allowed'] . '</div>';

  }

  $form = '<div class="submit_store_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['submit_store_form'] ) && \site\utils::check_csrf( $_POST['submit_store_form']['csrf'], 'submit_store_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['submit_store_form'] );

  try {

    \user\main::submit_store( $GLOBALS['me']->ID, $pd );
    $form .= '<div class="success">' . $LANG['submit_store_success'] . '</div>';

    unset( $pd );

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['submit_store_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#" enctype="multipart/form-data">
  <div class="form_field"><label for="submit_store_form[category]">' . $LANG['form_category'] . '</label>
  <div><select name="submit_store_form[category]" id="submit_store_form[category]">';
  foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
  $wcat = '<optgroup label="' . $cat['infos']->name . '">';
  $wcat .= '<option value="' . $cat['infos']->ID . '"' . ( isset( $pd['category'] ) && $pd['category']== $cat['infos']->ID ? ' selected' : '' ) . '>' . $cat['infos']->name . '</option>';
  if( isset( $cat['subcats'] ) ) {
    foreach( $cat['subcats'] as $subcat ) {
      $wcat .= '<option value="' . $subcat->ID . '"' . ( isset( $pd['category'] ) && $pd['category']== $cat['infos']->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
    }
  }
  $wcat .= '</optgroup>';
  $form .= $wcat;
  }

  $form .= '</select></div>
  </div>
  <div class="form_field"><label for="submit_store_form[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="submit_store_form[name]" id="submit_store_form[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" placeholder="' . $LANG['submit_store_name_ph'] . '" required /></div></div>
  <div class="form_field"><label for="submit_store_form[url]">' . $LANG['form_store_url'] . ':</label> <div><input type="text" name="submit_store_form[url]" id="submit_store_form[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : '' ) . '" placeholder="http://" required /></div></div>
  <div class="form_field"><label for="submit_store_form[description]">' . $LANG['form_description'] . ':</label> <div><textarea name="submit_store_form[description]" id="submit_store_form[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>
  <div class="form_field"><label for="submit_store_form[tags]">' . $LANG['form_tags'] . ':</label> <div><input type="text" name="submit_store_form[tags]" id="submit_store_form[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : '' ) . '" /></div></div>
  <div class="form_field"><label for="submit_store_form_logo">' . $LANG['form_logo'] . ':</label> <div><input type="file" name="submit_store_form_logo" id="submit_store_form_logo" />
  <span>Note:* max width: 600px, max height: 400px.</span></div></div>
  <input type="hidden" name="submit_store_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['submit_store_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}

/*

SUBMIT STORE FORM

*/

function edit_store_form( $id ) {

global $LANG;

if( $GLOBALS['me'] ) {

if( $GLOBALS['me']->Stores > 0 ) {

$store = \query\main::store_infos( $id );

  if( $store->userID !== $GLOBALS['me']->ID ) {

    return '<div class="info_form">' . $LANG['edit_store_cant'] . '</div>';

  }

  /* */
  $store_image = $store->image;

  $form = '<div class="edit_store_form other_form">';

  if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['edit_store_form'] ) && \site\utils::check_csrf( $_POST['edit_store_form']['csrf'], 'edit_store_csrf' ) ) {

  $pd = \site\utils::validate_user_data( $_POST['edit_store_form'] );

  try {

    $post_info = \user\main::edit_store( $id, $GLOBALS['me']->ID, $pd );
    $store_image = $post_info->image;

    $form .= '<div class="success">' . $LANG['edit_store_success'] . '</div>';

  }

  catch( Exception $e ){
    $form .= '<div class="error">' . $e->getMessage() . '</div>';
  }

  }

  $csrf = $_SESSION['edit_store_csrf'] = \site\utils::str_random(12);

  $form .= '<form method="POST" action="#" enctype="multipart/form-data">
  <div class="form_field"><label for="edit_store_form[category]">' . $LANG['form_category'] . '</label>
  <div><select name="edit_store_form[category]" id="edit_store_form[category]">';
  foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
  $wcat = '<optgroup label="' . $cat['infos']->name . '">';
  $wcat .= '<option value="' . $cat['infos']->ID . '"' . ( isset( $store->catID ) && $store->catID == $cat['infos']->ID ? ' selected' : '' ) . '>' . $cat['infos']->name . '</option>';
  if( isset( $cat['subcats'] ) ) {
    foreach( $cat['subcats'] as $subcat ) {
      $wcat .= '<option value="' . $subcat->ID . '"' . ( isset( $store->catID ) && $store->catID == $subcat->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
    }
  }
  $wcat .= '</optgroup>';
  $form .= $wcat;
  }

  $form .= '</select></div>
  </div>
  <div class="form_field"><label for="edit_store_form[name]">' . $LANG['form_name'] . ':</label> <div><input type="text" name="edit_store_form[name]" id="edit_store_form[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : $store->name ) . '" placeholder="' . $LANG['edit_store_name_ph'] . '" required /></div></div>
  <div class="form_field"><label for="edit_store_form[url]">' . $LANG['form_store_url'] . ':</label> <div><input type="text" name="edit_store_form[url]" id="edit_store_form[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : $store->url ) . '" placeholder="http://" required /></div></div>
  <div class="form_field"><label for="edit_store_form[description]">' . $LANG['form_description'] . ':</label> <div><textarea name="edit_store_form[description]" id="edit_store_form[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : $store->description ) . '</textarea></div></div>
  <div class="form_field"><label for="edit_store_form[tags]">' . $LANG['form_tags'] . ':</label> <div><input type="text" name="edit_store_form[tags]" id="edit_store_form[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : $store->tags ) . '" /></div></div>
  <div class="form_field"><label for="edit_store_form_logo">' . $LANG['form_logo'] . ':</label> <div><img src="' . store_avatar( $store_image ) . '" alt="" style="width:100px; height:50px;" /> <input type="file" name="edit_store_form_logo" id="edit_store_form_logo" />
  <span>Note:* max width: 600px, max height: 400px.</span></div></div>
  <input type="hidden" name="edit_store_form[csrf]" value="' . $csrf . '" />
  <button>' . $LANG['edit_store_button'] . '</button>
  </form>

  </div>';

  return $form;

} else {

  return '<div class="info_form">' . $LANG['unavailable_form2'] . '</div>';

}

} else {

  return '<div class="info_form">' . $LANG['unavailable_form'] . '</div>';

}

}