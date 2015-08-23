<?php

namespace site;

/** */

class language {

public static function languages() {

  $lang = array();
  // built-in languages
  $lang['english']['name'] = 'English';
  $lang['english']['image'] = $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/US.png';
  $lang['english']['location'] = LDIR . '/english.php';
  $lang['romanian']['name'] = 'Româna';
  $lang['romanian']['image'] = $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/Romania.png';
  $lang['romanian']['location'] = LDIR . '/romanian.php';
  // user plugins
  foreach( \query\main::user_plugins( 'language' ) as $ulang ) {
  $lang['up_' . strtolower( $ulang->name )]['name'] = $ulang->name;
  $lang['up_' . strtolower( $ulang->name )]['image'] = $GLOBALS['siteURL'] . $ulang->image;
  $lang['up_' . strtolower( $ulang->name )]['location'] = UPDIR . '/'. $ulang->main_file;
  }
  return $lang;

}

}