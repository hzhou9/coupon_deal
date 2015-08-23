<div class="top-nav">

<ul class="left-top">
    <li><a href="../"> <?php echo $LANG['top_menu']; ?></a></li>
    <li><a href="../"> <?php echo sprintf( $LANG['visit_site'], \query\main::get_option( 'sitename' ) ); ?></a></li>
    <li><a href="//couponscms.com">CouponsCMS.com</a></li>
</ul>

<ul class="right-top">
  <?php if( ab_to( array( 'mail' => 'send' ) ) ) { ?>
   <li><a href="?route=users.php&amp;action=sendmail"></a></li>
  <?php } ?>
   <li><a href="<?php echo ( ab_to( array( 'users' => 'edit' ) ) ? '?route=users.php&amp;action=edit&amp;id=' . $GLOBALS['me']->ID : '#' ); ?>" class="avatar"><img src="<?php echo \query\main::user_avatar( $GLOBALS['me']->Avatar ); ?>" alt="" /> <?php echo $GLOBALS['me']->Name; ?></a>
   <div class="profhov"><a href="?route=logout.php"><?php echo $LANG['logout']; ?></a></div></li>
</ul>

</div>

<?php

$nav = array();

$nav['dashboard.php']['primary']['name'] = '<a href="?route=dashboard.php">' . $LANG['dashboard'] . '</a>';
$nav['dashboard.php']['primary']['.class'] = 'dashboard';

if( ab_to( array( 'stores' => 'view', 'stores' => 'add', 'categories' => 'view' ) ) ) {
  $nav['stores.php']['primary']['name'] = '<a href="?route=stores.php">' . $LANG['stores'] . '</a>';
  $nav['stores.php']['primary']['.class'] = 'stores';
  $nav['stores.php']['primary']['others'] = 'categories.php';
  if( ab_to( array( 'categories' => 'view' ) ) ) {
    $nav['stores.php']['categories.php'] = '<a href="?route=categories.php&amp;action=list">' . $LANG['categories'] . '</a>';
  }
  if( ab_to( array( 'stores' => 'add' ) ) ) {
    $nav['stores.php']['add'] = '<a href="?route=stores.php&amp;action=add">' . $LANG['stores_add'] . '</a>';
  }
  if( ab_to( array( 'stores' => 'view' ) ) ) {
    $nav['stores.php']['list'] = '<a href="?route=stores.php&amp;action=list">' . $LANG['stores_view'] . '</a>';
  }
}

if( ab_to( array( 'coupons' => 'view', 'coupons' => 'add' ) ) ) {
  $nav['coupons.php']['primary']['name'] = '<a href="?route=coupons.php">' . $LANG['coupons'] . '</a>';
  $nav['coupons.php']['primary']['.class'] = 'coupons';
  if( ab_to( array( 'coupons' => 'add' ) ) ) {
    $nav['coupons.php']['add'] = '<a href="?route=coupons.php&amp;action=add">' . $LANG['coupons_add'] . '</a>';
  }
  if( ab_to( array( 'coupons' => 'view' ) ) ) {
    $nav['coupons.php']['list'] = '<a href="?route=coupons.php&amp;action=list">' . $LANG['coupons_view'] . '</a>';
  }
}

if( ab_to( array( 'products' => 'view', 'products' => 'add' ) ) ) {
  $nav['products.php']['primary']['name'] = '<a href="?route=products.php">' . $LANG['products'] . '</a>';
  $nav['products.php']['primary']['.class'] = 'products';
  if( ab_to( array( 'products' => 'add' ) ) ) {
    $nav['products.php']['add'] = '<a href="?route=products.php&amp;action=add">' . $LANG['products_add'] . '</a>';
  }
  if( ab_to( array( 'products' => 'view' ) ) ) {
    $nav['products.php']['list'] = '<a href="?route=products.php&amp;action=list">' . $LANG['products_view'] . '</a>';
  }
}

if( ab_to( array( 'feed' => 'view', 'feed' => 'import' ) ) ) {
  $nav['feed.php']['primary']['name'] = '<a href="?route=feed.php">' . $LANG['feed'] . '</a>';
  $nav['feed.php']['primary']['.class'] = 'feed';
  if( ab_to( array( 'feed' => 'view' ) ) ) {
    $nav['feed.php']['list'] = '<a href="?route=feed.php&amp;action=list">' . $LANG['stores'] . '</a>';
    $nav['feed.php']['coupons'] = '<a href="?route=feed.php&amp;action=coupons">' . $LANG['coupons'] . '</a>';
  }
  if( ab_to( array( 'feed' => 'import' ) ) ) {
    $nav['feed.php']['import'] = '<a href="?route=feed.php&amp;action=import">' . $LANG['feed_icoupons'] . '</a>';
  }
}

if( ab_to( array( 'pages' => 'view', 'pages' => 'add' ) ) ) {
  $nav['pages.php']['primary']['name'] = '<a href="?route=pages.php">' . $LANG['pages'] . '</a>';
  $nav['pages.php']['primary']['.class'] = 'pages';
  if( ab_to( array( 'pages' => 'add' ) ) ) {
    $nav['pages.php']['add'] = '<a href="?route=pages.php&amp;action=add">' . $LANG['pages_add'] . '</a>';
  }
  if( ab_to( array( 'pages' => 'view' ) ) ) {
    $nav['pages.php']['list'] = '<a href="?route=pages.php&amp;action=list">' . $LANG['pages_view'] . '</a>';
  }
}

if( ab_to( array( 'users' => 'view', 'users' => 'add', 'subscribers' => 'view' ) ) ) {
  $nav['users.php']['primary']['name'] = '<a href="?route=users.php">' . $LANG['users'] . '</a>';
  $nav['users.php']['primary']['.class'] = 'users';
  if( ab_to( array( 'subscribers' => 'view' ) ) ) {
    $nav['users.php']['subscribers'] = '<a href="?route=users.php&amp;action=subscribers">' . $LANG['users_subscribers'] . '</a>';
  }
  if( $GLOBALS['me']->is_admin ) {
    $nav['users.php']['sessions'] = '<a href="?route=users.php&amp;action=sessions">' . $LANG['users_sessions'] . '</a>';
   }
  if( ab_to( array( 'users' => 'add' ) ) ) {
    $nav['users.php']['add'] = '<a href="?route=users.php&amp;action=add">' . $LANG['users_add'] . '</a>';
  }
  if( ab_to( array( 'users' => 'view' ) ) ) {
    $nav['users.php']['list'] = '<a href="?route=users.php&amp;action=list">' . $LANG['users_view'] . '</a>';
  }
}

if( ab_to( array( 'payments' => 'view' ) ) ) {
  $nav['payments.php']['primary']['name'] = '<a href="?route=paymenbts.php">' . $LANG['payments'] . '</a>';
  $nav['payments.php']['primary']['.class'] = 'payments';
  if( $GLOBALS['me']->is_admin ) {
    $nav['payments.php']['plan_add'] = '<a href="?route=payments.php&amp;action=plan_add">' . $LANG['payments_plan_add'] . '</a>';
    $nav['payments.php']['plan_view'] = '<a href="?route=payments.php&amp;action=plan_view">' . $LANG['payments_plan_view'] . '</a>';
  }
  if( ab_to( array( 'payments' => 'view' ) ) ) {
    $nav['payments.php']['list'] = '<a href="?route=payments.php&amp;action=list">' . $LANG['payments_invoices'] . '</a>';
  }
}

if( ab_to( array( 'reviews' => 'view', 'reviews' => 'add' ) ) ) {
  $nav['reviews.php']['primary']['name'] = '<a href="?route=reviews.php">' . $LANG['reviews'] . '</a>';
  $nav['reviews.php']['primary']['.class'] = 'reviews';
  if( ab_to( array( 'reviews' => 'add' ) ) ) {
    $nav['reviews.php']['add'] = '<a href="?route=reviews.php&amp;action=add">' . $LANG['reviews_add'] . '</a>';
  }
  if( ab_to( array( 'reviews' => 'view' ) ) ) {
    $nav['reviews.php']['list'] = '<a href="?route=reviews.php&amp;action=list">' . $LANG['reviews_view'] . '</a>';
  }
}

if( ab_to( array( 'suggestions' => 'view' ) ) ) {
  $nav['suggestions.php']['primary']['name'] = '<a href="?route=suggestions.php">' . $LANG['suggestions'] . '</a>';
  $nav['suggestions.php']['primary']['.class'] = 'suggestions';
}

if( ab_to( array( 'claim_reqs' => 'view' ) ) && template::have_reward() ) {
  $nav['rewards.php']['primary']['name'] = '<a href="?route=rewards.php">' . $LANG['rewards'] . '</a>';
  $nav['rewards.php']['primary']['.class'] = 'rewards';
  if( $GLOBALS['me']->is_admin ) {
    $nav['rewards.php']['add'] = '<a href="?route=rewards.php&amp;action=add">' . $LANG['rewards_add'] . '</a>';
    $nav['rewards.php']['list'] = '<a href="?route=rewards.php&amp;action=list">' . $LANG['rewards_view'] . '</a>';
  }
    $nav['rewards.php']['requests'] = '<a href="?route=rewards.php&amp;action=requests">' . $LANG['rewards_claimr'] . '</a>';
}

if( $GLOBALS['me']->is_admin ) {
  $nav['themes.php']['primary']['name'] = '<a href="?route=themes.php">' . $LANG['themes'] . '</a>';
  $nav['themes.php']['primary']['.class'] = 'themes';
  $nav['themes.php']['upload'] = '<a href="?route=themes.php&amp;action=upload">' . $LANG['themes_upload'] . '</a>';
  $nav['themes.php']['editor'] = '<a href="?route=themes.php&amp;action=editor&amp;id=' . \query\main::get_option( 'theme' ) . '">' . $LANG['themes_editor'] . '</a>';
  $nav['themes.php']['list'] = '<a href="?route=themes.php&amp;action=list">' . $LANG['themes_view'] . '</a>';
}

if( $GLOBALS['me']->is_admin && template::have_widgets() ) {
  $nav['widgets.php']['primary']['name'] = '<a href="?route=widgets.php">' . $LANG['widgets'] . '</a>';
  $nav['widgets.php']['primary']['.class'] = 'widgets';
}

if( ab_to( array( 'raports' => 'view' ) ) ) {
  $nav['clicks.php']['primary']['name'] = '<a href="?route=clicks.php">' . $LANG['ratings'] . '</a>';
  $nav['clicks.php']['primary']['.class'] = 'raports';
  $nav['clicks.php']['list'] = '<a href="?route=clicks.php&amp;action=list">' . $LANG['ratings_clicks'] . '</a>';
}

if( $GLOBALS['me']->is_admin ) {

  $nav['plugins.php']['primary']['name'] = '<a href="?route=plugins.php">' . $LANG['plugins'] . '</a>';
  $nav['plugins.php']['primary']['.class'] = 'plugins';
  $nav['plugins.php']['install'] = '<a href="?route=plugins.php&amp;action=install">' . $LANG['plugins_install'] . '</a>';
  $nav['plugins.php']['list'] = '<a href="?route=plugins.php&amp;action=list">' . $LANG['plugins_view'] . '</a>';

  foreach( \query\main::user_plugins( false, 'menu' ) as $plugin ) {
    $plugin_dir = dirname( $plugin->main_file );
    $nav[$plugin_dir]['primary']['name'] = '<a href="?plugin=' . $plugin->main_file . '">' . $plugin->name . '</a>';
    $nav[$plugin_dir]['primary']['.class'] = 'plugin' . $plugin->menu_icon;
    if( isset( $plugin->vars['menu_add'] ) )
    foreach( $plugin->vars['menu_add'] as $subnavp ) {
      $nav[$plugin_dir][] = '<a href="?plugin=' . $subnavp['url'] . '">' . $subnavp['title'] . '</a>';
    }
  }

  $nav['settings.php']['primary']['name'] = '<a href="?route=settings.php">' . $LANG['settings'] . '</a>';
  $nav['settings.php']['primary']['.class'] = 'settings';
  $nav['settings.php']['primary']['others'] = 'banned.php';
  $nav['settings.php']['general'] = '<a href="?route=settings.php&amp;action=general">' . $LANG['settings_general'] . '</a>';
  $nav['settings.php']['meta'] = '<a href="?route=settings.php&amp;action=meta">' . $LANG['settings_metatags'] . '</a>';
  $nav['settings.php']['seolinks'] = '<a href="?route=settings.php&amp;action=seolinks">' . $LANG['settings_seolinks'] . '</a>';
  $nav['settings.php']['prices'] = '<a href="?route=settings.php&amp;action=prices">' . $LANG['settings_prices'] . '</a>';
  $nav['settings.php']['default'] = '<a href="?route=settings.php&amp;action=default">' . $LANG['settings_default'] . '</a>';
  $nav['settings.php']['api'] = '<a href="?route=settings.php&amp;action=api">' . $LANG['settings_api'] . '</a>';
  $nav['settings.php']['feed'] = '<a href="?route=settings.php&amp;action=feed">' . $LANG['settings_feed'] . '</a>';
  $nav['settings.php']['cron'] = '<a href="?route=settings.php&amp;action=cron">' . $LANG['settings_cron'] . '</a>';
  $nav['settings.php']['banned.php'] = '<a href="?route=banned.php&amp;action=list">' . $LANG['settings_banned'] . '</a>';
  $nav['settings.php']['socialacc'] = '<a href="?route=settings.php&amp;action=socialacc">' . $LANG['settings_socialnet'] . '</a>';

}

?>

<div class="main-nav">

<ul class="nav">

<?php

  foreach( $nav as $k => $v ) {
    $sn = count( $v ) - 1;
    $show_subnav = $show_other = false;
    $pclass = array();
    $pclass[] = $v['primary']['.class'];
    if( $sn ) {
      $pclass[] = 'drop-down';
    }
    if( !empty( $_GET['plugin'] ) && dirname( $_GET['plugin'] ) == $k ) {
      $pclass[] = 'secselected';
      $show_subnav = true;
    } else if( !empty( $_GET['route'] ) ) {
      if( $_GET['route'] == $k ) {
      $pclass[] = 'secselected';
      $show_subnav = true;
      } else if( isset( $v['primary']['others'] ) && strpos( $v['primary']['others'], $_GET['route'] ) !== false ) {
      $pclass[] = 'secselected';
      $show_other = true;
      }
    }

    echo '<li' . ( !empty( $pclass ) ? ' class="' . implode( ' ', $pclass ) . '"' : '' ) . '>' . $v['primary']['name'];
    if( $sn ) {
      echo '<ul class="subnav"' . ( $show_subnav || $show_other ? ' style="display: block;"' : '' ) . '>';
      unset( $v['primary'] );
      foreach( $v as $k1 => $v1 ) {
        echo '<li' . ( isset( $_GET['route'] ) && ( isset( $_GET['action'] ) && $show_subnav && $_GET['action'] == $k1 || $show_other && $_GET['route'] == $k1 ) ? ' class="secselected"' : '' ) . '>' . $v1 . '</li>';
      }
      echo '</ul>';
    }
    echo '</li>';
  }

?>

</ul>

</div>