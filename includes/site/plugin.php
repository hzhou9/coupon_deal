<?php

namespace site;

/** */

class plugin {

public static function constants() {

  $dir = array();
  $dir['{SITEURL}'] = rtrim( $GLOBALS['siteURL'], '/' );
  $dir['{DB_PREFIX}'] = DB_TABLE_PREFIX;
  $dir['{DB_CARSET}'] = DB_CHARSET;
  $dir['{ADMIN_DIR}'] = ADMINDIR;
  $dir['{INCLUDES_DIR}'] = IDIR;
  $dir['{LANGUAGES_DIR}'] = LDIR;
  $dir['{PLUGINS_DIR}'] = PDIR;
  $dir['{USER_PLUGINS_DIR}'] = UPDIR;
  $dir['{LIBRARIES_DIR}'] = LBDIR;
  $dir['{THEMES_DIR}'] = THEMES_LOC;
  $dir['{IMAGES_DIR}'] = UPLOAD_IMAGES_LOC;
  $dir['{WIDGETS_DIR}'] = WIGETS_LOCATION;
  $dir['{AJAX_DIR}'] = AJAX_LOCATION;
  $dir['{MAIL_TEMPLATES_DIR}'] = TMAIL_LOCATION;
  $dir['{CRON_DIR'] = CRONDIR;

  return $dir;

}

public static function replace_constant( $string ) {

  $str = str_replace( array_keys( self::constants() ), array_values( self::constants() ), $string );
  return $str;

}

}