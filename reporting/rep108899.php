<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");

//----------------------------------------------------------------------------------------------------

print_employee_balances();

function getTransactions($fromcust)
{

    $sql = "SELECT  dm.*, pr.*

    			FROM ".TB_PREF."debtors_master AS dm
    			LEFT JOIN ".TB_PREF."cust_branch cb ON cb.`debtor_no` = dm.`debtor_no` 
    			LEFT JOIN ".TB_PREF."crm_contacts con ON cb.`branch_code` = con.`entity_id`
                LEFT JOIN ".TB_PREF."crm_persons pr ON con.`person_id` = pr.`id`
                  ";

    	if ($fromcust != ALL_TEXT )
        {
            $sql .= " WHERE dm.debtor_no=".db_escape($fromcust);
        }

    $result = db_query($sql, "The customers could not be retrieved");
    	return $result;
}

//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    global $path_to_root, $systypes_array;
    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $fromcust = $_POST['PARAM_0'];;
    $comments = $_POST['PARAM_1'];
    $orientation = $_POST['PARAM_2'];
    $destination = $_POST['PARAM_3'];
    if ($destination)
    	include_once($path_to_root . "/reporting/includes/excel_report.inc");

    $orientation = ($orientation ? 'L' : 'P');



    $cols = array(0, 20, 120,250, 290,	350,400,450, 510);

    $headers = array(_('S.No'), _('A/C No'), _('Employee Name'), _('Designation'), _('Amount'));

    $aligns = array('left',	'left',	'left',	'left',	'right');

    $params =   array( 	0 => $comments,
      //  1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to)
        //2 => array('text' => _('Bank'), 'from' => $bank_name, 'to' => '')

    );

    $rep = new FrontReport(_('Bank Direct Transfer Letter'), "SupplierBalances", user_pagesize(), 9, P);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->SetHeaderType('Header108882');
    $rep->Info($params, $cols, null, $aligns);

    $rep->Font();

    $rep->NewPage();

    $bank_account_details = get_bank_account($banks);
    $bank_account_no = $bank_account_details['bank_account_number'];
    $bank_address = $bank_account_details['bank_address'];

    $rep->NewLine();
    $rep->NewLine();

    $rep->NewLine(2);
    $rep->NewLine(1);

    $result = getTransactions($fromcust);
    while ($myrow=db_fetch($result)) {
        
        $rep->SetCommonData($myrow, null, $myrow, $bank_account_details, ST_SALESORDER12, null);

        $rep->MultiCell(50, 15, "", 0, 'L', 0, 2, 30, 110, true);

        $rep->multicell(545, 20, "REQUEST FORM - CUSTOMER ACCOUNT OPENING IN SOFTWARE", 1, 'C', 1, 0, 25, 170, false);

        $rep->MultiCell(50, 50, "DATE :", 0, 'L', 0, 2, 30, 235, true);
        $rep->MultiCell(550, 10, "________________", 0, 'L', 0, 2, 120, 235, true);

        $rep->MultiCell(50, 50, "NAME    ", 0, 'L', 0, 2, 30, 265, true);
        $rep->MultiCell(550, 10, $myrow['debtor_ref'], 0, 'L', 0, 2, 120, 263, true);
        $rep->MultiCell(550, 10, "_________________________", 0, 'L', 0, 2, 120, 265, true);

        $rep->MultiCell(150, 50, "ADDRESS     ", 0, 'L', 0, 2, 30, 295, true);
        $rep->MultiCell(550, 50, $myrow['address'], 0, 'L', 0, 2, 120, 292, true);
        $rep->MultiCell(550, 50, "___________________________________________________________________________________", 0, 'L', 0, 2, 120, 295, true);
        $rep->MultiCell(550, 50, "___________________________________________________________________________________", 0, 'L', 0, 2, 120, 320, true);

        $rep->MultiCell(150, 50, "PHONE NO. ", 0, 'L', 0, 2, 30, 350, true);
        $rep->MultiCell(150, 50, $myrow['phone2'], 0, 'L', 0, 2, 120, 347, true);
        $rep->MultiCell(150, 50, "_________________________", 0, 'L', 0, 2, 120, 350, true);

        $rep->MultiCell(150, 50, "FAX NO. ", 0, 'L', 0, 2, 350, 350, true);
        $rep->MultiCell(150, 50, $myrow['fax'], 0, 'L', 0, 2, 420, 347, true);
        $rep->MultiCell(150, 50, "_________________________", 0, 'L', 0, 2, 410, 350, true);

        $rep->MultiCell(150, 50, "CELL ", 0, 'L', 0, 2, 30, 380, true);
        $rep->MultiCell(150, 50, $myrow['phone'], 0, 'L', 0, 2, 120, 380, true);
        $rep->MultiCell(150, 50, "_________________________", 0, 'L', 0, 2, 120, 380, true);

        $rep->MultiCell(150, 50, "N.T.N. NO.", 0, 'L', 0, 2, 30, 410, true);
        $rep->MultiCell(150, 50, $myrow['ntn_no'], 0, 'L', 0, 2, 120, 410, true);
        $rep->MultiCell(250, 50, "_________________________", 0, 'L', 0, 2, 120, 410, true);

        $rep->MultiCell(250, 50, "CONTACT PERSON ", 0, 'L', 0, 2, 30, 440, true);
        $rep->MultiCell(250, 50, "_________________________", 0, 'L', 0, 2, 120, 440, true);

        $rep->MultiCell(150, 50, "REMARKS", 0, 'L', 0, 2, 30, 470, true);
        $rep->MultiCell(550, 50, $myrow['notes'], 0, 'L', 0, 2, 120, 470, true);
        $rep->MultiCell(550, 50, "____________________________________________________________________________________", 0, 'L', 0, 2, 120, 470, true);

        $rep->MultiCell(150, 50, "STRN NO. ", 0, 'L', 0, 2, 30, 500, true);
        $rep->MultiCell(150, 50, "__________________________", 0, 'L', 0, 2, 120, 500, true);

        $rep->MultiCell(150, 50, "TRANSPORT. ", 0, 'L', 0, 2, 30, 530, true);
        $rep->MultiCell(550, 650, "_________________________ ", 0, 'L', 0, 2, 120, 530, true);

        $rep->MultiCell(150, 50, "EMAIL. ", 0, 'L', 0, 2, 30, 560, true);
        $rep->MultiCell(250, 50, $myrow['email'], 0, 'L', 0, 2, 120, 560, true);
        $rep->MultiCell(250, 50, "__________________________", 0, 'L', 0, 2, 120, 560, true);

        $rep->MultiCell(150, 50, "Sales Manager ", 0, 'L', 0, 2, 63, 680, true);
        $rep->MultiCell(150, 50, " ____________________________", 0, 'L', 0, 2, 33, 670, true);

        $rep->MultiCell(150, 50, "Assistant Manager Sales ", 0, 'L', 0, 2, 438, 680, true);
        $rep->MultiCell(150, 50, "_____________________________", 0, 'L', 0, 2, 420, 670, true);

        $rep->MultiCell(100, 50, "Accounts Officer", 0, 'L', 0, 2, 63, 745, true);
        $rep->MultiCell(550, 650, "____________________________", 0, 'L', 0, 2, 33, 730, true);

        $rep->MultiCell(150, 50, "Assistant Manager Accounts", 0, 'L', 0, 2, 435, 745, true);
        $rep->MultiCell(550, 650, "______________________________", 0, 'L', 0, 2, 420, 730, true);

        $rep->MultiCell(100, 50, "Manager Finance", 0, 'L', 0, 2, 270, 780, true);
        $rep->MultiCell(550, 650, "____________________________________ ", 0, 'L', 0, 2, 220, 765, true);

    }


    $rep->NewLine();
    $rep->End();
}

?>
