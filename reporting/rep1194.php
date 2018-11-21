<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Janusz Dobrwolski
// date_:	2008-01-14
// Title:	Print Delivery Notes
// draft version!
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

$packing_slip = 0;
//----------------------------------------------------------------------------------------------------

function get_description_getpass($id)
{
    $sql = "SELECT description FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($id)."";

    $result =  db_query($sql, "could not get customer");
    
    $row = db_fetch_row($result);

	return $row[0];
}



function get_con_factor_getpass($id)
{
    $sql = "SELECT con_factor FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($id)."";

    $result =  db_query($sql, "could not get customer");
    
    $row = db_fetch_row($result);

	return $row[0];
}
function get_quotation_no($ref)
{
	$sql = "SELECT reference FROM ".TB_PREF."sales_orders WHERE trans_type=32
	AND order_no=".db_escape($ref)."";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_purch_no($item_code)
{
    $sql = "SELECT reference FROM ".TB_PREF."purch_orders WHERE trans_type=18
	AND order_no=".db_escape($item_code)."";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_phone_1107($debtor_no)
{
	$sql = "SELECT phone FROM `0_crm_persons` WHERE `id` IN (
   SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general'
    AND entity_id IN (
   SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch_row($db);
	return $ft[0];
}
function get_gatepass_data($id)
{
    // display_error($id);
    $sql = "SELECT * FROM ".TB_PREF."multiple_gate_pass WHERE gate_pass_no=".db_escape($id)."";

    return db_query($sql, "could not get customer");

}

print_deliveries();

//----------------------------------------------------------------------------------------------------

function print_deliveries()
{
	global $path_to_root, $packing_slip, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$email = $_POST['PARAM_2'];
	$packing_slip = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];


	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

//	$fno = explode("-", $from);
//	$tno = explode("-", $to);
//	$from = min($fno[0], $tno[0]);
//	$to = max($fno[0], $tno[0]);

	$cols = array(3, 60, 265, 330, 400, 460, 520);

	// $headers in doctext.inc
    $headers = array(_("S#"), _(" DESCRIPTION"), _("PACKING(PCS)"), _("DO NO"), _("CARTONS"), _("QTY IN PACK"));

    $aligns = array('center', 'left', 'center', 'center', 'center',	'center');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		if ($packing_slip == 0)
			$rep = new FrontReport(_('DELIVERY'), "DeliveryNoteBulk", user_pagesize(), 9, $orientation);
		else
			$rep = new FrontReport(_('PACKING SLIP'), "PackingSlipBulk", user_pagesize(), 9, $orientation);
	}
	if ($orientation == 'L')
		recalculate_cols($cols);
	for ($i = $from; $i <= $to; $i++)
	{
	   // $sql = "SELECT * from 0_multiple_gate_pass WHERE id = $i";
	   // $db_query = db_query($sql);
	   // $row =  db_fetch($db_query);
	   // $i = $row['gate_pass_no'];
//		if (!exists_customer_trans(ST_CUSTDELIVERY, $i))
//			continue;
//		$myrow = get_customer_trans($i, ST_CUSTDELIVERY);
//		$branch = get_branch($myrow["branch_code"]);
//		$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER); // ?
//		if ($email == 1)
//		{
//			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
//			if ($packing_slip == 0)
//			{
//				$rep->title = _('DELIVERY NOTE');
//				$rep->filename = "Delivery" . $myrow['reference'] . ".pdf";
//			}
//			else
//			{
//				$rep->title = _('PACKING SLIP');
//				$rep->filename = "Packing_slip" . $myrow['reference'] . ".pdf";
//			}
//		}
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, $headers, $aligns);

        $GatePassHeader = get_gatepass_data($i);
		$rep->SetHeaderType('Header1194');

        $GatePassHeader = db_fetch($GatePassHeader);
        $rep->SetCommonData($GatePassHeader, null, null, '', ST_CUSTDELIVERY, null);
        $rep->NewPage();
        $GatePassDelivery = get_gatepass_data($i);
        $s_no=0;
        $total_carton = 0;
        $packing = 0;
        $total_qty_carton = 0;
        while($myrow = db_fetch($GatePassDelivery))
        {
            $rep->TextCol(3, 4, $myrow['delivery_no']);
        
          $result = get_customer_trans_details(ST_CUSTDELIVERY, $myrow['delivery_no']);
            if($myrow['type'] == 16)
            {
            $sql = "SELECT * FROM ".TB_PREF."stock_moves WHERE trans_no = ".$myrow['delivery_no']." AND type = ".$myrow['type']." AND qty > 0";
            $result = db_query($sql," not send");
          }
        //   elseif($myrow['type'] == 13)
        //   {
            //   $result = get_customer_trans_details(ST_CUSTDELIVERY, $myrow['delivery_no']);
        //   }
            // display_error($myrow['delivery_no']);
            				//   $rep->NewLine(-1);

            while ($myrow2=db_fetch($result))
            {
                
                
                       if($myrow['type'] == 16)
            {
                if ($myrow2['qty'] == 0)
				continue;
				$s_no ++;
				   $rep->NewLine();
				 $item=get_item($myrow2['stock_id']);
      $rep->TextCol(2, 3, $myrow2['carton']);
				   $rep->TextCol(0, 1, $s_no);
				                 $qty_in_carton = $myrow2['qty'] / $item["con_factor"];

				       $rep->TextCol(4, 5, "".$qty_in_carton);
                $total_qty_carton +=  $qty_in_carton;
                $rep->TextCol(1, 3, get_description_getpass($myrow2['stock_id']));
                // $rep->TextCol(1, 3, get_con_factor_getpass($myrow2['stock_id']));
                        $rep->TextCol(5, 6, "".$myrow2['qty']);
                        $total_carton += $myrow2['qty'];
                       if($myrow2['units_id']!='pack'){
                        $cartons=$myrow2["qty"] * $item["con_factor"];
                        $rep->TextCol(2, 3,"".$item['carton']	, -2);


                    }
                

            }
                
                if ($myrow2['quantity'] == 0)
                    continue;
                $s_no++;
                $item = get_item($myrow2['stock_id']);
                $rep->TextCol(0, 1, $s_no);
                $rep->TextCol(1, 3, $myrow2['description']);
                $rep->TextCol(2, 3, $myrow2['carton']);
                $qty_in_carton = $myrow2['quantity'] / $item["con_factor"];
                $rep->TextCol(4, 5, $qty_in_carton);
                $packing += $myrow2['carton'];
                $total_qty_carton +=  $qty_in_carton;
                $rep->TextCol(5, 6, $myrow2['quantity']);
                 $total_carton += $myrow2['quantity'];

                if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
                {
                    

                        if($myrow2['units_id']!='pack'){

                            $cartons=$myrow2["quantity"]*$item["con_factor"];

                            // $rep->TextCol(2, 3,$item['carton']	, -2);

                        }
                }
                $rep->NewLine();
            }
            if ($rep->row < $rep->bottomMargin + (7 * $rep->lineHeight))
                $rep->NewPage();
        }
         $rep->MultiCell(115, 15, "".$total_qty_carton, 0, 'L', 0, 2, 459,668, true);
 // $rep->MultiCell(115, 15, "".$packing, 0, 'L', 0, 2, 334,668, true);
  $rep->MultiCell(115, 15, "".$total_carton, 0, 'L', 0, 2, 515,668, true);
     $rep->MultiCell(115, 15, "".$total_qty_carton, 0, 'L', 0, 2, 459,255, true);
  $rep->MultiCell(525, 20, "", 1, 'L', 0, 2, 40,252, true);
    $rep->MultiCell(520, 20, "", 1, 'L', 0, 2, 40,664, true);
  $rep->MultiCell(525, 20, "Total", 0, 'L', 0, 2, 50,257, true);
$rep->MultiCell(525, 20, "Total", 0, 'L', 0, 2, 50,668, true);
  $rep->MultiCell(115, 15, "".$total_carton, 0, 'L', 0, 2, 515,255, true);
        if($s_no >= 1) {
            $rep->NewLine(36 - $s_no);
        }
        else{
            $rep->NewLine(35);
        }

        $GatePassHeader = db_fetch($GatePassHeader);
        $rep->SetCommonData($GatePassHeader, null, null, '', ST_CUSTDELIVERY, null);
//        $rep->NewPage();
        $GatePassDelivery = get_gatepass_data($i);
           $s_no=0;
           
        while($myrow = db_fetch($GatePassDelivery))
        {
            	  
    //   $rep->NewLine(1.0);
            $rep->TextCol(3, 4, $myrow['delivery_no']);
              
                    // $rep->NewLine();

            $result = get_customer_trans_details(ST_CUSTDELIVERY, $myrow['delivery_no']);
             if($myrow['type'] == 16)
            {
            $sql = "SELECT * FROM ".TB_PREF."stock_moves WHERE trans_no = ".$myrow['delivery_no']." AND type = ".$myrow['type']." AND qty > 0";
            $result = db_query($sql," not send");
          }
        
            while ($myrow2=db_fetch($result))
            {
                 
                    if($myrow['type'] == 16)
            {
                if ($myrow2['qty'] == 0)
				continue;
				$s_no ++;
				   $rep->NewLine();
				 $item=get_item($myrow2['stock_id']);
      $rep->TextCol(2, 3, $myrow2['carton']);
				   $rep->TextCol(0, 1, $s_no);
				 $qty_in_carton = $myrow2['qty'] / $item["con_factor"];

				       $rep->TextCol(4, 5, "".$qty_in_carton);
                $total_qty_carton +=  $qty_in_carton;
                $rep->TextCol(1, 3, get_description_getpass($myrow2['stock_id']));
                                $rep->TextCol(5, 6, "".$myrow2['qty']);

                       if($myrow2['units_id']!='pack'){

                        $cartons=$myrow2["qty"] * $item["con_factor"];


                        $rep->TextCol(2, 3,"".$item['carton']	, -2);


                    }
            }
               
               
               
               
                if ($myrow2['quantity'] == 0)
				continue;
                 $s_no++;
                $item=get_item($myrow2['stock_id']);
                $rep->TextCol(0, 1, $s_no);
                  $rep->TextCol(2, 3, $myrow2['carton']);
              $qty_in_carton = $myrow2['quantity'] / $item["con_factor"];
                $rep->TextCol(4, 5, $qty_in_carton);
                $total_qty_carton +=  $qty_in_carton;
                $rep->TextCol(5, 6, $myrow2['quantity']);

                if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
                {
                    $item=get_item($myrow2['stock_id']);


                    if($myrow2['units_id']!='pack'){

                        $cartons=$myrow2["quantity"] * $item["con_factor"];


                        // $rep->TextCol(2, 3,$item['carton']	, -2);


                    }
                }
                                $rep->TextCol(1, 3, $myrow2['description']);

                $rep->NewLine();
            }

        }

	}
	            


	if ($email == 0)
		$rep->End();
}

?>