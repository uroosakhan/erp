<?php
$page_security = 'SA_CUSTPAYMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Aged Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

function get_cust_branches_($debtor_no)
{
    $sql = "SELECT branch_ref FROM ".TB_PREF."cust_branch 
		WHERE debtor_no = ".db_escape($debtor_no);
    $result = db_query($sql, "could not get ");

    $row = db_fetch_row($result);

    return $row[0];
}

print_aged_customer_analysis();

function get_invoices($customer_id,$branc_code, $all=true,$from,$to)
{
//	$todate = date2sql($to);
//	$PastDueDays1 = get_company_pref('past_due_days');
//	$PastDueDays2 = 2 * $PastDueDays1;


       $from = date2sql($from);
	$to = date2sql($to);

   $date12 = date('Y-m-d');
    $date11 = date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
    $date10 = date('Y-m-d',mktime(0,0,0,date('m')-1,1,date('Y')));
    $date9 = date('Y-m-d',mktime(0,0,0,date('m')-2,1,date('Y')));
    $date8 = date('Y-m-d',mktime(0,0,0,date('m')-3,1,date('Y')));
    $date7 = date('Y-m-d',mktime(0,0,0,date('m')-4,1,date('Y')));
    $date6 = date('Y-m-d',mktime(0,0,0,date('m')-5,1,date('Y')));
    $date5 = date('Y-m-d',mktime(0,0,0,date('m')-6,1,date('Y')));
    $date4 = date('Y-m-d',mktime(0,0,0,date('m')-7,1,date('Y')));
    $date3 = date('Y-m-d',mktime(0,0,0,date('m')-8,1,date('Y')));
    $date2 = date('Y-m-d',mktime(0,0,0,date('m')-9,1,date('Y')));
    $date1 = date('Y-m-d',mktime(0,0,0,date('m')-10,1,date('Y')));
    $date0 = date('Y-m-d',mktime(0,0,0,date('m')-11,1,date('Y')));
    // Revomed allocated from sql

//    $value = "(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + "
//    .TB_PREF."debtor_trans.ov_freight + ".TB_PREF."debtor_trans.ov_freight_tax + "
//    .TB_PREF."debtor_trans.ov_discount)";

//    $value = "(IF(".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.",
//    (".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst +".TB_PREF."debtor_trans.ov_freight +
//     ".TB_PREF."debtor_trans.ov_freight_tax +".TB_PREF."debtor_trans.ov_discount), 0))";
//



    $value = "(IF(".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.",
    ((d.unit_price*d.quantity) + (d.unit_tax*d.quantity*d.unit_price) -
            (d.discount_percent*d.quantity*d.unit_price)), 0))";


    $reciept = "(IF(".TB_PREF."debtor_trans.type = ".ST_BANKDEPOSIT." OR ".TB_PREF."debtor_trans.type = ".ST_CUSTPAYMENT.",
    (".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst +".TB_PREF."debtor_trans.ov_freight +
     ".TB_PREF."debtor_trans.ov_freight_tax +".TB_PREF."debtor_trans.ov_discount), 0))";



    $credit_note = "(IF(".TB_PREF."debtor_trans.type = ".ST_CUSTCREDIT." AND s.mb_flag = 'D' AND ".TB_PREF."debtor_trans.f_year=0,
    ((d.unit_price*d.quantity)+ (d.unit_tax*d.quantity*d.unit_price) - 
	    	(d.discount_percent*d.quantity*d.unit_price)), 0))";




    $due = "IF (".TB_PREF."debtor_trans.type=".ST_SALESINVOICE.",".TB_PREF."debtor_trans.due_date,
    		".TB_PREF."debtor_trans.tran_date)";
//$value as Balance, $reciept as receipts, $credit_note as credit_note,
    $sql = "SELECT ".TB_PREF."debtor_trans.type, ".TB_PREF."debtor_trans.reference,
    ".TB_PREF."debtor_trans.branch_code,".TB_PREF."debtor_trans.tran_date,
     
    	
		IF (".TB_PREF."debtor_trans.tran_date >= '$date0' AND ".TB_PREF."debtor_trans.tran_date < '$date1',$value,0) AS prd0,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date1' AND ".TB_PREF."debtor_trans.tran_date < '$date2',$value,0) AS prd1,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date2' AND ".TB_PREF."debtor_trans.tran_date < '$date3',$value,0) AS prd2,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date3' AND ".TB_PREF."debtor_trans.tran_date < '$date4',$value,0) AS prd3,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date4' AND ".TB_PREF."debtor_trans.tran_date < '$date5',$value,0) AS prd4,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date5' AND ".TB_PREF."debtor_trans.tran_date < '$date6',$value,0) AS prd5,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date6' AND ".TB_PREF."debtor_trans.tran_date < '$date7',$value,0) AS prd6,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date7' AND ".TB_PREF."debtor_trans.tran_date < '$date8',$value,0) AS prd7,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date8' AND ".TB_PREF."debtor_trans.tran_date < '$date9',$value,0) AS prd8,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date9' AND ".TB_PREF."debtor_trans.tran_date < '$date10',$value,0) AS prd9,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date10' AND ".TB_PREF."debtor_trans.tran_date < '$date11',$value,0) AS prd10,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date11' AND ".TB_PREF."debtor_trans.tran_date < '$date12',$value,0) AS prd11,
		
		IF (".TB_PREF."debtor_trans.tran_date >= '$date0' AND ".TB_PREF."debtor_trans.tran_date < '$date1',$reciept,0) AS rec0,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date1' AND ".TB_PREF."debtor_trans.tran_date < '$date2',$reciept,0) AS rec1,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date2' AND ".TB_PREF."debtor_trans.tran_date < '$date3',$reciept,0) AS rec2,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date3' AND ".TB_PREF."debtor_trans.tran_date < '$date4',$reciept,0) AS rec3,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date4' AND ".TB_PREF."debtor_trans.tran_date < '$date5',$reciept,0) AS rec4,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date5' AND ".TB_PREF."debtor_trans.tran_date < '$date6',$reciept,0) AS rec5,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date6' AND ".TB_PREF."debtor_trans.tran_date < '$date7',$reciept,0) AS rec6,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date7' AND ".TB_PREF."debtor_trans.tran_date < '$date8',$reciept,0) AS rec7,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date8' AND ".TB_PREF."debtor_trans.tran_date < '$date9',$reciept,0) AS rec8,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date9' AND ".TB_PREF."debtor_trans.tran_date < '$date10',$reciept,0) AS rec9,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date10' AND ".TB_PREF."debtor_trans.tran_date < '$date11',$reciept,0) AS rec10,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date11' AND ".TB_PREF."debtor_trans.tran_date < '$date12',$reciept,0) AS rec11,
	
	
		IF (".TB_PREF."debtor_trans.tran_date >= '$date0' AND ".TB_PREF."debtor_trans.tran_date < '$date1',$credit_note,0) AS credit_note0,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date1' AND ".TB_PREF."debtor_trans.tran_date < '$date2',$credit_note,0) AS credit_note1,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date2' AND ".TB_PREF."debtor_trans.tran_date < '$date3',$credit_note,0) AS credit_note2,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date3' AND ".TB_PREF."debtor_trans.tran_date < '$date4',$credit_note,0) AS credit_note3,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date4' AND ".TB_PREF."debtor_trans.tran_date < '$date5',$credit_note,0) AS credit_note4,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date5' AND ".TB_PREF."debtor_trans.tran_date < '$date6',$credit_note,0) AS credit_note5,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date6' AND ".TB_PREF."debtor_trans.tran_date < '$date7',$credit_note,0) AS credit_note6,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date7' AND ".TB_PREF."debtor_trans.tran_date < '$date8',$credit_note,0) AS credit_note7,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date8' AND ".TB_PREF."debtor_trans.tran_date < '$date9',$credit_note,0) AS credit_note8,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date9' AND ".TB_PREF."debtor_trans.tran_date < '$date10',$credit_note,0) AS credit_note9,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date10' AND ".TB_PREF."debtor_trans.tran_date < '$date11',$credit_note,0) AS credit_note10,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date11' AND ".TB_PREF."debtor_trans.tran_date < '$date12',$credit_note,0) AS credit_note11
		FROM ".TB_PREF."debtors_master,
			".TB_PREF."debtor_trans
				LEFT JOIN ".TB_PREF."debtor_trans_details d
			ON d.debtor_trans_type  = ".TB_PREF."debtor_trans.type
			AND d.debtor_trans_no =  ".TB_PREF."debtor_trans.trans_no 

		LEFT JOIN ".TB_PREF."stock_master s
			ON d.stock_id  =  s.stock_id

		WHERE ";

    $sql .= " ".TB_PREF."debtors_master.debtor_no = ".TB_PREF."debtor_trans.debtor_no
         AND ".TB_PREF."debtor_trans.debtor_no = '$customer_id' 
         AND ".TB_PREF."debtor_trans.branch_code = '$branc_code' 


         AND ".TB_PREF."debtor_trans.tran_date >= '$from'
	 AND ".TB_PREF."debtor_trans.tran_date <= '$to'
         AND ABS(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + 
        ".TB_PREF."debtor_trans.ov_freight + ".TB_PREF."debtor_trans.ov_freight_tax + 
        ".TB_PREF."debtor_trans.ov_discount) > ".FLOAT_COMP_DELTA." ";

    return db_query($sql, "The customer details could not be retrieved");
}

function get_creditnote_amount($debtor_no,$from,$to)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $date12 = date('Y-m-d');
    $date11 = date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
    $date10 = date('Y-m-d',mktime(0,0,0,date('m')-1,1,date('Y')));
    $date9 = date('Y-m-d',mktime(0,0,0,date('m')-2,1,date('Y')));
    $date8 = date('Y-m-d',mktime(0,0,0,date('m')-3,1,date('Y')));
    $date7 = date('Y-m-d',mktime(0,0,0,date('m')-4,1,date('Y')));
    $date6 = date('Y-m-d',mktime(0,0,0,date('m')-5,1,date('Y')));
    $date5 = date('Y-m-d',mktime(0,0,0,date('m')-6,1,date('Y')));
    $date4 = date('Y-m-d',mktime(0,0,0,date('m')-7,1,date('Y')));
    $date3 = date('Y-m-d',mktime(0,0,0,date('m')-8,1,date('Y')));
    $date2 = date('Y-m-d',mktime(0,0,0,date('m')-9,1,date('Y')));
    $date1 = date('Y-m-d',mktime(0,0,0,date('m')-10,1,date('Y')));
    $date0 = date('Y-m-d',mktime(0,0,0,date('m')-11,1,date('Y')));


    $credit_notes = "".TB_PREF."debtor_trans.ov_amount";


    $sql = "SELECT ".TB_PREF."debtor_trans.`ov_amount`,
 		IF (".TB_PREF."debtor_trans.tran_date >= '$date0' AND	 ".TB_PREF."debtor_trans.tran_date < '$date1',$credit_notes,0) AS credit_notes0,
IF (".TB_PREF."debtor_trans.tran_date >= '$date1' AND ".TB_PREF."debtor_trans.tran_date < '$date2',$credit_notes,0) AS credit_notes1,
IF (".TB_PREF."debtor_trans.tran_date >= '$date2' AND  ".TB_PREF."debtor_trans.tran_date < '$date3',$credit_notes,0) AS credit_notes2,
IF (".TB_PREF."debtor_trans.tran_date >= '$date3' AND  ".TB_PREF."debtor_trans.tran_date < '$date4',$credit_notes,0) AS credit_notes3,
IF (".TB_PREF."debtor_trans.tran_date >= '$date4' AND 	 ".TB_PREF."debtor_trans.tran_date < '$date5',$credit_notes,0) AS credit_notes4,
IF (".TB_PREF."debtor_trans.tran_date >= '$date5' AND ".TB_PREF."debtor_trans.tran_date < '$date6',$credit_notes,0) AS credit_notes5,
IF (".TB_PREF."debtor_trans.tran_date >= '$date6' AND ".TB_PREF."debtor_trans.tran_date < '$date7',$credit_notes,0) AS credit_notes6,
IF (".TB_PREF."debtor_trans.tran_date >= '$date7' AND ".TB_PREF."debtor_trans.tran_date < '$date8',$credit_notes,0) AS credit_notes7,
IF (".TB_PREF."debtor_trans.tran_date >= '$date8' AND ".TB_PREF."debtor_trans.tran_date < '$date9',$credit_notes,0) AS credit_notes8,
IF (".TB_PREF."debtor_trans.tran_date >= '$date9' AND ".TB_PREF."debtor_trans.tran_date < '$date10',$credit_notes,0) AS credit_notes9,
IF (".TB_PREF."debtor_trans.tran_date >= '$date10' AND	 ".TB_PREF."debtor_trans.tran_date < '$date11',$credit_notes,0) AS credit_notes10,
IF (".TB_PREF."debtor_trans.tran_date >= '$date11' AND ".TB_PREF."debtor_trans.tran_date < '$date12',$credit_notes,0) AS credit_notes11


 FROM ".TB_PREF."debtor_trans WHERE ".TB_PREF."debtor_trans.type=11 AND ".TB_PREF."debtor_trans.f_year!=0 
AND ".TB_PREF."debtor_trans.debtor_no=$debtor_no	
 AND ".TB_PREF."debtor_trans.tran_date >= '$from'
	 AND ".TB_PREF."debtor_trans.tran_date <= '$to' ";
    $result = db_query($sql, "could not get sales type");
    $row = db_fetch($result);
    return $row;
}
function get_areas($id)
{
    $sql = "SELECT description FROM ".TB_PREF."areas WHERE area_code=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_aged_customer_analysis()
{
    global $path_to_root, $systypes_array;

    $from_date = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $fromcust = $_POST['PARAM_2'];
    $area = $_POST['PARAM_3'];
    $groups = $_POST['PARAM_4'];
    $folk = $_POST['PARAM_5'];
    $currency = $_POST['PARAM_6'];
    $comments = $_POST['PARAM_7'];
    $destination = $_POST['PARAM_8'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
//	$orientation = ($orientation ? 'L' : 'P');

    $orientation = 'L';
    /*	if ($graphics)
        {
            include_once($path_to_root . "/reporting/includes/class.graphic.inc");
            $pg = new graph();
        }
    */
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
    /*
        if ($summaryOnly == 1)
            $summary = _('Summary Only');
        else
            $summary = _('Detailed Report');
    */
    if ($currency == ALL_TEXT)
    {
        $convert = true;
        $currency = _('Balances in Home Currency');
    }
    else
        $convert = false;
    /*
        if ($no_zeros) $nozeros = _('Yes');
        else $nozeros = _('No');
        if ($show_all) $show = _('Yes');
        else $show = _('No');
    */
    /*	$PastDueDays1 = get_company_pref('past_due_days');
        $PastDueDays2 = 2 * $PastDueDays1;
        $nowdue = "1-" . $PastDueDays1 . " " . _('Days');
        $pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
        $pastdue2 = _('Over') . " " . $PastDueDays2 . " " . _('Days');
    */
//	$cols = array(0, 100, 130, 190,	250, 320, 385, 450,	515);
//	$headers = array(_('Customer'),	'',	'',	_('Current'), $nowdue, $pastdue1, $pastdue2,
//		_('Total Balance'));

//	$aligns = array('left',	'left',	'left',	'right', 'right', 'right', 'right',	'right');


//	$cols = array(0, 0, 95, 150, 195, 250, 295, 350, 395, 450, 495, 550);
    $cols = array(0, 0, 70, 80,130, 160,190, 220,250, 290, 320, 340, 380,410,450,490,540,620
    ,650,690,720,750,780,800,835,870,905,940,980,1070,1100,1150);
//	$cols = array(0, 0, 80, 90,130, 170,210, 250,290, 340, 390, 440, 490,540,590,610,650);

    $per0 = strftime('%b',mktime(0,0,0,date('m'),1,date('Y')));
    $per1 = strftime('%b',mktime(0,0,0,date('m')-1,1,date('Y')));
    $per2 = strftime('%b',mktime(0,0,0,date('m')-2,1,date('Y')));
    $per3 = strftime('%b',mktime(0,0,0,date('m')-3,1,date('Y')));
    $per4 = strftime('%b',mktime(0,0,0,date('m')-4,1,date('Y')));
    $per5 = strftime('%b',mktime(0,0,0,date('m')-5,1,date('Y')));
    $per6 = strftime('%b',mktime(0,0,0,date('m')-6,1,date('Y')));
    $per7 = strftime('%b',mktime(0,0,0,date('m')-7,1,date('Y')));
    $per8 = strftime('%b',mktime(0,0,0,date('m')-8,1,date('Y')));
    $per9 = strftime('%b',mktime(0,0,0,date('m')-9,1,date('Y')));
    $per10 = strftime('%b',mktime(0,0,0,date('m')-10,1,date('Y')));
    $per11 = strftime('%b',mktime(0,0,0,date('m')-11,1,date('Y')));

    $headers = array(_('Customers'), '', '',_("Group"),_("City"), $per11."Sales",_("$per11 Recovery"), $per10."Sales",_("$per10 Recovery"),
        $per9."Sales",_("$per9 Recovery"),  $per8."Sales",_("$per8 Recovery"),  $per7."Sales",_("$per7 Recovery"), $per6."Sales",_("$per6 Recovery"), $per5."Sales",_("$per5 Recovery"),
        $per4."Sales",_("$per4 Recovery"),$per3."Sales",_("$per3 Recovery"), $per2."Sales",_(" $per2 Recovery"), $per1."Sales",_("$per1 Recovery"),  $per0."Sales",_("$per0 Recovery"),
        _('Total Quantity'),
        _('Total Recovery'));

    $aligns = array('left',	'left','left','left', 'right','right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right');


    $params =   array( 	0 => $comments,
        1 => array('text' => _('End Date'), 'from' => $to, 'to' => ''),
        2 => array('text' => _('Customer'),	'from' => $from, 'to' => ''),
        3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
        4 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),
        5 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
    );

//	if ($convert)
//		$headers[2] = _('Currency');
    $rep = new FrontReport(_('Annual Sales Report'), "AgedCustomerAnalysis", user_pagesize(), 10, $orientation);
//    if ($orientation == 'L')

    recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $total = array(0,0,0,0,0);
    /*
        $sql = "SELECT debtor_no, name, curr_code FROM ".TB_PREF."debtors_master";
        if ($fromcust != ALL_TEXT)
            $sql .= " WHERE debtor_no=".db_escape($fromcust);
        $sql .= " ORDER BY name";
        $result = db_query($sql, "The customers could not be retrieved");
    */

    $sql = "SELECT ".TB_PREF."debtors_master.debtor_no,
			".TB_PREF."debtors_master.name,
			".TB_PREF."cust_branch.branch_code,
			".TB_PREF."cust_branch.branch_ref,
			".TB_PREF."cust_branch.area,
			".TB_PREF."cust_branch.group_no
	    	FROM ".TB_PREF."debtors_master,
	    	
	    	
		 ".TB_PREF."cust_branch,
		 ".TB_PREF."areas,
		  ".TB_PREF."salesman
		 	WHERE ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		 	 AND ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		 	 AND ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";


    if ($fromcust != ALL_TEXT )
    {

        $sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
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
    elseif ($groups != 0)
    {
        $sql .= " AND ".TB_PREF."cust_branch.group_no= ".db_escape($groups);
    }

    $sql .= " GROUP BY  ".TB_PREF."cust_branch.branch_code";
    $result = db_query($sql, "The customers could not be retrieved");




    while ($myrow=db_fetch($result))
    {
        if (!$convert && $currency != $myrow['curr_code'])
            continue;

        if ($convert) $rate = get_exchange_rate_from_home_currency($myrow['curr_code'], $to);
        else $rate = 1.0;
        $rep->TextCol(0, 3, $myrow['branch_ref']);
        $total[0] = 0;
        $total[1] = 0;
        $total[2] = 0;
        $total[3] = 0;
        $total[4] = 0;
        $total[5] = 0;
        $total[6] = 0;
        $total[7] = 0;
        $total[8] = 0;
        $total[9] = 0;
        $total[10] = 0;
        $total[11] = 0;

  $total[12] =0;
                $total[13]=  0;
                $total[14] = 0;
                $total[15] =  0;
                $total[16] = 0;
                $total[17] =  0;
                $total[18] = 0;
                $total[19] = 0;
                $total[20] = 0;
                $total[21] =  0;
                $total[22] =  0;
                $total[23] =0;





        $linetotal = 0;
        $recover_total = 0;
        {

            $res = get_invoices($myrow['debtor_no'],$myrow['branch_code'],'',$from_date,$to);
            while ($trans=db_fetch($res))
            {

                $credit=get_creditnote_amount($myrow['debtor_no'],$from_date,$to);

                

                foreach ($trans as $i => $value)
                    $trans[$i] *= $rate;
                $str = array(
                    $trans["prd0"],
                    $trans["prd1"],
                    $trans["prd2"],
                    $trans["prd3"],
                    $trans["prd4"],
                    $trans["prd5"],
                    $trans["prd6"],
                    $trans["prd7"],
                    $trans["prd8"],
                    $trans["prd9"],
                    $trans["prd10"],
                    $trans["prd11"],

                    //for reciept
                    $trans["rec0"],
                    $trans["rec1"],
                    $trans["rec2"],
                    $trans["rec3"],
                    $trans["rec4"],
                    $trans["rec5"],
                    $trans["rec6"],
                    $trans["rec7"],
                    $trans["rec8"],
                    $trans["rec9"],
                    $trans["rec10"],
                    $trans["rec11"],

                    //for creditnote

                    $trans["credit_note0"],
                    $trans["credit_note1"],
                    $trans["credit_note2"],
                    $trans["credit_note3"],
                    $trans["credit_note4"],
                    $trans["credit_note5"],
                    $trans["credit_note6"],
                    $trans["credit_note7"],
                    $trans["credit_note8"],
                    $trans["credit_note9"],
                    $trans["credit_note10"],
                    $trans["credit_note11"]

                );


                $recovery[0]= $trans["rec0"]+ $trans["credit_note0"]+ $credit["credit_notes0"];
                $recovery[1]=$trans["rec1"]+ $trans["credit_note1"]+ $credit["credit_notes1"];
                $recovery[2]=$trans["rec2"]+ $trans["credit_note2"]+ $credit["credit_notes2"];
                $recovery[3]=$trans["rec3"]+ $trans["credit_note3"]+ $credit["credit_notes3"];
                $recovery[4]=$trans["rec4"]+ $trans["credit_note4"]+ $credit["credit_notes4"];
                $recovery[5]=$trans["rec5"]+ $trans["credit_note5"]+ $credit["credit_notes5"];
                $recovery[6]=$trans["rec6"]+ $trans["credit_note6"]+ $credit["credit_notes6"];
                $recovery[7]=$trans["rec7"]+ $trans["credit_note7"]+ $credit["credit_notes7"];
                $recovery[8]=$trans["rec8"]+ $trans["credit_note8"]+ $credit["credit_notes8"];
                $recovery[9]=$trans["rec9"]+ $trans["credit_note9"]+ $credit["credit_notes9"];
                $recovery[10]=$trans["rec10"]+ $trans["credit_note10"]+ $credit["credit_notes10"];
                $recovery[11]=$trans["rec11"]+ $trans["credit_note11"]+ $credit["credit_notes11"];



                $total[0] += $str["0"];
                $total[1] += $str["1"];
                $total[2] += $str["2"];
                $total[3] += $str["3"];
                $total[4] += $str["4"];
                $total[5] += $str["5"];
                $total[6] += $str["6"];
                $total[7] += $str["7"];
                $total[8] += $str["8"];
                $total[9] += $str["9"];
                $total[10] += $str["10"];
                $total[11] += $str["11"];


                $total[12] +=  $recovery[0];
                $total[13] +=  $recovery[1];
                $total[14] +=  $recovery[2];
                $total[15] +=  $recovery[3];
                $total[16] +=  $recovery[4];
                $total[17] +=  $recovery[5];
                $total[18] +=  $recovery[6];
                $total[19] +=  $recovery[7];
                $total[20] +=  $recovery[8];
                $total[21] +=  $recovery[9];
                $total[22] +=  $recovery[10];
                $total[23] +=  $recovery[11];





                $linetotal = 
                    $total[0] + $total[1] + $total[2] + $total[3] + $total[4]+ $total[5]+ $total[6]+ $total[7]+ $total[8]+ $total[9]+ $total[10]+ $total[11];

               
                $recover_total = $total[12] + $total[13] + $total[14] + $total[15] + $total[16]+ $total[17]+ $total[18]
                    + $total[19]+ $total[20]+ $total[21]+ $total[22]+ $total[23];







              $grandtotal[0] += $str["0"];
                $grandtotal[1] += $str["1"];
                $grandtotal[2] += $str["2"];
                $grandtotal[3] += $str["3"];
                $grandtotal[4] += $str["4"];
                $grandtotal[5] += $str["5"];
                $grandtotal[6] += $str["6"];
                $grandtotal[7] += $str["7"];
                $grandtotal[8] += $str["8"];
                $grandtotal[9] += $str["9"];
                $grandtotal[10] += $str["10"];
                $grandtotal[11] += $str["11"];

                $grandtotal[12] +=  $recovery[0];
                $grandtotal[13] +=  $recovery[1];
                $grandtotal[14] +=  $recovery[2];
                $grandtotal[15] +=  $recovery[3];
                $grandtotal[16] +=  $recovery[4];
                $grandtotal[17] +=  $recovery[5];
                $grandtotal[18] +=  $recovery[6];
                $grandtotal[19] +=  $recovery[7];
                $grandtotal[20] +=  $recovery[8];
                $grandtotal[21] +=  $recovery[9];
                $grandtotal[22] +=  $recovery[10];
                $grandtotal[23] +=  $recovery[11];







        }
            $rep->TextCol(3, 4, get_sales_group_name($myrow['group_no']), $dec);
            $rep->TextCol(4, 5, get_areas($myrow['area']), $dec);
            $rep->AmountCol(5, 6, $total[0], $dec);
            $rep->AmountCol(6, 7, $total[12], $dec);




            $ID1 = number_format2(($total['1'] - $total['0']) / $total['0']*100, $dec);
            //$rep->TextCol(3, 4, $ID1 ." %", -2);
            $rep->AmountCol(7, 8, $total[1], $dec);
            $rep->AmountCol(8, 9, $total[13], $dec);



            $ID2 = number_format2(($total['2'] - $total['1']) / $total['1']*100, $dec);
            //$rep->TextCol(5, 6, $ID2 ." %", -2);
            $rep->AmountCol(9, 10, $total[2], $dec);
            $rep->AmountCol(10, 11, $total[14], $dec);



            $ID3 = number_format2(($total['3'] - $total['2']) / $total['2']*100, $dec);
            //$rep->TextCol(7, 8, $ID3 ." %", -2);
            $rep->AmountCol(11, 12, $total[3], $dec);
            $rep->AmountCol(12, 13, $total[15], $dec);
            $ID4 = number_format2(($total['4'] - $total['3']) / $total['3']*100, $dec);
            //$rep->TextCol(9, 10, $ID4 ." %", -2);
            $rep->AmountCol(13, 14, $total[4], $dec);
            $rep->AmountCol(14, 15, $total[16], $dec);


            $rep->AmountCol(15, 16, $total[5], $dec);
            $rep->AmountCol(16, 17, $total[17], $dec);


            $rep->AmountCol(17, 18, $total[6], $dec);
            $rep->AmountCol(18, 19, $total[18], $dec);




            $rep->AmountCol(19, 20, $total[7], $dec);
            $rep->AmountCol(20, 21, $total[19], $dec);



            $rep->AmountCol(21, 22, $total[8], $dec);
            $rep->AmountCol(22, 23, $total[20], $dec);

            $rep->AmountCol(23, 24, $total[9], $dec);
            $rep->AmountCol(24, 25, $total[21], $dec);


            $rep->AmountCol(25, 26, $total[10], $dec);
            $rep->AmountCol(26, 27, $total[22], $dec);

            $rep->AmountCol(27, 28, $total[11], $dec);
            $rep->AmountCol(28, 29, $total[23], $dec);

            $rep->Font(b);
            $rep->AmountCol(29, 30 , $linetotal, $dec);
            $rep->AmountCol(30, 31 , $recover_total, $dec);
            $rep->Font();
            $grosstotal += $linetotal;
            $gross_rec_total += $recover_total;

            //	$rep->Line($rep->row - 8);
            $rep->NewLine();
        }

    }

    $rep->Line($rep->row  + 4);
    $rep->NewLine();
    $rep->fontSize += 2;
    $rep->TextCol(0, 3, _('Grand Total'));
    $rep->fontSize -= 2;
//	for ($i = 0; $i < count($total); $i++)
//	{
    $rep->Font(b);
    $rep->AmountCol(5, 6, $grandtotal[0], $dec);
    $rep->AmountCol(6, 7, $grandtotal[12], $dec);



    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(7, 8, $grandtotal[1], $dec);
    $rep->AmountCol(8, 9, $grandtotal[13], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(9, 10, $grandtotal[2], $dec);
    $rep->AmountCol(10, 11, $grandtotal[14], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(11, 12, $grandtotal[3], $dec);
    $rep->AmountCol(12, 13, $grandtotal[15], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(13, 14, $grandtotal[4], $dec);
    $rep->AmountCol(14, 15, $grandtotal[16], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(15, 16, $grandtotal[5], $dec);
    $rep->AmountCol(16, 17, $grandtotal[17], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(17, 18, $grandtotal[6], $dec);
    $rep->AmountCol(18, 19, $grandtotal[18], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(19, 20, $grandtotal[7], $dec);
    $rep->AmountCol(20, 21, $grandtotal[19], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(21, 22, $grandtotal[8], $dec);
    $rep->AmountCol(22, 23, $grandtotal[20], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(23, 24, $grandtotal[9], $dec);
    $rep->AmountCol(24, 25, $grandtotal[21], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(25, 26, $grandtotal[10], $dec);
    $rep->AmountCol(26, 27, $grandtotal[22], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(27, 28, $grandtotal[11], $dec);
    $rep->AmountCol(28, 29, $grandtotal[23], $dec);
    $rep->Font();

    $rep->Font(b);
    $rep->AmountCol(29, 30, $grosstotal, $dec);
    $rep->Font(); 
    
    $rep->Font(b);
    $rep->AmountCol(30, 31, $gross_rec_total, $dec);
    $rep->Font();

    $rep->NewLine();
    $rep->End();
}

?>