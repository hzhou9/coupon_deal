<?php

class widgets {

public static function widgets_list() {

global $LANG;

  $widgets = array();

  $widgets[1] = array( 'name' => $LANG['widget_search_box'], 'file' => 'search.php', 'def_type' => '', 'allow_orderby' => false, 'allow_show' => false );                                                                                                                                     
  $widgets[2] = array( 'name' => $LANG['widget_categories'], 'file' => 'categories.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => false, 'allow_limit' => true );
  $widgets[3] = array( 'name' => $LANG['widget_coupons'], 'file' => 'coupons.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'], 'votes' => $LANG['order_votes'], 'votes desc' => $LANG['order_votes_desc'], 'rating' => $LANG['order_rating'], 'rating desc' => $LANG['order_rating_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => array( '' => $LANG['show_all'], 'active' => $LANG['show_active'], 'popular' => $LANG['show_popular'], 'exclusive' => $LANG['show_exclusive'] ), 'allow_limit' => true );
  $widgets[4] = array( 'name' => $LANG['widget_coupons'], 'description' => 'v2', 'file' => 'coupons_v2.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'], 'votes' => $LANG['order_votes'], 'votes desc' => $LANG['order_votes_desc'], 'rating' => $LANG['order_rating'], 'rating desc' => $LANG['order_rating_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => array( '' => $LANG['show_all'], 'active' => $LANG['show_active'], 'popular' => $LANG['show_popular'], 'exclusive' => $LANG['show_exclusive'] ), 'allow_limit' => true );
  $widgets[16] = array( 'name' => $LANG['widget_products'], 'file' => 'products.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'], 'votes' => $LANG['order_votes'], 'votes desc' => $LANG['order_votes_desc'], 'rating' => $LANG['order_rating'], 'rating desc' => $LANG['order_rating_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => array( '' => $LANG['show_all'], 'active' => $LANG['show_active'], 'popular' => $LANG['show_popular'], 'exclusive' => $LANG['show_exclusive'] ), 'allow_limit' => true );
  $widgets[17] = array( 'name' => $LANG['widget_products'], 'description' => 'v2', 'file' => 'products_v2.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'votes' => $LANG['order_votes'], 'votes desc' => $LANG['order_votes_desc'], 'rating' => $LANG['order_rating'], 'rating desc' => $LANG['order_rating_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => array( '' => $LANG['show_all'], 'active' => $LANG['show_active'], 'popular' => $LANG['show_popular'], 'exclusive' => $LANG['show_exclusive'] ), 'allow_limit' => true );
  $widgets[5] = array( 'name' => $LANG['widget_reviews'], 'file' => 'reviews.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => false, 'allow_limit' => true );
  $widgets[6] = array( 'name' => $LANG['widget_pages'], 'file' => 'pages.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'] ), 'allow_show' => false, 'allow_limit' => true );
  $widgets[7] = array( 'name' => $LANG['widget_stores'], 'file' => 'stores.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'], 'votes' => $LANG['order_votes'], 'votes desc' => $LANG['order_votes_desc'], 'rating' => $LANG['order_rating'], 'rating desc' => $LANG['order_rating_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => array( '' => $LANG['show_all'], 'popular' => $LANG['show_popular'] ), 'allow_limit' => true );
  $widgets[8] = array( 'name' => $LANG['widget_stores'], 'description' => 'v2', 'file' => 'stores_v2.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'views' => $LANG['order_views'], 'views desc' => $LANG['order_views_desc'], 'votes' => $LANG['order_votes'], 'votes desc' => $LANG['order_votes_desc'], 'rating' => $LANG['order_rating'], 'rating desc' => $LANG['order_rating_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ), 'allow_show' => array( '' => $LANG['show_all'], 'popular' => $LANG['show_popular'] ), 'allow_limit' => true );
  $widgets[9] = array( 'name' => $LANG['widget_history'], 'description' => 'v2', 'file' => 'stores_history.php', 'def_type' => '', 'def_limit' => 10, 'max_limit' => 30, 'allow_orderby' => false, 'allow_show' => false, 'allow_limit' => true );
  $widgets[10] = array( 'name' => $LANG['widget_text_box'], 'file' => 'text-box.php', 'def_type' => '', 'text' => $LANG['widget_text_box_deftext'], 'allow_text' => true, 'allow_html' => true, 'allow_orderby' => false, 'allow_show' => false  );
  $widgets[11] = array( 'name' => $LANG['widget_users'], 'file' => 'users.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => $LANG['order_random'], 'name' => $LANG['order_name'], 'name desc' => $LANG['order_name_desc'], 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'], 'points' => $LANG['order_points'], 'points desc' => $LANG['order_points_desc'], 'visits' => $LANG['order_visits'], 'visits desc' => $LANG['order_visits_desc'] ), 'allow_show' => false, 'allow_limit' => true );
  $widgets[12] = array( 'name' => $LANG['widget_newsletter'], 'file' => 'newsletter.php', 'def_type' => '', 'text' => $LANG['widget_text_newsletter'], 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );
  $widgets[13] = array( 'name' => $LANG['widget_fb_like_box'], 'file' => 'facebook_like_box.php', 'def_type' => '', 'text' => $LANG['widget_fb_like_box_msg'], 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );
  $widgets[14] = array( 'name' => $LANG['widget_suggest'], 'file' => 'suggest.php', 'def_type' => '', 'text' => $LANG['widget_suggest_msg'], 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );
  $widgets[15] = array( 'name' => $LANG['widget_contact'], 'file' => 'contact.php', 'def_type' => '', 'text' => $LANG['widget_contact_msg'], 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );

  return $widgets;

}

public static function widget_from_id( $id ) {

$list = widgets::widgets_list();

if( in_array( $id, array_keys( $list ) ) ) {
  return (object)$list[$id];
}

  return false;

}

public static function available_list( $available = array() ) {

  return array_diff_key( widgets::widgets_list(), array_flip( $available ) );

}

}