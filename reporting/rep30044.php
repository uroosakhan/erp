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

function getTransactions($from, $to,$category=null, $location=null,$customer)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT d.*,(d.quantity * d.unit_price) AS Trade_value , d.quantity,d.bonus,
                 (d.bonus*d.unit_price) AS scheme_value ,d.discount_percent
                 
                 FROM  ".TB_PREF."debtor_trans trans , 
                        ".TB_PREF."debtor_trans_details d ,
                        ".TB_PREF."stock_master stock_master,
                        ".TB_PREF."loc_stock loc,
                        ".TB_PREF."cust_allocations alloc
                 WHERE 
                 trans.`tran_date` >= '$from' AND
                 trans.`tran_date` <= '$to' 
                 
                 AND d.debtor_trans_no = alloc.trans_no_to 
                 AND d.debtor_trans_type = alloc.trans_type_to
                                      
                 
                 AND trans.`trans_no` = d.`debtor_trans_no` AND 
                 d.debtor_trans_type=10 AND trans.debtor_no=$customer";
//    if($items!=null)
//        $sql.=" AND d.`stock_id` ='$items'";

    if($category!=null)
        $sql.=" AND d.`stock_id` = stock_master.`stock_id` AND stock_master.`category_id` =".$category;

    if($location!=null)
        $sql.=" AND d.`stock_id` = loc.`stock_id` AND loc.`loc_code` ='$location'";

    $sql .=" GROUP BY d.`id`";

    return db_query($sql,"No transactions were returned");

}
function  get_credit_note_against_invoice($debtor_trans_no)
{

    $sql = "SELECT trans_no_from  FROM 0_cust_allocations 
                WHERE  0_cust_allocations.trans_no_to = ".db_escape($debtor_trans_no)."
                AND 0_cust_allocations.trans_type_to = 10 ";
    return db_query($sql,"No transactions were returned");
//    return  $result;
}
function  get_credit_note_values($cre_note_id)
{

    $sql = "SELECT * 
                 FROM ".TB_PREF."debtor_trans_details 
                 WHERE debtor_trans_type=11 AND debtor_trans_no =".db_escape($cre_note_id)."";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function check_if_cust_delivery($item,$trans_no)
{
    $sql = "SELECT id FROM ".TB_PREF."debtor_trans_details WHERE stock_id=".db_escape($item)."
            AND debtor_trans_no=".$trans_no." AND debtor_trans_type=11";
    return db_query($sql, "could not get customer");
}

//----------------------------------------------------------------------------------------------------

function print_inventory_sales()
{
    global $path_to_root;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $items = $_POST['PARAM_4'];
    $fromcust = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $destination = $_POST['PARAM_7'];
//    $show_service = $_POST['PARAM_5'];
//    $orientation = $_POST['PARAM_7'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

//    $orientation = ($orientation ? 'L' : 'P');
    $orientation = 'L';
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

    if ($fromcust == '')
        $fromc = _('All'); //14
    else
        $fromc = get_customer_name($fromcust);
    if ($show_service) $show_service_items = _('Yes');
    else $show_service_items = _('No');

    $cols2 = array(0, 120, 160, 190, 240, 280, 330,360, 410,473,555,610,665,710);
    $headers2 = array(_('Description'), _('Trade'), _('Qty'), _('Scheme'), _('Trade'), _('Scheme'), _('Qty'), _('Returned'), _('Scheme'), _('Scheme Qty'),         _('Net'),   _('Sales'), _('Disc %'),          _('Net'));
    $aligns2 = array('left','right',	'right', 'right', 'right', 'right', 'right'   ,   'right',	'right',	'right', 'right', 'right', 'right', 'right');


    $cols = array(0, 88, 112, 135, 163, 200, 230,263, 290,338,395,443,480,505);
    $headers = array(_(''), _('Price'),   _('Isue'),    _('Qty')   , _('Value'), _('Value'),  _('Return'), _('Value'),  _('Qty Return'),_('Return Value'), _('Sales Qty'), _('Amount'),  _('    '),  ('Amount'));
    $aligns =  array('left','right',	'right', 'right', 'right', 'right', 'right'   ,   'right',	'right',	'right', 'right', 'right', 'right', 'right');


    //    $cols = array(0, 75, 110, 130, 158, 210, 250,270,310,350,395,440,460,520,550);
//    $cols2 = array(0, 75, 110, 140, 170, 210, 250,270,310,350,400,430,440,510,590);

    if ($fromcust != '')
        $headers[2] = '';

//    $aligns = array('right',	'center',	'center', 'center', 'center', 'center', 'center','center',	'center',	'center', 'center', 'center', 'center', 'center');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
//        4 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''),
        5 => array('text' => _('Show Service Items'), 'from' => $show_service_items, 'to' => ''));

    $rep = new FrontReport(_('Inventory Sales And Return Report Customer Wise'), "InventorySalesReport", user_pagesize(), 7.5, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns,$cols2,$headers2,$aligns2);
    $rep->NewPage();


    $sql_cust = "SELECT DISTINCT(debtor_no) FROM 0_debtor_trans  Where type=11 ";
    if($fromcust!=null)
        $sql_cust.=" AND debtor_no=".$fromcust;

    $result_cust =  db_query($sql_cust,'no Customer Returns');

    while($customer_data = db_fetch($result_cust))
    {

        $res = getTransactions($from, $to,$category, $location,$customer_data['debtor_no']);
        if(db_num_rows($res)== 0) continue;

        $name = get_customer_name($customer_data['debtor_no']);
        $rep->TextCol(0, 1, $name);
        $rep->newline();

        $total_qty_returned2 =0;
        $total_returned_values2 =0;
        $total_scehemed_qty_returned2 =0;
        $total_scehemed_returned_values2 =0;
        $total_qty_soled2 =0;
        $total_qty_soled_amount2 =0;
        $total_discount2 =0;
        $total_net_amount2 =0;
        $total_trade_price =0;
        $total_qty_issued =0;
        $total_scheme_qty =0;
        $total_trade_value =0;
        $total_sceheme_value =0;



        while($trans=db_fetch($res))
        {
            $checker = check_if_cust_delivery($trans['stock_id'],$trans['debtor_trans_no']);
            if(db_num_rows($checker)== 0) continue;

            $rep->TextCol(0, 1, $trans['description']);
            $rep->AmountCol(1, 2, $trans['unit_price']);
            $rep->AmountCol(2, 3, $trans['quantity']);
            $rep->AmountCol(3, 4, $trans['bonus']);
            $rep->AmountCol(4, 5, $trans['Trade_value']);
            $rep->AmountCol(5, 6, $trans['scheme_value']);
            $total_invoiced_values = $trans['Trade_value'] + $trans['scheme_value'];

            //Return item Values
            $cre_note_id =  get_credit_note_against_invoice($trans['debtor_trans_no']);
            $total_qty_returned =0;

            $total_returned_values =0;
            $total_scehemed_qty_returned =0;
            $total_scehemed_returned_values =0;
            $total_qty_soled =0;
            $total_qty_soled_amount =0;
            $total_discount =0;
            $total_net_amount =0;

            while($cre = db_fetch($cre_note_id)) {

                $cre_note_detail = get_credit_note_values($cre['trans_no_from']);

                $rep->AmountCol(6, 7, $cre_note_detail['quantity']);
                $return_value = $cre_note_detail['unit_price'] * $cre_note_detail['quantity'];
                $rep->AmountCol(7, 8, $return_value);
                $rep->AmountCol(8, 9, $cre_note_detail['bonus']);
                $scheme_qty_ret_value = $cre_note_detail['bonus'] * $cre_note_detail['unit_price'];
                $rep->AmountCol(9, 10, $scheme_qty_ret_value);
                $total_cn_values = $cre_note_detail['bonus'] * $cre_note_detail['unit_price'] + $cre_note_detail['unit_price'] * $cre_note_detail['quantity'];
                //total items

                $total_sales_qty = ($trans['quantity'] + $trans['bonus']) - ($cre_note_detail['quantity'] + $cre_note_detail['bonus']);
                $total_sales_amount = $total_invoiced_values - $total_cn_values;
                $rep->AmountCol(10, 11, $total_sales_qty);
                $rep->AmountCol(11, 12, $total_sales_amount);
                $discount = $trans['discount_percent']  * 100;
                $rep->AmountCol(12, 13, $discount);
                $discounted_amount =   ($discount / 100) * $total_sales_amount ;
                $net_amnt = $total_sales_amount - $discounted_amount  ;

                $rep->AmountCol(13, 14, $net_amnt);


                $total_qty_returned += $cre_note_detail['quantity'];
                $total_returned_values += $return_value;
                $total_scehemed_qty_returned += $cre_note_detail['bonus'];
                $total_scehemed_returned_values += $scheme_qty_ret_value;
                $total_qty_soled += $total_sales_qty;
                $total_qty_soled_amount += $total_sales_amount;
                $total_discount += $discount ;
                $total_net_amount += $net_amnt;
                $rep->NewLine();

            }
//            $rep->NewLine(-1);
            $total_qty_returned2 +=$total_qty_returned;
            $total_returned_values2 +=$total_returned_values;
            $total_scehemed_qty_returned2 +=$total_scehemed_qty_returned;
            $total_scehemed_returned_values2 +=$total_scehemed_returned_values;
            $total_qty_soled2 +=$total_qty_soled;
            $total_qty_soled_amount2 +=$total_qty_soled_amount;
            $total_discount2 +=$total_discount;
            $total_net_amount2 += $total_net_amount;

            //        $rep->Line($rep->row + 8);
                $rep->NewLine();
            $total_trade_price  += $trans['unit_price'];
            $total_qty_issued  += $trans['quantity'];
            $total_scheme_qty  += $trans['bonus'];
            $total_trade_value  += $trans['Trade_value'];
            $total_sceheme_value  += $trans['scheme_value'];
        }
        $rep->Line($rep->row + 8);
        $rep->Line($rep->row - 1);
        $rep->TextCol(0, 1, _('Total'));
        $rep->AmountCol(1,2, $total_trade_price);
        $rep->AmountCol(2,3, $total_qty_issued);
        $rep->AmountCol(3,4, $total_scheme_qty);
        $rep->AmountCol(4,5, $total_trade_value);
        $rep->AmountCol(5,6, $total_sceheme_value);
        // Items Return details
        $rep->AmountCol(6,7, $total_qty_returned2);
        $rep->AmountCol(7,8, $total_returned_values2);
        $rep->AmountCol(8,9, $total_scehemed_qty_returned2);
        $rep->AmountCol(9,10,$total_scehemed_returned_values2);
        $rep->AmountCol(10,11,$total_qty_soled2);
        $rep->AmountCol(11,12,$total_qty_soled_amount2);
        $rep->AmountCol(12,13,$total_discount2);
        $rep->AmountCol(13,14,$total_net_amount2);
        $rep->NewLine();


        $total_trade_price2 +=$total_trade_price;
        $total_qty_issued2 +=$total_qty_issued;
        $total_scheme_qty2 +=$total_scheme_qty;
        $total_trade_value2 +=$total_trade_value;
        $total_sceheme_value2 +=$total_sceheme_value;
        //Grand total item_detail
        $grand_total_qty_returned +=$total_qty_returned2;
        $grand_total_returned_values +=$total_returned_values2;
        $grand_total_scehemed_qty_returned +=$total_scehemed_qty_returned2;
        $grand_total_scehemed_returned_values +=$total_scehemed_returned_values2;
        $grand_total_qty_soled +=$total_qty_soled2;
        $grand_total_qty_soled_amount +=$total_qty_soled_amount2;
        $grand_total_discount +=$total_discount2;
        $grand_total_net_amount +=$total_net_amount2;

        $rep->NewLine();


    }


    $rep->Line($rep->row - 1);
//    $rep->Line($rep->row - 16);
    $rep->NewLine();
    $rep->TextCol(0, 1, _('Grand Total'));
    $rep->AmountCol(1,2, $total_trade_price2);
    $rep->AmountCol(2,3, $total_qty_issued2);
    $rep->AmountCol(3,4, $total_scheme_qty2);
    $rep->AmountCol(4,5, $total_trade_value2);
    $rep->AmountCol(5,6, $total_sceheme_value2);
    //Grand total item_detail
    $rep->AmountCol(6,7, $grand_total_qty_returned);
    $rep->AmountCol(7,8, $grand_total_returned_values);
    $rep->AmountCol(8,9, $grand_total_scehemed_qty_returned);
    $rep->AmountCol(9,10, $grand_total_scehemed_returned_values);
    $rep->AmountCol(10,11, $grand_total_qty_soled);
    $rep->AmountCol(11,12, $grand_total_qty_soled_amount);
    $rep->AmountCol(12,13, $grand_total_discount);
    $rep->AmountCol(13,14, $grand_total_net_amount);
    $rep->Line($rep->row - 1);

    $rep->End();
}
