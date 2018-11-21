<?php
$page_security = 'SA_SUPPLIERINVOICE';
$path_to_root = "..";

include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/admin/db/gl_set_db.inc");

// $js = "";
// if ($use_popup_windows)
// 	$js .= get_js_open_window(900, 500);
// if ($use_date_picker)
// 	$js .= get_js_date_picker();
$js = '';

if ($SysPrefs->use_popup_windows) {
    $js .= get_js_open_window(900, 500);
}

if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}


add_js_file('credit_bill.js');

page(_($help_context = "Enter Supplier Invoice"), false, false, "", $js);




//----------------------------------------------------------------------------------------

check_db_has_suppliers(_("There are no suppliers defined in the system."));

//---------------------------------------------------------------------------------------------------------------

if (isset($_GET['AddedID']))
{
    $invoice_no = $_GET['AddedID'];
    $trans_type = ST_SUPPINVOICE;


    echo "<center>";
    display_notification_centered(_("Supplier invoice has been processed."));
    display_note(get_trans_view_str(ST_SUPPCREDIT_IMPORT, $invoice_no, _("View this Invoice")));

    display_note(get_gl_view_str(ST_SUPPCREDIT_IMPORT, $invoice_no, _("View the GL Journal Entries for this Invoice")), 1);

    hyperlink_no_params("$path_to_root/purchasing/supplier_payment.php", _("Entry supplier &payment for this invoice"));

    hyperlink_params($_SERVER['PHP_SELF'], _("Enter Another Invoice"), "New=1");

    hyperlink_no_params("$path_to_root/purchasing/inquiry/grn_search_completed.php?", _("Enter Another Import Invoice"));

    hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$trans_type&trans_no=$invoice_no");

    display_footer_exit();
}

//--------------------------------------------------------------------------------------------------

// if ($_GET['New']&&$_GET['supplier_id']&&$_GET['trans_no']&&$_GET['po_no'])
// {
// 	if (isset( $_SESSION['supp_trans']))
// 	{
// 		unset ($_SESSION['supp_trans']->grn_item_import);
// 		unset ($_SESSION['supp_trans']->gl_codes_import);
// 		unset ($_SESSION['supp_trans']);
// 	}

// 	$_SESSION['supp_trans'] = new supp_trans_import(ST_SUPPCREDIT_IMPORT);//isko change kerian tu us ka ref ly ata hy

// }

if ($_GET['New']) {

    if (isset( $_SESSION['supp_trans'])) {
        unset($_SESSION['supp_trans']->grn_item_import);
        unset($_SESSION['supp_trans']->gl_codes_import);
        unset($_SESSION['supp_trans']);

    }
    $_SESSION['supp_trans'] = new supp_trans_import(ST_SUPPCREDIT_IMPORT);
    //isko change kerian tu us ka ref ly ata hy
}

if ($_GET['supplier_id']) {

    $ImportBatch = $_SESSION['ImportBatch'];

    $_SESSION['supp_trans'] = new supp_trans_import(ST_SUPPCREDIT_IMPORT, $ImportBatch);
    //isko change kerian tu us ka ref ly ata hy

}

//--------------------------------------------------------------------------------------------------
function clear_fields()
{
    global $Ajax;

    unset($_POST['gl_code']);
    unset($_POST['dimension_id']);
    unset($_POST['dimension2_id']);
    unset($_POST['amount']);
    unset($_POST['memo_']);
    unset($_POST['AddGLCodeToTrans']);
    $Ajax->activate('gl_items');
    set_focus('gl_code');
}
//------------------------------------------------------------------------------------------------
//	GL postings are often entered in the same form to two accounts
//  so fileds are cleared only on user demand.
//
if (isset($_POST['ClearFields']))
{
    clear_fields();
}

if (isset($_POST['AddGLCodeToTrans'])){

    $Ajax->activate('gl_items');
    $input_error = false;

    $result = get_gl_account_info($_POST['gl_code']);
    if (db_num_rows($result) == 0)
    {
        display_error(_("The account code entered is not a valid code, this line cannot be added to the transaction."));
        set_focus('gl_code');
        $input_error = true;
    }
    else
    {
        $myrow = db_fetch_row($result);
        $gl_act_name = $myrow[1];
        if (!check_num('amount'))
        {
            display_error(_("The amount entered is not numeric. This line cannot be added to the transaction."));
            set_focus('amount');
            $input_error = true;
        }
    }

    if (!is_tax_gl_unique(get_post('gl_code'))) {
        display_error(_("Cannot post to GL account used by more than one tax type."));
        set_focus('gl_code');
        $input_error = true;
    }

    if ($input_error == false)
    {
        $_SESSION['supp_trans']->add_gl_codes_to_trans_import($_POST['gl_code'], $gl_act_name,
            $_POST['dimension_id'], $_POST['dimension2_id'],
            input_num('amount'), $_POST['memo_']);
        set_focus('gl_code');
    }
}

//------------------------------------------------------------------------------------------------

function check_data()
{
    global $Refs;

    if (!$_SESSION['supp_trans']->is_valid_trans_to_post_import())
    {
        display_error(_("The invoice cannot be processed because the there are no items or values on the invoice.  Invoices are expected to have a charge."));
        return false;
    }

//	if (!$Refs->is_valid($_SESSION['supp_trans']->reference))
//	{
//		display_error(_("You must enter an invoice reference."));
//		set_focus('reference');
//		return false;
//	}

    if (!is_new_reference($_SESSION['supp_trans']->reference, ST_SUPPCREDIT_IMPORT))
    {
        display_error(_("The entered reference is already in use."));
        set_focus('reference');
        return false;
    }

//	if (!$Refs->is_valid($_SESSION['supp_trans']->supp_reference))
//	{
//		display_error(_("You must enter a supplier's invoice reference."));
//		set_focus('supp_reference');
//		return false;
//	}

    if (!is_date( $_SESSION['supp_trans']->tran_date))
    {
        display_error(_("The invoice as entered cannot be processed because the invoice date is in an incorrect format."));
        set_focus('trans_date');
        return false;
    }
    elseif (!is_date_in_fiscalyear($_SESSION['supp_trans']->tran_date))
    {
        display_error(_("The entered date is not in fiscal year."));
        set_focus('trans_date');
        return false;
    }
    if (!is_date( $_SESSION['supp_trans']->due_date))
    {
        display_error(_("The invoice as entered cannot be processed because the due date is in an incorrect format."));
        set_focus('due_date');
        return false;
    }

//	if (is_reference_already_there($_SESSION['supp_trans']->supplier_id, $_POST['supp_reference']))
//	{ 	/*Transaction reference already entered */
//		display_error(_("This invoice number has already been entered. It cannot be entered again.") . " (" . $_POST['supp_reference'] . ")");
//		return false;
//	}


    if ($_POST['gl_code_header']=='')
    {
        display_error( _("Atleast Account must be selected."));
        set_focus('gl_code_header');
        return false;
    }


    $row = get_company_pref('back_days');
    $row1 = get_company_pref('future_days');
    $row2 = get_company_pref('deadline_time');
    if($row != '')
    {
        $diff   =  date_diff2(date('d-m-Y'),$_POST['tran_date'], 'd');

        if($row == 0)

        {
            $allowed_days = 'before yesterday.';
        }

        else
            $allowed_days =  'more than '. $row . ' day old' ;

        if($diff > $row  ){

            display_error("You are not allowed to enter entries $allowed_days");
            return false;
        }

//		else
//		{
//			if($diff < 0 )
//			{
//				display_error("You are not allowed to enter data $row day/s ahead");
//				return false;
//			}

        //}

    }

    if($row1 != '')
    {

        $diff_futuredays   =  date_diff2($_POST['tran_date'],date('d-m-Y'), 'd');

        if( $diff_futuredays > $row1)
        {
            //	display_error($diff_futuredays);
            display_error("You are not allowed to enter data $row1 day/s ahead");

            return false ;

        }

    }
    if($row2 != '')
    {

        $now = date('h:i:s');

        if($row2 != 0)
        {
            $allowed_time = 'after '. $row2;
        }
        else
            $allowed_time=  '' ;

        if($row2 > $now )
        {
            display_error("You are not allowed to enter data $allowed_time pm");
            return false ;
        }

    }

    return true;
}

//--------------------------------------------------------------------------------------------------

function handle_commit_invoice()
{

    copy_to_trans_import($_SESSION['supp_trans']);

    if (!check_data())
        return;

    $invoice_no = add_supp_invoice_import($_SESSION['supp_trans']);

    $_SESSION['supp_trans']->clear_items_import();
    unset($_SESSION['supp_trans']);

    meta_forward($_SERVER['PHP_SELF'], "AddedID=$invoice_no");
}

//--------------------------------------------------------------------------------------------------

if (isset($_POST['PostInvoice']))
{

    handle_commit_invoice();
}

function check_item_data($n)
{
    global $check_price_charged_vs_order_price,
           $check_qty_charged_vs_del_qty, $SysPrefs;
//	if (!check_num('this_quantity_inv'.$n, 0) || input_num('this_quantity_inv'.$n)==0)
//	{
//		display_error( _("The quantity to invoice must be numeric and greater than zero."));
//		set_focus('this_quantity_inv'.$n);
//		return false;
//	}
//	if (!check_num('Other_Expense'.$n, 0) || input_num('Other_Expense'.$n)==0)
//	{
//		display_error( _("The  to invoice must be numeric and greater than zero."));
//		set_focus('this_quantity_inv'.$n);
//		return false;
//	}
    if (!check_num('ChgPrice'.$n))
    {
        display_error( _("The price is not numeric."));
        set_focus('ChgPrice'.$n);
        return false;
    }


    $margin = $SysPrefs->over_charge_allowance();
    if ($check_price_charged_vs_order_price == True)
    {
        if ($_POST['order_price'.$n]!=input_num('ChgPrice'.$n)) {
            if ($_POST['order_price'.$n]==0 ||
                input_num('ChgPrice'.$n)/$_POST['order_price'.$n] >
                (1 + ($margin/ 100)))
            {
                display_error(_("The price being invoiced is more than the purchase order price by more than the allowed over-charge percentage. The system is set up to prohibit this. See the system administrator to modify the set up parameters if necessary.") .
                    _("The over-charge percentage allowance is :") . $margin . "%");
                set_focus('ChgPrice'.$n);
                return false;
            }
        }
    }

    if ($check_qty_charged_vs_del_qty == True)
    {
        if (input_num('this_quantity_inv'.$n) / ($_POST['qty_recd'.$n] - $_POST['prev_quantity_inv'.$n]) >
            (1+ ($margin / 100)))
        {
            display_error( _("The quantity being invoiced is more than the outstanding quantity by more than the allowed over-charge percentage. The system is set up to prohibit this. See the system administrator to modify the set up parameters if necessary.")
                . _("The over-charge percentage allowance is :") . $margin . "%");
            set_focus('this_quantity_inv'.$n);
            return false;
        }
    }

    return true;
}
function get_import_gl_names()
{
    /*Gets the GL Codes relevant to the item account  */
    $sql = "SELECT * FROM `0_sys_pay_pref_new`";

    return db_query($sql,"retreive stock gl code");
    //return db_fetch($get);
}

function commit_item_data($n)
{
    if (check_item_data($n))
    {



        /*** Calculation*/
//		S.H
        $supplier_currency = get_supplier_currency($_SESSION['supp_trans']->supplier_id);
        $Currency_Amount = get_exchange_rate_for_import($supplier_currency);
        //if($Currency_Amount == 0)
        //	$_SESSION['supp_trans']->Currency_Amount = 1;
        //else
        //	$_SESSION['supp_trans']->Currency_Amount = $Currency_Amount;

        $Amt_FC = input_num('this_quantity_inv'.$n)*input_num('ChgPrice'.$n);

        global  $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'OZONE') {

            $Gross_Amt =input_num('Unit_Amt'.$n)*round2($Amt_FC);

            $Landing_Amt = $Gross_Amt*$_POST['Landing'.$n]/100;

            $Value_invl_Landing = $Gross_Amt + $Landing_Amt;

            $INS_Amt = $Gross_Amt*$_POST['INS'.$n]/100;

            $Value_Incl_INC = $Value_invl_Landing + $INS_Amt;

            $F_E_D_Amt = $Value_Incl_INC*$_POST['F_E_D'.$n]/100;


            $Value_income = $F_E_D_Amt+$Value_invl_Landing + $INS_Amt;
            $Duty_Amt = $Value_income*$_POST['Duty'.$n]/100;

            $Value_And_Duty = $Value_Incl_INC + $Duty_Amt+$F_E_D_Amt;



            $S_T_Amt = $Value_And_Duty*$_POST['S_T'.$n]/100;

            $Amount_Incl_S_T = $Value_And_Duty + $S_T_Amt;
            $Add_S_T_Amt = $Value_And_Duty*$_POST['Add_S_T'.$n]/100;
            //	$I_Tax_Amt = ($Amount_Incl_S_T+$Add_S_T_Amt)*$_POST['I_Tax'.$n]/100;
            $I_Tax_Amt = ($Amount_Incl_S_T)*$_POST['I_Tax'.$n]/100;
            $Total_Charges = /*$F_E_D_Amt +*/ $Value_And_Duty + $S_T_Amt + $I_Tax_Amt + $Add_S_T_Amt;
            $Total_ = $Amt_FC * $_SESSION['supp_trans']->Currency_Amount;

            $Net_Amount = $Total_Charges+ input_num('Other_Expense'.$n);
        }



        else{

//		    display_error(input_num('as_per_be'.$n));
//		    die;



            if(input_num('as_per_be'.$n) > 0 )
                $Gross_Amt_as_per_be = input_num('as_per_be'.$n) /* input_num('this_quantity_inv'.$n) *input_num('Unit_Amt'.$n)*/;
            else
                $Gross_Amt_as_per_be	 = input_num('this_quantity_inv'.$n) *input_num('Unit_Amt'.$n)*input_num('Gross_Amt_new'.$n);




            $Gross_Amt    = input_num('this_quantity_inv'.$n) *input_num('ChgPrice'.$n)*input_num('po_exchange_rate'.$n);
            $INS_as_per_be = get_sys_pay_pref_as_per_be('INS_Amt');
            $F_E_D_as_per_be = get_sys_pay_pref_as_per_be('F_E_D_Amt');
            $Duty_as_per_be = get_sys_pay_pref_as_per_be('Duty_Amt');
            $s_Tax_as_per_be = get_sys_pay_pref_as_per_be('S_T_Amt');
            $I_tax_as_per_be = get_sys_pay_pref_as_per_be('I_Tax_Amt');
            $Add_tax_as_per_be = get_sys_pay_pref_as_per_be('Add_S_T_Amt');
            $other_exp_as_per_be = get_sys_pay_pref_as_per_be('Other_Expense');

                $Landing_Amt = $Gross_Amt_as_per_be * $_POST['Landing' . $n] / 100;

            $Value_invl_Landing = $Gross_Amt_as_per_be + $Landing_Amt;

            if($INS_as_per_be) {
                $INS_Amt = $Gross_Amt_as_per_be * $_POST['INS' . $n] / 100;
            }
            else{
                $INS_Amt = $Value_invl_Landing * $_POST['INS' . $n] / 100;
            }
            $Value_Incl_INC = $Value_invl_Landing + $INS_Amt;
            if($F_E_D_as_per_be) {
                $F_E_D_Amt = $Gross_Amt_as_per_be * $_POST['F_E_D' . $n] / 100;
            }
            else{
                $F_E_D_Amt = $Value_Incl_INC * $_POST['F_E_D' . $n] / 100;
            }

            if($Duty_as_per_be) {
                $Duty_Amt = $Gross_Amt_as_per_be * $_POST['Duty' . $n] / 100;
            }
            else{
                $Duty_Amt = $Value_Incl_INC * $_POST['Duty' . $n] / 100;
            }

            $Value_And_Duty = $Value_Incl_INC + $Duty_Amt+$F_E_D_Amt;

            if($s_Tax_as_per_be) {
                $S_T_Amt = $Gross_Amt_as_per_be * $_POST['S_T' . $n] / 100;
            }
            else{
                  
                $S_T_Amt = $Value_And_Duty * $_POST['S_T' . $n] / 100;
            }
            $Amount_Incl_S_T = $Value_And_Duty + $S_T_Amt;



            if($Add_tax_as_per_be) {
                $Add_S_T_Amt = $Gross_Amt_as_per_be * $_POST['Add_S_T' . $n] / 100;
            }
            else{
             
                $Add_S_T_Amt =$Value_And_Duty * $_POST['Add_S_T' . $n] / 100;
            }
            
          
            $Amount_Incl_S_T_AND_ADD_TAX = $Value_And_Duty + $S_T_Amt + $Add_S_T_Amt;
            //	$I_Tax_Amt = ($Amount_Incl_S_T+$Add_S_T_Amt)*$_POST['I_Tax'.$n]/100;
            global $db_connections;
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CASAMIA'){
                if($I_tax_as_per_be) {
                    $I_Tax_Amt = ($Gross_Amt_as_per_be) * $_POST['I_Tax' . $n] / 100;
                }
                else{
                    $I_Tax_Amt = ($Amount_Incl_S_T_AND_ADD_TAX) * $_POST['I_Tax' . $n] / 100;
                }
            }
            else{
                if($I_tax_as_per_be) {
                    $I_Tax_Amt = ($Gross_Amt_as_per_be) * $_POST['I_Tax' . $n] / 100;
                }
                else{
                    $I_Tax_Amt = ($Amount_Incl_S_T) * $_POST['I_Tax' . $n] / 100;
                }
            }



             $landing_gl = get_sys_pay_pref_gl_entry('Landing_Amt');
            $INS_gl = get_sys_pay_pref_gl_entry('INS_Amt');
            $F_E_D_gl = get_sys_pay_pref_gl_entry('F_E_D_Amt');
            $Duty_gl = get_sys_pay_pref_gl_entry('Duty_Amt');
            $S_T_gl = get_sys_pay_pref_gl_entry('S_T_Amt');
            $I_T_gl = get_sys_pay_pref_gl_entry('I_Tax_Amt');
            $Add_S_T_gl = get_sys_pay_pref_gl_entry('Add_S_T_Amt');
            $Other_exp_gl = get_sys_pay_pref_gl_entry('Other_Expense');


            if($landing_gl == 0)
                $Landing_Amts_gl = $Landing_Amt;
            else
                $Landing_Amts_gl = 0;

            if($INS_gl == 0)
                $INS_Amts_gl = $INS_Amt;
            else
                $INS_Amts_gl = 0;


            if($F_E_D_gl == 0)
                $F_E_D_Amts_gl = $F_E_D_Amt;
            else
                $F_E_D_Amts_gl = 0;

            if($Duty_gl == 0)
                $Duty_Amts_gl = $Duty_Amt;
            else
                $Duty_Amts_gl = 0;

            if($S_T_gl == 0)
                $I_Tax_Amts_gl = $I_Tax_Amt;
            else
                $I_Tax_Amts_gl = 0;

            if($I_T_gl== 0)
                $Add_S_T_Amts_gl = $Add_S_T_Amt;
            else
                $Add_S_T_Amts_gl = 0;


            if($Add_S_T_gl == 0)
                $S_T_Amts_gl = $S_T_Amt;
            else
                $S_T_Amts_gl = 0;

            if($Other_exp_gl == 0){
                $Other_Expensess_gl =  $_POST['this_quantity_inv'.$n];
            }
            else {
                $Other_Expensess_gl = 0;
            }



        

            $Total_ = $Amt_FC * $_SESSION['supp_trans']->Currency_Amount;
            $Total_Charges = $S_T_Amt + $Add_S_T_Amt  + $Duty_Amt + $F_E_D_Amt + $INS_Amt + $Landing_Amt;


            $Total_Charges_gl  = $Landing_Amts_gl + $INS_Amts_gl + $F_E_D_Amts_gl + $Duty_Amts_gl + $I_Tax_Amts_gl
                + $Add_S_T_Amts_gl + $S_T_Amts_gl + $Other_Expensess_gl ;
//                display_error($Landing_Amts_gl."landing".$INS_Amts_gl."landing".$F_E_D_Amts_gl."landing".$Duty_Amts_gl."landing"
//                .$I_Tax_Amts_gl."landing".$Add_S_T_Amts_gl."landing".$S_T_Amts_gl."landing".$Other_Expensess_gl."landing");


            $Net_Amount = $Total_Charges_gl ;
        }








        $get_import_gl_code = get_import_gl_names();
        $i = 0;
        $data = array();
        while ($myrow = db_fetch($get_import_gl_code)) {
            $data[$i]=$myrow[2];
            $i++;
        }
        $landing_amount = get_import_unit_cost ($data[0]);
        $ins_amount = get_import_unit_cost ($data[1]);
        $fed_amount = get_import_unit_cost ($data[2]);
        $duty_amount = get_import_unit_cost ($data[3]);
        $s_t_amount = get_import_unit_cost ($data[4]);
        $i_tax_amount = get_import_unit_cost ($data[5]);
        $add_st_amount = get_import_unit_cost ($data[6]);
        $ohter_expense = get_import_unit_cost ($data[7]);

        if($landing_amount == 0)
            $Landing_Amts = $Landing_Amt;
        else
            $Landing_Amts = 0;

        if($ins_amount == 0)
            $INS_Amts = $INS_Amt;
        else
            $INS_Amts = 0;


        if($fed_amount == 0)
            $F_E_D_Amts = $F_E_D_Amt;
        else
            $F_E_D_Amts = 0;

        if($duty_amount == 0)
            $Duty_Amts = $Duty_Amt;
        else
            $Duty_Amts = 0;
        if($i_tax_amount == 0)
            $I_Tax_Amts = $I_Tax_Amt;
        else
            $I_Tax_Amts = 0;

        if($add_st_amount== 0)
            $Add_S_T_Amts = $Add_S_T_Amt;
        else
            $Add_S_T_Amts = 0;


        if($s_t_amount == 0)
            $S_T_Amts = $S_T_Amt;
        else
            $S_T_Amts = 0;

        if($ohter_expense == 0){
            $Other_Expensess = input_num('Other_Expense'.$n);
            }
        else {
            $Other_Expensess = 0;
        }
        global  $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] != 'OZONE') {
            $tot_import_expenses = $S_T_Amts + $Add_S_T_Amts + $I_Tax_Amts + $Duty_Amts + $F_E_D_Amts + $INS_Amts + $Landing_Amts + $Other_Expensess;
        }
        $_SESSION["supp_trans"]->sum = ($Gross_Amt_as_per_be+$Landing_Amts+$INS_Amts+$F_E_D_Amts+$Duty_Amts+$S_T_Amts+$I_Tax_Amts	+$Add_S_T_Amts+$Other_Expensess);

        global $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CASAMIA') {
            // display_error( $tot_import_expenses ."+". $Gross_Amt ."/". $_SESSION['supp_trans']->this_quantity_inv."//".$_POST['this_quantity_inv'.$n]);
            $Unit_Cost = $tot_import_expenses + $Gross_Amt / $_POST['this_quantity_inv'.$n];
        }
        else{
// display_error( $tot_import_expenses ."+". $Gross_Amt ."/". $_SESSION['supp_trans']->this_quantity_inv."/////");
            $Unit_Cost = $tot_import_expenses + $Gross_Amt / $_SESSION['supp_trans']->this_quantity_inv;
        }


        $con_factor = $Unit_Cost/input_num('ChgPrice'.$n);


        $_SESSION['supp_trans']->add_grn_to_trans_import($n, $_POST['po_detail_item'.$n],
            $_POST['item_code'.$n], $_POST['item_description'.$n], $_POST['qty_recd'.$n],
            $_POST['prev_quantity_inv'.$n], input_num('this_quantity_inv'.$n),
            $_POST['order_price'.$n], input_num('ChgPrice'.$n), $_POST['std_cost_unit'.$n], "",
            input_num('Unit_Amt'.$n),
            /*$_POST['Gross_Amt'.$n]*/input_num('Gross_Amt_new'.$n),
            /*$_POST['As_Per_B_E'.$n]*/$Gross_Amt,$Gross_Amt_as_per_be,
            $_POST['Landing'.$n],
            /*$_POST['Landing_Amt'.$n]*/$Landing_Amt,
            /*$_POST['Value_invl_Landing'.$n]*/$Value_invl_Landing,
            $_POST['INS'.$n],
            /*$_POST['INS_Amt'.$n]*/$INS_Amt,
            /*$_POST['Value_Incl_INC'.$n]*/$Value_Incl_INC,
            $_POST['F_E_D'.$n],
            /*$_POST['F_E_D_Amt'.$n]*/$F_E_D_Amt,
            $_POST['Duty'.$n],
            /*$_POST['Duty_Amt'.$n]*/$Duty_Amt,
            /*$_POST['Value_And_Duty'.$n]*/$Value_And_Duty,
            /*$_POST['Value_Excl_S_T'.$n]*/$Value_And_Duty,	//Value_Excl_S_T
            $_POST['S_T'.$n],
            /*$_POST['S_T_Amt'.$n]*/$S_T_Amt,
            /*$_POST['Amount_Incl_S_T'.$n]*/$Amount_Incl_S_T,
            $_POST['I_Tax'.$n],
            /*$_POST['I_Tax_Amt'.$n]*/$I_Tax_Amt,
            $_POST['Add_S_T'.$n],
            /*$_POST['Add_S_T_Amt'.$n]*/$Add_S_T_Amt,
            /*$_POST['Total_Charges'.$n]*/$Total_Charges,
            input_num('Other_Expense'.$n),
            /*$_POST['Net_Amount'.$n]*/$Net_Amount,
            $_POST['Job_Name'.$n],
            $_POST['Gross_Amt_new'.$n],
            $con_factor,input_num('po_exchange_rate'.$n),$tot_import_expenses);
    }
}

//-----------------------------------------------------------------------------------------

$id = find_submit('grn_item_id');
if ($id != -1)
{
    commit_item_data($id);
}

if (isset($_POST['InvGRNAll']))
{
    foreach($_POST as $postkey=>$postval )
    {
        if (strpos($postkey, "qty_recd") === 0)
        {
            $id = substr($postkey, strlen("qty_recd"));
            $id = (int)$id;
            commit_item_data($id);
        }
    }
}

//--------------------------------------------------------------------------------------------------
$id3 = find_submit('Delete');
if ($id3 != -1)
{
    $_SESSION['supp_trans']->remove_grn_from_trans_import($id3);
    $Ajax->activate('grn_items');
    $Ajax->activate('inv_tot');
}

$id4 = find_submit('Delete2');
if ($id4 != -1)
{
    $_SESSION['supp_trans']->remove_gl_codes_from_trans_import($id4);
    clear_fields();
    $Ajax->activate('gl_items');
    $Ajax->activate('inv_tot');
}

$id2 = -1;
if ($_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))
{
    $id2 = find_submit('void_item_id');
    if ($id2 != -1)
    {
        remove_not_invoice_item($id2);
        display_notification(sprintf(_('All yet non-invoiced items on delivery line # %d has been removed.'), $id2));

    }
}

if (isset($_POST['go']))
{
    $Ajax->activate('gl_items');
    display_quick_entries($_SESSION['supp_trans'], $_POST['qid'], input_num('totamount'), QE_SUPPINV);
    $_POST['totamount'] = price_format(0); $Ajax->activate('totamount');
    $Ajax->activate('inv_tot');
}

start_form();

invoice_header_import($_SESSION['supp_trans']);

if ($_POST['supplier_id']=='')
    display_error(_("There is no supplier selected."));
else {
    display_grn_items_import($_SESSION['supp_trans'], 1);

    display_gl_items_import($_SESSION['supp_trans'], 1);

    div_start('inv_tot');
    invoice_totals_import($_SESSION['supp_trans']);

    div_end();

}

//-----------------------------------------------------------------------------------------

if ($id != -1 || $id2 != -1)
{
    $Ajax->activate('grn_items');
    $Ajax->activate('inv_tot');
}

if (get_post('AddGLCodeToTrans'))
    $Ajax->activate('inv_tot');

br();
submit_center('PostInvoice', _("Enter Invoice"), true, '', 'default');
br();

end_form();

//--------------------------------------------------------------------------------------------------

end_page();
?>