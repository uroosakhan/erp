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
    $templates = $_POST['PARAM_3'];
    // $papersize = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_4'];
    $orientation = $_POST['PARAM_5'];
    $destination = $_POST['PARAM_6'];
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

            $code['item_code']=$myrow['item_code'];
            $code_length= strlen($code['item_code']);
           $item = get_item($myrow['item_code']);
            if($templates == 0) {
                $rep->SetFontSize(8);
                $rep->MultiCell(108, 35, $rep->company['coy_name'], 0, 'L', 0, 2, 1, 4, true);

                $rep->MultiCell(108, 35, $rep->company['coy_name'], 0, 'L', 0, 2, 150, 4, true);
                
                
                 $rep->MultiCell(108, 35, $rep->company['email'], 0, 'L', 0, 2, 48, 4, true);

                $rep->MultiCell(108, 35, $rep->company['email'], 0, 'L', 0, 2, 198, 4, true);
                
                
               
                    $item = get_item($code['item_code']);

                $myrow7 = get_company_item_pref('text1');
                if ($code_length < 8) {
                    $rep->MultiCell(108, 35, $myrow7['label_value'] . $item['text1'], 0, 'L', 0, 2, 1, 17, true); // for price
                    $rep->MultiCell(108, 35, $myrow7['label_value'] . $item['text1'], 0, 'L', 0, 2, 150, 17, true); // for price
//			$rep->MultiCell(81, 30, $myrow['description']   ,0, 'L', 0, 2, 235, 10, true); // for pri
                } else {
                    $rep->MultiCell(108, 35, $myrow7['label_value'] . $item['text1'], 0, 'L', 0, 2, 1, 17, true); // for price
                    $rep->MultiCell(108, 35, $myrow7['label_value'] . $item['text1'], 0, 'L', 0, 2, 150, 17, true); // for price
                }
                $rep->SetFontSize(8);

              

                if ($code_length < 8) {
                    $rep->write1DBarcode(($item['text4']), 'C128A', 2, 30, 190, 30, .9, $style, 'N'); //dz
                    $rep->write1DBarcode(($item['text4']), 'C128A', 150, 30, 180, 30, .9, $style, 'L'); //dz
                } else {
                    $rep->write1DBarcode(($item['text4']), 'C128A', 2, 30, 175, 30, .7, $style1, 'N'); //dz
                    $rep->write1DBarcode(($item['text4']), 'C128A', 150, 30, 180, 30, .7, $style, 'L'); //dz
                }
                $price = get_price($myrow['stock_id'], $currency, $salestype);

                $retailprice = round2((get_price($myrow['stock_id'], $currency, 1)) * $myrow['quantity']);
                $wholesaleprice = round2(get_price($myrow['stock_id'], $currency, 2) * $myrow['quantity']);

                $price = number_format2($price);


              $rep->SetFontSize(8.5);
$rep->font('B');
                $rep->MultiCell(200, 30, "Sale Price Rs." . $retailprice . "/", 0, 'L', 0, 2, 1, 60, true); // for price
                $rep->MultiCell(200, 30, "Sale Price Rs." . $retailprice . "/", 0, 'L', 0, 2, 150, 60, true); // for price




                $rep->SetFontSize(8);

            }
            elseif($templates == 1){
                $rep->SetFontSize(8);
                $rep->MultiCell(108, 35,  $rep->company['coy_name'], 0, 'C', 0, 2,15, 4, true);

                $rep->MultiCell(108, 35,  $rep->company['coy_name'], 0, 'C', 0, 2, 165, 4, true);

                $myrow7 = get_company_item_pref('text1');
                if($code_length < 8  ) {
                    $rep->MultiCell(108, 35, $myrow7['label_value'].$item['text1'], 0, 'L', 0, 2, 1, 17, true); // for price
                    $rep->MultiCell(108, 35, $myrow7['label_value'].$item['text1'], 0, 'L', 0, 2, 150, 17, true); // for price
                }
                else{
                    $rep->MultiCell(108, 35,$rep->company['email'], 0, 'C', 0, 2, 15, 13, true); // for price
                    $rep->MultiCell(108, 35,$rep->company['email'], 0, 'C', 0, 2, 165, 13, true); // for price
                }
                $rep->SetFontSize(8);


                $price = get_price($myrow['stock_id'], $currency, $salestype);

                $retailprice = round2((get_price($myrow['stock_id'], $currency, 1)) * $myrow['quantity']);
                $wholesaleprice = round2(get_price($myrow['stock_id'], $currency, 2)* $myrow['quantity']);

                $price = number_format2($price);


                $rep->SetFontSize(8.2);
                $rep->MultiCell(200, 30,  $myrow7['label_value'].$item['text1'] , 0, 'L', 0, 2, 1, 35, true); // for price
                $rep->MultiCell(200, 30, $myrow7['label_value'].$item['text1'] , 0, 'L', 0, 2, 150, 35, true); // for price

$rep->SetFontSize(8.5);
$rep->font('B');
                $rep->MultiCell(200, 30, "Sale Price Rs.". $retailprice."/" , 0, 'L', 0, 2, 1, 45, true); // for price
                $rep->MultiCell(200, 33, "Sale Price Rs.". $retailprice."/" , 0, 'L', 0, 2, 150, 45, true); // for price


                $rep->SetFontSize(8);

            }
            elseif($templates == 2){

                $rep->SetFontSize(8);


                $myrow7 = get_company_item_pref('text1');
                if($code_length < 8  ) {

                }
                else{

                }
                $rep->SetFontSize(8.2);


                if($code_length < 8  ) {
                    $rep->write1DBarcode(($code['item_code']), 'C128A', 2, 30, 190, 30, .9, $style, 'N'); //dz
                    $rep->write1DBarcode(($code['item_code']), 'C128A', 150, 30, 190, 30, .9, $style, 'L'); //dz
                }
                else{
                    $rep->write1DBarcode(($code['item_code']), 'C128A', 2, 30, 175, 30, .7, $style1, 'N'); //dz
                    $rep->write1DBarcode(($code['item_code']), 'C128A', 150, 30, 180, 30, .7, $style, 'L'); //dz
                }
                $price = get_price($myrow['stock_id'], $currency, $salestype);

                $retailprice = round2((get_price($myrow['stock_id'], $currency, 1)) * $myrow['quantity']);
                $wholesaleprice = round2(get_price($myrow['stock_id'], $currency, 2)* $myrow['quantity']);

                $price = number_format2($price);




                $rep->SetFontSize(8);
            }

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