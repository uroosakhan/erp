<?php
include_once($path_to_root . "/POS/includes/cart_class.inc");
include_once($path_to_root . "/includes/manufacturing.inc");

//--------------------------------------------------------------------------------
function add_to_order(&$order, $new_item, $new_item_qty, $price, $discount, $description='',
					  $deal_id,$stk_id, $disc123, $deal)
{

	// calculate item price to sum of kit element prices factor for
	// value distribution over all exploded kit items
	 $std_price = get_kit_price($new_item, $order->customer_currency,
		$order->sales_type,	$order->price_factor, get_post('OrderDate'), true);

	if ($std_price == 0)
		$price_factor = 0;
	else
		$price_factor = $price/$std_price;

	$kit = get_item_kit($new_item);
	$item_num = db_num_rows($kit);


	while($item = db_fetch($kit)) {
		$std_price = get_kit_price($item['stock_id'], $order->customer_currency,
			$order->sales_type,	$order->price_factor, get_post('OrderDate'), true);

		// rounding differences are included in last price item in kit
		$item_num--;
		if ($item_num) {
			$price -= $item['quantity']*$std_price*$price_factor;
			$item_price = $std_price*$price_factor;
		} else {
			if ($item['quantity'])
				$price = $price/$item['quantity'];
			$item_price = $price;
		}
		$item_price = round($item_price, user_price_dec());

        if (!$item['is_foreign'] && $item['item_code'] != $item['stock_id'])
        {	// this is sales kit - recurse
        add_to_order($order, $item['stock_id'], $new_item_qty*$item['quantity'],
        $item_price, $discount,'',$deal_id,$stk_id,$disc123);
        }
        else
        {	// stock item record eventually with foreign code
            // check duplicate stock item
            // stock item record eventually with foreign code
           if($deal == 2)
            {
                $quantity = 0;
                $error =  $discount1 =  $discount2 = 0;
                // check duplicate stock item
                foreach ($order->line_items as $line_no=>$stock_item)
                {
                    if (strcasecmp($stock_item->stock_id, $item['stock_id']) == 0) {
                        $error = 1;
                        $quantity += $stock_item->quantity;
                        $discount1 = $stock_item->discount_percent*100;
                        $_SESSION['Items']->remove_from_cart($line_no);
                    }
                }
                $quantity = $quantity + 1;
                $discount2 = $discount1 + get_discount($order->sales_type, $item['stock_id']);
//            $discount1 += get_discount($order->sales_type, $item['stock_id']);


                if($error == 1) {

                    $order->add_to_cart(count($order->line_items),	$item['stock_id'],
                        $quantity*$item['quantity'], $item_price, $discount, 0,0,
                        $description,0,0,0,$deal_id,$stk_id,$disc123);
                }
                else{
                    $order->add_to_cart(count($order->line_items),	$item['stock_id'],
                        $new_item_qty*$item['quantity'], $item_price, $discount, 0,0,
                        $description,0,0,0,$deal_id,$stk_id,$disc123);
                }
            }
            elseif($deal == 1){
                $order->add_to_cart(count($order->line_items),	$item['stock_id'],
                    $new_item_qty*$item['quantity'], $item_price, $discount, 0,0,
                    $description,0,0,0,$deal_id,$stk_id,$disc123);
            }
            else
                display_error("Deal must be select.");
        }
	}

}
//---------------------------------------------------------------------------------
function get_customer_name_new($customer_id)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$db = db_query($sql, "could not get customer");

	$ft = db_fetch($db);

	return $ft [0];
}
function get_customer_details_to_order(&$order, $customer_id, $branch_id)
{
	global $SysPrefs;

	$ret_error = "";

	$myrow = get_customer_to_order_pos($customer_id);

	$name = $myrow['name'];

	if ($myrow['dissallow_invoices'] == 1)
		$ret_error = _("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");

	$deliver = $myrow['address']; // in case no branch address use company address

	$order->set_customer($customer_id, $name, $myrow['curr_code'],
		$myrow['discount'], $myrow['payment_terms'], $myrow['pymt_discount']);

	// the sales type determines the price list to be used by default
	$order->set_sales_type($myrow['salestype'], $myrow['sales_type'], $myrow['tax_included'],
	    $myrow['factor']);

	$order->credit = $myrow['cur_credit'];

	if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE)
	{
		$order->dimension_id = $myrow['dimension_id'];
		$order->dimension2_id = $myrow['dimension2_id'];
	}
	$result = get_branch_to_order_pos($customer_id, $branch_id);

	if (db_num_rows($result) == 0)
	{
		return _("The selected customer and branch are not valid, or the customer does not have any branches.");
	}

	$myrow = db_fetch($result);

	// FIX - implement editable contact selector in sales order
	$contact = get_branch_contacts_pos($branch_id, 'order', $customer_id);
	$order->set_branch($branch_id, $myrow["tax_group_id"],
	$myrow["tax_group_name"], @$contact["phone"], @$contact["email"]);

	$address = trim($myrow["br_post_address"]) != '' ? $myrow["br_post_address"]
		: (trim($myrow["br_address"]) != '' ? $myrow["br_address"]:$deliver);

	$order->set_delivery($myrow["default_ship_via"], $myrow["br_name"],
		$address);
	if ($order->trans_type == ST_SALESINVOICE) {
		$order->due_date = get_invoice_duedate_pos($order->payment, $order->document_date);
	}
	elseif ($order->trans_type == ST_SALESORDER)
		$order->due_date = add_days($order->document_date, $SysPrefs->default_delivery_required_by());
	if($order->payment_terms['cash_sale']) {
		$order->set_location($order->pos["pos_location"], $order->pos["location_name"]);
	} else
		$order->set_location($myrow["default_location"], $myrow["location_name"]);

	return $ret_error;
}

//---------------------------------------------------------------------------------
function get_price_for_deal_itm($id)
{
	$sql="SELECT price FROM ".TB_PREF."prices where stock_id='".$id."' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function display_order_summary($title, &$order, $editable_items=false)
{
    global $SysPrefs;

    display_heading($title);

    div_start_new('items_table');
    start_table_custom(TABLESTYLE, "colspan=2 width=20%");
    $th = array(_("#"), _("Item Code"), _("Item Description"), _("Quantity"),
     _("Price"),_("Disc%"), _("Total"),"","");

//    if ($order->trans_no == 0) {
//        unset($th[7]);
//    }
//    if (count($order->line_items))
//        $th[]= '';
    table_header($th);
    $total = $serial  = 0;
    $k = 0;  //row colour counter

    $id = find_submit('Edit');
    $has_marked = false;
    foreach ($order->line_items as $line_no=>$stock_item)
    {
       // display_error($stock_item->price);
        $line_total = round($stock_item->qty_dispatched * $stock_item->price * (1 - $stock_item->discount_percent),
            user_price_dec());

        $qoh_msg = '';
        if (!$editable_items || $id != $line_no)
        {
            if (!$SysPrefs->allow_negative_stock() && is_inventory_item($stock_item->stock_id) &&
                $order->trans_type != ST_SALESORDER && $order->trans_type!=ST_SALESQUOTE) {
                $qoh = get_qoh_on_date($stock_item->stock_id,
                    $_POST['Location'], $_POST['delivery_date']);
                if (($stock_item->qty_dispatched -$stock_item->qty_done) > $qoh)
                {
                    // oops, we don't have enough of one of the component items
                    start_row("class='stockmankobg'");
                    $qoh_msg .= $stock_item->stock_id . " - " . $stock_item->item_description . ": " .
                        _("Quantity On Hand") . " = "
                        . number_format2($qoh, get_qty_dec($stock_item->stock_id)) . '<br>';
                    $has_marked = true;
                }
                else
                    alt_table_row_color($k);
            } else {
                alt_table_row_color($k);
            }
            $serial++;
            label_cell($serial);
            view_stock_status_cell($stock_item->stock_id);
            //label_cell($stock_item->item_description, "nowrap" );
            label_cell($stock_item->item_description );
            $dec = get_qty_dec($stock_item->stock_id);
            qty_cell($stock_item->qty_dispatched, false, $dec);
//            if ($order->trans_no!=0)
//                qty_cell($stock_item->qty_done, false, $dec);
//			label_cell($stock_item->units);
            amount_cell($stock_item->price);
            percent_cell($stock_item->discount_percent * 100);
            amount_cell($line_total);
            if ($editable_items)
            {
                edit_button_cell("Edit$line_no", _("Edit"),
                    _('Edit document line'));
                delete_button_cell("Delete$line_no", _("Delete"),
                    _('Remove line from document'));
            }
            end_row();
        }
        else
        {
            sales_order_item_controls($order, $k,  $line_no);
        }
        $total += $line_total;
    }
    if ($id==-1 && $editable_items)
        sales_order_item_controls($order, $k);
    $colspan = 1;
    if ($order->trans_no!=0)
        ++$colspan;
    start_row();
    label_cell(_("Delivery Charges"), "colspan=0 align=left");
    small_amount_cells(null, 'freight_cost', price_format(get_post('freight_cost',0)));
//    label_cell('', 'colspan=2');
    submit_cells('update', _("Update Amount"), "colspan=5 align='center'", _("Refresh"), true);
    end_row();
    $display_sub_total = $total + input_num('freight_cost');

    // $display_sub_total = $display_sub_total1 + $tax_total;
//  Deal Amount
//    $deal_amount = $stock_item->deal_id;
    start_row();
    if(count($stock_item->stock_id) == 0)
        unset($_SESSION['total_deal']);
//    if($deal_amount != '' && $deal_amount != 0) {
//        //$total_amount = ($total + input_num('freight_cost') - $total_discount + $tax_total);
//        //$discount_persentage = round2((( $total_amount - $deal_amount*$stock_item->qty_dispatched) / $total_amount)  * 100 );
//        //$display_total = $deal_amount*$stock_item->qty_dispatched ;
//        //$display_total = 0;
//        $_SESSION['total_deal'] += $stock_item->deal_id ;
////      yhe line m n khud comment ki hy qk client ki requirment hy agr deal wise implement kerian gy tu isko enable kr dian gy
//        //$display_total = $_SESSION['total_deal'];
//
//        $sub = $display_sub_total - $display_total ;
//        //$_POST['total_discount'] = round2( $sub * 100 / $display_sub_total );
//        $num =  $sub * 100 / $display_sub_total ;
//        $_POST['total_discount'] = number_format($num, 3, '.', '');
////	var_dump($display_total."    --".$display_sub_total ." -- sub =".$_POST['total_discount'] ." -- sub =".$sub);
////	session_unset();
//    } else {
//        $_SESSION['total_deal'] += $stock_item->price ;
//        $display_total = $_SESSION['total_deal'];
//        //  $total_discount = $total / 100 * input_num('total_discount') ;
//        $display_total = price_format(($display_total + input_num('freight_cost')  + $tax_total ));
//
//    }
    /*
    if($deal_amount != '' && $deal_amount != 0) {
    //( (
        $total_amount = ($total + input_num('freight_cost') - $total_discount + $tax_total);
        $discount_persentage = round2((( $total_amount - $deal_amount) / $total_amount)  * 100 );
        $_POST['total_discount'] = $discount_persentage ;
        $display_total = $deal_amount ;
    }*/
    $dec2 = user_price_dec();
    
    label_cells(_("Sub-total"), number_format($display_sub_total, $dec2), "colspan=0 align=left","align=right", 2);
    echo "<tr>";
    
    discount_list_cells( _("Total Discount(%)"), 'total_discount_pos1', null, false, true);
    echo "<tr>";
    $sql = "SELECT discount FROM ".TB_PREF."discount WHERE id = ".db_escape($_POST['total_discount_pos1']);
    $query = db_query($sql, "Error");
    $fetch = db_fetch($query);
    
    hidden('total_discount_pos', $fetch['discount']);
    $dis = $_POST['total_discount_pos']/100;
    $Discount = $display_sub_total*$dis;
    label_cells(_("Total Dis. Amt."), number_format($Discount, $dec2), "colspan=0 align=left","align=right", 2);
    hidden('total_discount', $Discount);
    $DisplayTotal = ($display_sub_total - $Discount);
    echo "<tr>";
    label_cells(_("Amount After Dis."), number_format($DisplayTotal, $dec2), "colspan=0 align=left","align=right", 2);
    // label_cells(_("Total Discount+Gross Amount"), number_format($DisplayTotal, $dec2), "colspan=0 align=left","align=right", 2);
    echo "<tr>";    
    // $taxes = $order->get_taxes(input_num('freight_cost'));
    // $tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2);
    hidden('AmountCheck', $total + input_num('freight_cost'));
    $DisplayTotal = ($display_sub_total - $Discount);
    $Total_ = $DisplayTotal*0.13; // time shortage that's why.
    $TotalGst = $DisplayTotal + $Total_;
    label_cells(_("GST 13%"), number_format($Total_, $dec2), "colspan=0 align=left","align=right", 2);
    hidden('CashGst', number_format($Total_));
    echo "<tr>";
    text_cells_pos1("<b><font color=black>" . _("TOTAL AMOUNT") . "</font></b>",'TotalAmountReceived',round($TotalGst), 7, 50);
        

    echo "<tr>";
//    ref_cells("Cash Standard:", 'cash_standard', null, null, null, true);
    $TotalAmountReceived = $display_sub_total - $Discount;
    hidden('TotalAmountReceived', $TotalAmountReceived);
    text_cells_pos("<b><font color=black>" . _("CASH TENDER") . "</font></b>", 'CashTender', null, 7, 50);

//  text_cells_new4(null, 'disc1', $_POST['disc3'], 5, 50, false, "", "", "");
    echo "<tr>";
//  text_cells_new4("Balance :", 'Balance', null, 7);

    text_cells_pos1("<b><font color=black>" . _("CASH RETURN") . "</font></b>", 'CashReturn', null, 7);


//    label_cells(_("Amount Total"), $DisplayTotal , "colspan=0 align=left","align=left");
//	label_cell(_("Total Discount(%)"), "colspan=$colspan align=right");
//	small_amount_cells_discount(null, 'total_discount', null, 2);
//	label_cell('', 'colspan=2');
//  end_row();
//  start_row();
//  $dis=$display_sub_total*($_POST['total_discount']/100);
//  label_cells(_("Amount Total"), $display_total , "colspan=$colspan align=right","align=right");
//  label_cells(_("Amount Total"), $display_sub_total-$dis)) , "colspan=$colspan align=right","align=right");
//  label_cells(_("Amount Total"), $display_total , "colspan=$colspan align=right","align=right");
    end_row();
    end_table();
    if ($has_marked) {
        display_note(_("Marked items have insufficient quantities in stock as on day of delivery."), 0, 1, "class='stockmankofg'");
        if ($order->trans_type != 30 && !$SysPrefs->allow_negative_stock())
            display_error(_("The delivery cannot be processed because there is an insufficient quantity for item:")
                . '<br>'. $qoh_msg);
    }
    div_end();
}

function display_order_header(&$order, $editable, $date_text)
{
    global $Ajax, $SysPrefs;
//    echo '<div style="width: 700px; background-color: ; height: 100px; margin-left: 400px;">';
   // echo '</div>';
    //var_dump(get_post('category_id').'343'.$_POST['customer_id']);
  //  echo '<div style="width: 300px; position: absolute; margin-top: -100px; background-color: ; height: 100px;">';
    start_outer_table1(TABLESTYLE2, "width=100%" );
    function get_cat()
    {
        global $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'BALOCH1')
            $sql="SELECT * from ".TB_PREF."stock_category  ORDER BY  `description` ASC"; 
        else
            $sql="SELECT * from ".TB_PREF."stock_category  ORDER BY  `short_name` ASC"; 
        $db = db_query($sql,'error');
        return $db;
    }

    function get_item_qty_default($id)
    {
        $sql = "SELECT 	carton FROM ".TB_PREF."stock_master where stock_id=" . db_escape($id) . " ";
        $db = db_query($sql, 'error');
        $ft = db_fetch($db);
        return $ft[0];
    }


    function get_cat_for_box($id)
    {
        $sql = "SELECT * from ".TB_PREF."stock_master where category_id=".$id;
        $sql .= " AND inactive = 0";
//        $sql = "SELECT s.stock_id,i.item_code, i.description, c.description,
//    count(*)>1 as kit,
//     i.inactive, if(count(*)>1, '0', s.editable) as editable FROM ".TB_PREF."stock_master s,
//     ".TB_PREF."item_codes i LEFT JOIN ".TB_PREF."stock_category c ON i.category_id=c.category_id
//
//     WHERE i.stock_id=s.stock_id AND c.category_id=$id GROUP BY i.item_code ORDER BY i.id";
        $db  = db_query($sql,'error');
        return $db;
    }

	function get_item_names($id,$cat,$stid)
	{
		if($cat != 15) {
			$sql = "SELECT description FROM ".TB_PREF."stock_master where stock_id=" . db_escape($id) . " ";
			$db = db_query($sql, 'error');
			$ft = db_fetch($db);
			return $ft[0];
		}
		else
		{
			$sql = "SELECT item_code FROM ".TB_PREF."item_codes where  category_id=15 AND item_code='$stid'";
			$db = db_query($sql, 'error');
			$ft = db_fetch($db);
			return $ft[0];
		}
	}


	echo'<style type="text/css">

ul.tab {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}


ul.tab li {float: left;}


ul.tab li a {
    display: inline-block;
    color: black;
    text-align: center;
    padding: 10px 10px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
}


ul.tab li a:hover {background-color: #ddd;}


ul.tab li a:focus, .active {background-color: #ccc;}


.tabcontent {
    display: none;
    padding: 3px 6px;
    border: 1px solid #ccc;
    border-top: none;

}

</style>';
	echo'<script>

   
      

function openCity(evt, cityName) {
   
    var i, tabcontent, tablinks;

  
    tabcontent = document.getElementsByClassName("tabcontent");
   
    for (i = 0; i < tabcontent.length; i++) {
         
        tabcontent[i].style.display = "none";
       
    }


    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }


    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}


</script>';
    echo '<div style="height:400px; width:1024px;margin-right: 30px;" >';
    display_order_summary(null, $_SESSION['Items'], true);
    div_end();
    div_start();
    start_table_custom1();

    echo'<ul style="width:450px; "  class="tab" >';

	$result = get_cat();

	while ($my_row = db_fetch($result))
	{
	    ?><script>
        openCity1(event, 0);
        </script>
       <?php
        echo '<li>
               <a href="#" class="tablinks" onclick="openCity(event, \''.$my_row['category_id'].'\') ">
                    '.$my_row['description'].'
            </a>
            </li>';
    }
  	echo'</ul>';

	echo '
	<script>
	
			function set_id(a)
			{
			var b = a.id;
				//alert(b);
				document.getElementById("_stock_id_edit").value = b;
				
				
			}
			function set_qty(b)
			{
				var id = b.id;
				var c = document.getElementById(id).value ;
				document.getElementById("qty_item").value = c;
				
				//alert(b);
				//console.log(b.id);
			}
	
    </script>';

	$result123 = get_cat();
	while ($my_row2 = db_fetch($result123))
	{
		echo '<div id="'.$my_row2['category_id'].'" class="tabcontent">
		  <h3></h3>';

		echo '<div style="height:350px;background-color:;overflow-x: scroll; ">';
		echo '<div style="height:;background-color:;width:auto;">';


		$new = get_cat_for_box($my_row2['category_id']);
	while ($myrow = db_fetch($new)) {
        echo '<div style="width: 115px;height:;
                   background-color:#ededf4;margin-left:20px;margin-top:5px;
                   float: left;padding-bottom: 5px;">';
        echo '<div style="width: 75px;
                   background-color:;margin-left:8px;margin-top:10px;">';
        $_POST['qty_num'] = $myrow['stock_id'];

        if ($my_row2['category_id'] == 15) {
            echo '<div style="width:100px;height: 36px;">
            <label style="font-size: 18px;font-family:Arial, Helvetica, sans-serif;font-weight:bolder " >' . get_item_names($myrow['stock_id'], $my_row2['category_id'], $myrow['item_code']) . '</label>
			</div>';

            echo '<input type="text" style="width:60px;margin-top:5px"  id="' . $myrow['stock_id'] . '"
		
		onclick="set_id(this)" name="qty_num' . $myrow['item_code'] . '" value="1" onmouseout="set_qty(this)" >';

            //	$_POST['deal_qty'] = $myrow['stock_id'];

            //hidden('deal_qty', $_POST['deal_qty']);
        } else {

            echo '<div style="width:90px;height: 26px;">
            <label style="font-size:12px;font-family:Arial, Helvetica, sans-serif; " >' . get_item_names($myrow['stock_id'], $my_row2['category_id'], $myrow['item_code']) . '</label>
			</div>';

            //echo "<img src='../company/0/images/" . $myrow['stock_id'] . ".jpg' value='' width='95px' height='50px'
            //	>";
//echo "<img src='".company_path() ."/images/" . $myrow['stock_id'] . ".jpg' value='' width='65px' height='65px'
//				>";

            echo '<input type="text"  style="display: none; width:30px;margin-top:5px"  id="' . $myrow['stock_id'] . '"

		onclick="set_id(this)" name="qty_num' . $myrow['stock_id'] . '" value="' . get_item_qty_default($myrow['stock_id']) . '" onmouseout="set_qty(this)" >';

            $getimg = company_path() . "/images/" . $myrow["stock_id"] . '.jpg';

        }
        //submit_cells_cart('AddItem', _("Add Item"), "", _('Add Item'), true);
//if($my_row2['category_id']==15) {
//	echo button_new("AddItem2" . $myrow['item_code'], _("Edit"), _('Add Item'), ICON_EDIT, '');
//
//}else{echo button_new("AddItem2" . $myrow['stock_id'], _("Edit"), _('Add Item'), ICON_EDIT, '');
//}
        if ($my_row2['category_id'] == 15) {
            echo button_new_("AddItem2" . $myrow['item_code'], _("Edit"), _('Add Item'), ICON_EDIT, '', $getimg);
        } else {
            echo button_new_("AddItem2" . $myrow['stock_id'], _("Edit"), _('Add Item'), ICON_EDIT, '',$getimg);
        }
		echo '</div></div>';
	}
	echo '</div>
	</div>';

		echo'</div>';
	}

end_table();
    echo '</div>';
	end_outer_table(1);
//    echo '</div>';

    start_outer_table_pos2(TABLESTYLE2, "width=20%");

//    table_section(1);

    $customer_error = "";
    $change_prices = 0;

    if (isset($order) && !$editable)
    {
        // can't change the customer/branch if items already received on this order
        //echo $order->customer_name . " - " . $order->deliver_to;
        label_row(null, $order->customer_name . " - " . $order->deliver_to);
        hidden('customer_id', $order->customer_id);
        hidden('branch_id', $order->Branch);
        hidden('sales_type', $order->sales_type);
        if ($order->trans_type != ST_SALESORDER  && $order->trans_type != ST_SALESQUOTE) {
            hidden('dimension_id', $order->dimension_id); // 2008-11-12 Joe Hunt
            hidden('dimension2_id', $order->dimension2_id);
        }
    }
    else
    {
//        customer_pos_list_row(_("Customer:"), 'customer_id', null, false, true, false, true);
//	    $_POST['customer_id'] = $_GET['customer_id'];
//      var_dump($_GET['customer_id']);
//		$id = $order->customer_id;
//		$name = get_customer_name_new($id);
//		label_cell($name);
//		hidden('customer_id',$order->customer_id);
        if ($order->customer_id != get_post('customer_id', -1))
        {
            // customer has changed
            $Ajax->activate('branch_id');
        }
        //customer_pos_branches_list_cells(_("Branch:"),
         //   $_POST['customer_id'], 'branch_id', null, false, true, true, true//);

        if( ($order->customer_id != get_post('customer_id', -1)) ||
            ($order->Branch != get_post('customer_id', -1)) ||
            list_updated('customer_id'))
        {

            if (!isset($_POST['customer_id']) || $_POST['customer_id'] == "")
            {

                // ignore errors on customer search box call
                /*if ($_POST['customer_id'] == 0)
                    $customer_error = _("No customer found for entered text.");
                else
                    $customer_error = _("The selected customer does not have any branches. Please create at least one branch.");
                */unset($_POST['branch_id']);
                $order->Branch = 0;
            }
            else
            {
                $old_order = (PHP_VERSION<5) ? $order : clone( $order );

                $customer_error = get_customer_details_to_order($order, $_POST['customer_id'], $_POST['customer_id']);
                $_POST['Location'] = $order->Location;
                $_POST['deliver_to'] = $order->deliver_to;
                $_POST['delivery_address'] = $order->delivery_address;
                $_POST['phone'] = $order->phone;
                $_POST['delivery_date'] = $order->due_date;

                if (!in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER))
                    && ($order->pos['cash_sale'] != $order->pos['credit_sale'])
                    && (($order->payment_terms['cash_sale'] && !$order->pos['cash_sale']) ||
                        (!$order->payment_terms['cash_sale'] && !$order->pos['credit_sale']))) {
                    // force payment terms refresh if terms are editable
                    // and pos have no permitions for terms selected in customer record.
                    // Terms are set to first terms in allowed category below.
                    display_warning(
                        sprintf(_("Customer's payment terms '%s' cannot be selected on this POS"),
                            $order->payment_terms['terms']));
                    $order->payment = '';
                } elseif (get_post('payment') !== $order->payment) {
                    $_POST['payment'] = $order->payment;
                    $Ajax->activate('delivery');
                    $Ajax->activate('payment');
                } else {
                    if ($order->trans_type == ST_SALESINVOICE)
                    {
                        $_POST['delivery_date'] = $order->due_date;
                        $Ajax->activate('delivery_date');
                    }
                    $Ajax->activate('Location');
                    $Ajax->activate('deliver_to');
                    $Ajax->activate('phone');
                    $Ajax->activate('delivery_address');
                }
                // change prices if necessary
                // what about discount in template case?
                if ($old_order->customer_currency != $order->customer_currency) {
                    $change_prices = 1;
                }
                if ($old_order->sales_type != $order->sales_type) {
                    //  || $old_order->default_discount!=$order->default_discount
                    $_POST['sales_type'] = $order->sales_type;
                    $Ajax->activate('sales_type');
                    $change_prices = 1;
                }
                if ($old_order->dimension_id != $order->dimension_id) {
                    $_POST['dimension_id'] = $order->dimension_id;
                    $Ajax->activate('dimension_id');
                }
                if ($old_order->dimension2_id != $order->dimension2_id) {
                    $_POST['dimension2_id'] = $order->dimension2_id;
                    $Ajax->activate('dimension2_id');
                }
                unset($old_order);
            }
            set_global_customer($_POST['customer_id']);
        } // changed branch
        else
        {
            $row = get_customer_to_order_pos($_POST['customer_id']);
            if ($row['dissallow_invoices'] == 1)
                $customer_error = _("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");

        }
    }
    //ref_row(_("Reference").':', 'ref', _('Reference number unique for this document type'), null, '');

//    table_section(2);

//    if (!is_company_currency($order->customer_currency))
//    {
//        label_row(_("Customer Currency:"), $order->customer_currency);
//        exchange_rate_display($order->customer_currency, get_company_currency(),
//            ($editable ? $_POST['OrderDate'] : $order->document_date));
//    }

    //label_cells(_("Customer Discount:"), ($order->default_discount * 100) . "%");
    //customer_credit_cell($_POST['customer_id'], $order->credit);

//    table_section(3);
//    if ($order->pos['cash_sale'] || $order->pos['credit_sale']) {
//        // editable payment type
//        if (get_post('payment') !== $order->payment) {
//            $order->payment = get_post('payment');
//            $order->payment_terms = get_payment_terms($order->payment);
//            $order->due_date = get_invoice_duedate_pos($order->payment, $order->document_date);
//            if ($order->payment_terms['cash_sale']) {
//                $_POST['Location'] = $order->Location = $order->pos['pos_location'];
//                $order->location_name = $order->pos['location_name'];
//            }
//            $Ajax->activate('items_table');
//            $Ajax->activate('delivery');
//        }
//        $paymcat = !$order->pos['cash_sale'] ? PM_CREDIT :
//            (!$order->pos['credit_sale'] ? PM_CASH : PM_ANY);
//        // all terms are available for SO
//        sale_pos_payment_list_cells(_('Payment:'), 'payment',
//            (in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER))
//                ? PM_ANY : $paymcat), null);
//    } else {
//        label_cells(_('Payment:'), $order->payment_terms['terms']);
//    }

//    if($editable) {
//        $str = sales_pos_types_list_cells(_("Price List:"), 'sales_type', null, true);
//    } else {
//        label_cells(_("Price List:"), $order->sales_type_name);
//    }
    if ($order->sales_type != $_POST['sales_type']) {
        $myrow = get_sales_type_pos($_POST['sales_type']);
        $order->set_sales_type($myrow['id'], $myrow['sales_type'],
            $myrow['tax_included'], $myrow['factor']);
        $Ajax->activate('sales_type');
        $change_prices = 1;
    }

//    table_section(4);

    if ($editable)
    {
        if (!isset($_POST['OrderDate']) || $_POST['OrderDate'] == "")
            $_POST['OrderDate'] = $order->document_date;

//        date_row($date_text, 'OrderDate', _('Date of order receive'),
//            $order->trans_no==0, 0, 0, 0, null, true);
        if (isset($_POST['_OrderDate_changed']) || list_updated('payment')) {
            if (!is_company_currency($order->customer_currency)
                && (get_base_sales_type()>0)) {
                $change_prices = 1;
            }
            $Ajax->activate('_ex_rate');
            if ($order->trans_type == ST_SALESINVOICE) {
                $_POST['delivery_date'] = get_invoice_duedate_pos(get_post('payment'), get_post('OrderDate'));
            } else
                $_POST['delivery_date'] = add_days(get_post('OrderDate'), $SysPrefs->default_delivery_required_by());
            $Ajax->activate('items_table');
            $Ajax->activate('delivery_date');
        }
        if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE)
        {	// 2008-11-12 Joe Hunt added dimensions
            $dim = get_company_pref('use_dimension');
            if ($dim > 0)
                dimensions_list_row(_("Dimension").":", 'dimension_id',
                    null, true, ' ', false, 1, false);
            else
                hidden('dimension_id', 0);
            if ($dim > 1)
                dimensions_list_row(_("Dimension")." 2:", 'dimension2_id',
                    null, true, ' ', false, 2, false);
            else
                hidden('dimension2_id', 0);
        }
    }
    else
    {
        label_row($date_text, $order->document_date);
        hidden('OrderDate', $order->document_date);
    }
//	customer_list_row_new(_("Category :"), 'category_id', null, false, true, false, true);
//  display_error($_POST['customer_id']);
//  stock_categories_list_row(_("Category:"), 'category_id', null, false, $new_item);
    end_outer_table(1); // outer table

	if ($change_prices != 0) {
		foreach ($order->line_items as $line_no=>$item) {
			$line = &$order->line_items[$line_no];
			$line->price = get_kit_price($line->stock_id, $order->customer_currency,
				$order->sales_type, $order->price_factor, get_post('OrderDate'));
		//		$line->discount_percent = $order->default_discount;
		}
	    $Ajax->activate('items_table');
	}
    //echo '</div>';
	return $customer_error;
}

//--------------------------------------------------------------------------------

function sales_order_item_controls(&$order, &$rowcounter, $line_no=-1)
{
    global $Ajax;

	alt_table_row_color($rowcounter);

	$id = find_submit('Edit');
	if ($line_no!=-1 && $line_no == $id) // edit old line
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$dec = get_qty_dec($_POST['stock_id']);
		$_POST['qty'] = number_format2($order->line_items[$id]->qty_dispatched, $dec);
		$_POST['price'] = price_format($order->line_items[$id]->price);
		//$_POST['Disc'] = $order->line_items[$id]->discount_percent*100;
		$_POST['Disc'] = $order->line_items[$id]->disc12;
		$units = $order->line_items[$id]->units;
		$_POST['item_description'] = $order->line_items[$id]->item_description;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		if ($order->line_items[$id]->descr_editable)
			text_cells(null,'item_description', null, 45, 150);
		else {
			hidden('item_description', $_POST['item_description']);
			label_cell($_POST['item_description']);
		}
//		} else {
//			sales_items_list_cells(null,'item_description', null, false, false);
//		}
		//label_cell($order->line_items[$line_no]->item_description, "nowrap");
	   $Ajax->activate('items_table');
	}
	else	// prepare new line
	{
        label_cell('');
        sales_items_pos_list_cells(null,'stock_id', null, false, false);
		if (list_updated('stock_id')) {
			    $Ajax->activate('price');
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('line_total');
			    $Ajax->activate('Disc');
		}

		$item_info = get_item_edit_info($_POST['stock_id']);
		$units = $item_info["units"];
		$dec = $item_info['decimals'];
		$_POST['qty'] = number_format2(1, $dec);
		$price = get_kit_price($_POST['stock_id'],
			$order->customer_currency, $order->sales_type,
		$order->price_factor, get_post('OrderDate'));
		$_POST['price'] = price_format($price);
        $price_discount = get_discount($order->sales_type, $_POST['stock_id']);
		// default to the customer's discount %
		$_POST['Disc'] = percent_format($price_discount);
	}

	qty_cells_new(null, 'qty', $_POST['qty'], null, null, $dec);
//	if ($order->trans_no!=0) {
//		qty_cell($line_no==-1 ? 0 :$order->line_items[$line_no]->qty_done, false, $dec);
//	}
//	label_cell('');
//  text_cells( '', 'price');
	hidden('price',$_POST['price']);
//  display_error($_POST['price']);
//  change_hidden_value
	hidden('change', $_GET['change']);
//	hidden('Disc', $_POST['Disc']);
    label_cell('');
//    label_cell('');
//	small_amount_cells_new(null, 'Disc', percent_format($_POST['Disc']), null, null, user_percent_dec());
    discount_list_cells(null, 'Disc', $_POST['Disc'], false, false);


    $line_total = input_num('qty') * input_num('price') * (1 - input_num('Disc') / 100);
	amount_cell($line_total, false, '','line_total');
	if ($id!=-1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
		set_focus('qty');
	}
	else
	{
		label_cell(_(""));
		submit_cells('AddItem', _("Add Item"), "colspan= align='left'",
		   _('Add new item to document'), true);
	}

	end_row();
}

//--------------------------------------------------------------------------------
//
//function display_delivery_details(&$order)
//{
//	global $Ajax;
//
//	div_start('delivery');
//
//
//	if ($order->payment_terms['cash_sale']) {	// Direct payment sale
//		$Ajax->activate('items_table');
//		display_heading(_('Cash payment'));
//		start_table(TABLESTYLE2, "width=60%");
//
//		locations_list_row(_("Deliver from Location:"), 'Location', null, false, true);
// 		if (list_updated('Location'))
// 			$Ajax->activate('items_table');
//		label_row(_("Cash account:"), $order->pos['bank_account_name']);
//
//
//		text_row(_("Card Number :"), 'card_num', null, 30, 4);
//                sales_persons_list_row( _("Server/Waiter Name:"), 'w_id', $order->w_id);
//		//textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
//		end_table();
//		hidden('delivery_date', $order->due_date);
//	} else
//	{
//		//$order->payment_terms['cash_sale']) {	// Direct payment sale
//		$Ajax->activate('items_table');
//		display_heading(_('Card payment'));
//		start_table(TABLESTYLE2, "width=60%");
//
//		locations_list_row(_("Deliver from Location:"), 'Location', null, false, true);
//		if (list_updated('Location'))
//			$Ajax->activate('items_table');
//		//label_row(_("Cash account:"), $order->pos['bank_account_name']);
//
//
//
//		textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
//		end_table();
//		hidden('delivery_date', $order->due_date);
//
//	/*	if ($order->trans_type==ST_SALESINVOICE)
//		{
//			$title = _("Delivery Details");
//			$delname = _("Due Date").':';
//		}
//		elseif ($order->trans_type==ST_CUSTDELIVERY)
//		{
//			$title = _("Invoice Delivery Details");
//			$delname = _("Invoice before").':';
//		}
//		elseif ($order->trans_type==ST_SALESQUOTE)
//		{
//			$title = _("Quotation Delivery Details");
//			$delname = _("Valid until").':';
//		}
//		else
//		{
//			$title = _("Order Delivery Details");
//			$delname = _("Required Delivery Date").':';
//		}
//		display_heading($title);
//		start_outer_table(TABLESTYLE2, "width=90%");
//		table_section(1);
//
//		locations_list_row(_("Deliver from Location:"), 'Location', null, false, true);
//		if (list_updated('Location'))
//			//$Ajax->activate('items_table');
//
//
//		date_row($delname, 'delivery_date',
//			$order->trans_type==ST_SALESORDER ?  _('Enter requested day of delivery')
//				: $order->trans_type==ST_SALESQUOTE ? _('Enter Valid until Date') : '');
//		text_row(_("Deliver To:"), 'deliver_to', $order->deliver_to, 40, 40,
//			_('Additional identifier for delivery e.g. name of receiving person'));
//
//		textarea_row(_("Address:"), 'delivery_address', $order->delivery_address, 35, 5,
//			_('Delivery address. Default is address of customer branch'));
//
//		table_section(2);
//
//		text_row(_("Contact Phone Number:"), 'phone', $order->phone, 25, 25,
//		    _('Phone number of ordering person. Defaults to branch phone number'));
//		text_row(_("Customer Reference:"), 'cust_ref', $order->cust_ref, 25, 25,
//		  _('Customer reference number for this order (if any)'));
//		textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
//
//		shippers_list_row(_("Delivery Company:"), 'ship_via', $order->ship_via);
//
//		end_outer_table(1);*/
//	}
//	div_end();
//}
function display_delivery_details(&$order)
{
    global $Ajax;

    div_start('delivery');


    if ($order->payment_terms['cash_sale']) {	// Direct payment sale
        $Ajax->activate('items_table');
        display_heading(_('Cash payment'));
        start_table(TABLESTYLE2, "width=60%");

        locations_list_row(_("Deliver from Location:"), 'Location', null, false, true);
        if (list_updated('Location'))
            $Ajax->activate('items_table');
        label_row(_("Cash account:"), $order->pos['bank_account_name']);


        text_row(_("Card Number :"), 'card_num', null, 30, 4);
        sales_persons_list_row( _("Server/Waiter Name:"), 'w_id', $order->w_id);
        //textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
        end_table();
        hidden('delivery_date', $order->due_date);
    } else
    {
        //$order->payment_terms['cash_sale']) {	// Direct payment sale
        $Ajax->activate('items_table');
        display_heading(_('Card payment'));
        start_table(TABLESTYLE2, "width=60%");

        locations_list_row(_("Deliver from Location:"), 'Location', null, false, true);
        if (list_updated('Location'))
            $Ajax->activate('items_table');
        //label_row(_("Cash account:"), $order->pos['bank_account_name']);


        textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
        end_table();
        hidden('delivery_date', $order->due_date);
        /*	if ($order->trans_type==ST_SALESINVOICE)
            {
                $title = _("Delivery Details");
                $delname = _("Due Date").':';
            }
            elseif ($order->trans_type==ST_CUSTDELIVERY)
            {
                $title = _("Invoice Delivery Details");
                $delname = _("Invoice before").':';
            }
            elseif ($order->trans_type==ST_SALESQUOTE)
            {
                $title = _("Quotation Delivery Details");
                $delname = _("Valid until").':';
            }
            else
            {
                $title = _("Order Delivery Details");
                $delname = _("Required Delivery Date").':';
            }
            display_heading($title);
            start_outer_table(TABLESTYLE2, "width=90%");
            table_section(1);

            locations_list_row(_("Deliver from Location:"), 'Location', null, false, true);
            if (list_updated('Location'))
                //$Ajax->activate('items_table');


            date_row($delname, 'delivery_date',
                $order->trans_type==ST_SALESORDER ?  _('Enter requested day of delivery')
                    : $order->trans_type==ST_SALESQUOTE ? _('Enter Valid until Date') : '');
            text_row(_("Deliver To:"), 'deliver_to', $order->deliver_to, 40, 40,
                _('Additional identifier for delivery e.g. name of receiving person'));

            textarea_row(_("Address:"), 'delivery_address', $order->delivery_address, 35, 5,
                _('Delivery address. Default is address of customer branch'));

            table_section(2);

            text_row(_("Contact Phone Number:"), 'phone', $order->phone, 25, 25,
                _('Phone number of ordering person. Defaults to branch phone number'));
            text_row(_("Customer Reference:"), 'cust_ref', $order->cust_ref, 25, 25,
              _('Customer reference number for this order (if any)'));
            textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);

            shippers_list_row(_("Delivery Company:"), 'ship_via', $order->ship_via);

            end_outer_table(1);*/
    }
    div_end();
}
?>
<script>

    function codeAddress() {
        var a = 1;
        document.getElementById(1).style.display = "block";
    }
    window.onload = codeAddress;
</script>

