<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS (see changelog) */

error_reporting( E_ALL );

/** include FILES */

include '../settings.php';

/** CONNECT TO DB */

include DIR . '/' . IDIR . '/site/db.php';

if( $db->connect_errno ) {
  header( 'Location: ../index.php' );
  die;
}

$db->set_charset( DB_CHARSET );

/** */

spl_autoload_register(function ( $cn ) {

  $type = strstr( $cn, '\\', true );

  if( $type == 'plugin' ) {
    $cn = str_replace( '\\', '/', $cn );
    include DIR . '/' . UPDIR . '/' . substr( $cn, strpos( $cn, '/' )+1 ) . '.php';
  } else {
    include DIR . '/' . IDIR . '/' . str_replace( '\\', '/', $cn ) . '.php';
  }

});

/** */

$load =  new \main\load;
$LANG = $load->get_ap_language();

        include 'includes/functions.php';

if( $GLOBALS['me'] && $GLOBALS['me']->is_subadmin ) {

        include 'includes/template.php';
        include 'etc/connector.php';
        include 'includes/admin.php';
        include 'includes/query.php';
        include 'includes/widgets.php';
        include 'includes/importer.php';

        // this it's not mandatory, but good to clear informations in real time
        actions::cleardata( true, \query\main::get_option( 'delete_old_coupons' ) );

    if( isset( $_GET['ajax'] ) && file_exists( 'ajax/' . $_GET['ajax'] ) ) {
            include 'ajax/' . $_GET['ajax'];
            die;
    } else if( isset( $_GET['download'] ) && file_exists( 'etc/download/' . $_GET['download'] ) ) {
            include 'etc/download/' . $_GET['download'];
            die;
    }

        include 'html/header.php';
        include 'html/nav.php';
        include 'html/logged.php';

    new importer;

    if( !isset( $_GET['action'] ) ) {
      $_GET['action'] = '';
    }

    if( !empty( $_GET['plugin'] ) && file_exists( DIR . '/' . IDIR . '/user_plugins/' . $_GET['plugin'] ) ) {

        include DIR . '/' . IDIR . '/user_plugins/' . $_GET['plugin'];

    } else if( isset( $_GET['route'] ) && file_exists( $_GET['route'] ) ) {

        include $_GET['route'];

    } else

        include 'dashboard.php';

} else if( isset( $_GET['action'] ) && $_GET['action'] == 'password_recovery' ) {

        include 'html/header.php';
        include 'password_recovery.php';

} else {

        include 'html/header.php';
        include 'signin.php';

}

include 'html/footer.php';

$db->close();