<?php
    
    if( !$GLOBALS['me']->is_admin ) die;

    if(isset( $_GET['csrf'] ) && ($_GET['csrf'] == \query\main::get_option( 'cron_secret' ) || check_csrf( $_GET['csrf'], 'slider_csrf' ))){//do sync
        
        $nofav=$_GET['nofav'];
        $fav=$_GET['fav'];
        $fav_anchor=$_GET['fav_anchor'];
        $favitem=$_GET['favdeal'];
        $favsale=$_GET['favsale'];
        $root=$_GET['root'];
        $template=$_GET['template'];
        
        $template_nofav = file_get_contents($root.$nofav);
        $template_fav = file_get_contents($root.$fav);
        $template_favitem = file_get_contents($root.$favitem);
        $template_favsale = file_get_contents($root.$favsale);
        
        $now = date("Y-m-d H:i:s");
        $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
        $seo_link_coupon = \query\main::get_option( 'seo_link_coupon' );
        $seo_link_product = \query\main::get_option( 'seo_link_product' );
        $seo_link_store = \query\main::get_option( 'seo_link_store' );
        $sendy_url = \query\main::get_option( 'sendy_url' ).'subscribe';
        $sendy_list_id = \query\main::get_option( 'sendy_list_id' );
        
        $userdata = array();
        $coupondata = array();
        //list subscribers
        $stmt = $db->stmt_init();
        $search = "SELECT id,name,email FROM users WHERE ".DB_TABLE_PREFIX."subscriber>0 and valid>0 and email<>''";
        $stmt->prepare($search);
        $stmt->execute();
        $stmt->bind_result( $id,$name,$email );
        while ( $stmt->fetch() ) {
            array_push($userdata, array('id'=>$id,'name'=>$name,'email'=>$email));
        }
        foreach($userdata as $user){
            //list stores
            $search_store = "SELECT store FROM ".DB_TABLE_PREFIX."favorite WHERE user=".$user['id'];
            $stmt->prepare($search_store);
            $stmt->execute();
            $stmt->bind_result( $store );
            $mystores = array();
            while($stmt->fetch()){
                array_push($mystores, $store);
            }
            $strnofav = '';
            $strfav = '';
            if(count($mystores) > 0){
                foreach($mystores as $store){
                    if(!isset($coupondata[$store])){
                        //list coupondata for the store
                        $search_coupons = "SELECT c.id, c.title, c.link, c.description, c.tags, s.image, s.name, s.link, s.id, c.expiration FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE c.visible > 0 AND s.visible > 0 AND c.expiration > '".$now."' and c.store = ".$store;
                        $stmt->prepare($search_coupons);
                        $stmt->execute();
                        $stmt->bind_result( $c_id, $c_title, $c_link, $c_description, $c_tags, $s_image, $s_name, $s_link, $s_id, $c_expiration );
                        $coupons = array();
                        while($stmt->fetch()){
                            $coupon_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $c_title, $c_id ) : $GLOBALS['siteURL'] . '?id=' . $c_id );
                            $store_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $s_name, $s_id ) : $GLOBALS['siteURL'] . '?store=' . $s_id );
                            $c_expiration = str_replace(' 00:00:00','',$c_expiration);
                            //build coupon html
                            $coupons['c_'.$c_id] = str_replace(array('{_BANNER_URL_}','{_BANNER_IMAGE_}','{_STORE_URL_}','{_STORE_NAME_}','{_ITEM_URL_}','{_ITEM_NAME_}','{_EXPIRE_TEXT_}'),array($coupon_link,$s_image,$store_link,$s_name,$coupon_link,$c_title,'Expires on '.$c_expiration),$template_favitem);
                        }
                        //list product
                        $search_products = "SELECT p.id, p.title, p.link, p.description, p.tags, p.image, p.currency, p.price, p.old_price, s.name, s.link, s.id, p.expiration FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE p.visible > 0 AND s.visible > 0 AND p.expiration > '".$now."' and p.store = ".$store;
                        $stmt->prepare($search_products);
                        $stmt->execute();
                        $stmt->bind_result( $p_id, $p_title, $p_link, $p_description, $p_tags, $p_image, $p_currency, $p_price, $p_old_price, $s_name, $s_link, $s_id, $p_expiration );
                        while($stmt->fetch()){
                            $product_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $p_title, $p_id ) : $GLOBALS['siteURL'] . '?product=' . $p_id );
                            $store_link = ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $s_name, $s_id ) : $GLOBALS['siteURL'] . '?store=' . $s_id );
                            $p_expiration = str_replace(' 00:00:00','',$p_expiration);
                            //build coupon html
                            $coupons['p_'.$p_id] = str_replace(array('{_BANNER_URL_}','{_BANNER_IMAGE_}','{_STORE_URL_}','{_STORE_NAME_}','{_ITEM_URL_}','{_ITEM_NAME_}','{_EXPIRE_TEXT_}','{_NOW_PRICE_}','{_OLD_PRICE_}'),array($product_link,$p_image,$store_link,$s_name,$product_link,$p_title,'Expires on '.$p_expiration, $p_currency.$p_price, $p_currency.$p_old_price),$template_favsale);
                        }
                        $coupondata[$store] = $coupons;
                    }
                    foreach($coupondata[$store] as $coupon_content){
                        $strfav .= $coupon_content;
                    }
                }
                if($strfav == ''){
                    $strnofav = $template_nofav;
                }else{
                    $strfav = str_replace($fav_anchor,$strfav,$template_fav);
                }
                
            }else{
                //this user has no fav
                $strnofav = $template_nofav;
            }
            //call sendy
            $data = array('email' => $user['email'], 'name' => $user['name'], 'list' => $sendy_list_id, '_update_custom_fields' => 1, 'NOFAV' => $strnofav, 'MYFAV' => $strfav);
            // use key 'http' even if you send the request to https://...
            $options = array(
                             'http' => array(
                                             'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                             'method'  => 'POST',
                                             'content' => http_build_query($data),
                                             )
                             );
            $context  = stream_context_create($options);
            $result = file_get_contents($sendy_url, false, $context);
            echo "UID(".$user['id']."):".strip_tags($result)."<br>";
        }
        $stmt->close();
        
        actions::set_option( array( 'sendy_last_sync' => time() ) );
    }else{
        echo 'signature error!<br>';
    }

    echo '<div><button class="btn" onclick="parent.location.reload();">Close</button></div>';