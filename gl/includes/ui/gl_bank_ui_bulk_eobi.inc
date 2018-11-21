<?php
function get_employee_names($employee_id)
{
    $sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_divsion_prefix($division)
{
    $sql = "SELECT prefix FROM ".TB_PREF."dimensions WHERE 	id=".db_escape($division) ."AND type_=1";
    $result = db_query($sql, "could not get supplier");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_p_v_prefix($division)
{
    $sql = "SELECT prefix FROM ".TB_PREF."reflines WHERE  trans_type=1";
    $result = db_query($sql, "could not get supplier");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_max_serial($date,$month,$year)
{
    $date11 = "$year-".$month."-01";
    $sql = "SELECT MAX(serial) FROM ".TB_PREF."bank_trans WHERE trans_date>=".db_escape($date11) ." AND trans_date<=".db_escape($date) ." AND type=1";
    $result = db_query($sql, "could not get supplier");
    $row = db_fetch_row($result);
    return $row[0];
}
function display_bank_header_bulk(&$order)
{
	global $Ajax, $Refs;
	$payment = $order->trans_type == ST_BANKPAYMENT;

	$customer_error = false;
	div_start('pmt_header');

	start_outer_table(TABLESTYLE2, "width=90%"); // outer table

	table_section(1);
	
    date_row(_("Date:"), 'date_', '', true, 0, 0, 0, null, true);
    $month = date('m', strtotime(date2sql($_POST['date_'])));
    $year = date('Y', strtotime(date2sql($_POST['date_'])));
    $ser=get_max_serial(date2sql($_POST['date_']),$month,$year);
    if($ser!=0)
        $serial =$ser+1;
    else{
        $serial =1;
    }
	ref_row(_("Reference:"), 'ref', '');
    dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
    $ref='BPV/'.'00'.$serial.'/'.get_divsion_prefix($_POST['division']).'/'.$month.'/'.$year;
    text_row(_("Reference2:"), 'reference2', $ref);
    //text_row(_("Serial:"), 'serial', '');
    hidden('serial', $serial);
    pro_list_row(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
    loc_list_row(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);
    month_list_row( "Month", 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
   // salary_list_row(_("Mode Of Salary Payment"),'mod_payment',null,false);
    submit_cells('getitems', _("Fetch History"), "colspan=2 align='center'",
        _('Add new item to document'), true);
/*	text_row("Cheque No.", 'cheque_no', null);

	date_row(_("Cheque Date:"), 'cheque_date', '', true, 0, 0, 0, null, true);*/

	table_section(2, "33%");

	if (!isset($_POST['PayType']))
	{
		if (isset($_GET['PayType']))
			$_POST['PayType'] = $_GET['PayType'];
		else
			$_POST['PayType'] = "";
	}
	if (!isset($_POST['person_id']))
	{
		if (isset($_GET['PayPerson']))
			$_POST['person_id'] = $_GET['PayPerson'];
		else
			$_POST['person_id'] = "";
	}
	if (isset($_POST['_PayType_update'])) {
		$_POST['person_id'] = '';
		$Ajax->activate('pmt_header');
		$Ajax->activate('code_id');
		$Ajax->activate('pagehelp');
		$Ajax->activate('editors');
	}
    payment_person_types_list_row( $payment ? _("Pay To:"):_("From:"),
		 'PayType', $_POST['PayType'], true);

text_cells_ex("Cheque No", 'cheque_no_line', 15, 25);
date_row("Cheque Date",'cheque_date_line');


  /*  switch ($_POST['PayType'])
    {
		case PT_MISC :
    		text_row_ex($payment ?_("To the Order of:"):_("Name:"),
				 'person_id', 40, 50);
    		break;
		//case PT_WORKORDER :
    	//	workorders_list_row(_("Work Order:"), 'person_id', null);
    	//	break;
		case PT_SUPPLIER :
    		supplier_list_row(_("Supplier:"), 'person_id', null, false, true, false, true);
    		break;
		case PT_CUSTOMER :
    		customer_list_row(_("Customer:"), 'person_id', null, false, true, false, true);

        	if (db_customer_has_branches($_POST['person_id']))
        	{
        		customer_branches_list_row(_("Branch:"), $_POST['person_id'], 
					'PersonDetailID', null, false, true, true, true);
        	}
        	else
        	{
				$_POST['PersonDetailID'] = ANY_NUMERIC;
        		hidden('PersonDetailID');
        	}
        	$trans = get_customer_habit($_POST['person_id']); // take care of customers on hold
        	if ($trans['dissallow_invoices'] != 0)
        	{
        		if ($payment)
        		{
        			$customer_error = true;
					display_error(_("This customer account is on hold."));
        		}
        		else			
					display_warning(_("This customer account is on hold."));
        	}		
    		break;

		case PT_QUICKENTRY :
			quick_entries_list_row(_("Type").":", 'person_id', null, ($payment ? QE_PAYMENT : QE_DEPOSIT), true);
			$qid = get_quick_entry(get_post('person_id'));
			if (list_updated('person_id')) {
				unset($_POST['totamount']); // enable default
				$Ajax->activate('totamount');
			}
			amount_row($qid['base_desc'].":", 'totamount', price_format($qid['base_amount']),
				 null, "&nbsp;&nbsp;".submit('go', _("Go"), false, false, true));
			break;	
		//case payment_person_types::Project() :
    	//	dimensions_list_row(_("Cost Centre:"), 'person_id', $_POST['person_id'], false, null, true);
    	//	break;
    }*/

	table_section(3, "33%");

	if (!$order->order_id && !list_updated('bank_account'))
	{
		if ($_POST['PayType'] == PT_CUSTOMER)
			$_POST['bank_account'] = get_default_customer_bank_account($_POST['person_id']);
		elseif ($_POST['PayType'] == PT_SUPPLIER)	
			$_POST['bank_account'] = get_default_supplier_bank_account($_POST['person_id']);
		else
			unset($_POST['bank_account']);
	}		
    
    bank_accounts_list_row( $payment ? _("From:") : _("Into:"), 'bank_account', null, true,$_POST['division']);
    if ($payment)
		bank_balance_row($_POST['bank_account']);
    	
	$person_currency = payment_person_currency($_POST['PayType'], $_POST['person_id']);
	$bank_currency = get_bank_account_currency($_POST['bank_account']);

	exchange_rate_display($bank_currency, $person_currency, $_POST['date_']);

	end_outer_table(1); // outer table
	
	div_end();
	if ($customer_error)
	{
		end_form();
		end_page();
		exit;
	}
}
//---------------------------------------------------------------------------------

function display_gl_items_bulk($title, &$order)
{
	global $path_to_root;

	$dim = get_company_pref('use_dimension');
	$colspan = ($dim == 2 ? 4 : ($dim == 1 ? 3 : 2));
	display_heading($title);

    div_start('items_table');
	start_table(TABLESTYLE, "colspan=7 width=95%");

	/*if ($dim == 2)
		$th = array(_("Account Code"), _("Account Description"), _("Cost Centre")." 1",
			_("Cost Centre")." 2", _("Amount"), _("Memo"), "");
	else if ($dim == 1)
		$th = array(_("Account Code"), _("Account Description"), _("Cost Centre"),
			_("Amount"), _("Memo"), "");
	else*/


        $th = array(_("Account Code"), _("Account Description"), _("Division"),
            _("Project"), _("Location"),
            _("Amount"), _("Memo"), "");




	if (count($order->gl_items)) $th[] = '';

	table_header($th);
	$k = 0;  //row colour counter

	$id = find_submit('Edit');
	foreach ($order->gl_items as $line => $item)
	{
	    $_POST['count']=count($order->gl_items );
		if ($id != $line)
		{
    		alt_table_row_color($k);
            if ($_POST['PayType'] == '3'){



                label_cell(get_employee_names($item->person_id));


            }
elseif ($_POST['PayType'] == '0'){



}


        else {

               label_cell(get_customer_name($item->person_id));
                label_cell(get_branch_name($item->PersonDetailID));

            }




			label_cell($item->code_id);
			label_cell($item->description);
    		//if ($dim >= 1)
				label_cell(get_dimension_string($item->dimension_id, true));
    		//if ($dim > 1)
				label_cell(get_dimension_string($item->dimension2_id, true));
            label_cell(get_dimension_string($item->dimension3_id, true));
			//amount_cell(abs($item->amount));
			if ($order->trans_type == ST_BANKDEPOSIT)
				amount_cell(-$item->amount);
			else		
				amount_cells_ex($item->amount);
           // amount_cells_ex("",$item->amount, '','','','',$item->amount, 15);
			//label_cell($item->reference);

			//label_cell($item->cheque_no_line);
			//label_cell($item->cheque_date_line);

			edit_button_cell("Edit$line", _("Edit"),
				_('Edit document line'));
			delete_button_cell("Delete$line", _("Delete"),
				_('Remove line from document'));
    		end_row();
		}
		else
		{
			gl_edit_item_controls($order, $dim, $line);
		}
	}

	if ($id == -1)
		gl_edit_item_controls($order, $dim);

	if ($order->count_gl_items())
		label_row(_("Total"), number_format2(abs($order->gl_items_total()), user_price_dec()),"colspan=" . $colspan . " align=right", "align=right",3);

    end_table();
	div_end();
}

//---------------------------------------------------------------------------------

function gl_edit_item_controls(&$order, $dim, $Index=null)
{
	global $Ajax;
	$payment = $order->trans_type == ST_BANKPAYMENT;

	start_row();
	$id = find_submit('Edit');



    if ($_POST['PayType'] == '3'){
        echo '<td>';
        emp_list_cells(null, 'person_id', null, false, true, true,  true);
       // emp_list_cells(_(" "), 'person_id', null,
         //   _('New Employee'), true, check_value('show_inactive'));



    }
	elseif($_POST['PayType'] == '0'){



	}

	else {
        customer_list_cells(null, 'person_id', null, false, true, false, true);
         customer_branches_list_cells(null, $_POST['person_id'],'PersonDetailID', null, false, true, true, true);



    }


	if ($Index != -1 && $Index == $id)
	{
        $division = get_post('division','');
        $project = get_post('project','');
        $location = get_post('location','');
        $month= get_post('month','');

		$item = $order->gl_items[$Index];
		$_POST['code_id'] = $item->code_id;
		$_POST['dimension_id'] = $item->dimension_id;
		$_POST['dimension2_id'] = $item->dimension2_id;
        $_POST['dimension3_id'] = $item->dimension3_id;
		$_POST['amount'] = price_format(abs($item->amount));
		$_POST['description'] = $item->description;
		$_POST['LineMemo'] = $item->reference;
		$_POST['person_id'] = $item->person_id;
		$_POST['PersonDetailID'] = $item->PersonDetailID;
		$_POST['cheque_no_line'] = $item->cheque_no_line;
		$_POST['cheque_date_line'] = $item->cheque_date_line;

		hidden('Index', $id);
        hidden('dimension_id', $division);
        hidden('dimension2_id', $project);
        hidden('dimension3_id', $location);
        hidden('month', $month);
        echo gl_all_accounts_list('code_id', $_POST['code_id'], true, true,false,false,false,$division);
        label_cell(get_dimension_string($division, true));
        //if ($dim > 1)
        label_cell(get_dimension_string($project, true));
        label_cell(get_dimension_string($location, true));
	    $Ajax->activate('items_table');
	}
	else
	{
		$_POST['amount'] = price_format(0);
		$_POST['dimension_id'] = 0;
		$_POST['dimension2_id'] = 0;
        $_POST['dimension3_id'] = 0;
		//$_POST['LineMemo'] = ""; // let memo go to next line Joe Hunt 2010-05-30
		if(isset($_POST['_code_id_update'])) {
			    $Ajax->activate('code_id');
		}

		if ($_POST['PayType'] == PT_CUSTOMER)
		{
			$acc = get_branch_accounts($_POST['PersonDetailID']);
			$_POST['code_id'] = $acc['receivables_account'];
		}
		elseif ($_POST['PayType'] == PT_SUPPLIER)
		{
			$acc = get_supplier_accounts($_POST['person_id']);
			$_POST['code_id'] = $acc['payable_account'];
		}
		//elseif ($_POST['PayType'] == PT_WORKORDER)
		//	$_POST['code_id'] = get_company_pref('default_assembly_act');
		else {
			$_POST['code_id'] =
				get_company_pref($payment ? 'default_cogs_act':'default_inv_sales_act');
		}

        $division = get_post('division','');
        $project = get_post('project','');
        $location = get_post('location','');
        echo gl_all_accounts_list('code_id', $_POST['code_id'], true, true,false,false,false,$division);
        label_cell(get_dimension_string($division, true));
        //if ($dim > 1)
        label_cell(get_dimension_string($project, true));
        label_cell(get_dimension_string($location, true));
	}
    $division = get_post('division','');
    $project = get_post('project','');
    $location = get_post('location','');
    //if ($dim < 1)
    hidden('dimension_id', $division);
    //if ($dim < 2)
    hidden('dimension2_id', $project);
    hidden('dimension3_id', $location);

	amount_cells(null, 'amount');
	text_cells_ex(null, 'LineMemo', 35, 255);
	//if($order->trans_type == 2)
		//text_cells_ex(null, 'cheque_no_line', 15, 25);
	//display_error($order->trans_type);
	//if($order->trans_type == 1)
	//	cheque_list_cells(null, 'cheque_no_line', $_POST['cheque_no_line'], true);


	//date_cells(null,'cheque_date_line');

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
 		set_focus('amount');
	}
	else
	{
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}


//---------------------------------------------------------------------------------

function gl_options_controls()
{
	echo "<br><table align='center'>";

	textarea_row(_("Memo"), 'memo_', null, 50, 3);

  	echo "</table>";
}


//---------------------------------------------------------------------------------

?>