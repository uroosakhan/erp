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

function get_purch_price($stock_id)
{
    $sql = "SELECT price 
    FROM ".TB_PREF."purch_data 
    WHERE stock_id = ".db_escape($stock_id)
   ;
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

function getTransactions($category, $location, $date)
{
    $date = date2sql($date);

    $sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id,
			item.units,
			item.description, 
			item.carton, 
			item.inactive,
			move.loc_code,
			move.price,
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

function get_item_price_301($stock_id,$sales_type)
{
    $sql = "SELECT  `price` 
FROM  `".TB_PREF."prices` 
WHERE  `stock_id` =  '$stock_id'
AND  `sales_type_id` =$sales_type";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_inventory_valuation_report()
{
    global $path_to_root, $SysPrefs;

    $date = $_POST['PARAM_0'];
    $category = $_POST['PARAM_1'];
    $location = $_POST['PARAM_2'];
    $price_st = $_POST['PARAM_3'];
    $salestype = $_POST['PARAM_4'];
    $supplier = $_POST['PARAM_5'];
    $detail = $_POST['PARAM_6'];
    $comments = $_POST['PARAM_7'];
    $orientation = $_POST['PARAM_8'];
    $destination = $_POST['PARAM_9'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    $detail = !$detail;
    $dec = user_price_dec();

    $orientation = ($orientation ? 'L' : 'P');
    if ($category == ALL_NUMERIC)
        $category = 0;
    if ($category == 0)
        $cat = _('All');
    else
        $cat = get_category_name($category);

    if ($location == ALL_TEXT)
        $location = 'all';
    if ($location == 'all')
        $loc = _('All');
    else
        $loc = get_location_name($location);

    $cols = array(0, 75, 90, 230, 310, 400 ,320,515);
    if($price_st == 0)
        $headers = array(_('Sr# Code'), '', _('Product Name'), _('Packing Size'), _('Product Type'), _('Cost Price'), _('Retail Price'));
    elseif($price_st == 1)
        $headers = array(_('Sr# Code'), '', _('Product Name'), _('Packing Size'), _('Product Type'), _('Cost Price'), _('Retail Price'));
    else
        $headers = array(_('Sr# Code'), '', _('Product Name'), _('Packing Size'), _('Product Type'), _('Cost Price'), _('Retail Price'));
    $aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'right');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('End Date'), 'from' => $date, 		'to' => ''),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''));

    $rep = new FrontReport(_('Inventory Valuation Report'), "InventoryValReport", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();


        $res = getTransactions($category, $location, $date);

    $total = $grandtotal = $Qtytotal = $GrandQtytotal = 0.0;
    $catt = '';
    while ($trans=db_fetch($res)) {
        if ($catt != $trans['cat_description']) {
            if ($catt != '') {
                if ($detail) {
                    $rep->NewLine(2, 3);
                    $rep->TextCol(0, 4, _('Total'));
                }
//                if(!$salestype)
                $rep->AmountCol(5, 6, $total, $dec);
                //  $rep->AmountCol(3, 4, $Qtytotal, get_qty_dec($trans['stock_id']));
                if ($detail) {
                    $rep->Line($rep->row - 2);
                    $rep->NewLine();
                }
                $rep->NewLine();
                $total = $Qtytotal = 0.0;
            }
            $rep->TextCol(0, 1, $trans['category_id']);
            $rep->TextCol(1, 3, $trans['cat_description']);

            $catt = $trans['cat_description'];
            if ($detail)
                $rep->NewLine();
        }

        {
            $UnitCost = $trans['UnitCost'];
            $ItemTotal = $trans['ItemTotal'];
        }

        if ($detail) {
            $rep->NewLine();
            $rep->fontSize -= 2;
            $rep->TextCol(0, 1, $trans['stock_id']);
            $rep->TextCol(1, 3, $trans['description'] . ($trans['inactive'] == 1 ? " (" . _("Inactive") . ")" : ""), -1);
            $rep->TextCol(3, 4, $trans['carton'] . " " . $trans['units']);
            $purch_price = get_purch_price($trans['stock_id']);
            $rep->AmountCol(6, 7, $purch_price, $dec);

            $dec2 = 0;
            $item = get_item($trans['stock_id']);
            price_decimal_format($UnitCost, $dec2);
            //  $rep->AmountCol(4, 5, $UnitCost, $dec2);
            $price = get_price($trans['stock_id'], null, $salestype);
            $rep->AmountCol(5, 6, $trans['ItemTotal'], $dec);


            $total += $purch_price * $trans['QtyOnHand'];
            $grandtotal += $purch_price * $trans['QtyOnHand'];
        }



//            if(!$salestype)
//            $rep->AmountCol(5, 6, $ItemTotal, $dec);
            $rep->fontSize += 2;


        $Qtytotal += $trans['QtyOnHand'];
        $GrandQtytotal += $trans['QtyOnHand'];
    }
    if ($detail)
    {
//        $rep->NewLine(2, 3);
//        $rep->TextCol(0, 4, _('Total'));
    }
//    if(!$salestype)
//        $rep->Amountcol(5, 6, $total, $dec);
//    $rep->Amountcol(3, 4, $Qtytotal, $dec);
    if ($detail)
    {
        $rep->Line($rep->row - 2);
        $rep->NewLine();
    }
//    $rep->NewLine(2, 1);
//    $rep->TextCol(0, 4, _('Grand Total'));
//    if(!$salestype)
//        $rep->AmountCol(5, 6, $grandtotal, $dec);
//    $rep->AmountCol(3, 4, $GrandQtytotal, $dec);
//    $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}