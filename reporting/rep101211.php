<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances Detailed
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");

//----------------------------------------------------------------------------------------------------
print_customer_balances();

function get_transactions()
{
    $sql = "SELECT *
	 FROM ".TB_PREF."debtor_trans
     WHERE type = 12
     AND supply_disc != 0
     OR service_disc != 0
     OR fbr_disc != 0
     OR srb_disc != 0";

    $sql .=  " GROUP BY debtor_no";

    return db_query($sql,"No transactions were returned");
}


//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    global $path_to_root;

    $comments = $_POST['PARAM_0'];
    $orientation = $_POST['PARAM_1'];
    $destination = $_POST['PARAM_2'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation ? 'L' : 'P');

	$cols = array(0);

	$headers = array(_('Customer'));

	$aligns = array('left');

    $params = array( 0 => $comments);


    $rep = new FrontReport(_('Customer Balances - Detailed 1'), "CustomerBalancesDetailed1", user_pagesize(), 9,$orientation);

    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $result = get_transactions();
	while ($myrow = db_fetch($result))
    {
//if($myrow['supply_disc'] != 0 || $myrow['service_disc'] != 0 || $myrow['fbr_disc'] != 0 || $myrow['srb_disc'] != 0)
        $rep->TextCol(0, 3, get_customer_name($myrow['debtor_no']));

        $rep->NewLine();

    }
	$rep->NewLine();
    	$rep->End();
}

?>