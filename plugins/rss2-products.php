<?php

/*

// You can kill the script if extension it's not xml, it could be xml, html or php if you choose to not uncomment this

if( defined( 'SEO_LINKS' ) && SEO_LINKS === true && isset( $_GET['ext'] ) ) {
if( !isset( $_GET['ext'] ) || strtolower( $_GET['ext'] ) !== 'xml' )
  die;
}

*/

/** HEADER **/

  header('Content-Type: application/xml');

/** ----- **/

$categories = array();

$categories['max'] = 100; // max should be defined

if( isset( $_GET['cat'] ) ) {

  $categories['categories'] = $_GET['cat'];

} else if( isset( $_GET['store'] ) ) {

  $categories['store'] = $_GET['store'];

}

echo '<?xml version="1.0" encoding="UTF-8" ?>

<rss version="2.0">

    <channel>

    <title>' . \query\main::get_option( 'sitename' ) . ' Products</title>
    <link>' . $GLOBALS['siteURL'] . '</link>
    <description>List of products</description>
    <language>en-us</language>';

    foreach( \query\main::while_products( $categories ) as $item ) {

      echo '
        <item>
            <title>' . $item->title . '</title>
            <image>
              <link>' . $item->link . '</link>
              <title>' . $item->title . '</title>
              <url>' . \query\main::product_avatar( $item->image ) . '</url>
            </image>
            <link>' . $item->link . '</link>
            <description><![CDATA[' . $item->description . ']]></description>
            <pubDate>' . date( 'r', strtotime( $item->date ) ) . '</pubDate>
            <guid>' . $item->link . '</guid>
        </item>
      ';

      }

    echo '</channel>
</rss>';

?>