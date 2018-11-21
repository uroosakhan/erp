<?php

$page_security = 'SA_SUMMMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Sales Summary Report
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//------------------------------------------------------------------


print_sales_summary_report();

function getTaxTransactions($from, $to, $tax_id,$fromcust, $area, $folk, $groups)
{
	$fromdate = date2sql($from);
	$todate = date2sql($to);

	$sql = "SELECT d.debtor_no, d.name AS cust_name, d.tax_id, dt.type, dt.trans_no,  
			CASE WHEN dt.type=".ST_CUSTCREDIT." THEN (ov_amount+ov_freight+ov_discount-discount1-discount2)*-1 
			ELSE (ov_amount+ov_freight+ov_discount-discount1-discount2) END *dt.rate AS total
		FROM ".TB_PREF."debtor_trans dt
			INNER JOIN ".TB_PREF."debtors_master d 
			    ON d.debtor_no=dt.debtor_no
			INNER JOIN ".TB_PREF."cust_branch
			    ON d.debtor_no=".TB_PREF."cust_branch.debtor_no
		    INNER JOIN ".TB_PREF."areas
			    ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		    INNER JOIN ".TB_PREF."salesman
			    ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code
		WHERE (dt.type=".ST_SALESINVOICE." OR dt.type=".ST_CUSTCREDIT.") ";

	if ($tax_id)
		$sql .= "AND tax_id<>'' ";
    if ($fromcust != ALL_TEXT )
    {
        $sql .= " AND d.debtor_no=".db_escape($fromcust);
    }
    if ($area != 0)
    {
        $sql .= " AND ".TB_PREF."cust_branch.area=".db_escape($area);
    }
    if ($folk != -1)
    {
        $sql .= " AND ".TB_PREF."cust_branch.salesman= ".db_escape($folk);
    }
    if ($groups != 0)
    {
        $sql .= " AND ".TB_PREF."cust_branch.group_no= ".db_escape($groups);
    }
	$sql .= "AND dt.tran_date >=".db_escape($fromdate)."
	 AND dt.tran_date<=".db_escape($todate)."
	GROUP BY dt.trans_no ORDER BY d.debtor_no";
    return db_query($sql,"No transactions were returned");
}

function getTaxes($type, $trans_no)
{
	$sql = "SELECT included_in_price, SUM(CASE WHEN trans_type=".ST_CUSTCREDIT." THEN -amount ELSE amount END * ex_rate) AS tax
		FROM ".TB_PREF."trans_tax_details WHERE trans_type=$type AND trans_no=$trans_no GROUP BY included_in_price";

    $result = db_query($sql,"No transactions were returned");
    if ($result !== false)
    	return db_fetch($result);
    else
    	return null;
}    	

//----------------------------------------------------------------------------------------------------

function print_sales_summary_report()
{
	global $path_to_root;
	
	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$tax_id = $_POST['PARAM_2'];
    $fromcust = $_POST['PARAM_3'];
    $area = $_POST['PARAM_4'];
    $folk = $_POST['PARAM_5'];
    $groups = $_POST['PARAM_6'];
	$comments = $_POST['PARAM_7'];
	$orientation = $_POST['PARAM_8'];
	$destination = $_POST['PARAM_9'];
	if ($tax_id == 0)
		$tid = _('No');
	else
		$tid = _('Yes');

    if ($fromcust == ALL_TEXT)
        $cust = _('All');
    else
        $cust = get_customer_name($fromcust);
    $dec = user_price_dec();

    if ($area == -1)
        $area_name = _('All');
    else
        $area_name = get_area_name($area);
    if ($folk == -1)
        $folk_name = _('All');
    else
        $folk_name = get_salesman_name($folk);

    if ($groups == 0)
        $groups_name = _('All');
    else
        $groups_name = get_sales_group_name($groups);

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$orientation = ($orientation ? 'L' : 'P');

	$dec = user_price_dec();

	$rep = new FrontReport(_('Sales Summary Report'), "SalesSummaryReport", user_pagesize(), 9, $orientation);

	$params =   array( 	0 => $comments,
						1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
						2 => array('text' => _('Tax Id Only'),'from' => $tid,'to' => ''),
                        3 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
    				    4 => array('text' => _('Area'), 'from' => $area_name, 'to' => ''),
    				    5 => array('text' => _('Salesman'), 'from' => $folk_name, 'to' => ''),
    				    6 => array('text' => _('Groups'), 'from' => $groups_name, 'to' => ''));

	$cols = array(0, 130, 180, 270, 350, 420, 500);

	$headers = array(_('Customer'), _('Tax Id'), _('Total ex. Tax'), _('Tax'), _('Total'));
	$aligns = array('left', 'left', 'right', 'right', 'right');
    if ($orientation == 'L')
    	recalculate_cols($cols);

	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns);
	$rep->NewPage();
	
	$totalnet = 0.0;
	$totaltax = 0.0;
	$transactions = getTaxTransactions($from, $to, $tax_id,$fromcust, $area, $folk, $groups);

	$rep->TextCol(0, 4, _('Balances in Home Currency'));
	$rep->NewLine(2);
	
	$custno = 0;
	$tax = $total = 0;
	$custname = $tax_id = "";

	while ($trans=db_fetch($transactions))
	{

		if ($custno != $trans['debtor_no'])
		{
			if ($custno != 0)
			{
				$rep->TextCol(0, 1, $custname);
				$rep->TextCol(1, 2,	$tax_id);
				$rep->AmountCol(2, 3, $total, $dec);
				$rep->AmountCol(3, 4, $tax, $dec);
                $rep->AmountCol(4, 5, $total + $tax, $dec);
				$totalnet += $total;
				$totaltax += $tax;
				$total = $tax = 0;
				$rep->NewLine();

				if ($rep->row < $rep->bottomMargin + $rep->lineHeight)
				{
					$rep->Line($rep->row - 2);
					$rep->NewPage();
				}
			}
			$custno = $trans['debtor_no'];
			$custname = $trans['cust_name'];
			$tax_id = $trans['tax_id'];
		}	
		$taxes = getTaxes($trans['type'], $trans['trans_no']);
		if ($taxes != null)
		{
			if ($taxes['included_in_price'])
				$trans['total'] -= $taxes['tax'];
			$tax += $taxes['tax'];
		}	
		$total += $trans['total']; 
	}

	if ($custno != 0)
	{
		$rep->TextCol(0, 1, $custname);
		$rep->TextCol(1, 2,	$tax_id);
		$rep->AmountCol(2, 3, $total, $dec);
		$rep->AmountCol(3, 4, $tax, $dec);
        $rep->AmountCol(4, 5, $total + $tax, $dec);
		$totalnet += $total;
		$totaltax += $tax;
		$rep->NewLine();
	}
	$rep->Font('bold');
	$rep->NewLine();
	$rep->Line($rep->row + $rep->lineHeight);
	$rep->TextCol(0, 2,	_("Total"));
	$rep->AmountCol(2, 3, $totalnet, $dec);
	$rep->AmountCol(3, 4, $totaltax, $dec);
    $rep->AmountCol(4, 5, $totalnet + $totaltax, $dec);
	$rep->Line($rep->row - 5);
	$rep->Font();

	$rep->End();
}
