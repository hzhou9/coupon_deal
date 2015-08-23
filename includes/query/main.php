<?php

namespace query;

/** */

class main {

/*

GET NUMBER OF STORES

*/

public static function stores( $categories = array() ) {
  return \query\main::have_stores( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF CATEGORIES

*/

public static function categories( $categories = array() ) {
  return \query\main::have_categories( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF COUPONS

*/

public static function coupons( $categories = array() ) {
  return \query\main::have_items( $categories, '', array( 'only_count' => '' ) );
}

/*

GET NUMBER OF PRODUCTS

*/

public static function products( $categories = array() ) {
  return \query\main::have_products( $categories, '', array( 'only_count' => '' ) );
}

/*

GET NUMBER OF PAGES

*/

public static function pages( $categories = array() ) {
  return \query\main::have_pages( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF USERS

*/

public static function users( $categories = array() ) {
  return \query\main::have_users( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF REVIEWS

*/

public static function reviews( $categories = array() ) {
  return \query\main::have_reviews( $categories, '', array( 'only_count' => '' ) );
}

/*

GET NUMBER OF REWARDS

*/

public static function rewards( $categories = array() ) {
  return \query\main::have_rewards( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF CLAIM REWARD REQUESTS

*/

public static function rewards_reqs( $categories = array() ) {
  return \query\main::have_rewards_reqs( $categories, array( 'only_count' => '' ) );
}

/*

GET NUMBER OF FAVORITE STORES

*/

public static function favorites( $categories = array() ) {
  return \query\main::have_favorites( $categories, array( 'only_count' => '' ) );
}

/*

GET OPTIONS

*/

public static function get_option( $option = '' ) {

global $db;

  if( empty( $option ) ) {

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT option_name, option_value FROM " . DB_TABLE_PREFIX . "options");
  $stmt->execute();
  $stmt->bind_result( $name, $value );

  $params = array();

  while ( $stmt->fetch() ) {
    $params[$name] = $value;
  }

  $stmt->close();

  return (object) $params;

  } else {

  $cache = new \cache\main;

  if( $show_from_cache = $cache->check( 'options_' . $option ) ) {

    return $show_from_cache;

  } else {

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*), option_value FROM " . DB_TABLE_PREFIX . "options WHERE option_name = ?");
  $stmt->bind_param( "s", $option );
  $stmt->execute();
  $stmt->bind_result( $count, $value );
  $stmt->fetch();
  $stmt->close();

  if( empty( $count ) ) {
    return false;
  }

  $cache->add( 'options_' . $option, $value );

  return $value;

  }

  }

}

/*

SHOW USER PLUGINS

*/

public static function user_plugins( $scope = '', $view = '' ) {

global $db;

  $where = array();

  /*

  WHERE / ORDER BY

  */

    if( !empty( $scope ) ) {
      $where[] = 'scope = "' . \site\utils::dbp( $scope ) . '"';
    }

    switch( $view ) {
      case 'all': break;
      case 'menu': $where[] = 'menu = 1 AND visible > 0'; break;
      default:  $where[] = 'visible > 0'; break;
    }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, user, name, image, scope, main, menu, menu_icon, extend_vars, visible, date FROM " . DB_TABLE_PREFIX . "plugins" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $name, $image, $scope, $main, $menu, $menu_icon, $vars, $visible, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => htmlspecialchars( $name ), 'image' => htmlspecialchars( $image ), 'scope' => htmlspecialchars( $scope ), 'main_file' => htmlspecialchars( $main ), 'menu_icon' => $menu_icon, 'vars' => @unserialize( $vars ), 'in_menu' => $menu, 'is_active' => ( $visible !== 0 ? true : false ), 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

SHOW USER AVATAR

*/

public static function user_avatar( $text ) {

  if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
    return $text;
  } else if( empty( $text ) ) {
    return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/' . \query\main::get_option( 'default_user_avatar' );
  }
  return $GLOBALS['siteURL'] . $text;

}

/*

SHOW STORE AVATAR

*/

public static function store_avatar( $text ) {

  if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
    return $text;
  } else if( empty( $text ) ) {
    return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/' . \query\main::get_option( 'default_store_avatar' );
  }
  return $GLOBALS['siteURL'] . $text;

}

/*

SHOW PRODUCT AVATAR

*/

public static function product_avatar( $text ) {

  if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
    return $text;
  } else if( empty( $text ) ) {
    return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/product_avatar_aa.png';
  }
  return $GLOBALS['siteURL'] . $text;

}

/*

SHOW REWARD AVATAR

*/

public static function reward_avatar( $text ) {

  if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
    return $text;
  } else if( empty( $text ) ) {
    return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/' . \query\main::get_option( 'default_reward_avatar' );
  }
  return $GLOBALS['siteURL'] . $text;

}

/*

SHOW THEME AVATAR

*/

public static function theme_avatar( $text ) {

  if( empty( $text ) ) {
    return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/theme_aa.png';
  }
  return $GLOBALS['siteURL'] . $text;

}

/*

SHOW PAYMENT PLAN AVATAR

*/

public static function payment_plan_avatar( $text ) {

  if( empty( $text ) ) {
    return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/payplan_aa.png';
  }
  return $GLOBALS['siteURL'] . $text;

}

/*

CHECK IF CATEGORY EXISTS

*/

public static function category_exists( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "categories WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT CATEGORY

*/

public static function category_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, subcategory, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = c.user), name, description, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE category = c.id), meta_title, meta_desc, date FROM " . DB_TABLE_PREFIX . "categories c WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $subcategory, $user, $user_name, $name, $description, $stores, $meta_title, $meta_desc, $date );
  $stmt->fetch();
  $stmt->close();

  return (object)array( 'ID' => $id, 'subcatID' => $subcategory, 'user' => $user, 'user_name' => htmlspecialchars( $user_name ), 'name' => htmlspecialchars( $name ), 'description' => htmlspecialchars( $description ), 'stores' => $stores, 'meta_title' => htmlspecialchars( $meta_title ), 'meta_description' => htmlspecialchars( $meta_desc ), 'date' => $date, 'is_subcat' => ( $subcategory !== 0 ? true : false ), 'link' => ( defined( 'SEO_LINKS' ) && SEO_LINKS ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_category' ), $name, $id ) : $GLOBALS['siteURL'] . '?cat=' . $id ) );

}

/*

CHECK IF WIDGET EXISTS

*/

public static function widget_exists( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "widgets WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT WIDGET

*/

public static function widget_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, user, widget_id, title, stop, type, orderby, position, text, html, mobile_view, date FROM " . DB_TABLE_PREFIX . "widgets WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $widget, $title, $stop, $type, $orderby, $position, $text, $html, $mobile_view, $date );
  $stmt->fetch();
  $stmt->close();

  return (object)array( 'id' => $id, 'user' => $user, 'widget_id' => $widget, 'title' => htmlspecialchars( $title ), 'limit' => $stop, 'type' => $type, 'orderby' => $orderby, 'position' => $position, 'text' => htmlspecialchars( $text ), 'html' => $html, 'mobile_view' => $mobile_view, 'date' => $date );

}

/*

SHOW WIDGETS

*/

public static function show_widgets( $id, $dir = '' ) {

global $db;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, widget_id, location, title, stop, type, orderby, text, html, mobile_view FROM " . DB_TABLE_PREFIX . "widgets WHERE theme = ? AND sidebar = ? ORDER BY position, last_update DESC");
  $theme = \query\main::get_option( 'theme' );
  $zone = trim( $id );
  $stmt->bind_param( "ss", $theme, $zone );
  $stmt->execute();
  $stmt->bind_result( $id, $widget, $location, $title, $limit, $type, $orderby, $text, $html, $mobile_view );

  $data = array();
  while( $stmt->fetch() ) {
    if( file_exists( $dir . WIGETS_LOCATION . '/' . $location ) ) {
      $data[] = array( 'ID' => $id, 'widget_id' => $widget, 'title' => htmlspecialchars( $title ), 'limit' => $limit, 'type' => $type, 'orderby' => $orderby, 'content' => ( $html ? $text : htmlspecialchars( $text ) ), 'mobile_view' => $mobile_view, 'file' => WIGETS_LOCATION . '/' . $location );
    }
  }

  $stmt->close();

  return $data;

}

/*

CHECK IF USER EXISTS

*/

public static function user_exists( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "users WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT USER

*/

public static function user_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT name, email, avatar, points, credits, ipaddr, privileges, erole, subscriber, last_login, last_action, visits, valid, ban, refid, date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE user = u.id), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE user = u.id), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE user = u.id), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE user = u.id) FROM " . DB_TABLE_PREFIX . "users u WHERE id = ?" );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $name, $email, $avatar, $points, $credits, $ip, $privileges, $erole, $subscriber, $last_login, $last_action, $visits, $valid, $ban, $refid, $date, $reviews, $stores, $coupons, $products );
  $stmt->fetch();
  $stmt->close();

  return (object)array( 'ID' => $id, 'name' => htmlspecialchars( $name ), 'email' => htmlspecialchars( $email ), 'avatar' => htmlspecialchars( $avatar ), 'points' => $points, 'credits' => $credits, 'IP' => htmlspecialchars( $ip ), 'privileges' => $privileges, 'erole' => @unserialize( $erole ), 'is_subscribed' => (boolean) $subscriber, 'is_confirmed' => (boolean) $valid, 'is_banned' => (strtotime( $ban ) > time() ? true : false) , 'is_subadmin' => ( $privileges === 1 ? true : false ), 'is_admin' => ( $privileges > 1 ? true : false ), 'last_login' => $last_login, 'last_action' => $last_action, 'visits' => $visits, 'ban' => $ban, 'refid' => $refid, 'date' => $date, 'reviews' => $reviews, 'stores' => $stores, 'coupons' => $coupons, 'products' => $products );

}

/*

CHECK IF REVIEW EXISTS

*/

public static function review_exists( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT REVIEW

*/

public static function review_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT r.id, r.user, r.store, s.name, s.link, r.text, r.stars, r.valid, r.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.lastupdate_by), r.lastupdate, r.date, u.name, u.avatar FROM " . DB_TABLE_PREFIX . "reviews r LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = r.store) LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = r.user) WHERE r.id = ?" );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $store, $store_name, $store_url, $text, $stars, $valid, $lastupdate_by, $lastupdate_by_name, $last_update, $date, $user_name, $user_avatar );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'user' => $user, 'user_name' => htmlspecialchars( $user_name ), 'storeID' => $store, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => $store_url, 'text' => htmlspecialchars( $text ), 'stars' => $stars, 'valid' => $valid, 'date' => $date, 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'user_avatar' => htmlspecialchars( $user_avatar ), 'store_link' => ( defined( 'SEO_LINKS' ) && SEO_LINKS ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $store_name, $store ) : $GLOBALS['siteURL'] . '?store=' . $store ) );

}

/*

CHECK IF PAGE EXISTS

*/

public static function page_exists( $id = 0, $special = array() ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $where = array();

  if( isset( $special['user_view'] ) ) {
    $where[] = 'visible > 0';
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "pages WHERE id = ?" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', $where ) ) );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT PAGE

*/

public static function page_infos( $id = 0, $special = array() ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();

  if( isset( $special['update_views'] ) ) {
    $stmt->prepare("UPDATE " . DB_TABLE_PREFIX . "pages SET views = views + 1 WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
  }

  $stmt->prepare("SELECT user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.user), name, text, visible, views, meta_title, meta_desc, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.lastupdate_by), lastupdate, date FROM " . DB_TABLE_PREFIX . "pages p WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $user, $user_name, $name, $text, $visible, $views, $meta_title, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'user' => $user, 'name' => htmlspecialchars( $name ), 'user_name' => htmlspecialchars( $user_name ), 'text' => $text, 'visible' => $visible, 'views' => $views, 'meta_title' => htmlspecialchars( $meta_title ), 'meta_description' => htmlspecialchars( $meta_desc ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'date' => $date, 'link' => ( defined( 'SEO_LINKS' ) && SEO_LINKS ? \site\utils::make_seo_link( '', $name, $id ): $GLOBALS['siteURL'] . '?p=' . $id ) );

}

/*

CHECK IF COUPON EXISTS

*/

public static function item_exists( $id = 0, $special = array() ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $where = array();

  if( isset( $special['user_view'] ) ) {
    $where[] = 'visible > 0';
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id FROM " . DB_TABLE_PREFIX . "coupons WHERE id = ?" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', $where ) ) );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT A COUPON

*/

public static function item_infos( $id = 0, $special = array() ) {

global $db, $GET;

/** make or not seo links */
$seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();

  if( isset( $special['update_views'] ) ) {
    $stmt->prepare("UPDATE " . DB_TABLE_PREFIX . "coupons SET views = views + 1 WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
  }

  $stmt->prepare("SELECT c.id, c.feedID, c.user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = c.user), c.store, c.category, c.popular, c.exclusive, c.title, c.link, c.description, c.tags, c.code, c.visible, c.views, c.start, c.expiration, c.cashback, c.meta_title, c.meta_desc, c.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = c.lastupdate_by), c.lastupdate, c.paid_until, c.date, s.image, s.name, s.link, s.category, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE c.id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $user_name, $store, $cat, $popular, $exclusive, $title, $link, $description, $tags, $code, $visible, $views, $start, $expiration, $cashback, $meta_title, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $paid_until, $date, $store_img, $store_name, $store_link, $store_cat, $reviews, $stars );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'user_name' => htmlspecialchars( $user_name ), 'code' => htmlspecialchars( $code ), 'title' => htmlspecialchars( $title ), 'original_url' => $link, 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true: false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'meta_title' => htmlspecialchars( $meta_title ), 'meta_description' => htmlspecialchars( $meta_desc ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'is_exclusive' => (boolean) $exclusive, 'paid_until' => $paid_until, 'date' => $date, 'store_img' => htmlspecialchars( $store_img ), 'storeID' => $store, 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'reviews' => $reviews, 'stars' => $stars, 'link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_coupon' ), $title, $id ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $store_name, $store ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_reviews' ), $store_name, $store ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) );

}

/*

CHECK IF PRODUCT EXISTS

*/

public static function product_exists( $id = 0, $special = array() ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $where = array();

  if( isset( $special['user_view'] ) ) {
    $where[] = 'visible > 0';
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id FROM " . DB_TABLE_PREFIX . "products WHERE id = ?" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', $where ) ) );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT A PRODUCT

*/

public static function product_infos( $id = 0, $special = array() ) {

global $db, $GET;

/** make or not seo links */
$seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();

  if( isset( $special['update_views'] ) ) {
    $stmt->prepare("UPDATE " . DB_TABLE_PREFIX . "products SET views = views + 1 WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
  }

  $stmt->prepare("SELECT p.id, p.feedID, p.user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.user), p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.meta_title, p.meta_desc, p.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.lastupdate_by), p.lastupdate, p.paid_until, p.date, s.image, s.id, s.name, s.link, s.category, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE p.id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $user_name, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $meta_title, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $paid_until, $date, $store_img, $store_id, $store_name, $store_link, $store_cat, $reviews, $stars );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'user_name' => htmlspecialchars( $user_name ), 'title' => htmlspecialchars( $title ), 'original_url' => $link, 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'price' => $price, 'old_price' => $old_price, 'currency' => htmlspecialchars( $currency ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'meta_title' => htmlspecialchars( $meta_title ), 'meta_description' => htmlspecialchars( $meta_desc ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'paid_until' => $paid_until, 'date' => $date, 'store_img' => htmlspecialchars( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'reviews' => $reviews, 'stars' => $stars, 'link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_product' ), $title, $id ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $store_name, $store_id ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_reviews' ), $store_name, $store_id ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) );

}

/*

CHECK IF STORE EXISTS

*/

public static function store_exists( $id = 0, $special = array() ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $where = array();

  if( isset( $special['user_view'] ) ) {
    $where[] = 'visible > 0';
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE id = ?" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', $where ) ) );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT A STORE

*/

public static function store_infos( $id = 0, $special = array() ) {

global $db, $GET;

/** make or not seo links */
$seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();

  if( isset( $special['update_views'] ) ) {
    $stmt->prepare("UPDATE " . DB_TABLE_PREFIX . "stores SET views = views + 1 WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
  }

  $stmt->prepare("SELECT s.id, s.feedID, s.user, s.category, s.popular, s.name, s.link, s.description, s.tags, s.image, s.visible, s.views, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id), s.meta_title, s.meta_desc, s.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = s.lastupdate_by), s.lastupdate, s.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0), u.name FROM " . DB_TABLE_PREFIX . "stores s LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = s.user) WHERE s.id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $name, $link, $description, $tags, $image, $visible, $views, $coupons, $meta_title, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $date, $reviews, $stars, $products, $user_name);
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'user_name' => htmlspecialchars( $user_name ), 'catID' => $cat, 'name' => htmlspecialchars( $name ), 'url' => htmlspecialchars( $link ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'visible' => $visible, 'views' => $views, 'coupons' => $coupons, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'products' => $products, 'meta_title' => htmlspecialchars( $meta_title ), 'meta_description' => htmlspecialchars( $meta_desc ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'is_popular' => (boolean) $popular, 'link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $name, $id ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_reviews' ), $name, $id ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) );

}

/*

CHECK IF A REWARD EXISTS

*/

public static function reward_exists( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $where = array();

  if( isset( $special['user_view'] ) ) {
    $where[] = 'visible > 0';
  }

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards WHERE id = ?" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', $where ) ) );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT A REWARD

*/

public static function reward_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT id, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.user), points, title, description, image, fields, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.lastupdate_by), lastupdate, visible, date FROM " . DB_TABLE_PREFIX . "rewards r WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $user_name, $points, $title, $description, $image, $fields, $lastupdate_by, $lastupdate_by_name, $last_update, $visible, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'user' => $user, 'user_name' => $user_name, 'points' => $points, 'title' => htmlspecialchars( $title ), 'description' => htmlspecialchars( $description ), 'image' => htmlspecialchars( $image ), 'fields' => @unserialize( $fields ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'visible' => $visible, 'date' => $date );

}

/*

CHECK IF A REWARD REQUEST EXISTS

*/

public static function reward_req_exists( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards_reqs WHERE id = ?");
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

GET INFORMATIONS ABOUT A REWARD REQUEST

*/

public static function reward_req_infos( $id = 0 ) {

global $db, $GET;

$id = empty( $id ) ? $GET['id'] : $id;

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, name, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.user), points, reward,  (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards WHERE id = r.reward), fields, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.lastupdate_by), lastupdate, claimed, date FROM " . DB_TABLE_PREFIX . "rewards_reqs r WHERE id = ?" );
  $stmt->bind_param( "i", $id );
  $stmt->execute();
  $stmt->bind_result( $id, $name, $user, $user_name, $points, $reward, $reward_exists, $fields, $lastupdate_by, $lastupdate_by_name, $last_update, $claimed, $date );
  $stmt->fetch();
  $stmt->close();

  return (object) array( 'ID' => $id, 'name' => htmlspecialchars( $name ), 'user' => $user, 'user_name' => htmlspecialchars( $user_name ), 'points' => $points, 'reward' => $reward, 'reward_exists' => ( $reward_exists > 0 ? 1 : 0 ), 'fields' => @unserialize( $fields ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => htmlspecialchars( $lastupdate_by_name ), 'last_update' => $last_update, 'claimed' => $claimed, 'date' => $date );

}

/*

CHECK IF AN USE HAVE A STORE

*/

public static function have_store( $id, $user ) {

global $db;

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE id = ? AND user = ?");
  $stmt->bind_param( "ii", $id, $user );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( $count > 0 ) {
    return true;
  }

  return false;

}

/*

NUMBER OF CATEGORIES

*/

public static function have_categories( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'cats': $where[] = 'subcategory = 0'; break;
      case 'subcats':  $where[] = 'subcategory > 0'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "categories" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE CATEGORIES

*/

public static function while_categories( $category = array() ) {

global $db;

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  $seo_link_category = \query\main::get_option( 'seo_link_category' );

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 'c.name'; break;
      case 'name desc': $orderby[] = 'c.name DESC'; break;
      case 'date': $orderby[] = 'c.date'; break;
      case 'date desc': $orderby[] = 'c.date DESC'; break;
    }
  }
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'cats': $where[] = 'c.subcategory = 0'; break;
      case 'subcats':  $where[] = 'c.subcategory > 0'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT c.id, c.subcategory, c.user, c.name, c.description, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE category = c.id) FROM " . DB_TABLE_PREFIX . "categories c" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $subcategory, $user, $name, $description, $date, $stores );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'subcatID' => $subcategory, 'user' => $user, 'name' => htmlspecialchars( $name ), 'description' => htmlspecialchars( $description ), 'date' => $date, 'stores' => $stores, 'is_subcat' => ( $subcategory !== 0 ? true : false ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_category, $name, $id ) : $GLOBALS['siteURL'] . '?cat=' . $id ) );
  }

  $stmt->close();

  return $data;

}

/*

WHILE THE CATEGORIES

*/

public static function group_categories( $category = array() ) {

  $array = array();
  foreach( \query\main::while_categories( $category ) as $c ) {
    if( $c->is_subcat ) {
    $array['cat_' . $c->subcatID]['subcats'][] = $c;
    } else {
    $array['cat_' . $c->ID]['infos'] = $c;
    }
  }

  return $array;

}

/*

NUMBER OF USERS

*/

public static function have_users( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['ip'] ) ) {
    $where[] = 'ipaddr = "' . \site\utils::dbp( $categories['ip'] ) . '"';
  }

  if( !empty( $categories['referrer'] ) ) {
    $where[] = 'refid = "' . (int) $categories['referrer'] . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'members': $where[] = 'privileges = 0'; break;
      case 'subadmins':  $where[] = 'privileges = 1'; break;
      case 'admins':  $where[] = 'privileges >= 2'; break;
      case 'verified': $where[] = 'valid >= 1'; break;
      case 'notverified': $where[] = 'valid = 0'; break;
      case 'banned': $where[] = 'ban >= NOW()';
      case 'referred': $where[] = 'refid > 0'; break;
    }
  }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "users" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE USERS

*/

public static function while_users( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['ip'] ) ) {
    $where[] = 'ipaddr = "' . \site\utils::dbp( $categories['ip'] ) . '"';
  }

  if( !empty( $categories['referrer'] ) ) {
    $where[] = 'refid = "' . (int) $categories['referrer'] . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'members': $where[] = 'privileges = 0'; break;
      case 'subadmins':  $where[] = 'privileges = 1'; break;
      case 'admins':  $where[] = 'privileges >= 2'; break;
      case 'verified': $where[] = 'valid >= 1'; break;
      case 'notverified': $where[] = 'valid = 0'; break;
      case 'banned': $where[] = 'ban >= NOW()';
      case 'referred': $where[] = 'refid > 0'; break;
    }
  }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 'name'; break;
      case 'name desc': $orderby[] = 'name DESC'; break;
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
      case 'action': $orderby[] = 'last_action'; break;
      case 'action desc': $orderby[] = 'last_action DESC'; break;
      case 'points': $orderby[] = 'points'; break;
      case 'points desc': $orderby[] = 'points DESC'; break;
      case 'credits': $orderby[] = 'credits'; break;
      case 'credits desc': $orderby[] = 'credits DESC'; break;
      case 'visits': $orderby[] = 'visits'; break;
      case 'visits desc': $orderby[] = 'visits DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, name, email, avatar, points, credits, ipaddr, privileges, subscriber, last_login, last_action, visits, valid, ban, refid, date FROM " . DB_TABLE_PREFIX . "users" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $name, $email, $avatar, $points, $credits, $ip, $privileges, $subscriber, $last_login, $last_action, $visits, $valid, $ban, $refid, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'name' => htmlspecialchars( $name ), 'email' => htmlspecialchars( $email ), 'avatar' => htmlspecialchars( $avatar ), 'points' => $points, 'credits' => $credits, 'IP' => htmlspecialchars( $ip ), 'privileges' => $privileges, 'is_subscribed' => (boolean) $subscriber, 'is_confirmed' => (boolean) $valid, 'is_banned' => (strtotime( $ban ) > time() ? true : false) , 'is_subadmin' => ( $privileges === 1 ? true : false ), 'is_admin' => ( $privileges > 1 ? true : false ), 'last_login' => $last_login, 'last_action' => $last_action, 'visits' => $visits, 'ban' => $ban, 'refid' => $refid, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF PAGES

*/

public static function have_pages( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, text) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'all': break;
      default: $where[] = 'visible > 0'; break;
    }
  } else {
    $where[] = 'visible > 0';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "pages" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE PAGES

*/

public static function while_pages( $category = array() ) {

global $db;

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  $seo_link_page = \query\main::get_option( 'seo_link_page' );

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, text) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'all': break;
      default: $where[] = 'visible > 0'; break;
    }
  } else {
    $where[] = 'visible > 0';
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 'name'; break;
      case 'name desc': $orderby[] = 'name DESC'; break;
      case 'update': $orderby[] = 'lastupdate'; break;
      case 'update desc': $orderby[] = 'lastupdate DESC'; break;
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
      case 'views': $orderby[] = 'views'; break;
      case 'views desc': $orderby[] = 'views DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, user, name, text, visible, date FROM " . DB_TABLE_PREFIX . "pages" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $name, $text, $visible, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'user' => $user, 'name' => htmlspecialchars( $name ), 'text' => $text, 'visible' => $visible, 'date' => $date, 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_page, $name, $id ) : $GLOBALS['siteURL'] . '?p=' . $id ) );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF ITEMS - COUPONS

*/

public static function have_items( $category = array(), $place = '', $special = array() ) {

global $db, $GET;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['update'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['update'] ) );
    $where[] = 'c.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'c.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  switch( $place ) {

  case 'category':

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
  $stmt->bind_param( "i", $GET['id'] );
  $stmt->execute();
  $stmt->bind_result( $id );

  $ids[] = (int) $GET['id'];
  while( $stmt->fetch() ) {
    $ids[] = $id;
  }

  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (c.store = s.id) WHERE c.category IN(" . implode( ',', $ids ) . ") AND c.visible > 0 AND s.visible > 0");
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  case 'search':

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE CONCAT(c.title, c.tags, s.name, s.tags) REGEXP ? AND c.visible > 0 AND s.visible > 0");

  if( gettype( $GET['id'] ) === 'string' ) {
  $search = implode( '.*', explode( ' ', trim( $GET['id'] ) ) );
  } else {
  $search = '';
  }

  $stmt->bind_param( "s", $search  );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  default:

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['ids'] ) && $categories['ids'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['ids'] ) ));
    if( !empty( $arr ) )
    $where[] = 'c.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['categories'] ) && $categories['categories'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['categories'] ) ));
    if( !empty( $arr ) )
    $where[] = 'c.category IN(' . \site\utils::dbp ( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['store'] ) && $categories['store'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['store'] ) ));
    if( !empty( $arr ) )
    $where[] = 'c.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 'c.user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(c.title, c.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'all': break;
      case 'expired': $where[] = 'c.expiration <= NOW()'; break;
      case 'active':  $where[] = 'c.expiration > NOW()'; break;
      case 'popular':  $where[] = 'c.popular > 0'; break;
      case 'exclusive':  $where[] = 'c.exclusive > 0'; break;
      case 'feed':  $where[] = 'c.feedID > 0'; break;
      case 'visible':  $where[] = 'c.visible > 0 AND s.visible > 0'; break;
      case 'notvisible': $where[] = 'c.visible = 0'; break;
      default: $where[] = 'c.visible > 0 AND s.visible > 0'; break;
    }
  }
  } else {
    $where[] = 'c.visible > 0 AND s.visible > 0';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  }

  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE ITEMS - COUPONS

*/

public static function while_items( $category = array(), $place = '' ) {

global $db, $GET;

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  list( $seo_link_coupon, $seo_link_store, $seo_link_reviews ) = array( \query\main::get_option( 'seo_link_coupon' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ) );

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = ( isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0 );

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['update'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['update'] ) );
    $where[] = 'c.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'c.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 'c.title'; break;
      case 'name desc': $orderby[] = 'c.title DESC'; break;
      case 'update': $orderby[] = 'c.lastupdate'; break;
      case 'update desc': $orderby[] = 'c.lastupdate DESC'; break;
      case 'rating': $orderby[] = 'rating'; break;
      case 'rating desc': $orderby[] = 'rating DESC'; break;
      case 'votes': $orderby[] = 'votes'; break;
      case 'votes desc': $orderby[] = 'votes DESC'; break;
      case 'views': $orderby[] = 'c.views'; break;
      case 'views desc': $orderby[] = 'c.views DESC'; break;
      case 'date': $orderby[] = 'c.date'; break;
      case 'date desc': $orderby[] = 'c.date DESC'; break;
      case 'active': $orderby[] = 'c.expiration'; break;
      case 'active desc': $orderby[] = 'c.expiration DESC'; break;
    }
  }
  }

  /*

  */

  switch( $place ) {

  case 'category':

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
  $stmt->bind_param( "i", $GET['id'] );
  $stmt->execute();
  $stmt->bind_result( $id );

  $ids[] = (int) $GET['id'];
  while( $stmt->fetch() ) {
    $ids[] = $id;
  }

  $stmt->prepare( "SELECT c.id, c.feedID, c.user, c.store, c.category, c.title, c.link, c.description, c.tags, c.code, c.visible, c.views, c.start, c.expiration, c.cashback, c.paid_until, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.image, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE c.category IN(" . implode( ',', $ids ) . ") AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $title, $link, $description, $tags, $code, $visible, $views, $start, $expiration, $cashback, $paid_until, $date, $reviews, $stars, $store_img, $store_name, $store_link, $store_cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => htmlspecialchars( $code ), 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true: false ), 'is_deal' => ( empty( $code ) ? true: false ), 'is_running' => ( strtotime( $start ) > time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $id ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) );
  }

  $stmt->close();

  return $data;

  break;

  case 'search':

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT c.id, c.feedID, c.user, c.store, c.category, c.title, c.link, c.description, c.tags, c.code, c.visible, c.views, c.start, c.expiration, c.cashback, c.paid_until, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.image, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE CONCAT(c.title, c.tags, s.name, s.tags) REGEXP ? AND c.visible > 0 AND s.visible > 0" . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );

  if( gettype( $GET['id'] ) === 'string' ) {
  $search = implode( '.*', explode( ' ', trim( $GET['id'] ) ) );
  $search = substr( $search, 0, 50 );
  } else {
  $search = '';
  }

  $stmt->bind_param( "s", $search );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $title, $link, $description, $tags, $code, $visible, $views, $start, $expiration, $cashback, $paid_until, $date, $reviews, $stars, $store_img, $store_name, $store_link, $store_cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => htmlspecialchars( $code ), 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true : false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $id ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) );
  }

  $stmt->close();

  return $data;

  break;

  default:

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['ids'] ) && $categories['ids'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['ids'] ) ));
    if( !empty( $arr ) )
    $where[] = 'c.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    if( !isset( $categories['orderby'] ) ) {
      $orderby[] = 'field(c.id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }
  }

  if( !empty( $categories['categories'] ) && $categories['categories'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['categories'] ) ));
    if( !empty( $arr ) )
    $where[] = 'c.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['store'] ) && $categories['store'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['store'] ) ));
    if( !empty( $arr ) )
    $where[] = 'c.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 'c.user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(c.title, c.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'all': break;
      case 'expired': $where[] = 'c.expiration <= NOW()'; break;
      case 'active':  $where[] = 'c.expiration > NOW()'; break;
      case 'popular':  $where[] = 'c.popular > 0'; break;
      case 'exclusive':  $where[] = 'c.exclusive > 0'; break;
      case 'feed':  $where[] = 'c.feedID > 0'; break;
      case 'visible':  $where[] = 'c.visible > 0 AND s.visible > 0'; break;
      case 'notvisible': $where[] = 'c.visible = 0'; break;
      default: $where[] = 'c.visible > 0 AND s.visible > 0'; break;
    }
  }
  } else {
    $where[] = 'c.visible > 0 AND s.visible > 0';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT c.id, c.feedID, c.user, c.store, c.category, c.title, c.link, c.description, c.tags, c.code, c.visible, c.views, c.start, c.expiration, c.cashback, c.paid_until, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.image, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $title, $link, $description, $tags, $code, $visible, $views, $start, $expiration, $cashback, $paid_until, $date, $reviews, $stars, $store_img, $store_name, $store_link, $store_cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => htmlspecialchars( $code ), 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true: false ), 'is_deal' => ( empty( $code ) ? true : false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $id ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) );
  }

  $stmt->close();

  return $data;

  break;

  }

}

/*

NUMBER OF PRODUCTS

*/

public static function have_products( $category = array(), $place = '', $special = array() ) {

global $db, $GET;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['update'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['update'] ) );
    $where[] = 'p.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'p.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'p.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'p.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  switch( $place ) {

  case 'category':

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
  $stmt->bind_param( "i", $GET['id'] );
  $stmt->execute();
  $stmt->bind_result( $id );

  $ids[] = (int) $GET['id'];
  while( $stmt->fetch() ) {
    $ids[] = $id;
  }

  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (p.store = s.id) WHERE p.category IN(" . implode( ',', $ids ) . ") AND p.visible > 0 AND s.visible > 0");
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  case 'search':

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE CONCAT(p.title, p.tags, s.name, s.tags) REGEXP ? AND p.visible > 0 AND s.visible > 0");

  if( gettype( $GET['id'] ) === 'string' ) {
  $search = implode( '.*', explode( ' ', trim( $GET['id'] ) ) );
  } else {
  $search = '';
  }

  $stmt->bind_param( "s", $search  );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  default:

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['ids'] ) && $categories['ids'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['ids'] ) ));
    if( !empty( $arr ) )
    $where[] = 'p.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['categories'] ) && $categories['categories'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['categories'] ) ));
    if( !empty( $arr ) )
    $where[] = 'p.category IN(' . \site\utils::dbp ( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['store'] ) && $categories['store'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['store'] ) ));
    if( !empty( $arr ) )
    $where[] = 'p.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 'p.user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(p.title, p.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'all': break;
      case 'expired': $where[] = 'p.expiration <= NOW()'; break;
      case 'active':  $where[] = 'p.expiration > NOW()'; break;
      case 'popular':  $where[] = 'p.popular > 0'; break;
      case 'exclusive':  $where[] = 'p.exclusive > 0'; break;
      case 'feed':  $where[] = 'p.feedID > 0'; break;
      case 'visible':  $where[] = 'p.visible > 0 AND s.visible > 0'; break;
      case 'notvisible': $where[] = 'p.visible = 0'; break;
      default: $where[] = 'p.visible > 0 AND s.visible > 0'; break;
    }
  }
  } else {
    $where[] = 'p.visible > 0 AND s.visible > 0';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  }

  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE PRODUCTS

*/

public static function while_products( $category = array(), $place = '' ) {

global $db, $GET;

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  list( $seo_link_product, $seo_link_store, $seo_link_reviews ) = array( \query\main::get_option( 'seo_link_product' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ) );

  $categories = \site\utils::validate_user_data( $category );

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = ( isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0 );

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['update'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['update'] ) );
    $where[] = 'p.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'p.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'p.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'p.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 'p.title'; break;
      case 'name desc': $orderby[] = 'p.title DESC'; break;
      case 'update': $orderby[] = 'p.lastupdate'; break;
      case 'update desc': $orderby[] = 'p.lastupdate DESC'; break;
      case 'rating': $orderby[] = 'rating'; break;
      case 'rating desc': $orderby[] = 'rating DESC'; break;
      case 'votes': $orderby[] = 'votes'; break;
      case 'votes desc': $orderby[] = 'votes DESC'; break;
      case 'views': $orderby[] = 'p.views'; break;
      case 'views desc': $orderby[] = 'p.views DESC'; break;
      case 'date': $orderby[] = 'p.date'; break;
      case 'date desc': $orderby[] = 'p.date DESC'; break;
      case 'active': $orderby[] = 'p.expiration'; break;
      case 'active desc': $orderby[] = 'p.expiration DESC'; break;
    }
  }
  }

  /*

  */

  switch( $place ) {

  case 'category':

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
  $stmt->bind_param( "i", $GET['id'] );
  $stmt->execute();
  $stmt->bind_result( $id );

  $ids[] = (int) $GET['id'];
  while( $stmt->fetch() ) {
    $ids[] = $id;
  }

  $stmt->prepare( "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.paid_until, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.image, s.id, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE p.category IN(" . implode( ',', $ids ) . ") AND p.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $paid_until, $date, $reviews, $stars, $store_img, $store_id, $store_name, $store_link, $store_cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => htmlspecialchars( $currency ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $id ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) );
  }

  $stmt->close();

  return $data;

  break;

  case 'search':

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.paid_until, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.image, s.id, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE CONCAT(p.title, p.tags, s.name, s.tags) REGEXP ? AND p.visible > 0 AND s.visible > 0" . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );

  if( gettype( $GET['id'] ) === 'string' ) {
  $search = implode( '.*', explode( ' ', trim( $GET['id'] ) ) );
  $search = substr( $search, 0, 50 );
  } else {
  $search = '';
  }

  $stmt->bind_param( "s", $search );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $paid_until, $date, $reviews, $stars, $store_img, $store_id, $store_name, $store_link, $store_cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => htmlspecialchars( $currency ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $id ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) );
  }

  $stmt->close();

  return $data;

  break;

  default:

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['ids'] ) && $categories['ids'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['ids'] ) ));
    if( !empty( $arr ) )
    $where[] = 'p.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    if( !isset( $categories['orderby'] ) ) {
      $orderby[] = 'field(p.id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }
  }

  if( !empty( $categories['categories'] ) && $categories['categories'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['categories'] ) ));
    if( !empty( $arr ) )
    $where[] = 'p.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['store'] ) && $categories['store'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['store'] ) ));
    if( !empty( $arr ) )
    $where[] = 'p.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 'p.user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(p.title, p.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'all': break;
      case 'expired': $where[] = 'p.expiration <= NOW()'; break;
      case 'active':  $where[] = 'p.expiration > NOW()'; break;
      case 'popular':  $where[] = 'p.popular > 0'; break;
      case 'exclusive':  $where[] = 'p.exclusive > 0'; break;
      case 'feed':  $where[] = 'p.feedID > 0'; break;
      case 'visible':  $where[] = 'p.visible > 0 AND s.visible > 0'; break;
      case 'notvisible': $where[] = 'p.visible = 0'; break;
      default: $where[] = 'p.visible > 0 AND s.visible > 0'; break;
    }
  }
  } else {
    $where[] = 'p.visible > 0 AND s.visible > 0';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.paid_until, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.image, s.id, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $paid_until, $date, $reviews, $stars, $store_img, $store_id, $store_name, $store_link, $store_cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => htmlspecialchars( $currency ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $id ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) );
  }

  $stmt->close();

  return $data;

  break;

  }

}

/*

NUMBER OF REVIEWS

*/

public static function have_reviews( $category = array(), $place = '', $special = array() ) {

global $db, $GET;

  $categories = \site\utils::validate_user_data( $category ); 

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  switch( $place ) {

  case 'store':

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = ? AND valid > 0");
  $stmt->bind_param( "i", $GET['id'] );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  default:

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['store'] ) && $categories['store'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['store'] ) ));
    if( !empty( $arr ) )
    $where[] = 'store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 'user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'text REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'all': break;
      case 'notvalid': $where[] = 'valid = 0'; break;
      default: $where[] = 'valid > 0'; break;
    }
  } else {
    $where[] = 'valid > 0';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  break;

  }

  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE REVIEWS

*/

public static function while_reviews( $category = array(), $place = '' ) {

global $db, $GET;

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  $seo_link_store = \query\main::get_option( 'seo_link_store' );

  $categories = \site\utils::validate_user_data( $category ); 

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'r.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'r.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'date': $orderby[] = 'r.date'; break;
      case 'date desc': $orderby[] = 'r.date DESC'; break;
    }
  }
  }

  /*

  */

  switch( $place ) {

  case 'store':

  $stmt = $db->stmt_init();
  $stmt->prepare("SELECT r.id, r.user, r.store, r.text, r.stars, r.valid, r.date, u.name, u.avatar FROM " . DB_TABLE_PREFIX . "reviews r LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = r.user) WHERE r.store = ? AND r.valid > 0" . ( empty( $where ) ? '' : implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->bind_param( "i", $GET['id'] );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $store, $text, $stars, $valid, $date, $user_name, $user_avatar );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'user' => $user, 'user_name' => htmlspecialchars( $user_name ), 'storeID' => $store, 'text' => htmlspecialchars( $text ), 'stars' => $stars, 'valid' => $valid, 'date' => $date, 'user_avatar' => htmlspecialchars( $user_avatar ) );
  }

  $stmt->close();

  return $data;

  break;

  default:

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['store'] ) && $categories['store'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['store'] ) ));
    if( !empty( $arr ) )
    $where[] = 'r.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 'r.user = "' . (int) $categories['user'] . '"';
  }


  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'text REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'all': break;
      case 'notvalid': $where[] = 'r.valid = 0'; break;
      default: $where[] = 'r.valid > 0'; break;
    }
  } else {
    $where[] = 'r.valid > 0';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT r.id, r.user, r.store, r.text, r.stars, r.valid, r.date, u.name, u.avatar, s.name, s.link FROM " . DB_TABLE_PREFIX . "reviews r LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = r.store) LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = r.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $store, $text, $stars, $valid, $date, $user_name, $user_avatar, $store_name, $store_url );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'user' => $user, 'user_name' => htmlspecialchars( $user_name ), 'storeID' => $store, 'store_url' => $store_url, 'text' => htmlspecialchars( $text ), 'stars' => $stars, 'valid' => $valid, 'date' => $date, 'user_avatar' => htmlspecialchars( $user_avatar ), 'store_name' => htmlspecialchars( $store_name ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store ) : $GLOBALS['siteURL'] . '?store=' . $store ) );
  }

  $stmt->close();

  return $data;

  break;

  }

}

/*

NUMBER OF STORES

*/

public static function have_stores( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category ); 

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]$|^0-9$)/u', $categories['firstchar'] ) ) {
    $where[] = 'name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 'user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['ids'] ) && $categories['ids'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['ids'] ) ));
    if( !empty( $arr ) )
    $where[] = 'id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['categories'] ) && $categories['categories'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['categories'] ) ));
    if( !empty( $arr ) )
    $where[] = 'category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(name, tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'all': break;
      case 'popular':  $where[] = 'popular > 0'; break;
      case 'exclusive':  $where[] = 'exclusive > 0'; break;
      case 'feed':  $where[] = 'feedID > 0'; break;
      case 'notvisible': $where[] = 'visible = 0'; break;
      default: $where[] = 'visible > 0'; break;
    }
  }
  } else {
    $where[] = 'visible > 0';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();

  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE STORES

*/

public static function while_stores( $category = array() ) {

global $db;

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  list( $seo_link_store, $seo_link_reviews ) = array( \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ) );

  $categories = \site\utils::validate_user_data( $category ); 

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]$|^0-9$)/u', $categories['firstchar'] ) ) {
    $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  if( !empty( $categories['user'] ) ) {
    $where[] = 's.user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['ids'] ) && $categories['ids'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['ids'] ) ));
    if( !empty( $arr ) )
    $where[] = 's.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    if( !isset( $categories['orderby'] ) ) {
      $orderby[] = 'field(s.id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }
  }

  if( !empty( $categories['categories'] ) && $categories['categories'] != 'all' ) {
    $arr = array_filter( array_map( function( $w ){
        return (int) $w;
    }, explode( ',', $categories['categories'] ) ));
    if( !empty( $arr ) )
    $where[] = 's.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    if( !isset( $categories['orderby'] ) ) {
      $orderby[] = 'field(s.id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(s.name, s.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
  $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
  foreach( $show as $v ) {
    switch( $v ) {
      case 'all': break;
      case 'popular':  $where[] = 's.popular > 0'; break;
      case 'feed':  $where[] = 's.feedID > 0'; break;
      case 'notvisible': $where[] = 's.visible = 0'; break;
      default: $where[] = 's.visible > 0'; break;
    }
  }
  } else {
    $where[] = 's.visible > 0';
  }

  if( !empty( $categories['date'] ) ) {
    $date = array_map( 'trim', explode( ',', $categories['date'] ) );
    $where[] = 's.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
    if( isset( $date[1] ) ) {
      $where[] = 's.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 's.name'; break;
      case 'name desc': $orderby[] = 's.name DESC'; break;
      case 'update': $orderby[] = 's.lastupdate'; break;
      case 'update desc': $orderby[] = 's.lastupdate DESC'; break;
      case 'rating': $orderby[] = 'rating'; break;
      case 'rating desc': $orderby[] = 'rating DESC'; break;
      case 'votes': $orderby[] = 'votes'; break;
      case 'votes desc': $orderby[] = 'votes DESC'; break;
      case 'views': $orderby[] = 's.views'; break;
      case 'views desc': $orderby[] = 's.views DESC'; break;
      case 'date': $orderby[] = 's.date'; break;
      case 'date desc': $orderby[] = 's.date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT s.id, s.feedID, s.user, s.category, s.popular, s.name, s.link, s.description, s.tags, s.image, s.visible, s.views, s.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id AND visible > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0) FROM " . DB_TABLE_PREFIX . "stores s" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $name, $link, $description, $tags, $image, $visible, $views, $date, $reviews, $stars, $coupons, $products );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'catID' => $cat, 'name' => htmlspecialchars( $name ), 'url' => htmlspecialchars( $link ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'date' => $date, 'visible' => $visible, 'views' => $views, 'reviews' => $reviews, 'stars' => $stars, 'coupons' => $coupons, 'products' => $products, 'is_popular' => (boolean) $popular, 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $name, $id ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $name, $id ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF FAVORITES

*/

public static function have_favorites( $category = array(), $special = array() ) {

if( $GLOBALS['me'] ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND s.visible > 0';

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
    $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "favorite f LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

} else {

  return false;

}

}

/*

WHILE THE FAVORITES

*/

public static function while_favorites( $category = array() ) {

if( $GLOBALS['me'] ) {

global $db;

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  list( $seo_link_store, $seo_link_reviews ) = array( \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ) );

  $categories = \site\utils::validate_user_data( $category ); 

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categoriesry['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND s.visible > 0';

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
    $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'date': $orderby[] = 'c.date'; break;
      case 'date desc': $orderby[] = 'c.date DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT s.id, s.feedID, s.user, s.category, s.popular, s.name, s.link, s.description, s.tags, s.image, s.views, s.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id) FROM " . DB_TABLE_PREFIX . "favorite f LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (f.store = s.id)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $name, $link, $description, $tags, $image, $views, $date, $reviews, $stars, $coupons );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'feedID' => $feed_id, 'user' => $user, 'category' => $cat, 'name' => htmlspecialchars( $name ), 'url' => htmlspecialchars( $link ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'date' => $date, 'catID' => $category, 'views' => $views, 'reviews' => $reviews, 'stars' => $stars, 'coupons' => $coupons, 'is_popular' => (boolean) $popular, 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $name, $id ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $name, $id ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) );
  }

  $stmt->close();

  return $data;

} else {

  return array();

}

}

/*

NUMBER OF ITEMS - COUPONS ON WALL

*/

public static function have_wall( $category = array() ) {

if( $GLOBALS['me'] ) {

global $db;

  $categories = \site\utils::validate_user_data( $category ); 

  $where = array();

  /*

  WHERE / ORDER BY

  */

  $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND c.visible > 0 AND s.visible > 0';

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
    $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = c.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

} else {

  return false;

}

}

/*

WHILE THE WALL - COUPONS

*/

public static function while_wall( $category = array() ) {

if( $GLOBALS['me'] ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $limit = $orderby = array();

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  list( $seo_link_coupon, $seo_link_store, $seo_link_reviews ) = array( \query\main::get_option( 'seo_link_coupon' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ) );

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND c.visible > 0 AND s.visible > 0';

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
    $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  if( isset( $categories['orderby'] ) ) {

  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );

  foreach( $order as $v ) {
    switch( $v ) {
        case 'rand': $orderby[] = 'RAND()'; break;
        case 'date': $orderby[] = 'c.date'; break;
        case 'date desc': $orderby[] = 'c.date DESC'; break;
    }
  }

  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT c.id, c.feedID, c.user, c.title, c.link, c.description, c.tags, c.code, c.views, c.start, c.expiration, c.cashback, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), s.image, s.id, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = c.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $title, $link, $description, $tags, $code, $views, $start, $expiration, $cashback, $date, $reviews, $stars, $store_img, $store_id, $store_name, $store_link, $cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'feedID' => $feed_id, 'user' => $user, 'code' => htmlspecialchars( $code ), 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'catID' => $cat, 'views' => $views, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true: false ), 'is_deal' => ( empty( $code ) ? true: false ), 'is_running' => ( strtotime( $start ) > time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'storeID' => $store_id, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $id ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) );
  }

  $stmt->close();

  return $data;

} else {

  return array();

}

}

/*

NUMBER OF ITEMS - PRODUCTS ON WALL

*/

public static function have_wall_products( $category = array() ) {

if( $GLOBALS['me'] ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND p.visible > 0 AND s.visible > 0';

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
    $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = p.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

} else {

  return false;

}

}

/*

WHILE THE WALL - PRODUCTS

*/

public static function while_wall_products( $category = array() ) {

if( $GLOBALS['me'] ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = $limit = $orderby = array();

  /** make or not seo links */
  $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
  list( $seo_link_product, $seo_link_store, $seo_link_reviews ) = array( \query\main::get_option( 'seo_link_product' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ) );

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND p.visible > 0 AND s.visible > 0';

  if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
    $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
  }

  if( isset( $categories['orderby'] ) ) {

  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );

  foreach( $order as $v ) {
    switch( $v ) {
        case 'rand': $orderby[] = 'RAND()'; break;
        case 'date': $orderby[] = 'p.date'; break;
        case 'date desc': $orderby[] = 'p.date DESC'; break;
    }
  }

  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.paid_until, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.image, s.id, s.name, s.link, s.category FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = p.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $paid_until, $date, $reviews, $stars, $store_img, $store_id, $store_name, $store_link, $store_cat );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object) array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => htmlspecialchars( $title ), 'url' => ( filter_var( $link, FILTER_VALIDATE_URL ) ? htmlspecialchars( $link ) : htmlspecialchars( $store_link ) ), 'description' => htmlspecialchars( $description ), 'tags' => htmlspecialchars( $tags ), 'image' => htmlspecialchars( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => htmlspecialchars( $currency ), 'visible' => $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_img' => htmlspecialchars( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => htmlspecialchars( $store_name ), 'store_url' => htmlspecialchars( $store_link ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $id ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_id ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) );
  }

  $stmt->close();

  return $data;

} else {

  return array();

}

}

/*

NUMBER OF REWARDS

*/

public static function have_rewards( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category );

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(title, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'active':  $where[] = 'visible > 0'; break;
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE REWARDS

*/

public static function while_rewards( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category ); 

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'CONCAT(title, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'active':  $where[] = 'visible > 0'; break;
    }
  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'name': $orderby[] = 'title'; break;
      case 'name desc': $orderby[] = 'title DESC'; break;
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
      case 'points': $orderby[] = 'points'; break;
      case 'points desc': $orderby[] = 'points DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, user, points, title, description, image, fields, visible, date FROM " . DB_TABLE_PREFIX . "rewards" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $user, $points, $title, $description, $image, $fields, $visible, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'user' => $user, 'points' => $points, 'title' => htmlspecialchars( $title ), 'description' => htmlspecialchars( $description ), 'image' => htmlspecialchars( $image ), 'fields' => @unserialize( $fields ), 'visible' => $visible, 'date' => $date );
  }

  $stmt->close();

  return $data;

}

/*

NUMBER OF REWARD REQUESTS

*/

public static function have_rewards_reqs( $category = array(), $special = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category ); 

  $where = array();

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['user'] ) ) {
    $where[] = 'user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['reward'] ) ) {
    $where[] = 'reward = "' . (int) $categories['reward'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'fields REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'valid': $where[] = 'claimed = 1'; break;
      case 'notvalid': $where[] = 'claimed = 0'; break;
    }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards_reqs" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) );
  $stmt->execute();
  $stmt->bind_result( $count );
  $stmt->fetch();
  $stmt->close();

  if( isset( $special['only_count'] ) ) {
    return $count;
  }


  $pags = array();
  $pags['results'] = $count;
  $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
  $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
  $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
  if( $page < 1 ) $page = 1;
  if( $page > $pags['pages'] ) $page = $_GET['page'] = $pags['pages'];
  $pags['page'] =  $page;
  if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
  if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

  return $pags;

}

/*

WHILE THE REWARDS REQUESTS

*/

public static function while_rewards_reqs( $category = array() ) {

global $db;

  $categories = \site\utils::validate_user_data( $category ); 

  $where = $orderby = $limit = array();

  if( isset( $categories['max'] ) ) {
    if( !empty( $categories['max'] ) ) {
      $limit[] = $categories['max'];
    }
  } else {
    $page = ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 );
    $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

    $limit[] = $offset;
    $limit[] = $per_page;
  }

  /*

  WHERE / ORDER BY

  */

  if( !empty( $categories['user'] ) ) {
    $where[] = 'user = "' . (int) $categories['user'] . '"';
  }

  if( !empty( $categories['reward'] ) ) {
    $where[] = 'reward = "' . (int) $categories['reward'] . '"';
  }

  if( !empty( $categories['search'] ) ) {
    $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
    $where[] = 'fields REGEXP "' . \site\utils::dbp( $search ) . '"';
  }

  if( isset( $categories['show'] ) ) {
    $show = strtolower( $categories['show'] );
    switch( $show ) {
      case 'valid': $where[] = 'claimed = 1'; break;
      case 'notvalid': $where[] = 'claimed = 0'; break;
    }

  }

  if( isset( $categories['orderby'] ) ) {
  $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
  foreach( $order as $v ) {
    switch( $v ) {
      case 'rand': $orderby[] = 'RAND()'; break;
      case 'date': $orderby[] = 'date'; break;
      case 'date desc': $orderby[] = 'date DESC'; break;
      case 'points': $orderby[] = 'points'; break;
      case 'points desc': $orderby[] = 'points DESC'; break;
    }
  }
  }

  /*

  */

  $stmt = $db->stmt_init();
  $stmt->prepare( "SELECT id, name, user, points, reward, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards WHERE id = r.reward), fields, claimed, date FROM " . DB_TABLE_PREFIX . "rewards_reqs r" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', $where ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
  $stmt->execute();
  $stmt->bind_result( $id, $name, $user, $points, $reward, $reward_exists, $fields, $claimed, $date );

  $data = array();
  while( $stmt->fetch() ) {
    $data[] = (object)array( 'ID' => $id, 'name' => htmlspecialchars( $name ), 'user' => $user, 'points' => $points, 'reward' => $reward, 'reward_exists' => ( $reward_exists > 0 ? 1 : 0 ), 'fields' => @unserialize( $fields ), 'claimed' => $claimed, 'date' => $date );
  }

  $stmt->close();

  return $data;

}


}