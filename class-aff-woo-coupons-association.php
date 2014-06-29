<?php 

class AFF_WOO_COUPONS_ASSOC
{
    var $assoc_items = array();
    
    function AFF_WOO_COUPONS_ASSOC()
    {
        
    }
    
    function add_item($item)
    {
        array_push($this->assoc_items, $item);
    }
	
    function find_item_by_code($coupon_code)
    {
        if(empty($this->assoc_items)){
            echo "<br />Admin needs to configure some discount coupons and affiliate ID association before it can be used";
            return new stdClass();
        }
        foreach($this->assoc_items as $key => $item)
        {
            if($item->coupon_code == $coupon_code){
                return $item;
            }
        }
        return new stdClass();
    }
        
    function delete_item_by_id($item_id)
    {
        $item_deleted = false;
        foreach($this->assoc_items as $key => $item)
        {
            if($item->id == $item_id){
                $item_deleted = true;
                unset($this->assoc_items[$key]);
            }
        }
        if($item_deleted){
            $this->assoc_items = array_values($this->assoc_items);
            AFF_WOO_COUPONS_ASSOC::save_object($this);
        }
    }
        
    function print_collection()
    {
        foreach ($this->assoc_items as $item){
            $item->print_item_details();
        }
    }
        
    static function save_object($obj_to_save)
    {
        update_option('aff_woo_coupon_association_data', $obj_to_save);
    }
        
    static function get_instance()
    {
        $obj = get_option('aff_woo_coupon_association_data');
        if($obj){
            return $obj;
        }else{
            return new AFF_WOO_COUPONS_ASSOC();
        }
    }
}

class AFF_WOO_ASSOC_ITEM
{
    var $id;
    var $coupon_code;
    var $aff_id;

    function AFF_WOO_ASSOC_ITEM($coupon_code, $aff_id)
    {
        $this->id = uniqid();
        $this->coupon_code = $coupon_code;
        $this->aff_id = $aff_id;
    }
    
    function print_item_details()
    {
        echo "<br />Coupon ID: ".$this->id;
        echo "<br />Coupon Code: ".$this->coupon_code;
        echo "<br />Affiliate ID: ".$this->aff_id;
    }
}