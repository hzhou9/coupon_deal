<?php

namespace site;

/** */

class files {

public static function delete_directory( $dir ) {

  $files = glob( rtrim( $dir, '/' ) . '/*' );

  foreach( $files as $file ) {
    if( is_dir( $file ) ) {
      \site\files::delete_directory( $file );
    } else {
    @unlink( $file );
    }
  }

  @rmdir( $dir );

}

}