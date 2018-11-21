<?php


function add_customer($CustName, $cust_ref, $address, $tax_id, $curr_code,
	$dimension_id, $dimension2_id, $credit_status, $DOB, $payment_terms, $discount, $pymt_discount, 
	$credit_limit, $sales_type, $notes,$ntn_no,$service_text,$credit_allowed)
{
    $dob = sql2date($DOB);
    
	$sql = "INSERT INTO ".TB_PREF."debtors_master (name, debtor_ref, address, tax_id,
		dimension_id, dimension2_id, curr_code, credit_status, DOB, payment_terms, discount, 
		pymt_discount,credit_limit, sales_type, notes,ntn_no,service_text,credit_allowed) VALUES ("
		.db_escape($CustName) .", " .db_escape($cust_ref) .", "
		.db_escape($address) . ", " . db_escape($tax_id) . ","
		.db_escape($dimension_id) . ", " 
		.db_escape($dimension2_id) . ", ".db_escape($curr_code) . ", 
		" . db_escape($credit_status) . ", " . db_escape($dob) . ", ".db_escape($payment_terms) . ",
		" . db_escape($discount) . ", 
		" . db_escape($pymt_discount) . ", " . db_escape($credit_limit) 
		 .", ".db_escape($sales_type).", " . db_escape($notes) . ",
		 ".db_escape($ntn_no) . ",".db_escape($service_text) .  ",".db_escape($credit_allowed) . ")";
	db_query($sql,"The customer could not be added");
}

function update_customer($customer_id, $CustName, $cust_ref, $address, $tax_id, $ntn_no,$service_text, $curr_code,
	$dimension_id, $dimension2_id, $credit_status, $DOB, $payment_terms, $discount, $pymt_discount,
	$credit_limit, $sales_type, $notes,$credit_allowed)
{
    $dob = sql2date($DOB);
    
	$sql = "UPDATE ".TB_PREF."debtors_master SET name=" . db_escape($CustName) . ", 
		debtor_ref=" . db_escape($cust_ref) . ",
		address=".db_escape($address) . ", 
		tax_id=".db_escape($tax_id) . ", 
		ntn_no=".db_escape($ntn_no) . ", 
		service_text=".db_escape($service_text) . ", 
		curr_code=".db_escape($curr_code) . ", 
		dimension_id=".db_escape($dimension_id) . ", 
		dimension2_id=".db_escape($dimension2_id) . ", 
		credit_status=".db_escape($credit_status) . ", 
		DOB=".db_escape($dob) . ",
		payment_terms=".db_escape($payment_terms) . ", 
		discount=" . $discount . ", 
		pymt_discount=" . $pymt_discount . ", 
		credit_limit=" . $credit_limit . ", 
		sales_type = ".db_escape($sales_type) . ", 
		notes=".db_escape($notes) .",
		credit_allowed=".db_escape($credit_allowed) ."
		WHERE debtor_no = ".db_escape($customer_id);

	db_query($sql,"The customer could not be updated");
}

function delete_customer($customer_id)
{
	begin_transaction();
	delete_entity_contacts('customer', $customer_id);

	$sql = "DELETE FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);;
	db_query($sql,"cannot delete customer");
	$sql1 = "DELETE FROM `0_crm_info` WHERE `customer_id` =".db_escape($customer_id);;
    db_query($sql1,"cannot delete customer's child");
	commit_transaction();
}

function get_customer_details($customer_id, $to=null, $all=true, $dim)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - debtor_trans.alloc from all summations

//	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
	if ($all)
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)"; 
  /*  else		
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
    		trans.alloc),0)"; */
    		
    else		//dz 24.7.18
    	$value = "IFNULL(
    	IF (type=".ST_JOURNAL." && ov_amount < 0,
    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 +
    		trans.alloc),
    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
    		trans.alloc)    		
    		)
    		,0)";     		

	$due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
			FROM ".TB_PREF."debtors_master debtor
				 LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
				 .TB_PREF."payment_terms terms,"
				 .TB_PREF."credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
	 			 if ($customer_id)
		$sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

	if (!$all)
		$sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA;
	
	if($dim != 0)
	    $sql .= " AND trans.dimension_id = ".db_escape($dim);
	$sql .= " GROUP BY
		  	debtor.name,
		  	terms.terms,
		  	terms.days_before_due,
		  	terms.day_in_following_month,
		  	debtor.credit_limit,
		  	credit_status.dissallow_invoices,
		  	credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

}

function get_customer_details_new($customer_id, $to=null, $all=true,$type)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - debtor_trans.alloc from all summations

//	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
	if ($all)
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)"; 
    else		
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
    		trans.alloc),0)"; 

	$due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
			FROM ".TB_PREF."debtors_master debtor
				 LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
				 .TB_PREF."payment_terms terms,"
				 .TB_PREF."credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
	 			 if ($customer_id)
		$sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

	if (!$all)
		$sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA;
		
	if($type != -1)
    $sql .= "AND type =".$type;
		
	$sql .= " GROUP BY
			  	debtor.name,
			  	terms.terms,
			  	terms.days_before_due,
			  	terms.day_in_following_month,
			  	debtor.credit_limit,
			  	credit_status.dissallow_invoices,
			  	credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

}
function get_customer($customer_id)
{
	$sql = "SELECT * FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	return db_fetch($result);
}

function get_customer_name($customer_id)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_customer_address($customer_id)
{
	$sql = "SELECT address FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_customer_habit($customer_id)
{
	$sql = "SELECT debtor.pymt_discount, credit_status.dissallow_invoices
			FROM ".TB_PREF."debtors_master debtor,"
				.TB_PREF."credit_status credit_status
			WHERE debtor.credit_status = credit_status.id
			AND debtor.debtor_no = ".db_escape($customer_id);

	$result = db_query($sql, "could not query customers");

	return db_fetch($result);
}

function get_customer_contacts($customer_id, $action=null)
{
	$results = array();
	$res = get_crm_persons('customer', $action, $customer_id);
	while($contact = db_fetch($res))
	{
		if ($contact['lang'] == 'C') // Fix for improper lang in demo sql files.
			$contact['lang'] = '';
		$results[] = $contact;
	}	
	return $results;
}

function get_current_cust_credit($customer_id)
{
	$custdet = get_customer_details($customer_id);

	return $custdet['credit_limit']-$custdet['Balance'];

}

function is_new_customer($id)
{
	$tables = array('cust_branch', 'debtor_trans', 'recurrent_invoices', 'sales_orders');

	return !key_in_foreign_table($id, $tables, 'debtor_no');
}

function get_customer_by_ref($reference)
{
	$sql = "SELECT * FROM ".TB_PREF."debtors_master WHERE debtor_ref=".db_escape($reference);

	$result = db_query($sql, "could not get customer");

	return db_fetch($result);
}

//----------------------------------------------------------------------------------

function get_customer_currency($customer_id=null, $branch_id=null)
{
    $sql = "SELECT curr_code
    	FROM ".TB_PREF."debtors_master cust
    		LEFT JOIN ".TB_PREF."cust_branch branch ON branch.debtor_no=cust.debtor_no
    	WHERE " .(isset($branch_id) ? "branch_code = ".db_escape($branch_id) : "cust.debtor_no = ".db_escape($customer_id));

	$result = db_query($sql, "Retreive currency of customer $customer_id");

	$myrow=db_fetch_row($result);
	return $myrow[0];
}

function get_customers_search($customer)
{
	global $SysPrefs;

	if (isset($SysPrefs->max_rows_in_search))
		$limit = $SysPrefs->max_rows_in_search;
	else
		$limit = 10;

	$sql = "SELECT debtor_no, name, debtor_ref, address, tax_id FROM ".TB_PREF."debtors_master 
	  WHERE (  name LIKE " . db_escape("%" . $customer. "%") . " OR 
    		 debtor_ref LIKE " . db_escape("%" . $customer. "%") . " OR 
	        address LIKE " . db_escape("%" . $customer. "%") . " OR 
    	     tax_id LIKE " . db_escape("%" . $customer. "%").")
	  ORDER BY name LIMIT 0,".(int)($limit);

	return db_query($sql, "Failed in retreiving customer list.");
}

function get_sales_order_ref_search($reference)
{
    global $SysPrefs;

    if (isset($SysPrefs->max_rows_in_search))
        $limit = $SysPrefs->max_rows_in_search;
    else
        $limit = 10;

    $sql = "SELECT order_no, reference FROM ".TB_PREF."sales_orders 
     WHERE (  reference LIKE " . db_escape("%" . $reference. "%").")
     ORDER BY reference LIMIT 0,".(int)($limit);

    return db_query($sql, "Failed in retreiving customer list.");
}

function get_credit_balance_customer($customer_id)
{
    $sql = " SELECT 
        Sum(IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2 OR trans.type=42,
         -1, 1) * (ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount
         + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc
         - discount1 - discount2),0)) - cust.credit_limit as cur_credit
      FROM ".TB_PREF."debtors_master cust
        LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.type!=".ST_CUSTDELIVERY." AND trans.debtor_no = cust.debtor_no,"
        .TB_PREF."credit_status, "
        .TB_PREF."sales_types stype
      WHERE cust.sales_type=stype.id
         AND cust.credit_status=".TB_PREF."credit_status.id
         AND cust.debtor_no = ".db_escape($customer_id)
        ." GROUP by cust.debtor_no";

    $result = db_query($sql, "The supplier details could not be retreived");
    $fetch = db_fetch($result);
    return $fetch['cur_credit'];
}