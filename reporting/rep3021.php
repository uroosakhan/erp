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




function getTransactions($category, $location, $date)
{
    $date = date2sql($date);

    $sql = "SELECT * 
			FROM "
        .TB_PREF."stock_master item
	GROUP BY stock_id
	
	
			";
   
   
    if ($category != 0)
        $sql .= " AND item.category_id = ".db_escape($category);
   
    $sql .= " ORDER BY item.category_id,
			item.stock_id";

    return db_query($sql,"No transactions were returned");
}


//----------------------------------------------------------------------------------------------------

function print_inventory_valuation_report()
{
    global $path_to_root, $SysPrefs;

    $date = $_POST['PARAM_0'];
    $category = $_POST['PARAM_1'];
   
   
    $detail = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    $detail = !$detail;
    $dec = user_price_dec();

    $orientation = 'L' ;
    if ($category == ALL_NUMERIC)
        $category = 0;
    if ($category == 0)
        $cat = _('All');
    else
        $cat = get_category_name($category);

    $cols = array(4, 50, 130, 290, 340, 410, 440, 470, 520);

    $headers = array(_('Product Code'), _('Bar Code'), _('Description'), _('Opening Balance'), _('Brand'), _('Units'), _('Grams'), _('Pack Size'));

    $aligns = array('left',	'left',	'left', 'right', 'center','center','right','right');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('End Date'), 'from' => $date, 		'to' => ''),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''));

    $rep = new FrontReport(_('Inventory Items List Report'), "InventoryItemsListReport", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

   $res = getTransactions($category, $location, $date);

    $total = $grandtotal = $Qtytotal = $GrandQtytotal = 0.0;
    $catt = '';
    while ($trans=db_fetch($res))
    {

        $qoh_start = 0;

        $qoh_start += get_qoh_on_date($trans['stock_id'], $location);

 $rep->TextCol(0, 1, $trans['stock_id']);
 
  $rep->TextCol(1, 2, $trans['text3']);
 $rep->TextCol(2, 3, $trans['description']);
 $rep->AmountCol(3, 4, $qoh_start, get_qty_dec($trans['stock_id']));
 $rep->TextCol(4, 5, get_category_name($trans['category_id']));
 $rep->TextCol(5, 6, $trans['units']);
 $rep->TextCol(6, 7, $trans['text4']);
 $rep->TextCol(7, 8, $trans['carton']);

 $rep->NewLine(); 
 
           
        

    }
   
    if ($detail)
    {
        $rep->Line($rep->row - 2);
        $rep->NewLine();
    }
   
   
   
   
    $rep->End();
}