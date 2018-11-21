<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Janusz Dobrwolski
// date_:	2008-01-14
// Title:	Print Delivery Notes
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------

function get_gate_pass($dimension_id, $column_name)
{
 
	$sql = "SELECT ".$column_name." FROM ".TB_PREF."gate_pass1 WHERE dimension_id=".db_escape($dimension_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}


function get_delivery_1108($dimension_id)
{
	$sql = "SELECT *
    	FROM ".TB_PREF."debtor_trans
    	WHERE type = 13 
    	AND dimension_id = ".db_escape($dimension_id);

	$result = db_query($sql, "No transactions were returned");
	$data = db_fetch($result);
	return $data;
}

function get_color($dimension_id)
{
	$sql = "SELECT ".TB_PREF."sales_order_details.*,".TB_PREF."debtor_trans.*
    	FROM ".TB_PREF."debtor_trans,".TB_PREF."sales_order_details
    	WHERE  ".TB_PREF."debtor_trans.type = 13 
    	AND ".TB_PREF."debtor_trans.order_ =".TB_PREF."sales_order_details.order_no
    	AND ".TB_PREF."debtor_trans.dimension_id = ".db_escape($dimension_id);

	$result = db_query($sql, "No transactions were returned");
	$data = db_fetch($result);
	return $data;
}

function get_grn_information ($dimension_id)
{
	$sql = "SELECT ".TB_PREF."purch_orders.*,".TB_PREF."grn_items.*,".TB_PREF."grn_items.*,".TB_PREF."grn_items.text2 as chasis,
	".TB_PREF."grn_items.text1 as engine,".TB_PREF."grn_batch.*,".TB_PREF."grn_batch.reference as ref
    	FROM ".TB_PREF."purch_orders,".TB_PREF."grn_batch,".TB_PREF."grn_items
    	WHERE  ".TB_PREF."purch_orders.order_no = ".TB_PREF."grn_batch.purch_order_no
    	AND   ".TB_PREF."grn_items.grn_batch_id = ".TB_PREF."grn_batch.id
    	AND ".TB_PREF."purch_orders.dimension = ".db_escape($dimension_id);

	$result = db_query($sql, "No transactions were returned");
	$data = db_fetch($result);
	return $data;
}
function get_audit_stamps ($transtype, $trans)
{
	$sql = "SELECT  user,stamp  FROM ".TB_PREF."audit_trail"
		." WHERE type=".db_escape($transtype)." AND trans_no="
		.db_escape($trans);

	$query= db_query($sql, "Cannot get all audit info for transaction");
	$fetch=db_fetch($query);
	return $fetch;
}
function get_users_realname1108($row)

{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_combo1_names($stock_id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo1  WHERE combo_code =".db_escape($stock_id)."
	";

	$result = db_query($sql, "could not retreive the location name for $stock_id");


	$row = db_fetch_row($result);
	return $row[0];

}



function get_dimension_name1108($dimension_id)
{
	$sql = "SELECT CONCAT(reference,'-',name) FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_ref_name1108($dimension_id)
{
    $sql = "SELECT supp_reference FROM ".TB_PREF."supp_trans WHERE dimension_id=".db_escape($dimension_id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}


function get_customer_name1108($customer_id)
{
    $sql = " SELECT `debtor_ref` FROM `0_debtors_master` INNER JOIN 0_sales_orders ON 0_sales_orders.debtor_no=0_debtors_master.`debtor_no` WHERE 0_sales_orders.dimension_id=$customer_id AND 0_sales_orders. trans_type=30 ";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
print_deliveries();

//----------------------------------------------------------------------------------------------------

function print_deliveries()
{
	global $path_to_root, $SysPrefs;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$dimension = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$packing_slip = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(4, 60, 225, 300, 325, 385, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'left', 'right', 'right', 'right');

	$params = array('comments' => $comments, 'packing_slip' => $packing_slip);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		if ($packing_slip == 0)
			$rep = new FrontReport(_('                                           '), "DeliveryNoteBulk", user_pagesize(),6, $orientation);
		else
			$rep = new FrontReport(_('PACKING SLIP'), "PackingSlipBulk", user_pagesize(), 9, $orientation);
	}
    if ($orientation == 'L')
    	recalculate_cols($cols);
	for ($i = $from; $i <= $to; $i++)
	{
			if (!exists_customer_trans(ST_CUSTDELIVERY, $i))
				continue;
			$myrow = get_customer_trans($i, ST_CUSTDELIVERY);
			$branch = get_branch($myrow["branch_code"]);

        $sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER); // ?
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(), 5, $orientation);
//				if ($packing_slip == 0)
//				{
//					$rep->title = _('Vehicle Delivery Acceptance Note');
//					$rep->filename = "Delivery" . $myrow['reference'] . ".pdf";
//				}
//				else
//				{
//					$rep->title = _('PACKING SLIP');
//					$rep->filename = "Packing_slip" . $myrow['reference'] . ".pdf";
//				}
			}
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'delivery', $branch['debtor_no'], true);
			$rep->SetCommonData($myrow, $branch, $sales_order, '', ST_CUSTDELIVERY, $contacts);
			$rep->SetHeaderType('Header1108');
			$rep->NewPage();

   			$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
			$SubTotal = 0;



        $rep->SetFontSize(8);
        $rep->MultiCell(220,15,$rep->company['postal_address'],0,'L',0,0,40,55);
        $rep->SetFontSize(10);
//            $this->TextWrapLines($ccol+40, $icol, $this->company['postal_address']);
//			while ($myrow2=db_fetch($result))
//			{
//			     $item=get_item($myrow2['stk_code']);
//			     $pref = get_company_prefs();
//
//			    if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
//		    	$qty=$myrow2['quantity'] * $myrow2['con_factor'];
//		    	else
//		    	$qty=$myrow2['quantity'];
//
//
//
//				if ($qty == 0)
//					continue;
//
//				$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $qty),
//				   user_price_dec());
//				$SubTotal += $Net;
//	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
//	    		$DisplayQty = number_format2($qty,get_qty_dec($myrow2['stock_id']));
//	    		$DisplayNet = number_format2($Net,$dec);
//	    		if ($myrow2["discount_percent"]==0)
//		  			$DisplayDiscount ="";
//	    		else
//		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
//			//	$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
//				$oldrow = $rep->row;
//				//$rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
//				$newrow = $rep->row;
//				$rep->row = $oldrow;
////				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
////				{
////
////
//
////			  $rep->Amountcol(2, 3,	$qty ,$dec);
//
//
//
////                $item=get_item($myrow2['stk_code']);
////                if($pref['alt_uom'] == 1)
////                {
////                    $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
////                }
////                else
////                {
////                    $rep->TextCol(3, 4,	$myrow2['units'], -2);
////                }
////
////					if ($packing_slip == 0)
////					{
////						$rep->TextCol(4, 5,	$DisplayPrice, -2);
////						$rep->TextCol(5, 6,	$DisplayDiscount, -2);
////						$rep->TextCol(6, 7,	$DisplayNet, -2);
////					}
////				}
//				$rep->row = $newrow;
//				//$rep->NewLine(1);
//				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//					$rep->NewPage();
//			}

//			$memo = get_comments_string(ST_CUSTDELIVERY, $i);
//			if ($memo != "")
//			{
//				$rep->NewLine();
//				$rep->TextColLines(1, 5, $memo, -2);
//			}

   			//$DisplaySubTot = number_format2($SubTotal,$dec);


$i=30;
		$row1 = get_delivery_1108($dimension);

$date = date('Y-m-d',strtotime($row1['tran_date'].'+30 days' ));

		$stamp = get_audit_stamps (ST_CUSTDELIVERY, $myrow['trans_no']);
		$color = get_color($dimension);
        $gate_pass1 = get_gate_pass($dimension, 'receiver_enable');
        $gate_pass2 = get_gate_pass($dimension, 'address_enable');
        $gate_pass3 = get_gate_pass($dimension, 'tel_enable');
        $gate_pass4 = get_gate_pass($dimension, 'reg_enable');
		$grn_information =get_grn_information($dimension);
		$rep->Font('');
	//	$rep->MultiCell(200, 15, "Date:     ".sql2date($row1['tran_date']),0, //'L', 0, 2, 30,110, true);
		$rep->MultiCell(250, 15, "Date/Time:     " .$stamp['stamp'],0, 'L', 0, 2, 30,120, true);

		$rep->MultiCell(300, 15, "Owner:   ".get_customer_name1108($dimension) ,0, 'L', 0, 2, 30,150, true);
		$rep->MultiCell(200, 15, "Make:      Honda" ,0, 'L', 0, 2, 30,170, true);
		$rep->MultiCell(200, 15, "Color:     " .get_combo1_names($sales_order['h_combo1']),0, 'L', 0, 2, 30,190, true);
		$rep->MultiCell(500, 20, "Receiver's Name:           ".$gate_pass1 ,0, 'L', 0, 2, 30,210, true);
		$rep->MultiCell(300, 15, "Address :   ".$sales_order['delivery_address'] ,0, 'L', 0, 2, 30,250, true);
		$rep->MultiCell(200, 15, "Receiver's Tel.#:       ".$gate_pass3 ,0, 'L', 0, 2, 30,230, true);
		$rep->MultiCell(200, 15, "User Name :   " .get_users_realname1108($stamp['user']),0, 'L', 0, 2, 30,273, true);
		$rep->MultiCell(600, 15, "Effective today, the dealership & Honda Atlas Cars Pakistan Ltd will not be responsible for any
loss or damage on any account." ,0, 'L', 0, 2, 76,300, true);

		$rep->MultiCell(200, 15, "Order # :" .get_dimension_name1108($sales_order['dimension_id']),0, 'L', 0, 2, 350,110, true);
		$rep->MultiCell(150, 15, "Inv. #   :".get_ref_name1108($row1['dimension_id']),0, 'L', 0, 2, 350,130, true);
		$rep->MultiCell(300, 15, "Model  :" .$sales_order['h_text1'] ,0, 'L', 0, 2, 350,150, true);
		$rep->MultiCell(200, 15, "Chasis :".$grn_information['chasis'] ,0, 'L', 0, 2, 350,170, true);
		$rep->MultiCell(200, 15, "Eng No :".$grn_information['engine'] ,0, 'L', 0, 2, 350,190, true);
		$rep->MultiCell(500, 20, "Reg no :".$gate_pass4 ,0, 'L', 0, 2, 350,210, true);
		$rep->MultiCell(300, 15, "NIC :".$sales_order['h_text2'] ,0, 'L', 0, 2, 350,250, true);
		$rep->MultiCell(200, 15, "Investor: yes/no" ,0, 'L', 0, 2, 350,273, true);

///.....................................................boxes........................................................///
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 30,378, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 30,405, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 30,430, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 30,460, true);


    $rep->MultiCell(25, 15,"",1, 'L', 0, 0, 230,378, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 230,405, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 230,430, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 230,460, true);

    $rep->MultiCell(25, 15,"",1, 'L', 0, 0, 410,378, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 410,405, true);
	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 410,460, true);

	$rep->MultiCell(25, 15,"",1, 'L', 0, 0, 390,495, true);


	$rep->MultiCell(400, 15,"",1, 'L', 0, 0, 150,530, true);
	
	
	$chk_mark = company_path() . "/images/" .'check_mark.png';
		$unchk_mark = company_path() . "/images/" .'wrong.png';

		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
//			Spare Wheel

// display_error($dimension);
			if(get_gate_pass($dimension, 'spare_enable') == 1) {
				$rep->AddImage($chk_mark, 40, 452, 0, 8);
			}
			else {
				$rep->AddImage($unchk_mark, 40, 452, 0, 8);
			}
		$rep->NewLine();
		//}
	}
if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			//Tool Kit with Jack
			if(get_gate_pass($dimension, 'tool_enable') == 1 ) {
				$rep->AddImage($chk_mark, 241, 452, 0, 8);}
			if(get_gate_pass($dimension, 'tool_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  241, 452, 0, 8);
			    
			}
		$rep->NewLine();
		//}
	}

		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			//Warranty Book
			if(get_gate_pass($dimension, 'warranty_enable') == 1 ) {
				$rep->AddImage($chk_mark, 420, 452, 0, 8);}
			if(get_gate_pass($dimension, 'warranty_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  420, 452, 0, 8);}
			$rep->NewLine();
			//}
		}
	if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///Warranty Card of Battery
			if(get_gate_pass($dimension, 'card_enable') == 1 ) {
				$rep->AddImage($chk_mark, 420, 426, 0, 8);}
			if(get_gate_pass($dimension, 'card_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  420, 426, 0, 8);}
			$rep->NewLine();
			//}
		}
		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///Owner's Manual
			if(get_gate_pass($dimension, 'owners_enable') == 1 ) {
				$rep->AddImage($chk_mark, 40, 425, 0, 8);}
			if(get_gate_pass($dimension, 'owners_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  40, 425, 0, 8);}
			$rep->NewLine();
			//}
		}

		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///Remote of Player
			if(get_gate_pass($dimension, 'remote_enable') == 1 ) {
				$rep->AddImage($chk_mark, 240, 425, 0, 8);}
			if(get_gate_pass($dimension, 'remote_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  240, 425, 0, 8);}
			$rep->NewLine();
			//}
		}
		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///Cigarette Lighter/Ash Tray
			if(get_gate_pass($dimension, 'cigrette_enable') == 1 ) {
				$rep->AddImage($chk_mark, 40, 400, 0, 8);}
			if(get_gate_pass($dimension, 'cigrette_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  40, 400, 0, 8);}
			$rep->NewLine();
			//}
		}
		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///2 Number Plates (Original / Temporary)
			if(get_gate_pass($dimension, 'number_enable') == 1 ) {
				$rep->AddImage($chk_mark, 240, 400, 0, 8);}
			if(get_gate_pass($dimension, 'number_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  240, 400, 0, 8);}
			$rep->NewLine();
			//}
		}
		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///Keys (Qty)
			if(get_gate_pass($dimension, 'keysqty_enable') == 1 ) {
				$rep->AddImage($chk_mark, 240, 372, 0, 8);}
			if(get_gate_pass($dimension, 'keysqty_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  240, 372, 0, 8);}
			$rep->NewLine();
			//}
		}
		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///Floor Mats
			if(get_gate_pass($dimension, 'floor_enable') == 1 ) {
				$rep->AddImage($chk_mark, 40, 372, 0, 8);}
			if(get_gate_pass($dimension, 'floor_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  40, 372, 0, 8);}
			$rep->NewLine();
			//}
		}

		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
			$rep->NewPage();
		{
			///utility package
			if(get_gate_pass($dimension, 'utility_enable') == 1 ) {
				$rep->AddImage($chk_mark, 420, 372, 0, 8);}
			if(get_gate_pass($dimension, 'utility_enable') == 0 ) {
				$rep->AddImage($unchk_mark,  420, 372, 0, 8);}
			$rep->NewLine();
			//}
		}



///.....................................................end........................................................///
		$rep->MultiCell(200, 15,"Spare Wheel",0, 'L', 0, 0, 65,378, true);
		$rep->MultiCell(200, 15,"Owner's Manual",0, 'L', 0, 0, 65,405, true);
		$rep->MultiCell(200, 15,"Cigarette Lighter/Ash Tray",0, 'L', 0, 0, 65,430, true);
		$rep->MultiCell(200, 15,"Floor Mats",0, 'L', 0, 0, 65,455, true);

        $rep->MultiCell(200, 15,"Tool Kit with Jack",0, 'L', 0, 0, 260,378, true);
		$rep->MultiCell(200, 15,"Remote of Player",0, 'L', 0, 0, 260,405, true);
		$rep->MultiCell(500, 15,"2 Number Plates (Original / Temporary)",0, 'L', 0, 0, 260,430, true);
		$rep->MultiCell(200, 15,"Keys (Qty)",0, 'L', 0, 0, 260,455, true);

//
        $rep->MultiCell(200, 15,"Warranty Book",0, 'L', 0, 0, 440,378, true);
		$rep->MultiCell(200, 15,"Warranty Card of Battery",0, 'L', 0, 0, 440,405, true);

		$rep->MultiCell(200, 15,"Utility Package",0, 'L', 0, 0, 440,460, true);

//



		$rep->Font('b');
		$rep->MultiCell(500, 15, "NOTE:" ,0, 'L', 0, 2, 30,300, true);

		$rep->MultiCell(600, 15, "Delivered above said vehicles to the Authorized Receiver's along with the following items" ,0, 'L', 0, 2, 30,345, true);
		$rep->MultiCell(600, 15, "Are you satisfied with the delivered vehicle (Please Tick)" ,0, 'L', 0, 2, 30,495, true);
		$rep->MultiCell(600, 15, "Comment (if any)" ,0, 'L', 0, 2, 30,530, true);
		$rep->MultiCell(700, 15, "Appointment for First Free Inspection of your Vehicle has been blocked on  ".sql2date($date ),0, 'L', 0, 2, 30,550, true);
		$rep->Font('');

		$rep->MultiCell(600, 15, "For Appointment Confirmation Please Call :".$rep->company['phone'] ,0, 'L', 0, 2, 30,570, true);
		$rep->MultiCell(600, 15, "I hereby, accept that I have taken the delivery of the said Vehicle with all relevant information
documents & accessories" ,0, 'L', 0, 2, 30,620, true);


$rep->MultiCell(600, 15, "__________________________" ,0, 'L', 0, 2, 30,720, true);
$rep->MultiCell(600, 15, "__________________________" ,0, 'L', 0, 2, 360,720, true);
$rep->MultiCell(600, 15, "RECEIVER'S SIGNATTURE" ,0, 'L', 0, 2, 375,740, true);
$rep->MultiCell(600, 15, get_salesman_name($row1['salesman']) ,0, 'L', 0, 2, 50,740, true);


    		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY;
			if ($packing_slip == 0)
			{
			//	$rep->TextCol(3, 6, _("Sub-total"), -2);
				//$rep->TextCol(6, 7,	$DisplaySubTot, -2);
				$rep->NewLine();
				if ($myrow['ov_freight'] != 0.0)
				{
					$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
				//	$rep->TextCol(3, 6, _("Shipping"), -2);
					$rep->TextCol(6, 7,	$DisplayFreight, -2);
					$rep->NewLine();
				}	
				$tax_items = get_trans_tax_details(ST_CUSTDELIVERY, $i);
				$first = true;
    			while ($tax_item = db_fetch($tax_items))
    			{
    				if ($tax_item['amount'] == 0)
    					continue;
    				$DisplayTax = number_format2($tax_item['amount'], $dec);
 
 					if ($SysPrefs->suppress_tax_rates() == 1)
 		   				$tax_type_name = $tax_item['tax_type_name'];
 		   			else
 		   				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

 					if ($myrow['tax_included'])
    				{
   						if ($SysPrefs->alternative_tax_include_on_docs() == 1)
    					{
    						if ($first)
    						{
//								$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//								$rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
//								$rep->NewLine();
    						}
//							$rep->TextCol(3, 6, $tax_type_name, -2);
//							$rep->TextCol(6, 7,	$DisplayTax, -2);
//							$first = false;
    					}
    					else
							$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
					}
    				else
    				{
//						$rep->TextCol(3, 6, $tax_type_name, -2);
//						$rep->TextCol(6, 7,	$DisplayTax, -2);
					}
					$rep->NewLine();
    			}
    			$rep->NewLine();
				$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
					$myrow["ov_amount"],$dec);
				$rep->Font('bold');
//				$rep->TextCol(3, 6, _("TOTAL DELIVERY INCL. VAT"). ' ' . $rep->formData['curr_code'], - 2);
//				$rep->TextCol(6, 7,	$DisplayTotal, -2);
				$words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
				if ($words != "")
				{
					$rep->NewLine(1);
					$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
				}	
				$rep->Font();
			}	
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

