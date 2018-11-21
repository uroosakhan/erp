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
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans WHERE type = 10
	
	AND order_ =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function getTransactions($category, $location, $fromsupp, $item, $from, $to, $brand)
{

    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT ".TB_PREF."stock_moves.trans_no,
    ".TB_PREF."stock_master.category_id,
    ".TB_PREF."stock_category.description AS cat_description,
    ".TB_PREF."stock_master.stock_id, 
    ".TB_PREF."stock_master.con_factor, 
    ".TB_PREF."stock_master.description,".TB_PREF."stock_master.inactive,
    ".TB_PREF."stock_moves.loc_code,".TB_PREF."stock_moves.type, 
    ".TB_PREF."debtors_master.debtor_no,
    ".TB_PREF."debtors_master.name, 
    ".TB_PREF."stock_moves.tran_date,
    ".TB_PREF."stock_moves.qty AS qty,
    ".TB_PREF."stock_moves.price,
    ".TB_PREF."cust_branch.tax_group_id,
    ".TB_PREF."debtor_trans.trans_no,
    ".TB_PREF."debtor_trans.order_,
    ".TB_PREF."debtor_trans.discount1,
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

    if ($brand != 0)
        $sql .= " AND ".TB_PREF."stock_master.combo1 = ".db_escape($brand);

    $sql .= " AND ".TB_PREF."stock_moves.type=13";

    if ($location != '')
        $sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($location);

    if ($item != '')
        $sql .= " AND ".TB_PREF."stock_master.stock_id = ".db_escape($item);

    $sql .= "
    ORDER BY
 ".TB_PREF."cust_branch.tax_group_id,
 ".TB_PREF."stock_moves.type,
 ".TB_PREF."stock_master.category_id, 
 ".TB_PREF."stock_moves.tran_date";
    return db_query($sql,"No transactions were returned");

}

//function getTransactions($category, $location, $fromsupp, $item, $from, $to)
//{
//
//    SELECT 0_stock_moves . trans_no FROM 0_stock_moves
//WHERE 0_stock_moves . trans_no = '219'
//AND 0_stock_moves . tran_date >= '2018-04-01'
//AND 0_stock_moves . tran_date <= '2018-04-30'
//AND 0_stock_moves . type = 13
//
//}




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
 AND ".TB_PREF."debtor_trans.type = ".db_escape($type)
        //AND  ".TB_PREF."cust_branch.tax_group_id=".db_escape($id)
    ;

    if ($category != 0)
        $sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);


    if ($fromsupp != ALL_TEXT)
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromsupp);


    if ($item != '')
        $sql .= " AND ".TB_PREF."stock_master.stock_id = ".db_escape($item);

    if ($location != '')
        $sql .= " AND ".TB_PREF."debtor_trans_details.item_location = ".db_escape($location);

    $sql .= " 
 ORDER BY
 ".TB_PREF."debtor_trans.tran_date,
 ".TB_PREF."debtor_trans.reference

";
    return db_query($sql,"No transactions were returned");

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
function get_multiple_gatepass($delivery_no)
{
    $sql = "SELECT *
		FROM ".TB_PREF."multiple_gate_pass
		WHERE delivery_no=".db_escape($delivery_no);
    $result = db_query($sql,"No gst returned");
    $row = db_fetch($result);
    return $row;
}
function get_invoice_no($order_)
{
    $sql = "SELECT *
		FROM ".TB_PREF."debtor_trans
		WHERE order_ =".db_escape($order_)."
		AND type = 10";
    $result = db_query($sql,"No gst returned");
    $row = db_fetch($result);
    return $row;
}
function get_items_details($debtor_trans_no)
{
    $sql = "SELECT *
		FROM ".TB_PREF."debtor_trans_details
		WHERE debtor_trans_no =".db_escape($debtor_trans_no)."
    AND debtor_trans_type = 10";
    $result = db_query($sql,"No gst returned");
    $row = db_fetch($result);
    return $row;
}
function get_sales_orders_details($order_no)
{
    $sql = "SELECT *
		FROM ".TB_PREF."sales_order_details
		WHERE order_no =".db_escape($order_no);
    $result = db_query($sql,"No gst returned");
    $row = db_fetch($result);
    return $row;
}
function get_sales_orders($order_no)
{
    $sql = "SELECT *
		FROM ".TB_PREF."sales_orders
		WHERE order_no =".db_escape($order_no);
    $result = db_query($sql,"No gst returned");
    $row = db_fetch($result);
    return $row;
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

function get_tax_description($id)
{
    $sql = "SELECT id, name
	FROM 
	".TB_PREF."tax_groups
	WHERE id=".db_escape($id);
    $result = db_query($sql,"No tax group found");
    return $result;
}

function print_inventory_purchase()
{
    global $path_to_root;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $fromsupp = $_POST['PARAM_4'];
    $item = $_POST['PARAM_5'];
    $brand = $_POST['PARAM_6'];
    $comments = $_POST['PARAM_7'];
    $orientation = $_POST['PARAM_8'];
    $destination = $_POST['PARAM_9'];
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

    $cols = array(0, 15, 30, 75, 110, 150, 190, 245, 260, 290, 350, 390, 430, 460, 500,540,575,615,640,685, 715,770);

    $aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'right', 'right', 'right','right', 'right', 'right','right', 'right', 'right','right',
        'right');

    $headers2 = array(_('S.'), _('DC'), _('Date of'), _('Gate'), _('Bill T No'), _('Bill T Date'),
        _('Transportation'), _('SI #'), _('SI Date'), _('Customer Name'), _('Details of Items'),
        _('Sales QTY'), _('Sales QTY'), _('Sales'), _('QTY'), _('Rate'), _('Amount'), _('Disc'),
        _('Amt net of'), _('GST'), _('Total Invoice'));
    $headers =  array(_('#'), _('#'), _('DC'), _('Pass #'), _(''), _(''), _(''), _(''), _(''), _(''),
        _(''), _('in Carton'), _('in Pack'), _('Return QTY'), _('Delivered'), _(''), _(''), _(''),
        _('Disc'), _('17%'), _('Amt'));

    if ($fromsupp != '')
        $headers[4] = '';


    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
        4 => array('text' => _('Customer'), 'from' => $froms, 'to' => ''),
        5 => array('text' => _('Item'), 'from' => $itm, 'to' => ''));

    $orientation = ('L');
    $rep = new FrontReport(_('Supply Register'), "InventoryPurchasingReport", user_pagesize(), 9, $orientation);
//    if ($orientation == 'L')
//        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns, $cols, $headers2, $aligns);
    $rep->NewPage();
    foreach($item as $key => $value) {
        $res = getTransactions($category, $location, $fromsupp, $value, $from, $to, $brand);

        $total = $total_supp = $grandtotal = 0.0;
        $total_qty = 0.0;

        $catt = $stock_description = $stock_id = '';

        //Credit Note
        $total_ve = $total_supp_ve = $grandtotal_ve = 0.0;
        $total_qty_ve = 0.0;
        $grand_tot1 = $grand_tot2 = $grand_tot3 = $grand_tot4 = $grand_tot5 = 0;
        //Invoice
        $total_inv = $total_supp_inv = $grandtotal_inv = 0.0;

        $tax_group = db_fetch_row(get_tax_description($trans['tax_group_id'])); //asad
        $query = getCreditTransactions($category, $location, $fromsupp, $item, $from, $to, 11, 1);

        $numOfRows = db_num_rows($res);
        if ($numOfRows > 0) {
            $rep->Font('bold');
            $rep->fontSize += 1;

            $rep->fontSize -= 1;
            $rep->Font();

            $sr = 0;
            while ($myrow = db_fetch($res)) {
                $rep->NewLine();
                $rep->fontSize -= 2;
                $rep->TextCol(0, 1, $sr = $sr + 1);

                $order_no = get_invoice_reference($myrow['trans_no'], $myrow['type']);

                $reference = get_invoice_referencess($order_no);
                $gate_pass = get_multiple_gatepass($myrow['trans_no']);
                $invoice_data = get_invoice_no($myrow['order_']);
                $sales_data = get_sales_orders_details($myrow['order_']);
                $sales_data2 = get_sales_orders($myrow['order_']);
                $item_detail = get_items_details($myrow['trans_no']);

                $rep->TextCol(1, 2, $myrow['trans_no']);
                $rep->DateCol(2, 3, $myrow["tran_date"], true);
                $rep->TextCol(3, 4, $gate_pass['gate_pass_no']);
                $rep->TextCol(4, 5, $invoice_data['text1']);
                $rep->DateCol(5, 6, $invoice_data['cheque_date'], true);
                $rep->TextCol(6, 7, $sales_data2['f_text1']);
                $rep->TextCol(7, 8, $invoice_data['reference']);
                $rep->TextCol(8, 9, $invoice_data['tran_date']);
                $rep->TextCol(9, 10, $myrow['name']);
                $rep->TextCol(10, 11, $myrow['description']);
                $rep->AmountCol(11, 12, ($sales_data['quantity'] / $myrow["con_factor"]), 2);
                $rep->AmountCol(12, 13, $sales_data['quantity'], 2);
                $rep->AmountCol(14, 15, abs($myrow['qty']), 2);
                if(!user_check_access('SA_ITEMSPRICES')) {
                    $rep->AmountCol(15, 16, $myrow['price'], 2);
                    $rep->AmountCol(16, 17, abs($myrow['qty'] * $myrow['price']), 2);
                    $rep->AmountCol(17, 18, $myrow['discount1'], 2);
                    $rep->AmountCol(18, 19, abs($myrow['qty'] * $myrow['price']) - $myrow['discount1'], 2);
                    $rep->AmountCol(19, 20, ($item_detail['unit_tax']), 2);
                    $rep->AmountCol(20, 21, (abs($myrow['qty'] * $myrow['price']) - $myrow['discount1']) + ($item_detail['unit_tax']), 2);
                }

                $amount = $myrow['qty'] * $myrow['price'];

                $sales_tax = ($amount * 17) / 100;
                $incl_tax = ($sales_tax + $amount);

                $rep->fontSize += 2;

                $grand_tot1 += $myrow['qty'] * $myrow['price'];
                $grand_tot2 += $myrow['discount1'];
                $grand_tot3 += abs($myrow['qty'] * $myrow['price']) - $myrow['discount1'];
                $grand_tot4 += ($item_detail['unit_tax']);
                $grand_tot5 += (abs($myrow['qty'] * $myrow['price']) - $myrow['discount1']) + ($item_detail['unit_tax']);

            }

            //while
            $rep->NewLine(2);
            $rep->Line($rep->row - 4);
            $rep->NewLine();
        }

    }


    $rep->NewLine(2, 1);

    $rep->TextCol(0, 7, _('Grand Total'));
    if(!user_check_access('SA_ITEMSPRICES')) {
        $rep->AmountCol(16, 17, abs($grand_tot1), $dec);
        $rep->AmountCol(17, 18, ($grand_tot2), $dec);
        $rep->AmountCol(18, 19, ($grand_tot3), $dec);
        $rep->AmountCol(19, 20, ($grand_tot4), $dec);
        $rep->AmountCol(20, 21, ($grand_tot5), $dec);
    }


    $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}

?>