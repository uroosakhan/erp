<?php
//--------------------------------------------------------------------------------------------------

function copy_from_trans_import(&$supp_trans)
{
    $_POST['Comments'] = $supp_trans->Comments;
    $_POST['tran_date'] = $supp_trans->tran_date;
    $_POST['due_date'] = $supp_trans->due_date;
    $_POST['supp_reference'] = $supp_trans->supp_reference;
    $_POST['reference'] = $supp_trans->reference;
    $_POST['supplier_id'] = $supp_trans->supplier_id;
    $_POST['gross_rate'] = $supp_trans->gross_rate;
    $_POST['gl_code_header'] = $supp_trans->gl_code_header;

}

////--------------------------------------------------------------------------------------------------

function copy_to_trans_import(&$supp_trans)
{
    $supp_trans->Comments = $_POST['Comments'];
    $supp_trans->tran_date = $_POST['tran_date'];
    $supp_trans->due_date = $_POST['due_date'];
    $supp_trans->supp_reference = $_POST['supp_reference'];
    $supp_trans->reference = $_POST['reference'];
    $supp_trans->gross_rate = $_POST['gross_rate'];
    $supp_trans->gl_code_header = $_POST['gl_code_header'];
    $supp_trans->ov_amount = $supp_trans->ov_discount = 0; /* for starters */
    if (count($supp_trans->grn_items) > 0)
    {
        foreach ( $supp_trans->grn_items as $grn)
        {
            $supp_trans->ov_amount += round2(($grn->this_quantity_inv * $grn->chg_price),
                user_price_dec());
        }
    }
    if (count($supp_trans->gl_codes) > 0)
    {
        foreach ( $supp_trans->gl_codes as $gl_line)
        {
            ////////// 2009-08-18 Joe Hunt
            if (!is_tax_account($gl_line->gl_code) || $supp_trans->tax_included)
                $supp_trans->ov_amount += $gl_line->amount;
        }
    }
}

////--------------------------------------------------------------------------------------------------
//

function get_sys_pay_pref_field_import ($name)
{
    $sql = "SELECT default_value FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function invoice_header_import(&$supp_trans)
{
    global $Ajax, $Refs;

    // if vars have been lost, recopy
    if (!isset($_POST['tran_date']))
        copy_from_trans_import($supp_trans);

    start_outer_table(TABLESTYLE2, "width=95%");

    table_section(1);

    if (isset($_POST['invoice_no']))
    {
        $trans = get_supp_trans_import($_POST['invoice_no'], ST_SUPPCREDIT_IMPORT);
        $_POST['supplier_id'] = $trans['supplier_id'];
        $supp = $trans['supplier_name'] . " - " . $trans['SupplierCurrCode'];
        label_row(_("Supplier:"), $supp.hidden('supplier_id', $_POST['supplier_id'], false));
    }
    else
    {

        if(isset($_GET['supplier_id'])){

            $supp_trans->supplier_id = $_GET['supplier_id'];
            $_POST['supplier_id'] = $_GET['supplier_id'];

        }



        supplier_list_row(_("Supplier:"), 'supplier_id', $_POST['supplier_id'], false, true);
    }




    if ($supp_trans->supplier_id != $_POST['supplier_id'])
    {
        // supplier has changed
        // delete all the order items - drastic but necessary because of
        // change of currency, etc
        $supp_trans->clear_items_import();
        read_supplier_details_to_trans_import($supp_trans, $_POST['supplier_id']);
        copy_from_trans_import($supp_trans);
    }


    ref_row(_("Reference:"), 'reference', '', $Refs->get_next(ST_SUPPCREDIT_IMPORT
        , null, array('date'=> date('M-Y'))));


    if (isset($_POST['invoice_no']))
    {
        label_row(_("Supplier's Ref.:"), $_POST['invoice_no'].hidden('invoice_no', $_POST['invoice_no'], false).hidden('supp_reference', $_POST['invoice_no'], false));
    }
    else
        text_row(_("Supplier's Ref.:"), 'supp_reference', $_POST['supp_reference'], 20, 60);


    amount_cells_ex("Gross Expenses :", 'gross_rate');
    yesno_nkr_list_cells(null, 'alloc', null, "", "", true);
// check_cells(_("Allocation On Quantity:"), 'alloc_qty', null, true);
// 	check_cells(_("Allocation On Amount:"), 'alloc_amt', null, true);
    submit_cells('Load_Exp', _("Allocate Expenses"), "colspan=2",
        _('Calculate Other Expense in Cart'), true);


    echo "<tr><td>Import Control Account:</td>".gl_all_accounts_list('gl_code_header', $_POST['gl_code_header'], false, true,_("All Accounts"),true);
    $bfw = get_gl_balance_from_to('','', $_POST["gl_code_header"]);

    $url = "<a href='$path_to_root/gl/inquiry/gl_account_inquiry.php?&account=" . $_POST['gl_code_header'] . "&popup=1' onclick=\"javascript:openWindow(this.href,this.target); return false;\"'>" . number_format2($bfw) . "</a>";
    label_cell($url);

    if(isset($_POST['Load_Exp']) && $_POST['alloc']==0)
    {

        $Ajax->activate('grn_items');
        $Ajax->activate('inv_tot');
        $Ajax->activate('Calculation');

        div_start('Calculation');
        $total_Gross_Amts=0;
        foreach ($supp_trans->grn_items as $line => $entered_grn) {
            //	$total_Gross_Amts += $entered_grn->total_Gross_Amt;

            $total_Gross_Amts += $entered_grn->Gross_Amt;

            if ($_POST['gross_rate'] == 0) {


                $supp_trans->grn_items[$line]->Other_Expense = 1 * $entered_grn->Gross_Amt / $_SESSION['total_amt'];
            }
            else {


                // $supp_trans->grn_items[$line]->Total_Other_Expenses = $_POST['gross_rate']*$entered_grn->Gross_Amt/$total_Gross_Amts;

                $supp_trans->grn_items[$line]->Total_Other_Expense = input_num('gross_rate') * $entered_grn->Gross_Amt / $_SESSION['total_amt'];


            }


        }

        $_SESSION['total_amt']=$total_Gross_Amts;

        div_end();
    }


    if(isset($_POST['Load_Exp']) && $_POST['alloc']==1)
    {

        $Ajax->activate('grn_items');
        $Ajax->activate('inv_tot');
        $Ajax->activate('Calculation');

        div_start('Calculation');


        $totalquantity_inv=0;
        foreach ($supp_trans->grn_items as $line => $entered_grn) {
            //	$total_Gross_Amts = $entered_grn->total_Gross_Amt;


            $totalquantity_inv += ($entered_grn->this_quantity_inv);
            if ($_POST['gross_rate'] == 0) {


                $supp_trans->grn_items[$line]->Other_Expense = 1 * $entered_grn->this_quantity_inv / $_SESSION['total_qty'];
            }
            else {




                /*$supp_trans->grn_items[$line]->Total_Other_Expenses = $_POST['gross_rate']*$entered_grn->this_quantity_inv/$totalquantity_inv*/;

                $supp_trans->grn_items[$line]->Total_Other_Expense = input_num('gross_rate') * $entered_grn->this_quantity_inv / $_SESSION['total_qty'];

            }
        }
        $_SESSION['total_qty']=$totalquantity_inv;
        div_end();
    }






    table_section(2, "33%");

    date_row(_("Date") . ":", 'tran_date', '', true, 0, 0, 0, "", true);
    if (isset($_POST['_tran_date_changed'])) {
        $Ajax->activate('_ex_rate');
        $supp_trans->tran_date = $_POST['tran_date'];
        get_duedate_from_terms_import($supp_trans);
        $_POST['due_date'] = $supp_trans->due_date;
        $Ajax->activate('due_date');
    }

    date_row(_("Due Date") . ":", 'due_date');

    label_row(_("Terms:"), $supp_trans->terms['description']);


    table_section(3, "33%");

    //set_global_supplier($_POST['supplier_id']);

    $supplier_currency = get_supplier_currency($supp_trans->supplier_id);

    $company_currency = get_company_currency();

    if ($supplier_currency != $company_currency)
    {
        label_row(_("Supplier's Currency:"), "<b>" . $supplier_currency . "</b>");
        exchange_rate_display($supplier_currency, $company_currency, $_POST['tran_date']);
    }


    label_row(_("Tax Group:"), $supp_trans->tax_description);
    supplier_credit_row($supp_trans->supplier_id, $supp_trans->credit);

    end_outer_table(1);
}

////--------------------------------------------------------------------------------------------------
//
function invoice_totals_import(&$supp_trans)
{
    copy_to_trans_import($supp_trans);

    $dim = get_company_pref('use_dimension');
    $colspan = ($dim == 2 ? 7 : ($dim == 1 ? 6 : 5));
    start_table(TABLESTYLE, "width=95%");
    /*	label_row(_("Sub-total:"), price_format( $supp_trans->ov_amount), "colspan=$colspan align=right", "align=right");*/

    $taxes = $supp_trans->get_taxes_import($supp_trans->tax_group_id);
    $tax_total = display_edit_tax_items($taxes, $colspan, $supp_trans->tax_included);

    $display_total = price_format($supp_trans->ov_amount + $tax_total);

    if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
    {/*	label_row(_("Invoice Total:"),
			$display_total, "colspan=$colspan align=right style='font-weight:bold;'", "align=right style='font-weight:bold;'");
			*/}
    else
        label_row(_("Credit Note Total"),
            $display_total, "colspan=$colspan align=right style='font-weight:bold;color:red;'", "nowrap align=right style='font-weight:bold;color:red;'");

    end_table(1);
    start_table(TABLESTYLE2);
    textarea_row(_("Memo:"), "Comments", null, 50, 3);
    end_table(1);
}

////--------------------------------------------------------------------------------------------------
function display_gl_controls_import(&$supp_trans, $k)
{
    $accs = get_supplier_accounts($supp_trans->supplier_id);
    $_POST['gl_code'] = $accs['purchase_account'] ?
        $accs['purchase_account'] : get_company_pref('default_cogs_act');

    alt_table_row_color($k);
    echo gl_all_accounts_list('gl_code', null, true, true);
    $dim = get_company_pref('use_dimension');
    if ($dim >= 1)
        dimensions_list_cells(null, 'dimension_id', null, true, " ", false, 1);
    if ($dim > 1)
        dimensions_list_cells(null, 'dimension2_id', null, true, " ", false, 2);
    amount_cells(null, 'amount');
    if ($dim < 1)
        text_cells_ex(null, 'memo_', 35, 50, null, null, null, hidden('dimension_id', 0, false).hidden('dimension2_id', 0, false));
    else if ($dim < 2)
        text_cells_ex(null, 'memo_', 35, 50, null, null, null, hidden('dimension2_id', 0, false));
    else
        text_cells_ex(null, 'memo_', 35, 50, null, null, null);
    submit_cells('AddGLCodeToTrans', _("Add"), "",
        _('Add GL Line'), true);
    submit_cells('ClearFields', _("Reset"), "",
        _("Clear all GL entry fields"), true);
    end_row();
}

function display_gl_items_import(&$supp_trans, $mode=0)
{
    global $path_to_root, $Ajax;

    // if displaying in form, and no items, exit
    if (($mode == 2 || $mode == 3) && count($supp_trans->gl_codes) == 0)
        return 0;

    if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
        $heading = _("GL Items for this Invoice");
    else
        $heading = _("GL Items for this Credit Note");

    start_outer_table(TABLESTYLE, "width=95%");

    if ($mode == 1)
    {
        $qes = has_quick_entries(QE_SUPPINV);
        if ($qes !== false)
        {
            echo "<div style='float:right;'>";
            echo _("Quick Entry:")."&nbsp;";
            echo quick_entries_list('qid', null, QE_SUPPINV, true);
            $qid = get_quick_entry(get_post('qid'));
            if (list_updated('qid')) {
                unset($_POST['totamount']); // enable default
                $Ajax->activate('totamount');
            }
            echo "&nbsp;".$qid['base_desc'].":"."&nbsp;";

            $amount = input_num('totamount', $qid['base_amount']);
            $dec = user_price_dec();
            echo "<input class='amount' type='text' name='totamount' size='7' maxlength='12' dec='$dec' value='$amount'>&nbsp;";
            submit('go', _("Go"), true, false, true);
            echo "</div>";

        }
    }
    display_heading($heading);

    end_outer_table(0, false);

    div_start('gl_items');
    start_table(TABLESTYLE, "width=95%");

    $dim = get_company_pref('use_dimension');
    if ($dim == 2)
        $th = array(_("Account"), _("Name"), _("Dimension")." 1", _("Dimension")." 2", _("Amount"), _("Memo"));
    else if ($dim == 1)
        $th = array(_("Account"), _("Name"), _("Dimension"), _("Amount"), _("Memo"));
    else
        $th = array(_("Account"), _("Name"), _("Amount"), _("Memo"));

    if ($mode == 1)
    {
        $th[] = "";
        $th[] = "";
    }
    table_header($th);
    $total_gl_value=$total = 0;
    $i = $k = 0;

    if (count($supp_trans->gl_codes) > 0)
    {

        foreach ($supp_trans->gl_codes as $entered_gl_code)
        {

            alt_table_row_color($k);

            if ($mode == 3)
                $entered_gl_code->amount = -$entered_gl_code->amount;

            label_cell($entered_gl_code->gl_code);
            label_cell($entered_gl_code->gl_act_name);

            if ($dim >= 1)
                label_cell(get_dimension_string($entered_gl_code->gl_dim, true));
            if ($dim > 1)
                label_cell(get_dimension_string($entered_gl_code->gl_dim2, true));

            amount_cell($entered_gl_code->amount, true);
            label_cell($entered_gl_code->memo_);

            if ($mode == 1)
            {
                delete_button_cell("Delete2" . $entered_gl_code->Counter, _("Delete"),
                    _('Remove line from document'));
                label_cell("");
            }
            end_row();
            /////////// 2009-08-18 Joe Hunt
            if ($mode > 1) {
                if ($supp_trans->tax_included || !is_tax_account($entered_gl_code->gl_code))
                    $total_gl_value += $entered_gl_code->amount;
            }
            else
                $total_gl_value += $entered_gl_code->amount;
            $total += $entered_gl_code->amount;
            $i++;
            if ($i > 15)
            {
                $i = 0;
                table_header($th);
            }
        }

    }
    if ($mode == 1)
        display_gl_controls_import($supp_trans, $k);
    $colspan = ($dim == 2 ? 4 : ($dim == 1 ? 3 : 2));
    label_row(_("Total"), price_format($total),
        "colspan=".$colspan." align=right", "nowrap align=right", ($mode==1?3:1));

    end_table(1);
    div_end();

    return $total_gl_value;
}
//
////--------------//-----------------------------------------------------------------------------------------
//



function display_grn_items_for_selection_import(&$supp_trans, $k)
{

//	$supplier_currency = get_supplier_currency($supp_trans->supplier_id);
//	$supp_trans->Currency_Amount = get_exchange_rate_for_import($supplier_currency);

    $supplier_currency = get_supplier_currency($supp_trans->supplier_id);
    $Currency_Amount = get_exchange_rate_for_import($supplier_currency);

    if($Currency_Amount == 0)
        $supp_trans->Currency_Amount = 1;
    else
        $supp_trans->Currency_Amount = $Currency_Amount;



    if(isset($_GET['supplier_id'])){

        $supp_trans->supplier_id = $_GET['supplier_id'];

    }



    if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
        $result = get_grn_import_items($supp_trans->ImportBatch, $supp_trans->supplier_id,
            true,false,$supp_trans->trans_no,"","",
            1);

    if (db_num_rows($result) == 0)
    {
        return false;
    }


    /*Set up a table to show the outstanding GRN items for selection */
    $serial_no = 0;
    $i = 0;
    while ($myrow = db_fetch($result))
    {
        $serial_no ++;
        $i ++;
//        display_error($serial_no);

        $grn_already_on_invoice = false;

        foreach ($supp_trans->grn_items as $entered_grn)
        {
            if ($entered_grn->id == $myrow["id"])
            {
                $grn_already_on_invoice = true;
            }
        }
        if ($grn_already_on_invoice == false)
        {

//            alt_table_row_color($i);



            if($serial_no % 2 == 0){
                $color = '#cecccc';
            }
            else{
                $color = '#efefef';
            }

            start_row("class='oddrow' style='background-color:$color; font-size:13px;color:#454545;' ");
            $n = $myrow["id"];
            label_cell($serial_no,'rowspan="2" style="line-height:80px;"');
            label_cell(get_trans_view_str(25, $myrow["grn_batch_id"]).
                hidden('qty_recd'.$n, $myrow["qty_recd"], false).
                hidden('item_code'.$n, $myrow["item_code"], false).
                hidden('item_description'.$n, $myrow["description"], false).
                hidden('prev_quantity_inv'.$n, $myrow['quantity_inv'], false).
                hidden('order_price'.$n, (1 - $myrow["discount_percent"]) * $myrow['unit_price'], false).
                hidden('std_cost_unit'.$n, $myrow['std_cost_unit'], false).
                hidden('po_detail_item'.$n, $myrow['po_detail_item'], false).
                hidden('location'.$n, $myrow["loc_code"], false));

            label_cell($myrow["item_code"]);
            label_cell(sql2date($myrow["delivery_date"]));
            if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
            {

                amount_cells(null, 'ChgPrice'.$n, price_decimal_format( (1 - $myrow["discount_percent"]) * $myrow["unit_price"], $dec2), null, null, $dec2);
                $Amount_in_FC = (round2( (1 - $myrow["discount_percent"]) * $myrow["unit_price"] * ($myrow["qty_recd"] - $myrow["quantity_inv"])));
            }
            else
            {
                amount_cells(null, 'ChgPrice'.$n, price_decimal_format($myrow["act_price"], $dec2), null, null, $dec2);
            }

            amount_ex_rate_cells(null, 'po_exchange_rate'.$n,price_exrate_format($supp_trans->Currency_Amount)); // po_exchange_rate
            amount_cells(null,'Gross_Amt_new'.$n,price_decimal_format( (1 - $myrow["discount_percent"]) * $myrow["unit_price"], $dec2)); // Gross_Amt



            amount_ex_rate_cells(null, 'as_per_be'.$n,price_decimal_format(0,$dec2)); // as_per_be

            $landing=get_sys_pay_pref_field_import('Landing_Amt');
            amount_cells_ex(null, 'Landing'.$n,1,7,$landing); // Landing
            label_cell("");
//            amount_cell(123);

            $ins_amount  =get_sys_pay_pref_field_import('INS_Amt');
            amount_cells_ex(null, 'INS'.$n,1,7,$ins_amount); // INS
            label_cell("");
            $fed_amount  =get_sys_pay_pref_field_import('F_E_D_Amt');
            amount_cells_ex(null, 'F_E_D'.$n,1,7,$fed_amount); // F.E.D 1%
            $duty_amount  =get_sys_pay_pref_field_import('Duty_Amt');
            amount_cells_ex(null, 'Duty'.$n,1,7,$duty_amount); // Duty
            amount_cell('Value_And_Duty'.$n, true); // Value_And_Duty


            $st_amount  =get_sys_pay_pref_field_import('S_T_Amt');
            amount_cells_ex(null, 'S_T'.$n,1,7,$st_amount); // S_T

            amount_cell('Amount_Incl_S_T'.$n, true); // Amount_Incl_S_T
            ///
            ///
            $itax_amount  =get_sys_pay_pref_field_import('I_Tax_Amt');

            amount_cells_ex(null, 'I_Tax'.$n,1,7,$itax_amount); // I/Tax
            $addsttax_amount  =get_sys_pay_pref_field_import('Add_S_T_Amt');
            amount_cells_ex(null, 'Add_S_T'.$n,1,7,$addsttax_amount); // Add_S_T
            amount_cell('Total_Charges'.$n, true); // Total_Charges
            amount_cell('Net_Amount'.$n); // Net_Amount
            $gross_amount =$supp_trans->Currency_Amount*$Amount_in_FC;
            $landing_amount = get_import_unit_cost ('Landing_Amt');
            $ins_amount = get_import_unit_cost ('INS_Amt');
            $fed_amount = get_import_unit_cost ('F_E_D_Amt');
            $duty_amount = get_import_unit_cost ('Duty_Amt');
            $s_t_amount = get_import_unit_cost ('I_Tax_Amt');
            $i_tax_amount = get_import_unit_cost ('Add_S_T_Amt');
            $add_st_amount = get_import_unit_cost ('S_T_Amt');
            $ohter_expense = get_import_unit_cost ('Other_Expenses');

            if($landing_amount == 0)
                $Landing_Amt = $_POST['Landing_Amt'];
            else
                $Landing_Amt = 0;

            if($ins_amount == 0)
                $INS_Amt = $_POST['INS_Amt'];
            else
                $INS_Amt = 0;


            if($fed_amount == 0)
                $F_E_D_Amt = $_POST['F_E_D_Amt'];
            else
                $F_E_D_Amt = 0;

            if($duty_amount == 0)
                $Duty_Amt = $_POST['Duty_Amt'];
            else
                $Duty_Amt = 0;
            if($i_tax_amount == 0)
                $I_Tax_Amt = $_POST['I_Tax_Amt'];
            else
                $I_Tax_Amt = 0;

            if($add_st_amount== 0)
                $Add_S_T_Amt = $_POST['Add_S_T_Amt'];
            else
                $Add_S_T_Amt = 0;


            if($s_t_amount == 0)
                $S_T_Amt = $_POST['S_T_Amt'];
            else
                $S_T_Amt = 0;

            if($ohter_expense == 0)
                $Other_Expenses = $_POST['Other_Expenses'];
            else
                $Other_Expenses = 0;

            if($expense == 0)
                $Total_Other_Expenses = $_POST['Total_Other_Expenses'];
            else
                $Total_Other_Expenses = 0;




            $sum_import=$gross_amount+$Landing_Amt+$INS_Amt+$F_E_D_Amt+$Duty_Amt+$S_T_Amt+$I_Tax_Amt
                +$Total_Other_Expenses+$Add_S_T_Amt+$Other_Expenses+$expense;




            amount_cell();

            amount_cell($sum_import); // Standard Cost
            amount_cell($myrow['material_cost']/$myrow["act_price"]); // Standard Cost
            label_cell(_(""));
            label_cell(_(""));

            end_row();
            start_row("class='oddrow' style='background-color:$color;  font-size:13px;color:454545;' ");

            label_cell(get_trans_view_str(ST_PURCHORDER, $myrow["purch_order_no"]));
            label_cell($myrow["description"]);
            if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
                amount_cells_ex(null, 'this_quantity_inv'.$n,2,20, number_format2($myrow["qty_recd"] - $myrow["quantity_inv"], $dec), null, null);
            else
                amount_cells_ex(null, 'This_QuantityCredited'.$n,2,20, number_format2(max($myrow["quantity_inv"], 0), $dec), null, null);
            if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
            {

//                amount_cells(null, 'ChgPrice'.$n, price_decimal_format( (1 - $myrow["discount_percent"]) * $myrow["unit_price"], $dec2), null, null, $dec2);
                $Amount_in_FC = (round2( (1 - $myrow["discount_percent"]) * $myrow["unit_price"] * ($myrow["qty_recd"] - $myrow["quantity_inv"])));
                //amount_cell(round2($Amount_in_FC * $supp_trans->Currency_Amount), user_price_dec());
                amount_cell(round2($Amount_in_FC ), user_price_dec());
            }
            else
            {
//                amount_cells(null, 'ChgPrice'.$n, price_decimal_format($myrow["act_price"], $dec2), null, null, $dec2);
                amount_cell(round2($myrow["act_price"] * max($myrow['quantity_inv'], 0), user_price_dec()));
            }
            amount_ex_rate_cells(null, 'Unit_Amt'.$n,price_exrate_format($supp_trans->Currency_Amount)); // Unit_Amt
//            amount_cells(null,'Gross_Amt_new'.$n,price_decimal_format( (1 - $myrow["discount_percent"]) * $myrow["unit_price"], $dec2)); // Gross_Amt
            label_cell(null);

            amount_cell(null);

            amount_cell('Landing_Amt'.$n, true); // Landing_Amt
            amount_cell('Value_invl_Landing'.$n);
            amount_cell('INS_Amt'.$n); // INS_Amt
            amount_cell('Value_Incl_INC'.$n); // Value_Incl_INC
            amount_cell('F_E_D_Amt'.$n); // F.E.D Value
            amount_cell('Duty_Amt'.$n); // Duty_Amt
            amount_cell('Value_Excl_S_T'.$n); // Value_Excl_S_T
            amount_cell('S_T_Amt'.$n); // S_T_Amt
            amount_cell('Amount_Incl_S_T'.$n); // Amount_Incl_S_T



            $itax_amount  =get_sys_pay_pref_field_import('I_Tax_Amt');
            amount_cell('I_Tax_Amt'.$n); // I_Tax_Amt
            $other_expenses  =get_sys_pay_pref_field_import('Other_Expense');

            amount_cell(null, 'Add_S_T_Amt'.$n); // Add_S_T
            amount_cells(null, 'Other_Expense'.$n,$other_expenses); // Other Expense
            dimensions_list_cells(_(""), 'Job_Name'.$n, null, true);

            $myrow_new = get_item($myrow["item_code"]);
            amount_cell(null);

            amount_cell($myrow_new['material_cost']); // Standard Cost
            label_cell(' ');
//            label_cell(get_trans_view_str(ST_PURCHORDER, $myrow["purch_order_no"]));
//            label_cell($myrow["description"]);
//
//            $dec = get_qty_dec($myrow["item_code"]);
////			amount_cells_ex(null,null,2,20,$myrow["qty_recd"], null);
////			amount_cells_ex(null,null,2,20,$myrow["quantity_inv"], null);
//            if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
//                amount_cells_ex(null, 'this_quantity_inv'.$n,2,20, number_format2($myrow["qty_recd"] - $myrow["quantity_inv"], $dec), null, null);
//            else
//                amount_cells_ex(null, 'This_QuantityCredited'.$n,2,20, number_format2(max($myrow["quantity_inv"], 0), $dec), null, null);
//            $dec2 = 0;
//            if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
//            {
//
////                amount_cells(null, 'ChgPrice'.$n, price_decimal_format( (1 - $myrow["discount_percent"]) * $myrow["unit_price"], $dec2), null, null, $dec2);
//                $Amount_in_FC = (round2( (1 - $myrow["discount_percent"]) * $myrow["unit_price"] * ($myrow["qty_recd"] - $myrow["quantity_inv"])));
//                //amount_cell(round2($Amount_in_FC * $supp_trans->Currency_Amount), user_price_dec());
//                amount_cell(round2($Amount_in_FC ), user_price_dec());
//            }
//            else
//            {
////                amount_cells(null, 'ChgPrice'.$n, price_decimal_format($myrow["act_price"], $dec2), null, null, $dec2);
//                amount_cell(round2($myrow["act_price"] * max($myrow['quantity_inv'], 0), user_price_dec()));
//            }
//            $Amount_in_Local = (round2((1 - $myrow["discount_percent"]) *$myrow["unit_price"] * ($myrow["qty_recd"] - $myrow["quantity_inv"])));
//
//            $currency_rate = price_format($supp_trans->Currency_Amount);
//
//            $supplier_currency = get_supplier_currency($supp_trans->supplier_id);
//            $Currency_Amount = get_exchange_rate_for_import($supplier_currency);
//
//            $cur = get_company_Pref('curr_default');
//            if($supplier_currency == $cur){
//
//                $supp_trans->Currency_Amount = 1;
//            }
//
//            elseif($supplier_currency != $cur){
//
//                $supp_trans->Currency_Amount = $Currency_Amount;
//
//            }
//
//// 			amount_cells(null, 'Unit_Amt'.$n,price_exrate_format($supp_trans->Currency_Amount)); // Unit_Amt
//
//            amount_ex_rate_cells(null, 'Unit_Amt'.$n,price_exrate_format($supp_trans->Currency_Amount)); // Unit_Amt
//
//
//
//            amount_cell($gross_amount); // As_Per_B_E
////            amount_cell(null);
//
//
//            amount_cell('F_E_D_Amt'.$n); // F.E.D Value
//

//
//

//
//
//

//
//
//
//
//            amount_cell('I_Tax_Amt'.$n); // I_Tax_Amt
//
//
//
//
//            amount_cell('Add_S_T_Amt'.$n); // Add_S_T_Amt
//
//
//            $other_expenses  =get_sys_pay_pref_field_import('Other_Expense');
//
//            amount_cells(null, 'Other_Expense'.$n,$other_expenses); // Other Expense
//
//            //text_cells(null, 'Job_Name'.$n); // Job_Name
//            dimensions_list_cells(_(""), 'Job_Name'.$n, null, true);
//            $myrow = get_item($myrow["item_code"]);
//
//
//
//            amount_cell($myrow['material_cost']); // Standard Cost

            //display_error($_POST['Job_Name'.$n]);
            if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
                submit_cells('grn_item_id'.$n, _("Add"), '', _("Add to Invoice"), true);
            else
                submit_cells('grn_item_id'.$n, _("Add"), '', _("Add to Credit Note"), true);
            if (($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE')) {	// Added 2008-10-18 by Joe Hunt. Special access rights needed.
                submit_cells('void_item_id'.$n, _("Remove"), '', _("WARNING! Be careful with removal. The operation is executed immediately and cannot be undone !!!"), true);
                submit_js_confirm('void_item_id'.$n,
                    _('You are about to remove all yet non-invoiced items from selected delivery line. This operation also irreversibly changes related order line. Do you want to continue ?'));
            }
            end_row();
        }
    }
    return true;
}
//**** Double Cart Work
function table_double_Header($labels, $params='')
{
    ?>
    <style>
        .import_style{
            background-color:#3c8dbc; color:white;
            font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;font-size: 13px; background-color:#3c8dbc;
        }
    </style>
    <?php
    start_row();
    echo "<td class='import_style'  rowspan='2'>S.No</td>\n";
//    foreach ($labels as $label)
//        echo "<td  $params>$label</td>\n";
    $l= COUNT($labels);
    for($i=0; $i<$l;$i+=2)
        echo "<td class='import_style' $params>$labels[$i]</td>\n";

    echo "<td style='border: #0b0800 1px solid;' >  </td>\n";
    end_row();
    start_row();
//    foreach ($labels as $label)
//        echo "<td  $params>$label</td>\n";
    $l= COUNT($labels);
    for($i=1; $i<$l;$i+=2)
        echo "<td  class='import_style'  $params>$labels[$i]</td>\n";

    echo "<td style='border: #0b0800 1px solid;' >  </td>\n";
    end_row();

}
function double_labelheader_cell_new($label, $params="")
{
    echo "<td  $params>$label</td>\n";
}
//
////------------------------------------------------------------------------------------

//
//// $mode = 0 none at the moment
////		 = 1 display on invoice/credit page
////		 = 2 display on view invoice
////		 = 3 display on view credit
//

function display_grn_items_import(&$supp_trans, $mode=0)
{
    global $path_to_root, $Ajax;

    $ret = true;
    // if displaying in form, and no items, exit
    if (($mode == 2  || $mode == 3) && count($supp_trans->grn_items) == 0)
        return 0;

    start_outer_table("style='border:1px solid #cccccc;' width=95%");

    $heading2 = "";
    if ($mode == 1)
    {
        if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
        {
            $heading = _("Items Received Yet to be Invoiced");
            if ($_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))	// Added 2008-10-18 by Joe Hunt. Only admins can remove GRNs
                $heading2 = _("WARNING! Be careful with removal. The operation is executed immediately and cannot be undone !!!");
        }
        else
            $heading = _("Delivery Item Selected For Adding To A Supplier Credit Note");
    }
    else
    {
        if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
            $heading = _("Received Items Charged on this Invoice");
        else
            $heading = _("Received Items Credited on this Note");
    }

    display_heading($heading);

    if ($mode == 1)
    {
        if ($supp_trans->trans_type == ST_SUPPCREDIT && !isset($_POST['invoice_no']))
        {
            echo "</td>";
            date_cells(_("Received between"), 'receive_begin', "", null, -30, 0, 0, "valign=middle");
            date_cells(_("and"), 'receive_end', '', null, 1, 0, 0, "valign=middle");
            submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), true);
            echo "<td>";
        }

        if ($heading2 != "")
        {
            display_note($heading2, 0, 0, "class='overduefg'");
        }
        echo "</td><td width=10% align='right'>";
        submit('InvGRNAll', _("Add All Items"), true, false,true);

    }

    end_outer_table(0, false);

    div_start('grn_items');
    start_table_new(TABLESTYLE_NEW, "width=95%");
    function get_import_gl_name()
    {
        /*Gets the GL Codes relevant to the item account  */
        $sql = "SELECT * FROM `0_sys_pay_pref_new`";

        return db_query($sql,"retreive stock gl code");
        //return db_fetch($get);
    }
    $get_import_gl_code = get_import_gl_name();
    $i = 0;
    $data = array();
    while ($myrow = db_fetch($get_import_gl_code)) {
        $data[$i]=$myrow[2];
        $i++;
    }
    function get_cost_center_name($dim_id)
    {
        $sql = "SELECT name FROM ".TB_PREF."dimensions WHERE id=".db_escape($dim_id);

        $result = db_query($sql, "could not get customer");

        $row = db_fetch_row($result);

        return $row[0];
    }
    $supplier_currency = get_supplier_currency($supp_trans->supplier_id);
    $currency = get_company_Pref('curr_default');

    if ($mode == 1)
    {

        $th = array(_("Delivery"), _("P.O."), _("Item"), _("Description"),
            _("Received On"),
            //_("Quantity Received"),
            //_("Quantity Invoiced"),
            _("Qty"),
            _("Price   ($supplier_currency)") ,
            _("Amount ($supplier_currency)"),
            _("Supplier Exchange rate"),
            _("GD Exchange rate"),
            _("Custom Value Per Unit as per GD ($supplier_currency)"),
            "",
            _("As Per B/E or GD"),
            _("Gross PO Amt ($currency) "),
            _("$data[0] %"),
            _("$data[0]"),
            "",
            _("Value Incl $data[0]($currency)"),
            _("$data[1] %"),
            _("$data[1]"),
            "",
            _("Value Incl $data[1]($currency)"),
            _("$data[2] %"),
            _("$data[2]($currency)"),
            _("$data[3] %"),
            _("$data[3]($currency)"),
            _("Value Incl $data[3] + $data[2]($currency)"), // Value And Duty
            _("Value Excl $data[4]($currency)"),
            _("$data[4] %"),
            _("$data[4]($currency)"),
            "",
            _("Amount Incl $data[4]($currency)"),

            _("$data[5] %"),
            _("$data[5]($currency)"),

            _("$data[6]%"),
            _("$data[6]($currency)"),
            _("Total Charges($currency)"),
            _("Other Expenses($currency)"),
            _("Net Amount($currency)"),
            _("Job Name"),_("Total import Expenses"),"","PO Price + Import Expenses",
            _("Unit Cost"),_("Con.Fact"),"","");
        if (($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))	// Added 2008-10-18 by Joe Hunt. Only admins can remove GRNs
            $th[] = "";
        if ($supp_trans->trans_type == ST_SUPPCREDIT)
        {
            $th[8] = _("Qty Yet To Credit");
        }
    }
    else
        $th = array(_("Delivery"), _("P.O."), _("Item"), _("Description"),
            _("Received On"),
            //_("Quantity Received"),
            //_("Quantity Invoiced"),
            _("Qty"),
            _("Price   ($supplier_currency)") ,
            _("Amount ($supplier_currency)"),
            _("Supplier Exchange rate"),
            _("GD Exchange rate"),
            _("Custom Value Per Unit as per GD ($supplier_currency)"),
            "",
            _("As Per B/E or GD"),
            _("Gross PO Amt ($currency) "),
            _("$data[0] %"),
            _("$data[0]"),
            "",
            _("Value Incl $data[0]($currency)"),
            _("$data[1] %"),
            _("$data[1]"),
            "",
            _("Value Incl $data[1]($currency)"),
            _("$data[2] %"),
            _("$data[2]($currency)"),
            _("$data[3] %"),
            _("$data[3]($currency)"),
            _("Value Incl $data[3] + $data[2]($currency)"), // Value And Duty
            _("Value Excl $data[4]($currency)"),
            _("$data[4] %"),
            _("$data[4]($currency)"),
            "",
            _("Amount Incl $data[4]($currency)"),

            _("$data[5] %"),
            _("$data[5]($currency)"),

            _("$data[6]%"),
            _("$data[6]($currency)"),
            _("Total Charges($currency)"),
            _("Other Expenses($currency)"),
            _("Net Amount($currency)"),
            _("Job Name"),_("Total import Expenses"),"","PO Price + Import Expenses",
            _("Unit Cost"),_("Con.Fact"),"","");


//    table_header_new($th);
    table_double_Header($th);


    $total_grn_value = 0;
    $total_net_amount=0;
    $Grand_Amount_in_FC=0;
    $total_Unit_Amt=0;
    $total_po_exchange_rate=0;
    $total_Gross_Amt=0;
    $total_Landing_Amt=0;
    $total_Value_invl_Landing=0;
    $total_INS_Amtt=0;
    $total_Value_Incl_INC=0;
    $total_F_E_D_Amt=0;
    $total_Duty_Amt=0;
    $total_Value_And_Duty=0;
    $total_S_T_Amt=0;
    $total_Amount_Incl_S_T=0;
    $total_I_Tax_Amt=0;
    $total_Add_S_T_Amt=0;
    $total_Total_Charges=0;
    $total_Gross_Amts = 0;
    $total_import_expenses = 0;
    $total_unit_cost = 0;
    $total_exc=0;


    $i = $k = 0;

    if (count($supp_trans->grn_items) > 0)
    {
        $serial_no =0;
        foreach ($supp_trans->grn_items as $entered_grn) {
            $serial_no ++;
//            display_error($serial_no);
//            alt_table_row_color($k);
            if($serial_no % 2 == 0){
                $color = '#cecccc';
            }
            else{
                $color = '#efefef';
            }

            start_row("class='oddrow' style='background-color:$color; font-size:13px;color:#454545;' ");

            $grn_batch = get_grn_batch_from_item($entered_grn->id);
            //_dump($entered_grn);
            label_cell($entered_grn->id);
            label_cell(get_trans_view_str(ST_SUPPRECEIVE, $grn_batch));

            label_cell($entered_grn->item_code);
//            if ($mode == 1) {

                $row = get_grn_batch($grn_batch);
                label_cell(sql2date($row['delivery_date']));
//				qty_cell($entered_grn->qty_recd, false, $dec);
//				qty_cell($entered_grn->prev_quantity_inv, false, $dec);
//            }


            $landing_amount = get_import_unit_cost ($data[0]);
            $ins_amount = get_import_unit_cost ($data[1]);
            $fed_amount = get_import_unit_cost ($data[2]);
            $duty_amount = get_import_unit_cost ($data[3]);
            $s_t_amount = get_import_unit_cost ($data[4]);
            $i_tax_amount = get_import_unit_cost ($data[5]);
            $add_st_amount = get_import_unit_cost ($data[6]);
            $ohter_expense = get_import_unit_cost ($data[7]);

            $line_taxfree = $entered_grn->taxfree_charge_price_import($supp_trans->tax_group_id);
            $line_tax = $entered_grn->full_charge_price_import($supp_trans->tax_group_id) - $line_taxfree;
            $Grand_Amount_in_FC += $Amount_in_FC;
            $total_net_amount += ($entered_grn->Net_Amount + $line_tax);
            $total_Unit_Amt += ($entered_grn->Unit_Amt);
            $total_po_exchange_rate += ($entered_grn->po_exchange_rate);
            $total_Gross_Amt += ($entered_grn->Gross_Amt);
            $total_Landing_Amt += ($entered_grn->Landing_Amt);
            $total_Value_invl_Landing += ($entered_grn->Value_invl_Landing);
            $total_INS_Amtt += ($entered_grn->INS_Amt);
            $total_Value_Incl_INC += ($entered_grn->Value_Incl_INC);
            $total_F_E_D_Amt += ($entered_grn->F_E_D_Amt);
            $total_Duty_Amt += ($entered_grn->Duty_Amt);
            $total_Value_And_Duty += ($entered_grn->Value_And_Duty);
            $total_exc +=$entered_grn->Value_Excl_S_T;
            $total_S_T_Amt += ($entered_grn->S_T_Amt);
            $total_Amount_Incl_S_T += ($entered_grn->Amount_Incl_S_T);
            $total_I_Tax_Amt += ($entered_grn->I_Tax_Amt);
            $total_Add_S_T_Amt += ($entered_grn->Add_S_T_Amt);
            $total_Total_Charges += ($entered_grn->Total_Charges);
            $total_net_amount += ($entered_grn->Net_Amount);
            $total_import_expenses += ($entered_grn->tot_import_expenses);

            $totalquantity_inv += ($entered_grn->this_quantity_inv);
            $total_B_E +=$entered_grn->As_Per_B_E;

            if($landing_amount == 0)
                $Landing_Amt = $entered_grn->Landing_Amt;
            else
                $Landing_Amt = 0;

            if($ins_amount == 0)
                $INS_Amt = $entered_grn->INS_Amt;
            else
                $INS_Amt = 0;


            if($fed_amount == 0)
                $F_E_D_Amt = $entered_grn->F_E_D_Amt;
            else
                $F_E_D_Amt = 0;

            if($duty_amount == 0)
                $Duty_Amt = $entered_grn->Duty_Amt;
            else
                $Duty_Amt = 0;
            if($i_tax_amount == 0)
                $I_Tax_Amt = $entered_grn->I_Tax_Amt;
            else
                $I_Tax_Amt = 0;

            if($add_st_amount== 0)
                $Add_S_T_Amt = $entered_grn->Add_S_T_Amt;
            else
                $Add_S_T_Amt = 0;


            if($s_t_amount == 0)
                $S_T_Amt = $entered_grn->S_T_Amt;
            else
                $S_T_Amt = 0;

            if($ohter_expense == 0)
                $Other_Expenses = $entered_grn->Other_Expenses;
            else
                $Other_Expenses = 0;

            if($expense == 0)
                $Total_Other_Expenses = $entered_grn->Total_Other_Expenses;
            else
                $Total_Other_Expenses = 0;


            $po_plus_import_exp = /*$entered_grn->tot_import_expenses*/ $entered_grn->Net_Amount + $entered_grn->Gross_Amt ;

 global $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CASAMIA') {
           // display_error($entered_grn->tot_import_expenses."+".$entered_grn->Gross_Amt ."/".abs($entered_grn->this_quantity_inv));
            $unit_cost = ($entered_grn->tot_import_expenses +  $entered_grn->Gross_Amt) /abs($entered_grn->this_quantity_inv) ;
            
            }
            else{
             $unit_cost = ($entered_grn->tot_import_expenses +  $entered_grn->Gross_Amt) /abs($entered_grn->this_quantity_inv) ;
            }
            $total_unit_cost += ($unit_cost);
            amount_decimal_cell($entered_grn->chg_price);
            amount_ex_rate_cell($entered_grn->po_exchange_rate); // PO exchange rate
            label_cell($entered_grn->Gross_Amt_new);
            amount_cell($entered_grn->As_Per_B_E); // As_Per_B_E
            amount_cell($entered_grn->Landing); // Landing
            label_cell(""); // blank
            amount_cell($entered_grn->INS); // INS
            label_cell(""); // blank
            amount_cell($entered_grn->F_E_D); // F.E.D 1%
            amount_cell($entered_grn->Duty); // Duty
            amount_cell($entered_grn->Value_And_Duty); // Value_And_Duty
            amount_cell($entered_grn->S_T); // S_T
            label_cell(""); // blank
            amount_cell($entered_grn->I_TAX); // I/Tax
            amount_cell($entered_grn->Add_S_T); // Add_S_T
            amount_cell($entered_grn->Total_Charges); // Total_Charges
            if ($entered_grn->Other_Expenses == 0)
                amount_cell($entered_grn->Net_Amount + $entered_grn->Total_Other_Expense); // Net_Amount
            else
                amount_cell($entered_grn->Net_Amount); // Net_Amount



            amount_cell($entered_grn->tot_import_expenses);
            amount_cell($entered_grn->tot_import_expenses +  $entered_grn->Gross_Amt);
            end_row();
            start_row("class='evenrow' style='background-color:$color;font-size:13px;' ");
            label_cell(""); // PO
//            if ($mode == 1) {
            $row = get_grn_batch($grn_batch);
            label_cell($row['purch_order_no']); // PO
//            }
            label_cell($entered_grn->item_description);
//            $dec = get_qty_dec($entered_grn->item_code);
            qty_cell(abs($entered_grn->this_quantity_inv), true, $dec);
//            amount_decimal_cell($entered_grn->chg_price);
            $Amount_in_FC = (round2($entered_grn->chg_price * abs($entered_grn->this_quantity_inv)));

            amount_cell(round2($Amount_in_FC), user_price_dec(), true);
            amount_ex_rate_cell($entered_grn->Unit_Amt); // Unit_Amt
            label_cell("");

            amount_cell($entered_grn->Gross_Amt, true); // Gross_Amt
            amount_cell($entered_grn->Landing_Amt, true); // Landing_Amt
            amount_cell($entered_grn->Value_invl_Landing); // Value_invl_Landing
            amount_cell($entered_grn->INS_Amt, true); // INS_Amtamount_cell($entered_grn->Value_Incl_INC); // Value_Incl_INC
            amount_cell($entered_grn->Value_Incl_INC); // Value_Incl_INC
            amount_cell($entered_grn->F_E_D_Amt, true); // F.E.D Value
            amount_cell($entered_grn->Duty_Amt, true); // Duty_Amt
            amount_cell($entered_grn->Value_Excl_S_T); // Value_Excl_S_T
            amount_cell($entered_grn->S_T_Amt, true); // S_T_Amt
            amount_cell($entered_grn->Amount_Incl_S_T); // Amount_Incl_S_T
            amount_cell($entered_grn->I_Tax_Amt, true); // I_Tax_Amt
            amount_cell($entered_grn->Add_S_T_Amt, true); // Add_S_T_Amt
            // S.H
            $entered_grn->total_Gross_Amt = $total_Gross_Amt;
            if ($entered_grn->Other_Expenses == 0){
                $Total_Other_Expense +=$entered_grn->Total_Other_Expense;
                amount_cell($entered_grn->Total_Other_Expense, true);
            }
            else{
                $Total_Other_Expense += $entered_grn->Other_Expenses;

                amount_cell($entered_grn->Other_Expenses, true);
            }

//            if ($entered_grn->Other_Expenses == 0)
//                amount_cell($entered_grn->Net_Amount + $entered_grn->Total_Other_Expense); // Net_Amount
//            else
//                amount_cell($entered_grn->Net_Amount); // Net_Amount
//            label_cell(get_cost_center_name($entered_grn->Job_Name)); // Job_Name
//			$Unit_Cost = $entered_grn->Net_Amount/$entered_grn->this_quantity_inv;
//			$myrow = db_fetch($get_import_gl_code);




            $url = "<a href='$path_to_root/purchasing/view/view_std_cost.php?&stock_id=" . $entered_grn->item_code . "&popup=1' onclick=\"javascript:openWindow(this.href,this.target); return false;\"'>" . number_format2($Unit_Cost). "</a>";


            if ($mode != 1)
            {
                label_cell("");
                label_cell("");
                label_cell($unit_cost);
            }
            else{

                label_cell("");
                label_cell("");
                amount_cell($unit_cost , true);

            }
            amount_cell($unit_cost / $entered_grn->chg_price);
            $entered_grn->UnitCost = $unit_cost;

            // Standard Cost

//			$All_Columns_Total = $F_E_D_Amt + $Amt_FC + $Duty_Amt + $S_T_Amt + $I_Tax_Amt + $Add_S_T_Amt;
//			$STD_Cost = $All_Columns_Total / input_num('this_quantity_inv'.$n);
//			$Net_Amount = $F_E_D_Amt + $Duty_Amt + $S_T_Amt + $I_Tax_Amt + $Add_S_T_Amt + $qwerty + $_POST['Other_Expense'.$n];
//			$STD_Cost = ($total_fc_amount + $entered_grn->F_E_D_Amt +
//				$entered_grn->S_T_Amt + $entered_grn->I_Tax_Amt + $entered_grn->Add_S_T_Amt +
//				$entered_grn->Total_Other_Expenses);
            $Round_Net_Amount = round($entered_grn->Net_Amount);
//		stock_cost_update_import($entered_grn->item_code, $Unit_Cost, 0, 0, 0);


//			display_error($Amount_in_FC ."---". $entered_grn->F_E_D_Amt ."---".
//				$entered_grn->S_T_Amt ."---". $entered_grn->I_Tax_Amt ."---". $entered_grn->Add_S_T_Amt ."---".
//				$entered_grn->this_quantity_inv);
//			display_error($STD_Cost);

            if ($mode == 1)
            {
                delete_button_cell("Delete" . $entered_grn->id, _("Edit"), _('Edit document line'));
                if (($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))
                    label_cell("");
            }
            end_row();

            $total_grn_value += round2($entered_grn->chg_price * abs($entered_grn->this_quantity_inv),
                user_price_dec());

            $i++;
            if ($i > 15)
            {
                $i = 0;
                table_header($th);
            }
            //S.H
            $total_Gross_Amts = $entered_grn->total_Gross_Amt;
        }

// 		if(isset($_POST['Load_Exp']))
// 		{
// 			//div_start('Calculation');
// 			foreach ($supp_trans->grn_items as $line => $entered_grn) {

// 				if ($_POST['gross_rate'] == 0) {

// 				$supp_trans->grn_items[$line]->Other_Expense = 1 * $entered_grn->Gross_Amt / $total_Gross_Amts;
// 			}
// 			else {
// 			//	display_error(input_num('gross_rate') * $entered_grn->Gross_Amt /// $total_Gross_Amts);
// //				$supp_trans->grn_items[$line]->Total_Other_Expenses = $_POST['gross_rate']*$entered_grn->Gross_Amt/$total_Gross_Amts;
// 				$supp_trans->grn_items[$line]->Total_Other_Expense = input_num('gross_rate') * $entered_grn->Gross_Amt / $total_Gross_Amts;

// 			}
// 			}
// 			div_end();
// 		}


    }
    if ($mode == 1)
    {
        $ret = display_grn_items_for_selection_import($supp_trans, $k);
        $colspan = 10;
    }
    else
        $colspan = 31;
//    if($mode == 0)
    {

        label_cell(_("Total"));
        label_cell(_(""));
        label_cell(_(""));
        label_cell(_(""));
        amount_cell($totalprice);
        amount_cell($total_po_exchange_rate);
        amount_cell($total_Gross_Amt_new);
        amount_cell($total_B_E);
        label_cell('');
        label_cell('');
        label_cell('');
        label_cell('');
        label_cell('');
        label_cell('');
        amount_cell($total_Value_And_Duty);
        label_cell('');
        label_cell('');
        label_cell('');
        label_cell('');
        amount_cell($total_Total_Charges);
        amount_cell($total_net_amount); // Net_Amount
        amount_cell($total_import_expenses);
        amount_cell($total_import_expenses + $total_Gross_Amt);
        amount_cell("");
        start_row();
        label_cell(_(""));
        label_cell(_(""));
        label_cell(_(""));
        amount_cell($totalquantity_inv);
        amount_cell($Grand_Amount_in_FC);
        amount_cell($total_Unit_Amt);
        label_cell('');
        amount_cell($total_Gross_Amt);
        amount_cell($total_Landing_Amt);
        amount_cell($total_Value_invl_Landing);
        amount_cell($total_INS_Amtt);
        amount_cell($total_Value_Incl_INC);
        amount_cell($total_F_E_D_Amt);
        amount_cell($total_Duty_Amt);
        amount_cell($total_exc);
        amount_cell($total_S_T_Amt);
        amount_cell($total_Amount_Incl_S_T);
        amount_cell($total_I_Tax_Amt);
        amount_cell($total_Add_S_T_Amt);
        amount_cell($Total_Other_Expense);
        label_cell('');
        label_cell('');
        amount_cell($total_unit_cost);
        amount_cell($total_con_fact);
    }
//    start_row("class='inquirybg' style='font-weight:bold'");
//    label_cell(_(""));
//    label_cell(_(""));
//    label_cell(_(""));
//    label_cell(_(""));
    if($mode == 1){
        //	label_cell(_(""));
        ///	label_cell(_(""));
//	label_cell(_(""));
//	label_cell(_(""));
    }


//    label_cell(_("Total"));
//    amount_cell($totalquantity_inv);
//    label_cell(_(""));
//    amount_cell($Grand_Amount_in_FC);
//    label_cell(_(""));// Unit_Amt
//    label_cell(_(""));// total_po_exchange_rate
//    label_cell(_(""));
//    amount_cell($total_Gross_Amt); // Gross_Amt
//    amount_cell(_($total_B_E)); // As_Per_B_E
//    label_cell(_("")); // Landing
//    amount_cell($total_Landing_Amt); // Landing_Amt
//    amount_cell($total_Value_invl_Landing); // Value_invl_Landing
//    label_cell("");
//    amount_cell(_($total_INS_Amtt)); // INS
//    //label_cell(""); // INS_Amt
//    amount_cell($total_Value_Incl_INC); // Value_Incl_INC
//    label_cell(_("")); // F.E.D 1%
//    amount_cell($total_F_E_D_Amt); // F.E.D Value
//    label_cell(_("")); // Duty
//    amount_cell($total_Duty_Amt); // Duty_Amt
//    amount_cell($total_Value_And_Duty); // Value_And_Duty
//    label_cell(number_format2($total_exc,$dec)); // Value_Excl_S_T
//    label_cell(_("")); // S_T
//    amount_cell($total_S_T_Amt); // S_T_Amt
//    amount_cell($total_Amount_Incl_S_T); // Amount_Incl_S_T
//    label_cell(_("")); // I/Tax
//    amount_cell($total_I_Tax_Amt); // I_Tax_Amt
//    label_cell(_("")); // Add_S_T
//    amount_cell($total_Add_S_T_Amt); // Add_S_T_Amt
//
//
//    amount_cell($total_Total_Charges); // Total_Charges
//    amount_cell($Total_Other_Expense); // other
//
//
//

    if (!$ret)
    {
        start_row();
        echo "<td colspan=".($colspan + 1).">";
        if ($supp_trans->trans_type == ST_SUPPCREDIT_IMPORT)
            display_note(_("There are no outstanding items received from this supplier that have not been invoiced by them."), 0, 0);
        else
        {
            display_note(_("There are no received items for the selected supplier that have been invoiced."));
            display_note(_("Credits can only be applied to invoiced items."), 0, 0);
        }
        echo "</td>";
        end_row();
    }
    end_table(1);
    div_end();

    return $total_grn_value;
}
//
////--------------------------------------------------------------------------------------------------
function get_duedate_from_terms_import(&$trans)
{
    $date = get_class($trans) == 'purch_order' ? $trans->orig_order_date : $trans->tran_date;

    if (!is_date($date))
    {
        $date = Today();
    }
    if ($trans->terms['day_in_following_month'])
    { /*Its a day in the following month when due */
        $trans->due_date =
            add_days(end_month($date), $trans->terms["day_in_following_month"]);
    }
    else
    { /*Use the Days Before Due to add to the invoice date */
        $trans->due_date = add_days($date, $trans->terms["days_before_due"]);
    }
}
//
////--------------------------------------------------------------------------------------------------
//
//?>