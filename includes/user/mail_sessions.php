<?php

namespace user;

/** */

class mail_sessions {

public static function check( $loc = '', $param = array() ) {

global $db;

  if( !isset( $param['session'] ) ) {
    $param['session'] = '';
  }

  $stmt = $db->stmt_init();

    switch( $loc ) {

    case 'confirmation':
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "email_sessions WHERE user = ? AND target='confirmation' AND session = ? AND expiration > NOW()" );
    $stmt->bind_param( "is", $param['user'], $param['session'] );
    $stmt->bind_result( $count );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    if( $count > 0 ) {
    return true;
    }
    return false;
    break;

    case 'password_recovery':
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "email_sessions WHERE user = ? AND target='password_recovery' AND session = ? AND expiration > NOW()" );
    $stmt->bind_param( "is", $param['user'], $param['session'] );
    $stmt->bind_result( $count );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    if( $count > 0 ) {
    return true;
    }
    return false;
    break;

    case 'subscription':
    case 'unsubscription':
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "email_sessions WHERE email = ? AND target = ? AND session = ? AND expiration > NOW()" );
    $stmt->bind_param( "sss", $param['email'], $loc, $param['session'] );
    $stmt->bind_result( $count );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    if( $count > 0 ) {
    return true;
    }
    return false;
    break;

    }

  return false;

}


public static function insert( $loc = '', $param = array() ) {

global $db;

  if( !isset( $param['session'] ) ) {
    $param['session'] = '';
  }

  $stmt = $db->stmt_init();

    switch( $loc ) {

    case 'confirmation':
    $now = date( 'Y.m.d, h:i:s' );
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "email_sessions (user, target, session, expiration, date) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 DAY), ?)" );
    $stmt->bind_param( "isss", $param['user'], $loc, $param['session'], $now );
    if( $stmt->execute() ) {
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE user = ? AND target = ? AND date < ?" );
    $stmt->bind_param( "iss", $param['user'], $loc, $now );
    $stmt->execute();
    $stmt->close();
    return true;
    }
    $stmt->close();
    return false;
    break;

    case 'password_recovery':
    $now = date( 'Y.m.d, h:i:s' );
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "email_sessions (user, target, session, expiration, date) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR), ?)" );
    $stmt->bind_param( "isss", $param['user'], $loc, $param['session'], $now );
    if( $stmt->execute() ) {
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE user = ? AND target = ? AND date < ?" );
    $stmt->bind_param( "iss", $param['user'], $loc, $now );
    $stmt->execute();
    $stmt->close();
    return true;
    }
    $stmt->close();
    return false;
    break;

    case 'subscription':
    case 'unsubscription':
    $now = date( 'Y.m.d, h:i:s' );
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "email_sessions (email, target, session, expiration, date) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR), ?)" );
    $stmt->bind_param( "ssss", $param['email'], $loc, $param['session'], $now );
    if( $stmt->execute() ) {
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE email = ? AND target = ? AND date < ?" );
    $stmt->bind_param( "sss", $param['email'], $loc, $now );
    $stmt->execute();
    $stmt->close();
    return true;
    }
    $stmt->close();
    return false;
    break;

    }

  return false;

}

public static function clear( $loc = '', $param = array() ) {

global $db;

  if( !isset( $param['session'] ) ) {
    $param['session'] = '';
  }

  $stmt = $db->stmt_init();

    switch( $loc ) {

    case 'confirmation':
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE (user = ? OR session = ?) AND target = 'confirmation'" );
    $stmt->bind_param( "is", $param['user'], $param['session'] );
    $execute = $stmt->execute();
    $stmt->close();
    if( $execute ) {
    return true;
    }
    return false;
    break;

    case 'password_recovery':
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE (user = ? OR session = ?) AND target = 'password_recovery'" );
    $stmt->bind_param( "is", $param['user'], $param['session'] );
    $execute = $stmt->execute();
    $stmt->close();
    if( $execute ) {
    return true;
    }
    return false;
    break;

    case 'subscription':
    case 'unsubscription':
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE (email = ? OR session = ?) AND target = ?" );
    $stmt->bind_param( "sss", $param['email'], $param['session'], $loc );
    $execute = $stmt->execute();
    $stmt->close();
    if( $execute ) {
    return true;
    }
    return false;
    break;

    }

  return false;

}

}