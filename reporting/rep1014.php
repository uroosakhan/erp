<?php
$page_security = 'SA_CUSTPAYMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances Summary
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_aged_customer_analysis();

function get_customer_details_new111($customer_id, $to=null, $all=true,$branch_id)
{
	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - debtor_trans.alloc from all summations

//	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
	if ($all)
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)"; 
  /*  else		
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
    		trans.alloc),0)"; */
    		
    else		//dz 24.7.18
    	$value = "IFNULL(
    	IF (type=".ST_JOURNAL." && ov_amount < 0,
    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 +
    		trans.alloc),
    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
    		trans.alloc)    		
    		)
    		,0)";     		

	$due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
			FROM ".TB_PREF."debtors_master debtor
				 LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
				 .TB_PREF."payment_terms terms,"
				 .TB_PREF."credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
	 			 if ($customer_id)
		$sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

	if (!$all)
		$sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA;
	
	if($dim != 0)
	    $sql .= " AND trans.dimension_id = ".db_escape($dim);
	$sql .= " GROUP BY
		  	debtor.name,
		  	terms.terms,
		  	terms.days_before_due,
		  	terms.day_in_following_month,
		  	debtor.credit_limit,
		  	credit_status.dissallow_invoices,
		  	credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

}
function get_cust_detail($cust_id)
{
    $sql = "SELECT * FROM `0_crm_persons`
INNER JOIN 0_crm_contacts ON 0_crm_contacts.person_id=0_crm_persons.id
INNER JOIN 0_cust_branch ON 0_cust_branch.branch_code=0_crm_contacts.entity_id
WHERE 0_cust_branch.branch_code=$cust_id
	";
//    $sql .= " ORDER BY pod.item_code ";
    $result = db_query($sql, "The customers could not be retrieved");
    return db_fetch($result);
}
//----------------------------------------------------------------------------------------------------

function print_aged_customer_analysis()
{
    global $path_to_root, $systypes_array;

    	$to = $_POST['PARAM_0'];
    	$group_by = $_POST['PARAM_1'];
    	$fromcust = $_POST['PARAM_2'];
    	$dimension = $_POST['PARAM_3'];
        $area = $_POST['PARAM_4'];
    	$folk = $_POST['PARAM_5'];					
    	$currency = $_POST['PARAM_6'];
    	//$show_all = $_POST['PARAM_5'];
    	$no_zeros = $_POST['PARAM_7'];
    	$graphics = $_POST['PARAM_8'];
    	$comments = $_POST['PARAM_9'];
	$orientation = $_POST['PARAM_10'];
	$destination = $_POST['PARAM_11'];
	


	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");				
	else		
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	if ($graphics)
	{
		include_once($path_to_root . "/reporting/includes/class.graphic.inc");
		$pg = new graph();
	}

	if ($fromcust == ALL_TEXT)
		$from = _('All');
	else
		$from = get_customer_name($fromcust);
    	$dec = user_price_dec();
        $summaryOnly=1;
	if ($summaryOnly == 1)
		$summary = _('Summary Only');
	else
		$summary = _('Detailed Report');
	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('Balances in Home Currency');
	}
	else
		$convert = false;

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

        $show_all=1;

	if ($show_all) $show = _('Yes');
	else $show = _('No');

	if ($fromcust == ALL_TEXT)
		$from = _('All');
	else
		$from = get_customer_name($fromcust);
    	$dec = user_price_dec();
		
			if ($area == ALL_NUMERIC)
		$area = 0;
		
	if ($area == 0)
		$sarea = _('All Areas');
	else
		$sarea = get_area_name($area);
		
	if ($folk == ALL_NUMERIC)
		$folk = 0;

	if ($folk == 0)
		$salesfolk = _('All Sales Man');
	else
		$salesfolk = get_salesman_name($folk);
		
	$PastDueDays1 = get_company_pref('past_due_days');
	$PastDueDays2 = 2 * $PastDueDays1;
	$nowdue = "1-" . $PastDueDays1 . " " . _('Days');
	$pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $PastDueDays2 . " " . _('Days');
if($orientation=='P')
{
	$cols = array(2, 200, 300, 350, 450, 520);
	$headers = array(_('Customer Name'), _(''), _(''), _(''), _('Balance'));
	$aligns = array('left',	'left', 'left', 'left', 'right');
}
else
{
		$cols = array(2, 150, 250,260,350,410,450,520);
	$headers = array(_('Customer Name'), _('Address'), _(''), _('Contact Person'), _('Phone'), _('Phone2'), _('Balance'));
	$aligns = array('left',	'left', 'left', 'left','left','left',  'right');
}
    	$params =   array( 	0 => $comments,
    				1 => array('text' => _('End Date'), 'from' => $to, 'to' => ''),
    				2 => array('text' => _('Customer'),	'from' => $from, 'to' => ''),
    				3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
                    4 => array('text' => _('Show Also Allocated'), 'from' => $show, 'to' => ''),		
				    5 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''),
    				6 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),						
    				7 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),				
				);

	if ($convert)
		$headers[2] = _('');

    $rep = new FrontReport(_('Customer Balances Summary'), "CustomerBalancesSummary", user_pagesize(), 9, $orientation);

if ($orientation == 'L')
    	recalculate_cols($cols);
 
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$total = array(0,0,0,0, 0);
	
if ($group_by==1)
{
    		$sql = "SELECT `area_code`,`description` FROM `".TB_PREF."areas` GROUP BY ".TB_PREF."areas.area_code";
	        $res = db_query($sql, "The customers could not be retrieved");
}
elseif($group_by==2)
{
   	$sql = "SELECT `id`,`description` FROM `0_groups` GROUP BY ".TB_PREF."groups.id ";
	$res = db_query($sql, "The customers could not be retrieved"); 
}
	
	while ($myrow1=db_fetch($res))
	{
	     $rep->NewLine();
	     $rep->Font('bold');
	    $rep->fontSize += 2;
	    $rep->TextCol(0, 2, $myrow1['description']);
	    $rep->fontSize -= 2;
	    $rep->Font('');
	    $rep->NewLine(2);
	    $rep->Line($rep->row - 2);
	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no,
			".TB_PREF."debtors_master.name,".TB_PREF."cust_branch.branch_code,".TB_PREF."cust_branch.br_address 
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code WHERE ".TB_PREF."debtors_master.debtor_no!=0";
 if ($group_by==1)
 $sql .= " AND ".TB_PREF."areas.area_code=".db_escape($myrow1['area_code']);
 elseif($group_by==2)
 $sql .= " AND ".TB_PREF."cust_branch.group_no=".db_escape($myrow1['id']);
//{
    	
// }
// else
// {
		if ($fromcust != ALL_TEXT )
			{
				if ($area != 0 || $folk != 0);
				$sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
			}
	
	    elseif ($dimension != 0 )
			
			{
		        $sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
			}
			
		elseif ($area != 0)
			{
				if ($folk != 0)
					$sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
						AND ".TB_PREF."areas.area_code=".db_escape($area);
				else
					$sql .= " AND ".TB_PREF."areas.area_code=".db_escape($area);
			}			
		elseif ($folk != 0 )
			{
				$sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk);
			}			
//}

    $sql .= " GROUP BY ".TB_PREF."debtors_master.debtor_no ORDER BY Name"; 
	$result = db_query($sql, "The customers could not be retrieved");
	


	while ($myrow=db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code'])
			continue;

		if ($convert) $rate = get_exchange_rate_from_home_currency($myrow['curr_code'], $to);
		else $rate = 1.0;
		$custrec = get_customer_details_new111($myrow['debtor_no'], $to, $show_all,$myrow['branch_code']); 
		if (!$custrec)
			continue;
		$custrec['Balance'] *= $rate;
		$custrec['Due'] *= $rate;
		$custrec['Overdue1'] *= $rate;
		$custrec['Overdue2'] *= $rate;
		$str = array(
			$custrec["Balance"]);
		if ($no_zeros && floatcmp(array_sum($str), 0) == 0) continue;

		$rep->fontSize += 2;
$name =  $myrow['name'];
if($destination)
$rep->TextCol(0, 2, $name);
else
				if (strlen($name) > 15)
                $name = substr($name, 0, 31).'...';
				
				if($orientation=='L' )
				$rep->TextCol(0, 2, $name);
				else
		$rep->TextCol(0, 2, $myrow['name']);
		if($orientation=='L')
		{
		$rep->TextCol(1, 2, $myrow['br_address']);
		$row=get_cust_detail($myrow['branch_code']);
			
				$rep->TextCol(3, 4, $row['name']);
					$rep->TextCol(4, 5, $row['phone']);
						$rep->TextCol(5, 6, $row['phone2']);
		}
		if ($convert) //$rep->TextCol(2, 3,	$myrow['curr_code']);
	
	
		$total[4] += $custrec["Balance"];
			if($orientation=='L')
		{
		for ($i = 0; $i < count($str); $i++)
			$rep->AmountCol(6, 7, $str[$i], $dec);
		}
		else
		{
		    for ($i = 0; $i < count($str); $i++)
			$rep->AmountCol(4, 5, $str[$i], $dec);
		}
			$rep->fontSize -= 2;
		$rep->Line($rep->row - 2);
		
		$rep->NewLine(1.2);
		

	}
	
	}//first
	//////////////////////////////
	if($group_by==0)
	{
		$sql = "SELECT ".TB_PREF."debtors_master.debtor_no,
			".TB_PREF."debtors_master.name,".TB_PREF."cust_branch.branch_code ,".TB_PREF."cust_branch.br_address
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";
		if ($fromcust != ALL_TEXT )
			{
				if ($area != 0 || $folk != 0);
				$sql .= " WHERE ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
			}
	
	    elseif ($dimension != 0 )
			
			{
		        $sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
			}
			
		elseif ($area != 0)
			{
				if ($folk != 0)
					$sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
						AND ".TB_PREF."areas.area_code=".db_escape($area);
				else
					$sql .= " WHERE ".TB_PREF."areas.area_code=".db_escape($area);
			}			
		elseif ($folk != 0 )
			{
				$sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk);
			}			
//}

    $sql .= " GROUP BY ".TB_PREF."debtors_master.debtor_no ORDER BY Name"; 
	$result = db_query($sql, "The customers could not be retrieved");
	


	while ($myrow=db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code'])
			continue;

		if ($convert) $rate = get_exchange_rate_from_home_currency($myrow['curr_code'], $to);
		else $rate = 1.0;
		$custrec = get_customer_details_new111($myrow['debtor_no'], $to, $show_all,$myrow['branch_code']); 
		if (!$custrec)
			continue;
		$custrec['Balance'] *= $rate;
		$custrec['Due'] *= $rate;
		$custrec['Overdue1'] *= $rate;
		$custrec['Overdue2'] *= $rate;
		$str = array(
			$custrec["Balance"]);
		if ($no_zeros && floatcmp(array_sum($str), 0) == 0) continue;

		$rep->fontSize += 2;
		
		      $name =  $myrow['name'];
		      if($destination)
$rep->TextCol(0, 2, $name);
else
				if (strlen($name) > 15)
                $name = substr($name, 0, 31).'...';
				
				if($orientation=='L')
				$rep->TextCol(0, 2, $name);
				else
		$rep->TextCol(0, 2, $myrow['name']);
		if($orientation=='L')
		{
		$rep->TextCol(1, 2, $myrow['br_address']);
		$row=get_cust_detail($myrow['branch_code']);
			
				$rep->TextCol(3, 4, $row['name']);
					$rep->TextCol(4, 5, $row['phone']);
					$rep->TextCol(5, 6, $row['phone2']);
		}
		if ($convert) //$rep->TextCol(2, 3,	$myrow['curr_code']);
	
	
		$total[4] += $custrec["Balance"];
			if($orientation=='L')
		{
		for ($i = 0; $i < count($str); $i++)
			$rep->AmountCol(6, 7, $str[$i], $dec);
		}
		else
		{
		    for ($i = 0; $i < count($str); $i++)
			$rep->AmountCol(4, 5, $str[$i], $dec);
		}
			$rep->fontSize -= 2;
		$rep->Line($rep->row - 2);
		
		$rep->NewLine(1.2);
		

	}
	}
	////////
	
		$rep->NewLine(1);

	$rep->fontSize += 2;
    $rep->Font('bold');
    if($orientation=='L')
		{
	$rep->TextCol(0, 3, _('Grand Total'));
	for ($i = 4; $i < count($total); $i++)
	{
		$rep->AmountCol(6, 7, $total[$i], $dec);
		if ($graphics && $i < count($total) - 1)
		{
			$pg->y[$i] = abs($total[$i]);
		}
	}
		}
		else
		{
		    	$rep->TextCol(0, 3, _('Grand Total'));
	for ($i = 4; $i < count($total); $i++)
	{
		$rep->AmountCol(4, 5, $total[$i], $dec);
		if ($graphics && $i < count($total) - 1)
		{
			$pg->y[$i] = abs($total[$i]);
		}
	}
		}
	$rep->Font('');
	$rep->fontSize -= 2;
	
   	$rep->Line($rep->row - 8);
   	if ($graphics)
   	{
   		global $decseps, $graph_skin;
		$pg->x = array(_('Current'), $nowdue, $pastdue1, $pastdue2);
		$pg->title     = $rep->title;
		$pg->axis_x    = _("Days");
		$pg->axis_y    = _("Amount");
		$pg->graphic_1 = $to;
		$pg->type      = $graphics;
		$pg->skin      = $graph_skin;
		$pg->built_in  = false;
		$pg->latin_notation = ($decseps[$_SESSION["wa_current_user"]->prefs->dec_sep()] != ".");
		$filename = company_path(). "/pdf_files/". uniqid("").".png";
		$pg->display($filename, true);
		$w = $pg->width / 1.5;
		$h = $pg->height / 1.5;
		$x = ($rep->pageWidth - $w) / 2;
		$rep->NewLine(2);
		if ($rep->row - $h < $rep->bottomMargin)
			$rep->NewPage();
		$rep->AddImage($filename, $x, $rep->row - $h, $w, $h);
	}


	$rep->NewLine();
    $rep->End();
}

?>