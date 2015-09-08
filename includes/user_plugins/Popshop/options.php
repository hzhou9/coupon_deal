<?php

if( !$GLOBALS['me']->is_admin ) die;

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'slider_csrf' ) ) {
        
        if( isset( $_POST['popshop_account'] ) && isset( $_POST['popshop_catalog'] )){
            //update account
            if( actions::set_option( array( 'popshop_account' => $_POST['popshop_account'], 'popshop_catalog' => $_POST['popshop_catalog'], 'cj_ipp' => $_POST['cj_ipp'] ) ) )
                echo '<div class="a-success">Saved!</div>';
            else
                echo '<div class="a-error">Error!</div>';
        }else if( isset( $_POST['catid'] ) && isset( $_POST['catid_old'] )){
            foreach($_POST['catid'] as $id=>$catid){
                $catid = intval($catid);
                if($catid != 0){
                    $catid_old = intval($_POST['catid_old'][$id]);
                    \plugin\Popshop\inc\actions::setMerchantTypeMapping( intval($id), $catid, $catid_old );
                }
            }
            echo '<div class="a-success">Saved!</div>';
        }
    }
    $csrf = $_SESSION['slider_csrf'] = \site\utils::str_random(10);
    
switch( $_GET['action'] ) {

default:
echo '<script>
function dosync(full){
    var infotext = "Please do NOT close! Updating";
    var intervalR = setInterval(function(){
        infotext+=".";$("#popshop_sync_info").html(infotext);
    }, 500);
    $("#popshop_sync_ret").on("load", (function () {
        clearInterval(intervalR);
        $("#popshop_sync_ret").show();
    }));
    var url = location.href.replace("options.php","sync.php")+"&csrf='.$csrf.'";
    if(full == 1){
        url += "&auto=1&visible=1";
    }else if(full == 2){
        url += "&nosync=1&auto=1&visible=1";
    }
    $("#popshop_sync_ret").attr("src", url);
}
function setCatID(id){
    var targetdiv = $("[name=\"div["+id+"]\"]");
    if(targetdiv.find("select").length > 0){
        return;
    }
    var template = $("[name=template_category]").clone();
    var template_ok = $("[name=template_category_OK]").clone();
    var template_cancel = $("[name=template_category_Cancel]").clone();
    var catid = $("[name=\"catid["+id+"]\"]");
    template.val(catid.val());
    template_ok.click(function(){targetdiv.find("span").html(template.find("option:selected").text());catid.val(template.val());template_cancel.click();});
    template_cancel.click(function(){template.remove();template_ok.unbind().remove();template_cancel.unbind().remove();});
    targetdiv.append(template.show());
    targetdiv.append(template_ok.show());
    targetdiv.append(template_cancel.show());
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
<div class="row"><span>Last Update: <span class="info"><span>Last Update Time</span></span></span><div>' . date("Y-m-d H:i:s", \query\main::get_option( 'popshop_lastupdate' ) ) . '&nbsp;<button class="btn" onclick="dosync()">Sync</button>&nbsp;<button class="btn" onclick="dosync(2)">AutoImport</button>&nbsp;<button class="btn" onclick="dosync(1)">Sync+AutoImport</button></div><div id="popshop_sync_info"></div></div>
</div>
<br><br><br>
<div class="title">
<h2>Category Mapping</h2>
<span>Category Mapping for Data Automation</span>
</div>';

$mappingdata = \plugin\Popshop\inc\actions::listMerchantTypeMapping();
$categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
        echo '<select name="template_category" style="display:none;">';
        foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
        echo '</select>';
        
echo '
<input type="button" value="OK" name="template_category_OK" style="display:none;"><input type="button" value="Cancel" name="template_category_Cancel" style="display:none;">
<form action="#" method="POST">
<div class="form-table">';
        
        foreach($mappingdata as $km=>$vm){
            $catname = 'N/A';
            $catid = 0;
            if($vm['catid'] && $vm['catid'] > 0){
                foreach( $categories_while as $cat ){
                    if($cat->ID == $vm['catid']){
                        $catname = $cat->name;
                        $catid = $cat->ID;
                        break;
                    }
                }
            }
            echo '<div class="row"><span>'.$vm['name'].': </span><div name="div['.$km.']"><span style="text-decoration: underline;" onclick="setCatID('.$km.');">'.$catname.'</span><input type="hidden" name="catid[' . $km . ']" value="' . $catid . '" /><input type="hidden" name="catid_old[' . $km . ']" value="' . $catid . '" /></div></div>';
        }

echo '</div><input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>
    
</form>';
    
break;

}