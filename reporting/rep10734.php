<?php
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Invoices
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------
function get_customer_information($debtor_no)
{
	$sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no = '$debtor_no'))";
	$result = db_query($sql,"Error");
	return db_fetch($result);
}
/*
function get_tax ($id)
{
	$sql = "SELECT rate FROM ".TB_PREF."tax_types WHERE id = ".db_escape($id)."
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch_row($result);
	return $row['0'];
}
*/
function get_user_name_701232($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_tax_rate($trans_no, $stock_id)
{
	$sql = "SELECT (unit_tax/unit_price*100) FROM ".TB_PREF."debtor_trans_details
	WHERE debtor_trans_no = ".db_escape($trans_no)."
	AND stock_id= ".db_escape($stock_id)."
	AND debtor_trans_type = 10
	
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch_row($result);
	return $row['0'];
}



function get_order_no($trans_no)
{
    $sql = "SELECT order_ ,tran_date FROM ".TB_PREF."debtor_trans
	WHERE trans_no = ".db_escape($trans_no)."

	AND type = 13
	
	";
    $result = db_query($sql, "could not retreive default customer currency code");
    $row = db_fetch($result);
    return $row;
}


function get_order_no1($order_)
{
    $sql = "SELECT trans_no FROM ".TB_PREF."debtor_trans
	WHERE type = 13 AND ov_amount != 0 AND order_ = ".db_escape($order_);
    return db_query($sql, "could not retreive default customer currency code");
    
}

function get_tax_amount($trans_no, $stock_id)
{
	$sql = "SELECT (unit_tax * quantity) FROM ".TB_PREF."debtor_trans_details 
	WHERE debtor_trans_no = ".db_escape($trans_no)."
	AND stock_id= ".db_escape($stock_id)."
	AND debtor_trans_type = 10
	
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch_row($result);
	return $row['0'];
}
function get_debtor_trans_info($trans_no , $tyep)
{
	$sql = "SELECT * FROM ".TB_PREF."debtor_trans 
	WHERE trans_no = ".db_escape($trans_no)."
	AND type = 10
	
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch($result);
	return $row;
}
function get_tax_rate_1()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 2";
    $result = db_query($sql, 'error');
    return $result;
}
function get_phoneno_for_suppliers_($customer_id)
{
    $sql = "SELECT * FROM ".TB_PREF."crm_persons WHERE `id` IN (
            SELECT person_id FROM ".TB_PREF."crm_contacts WHERE `type`='customer' AND `action`='general' AND entity_id IN (
            SELECT branch_code FROM ".TB_PREF."cust_branch WHERE debtor_no=".db_escape($customer_id)."))";
    $query = db_query($sql, "Error");
    return db_fetch($query);
}

function get_item1($stock_id)
{
    $sql = "SELECT item.*, taxtype.name AS tax_type_name
      FROM ".TB_PREF."stock_master item,"
        .TB_PREF."item_tax_types taxtype
      WHERE taxtype.id=item.tax_type_id
      AND stock_id=".db_escape($stock_id);
    $sql .="ORDER BY stock_id";
    $result = db_query($sql,"an item could not be retreived");

    return db_fetch($result);
}

print_invoices();

//----------------------------------------------------------------------------------------------------
function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function print_invoices()
{

	global $path_to_root, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;
	
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$pay_service = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];

	if (!$from || !$to) return;

	$orientation = ('P');
	//$dec = user_price_dec();
	$dec = 0;

 	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(1, 22, 65, 185, 240, 290, 340, 370,420,465,495);
//	$cols2 = array(6, 45, 190, 250, 320, 380, 430, 550);
	// $headers in doctext.inc
	$aligns = array('center','left','left','center','right','right','center','center','right','right','center');
//	$aligns2 = array('left','left','left','left','left','left','left');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');
	if ($email == 0)
		$rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);
	for ($i = $from; $i <= $to; $i++)
	{
			if (!exists_customer_trans(ST_SALESINVOICE, $i))
				continue;
			$sign = 1;
			$myrow = get_customer_trans($i, ST_SALESINVOICE);
			$baccount = get_default_bank_account($myrow['curr_code']);
			$params['bankaccount'] = $baccount['id'];

			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(), 6, $orientation);
				$rep->title = _('INVOICE');
				$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
			}	
			$rep->SetHeaderType('Header10734');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns, null, null,null);


			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			$rep->NewPage();
   			$result = get_customer_trans_details(ST_SALESINVOICE, $i);
			$SubTotal = 0;
            $total_price =0;
            $total_including_tax=0;
            $total_amount=0;
            $DisplayPq =0;
            $amount_including_tax = 0;
        $serial_no=0;
        $myrow3 = db_fetch(get_tax_rate_1());
			while ($myrow2=db_fetch($result))
			{
				if ($myrow2["quantity"] == 0)
					continue;

				$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"]);
	    		$DisplayQty = number_format2($sign*$myrow2["quantity"], 0);
				$DisplayPq =  ($myrow2["unit_price"] * $myrow2["quantity"] );
	    		$DisplayNet = number_format2($Net);
                $rate = $myrow3['rate'];
                $unit_price=$myrow2["unit_price"] - $myrow2["unit_tax"];
                $val_exc=$unit_price * $myrow2["quantity"];
                $val_inc = $myrow2["unit_price"] * $myrow2["quantity"];
                $amount_salestax=($rate / 100) * $val_exc;
                $amount_salestax2 = ($rate / 100) * $DisplayPq;
                $val_exc_=$amount_salestax + $val_exc;
                $val_inc_ =$amount_salestax2 + $DisplayPq;
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";

                $serial_no++;
                $rep->TextCol(0, 1,	$serial_no         , -2);
                 $rep->TextCol(1, 2,	$myrow2['stock_id']         , -2);
				$oldrow = $rep->row;
				$rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
	

				$newrow = $rep->row;
				$rep->row = $oldrow;
                $get_debtor_trans = get_debtor_trans_info($myrow['trans_no']);
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{
				    $item=get_item1($myrow2['stock_id']);

							         $rep->TextCol(3, 4, $item['carton'], -2);

                    if ($get_debtor_trans['tax_included'] == 1) {



         if($myrow2['units_id']!='pack'){

                        $cartons=$myrow2["quantity"]*$item["con_factor"];


                        $rep->TextCol(6, 7, $unit_price, -2);
                        $rep->TextCol(4, 5,$myrow2["quantity"]	, -2);
                        $rep->TextCol(5, 6,$cartons	, -2);
                        
                        $exclusive_value=$unit_price*$myrow2["quantity"];
                         $total_packing +=$myrow2["quantity"];
                              
                              $total_cartons +=$cartons;


                    }else{

                        $rep->TextCol(6, 7, $unit_price, -2);
                        $rep->TextCol(4, 5,$myrow2["quantity"]	, -2);

                        $carton=$myrow2["quantity"]/$item["con_factor"];
                        $rep->TextCol(5, 6,number_format2($carton)	, -2);
                        $total_cartons +=$carton;

                        $total_packing +=$myrow2["quantity"];
                        $exclusive_value=$unit_price*$myrow2["quantity"];
                        $rates=($rate / 100) * $exclusive_value;

                    }

                        $rep->TextCol(8, 9, $rates , -2);

             $total_values=$rates+$exclusive_value;
             $total_exclusive_value +=$exclusive_value;
                     
               $total_valuess +=$total_values;

                }
                    else
                    {
                      $rep->TextCol(6, 7, $myrow2["unit_price"], -2);

                          if($myrow2['units_id']!='pack'){

                        $cartons=$myrow2["quantity"]*$item["con_factor"];

                        $rep->TextCol(4, 5,$DisplayQty	, -2);
                        $rep->TextCol(5, 6,$cartons	, -2);
                     $exclusive_value=$myrow2["unit_price"]*$myrow2["quantity"];
$total_packing +=$myrow2["quantity"];

$total_cartons +=$cartons;

                    }else{


                     $rep->TextCol(4, 5,$DisplayQty	, -2);

                    $carton=$myrow2["quantity"]/$item["con_factor"];
                    $rep->TextCol(5, 6,number_format2($carton)	, -2);
                        $total_cartons +=$carton;

                  $total_packing +=$myrow2["quantity"];
               $exclusive_value=$myrow2["unit_price"]*$myrow2["quantity"];
             
                    }

                        $rep->TextCol(8, 9, "", -2);

                        
 $total_values =$rate+$exclusive_value;
 $total_valuess+= $total_values;
                        
                            }
        $rep->MultiCell(205,15," ".$myrow['text_1']."  /  ".$myrow['cheque_date'] ,0,'L', 0, 2,409,105,true);

                $rep->TextCol(7, 8, number_format2($exclusive_value), -2);
                          $total_exclusive_value +=$exclusive_value;

                  $rep->TextCol(9, 10, number_format2($total_values), -2);

                        

					$amount_including_tax = get_tax_amount($i, $myrow2['stock_id']);

			


					$amount_excluding_tax = $DisplayPq-$DisplayPrice;



					$including_sales_tax=  $DisplayPq + $amount_including_tax;


                    $a=$DisplayPq -$amount_including_tax;
					$total_amount += $amount_salestax;
					$total_including_tax += ($val_exc_);

                    $total_value_excl_tax2 += $total_packing;
                    $total_amount2 += $amount_salestax2;
                    $total_including_tax2 += ($val_inc_);
                    $do_no1 =get_order_no1($myrow["order_"]);
                    $trans_ = '';
                    while($row = db_fetch($do_no1))
                    {
                        if($trans_ != '')
                            $trans_ .= ',';
                            
                        $trans_ .=  $row['trans_no'];
                    }
            $rep->Font('bold');     

$rep->Font('');
				}
				$rep->MultiCell(230,13," Do No:",1,'L', 0, 2,40,84,true);
$rep->MultiCell(230,13,"".$trans_,0,'L', 0, 2,75,84,true);

				  $rep->NewLine(1);
				//$rep->row = $newrow;
				$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();
			}

			$memo = get_comments_string(ST_SALESINVOICE, $i);
			if ($memo != "")
			{
				//$rep->NewLine();
				//$rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

		//$rep->NewLine(14.5);
		$rep->row = $rep->bottomMargin + (5 * $rep->lineHeight);
		$doctype = ST_SALESINVOICE;
	//	$rep->NewLine();

// 		$rep->NewLine(-14.5);
		$rep->Font('bold');
			$rep->MultiCell(525, 18, "TOTAL", 1, 'L', 0, 2, 40,589, true);
// 		$rep->TextCol(1, 2, "TOTAL", -2);
        if ($get_debtor_trans['tax_included'] == 1) {
			//$rep->TextCol(2, 3,price_format($total_price), -2);
			
$rep->MultiCell(525, 18,number_format2($total_packing ) , 0, 'L', 0, 2, 290,590, true);

 $rep->MultiCell(525, 18,number_format2($total_cartons ) , 0, 'L', 0, 2, 345,590, true);

 $rep->MultiCell(525, 18,number_format2($total_exclusive_value ) , 0, 'L', 0, 2, 412,590, true);
 $rep->MultiCell(525, 18,number_format2($total_valuess ) , 0, 'L', 0, 2, 520,590, true);
		//	$rep->TextCol(4, 5,price_format($total_packing ), -2);
		//	$rep->TextCol(5, 6,price_format($total_cartons), -2);
		//	$rep->TextCol(7, 8,price_format($total_exclusive_value), -2);
			     //      $rep->TextCol(8, 9,price_format($total_valuess), -2);

        }
        else
        {
            
            $rep->MultiCell(525, 18, number_format2($total_packing)  , 0, 'L', 0, 2, 290,590, true);
            
            
                 $rep->MultiCell(525, 18, number_format2($total_cartons) , 0, 'L', 0, 2, 345,590, true);
                 
                 
             
 $rep->MultiCell(525, 18, number_format2($total_exclusive_value) , 0, 'L', 0, 2, 412,590, true);
 $rep->MultiCell(525, 18, number_format2($total_valuess)  , 0, 'L', 0, 2, 520,590, true);
             
                 
          //  $rep->TextCol(4, 5,price_format($total_packing ), -2);
            //$rep->TextCol(5, 6,price_format($total_cartons), -2);
            // $rep->TextCol(7, 8,price_format($total_exclusive_value), -2);
            
            // $rep->TextCol(9, 10,price_format($total_valuess), -2);

        }
		$rep->Font('');
		//$rep->NewLine(-4);
//			$rep->TextCol(6, 7,	$total_price, -2);
//			$rep->NewLine();
//			$rep->TextCol(3, 6, _("Shipping"), -2);
//			$rep->TextCol(6, 7,	$DisplayFreight, -2);
			$rep->NewLine();
			$tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
			$first = true;
    		while ($tax_item = db_fetch($tax_items))
    		{
    			if ($tax_item['amount'] == 0)
    				continue;
    			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);

    			if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
    				$tax_type_name = $tax_item['tax_type_name'];
    			else
    				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

    			if ($tax_item['included_in_price'])
    			{
    				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
    				{
    					if ($first)
    					{
							$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
							$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
						$rep->TextCol(3, 6, $tax_type_name, -2);
						$rep->TextCol(6, 7,	$DisplayTax, -2);
						$first = false;
    				}
    				else
						$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
				}
    			else
    			{
					//$rep->TextCol(3, 6, $tax_type_name, -2);
					//$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}
			$rep->Font('b');
$rep->setfontsize(11);
$rep->MultiCell(525, 20, " Amount in Words: " , 1, 'L', 0, 2, 40,620, true);
				
				
	$rep->MultiCell(600, 18, convert_number_to_words($total_valuess)."  Only" , 0, 'L', 0, 2, 140,620, true);
	
				$rep->MultiCell(50, 18, "" , 1, 'L', 0, 2,280,589, true);
				
							$rep->MultiCell(50, 18, "" , 1, 'L', 0, 2,330,589, true);
										$rep->MultiCell(50, 18, "" , 1, 'L', 0, 2,410,589, true);
										$rep->MultiCell(46, 18, "" , 1, 'L', 0, 2,460,589, true);
					$rep->Font('');


    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"]));
			$rep->Font('bold');

			$words = price_in_words($myrow['Total'], ST_SALESINVOICE);
			if ($words != "")
			{
				$rep->NewLine(1);
				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
			}
			
			    $user =get_user_id($myrow['trans_no'],ST_SALESINVOICE);
        $rep->MultiCell(100, 25, "".get_user_name_701232($user) ,0, 'C', 0, 2, 140,695, true);

			$rep->Font();
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

?>