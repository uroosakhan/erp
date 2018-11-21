<?php
//$page_security = 'SA_SUPPLY_REP';
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

print_inventory_purchase();




function get_invoice_reference($trans_no,$type)
{
    $sql = "SELECT order_ FROM ".TB_PREF."debtor_trans WHERE type = ".db_escape($type)."
	
	AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}


function get_cheque($trans_no)
{
    $sql = "SELECT cheque FROM ".TB_PREF."gl_trans WHERE type_no = ".db_escape($trans_no)."
    AND type =12
    AND amount < 0 ";
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}


function get_cust_amt($trans_no)
{
    $sql = "SELECT trans_no_from FROM ".TB_PREF."cust_allocations WHERE trans_no_to = ".db_escape($trans_no)."
    
    AND trans_type_from=12 AND trans_type_to =10";
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}



function get_gl_entries($trans_no)
{
    $sql = "SELECT * FROM ".TB_PREF."gl_trans WHERE type_no = ".db_escape($trans_no)."
    AND type =12
    AND amount > 0 " ;
   return  db_query($sql, "error");
    
}


function get_bank_entries($trans_no)
{
    $sql = "SELECT * FROM ".TB_PREF."bank_trans WHERE trans_no = ".db_escape($trans_no)."
    AND type =12" ;
   
      $result = db_query($sql, "error");
    return db_fetch($result);
    
}





function get_invoice_referencess ($trans_no)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans WHERE type = 10
	
	AND order_ =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function getTransactions($category, $location, $fromsupp, $item, $from, $to)
{

    $from = date2sql($from);
    $to = date2sql($to);
    
  /*  $sql = "SELECT ".TB_PREF."stock_moves.trans_no,".TB_PREF."stock_master.category_id,".TB_PREF."stock_category.description AS cat_description,
 ".TB_PREF."stock_master.stock_id, 
 ".TB_PREF."stock_master.description,".TB_PREF."stock_master.inactive,
 ".TB_PREF."stock_moves.loc_code,".TB_PREF."stock_moves.type, 
 ".TB_PREF."debtors_master.debtor_no,
 ".TB_PREF."debtors_master.name, 
 ".TB_PREF."stock_moves.tran_date,
 ".TB_PREF."stock_moves.qty AS qty,
 ".TB_PREF."cust_branch.tax_group_id,
  ".TB_PREF."debtor_trans.trans_no ,
 ".TB_PREF."debtor_trans.ov_discount,
 ".TB_PREF."stock_moves.price*(1- ".TB_PREF."stock_moves.discount_percent) AS price
 FROM ".TB_PREF."stock_master, ".TB_PREF."stock_category, ".TB_PREF."cust_branch, ".TB_PREF."stock_moves,".TB_PREF."debtors_master,".TB_PREF."debtor_trans
 WHERE ".TB_PREF."stock_master.stock_id= ".TB_PREF."stock_moves.stock_id
 AND ".TB_PREF."stock_master.category_id= ".TB_PREF."stock_category.category_id 
 AND ".TB_PREF."stock_moves.type = ".TB_PREF."debtor_trans.type
 AND ".TB_PREF."stock_moves.trans_no = ".TB_PREF."debtor_trans.trans_no
 AND ".TB_PREF."debtor_trans.debtor_no = ".TB_PREF."debtors_master.debtor_no 
 AND ".TB_PREF."cust_branch.debtor_no = ".TB_PREF."debtor_trans.debtor_no
 AND ".TB_PREF."stock_moves.tran_date>= '$from'
 AND ".TB_PREF."stock_moves.tran_date<= '$to' ";

    if ($fromsupp != ALL_TEXT)
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromsupp);

    if ($category != 0)
        $sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);

    $sql .= " AND ( ".TB_PREF."stock_moves.type=11 OR ".TB_PREF."stock_moves.type=13)";

    if ($location != '')
        $sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($location);

    if ($item != '')
        $sql .= " AND ".TB_PREF."stock_master.stock_id = ".db_escape($item);

    $sql .= "
    ORDER BY
 ".TB_PREF."cust_branch.tax_group_id,
 ".TB_PREF."stock_moves.type,
 ".TB_PREF."stock_master.category_id, 
 ".TB_PREF."stock_moves.tran_date";*/
 
    return db_query($sql,"No transactions were returned");

}

//--------------Credit
function getCreditTransactions( $from, $to,$fromsupp)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT  *
,trans_no
 FROM ".TB_PREF."debtor_trans
 WHERE ".TB_PREF."debtor_trans.tran_date>= '$from'
 AND ".TB_PREF."debtor_trans.tran_date<= '$to' 
  AND ".TB_PREF."debtor_trans.alloc != 0

 AND ".TB_PREF."debtor_trans.type = ".db_escape(10)

      //AND  ".TB_PREF."cust_branch.tax_group_id=".db_escape($id)
    ;
    if ($fromsupp != ALL_TEXT)
        $sql .= " AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($fromsupp);
    $sql .= " 
 ORDER BY
 ".TB_PREF."debtor_trans.tran_date,
 ".TB_PREF."debtor_trans.reference

";
    return db_query($sql,"No transactions were returned");

}

function get_income_taxes ($trans_no)
{
    $sql = "SELECT supply_disc,service_disc,st_challan,st_chalan_date, chalan,chalan_date FROM ".TB_PREF."debtor_trans WHERE type = 12
    AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch($result);
    return $row;
}
//----------------------------------------------------------------------------------------------------
function get_gst_no($customer_id)
{
    $sql = "SELECT tax_id
		FROM 
		".TB_PREF."debtors_master
		WHERE ".TB_PREF."debtors_master.debtor_no=".db_escape($customer_id);
    $result = db_query($sql,"No gst returned");
    $row = db_fetch_row($result);
    return $row[0];

}
function get_ntn_no($customer_id)
{
    $sql = "SELECT ntn_id
		FROM 
		".TB_PREF."debtors_master
		WHERE ".TB_PREF."debtors_master.debtor_no=".db_escape($customer_id);
    $result = db_query($sql,"No ntn returned");
    $row = db_fetch_row($result);
    return $row[0];

}
function get_date_order($order_no)
{
    $sql = "SELECT h_date2
		FROM 
		".TB_PREF."sales_orders
		WHERE ".TB_PREF."sales_orders.order_no=".db_escape($order_no);
    $result = db_query($sql,"No order no  returned");
    $row = db_fetch_row($result);
    return $row[0];

}
//----------------------------------------------------------------------------------------------------
function get_tax_description($id)
{
    $sql = "SELECT id, name
	FROM 
	".TB_PREF."tax_groups
	WHERE id=".db_escape($id);
    $result = db_query($sql,"No tax group found");

    //$row = db_fetch_row($result);
    return $result;
}

//-------------------------------------------------------------------------------------------------

function print_inventory_purchase()
{
    global $path_to_root;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
   
    $fromsupp = $_POST['PARAM_2'];
    
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    if ($category == ALL_NUMERIC)
        $category = 0;
    if ($category == 0)
        $cat = _('All');
    else
        $cat = get_category_name($category);

    if ($location == '')
        $loc = _('All');
    else
        $loc = get_location_name($location);


    if ($fromsupp == ALL_TEXT)
        $froms = _('All');
    else
        $froms = get_customer_name($fromsupp);

    if ($item == '')
        $itm = _('All');
    else
        $itm = $item;


$cols = array(0, 30, 80, 160, 220, 300, 360, 400, 440,	465, 490, 530, 560, 600,630);

    $aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left',  'left', 'left', 'right', 'left');

    $headers2 = array(_('Cheque'), _('Cheque'), _('Cheque')  , _('Sales') , _('S.T Challan No.') , _('Date')   , _('Income') , _('I.T Challan No.') , _('')   , _('Date') , _('Outstanding')   , _('Customer') );
    
    
    $headers =  array(_('No.')    , _('Date'),  _('Amount Recieved')    , _('Tax')        , _('/Certificate No.') , _('')           , _('Tax') , _('/Certificate No.') , _('')    , _('') , _('')     , _('Name')  );

    if ($fromsupp != '')
        $headers[4] = '';


    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
        4 => array('text' => _('Customer'), 'from' => $froms, 'to' => ''),
        5 => array('text' => _('Item'), 'from' => $itm, 'to' => ''));

    $orientation = L;
    $rep = new FrontReport(_('Income/Sales tax Register'), "InventoryPurchasingReport", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns, $cols, $headers2, $aligns);
    $rep->NewPage();

   // $res = getTransactions($category, $location, $fromsupp, $item, $from, $to);
    $res =   getCreditTransactions( $from, $to,$fromsupp);
    $total = $total_supp = $grandtotal = 0.0;
    $total_qty = 0.0;

    $catt = $stock_description = $stock_id = '';

    //Credit Note
    $total_ve = $total_supp_ve = $grandtotal_ve = 0.0;
    $total_qty_ve = 0.0;

    //Invoice
    $total_inv = $total_supp_inv = $grandtotal_inv = 0.0;
    $total_qty_inv = 0.0;
    $prev_trans_no = 0.0;
    $tax_group_id_trigger = 1;
    $tax_group_id = ''; //asad


    //while ($trans = db_fetch($res))
    {
        //$first_id = $trans['tax_group_id'];

        //if($first_id != $second_id)
        {
            $tax_group = db_fetch_row(get_tax_description($trans['tax_group_id'])); //asad
          
            $numOfRows = db_num_rows($res);
            if($numOfRows > 0) //some -ve results exist
            {
                $rep->NewLine();
                $rep->Font('bold');
                $rep->fontSize += 1;
             //   $rep->TextCol( 0, 2,  $tax_group[1]." Credit Note");
                $rep->fontSize -= 1;
                $rep->Font();
                $rep->NewLine();

                while($myrow = db_fetch($res))
                {
                    $rep->NewLine();
                    $rep->fontSize -= 2;
                    $cust_amt=   get_cust_amt($myrow['trans_no']);
            $income_taxes=   get_income_taxes ($cust_amt);
               

                $gl_entries=  get_gl_entries($cust_amt);
                
                 $cheque=  get_cheque($cust_amt);
                $bank_entries=  get_bank_entries($cust_amt);
               
                $rep->TextCol(0, 1, $cheque);
                $rep->TextCol(1, 2, sql2date($bank_entries['cheque_date']));
                $rep->TextCol(2, 3, ($myrow['alloc']));
             
                // $income_tax=$myrow['supply_disc']+$myrow['service_disc'];
             
                $rep->TextCol(3, 4,$myrow['ov_gst']);

               
                $rep->TextCol(4, 5,$income_taxes['st_challan']);
                $rep->TextCol(5, 6,sql2date($income_taxes['st_chalan_date']));


                
                $rep->TextCol(6, 7,$income_taxes['supply_disc']+$income_taxes['service_disc']);
                
                $rep->TextCol(11, 12,get_customer_name($myrow['debtor_no']));

                $order_no = get_invoice_reference($myrow['trans_no'],$myrow['type']);
                $reference=  get_invoice_referencess ($order_no);
            //   while($myrow1 = db_fetch($gl_entries))
            //     {
                    
                   $rep->TextCol(7, 8,$income_taxes['chalan']);//income tax
                    $rep->DateCol(8,9,"      " .sql2date($income_taxes['chalan_date']));//income chalan date

                      $rep->NewLine();

                // }
                 $rep->Line($rep->row  - 4);
              
                    $sales_tax = ($amount * 17)/100; //manual entry of 17 done by dz 20.2.14
                    $incl_tax = ($sales_tax + $amount);

                    //	$rep->AmountCol(10, 11, $gst_rate . _('%'), $dec);
                 //   $rep->AmountCol(10, 11, abs($sales_tax), 0);
                 //   $rep->AmountCol(11, 12, abs($incl_tax), 0);
                    $rep->fontSize += 2;

                    $total_ve += $amount;
                    $total_supp_ve += $amount;
                    $grandtotal_ve += $amount;
                    $total_qty_ve += $myrow['qty'];
                    $total_sales_tax += $sales_tax;
                    $total_incl_tax += $incl_tax;

                    $grandtotal_inv += $amount;
                    $grand_total_qty_inv += $myrow['qty'];
                    $grand_total_sales_tax += $sales_tax;
                    $grand_total_incl_tax += $incl_tax;

                } //while
                // $rep->NewLine(2);
                // $rep->Line($rep->row - 4);
                // $rep->TextCol(0, 1, _('Total Credit'));
                // $rep->AmountCol(5, 6, abs($total_qty_ve), $dec);
                // $rep->AmountCol(6, 7, abs($total_supp_ve), $dec);
                // $rep->AmountCol(10, 11, abs($total_sales_tax)  , $dec);
                // $rep->AmountCol(11, 12, abs($total_incl_tax) , $dec);
                // $total_supp_ve = $total_qty_ve = 0.0;
                $rep->NewLine();

            }//if($numOfRows > 0)
            // $queryInvoice = getCreditTransactions($category, $location, $fromsupp, $item, $from, $to, 10, 1);	        $numOfRowsInvoice = db_num_rows($queryInvoice);
            /*if($numOfRowsInvoice > 0) //some -ve results exist
            {


                $rep->NewLine();
                $rep->Font('bold');
                $rep->fontSize += 1;
                $rep->TextCol( 0, 2,  $tax_group[1]." Invoice");
                $rep->fontSize -= 1;
                $rep->Font();
                $rep->NewLine();

                while($meorow = db_fetch($queryInvoice))
                {

                    if($meorow['trans_no'] != $prev_trans_no)
                    {
                        $rep->Line($rep->row  - 4);
                    }

                    $rep->NewLine();


                    $rep->fontSize -= 2;
                    $rep->TextCol(0, 1, sql2date($meorow['tran_date']));

                    $rep->TextCol(1, 2, $meorow['reference']); //Invoice
                    $rep->TextCol(2, 3, $meorow['name']); //Customer
                    //$rep->TextCol(3, 4, get_ntn_no($meorow['debtor_no']) ); //NTN
                    //$rep->TextCol(4, 5, get_gst_no($meorow['debtor_no']) ); // STRN
                    $rep->TextCol(3, 4, $meorow['description'].($meorow['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
                    $rep->TextCol(4, 5, $meorow['stock_id']);
                    $rep->AmountCol(5, 6, $meorow['quantity'], get_qty_dec($meorow['stock_id']));
                    $rep->AmountCol(6, 7, $meorow['unit_price'], $dec);
                    $rep->AmountCol(7, 8, $meorow['ov_discount'], $dec);
                    $amount_inv = $meorow['quantity'] * $meorow['unit_price'];
                    $rep->AmountCol(9, 10, $amount_inv, 0);

                    $amt = $meorow['qty'] * $meorow['unit_price'];

                    $gst_rate = ($meorow['ov_gst']/$meorow['ov_amount'])*100;
                    $sales_tax = ($amount_inv * 17)/100; //manual entry of 17 done by dz 20.2.14
                    $incl_tax = ($sales_tax + $amount_inv);

                    //	$rep->AmountCol(10, 11, $gst_rate . _('%'), $dec);
                    $rep->AmountCol(10, 11, $sales_tax, 0);
                    $rep->AmountCol(11, 12, $incl_tax, 0);


                    $rep->fontSize += 2;


                    $total_inv += $amount_inv;
                    $total_supp_inv += $amount_inv;
                    $total_qty_inv += $meorow['quantity'];
                    $total_sales_tax += $sales_tax;
                    $total_incl_tax += $incl_tax;

                    $grandtotal_inv += $amount_inv;
                    $grand_total_qty_inv += $meorow['quantity'];
                    $grand_total_sales_tax += $sales_tax;
                    $grand_total_incl_tax += $incl_tax;

                    $prev_trans_no = $meorow['trans_no'];


                } //while

                $rep->NewLine(1);
                $rep->Line($rep->row - 4);
                $rep->TextCol(0, 1, _('Total Invoice'));
                $rep->AmountCol(5, 6, $total_qty_inv, $dec);
                $rep->AmountCol(6, 7, $total_supp_inv, $dec);
                $rep->AmountCol(10, 11, $total_sales_tax  , $dec);
                $rep->AmountCol(11, 12, $total_incl_tax , $dec);
                $total_supp_inv = $total_qty_inv = $total_sales_tax = $total_incl_tax = 0.0;
                $rep->NewLine();


            }*///if($numOfRows > 0)

        }

        $second_id = $trans['tax_group_id'];
    }//Main while Of Trans


    $rep->NewLine(2, 1);


    $grandtotal = $grandtotal_inv + $grandtotal_ve;   //Sum Of Both Credit And Invoice
    // $rep->TextCol(0, 7, _('Grand Total'));
    // $rep->AmountCol(5, 6, abs($grand_total_qty_inv) , $dec);
    // $rep->AmountCol(6, 7, abs($grandtotal), $dec);
    // $rep->AmountCol(10, 11, abs($grand_total_sales_tax), $dec);
    // $rep->AmountCol(11, 12, abs($grand_total_incl_tax), $dec);

    // $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}

?>