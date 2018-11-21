<?php

$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");

//----------------------------------------------------------------------------------------------------

print_customer_balances();

function get_open_balance($debtorno, $to)
{
	if($to)
		$to = date2sql($to);

     $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0),
     	-abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";
     $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND (t.type = ".ST_JOURNAL." AND t.ov_amount<0),
     	abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * -1, 0)) AS credits,";
    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount<0), t.alloc * -1, t.alloc)) AS Allocated,";

 	$sql .=	"SUM(IF(t.type = ".ST_SALESINVOICE.", 1, -1) *
 			(-abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) - abs(t.alloc))) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
function get_sql_for_query($from, $to, $item, $resource,$user)
{

    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT ".TB_PREF."query.`date`,	".TB_PREF."source_status.description AS  source,
	".TB_PREF."query.`name`,
	".TB_PREF."query.`mobile`,".TB_PREF."query.`email`,".TB_PREF."query_status.status AS status,".TB_PREF."query.`business_name`,
	".TB_PREF."query.`care_of`,
".TB_PREF."query.`phone1`,
	".TB_PREF."query.`phone2`,".TB_PREF."query.`package_final`,
	".TB_PREF."query.`address`,".TB_PREF."query.`remarks`,".TB_PREF."query.`package` ,".TB_PREF."users.`user_id`,".TB_PREF."query.`id`
	,".TB_PREF."query.`stock_id`	,".TB_PREF."query.`source_status`,".TB_PREF."query.`status` as status_	
	 FROM `".TB_PREF."query` 
		INNER JOIN  ".TB_PREF."query_status ON ".TB_PREF."query_status.id=".TB_PREF."query.`status`
		INNER JOIN  ".TB_PREF."source_status ON ".TB_PREF."source_status.id=".TB_PREF."query.`source_status`
		INNER JOIN  ".TB_PREF."users ON ".TB_PREF."users.id=".TB_PREF."query.`user`
		WHERE ".TB_PREF."query.`date`>='$from'
		AND ".TB_PREF."query.`date`<='$to'";
    
    if($item != ALL_TEXT)
        $sql .= " AND ".TB_PREF."query.`stock_id`=".db_escape($item);
    if($resource != ALL_TEXT)
        $sql .= " AND ".TB_PREF."query.`source_status`=".db_escape($resource);

 if($user != '')
        $sql .= " AND ".TB_PREF."query.`user`=".db_escape($user);

    return db_query($sql,"error");


}
function get_transactions($debtorno, $from, $to, $user)
{
	$from = date2sql($from);
	$to = date2sql($to);

 	$allocated_from = 
 			"(SELECT trans_type_from as trans_type, trans_no_from as trans_no, date_alloc, sum(amt) amount
 			FROM ".TB_PREF."cust_allocations alloc
 				WHERE person_id=".db_escape($debtorno)."
 					AND date_alloc <= '$to'
 				GROUP BY trans_type_from, trans_no_from) alloc_from";
 	$allocated_to = 
 			"(SELECT trans_type_to as trans_type, trans_no_to as trans_no, date_alloc, sum(amt) amount
 			FROM ".TB_PREF."cust_allocations alloc
 				WHERE person_id=".db_escape($debtorno)."
 					AND date_alloc <= '$to'
 				GROUP BY trans_type_to, trans_no_to) alloc_to";

     $sql = "SELECT trans.*,
 		(trans.ov_amount + trans.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2) AS TotalAmount,
 		IFNULL(alloc_from.amount, alloc_to.amount) AS Allocated,
 		((trans.type = ".ST_SALESINVOICE.")	AND trans.due_date < '$to') AS OverDue
     	FROM ".TB_PREF."debtor_trans trans
 			LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
 			LEFT JOIN $allocated_from ON alloc_from.trans_type = trans.type AND alloc_from.trans_no = trans.trans_no
 			LEFT JOIN $allocated_to ON alloc_to.trans_type = trans.type AND alloc_to.trans_no = trans.trans_no

     	WHERE trans.tran_date >= '$from'
 			AND trans.tran_date <= '$to'
 			AND trans.debtor_no = ".db_escape($debtorno)."
 			AND trans.type <> ".ST_CUSTDELIVERY."
 			AND ISNULL(voided.id)
     	ORDER BY trans.tran_date";
    return db_query($sql,"No transactions were returned");
}
function get_item_name($item)
{
    $sql = "SELECT description FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($item);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_resource($resource)
{
    $sql = "SELECT description FROM ".TB_PREF."source_status WHERE id=".db_escape($resource);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
    	$user = $_POST['PARAM_2'];
        $item = $_POST['PARAM_3'];
    	$resource = $_POST['PARAM_4'];
        $orientation = $_POST['PARAM_5'];
        $destination = $_POST['PARAM_6'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ('L');
	if ($fromcust == ALL_TEXT)
		$cust = _('All');
	else
		$cust = get_customer_name($fromcust);
    	$dec = user_price_dec();

	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('Balances in Home Currency');
	}
	else
		$convert = false;

    if ($item == '')
        $itm = _('All');
    else
        $itm = $item;

    if ($resource == '')
        $res = _('All');
    else
        $res = $item;
    if ($resource == ALL_TEXT)
        $res = _('All');
    else
        $res = get_resource($resource);


	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 50, 130, 230,	300, 470, 510, 600, 700);

	$headers = array(_('Date'), _('Resource'), _('Mobile'), _('Status'), _('Item'), _('C/O'),
		_('Phone1'), _('Phone2'),  _('Users'));

	if ($show_balance)
		$headers[7] = _('Balance');
	$aligns = array('left',	'left',	'left',	'left',	'left', 'left', 'left', 'left','left');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    2 => array('text' => _('Item'), 'from' => $itm,   	'to' => ''),
    				    2 => array('text' => _('Resource'), 'from' => $res,   	'to' => ''));
    				  

    $rep = new FrontReport(_('Query Report'), "CustomerBalances", user_pagesize(), 9, $orientation);
//    if ($orientation == 'L')
//    	recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);

    $result = get_sql_for_query($from, $to, $item, $resource, $user);
	while ($myrow = db_fetch($result))
	{

		$rep->fontSize += 2;
		$rep->TextCol(0, 2, $myrow['name']);
		if ($convert)

		$rep->fontSize -= 2;
		$rep->NewLine(1, 2);

		if (db_num_rows($result)==0) {
			$rep->NewLine(1, 2);
			continue;
		}
			$rep->NewLine(1, 2);
			$rep->DateCol(0, 1,	$myrow['date'],true);
			$rep->TextCol(1, 2,	$myrow['source']);
            $rep->TextCol(2, 3, $myrow['mobile']);
            $rep->TextCol(3, 4,	$myrow['status']);
            $rep->TextCol(4, 5, get_item_name($myrow['stock_id']));
            $rep->TextCol(5, 6,	$myrow['care_of']);
            $rep->TextCol(6, 7,	$myrow['phone1']);
            $rep->TextCol(7, 8,	$myrow['phone2']);
            $rep->TextCol(8, 9,	$myrow['user_id']);

            if ($myrow['remarks'] != "")
            {
                $rep->NewLine();
                $rep->TextCol(0, 8, "Remarks: ".$myrow['remarks'] , -2);
            }

   		$rep->Line($rep->row  - 4);
   		$rep->NewLine(2);
	}

//	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    	$rep->End();
}

