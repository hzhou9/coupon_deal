<?php

/** */

class plugin_installer {

/*

Construct class

*/

function __construct( $dir ) {

global $db, $LANG;

/*

CURRENT DIRECTORY

*/

$this->dir = rtrim( $dir, '/' );

/*

PLUGIN DIRECTORY

*/

$this->directory = DIR . '/' . UPDIR . '/' . rtrim( $dir, '/' ) . '/';

/*

ALL FILES INSIDE THE PLUGIN

*/

$this->urls = glob( $this->directory . '*' );

/*

CHECK THE XML FILE

*/

$this->xml = $this->check_xml();

/*

ALL AVAILABLE SCOPES FOR A PLUGIN

*/

$this->scopes = $this->scopes();

$this->db = $db;

$this->lang = $LANG;

}


/*

List of all scopes

*/

private function scopes() {

  return array(
  'language' => array( 'menu' => false, 'options' => false, 'db_query' => false, 'add_head' => false, 'extend_vars' => false ),
  'feed_server' => array( 'menu' => false, 'options' => false, 'db_query' => false, 'add_head' => false, 'extend_vars' => false ),
  'pay_gateway' => array( 'menu' => false, 'options' => true, 'db_query' => true, 'add_head' => false, 'extend_vars' => true )
  );

}

/*

List of all files

*/

public function files() {

  return $this->urls;

}

/*

Plugin name

*/

public function name() {

  return $this->xml->name;

}

/*

Main file

*/

public function main_file() {

  return $this->dir . '/' . $this->xml->main_file;

}

/*

Options file

*/

public function options_file() {

  if( isset( $this->xml->options_file ) ) {
    $scope = (string) $this->scope();
    if( empty( $scope ) || $this->scopes[$scope]['options'] ) {
      return $this->dir . '/' . $this->xml->options_file;
    }
  }
  return '';

}

/*

Image

*/

public function image() {

  if( !isset( $this->xml->image ) ) return '';
  return UPDIR . '/' . $this->dir . '/' . $this->xml->image;

}

/*

Scope

*/

public function scope() {

  if( !isset( $this->xml->scope ) ) return '';
  return $this->xml->scope;

}

/*

It's ready to show this plugin in menu?

*/

public function menu_ready() {

  if( isset( $this->xml->menu_ready ) && preg_match( '/^(1|true|yes)$/', $this->xml->menu_ready ) ) {
    if( $this->menu() ) {
      return true;
    }
  }
  return false;

}

/*

Show in menu

*/

public function menu() {

  if( isset( $this->xml->menu ) && preg_match( '/^(1|true|yes)$/', $this->xml->menu ) ) {
    $scope = (string) $this->scope();
    if( empty( $scope ) || $this->scopes[$scope]['menu'] ) {
      return true;
    }
  }
  return false;

}

/*

Extended variables

*/

public function extend_vars() {

  if( isset( $this->xml->extend ) ) {
    $scope = (string) $this->scope();
    if( empty( $scope ) || $this->scopes[$scope]['extend_vars'] ) {
      return $this->xml2array( $this->xml->extend );
    }
  }
  return '';

}

/*

Description

*/

public function description() {

  if( isset( $this->xml->description ) ) {
    return $this->xml->description;
  }
  return '';

}

/*

Version

*/

public function version() {
  if( isset( $this->xml->version ) ) {
    return $this->xml->version;
  }
  return '1.00';
}

/*

Check for updates at this address

*/

public function update_checker() {

  if( isset( $this->xml->update ) ) {
    return $this->xml->update;
  }
  return '';

}

/*

Uninstall

*/

public function uninstall() {

  if( isset( $this->xml->uninstall ) ) {
    return $this->xml2array( $this->xml->uninstall );
  }
  return '';

}

/*

Database queries

*/

public function db_query() {

  if( isset( $this->xml->db_query ) ) {
    $scope = (string) $this->scope();
    if( empty( $scope ) || $this->scopes[$scope]['db_query'] ) {
      $out = array();
      foreach( $this->xml->db_query as $v ) {
        $out[] = \site\plugin::replace_constant( (string) $v );
      }
      return $out;
    }
  }
  return false;

}

/*

Add line in admin theme head

*/

public function add_to_admin_head() {

  if( isset( $this->xml->admin_head ) ) {
    $scope = (string) $this->scope();
    if( empty( $scope ) || $this->scopes[$scope]['add_head'] ) {
      $out = array();
      foreach( $this->xml->admin_head as $v ) {
        $out[] = (string) $v;
      }
      return $out;
    }
  }
  return false;

}

/*

Add line in theme head

*/

public function add_to_head() {

  if( isset( $this->xml->theme_head ) ) {
    $scope = (string) $this->scope();
    if( empty( $scope ) || $this->scopes[$scope]['add_head'] ) {
      $out = array();
      foreach( $this->xml->theme_head as $v ) {
        $out[] = (string) $v;
      }
      return $out;
    }
  }
  return false;

}

/*

PROCEED INSTALLATION

*/

public function install() {

  $stmt = $this->db->stmt_init();
  $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "plugins (user, name, image, scope, main, options, menu, menu_ready, extend_vars, description, version, update_checker, uninstall, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())" );

  // plugin name, the same with `name` tag from XML file

  $name = $this->name();

  // store the image into the public upload folder

  $image = \site\images::upload( $this->image(), 'plugin_', array( 'path' => DIR . '/', 'max_size' => 1024, 'max_width' => 600, 'max_height' => 400, 'current' => $this->image() ) );

  // all other informations about this plugin

  list( $scope, $main, $options, $menu, $menu_ready, $extend, $description, $version, $update, $uninstall ) = array( $this->scope(), $this->main_file(), $this->options_file(), $this->menu(), $this->menu_ready(), @serialize( $this->extend_vars() ), $this->description(), $this->version(), $this->update_checker(), @serialize( $this->uninstall() ) );

  $stmt->bind_param( "isssssiissdss", $GLOBALS['me']->ID, $name, $image, $scope, $main, $options, $menu, $menu_ready, $extend, $description, $version, $update, $uninstall );
  $execute = $stmt->execute();
  $stmt->close();

  if( !$execute ) {

    // delete image if it was inserted

    @unlink( DIR . '/' . $image );

    throw new Exception( $this->lang['msg_error'] );

  } else {

  /*

  INSTALLATION COMPLETE

  */

    // delete installation file

    @unlink( $this->directory . 'install.xml' );

    // insert tables, if plugin has tables

    if( $tables = $this->db_query() )
    foreach( $tables as $table ) {
      $this->db->query( $table );
    }

    // insert lines in admin head, if plugins has that

    $admin_head = $this->add_to_admin_head();
    $theme_head = $this->add_to_head();

    if( $admin_head || $theme_head ) {
    $stmt = $this->db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "head (text, admin, theme, plugin, date) VALUES (?, ?, ?, ?, NOW())" );

    $zero = 0;
    $one = 1;

    if( $admin_head ) {
      foreach( $admin_head as $line ) {
      $line = trim( $line );
      $stmt->bind_param( "siis", $line, $one, $zero, $this->dir );
      $stmt->execute();
      }
    }

    if( $theme_head ) {
      foreach( $theme_head as $line ) {
      $line = trim( $line );
      $stmt->bind_param( "siis", $line, $zero, $one, $this->dir );
      $stmt->execute();
      }
    }

    $stmt->close();
    }

 }

}

/*

Utils: xml2array

*/

private function xml2array( $object ) {

  $out = array();
  foreach( (array) $object as $k => $v ) {
    $out[$k] = ( is_object( $v ) ? (array) $v : $this->xml2array( $v ) );
  }
  return $out;

}

/*

Check the XML file

*/

private function check_xml() {

global $LANG;

  if( !file_exists( $this->directory . 'install.xml' ) ) {
    throw new Exception( $LANG['plugins_err_installmiss'] );
  } else if( ! ( $xml = @simplexml_load_file( $this->directory . 'install.xml' ) ) ) {
    throw new Exception( $LANG['plugins_err_cntreadxml'] );
  } else if( !isset( $xml->main_file ) || !file_exists( $this->directory . $xml->main_file ) ) {
    throw new Exception( $LANG['plugins_err_mainmiss'] );
  } else if( !isset( $xml->min_version ) || ( isset( $xml->scope ) && !in_array( $xml->scope, array_keys( $this->scopes() ) ) ) ) {
    throw new Exception( $LANG['plugins_err_paraiss'] );
  } else if( $xml->scope == 'language' && !file_exists( $this->directory . $xml->main_file ) ) {
    throw new Exception( $LANG['plugins_err_mainlanmiss'] );
  } else if( $xml->scope == 'feed_server' && !file_exists( $this->directory . $xml->main_file ) ) {
    throw new Exception( $LANG['plugins_err_mainfeedsmiss'] );
  } else if( $xml->scope == 'pay_gateway' && !file_exists( $this->directory . $xml->main_file ) ) {
    throw new Exception( $LANG['plugins_err_mainpgatmiss'] );
  } else if( (double) $xml->min_version > VERSION || ( isset( $xml->max_version ) && VERSION > $xml->max_version ) ) {
    throw new Exception( $LANG['plugins_err_inconpat'] );
  } else {
    return $xml;
  }

}

}