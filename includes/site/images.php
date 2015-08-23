<?php

namespace site;

/** */

class images {

/*

Upload image

*/

public static function upload( $file, $prefix, $etc = array( 'name' => '', 'location' => '', 'current' => '', 'path' => '', 'max_size' => '', 'max_height' => '', 'max_width' => '' ), $delete_old_file = true ) {

  if( !isset( $etc['path'] ) ) $etc['path'] = '';

  if( !isset( $file['tmp_name'] ) ) {

    // check if file is empty, local or external url
    if( empty( $file ) ) {

      return ( isset( $etc['current'] ) ? $etc['current'] : false );

    } else if( filter_var( $file, FILTER_VALIDATE_URL )) {
    $ufile['tmp_name'] =  $etc['path'] . TEMP_LOCATION . '/' . basename( $file );
    $ufile['size'] = @file_put_contents( $ufile['tmp_name'], file_get_contents( $file ) );
    } else {
    $ufile['tmp_name'] =  $etc['path'] . TEMP_LOCATION . '/' . basename( $file );
    $ufile['size'] = @file_put_contents( $ufile['tmp_name'], file_get_contents( $etc['path'] . $file ) );
    }

    $ufile['name'] = basename( $ufile['tmp_name'] );
    $file = $ufile;

  }

  if( !empty( $etc['location'] ) ) {
    $location = $etc['location'];
  } else {
    $location = UPLOAD_IMAGES_LOC;
  }

  if( isset( $file['size'] ) && (int) $file['size'] === 0 ) {
    @unlink( $file['tmp_name'] );
    return ( isset( $etc['current'] ) ? $etc['current'] : false );
  }

  list( $width, $height ) = getimagesize( $file['tmp_name'] );
  if( isset( $etc['max_size'] ) && ( $etc['max_size'] * 1024 ) < $file['size'] || isset( $etc['max_height'] ) && $etc['max_height'] < $height || isset( $etc['max_width'] ) && $etc['max_width'] < $width ) {
  if( !empty( $file['tmp_name'] ) ) {
    // delete the temporary file
    @unlink( $file['tmp_name'] );
  }
  return ( !empty( $etc['current'] ) ? $etc['current'] : false ); // It's not a image in standars, size it's too big or filename it's empty. In this case return the current image, if is not set, then return false.
  }

  if( ! \site\utils::file_has_extension( $file['name'], '.jpg,.jpeg,.png,.gif' ) ) {
  if( !empty( $file['tmp_name'] ) ) {
    // delete the temporary file
    @unlink( $file['tmp_name'] );
  }
  return ( !empty( $etc['current'] ) ? $etc['current'] : false ); // This file has not an allowed extension.
  }

  $new_name = ( !empty( $etc['name'] ) && strtolower( $etc['name'] ) !== 'auto' ) ? $etc['name'] : uniqid( $prefix ) . \site\utils::get_extension( $file['name'] );

  if( file_exists( $etc['path'] . $location . '/' . $new_name ) || !copy( $file['tmp_name'], $etc['path'] .  $location . '/' . $new_name ) ) {
    // delete the temporary file
    @unlink( $file['tmp_name'] );
    return ( !empty( $etc['current'] ) ? $etc['current'] : false );
  }

  if( !empty( $etc['current'] ) && $delete_old_file === true ) {
    // delete the temporary file
    @unlink( $etc['path'] . $etc['current'] );
  }

  // delete the temporary file
  @unlink( $file['tmp_name'] );

  return $location . '/' . $new_name;

}

}