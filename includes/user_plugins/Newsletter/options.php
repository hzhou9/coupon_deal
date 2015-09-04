<?php

if( !$GLOBALS['me']->is_admin ) die;

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'slider_csrf' ) ) {
        
        if( isset( $_POST['sendy_brand_id'] ) && isset( $_POST['sendy_list_id'] ) && isset( $_POST['sendy_reply_to'] ) && isset( $_POST['sendy_from_email'] ) && isset( $_POST['sendy_from_name'] ) && isset( $_POST['sendy_api_key'] ) && isset( $_POST['sendy_url'] )  && isset( $_POST['sendy_template_root'] ) ){
            //update account
            $sendy_url = (substr($_POST['sendy_url'], -1) == '/')?$_POST['sendy_url']:$_POST['sendy_url'].'/';
            $sendy_template_root = (substr($_POST['sendy_template_root'], -1) == '/')?$_POST['sendy_template_root']:$_POST['sendy_template_root'].'/';
            $sendy_query_string = isset( $_POST['sendy_query_string'] )?$_POST['sendy_query_string']:'';
            
            if( actions::set_option( array( 'sendy_query_string' => $sendy_query_string, 'sendy_brand_id' => $_POST['sendy_brand_id'], 'sendy_list_id' => $_POST['sendy_list_id'], 'sendy_reply_to' => $_POST['sendy_reply_to'], 'sendy_from_email' => $_POST['sendy_from_email'], 'sendy_from_name' => $_POST['sendy_from_name'], 'sendy_api_key' => $_POST['sendy_api_key'], 'sendy_url' => $sendy_url, 'sendy_template_root' => $sendy_template_root ) ) )
                echo '<div class="a-success">Saved!</div>';
            else
                echo '<div class="a-error">Error!</div>';
        }else{
            echo '<div class="a-error">Param Error ('.isset( $_POST['sendy_brand_id'] ).','.isset( $_POST['sendy_list_id'] ).','.isset( $_POST['sendy_reply_to'] ).','.isset( $_POST['sendy_from_email'] ).','.isset( $_POST['sendy_from_name'] ).','.isset( $_POST['sendy_api_key'] ).','.isset( $_POST['sendy_url']).')</div>';
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo '<div class="a-error">Signature Error</div';
    }
    $csrf = $_SESSION['slider_csrf'] = \site\utils::str_random(10);
    
switch( $_GET['action'] ) {

default:
echo '
<div class="title">

<h2>Sendy settings</h2>

<span>Modify Sendy settings</span>

</div>
';

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>api_key: <span class="info"><span>sendy_api_key</span></span></span><div><input type="text" name="sendy_api_key" value="' . htmlspecialchars( \query\main::get_option( 'sendy_api_key' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>sendy_url: <span class="info"><span>sendy_url</span></span></span><div><input type="text" name="sendy_url" value="' . htmlspecialchars( \query\main::get_option( 'sendy_url' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>list_id: <span class="info"><span>sendy_list_id</span></span></span><div><input type="text" name="sendy_list_id" value="' . htmlspecialchars( \query\main::get_option( 'sendy_list_id' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>brand_id: <span class="info"><span>sendy_brand_id</span></span></span><div><input type="text" name="sendy_brand_id" value="' . htmlspecialchars( \query\main::get_option( 'sendy_brand_id' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>from_name: <span class="info"><span>sendy_from_name</span></span></span><div><input type="text" name="sendy_from_name" value="' . htmlspecialchars( \query\main::get_option( 'sendy_from_name' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>from_email: <span class="info"><span>sendy_from_email</span></span></span><div><input type="text" name="sendy_from_email" value="' . htmlspecialchars( \query\main::get_option( 'sendy_from_email' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>reply_to: <span class="info"><span>sendy_reply_to</span></span></span><div><input type="text" name="sendy_reply_to" value="' . htmlspecialchars( \query\main::get_option( 'sendy_reply_to' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>query_string: <span class="info"><span>sendy_query_string</span></span></span><div><input type="text" name="sendy_query_string" value="' . htmlspecialchars( \query\main::get_option( 'sendy_query_string' ) ) . '" style="background: #F8E0E0;" /></div></div>
<div class="row"><span>template_root: <span class="info"><span>sendy_template_root</span></span></span><div><input type="text" name="sendy_template_root" value="' . htmlspecialchars( \query\main::get_option( 'sendy_template_root' ) ) . '" style="background: #F8E0E0;" /></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>

</form>
';

break;

}