<?php

if( !$GLOBALS['me']->is_admin ) die;

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'slider_csrf' ) ) {
        
        if( isset( $_POST['popshop_account'] ) && isset( $_POST['popshop_catalog'] )){
            //update account
            if( actions::set_option( array( 'popshop_account' => $_POST['popshop_account'], 'popshop_catalog' => $_POST['popshop_catalog'], 'cj_ipp' => $_POST['cj_ipp'] ) ) )
                echo '<div class="a-success">Saved!</div>';
            else
                echo '<div class="a-error">Error!</div>';
        }
    }
    $csrf = $_SESSION['slider_csrf'] = \site\utils::str_random(10);
    
switch( $_GET['action'] ) {

default:
echo '<script>
function dosync(){
    var infotext = "Please do NOT close! Updating";
    var intervalR = setInterval(function(){
        infotext+=".";$("#popshop_sync_info").html(infotext);
    }, 500);
    $("#popshop_sync_ret").on("load", (function () {
        clearInterval(intervalR);
        $("#popshop_sync_ret").show();
    }));
    var url = location.href.replace("options.php","sync.php")+"&csrf='.$csrf.'";
    $("#popshop_sync_ret").attr("src", url);
}
</script>
<iframe id="popshop_sync_ret" style="position:fixed;left:0px;top:0px;width:100%;height:100%;z-index:99;display:none;">
</iframe>
<div class="title">

<h2>Popshop settings</h2>

<span>Modify Popshop settings</span>

</div>
';

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>Account: <span class="info"><span>Popshop account</span></span></span><div><input type="text" name="popshop_account" value="' . htmlspecialchars( \query\main::get_option( 'popshop_account' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>Catalog: <span class="info"><span>Popshop catalog</span></span></span><div><input type="text" name="popshop_catalog" value="' . htmlspecialchars( \query\main::get_option( 'popshop_catalog' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>Num Dispaly: <span class="info"><span>Num of items per page</span></span></span><div><input type="text" name="cj_ipp" value="' . htmlspecialchars( \query\main::get_option( 'cj_ipp' ) ) . '" style="background: #F8E0E0;" required /></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>

</form>
<br><br><br>
<div class="title">
<h2>Popshop sync</h2>
<span>Sync with Popshop</span>
</div>
<div class="form-table">
<div class="row"><span>Last Update: <span class="info"><span>Last Update Time</span></span></span><div>' . date("Y-m-d H:i:s", \query\main::get_option( 'popshop_lastupdate' ) ) . '&nbsp;<button class="btn" onclick="dosync()">Sync</button></div><div id="popshop_sync_info"></div></div>
</div>
    
';

break;

}