<?php
$page_security = 'SA_SALESANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Inventory Sales Report
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_inventory_sales();

function getTransactions($fromcust, $from, $to)
{
	//$from = date2sql($from);
	//$to = date2sql($to);
	/*$sql = "SELECT
			".TB_PREF."sales_order_details.order_no,
			".TB_PREF."sales_order_details.description,
			SUM(".TB_PREF."sales_order_details.qty_sent)
			FROM
			`".TB_PREF."sales_order_details`,
			`".TB_PREF."sales_orders`,
			`".TB_PREF."stock_master`
			WHERE 
			".TB_PREF."stock_master.stock_id = ".TB_PREF."sales_order_details.stk_code
			AND
			".TB_PREF."sales_order_details.order_no = ".TB_PREF."sales_orders.order_no
			AND 
			".TB_PREF."sales_orders.order_no>=1
			AND
			".TB_PREF."sales_orders.order_no<=1800
			AND
			(".TB_PREF."stock_master.mb_flag='B'
			OR
			".TB_PREF."stock_master.mb_flag='M')";
	*/
		
			$sql = "SELECT 
			".TB_PREF."sales_order_details.description,
			SUM(".TB_PREF."sales_order_details.quantity) AS quantity,
			".TB_PREF."stock_master.carton
	
			FROM
			".TB_PREF."sales_order_details,
			".TB_PREF."sales_orders,
			".TB_PREF."stock_master
			
	
			WHERE 
			".TB_PREF."stock_master.stock_id = ".TB_PREF."sales_order_details.stk_code
			AND
			".TB_PREF."sales_order_details.order_no = ".TB_PREF."sales_orders.order_no
			AND
			".TB_PREF."sales_order_details.order_no>=$to
			AND
			".TB_PREF."sales_orders.order_no<=$from
			AND
			(".TB_PREF."stock_master.mb_flag='B'
			OR
			".TB_PREF."stock_master.mb_flag='M')";
			//$sql .= " GROUP BY ".TB_PREF."stock_master.stock_id
			$sql .= " GROUP BY ".TB_PREF."sales_order_details.description
			ORDER BY
			".TB_PREF."sales_order_details.description";
	
	
		//if ($category != 0)
		//	$sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);
		//if ($location != '')
		//	$sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($location);
		//if ($fromcust != '')
		//	$sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromcust);
		//$sql .= " GROUP BY ".TB_PREF."stock_master.stock_id
			//ORDER BY
			//".TB_PREF."stock_master.stock_id";
    return db_query($sql,"No transactions were returned");
	

}

//----------------------------------------------------------------------------------------------------

function print_inventory_sales()
{
    global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
    $comments = $_POST['PARAM_2'];
	$orientation = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	
	/*
	$category = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $fromcust = $_POST['PARAM_4'];
	*/
	
	
	
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

	

	

	if ($fromcust == '')
		$fromc = _('All');
	else
		$fromc = get_customer_name($fromcust);

	$cols = array(0, 75, 175, 250, 300, 385, 510,	530);

	$headers = array(_('Description'), _(''), _(''), _(''), _(''), _('  Total Quantity'), _(''));
	if ($fromcust != '')
		$headers[2] = '';

	$aligns = array('left',	'left',	'center', 'center', 'center', 'center', 'center');

    $params =   array( 	0 => $comments,
    		    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    		    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    		    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    		    4 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''));

    $rep = new FrontReport(_('Bulk Delivery Order Report'), "InventorySalesReport", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$res = getTransactions($fromcust, $from, $to);
	$total0 = $grandtotal0 = 0.0;
        $grand_carton1 = 0.0; //shariq
	//$total1 = $grandtotal1 = 0.0;
	//$total2 = $grandtotal2 = 0.0;
	$catt = '';
	while ($trans=db_fetch($res))
	{
		
               //shariq for horizontal lines
                  
                $rep->Line($rep->row -2);	

				 if ($catt != $trans['cat_description'])
			{
				if ($catt != '')
				{
					//$rep->NewLine();
					//$rep->TextCol(0, 4, _('Total'));
					//$rep->AmountCol(3, 4, $total0, $dec);
					//$rep->AmountCol(4, 5, $total, $dec);
					//$rep->AmountCol(5, 6, $total1, $dec);
					//$rep->AmountCol(6, 7, $total2, $dec);
					//$rep->Line($rep->row - 2);
					//$rep->NewLine();
					//$rep->NewLine();
					$total0 = $total = $total1 = $total2 = 0.0;
				}
				//$rep->TextCol(0, 1, $trans['category_id']);
				//$rep->TextCol(1, 6, $trans['cat_description']);
				$catt = $trans['cat_description'];
				//$rep->NewLine();
			}




		$curr = get_customer_currency($trans['debtor_no']);
		$rate = get_exchange_rate_from_home_currency($curr, sql2date($trans['tran_date']));
		$trans['amt'] *= $rate;
		$cb = $trans['amt'] - $trans['cost'];
		$rep->NewLine();
		$rep->fontSize -= 2;
		//$rep->TextCol(0, 1, $trans['stock_id']);
		if ($fromcust == ALL_TEXT)
		{
                

			$rep->fontSize += 3;
            $rep->TextCol(0, 3, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
			//$rep->TextCol(2, 3, $trans['debtor_name']);
		}
		else
            $rep->TextCol(1, 3, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
		
                 $crtn = $trans['quantity']/$trans['carton'];
                 $round_crtn = floor($crtn); 
                 $dec_crtn  = $crtn - $round_crtn;
                 $loose_units = $dec_crtn * $trans['carton'];

			// $rep->AmountCol(4, 5, $loose_units, get_qty_dec($trans['stock_id']));// loose unit
			
			// $rep->AmountCol(4, 5, $loose_units);// loose unit
			
			// $rep->AmountCol(5, 6, $trans['quantity'], get_qty_dec($trans['stock_id']));// qty
			
			// $rep->AmountCol(4, 5, $crtn, get_qty_dec($trans['stock_id']));//TOTAL CARTON
      
	  			$tot_crtn = $trans['quantity']/$trans['carton'];
	  
	    	//  $rep->AmountCol(5, 6,$tot_crtn );//TOTAL CARTON

        	$var_qty = $trans['quantity'];
            $rep->AmountCol(5, 6, $var_qty );//TOTAL CARTON shariq


            $grandqty = $trans['quantity']/$tot_crtn;

	        //$rep->AmountCol(3, 4, $grandqty, get_qty_dec($trans['stock_id']));// qty
        	// $rep->AmountCol(3, 4, $grandqty);// qty

      
            $rep->fontSize -= 3;	  


          $rep->MultiCell(400, 624, "" , 1, 'L', 0, 2, 36,109, true);
          $rep->MultiCell(529, 624, "" , 1, 'L', 0, 2, 36,109, true);
          //$rep->MultiCell(76, 600, "" , 1, 'L', 0, 2, 350,133, true);
         // $rep->MultiCell(139, 600, "" , 1, 'L', 0, 2, 425.5,133, true);
	  
      // $rep->AmountCol(5, 6, floor($crtn), get_qty_dec($trans['stock_id']));//find
      //$rep->AmountCol(4, 5, $trans['amt'], $dec);
		//$rep->AmountCol(5, 6, $trans['cost'], $dec);
		//$rep->AmountCol(6, 7, $cb, $dec);
		$rep->fontSize += 2;
		$total0 += $trans['qty'];
		$total += $trans['amt'];
		$total1 += $trans['cost'];
		$total2 += $cb;

		//$grandtotal0 += $trans['qty'];
               $grandtotal  +=  $tot_crtn; //grand crtn 
               $grand_of_qty += $var_qty;
               $grand_grandqty += $grandqty;
		//$grandtotal += $trans['amt'];
		//$grandtotal1 += $trans['cost'];
		//$grandtotal2 += $cb;

         if ($rep->row < $rep->bottomMargin + (8 * $rep->lineHeight))
					$rep->NewPage();

	}
	$rep->NewLine();
	//$rep->TextCol(0, 4, _('Total'));
	//$rep->AmountCol(3, 4, $total0, $dec);
	//$rep->AmountCol(4, 5, $total, $dec);
	//$rep->AmountCol(5, 6, $total1, $dec);
	//$rep->AmountCol(6, 7, $total2, $dec);
	//$rep->Line($rep->row - 2);
	$rep->NewLine();
	//$rep->NewLine(2, 1);


/*
           $rep->NewLine(46.5);
      $rep->TextCol(5, 8, "___________________________");
      $rep->NewLine();
      $rep->TextCol(5, 7, "AUTHORIZED SIGNATURE");
       $rep->NewLine(-46.5);
*/ 
	//$rep->AmountCol(4, 5, $grandtotal, $dec);
	//$rep->AmountCol(5, 6, $grandtotal1, $dec);
	//$rep->AmountCol(6, 7, $grandtotal2, $dec);

//$rep->NewLine(42);

$rep->fontSize += 2;

/*
	$rep->TextCol(0, 4, _('Grand Total'));
	//$rep->AmountCol(4, 5, $grandtotal0, $dec);
        $rep->TextCol(5, 6,round($grandtotal), -2);// grnd crtn 
        $rep->TextCol(3, 4,round($grand_grandqty), -2);// grnd crtn 
*/

                //shariq
               $rep->MultiCell(200, 30, _('Grand Total') , 0, 'L', 0, 2, 36,735, true);         
             //  $rep->MultiCell(75, 30, round($grand_grandqty) , 0, 'C', 0, 2, 286,735, true);  
             //  $rep->MultiCell(130, 30, round($grandtotal) , 0, 'C', 0, 2, 426,735, true);


              $rep->MultiCell(130, 30, round($grand_of_qty) , 0, 'C', 0, 2, 426,735, true);

  $rep->MultiCell(160, 30, "___________________________" , 0, 'C', 0, 2, 410,805, true);
 $rep->MultiCell(160, 30, "AUTHORIZED SIGNATURE" , 0, 'C', 0, 2, 410,815, true);


$rep->fontSize -= 2;

	//$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

?>