<?php 

function wp_aff_woo_coupons_settings_menu()
{
	echo '<div class="wrap">';
	echo '<div id="poststuff"><div id="post-body">';
	echo '<h2>Affiliate WooCommerce Coupons Addon</h2>';
	
	if (isset($_POST['aff_woo_delete_item_id']))
	{
        $item_id = $_REQUEST['aff_woo_delete_item_id'];
        $collection_obj = AFF_WOO_COUPONS_ASSOC::get_instance();
        $collection_obj->delete_item_by_id($item_id);
        echo '<div id="message" class="updated fade"><p>';
        echo 'Item successfully deleted!';
        echo '</p></div>';
	}
	
	if (isset($_POST['aff_woo_save_association']))
	{
		$coupon_code = $_POST['aff_woo_coupon_code'];
		$aff_id = $_POST['aff_woo_affiliate_id'];
		
		if(!empty($coupon_code) || !empty($aff_id)){
			$item = new AFF_WOO_ASSOC_ITEM($coupon_code, $aff_id);
			$collection_obj = AFF_WOO_COUPONS_ASSOC::get_instance();
	        $collection_obj->add_item($item);
	        AFF_WOO_COUPONS_ASSOC::save_object($collection_obj);

	        echo '<div id="message" class="updated fade"><p><strong>';
	        echo 'Saved!';
	        echo '</strong></p></div>';
		}
		else{
			echo '<div id="message" class="updated fade"><p><strong>';
	        echo 'Error! You must enter a coupon code and an affiliate ID.';
	        echo '</strong></p></div>';
		}
	}
	
	?>
	<div class="postbox">
    <h3><label for="title">Configure Coupons and Affiliate ID Association</label></h3>
    <div class="inside">
    
    <form method="post" action="">
    <table class="form-table" border="0" cellspacing="0" cellpadding="6" style="max-width:600px;">

    <tr valign="top">

    <td width="25%" align="left">
    Coupon Code<br />
    <input name="aff_woo_coupon_code" type="text" size="20" value=""/>   
    </td>

    <td width="25%" align="left">
    Affiliate ID<br />
    <input name="aff_woo_affiliate_id" type="text" size="20" value=""/>            
    </td>

    <td width="25%" align="left">
    <div class="submit">
        <input type="submit" name="aff_woo_save_association" class="button-primary" value="Save" />
    </div>                
    </td> 

    </tr>

    </table>
    </form>
    
    </div>
    </div>

	<?php 
	
    //Display table
    $output = "";
    $output .= '
    <table class="widefat" style="max-width:800px;">
    <thead><tr>
    <th scope="col">Coupon Code</th>
    <th scope="col">Affiliate ID</th>
    <th scope="col"></th>
    </tr></thead>
    <tbody>';

    $row_count = 0;
    $collection_obj = AFF_WOO_COUPONS_ASSOC::get_instance();
    if($collection_obj)
    {
        $items = $collection_obj->assoc_items; 
        $number_of_items = count($items);
        if($number_of_items > 0)
        {
            foreach ($items as $item)
            {
                $output .= '<tr>';
                $output .= '<td><strong>'.$item->coupon_code.'</strong></td>';
                $output .= '<td><strong>'.$item->aff_id.'</strong></td>';			
                $output .= '<td>';
                $output .= "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";				
                $output .= "<input type=\"hidden\" name=\"aff_woo_delete_item_id\" value=".$item->id." />";
                $output .= '<input style="border: none; background-color: transparent; padding: 0; cursor:pointer;" type="submit" name="Delete" value="Delete">';
                $output .= "</form>";
                $output .= '</td>';
                $output .= '</tr>';
                $row_count = $row_count + 1;
            }
        }
        else
        {
            $output .= '<tr><td colspan="5">No Record found.</td></tr>';
        }
    }
    else
    {
        $output .= '<tr><td colspan="5">No Record found.</td></tr>';
    }

    $output .= '</tbody>
    </table>';

    //$output .= '<p><a href="options-general.php?page=wordpress-paypal-shopping-cart&action=discount-settings">Add New</a></p>';
    echo $output;
    	
	echo '</div></div>';//End of poststuff and body
	echo '</div>';//End of wrap
}