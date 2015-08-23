<?php

echo '<div class="page-toolbar">
' .  sprintf( $LANG['first_msg_v'], VERSION, \query\main::get_option( 'theme' ) ) . ' <span class="right-text">' . $LANG['server_time'] . ': ' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i') ) . '</span>';
echo '</div>';

echo '<div class="form-table">

<div class="el-two">';

$alerts = array();
if( ( $a_suggs = admin_query::suggestions( array( 'show' => 'notread' ) ) ) > 0 && ab_to( array( 'suggestions' => 'view' ) ) ) $alerts[] = '<a href="?route=suggestions.php&amp;action=list&amp;view=notread">' . sprintf( $LANG['alerts_suggestions'], $a_suggs ) . '</a>';
if( ( $a_rews = \query\main::reviews( array( 'show' => 'notvalid' ) ) ) > 0 && ab_to( array( 'reviews' => 'view' ) ) )$alerts[] = '<a href="?route=reviews.php&amp;action=list&amp;view=notvalid">' . sprintf( $LANG['alerts_reviews'], $a_rews ) . '</a>';
if( ( $a_clmreqs = \query\main::rewards_reqs( array( 'show' => 'notvalid' ) ) ) > 0 && ab_to( array( 'claim_reqs' => 'view' ) ) )$alerts[] = '<a href="?route=rewards.php&amp;action=requests&amp;view=notvalid">' . sprintf( $LANG['alerts_rewardreq'], $a_clmreqs ) . '</a>';
if( ( $a_coupons = \query\main::coupons( array( 'show' => 'notvisible' ) ) ) > 0 && ab_to( array( 'coupons' => 'view' ) ) )$alerts[] = '<a href="?route=coupons.php&amp;action=list&amp;view=notvisible">' . sprintf( $LANG['alerts_unpubcoupons'], $a_coupons ) . '</a>';
if( ( $a_products = \query\main::products( array( 'show' => 'notvisible' ) ) ) > 0 && ab_to( array( 'products' => 'view' ) ) )$alerts[] = '<a href="?route=products.php&amp;action=list&amp;view=notvisible">' . sprintf( $LANG['alerts_unpubproducts'], $a_products ) . '</a>';
if( ( $a_stores = \query\main::stores( array( 'show' => 'notvisible' ) ) ) > 0 && ab_to( array( 'claim_reqs' => 'view' ) ) )$alerts[] = '<a href="?route=stores.php&amp;action=list&amp;view=notvisible">' . sprintf( $LANG['alerts_unpubstores'], $a_stores ) . '</a>';
if( ( $a_payments = \query\payments::invoices( array( 'show' => 'undeliveredpayments' ) ) ) > 0 && ab_to( array( 'payments' => 'view' ) ) )$alerts[] = '<a href="?route=payments.php&amp;action=list&amp;view=undeliveredpayments">' . sprintf( $LANG['alerts_undelipay'], $a_payments ) . '</a>';

if( !empty( $alerts ) ) {

echo '<section class="el-row">

<h2>' . $LANG['news_alerts'] . ' <a href="#" class="updown" data-set="alerts">' . ( isset( $_SESSION['ses_set']['alerts'] ) && ( $show_alerts = $_SESSION['ses_set']['alerts'] ) ? 'S' : 'R' ) . '</a></h2>

<div class="el-row-body"' . ( !empty( $show_alerts ) ? ' style="display: none;"' : '' ) . '>

<ul class="elements-list">';

foreach( $alerts as $v ) {
  echo '<li>' . $v . '</li>';
}

echo '</ul>

</div>

</section>';

}

if( $GLOBALS['me']->is_admin ) {

echo '<section class="el-row">

<h2>' . $LANG['payments'] . ' <a href="#" class="updown" data-set="payments">' . ( isset( $_SESSION['ses_set']['payments'] ) && ( $show_payments = $_SESSION['ses_set']['payments'] ) ? 'S' : 'R' ) . '</a></h2>

<div class="el-row-body"' . ( !empty( $show_payments ) ? ' style="display: none;"' : '' ) . '>

<ul class="announce-box abdash">
<li>' . $LANG['today'] . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( 'today' ) ) )['sum'] ) ) ) . '</b></li>
<li>' . $LANG['yesterday'] . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( '-2 days 00:00:00' ) . ',' . strtotime( 'today' ) ) )['sum'] ) ) ) . '</b></li>
<li>' . $LANG['this_week'] . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( 'last week 00:00:00' ) ) )['sum'] ) ) ) . '</b></li>
<li>' . $LANG['this_month'] . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( 'first day of this month 00:00:00' ) ) )['sum'] ) ) ) . '</b></li>
</ul>

</section>';

}

echo '<ul class="elements-list">';

if( ab_to( array( 'stores' => 'view' ) ) ) {

  echo '<li>
  <div class="info-div"><b>' . \query\main::stores() . '</b> ' . strtolower( $LANG['stores'] ) . '</div>
  <div class="options">
  <a href="?route=stores.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=stores.php&amp;action=add">' . $LANG['add'] . '</a>
  </div>
  </li>';

}

if( ab_to( array( 'coupons' => 'view' ) ) ) {


  echo '<li>
  <div class="info-div"><b>' . \query\main::coupons() . '</b> ' . strtolower( $LANG['coupons'] ) . '</div>
  <div class="options">
  <a href="?route=coupons.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=coupons.php&amp;action=add">' . $LANG['add'] . '</a>
  </div>
  </li>';

}

if( ab_to( array( 'products' => 'view' ) ) ) {


  echo '<li>
  <div class="info-div"><b>' . \query\main::products() . '</b> ' . strtolower( $LANG['products'] ) . '</div>
  <div class="options">
  <a href="?route=products.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=products.php&amp;action=add">' . $LANG['add'] . '</a>
  </div>
  </li>';

}

if( ab_to( array( 'categories' => 'view' ) ) ) {

  echo '<li>
  <div class="info-div"><b>' . \query\main::categories() . '</b> ' . strtolower( $LANG['categories'] ) . '</div>
  <div class="options">
  <a href="?route=categories.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=categories.php&amp;action=add">' . $LANG['add'] . '</a>
  </div>
  </li>';

}

if( ab_to( array( 'pages' => 'view' ) ) ) {

  echo '<li>
  <div class="info-div"><b>' . \query\main::pages() . '</b> ' . strtolower( $LANG['pages'] ) . '</div>
  <div class="options">
  <a href="?route=pages.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=pages.php&amp;action=add">' . $LANG['add'] . '</a>
  </div>
  </li>';

}

if( ab_to( array( 'users' => 'view' ) ) ) {

  echo '<li>
  <div class="info-div"><b>' . \query\main::users() . '</b> ' . strtolower( $LANG['users'] ) . '</div>
  <div class="options">
  <a href="?route=users.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=users.php&amp;action=add">' . $LANG['add'] . '</a>
  </div>
  </li>';

}

if( $GLOBALS['me']->is_admin ) {

  echo '<li>
  <div class="info-div"><b>' . admin_query::user_sessions() . '</b> ' . strtolower( $LANG['users_sessions'] ) . '</div>
  <div class="options">
  <a href="?route=users.php&amp;action=sessions">' . $LANG['view'] . '</a>
  </div>
  </li>';

}

if( $GLOBALS['me']->is_admin ) {

  echo '<li>
  <div class="info-div"><b>' . admin_query::subscribers() . '</b> ' . strtolower( $LANG['users_subscribers'] ) . '</div>
  <div class="options">
  <a href="?route=users.php&amp;action=subscribers">' . $LANG['view'] . '</a>
  </div>
  </li>';

}

if( ab_to( array( 'reviews' => 'view' ) ) ) {

  echo '<li>
  <div class="info-div"><b>' . \query\main::reviews() . '</b> ' . strtolower( $LANG['reviews'] ) . '</div>
  <div class="options">
  <a href="?route=reviews.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=coupons.php&amp;action=add">' . $LANG['add'] . '</a>
  </div>
  </li>';

}

if( ab_to( array( 'suggestions' => 'view' ) ) ) {

  echo '<li>
  <div class="info-div"><b>' . admin_query::suggestions() . '</b> ' . strtolower( $LANG['suggestions'] ) . '</div>
  <div class="options">
  <a href="?route=suggestions.php&amp;action=list">' . $LANG['view'] . '</a>
  </div>
  </li>';

}

if( $GLOBALS['me']->is_admin ) {

  echo '<li>
  <div class="info-div"><b>' . admin_query::plugins() . '</b> ' . strtolower( $LANG['plugins'] ) . '</div>
  <div class="options">
  <a href="?route=plugins.php&amp;action=list">' . $LANG['view'] . '</a>
  <a href="?route=plugins.php&amp;action=install">' . $LANG['plugins_install'] . '</a>
  </div>
  </li>';

}

echo '</ul>

</div>

<div class="el-two">';

if( ab_to( array( 'chat' => 'view' ) ) ) {

echo '<section class="el-row">

<h2>' . $LANG['chat_title'] . ' <a href="#" class="updown" data-set="chat">' . ( isset( $_SESSION['ses_set']['chat'] ) && ( $show_chat = $_SESSION['ses_set']['chat'] ) ? 'S' : 'R' ) . '</a></h2>

<div class="el-row-body"' . ( !empty( $show_chat ) ? ' style="display: none;"' : '' ) . '>

<div id="post-chat">';

$chat_csrf = \site\utils::str_random(10);

if( ab_to( array( 'chat' => 'add' ) ) ) {

  echo '<form action="#" method="POST">
    <input type="text" name="text" value="" placeholder="' . $LANG['chat_write_input'] . '" />
    <button class="btn">' . $LANG['chat_write_button'] . '</button>
    <a href="#" class="btn useggfont" title="Reload">Z</a>
    <input type="hidden" name="chat_csrf" value="' . $chat_csrf . '" />
  </form>';

}

echo '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

if( isset( $_POST['chat_csrf'] ) && check_csrf( $_POST['chat_csrf'], 'chat_csrf' ) && isset( $_POST['text'] ) )
  actions::post_chat_message( $_POST['text'] );

}

$_SESSION['chat_csrf'] = $chat_csrf;

echo '<ul class="elements-list" id="chat-msgs-list">';

if( $chatmsgs = admin_query::chat_messages() > 0 )

foreach( admin_query::while_chat_messages( array( 'max' => 5, 'orderby' => 'date DESC' ) ) as $item ) {
  echo '<li>
  <div style="display: table;">
  <img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />
  <div class="info-div"><h2>' . $item->user_name . '
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>
  <div class="info-bar">' . \site\utils::bbcodes( $item->text ) . '</div>
  </div></div>
  </li>';
}

else {
  echo '<li>' . $LANG['no_chat_yet'] . '</li>';
}

echo '</ul>';

if( $chatmsgs > 0 ) {

echo '<div class="links">
  <a href="?route=chat.php">' .$LANG['chat_list'] . '</a>
</div>';

}

echo '</div>

</section>';

}

if( ab_to( array( 'raports' => 'view' ) ) ) {

echo '<section class="el-row">

<h2>' . $LANG['clicksr_graport'] . ' <a href="#" class="updown" data-set="graprap">' . ( isset( $_SESSION['ses_set']['graprap'] ) && ( $show_graprap = $_SESSION['ses_set']['graprap'] ) ? 'S' : 'R' ) . '</a>

</h2>

<div class="el-row-body" id="raport-date"' . ( !empty( $show_graprap ) ? ' style="display: none;"' : '' ) . '>

<select>';
foreach( array( 'hours' => $LANG['hours'], 'days' => $LANG['days'], 'weeks' => $LANG['weeks'], 'months' => $LANG['months'] ) as $k => $v ) {
  echo '<option value="' . $k . '"' .( isset( $_SESSION['ses_set']['lgcl'] ) && $_SESSION['ses_set']['lgcl'] == $k ? ' selected' : '' ) . '>' . $v . '</option>';
}
echo'</select>';

?>

<script type="text/javascript">

google.load("visualization", "1.1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {

$('#raport-date select').on('change', function() {

var view = $(this).val();

var jsonData = $.ajax({url: "?ajax=click_raport_json.php&view=" + view + "&limit=5", dataType:"json", async: false}).responseText;

var data = new google.visualization.DataTable(jsonData);

var options = {

  legend: { position: 'none' },

  colors: ['green', '#003366', '#990099'],
  backgroundColor: {stroke: 'none', fill: 'none'},
  chartArea:{width: '100%', height: 'auto', top: 20}

};

var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_material'));

chart.draw(data, options);

}).change();

}

</script>

<div id="columnchart_material" style="margin:0 auto;"></div>

<div class="links">
  <a href="?route=clicks.php"><?php echo $LANG['news_list']; ?></a>
</div>

</div>

</section>

<?php

}

echo '<section class="el-row">

<h2>' . $LANG['news_title'] . ' <a href="#" class="updown" data-set="news">' . ( isset( $_SESSION['ses_set']['news'] ) && ( $show_news = $_SESSION['ses_set']['news'] ) ? 'S' : 'R' ) . '</a></h2>

<div class="el-row-body"' . ( !empty( $show_news ) ? ' style="display: none;"' : '' ) . '>

<ul class="elements-list">';

if( $news = admin_query::news() > 0 )

foreach( admin_query::while_news( array( 'max' => 7, 'orderby' => 'date DESC' ) ) as $item ) {
  echo '<li><a href="' . $item->url . '" target="_blank">' . $item->title . '</a></li>';
}

else {
  echo '<li>' . $LANG['no_news_yet'] . '</li>';
}

echo '</ul>';

if( $news > 0 ) {

echo '<div class="links">
  <a href="?route=news.php">' .$LANG['news_list'] . '</a>
</div>';

}

echo '</div>

</section>

</div>

</div>

<div style="clear: both"></div>';

echo '<div class="idashbtm">' . sprintf( $LANG['operatesfrom'], date('m/d/Y', \query\main::get_option( 'siteinstalled' )) ) . ' / <a href="//couponscms.com" target="_blank" style="color: #000;">CouponsCMS.com</a></div>';