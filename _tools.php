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

if( isset( $_POST['action'] ) &&  $_POST['action'] == 'set_cjimg' ) {
    
    $data = $_POST['data'];
    $cjimginfo = json_decode($data,true);
    if(!$cjimginfo){
        echo "Data error:".$data;
        
        die;
    }

    $addcount = 0;
  $stmt = $db->stmt_init();
    foreach($cjimginfo as $info){
    $stmt->prepare( "INSERT INTO cj_img (advertiserId, logoId) VALUES (?, ?)" );
  $stmt->bind_param( "ii", $info[0], $info[1] );
        if($stmt->execute()){
            $addcount++;
            $stmt->prepare( "UPDATE stores SET image = '"."https://members.cj.com/member/publisher/logo/".$info[1].".gif"."' where (image IS NULL or image = '') and cjID = ".$info[0] );
            $stmt->execute();
        }
    }
  @$stmt->close();

  echo 'SUCCESS! '.$addcount.' added';

  die;

}

$db->close();