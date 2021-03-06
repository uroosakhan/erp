<?php
//
//	FIXME - this should be revised for transaction update case.
//
function write_supp_trans($type, $trans_no, $supplier_id, $date_, $due_date, $reference, $supp_reference,
	$amount, $amount_tax, $gst_wh, $discount, $err_msg="", $rate=0, $included=0)
{
	$new = $trans_no==0;
	$date = date2sql($date_);
	if ($due_date == "")
		$due_date = "0000-00-00";
	else
		$due_date = date2sql($due_date);

	if ($new)
		$trans_no = get_next_trans_no($type);

	$curr = get_supplier_currency($supplier_id);
	
	if ($rate == 0)
		$rate = get_exchange_rate_from_home_currency($curr, $date_);


	$sql = "INSERT INTO ".TB_PREF."supp_trans (trans_no, type, supplier_id, tran_date, due_date,
		reference, supp_reference, ov_amount, ov_gst, rate, gst_wh, ov_discount, tax_included) ";
	$sql .= "VALUES (".db_escape($trans_no).", ".db_escape($type)
	.", ".db_escape($supplier_id).", '$date', '$due_date',
		".db_escape($reference).", ".db_escape($supp_reference).", ".db_escape($amount)
		.", ".db_escape($amount_tax).", ".db_escape($rate).", ".db_escape($gst_wh).", ".db_escape($discount).", ".db_escape($included).")";

	if ($err_msg == "")
		$err_msg = "Cannot insert a supplier transaction record";

	db_query($sql, $err_msg);
	add_audit_trail($type, $trans_no, $date_);

	return $trans_no;
}

//-------------------------------------------------------------------------------------------------------------

function get_supp_trans($trans_no, $trans_type=-1)
{
	$sql = "SELECT ".TB_PREF."supp_trans.*, (".TB_PREF."supp_trans.ov_amount+".TB_PREF."supp_trans.ov_gst+".TB_PREF."supp_trans.ov_discount+".TB_PREF."supp_trans.gst_wh) AS Total,
		".TB_PREF."suppliers.supp_name AS supplier_name, ".TB_PREF."suppliers.curr_code AS SupplierCurrCode ";

	if ($trans_type == ST_SUPPAYMENT)
	{
		// it's a payment so also get the bank account
		$sql .= ", ".TB_PREF."bank_accounts.bank_name, ".TB_PREF."bank_accounts.bank_account_name, ".TB_PREF."bank_accounts.bank_curr_code,
			".TB_PREF."bank_accounts.account_type AS BankTransType, ".TB_PREF."bank_trans.amount AS BankAmount,
			".TB_PREF."bank_trans.ref ";
	}

	$sql .= " FROM ".TB_PREF."supp_trans, ".TB_PREF."suppliers ";

	if ($trans_type == ST_SUPPAYMENT)
	{
		// it's a payment so also get the bank account
		$sql .= ", ".TB_PREF."bank_trans, ".TB_PREF."bank_accounts";
	}

	$sql .= " WHERE ".TB_PREF."supp_trans.trans_no=".db_escape($trans_no)."
		AND ".TB_PREF."supp_trans.supplier_id=".TB_PREF."suppliers.supplier_id";

	if ($trans_type > 0)
		$sql .= " AND ".TB_PREF."supp_trans.type=".db_escape($trans_type);

	if ($trans_type == ST_SUPPAYMENT)
	{
		// it's a payment so also get the bank account
		$sql .= " AND ".TB_PREF."bank_trans.trans_no =".db_escape($trans_no)."
			AND ".TB_PREF."bank_trans.type=".db_escape($trans_type)."
			AND ".TB_PREF."bank_accounts.id=".TB_PREF."bank_trans.bank_act ";
	}

	$result = db_query($sql, "Cannot retreive a supplier transaction");

    if (db_num_rows($result) == 0)
    {
       // can't return nothing
       display_db_error("no supplier trans found for given params", $sql, true);
       exit;
    }

    if (db_num_rows($result) > 1)
    {
       // can't return multiple
       display_db_error("duplicate supplier transactions found for given params", $sql, true);
       exit;
    }

    return db_fetch($result);
}

//----------------------------------------------------------------------------------------

function exists_supp_trans($type, $type_no)
{
	if ($type == ST_SUPPRECEIVE)
		return exists_grn($type_no);

	$sql = "SELECT trans_no FROM ".TB_PREF."supp_trans WHERE type=".db_escape($type)."
		AND trans_no=".db_escape($type_no);
	$result = db_query($sql, "Cannot retreive a supplier transaction");

    return (db_num_rows($result) > 0);
}

//----------------------------------------------------------------------------------------

function void_supp_trans($type, $type_no)
{
	$sql = "UPDATE ".TB_PREF."supp_trans SET ov_amount=0, gst_wh=0, ov_discount=0, ov_gst=0,
		alloc=0 WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

	db_query($sql, "could not void supp transactions for type=$type and trans_no=$type_no");
}

//----------------------------------------------------------------------------------------

function clear_supp_trans($type, $type_no)
{
	$sql = "DELETE FROM ".TB_PREF."supp_trans 
			WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

	db_query($sql, "could not clear supp transactions for type=$type and trans_no=$type_no");
}
//----------------------------------------------------------------------------------------

function post_void_supp_trans($type, $type_no)
{
	if ($type == ST_SUPPAYMENT)
	{
		void_supp_payment($type, $type_no);
		return true;
	}

	if ($type == ST_SUPPINVOICE || $type == ST_SUPPCREDIT)
	{
	 	void_supp_invoice($type, $type_no);
		return true;
	}

	if ($type == ST_SUPPRECEIVE)
	{
	 	return void_grn($type_no);
	}

	return false;
}

//----------------------------------------------------------------------------------------

function get_sql_for_supplier_inquiry($filter, $after_date, $to_date, $supplier_id=ALL_TEXT)
{
    $date_after = date2sql($after_date);
    $date_to = date2sql($to_date);

    $sql = "SELECT trans.type, 
		trans.trans_no,
		trans.reference, 
		supplier.supp_name, 
		trans.supp_reference,
    	trans.tran_date, 
		trans.due_date,
		supplier.curr_code, 
    	(trans.ov_amount + trans.ov_gst  + trans.gst_wh + trans.ov_discount) AS TotalAmount, 
		trans.alloc AS Allocated,
		((trans.type = ".ST_SUPPINVOICE." OR trans.type = ".ST_SUPPCREDIT.") AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue,
    	(ABS(trans.ov_amount + trans.ov_gst  + trans.gst_wh + trans.ov_discount) - trans.alloc <= ".FLOAT_COMP_DELTA.") AS Settled
    	FROM ".TB_PREF."supp_trans as trans, ".TB_PREF."suppliers as supplier
    	WHERE supplier.supplier_id = trans.supplier_id
     	AND trans.tran_date >= '$date_after'
    	AND trans.tran_date <= '$date_to'
		AND trans.ov_amount != 0";	// exclude voided transactions

	$sql2 = "SELECT ".ST_SUPPRECEIVE." as type, 
		trans.id as trans_no,
		trans.reference, 
		supplier.supp_name, 
		'' as supp_reference,
    	delivery_date as tran_date, 
		'' as due_date,
		supplier.curr_code, 
    	'' AS TotalAmount,
		'' AS Allocated,
		0 as OverDue,
    	1 as Settled
    	FROM ".TB_PREF."grn_batch as trans, ".TB_PREF."suppliers as supplier
    	WHERE supplier.supplier_id = trans.supplier_id
     	AND trans.delivery_date >= '$date_after'
    	AND trans.delivery_date <= '$date_to'";

   	if ($supplier_id != ALL_TEXT) {
   		$sql .= " AND trans.supplier_id = ".db_escape($supplier_id);
   		$sql2 .= " AND trans.supplier_id = ".db_escape($supplier_id);
	}
	if (($filter == '6')) 
	{
			$sql = $sql2;
	} 
	elseif (!isset($filter) || $filter == ALL_TEXT || $filter == '6') {
		$sql = "SELECT * FROM (($sql) UNION ($sql2)) as tr";
   	}

   	if (isset($filter) && $filter != ALL_TEXT)
   	{
   		if (($filter == '1')) 
   		{
   			$sql .= " AND (trans.type = ".ST_SUPPINVOICE." OR trans.type = ".ST_BANKDEPOSIT.")";
   		} 
   		elseif (($filter == '2')) 
   		{
   			$sql .= " AND trans.type = ".ST_SUPPINVOICE." ";
   		} 
   		elseif ($filter == '3') 
   		{
			$sql .= " AND (trans.type = ".ST_SUPPAYMENT." OR trans.type = ".ST_BANKPAYMENT.") ";
   		} 
   		elseif (($filter == '4') || ($filter == '5')) 
   		{
			$sql .= " AND trans.type = ".ST_SUPPCREDIT."  ";
   		}

   		if (($filter == '2') || ($filter == '5')) 
   		{
   			$today =  date2sql(Today());
			$sql .= " AND trans.due_date < '$today' ";
   		}
   	}
   	return $sql;
}


function get_sql_for_employee_inquiry($employee_id, $emp_dept, $emp_grade, $emp_desig)
{
        $sql = "SELECT 
	emp_code, emp_name, emp_father, emp_cnic, DOB, j_date, l_date ,emp_reference, emp_home_phone, emp_mobile, emp_email,emp_bank, emp_address, notes,emp_title,emp_gen,emp_dept, emp_desig,emp_grade
	
    	FROM ".TB_PREF."employee";
    //	WHERE employee.employee_id = ".db_escape($employee_id);
	
	if ($employee_id != ALL_TEXT || $emp_dept != ALL_TEXT || $emp_grade != ALL_TEXT) 
	{
   		$sql .= " WHERE ".TB_PREF."employee.employee_id = ".db_escape($employee_id);
		$sql .= " AND ".TB_PREF."employee.emp_dept = ".db_escape($emp_dept);
		$sql .= " AND ".TB_PREF."employee.emp_grade = ".db_escape($emp_grade);
		$sql .= " AND ".TB_PREF."employee.emp_desig = ".db_escape($emp_desig);
	}
	
/*	if ($emp_dept != ALL_TEXT) 
	{
   		$sql .= " WHERE ".TB_PREF."employee.emp_dept = ".db_escape($emp_dept);
	}
	*/

   	return $sql;
}



?>