<!DOCTYPE html>

<html>

<head>
<title><?php echo \query\main::get_option( 'sitetitle' ); ?> - Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="robots" content="noindex, nofollow">
<link href="<?php echo \query\main::get_option( 'admintheme' ); ?>" media="all" rel="stylesheet" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//www.google.com/jsapi"></script>
<script src="html/actions.js"></script>
<script src="js/show_next.js"></script>
<script src="js/sinspan.js"></script>
<script src="js/data_search.js"></script>
<?php echo add_extra_head(); ?>
</head>

<body>

<div id="wrap">