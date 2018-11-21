<?php
$page_security = 'SA_SUPPLY_REP';
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



function get_invoice_referencess ($trans_no)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans 
            WHERE type = 10
            AND order_ =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_unit_tax($trans_no, $stock_id)
{
    $sql = "SELECT 0_debtor_trans_details.debtor_trans_no 
    FROM 0_debtor_trans, 0_debtor_trans_details
 WHERE 0_debtor_trans.type = 13 
 AND 0_debtor_trans.trans_no =".db_escape($trans_no)." 
 AND 0_debtor_trans.trans_no =0_debtor_trans_details.debtor_trans_no
 AND 0_debtor_trans_details.stock_id=".db_escape($stock_id)."  ";
    /*$sql = "SELECT unit_tax FROM ".TB_PREF."debtor_trans_details
            WHERE debtor_trans_type = 13
            AND debtor_trans_no =".db_escape($trans_no)."
            AND stock_id =".db_escape($stock_id)."
            ";*/
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function getTransactions($category, $location, $fromsupp, $item, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT ".TB_PREF."stock_moves.trans_no,".TB_PREF."stock_master.category_id,
    ".TB_PREF."stock_category.description AS cat_description,
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

    $sql .= " ORDER BY
    ".TB_PREF."cust_branch.tax_group_id,
    ".TB_PREF."stock_moves.type,
    ".TB_PREF."stock_master.category_id, 
    ".TB_PREF."stock_moves.tran_date";
    return db_query($sql,"No transactions were returned");

}
//--------------Credit
function getCreditTransactions($category, $location, $fromsupp, $item, $from, $to, $type, $id)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT  ".TB_PREF."debtors_master.debtor_no,
            ".TB_PREF."stock_master.description,
            ".TB_PREF."debtors_master.name, 
            ".TB_PREF."debtor_trans.*,
            ".TB_PREF."debtor_trans_details.stock_id,
            ".TB_PREF."debtor_trans_details.quantity,
            ".TB_PREF."debtor_trans_details.unit_price,
            ".TB_PREF."debtor_trans_details.item_location
            FROM ".TB_PREF."stock_master, ".TB_PREF."cust_branch, ".TB_PREF."debtors_master, ".TB_PREF."debtor_trans,  ".TB_PREF."debtor_trans_details
            WHERE ".TB_PREF."debtor_trans.debtor_no = ".TB_PREF."debtors_master.debtor_no  
            AND ".TB_PREF."debtor_trans.branch_code = ".TB_PREF."cust_branch.branch_code 
            AND ".TB_PREF."cust_branch.debtor_no = ".TB_PREF."debtors_master.debtor_no
            AND ".TB_PREF."debtor_trans.trans_no = ".TB_PREF."debtor_trans_details.debtor_trans_no
            AND ".TB_PREF."debtor_trans.type = ".TB_PREF."debtor_trans_details.debtor_trans_type
            AND ".TB_PREF."stock_master.stock_id = ".TB_PREF."debtor_trans_details.stock_id
            AND ".TB_PREF."debtor_trans_details.quantity > 0
            AND ".TB_PREF."debtor_trans.tran_date>= '$from'
            AND ".TB_PREF."debtor_trans.tran_date<= '$to' 
            AND ".TB_PREF."debtor_trans.type = ".db_escape($type);
    //AND  ".TB_PREF."cust_branch.tax_group_id=".db_escape($id);

    if ($category != 0)
        $sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);
    if ($fromsupp != ALL_TEXT)
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromsupp);
    if ($item != '')
        $sql .= " AND ".TB_PREF."stock_master.stock_id = ".db_escape($item);
    if ($location != '')
        $sql .= " AND ".TB_PREF."debtor_trans_details.item_location = ".db_escape($location);
    $sql .= " ORDER BY ".TB_PREF."debtor_trans.tran_date, ".TB_PREF."debtor_trans.reference";
    return db_query($sql,"No transactions were returned");
}
//----------------------------------------------------------------------------------------------------
function get_gst_no($customer_id)
{
    $sql = "SELECT tax_id
		FROM ".TB_PREF."debtors_master
		WHERE ".TB_PREF."debtors_master.debtor_no=".db_escape($customer_id);
    $result = db_query($sql,"No gst returned");
    $row = db_fetch_row($result);
    return $row[0];

}
function get_ntn_no($customer_id)
{
    $sql = "SELECT ntn_id
		FROM ".TB_PREF."debtors_master
		WHERE ".TB_PREF."debtors_master.debtor_no=".db_escape($customer_id);
    $result = db_query($sql,"No ntn returned");
    $row = db_fetch_row($result);
    return $row[0];

}
//----------------------------------------------------------------------------------------------------
function get_tax_description($id)
{
    $sql = "SELECT id, name
	FROM ".TB_PREF."tax_groups
	WHERE id=".db_escape($id);
    $result = db_query($sql,"No tax group found");

    //$row = db_fetch_row($result);
    return $result;
}
//-------------------------------------------------------------------------------------------------
function get_price_for_item($stock_id)
{
    $sql = "SELECT price FROM ".TB_PREF."prices WHERE stock_id=".db_escape($stock_id);
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
//-------------------------------------------------------------------------------------------------
function get_payments_against_invoice($trans_type, $trans_no, $person_id)
{
    if($trans_type == 13)
        $trans_type = ST_SALESINVOICE;
    $sql = "SELECT *
            FROM ".TB_PREF."cust_allocations
			WHERE (trans_type_to=".db_escape($trans_type)." 
			AND trans_no_to=".db_escape($trans_no)." 
			AND person_id=".db_escape($person_id).")";
    return db_query($sql, "Error");
}

function get_tax_rate($trans_no, $tax_type_id)
{
    $sql = "SELECT rate, amount FROM ".TB_PREF."trans_tax_details
	WHERE trans_no = ".db_escape($trans_no)."
	AND tax_type_id = ".db_escape($tax_type_id)."
	AND trans_type = 10";
    $result = db_query($sql, "could not retreive default customer currency code");
    return db_fetch($result);

}
//-------------------------------------------------------------------------------------------------

function print_inventory_purchase()
{
    global $path_to_root;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $fromsupp = $_POST['PARAM_4'];
    $item = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $orientation = $_POST['PARAM_7'];
    $destination = $_POST['PARAM_8'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ('L');
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
    $cols = array(0, 25, 55, 90, 145, 170, 195, 225, 250, 280, 310, 340, 370, 420, 460);
    $aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left',  'left', 'right', 'right', 'right', 'right');
    $headers2 = array(_('Date'), _('Invoice'), _('Client')  , _('Product') , _('Item') , _('Quantity')   , _(' Unit ') , _('Discount') , _('Amnt Excl')   , _('Sales') , _('Amnt Incl')   , _('Sales')  , _('TTl PYMT against')  , _('Date of'));
    $headers =  array(_('')    , _('Number'),  _('Name')    , _('')        , _('Code') , _('')           , _(' Price') , _('')         , _('S.Tax')    , _('Tax')    , _('S.Tax')     , _('(PKR)')    , _(' this Inv.') , _('  Last Payment'));
    if ($fromsupp != '')
        $headers[4] = '';
    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
        4 => array('text' => _('Customer'), 'from' => $froms, 'to' => ''),
        5 => array('text' => _('Item'), 'from' => $itm, 'to' => ''));
//    $orientation = 'L';
    $rep = new FrontReport(_('Supply Register'), "SupplyRegister", user_pagesize(), 7, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns, $cols, $headers2, $aligns);
    $rep->NewPage();
    $res = getTransactions($category, $location, $fromsupp, $item, $from, $to);
    $total = $total_supp = $grandtotal = 0.0;
//    $total_qty = 0.0;
    $catt = $stock_description = $stock_id = '';
    //Credit Note
    $total_ve = $total_supp_ve = $grandtotal_ve = 0.0;
    $total_qty_ve = 0.0;
    //Invoice
    $total_inv = $total_supp_inv = $grandtotal_inv = 0.0;
//    $total_qty_inv = 0.0;
//    $prev_trans_no = 0.0;
//    $tax_group_id_trigger = 1;
//    $tax_group_id = ''; //asad
    //while ($trans = db_fetch($res))
    {
        //$first_id = $trans['tax_group_id'];
        //if($first_id != $second_id)
        {
//            $tax_group = db_fetch_row(get_tax_description($trans['tax_group_id'])); //asad
//            $query = getCreditTransactions($category, $location, $fromsupp, $item, $from, $to, 11, 1);
            $numOfRows = db_num_rows($res);
            if($numOfRows > 0) //some -ve results exist
            {
//                $rep->NewLine();
                $rep->Font('bold');
                $rep->fontSize += 1;
             //   $rep->TextCol( 0, 2,  $tax_group[1]." Credit Note");
                $rep->fontSize -= 1;
                $rep->Font();
//                $rep->NewLine();
                $TotalPayment = 0.0;
                $count = 0;
                while($myrow = db_fetch($res))
                {
                    $rep->NewLine();
                    $rep->fontSize -= 2;
                    $rep->TextCol(0, 1, sql2date($myrow['tran_date']));
                    $order_no = get_invoice_reference($myrow['trans_no'],$myrow['type']);
                    $reference=  get_invoice_referencess ($order_no);
                    $unit_tax=  get_unit_tax($myrow['trans_no'],$myrow['stock_id']);
                    $amount = $myrow['qty'] * $myrow['price'];

                    $amt_1 = get_tax_rate($unit_tax, 2);
                    $amt_2 = get_tax_rate($unit_tax, 3);
                    $amt_3= get_tax_rate($unit_tax, 4);
                    $sales_tax1 = ($amt_1['rate']/100) * (abs($amount));
                    $sales_tax2 = ($amt_2['rate']/100) * (abs($amount));
                    $sales_tax3 = ($amt_3['rate']/100) * (abs($amount));


                    $rep->TextCol(1, 2,$reference);
                    $rep->TextCol(4, 5, $myrow['stock_id']);
                    $rep->AmountCol(5, 6, abs($myrow['qty']), get_qty_dec($myrow['stock_id']));
                    if(!user_check_access('SA_ITEMSPRICES')) {
                        $rep->AmountCol(6, 7, $myrow['price'], $dec);
                        $rep->AmountCol(7, 8, $myrow['ov_discount'], $dec);

                        $rep->AmountCol(8, 9, abs($amount), $dec);
                         //manual entry of 17 done by dz 20.2.14

                        //	$rep->AmountCol(10, 11, $gst_rate . _('%'), $dec);
                        global $db_connections;
                        if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='ICTC' )
                        {
                            $sales_tax = $sales_tax1 + $sales_tax2 + $sales_tax3;
                            $incl_tax = ($sales_tax + abs($amount));
                            $rep->AmountCol(9, 10, $sales_tax, 2);
                            $rep->AmountCol(10, 11, abs($incl_tax), 2);
                        }
                        else
                        {
                            $sales_tax = ($amount * 17) / 100; //manual entry of 17 done by dz 20.2.14
                            $incl_tax = ($sales_tax + $amount);
                            $rep->AmountCol(9, 10, abs($sales_tax), 2);
                            $rep->AmountCol(10, 11, abs($incl_tax), 2);
                        }
                        $sales_price = get_price_for_item($myrow['stock_id']);
                        $rep->AmountCol(11, 12, abs($sales_price), 0);
                        $result = get_payments_against_invoice($myrow['type'], $myrow['trans_no'], $myrow['debtor_no']);
                        $TotalPayment = 0.0;
                        $lastPaymentDate = '';
                        while ($alloc = db_fetch($result)) {
                            $TotalPayment += $alloc['amt'];
                            $lastPaymentDate = $alloc['date_alloc'];
                        }
//                    while($fetch = db_fetch($result))
//                        $TotalPayment += $fetch['amt'];
                        $rep->AmountCol(12, 13, $TotalPayment, 0);
                    }
                    
                
                                     $rep->TextCollines(2, 3, $myrow['name']);


                    $rep->DateCol(13, 14, $lastPaymentDate, -2);
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
                    
                     $rep->NewLine(-1);
                    
                                                      $rep->TextCollines(3, 4, $myrow['description'].($myrow['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
                     $rep->NewLine(+1);

                } //while
                $rep->NewLine(2);
                $rep->Line($rep->row - 4);
                $rep->TextCol(0, 1, _('Total Credit'));
                $rep->AmountCol(5, 6, abs($total_qty_ve), $dec);
                if(!user_check_access('SA_ITEMSPRICES')) {
                    $rep->AmountCol(6, 7, abs($total_supp_ve), $dec);
                    $rep->AmountCol(9, 10, abs($total_sales_tax), $dec);
                    $rep->AmountCol(10, 11, abs($total_incl_tax), $dec);
                }
                $total_supp_ve = $total_qty_ve = 0.0;
                $rep->NewLine();
            }//if($numOfRows > 0)
            $queryInvoice = getCreditTransactions($category, $location, $fromsupp, $item, $from, $to, 10, 1);	        $numOfRowsInvoice = db_num_rows($queryInvoice);
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
    $rep->TextCol(0, 7, _('Grand Total'));
    $rep->AmountCol(5, 6, abs($grand_total_qty_inv) , $dec);
    if(!user_check_access('SA_ITEMSPRICES')) {
        $rep->AmountCol(6, 7, abs($grandtotal), $dec);
        $rep->AmountCol(9, 10, abs($grand_total_sales_tax), $dec);
        $rep->AmountCol(10, 11, abs($grand_total_incl_tax), $dec);
    }
    $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}
?>