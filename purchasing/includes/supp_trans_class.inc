<?php

/* Definition of the Supplier Transactions class to hold all the information for an accounts payable invoice or credit note
*/

include_once($path_to_root . "/taxes/tax_calc.inc");

class supp_trans 
{

	var $grn_items; /*array of objects of class grn_item using the id as the pointer */
	var $gl_codes; /*array of objects of class gl_codes using a counter as the pointer */
	var $supplier_id;
	var $supplier_name;
	var $terms;
	var $ex_rate;
	
	var $tax_description;
	var $tax_group_id;
	var $tax_included;
	
	var $trans_type;	// invoice or credit
	var $trans_no;

	var $Comments;
	var $tran_date;
	var $due_date;
	var $src_docs = array();		// source invoice for this credit note (if any)

	var $supp_reference;
	var $reference;
	var $ov_amount;
	var $ov_discount;
	var $ov_gst;
	var $gl_codes_counter=0;
	var $credit = 0;
	var $currency;
	var $tax_overrides = array();		// array of taxes manually inserted during sales invoice entry

	var $dimension,
		$dimension2;

	function __construct($trans_type, $trans_no=0)
	{
		$this->trans_type = $trans_type;
		/*Constructor function initialises a new Supplier Transaction object */
		$this->read($trans_type, $trans_no);
	}

	function read($trans_type, $trans_no)
	{
		$this->trans_type = $trans_type;
		$this->trans_no = $trans_no;
 		$this->grn_items = array();
 		$this->gl_codes = array();
		if ($trans_no) {
			read_supp_invoice($trans_no, $trans_type, $this);
			if ($trans_type == ST_SUPPCREDIT)
			{
				$this->src_docs = find_src_invoices($trans_no);
			}
			read_supplier_details_to_trans($this, $this->supplier_id);
		}
	}

	function add_grn_to_trans($grn_item_id, $po_detail_item, $item_code, $item_description,
		$qty_recd, $prev_quantity_inv, $this_quantity_inv, $order_price, $chg_price, 
		$std_cost_unit, $gl_code,$text1, $text2, $text3, $text4, $text5, $text6,$text7,
							  $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
							  $date1, $date2, $date3,
							  $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$batch,$con_factor,$units_id,
							  $dimension, $dimension2,$discount_percent)
	{
		$this->grn_items[$grn_item_id] = new grn_item($grn_item_id, $po_detail_item, 
			$item_code, $item_description, $qty_recd, $prev_quantity_inv, $this_quantity_inv, 
			$order_price, $chg_price, $std_cost_unit, $gl_code, $this->tax_included,$text1,
			$text2, $text3, $text4, $text5, $text6,$text7,
			$amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
			$date1, $date2, $date3,
			$combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$batch,$con_factor,$units_id,
			$dimension, $dimension2,$discount_percent);

		$this->src_docs = find_src_invoices($this);

		unset($this->tax_overrides); // cancel tax overrides after cart change
		return 1;
	}

	function add_gl_codes_to_trans($gl_code, $gl_act_name, $gl_dim, $gl_dim2, $amount, $memo_)
	{
		$this->gl_codes[$this->gl_codes_counter] = new gl_codes($this->gl_codes_counter, 
			$gl_code, $gl_act_name, $gl_dim, $gl_dim2, $amount, $memo_);
		$this->gl_codes_counter++;
		unset($this->tax_overrides); // cancel tax overrides after cart change
		return 1;
	}

	function remove_grn_from_trans($grn_item_id)
	{
		unset($this->tax_overrides); // cancel tax overrides after cart change
	    unset($this->grn_items[$grn_item_id]);
	}
	function remove_gl_codes_from_trans($gl_code_counter)
	{
		unset($this->tax_overrides); // cancel tax overrides after cart change
		unset($this->gl_codes[$gl_code_counter]);
	}

	function is_valid_trans_to_post()
	{
		return (count($this->grn_items) > 0 || count($this->gl_codes) > 0 || 
			($this->ov_amount != 0) || ($this->ov_discount > 0));
	}

	function clear_items()
	{
		unset($this->grn_items);
		unset($this->gl_codes);
		$this->ov_amount = $this->ov_discount = $this->supplier_id = 0;

		$this->grn_items = array();
		$this->gl_codes = array();
	}

    function get_taxes($tax_group_id=null, $shipping_cost=0, $gl_codes=true)
    {
    	$items = array();
    	$prices = array();

        if ($tax_group_id == null)
        	$tax_group_id = $this->tax_group_id;

		// preload the taxgroup
		$tax_group = get_tax_group_items_as_array($tax_group_id);

        foreach ($this->grn_items as $ln_itm) 
        {
        	$items[] = $ln_itm->item_code;
        	$prices[] =round( ($ln_itm->this_quantity_inv * $ln_itm->chg_price),
				user_price_dec());
        }

        if ($tax_group_id == null)
        	$tax_group_id = $this->tax_group_id;
        $taxes = get_tax_for_items($items, $prices, $shipping_cost, $tax_group_id, 
        	$this->tax_included);

		if (isset($this->tax_overrides))
			foreach($this->tax_overrides as $id => $value) // add values entered manually
			{
				$taxes[$id]['Override'] = $value;
			}

		// Taxes included in gl_codes table have exact value, but count to overrides as well.
		// Therefore when we want to now taxes only for items (gl_codes==false),
		// we have to subtract gl_taxes from override values.
		foreach ($this->gl_codes as $gl_code)
		{
			$index = is_tax_account($gl_code->gl_code);
			if ($index !== false)
			{
				if ($gl_codes)
					$taxes[$index]['Value'] += $gl_code->amount;
				elseif (isset($this->tax_overrides))
					$taxes[$index]['Override'] -= $gl_code->amount;
			}
		}

        return $taxes;
    }
	//
	//	Returns total invoice amount without taxes.
	//
    function get_total_taxfree($tax_group_id=null)
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
   		{
   			if (!is_tax_account($gl_line->gl_code))
   				$total += $gl_line->amount;
   		}
   		return $total;
    }
	//
	//	Returns transaction total 
	//
	function get_items_total()
	{

		$total = 0;
        $pref = get_company_prefs();
		if($pref['disc_in_amount'] == 1) {


            foreach($this->grn_items as $ln_itm) {
                $total += round(( $ln_itm->chg_price - $ln_itm->discount_percent ) * $ln_itm->this_quantity_inv  , user_price_dec());
            }

        }
        else{
            foreach ($this->grn_items as $ln_itm)
                $total += round((1 - $ln_itm->discount_percent / 100) * $ln_itm->this_quantity_inv * $ln_itm->chg_price, user_price_dec());
        }

		foreach ($this->gl_codes as $gl_line)
		{
   			if (!is_tax_account($gl_line->gl_code) || $this->tax_included)
				$total += $gl_line->amount;
		}
		return $total;
	}
} /* end of class defintion */

class grn_item 
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
	
	function __construct ($id, $po_detail_item, $item_code, $item_description, $qty_recd,
		$prev_quantity_inv, $this_quantity_inv, $order_price, $chg_price,
		$std_cost_unit, $gl_code, $tax_included,$text1, $text2, $text3, $text4, $text5, $text6,$text7,
					   $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
					   $date1, $date2, $date3,
					   $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$batch,$con_factor,$units_id,
			$dimension, $dimension2,$discount_percent)
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
        $this->text1 = $text1;
        $this->text2 = $text2;
        $this->text3 = $text3;
        $this->text4 = $text4;
        $this->text5 = $text5;
        $this->text6 = $text6;
        
        $this->text7 = $text7;

		$this->amount1 = $amount1;
		$this->amount2 = $amount2;
		$this->amount3 = $amount3;
		$this->amount4 = $amount4;
		$this->amount5 = $amount5;
		$this->amount6 = $amount6;
		$this->date1 = $date1;
		$this->date2 = $date2;
		$this->date3 = $date3;
		$this->combo1 = $combo1;
		$this->combo2 = $combo2;
		$this->combo3 = $combo3;
		$this->combo4 = $combo4;
		$this->combo5 = $combo5;
		$this->combo6 = $comb6;
		$this->con_factor = $con_factor;
		$this->units_id = $units_id;
		$this->grn_batch = $batch;
		$this->dimension = $dimension;
		$this->dimension2 = $dimension2;
        $this->discount_percent = $discount_percent;
	}

	function full_charge_price($tax_group_id, $tax_group=null)
	{
		return get_full_price_for_item($this->item_code, 
		  $this->chg_price, $tax_group_id, $this->tax_included, $tax_group);
	}

	function taxfree_charge_price($tax_group_id, $tax_group=null)
	{
		return get_tax_free_price_for_item($this->item_code, $this->chg_price, 
		  $tax_group_id, $this->tax_included, $tax_group);
	}
}


class gl_codes 
{

	var $Counter;
	var $gl_code;
	var $gl_act_name;
	var $gl_dim;
	var $gl_dim2;
	var $amount;
	var $memo_;

	function __construct ($Counter, $gl_code, $gl_act_name, $gl_dim, $gl_dim2, $amount, $memo_)
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

