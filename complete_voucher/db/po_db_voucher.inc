<?php
//----------------------------------------------------------------------------------------

//function delete_po($po)
//{
//	hook_db_prevoid($po, ST_PURCHORDER);
//	$sql = "DELETE FROM ".TB_PREF."purch_orders WHERE order_no=".db_escape($po);
//	db_query($sql, "The order header could not be deleted");
//
//	$sql = "DELETE FROM ".TB_PREF."purch_order_details WHERE order_no =".db_escape($po);
//	db_query($sql, "The order detail lines could not be deleted");
//}

//----------------------------------------------------------------------------------------

function add_po_voucher($Get_PO, $Get_PO_Details)
{
	global $Refs;

	begin_transaction();
	hook_db_prewrite($po_obj, ST_PURCHORDER);
    $date = sql2date($Get_PO['ord_date']);
    if($Get_PO['reference'] == 'auto')
        $reference = 'auto';
    else
        $reference = $Refs->get_next(ST_PURCHORDER, null, sql2date($Get_PO['ord_date']));
     /*Insert to purchase order header record */
     $sql = "INSERT INTO ".TB_PREF."purch_orders(supplier_id, Comments, ord_date, reference, 
     	requisition_no, into_stock_location, delivery_address, total, tax_included, prep_amount,payments,
     	pr,delivery_term,sales_tax,h_text1,h_text2,h_text3,h_comb1,h_comb2,h_comb3,Comments1,Comments2,
     	Comments3,Comments4,Comments5,dimension,dimension2,sale_order,transaction_type) VALUES(";
     $sql .= db_escape($Get_PO['supplier_id']) . "," .
         db_escape($Get_PO['Comments']) . ",'" .
         date2sql($date) . "', " .
		 db_escape($reference) . ", " .
         db_escape($Get_PO['requisition_no']) . ", " .
         db_escape($Get_PO['into_stock_location']) . ", " .
         db_escape($Get_PO['delivery_address']) . ", " .
         db_escape($Get_PO['total']). ", " .
         db_escape($Get_PO['tax_included']). ", " .
         db_escape($Get_PO['prep_amount']). ", " .
         db_escape($Get_PO['payments']). ",".
         db_escape($Get_PO['pr']). ",".
         db_escape($Get_PO['delivery_term']). ",".
         db_escape($Get_PO['sales_tax']). ",".
         db_escape($Get_PO['h_text1']). ",".
         db_escape($Get_PO['h_text2']). ",".
         db_escape($Get_PO['h_text3']). ",".
         db_escape($Get_PO['h_comb1']). ",".
         db_escape($Get_PO['h_comb2']). ",".
         db_escape($Get_PO['h_comb3']). ",".
         db_escape($Get_PO['Comments1']). ",".
         db_escape($Get_PO['Comments2']). ",".
         db_escape($Get_PO['Comments3']). ",".
         db_escape($Get_PO['Comments4']). ",".
         db_escape($Get_PO['Comments5']). ",".
         db_escape($Get_PO['dimension']). ",".
         db_escape($Get_PO['dimension2']). ",".
         db_escape($Get_PO['sale_order']). ",".
         db_escape($Get_PO['transaction_type']). ")";

	db_query($sql, "The purchase order header record could not be inserted");

     /*Get the auto increment value of the order number created from the sql above */
     $order_no = db_insert_id();

     /*Insert the purchase order detail records */
     while($Add_PO_Details = db_fetch($Get_PO_Details))
     {
		$sql = "INSERT INTO ".TB_PREF."purch_order_details (order_no, item_code, description,
		 delivery_date, unit_price, quantity_ordered,text1, text2, text3, text4,text5, text6,
        amount1, amount2, amount3, amount4,amount5, amount6,
        date1, date2, date3,
        combo1, combo2, combo3,combo4, combo5, combo6, 
        con_factor,units_id,discount_percent) VALUES (";
		$sql .= $order_no . ", " . db_escape($Add_PO_Details['item_code']). "," .
		db_escape($Add_PO_Details['description']). ",'" .
		$Add_PO_Details['delivery_date'] . "'," .
		db_escape($Add_PO_Details['unit_price']) . ", " .
		db_escape($Add_PO_Details['quantity_ordered']) . ", "
            .db_escape($Add_PO_Details['text1']).","
            .db_escape($Add_PO_Details['text2']).","
            .db_escape($Add_PO_Details['text3']).","
            .db_escape($Add_PO_Details['text4']).","
            .db_escape($Add_PO_Details['text5']).","
            .db_escape($Add_PO_Details['text6']).","
            .db_escape($Add_PO_Details['amount1']).","
            .db_escape($Add_PO_Details['amount2']).","
            .db_escape($Add_PO_Details['amount3']).","
            .db_escape($Add_PO_Details['amount4']).","
            .db_escape($Add_PO_Details['amount5']).","
            .db_escape($Add_PO_Details['amount6']).","
            .db_escape($Add_PO_Details['date1']).","
            .db_escape($Add_PO_Details['date2']).","
            .db_escape($Add_PO_Details['date3']).","
            .db_escape($Add_PO_Details['combo1']).","
            .db_escape($Add_PO_Details['combo2']).","
            .db_escape($Add_PO_Details['combo3']).","
            .db_escape($Add_PO_Details['combo4']).","
            .db_escape($Add_PO_Details['combo5']).","
            .db_escape($Add_PO_Details['combo6']).","
            .db_escape($Add_PO_Details['con_factor']).","
            .db_escape($Add_PO_Details['units_id']).","
            .db_escape($Add_PO_Details['discount_percent']).")";
		db_query($sql, "One of the purchase order detail records could not be inserted");
//		$po_obj->line_items[$line_no]->po_detail_rec = db_insert_id();
     }

	$Refs->save(ST_PURCHORDER, $order_no, $reference);

//	add_comments(ST_PURCHORDER, $po_obj->order_no, $po_obj->orig_order_date, $po_obj->Comments);

	add_audit_trail(ST_PURCHORDER, $order_no, $date);
	hook_db_postwrite($po_obj, ST_PURCHORDER);
	commit_transaction();

	return $order_no;
}

//----------------------------------------------------------------------------------------

//function update_po(&$po_obj)
//{
//	begin_transaction();
//	hook_db_prewrite($po_obj, ST_PURCHORDER);
//
//    /*Update the purchase order header with any changes */
//    $sql = "UPDATE ".TB_PREF."purch_orders SET Comments=" . db_escape($po_obj->Comments) . ",
//		requisition_no= ". db_escape( $po_obj->supp_ref). ",
//		into_stock_location=" . db_escape($po_obj->Location). ",
//		ord_date='" . date2sql($po_obj->orig_order_date) . "',
//		delivery_address=" . db_escape($po_obj->delivery_address).",
//		total=". db_escape($po_obj->get_trans_total()).",
//		tax_included=". db_escape($po_obj->tax_included).",
//		users=". db_escape($_SESSION["wa_current_user"]->username);
//    $sql .= " WHERE order_no = " . $po_obj->order_no;
//	db_query($sql, "The purchase order could not be updated");
//
//	$sql = "DELETE FROM ".TB_PREF."purch_order_details WHERE order_no="
//		.db_escape($po_obj->order_no);
//	db_query($sql, "could not delete old purch order details");
//
//    /*Now Update the purchase order detail records */
//    foreach ($po_obj->line_items as $po_line)
//    {
//    	$sql = "INSERT INTO ".TB_PREF."purch_order_details (po_detail_item, order_no, item_code,
//    		description, delivery_date, unit_price, quantity_ordered, quantity_received, carton,color,cap,capcolor) VALUES ("
//			.db_escape($po_line->po_detail_rec ? $po_line->po_detail_rec : 0). ","
//			.$po_obj->order_no . ","
//			.db_escape($po_line->stock_id). ","
//			.db_escape($po_line->item_description). ",'"
//			.date2sql($po_line->req_del_date) . "',"
//			.db_escape($po_line->price) . ", "
//			.db_escape($po_line->quantity) . ", "
//			.db_escape($po_line->qty_received) . ", "
//			.db_escape($po_line->carton) .","
//			.db_escape($po_line->color) .","
//			.db_escape($po_line->cap) .","
//			.db_escape($po_line->capcolor) .
//
//
//
//            ")";
//
//		db_query($sql, "One of the purchase order detail records could not be updated");
//    }
//
//	// add_comments(ST_PURCHORDER, $po_obj->order_no, $po_obj->orig_order_date, $po_obj->Comments);
//
//	add_audit_trail($po_obj->trans_type, $po_obj->order_no, Today(), _("Updated."));
//	hook_db_postwrite($po_obj, ST_PURCHORDER);
//	commit_transaction();
//
//	return $po_obj->order_no;
//}

//----------------------------------------------------------------------------------------
//

function read_po_header_voucher($order_no)
{
    $sql = "SELECT 0_purch_orders.*, 0_suppliers.supp_name, 0_suppliers.tax_group_id,
   		0_suppliers.curr_code, 0_locations.location_name
		FROM 0_purch_orders, 0_suppliers, 0_locations
		WHERE 0_purch_orders.supplier_id = 0_suppliers.supplier_id
		AND 0_locations.loc_code = into_stock_location
		AND 0_purch_orders.order_no = ".db_escape($order_no);

   	$result = db_query($sql, "The order cannot be retrieved");
	if (db_num_rows($result) == 1)
        return db_fetch($result);
	display_db_error("FATAL : duplicate purchase order found", "", true);
	return false;
}

//----------------------------------------------------------------------------------------

function read_po_items_voucher($order_no)
{
	$sql = "SELECT 0_purch_order_details.*, units
		FROM 0_purch_order_details
		LEFT JOIN 0_stock_master
		ON 0_purch_order_details.item_code=0_stock_master.stock_id
		WHERE order_no =".db_escape($order_no);
	$sql .= " ORDER BY po_detail_item";

	return db_query($sql, "The lines on the purchase order cannot be retrieved");
}
function read_current_db_po_items_voucher($order_no)
{
	$sql = "SELECT ".TB_PREF."purch_order_details.*, units
		FROM ".TB_PREF."purch_order_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.item_code=".TB_PREF."stock_master.stock_id
		WHERE order_no =".db_escape($order_no);
	$sql .= " ORDER BY po_detail_item";

	return db_query($sql, "The lines on the purchase order cannot be retrieved");
}

function read_current_db_po_header_voucher($order_no)
{
	$sql = "SELECT *
		FROM ".TB_PREF."purch_orders
		WHERE order_no =".db_escape($order_no);
	$sql .= " ORDER BY po_detail_item";

	return db_query($sql, "The lines on the purchase order cannot be retrieved");
}

//----------------------------------------------------------------------------------------

//function read_po($order_no, &$order, $open_items_only=false)
//{
//	$result = read_po_header($order_no, $order);
//
//	if ($result)
//		read_po_items($order_no, $order, $open_items_only);
//}

//----------------------------------------------------------------------------------------

//function get_po_items($order_no)
//{
//	$sql = "SELECT item_code, quantity_ordered, quantity_received, qty_invoiced,color,cap,capcolor
//		FROM ".TB_PREF."purch_order_details
//		WHERE order_no=".db_escape($order_no)
//		." ORDER BY po_detail_item";
//
//	$result = db_query($sql, "could not query purch order details");
//    check_db_error("Could not check that the details of the purchase order had not been changed by another user ", $sql);
//    return $result;
//}
//----------------------------------------------------------------------------------------

//function get_short_info($stock_id)
//{
//	$sql = "SELECT description, units, mb_flag
//		FROM ".TB_PREF."stock_master WHERE stock_id = ".db_escape($stock_id);
//
//	return db_query($sql,"The stock details for " . $stock_id . " could not be retrieved");
//}
//
//function get_sql_for_po_search_completed()
//{
//	global $order_number, $selected_stock_item;;
//
//	$sql = "SELECT
//		porder.order_no,
//		porder.reference,
//		supplier.supp_name,
//		location.location_name,
//		porder.requisition_no,
//		porder.ord_date,
//		supplier.curr_code,
//		Sum(line.unit_price*line.quantity_ordered) AS OrderValue,
//		porder.users,
//		porder.into_stock_location
//		FROM ".TB_PREF."purch_orders as porder, "
//			.TB_PREF."purch_order_details as line, "
//			.TB_PREF."suppliers as supplier, "
//			.TB_PREF."locations as location
//		WHERE porder.order_no = line.order_no
//		AND porder.supplier_id = supplier.supplier_id
//		AND location.loc_code = porder.into_stock_location ";
//
//	if (isset($_GET['supplier_id']))
//		$sql .= "AND supplier.supplier_id=".@$_GET['supplier_id']." ";
//	if (isset($order_number) && $order_number != "")
//	{
//		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
//	}
//	else
//	{
//
//		$data_after = date2sql($_POST['OrdersAfterDate']);
//		$date_before = date2sql($_POST['OrdersToDate']);
//
//		$sql .= " AND porder.ord_date >= '$data_after'";
//		$sql .= " AND porder.ord_date <= '$date_before'";
//
//		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != ALL_TEXT)
//		{
//			$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
//		}
//		if (isset($selected_stock_item))
//		{
//			$sql .= " AND line.item_code=".db_escape($selected_stock_item);
//		}
//
//	} //end not order number selected
//
//	$sql .= " GROUP BY porder.order_no";
//	return $sql;
//}

//function get_sql_for_po_search()
//{
//	global $all_items, $order_number, $selected_stock_item;;
//
//	$sql = "SELECT
//		porder.order_no,
//		porder.reference,
//		supplier.supp_name,
//		location.location_name,
//		porder.requisition_no,
//		porder.ord_date,
//		supplier.curr_code,
//		Sum(line.unit_price*line.quantity_ordered) AS OrderValue,
//		Sum(line.delivery_date < '". date2sql(Today()) ."'
//		AND (line.quantity_ordered > line.quantity_received)) As OverDue
//		FROM "
//			.TB_PREF."purch_orders as porder, "
//			.TB_PREF."purch_order_details as line, "
//			.TB_PREF."suppliers as supplier, "
//			.TB_PREF."locations as location
//		WHERE porder.order_no = line.order_no
//		AND porder.supplier_id = supplier.supplier_id
//		AND location.loc_code = porder.into_stock_location
//		AND (line.quantity_ordered > line.quantity_received) ";
//
//	if (isset($order_number) && $order_number != "")
//	{
//		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
//	}
//	else
//	{
//		$data_after = date2sql($_POST['OrdersAfterDate']);
//		$data_before = date2sql($_POST['OrdersToDate']);
//
//		$sql .= "  AND porder.ord_date >= '$data_after'";
//		$sql .= "  AND porder.ord_date <= '$data_before'";
//
//		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != $all_items)
//		{
//			$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
//		}
//
//		if (isset($selected_stock_item))
//		{
//			$sql .= " AND line.item_code=".db_escape($selected_stock_item);
//		}
//	} //end not order number selected
//
//	$sql .= " GROUP BY porder.order_no";
//	return $sql;
//}
//
//function get_cap_colours_names  ($id)
//{
//    $sql = "SELECT cap_colour FROM ".TB_PREF."cap_colour WHERE id=".db_escape($id);
//    $result = db_query($sql, "Error");
//    $myrow = db_fetch_row($result);
//    return $myrow[0];
//}
//function get_cap_names ($selected_id)
//{
//    $sql = "SELECT cap FROM ".TB_PREF."cap WHERE id=".db_escape($selected_id);
//
//    $result = db_query($sql, "Error");
//    $myrow = db_fetch_row($result);
//    return $myrow[0];
//}
//
//function get_colour_names ($selected_id)
//{
//    $sql = "SELECT color  FROM ".TB_PREF."color WHERE id=".db_escape($selected_id);
//    $result = db_query($sql, "Error");
//    $myrow = db_fetch_row($result);
//    return $myrow[0];
//}
////
////function get_packing_names ($selected_id)
////{
////    $sql = "SELECT packing FROM ".TB_PREF."packing WHERE id=".db_escape($selected_id);
////    $result = db_query($sql, "could not get sales type");
////
////    $row = db_fetch_row($result);
////    return $row[0];
////}


?>