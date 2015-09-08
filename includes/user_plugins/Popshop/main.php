<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {
    
case 'stores':
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $storedata = array();
            foreach($_POST['id'] as $index=>$val){
                $storedata[] = json_decode(urldecode($_POST['store'][$index]),true);
            }
            $catID = $_POST['category'];
            $ret = \plugin\Popshop\inc\actions::add_store($catID, $storedata);
            echo '<div class="a-alert">Merchants ('.$ret.') import success.</div>';
        }
    
    echo '<script>
        function goFilter(){
            var view = $("[name=view]").val();
            var search = $("[name=search]").val();
            var mtype = $("[name=mtype]").val();
            var url = "?plugin=Popshop/main.php&action=stores&view="+view;
            if(search != ""){
                url += "&search="+search;
            }
            if(mtype && mtype != "" && mtype != "0"){
                url += "&mtype="+mtype;
            }
            location.href=url;
        }
    </script>
    <div class="title">
    
    <h2>Merchants</h2>
    <span>List of merchants</span>
    
    </div>';
    
    $csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);
    
    echo '<div class="page-toolbar">
    
    
    View: <select name="view">';
    foreach( ( $views = array( 'joined' => 'Imported', 'notjoined' => 'Not Imported' ) ) as $k => $v ) echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && urldecode( $_GET['view'] ) == $k || !isset( $_GET['view'] ) && $k == 'notjoined' ? ' selected' : '') . '>' . $v . '</option>';
    echo '</select><select name="mtype"><option value="0">Any Type</option>';
    $merchant_types = \plugin\Popshop\inc\actions::listMerchantTypes();
        foreach( $merchant_types as $k => $v ){
            echo '<option value="' . $k . '"' . ((isset( $_GET['mtype'] ) && urldecode( $_GET['mtype'] ) == $k) ? ' selected' : '') . '>' . $v . '</option>';
        }
        echo '</select><input type="search" name="search" value="' . (!isset( $_GET['search'] ) ? '' : htmlspecialchars( $_GET['search'] )) . '" placeholder="Search merchants" /> <button class="btn" onclick="goFilter();">View</button></div>';
        
        /* do display job */
        $view = isset( $_GET['view'] ) && array_key_exists( $_GET['view'], $views ) ? $_GET['view'] : 'notjoined';
        $mtype = isset( $_GET['mtype'] ) && array_key_exists( $_GET['mtype'], $merchant_types ) ? $_GET['mtype'] : 0;
        /* */
        
        /* pagination */
        $per_page = \query\main::get_option( 'cj_ipp' );
        $page = isset( $_GET['page'] ) && $_GET['page'] > 0 ? $_GET['page'] : 0;
        /* */
        
        $lookup = array( 'status' => $view, 'type'=>$mtype, 'per_page' => $per_page, 'page' => $page );
        if( !empty( $_GET['search'] ) ) {
            $lookup['name'] = $_GET['search'];
        }
        
        $advs = \plugin\Popshop\inc\actions::listMerchants( $lookup );
        
        if($page > 0 || count($advs)>0) {
            
            echo '<form action="?plugin=Popshop/main.php&amp;action=stores" method="POST" autocomplete="off">
            
            <ul class="elements-list">
            
            <li class="head"><input type="checkbox" checkall /> Name</li>
            
            <div class="bulk_options">
            
        Category: ';
            echo '<select name="category">';
            foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
                echo '<optgroup label="' . $cat['infos']->name . '">';
                echo '<option value="' . $cat['infos']->ID . '">' . $cat['infos']->name . '</option>';
                if( isset( $cat['subcats'] ) ) {
                    foreach( $cat['subcats'] as $subcat ) {
                        echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>
            
            <button class="btn">Import all</button>';
            
            echo '</div>';
            
            foreach( $advs as $item ) {
                
                // check first if this store is imported on your website
                $imported = ($item['storeID'] > 0);
                
                echo '<li>
                <input type="checkbox" name="id[' . $item['id'] . ']"' . ( $imported ? ' disabled' : '' ) . ' />
                
                <div style="display: table;">
                
                <img src="' . $item['logo_url'] . '" alt="" style="width: 80px;" />
                <div class="info-div"><h2>' . htmlspecialchars( $item['name'] ) . ' <small>['.$merchant_types[$item['merchant_type']].']</small></h2>
                
            URL: <a href="' . $item['url'] . '" target="_blank">' . ( ( $url = urldecode( $item['url'] ) ) && strlen( $url ) > 50 ? substr( $url, 0, 50 ) . '...' : $url ) . '</a> <br />
                
                </div></div>
                
                <div style="clear:both;"></div>
                
                <div class="options">';
                if( !$imported ) {
                    echo '<a href="javasript:void(0)" onclick="$(this).parents(\'li\').children(\'input\').click(); return false;">Check/Uncheck</a>';
                    echo '<a href="?plugin=Popshop/main.php&amp;action=store_preview&amp;store=' . ( $cdata = urlencode( json_encode( $item ) ) ) . '">Preview & Import</a>';
                    echo '<a href="?plugin=Popshop/main.php&amp;action=store_bind&amp;store=' . ( $cdata = urlencode( json_encode( $item ) ) ) . '">Bind to Existing</a>';
                    echo '<input type="hidden" name="store['.$item['id'].']" value="' . $cdata . '" />';
                }
                echo '<a href="?plugin=Popshop/main.php&amp;action=coupons&amp;merchant=' . $item['id'] . '">View Deals</a>';
                echo '</div>
                </li>';
                
            }
            
            echo '</ul>
            
            <input type="hidden" name="token" value="' . $csrf . '" />
            
            </form>';
            
            
            
                
                echo '<div class="pagination">';
                if( $page >= 1 ) echo '<a href="' . \site\utils::update_uri( '', array( 'page' => ($page-1) ) ) . '" class="btn">← Prev</a>';
                if( count($advs) >= $per_page ) echo '<a href="' . \site\utils::update_uri( '', array( 'page' => ($page+1) ) ) . '" class="btn">Next →</a>';
                echo '</div>';
            
            
        } else {
            
            echo '<div class="a-alert">No merchant.</div>';
            
        }
        
   
    
    break;
/** BIND STORE */
case 'store_bind':
        
        echo '<div class="title">
        
        <h2>Bind to Existing Store</h2>
        
        <span>Here you can bind the Popshop merchant tp an existing store</span>
        
        </div>';
        
        if( isset( $_GET['store'] ) ) {
            
            $store = json_decode( urldecode( $_GET['store'] ), true );
            
            $id = $store['id'];
            
        } else {
            $store = array();
        }
        
        if( $store['storeID'] ) {
            echo '<div class="a-error">Sorry, this store is already imported.</div>';
        } else {
            $searchname = isset( $_POST['search_name'] )?$_POST['search_name']:$store['name'];
            $stores_choose = \plugin\Popshop\inc\actions::list_store_bind($searchname);
            
            if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'cjapi_csrf' ) ) {
                
                if( isset( $_POST['choose_store'] ) && $_POST['choose_store'] != '') {
                    
                    $params = explode("|", $_POST['choose_store']);
                    $ret = \plugin\Popshop\inc\actions::bind_store($params[0],$id,$params[1]);
                    
                    if( $ret > 0 ){
                        echo '<div class="a-success">Added!</div><button class="btn" onclick="window.history.go(-2);">Back</button>';
                        
                        return;
                    }
                    else
                        echo '<div class="a-error">Error!</div>';
                    
                }
                
            }
            
            $csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);
            
            echo '<div class="form-table">
            
            <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="row"><span>Name:</span><div><input name="search_name" type="text" value="'.$searchname.'"><button class="btn">Search</button></div></div>
            <div class="row"><span>Select:</span>
            <div><select name="choose_store">';
            echo '<option value=""'.( (isset( $_POST['choose_store'] ) && $_POST['choose_store'] != '0' )?'':' selected' ).'>N/A</option>';
            foreach($stores_choose as $data){
                echo '<option value="' . $data['id'] .'|' . $data['popshopID'] . '"'. ( (isset( $_POST['choose_store'] ) && $_POST['choose_store'] == $data['id'] )?' selected':'' ) .'>' . $data['name'] . '</option>';
            }
            echo '</select></div></div>
                    
                    <input type="hidden" name="csrf" value="' . $csrf . '" />
                    <button class="btn">Import</button>
                    
                    </form>
                    
                    </div>';
                    
        }
        
        break;
/** PREVIEW STORE */
case 'store_preview':
        
        echo '<div class="title">
        
        <h2>Preview & Import</h2>
        
        <span>Here you can edit the details of this store before the import</span>
        
        </div>';
        
        if( isset( $_GET['store'] ) ) {
            
            $store = json_decode( urldecode( $_GET['store'] ), true );
            
            $id = $store['id'];
            
        } else {
            $store = array();
        }
        
        if( $store['storeID'] ) {
            echo '<div class="a-error">Sorry, this store is already imported.</div>';
        } else {
            
            if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['store'] ) ) {
                
                if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'cjapi_csrf' ) ) {
                    
                    $storedata = array('id' => $id, 'popular' => ( isset( $_POST['store']['Popular'] ) ? 1 : 0 ), 'name' => $_POST['store']['Name'], 'logo_url' => $_POST['store']['logo_url'], 'url' => $_POST['store']['Link'], 'description' => $_POST['store']['Description'], 'tags' => $_POST['store']['Tags'], 'publish' => ( isset( $_POST['store']['Publish'] ) ? 1 : 0 ), 'meta_title' => $_POST['store']['MTitle'], 'meta_desc' => $_POST['store']['MDesc']);
                    
                    $ret = \plugin\Popshop\inc\actions::add_store($_POST['store']['Category'], array($storedata));
                    
                    if( $ret > 0 ){
                        echo '<div class="a-success">Added!</div><button class="btn" onclick="window.history.go(-2);">Back</button>';
                        
                        return;
                    }
                    else
                        echo '<div class="a-error">Error!</div>';
                    
                }
                
            }
            
            $csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);
            
            echo '<div class="form-table">
            
            <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
            
            <div class="row"><span>Category:</span>
            <div><select name="store[Category]">';
            $merchant_types = \plugin\Popshop\inc\actions::listMerchantTypes($store['merchant_type']);
            foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
                echo '<optgroup label="' . $cat['infos']->name . '">';
                echo '<option value="' . $cat['infos']->ID . '">' . $cat['infos']->name . '</option>';
                if( isset( $cat['subcats'] ) ) {
                    foreach( $cat['subcats'] as $subcat ) {
                        echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>'.$merchant_types[$store['merchant_type']].'</div></div>';
            
            echo '<div class="row"><span>Name:</span><div><input type="text" name="store[Name]" value="' . ( isset( $store['name'] ) ? $store['name'] : '' ) . '" required /></div></div>
            <div class="row"><span>Store URL:</span><div><input type="text" name="store[Link]" value="' . ( isset( $store['url'] ) ? $store['url'] : '' ) . '" /></div></div>
            <div class="row"><span>Description:</span><div><textarea name="store[Description]">' . ( isset( $store['Description'] ) ? $store['Description'] : '' ) . '</textarea></div></div>
            <div class="row"><span>Tags:</span><div><input type="text" name="store[Tags]" value="' . ( isset( $store['Tags'] ) ? $store['Tags'] : '' ) . '" /></div></div>
            <div class="row"><span>Logo:</span><div><input type="text" name="store[logo_url]" value="' . ( isset( $store['logo_url'] ) ? $store['logo_url'] : '' ) . '" /></div></div>
            <div class="row"><span>Add to:</span><div><input type="checkbox" name="store[Popular]" id="popular"' . ( isset( $store['Popular'] ) ? ' checked' : '' ) . ' /> <label for="popular">Populars</label></div></div>
                <div class="row"><span>Publish:</span><div><input type="checkbox" name="store[Publish]" id="publish"' . ( $_SERVER['REQUEST_METHOD'] == 'POST' && !isset( $store['Publish'] ) ? '' : ' checked' ) . ' /> <label for="publish">Publish this store</label></div></div>
                    
                    <div id="modify_mt" style="display: none; margin-top: 20px;">
                    
                    <div class="title">
                    <h2>Personalized Meta-Tags</h2>
                    </div>
                    
                    <div class="row"><span>Title <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><input type="text" name="store[MTitle]" value="' . ( isset( $store['MTitle'] ) ? $store['MTitle'] : '' ) . '" /></div></div>
                    <div class="row"><span>Description <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><textarea name="store[MDesc]">' . ( isset( $store['MDesc'] ) ? $store['MDesc'] : '' ) . '</textarea></div></div>
                    
                    </div>
                    
                    <input type="hidden" name="csrf" value="' . $csrf . '" />
                    <button class="btn">Import</button>
                    
                    <a href="#" id="modify_mt_but">Meta Tags</a>
                    
                    </form>
                    
                    </div>';
                    
                    }
        
        break;
/** LIST OF coupons */
case 'coupons':
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $coupondata = array();
            foreach($_POST['id'] as $index=>$val){
                $coupondata[] = json_decode(urldecode($_POST['coupon'][$index]),true);
            }
            $ret = \plugin\Popshop\inc\actions::add_item($coupondata);
            echo '<div class="a-alert">Coupons ('.$ret.') import success.</div>';
        }
        
        echo '<script>
        function goFilter(){
            var view = $("[name=view]").val();
            var type = $("[name=type]").val();
            var url = "?plugin=Popshop/main.php&action=coupons&view="+view;
            if(type && type != ""){
                url += "&type="+type;
            }
            location.href=url;
        }
        </script>
        <div class="title">
        
        <h2>Deals</h2>
        
        <span>List of Popshop deals</span>
        
        </div>';
        
        $csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);
        
        echo '<div class="page-toolbar">
        
    View: <select name="view">';
        foreach( ( $views = array( 'joined' => 'Imported', 'notjoined' => 'Not Imported' ) ) as $k => $v ) echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && urldecode( $_GET['view'] ) == $k || !isset( $_GET['view'] ) && $k == 'notjoined' ? ' selected' : '') . '>' . $v . '</option>';
        
        echo '</select>Type: <select name="type">';
        $types = \plugin\Popshop\inc\actions::listDealTypes(NULL);
        foreach( $types as $k => $v ) echo '<option value="' . $k . '"' . ( isset( $_GET['type'] ) && urldecode( $_GET['type'] ) == $k ? ' selected' : '' ) . '>' . $v . '</option>';
        echo '</select> <button class="btn" onclick="goFilter();">View</button>
        </div>';
            
            /* view after relationship */
            $view = isset( $_GET['view'] ) && array_key_exists( $_GET['view'], $views ) ? $_GET['view'] : 'notjoined';
            
            $merchantid = isset( $_GET['merchant'] )?intval($_GET['merchant']):0;
            
            /* view after type */
            $type = isset( $_GET['type'] ) && array_key_exists( $_GET['type'], $types ) ? $_GET['type'] : '';
            
            /* pagination */
            $per_page = \query\main::get_option( 'cj_ipp' );
            $page = isset( $_GET['page'] ) && $_GET['page'] > 0 ? $_GET['page'] : 0;
            /* */
            
            $lookup = array( 'status' => $view, '$merchant' => $merchantid, 'deal_type' => $type, 'page' => $page, 'per_page' => $per_page);
            $links = \plugin\Popshop\inc\actions::listDeals( $lookup );
            
            if($page > 0 || count($links)>0) {
                
                echo '<form action="?plugin=Popshop/main.php&amp;action=coupons" method="POST">
                
                <ul class="elements-list">
                
                <li class="head"><input type="checkbox" checkall /> Name</li>
                
                <div class="bulk_options">
                
                <button class="btn">Import all</button>
                
                </div>';
                $stores = array();
                $deal_types = \plugin\Popshop\inc\actions::listDealTypes();
                foreach( $links as $item ) {
                    
                    // check first if this store is imported on your website
                    $store = NULL;
                    if(isset($stores[$item['merchant']])){
                        $store = $stores[$item['merchant']];
                    }else{
                        $store = \plugin\Popshop\inc\actions::check_store($item['merchant']);
                        if($store){
                            $stores[$item['merchant']] = $store;
                        }
                    }
                    $store_imported = ($store['storeID'] > 0);
                    
                    // check first if this coupon is imported on your website
                    $coupon_imported = ($item['couponID'] > 0);
                    
                    $deal_type = '';
                    if($item['deal_type'] != ''){
                        $arrs = explode(",", $item['deal_type']);
                        foreach($arrs as $arr){
                            $deal_type .= '['.$deal_types[$arr].']';
                        }
                    }
                    
                    echo '<li>
                    <input type="checkbox" name="id[' . $item['id'] . ']"' . ( !$store_imported || ( $store_imported && $coupon_imported ) ? ' disabled' : '' ) . ' />
                    
                    <div style="display: table;">
                    
                    <img src="' .$store['logo_url']. '" alt="" style="width: 80px;" />
                    <div class="info-div"><h2>' . ( $store_imported && $coupon_imported ? '<span class="msg-alert">Imported</span> ' : '' ) . htmlspecialchars( $item['name'] ) . '</h2>
                Merchant: <b><a href="?plugin=Popshop/main.php&amp;action=coupons&amp;merchant=' . $item['merchant'] . '">' . htmlspecialchars( $store['name'] ) . '</a></b> <br />
                URL: <a href="' . $item['url'] . '" target="_blank">' . ( ( $url = urldecode( $item['url'] ) ) && strlen( $url ) > 50 ? substr( $url, 0, 50 ) . '...' : $url ) . '</a> <br />
                Category: <b>' . $deal_type . '</b> <br />
                    
                    </div></div>
                    
                    <div style="clear:both;"></div>
                    
                    <div class="options">';
                    
                    if( $store_imported && !$coupon_imported ) {
                        echo '<a href="javasript:void(0)" onclick="$(this).parents(\'li\').children(\'input\').click(); return false;">Check/Uncheck</a>';
                        echo '<a href="?plugin=Popshop/main.php&amp;action=coupon_preview&amp;coupon=' . ( $cdata = urlencode( json_encode( $item ) ) ) . '&amp;store='. ( $cdata2 = urlencode( json_encode( $store ) ) ) .'">Preview & Import</a>';
                        echo '<input type="hidden" name="coupon['.$item['id'].']" value="' . $cdata . '" />';
                    }
                    
                    echo '</div>
                    </li>';
                    
                }
                
                echo '</ul>
                
                <input type="hidden" name="token" value="' . $csrf . '" />
                
                </form>';
                
                
                echo '<div class="pagination">';
                if( $page >= 1 ) echo '<a href="' . \site\utils::update_uri( '', array( 'page' => ($page-1) ) ) . '" class="btn">← Prev</a>';
                if( count($links) >= $per_page ) echo '<a href="' . \site\utils::update_uri( '', array( 'page' => ($page+1) ) ) . '" class="btn">Next →</a>';
                echo '</div>';
                
            } else {
                
                echo '<div class="a-alert">No links.</div>';
                
            }
        
    break;
/** PREVIEW COUPON */
case 'coupon_preview':
        
        echo '<div class="title">
        
        <h2>Preview & Import</h2>
        <span>Here you can edit the details of this coupon before the import</span>
        
        </div>';
        
        if( isset( $_GET['coupon'] ) ) {
            
            $coupon_p = json_decode( urldecode( $_GET['coupon'] ), true );
            $store_p = json_decode( urldecode( $_GET['store'] ), true );
            
            $id = $coupon_p['id'];
            
        }
        
        if( !isset( $store_p ) || $store_p['storeID'] == 0 ) {
            echo '<div class="a-error">Sorry, the store is not imported.</div>';
        } else if( $coupon_p['couponID'] > 0 ) {
            echo '<div class="a-alert">Sorry, the coupon is already imported.</div>';
        } else {
            
            if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['coupon'] ) ) {
                
                if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'cjapi_csrf' ) ) {
                    
                    $data =  array( 'store' => $store_p['storeID'], 'category' => $_POST['coupon']['Category'], 'popular' => ( isset( $_POST['coupon']['Popular'] ) ? true : false ), 'exclusive' => ( isset( $_POST['coupon']['Exclusive'] ) ? true : false ), 'name' => $_POST['coupon']['Title'], 'url' => ( !isset( $_POST['coupon']['Ownlink'] ) && isset( $_POST['coupon']['Link'] ) && filter_var( $_POST['coupon']['Link'], FILTER_VALIDATE_URL ) ? $_POST['coupon']['Link'] : '' ), 'code' => $_POST['coupon']['Code'], 'description' => $_POST['coupon']['Description'], 'tags' => $_POST['coupon']['Tags'], 'start_on' => implode( $_POST['coupon']['SD'], ', ' ), 'end_on' => implode( $_POST['coupon']['ED'], ', ' ), 'publish' => ( isset( $_POST['coupon']['Publish'] ) ? true : false ), 'meta_title' => $_POST['coupon']['MTitle'], 'meta_desc' => $_POST['coupon']['MDesc'] );
                    
                    if( \plugin\Popshop\inc\actions::add_item(array(array_merge($coupon_p, $data))) > 0 ){
                        echo '<div class="a-success">Added!</div><button class="btn" onclick="window.history.go(-2);">Back</button>';
                        return;
                    }else
                        echo '<div class="a-error">Error!</div>';
                    
                }
                
            }
            
            $csrf = $_SESSION['cjapi_csrf'] = \site\utils::str_random(10);
            
            $store = \plugin\Popshop\inc\actions::get_import_store($store_p['storeID']);
            
            echo '<div class="form-table">
            
            <form action="#" method="POST" autocomplete="off">
            <div class="row"><span>Category:</span>
            <div><select name="coupon[Category]">';
            foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
                echo '<optgroup label="' . $cat['infos']->name . '">';
                echo '<option value="' . $cat['infos']->ID . '"' . ( $store['category'] == $cat['infos']->ID ? ' selected' : '' ) . '>' . $cat['infos']->name . '</option>';
                if( isset( $cat['subcats'] ) ) {
                    foreach( $cat['subcats'] as $subcat ) {
                        echo '<option value="' . $subcat->ID . '"' . ( $store['category'] == $subcat->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
                    }
                }
                echo '</optgroup>';
            }
            $tags = '';
            if($coupon_p['deal_type'] != ''){
                $deal_types = \plugin\Popshop\inc\actions::listDealTypes($coupon_p['deal_type']);
                foreach($deal_types As $deal_type){
                    if($tags == ''){
                        $tags = $deal_type;
                    }else{
                        $tags .= ', '.$deal_type;
                    }
                }
            }
            echo '</select></div></div>
            
            <div class="row"><span>Name:</span><div><input type="text" name="coupon[Title]" value="' . ( isset( $coupon_p['name'] ) ? $coupon_p['name'] : '' ) . '" required /></div></div>
            <div class="row"><span>Code:</span><div><input type="text" name="coupon[Code]" value="' . ( isset( $coupon_p['code'] ) ? $coupon_p['code'] : '' ) . '" /></div></div>
            <div class="row"><span>Coupon URL:</span><div><input type="checkbox" name="coupon[Ownlink]" value="1" id="ownlink" onclick="$(this).show_next({element:\'#link\', type:\'next\'});"' . ( ( isset( $coupon_p['url'] ) && $coupon_p['url'] ) || empty( $store['link'] ) ? ' checked' : '' ) . ' /> <label for="ownlink">Use store address</label> <br />
                <input type="text" name="coupon[Link]" value="' . ( isset( $store['link'] ) ? $store['link'] : 'http://' ) . '" id="link"' . ( ( isset( $coupon_p['url'] ) && $coupon_p['url'] ) || empty( $store['link'] ) ? ' style="display: none;"' : '' ) . ' />
                </div></div>
                <div class="row"><span>Description:</span><div><textarea name="coupon[Description]">' . ($coupon_p['site_wide']?'Applicable across the entire merchant website':'Applicable to the specific product or category').( isset( $store['description'] ) ? ' '.$store['description'] : '' ) . '</textarea></div></div>
                <div class="row"><span>Tags:</span><div><input type="text" name="coupon[Tags]" value="' . $tags . '" /></div></div>
                <div class="row"><span>Start Date:</span><div><input type="date" name="coupon[SD][]" value="' . $coupon_p['start_on'] . '" style="width: 80%" /><input type="time" name="coupon[SD][]" value="00:00:00" style="width: 20%" /></div></div>
                <div class="row"><span>End Date:</span><div><input type="date" name="coupon[ED][]" value="' . $coupon_p['end_on'] . '" style="width: 80%" /><input type="time" name="coupon[ED][]" value="00:00:00" style="width: 20%" /></div></div>
                <div class="row"><span>Add to:</span><div>
                <input type="checkbox" name="coupon[Popular]" id="popular"' . ($store['popular'] ? ' checked' : '' ) . ' /> <label for="popular">Populars</label> <br />
                    <input type="checkbox" name="coupon[Exclusive]" id="exclusive"/> <label for="exclusive">Exclusive</label></div></div>
                        <div class="row"><span>Publish:</span><div><input type="checkbox" name="coupon[Publish]" id="publish"' . ( $_SERVER['REQUEST_METHOD'] == 'POST' && $store['visible'] ? '' : ' checked' ) . ' /> <label for="publish">Publish this coupon</label></div></div>
                            
                            <div id="modify_mt" style="display: none; margin-top: 20px;">
                            
                            <div class="title">
                            <h2>Personalized Meta-Tags</h2>
                            </div>
                            
                            <div class="row"><span>Title <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><input type="text" name="coupon[MTitle]" value="' . ( isset( $coupon['MTitle'] ) ? $coupon['MTitle'] : '' ) . '" /></div></div>
                            <div class="row"><span>Description <span class="info"><span>Supported shortcodes: %MONTH%, %YEAR%</span></span>:</span><div><textarea name="coupon[MDesc]">' . ( isset( $coupon['MDesc'] ) ? $coupon['MDesc'] : '' ) . '</textarea></div></div>
                            
                            </div>
                            
                            <input type="hidden" name="csrf" value="' . $csrf . '" />
                            <button class="btn">Import</button>
                            
                            <a href="#" id="modify_mt_but">Meta Tags</a>
                            
                            </form>
                            
                            </div>';
                            
                            }
        
    break;
}