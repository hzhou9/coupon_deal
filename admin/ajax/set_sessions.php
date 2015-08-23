<?php

session_start();

if( isset( $_POST['ses'] ) && isset( $_POST['type'] ) ) {

$_SESSION['ses_set'][$_POST['ses']] = $_POST['type'];

}