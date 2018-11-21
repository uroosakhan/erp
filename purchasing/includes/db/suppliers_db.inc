<?php


function add_supplier($supp_name, $supp_ref, $address, $supp_address, $gst_no,$ntn_no,
	$website, $supp_account_no, $bank_account, $credit_limit, $dimension_id, $dimension2_id, 
	$curr_code, $payment_terms, $payable_account, $purchase_account, $payment_discount_account, 
	$notes, $tax_group_id, $tax_included,$service_text,$combo1)
{
	$sql = "INSERT INTO ".TB_PREF."suppliers (supp_name, supp_ref, address, supp_address, gst_no,ntn_no, website,
		supp_account_no, bank_account, credit_limit, dimension_id, dimension2_id, curr_code,
		payment_terms, payable_account, purchase_account, payment_discount_account, notes, 
		tax_group_id, tax_included, service_text,combo1)
		VALUES (".db_escape($supp_name). ", "
		.db_escape($supp_ref). ", "
		.db_escape($address) . ", "
		.db_escape($supp_address) . ", "
		.db_escape($gst_no). ", "
		.db_escape($ntn_no). ", "
		.db_escape($website). ", "
		.db_escape($supp_account_no). ", "
		.db_escape($bank_account). ", "
		.$credit_limit. ", "
		.db_escape($dimension_id). ", "
		.db_escape($dimension2_id). ", "
		.db_escape($curr_code). ", "
		.db_escape($payment_terms). ", "
		.db_escape($payable_account). ", "
		.db_escape($purchase_account). ", "
		.db_escape($payment_discount_account). ", "
		.db_escape($notes). ", "
		.db_escape($tax_group_id). ", "
		.db_escape($tax_included). ", "
        .db_escape($service_text). ", "
		.db_escape($combo1). ")";

	db_query($sql,"The supplier could not be added");
}

function update_supplier($supplier_id, $supp_name, $supp_ref, $address, $supp_address, $gst_no, $ntn_no,
	$website, $supp_account_no, $bank_account, $credit_limit, $dimension_id, $dimension2_id, 
	$curr_code, $payment_terms, $payable_account, $purchase_account, $payment_discount_account, 
	$notes, $tax_group_id, $tax_included,$service_text,$combo1)
{
	$sql = "UPDATE ".TB_PREF."suppliers SET supp_name=".db_escape($supp_name) . ",
		supp_ref=".db_escape($supp_ref) . ",
		address=".db_escape($address) . ",
		supp_address=".db_escape($supp_address) . ",
		gst_no=".db_escape($gst_no) . ",
		ntn_no=".db_escape($ntn_no) . ",
		website=".db_escape($website) . ",
		supp_account_no=".db_escape($supp_account_no) . ",
		bank_account=".db_escape($bank_account) . ",
		credit_limit=".$credit_limit . ",
		dimension_id=".db_escape($dimension_id) . ",
		dimension2_id=".db_escape($dimension2_id) . ",
		curr_code=".db_escape($curr_code).",
		payment_terms=".db_escape($payment_terms) . ",
		payable_account=".db_escape($payable_account) . ",
		purchase_account=".db_escape($purchase_account) . ",
		payment_discount_account=".db_escape($payment_discount_account) . ",
		notes=".db_escape($notes) . ",
		tax_group_id=".db_escape($tax_group_id). ",
		tax_included=".db_escape($tax_included). ",
		tax_included=".db_escape($tax_included). ",
		service_text=".db_escape($service_text). ",
		combo1=".db_escape($combo1)
		." WHERE supplier_id = ".db_escape($supplier_id);

	db_query($sql,"The supplier could not be updated");
}

function delete_supplier($supplier_id)
{
	$sql="DELETE FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($supplier_id);
	db_query($sql,"check failed");
}

function get_supplier_details($supplier_id, $to=null, $all=true)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - supp_trans.alloc from all summations

    if ($all)
        $value = "(trans.ov_amount + trans.ov_gst + trans.ov_discount- ( supply_disc + service_disc + fbr_disc +  srb_disc)  )";
    else
        $value = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_BANKDEPOSIT." OR trans.type=".ST_CRV."
  	OR (trans.type=".ST_JOURNAL." AND (trans.ov_amount + trans.ov_gst + trans.ov_discount)>0), 1, -1)*
    		ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount) - trans.alloc)";
	$due = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_SUPPCREDIT." OR trans.type=".ST_SUPPCREDIT_IMPORT.",trans.due_date,trans.tran_date)";
    $sql = "SELECT supp.supp_name, supp.curr_code, ".TB_PREF."payment_terms.terms,

		Sum(IFNULL($value,0)) AS Balance,

		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2,
		supp.credit_limit - Sum(IFNULL(IF(trans.type=".ST_SUPPCREDIT.", -1, 1) 
			* (ov_amount + ov_gst + ov_discount),0)) as cur_credit,
		supp.tax_group_id

		FROM ".TB_PREF."suppliers supp
			 LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id AND trans.tran_date <= '$todate',
			 ".TB_PREF."payment_terms

		WHERE
			 supp.payment_terms = ".TB_PREF."payment_terms.terms_indicator
			 AND supp.supplier_id = $supplier_id 
			 	 AND trans.type NOT IN(".ST_SUPPCREDIT_IMPORT.")";
	if (!$all)
		$sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount) - trans.alloc > ".FLOAT_COMP_DELTA." ";  
	$sql .= "GROUP BY
			  supp.supp_name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month";
    $result = db_query($sql,"The customer details could not be retrieved");
    $supp = db_fetch($result);

    return $supp;
}

function get_supplier($supplier_id)
{
	$sql = "SELECT * FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($supplier_id);

	$result = db_query($sql, "could not get supplier");

	return db_fetch($result);
}

function get_supplier_name($supplier_id)
{
	$sql = "SELECT supp_name AS name FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($supplier_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_u_name($employee_id)
{
    $sql = "SELECT emp_name AS name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_supplier_accounts($supplier_id)
{
	$sql = "SELECT payable_account,purchase_account,payment_discount_account FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($supplier_id);

	$result = db_query($sql, "could not get supplier");

	return db_fetch($result);
}

function get_supplier_contacts($supplier_id, $action=null)
{
	$results = array();
	$res = get_crm_persons('supplier', $action, $supplier_id);
	while($contact = db_fetch($res))
		$results[] = $contact;

	return $results;
}

function get_current_supp_credit($supplier_id)
{
	$suppdet = get_supplier_details($supplier_id);
	return $suppdet['cur_credit'];

}

function is_new_supplier($id)
{
	$tables = array('supp_trans', 'grn_batch', 'purch_orders', 'purch_data');

	return !key_in_foreign_table($id, $tables, 'supplier_id');
}

function get_supplier_currency($supplier_id)
{
    $sql = "SELECT curr_code FROM ".TB_PREF."suppliers WHERE supplier_id = ".db_escape($supplier_id);

	$result = db_query($sql, "Retreive currency of supplier $supplier_id");

	$myrow=db_fetch_row($result);
	return $myrow[0];
}

function get_employee_currency($employee_id)
{
    $sql = "SELECT curr_code FROM ".TB_PREF."employee WHERE employee_id = ".db_escape($employee_id);

    $result = db_query($sql, "Retreive currency of supplier $employee_id");

    $myrow=db_fetch_row($result);
    return $myrow[0];
}

function get_suppliers_search($supplier)
{
   global $SysPrefs;

   if (isset($SysPrefs->max_rows_in_search))
      $limit = $SysPrefs->max_rows_in_search;
   else
      $limit = 10;
    if($SysPrefs->hide_stock_list() == 0) {
        $sql = "SELECT supplier_id, supp_name, supp_ref, address, gst_no
      FROM " . TB_PREF . "suppliers
      WHERE (supp_name LIKE " . db_escape("%" . $supplier . "%") . " OR 
         supp_ref LIKE " . db_escape("%" . $supplier . "%") . " OR 
         address LIKE " . db_escape("%" . $supplier . "%") . " OR 
         gst_no LIKE " . db_escape("%" . $supplier . "%") . ")
      ORDER BY supp_name LIMIT 0," . (int)($limit);
    }
    else
    {
        $sql = "SELECT supplier_id, supp_name, supp_ref, address, gst_no
      FROM " . TB_PREF . "suppliers
      WHERE (supp_name LIKE " . db_escape("%" . $supplier . "%") . ")
      ORDER BY supp_name";
    }

   return db_query($sql, "Failed in retreiving supplier list.");
}

//----------------work start------------------
function get_gl_account1($purc_acc)
{
	$sql = "SELECT CONCAT(account_code,'-',account_name) AS name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($purc_acc['purchase_account']);

	$result = db_query($sql, "could not get gl account");
	$row= db_fetch_row($result);
	return $row[0];
}

function get_gl_account2($pay_acc)
{
	$sql = "SELECT CONCAT(account_code,'-',account_name) AS name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($pay_acc['payable_account']);

	$result = db_query($sql, "could not get gl account");
	$row= db_fetch_row($result);
	return $row[0];
}

function get_gl_account3($paym_disc_acc)
{
	$sql = "SELECT CONCAT(account_code,'-',account_name) AS name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($paym_disc_acc['payment_discount_account']);

	$result = db_query($sql, "could not get gl account");
	$row= db_fetch_row($result);
	return $row[0];
}

function get_tax_included($row)
{
	if ($row["tax_included"] == 0)
	{
		$a='YES';
	}
	else
	{
		$a='NO';
	}
	return $a;
}
///////for supplier_dashboard-------------------------
function  get_sql_for_supplier_log_view( $supp_name, $supp_ref, $address, $supp_address, $ntn_no, $gst_no,
										 $contact, $supp_account_no, $website, $bank_account, $curr_code='',
										 $payment_terms, $tax_included, $tax_group_id, $credit_limit,
										 $purchase_account, $payable_account, $payment_discount_account,
										 $dimension_id, $dimension2_id, $notes)
{

	$sql = " SELECT
			
			s.supp_name,
			s.supp_ref,
			s.address,
			s.supp_address,	
			s.ntn_no,
			s.gst_no,
			s.contact,
			s.supp_account_no,
			s.website,
			s.bank_account,
			s.curr_code,
			s.payment_terms,
			s.tax_included,
			s.tax_group_id,	
			s.credit_limit,
			s.purchase_account,	
			s.payable_account,	
			s.payment_discount_account,			
			s.dimension_id,	
			s.dimension2_id,	
			s.notes,
			s.supplier_id
				
		
		FROM ".TB_PREF."suppliers as s
		
		WHERE s.supplier_id != 0";

	if ($supp_name != "")
	{
		$number_like = "%".$supp_name."%";
		$sql .= " AND s.supp_name LIKE ".db_escape($number_like);
	}

	if ($supp_ref != "")
	{
		$number_like = "%".$supp_ref."%";
		$sql .= " AND s.supp_ref LIKE ".db_escape($number_like);
	}

	if ($address != "")
	{
		$number_like = "%".$address."%";
		$sql .= " AND s.address LIKE ".db_escape($number_like);
	}

	if ($supp_address != "")
	{
		$number_like = "%".$supp_address."%";
		$sql .= " AND s.supp_address LIKE ".db_escape($number_like);
	}

	if ($ntn_no != "")
	{
		$number_like = "%".$ntn_no."%";
		$sql .= " AND s.ntn_no LIKE ".db_escape($number_like);
	}

	if ($gst_no != "")
	{
		$number_like = "%".$gst_no."%";
		$sql .= " AND s.gst_no LIKE ".db_escape($number_like);
	}

	if ($contact != "")
	{
		$number_like = "%".$contact."%";
		$sql .= " AND s.contact LIKE ".db_escape($number_like);
	}

	if ($supp_account_no != "")
	{
		$number_like = "%".$supp_account_no."%";
		$sql .= " AND s.supp_account_no LIKE ".db_escape($number_like);
	}

	if ($website != "")
	{
		$number_like = "%".$website."%";
		$sql .= " AND s.website LIKE ".db_escape($number_like);
	}

	if ($bank_account != "")
	{
		$number_like = "%".$bank_account."%";
		$sql .= " AND s.bank_account LIKE ".db_escape($number_like);
	}

	if ($curr_code != 0)
	{
		$sql .= " AND s.curr_code = ".db_escape($curr_code);
	}

	if ($payment_terms != "")
	{
		$sql .= " AND s.payment_terms = ".db_escape($payment_terms);
	}

	if ($tax_group_id != "")
	{
		$sql .= " AND s.tax_group_id = ".db_escape($tax_group_id);
	}

	if ($dimension_id != "")
	{
		$sql .= " AND s.dimension_id = ".db_escape($dimension_id);
	}

	if ($dimension2_id != "")
	{
		$sql .= " AND s.dimension2_id = ".db_escape($dimension2_id);
	}

	$sql .= " GROUP BY s.supplier_id";

	return $sql;
}

function get_credit_balance_supplier($supplier_id)
{
    $sql = "SELECT Sum(IFNULL(IF(trans.type IN(".implode(',', array(ST_SUPPCREDIT, ST_SUPPAYMENT, ST_BANKPAYMENT, ST_CPV))."), 1, 1) 
            * (ov_amount + ov_gst + ov_discount),0))-  supp.credit_limit  as cur_credit
      FROM ".TB_PREF."suppliers supp
          LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id
          LEFT JOIN ".TB_PREF."payment_terms terms ON supp.payment_terms=terms.terms_indicator
      WHERE supp.supplier_id = ".db_escape($supplier_id)."
      
      GROUP BY
           supp.supp_name";

    $result = db_query($sql, "The supplier details could not be retreived");
    $fetch = db_fetch($result);
    return $fetch['cur_credit'];
}