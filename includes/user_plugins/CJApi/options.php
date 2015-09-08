<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

default:

        echo '<script>
        function setCatID(id){
            //id = decodeURIComponent(id);
            var targetdiv = $("[name=\"div["+id+"]\"]");
            if(targetdiv.find("select").length > 0){
                return;
            }
            var template = $("[name=template_category]").clone();
            var template_ok = $("[name=template_category_OK]").clone();
            var template_cancel = $("[name=template_category_Cancel]").clone();
            var catid = $("[name=\"catid["+id+"]\"]");
            template.val(catid.val());
            template_ok.click(function(){
                targetdiv.find("span").html(template.find("option:selected").text());
                catid.val(template.val());
                template_cancel.click();
            });
            template_cancel.click(function(){template.remove();template_ok.unbind().remove();template_cancel.unbind().remove();});
            targetdiv.append(template.show());
            targetdiv.append(template_ok.show());
            targetdiv.append(template_cancel.show());
        }
        </script>
<div class="title">

<h2>CJ.com Options</h2>

<div style="float:right; margin: 0 2px 0 0;">

<div class="options">
<a href="#" class="btn">Show</a>
<ul>
<li><a href="?plugin=CJApi/cj.php&amp;action=advertisers">Advertisers</a></li>
<li><a href="?plugin=CJApi/cj.php&amp;action=links">Links</a></li>
<li><a href="?plugin=CJApi/main.php&amp;action=sales">Sales</a></li>
</ul>
</div>

</div>

<span>Modify CJ.com API settings</span>

</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'slider_csrf' ) ) {

    if( isset( $_POST['key'] ) && isset( $_POST['site-id'] ) && isset( $_POST['exp'] ) && isset( $_POST['ipp'] ) ){
  if( actions::set_option( array( 'cj_key' => $_POST['key'], 'cj_site-id' => $_POST['site-id'], 'cj_exp' => $_POST['exp'], 'cj_ipp' => $_POST['ipp'] ) ) )
  echo '<div class="a-success">Saved!</div>';
  else
  echo '<div class="a-error">Error!</div>';
    }else if( isset( $_POST['catid'] ) && isset( $_POST['catid_old'] )){
        //var_dump($_POST['catid']);
        //var_dump($_POST['catid_old']);
        
        foreach($_POST['catid'] as $id=>$catid){
            $catid = intval($catid);
            if($catid != 0){
                $catid_old = intval($_POST['catid_old'][$id]);
                \plugin\CJApi\inc\actions::setCategoryMapping( $id, $catid, $catid_old );
            }
        }
        echo '<div class="a-success">Saved!</div>';
    }

}

$csrf = $_SESSION['slider_csrf'] = \site\utils::str_random(10);

echo '<form action="#" method="POST">

<div class="form-table">

<div class="row"><span>Developer Key: <span class="info"><span>Developer keys can be generated at <a href="https://api.cj.com" target="_blank" style="color: #FFF;">api.cj.com</a></span></span></span><div><input type="text" name="key" value="' . htmlspecialchars( \query\main::get_option( 'cj_key' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>Site ID: <span class="info"><span>To see your website ID, log into your <a href="//cj.com" target="_blank" style="color: #FFF;">cj.com</a> account, then go in section <i>Account</i> and select <i>Websites.</i></span></span></span><div><input type="text" name="site-id" value="' . htmlspecialchars( \query\main::get_option( 'cj_site-id' ) ) . '" style="background: #F8E0E0;" required /></div></div>
<div class="row"><span>Deals Expiration (days) <span class="info"><span>Deals and some coupons do have not set an expiration date. This will be set automatically after a number of days that you can define here. It can be changed in preview mode.</span></span>:</span><div><input type="number" name="exp" value="' . (int) \query\main::get_option( 'cj_exp' ) . '" min="1" max="1000" required /></div></div>
<div class="row"><span>Items Per Page:</span><div><input type="number" name="ipp" value="' . (int) \query\main::get_option( 'cj_ipp' ) . '" min="1" max="100" required /></div></div>

</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>

</form><br><br><br>
<div class="title">
<h2>Category Mapping</h2>
<span>Category Mapping for Data Automation</span>
</div>';

$cj = new \plugin\CJApi\inc\client( \query\main::get_option( 'cj_key' ) );
$categories_cj = $cj->categories();
$category_mapping = \plugin\CJApi\inc\actions::listCategoryMapping();
        foreach($categories_cj as $category){
            if(!isset($category_mapping[$category])){
                $category_mapping[$category] = 0;
            }
        }
        
$categories_while = \query\main::while_categories( array( 'max' => 0, 'show' => 'subcats' ) );
        
echo '<select name="template_category" style="display:none;">';
        foreach( $categories_while as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
echo '</select>
<input type="button" value="OK" name="template_category_OK" style="display:none;"><input type="button" value="Cancel" name="template_category_Cancel" style="display:none;">
<form action="#" method="POST">
<div class="form-table">';

        foreach($category_mapping as $km=>$vm){
            $catname = 'N/A';
            $catid = 0;
            if($vm > 0){
                foreach( $categories_while as $cat ){
                    if($cat->ID == $vm){
                        $catname = $cat->name;
                        $catid = $cat->ID;
                        break;
                    }
                }
            }
            echo '<div class="row"><span>'.$km.': </span><div name="div['.$km.']"><span style="text-decoration: underline;" onclick="setCatID(\''.addslashes($km).'\');">'.$catname.'</span><input type="hidden" name="catid[' . $km . ']" value="' . $catid . '" /><input type="hidden" name="catid_old[' . $km . ']" value="' . $catid . '" /></div></div>';
        }
        
echo '</div><input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">Save</button>
</form>';
        
break;

}