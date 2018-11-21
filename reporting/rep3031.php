
Conversation opened. 1 unread message.

Skip to content
Using Gmail with screen readers

  More 
1 of 682
 
inventory val summary 
Inbox
x 

Ramsha Ather
Attachments5:45 PM (16 minutes ago)
to me 

$reports->addReport(RC_INVENTORY,  3010, _('Inventory &Valuation Summary'),
    array( _('End Date') => 'DATE',
        _('Items') => 'ITEMS_ALL_',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

Attachments area
	
Click here to Reply or Forward
2.94 GB (19%) of 15 GB used
Manage
Terms ¡¤ Privacy ¡¤ Program Policies
Last account activity: 1 hour ago
Details


<?php
/**********************************************************************
Copyright (C) FrontAccounting, LLC.
Released under the terms of the GNU General Public License, GPL,
as published by the Free Software Foundation, either version 3
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
$page_security = 'SA_ITEMSVALREP';
// ----------------------------------------------------------------
// $ Revision:	2.4 $
// Creator:		Joe Hunt, boxygen
// date_:		2014-05-13
// Title:		Inventory Valuation
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_inventory_valuation_report();

function get_domestic_price($myrow, $stock_id)
{
    if ($myrow['type'] == ST_SUPPRECEIVE || $myrow['type'] == ST_SUPPCREDIT)
    {
        $price = $myrow['price'];
        if ($myrow['person_id'] > 0)
        {
            // Do we have foreign currency?
            $supp = get_supplier($myrow['person_id']);
            $currency = $supp['curr_code'];
            $ex_rate = get_exchange_rate_to_home_currency($currency, sql2date($myrow['tran_date']));
            $price /= $ex_rate;
        }
    }
    else
        $price = $myrow['standard_cost']; // Item Adjustments just have the real cost
    return $price;
}

function get_purch_price($stock_id, $person_id)
{
    $sql = "SELECT price 
    FROM ".TB_PREF."purch_data 
    WHERE stock_id = ".db_escape($stock_id)."
    AND supplier_id = ".db_escape($person_id);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function getAverageCost($stock_id, $location, $to_date)
{
    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT move.*, IF(ISNULL(supplier.supplier_id), debtor.debtor_no, supplier.supplier_id) person_id
  		FROM ".TB_PREF."stock_moves move
				LEFT JOIN ".TB_PREF."supp_trans credit ON credit.trans_no=move.trans_no AND credit.type=move.type
				LEFT JOIN ".TB_PREF."grn_batch grn ON grn.id=move.trans_no AND 25=move.type
				LEFT JOIN ".TB_PREF."suppliers supplier ON IFNULL(grn.supplier_id, credit.supplier_id)=supplier.supplier_id
				LEFT JOIN ".TB_PREF."debtor_trans cust_trans ON cust_trans.trans_no=move.trans_no AND cust_trans.type=move.type
				LEFT JOIN ".TB_PREF."debtors_master debtor ON cust_trans.debtor_no=debtor.debtor_no
			WHERE stock_id=".db_escape($stock_id)."
			AND move.tran_date < '$to_date' AND standard_cost > 0.001 AND qty <> 0 AND move.type <> ".ST_LOCTRANSFER;

    if ($location != 'all')
        $sql .= " AND move.loc_code = ".db_escape($location);

    $sql .= " ORDER BY tran_date";

    $result = db_query($sql, "No standard cost transactions were returned");

    if ($result == false)
        return 0;
    $qty = $tot_cost = 0;
    while ($row=db_fetch($result))
    {
        $qty += $row['qty'];
        $price = get_domestic_price($row, $stock_id);
        $tran_cost = $row['qty'] * $price;
        $tot_cost += $tran_cost;
    }
    if ($qty == 0)
        return 0;
    return $tot_cost / $qty;
}

function getTransactions($items, $date)
{
    $date = date2sql($date);

    $sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id,
			item.description,
			SUM(price) as Price,
			move.type
			FROM "
        .TB_PREF."stock_master item,"
        .TB_PREF."stock_category category,"
        .TB_PREF."stock_moves move
		WHERE item.stock_id=move.stock_id
		AND item.category_id=category.category_id
		AND item.mb_flag<>'D' AND mb_flag <> 'F' 
		AND move.tran_date <= '$date'
		 ";
    if ($items != '')
        $sql .= " AND item.stock_id = ".db_escape($items);
    $sql .= " GROUP BY 
			item.stock_id";

    return db_query($sql,"No transactions were returned");
}

function getTransactions_with_std_cost($category, $location, $date)
{
    $date = date2sql($date);

    $sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id,
			item.units,
			item.description, item.inactive,
			move.loc_code,
			SUM(move.qty) AS QtyOnHand, 
			item.material_cost AS UnitCost,
			SUM(move.qty) * item.material_cost AS ItemTotal 
			FROM "
        .TB_PREF."stock_master item,"
        .TB_PREF."stock_category category,"
        .TB_PREF."stock_moves move
		WHERE item.stock_id=move.stock_id
		AND item.category_id=category.category_id
		AND item.mb_flag<>'D' AND mb_flag <> 'F' 
		AND move.tran_date <= '$date'
		AND item.material_cost > 0
		GROUP BY item.category_id,
			category.description, ";
    if ($location != 'all')
        $sql .= "move.loc_code, ";
    $sql .= "item.stock_id,
			item.description
		HAVING SUM(move.qty) != 0";
    if ($category != 0)
        $sql .= " AND item.category_id = ".db_escape($category);
    if ($location != 'all')
        $sql .= " AND move.loc_code = ".db_escape($location);
    $sql .= " ORDER BY item.category_id,
			item.stock_id";

    return db_query($sql,"No transactions were returned");
}

function get_purchase($stock_id, $date)
{
    $date = date2sql($date);
    $sql = "SELECT SUM(qty) as QTY, SUM(price) as Price
    FROM  ".TB_PREF."stock_moves 
    WHERE  stock_id =  ".db_escape($stock_id)."
    AND tran_date = ".db_escape($date)."
    AND  type = 25";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch($result);

    return $row;
}

function get_purchase_return($stock_id, $date)
{
    $date = date2sql($date);
    $sql = "SELECT SUM(qty) as QTY, SUM(price) as Price
    FROM  ".TB_PREF."stock_moves 
    WHERE  stock_id =  ".db_escape($stock_id)."
    AND tran_date = ".db_escape($date)."
    AND  type = 21";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch($result);

    return $row;
}

function get_sold_qty($stock_id, $date)
{
    $date = date2sql($date);
    $sql = "SELECT SUM(qty) as QTY, SUM(price) as Price, standard_cost
    FROM  ".TB_PREF."stock_moves 
    WHERE  stock_id =  ".db_escape($stock_id)."
    AND tran_date <= ".db_escape($date)."
    AND  type = 13";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch($result);

    return $row;
}

function get_sale_return($stock_id, $date)
{
    $date = date2sql($date);
    $sql = "SELECT SUM(qty) as QTY, SUM(price) as Price, standard_cost
    FROM  ".TB_PREF."stock_moves 
    WHERE  stock_id =  ".db_escape($stock_id)."
    AND tran_date <= ".db_escape($date)."
    AND  type = 11";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch($result);

    return $row;
}
//----------------------------------------------------------------------------------------------------

function print_inventory_valuation_report()
{
    global $path_to_root, $SysPrefs;

    $date = $_POST['PARAM_0'];
    $items = $_POST['PARAM_1'];
    $comments = $_POST['PARAM_7'];
    $orientation = $_POST['PARAM_8'];
    $destination = $_POST['PARAM_9'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $dec = user_price_dec();

    $orientation = ('L');

    $cols = array( 150,  240,  280, 360,  480,  570, 660);
    $cols2 = array(0, 40, 150, 190, 230, 260, 300, 330, 380, 410, 460, 510, 540, 580, 630, 700, 750, 800);

    $headers = array( _('Opening Balance'),  _('Purchases'),
        _('Purchase Return'), _('Goods Available for Sale'),_('Closing Balance'),
        _('Sale & Return Qty'),  _('Cost of Goods Sold'));
    $headers2 = array(_('Product Code'), _(' Product Name'), _('Qty'), _('Value'), _('Qty'), _('Value'),
        _('Qty'), _('Value'),_('Qty'), _('Value'), _('Avg Cost'),_('Qty'), _('Value'),
        _('Sold Qty'), _('S.R. Qty'),_('Qty'), _('Value'));

    $aligns = array('left',	'left',	'right', 'right', 'right', 'right',	'right', 'right', 'right', 'right',
        'right', 'right', 'right',	'right', 'right', 'right', 'right');
    $aligns2 = array('left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('End Date'), 'from' => $date, 'to' => ''));

    $rep = new FrontReport(_('Inventory Valuation Report'), "InventoryValReport", user_pagesize(), 9, $orientation);

    $rep->Font();
    $rep->Info($params, $cols2, $headers2, $aligns2, $cols, $headers, $aligns);
    $rep->NewPage();

    $res = getTransactions($items, $date);

    $total = $grandtotal = $Qtytotal = $GrandQtytotal = 0.0;
    $catt = '';
    while ($trans=db_fetch($res))
    {

    $rep->NewLine();
    $qoh_start = 0;
    $rep->TextCol(0, 1, $trans['stock_id']);
    $rep->TextCol(1, 2, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
    $qoh_start += get_qoh_on_date($trans['stock_id'], null, add_days($date, -1));
    $purchase=get_purchase($trans['stock_id'], $date);
    $purchase_return=get_purchase_return($trans['stock_id'], $date);
    $customer_delivery=get_sold_qty($trans['stock_id'], $date);
    $delivered_qty =  -$customer_delivery['QTY'];
    $sales_return=get_sale_return($trans['stock_id'], $date);

    $rep->AmountCol(2, 3, $qoh_start);
    $rep->AmountCol(3, 4, $qoh_start * $trans['Price']);
    $rep->AmountCol(4, 5, $purchase['QTY']);
    $rep->AmountCol(5, 6, $purchase['QTY'] * $purchase['Price']);
    $rep->AmountCol(6, 7, $purchase_return['QTY']);
    $rep->AmountCol(7, 8, $purchase_return['QTY']*$purchase_return['Price']);
    $rep->AmountCol(8, 9, $qoh_start + $purchase['QTY'] - $purchase_return['QTY']);
    $rep->AmountCol(9, 10, ($qoh_start * $trans['Price']) + ($purchase['QTY'] * $purchase['Price']) -($purchase_return['QTY']*$purchase_return['Price']));
    $rep->AmountCol(13, 14 ,$delivered_qty);
    $rep->AmountCol(14, 15, $sales_return['QTY']);
    $rep->AmountCol(15, 16,$delivered_qty - $sales_return['QTY'] );




    }

    $rep->NewLine(2, 1);
    $rep->TextCol(0, 4, _('Grand Total'));

    $rep->AmountCol(5, 6, $grandtotal, $dec);
    $rep->AmountCol(3, 4, $GrandQtytotal, $dec);
    $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}
