<?php
$page_security = 'SA_ITEMSVALREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Stock Check Sheet
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");

//----------------------------------------------------------------------------------------------------

print_stock_check();

function getTransactions($category, $location, $rep_date)
{
	$sql = "SELECT ".TB_PREF."stock_master.category_id,
			".TB_PREF."stock_category.description AS cat_description,
			".TB_PREF."stock_master.stock_id,
			".TB_PREF."stock_master.material_cost,
			".TB_PREF."stock_master.description,
			".TB_PREF."stock_master.units,
            ".TB_PREF."stock_master.inactive,
            ".TB_PREF."stock_master.text1,
			IF(".TB_PREF."stock_moves.stock_id IS NULL, '', ".TB_PREF."stock_moves.loc_code) AS loc_code,
			SUM(IF(".TB_PREF."stock_moves.stock_id IS NULL,0,".TB_PREF."stock_moves.qty)) AS QtyOnHand,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'ISD' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS   ISD,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'ISD-K' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS ISDK,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'ISD-L' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS ISDL,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'KHI' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS   KHI,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'KHI-I' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS KHII,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'KHI-L' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS KHIL,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'LHR' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS   LHR,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'LHR-I' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS LHRI,
            SUM(CASE WHEN ".TB_PREF."stock_moves.loc_code = 'LHR-K' THEN ".TB_PREF."stock_moves.qty ELSE NULL END) AS LHRK
		FROM (".TB_PREF."stock_master,
			".TB_PREF."stock_category)
		LEFT JOIN ".TB_PREF."stock_moves ON
			(".TB_PREF."stock_master.stock_id=".TB_PREF."stock_moves.stock_id)
		WHERE ".TB_PREF."stock_master.category_id=".TB_PREF."stock_category.category_id

		AND (".TB_PREF."stock_master.mb_flag='B' OR ".TB_PREF."stock_master.mb_flag='M')";

	if ($category != 0)
		$sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);
	if ($rep_date != 0)
		$sql .= " AND IF(".TB_PREF."stock_moves.stock_id IS NULL, '1=1',".TB_PREF."stock_moves.tran_date <= '$rep_date')";
	//	$sql .= " AND ".TB_PREF."stock_moves.tran_date <= '$rep_date'";	
	if ($location != 'all')
		$sql .= " AND IF(".TB_PREF."stock_moves.stock_id IS NULL, '1=1',".TB_PREF."stock_moves.loc_code = ".db_escape($location).")";

	$sql .= " GROUP BY ".TB_PREF."stock_master.category_id,
		".TB_PREF."stock_category.description,
		".TB_PREF."stock_master.stock_id";

	$sql .= "	ORDER BY ".TB_PREF."stock_master.stock_id";

    return db_query($sql,"No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_stock_check()
{
    global $path_to_root, $pic_height;

		$rep_date = $_POST['PARAM_0'];
    	$category = $_POST['PARAM_1'];
    	$location = $_POST['PARAM_2'];
    	$show_value = $_POST['PARAM_3'];
    // 	$check    = $_POST['PARAM_4'];
    // 	$shortage = $_POST['PARAM_5'];
    // 	$no_zeros = $_POST['PARAM_6'];
    // 	$comments = $_POST['PARAM_7'];
// 		$orientation = $_POST['PARAM_8'];
		$destination = $_POST['PARAM_4'];
		$no_zeros = $_POST['PARAM_5'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = 'P';
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
	if ($show_value)
	{
		$short = _('On Value');
	}
	else
	{
		$short = _('On Quantity');
	}
	$sql = "SELECT * FROM ".TB_PREF."item_pref WHERE name = 'text1'";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);

// 	if ($no_zeros) $nozeros = _('Yes');
// 	else $nozeros = _('No');
/*	if ($check)
	{
		$cols = array(0, 75, 225, 250, 295, 345, 390, 445, 495, 520);
		$headers = array(_('Stock ID'), _('Description'), _('UOM'), _('Quantity'), _('Check'), _('Demand'), $available, _('On Order'));
		$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right', 'right', 'right');
	}
	else*/
	{
		$cols = array(-20, 80, 150, 180, 230, 270, 300, 340, 380, 415, 450, 490, 520);
		$headers = array(_('Description'), _($fetch['label_value']),_('ISD'), _('ISD-K'), _('ISD-L'), _('KHI'), _('KHI-I') , _('KHI-L'), _('LHR'), _('LHR-I'),_('LHR-K'), _('Total')/*, _('DISH'), _('MCP'), _('OBFL'), _('OBSP'), _('OBST'), _('MCR-C')*/);
		$aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right');
	}
    	$params =   array( 	
         	0 => $comments,
        1=> array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    2 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
        3 => array('text' => _('Show'), 'from' => $short, 'to' => '')
);

//	if ($pictures)
//		$user_comp = user_company();
//	else
//		$user_comp = "";

   	$rep = new FrontReport(_('Multiple Location Stock Report'), "MultipleLocationStockReport", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
	$res = getTransactions($category, $location, date2sql($rep_date));
	$SrNo = 1;
	while ($trans=db_fetch($res))
	{	
	    
	    if($no_zeros && $trans['ISD']*$trans['material_cost']==0  && 
	$trans['ISDK']*$trans['material_cost']==0  &&
	
	$trans['ISDL']*$trans['material_cost']==0  &&
	
	$trans['KHI']*$trans['material_cost']==0
	
	&& 
	
	$trans['KHII']*$trans['material_cost']==0  &&
	
	$trans['KHIL']*$trans['material_cost']==0 &&
	
	$trans['LHR']*$trans['material_cost']==0  &&
	
	$trans['LHRI']*$trans['material_cost']==0  &&
	
	$trans['LHRK']*$trans['material_cost']==0  )
		continue;
		
		
		$rep->NewLine();
		$dec = $line_total = 0;

		
		$rep->TextCol(0, 1, $trans['description']);
		$rep->TextCol(1, 2, $trans['text1']);
		if($show_value){
		    $item_total = $trans['ISD']*$trans['material_cost'];
		    $line_total += $trans['ISD']*$trans['material_cost'];
		    $rep->AmountCol(2, 3, $item_total, $dec);
		}
		else    
		    $rep->AmountCol(2, 3,   $trans['ISD'], $dec);
		    
		if($show_value){
		    $item_total = $trans['ISDK']*$trans['material_cost'];
		    $line_total += $trans['ISDK']*$trans['material_cost'];
		    $rep->AmountCol(3, 4, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(3, 4,   $trans['ISDK'], $dec);
		   
		if($show_value){
		    $item_total = $trans['ISDL']*$trans['material_cost'];
		    $line_total += $trans['ISDL']*$trans['material_cost'];
		    $rep->AmountCol(4, 5, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(4, 5,   $trans['ISDL'], $dec);
		   		   
		if($show_value){
		    $item_total = $trans['KHI']*$trans['material_cost'];
		    $line_total += $trans['KHI']*$trans['material_cost'];
		    $rep->AmountCol(5, 6, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(5, 6,   $trans['KHI'], $dec);
		   		   
		if($show_value){
		    $item_total = $trans['KHII']*$trans['material_cost'];
		    $line_total += $trans['KHII']*$trans['material_cost'];
		    $rep->AmountCol(6, 7, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(6, 7,   $trans['KHII'], $dec);
		   		   		   
		if($show_value){
		    $item_total = $trans['KHIL']*$trans['material_cost'];
		    $line_total += $trans['KHIL']*$trans['material_cost'];
		    $rep->AmountCol(7, 8, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(7, 8,   $trans['KHIL'], $dec);

		if($show_value){
		    $item_total = $trans['LHR']*$trans['material_cost'];
		    $line_total += $trans['LHR']*$trans['material_cost'];
		    $rep->AmountCol(8, 9, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(8, 9,   $trans['LHR'], $dec);

		if($show_value){
		    $item_total = $trans['LHRI']*$trans['material_cost'];
		    $line_total += $trans['LHRI']*$trans['material_cost'];
		    $rep->AmountCol(9, 10, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(9, 10, $trans['LHRI'], $dec);

		if($show_value){
		    $item_total = $trans['LHRK']*$trans['material_cost'];
		    $line_total += $trans['LHRK']*$trans['material_cost'];
		    $rep->AmountCol(10, 11, $item_total, $dec);
		}
		else    
		   $rep->AmountCol(10, 11, $trans['LHRK'], $dec);
		
		$rep->AmountCol(11, 12, $line_total, $dec);
//		$rep->AmountCol(13, 14, $trans['MCP'], $dec);
//		$rep->AmountCol(14, 15, $trans['OBFL'], $dec);
//		$rep->AmountCol(15, 16, $trans['OBSP'], $dec);
//		$rep->AmountCol(16, 17, $trans['OBST'], $dec);
//		$rep->AmountCol(17, 18, $trans['MCRC'], $dec);
//		$GrandTotal += $trans['QtyOnHand'];

        if($show_value){
            $ISD +=  $trans['ISD']*$trans['material_cost'];
            $ISDK += $trans['ISDK']*$trans['material_cost'];
            $ISDL += $trans['ISDL']*$trans['material_cost'];
            $KHI += $trans['KHI']*$trans['material_cost'];
            $KHII += $trans['KHII']*$trans['material_cost'];
            $KHIL += $trans['KHIL']*$trans['material_cost'];
            $LHR += $trans['LHR']*$trans['material_cost'];
            $LHRI += $trans['LHRI']*$trans['material_cost'];
            $LHRK += $trans['LHRK']*$trans['material_cost']; 
        }
        else
        {
            $ISD += $trans['ISD'];
            $ISDK += $trans['ISDK'];
            $ISDL += $trans['ISDL'];
            $KHI += $trans['KHI'];
            $KHII += $trans['KHII'];
            $KHIL += $trans['KHIL'];
            $LHR += $trans['LHR'];
            $LHRI += $trans['LHRI'];
            $LHRK += $trans['LHRK']; 
        }
    		
		

	$SrNo += 1;
	}
	$GrandTotal = $ISD + $ISDK + $ISDL + $KHI + $KHII + $KHIL + $LHR + $LHRI + $LHRK;
	$rep->NewLine();
	$rep->Line($rep->row - 4);
    $rep->Font(b);
	$rep->TextCol(0, 2, _("GRAND TOTAL"));
	//$rep->AmountCol(1, 2, $GrandTotal, $dec);
	$rep->AmountCol(2, 3, $ISD, $dec);
	$rep->AmountCol(3, 4, $ISDK, $dec);
	$rep->AmountCol(4, 5, $ISDL, $dec);
	$rep->AmountCol(5, 6, $KHI, $dec);
	$rep->AmountCol(6, 7, $KHII, $dec);
	$rep->AmountCol(7, 8, $KHIL, $dec);
	$rep->AmountCol(8, 9, $LHR, $dec);
	$rep->AmountCol(9, 10, $LHRI, $dec);
	$rep->AmountCol(10, 11, $LHRK, $dec);
	$rep->AmountCol(11, 12, $GrandTotal, $dec);
    $rep->Font();
	$rep->Line($rep->row - 4);
	$rep->NewLine();
    $rep->End();
}

?>