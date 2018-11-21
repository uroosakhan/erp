<?php
/* Definition of the Supplier Transactions class to hold all the information for an accounts payable invoice or credit note
*/

include_once($path_to_root . "/taxes/tax_calc.inc");

class supp_trans_price_import
{

	var $grn_items; /*array of objects of class grn_item using the id as the pointer */
	var $gl_codes; /*array of objects of class gl_codes using a counter as the pointer */
	var $supplier_id;
	var $supplier_name;
	var $terms;
	
	var $tax_description;
	var $tax_group_id;
	var $tax_included;
	
	var $trans_type;	// invoice or credit

	var $Comments;
	var $tran_date;
	var $due_date;

	var $supp_reference;
	var $reference;
	var $ov_amount;
	var $ov_discount;
	var $ov_gst;
	var $gl_codes_counter=0;
	var $credit = 0;

	//
	var $Unit_Amt;
	var $Gross_Amt;
	var $As_Per_B_E;
	var $Landing;
	var $Landing_Amt;
	var $Value_invl_Landing;
	var $INS;
	var $INS_Amt;
	var $Value_Incl_INC;
	var $F_E_D;
	var $F_E_D_Amt;
	var $Duty;
	var $Duty_Amt;
	var $Value_And_Duty;
	var $Value_Excl_S_T; 
	var $S_T;
	var $S_T_Amt; 
	var $Amount_Incl_S_T; 
	var $I_Tax;
	var $I_Tax_Amt;
	var $Add_S_T;
	var $Add_S_T_Amt; 
	var $Total_Charges; 
	var $Other_Expenses;
	var $Net_Amount;
	var $Job_Name;
	var $Currency_Amount;
	var $Total_Other_Expense;


	function supp_trans_price_import($trans_type)
	{
		
		$this->trans_type = $trans_type;
		/*Constructor function initialises a new Supplier Transaction object */
		$this->grn_items = array();
		$this->gl_codes = array();

	}

	function add_grn_to_trans_price_import($grn_item_id, $po_detail_item, $item_code, $item_description,
		$qty_recd, $prev_quantity_inv, $this_quantity_inv, $order_price, $chg_price, 
		$std_cost_unit, $gl_code, $Unit_Amt, $Gross_Amt, $As_Per_B_E,
			$Landing, $Landing_Amt, $Value_invl_Landing, $INS,
			$INS_Amt, $Value_Incl_INC, $F_E_D, $F_E_D_Amt, $Duty, $Duty_Amt,
			$Value_And_Duty, $Value_Excl_S_T, $S_T,
			$S_T_Amt, $Amount_Incl_S_T, $I_Tax, $I_Tax_Amt,
			$Add_S_T, $Add_S_T_Amt, $Total_Charges, $Other_Expense,
			$Net_Amount, $Job_Name, $Gross_Amt_new)
	{

		$this->grn_items[$grn_item_id] = new grn_item_price_import($grn_item_id, $po_detail_item,
			$item_code, $item_description, $qty_recd, $prev_quantity_inv, $this_quantity_inv, 
			$order_price, $chg_price, $std_cost_unit, $gl_code, $this->tax_included,
			$Unit_Amt, $Gross_Amt, $As_Per_B_E,
			$Landing, $Landing_Amt, $Value_invl_Landing, $INS,
			$INS_Amt, $Value_Incl_INC, $F_E_D, $F_E_D_Amt, $Duty, $Duty_Amt,
			$Value_And_Duty, $Value_Excl_S_T, $S_T,
			$S_T_Amt, $Amount_Incl_S_T, $I_Tax, $I_Tax_Amt,
			$Add_S_T, $Add_S_T_Amt, $Total_Charges, $Other_Expense,
			$Net_Amount, $Job_Name, $Gross_Amt_new);
		return 1;
	}

	function add_gl_codes_to_trans_price_import($gl_code, $gl_act_name, $gl_dim, $gl_dim2
		, $amount, $memo_)
	{
		$this->gl_codes[$this->gl_codes_counter] = new gl_codes($this->gl_codes_counter,
			$gl_code, $gl_act_name, $gl_dim, $gl_dim2, $amount, $memo_);
		$this->gl_codes_counter++;
		unset($this->tax_overrides); // cancel tax overrides after cart change
		return 1;
	}

	function remove_grn_from_trans_price_import($grn_item_id)
	{
	    unset($this->grn_items[$grn_item_id]);
	}
	function remove_gl_codes_from_trans_price_import($gl_code_counter)
	{
	     unset($this->gl_codes[$gl_code_counter]);
	}
	
	function is_valid_trans_to_post_price_import()
	{
		return (count($this->grn_items) > 0 || count($this->gl_codes) > 0 || 
			($this->ov_amount != 0) || ($this->ov_discount > 0));
	}
	
	function clear_items_price_import()
	{
		unset($this->grn_items);
		unset($this->gl_codes);
		$this->ov_amount = $this->ov_discount = $this->supplier_id = 0;
		
		$this->grn_items = array();
		$this->gl_codes = array();
	}
	
    function get_taxes_price_import($tax_group_id=null, $shipping_cost=0, $gl_codes=true)
    {
    	$items = array();
    	$prices = array();
    	
        if ($tax_group_id == null)
        	$tax_group_id = $this->tax_group_id;
        	
		// preload the taxgroup !
		$tax_group = get_tax_group_items_as_array($tax_group_id);    	
        
        foreach ($this->grn_items as $ln_itm) 
        {
        	$items[] = $ln_itm->item_code;
//        	$prices[] =round( ($ln_itm->this_quantity_inv * $ln_itm->taxfree_charge_price($tax_group_id, $tax_group)),
        	$prices[] =round( ($ln_itm->this_quantity_inv * $ln_itm->chg_price),
			 user_price_dec());
        }

        if ($tax_group_id == null)
        	$tax_group_id = $this->tax_group_id;
        $taxes = get_tax_for_items($items, $prices, $shipping_cost, $tax_group_id, 
        	$this->tax_included);

///////////////// Joe Hunt 2009.08.18

		if ($gl_codes)
		{
			foreach ($this->gl_codes as $gl_code)
			{
				$index = is_tax_account($gl_code->gl_code);
				if ($index !== false)
				{
					$taxes[$index]['Value'] += $gl_code->amount;
				}	
			}
		}	
////////////////		
        return $taxes;
    }		
	//
	//	Returns total invoice amount without taxes.
	//
    function get_total_taxfree_price_import($tax_group_id=null)
    {
    	$total = 0;
    	
		// preload the taxgroup !
		if ($tax_group_id != null)
			$tax_group = get_tax_group_items_as_array($tax_group_id);
		else 		
			$tax_group = null;    	
    	
		foreach ($this->grn_items as $ln_itm)
    		$total += round(($ln_itm->this_quantity_inv * $ln_itm->taxfree_charge_price($tax_group_id, $tax_group)),
			 user_price_dec());

   		foreach ($this->gl_codes as $gl_line)
   		{	//////// 2009-08-18 Joe Hunt
   			if (!is_tax_account($gl_line->gl_code))
   				$total += $gl_line->amount;
   		}	
   		return $total;
    }

	function get_items_total_price_import()
	{
		$total = 0;

		foreach ($this->grn_items as $ln_itm)
			$total += round($ln_itm->Net_Amount, user_price_dec());

		foreach ($this->gl_codes as $gl_line)
		{   //////// 2010-10-10 Joe Hunt
   			if (!is_tax_account($gl_line->gl_code) || $this->tax_included)
				$total += $gl_line->amount;
		}
		return $total;
	}
} /* end of class defintion */

class grn_item_price_import
{

/* Contains relavent information from the purch_order_details as well to provide in cached form,
all the info to do the necessary entries without looking up ie additional queries of the database again */

	var $id;
	var $po_detail_item;
	var $item_code;
	var $item_description;
	var $qty_recd;
	var $prev_quantity_inv;
	var $this_quantity_inv;
	var $order_price;
	var $chg_price;
	var $std_cost_unit;
	var $gl_code;
	var $tax_included;

	function grn_item_price_import($id, $po_detail_item, $item_code, $item_description, $qty_recd,
		$prev_quantity_inv, $this_quantity_inv, $order_price, $chg_price,
		$std_cost_unit, $gl_code, $tax_included, $Unit_Amt, $Gross_Amt, $As_Per_B_E,
		 $Landing, $Landing_Amt, $Value_invl_Landing, $INS,
		 $INS_Amt, $Value_Incl_INC, $F_E_D, $F_E_D_Amt, $Duty, $Duty_Amt,
		 $Value_And_Duty, $Value_Excl_S_T, $S_T,
		 $S_T_Amt, $Amount_Incl_S_T, $I_Tax, $I_Tax_Amt,
		 $Add_S_T, $Add_S_T_Amt, $Total_Charges, $Other_Expense,
		 $Net_Amount, $Job_Name, $Gross_Amt_new)
	{

		$this->id = $id;
		$this->po_detail_item = $po_detail_item;
		$this->item_code = $item_code;
		$this->item_description = $item_description;
		$this->qty_recd = $qty_recd;
		$this->prev_quantity_inv = $prev_quantity_inv;
		$this->this_quantity_inv = $this_quantity_inv;
		$this->order_price =$order_price;
		$this->chg_price = $chg_price;
		$this->std_cost_unit = $std_cost_unit;
		$this->gl_code = $gl_code;
		$this->tax_included = $tax_included;
		//
		$this->Unit_Amt = $Unit_Amt;
		$this->Gross_Amt = $Gross_Amt;
		$this->As_Per_B_E = $As_Per_B_E;
		$this->Landing = $Landing;
		$this->Landing_Amt = $Landing_Amt;
		$this->Value_invl_Landing = $Value_invl_Landing;
		$this->INS = $INS;
		$this->INS_Amt = $INS_Amt;
		$this->Value_Incl_INC = $Value_Incl_INC;
		$this->F_E_D = $F_E_D;
		$this->F_E_D_Amt = $F_E_D_Amt;
		$this->Duty = $Duty;
		$this->Duty_Amt = $Duty_Amt;
		$this->Value_And_Duty = $Value_And_Duty;
		$this->Value_Excl_S_T = $Value_Excl_S_T;
		$this->S_T = $S_T;
		$this->S_T = $S_T;
		$this->S_T_Amt = $S_T_Amt;
		$this->Amount_Incl_S_T = $Amount_Incl_S_T;
		$this->I_Tax = $I_Tax;
		$this->I_Tax_Amt = $I_Tax_Amt;
		$this->Add_S_T = $Add_S_T;
		$this->Add_S_T_Amt = $Add_S_T_Amt;
		$this->Total_Charges = $Total_Charges;
		$this->Other_Expenses = $Other_Expense;
//		$this->Total_Other_Expense = $Other_Expense;
		$this->Net_Amount = $Net_Amount;
		$this->Job_Name = $Job_Name;
		$this->Gross_Amt_new = $Gross_Amt_new;
//		display_error($Net_Amount."====". $Other_Expense);
	}
	
	function full_charge_price_import($tax_group_id, $tax_group=null)
	{
		return get_full_price_for_item($this->item_code, 
		  $this->chg_price, $tax_group_id, $this->tax_included, $tax_group);
	}
	
	function taxfree_charge_price_import($tax_group_id, $tax_group=null)
	{
//		if ($tax_group_id==null)
//			return $this->chg_price;
		return get_tax_free_price_for_item($this->item_code, $this->chg_price, 
		  $tax_group_id, $this->tax_included, $tax_group);
	}
}


class gl_codes_price_import
{

	var $Counter;
	var $gl_code;
	var $gl_act_name;
	var $gl_dim;
	var $gl_dim2;
	var $amount;
	var $memo_;

	function gl_codes_price_import ($Counter, $gl_code, $gl_act_name, $gl_dim, $gl_dim2, $amount, $memo_)
	{

	/* Constructor function to add a new gl_codes object with passed params */
		$this->Counter = $Counter;
		$this->gl_code = $gl_code;
		$this->gl_act_name = $gl_act_name;
		$this->gl_dim = $gl_dim;
		$this->gl_dim2 = $gl_dim2;
		$this->amount = $amount;
		$this->memo_= $memo_;
	}
}

?>
