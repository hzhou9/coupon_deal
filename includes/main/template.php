<?php

namespace main;

/** */

class template {

protected $template = 'default';

protected function template_header() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/site_header.php' ) )
  include THEMES_LOC . '/' . $this->template . '/site_header.php';

}

protected function template_footer() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/site_footer.php' ) )
  include THEMES_LOC . '/' . $this->template . '/site_footer.php';

}

protected function template_tpage( $id ) {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/' . $id . '.php' ) )
  include THEMES_LOC . '/' . $this->template . '/' . $id . '.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_plugin( $id ) {

  global $db;

  if( file_exists( PDIR . '/' . $id . '.php' ) )
  include PDIR . '/' . $id . '.php';

}

protected function template_ajax( $id ) {

  global $LANG;

  if( file_exists( AJAX_LOCATION . '/' . $id . '.php' ) )
  include AJAX_LOCATION . '/' . $id . '.php';

}

protected function template_cron( $id ) {

  global $db;

  if( file_exists( CRONDIR . '/tasks/' . ( $name = strtok( $id, '.' ) ) . '.php' ) )
  include CRONDIR . '/tasks/' . $name . '.php';

}

protected function template_page() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/page.php' ) )
  include THEMES_LOC . '/' . $this->template . '/page.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_single() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/single.php' ) )
  include THEMES_LOC . '/' . $this->template . '/single.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_product() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/product.php' ) )
  include THEMES_LOC . '/' . $this->template . '/product.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_category() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/category.php' ) )
  include THEMES_LOC . '/' . $this->template . '/category.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_search() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/search.php' ) )
  include THEMES_LOC . '/' . $this->template . '/search.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_user( $id ) {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/user/' . $id . '.php' ) )
  include THEMES_LOC . '/' . $this->template . '/user/' . $id . '.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_store() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/store.php' ) )
  include THEMES_LOC . '/' . $this->template . '/store.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_stores() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/stores.php' ) )
  include THEMES_LOC . '/' . $this->template . '/stores.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_reviews() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/reviews.php' ) )
  include THEMES_LOC . '/' . $this->template . '/reviews.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

protected function template_index() {

  if( file_exists( THEMES_LOC . '/' . $this->template . '/index.php' ) )
  include THEMES_LOC . '/' . $this->template . '/index.php';
  else
  include THEMES_LOC . '/' . $this->template . '/404.php';

}

}