<?php
class class_inventory extends class_gallery{
    
    public $load_time;
    
    function __construct() {
        parent::__construct();
    }
    
    function get_categories($type=0){
        if($type){
            $typeSQL = " WHERE measure_type='".intval($type)."'";
        }else{
            $typeSQL = "";
        }
        $aCategories = db::get("SELECT * FROM ".PREF."goods_categories $typeSQL");
        return $aCategories;
    }
    
    function get_category($id){
        $aCategory = db::get_row("SELECT * FROM ".PREF."goods_categories WHERE id = '".intval($id)."'");
        return $aCategory;
    }
    
    function add_category($name, $type){
        if(!empty($name)){
            if(!empty($type)){
                $ok = db::query("INSERT INTO ".PREF."goods_categories SET name = ~~, measure_type = '".intval($type)."'",array($name));
                if($ok){
                    $this->set_current_instock();
                    return "ok";
                }else{
                    return "Database inserting error";
                }
            }else{
                return "Select measure type";
            }
        }else{        
            return "Empty category name";
        }
    }
    
    function update_category($id, $name){
        if(!empty($name)){
            $ok = db::query("UPDATE ".PREF."goods_categories SET name = ~~ WHERE id = '".intval($id)."'",array($name));
            if($ok){
                $this->set_current_instock();
                return "ok";
            }else{
                return "Database updating error";
            }
        }else{        
            return "Empty category name";
        }
    }
    
    function delete_category($id){
        $aGoodsIds = db::get("SELECT id, image FROM ".PREF."goods WHERE cat_id = '".intval($id)."'");
        foreach($aGoodsIds as $good){
            if(!empty($good['image'])){
                @unlink(ABS.GALLERY_FOLDER.'/'.$good['image']);
                @unlink(ABS.GALLERY_FOLDER.'/th_'.$good['image']);
            }
            $aBarCodesToDel = db::get("SELECT bar_code_image FROM ".PREF."goods_modifiers WHERE goods_item_id='".intval($good['id'])."'");
            if($aBarCodesToDel){
                foreach($aBarCodesToDel as $f){
                    if($f['bar_code_image']){
                        @unlink(ABS.GALLERY_FOLDER.'/'.$f['bar_code_image']);
                    }
                }
            }
        }
        $ok = db::query("DELETE FROM ".PREF."goods_categories WHERE id = '".intval($id)."'");
        if($ok){   
            $this->set_current_instock();
            return 'ok';
        }else{
            return 'Database deleting error';
        }
    }
    
    function activate_item($id, $table, $active){
        $alowedTables = ['goods_categories','goods'];
        if(!in_array($table, $alowedTables)){
            return 'wrong table name';
        }
        if($active){
            $act = 1;
        }else{
            $act = 0;
        } 
        $ok = db::query("UPDATE ".PREF.$table." SET active = '".$act."' WHERE id = '".intval($id)."'");
        if($ok){
            return 'ok';
        }else{
            return 'Database updating error';
        }
    }
    
    function get_goods($cat_id = 0, $ordby='', $ord='ASC', $search_str=''){
        global $aWeights, $aQTY;
        if($cat_id){
            $cat_where = "AND ".PREF."goods.cat_id = '".intval($cat_id)."'";
        }else{
            $cat_where = "";
        }
        if($search_str){
            $searchSQL = "AND ".PREF."goods.name LIKE '%".db::clear(addslashes($search_str))."%'";
        }else{
            $searchSQL = "";
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = "name"; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby == 'vendorName' ? PREF."vendors.name" : ( $ordby == 'price' ? (PREF."goods_modifiers.price") : ( $ordby == 'category_name' ? PREF.'goods_categories.name' : ($ordby == 'users.firstname' ? (PREF."users.firstname") : PREF."goods.".$ordby)))))." $ord";
        $aGoods = db::get("SELECT ".PREF."goods.*, ".PREF."vendors.name AS vendorName, ".PREF."vendors.common AS commonVendor, sales.last_sale, ".PREF."goods_modifiers.price AS price, ".PREF."goods_modifiers.price_pre_roll AS price_pre_roll, ".PREF."goods_categories.name AS category_name, CONCAT(".PREF."users.firstname, ' ', ".PREF."users.lastname) AS added_by_user
                                 FROM ".PREF."goods
                                 LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id
                                 LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                                 LEFT JOIN (SELECT goods_item_id, MAX(date) AS last_sale FROM ".PREF."orders_items GROUP BY goods_item_id) AS sales ON sales.goods_item_id = ".PREF."goods.id
                                 LEFT JOIN ".PREF."goods_modifiers ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                                 LEFT JOIN ".PREF."users ON ".PREF."users.id = ".PREF."goods.added_by
                                 WHERE ".PREF."goods.safe = '0' $searchSQL $cat_where $order_sql");
        if($aGoods){
            foreach($aGoods as $k=>$good){
                if($good['measure_type'] == 1){
                    $aParams = $aWeights;
                }else{
                    $aParams = $aQTY;
                }
                foreach($aParams as $p){
                    $aQTYByMods[$p['name']] = db::get_row("SELECT in_stock FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($good['id'])."' AND name = ~~", array($p['name']));
                }
                foreach($aQTYByMods as $name=>$q){                    
                    $aGoods[$k]['mods'][$name]['in_stock'] = floatval($q['in_stock']);  
                    //$aGoods[$k]['mods'][$name]['price'] = floatval($q['price']); 
                    $aGoods[$k]['q_'.$name] = floatval($q['in_stock']);  
                }  
            }
        }
        return $aGoods;
    }
    
    function get_meds_by_type($type, $ordby='', $ord='ASC'){        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = "name"; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby == 'vendorName' ? PREF."vendors.name" : ( $ordby == 'price' ? (PREF."goods_modifiers.price") : ( $ordby == 'category_name' ? PREF.'goods_categories.name' : ($ordby == 'users.firstname' ? (PREF."users.firstname") : PREF."goods.".$ordby)))))." $ord";
        $aGoods = db::get("SELECT ".PREF."goods.*, ".PREF."vendors.name AS vendorName, ".PREF."vendors.common AS commonVendor, sales.last_sale, ".PREF."goods_modifiers.price AS price, ".PREF."goods_modifiers.price_pre_roll AS price_pre_roll, ".PREF."goods_categories.name AS category_name, CONCAT(".PREF."users.firstname, ' ', ".PREF."users.lastname) AS added_by_user
                                 FROM ".PREF."goods
                                 LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id
                                 LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                                 LEFT JOIN (SELECT goods_item_id, MAX(date) AS last_sale FROM ".PREF."orders_items GROUP BY goods_item_id) AS sales ON sales.goods_item_id = ".PREF."goods.id
                                 LEFT JOIN ".PREF."goods_modifiers ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                                 LEFT JOIN ".PREF."users ON ".PREF."users.id = ".PREF."goods.added_by
                                 WHERE ".PREF."goods.safe = '0' AND ".PREF."goods.measure_type = '1' AND ".PREF."goods_categories.active = '1' AND ".PREF."goods.in_stock > 0 AND ".PREF."goods.meds_type = '".intval($type)."' $order_sql");
        if($aGoods){
            foreach($aGoods as $k=>$good){
                $aMods = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($good['id'])."'");
                $aGoods[$k]['mods'] = $aMods;                
            }
        }
        return $aGoods;
    }
    
    function get_items_by_cat($cat, $ordby='', $ord='ASC'){        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = "name"; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby == 'vendorName' ? PREF."vendors.name" : ( $ordby == 'price' ? (PREF."goods_modifiers.price") : ( $ordby == 'category_name' ? PREF.'goods_categories.name' : ($ordby == 'users.firstname' ? (PREF."users.firstname") : PREF."goods.".$ordby)))))." $ord";
        $aGoods = db::get("SELECT ".PREF."goods.*, ".PREF."vendors.name AS vendorName, ".PREF."vendors.common AS commonVendor, sales.last_sale, ".PREF."goods_modifiers.price AS price, ".PREF."goods_modifiers.price_pre_roll AS price_pre_roll, ".PREF."goods_categories.name AS category_name, CONCAT(".PREF."users.firstname, ' ', ".PREF."users.lastname) AS added_by_user
                                 FROM ".PREF."goods
                                 LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id
                                 LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                                 LEFT JOIN (SELECT goods_item_id, MAX(date) AS last_sale FROM ".PREF."orders_items GROUP BY goods_item_id) AS sales ON sales.goods_item_id = ".PREF."goods.id
                                 LEFT JOIN ".PREF."goods_modifiers ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                                 LEFT JOIN ".PREF."users ON ".PREF."users.id = ".PREF."goods.added_by
                                 WHERE ".PREF."goods.safe = '0' AND ".PREF."goods.active = '1' AND ".PREF."goods_categories.active = '1' AND ".PREF."goods.in_stock > 0 AND ".PREF."goods.cat_id = '".intval($cat)."' $order_sql");
        if($aGoods){
            foreach($aGoods as $k=>$good){
                $aMods = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($good['id'])."'");
                $aGoods[$k]['mods'] = $aMods;                
            }
        }
        return $aGoods;
    }
    
    function get_safe_goods($cat_id = 0, $ordby='', $ord='ASC', $search_str=''){
        global $aWeights, $aQTY;
        if($cat_id){
            $cat_where = "AND ".PREF."goods.cat_id = '".intval($cat_id)."'";
        }else{
            $cat_where = "";
        }
        if($search_str){
            $searchSQL = "AND ".PREF."goods.name LIKE '%".db::clear(addslashes($search_str))."%'";
        }else{
            $searchSQL = "";
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = "name"; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby == 'vendorName' ? PREF."vendors.name" : ( $ordby == 'price' ? (PREF."goods_modifiers.price") : ( $ordby == 'category_name' ? PREF.'goods_categories.name' : PREF."goods.".$ordby))))." $ord";
        $aGoods = db::get_pager("SELECT ".PREF."goods.*, ".PREF."vendors.name AS vendorName, sales.last_sale, ".PREF."goods_modifiers.price AS price, ".PREF."goods_categories.name AS category_name
                                 FROM ".PREF."goods
                                 LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id
                                 LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                                 LEFT JOIN (SELECT goods_item_id, MAX(date) AS last_sale FROM ".PREF."orders_items GROUP BY goods_item_id) AS sales ON sales.goods_item_id = ".PREF."goods.id
                                 LEFT JOIN ".PREF."goods_modifiers ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                                 WHERE ".PREF."goods.safe = '1' $searchSQL $cat_where $order_sql");
        if($aGoods){
            foreach($aGoods as $k=>$good){
                if($good['measure_type'] == 1){
                    $aParams = $aWeights;
                }else{
                    $aParams = $aQTY;
                }
                foreach($aParams as $p){
                    $aQTYByMods[$p['name']] = db::get_row("SELECT in_stock FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($good['id'])."' AND name = ~~", array($p['name']));
                }
                foreach($aQTYByMods as $name=>$q){                    
                    $aGoods[$k]['mods'][$name]['in_stock'] = floatval($q['in_stock']);  
                    //$aGoods[$k]['mods'][$name]['price'] = floatval($q['price']); 
                    $aGoods[$k]['q_'.$name] = floatval($q['in_stock']);  
                }
               
                $aModifiers = db::get("SELECT * FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($good['id'])."' AND name != '8th'");
                foreach($aModifiers as $m => $mod){
                    $aAddings = $this->get_added($mod['goods_item_id'], $mod['id']);
                    $aModifiers[$m]['added'] = $aAddings;
                }
                $aGoods[$k]['modifiers'] = $aModifiers;  
                
                $aParams = db::get("SELECT * FROM ".PREF."goods_params WHERE goods_item_id = '".intval($good['id'])."'");
                if(!empty($aParams)){
                   $aGoods[$k]['params'] = $aParams;
                }
            }
        }
        return $aGoods;
    }
    
    function update_safe_qty($id, $qty){
        db::query("UPDATE ".PREF."goods SET `starting`='".floatval($qty)."', in_stock='".floatval($qty)."' WHERE id = '".intval($id)."' AND safe = '1'");
        db::query("UPDATE ".PREF."goods_modifiers SET in_stock='".floatval($qty)."' WHERE goods_item_id = '".intval($id)."' AND safe = '1'");
        return true;
    }
    
    function update_safe_purchase_price($id, $amt){
        db::query("UPDATE ".PREF."goods SET `purchase_price`='".floatval($amt)."' WHERE id = '".intval($id)."' AND safe = '1'");
        return true;
    }
    
    function get_total_safe(){
        $amt = db::get_one("SELECT SUM(IF(".PREF."goods.measure_type = 1, purchase_price, purchase_price*in_stock)) FROM ".PREF."goods WHERE safe = '1'");
        return $amt;
    }
    
    function move_safe_to_stock($id){
        db::query("UPDATE ".PREF."goods SET safe = '0' WHERE id = '".intval($id)."'");
        db::query("UPDATE ".PREF."goods_modifiers SET safe = '0' WHERE goods_item_id = '".intval($id)."'");
        return $this->get_goods_item($id);
    }
    
    function get_iou_goods(){
        $aGoods = db::get("SELECT ".PREF."goods.*, ".PREF."goods_categories.name AS category_name, ".PREF."vendors.name AS vendor_name FROM ".PREF."goods
                           LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id         
						   LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                           WHERE ".PREF."goods.iou = '1'
                           ");
        return $aGoods;
    }
    
    function set_iou_status($id,$status){
        if(!empty($status)){
            $s = 1;
        }else{
            $s = 0;
        }
        db::query("UPDATE ".PREF."goods SET iou = '".$s."' WHERE id = '".intval($id)."'");
    }

    function get_iou_history($from, $to,  $ordby='', $ord='ASC', $userId=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        
        if($userId){
            $userSQL = "AND user_id = '".intval($userId)."'";
        }else{
            $userSQL = "";
        }
        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'date'; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aItems = db::get("SELECT ".PREF."iou_history.*, CONCAT(".PREF."users.firstname, ' ', ".PREF."users.lastname) AS user_name  FROM ".PREF."iou_history
                           LEFT JOIN ".PREF."users ON ".PREF."users.id = ".PREF."iou_history.user_id
                           WHERE ".PREF."iou_history.`payout_date` >= '".$from."' AND ".PREF."iou_history.`payout_date` <= '".$to."' $userSQL
                           $order_sql");
        return $aItems;        
    }
    
    function add_iou_history($user_id, $item_id){
        $aProd = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($item_id)."'");
        if(!empty($aProd)){
            $aProdCategory = $this->get_category($aProd['cat_id']);
            $aVendor = $this->get_vendor($aProd['vendor']);
            db::query("INSERT INTO ".PREF."iou_history SET
                        user_id = '".intval($user_id)."',
                        item_id = '".intval($item_id)."',
                        item_name = ~~,
                        cat_id = '".intval($aProd['cat_id'])."',
                        cat_name = ~~,
                        vendor_id = '".intval($aProd['vendor'])."',
                        vendor_name = ~~,
                        amount = '".floatval($aProd['purchase_price'])."',
                        payout_date = '".$this->load_time."',
                        date = ~~
                      ", array($aProd['name'], $aProdCategory['name'], $aVendor['name'], $aProd['purchase_date']));
            return true;
        }else{
            return false;
        }
    }
    
    function remove_from_iou_history($item_id){
        db::query("DELETE FROM ".PREF."iou_history WHERE item_id = '".intval($item_id)."'");
    }
    
    function get_users_goods($user){
        $aGoods = db::get("SELECT * FROM ".PREF."goods WHERE added_by = '".intval($user)."'");
        return $aGoods;
    }
    
    function get_vendors_goods($vendor_id, $ordby='', $ord='ASC', $search_str=''){
        global $aWeights, $aQTY;
        
        if($search_str){
            $searchSQL = "AND ".PREF."goods.name LIKE '%".db::clear(addslashes($search_str))."%'";
        }else{
            $searchSQL = "";
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }  
            if($ordby == "amtInStockSale" or $ordby == "amtInStockPurchase"){
                $ordby = 'modifiers.'.$ordby;
            }
        }else{
            $ordby = "name"; 
            $ord = 'ASC';
        }
        $aCategories = $this->get_categories();
        
        foreach($aCategories as $c=>$cat){
            $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby == 'vendorName' ? PREF."vendors.name" : (PREF."goods.".$ordby)))." $ord";
            $aGoods = db::get("SELECT ".PREF."goods.*, modifiers.amtInStockPurchase, modifiers.amtInStockSale  FROM ".PREF."goods
                               LEFT JOIN (SELECT SUM(in_stock*purchase_price) AS amtInStockPurchase, SUM(in_stock*price) AS amtInStockSale, goods_item_id FROM ".PREF."goods_modifiers GROUP BY goods_item_id) AS modifiers ON modifiers.goods_item_id = ".PREF."goods.id
                               WHERE ".PREF."goods.vendor = '".intval($vendor_id)."' AND ".PREF."goods.cat_id = '".intval($cat['id'])."' $searchSQL  $order_sql");
            if($aGoods){
                foreach($aGoods as $k=>$good){
                    if($good['measure_type'] == 1){
                        $aParams = $aWeights;
                    }else{
                        $aParams = $aQTY;
                    }
                    foreach($aParams as $p){
                        $aQTYByMods[$p['name']] = db::get_one("SELECT in_stock FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($good['id'])."' AND name = ~~", array($p['name']));
                    }
                    foreach($aQTYByMods as $name=>$q){                    
                        $aGoods[$k]['mods'][$name] = floatval($q); 
                        $aGoods[$k]['q_'.$name] = floatval($q);  
                    } 
                }
            }
            $aCategories[$c]['goods'] = $aGoods;
        }
        return $aCategories;
    }
    
    function get_added_products($from, $to, $category=0){
        if($category){
            $catFilter = "AND cat_id = '".intval($category)."'";
        }else{
            $catFilter = "";
        }
        $aProducts = db::get("SELECT ".PREF."goods.*, ".PREF."vendors.name AS vendorname, CONCAT(".PREF."users.firstname, ' ',".PREF."users.lastname) AS addedby FROM ".PREF."goods
                              LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                              LEFT JOIN ".PREF."users ON ".PREF."goods.added_by = ".PREF."users.id
                              WHERE purchase_date >= '".intval($from)."' AND purchase_date <= '".intval($to)."' ".$catFilter);
        return $aProducts;
    }
    
    function get_goods_item($id){
        $aItem = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($id)."'");
        if($aItem){
            $aModifiers = db::get("SELECT * FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($aItem['id'])."' AND name != '8th'");
            foreach($aModifiers as $k => $mod){
                $aAddings = $this->get_added($mod['goods_item_id'], $mod['id']);
                $aModifiers[$k]['added'] = $aAddings;
            }
            $aItem['modifiers'] = $aModifiers;
            $aParams = db::get("SELECT * FROM ".PREF."goods_params WHERE goods_item_id = '".intval($aItem['id'])."'");
            if(!empty($aParams)){
                $aItem['params'] = $aParams;
            }
            $aItem['history'] = db::get("SELECT ".PREF."goods_history.*, CONCAT(".PREF."users.firstname, ' ',".PREF."users.lastname) AS user_name
            	FROM ".PREF."goods_history
            	LEFT JOIN ".PREF."users ON ".PREF."goods_history.updated_by = ".PREF."users.id
            	WHERE item_id = '".intval($aItem['id'])."'");
            return $aItem;
        }else{
            return false;
        }
    }
    
    function get_modifier($item_id, $mod_id){
        $aModifier = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($item_id)."' AND id = '".intval($mod_id)."' AND name != '8th'");
        return $aModifier;
    }
    
    function get_goods_item_param_val($item_id, $param_id){
        $aParam = db::get_row("SELECT * FROM ".PREF."goods_params WHERE goods_item_id = '".intval($item_id)."' AND id = '".intval($param_id)."'");
        return $aParam;
    }
    
    function delete_goods_item($id, $userId = 0){
        $aItem = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($id)."'");        
        $imgToDel = db::get_one("SELECT image FROM ".PREF."goods WHERE id = '".intval($id)."'");
        if(!empty($imgToDel)){
            @unlink(ABS.GALLERY_FOLDER.'/'.$imgToDel);
            @unlink(ABS.GALLERY_FOLDER.'/th_'.$imgToDel);
        }
        $aBarCodesToDel = db::get("SELECT bar_code_image FROM ".PREF."goods_modifiers WHERE goods_item_id='".intval($id)."'");
        if($aBarCodesToDel){
            foreach($aBarCodesToDel as $f){
                if($f['bar_code_image']){
                    @unlink(ABS.GALLERY_FOLDER.'/'.$f['bar_code_image']);
                }
            }
        }
        $ok = db::query("DELETE FROM ".PREF."goods WHERE id = '".intval($id)."'");
        if($ok){ 
            $this->set_current_instock();
            if(!empty($aItem)){
                $aCategory = $this->get_category($aItem['cat_id']);
                db::query("INSERT INTO ".PREF."deleted_goods SET user_id = '".intval($userId)."', name=~~, category=~~, `date`=~~", array($aItem['name'], @$aCategory['name'], $this->load_time));
            }            
            return 'ok';
        }else{
            return 'Database deleting error';
        }
    }
    
    function get_deleted_items($from, $to,  $ordby='', $ord='ASC', $userId=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        
        if($userId){
            $userSQL = "AND user_id = '".intval($userId)."'";
        }else{
            $userSQL = "";
        }
        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'date'; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aItems = db::get("SELECT ".PREF."deleted_goods.*, CONCAT(".PREF."users.firstname, ' ', ".PREF."users.lastname) AS user_name  FROM ".PREF."deleted_goods
                           LEFT JOIN ".PREF."users ON ".PREF."users.id = ".PREF."deleted_goods.user_id
                           WHERE ".PREF."deleted_goods.`date` >= '".$from."' AND ".PREF."deleted_goods.`date` <= '".$to."' $userSQL
                           $order_sql");
        return $aItems;
        
    }
    
    function delete_goods_item_img($id){
        $imgToDel = db::get_one("SELECT image FROM ".PREF."goods WHERE id = '".intval($id)."'");
        if(!empty($imgToDel)){
            @unlink(ABS.GALLERY_FOLDER.'/'.$imgToDel);
            @unlink(ABS.GALLERY_FOLDER.'/th_'.$imgToDel);
        }
        db::query("UPDATE ".PREF."goods SET image = '' WHERE id = '".intval($id)."'");
        return "ok";
    }
    
    function delete_bar_code_img($id, $mod_id){
        $imgToDel = db::get_one("SELECT bar_code_image FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($id)."' AND id = '".intval($mod_id)."'");
        if(!empty($imgToDel)){
            @unlink(ABS.GALLERY_FOLDER.'/'.$imgToDel);
        }
        db::query("UPDATE ".PREF."goods_modifiers SET bar_code_image = '' WHERE goods_item_id = '".intval($id)."'AND id = '".intval($mod_id)."'");
        return "ok";
    }
    
    function search_goods_item_by_id($modifier_id){
        $aItemModifier = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE id = ~~ AND in_stock > 0 AND safe = '0'", array($modifier_id));
        if(!empty($aItemModifier['goods_item_id'])){
            $aItem = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($aItemModifier['goods_item_id'])."' AND safe = '0' LIMIT 1");
            if($aItem['id']){
                $aItem['modifier'] = $aItemModifier;
                global $aAlternativeWeights;
                $aAltWeights = array();
                foreach($aAlternativeWeights as $k=>$aw){
                    if(!empty($aItemModifier['price_'.$k])){
                        $aw['code'] = $k;
                        $aw['price'] = $aItemModifier['price_'.$k];
                        $aAltWeights[] = $aw;
                    }
                }
                $aItem['modifier']['alt'] = $aAltWeights;
                
                return $aItem;
            }else{
                return false;
            }
        }else{
            return false;
        }       
    }
    
    function search_goods_item($barcode){
        $aItemModifier = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE bar_code = ~~ AND in_stock > 0 AND safe = '0'", array($barcode));
        if(!empty($aItemModifier['goods_item_id'])){
            $aItem = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($aItemModifier['goods_item_id'])."' AND safe = '0' LIMIT 1");
            if($aItem['id']){
                $aItem['modifier'] = $aItemModifier;
                global $aAlternativeWeights;
                $aAltWeights = array();
                foreach($aAlternativeWeights as $k=>$aw){
                    if(!empty($aItemModifier['price_'.$k])){
                        $aw['code'] = $k;
                        $aw['price'] = $aItemModifier['price_'.$k];
                        $aAltWeights[] = $aw;
                    }
                }
                $aItem['modifier']['alt'] = $aAltWeights;
                $aParams = db::get("SELECT * FROM ".PREF."goods_params WHERE goods_item_id = '".intval($aItem['id'])."'");
                if(!empty($aParams)){
                    $aItem['params'] = $aParams;
                }
                return $aItem;
            }else{
                return false;
            }
        }else{
            return false;
        }       
    }
    
    function manual_search_goods_item($search_string){
        if($search_string){
            $search_string = addslashes($search_string);
            $aItems = db::get("SELECT ".PREF."goods_modifiers.id AS mod_id,
                                    ".PREF."goods_modifiers.name AS mod_name,
                                    ".PREF."goods_modifiers.bar_code,
                                    ".PREF."goods.id AS item_id,
                                    ".PREF."goods.name AS item_name,
                                    ".PREF."goods_categories.name AS category
                            FROM ".PREF."goods_modifiers
                            LEFT JOIN ".PREF."goods ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                            LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id
                            WHERE (".PREF."goods_modifiers.bar_code LIKE '%".$search_string."%' OR
                                   ".PREF."goods.name LIKE '%".$search_string."%')
                                  AND ROUND(".PREF."goods.in_stock*100) > 0
                                  AND ".PREF."goods_modifiers.name != '8th'
                                  AND ".PREF."goods.active = 1
                                  AND ".PREF."goods.safe = '0'
                                  AND ".PREF."goods_categories.active = 1
                                  ");
            return $aItems;
        }else{
            return false;
        }
    }
    
    function search_goods_by_category($category_id){
        $aItems = db::get("SELECT ".PREF."goods_modifiers.id AS mod_id,
                                    ".PREF."goods_modifiers.name AS mod_name,
                                    ".PREF."goods_modifiers.bar_code,
                                    ".PREF."goods.id AS item_id,
                                    ".PREF."goods.name AS item_name,
                                    ".PREF."goods.meds_type,
                                    ".PREF."goods.in_stock,
                                    ".PREF."vendors.name AS vendor_name,
                                    ".PREF."goods_categories.name AS category
                            FROM ".PREF."goods_modifiers
                            LEFT JOIN ".PREF."goods ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                            LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id
                            LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                            WHERE ".PREF."goods.cat_id = '".intval($category_id)."'
                                  AND ROUND(".PREF."goods.in_stock*100) > 0
                                  AND ".PREF."goods_modifiers.name != '8th'
                                  AND ".PREF."goods.active = 1
                                  AND ".PREF."goods.safe = '0'
                                  AND ".PREF."goods_categories.active = 1
                            ORDER BY ".PREF."goods.name");
        return $aItems;
    }
    
    function check_quantity($itemId, $modId){
        $q = db::get_one("SELECT in_stock FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($itemId)."' AND id = '".$modId."'");
        return $q;
    }
    
    function get_checkout_items(){
        $aItems = db::get("SELECT id, cat_id, name FROM ".PREF."goods WHERE checkout = '1' AND ROUND(in_stock*100) > 0 AND active = '1'");
        if(!empty($aItems)){
            foreach($aItems as &$item){
                $aModifiers = db::get("SELECT * FROM ".PREF."goods_modifiers WHERE goods_item_id = '".$item['id']."' AND ROUND(in_stock*100) > 0 AND name != '8th'");
                
                $item['modifiers'] = $aModifiers;
            }
        }
        return $aItems;
    }
    
    function get_all_checkout_items($search_string){
        if($search_string){
            $search_string = addslashes($search_string);
            $aItems = db::get("SELECT ".PREF."goods_modifiers.*,
                                    ".PREF."goods_modifiers.id AS mod_id,
                                    ".PREF."goods_modifiers.name AS mod_name,
                                    ".PREF."goods.id AS item_id,
                                    ".PREF."goods.cat_id,
                                    ".PREF."goods.name AS item_name,
                                    ".PREF."goods.measure_type,
                                    ".PREF."goods_categories.name AS category
                            FROM ".PREF."goods_modifiers
                            LEFT JOIN ".PREF."goods ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                            LEFT JOIN ".PREF."goods_categories ON ".PREF."goods.cat_id = ".PREF."goods_categories.id
                            WHERE (".PREF."goods_modifiers.bar_code LIKE '%".$search_string."%' OR
                                   ".PREF."goods.name LIKE '%".$search_string."%')
                                  AND ROUND(".PREF."goods.in_stock*100) > 0
                                  AND ".PREF."goods_modifiers.name != '8th'
                                  AND ".PREF."goods.active = 1
                                  AND ".PREF."goods.safe = '0'
                                  AND ".PREF."goods_categories.active = 1
                                  ");
            $aProducts = array();
            if(!empty($aItems)){
                global $aAlternativeWeights;
                foreach($aItems as $item){
                    $item['alt'] = '';
                    $aProducts[] = $item;
                    if($item['measure_type'] == 1){
                        foreach($aAlternativeWeights as $n=>$alt){
                            if(@$item['price_'.$n] > 0 and $item['in_stock'] >= $alt['quantity']){
                                $item['mod_name'] = $alt['name'];
                                $item['name'] = $alt['name'];
                                $item['alt'] = $n;
                                $aProducts[] = $item;
                            }                       
                        }
                    }
                }
            }
            return $aProducts;
        }else{
            return false;
        }
    }
    
    function getOtherPrice($modifier_id, $val){
        $aItemModifier = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE id = '".intval($modifier_id)."'"); 
        global $aAlternativeWeights; 
        $p = $aItemModifier['price'];
        foreach($aAlternativeWeights as $k=>$aw){
            if($k != 'pre_roll'){
                if($aItemModifier['price_'.$k] > 0){
                    if($val >= $aw['quantity']){
                        $p = ceil($aItemModifier['price_'.$k]/$aw['quantity']*100)/100;
                    }
                }
            }
        }
        return $p;
    }
    
    function add_goods_item($aData, $aFiles=null, $userId=0){ //dump($aData);die;
        if(empty($aData['name'])){
           return 'Empty required fields!';
        }
        
        //total in stock calculation
        $inStock = 0;
        if(!empty($aData['modifiers'])){            
            foreach($aData['modifiers'] as $n=>$mod){
                //check barcode
                if(!empty($mod['bar_code'])){
                    $is_barcode = db::get_one("SELECT id FROM ".PREF."goods_modifiers WHERE bar_code = ~~", array($mod['bar_code']));
                    if($is_barcode){
                        return "Bar Code Exists";
                    }
                }
                if($aData['measure_type'] == 1){
                    $inStock+= $mod['in_stock']*$mod['quantity'];
                }else{
                    $inStock = $mod['in_stock'];
                }
            }
        }
        
        $img_sql = "";
        if(!empty($aFiles['photo']['name'])){
            $is_image=preg_match("/^(image\/)[a-zA-Z]{3,4}/",$aFiles['photo']['type']);
            if($is_image){
                $size=getimagesize($aFiles['photo']['tmp_name']);
                if($size[0] > MAX_ALLOWED_IMAGE_WIDTH or $size[1] > MAX_ALLOWED_IMAGE_HEIGHT){
                    return 'Image exceeds allowed size. Max width or height is 2000px';
                }
                $img_name = $this->load_time.translit($aFiles['photo']['name']);
                $th_name = "th_".$img_name;
                $full_name = ABS.GALLERY_FOLDER.'/'.$img_name;
                if(move_uploaded_file($aFiles['photo']['tmp_name'],$full_name)){
                    $img_sql.= " image='".$img_name."',";
                    $this->make_preview(ABS.GALLERY_FOLDER.'/'.$img_name, ABS.GALLERY_FOLDER.'/'.$th_name, 90, 90);
                }
            }
        }
        
        if(!empty($userId)){
            $added_sql = "added_by='".intval($userId)."',";
        }else{
            $added_sql = "";
        }
        
        $ok = db::query("INSERT INTO ".PREF."goods SET
                         cat_id = '".intval($aData['cat_id'])."',
                         $img_sql
                         measure_type = '".intval($aData['measure_type'])."',
                         vendor = '".intval(@$aData['vendor'])."',
                         meds_type = '".intval(isset($aData['meds_type']) ? $aData['meds_type'] : 0)."',
                         in_stock = '".floatval($inStock)."',
                         purchase_price = '".floatval($aData['purchase_price'])."',  
                         `starting` = '".floatval($aData['starting'])."',  
                         purchase_date = '".(strtotime($aData['purchase_date']) ? strtotime($aData['purchase_date']) : $this->load_time)."',
                         name=~~,
                         param_name=~~,
                         allow_comp = '".(isset($aData['allow_comp']) ? 1 : 0)."',
                         checkout = '".(isset($aData['checkout']) ? 1 : 0)."',
                         dont_allow_rewards = '".(isset($aData['dont_allow_rewards']) ? 1 : 0)."',
                         safe = '".(isset($aData['safe']) ? 1 : 0)."',
                         iou = '".(isset($aData['iou']) ? 1 : 0)."',
                         $added_sql
                         discount_type = '".intval(isset($aData['discount_type']) ? $aData['discount_type'] : 1)."',
                         discount_value = '".(!empty($aData['discount_value']) ? floatval($aData['discount_value']) : 0)."',
                         discount_start = '".(!empty($aData['discount_start']) ? strtotime($aData['discount_start']) : 0)."',
                         discount_end = '".(!empty($aData['discount_end']) ? strtotime($aData['discount_end']) : 0)."',
                         note=~~",array($aData['name'], @$aData['param_name'], @$aData['note']));
        $last_id = db::get_last_id();
        if($last_id){
            if(!empty($aData['modifiers'])){ 
                foreach($aData['modifiers'] as $n=>$mod){
                    $bar_code_img_sql = "";
                    if(!empty($aFiles['bar_code_image']['name'][$n])){
                        $is_image=preg_match("/^(image\/)[a-zA-Z]{3,4}/",$aFiles['bar_code_image']['type'][$n]);
                        if($is_image){
                            $size=getimagesize($aFiles['bar_code_image']['tmp_name'][$n]);
                            if($size[0] > MAX_ALLOWED_IMAGE_WIDTH or $size[1] > MAX_ALLOWED_IMAGE_HEIGHT){
                                return 'Image exceeds allowed size. Max width or height is 2000px';
                            }
                            $img_name = $this->load_time.translit($aFiles['bar_code_image']['name'][$n]);
                            $full_name = ABS.GALLERY_FOLDER.'/'.$img_name;
                            if(move_uploaded_file($aFiles['bar_code_image']['tmp_name'][$n],$full_name)){
                                $bar_code_img_sql.= " bar_code_image='".$img_name."',";
                                $this->make_preview(ABS.GALLERY_FOLDER.'/'.$img_name);
                            }
                        }
                    } 
                    if($aData['measure_type'] == 1){
                        if(!empty($inStock) and !empty($mod['quantity'])){
                            $purchase_price = floatval(round($aData['purchase_price']/$inStock*$mod['quantity'],2));
                        }else{
                            $purchase_price = 0;
                        }
                    }else{
                        $purchase_price = floatval(round($aData['purchase_price'],2));
                    }
                    $sql = "INSERT INTO ".PREF."goods_modifiers SET
                               goods_item_id = '".intval($last_id)."',
                               name = ~~,
                               quantity = '".floatval($mod['quantity'])."',
                               bar_code = ~~,".$bar_code_img_sql;
                    
                    global $aAlternativeWeights;
                    foreach($aAlternativeWeights as $k=>$w){
                        if(isset($mod['price_'.$k])){
                            $sql.="price_".$k." = '".floatval($mod['price_'.$k])."',";
                        }
                    }
                    
                    $sql.= "price = '".floatval(@$mod['price'])."',
                               pricemultiple = '".(isset($mod['pricemultiple']) ? floatval($mod['pricemultiple']) : 0)."',
                               allow_comp = '".(isset($aData['allow_comp']) ? 1 : 0)."',
                               in_stock = '".floatval($mod['in_stock'])."',
                               safe = '".(isset($aData['safe']) ? 1 : 0)."',
                               purchase_price = '".$purchase_price."'";
                    
                    $barcode = $last_id;
                    $barcodeLength = strlen($barcode);
                    if($barcodeLength < 13){
                        $diff = 13-$barcodeLength;
                        for($i=0; $i<$diff; $i++){
                            $barcode = '0'.$barcode;
                        }
                    }
                    db::query($sql, array($mod['name'], $barcode));                    
                }
            }
            if(!empty($aData['params_new'])){
                foreach($aData['params_new'] as $param){
                    if(!empty($param['name'])){
                        db::query("INSERT INTO ".PREF."goods_params SET goods_item_id = '".intval($last_id)."', name=~~, qty='".intval($param['qty'])."'", array($param['name']));
                    }
                }
            }
            $this->set_current_instock();            
            return $last_id;
        }else{
            return "Database insert error";
        }
    }
    
    function duplicate_goods_item($id, $startVal=null, $userId=0){
        $aData = $this->get_goods_item($id);
        if(!empty($aData)){
            if(is_null($startVal)){
                $starting = $aData['starting'];
            }else{
                $starting = floatval($startVal);
            }
            
            if(!empty($userId)){
                $added_sql = "added_by='".intval($userId)."',";
            }else{
                $added_sql = "";
            }
            
            $ok = db::query("INSERT INTO ".PREF."goods SET
                             cat_id = '".intval($aData['cat_id'])."',
                             measure_type = '".intval($aData['measure_type'])."',
                             vendor = '".intval($aData['vendor'])."',
                             meds_type = '".intval(isset($aData['meds_type']) ? $aData['meds_type'] : 0)."',
                             in_stock = '".$starting."',
                             purchase_price = '".floatval($aData['purchase_price'])."',  
                             `starting` = '".$starting."',  
                             purchase_date = '".$this->load_time."',
                             name=~~,
                             param_name=~~,
                             allow_comp = '".(!empty($aData['allow_comp']) ? 1 : 0)."',
                             checkout = '".(!empty($aData['checkout']) ? 1 : 0)."',
                             dont_allow_rewards = '".(!empty($aData['dont_allow_rewards']) ? 1 : 0)."',
                             note=~~,
                             safe = '".(!empty($aData['safe']) ? 1 : 0)."',
                             iou = '".(!empty($aData['iou']) ? 1 : 0)."',
                             $added_sql
                             discount_type = '".intval($aData['discount_type'])."',
                             discount_value = '".floatval($aData['discount_value'])."',
                             discount_start = '".$aData['discount_start']."',
                             discount_end = '".$aData['discount_end']."',
                             active = '".(!empty($aData['active']) ? 1 : 0)."'",
                             array($aData['name'], @$aData['param_name'], $aData['note']));
            $last_id = db::get_last_id();
            if($last_id){
                if(!empty($aData['modifiers'])){ 
                    $barcode = $last_id;
                    $barcodeLength = strlen($barcode);
                    if($barcodeLength < 13){
                        $diff = 13-$barcodeLength;
                        for($i=0; $i<$diff; $i++){
                            $barcode = '0'.$barcode;
                        }
                    }
                    foreach($aData['modifiers'] as $n=>$mod){
                        $sql = "INSERT INTO ".PREF."goods_modifiers SET
                                   goods_item_id = '".intval($last_id)."',
                                   name = ~~,
                                   quantity = '".floatval($mod['quantity'])."',
                                   bar_code = ~~,
                                   bar_code_image = '',
                                   price = '".floatval($mod['price'])."',
                                   pricemultiple = '".floatval($mod['pricemultiple'])."',
                                   ";
                        global $aAlternativeWeights;
                        foreach($aAlternativeWeights as $k=>$w){
                            if(isset($mod['price_'.$k])){
                                $sql.="price_".$k." = '".floatval($mod['price_'.$k])."',";
                            }
                        }
                        $sql.= "allow_comp = '".(!empty($aData['allow_comp']) ? 1 : 0)."',
                                in_stock = '".$starting."',
                                safe = '".(!empty($aData['safe']) ? 1 : 0)."',
                                purchase_price = '".floatval($mod['purchase_price'])."'";

                        db::query($sql, array($mod['name'], $barcode));
                    }
                }
                $this->set_current_instock();
                return $last_id;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function update_goods_item($id, $aData, $aFiles=null, $userId=0){ //dump($aData);die;
        if(empty($aData['name'])){
           return 'Empty required fields!';
        }
        
        $aCurrentState = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($id)."'");
        
        //check bar code exists
        if(!empty($aData['modifiers'])){            
            foreach($aData['modifiers'] as $n=>$mod){
                //check barcode
                if(!empty($mod['bar_code'])){
                    $is_barcode = db::get_one("SELECT id FROM ".PREF."goods_modifiers WHERE bar_code = ~~ AND id != '".intval($n)."'", array($mod['bar_code']));
                    if($is_barcode){
                        return "Bar Code Exists";
                    }
                }                
            }
        }
        
        if($aCurrentState['in_stock'] <= 0 or $aData['measure_type'] == 2){
            $stockDataSQL = "purchase_price = '".floatval($aData['purchase_price'])."',";
            //total in stock calculation
            $inStock = 0;
            if(!empty($aData['modifiers'])){            
                foreach($aData['modifiers'] as $n=>$mod){                    
                    if($aData['measure_type'] == 1){
                        $inStock+= $mod['in_stock']*$mod['quantity'];
                    }else{
                        $inStock = $mod['in_stock'];
                    }
                }
                $stockDataSQL.= "in_stock = '".floatval($inStock)."',";
            }
        }else{
            $stockDataSQL = '';
        }
        
        $img_sql = "";
        if(!empty($aFiles['photo']['name'])){
            $is_image=preg_match("/^(image\/)[a-zA-Z]{3,4}/",$aFiles['photo']['type']);
            if($is_image){
                $size=getimagesize($aFiles['photo']['tmp_name']);
                if($size[0] > MAX_ALLOWED_IMAGE_WIDTH or $size[1] > MAX_ALLOWED_IMAGE_HEIGHT){
                    return 'Image exceeds allowed size. Max width or height is 2000px';
                }
                $img_name = $this->load_time.translit($aFiles['photo']['name']);
                $th_name = "th_".$img_name;
                $full_name = ABS.GALLERY_FOLDER.'/'.$img_name;
                if(move_uploaded_file($aFiles['photo']['tmp_name'],$full_name)){
                    $imgToDel = $aCurrentState['image'];
                    if(!empty($imgToDel)){
                        @unlink(ABS.GALLERY_FOLDER.'/'.$imgToDel);
                        @unlink(ABS.GALLERY_FOLDER.'/th_'.$imgToDel);
                    }
                    $img_sql.= " image='".$img_name."',";
                    $this->make_preview(ABS.GALLERY_FOLDER.'/'.$img_name, ABS.GALLERY_FOLDER.'/'.$th_name, 90, 90);
                }
            }
        } 
        
        if(!empty($userId)){
            $added_sql = "added_by='".intval($userId)."',";
        }else{
            $added_sql = "";
        }
        
        $ok = db::query("UPDATE ".PREF."goods SET
                     cat_id = '".intval($aData['cat_id'])."',
                     $img_sql
                     vendor = '".intval($aData['vendor'])."', 
                     meds_type = '".intval(isset($aData['meds_type']) ? $aData['meds_type'] : 0)."',
                     $stockDataSQL  
                     purchase_date = '".(strtotime($aData['purchase_date']) ? strtotime($aData['purchase_date']) : $this->load_time)."',
                     name=~~,
                     param_name=~~,
                     allow_comp = '".(isset($aData['allow_comp']) ? 1 : 0)."',
                     checkout = '".(isset($aData['checkout']) ? 1 : 0)."',
                     dont_allow_rewards = '".(isset($aData['dont_allow_rewards']) ? 1 : 0)."',
                     safe = '".(isset($aData['safe']) ? 1 : 0)."',
                     iou = '".(isset($aData['iou']) ? 1 : 0)."',
                     $added_sql
                     discount_type = '".(isset($aData['discount_type']) ? intval($aData['discount_type']) : 1)."',
                     discount_value = '".(!empty($aData['discount_value']) ? floatval($aData['discount_value']) : 0)."',
                     discount_start = '".(!empty($aData['discount_start']) ? strtotime($aData['discount_start']) : 0)."',
                     discount_end = '".(!empty($aData['discount_end']) ? strtotime($aData['discount_end']) : 0)."',
                     note=~~
                     WHERE id = '".intval($id)."'",array($aData['name'], @$aData['param_name'], $aData['note']));
        if(!empty($aData['modifiers'])){
            foreach($aData['modifiers'] as $n=>$mod){
                /*
                $bar_code_img_sql = "";
                if(!empty($aFiles['bar_code_image']['name'][$n])){
                    $is_image=preg_match("/^(image\/)[a-zA-Z]{3,4}/",$aFiles['bar_code_image']['type'][$n]);
                    if($is_image){
                        $size=getimagesize($aFiles['bar_code_image']['tmp_name'][$n]);
                        if($size[0] > MAX_ALLOWED_IMAGE_WIDTH or $size[1] > MAX_ALLOWED_IMAGE_HEIGHT){
                            return 'Image exceeds allowed size. Max width or height is 2000px';
                        }
                        $img_name = $this->load_time.translit($aFiles['bar_code_image']['name'][$n]);
                        $full_name = ABS.GALLERY_FOLDER.'/'.$img_name;
                        if(move_uploaded_file($aFiles['bar_code_image']['tmp_name'][$n],$full_name)){
                            $imgToDel = db::get_one("SELECT bar_code_image FROM ".PREF."goods_modifiers WHERE goods_item_id = '".intval($id)."' AND id = '".intval($n)."'");
                            if(!empty($imgToDel)){
                                @unlink(ABS.GALLERY_FOLDER.'/'.$imgToDel);
                            }
                            $bar_code_img_sql.= " bar_code_image='".$img_name."',";
                            $this->make_preview(ABS.GALLERY_FOLDER.'/'.$img_name);
                        }
                    }
                }
                */
                if($aCurrentState['in_stock'] <= 0 or $aData['measure_type'] == 2){
                    if($aData['measure_type'] == 1){
                        if($inStock > 0 and $mod['quantity']){
                            $purchase_price = floatval(round($aData['purchase_price']/$inStock*$mod['quantity'],2));
                        }else{
                            $purchase_price = 0;
                        }
                    }else{
                        $purchase_price = floatval(round($aData['purchase_price'],2));
                    }
                    $stockModifierSQL = "in_stock = '".floatval($mod['in_stock'])."',
                                         purchase_price = '".$purchase_price."',";
                }else{
                    $stockModifierSQL = '';
                }
                
                $sql = "UPDATE ".PREF."goods_modifiers SET ".$stockModifierSQL;

                global $aAlternativeWeights;
                foreach($aAlternativeWeights as $k=>$w){
                    if(isset($mod['price_'.$k])){
                        $sql.="price_".$k." = '".floatval($mod['price_'.$k])."',";
                    }
                }

                $sql.="price = '".floatval($mod['price'])."',
                   pricemultiple = '".(isset($mod['pricemultiple']) ? floatval($mod['pricemultiple']) : 0)."',
                   bar_code = ~~,
                   safe = '".(isset($aData['safe']) ? 1 : 0)."',
                   allow_comp = '".(isset($aData['allow_comp']) ? 1 : 0)."'
                   WHERE id = '".intval($n)."'";
                $barcode = $id;
                $barcodeLength = strlen($barcode);
                if($barcodeLength < 13){
                    $diff = 13-$barcodeLength;
                    for($i=0; $i<$diff; $i++){
                        $barcode = '0'.$barcode;
                    }
                }
                db::query($sql, array($barcode));   
                             
            }
        }
        if(!empty($aData['params_new'])){
            foreach($aData['params_new'] as $param){
                if(!empty($param['name'])){
                    db::query("INSERT INTO ".PREF."goods_params SET goods_item_id = '".intval($id)."', name=~~, qty='".intval($param['qty'])."'", array($param['name']));
                }
            }
        }
        if(!empty($aData['params'])){
            foreach($aData['params'] as $param_id=>$param){
                if(!empty($param['name'])){
                    db::query("UPDATE ".PREF."goods_params SET name=~~, qty='".intval($param['qty'])."' WHERE goods_item_id = '".intval($id)."' AND id = '".intval($param_id)."'", array($param['name']));
                }else{
                    db::query("DELETE FROM ".PREF."goods_params WHERE goods_item_id = '".intval($id)."' AND id = '".intval($param_id)."'");
                }
            }
        }
        if($ok){
            $this->set_current_instock();
            return "ok";
        }else{
            return "Database updating error";
        }
    }
    
    function setPreRollPrice($id, $price){
        $result = db::query("UPDATE ".PREF."goods_modifiers SET price_pre_roll = '".floatval($price)."' WHERE goods_item_id = '".intval($id)."'");
        if($result){
            return true;
        }else{
            return false;
        }
    }
    
    function updatePrice($itemId, $prices, $user_id = 0){
        if(is_array($prices)){
            global $aAlternativeWeights;
            $this->logPriceUpdating($user_id, $itemId, $prices['gram']);
            $sql = "UPDATE ".PREF."goods_modifiers SET ";
            foreach($aAlternativeWeights as $k=>$w){
                if(isset($prices[$k])){
                    $sql.="price_".$k." = '".floatval($prices[$k])."', ";
                }
            }    
            $sql.= "price = '".floatval($prices['gram'])."' ";
            $sql.= "WHERE goods_item_id = '".intval($itemId)."' ";            
            db::query($sql);           
            return true;
        }else{
            $this->logPriceUpdating($user_id, $itemId, $prices);
            $sql = "UPDATE ".PREF."goods_modifiers SET price = '".floatval($prices)."' WHERE goods_item_id = '".intval($itemId)."'";
            db::query($sql);
            return true;
        }
    }
    
    function logPriceUpdating($user_id, $item_id, $new_price, $old_price=0){
        $aItem = $this->get_goods_item($item_id);        
        if(!$old_price){
            $old_price = $aItem['modifiers'][0]['price'];
        }
        if($old_price != $new_price){
            $catName = $this->get_category($aItem['cat_id']);
            db::query("INSERT INTO ".PREF."change_price_history SET
                    user_id = '".intval($user_id)."',
                    item_id = '".intval($item_id)."',
                    item_name = ~~,
                    cat_id = '".intval($aItem['cat_id'])."',
                    cat_name = ~~,
                    old_price = '".floatval($old_price)."',
                    new_price = '".floatval($new_price)."',
                    date = '".$this->load_time."'
                 ", array($aItem['name'], $catName['name']));
        }
        return true;
    }
    
    function getPriceUpdatingLog($from, $to,  $ordby='', $ord='ASC', $userId=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        
        if($userId){
            $userSQL = "AND user_id = '".intval($userId)."'";
        }else{
            $userSQL = "";
        }
        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'date'; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aItems = db::get("SELECT ".PREF."change_price_history.*, CONCAT(".PREF."users.firstname, ' ', ".PREF."users.lastname) AS user_name  FROM ".PREF."change_price_history
                           LEFT JOIN ".PREF."users ON ".PREF."users.id = ".PREF."change_price_history.user_id
                           WHERE ".PREF."change_price_history.`date` >= '".$from."' AND ".PREF."change_price_history.`date` <= '".$to."' $userSQL
                           $order_sql");
        return $aItems;        
    }
    
    function logItemTransfer($user_id, $item_id, $value){
        $aItem = $this->get_goods_item($item_id);
        if(!empty($aItem)){
            $catName = $this->get_category($aItem['cat_id']);
            db::query("INSERT INTO ".PREF."transfer_history SET
                    user_id = '".intval($user_id)."',
                    item_id = '".intval($item_id)."',
                    item_name = ~~,
                    cat_id = '".intval($aItem['cat_id'])."',
                    cat_name = ~~,
                    transfered_value = '".floatval($value)."',
                    date = '".$this->load_time."'
                 ", array($aItem['name'], $catName['name']));
            return true;
        }else{
            return false;
        }
    }
    
    function getProductTotalTransfers($id){
        $transferedValue = db::get_one("SELECT SUM(transfered_value) FROM ".PREF."transfer_history WHERE item_id = '".intval($id)."'");
        return $transferedValue;
    }
    
    function itemToZero($item_id){
        $aData = $this->get_goods_item($item_id);
        if(!empty($aData)){
            $newInStock = 0;
            db::query("UPDATE ".PREF."goods_modifiers SET in_stock = '".floatval($newInStock)."' WHERE goods_item_id = '".intval($item_id)."'");
            db::query("UPDATE ".PREF."goods SET in_stock = '".floatval($newInStock)."' WHERE id = '".intval($item_id)."'");
            return true;
        }else{
            return false;
        }
    }
    
    function setItemInStock($item_id, $instock=0){
        $aData = $this->get_goods_item($item_id);
        if(!empty($aData)){
            $newInStock = $instock;
            db::query("UPDATE ".PREF."goods_modifiers SET in_stock = '".floatval($newInStock)."' WHERE goods_item_id = '".intval($item_id)."'");
            db::query("UPDATE ".PREF."goods SET in_stock = '".floatval($newInStock)."' WHERE id = '".intval($item_id)."'");
        }else{
            return false;
        }
    }
    
    function itemLosses($item_id, $losses, $userId=0){
        if($losses > 0){
            $aData = $this->get_goods_item($item_id); //dump($aData);die;
            if(!empty($aData)){
                $aCategory = $this->get_category($aData['cat_id']);
                $catName = @$aCategory['name'];
                if(!empty($aData['modifiers'])) foreach($aData['modifiers'] as $mod){
                    db::query("INSERT INTO ".PREF."goods_losses SET
                                    goods_item_id = '".intval($aData['id'])."',
                                    modifier_id = '".intval($mod['id'])."',
                                    goods_name = ~~,
                                    modifier_name = ~~,
                                    category_id = '".intval($aData['cat_id'])."',
                                    category_name = ~~,
                                    starting_date = ~~,
                                    starting_value = '".floatval($aData['starting'])."',
                                    purchase_price = '".floatval($aData['purchase_price'])."',
                                    loss_value = '".floatval($losses)."',
                                    loss_date = '".$this->load_time."'
                                    ",
                             array($aData['name'], $mod['name'], $catName, $aData['purchase_date']), true);
                }
                if($aData['modifiers'][0]['in_stock'] <= $losses){
                    $this->delete_goods_item($item_id, $userId);
                }else{
                    $newInStock = $aData['modifiers'][0]['in_stock'] - $losses;
                    db::query("UPDATE ".PREF."goods_modifiers SET in_stock = '".floatval($newInStock)."' WHERE goods_item_id = '".intval($item_id)."'");
                    db::query("UPDATE ".PREF."goods SET in_stock = '".floatval($newInStock)."' WHERE id = '".intval($item_id)."'");
                }
            }else{
                return false;
            }
            
        }        
        return true;
    }    
    
    function getLosses($cat, $from, $to, $ordby='', $ord='ASC'){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'goods_name'; 
            $ord = 'ASC';
        }
        if($cat){
            $cat_filter = "AND category_id = '".intval($cat)."'";
        }else{
            $cat_filter = "";
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        $aLosses = db::get("SELECT *, IF(modifier_name = 'qty', purchase_price*loss_value, purchase_price/starting_value*loss_value) AS loss_amt FROM ".PREF."goods_losses WHERE loss_date >= '".$from."' AND loss_date <= '".$to."' ".$cat_filter." ".$order_sql);
        return $aLosses;
    }
    
    function getProductLosses($id){
        $aLosses = db::get("SELECT SUM(loss_value) AS lost_qty, IF(modifier_name = 'qty', purchase_price*loss_value, purchase_price/starting_value*loss_value) AS loss_amt, modifier_name FROM ".PREF."goods_losses WHERE goods_item_id = '".intval($id)."' GROUP BY modifier_id");
        return $aLosses;
    }
    
    function get_losses_categories(){
        $aResult = db::get("SELECT DISTINCT category_id, category_name FROM ".PREF."goods_losses");
        return $aResult;
    }
    
    function removeLoss($loss_id){
        db::query("DELETE FROM ".PREF."goods_losses WHERE id = '".intval($loss_id)."'");
        return true;
    }
    
    function add_to_stock($goods_id, $mod_id, $val){        
        $inStock = floatval($val);
        db::query("UPDATE ".PREF."goods SET in_stock = in_stock + $inStock, `starting` = `starting` + $inStock WHERE id = '".intval($goods_id)."'");        
        db::query("UPDATE ".PREF."goods_modifiers SET in_stock = in_stock + ".$inStock." WHERE goods_item_id = '".intval($goods_id)."' AND id = '".intval($mod_id)."'");
        db::query("INSERT INTO ".PREF."goods_additions SET goods_id = '".intval($goods_id)."', modifier_id = '".intval($mod_id)."', addedvalue = '".$inStock."', addeddate = '".$this->load_time."'");
        $this->set_current_instock();
        return true;
    }
    
    function get_added($goods_id, $mod_id){
        $aItems = db::get("SELECT * FROM ".PREF."goods_additions WHERE goods_id = '".intval($goods_id)."' AND modifier_id = '".intval($mod_id)."'");
        return $aItems;
    }
    
    function get_stock($ordby='', $ord='ASC'){
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = "name"; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        $aGoods = db::get("SELECT ".PREF."goods.id, ".PREF."goods.name, modifiers.amtInStockPurchase, modifiers.amtInStockSale, ".PREF."goods.in_stock, orders.lastSale
                           FROM ".PREF."goods 
                           LEFT JOIN (SELECT MAX(date) AS lastSale, goods_item_id FROM ".PREF."orders_items GROUP BY goods_item_id) AS orders ON orders.goods_item_id = ".PREF."goods.id
                           LEFT JOIN (SELECT SUM(in_stock*purchase_price) AS amtInStockPurchase, SUM(in_stock*price) AS amtInStockSale, goods_item_id FROM ".PREF."goods_modifiers GROUP BY goods_item_id) AS modifiers ON modifiers.goods_item_id = ".PREF."goods.id
                           $order_sql");        
        return $aGoods;
    }
    
    function set_current_instock(){
        $ct = getdate();
        $day = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
        $aCategories = $this->get_categories();
        foreach($aCategories as &$cat){
            $aStock = $this->get_stock_by_cat($cat['id']);
            $amtInStockPurchase = $aStock['amtInStockPurchase'];
            $amtInStockSale = $aStock['amtInStockSale'];
            $aRecord = db::get_row("SELECT * FROM ".PREF."instock_history WHERE day = ~~ AND category = ~~", array($day, $cat['name']));
            if(!empty($aRecord['id'])){
                if(($aRecord['amount_purchase'] != $amtInStockPurchase or $aRecord['amount_sale'] != $amtInStockSale)){                
                    db::query("UPDATE ".PREF."instock_history SET amount_purchase = '".floatval($amtInStockPurchase)."', amount_sale = '".floatval($amtInStockSale)."' WHERE day = ~~ AND category = ~~", array($day, $cat['name']));
                }                    
            }else{
                db::query("INSERT INTO ".PREF."instock_history SET category = ~~, amount_purchase = '".floatval($amtInStockPurchase)."', amount_sale = '".floatval($amtInStockSale)."', day = ~~", array($cat['name'], $day));
            }
        }
        return true;
    }
    
    function get_daily_instock($day, $categoryName=''){
        if($categoryName){
            $aData = db::get("SELECT * FROM ".PREF."instock_history WHERE day = ~~ AND category = ~~", array($day, $categoryName));
        }else{
            $aData = db::get("SELECT * FROM ".PREF."instock_history WHERE day = ~~", array($day));
        }        
        return $aData;
    }
    
    function get_instock_categories(){
        return db::get("SELECT DISTINCT category FROM ".PREF."instock_history");
    }
    
    function get_stock_by_cat($catId){
        $aStock = db::get_row("SELECT modifiers.amtInStockPurchase, modifiers.amtInStockSale
                               FROM ".PREF."goods
                               LEFT JOIN (SELECT ROUND(SUM(".PREF."goods_modifiers.in_stock*".PREF."goods_modifiers.purchase_price),2) AS amtInStockPurchase, ROUND(SUM(".PREF."goods_modifiers.in_stock*".PREF."goods_modifiers.price),2) AS amtInStockSale, ".PREF."goods.cat_id, ".PREF."goods_modifiers.goods_item_id
                                          FROM ".PREF."goods_modifiers
                                          LEFT JOIN ".PREF."goods ON ".PREF."goods.id = ".PREF."goods_modifiers.goods_item_id
                                          GROUP BY ".PREF."goods.cat_id) AS modifiers ON modifiers.goods_item_id = ".PREF."goods.id
                               WHERE ".PREF."goods.cat_id = '".intval($catId)."' AND ".PREF."goods.safe = 0");
        return $aStock;
    }
    
    function get_common_vendors(){
        include_once 'admin_config.php';
        db::connect($aConfAdmin);
        $aVendors = db::get("SELECT * FROM ".PREF."accounts WHERE version = '3' AND status != '0'");
        global $aConf;
        db::connect($aConf);
        return $aVendors;
    }
    
    function add_common_vendors(){
        $aVendors = $this->get_common_vendors();
        if(!empty($aVendors)){
            foreach($aVendors as $vnd){
                $is_vendor = db::get_one("SELECT id FROM ".PREF."vendors WHERE common = ~~", array($vnd['folder']));
                if(!$is_vendor){
                    db::query("INSERT INTO ".PREF."vendors SET
                                    name = ~~,
                                    phone = ~~,
                                    email = ~~,
                                    contact_person = ~~,
                                    common = ~~",
                                array($vnd['name'],$vnd['phone'],$vnd['admin_email'],'', $vnd['folder']));
                }else{
                    db::query("UPDATE ".PREF."vendors SET
                                    name = ~~,
                                    phone = ~~,
                                    email = ~~,
                                    contact_person = ~~
                              WHERE common = ~~",
                                array($vnd['name'],$vnd['phone'],$vnd['admin_email'],'', $vnd['folder']));
                }
            }
            return true;
        }else{
            return false;
        }
    }
    
    function get_vendors(){
        $aVendors = db::get("SELECT * FROM ".PREF."vendors");
        return $aVendors;
    }
    
     function get_vendor($id){
        $aVendor = db::get_row("SELECT * FROM ".PREF."vendors WHERE id = '".intval($id)."'");
        return $aVendor;
    }
    
    function add_vendor($aData, $returnId=false){
        if(empty($aData['name'])){
           return 'Empty required fields!';
        }        
        $ok = db::query("INSERT INTO ".PREF."vendors SET
                  name = ~~,
                  phone = ~~,
                  email = ~~,
                  contact_person = ~~",
            array($aData['name'],$aData['phone'],$aData['email'],$aData['contact_person']));
        if($ok){
            if($returnId){
                return mysql_insert_id();
            }else{
                return 'ok';
            }
        }else{
            return 'Database inserting error';
        }
    }
    
    function update_vendor($id, $aData){
        if(empty($aData['name'])){
           return 'Empty required fields!';
        }        
        $ok = db::query("UPDATE ".PREF."vendors SET
                  name = ~~,
                  phone = ~~,
                  email = ~~,
                  contact_person = ~~ 
                  WHERE id = '".intval($id)."'",
            array($aData['name'],$aData['phone'],$aData['email'],$aData['contact_person']));
        if($ok){
            return 'ok';
        }else{
            return 'Database updating error';
        }
    }
    
    function delete_vendor($id){
        $ok = db::query("DELETE FROM ".PREF."vendors WHERE id = '".intval($id)."'");
        if($ok){
            return 'ok';
        }else{
            return 'Database deleting error';
        }
    }

    function get_invoice_categories(){
        return db::get("SELECT * FROM ".PREF."invoice_category", null, false, false, true);
    }
    
    function add_invoice_category($name){
    	if(!name) return "Empty category name";
    	$ok = db::query("INSERT INTO ".PREF."invoice_category SET name = ~~",array($name));
        if(!$ok) return "Database inserting error";
        return "ok";
    }
    
    function add_invoice($aData, $aFiles, $employee_id){
        if(empty($aFiles['file']['name'])){
            return "Required fields are empty";
        }        
        $file_sql = "";
        $is_image = preg_match("/^(image\/)[a-zA-Z]{3,4}/",$aFiles['file']['type']);
        $is_pdf = strpos($aFiles['file']['name'], ".pdf");
        $is_doc = (strpos($aFiles['file']['name'], ".doc") or strpos($aFiles['file']['name'], ".docx") or strpos($aFiles['file']['name'], ".xls") or strpos($aFiles['file']['name'], ".xlsx"));
        if($is_image or $is_pdf or $is_doc){            
            $file_name = $this->load_time.translit($aFiles['file']['name']);
            $full_name = ABS.GALLERY_FOLDER.'/'.$file_name;
            if(move_uploaded_file($aFiles['file']['tmp_name'],$full_name)){
                $file_sql.= "`file`='".$file_name."',";                
            }
        }else{
            return "Unsupported file format";
        }        
        if($file_sql){
            $vendorName = db::get_one("SELECT name FROM ".PREF."vendors WHERE id = '".intval($aData['vendor'])."'");
            db::query("INSERT INTO ".PREF."invoices SET
                         `vendor_id` = '".intval($aData['vendor'])."',
                         $file_sql  
                         `goods_category_id` = '".intval($aData['category'])."',
                         `category_id` = '".intval($aData['invoice_category'])."',
                         `employee_id` = '".intval($employee_id)."',
                         `quantity` = '".floatval($aData['quantity'])."',
                         `name` = ~~,
                         `vendor_name`=~~,
                         `date`=~~",array($aData['name'], !empty($vendorName) ? $vendorName : '', $this->load_time));
            $last_id = db::get_last_id();
            if($last_id){
                return "ok";
            }else{
                return "Database inserting error";
            }
        }else{
            return "File uploading error";
        }
    }
    
    function get_invoices($from, $to, $vendor=0, $category=null){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($vendor){
            $vendorSQL = "AND vendor_id = '".intval($vendor)."'";
        }else{
            $vendorSQL = "";
        }
        $categorySQL = "";
        if($category) $categorySQL = "AND category_id = '".intval($category)."'";
        $aInvoices = db::get("SELECT ".PREF."invoices.*, CONCAT(".PREF."users.firstname, ' ', ".PREF."users.lastname) AS employee_name, ".PREF."goods_categories.name AS goods_category_name, ".PREF."invoice_category.name AS category_name
                              FROM ".PREF."invoices
                              LEFT JOIN ".PREF."goods_categories ON ".PREF."goods_categories.id = ".PREF."invoices.goods_category_id
                              LEFT JOIN ".PREF."invoice_category ON ".PREF."invoice_category.id = ".PREF."invoices.category_id
                              LEFT JOIN ".PREF."users ON ".PREF."users.id = ".PREF."invoices.employee_id
                              WHERE ".PREF."invoices.date >= ~~ AND ".PREF."invoices.date <= ~~ $vendorSQL $categorySQL", array($from, $to)); 
        return $aInvoices;
    }
    
    function delete_invoice($id){
        $fileToDel = db::get_one("SELECT file FROM ".PREF."invoices WHERE id = '".intval($id)."'");
        if(!empty($fileToDel)){
            @unlink(ABS.GALLERY_FOLDER.'/'.$fileToDel);
        }
        
        $ok = db::query("DELETE FROM ".PREF."invoices WHERE id = '".intval($id)."'");
        if($ok){            
            return 'ok';
        }else{
            return 'Database deleting error';
        }
    }
    
    function get_preset_prices(){
        $aPrices = db::get("SELECT * FROM ".PREF."prices");
        return $aPrices;
    }
    
    function get_preset_price($id){
        $aPrice = db::get_row("SELECT * FROM ".PREF."prices WHERE id = '".intval($id)."'");
        return $aPrice;
    }
    
    function add_preset_prices($aData){
        if(!empty($aData) and isset($aData['eighth'])){
            db::query("INSERT INTO ".PREF."prices SET
                        eighth = '".floatval($aData['eighth'])."',
                        gram = '".floatval($aData['gram'])."',
                        fourth = '".floatval($aData['fourth'])."',
                        half = '".floatval($aData['half'])."',
                        oz = '".floatval($aData['oz'])."',
                        halfeighth = '".floatval($aData['halfeighth'])."',
                        twograms = '".floatval($aData['twograms'])."',
                        fourgrams = '".floatval($aData['fourgrams'])."',
                        fivegrams = '".floatval($aData['fivegrams'])."'
                      ");
            return true;
        }else{
            return false;
        }
    }
    
    function edit_preset_prices($id, $aData){
        if(!empty($aData) and isset($aData['eighth'])){
            db::query("UPDATE ".PREF."prices SET
                        eighth = '".floatval($aData['eighth'])."',
                        gram = '".floatval($aData['gram'])."',
                        fourth = '".floatval($aData['fourth'])."',
                        half = '".floatval($aData['half'])."',
                        oz = '".floatval($aData['oz'])."',
                        halfeighth = '".floatval($aData['halfeighth'])."',
                        twograms = '".floatval($aData['twograms'])."',
                        fourgrams = '".floatval($aData['fourgrams'])."',
                        fivegrams = '".floatval($aData['fivegrams'])."'
                      WHERE id = '".intval($id)."'
                      ");
            return true;
        }else{
            return false;
        }
    }
    
    function delete_preset_prices($id){
        db::query("DELETE FROM ".PREF."prices WHERE id = '".intval($id)."'");
        return true;
    }
    
    function get_preset_qty_prices(){
        $aPrices = db::get("SELECT ".PREF."qty_prices.*, ".PREF."vendors.name AS vendor_name FROM ".PREF."qty_prices LEFT JOIN ".PREF."vendors ON ".PREF."vendors.id = ".PREF."qty_prices.vendor_id ");
        return $aPrices;
    }
    
    function get_preset_qty_price($id){
        $aPrice = db::get_row("SELECT * FROM ".PREF."qty_prices WHERE id = '".intval($id)."'");
        return $aPrice;
    }
    
    function get_preset_qty_prices_by_vendor($vendor_id){
        $aPrices = db::get("SELECT product_name, price, selling_price FROM ".PREF."qty_prices WHERE vendor_id = '".intval($vendor_id)."'");
        return $aPrices;
    }
    
    function add_preset_qty_prices($aData){
        if(!empty($aData) and isset($aData['product_name'])){
            db::query("INSERT INTO ".PREF."qty_prices SET
                        vendor_id = '".floatval($aData['vendor_id'])."',
                        product_name = ~~,
                        price = '".floatval($aData['price'])."',
                        selling_price = '".floatval($aData['selling_price'])."'
                      ", array($aData['product_name']));
            return true;
        }else{
            return false;
        }
    }
    
    function edit_preset_qty_prices($id, $aData){
        if(!empty($aData) and isset($aData['product_name'])){
            db::query("UPDATE ".PREF."qty_prices SET
                        vendor_id = '".floatval($aData['vendor_id'])."',
                        product_name = ~~,
                        price = '".floatval($aData['price'])."',
                        selling_price = '".floatval($aData['selling_price'])."'
                      WHERE id = '".intval($id)."'
                      ", array($aData['product_name']));
            return true;
        }else{
            return false;
        }
    }
    
    function delete_preset_qty_prices($id){
        db::query("DELETE FROM ".PREF."qty_prices WHERE id = '".intval($id)."'");
        return true;
    }
    
    function set_price_mode($category_id, $mode){
        db::query("UPDATE ".PREF."goods_categories SET set_price = '".intval($mode)."'WHERE id='".intval($category_id)."'");
        return true;
    }
    
    function set_price_mode_all($category_type, $mode){
        db::query("UPDATE ".PREF."goods_categories SET set_price = '".intval($mode)."'WHERE measure_type = '".intval($category_type)."'");
        return true;
    }
    
    function get_nearest_unit($q){
        $aRounds = $this->get_round();
        if($q > 1 and $q <= $aRounds['gram']){
            $aNearestUnit['code'] = 'default';
            $aNearestUnit['name'] = 'gram';
        }elseif($q > 1.75 and $q <= $aRounds['halfeighth']){
            $aNearestUnit['code'] = 'halfeighth';
            $aNearestUnit['name'] = '1/2 8th';
        }elseif($q > 2 and $q <= $aRounds['twograms']){
            $aNearestUnit['code'] = 'twograms';
            $aNearestUnit['name'] = '2 grams';
        }elseif($q > 3.5 and $q <= $aRounds['eighth']){
            $aNearestUnit['code'] = 'eighth';
            $aNearestUnit['name'] = '1/8';
        }elseif($q > 4 and $q <= $aRounds['fourgrams']){
            $aNearestUnit['code'] = 'fourgrams';
            $aNearestUnit['name'] = '4 grams';
        }elseif($q > 5 and $q <= $aRounds['fivegrams']){
            $aNearestUnit['code'] = 'fivegrams';
            $aNearestUnit['name'] = '5 grams';
        }elseif($q > 7 and $q <= $aRounds['fourth']){
            $aNearestUnit['code'] = 'fourth';
            $aNearestUnit['name'] = '1/4';
        }elseif($q > 14 and $q <= $aRounds['half']){
            $aNearestUnit['code'] = 'half';
            $aNearestUnit['name'] = '1/2';
        }elseif($q > 28 and $q <= $aRounds['oz']){
            $aNearestUnit['code'] = 'oz';
            $aNearestUnit['name'] = 'oz';
        }else{
            $aNearestUnit['code'] = '';
            $aNearestUnit['name'] = '';
        }
        return $aNearestUnit;
    }
    
    function get_price_autoround($category_type){
        $result = db::get_one("SELECT autoround FROM ".PREF."goods_categories WHERE measure_type = '".intval($category_type)."' LIMIT 1");
        return $result;
    }
    
    function set_price_autoround($category_id, $mode){
        db::query("UPDATE ".PREF."goods_categories SET autoround = '".intval($mode)."' WHERE id='".intval($category_id)."'");
        return true;
    }
    
    function set_price_autoround_all($category_type, $mode){
        db::query("UPDATE ".PREF."goods_categories SET autoround = '".intval($mode)." 'WHERE measure_type = '".intval($category_type)."'");
        return true;
    }
    
    function get_round(){
        $aRound = db::get_row("SELECT * FROM ".PREF."round");
        return $aRound;
    }
    
    function edit_round($aData){
        if(!empty($aData)){
            db::query("UPDATE ".PREF."round SET
                        eighth = '".floatval($aData['eighth'])."',
                        gram = '".floatval($aData['gram'])."',
                        fourth = '".floatval($aData['fourth'])."',
                        half = '".floatval($aData['half'])."',
                        oz = '".floatval($aData['oz'])."',
                        halfeighth = '".floatval($aData['halfeighth'])."',
                        twograms = '".floatval($aData['twograms'])."',
                        fourgrams = '".floatval($aData['fourgrams'])."',
                        fivegrams = '".floatval($aData['fivegrams'])."'
                      ");
            return true;
        }else{
            return false;
        }
    }
    
    function get_inactive_timeframe($category_type){
        $result = db::get_one("SELECT inactive_time_frame FROM ".PREF."goods_categories WHERE measure_type = '".intval($category_type)."' LIMIT 1");
        return $result;
    }
    
    function set_inactive_timeframe($timeframe){
        db::query("UPDATE ".PREF."goods_categories SET inactive_time_frame = '".intval($timeframe)."'");
        return true;
    }
    
    function addItemToWeedmaps($id, $aData=array()){
        $weedmaps_apikey = settings::get('weedmaps_apikey');
        if($weedmaps_apikey){
            $aItem = $this->get_goods_item($id);
            if(empty($aData)){
                $aData = $aItem;
            }
            $url = WEEDMAPS_API_SERVER.'/'.$weedmaps_apikey;
            $data = array();
            $data['item_id'] = 'pos'.$id;
            $data['name'] = $aData['name'];
            if($aData['measure_type'] == 1){
                if(isset($aData['meds_type'])){
                    switch ($aData['meds_type']){
                        case 1:
                            $data['category_id'] = 2;
                        break;
                        case 2:
                            $data['category_id'] = 1;
                        break;
                        case 3:
                            $data['category_id'] = 3;
                        break;  
                        default:
                            $data['category_id'] = 0;
                        break;
                    }
                }else{
                    $data['category_id'] = 0;
                }
                if(!empty($aData['modifiers'])){ 
                    foreach($aData['modifiers'] as $n=>$mod){
                        if(!empty($mod['price'])){
                            $data['prices']['price_gram'] = floatval($mod['price']);
                        }
                        if(!empty($mod['price_twograms'])){
                            $data['prices']['price_two_grams'] = floatval($mod['price_twograms']);
                        }
                        if(!empty($mod['price_eighth'])){
                            $data['prices']['price_eighth'] = floatval($mod['price_eighth']);
                        }                        
                        if(!empty($mod['price_fourth'])){
                            $data['prices']['price_quarter'] = floatval($mod['price_fourth']);
                        }
                        if(!empty($mod['price_half'])){
                            $data['prices']['price_half'] = floatval($mod['price_half']);
                        }
                        if(!empty($mod['price_oz'])){
                            $data['prices']['price_ounce'] = floatval($mod['price_oz']);
                        }
                        if(!empty($mod['price_fourgrams'])){
                            $data['prices']['prices_other']['price_four_grams'] = floatval($mod['price_fourgrams']);
                        }
                        if(!empty($mod['price_fivegrams'])){
                            $data['prices']['prices_other']['price_five_grams'] = floatval($mod['price_fivegrams']);
                        }
                        if(!empty($mod['price_pre_roll'])){
                            $data['prices']['prices_other']['price_pre_roll'] = floatval($mod['price_pre_roll']);
                        }
                    }
                }
            }else{
                $catName = db::get_one("SELECT name FROM ".PREF."goods_categories WHERE id = '".intval($aData['cat_id'])."'");
                switch ($catName){
                    case 'Edibles';
                        $data['category_id'] = 4;
                    break;
                    case 'Edible';
                        $data['category_id'] = 4;
                    break;
                    case 'Concentrates';
                        $data['category_id'] = 5;
                    break;
                    case 'Concentrate';
                        $data['category_id'] = 5;
                    break;
                    default:
                        $data['category_id'] = 0;
                    break;
                }
                if(!empty($aData['modifiers'])){ 
                    foreach($aData['modifiers'] as $n=>$mod){
                        $data['prices']['price_unit'] = floatval(@$mod['price']);
                    }
                }                
            }
            $data['body'] = @$aData['note'];
            $data['published'] = true;
            $dataJSON = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJSON);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,FALSE);
            $result = curl_exec($ch);
            $commError = curl_error($ch);
            $commInfo = @curl_getinfo($ch);
            curl_close($ch);
            return $result;
        }else{
            return false;
        }
    }
    
    function editItemInWeedmaps($id, $aData=array()){
        $weedmaps_apikey = settings::get('weedmaps_apikey');
        if($weedmaps_apikey){
            $aItem = $this->get_goods_item($id);
            if(empty($aData)){
                $aData = $aItem;
            }
            $url = WEEDMAPS_API_SERVER.'/'.$weedmaps_apikey.'/'.'pos'.$id; //dump($url);
            $data = array();
            $data['item_id'] = 'pos'.$id;
            $data['name'] = $aData['name'];
            if($aData['measure_type'] == 1){
                if(isset($aData['meds_type'])){
                    switch ($aData['meds_type']){
                        case 1:
                            $data['category_id'] = 2;
                        break;
                        case 2:
                            $data['category_id'] = 1;
                        break;
                        case 3:
                            $data['category_id'] = 3;
                        break;  
                        default:
                            $data['category_id'] = 0;
                        break;
                    }
                }else{
                    $data['category_id'] = 0;
                }
                if(!empty($aData['modifiers'])){ 
                    foreach($aData['modifiers'] as $n=>$mod){
                        if(!empty($mod['price'])){
                            $data['prices']['price_gram'] = floatval($mod['price']);
                        }
                        if(!empty($mod['price_twograms'])){
                            $data['prices']['price_two_grams'] = floatval($mod['price_twograms']);
                        }
                        if(!empty($mod['price_eighth'])){
                            $data['prices']['price_eighth'] = floatval($mod['price_eighth']);
                        }                        
                        if(!empty($mod['price_fourth'])){
                            $data['prices']['price_quarter'] = floatval($mod['price_fourth']);
                        }
                        if(!empty($mod['price_half'])){
                            $data['prices']['price_half'] = floatval($mod['price_half']);
                        }
                        if(!empty($mod['price_oz'])){
                            $data['prices']['price_ounce'] = floatval($mod['price_oz']);
                        }
                        if(!empty($mod['price_fourgrams'])){
                            $data['prices']['prices_other']['price_four_grams'] = floatval($mod['price_fourgrams']);
                        }
                        if(!empty($mod['price_fivegrams'])){
                            $data['prices']['prices_other']['price_five_grams'] = floatval($mod['price_fivegrams']);
                        }
                        if(!empty($mod['price_pre_roll'])){
                            $data['prices']['prices_other']['price_pre_roll'] = floatval($mod['price_pre_roll']);
                        }
                    }
                }
            }else{
                $catName = db::get_one("SELECT name FROM ".PREF."goods_categories WHERE id = '".intval($aData['cat_id'])."'");
                switch ($catName){
                    case 'Edibles';
                        $data['category_id'] = 4;
                    break;
                    case 'Edible';
                        $data['category_id'] = 4;
                    break;
                    case 'Concentrates';
                        $data['category_id'] = 5;
                    break;
                    case 'Concentrate';
                        $data['category_id'] = 5;
                    break;
                    default:
                        $data['category_id'] = 0;
                    break;
                }
                if(!empty($aData['modifiers'])){ 
                    foreach($aData['modifiers'] as $n=>$mod){
                        $data['prices']['price_unit'] = floatval(@$mod['price']);
                    }
                }                
            }
            $data['body'] = @$aData['note'];
            if($aItem['active']){
                $data['published'] = true;
            }else{
                $data['published'] = false;
            }
            $dataJSON = json_encode($data); //dump($dataJSON);die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJSON);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,FALSE);
            $result = curl_exec($ch);
            $commError = curl_error($ch);
            $commInfo = @curl_getinfo($ch);
            curl_close($ch);
            return $result;
        }else{
            return false;
        }
    }
    
    function deleteItemFromWeedmaps($id){
        $weedmaps_apikey = settings::get('weedmaps_apikey');
        if($weedmaps_apikey){
            $url = WEEDMAPS_API_SERVER.'/'.$weedmaps_apikey.'/'.'pos'.$id;
            $data = array();
            $data['item_id'] = 'pos'.$id;
            $dataJSON = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJSON);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,FALSE);
            $result = curl_exec($ch);
            $commError = curl_error($ch);
            $commInfo = @curl_getinfo($ch);
            curl_close($ch);
            return $result;
        }else{
            return false;
        }
    }

}