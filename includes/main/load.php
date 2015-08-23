<?php

namespace main;

/** */

class load extends template {

function __construct() {

  global $GET, $db;

  date_default_timezone_set( \query\main::get_option( 'timezone' ) );
  $db->query( "SET time_zone='" . date('P') . "'" );

  $GLOBALS['siteURL'] = \site\utils::site_url();
  $GLOBALS['me'] = \user\main::is_logged();

  $this->template = \query\main::get_option( 'theme' );
  $this->language = $this->language();
  $this->ap_language = $this->admin_panel_language();
  if( isset( $GET['loc'] ) ) {
    $this->page_type = $GET['loc'];
  } else {
    $this->page_type = 'index';
  }
  $this->id = ( isset( $GET['id'] ) ? $GET['id'] : '' );

}

private function language() {

  $language = \query\main::get_option( 'sitelang' );
  $languages = \site\language::languages();
  if( (boolean) \query\main::get_option( 'allow_select_lang' ) && isset( $_COOKIE['language'] ) && in_array( strtolower( $_COOKIE['language'] ), array_keys( $languages ) ) ) {
    $language = strtolower( $_COOKIE['language'] );
  }
  if( file_exists( $languages[$language]['location'] ) ) {
    return array( 'name' => $languages[$language]['name'], 'location' => $languages[$language]['location'] );
  }
  return array( 'name' => $languages[$language]['name'], 'location' => $languages[$language]['location'] );

}

private function admin_panel_language() {

  $language = \query\main::get_option( 'adminpanel_lang' );
  $languages = \site\language::languages();
  if( file_exists( $languages[$language]['location'] ) ) {
    return array( 'name' => $languages[$language]['name'], 'location' => $languages[$language]['location'] );
  }
  return array( 'name' => $languages[$language]['name'], 'location' => $languages[$language]['location'] );

}

public function get_language() {

  include DIR . DIRECTORY_SEPARATOR . LDIR . '/english.php';

  $language = $this->language();
  if( $language['name'] !== 'English' ) {
    include DIR . DIRECTORY_SEPARATOR . $language['location'];
  }

  return $LANG;

}

public function get_ap_language() {

  include DIR . DIRECTORY_SEPARATOR . LDIR . '/english.php';

  $language = $this->admin_panel_language();
  if( $language['name'] !== 'English' ) {
    include DIR . DIRECTORY_SEPARATOR . $language['location'];
  }

  return $LANG;

}

private function plugin( $id = '' ) {

  $this->template_plugin( $id );

}

private function ajax( $id = '' ) {

  $this->template_ajax( $id );

}

private function cron( $id = '' ) {

  $this->template_cron( $id );

}

private function page_tpage( $id = '' ) {

  include IDIR . '/functions/functions_page.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_tpage( $id );
  $this->template_footer();

}

private function page_page() {

  include IDIR . '/functions/functions_page.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_page();
  $this->template_footer();

}

private function page_single() {

  include IDIR . '/functions/functions_single.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_single();
  $this->template_footer();

}

private function page_product() {

  include IDIR . '/functions/functions_product.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_product();
  $this->template_footer();

}

private function page_category() {

  include IDIR . '/functions/functions_category.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_category();
  $this->template_footer();

}

private function page_search() {

  include IDIR . '/functions/functions_search.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_search();
  $this->template_footer();

}

private function page_store() {

  include IDIR . '/functions/functions_store.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_store();
  $this->template_footer();

}

private function page_stores() {

  include IDIR . '/functions/functions_stores.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_stores();
  $this->template_footer();

}

private function page_reviews() {

  include IDIR . '/functions/functions_reviews.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_reviews();
  $this->template_footer();

}

private function page_user( $id = '' ) {

  include IDIR . '/functions/functions_user.php';
  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_user( $id );
  $this->template_footer();

}

private function page_index() {

  include IDIR . '/functions/functions_global.php';

  $this->template_header();
  $this->template_index();
  $this->template_footer();

}

public function execute() {

if( file_exists( THEMES_LOC . '/' . $this->template . '/functions.php' ) ) {
  include THEMES_LOC . '/' . $this->template . '/functions.php';
}

if( $redirect_to = \user\main::banned() ) {
  if( !filter_var( $redirect_to, FILTER_VALIDATE_URL ) ) {
  header( 'HTTP/1.0 403 Forbidden' );
  } else {
  header( 'Location: ' . $redirect_to );
  }
  die;
}

if( isset( $_GET['ref'] ) ) {
  setcookie ( 'referrer', (int) $_GET['ref'], strtotime( '+30 days' ) );
}

switch( $this->page_type ) {

  case 'page': $this->page_page(); break;
  case 'single': $this->page_single(); break;
  case 'product': $this->page_product(); break;
  case 'category': $this->page_category(); break;
  case 'search': $this->page_search(); break;
  case 'store': $this->page_store(); break;
  case 'stores': $this->page_stores(); break;
  case 'reviews': $this->page_reviews(); break;
  case 'user': $this->page_user( $this->id ); break;
  case 'tpage': $this->page_tpage( $this->id ); break;
  case 'ajax': $this->ajax( $this->id );break;
  case 'cron': $this->cron( $this->id );break;
  case 'plugin': $this->plugin( $this->id );break;
  default: $this->page_index(); break;

}

}

}