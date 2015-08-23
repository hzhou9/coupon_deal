<?php

$json['answer'] = false;

if( isset( $_POST['csrf'] ) && isset( $_SESSION['chat_csrf'] ) && $_SESSION['chat_csrf'] == $_POST['csrf'] && isset( $_POST['msg'] ) && actions::post_chat_message( $_POST['msg'] ) ) {

  $json['answer'] = true;

}

echo json_encode( $json );