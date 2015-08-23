<!DOCTYPE html>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo meta_charset(); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<title><?php echo meta_title(); ?></title>
<meta name="description" content="<?php echo meta_description(); ?>" />
<meta name="keywords" content="<?php echo meta_keywords(); ?>" />
<meta property="og:title" content="<?php echo meta_title(); ?>" />
<meta property="og:description" content="<?php echo meta_description(); ?>" />
<meta property="og:image" content="<?php echo meta_image( 'images/logo.png' ); ?>" />
<meta name="robots" content="index, follow" />
<link href="<?php echo theme_location(); ?>/reset.css" media="all" rel="stylesheet" />
<link href="<?php echo theme_location(); ?>/style.css" media="all" rel="stylesheet" />
<link href="//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="<?php echo theme_location(); ?>/js/read_more.js"></script>
<script src="<?php echo theme_location(); ?>/js/functions.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.1.6/ZeroClipboard.js"></script>
<?php echo add_extra_head(); ?>
</head>

<body>

<div style="height: 50px"></div>

<header>

<nav class="menu">

<div class="left">

<ul>
<li class="close"><a href="#">Close</a></li>
<li class="swcat"><a href="#">Categories</a></li>
<li><a href="<?php echo tlink( 'stores' ); ?>">Brands</a></li>
</ul>

</div>

<div class="right">

<ul>

<?php if( $me = me() ) { ?>
<li class="user-menu">
<a href="<?php echo tlink( 'user/edit-profile' ); ?>"><img src="<?php echo user_avatar( $me->Avatar ); ?>" alt="" /><span><?php echo $me->Name; ?></span></a>
<ul>
<li><a href="<?php echo tlink( 'user/edit-profile' ); ?>" class="profile">My Profile</a></li>
<li><a href="<?php echo tlink( 'user/wall' ); ?>" class="wall">My Wall</a></li>
<li><a href="<?php echo tlink( 'user/favorites' ); ?>" class="favorites">My Favorites</a></li>
<?php if( theme_has_rewards() ) { ?>
<li><a href="<?php echo tlink( 'user/rewards' ); ?>" class="rewards">Rewards</a></li>
<?php } if( $me->Stores > 0 ) { ?>
<li><a href="<?php echo tlink( 'user/owner_actions', 'action=my_stores' ); ?>" class="stores">My Stores</a></li>
<li><a href="<?php echo tlink( 'user/owner_actions', 'action=my_coupons' ); ?>" class="coupons">My Coupons</a></li>
<li><a href="<?php echo tlink( 'user/owner_actions', 'action=my_products' ); ?>" class="products">My Products</a></li>
<?php } else { ?>
<li><a href="<?php echo tlink( 'user/owner_actions', 'action=add_store' ); ?>" class="stores">Add a Store</a></li>
<?php } ?>
<li><a href="<?php echo tlink( 'user/logout' ); ?>" class="logout">Logout</a></li>
</ul>
</li>

<?php } else { ?>
<li><a href="<?php echo tlink( 'user/register' ); ?>">Register</a></li>
<li><a href="<?php echo tlink( 'user/login' ); ?>">Login</a></li>
<?php } ?>

</ul>

</div>

</nav>

</header>

<header>

<div class="top-bar">

<div class="left">
  <a href="<?php echo tlink( 'index' ); ?>"><div class="logo"></div></a>
</div>

<div class="right">

<form action="<?php echo tlink( 'search' ); ?>" method="GET" onsubmit='if( $(this).find("input").val().length == 0 ) return false;'>
<input type="text" name="s" maxlength="50" placeholder="Search and Save" />
<select name="type">
<option value="coupons">Coupons</option>
<?php echo ( !theme_has_products() ?: '<option value="products">Products</option>' ); ?>
</select>
<button class="search_button">Search</button>
</form>

</div>

</div>

</header>

<div class="top-categories">

<section>

<h2>Categories</h2>

<?php

foreach( all_grouped_categories() as $category ) {

  echo '<ul>';
  echo '<li><a href="' . $category['infos']->link . '">' . $category['infos']->name . '</a>';

  if( isset( $category['subcats'] ) ) {
  echo '<ul>';

  foreach( $category['subcats'] as $subcategory ) {
    echo '<li><a href="' . $subcategory->link . '">' . $subcategory->name . '</a></li>';
  }

  echo '</ul>';
  }

  echo '</li></ul>';

}

?>

</section>

</div>

<div id="wrap">

<div class="container">