<?php

namespace site;

/** */

class mail {

public static function send( $to, $subject, $params = array(), $vars = array() ) {

global $LANG;

if( empty( $params['template'] ) ) {
  if( empty( $params['message'] ) )
    return false;
  else {
    $text = $params['message'];
  }
} else {

if( !file_exists( ( isset( $params['path'] ) ? $params['path'] : '' ) . TMAIL_LOCATION . '/' . $params['template'] . '.html' ) ) {
  return false;
}

  $text = file_get_contents( ( isset( $params['path'] ) ? $params['path'] : '' ) . TMAIL_LOCATION . '/' . $params['template'] . '.html' );
  extract( $vars );
  eval("\$text = \"$text\";");

}

include ( isset( $params['path'] ) ? $params['path'] : '' ) . LBDIR . '/PHPMailer/class.phpmailer.php';

$mail = new \PHPMailer();

$mail->CharSet = 'UTF-8';

$mail->AddReplyTo( ( isset( $params['reply_to'] ) ? $params['reply_to'] : \query\main::get_option( 'email_answer_to' ) ), ( isset( $params['reply_name'] ) ? $params['reply_name'] : '' ) );

$mail->From = ( isset( $params['from_name'] ) ? $params['from_name'] : \query\main::get_option( 'email_answer_to' ) );
$mail->FromName = ( isset( $params['from_email'] ) ? $params['from_email'] : \query\main::get_option( 'email_from_name' ) );
$mail->AddAddress( $to );
$mail->Subject  = $subject;

$mail->MsgHTML( $text );
$mail->IsHTML(true);

switch( \query\main::get_option( 'mail_method' ) ) {

case 'SMTP':

  $mail->IsSMTP(); // tell the class to use SMTP

  $mail->SMTPAuth = \query\main::get_option( 'smtp_auth' );
  $mail->Port = \query\main::get_option( 'smtp_port' );
  $mail->Host = \query\main::get_option( 'smtp_host' );
  $mail->Username = \query\main::get_option( 'smtp_user' );
  $mail->Password = \query\main::get_option( 'smtp_password' );

break;

case 'sendmail':

  $mail->isSendmail();
  $mail->Sendmail = \query\main::get_option( 'sendmail_path' );

break;

default:

  $mail->isMail();

break;

}

if( $mail->Send() ) return true;
else return false;

}

}