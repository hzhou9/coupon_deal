<?php
    
    if( !$GLOBALS['me']->is_admin ) die;

    if(isset( $_GET['csrf'] ) && ($_GET['csrf'] == \query\main::get_option( 'cron_secret' ) || check_csrf( $_GET['csrf'], 'slider_csrf' ))){//do sync
        $page = 1;
        $dup_deals_count = 0;$dup_deals_count_max = 10;//stop querying server when we believe there is no new data
        $deals_more = 0;
        $deals_import_total = 0;$merchants_import_total = 0;
        $url = "http://api.popshops.com/v3/deals.json?catalog=".\query\main::get_option( 'popshop_catalog' )."&account=".\query\main::get_option( 'popshop_account' )."&tracking_id=_mystore365_track_id_&end_on_min=".date('Y/m/d')."&results_per_page=100";
        $url_m = "http://api.popshops.com/v3/merchants.json?catalog=".\query\main::get_option( 'popshop_catalog' )."&account=".\query\main::get_option( 'popshop_account' )."&tracking_id=_mystore365_track_id_&results_per_page=100";
        do{
            echo $url."&page=".$page.'<br>';
            $jsondata = file_get_contents($url."&page=".$page);
            $dealinfo = json_decode($jsondata,true);
            if($dealinfo['message'] != 'ok'){
                echo 'sync error:<br>'.$jsondata;
                die;
            }
            
            if(isset($dealinfo['results'])){
            //1.import deal types
            $ret = \plugin\Popshop\inc\actions::importDealType($dealinfo['resources']['deal_types']['deal_type']);
            //2.import merchants
            $merchants_import = 0;
            if(\plugin\Popshop\inc\actions::hasNewMerchant( $dealinfo['resources']['merchants']['merchant'] ) > 0){
                $page_m = 1;
                $merchants_more=0;
                do{
                    echo $url_m."&page=".$page_m.'<br>';
                    $jsondata_m = file_get_contents($url_m."&page=".$page_m);
                    $merchantinfo = json_decode($jsondata_m,true);
                    if($merchantinfo['message'] != 'ok'){
                        echo 'sync error:<br>'.$jsondata_m;
                        die;
                    }
                    if(isset($merchantinfo['results'])){
                        //import merchant_type
                        $ret = \plugin\Popshop\inc\actions::importMerchantType($merchantinfo['resources']['merchant_types']['merchant_type']);
                        //var_dump($ret);
                        //import with type field
                        $ret = \plugin\Popshop\inc\actions::importMerchant($merchantinfo['results']['merchants']['merchant']);
                        //var_dump($ret);
                        $merchants_import_total += $ret['num_imports'];
                        $merchants_import = $ret['num_imports'];
                        $merchants_more = $merchantinfo['results']['merchants']['count'] - $page_m*100;
                        $page_m++;
                    }else{
                        $merchants_more=0;
                    }
                }while($merchants_more > 0);
            }
            
            //3.import deals
            $ret = \plugin\Popshop\inc\actions::importDeal($dealinfo['results']['deals']['deal']);
            $dup_deals_count = $ret['num_dups'];
            $deals_import_total += $ret['num_imports'];
            $deals_more = $dealinfo['results']['deals']['count'] - $page*100;
            echo "imported ".$merchants_import.' new stores '.$ret['num_imports'].'('.$dup_deals_count.') new coupons<br>';
            $page++;
            }else{
            $deals_more=0;
            }
        }while($deals_more > 0 && $dup_deals_count < $dup_deals_count_max);
        actions::set_option( array( 'popshop_lastupdate' => time() ) );
        echo 'sync success!<br>'.$merchants_import_total.' new stores<br>'.$deals_import_total.' new coupons<br>';
    }

    echo '<div><button class="btn" onclick="parent.location.reload();">Close</button></div>';