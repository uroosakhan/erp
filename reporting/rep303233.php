<?php
$page_security = 'SA_ITEMSVALREP';

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
				".TB_PREF."stock_category.description
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
function get_items_code($barcode)
{
    $sql = "SELECT stock_id FROM ".TB_PREF."item_codes 
	WHERE item_code=".db_escape($barcode)."
	AND is_foreign= 1";

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
//for getting barcode description
function get_barcode_desc($barcode)
{
    $sql = "SELECT description FROM ".TB_PREF."item_codes 
	WHERE item_code=".db_escape($barcode)."
	AND is_foreign= 1";

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

//----------------------------------------------------------------------------------------------------

function print_price_listing()
{
    global $path_to_root, $pic_height, $pic_width;

    $currency = $_POST['PARAM_0'];
    $items = $_POST['PARAM_1'];
    //$image = $_POST['PARAM_2'];
    //$colors = $_POST['PARAM_3'];
    $salestype = $_POST['PARAM_2'];
    // $papersize = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];
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

    $item_desc =get_item(get_items_code($items));



//        $itm = get_barcode_desc($items); //barcode description

    $cols = array(0, 100, 360, 385, 450, 515);

    $aligns = array('left',	'left',	'left', 'right', 'right');


    $rep = new FrontReport(_(''), "BarcodePrinting",'MBARCODE', 9, 'P');
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->SetHeaderType('Header3031');
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

//ansar
    //$logo = company_path() . "/images/" . 'hisaab_logo.png';
    //if ($this->company['coy_logo'] != '' && file_exists($logo))
//		foreach($items as $key => $value)
    {


//get codes from items_code tabel - dz
        $sql = "SELECT ".TB_PREF."item_codes.category_id,".TB_PREF."item_codes.item_code,".TB_PREF."item_codes.description , ".TB_PREF."item_codes.stock_id, ".TB_PREF."item_codes.quantity
			FROM ".TB_PREF."item_codes,
				".TB_PREF."stock_category
			WHERE ".TB_PREF."item_codes.category_id=".TB_PREF."stock_category.category_id AND NOT ".TB_PREF."item_codes.inactive";
//			if($items!=ALL_TEXT)
        $sql .= " AND ".TB_PREF."item_codes.item_code = ".db_escape($items);

        $sql .= " ORDER BY ".TB_PREF."item_codes.category_id,
				".TB_PREF."item_codes.stock_id";

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

            //Outer Boxes
            $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 40,100, true); //  1
            $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 40,256, true); //  2
            $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 40,412, true); //  3

            if($papersize)
            {
                $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 300,100, true); //  1
                $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 560,100, true); //  1

                $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 300,256, true); //2
                $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 560,256, true); // 2

                $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 300,412, true); //3
                $rep->MultiCell(252, 148, _(" ") , 0, 'L', 0, 2, 560,412, true); // 3
            }
            //1 top
            /*if ($colors == 0)
              $rep->SetFillColor(100, 0, 0, 0);

            elseif ($colors == 1)
            {
                $rep->SetFillColor(0, 100, 0, 0);
            }
            elseif ($colors == 2)
            {
             $rep->SetFillColor(0, 0, 100, 0);
            }
            elseif ($colors == 3)
            {
             $rep->SetFillColor(255, 0, 0);
            }
            elseif ($colors == 4)
            {
             $rep->SetFillColor(0, 255, 0);
            }
            elseif ($colors == 5)
            {
             $rep->SetFillColor(0, 0, 255);
            }
            else
            {
              $rep->SetFillColor(127);
            }*/

            /* $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 206.5,110, true); // box 1
             $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 206.5,266, true); // box 4
             $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 206.5,422, true); // box 7

             if($papersize)
             {
            $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 466.5,110, true); // box 2
             $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 726.5,110, true); // box 3
            $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 466.5,266, true); // box 5
            $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 726.5,266, true); // box 6
            $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 466.5,422, true); // box 8
           $rep->MultiCell(85, 90, _(" ") , 1, 'L', 1, 2, 726.5,422, true); // box 9
             }*/

            //for logoes 1
            /*	$rep->Image($logo, '45', '261', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 45,261, true); // for logoes
                //$rep->MultiCell(90, 90,$itm  , 0, 'L', 0, 2, 117.5,272, true); // for item name
                 $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 117.5,100.5, true);
                //1 middle

                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 45,356, true); //  for barcode
                //



                //for logoes 1
                $rep->Image($logo, '45', '105', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 45,105, true); // for logoes
                $rep->MultiCell(90, 90, $itm , 0, 'L', 0, 2, 117.5,116, true); // for item name
               $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 117.5,256.5, true);

                //for logoes 1
                $rep->Image($logo, '45', '417', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 45,417, true); // for logoes
                $rep->MultiCell(90, 90, $itm  , 0, 'L', 0, 2, 117.5,428, true); // for item name
                $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 117.5,412.5, true);


                if($papersize)
                {
                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 45,200, true); //  for barcode
                //for logoes 2
                $rep->Image($logo, '305', '105', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 305,105, true); // for logoes
                $rep->MultiCell(90, 90,  $itm , 0, 'L', 0, 2, 377.5,116, true); // for item name
                 $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 377.5,100.5, true);

                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 305,200, true); //  for barcode border

                $rep->Image($logo, '565', '105', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 565,105, true); // for logoes
                $rep->MultiCell(90, 90, $itm , 0, 'L', 0, 2, 637.5,116, true); // for item name
                $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 637.5,100.5, true);
                //3 top

                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 565,200, true); //  for barcode
                //for logoes 2
                $rep->Image($logo, '305', '261', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 305,261, true); // for barcode border
                $rep->MultiCell(90, 90,  $itm  , 0, 'L', 0, 2, 377.5,272, true); // for item name
                $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 377.5,256.5, true);
                //2 middle

                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 305,356, true); //  for barcode border
                //for logoes 3
                $rep->Image($logo, '565', '261', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 565,261, true); // for logoes
                $rep->MultiCell(90, 90,  $itm  , 0, 'L', 0, 2, 637.5,272, true); // for item name
                //3 middle
                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 565,356, true); //  for barcode
                $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 637.5,256.5, true);
                //
                //1 bottom


                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 45,512, true); //  for barcode

                //for logoes 2
                $rep->Image($logo, '305', '417', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 305,417, true); // for barcode border
                $rep->MultiCell(90, 90,  $itm  , 0, 'L', 0, 2, 377.5,428, true); // for item name
                $rep->SetFillColor(0, 0, 255);
                $rep->MultiCell(175,2, "", 0, 'L', 1, 0, 377.5,412.5, true);

                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 305,512, true); //  for barcode
              $rep->Image($logo, '565', '417', 70, 90, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
                //$rep->MultiCell(70, 90, _(" ") , 1, 'L', 0, 2, 565,417, true); // for logoes
                $rep->MultiCell(90, 90,  $itm , 0, 'L', 0, 2, 637.5,428, true); // for item name
                //$rep->MultiCell(240, 42, _(" ") , 1, 'L', 0, 2, 565,512, true); //  for barcode
                $rep->SetFillColor(0, 0, 255);
                //$rep->MultiCell(175, 4,  "", 1, 'L', 0, 2, 637.5,413, true); // for blue color
                $rep->MultiCell(175,2, "", 0, 'J', 1, 0, 637.5,412.5, true); // for blue color

                }*/



            // define barcode style
            $style = array(
                'position' => 'L',
                'align' => 'L',
                'stretch' => false,
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
                'fontsize' => 9,
                'stretchtext' =>4
            );
            
              $style1 = array(
                'position' => 'L',
                'align' => 'L',
                'stretch' => false,
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
                'fontsize' => 9,
                'stretchtext' =>4
            );

            /*
            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => true,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0,0,128),
                'bgcolor' =>  false // array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 4,
                'stretchtext' => 1
            );
            */
//$rep->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));

//	$rep->SetFont('times', 'BI',15);
//$rep->SetFont('helvetica', '', 10);
//	$result = fetch_items(get_items_code($items));



//			$rep->NewLine();
            $code['item_code']=$myrow['item_code'];
            $code_length= strlen($code['item_code']);
           $item = get_item($myrow['item_code']);
            //1
            $rep->SetFontSize(8);
           //    $rep->MultiCell(108, 35,  $rep->company['coy_name'], 0, 'C', 0, 2, 1, 4, true);
               
           //    $rep->MultiCell(108, 35,  $rep->company['coy_name'], 0, 'C', 0, 2, 150, 4, true);
          
            $myrow7 = get_company_item_pref('text1');
            if($code_length < 8  ) {
             //   $rep->MultiCell(108, 35, $myrow7['label_value'].$item['text1'], 0, 'L', 0, 2, 1, 5, true); // for price
            //    $rep->MultiCell(108, 35, $myrow7['label_value'].$item['text1'], 0, 'L', 0, 2, 170, 5, true); // for price
//			$rep->MultiCell(81, 30, $myrow['description']   ,0, 'L', 0, 2, 235, 10, true); // for pri
            }
            else{
          //      $rep->MultiCell(108, 35,$myrow7['label_value'].$item['text1'], 0, 'L', 0, 2, 1, 17, true); // for price
                // $rep->MultiCell(108, 35, $myrow7['label_value'].$item['text1'], //0, 'L', 0, 2, 150, 17, true); // for price
            }
            $rep->SetFontSize(8);


if($code_length < 8  ) {
    $rep->write1DBarcode(($code['item_code']), 'C128A', 2, 30, 190, 30, .9, $style, 'N'); //dz
    $rep->write1DBarcode(($code['item_code']), 'C128A', 170, 30, 190, 30, .9, $style, 'L'); //dz
//$rep->write1DBarcode(($code['item_code']), 'C128A', 235, 20, 30, 30, .7, $style, 'N'); //dz
}
else{
    $rep->write1DBarcode(($code['item_code']), 'C128A', 2, 30, 175, 30, .7, $style1, 'N'); //dz
    $rep->write1DBarcode(($code['item_code']), 'C128A', 150, 30, 180, 30, .7, $style, 'L'); //dz
}
            $price = get_price($myrow['stock_id'], $currency, $salestype);

            $retailprice = round2((get_price($myrow['stock_id'], $currency, 1)) * $myrow['quantity']);
            $wholesaleprice = round2(get_price($myrow['stock_id'], $currency, 2)* $myrow['quantity']);

            $price = number_format2($price);
         
           
                $rep->SetFontSize(7);
              //  $rep->MultiCell(200, 30, "Sale Price Rs.". $retailprice."/" , 0, 'L', 0, 2, 1, 60, true); // for price
             //   $rep->MultiCell(200, 30, "Sale Price Rs.". $retailprice."/" , 0, 'L', 0, 2, 150, 60, true); // for price

                $rep->SetFontSize(7);
                // $rep->MultiCell(81, 30, "Rs:" . $retailprice . "", 0, 'L', 0, 2, 62, 58, true); // for price
                // $rep->MultiCell(81, 30, "Rs:" . $retailprice . "", 0, 'L', 0, 2, 235, 58, true); // for price

                $rep->SetFontSize(8);
         

            //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 45, 356, 30, 41, 0.8, $style, 'N');
            //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 45, 512, 30, 41, 0.8, $style, 'N');

            if($papersize)
            {
                //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 305, 40, 30, 41, 0.38, $style, '');
                //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 565, 40, 30, 41, 0.8, $style, '');

                //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 305, 356, 30, 41, 0.8, $style, '');
                //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 565, 356, 30, 41, 0.8, $style, '');

                //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 305, 512, 30, 41, 0.8, $style, '');
                //$rep->write1DBarcode($myrow['stock_id'], 'C128A', 565, 512, 30, 41, 0.8, $style, '');
            }

            /*$rep->MultiCell(60, 10, $myrow['units'], 0, 'R', 0, 2, 215.5,168, true); // for units
            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 215.5,320, true); // for units
            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 215.5,476, true); // for units

            if($papersize)
            {
            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 461.5,168, true); // for units
            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 721.5,168, true); // for units

            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 461.5,320, true); // for units
            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 721.5,320, true); // for units

            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 461.5,476, true); // for units
            $rep->MultiCell(60, 10, $myrow['units'] , 0, 'R', 0, 2, 721.5,476, true); // for units
            }


            $price = get_price($myrow['stock_id'], $currency, $salestype);
            $price = number_format2($price);

            $rep->MultiCell(81, 30, $price ,0, 'C', 0, 2, 207.5,140, true); // for price
            $rep->MultiCell(81, 30, $price ,0, 'C', 0, 2, 207.5,296, true); // for price
            $rep->MultiCell(81, 30, $price ,0, 'C', 0, 2, 207.5,452, true); // for price

            if($papersize)
            {
            $rep->MultiCell(81, 30, $price , 0, 'C', 0, 2, 468.5,140, true); // for price
            $rep->MultiCell(81, 30, $price , 0, 'C', 0, 2, 728.5,140, true); // for price

            $rep->MultiCell(81, 30, $price , 0, 'C', 0, 2, 468.5,296, true); // for price
            $rep->MultiCell(81, 30, $price , 0, 'C', 0, 2, 728.5,296, true); // for price

            $rep->MultiCell(81, 30, $price , 0, 'C', 0, 2, 468.5,452, true); // for price
            $rep->MultiCell(81, 30, $price , 0, 'C', 0, 2, 728.5,452, true); // for price

            }*/
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight));
            //$rep->NewPage();
        }
        //$rep->NewPage();

        //$rep->Line($rep->row  - 4);
    }

    $rep->NewLine();
    $rep->End();
}

?>