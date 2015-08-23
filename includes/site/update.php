<?php

namespace site;

/** */

class update {

/** SET DEFINE */

public static function set_define( $file = '', $updates = array(), $chmod_after = 0644, $chmod_before = 0777 ) {

if( !file_exists( $file ) ) {
  return false;
}

@chmod( $file, $chmod_before );

$text = @file_get_contents( $file );

foreach( $updates as $k => $v ) {
  $text = preg_replace( '/(define)\((\s+)?([\'"])(' . $k . ')([\'"]),(\s+)?([\'"])?([^\'"]+)?([\'"])?(\s+)?\);/i','$1($2$3$4$5,$6$7' . $v . '$9$10);', $text );
}

if( @file_put_contents( $file, utf8_encode(stripslashes($text)) ) ) {
  @chmod( $file, $chmod_after );
  return true;
}

  @chmod( $file, $chmod_after );

  return false;

}

}