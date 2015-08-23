<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS (see changelog) */

error_reporting( E_ALL );

/** REQUIRE SETTINGS */

include 'settings.php';

include IDIR . '/site/db.php';

/** CONNECT TO DB */

if( $db->connect_errno ) {
  header( 'Location: index.php' );
  die;
}

$db->set_charset( DB_CHARSET );

/** */

spl_autoload_register(function ( $cn ) {
    include IDIR . '/' . str_replace( '\\', '/', $cn ) . '.php';
});

/** */

include ( new \main\load )->language['location'];

if( isset( $_GET['action'] ) && $_GET['action'] == 'unsubscribe' ) {

  echo '<!DOCTYPE html>

  <html>
      <head>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>' . $LANG['uunsubscr_metatitle'] . '</title>
        <link href="//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
        <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

      </head>

  <body>
      <section class="msg">';

      if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

        if( isset( $_POST['token'] ) && isset( $_POST['email'] ) && \site\utils::check_csrf( $_POST['token'], 'sendunsubscr_csrf' ) ) {

          try {

            $type = \user\main::unsubscribe( array( 'email' => $_POST['email'] ) );
            if( $type == 1 ) echo '<div class="success">' . sprintf( $LANG['uunsubscr_reqsent'], $_POST['email'] ) . '</div>';
            else echo '<div class="success">' . $LANG['uunsubscr_ok'] . '</div>';

          }

          catch ( Exception $e ) {
            echo '<div class="error">' . $e->getMessage() . '</div>';
          }

        }

      }

      $csrf = $_SESSION['sendunsubscr_csrf'] = \site\utils::str_random(10);

      echo '<h2 style="color: #000;">' . $LANG['uunsubscr_title'] . '</h2>
      ' . sprintf( $LANG['uunsubscr_body'], '<span id="seconds">5</span>' ) . ' <br /><br />
      <form method="POST" action="#" autocomplete="off">
      <input type="email" name="email" value="' . ( isset( $_GET['email'] ) ? htmlspecialchars( $_GET['email'] ) : '' ) . '" required />
      <input type="hidden" name="token" value="' . $csrf . '" />
      <button>Unsubscribe me</button>
      </form> <br /><br />
      <a href="index.php">' . $LANG['cancel'] . '</a>
      </section>
  </body>
  </html>';

  die;

} else if( isset( $_GET['action'] ) && isset( $_GET['email'] ) && isset( $_GET['token'] ) && $_GET['action'] == 'unsubscribe2' && \user\mail_sessions::check( 'unsubscription', array( 'email' => $_GET['email'], 'session' => $_GET['token'] ) ) ) {

  $stmt = $db->stmt_init();
  $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
  $stmt->bind_param( "s", $_GET['email'] );
  $stmt->execute();
  @$stmt->close();

  \user\mail_sessions::clear( 'unsubscription', array( 'email' => $_GET['email'] ) );

  echo '<!DOCTYPE html>

  <html>
      <head>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <meta http-equiv="Refresh" content="5; url=index.php" />

        <title>' . $LANG['uunsubscr2_metatitle'] . '</title>

        <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

        <script type="text/javascript">

        var i = 5;

        var interval = setInterval(function(){

        var tag = document.getElementById("seconds");
        tag.innerHTML = i;
        i--;

        if( i == 0 ) {
            clearInterval(interval);
        }

        }, 1000);

        </script>

      </head>

  <body>
      <section class="msg">
      <h2>' . $LANG['uunsubscr2_title'] . '</h2>
      ' . sprintf( $LANG['uunsubscr2_body'], '<span id="seconds">5</span>' ) . ' <br /><br />
      <a href="index.php">' . $LANG['verify_clickhere'] . '</a>
      </section>
  </body>
  </html>';

  die;

} else if( isset( $_GET['action'] ) && isset( $_GET['email'] ) && isset( $_GET['token'] ) && $_GET['action'] == 'subscribe' && \user\mail_sessions::check( 'subscription', array( 'email' => $_GET['email'], 'session' => $_GET['token'] ) ) ) {

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 1 WHERE email = ?" );
  $stmt->bind_param( "s", $_GET['email'] );
  $stmt->execute();
  @$stmt->close();

  \user\mail_sessions::clear( 'subscription', array( 'email' => $_GET['email'] ) );

  echo '<!DOCTYPE html>

  <html>
      <head>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <meta http-equiv="Refresh" content="5; url=index.php" />

        <title>' . $LANG['usubscr_metatitle'] . '</title>

        <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

        <script type="text/javascript">

        var i = 5;

        var interval = setInterval(function(){

        var tag = document.getElementById("seconds");
        tag.innerHTML = i;
        i--;

        if( i == 0 ) {
            clearInterval(interval);
        }

        }, 1000);

        </script>

      </head>

  <body>
      <section class="msg">
      <h2>' . $LANG['usubscr_title'] . '</h2>
      ' . sprintf( $LANG['usubscr_body'], '<span id="seconds">5</span>' ) . ' <br /><br />
      <a href="index.php">' . $LANG['verify_clickhere'] . '</a>
      </section>
  </body>
  </html>';

  die;

} else if( isset( $_GET['user'] ) && isset( $_GET['token'] ) && \user\mail_sessions::check( 'confirmation', array( 'user' => (int) $_GET['user'], 'session' => $_GET['token'] ) ) ) {

  $stmt = $db->stmt_init();
  $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET valid = 1 WHERE id = ?" );
  $stmt->bind_param( "i", $_GET['user'] );
  $stmt->execute();
  @$stmt->close();

  \user\mail_sessions::clear( 'confirmation', array( 'user' => (int) $_GET['user'] ) );

  // check if user has been refered

  $uinfo = \query\main::user_infos( $_GET['user'] );
  if( !empty( $uinfo->refid ) ) {
    \user\update::add_points( $uinfo->refid, \query\main::get_option( 'u_points_refer' ) );
  }

  echo '<!DOCTYPE html>

  <html>
      <head>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <meta http-equiv="Refresh" content="5; url=index.php" />

        <title>' . $LANG['uverify_metatitle'] . '</title>

        <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

        <script type="text/javascript">

        var i = 5;

        var interval = setInterval(function(){

        var tag = document.getElementById("seconds");
        tag.innerHTML = i;
        i--;

        if( i == 0 ) {
            clearInterval(interval);
        }

        }, 1000);

        </script>

      </head>

  <body>
      <section class="msg">
      <h2>' . $LANG['uverify_title'] . '</h2>
      ' . sprintf( $LANG['uverify_body'], '<span id="seconds">5</span>' ) . ' <br /><br />
      <a href="index.php">' . $LANG['verify_clickhere'] . '</a>
      </section>
  </body>
  </html>';

  die;

}

header( 'Location: index.php' );

$db->close();