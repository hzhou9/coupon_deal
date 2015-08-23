<?php

if( !ab_to( array( 'raports' => 'view' ) ) ) die;

$view = isset( $_GET['view'] ) ? $_GET['view'] : 'days';

echo '<div class="title">

<h2>' . $LANG['clicksr_title'] . '</h2>';

if( !empty( $LANG['clicksr_subtitle'] ) ) {
  echo '<span>' . $LANG['clicksr_subtitle'] . '</span>';
}

echo '</div>';

echo '<div class="page-toolbar" id="raport-date">

' . $LANG['clicksr_graport'] . '

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="clicks.php" />
<select name="view">
<option value="hours"' . ( $view == 'hours' ? ' selected' : '' ) . '>' . $LANG['hours'] . '</option>
<option value="days"' . ( $view == 'days' ? ' selected' : '' ) . '>' . $LANG['days'] . '</option>
<option value="weeks"' . ( $view == 'weeks' ? ' selected' : '' ) . '>' . $LANG['weeks'] . '</option>
<option value="months"' . ( $view == 'months' ? ' selected' : '' ) . '>' . $LANG['months'] . '</option>
</select>
<button class="btn">' . $LANG['view'] . '</button>
</form>

</div>';

?>

<script type="text/javascript">

google.load("visualization", "1.1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {

$('#raport-date select').on('change', function() {

var view = $(this).val();

var jsonData = $.ajax({url: "?ajax=click_raport_json.php&view=" + view, dataType:"json", async: false}).responseText;

var data = new google.visualization.DataTable(jsonData);

var options = {

  legend: { position: 'none' },

  colors: ['green', '#003366', '#990099'],
  backgroundColor: {stroke: 'none', fill: 'none'},
  chartArea:{width: '90%', height: 'auto', top: 20}

};

var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_material'));

chart.draw(data, options);

}).change();

}

</script>

<div id="columnchart_material" style="width: 100%; margin:0 auto;"></div>

<?php

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="clicks.php" />

' . $LANG['order_by'] . ':
<select name="orderby">';
foreach( array( 'date' => $LANG['order_date'], 'date desc' => $LANG['order_date_desc'] ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

<button class="btn">' . $LANG['view'] . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="clicks.php" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . htmlspecialchars( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? htmlspecialchars( $_GET['search'] ) : '') . '" placeholder="' . $LANG['clicksr_search_input'] . '" />
<button class="btn">' . $LANG['search'] . '</button>
</form>

</div>';

$p = admin_query::have_clicks( $options = array( 'per_page' => 10, 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'coupon' => (isset( $_GET['coupon'] ) ? $_GET['coupon'] : ''), 'product' => (isset( $_GET['product'] ) ? $_GET['product'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( $LANG['result'], $p['results'] ) : sprintf( $LANG['results'], $p['results'] ) );
if( !empty( $_GET['coupon'] ) || !empty( $_GET['product'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=clicks.php&amp;action=list">' . $LANG['reset_view'] . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=stores.php&amp;action=list" method="POST">

<ul class="elements-list">';

$ab_edtu  = ab_to( array( 'users' => 'edit' ) );
$ab_edts  = ab_to( array( 'stores' => 'edit' ) );

foreach( admin_query::while_clicks( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>

  <div style="display: table;">

  <img src="' . \query\main::store_avatar( $item->store_img ) . '" alt="" style="width: 80px;" />

  <div class="info-div">

  <h2>' . ( !empty( $item->country ) ? '<img src="../' . LBDIR . '/iptocountry/flags/' . strtolower( $item->country ) . '.png" alt="' . $item->country_full . '" title="' . $item->country_full . '" /> ' : '' ) . '<span style="color: ' . ( !empty( $item->user ) ? '#990099' : '#003366' ) . ';" title="' . $item->browser . '">' . $item->IP . ( !empty( $item->user ) && ( $user = \query\main::user_infos( $item->user ) ) ? ' / ' . $user->name : '' ) . '</span>
  <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>
  <a href="?route=clicks.php&amp;store=' . $item->storeID . '">' . $item->store_name . '</a> ';
  if( !empty( $item->couponID ) && empty( $item->productID ) ) {
    echo '(' . $LANG['clicksr_couponid'] . ': <a href="?route=clicks.php&amp;coupon=' . $item->couponID . '">' . $item->couponID . '</a>)';
  } else if( empty( $item->couponID ) && !empty( $item->productID ) ) {
    echo '(' . $LANG['clicksr_productid'] . ': <a href="?route=clicks.php&amp;product=' . $item->productID . '">' . $item->productID . '</a>)';
  }
  echo '</div></div>

  <div class="options">';
  echo ( !empty( $item->user ) && $ab_edtu ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $item->user . '">' . $LANG['clicksr_edit_user'] . '</a>' : '' );
  if( $ab_edts ) echo '<a href="?route=stores.php&amp;action=edit&amp;id=' . $item->storeID . '">' . $LANG['clicksr_edit_store'] . '</a>';
  if( $GLOBALS['me']->is_admin ) echo '<a href="?route=banned.php&amp;action=add&amp;ip=' . $item->IP . '">' . $LANG['bann_ip'] . '</a>';
  echo '</div>

  </li>';

}

echo '</ul>

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
  echo '<div class="pagination">';

  if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . $LANG['prev_page'] . '</a>';
  if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . $LANG['next_page'] . '</a>';

  if( $p['pages'] > 1 ) {
  echo '<div class="pag_goto">' . sprintf( $LANG['pageofpages'], $page = $p['page'], $pages = $p['pages'] ) . '
  <form action="#" method="GET">';
  foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . htmlspecialchars( $gk ) . '" value="' . htmlspecialchars( $gv ) . '" />';
  echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
  <button class="btn">' . $LANG['go'] . '</button>
  </form>
  </div>';
  }

  echo '</div>';
}

} else {

  echo '<div class="a-alert">' . $LANG['no_clicks_yet'] . '</div>';

}