<?php

// ------------------------------------------------------------------------------
//			supp.credit_limit - Sum(IFNULL(IF(trans.type=".ST_SUPPCREDIT.", -1, 1)  //dz 21.8.17
function get_supplier_details_to_order(&$order, $supplier_id)
{
	$sql = "SELECT curr_code, supp_name, tax_group_id, supp.tax_included,supp.dimension_id,supp.dimension2_id,
		Sum(IFNULL(IF(trans.type IN(".implode(',', array(ST_SUPPCREDIT, ST_SUPPAYMENT, ST_BANKPAYMENT, ST_CPV))."), 1, 1) 
				* (ov_amount + ov_gst + ov_discount),0))-	supp.credit_limit   as cur_credit,
				terms.terms, terms.days_before_due, terms.day_in_following_month
		FROM ".TB_PREF."suppliers supp
			 LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id
			 LEFT JOIN ".TB_PREF."payment_terms terms ON supp.payment_terms=terms.terms_indicator
		WHERE supp.supplier_id = ".db_escape($supplier_id)."
		
		GROUP BY
			  supp.supp_name";

	$result = db_query($sql, "The supplier details could not be retreived");
	$myrow = db_fetch($result);

	$order->credit = $myrow["cur_credit"];
	$order->terms = array( 
		'description' => $myrow['terms'],
		'days_before_due' => $myrow['days_before_due'], 
		'day_in_following_month' => $myrow['day_in_following_month'] );

	$_POST['supplier_id'] = $supplier_id;
	$_POST['supplier_name'] = $myrow["supp_name"];
	$_POST['curr_code'] = $myrow["curr_code"];
$_POST['dimension'] = $myrow["dimension_id"];
	$_POST['dimension2'] = $myrow["dimension2_id"];
	$order->set_supplier($supplier_id, $myrow["supp_name"], $myrow["curr_code"], 
		$myrow["tax_group_id"], $myrow["tax_included"]);
}

//----------------------------------------------------------------------------------------


//----------------------------------------------------------------------------



function get_sql_for_po_search_outstanding()
{
	
global $all_items, $order_number, $selected_stock_item , $lading_no ,$po_number ,$lc_ref;

	
	$sql = "SELECT 
		
porder.reference, 
	
	porder.lc_ref,
            
    porder.lading_no,
    
    line.description,
		
supplier.supp_name, 
	
	location.location_name,
		
	
	porder.ord_date,
		
porder.arrival_date,

             
   SUM(line.quantity_ordered-line.quantity_received) AS QtyDUE,

	
	supplier.curr_code,
	
	Sum(-(line.quantity_received-line.quantity_ordered)*line.unit_price) AS OrderValue,
		Sum(line.delivery_date < '". date2sql(Today()) ."'
		AND (line.quantity_ordered > line.quantity_received)) As OverDue,
		porder.order_no
		
FROM ".TB_PREF."purch_orders as porder, 
".TB_PREF."purch_order_details as line, 
".TB_PREF."suppliers as supplier,
 ".TB_PREF."locations as location
	
	WHERE porder.order_no = line.order_no

		AND porder.supplier_id = supplier.supplier_id
	
	AND location.loc_code = porder.into_stock_location
	
	AND (line.quantity_ordered > line.quantity_received)
	AND porder.transaction_type = 1";

	

if (isset($order_number) && $order_number != "")

	{
		$sql .= " AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}


	else
	{
		$data_after = date2sql($_POST['OrdersAfterDate']);
		$data_before = date2sql($_POST['OrdersToDate']);

		$sql .= "  AND porder.ord_date >= '$data_after'";

		$sql .= "  AND porder.ord_date <= '$data_before'";

		
if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != $all_items)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
		}

		
if (isset($selected_stock_item))
	
	{
			$sql .= " AND line.item_code=".db_escape($selected_stock_item);
		}


if (isset($lading_no) && $lading_no!= "")
	{
		$sql .= "AND porder.receive_ref LIKE ".db_escape('%'. $lading_no. '%');
	}

if (isset($lc_ref) && $lc_ref!= "")
	{
		$sql .= "AND porder.lc_ref LIKE ".db_escape('%'. $lc_ref. '%');
	}


if (isset($po_number) && $po_number!= "")
	{
	
	$sql .= "AND porder.order_no LIKE ".db_escape('%'. $po_number. '%');

	}




	} //end not order number selected

	
$sql .= " GROUP BY porder.order_no";
	return $sql;
}





//---------------------------------------------------------------------------

function get_sql_for_po_search2($from, $to, $supplier_id=ALL_TEXT, $location=ALL_TEXT, $order_number='', $stock_id='')
{
	$sql = "SELECT 
		porder.order_no, 
		porder.reference,
		supplier.supp_name, 
		location.location_name,
		porder.requisition_no, 
		porder.ord_date,
		supplier.curr_code,
		Sum(line.unit_price*line.quantity_ordered) AS OrderValue,
		Sum(line.delivery_date < '". date2sql(Today()) ."'
		AND (line.quantity_ordered > line.quantity_received)) As OverDue,
		porder.approval
		FROM ".TB_PREF."purch_orders as porder,"
			.TB_PREF."purch_order_details as line, "
			.TB_PREF."suppliers as supplier, "
			.TB_PREF."locations as location
		WHERE porder.order_no = line.order_no
		AND porder.supplier_id = supplier.supplier_id
		AND location.loc_code = porder.into_stock_location
		AND (line.quantity_ordered > line.quantity_received)
		AND porder.transaction_type = 0";

	if ($order_number != "")
	{
		$sql .= " AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}
	else
	{
		$data_after = date2sql($from);
		$data_before = date2sql($to);

		$sql .= "  AND porder.ord_date >= '$data_after'";
		$sql .= "  AND porder.ord_date <= '$data_before'";

		if ($location != ALL_TEXT)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($location);
		}

		if ($stock_id != '')
		{
			$sql .= " AND line.item_code=".db_escape($stock_id);
		}
		if ($supplier_id != ALL_TEXT)
			$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);
	} //end not order number selected

	$sql .= " GROUP BY porder.order_no";
	return $sql;
}
//----------------------------------------------------------------------------------------

function delete_po($po)
{
	global $Refs;

	begin_transaction();
	hook_db_prevoid($po, ST_PURCHORDER);
	$sql = "DELETE FROM ".TB_PREF."purch_orders WHERE order_no=".db_escape($po);
	db_query($sql, "The order header could not be deleted");

	$sql = "DELETE FROM ".TB_PREF."purch_order_details WHERE order_no =".db_escape($po);
	db_query($sql, "The order detail lines could not be deleted");

	$Refs->restore_last(ST_PURCHORDER, $po);
	commit_transaction();
}

//----------------------------------------------------------------------------------------

function add_po(&$po_obj)
{
	global $Refs;

	begin_transaction();
	hook_db_prewrite($po_obj, ST_PURCHORDER);

     /*Insert to purchase order header record */
     $sql = "INSERT INTO ".TB_PREF."purch_orders (supplier_id, Comments, ord_date, reference, 
     	requisition_no, into_stock_location, delivery_address, total, tax_included, prep_amount,payments,
     	pr,delivery_term,sales_tax,h_text1,h_text2,h_text3,h_comb1,h_comb2,h_comb3,Comments1,
     	Comments2,Comments3,Comments4,Comments5,dimension,dimension2,sale_order,transaction_type,invoice_to,discount1,
     	disc1,lading_date,arrival_date,receive_ref,lc_ref,lading_no) VALUES(";
     $sql .= db_escape($po_obj->supplier_id) . "," .
         db_escape($po_obj->Comments) . ",'" .
         date2sql($po_obj->orig_order_date) . "', " .
		 db_escape($po_obj->reference) . ", " .
         db_escape($po_obj->supp_ref) . ", " .
         db_escape($po_obj->Location) . ", " .
         db_escape($po_obj->delivery_address) . ", " .
         db_escape($po_obj->get_trans_total()). ", " .
         db_escape($po_obj->tax_included). ", " .
		 db_escape($po_obj->prep_amount). ", " .
		 db_escape($po_obj->payments). ",".
		 db_escape($po_obj->pr). ",".
		 db_escape($po_obj->delivery_term). ",".
         db_escape($po_obj->sales_tax). ",".
         db_escape($po_obj->h_text1). ",".
         db_escape($po_obj->h_text2). ",".
         db_escape($po_obj->h_text3). ",".
         db_escape($po_obj->h_comb1). ",".
         db_escape($po_obj->h_comb2). ",".
         db_escape($po_obj->h_comb3). ",".
         db_escape($po_obj->Comments1). ",".
         db_escape($po_obj->Comments2). ",".
         db_escape($po_obj->Comments3). ",".
         db_escape($po_obj->Comments4). ",".
		 db_escape($po_obj->Comments5). ",".
         db_escape($po_obj->dimension). ",".
		 db_escape($po_obj->dimension2). "
	,".db_escape($po_obj->sale_order_ref). ",
		  ". db_escape($po_obj->transaction_type). ",
		  ". db_escape($po_obj->invoice_to). ",
		  ". db_escape($po_obj->discount1). ",
		   ". db_escape($po_obj->disc1). ",
		
		 ". db_escape(date2sql($po_obj->lading_date)). ",
		 
		 ". db_escape(date2sql($po_obj->arrival_date)). ",
		  ". db_escape($po_obj->receive_ref). ",
		  ". db_escape($po_obj->lc_ref). ",
		  ". db_escape($po_obj->lading_no). "
		  
		 )";

	db_query($sql, "The purchase order header record could not be inserted");

     /*Get the auto increment value of the order number created from the sql above */
     $po_obj->order_no = db_insert_id();

     /*Insert the purchase order detail records */
     foreach ($po_obj->line_items as $line_no => $po_line)
     {
		  $item=get_item($po_line->stock_id);
		 $pref=get_company_prefs();
		 $myrow_factors = get_company_item_pref('con_factor');
		 $item_info = get_item_edit_info($po_line->stock_id);
		 if($pref['alt_uom'] == 1) {
			if ($po_line->units_id != $item['units']) {
				 if ($item['con_type'] == 0) {
						 if ($myrow_factors['purchase_enable'] == 0) {
							 $po_line->con_factor = $item_info['con_factor'];
							 $qty = $po_line->quantity / $item_info['con_factor'];
						 }
						 else{
							 $po_line->con_factor;
							 $qty = $po_line->quantity / $po_line->con_factor;
						 }
				 } else {

					 $qty = $po_line->con_factor / $po_line->quantity;
				 }
			 } else {
				if ($myrow_factors['purchase_enable'] == 0) {
					$po_line->con_factor = $item_info['con_factor'];
				}
				else{
					$po_line->con_factor;
				}
				$po_line->con_factor ;
				 $qty = $po_line->quantity;
			 }
		 }
		 else{
			 $po_line->con_factor = $item_info['con_factor'];
			 $qty = $po_line->quantity;
		 }
		$sql = "INSERT INTO ".TB_PREF."purch_order_details (order_no, item_code, description, delivery_date,	
		unit_price,	quantity_ordered,text1, text2, text3, text4,text5, text6, text7,
				amount1, amount2, amount3, amount4,amount5, amount6,
				date1, date2, date3,
				combo1, combo2, combo3,combo4, combo5, combo6, con_factor,units_id,discount_percent) VALUES (";
		$sql .= $po_obj->order_no . ", " . db_escape($po_line->stock_id). "," .
		db_escape($po_line->item_description). ",'" .
		date2sql($po_line->req_del_date) . "'," .
		db_escape($po_line->price) . ", " .
		db_escape($qty). ", "
		.db_escape($po_line->text1).","
			.db_escape($po_line->text2).","
			.db_escape($po_line->text3).","
			.db_escape($po_line->text4).","
			.db_escape($po_line->text5).","
			.db_escape($po_line->text6).","
			.db_escape($po_line->text7).","
            .db_escape($po_line->amount1).","
			.db_escape($po_line->amount2).","
			.db_escape($po_line->amount3).","
			.db_escape($po_line->amount4).","
			.db_escape($po_line->amount5).","
			.db_escape($po_line->amount6).","
			.db_escape(date2sql($po_line->date1)).","
			.db_escape(date2sql($po_line->date2)).","
			.db_escape(date2sql($po_line->date3)).","
			.db_escape($po_line->combo1).","
			.db_escape($po_line->combo2).","
			.db_escape($po_line->combo3).","
			.db_escape($po_line->combo4).","
			.db_escape($po_line->combo5).","
			.db_escape($po_line->combo6).","
			.db_escape($po_line->con_factor).","
            .db_escape($po_line->units_id).","
            .db_escape($po_line->discount_percent).")";
		db_query($sql, "One of the purchase order detail records could not be inserted");
		$po_obj->line_items[$line_no]->po_detail_rec = db_insert_id();
     }

	$Refs->save(ST_PURCHORDER, $po_obj->order_no, $po_obj->reference);

	add_audit_trail(ST_PURCHORDER, $po_obj->order_no, $po_obj->orig_order_date);
	hook_db_postwrite($po_obj, ST_PURCHORDER);
	commit_transaction();

	return $po_obj->order_no;
}
//----------------------------------------------------------------------------------------
function get_sql_for_pr_search_completed()
{
	global $order_number, $selected_stock_item , $supplier_id, $party;

	$sql = "SELECT 
		prorder.requisition_id, 
		line.supplier_id, 
		supplier.supp_name, 
		prorder.application_date, 
		supplier.curr_code, 
		Sum(line.price*line.quantity) AS OrderValue
		FROM ".TB_PREF."requisitions as prorder, "
			.TB_PREF."requisition_details as line, "
			.TB_PREF."suppliers as supplier
		WHERE prorder.requisition_id = line.requisition_id
		AND line.supplier_id = supplier.supplier_id";

//	if (isset($_GET['supplier_id']))
//		$sql .= "AND supplier.supplier_id=".@$_GET['supplier_id']." ";
	if (isset($order_number) && $order_number != "")
	{
		$sql .= " AND prorder.requisition_id = ".db_escape( $order_number );
	}
	
		if (isset($party) && $party != "")
	{
		$sql .= " AND line.supplier_id = ".db_escape( $party );
	}
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
		if (isset($selected_stock_item))
		{
			$sql .= " AND line.item_code=".db_escape($selected_stock_item);
		}
//
		if ($supplier_id != null)
		{
			$sql .= " AND line.supplier_id=".db_escape($supplier_id);
		}
//
//	} //end not order number selected

	$sql .= " GROUP BY line.requisition_id";
	return $sql;
}
//----------------------------------------------------------------------------------------

function update_po(&$po_obj)
{
begin_transaction();
	hook_db_prewrite($po_obj, ST_PURCHORDER);

    /*Update the purchase order header with any changes */
    $sql = "UPDATE ".TB_PREF."purch_orders SET Comments=" . db_escape($po_obj->Comments) . ",
		requisition_no= ". db_escape( $po_obj->supp_ref). ",
		into_stock_location=" . db_escape($po_obj->Location). ",
		ord_date='" . date2sql($po_obj->orig_order_date) . "',
		delivery_address=" . db_escape($po_obj->delivery_address).",
		total=". db_escape($po_obj->get_trans_total()).",
		prep_amount=". db_escape($po_obj->prep_amount).",
tax_included=". db_escape($po_obj->tax_included).",
payments=". db_escape($po_obj->payments).",
pr=". db_escape($po_obj->pr).",
delivery_term=". db_escape($po_obj->delivery_term).",
sales_tax=". db_escape($po_obj->sales_tax).",
h_text1=". db_escape($po_obj->h_text1).",
h_text2=". db_escape($po_obj->h_text2).",
h_text3=". db_escape($po_obj->h_text3).",
h_comb1=". db_escape($po_obj->h_comb1).",
h_comb2=". db_escape($po_obj->h_comb2).",
h_comb3=". db_escape($po_obj->h_comb3).",
Comments1=". db_escape($po_obj->Comments1).",
Comments2=". db_escape($po_obj->Comments2).",
Comments3=". db_escape($po_obj->Comments3).",
Comments4=". db_escape($po_obj->Comments4).",
Comments5=". db_escape($po_obj->Comments5).",
dimension=". db_escape($po_obj->dimension).",
dimension2=". db_escape($po_obj->dimension2).",
invoice_to=". db_escape($po_obj->invoice_to).",
invoice_to=". db_escape($po_obj->invoice_to).",
discount1=". db_escape($po_obj->discount1).",
disc1=". db_escape($po_obj->disc1).",
transaction_type=". db_escape($po_obj->transaction_type).",

lading_date=". db_escape(date2sql($po_obj->lading_date)).",
arrival_date=". db_escape(date2sql($po_obj->arrival_date)).",
 receive_ref=". db_escape($po_obj->receive_ref).",
 lc_ref=". db_escape($po_obj->lc_ref).",
 supplier_id=". db_escape($po_obj->supplier_id).",
 lading_no =". db_escape($po_obj->lading_no);

    $sql .= " WHERE order_no = " . $po_obj->order_no;
	db_query($sql, "The purchase order could not be updated");

	$sql = "DELETE FROM ".TB_PREF."purch_order_details WHERE order_no="
		.db_escape($po_obj->order_no);
	db_query($sql, "could not delete old purch order details");

    /*Now Update the purchase order detail records */
    foreach ($po_obj->line_items as $po_line)
    {
		$item=get_item($po_line->stock_id);
		$pref=get_company_prefs();
		$myrow_factors = get_company_item_pref('con_factor');
		$item_info = get_item_edit_info($po_line->stock_id);
		if($pref['alt_uom'] == 1) {
			if ($po_line->units_id != $item['units']) {
				if ($item['con_type'] == 0) {
					if ($myrow_factors['purchase_enable'] == 0) {
						$po_line->con_factor = $item_info['con_factor'];
						$qty = $po_line->quantity / $item_info['con_factor'];
                        $qty_received = $po_line->qty_received / $item_info['con_factor'];
					}
					else{
						$po_line->con_factor;
						$qty = $po_line->quantity / $po_line->con_factor;
                        $qty_received = $po_line->qty_received / $po_line->con_factor;
					}
				} else {
					$qty = $po_line->con_factor / $po_line->quantity;
                    $qty_received = $po_line->con_factor / $po_line->qty_received;
				}
			} else {
				$qty = $po_line->quantity;
                $qty_received = $po_line->qty_received;
			}


		}
		else{
			$qty = $po_line->quantity;
            $qty_received = $po_line->qty_received;
		}
		$sql = "INSERT INTO ".TB_PREF."purch_order_details (po_detail_item, order_no, item_code, 
    		description, delivery_date, unit_price, quantity_ordered, quantity_received,
    		text1, text2, text3, text4,text5, text6, text7,
				amount1, amount2, amount3, amount4,amount5, amount6,
				date1, date2, date3,
				combo1, combo2, combo3,combo4, combo5, combo6, con_factor, units_id, purpose, discount_percent) VALUES ("
			.db_escape($po_line->po_detail_rec ? $po_line->po_detail_rec : 0). ","
			.$po_obj->order_no . ","
			.db_escape($po_line->stock_id). ","
			.db_escape($po_line->item_description). ",'"
			.date2sql($po_line->req_del_date) . "',"
			.db_escape($po_line->price) . ", "
			.db_escape($qty) . ", "
			.db_escape($qty_received) . ", "
			.db_escape($po_line->text1).","
			.db_escape($po_line->text2).","
			.db_escape($po_line->text3).","
			.db_escape($po_line->text4).","
			.db_escape($po_line->text5).","
			.db_escape($po_line->text6).","
			.db_escape($po_line->text7).","

			.db_escape($po_line->amount1).","
			.db_escape($po_line->amount2).","
			.db_escape($po_line->amount3).","
			.db_escape($po_line->amount4).","
			.db_escape($po_line->amount5).","
			.db_escape($po_line->amount6).","
			.db_escape(date2sql($po_line->date1)).","
			.db_escape(date2sql($po_line->date2)).","
			.db_escape(date2sql($po_line->date3)).","
			.db_escape($po_line->combo1).","
			.db_escape($po_line->combo2).","
			.db_escape($po_line->combo3).","
			.db_escape($po_line->combo4).","
			.db_escape($po_line->combo5).","
			.db_escape($po_line->combo6).","
			.db_escape($po_line->con_factor).","
			.db_escape($po_line->units_id).","
            .db_escape($po_line->purpose).","
			.db_escape($po_line->discount_percent).")";
		db_query($sql, "One of the purchase order detail records could not be updated");
    }

	reallocate_payments($po_obj->order_no, ST_PURCHORDER, $po_obj->orig_order_date, $po_obj->get_trans_total(),
        $po_obj->prepayments, $po_obj->supplier_id); //ansar 26-08-17

	add_audit_trail($po_obj->trans_type, $po_obj->order_no, Today(), _("Updated."));
	hook_db_postwrite($po_obj, ST_PURCHORDER);
	commit_transaction();

	return $po_obj->order_no;
}

//----------------------------------------------------------------------------------------

function read_po_header($order_no, &$order)
{
    	$sql = "SELECT po.*, supplier.*, loc.location_name 
		FROM ".TB_PREF."purch_orders po,"
			.TB_PREF."suppliers supplier,"
			.TB_PREF."locations loc
		WHERE po.supplier_id = supplier.supplier_id
		AND loc.loc_code = into_stock_location
		AND po.order_no = ".db_escape($order_no);

   	$result = db_query($sql, "The order cannot be retrieved");

	if (db_num_rows($result) == 1)
	{

      	$myrow = db_fetch($result);

      	$order->trans_type = ST_PURCHORDER;
      	$order->order_no = $order_no;

      	$order->set_supplier($myrow["supplier_id"], $myrow["supp_name"], $myrow["curr_code"],
      		$myrow['tax_group_id'], $myrow["tax_included"]);

		$order->credit = get_current_supp_credit($order->supplier_id);

      	$order->orig_order_date = sql2date($myrow["ord_date"]);
      	$order->Comments = $myrow["comments"];
        $order->Comments1 = $myrow["Comments1"];
        $order->Comments2 = $myrow["Comments2"];
        $order->Comments3 = $myrow["Comments3"];
        $order->Comments4 = $myrow["Comments4"];
        $order->Comments5 = $myrow["Comments5"];
      	$order->Location = $myrow["into_stock_location"];
      	$order->supp_ref = $myrow["requisition_no"];
      	$order->reference = $myrow["reference"];
      	$order->receive_ref = $myrow["receive_ref"];

       	$order->lc_ref = $myrow["lc_ref"];
       	$order->lading_no = $myrow["lading_no"];
      	$order->lading_date = sql2date($myrow["lading_date"]);
      	$order->arrival_date = sql2date($myrow["arrival_date"]);

        $order->pr= $myrow["pr"];
        $order->delivery_term= $myrow["delivery_term"];
        $order->payments= $myrow["payments"];
        $order->sales_tax= $myrow["sales_tax"];
        $order->h_text1= $myrow["h_text1"];
        $order->h_text2= $myrow["h_text2"];
        $order->h_text3= $myrow["h_text3"];
        $order->h_comb1= $myrow["h_comb1"];
        $order->h_comb2= $myrow["h_comb2"];
        $order->h_comb3= $myrow["h_comb3"];

      	$order->delivery_address = $myrow["delivery_address"];
      	$order->alloc = $myrow["alloc"];
      	$order->prep_amount = $myrow["prep_amount"];
        $order->prepayments = get_payments_for($order_no, ST_PURCHORDER, $myrow["supplier_id"]);//ansar 26-08-17
        $order->dimension= $myrow["dimension"];
        $order->dimension2= $myrow["dimension2"];
       	$order->transaction_type = $myrow["transaction_type"];
        $order->discount1 = $myrow["discount1"];
        $order->discount2 = $myrow["discount2"];


      	return true;
	}

	display_db_error("FATAL : duplicate purchase order found", "", true);
	return false;
}

//----------------------------------------------------------------------------------------

function read_po_items($order_no, &$order, $open_items_only=false)
{
	/*now populate the line po array with the purchase order details records */

	$sql = "SELECT poline.*, units
		FROM ".TB_PREF."purch_order_details poline
			LEFT JOIN ".TB_PREF."stock_master item	ON poline.item_code=item.stock_id
		WHERE order_no =".db_escape($order_no);

    if ($open_items_only)
 		$sql .= " AND (poline.quantity_ordered > poline.quantity_received) ";

	$sql .= " ORDER BY po_detail_item";

	$result = db_query($sql, "The lines on the purchase order cannot be retrieved");

    if (db_num_rows($result) > 0)
    {
		while ($myrow = db_fetch($result))
        {
        	$data = get_purchase_data($order->supplier_id, $myrow['item_code']);
        	if ($data !== false)
        	{
        		if ($data['supplier_description'] != "")
        			$myrow['description'] = $data['supplier_description'];
        	}		
            if (is_null($myrow["units"]))
            {
		       	$units = "";
            }
            else
            {
                $units = $myrow["units"];
            }

			$pref=get_company_prefs();
			if($pref['alt_uom'] == 1 ) {
				$item = get_item($myrow["item_code"]);
				$dec = 2;
				if ($myrow['units_id'] != $item['units']) {

					if ($item['con_type'] == 0) {
						$qty = round2($myrow["quantity_ordered"] * $myrow['con_factor'], $dec);
						$quantity_received = round2($myrow["quantity_received"] * $myrow['con_factor'], $dec);
					} else {

						$qty =  $myrow["quantity_ordered"] / $myrow['con_factor'];
						$quantity_received= $myrow['quantity_received'] / $myrow["quantity_ordered"];
					}
				} else {

					$qty = $myrow["quantity_ordered"];
					$quantity_received = $myrow['quantity_received'];
				}
			}
			else{
				$qty = $myrow["quantity_ordered"];
				$quantity_received = $myrow['quantity_received'];
			}
			if ($order->add_to_order($order->lines_on_order, $myrow["item_code"],
				$qty,$myrow["description"],
            	$myrow["unit_price"],$units, sql2date($myrow["delivery_date"]),
            	$myrow["qty_invoiced"], /*$myrow["quantity_received"]*/$quantity_received, $myrow['text1'], $myrow['text2'], $myrow['text3'], $myrow['text4'], $myrow['text5'], $myrow['text6'],$myrow['text7'],
				$myrow['amount1'], $myrow['amount2'], $myrow['amount3'], $myrow['amount4'], 
				$myrow['amount5'], $myrow['amount6'], sql2date($myrow['date1']), sql2date($myrow['date2']), sql2date($myrow['date3']),
				$myrow['combo1'], $myrow['combo2'], $myrow['combo3'], $myrow['combo4'], $myrow['combo5'], $myrow['combo6'],
				0,0,$myrow['con_factor'],$myrow['units_id'],$myrow['purpose'],null,null,null,$myrow['discount_percent'])) {
				$order->line_items[$order->lines_on_order-1]->po_detail_rec = $myrow["po_detail_item"];
			}
        } /* line po from purchase order details */
    } //end of checks on returned data set
}

//----------------------------------------------------------------------------------------

function read_po($order_no, &$order, $open_items_only=false)
{
	$result = read_po_header($order_no, $order);

	if ($result)
		read_po_items($order_no, $order, $open_items_only);
}

//----------------------------------------------------------------------------------------

function get_sql_for_po_search_completed_ozone()
{
	global $order_number, $selected_stock_item , $supplier_id,$lading_no,$po_number;

	$sql = "SELECT 
		porder.order_no, 
		porder.lc_ref,
        porder.receive_ref,
        line.description,

		supplier.supp_name, 
		location.location_name,
		
		porder.ord_date, 
		porder.arrival_date,
		supplier.curr_code, 
		Sum(line.unit_price*line.quantity_ordered) AS OrderValue,
		porder.into_stock_location
		FROM ".TB_PREF."purch_orders as porder, "
			.TB_PREF."purch_order_details as line, "
			.TB_PREF."suppliers as supplier, "
			.TB_PREF."locations as location
		WHERE porder.order_no = line.order_no
		AND porder.supplier_id = supplier.supplier_id
		AND location.loc_code = porder.into_stock_location ";

	if (isset($_GET['supplier_id']))
		$sql .= "AND supplier.supplier_id=".@$_GET['supplier_id']." ";
	if (isset($order_number) && $order_number != "")
	{
		$sql .= "AND porder.lc_ref LIKE ".db_escape('%'. $order_number . '%');
	}
	else
	{

		$data_after = date2sql($_POST['OrdersAfterDate']);
		$date_before = date2sql($_POST['OrdersToDate']);

		$sql .= " AND porder.ord_date >= '$data_after'";
		$sql .= " AND porder.ord_date <= '$date_before'";

		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != ALL_TEXT)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
		}
		if (isset($selected_stock_item))
		{
			$sql .= " AND line.item_code=".db_escape($selected_stock_item);
		}

		if ($supplier_id != null)
		{
			$sql .= " AND porder.supplier_id=".db_escape($supplier_id);
		}


if (isset($lading_no) && $lading_no!= "")
	{
		$sql .= "AND porder.receive_ref LIKE ".db_escape('%'. $lading_no. '%');
	}


if (isset($po_number) && $po_number!= "")
	{
		$sql .= "AND porder.order_no LIKE ".db_escape('%'. $po_number. '%');
	}





	} //end not order number selected

	$sql .= " GROUP BY porder.order_no";
	return $sql;
}	

function get_po_items($order_no)
{
	$sql = "SELECT item_code, quantity_ordered, quantity_received, qty_invoiced,
 con_factor, units_id
		FROM ".TB_PREF."purch_order_details
		WHERE order_no=".db_escape($order_no)
		." ORDER BY po_detail_item";

	$result = db_query($sql, "could not query purch order details");
    check_db_error("Could not check that the details of the purchase order had not been changed by another user ", $sql);
    return $result;
}
//----------------------------------------------------------------------------------------

function get_short_info($stock_id)
{
	$sql = "SELECT description, units, mb_flag
		FROM ".TB_PREF."stock_master WHERE stock_id = ".db_escape($stock_id);

	return db_query($sql,"The stock details for " . $stock_id . " could not be retrieved");
}

function get_sql_for_po_search_completed($from, $to, $supplier_id=ALL_TEXT, $location=ALL_TEXT,
	$order_number = '', $stock_id = '', $also_closed=false,$po_number)
{
	$sql = "SELECT 
		porder.order_no, 
		porder.reference, 
		supplier.supp_name, 
		location.location_name,
		porder.requisition_no, 
		porder.ord_date, 
		supplier.curr_code, 
		porder.total AS OrderValue,
		porder.into_stock_location,
		chk.isopen,
		porder.approval
		FROM ".TB_PREF."purch_orders as porder
				LEFT JOIN (
					SELECT order_no, SUM(quantity_ordered-quantity_received + quantity_ordered-qty_invoiced) isopen
					FROM ".TB_PREF."purch_order_details
					GROUP BY order_no
				) chk ON chk.order_no=porder.order_no,"
			.TB_PREF."purch_order_details as line, "
			.TB_PREF."suppliers as supplier, "
			.TB_PREF."locations as location
		WHERE porder.order_no = line.order_no
		AND porder.supplier_id = supplier.supplier_id
		AND location.loc_code = porder.into_stock_location ";

	if ($supplier_id != ALL_TEXT)
		$sql .= "AND supplier.supplier_id=".$supplier_id." ";
	if ($order_number != "")
	{
		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}
		if ($po_number != "")
	{
		$sql .= "AND porder.order_no LIKE ".db_escape('%'. $po_number . '%');
	}
	
	
	
	else
	{

		$data_after = date2sql($from);
		$date_before = date2sql($to);

		$sql .= " AND porder.ord_date >= '$data_after'";
		$sql .= " AND porder.ord_date <= '$date_before'";

		if ($location != ALL_TEXT)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($location);
		}
		if ($stock_id !== '')
		{
			$sql .= " AND line.item_code=".db_escape($stock_id);
		}
		if ($supplier_id != ALL_TEXT)
			$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);

	}

	if (!$also_closed)
		$sql .= " AND isopen";
	$sql .= " GROUP BY porder.order_no";
	return $sql;
}

function get_sql_for_po_search($from, $to, $supplier_id=ALL_TEXT, $location=ALL_TEXT, $order_number='', $stock_id='')
{
	$sql = "SELECT 
		porder.order_no, 
		porder.reference,
		supplier.supp_name, 
		location.location_name,
		porder.requisition_no, 
		porder.ord_date,
		supplier.curr_code,
		porder.total AS OrderValue,
		Sum(line.delivery_date < '". date2sql(Today()) ."'
		AND (line.quantity_ordered > line.quantity_received)) As OverDue,
		porder.approval
		FROM ".TB_PREF."purch_orders as porder,"
			.TB_PREF."purch_order_details as line, "
			.TB_PREF."suppliers as supplier, "
			.TB_PREF."locations as location
		WHERE porder.order_no = line.order_no
		AND porder.supplier_id = supplier.supplier_id
		AND location.loc_code = porder.into_stock_location
		AND (line.quantity_ordered > line.quantity_received)
		AND porder.transaction_type = 0";

	if ($order_number != "")
	{
		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}
	else
	{
		$data_after = date2sql($from);
		$data_before = date2sql($to);

		$sql .= "  AND porder.ord_date >= '$data_after'";
		$sql .= "  AND porder.ord_date <= '$data_before'";

		if ($location != ALL_TEXT)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($location);
		}

		if ($stock_id != '')
		{
			$sql .= " AND line.item_code=".db_escape($stock_id);
		}
		if ($supplier_id != ALL_TEXT)
			$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);
	} //end not order number selected

	$sql .= " GROUP BY porder.order_no";
	return $sql;
}
function update_check_purch($reconcile_id, $reconcile_value)
{
    $sql = "UPDATE ".TB_PREF."purch_orders SET approval = '$reconcile_value'
			WHERE order_no = ".db_escape($reconcile_id);
    db_query($sql, "Can't approve task");
}
//----------------------------------------------------------------------------------------
function get_pr_history($selected_id)
{
    $sql = "SELECT prorder.* ,
				line.supplier_id,
				supplier.supp_name,
				(line.price*line.order_quantity) AS OrderValue
 			FROM ".TB_PREF."requisitions as prorder, "
        .TB_PREF."requisition_details as line, "
        .TB_PREF."suppliers as supplier
			WHERE line.requisition_id = prorder.requisition_id
			AND prorder.requisition_id = ".db_escape($selected_id)."
			AND line.supplier_id = supplier.supplier_id";

    return db_query($sql,"could not get history");

}
//========================================================================================
//RAMSHA
function get_last_purchase_order($supplier_id)
{
    $sql = "SELECT order_no 
			FROM 0_purch_orders
			WHERE supplier_id	 = '$supplier_id' 
			GROUP BY order_no 
			ORDER BY order_no DESC LIMIT 7";

    return db_query($sql,"could not get sales orders");
}
function get_last_purchase_items($order1, $order2, $order3, $order4, $order5, $order6, $order7)
{
    $sql = "SELECT details.item_code, (details.quantity_ordered) AS average_order_qty, 
			SUM(details.quantity_ordered) as qty 
			FROM 0_purch_order_details details,0_stock_master masters 
			WHERE masters.stock_id = details.item_code 
			AND masters.mb_flag = 'B' 
			AND details.quantity_ordered != '0' 
			AND details.order_no 
			IN('$order1','$order2','$order3','$order4','$order5','$order6','$order7')
			GROUP BY details.item_code";

    $result = db_query($sql, "Could not get account.");
    $num = db_num_rows($result);
    if ($num < 1)
    {
        display_warning("There Are No purchase Items For This Customer.");
    } else
    {
        return $result;
    }
}
function get_last_price_purch($stock_id, $supplier_id) // for validate debtor_no
{

	$sql = "SELECT details.unit_price 
            FROM 0_purch_order_details details, 0_purch_orders trans 
            WHERE trans.order_no = details.order_no  
            AND trans.supplier_id = ".db_escape($supplier_id)."
            AND details.item_code = ".db_escape($stock_id)."
            ORDER BY trans.order_no 
            DESC LIMIT 1"
	;
	$result = db_query($sql, "Cannot retreive a customer branch");
	$fetch = db_fetch_row($result);
	return $fetch[0];
}


function get_sql_for_grn_search_completed($from, $to, $supplier_id=ALL_TEXT, $location=ALL_TEXT,
										 $order_number = '', $stock_id = '')
{
	$sql = "SELECT
		batch.id,
		batch.reference,
		items.item_code,
		supplier.supp_name,
		location.location_name,
		batch.delivery_date,
        supplier.supplier_id,
        batch.purch_order_no
		FROM ".TB_PREF."grn_batch
  as batch,

					".TB_PREF."grn_items as items, "
		.TB_PREF."suppliers as supplier, "
		.TB_PREF."locations as location, ".TB_PREF."purch_orders as po
		WHERE batch.id = items.grn_batch_id
		AND batch.supplier_id = supplier.supplier_id
		AND location.loc_code = batch.loc_code
	
		 AND batch.transaction_type=1
AND batch.purch_order_no = po.order_no
AND items.qty_recd !=0
AND items.qty_recd > items.quantity_inv

		 ";



		if ($supplier_id != ALL_TEXT)
			$sql .= "AND supplier.supplier_id=".$supplier_id." ";
		if ($order_number != "")
		{
			$sql .= "AND batch.reference LIKE ".db_escape('%'. $order_number . '%');
		}
		else
		{

			$data_after = date2sql($from);
			$date_before = date2sql($to);

			$sql .= " AND batch.delivery_date >= '$data_after'";
			$sql .= " AND batch.delivery_date <= '$date_before'";

			if ($location != ALL_TEXT)
			{
				$sql .= " AND batch.loc_code = ".db_escape($location);
			}
			if ($stock_id !== '')
			{
				$sql .= " AND items.item_code=".db_escape($stock_id);
			}
			if ($supplier_id != ALL_TEXT)
				$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);

		}




	$sql .= " GROUP BY batch.id";
	return $sql;
}

function get_sql_for_grn_search($from, $to, $supplier_id=ALL_TEXT, $location=ALL_TEXT,
										 $order_number = '', $stock_id = '')
{
	$sql = "SELECT
		batch.id,
		batch.reference,
		items.item_code,
		supplier.supp_name,
		location.location_name,
		batch.delivery_date,
        supplier.supplier_id,
        batch.purch_order_no,
        batch.approval
		FROM ".TB_PREF."grn_batch as batch,
        ".TB_PREF."grn_items as items, "
		.TB_PREF."suppliers as supplier, "
		.TB_PREF."locations as location
		WHERE batch.id = items.grn_batch_id
		AND batch.supplier_id = supplier.supplier_id
		AND location.loc_code = batch.loc_code
	    AND items.quantity_inv=0
		 AND batch.transaction_type=0

		 ";



		if ($supplier_id != ALL_TEXT)
			$sql .= "AND supplier.supplier_id=".$supplier_id." ";
		if ($order_number != "")
		{
			$sql .= "AND batch.reference LIKE ".db_escape('%'. $order_number . '%');
		}
		else
		{

			$data_after = date2sql($from);
			$date_before = date2sql($to);

			$sql .= " AND batch.delivery_date >= '$data_after'";
			$sql .= " AND batch.delivery_date <= '$date_before'";

			if ($location != ALL_TEXT)
			{
				$sql .= " AND batch.loc_code = ".db_escape($location);
			}
			if ($stock_id !== '')
			{
				$sql .= " AND items.item_code=".db_escape($stock_id);
			}
			if ($supplier_id != ALL_TEXT)
				$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);

		}

	$sql .= " GROUP BY batch.id";
	return $sql;
}

function get_sql_for_po_search_completed_import()
{
   global $order_number, $selected_stock_item , $supplier_id,$lading_no,$po_number,
          $transaction_type,$also_closed,$lc_ref,$pr_number;
    if($transaction_type==1)
    {
        $sql = "SELECT 
      porder.ord_date, 
      porder.order_no,  
      porder.lc_ref,
      porder.lading_no,
      line.description,
      supplier.supp_name, 
      location.location_name,
      porder.arrival_date,
      supplier.curr_code, 
      Sum(line.unit_price*line.quantity_ordered) AS OrderValue,
      porder.into_stock_location,
      chk.isopen,
      porder.approval
      FROM " . TB_PREF . "purch_orders as porder
      LEFT JOIN (
               SELECT order_no, SUM(quantity_ordered-quantity_received + quantity_ordered-qty_invoiced) isopen
               FROM ".TB_PREF."purch_order_details
               GROUP BY order_no
            ) chk ON chk.order_no=porder.order_no,"
            . TB_PREF . "purch_order_details as line, "
            . TB_PREF . "suppliers as supplier, "
            . TB_PREF . "locations as location
      WHERE porder.order_no = line.order_no
      AND porder.supplier_id = supplier.supplier_id
      AND location.loc_code = porder.into_stock_location 
      ";
    }
    elseif($transaction_type==0)
    {
        $sql = "SELECT 
      porder.ord_date,
      porder.order_no, 
      porder.pr,
      porder.reference, 
      supplier.supp_name, 
      location.location_name,
      porder.requisition_no,  
      supplier.curr_code, 
      porder.total AS OrderValue,
      porder.into_stock_location,
      chk.isopen,
      porder.approval
      FROM ".TB_PREF."purch_orders as porder
            LEFT JOIN (
               SELECT order_no, SUM(quantity_ordered-quantity_received + quantity_ordered-qty_invoiced) isopen
               FROM ".TB_PREF."purch_order_details
               GROUP BY order_no
            ) chk ON chk.order_no=porder.order_no,"
            .TB_PREF."purch_order_details as line, "
            .TB_PREF."suppliers as supplier, "
            .TB_PREF."locations as location
      WHERE porder.order_no = line.order_no
      AND porder.supplier_id = supplier.supplier_id
      AND location.loc_code = porder.into_stock_location ";
    }
    elseif($transaction_type==2)
    {
        $sql = "SELECT 
      porder.ord_date,
      porder.order_no, 
      porder.pr,
      supplier.supp_name, 
      location.location_name,
      supplier.curr_code,
      porder.total AS OrderValue,
      chk.isopen,
      porder.approval
      FROM ".TB_PREF."purch_orders as porder
      LEFT JOIN (
               SELECT order_no, SUM(quantity_ordered-quantity_received + quantity_ordered-qty_invoiced) isopen
               FROM ".TB_PREF."purch_order_details
               GROUP BY order_no
            ) chk ON chk.order_no=porder.order_no,"
            .TB_PREF."purch_order_details as line, "
            .TB_PREF."suppliers as supplier, "
            .TB_PREF."locations as location
      WHERE porder.order_no = line.order_no
      AND porder.supplier_id = supplier.supplier_id
      AND location.loc_code = porder.into_stock_location ";
    }
    if($transaction_type!=2)
    {
        $sql .= "AND porder.transaction_type=$transaction_type ";
    }
   if (isset($_GET['supplier_id']))
      $sql .= "AND supplier.supplier_id=".@$_GET['supplier_id']." ";

       if (isset($order_number) && $order_number != "")
       {
//           if($transaction_type==1) {
               $sql .= "AND porder.reference LIKE " . db_escape('%' . $order_number . '%');
//           }
//           elseif($transaction_type==0){
//               $sql .= "AND porder.reference LIKE " . db_escape('%' . $order_number . '%');
//
//           }
       }
   else
   {
      $data_after = date2sql($_POST['OrdersAfterDate']);
      $date_before = date2sql($_POST['OrdersToDate']);

      $sql .= " AND porder.ord_date >= '$data_after'";
      $sql .= " AND porder.ord_date <= '$date_before'";

      if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != ALL_TEXT)
      {
         $sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
      }
      if (isset($selected_stock_item))
      {
         $sql .= " AND line.item_code=".db_escape($selected_stock_item);
      }

      if ($supplier_id != null)
      {
         $sql .= " AND porder.supplier_id=".db_escape($supplier_id);
      }


if (isset($lading_no) && $lading_no!= "")
   {
      $sql .= "AND porder.lading_no LIKE ".db_escape('%'. $lading_no. '%');
   }
if (isset($lc_ref) && $lc_ref != "")
   {
      $sql .= "AND porder.lc_ref LIKE ".db_escape('%'. $lc_ref. '%');
   }


if (isset($po_number) && $po_number!= "")
   {
      $sql .= "AND porder.order_no LIKE ".db_escape('%'. $po_number. '%');
   }
   if (isset($pr_number) && $pr_number!= "")
   {
      $sql .= "AND porder.pr LIKE ".db_escape('%'. $pr_number. '%');
   }
   } //end not order number selected
    if (isset($also_closed))
        $sql .= " AND isopen";
   $sql .= " GROUP BY porder.order_no";
   return $sql;
}