<?php

class template {

public static function have_widgets() {
  if( file_exists( DIR . DIRECTORY_SEPARATOR . THEMES_LOC . DIRECTORY_SEPARATOR . \query\main::get_option( 'theme' ) . DIRECTORY_SEPARATOR . 'functions.php' ) ) {
  @require_once DIR . DIRECTORY_SEPARATOR . THEMES_LOC . DIRECTORY_SEPARATOR . \query\main::get_option( 'theme' ) . DIRECTORY_SEPARATOR . 'functions.php';

    if( function_exists( 'register_widgets' ) ) {
      return register_widgets();
    }
  }

  return false;
}

public static function have_reward() {
  if( file_exists( DIR . DIRECTORY_SEPARATOR . THEMES_LOC . DIRECTORY_SEPARATOR . \query\main::get_option( 'theme' ) . DIRECTORY_SEPARATOR . 'functions.php' ) ) {

  @require_once DIR . DIRECTORY_SEPARATOR . THEMES_LOC . DIRECTORY_SEPARATOR . \query\main::get_option( 'theme' ) . DIRECTORY_SEPARATOR . 'functions.php';

    if( function_exists( 'theme_has_rewards' ) && theme_has_rewards() ) {
      return true;
    }
  }

  return false;
}

public static function suggestion_intent( $id ) {

global $LANG;

  switch( $id ) {

    case 1:
    return $LANG['suggestion_store_owner'];
    break;

    case 2:
    return $LANG['suggestion_just_suggestion'];
    break;

  }

  return '-';

}

public static function read_dirs( $dir = '' ) {
  $dir = empty( $dir ) ? DIR . DIRECTORY_SEPARATOR . THEMES_LOC : $dir;

  if( !is_dir( $dir ) ) {
    return false;
  }

  $files = array();

  foreach( scandir( $dir ) as $f ) {
    if( $f !== '.' && $f !== '..' ) {
      if( is_dir( rtrim( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $f ) ) {
        $files['dirs'][] = $f;
      } else {
        $files['files'][] = $f;
      }
    }
  }

  return $files;
}

public static function read_theme_info_file( $theme = '' ) {
  if( empty( $theme ) || !is_dir( $theme_loc = rtrim( DIR . DIRECTORY_SEPARATOR . THEMES_LOC, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $theme ) ) {
    return false;
  }

  if( !file_exists( rtrim( $theme_loc, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'infos.txt'  ) ) {
    return false;
  }

  $infos = array();

  if( $content = @file_get_contents( rtrim( $theme_loc, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'infos.txt' ) ) {

    $lines = explode( "\n", $content );

    foreach( $lines as $line ) {

      $line = explode( ':', trim( $line ), 2 );

      switch( trim( strtolower( $line[0] ) ) ) {

        case 'version':
        $infos['version'] = trim( $line[1] );
        break;

        case 'published by':
        preg_match( '/(.*)\ (http(.*))?/i', $line[1], $pb );
        if( isset( $pb[1] ) )$infos['published_by'] = $pb[1];
        if( isset( $pb[2] ) )$infos['publisher_url'] = $pb[2];
        break;

        case 'description':
        $infos['description'] = trim( $line[1] );
        break;

      }

    }

  }
  return $infos;
}

public static function theme_editor_map( $theme = '' ) {
  $files = template::map_of_files_recursive( DIR . DIRECTORY_SEPARATOR . THEMES_LOC . DIRECTORY_SEPARATOR . $theme, '.php,.html,.htm,.xhtml,.css,.js' );
  return array_map( function( $file ) {
    return substr( $file, 1 );
  }, array_values( $files ) );
}

public static function plugin_editor_map( $plugin = '' ) {
  $files = template::map_of_files_recursive( DIR . DIRECTORY_SEPARATOR . UPDIR . DIRECTORY_SEPARATOR . $plugin, '.php,.html,.htm,.xhtml,.css,.js' );
  return array_map( function( $file ) {
    return substr( $file, 1 );
  }, $files );
}

public static function theme_min() {
  // check if a theme have minimum requirements
  return array( '404.php', 'category.php', 'index.php', 'page.php', 'reviews.php', 'search.php', 'single.php', 'site_header.php', 'site_footer.php', 'store.php', 'stores.php', 'style.css' );
}

public static function theme_have_min( $files = array() ) {
  $required_files = template::theme_min();
  if( count( array_intersect( $files, $required_files ) ) !== count( $required_files ) ) {
    return false;
  }

  return true;
}

public static function map_of_files_recursive( $directory, $allowed_ext = '' ) {
if( !is_dir( $directory ) ) {
  return false;
}

$dir = array();

foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory ) ) as $filename ) {
  if( \site\utils::file_has_extension( $filename, $allowed_ext ) )
  $dir[] = str_replace( $directory, '', $filename );
}

 return $dir;
}

}