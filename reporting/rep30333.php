<?php
$page_security = 'SA_BARREP';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui/ui_input.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");

//----------------------------------------------------------------------------------------------------

print_price_listing();
function fetch_items($stock_id)
{
	$sql = "SELECT ".TB_PREF."stock_master.stock_id, ".TB_PREF."stock_master.description AS name,
				".TB_PREF."stock_master.category_id,".TB_PREF."stock_master.units,
				".TB_PREF."stock_category.description,
				".TB_PREF."stock_master.cat2
			FROM ".TB_PREF."stock_master,
				".TB_PREF."stock_category
			WHERE ".TB_PREF."stock_master.category_id=".TB_PREF."stock_category.category_id AND NOT ".TB_PREF."stock_master.inactive";
	if($stock_id!=ALL_TEXT)
		$sql .= " AND ".TB_PREF."stock_master.stock_id = ".db_escape($stock_id);

	$sql .= " ORDER BY ".TB_PREF."stock_master.category_id,
				".TB_PREF."stock_master.stock_id";

	return db_query($sql,"No transactions were returned");
}

//for getting item code
function get_barcode_name($barcode_name)
{
	$sql = "SELECT description FROM ".TB_PREF."cat2 
	WHERE id=".db_escape($barcode_name);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

//function get_color_name($barcode_name)
//{
//	$sql = "SELECT description FROM ".TB_PREF."color
//	WHERE id=".db_escape($barcode_name);
//
//	$result = db_query($sql, "could not get sales type");
//
//	$row = db_fetch_row($result);
//	return $row[0];
//}

/*
//for getting barcode description
function get_barcode_desc($barcode)
{
	$sql = "SELECT description FROM ".TB_PREF."item_codes
	WHERE item_code=".db_escape($barcode)."
	AND is_foreign= 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}*/
/*function get_cat2($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."cat2 WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get cat1");
	return db_fetch($result);
}*/

function get_price_new($selected_id)
{
	$sql = "SELECT price FROM ".TB_PREF."prices WHERE stock_id=".db_escape($selected_id);

	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_price_listing()
{
	global $path_to_root, $pic_height, $pic_width;

	$currency = $_POST['PARAM_0'];
	$items = $_POST['PARAM_1'];
	$barcode_name = $_POST['PARAM_2'];
	$image = $_POST['PARAM_3'];
	$colors = $_POST['PARAM_4'];
	$salestype = $_POST['PARAM_5'];
	$papersize = $_POST['PARAM_6'];
	$comments = $_POST['PARAM_7'];
	$orientation = $_POST['PARAM_8'];
	$destination = $_POST['PARAM_9'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$home_curr = get_company_pref('curr_default');
	if ($currency == ALL_TEXT)
		$currency = $home_curr;
	$curr = get_currency($currency);
	$curr_sel = $currency . " - " . $curr['currency'];

//	$item_desc =get_item(get_items_code($items));



//        $itm = get_barcode_desc($items); //barcode description

	$cols = array(0, 100, 360, 385, 450, 515);

	$aligns = array('left',	'left',	'left', 'right', 'right');


	$rep = new FrontReport(_(''), "BarcodePrinting",'BLABELSING', 9, 'P');
	if ($orientation == 'L')
		recalculate_cols($cols);

	$rep->SetHeaderType('Header30333');
	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns);

//ansar
	//$logo = company_path() . "/images/" . 'hisaab_logo.png';
	//if ($this->company['coy_logo'] != '' && file_exists($logo))
	//foreach($items as $key => $value)
	//{
	$sql = "SELECT ".TB_PREF."stock_master.*, ".TB_PREF."stock_master.description AS name,".TB_PREF."stock_master.stock_id
			FROM ".TB_PREF."stock_master";
	if($items!=ALL_TEXT)
		$sql .= " WHERE ".TB_PREF."stock_master.stock_id = ".db_escape($items);
	$sql .= " GROUP BY  ".TB_PREF."stock_master.stock_id";
	$result = db_query($sql,"No transactions were returned");
	$catgor = '';
	$_POST['sales_type_id'] = $salestype;

	while ($myrow = db_fetch($result))
	{
		//$myrow=db_fetch($result);
		//{
		$rep->NewPage();
		//$item_desc = get_item($myrow['stock_id'] );
		// $itm = $item_desc['description'];
  		$logo = company_path() . "/images/" .$image.".jpg";    // $rep->company['coy_logo'];
		//$rep->TextCol(3, 5,	_($logo));
		//1 top
		// define barcode style
		$style = array(
			'position' => 'L',
			'align' => '',
			'stretch' => true,
			'fitwidth' => false,
			'cellfitalign' => '',
			'border' => false,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			//'fgcolor' => array(0,0,0),
			'bgcolor' =>  '',
			'text' => true,
			'label' => 'CUSTOM LABEL',
			'font' => 'helvetica',
			'fontsize' => 12,
			'stretchtext' => 4
		);

function get_discount_new($selected_id)
{
	$sql = "SELECT discount FROM ".TB_PREF."prices WHERE stock_id=".db_escape($selected_id);

	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}
		$price=get_price_new($myrow['stock_id']);
       $dicount =get_discount_new($myrow['stock_id']);
   
//		$price = get_price($myrow['stock_id'], $currency, $salestype);
		//display_error($price);
//		$rep->NewLine();

		//1
//		$rep->MultiCell(0, 15, get_barcode_name($barcode_name), 1, '', 0, 2, 26, 2, true);
		$rep->SetFontSize(9);
		/*		$rep->MultiCell(35, 10, "Article", 0, 0, 2, 0,20,18,'R');
                $rep->MultiCell(35, 10, "Color", 0, 0, 2, 0,55,18,'R');
                $rep->MultiCell(35, 10, "Size", 0, 0, 2, 0,90,18,'R');*/
		//
		/*		$rep->MultiCell(50, 13, " ".$myrow['cat2'], 1, 0, 10, 0,20,30,'R');
                $rep->MultiCell(20, 13, " ".$myrow['color'], 1, 0, 10, 0,70,30,'R');
                $rep->MultiCell(20, 13, " 7", 1, 0, 10, 0,90,30,'R');
        //		*/
//		$rep->MultiCell(50, 26, _("Rs.")."".$price, 1, 0, 10, 0,20,44,'R');
//		$rep->MultiCell(70, 26, "", 1, 0, 10, 0,55,44/*,'c'*/);
//		$rep->MultiCell(35, 20, "", 1, 0, 10, 0,90,45/*,'c'*/);
//		$rep->write1DBarcode($myrow['stock_id'] , 'C128A', 70, 45, 30, 25, 0.5, $style, 'N');
		//	$rep->SetFontSize(7);
//		$rep->MultiCell(75, 10, $myrow['name'], 1, 0, 10, 0, 95, 30, true);
//		$rep->SetFontSize(12);
//		$rep->Font(b);
		//$rep->MultiCell(0, 15, _("Rs.") ." " .$price, 0, '', 0, 2, 36, 45, true);
//		$rep->Font();

		//	$size= get_cat2($myrow['cat2']);
		//$sizes= get_size($myrow['stock_id']);
		$rep->SetFontSize(11);
		//$rep->MultiCell(70, 20, "Article", 0, 'C', 2, 0,7,23);
		//$rep->MultiCell(46, 20, "Color", 0, 'C', 2, 0,77,23);
		//$rep->MultiCell(58, 20, "Size", 0, 'C', 2, 0,123,23);
		//Article No
		//$rep->MultiCell(70, 14, "".$myrow['name'], 0, 'C', 2, 0,7,46/*,'c'*/);
		//$rep->MultiCell(70, 20, "", 1, 'C', 2, 0,7,43/*,'c'*/);
		//Color
		//$rep->MultiCell(46, 14, "".$myrow['name'], 0, 'C', 2, 0,77,46/*,'c'*/);
		//$rep->MultiCell(46, 20, "", 1, 'C', 2, 0,77,43/*,'c'*/);
		//Size
		//	$rep->MultiCell(58, 14, 44, 0, 'C', 10, 0,123,46/*,'c'*/);
		//$rep->MultiCell(58, 20, "", 1, 'C', 10, 0,123,43/*,'c'*/);
//		//price
		//$rep->MultiCell(50, 20, "".$price, 0, 0, 10, 0,27,69/*,'c'*/);
		//$rep->MultiCell(70, 20, "Rs:", 0, 0, 10, 0,7,69/*,'c'*/);
//		$rep->MultiCell(70, 26, "", 1, 0, 10, 0,195,44/*,'c'*/);
      $total_discount = $dicount / 100;
      
      	$rep->setfontsize(10);
	$rep->MultiCell(300, 20, "".$rep->company['coy_name'], 0, 'L', 2, 0,58, 2);
		
		$rep->SetFontSize(9);
		
		
$rep->MultiCell(120, 20, $myrow['name'] , 0, 'C', 0, 5, 20, 55, true);//for units
		$rep->setfontsize(6);
			$rep->MultiCell(100, 50, get_combo_name($myrow['combo1'],'combo1') , 0, '', 0, 70, 90, 11, true);
				$rep->MultiCell(100, 50, get_combo_name($myrow['combo2'],'combo2') , 0, '', 0, 70, 8, 11, true);
				
				$rep->MultiCell(100, 50, get_combo_name($myrow['combo4'],'combo4') , 0, '', 0, 70, 130, 11, true);
				
				//	$rep->MultiCell(100, 50, get_category_name($myrow['category_id']) , 0, '', 0, 70, 10, 65, true);
			$rep->setfontsize(8);
		//$rep->MultiCell(80, 10, $myrow['stock_id'] , 0, '', 0, 70, 65, 40, true); // for units
		
		
	
		$rep->write1DBarcode($myrow['stock_id'] , 'C128A', 40, 15, 235,40, 1, $style, 'N');
	
		global $db_connections;
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='XPLORE'){
		    
		    $disc=$total_discount*$price;
		   $total_price= ($price-$disc);
		   
		      
if($dicount !=0){
        $rep->font('b');

	$rep->SetFontSize(12);
	
	$rep->MultiCell(200, 50, " PKR  ".$price , 0, '', 0, 60, 9, 73, true);//A.A
	
 	$rep->MultiCell(200, 50, "_________" , 0, '', 0, 60, 12.5, 68, true);//A.A
}

else{
    
    $rep->font('b');
    	$rep->SetFontSize(12);
    	$rep->MultiCell(200, 50, " No Discount  " , 0, '', 0, 70, 7, 73, true);//A.A
    	
    
    
}


$rep->font('b');
$rep->MultiCell(200, 50, " PKR  ".round($total_price) , 0, '', 0, 70, 96, 73, true);//SYEDA HAREEM
	
	
	
		}
	
	
		else{
		    	$rep->MultiCell(200, 50, " Rs:".$price*$total_discount , 0, '', 0, 70, 35, 75, true);//SYEDA HAREEM
		    	$rep->MultiCell(200, 50, "Discount  ".$dicount , 0, '', 0, 50, 90, 75, true);//dz
		}
		
		//$rep->MultiCell(100, 14, $myrow['stock_id'], 0, 'C', 10, 0,79,82/*,'c'*/);
		//$rep->MultiCell(55, 5, $myrow['name'], 0, '', 0, 10, 215, 15, true);
		/*		$rep->SetFontSize(12);
                $rep->Font(b);
                $rep->MultiCell(0, 15, _("Rs.") ." " .$price, 0, '', 0, 2, 180, 45, true); */


		/*	if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight));*/
//$rep->NewPage();
		//}
		//$rep->NewPage();

		//$rep->Line($rep->row  - 4);
	}

	$rep->NewLine();
	$rep->End();
}

?>
rep3033.php
Open with
1 of 2 items
rep3033.phpheader3031.incDisplaying rep3033.php.
Abid(HA TRADERS), 0 3 2 1 9 2 8 5 4 3 0