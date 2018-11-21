<?php
//--------------------------------------------------------------------------------------------------
//
function copy_from_trans_price_import(&$supp_trans)
{
	$_POST['Comments'] = $supp_trans->Comments;
	$_POST['tran_date'] = $supp_trans->tran_date;
	$_POST['due_date'] = $supp_trans->due_date;
	$_POST['supp_reference'] = $supp_trans->supp_reference;
	$_POST['reference'] = $supp_trans->reference;
	$_POST['supplier_id'] = $supp_trans->supplier_id;
	$_POST['gross_rate'] = $supp_trans->gross_rate;

}

////--------------------------------------------------------------------------------------------------

function copy_to_trans_price_import(&$supp_trans)
{
	$supp_trans->Comments = $_POST['Comments'];
	$supp_trans->tran_date = $_POST['tran_date'];
	$supp_trans->due_date = $_POST['due_date'];
	$supp_trans->supp_reference = $_POST['supp_reference'];
	$supp_trans->reference = $_POST['reference'];
	$supp_trans->gross_rate= $_POST['gross_rate'];


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

function get_sys_pay_pref_field_price_import ($name)
{
    $sql = "SELECT default_value FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function invoice_header_price_import(&$supp_trans)
{
	global $Ajax, $Refs;

	// if vars have been lost, recopy
	if (!isset($_POST['tran_date']))
		copy_from_trans_price_import($supp_trans);

	start_outer_table(TABLESTYLE2, "width=95%");

	table_section(1);

	if (isset($_POST['invoice_no']))
	{
		$trans = get_supp_trans_price_import($_POST['invoice_no'], ST_SUPPINVOICE);
		$_POST['supplier_id'] = $trans['supplier_id'];
		$supp = $trans['supplier_name'] . " - " . $trans['SupplierCurrCode'];
		label_row(_("Supplier:"), $supp.hidden('supplier_id', $_POST['supplier_id'], false));
	}
	else
	{
		if (!isset($_POST['supplier_id']) && (get_global_supplier() != ALL_TEXT))
			$_POST['supplier_id'] = get_global_supplier();

		supplier_list_row(_("Supplier:"), 'supplier_id', $_POST['supplier_id'], false, true);
	}

	if ($supp_trans->supplier_id != $_POST['supplier_id'])
	{
		// supplier has changed
		// delete all the order items - drastic but necessary because of
		// change of currency, etc
		$supp_trans->clear_items_price_import();
		read_supplier_details_to_trans_price_import($supp_trans, $_POST['supplier_id']);
		copy_from_trans_price_import($supp_trans);
	}

// 	ref_row(_("Reference:"), 'reference', '', $Refs->get_next($supp_trans->trans_type));
ref_row(_("Reference:"), 'reference', '', null, false, $supp_trans->trans_type, array('date'=> @$_POST['tran_date']));
	if (isset($_POST['invoice_no']))
	{
		label_row(_("Supplier's Ref.:"), $_POST['invoice_no'].hidden('invoice_no', $_POST['invoice_no'], false).hidden('supp_reference', $_POST['invoice_no'], false));
	}
	else
		text_row(_("Supplier's Ref.:"), 'supp_reference', $_POST['supp_reference'], 20, 60);


//	text_row(_("Gross Expenses :"), 'gross_rate', null, 20, 60);
	amount_cells("Gross Expenses :", 'gross_rate'); // Add_S_T
	submit_cells('Load_Exp', _("Allocate Expenses"), "colspan=2",
		_('Calculate Other Expense in Cart'), true);

	if (isset($_POST['Load_Exp']))
	{
		$Ajax->activate('grn_items');
		$Ajax->activate('inv_tot');
		$Ajax->activate('Calculation');
	}

	table_section(2, "33%");

	date_row(_("Date") . ":", 'tran_date', '', true, 0, 0, 0, "", true);
	if (isset($_POST['_tran_date_changed'])) {
		$Ajax->activate('_ex_rate');
		$supp_trans->tran_date = $_POST['tran_date'];
		get_duedate_from_terms_price_import($supp_trans);
		$_POST['due_date'] = $supp_trans->due_date;
		$Ajax->activate('due_date');
	}

	date_row(_("Due Date") . ":", 'due_date');

	label_row(_("Terms:"), $supp_trans->terms['description']);


	table_section(3, "33%");

	set_global_supplier($_POST['supplier_id']);

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
function invoice_totals_price_import(&$supp_trans)
{
	copy_to_trans_price_import($supp_trans);

	$dim = get_company_pref('use_dimension');
	$colspan = ($dim == 2 ? 7 : ($dim == 1 ? 6 : 5));
	start_table(TABLESTYLE, "width=95%");
	label_row(_("Sub-total:"), price_format( $supp_trans->ov_amount), "colspan=$colspan align=right", "align=right");

	$taxes = $supp_trans->get_taxes_price_import($supp_trans->tax_group_id);
	$tax_total = display_edit_tax_items($taxes, $colspan, $supp_trans->tax_included);

	$display_total = price_format($supp_trans->ov_amount + $tax_total);

	if ($supp_trans->trans_type == ST_SUPPINVOICE)
		label_row(_("Invoice Total:"),
			$display_total, "colspan=$colspan align=right style='font-weight:bold;'", "align=right style='font-weight:bold;'");
	else
		label_row(_("Credit Note Total"),
			$display_total, "colspan=$colspan align=right style='font-weight:bold;color:red;'", "nowrap align=right style='font-weight:bold;color:red;'");

	end_table(1);
	start_table(TABLESTYLE2);
	textarea_row(_("Memo:"), "Comments", null, 50, 3);
	end_table(1);
}

////--------------------------------------------------------------------------------------------------
function display_gl_controls_price_import(&$supp_trans, $k)
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

// $mode = 0 none at the moment
//		 = 1 display on invoice/credit page
//		 = 2 display on view invoice
//		 = 3 display on view credit

function display_gl_items_price_import(&$supp_trans, $mode=0)
{
	global $path_to_root, $Ajax;

	// if displaying in form, and no items, exit
	if (($mode == 2 || $mode == 3) && count($supp_trans->gl_codes) == 0)
		return 0;

	if ($supp_trans->trans_type == ST_SUPPINVOICE)
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
		display_gl_controls_price_import($supp_trans, $k);
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



function display_grn_items_for_selection_price_import(&$supp_trans, $k)
{
//	$supplier_currency = get_supplier_currency($supp_trans->supplier_id);
//	$supp_trans->Currency_Amount = get_exchange_rate_for_import($supplier_currency);

		$supplier_currency = get_supplier_currency($supp_trans->supplier_id);
		$Currency_Amount = get_exchange_rate_for_import($supplier_currency);

	if($Currency_Amount == 0)
		$supp_trans->Currency_Amount = 1;
	else
		$supp_trans->Currency_Amount = $Currency_Amount;

	if ($supp_trans->trans_type == ST_SUPPINVOICE)
	$result = get_grn_items(0, $supp_trans->supplier_id, true);
	else
	{
		if (isset($_POST['receive_begin']) && isset($_POST['receive_end']))
			$result = get_grn_items(0, $supp_trans->supplier_id, false, true, 0, $_POST['receive_begin'], $_POST['receive_end']);
		else if (isset($_POST['invoice_no']))
			$result = get_grn_items(0, $supp_trans->supplier_id, false, true, $_POST['invoice_no']);
		else
			$result = get_grn_items(0, $supp_trans->supplier_id, false, true);
	}
	if (db_num_rows($result) == 0)
	{
		return false;
	}

	/*Set up a table to show the outstanding GRN items for selection */

	while ($myrow = db_fetch($result))
	{
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

			alt_table_row_color($k);

			$n = $myrow["id"];
			label_cell(get_trans_view_str(25, $myrow["grn_batch_id"]).
			hidden('qty_recd'.$n, $myrow["qty_recd"], false).
			hidden('item_code'.$n, $myrow["item_code"], false).
			hidden('item_description'.$n, $myrow["description"], false).
			hidden('prev_quantity_inv'.$n, $myrow['quantity_inv'], false).
			hidden('order_price'.$n, $myrow['unit_price'], false).
			hidden('std_cost_unit'.$n, $myrow['std_cost_unit'], false).
			hidden('po_detail_item'.$n, $myrow['po_detail_item'], false).
			hidden('location'.$n, $myrow["loc_code"], false));
			label_cell(get_trans_view_str(ST_PURCHORDER, $myrow["purch_order_no"]));
			label_cell($myrow["item_code"]);
			label_cell($myrow["description"]);
			label_cell(sql2date($myrow["delivery_date"]));
			$dec = get_qty_dec($myrow["item_code"]);
			qty_cell($myrow["qty_recd"], false, $dec);
			qty_cell($myrow["quantity_inv"], false, $dec);
			if ($supp_trans->trans_type == ST_SUPPINVOICE)
				qty_cells(null, 'this_quantity_inv'.$n, number_format2($myrow["qty_recd"] - $myrow["quantity_inv"], $dec), null, null, $dec);
			else
				qty_cells(null, 'This_QuantityCredited'.$n, number_format2(max($myrow["quantity_inv"], 0), $dec), null, null, $dec);
			$dec2 = 0;
			if ($supp_trans->trans_type == ST_SUPPINVOICE)
			{
				amount_cells(null, 'ChgPrice'.$n, price_decimal_format($myrow["unit_price"], $dec2), null, null, $dec2);
				$Amount_in_FC = (round2($myrow["unit_price"] * ($myrow["qty_recd"] - $myrow["quantity_inv"])));
				//amount_cell(round2($Amount_in_FC * $supp_trans->Currency_Amount), user_price_dec());
				amount_cell(round2($Amount_in_FC ), user_price_dec());
			}
			else
			{
				amount_cells(null, 'ChgPrice'.$n, price_decimal_format($myrow["act_price"], $dec2), null, null, $dec2);
				amount_cell(round2($myrow["act_price"] * max($myrow['quantity_inv'], 0), user_price_dec()));
			}
            $Amount_in_Local = (round2($myrow["unit_price"] * ($myrow["qty_recd"] - $myrow["quantity_inv"])));

            $currency_rate = price_format($supp_trans->Currency_Amount);


			amount_cells(null, 'Unit_Amt'.$n, $currency_rate); // Unit_Amt
			amount_cells(null,'Gross_Amt'.$n); // Gross_Amt
			amount_cell(null); // As_Per_B_E
            amount_cell(null);
           // $landing=get_sys_pay_pref_field_price_import('Landing_Amt');
			amount_cells(null, 'Landing'.$n); // Landing
			//amount_cell('Landing_Amt'.$n); // Landing_Amt
			amount_cell('Value_invl_Landing'.$n); // Value_invl_Landing

           // $ins_amount  =get_sys_pay_pref_field_price_import('INS_Amt');
            amount_cells(null, 'INS'.$n); // INS
			//amount_cell('INS_Amt'.$n); // INS_Amt


			amount_cell('Value_Incl_INC'.$n); // Value_Incl_INC
           // $fed_amount  =get_sys_pay_pref_field_price_import('F_E_D_Amt');

            amount_cells(null, 'F_E_D'.$n); // F.E.D 1%
			//amount_cell('F_E_D_Amt'.$n); // F.E.D Value

         //   $duty_amount  =get_sys_pay_pref_field_price_import('Duty_Amt');

            amount_cells(null,'Duty'.$n); // Duty
			//amount_cell('Duty_Amt'.$n); // Duty_Amt
			amount_cell('Value_And_Duty'.$n); // Value_And_Duty
			amount_cell('Value_Excl_S_T'.$n); // Value_Excl_S_T

         //   $st_amount  =get_sys_pay_pref_field_price_import('S_T_Amt');

            amount_cells(null, 'S_T'.$n); // S_T
			//amount_cell('S_T_Amt'.$n); // S_T_Amt

			amount_cell('Amount_Incl_S_T'.$n); // Amount_Incl_S_T
//
       //     $itax_amount  =get_sys_pay_pref_field_price_import('I_Tax_Amt');

            amount_cells(null, 'I_Tax'.$n); // I/Tax
			//amount_cell('I_Tax_Amt'.$n); // I_Tax_Amt

           // $addsttax_amount  =get_sys_pay_pref_field_price_import('Add_S_T_Amt');

            amount_cells(null, 'Add_S_T'.$n); // Add_S_T
			//amount_cell('Add_S_T_Amt'.$n); // Add_S_T_Amt
			amount_cell('Total_Charges'.$n); // Total_Charges

       //     $other_expenses  =get_sys_pay_pref_field_price_import('Other_Expense');

            amount_cells(null, 'Other_Expense'.$n); // Other Expense
			amount_cell('Net_Amount'.$n); // Net_Amount
			//text_cells(null, 'Job_Name'.$n); // Job_Name
			dimensions_list_cells(_(""), 'Job_Name'.$n, null, true);
			$myrow = get_item($myrow["item_code"]);
			amount_cell($myrow['material_cost']); // Standard Cost
			//display_error($_POST['Job_Name'.$n]);
			if ($supp_trans->trans_type == ST_SUPPINVOICE)
				submit_cells('grn_item_id'.$n, _("Add"), '', _("Add to Invoice"), true);
			else
				submit_cells('grn_item_id'.$n, _("Add"), '', _("Add to Credit Note"), true);
			if (($supp_trans->trans_type == ST_SUPPINVOICE) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE')) {	// Added 2008-10-18 by Joe Hunt. Special access rights needed.
				submit_cells('void_item_id'.$n, _("Remove"), '', _("WARNING! Be careful with removal. The operation is executed immediately and cannot be undone !!!"), true);
				submit_js_confirm('void_item_id'.$n,
					_('You are about to remove all yet non-invoiced items from selected delivery line. This operation also irreversibly changes related order line. Do you want to continue ?'));
			}
			end_row();
		}
	}
	return true;
}
//
////------------------------------------------------------------------------------------

//
//// $mode = 0 none at the moment
////		 = 1 display on invoice/credit page
////		 = 2 display on view invoice
////		 = 3 display on view credit
//

function display_grn_items_price_import(&$supp_trans, $mode=0)
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
		if ($supp_trans->trans_type == ST_SUPPINVOICE)
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
		if ($supp_trans->trans_type == ST_SUPPINVOICE)
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
	start_table(TABLESTYLE, "width=95%");
	function get_import_gl_name_price()
	{
		/*Gets the GL Codes relevant to the item account  */
		$sql = "SELECT * FROM `0_sys_pay_pref_new`";

		return db_query($sql,"retreive stock gl code");
		//return db_fetch($get);
	}
	$get_import_gl_code = get_import_gl_name_price();
	$i = 0;
	$data = array();
	while ($myrow = db_fetch($get_import_gl_code)) {
		$data[$i]=$myrow[2];
		$i++;
	}
	function get_cost_center_name_price($dim_id)
	{
		$sql = "SELECT name FROM ".TB_PREF."dimensions WHERE id=".db_escape($dim_id);

		$result = db_query($sql, "could not get customer");

		$row = db_fetch_row($result);

		return $row[0];
	}

	if ($mode == 1)
	{
		$th = array(_("Delivery"), _("P.O."), _("Item"), _("Description"),
			_("Received On"), _("Quantity Received"), _("Quantity Invoiced"),
			_("Qty Yet To Invoice"), $supp_trans->tax_included ? _("Price after Tax") : _("Price before Tax"),
			_("Amount in Local"),
                        _("Exchange rate"),
	 		_("Custom Value Per Unit"),
			_("Gross Amt"),
			_("As Per B/E"),
			_("$data[0] "),
			//_("$data[0]"),
			_("Value Incl $data[0]"),
			_("$data[1] "),
			//_("$data[1]"),
			_("Value Incl $data[1]"),
			_("$data[2] "),
			//_("$data[2]"),
			_("$data[3] "),
			//_("$data[3]"),
			_("Value Incl $data[3] + $data[2]"), // Value And Duty
			_("Value Excl $data[4]"),
			_("$data[4] "),
			//_("$data[4]"),
			_("Amount Incl $data[4]"),
			_("$data[5] "),
			//_("$data[5]"),
			_("$data[6]"),
			//_("$data[6]"),
			_("Total Charges"),
			_("Other Expenses"),
			_("Net Amount"),
			_("Job Name"),
			_("Unit Cost"), "");
		if (($supp_trans->trans_type == ST_SUPPINVOICE) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))	// Added 2008-10-18 by Joe Hunt. Only admins can remove GRNs
			$th[] = "";
		if ($supp_trans->trans_type == ST_SUPPCREDIT)
		{
			$th[8] = _("Qty Yet To Credit");
		}
	}
	else
        $th = array(_("Delivery"), _("Item"), _("Description"),
            _("Quantity Invoiced"), $supp_trans->tax_included ? _("Price after Tax") : _("Price before Tax"),
            _("Amount in Local"),
            _("Exchange rate"),
            _("Custom Value Per Unit"),
            _("Gross Amt"),
            _("As Per B/E"),
            _("$data[0] "),
           // _("$data[0]"),
            _("Value Incl $data[0]"),
            _("$data[1] "),
          //  _("$data[1]"),
            _("Value Incl $data[1]"),
            _("$data[2]"),
            //_("$data[2]"),
            _("$data[3] "),
          //  _("$data[3]"),
            _("Value Incl $data[3] + $data[2]"), // Value And Duty
            _("Value Excl $data[4]"),
//            _("$data[4] "),
            _("$data[4]"),
            _("Amount Incl $data[4]"),
            _("$data[5] "),
//            _("$data[5]"),
            _("$data[6]"),
//            _("$data[6]"),
            _("Total Charges"),
            _("Other Expenses"),
            _("Net Amount"),
            _("Job Name"),
            _("Unit Cost"), "");
//		$th = array(_("Delivery"), _("P.O."), _("Item"), _("Description"),
//			_("Received On"), _("Quantity Received"), _("Quantity Invoiced"),
//			_("Qty Yet To Invoice"), $supp_trans->tax_included ? _("Price after Tax") : _("Price before Tax"),
//			_("Amount in FC"),
//			_("Unit Amt"),
//			_("Gross Amt"),
//			_("As Per B/E"),
//			_("Landing %"),
//			_("$data[0]"),
//			_("Value invl Landing"),
//			_("INS %"),
//			_("$data[1]"),
//			_("Value Incl INC"),
//			_("ACD %"),
//			_("$data[2]"),
//			_("Duty %"),
//			_("$data[3]"),
//			_("Value Incl Duty"), // Value And Duty
//			_("Value Excl S.T."),
//			_("S.T. %"),
//			_("$data[4]"),
//			_("Amount Incl S.T"),
//			_("I.Tax. %"),
//			_("$data[5]"),
//			_("Add S/T %"),
//			_("$data[6]"),
//			_("Total Charges"),
//			_("Other Expenses"),
//			_("Net Amount"),
//			_("Job Name"), "");

	table_header($th);
	$total_grn_value = 0;
	$total_net_amount=0;
	$Grand_Amount_in_FC=0;
	$total_Unit_Amt=0;
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
    $total_exc=0;

    $total_Landing=0;
	$i = $k = 0;

	if (count($supp_trans->grn_items) > 0)
	{

		foreach ($supp_trans->grn_items as $entered_grn) {

			alt_table_row_color($k);

			$grn_batch = get_grn_batch_from_item($entered_grn->id);
			//_dump($entered_grn);
			label_cell(get_trans_view_str(ST_SUPPRECEIVE, $grn_batch));
			if ($mode == 1) {
				$row = get_grn_batch($grn_batch);
				label_cell($row['purch_order_no']); // PO
			}
			label_cell($entered_grn->item_code);
			label_cell($entered_grn->item_description);
			$dec = get_qty_dec($entered_grn->item_code);
			if ($mode == 1) {
				label_cell(sql2date($row['delivery_date']));
				qty_cell($entered_grn->qty_recd, false, $dec);
				qty_cell($entered_grn->prev_quantity_inv, false, $dec);
			}
			qty_cell(abs($entered_grn->this_quantity_inv), true, $dec);
			amount_decimal_cell($entered_grn->chg_price);
			$Amount_in_FC = (round2($entered_grn->chg_price * abs($entered_grn->this_quantity_inv))
				* $supp_trans->Currency_Amount);
//			$total_fc_amount = $Amount_in_FC * $supp_trans->Currency_Amount;

			amount_cell(round2($Amount_in_FC), user_price_dec(), true);
            amount_cell($entered_grn->Unit_Amt); // Unit_Amt
            label_cell($entered_grn->Gross_Amt_new); // Currency rate
            label_cell(price_format($entered_grn->Gross_Amt)); // Gross_Amt
            label_cell(price_format($entered_grn->As_Per_B_E)); // As_Per_B_E
            label_cell(price_format($entered_grn->Landing)); // Landing
           // label_cell($entered_grn->Landing_Amt); // Landing_Amt
            label_cell(price_format($entered_grn->Value_invl_Landing)); // Value_invl_Landing
            label_cell(price_format($entered_grn->INS)); // INS
           // label_cell($entered_grn->INS_Amt); // INS_Amt
            label_cell(price_format($entered_grn->Value_Incl_INC)); // Value_Incl_INC

            label_cell(price_format($entered_grn->F_E_D)); // F.E.D 1%
            //label_cell($entered_grn->F_E_D_Amt); // F.E.D Value
            label_cell(price_format($entered_grn->Duty)); // Duty
           // label_cell($entered_grn->Duty_Amt); // Duty_Amt
            label_cell(price_format($entered_grn->Value_And_Duty)); // Value_And_Duty
            label_cell(price_format($entered_grn->Value_Excl_S_T)); // Value_Excl_S_T
            label_cell(price_format($entered_grn->S_T)); // S_T
          //  label_cell($entered_grn->S_T_Amt); // S_T_Amt
            label_cell(price_format($entered_grn->Amount_Incl_S_T)); // Amount_Incl_S_T ===========
//display_error($entered_grn->S_T);
            label_cell(price_format($entered_grn->I_Tax)); // I/Tax
           // label_cell($entered_grn->I_Tax_Amt); // I_Tax_Amt
            label_cell(price_format($entered_grn->Add_S_T)); // Add_S_T
          //  label_cell($entered_grn->Add_S_T_Amt); // Add_S_T_Amt
            label_cell(price_format(($entered_grn->Total_Charges))); // Total_Charges
//            label_cells(null, $_POST['qwerty']); // Other Expense
//            hidden('qwerty', $_POST['qwerty']);


			$line_taxfree = $entered_grn->taxfree_charge_price_import($supp_trans->tax_group_id);
			$line_tax = $entered_grn->full_charge_price_import($supp_trans->tax_group_id) - $line_taxfree;
			$Grand_Amount_in_FC += $Amount_in_FC;
			$total_net_amount += ($entered_grn->Net_Amount + $line_tax);
			$total_Unit_Amt += ($entered_grn->Unit_Amt);
			$total_B_E += ($entered_grn->As_Per_B_E);
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
			$total_Landing += ($entered_grn->Landing);
			$total_INS+= ($entered_grn->INS);
			$total_F_E_D+= ($entered_grn->F_E_D);
			$total_Duty+= ($entered_grn->Duty);
			$total_S_T+= ($entered_grn->S_T);
			$total_I_Tax+= ($entered_grn->I_Tax);
			$total_Add_S_T+= ($entered_grn->Add_S_T);
			$total_Other_Expenses += ($entered_grn->Other_Expenses);

           // F.E.D 1%
            //label_cell($entered_grn->F_E_D_Amt); // F.E.D Value
            // S.H
			
			$entered_grn->total_Gross_Amt = $total_Gross_Amt;
			if ($entered_grn->Other_Expenses == 0)
                label_cell(price_format($entered_grn->Total_Other_Expense));
			else
                label_cell(price_format($entered_grn->Other_Expenses));
			if ($entered_grn->Other_Expenses == 0)
                label_cell(price_format($entered_grn->Net_Amount + $entered_grn->Total_Other_Expense)); // Net_Amount
			else
                label_cell(price_format($entered_grn->Net_Amount)); // Net_Amount
			label_cell(get_cost_center_name_price($entered_grn->Job_Name)); // Job_Name
			$Unit_Cost = $entered_grn->Net_Amount/$entered_grn->this_quantity_inv;
            label_cell(price_format($Unit_Cost)); // Standard Cost

//			$All_Columns_Total = $F_E_D_Amt + $Amt_FC + $Duty_Amt + $S_T_Amt + $I_Tax_Amt + $Add_S_T_Amt;
//			$STD_Cost = $All_Columns_Total / input_num('this_quantity_inv'.$n);
//			$Net_Amount = $F_E_D_Amt + $Duty_Amt + $S_T_Amt + $I_Tax_Amt + $Add_S_T_Amt + $qwerty + $_POST['Other_Expense'.$n];
//			$STD_Cost = ($total_fc_amount + $entered_grn->F_E_D_Amt +
//				$entered_grn->S_T_Amt + $entered_grn->I_Tax_Amt + $entered_grn->Add_S_T_Amt +
//				$entered_grn->Total_Other_Expenses);
			$Round_Net_Amount = round($entered_grn->Net_Amount);
            //stock_cost_update($entered_grn->item_code, ($Round_Net_Amount/$entered_grn->/////), 0, 0, 0);

//            stock_cost_update($entered_grn->item_code, ($Round_Net_Amount/$entered_grn->this_quantity_inv), 0, 0, 0);
           // update_average_material_cost($_SESSION['supp_trans']->supplier_id, $entered_grn->item_code, $Round_Net_Amount, $entered_grn->this_quantity_inv, $_SESSION['supp_trans']->tran_date, $adj_only=false);
//			display_error($Amount_in_FC ."---". $entered_grn->F_E_D_Amt ."---".
//			$entered_grn->S_T_Amt ."---". $entered_grn->I_Tax_Amt ."---". $entered_grn->Add_S_T_Amt ."---".
//			$entered_grn->this_quantity_inv);
//			display_error($STD_Cost);

			if ($mode == 1)
			{
				delete_button_cell("Delete" . $entered_grn->id, _("Edit"), _('Edit document line'));
				if (($supp_trans->trans_type == ST_SUPPINVOICE) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))
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
//			S.H
			$total_Gross_Amts = $entered_grn->total_Gross_Amt;
		}
		if(isset($_POST['Load_Exp']))
		{
			div_start('Calculation');
			foreach ($supp_trans->grn_items as $line => $entered_grn) {

				if ($_POST['gross_rate'] == 0) {

				$supp_trans->grn_items[$line]->Other_Expense = 1 * $entered_grn->Gross_Amt / $total_Gross_Amts;
			}
			else {
//				display_error(input_num('gross_rate') * $entered_grn->Gross_Amt / $total_Gross_Amts);
//				$supp_trans->grn_items[$line]->Total_Other_Expenses = $_POST['gross_rate']*$entered_grn->Gross_Amt/$total_Gross_Amts;
				$supp_trans->grn_items[$line]->Total_Other_Expense = input_num('gross_rate') * $entered_grn->Gross_Amt / $total_Gross_Amts;

			}
			}
			div_end();
		}


	}
	if ($mode == 1)
	{
		$ret = display_grn_items_for_selection_price_import($supp_trans, $k);
		$colspan = 10;
	}
	else
		$colspan = 31;

//	label_row(_("Total"),price_format($total_net_amount),
//		"colspan=$colspan align=right", "nowrap align=right");
	start_row("class='inquirybg' style='font-weight:bold'");
	label_cell(_(""));
	label_cell(_(""));
	label_cell(_(""));
	label_cell(_(""));
	if($mode == 1){
		label_cell(_(""));
		label_cell(_(""));
		label_cell(_(""));
		label_cell(_(""));
	}


	label_cell(_("Total"));
	amount_cell($Grand_Amount_in_FC);
	amount_cell($total_Unit_Amt); // Unit_Amt
    
label_cell("");
label_cell($total_B_E);
   // label_cell("");
	amount_cell($total_Gross_Amt); // Gross_Amt
	
	label_cell(price_format($total_Landing)); // Landing
	//amount_cell($total_Landing_Amt); // Landing_Amt
	amount_cell($total_Value_invl_Landing); // Value_invl_Landing
    label_cell(price_format($total_INS));
  //  amount_cell(_($total_INS_Amtt)); // INS
	//label_cell(""); // INS_Amt
    label_cell(price_format($total_Value_Incl_INC)); // Value_Incl_INC
	label_cell(price_format($total_F_E_D)); // F.E.D 1%
label_cell(price_format($total_Duty));
	//amount_cell($total_F_E_D_Amt); // F.E.D Value
	//label_cell(_("")); // Duty
	//amount_cell($total_Duty_Amt); // Duty_Amt
	amount_cell($total_Value_And_Duty); // Value_And_Duty
	label_cell(number_format2($total_exc,$dec)); // Value_Excl_S_T
	//label_cell(_("")); // S_T
//label_cell(_(""));	
//amount_cell($total_S_T_Amt); // S_T_Amt
	//amount_cell($total_Amount_Incl_S_T); // Amount_Incl_S_T
	//label_cell(_("")); // I/Tax
	
//amount_cell($total_I_Tax_Amt); // I_Tax_Amt
	label_cell(price_format($total_S_T)); // Add_S_T
	amount_cell($total_Add_S_T_Amt); // Add_S_T_Amt
label_cell(price_format($total_I_Tax));
label_cell(price_format($total_Add_S_T));

    amount_cell($total_Total_Charges); // Total_Charges
    label_cell(price_format($total_Other_Expenses)); // other



	amount_cell($total_net_amount); // Net_Amount
	if (!$ret)
	{
		start_row();
		echo "<td colspan=".($colspan + 1).">";
		if ($supp_trans->trans_type == ST_SUPPINVOICE)
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
function get_duedate_from_terms_price_import(&$trans)
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