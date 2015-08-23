<?php

/*

CHECK IF HAVE ITEMS

*/

function have_items( $category = array() ) {

    $GLOBALS['have_items'] = \query\main::have_stores( $category );

    /*

    ACTIVATE PAGES INFORMATIONS IF FUNCTION have_items() IS CALLED

    */

      /*

      NUMBER OF RESULTS

      */

      function results() {
        return $GLOBALS['have_items']['results'];
      }

      /*

      THIS PAGE IS

      */

      function page() {
        return $GLOBALS['have_items']['page'];
      }

      /*

      NUMBER OF PAGES

      */

      function pages() {
        return $GLOBALS['have_items']['pages'];
      }

      /*

      NEXT PAGE

      */

      function next_page() {
        if( !empty( $GLOBALS['have_items']['next_page'] ) ) {
          return $GLOBALS['have_items']['next_page'];
        }
        return false;
      }

      /*

      PREVIEW PAGE

      */

      function prev_page() {
        if( !empty( $GLOBALS['have_items']['prev_page'] ) ) {
          return $GLOBALS['have_items']['prev_page'];
        }
        return false;
      }

      return $GLOBALS['have_items']['results'];

}

/*

SHPW STORES

*/

function items( $category = array() ) {
    return \query\main::while_stores( $category );
}