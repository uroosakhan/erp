<?php

/**
Document blueprint use following parameters set in doctext.inc:

$Addr1,	$Addr2 - address info
$Payment_Terms - payment terms line
$Footer - footer texts
$this->company - company info
$this->title - report title
$this->formData - some other info
 ***/
$this->row = $this->pageHeight - $this->topMargin;

$upper = $this->row - 3 * $this->lineHeight;
$lower = $this->bottomMargin + 10 * $this->lineHeight;
$iline1 = $upper - 4.5 * $this->lineHeight;
$iline2 = $iline1 - 7.5* $this->lineHeight;
$iline3 = $iline2 - 3 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 -1 * $this->lineHeight;
$iline6 = $iline5 - 2.5 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin) / 5;
$icol = $this->pageWidth / 2;
$ccol = $this->cols[0] + 4;
$c2col = $ccol + 60;
$ccol2 = $icol / 2;
$mcol = $icol + 8;
$mcol2 = $this->pageWidth - $ccol2;
$cols = count($this->cols);
$this->SetDrawColor(205, 205, 205);
//	$this->Line($iline1, 3);
$this->SetDrawColor(128, 128, 128);
//	$this->Line($iline1);
//Auxilary Information Box
/*		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
		$this->Line($iline2);
		$this->Line($iline3);
		$this->Line($iline4);*/
//Auxilary Information Box
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//	$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);

$col = $this->leftMargin;
/*	for ($i = 0; $i < 5; $i++)
    {
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
    }
    $this->LineTo($right, $iline2 ,$right, $iline4);
*/

$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//$this->LineTo($this->cols[$cols - 5] - 2, $iline5 ,$this->cols[$cols - 5] - 2, $iline7);
//$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] - 2, $iline7);
//$this->LineTo($this->cols[$cols - 3] - 15, $iline5 ,$this->cols[$cols - 3] - 15, $iline7);
//$this->LineTo($this->cols[$cols - 4] - 16, $iline5 ,$this->cols[$cols - 4] - 16, $iline7);
//$this->LineTo($this->cols[$cols - 5] - 18, $iline5 ,$this->cols[$cols - 5] - 18, $iline7);
//$this->LineTo($this->cols[$cols - 6] - 5, $iline5 ,$this->cols[$cols - 6] - 5, $iline7);
//$this->LineTo($this->cols[$cols - 7] - 3, $iline5 ,$this->cols[$cols - 7] - 3, $iline7);

$this->LineTo($right, $iline5 ,$right, $iline7);

//Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
    $this->AddImage($logo, $ccol, $this->row, 0, 40);
}
else
{
    $this->NewLine(+1.5);

    $this->fontSize += 4;
    $this->Font('bold');
    //$this->Text($ccol, $this->company['coy_name'], $icol);
    $this->Font();
    $this->fontSize -= 4;
    $this->NewLine(-1.5);

}

// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->NewLine(-1.5);
$this->TextWrap($mcol - 130, $this->row - 46, $this->pageWidth - $this->rightMargin - $mcol + 10, "SALES INVOICE", 'center');

$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;


// Auxiliary document information
$col = $this->leftMargin;
foreach($aux_info as $info_header => $info_content)
{

    $this->row = $iline2 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_header, 'C');
    $this->row = $iline3 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
    $col += $width;
}

// Payment terms
//$this->row -= (2 * $this->lineHeight);
//		$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
//		$this->Font();

// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');


$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();
// Line headers

$this->row = $iline5 - $this->lineHeight - 1;
$this->Newline();
$this->Font('bold');
$count = count($this->headers2);
$this->cols[$count] = $right - 3;
//   $this->SetFontSize(-2);

for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers2[$i], -2);
$this->Font();
// $this->SetFontSize(-2);

// Footer
$this->Font('italic');
//$this->row = $iline7 - $this->lineHeight - 6;
// $this->SetFontSize(+8.75);

foreach ($Footer as $line => $txt)
{
    if (!is_numeric($line))	// title => link
    {
        $this->SetFontSize(+9.5);


        $this->TextWrap($ccol, $this->row, $right - $ccol, $line, 'C');
        $this->row -= $this->lineHeight;
        $this->SetTextColor(0, 0, 255);
        $this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->addLink($txt, $ccol, $this->row, $this->pageWidth - $this->rightMargin, $this->row + $this->lineHeight);
        $this->SetFontSize(-9.5);

    }
    else
        $this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
    $this->row -= $this->lineHeight;

}
$this->SetFontSize(+8.75);

$this->multicell(25, 400, '',1,'L', 0, 1, 40, 290);// bottom Lines
$this->multicell(260,55,"",1,'l',0,0,40,84);//upper header cell
$this->Font('bold');
$this->multicell(200,15,"".$this->company['coy_name'],0,'l',0,0,43,85);
$this->Font('');
$this->multicell(200,30,"".$this->company['postal_address'],0,'l',0,0,43,95);
$this->Font('bold');
$this->multicell(260,86," Buyer:",1,'l',0,0,40,139);

$this->multicell(300,35,"  ".@$this->formData['DebtorName'],0,'l',0,0,38,150);

$this->Font('');
$this->SetFontSize(+8.75);

$this->NewLine();
$this->multicell(255,20, "".@$this->formData['address'],0,'l',0,0,43,159);
$this->multicell(20,15,"PH:",0,'l',0,0,43,115);
$this->multicell(85,15,"".$this->company['phone'],0,'l',0,0,60,115);
$this->multicell(85,15," ",0,'l',0,0,125,185);
$this->multicell(83,15," ",0,'l',0,0,262,185);

$this->multicell(120,20,"",1,'L',0,0,300,84);
$this->multicell(120,18,"  Invoice No. ".$this->formData['document_number'],0,'L',0,0,300,86);//Invoice_no

$this->multicell(120,20,"",1,'l',0,0,420,84);
$this->multicell(120,18,"          Date:".sql2date($this->formData['document_date'])  ,0,'L',0,0,400,86);
$this->multicell(240,35,"                                                                                            Terms Of Payment:",1,'C',0,0,300,104);
$this->multicell(90,25," ".get_payment_terms_($this->formData['payment_terms']),0,'C',0,0,430,114);

$this->multicell(100,13,"Delivery .No",1,'C',0,0,40,227);

$this->multicell(100,13,"Delivery Date",1,'C',0,0,140,227);



$get = get_invoice_through_dn($this->formData['order_']);
$des = '';
$des11 = '';
$this->multicell(100,35,"",1,'C',0,0,40,240);

$deliveries_date = get_sales_parent_numbers(ST_SALESINVOICE, $this->formData['trans_no']);
if ($print_invoice_no == 0)
{
    foreach($deliveries_date as $n => $delivery_date) {
        $deliveries_date[$n] = get_invoice_date_through_dn(ST_CUSTDELIVERY, $delivery_date);
    }
}
$this->SetFontSize(+8.75);

$this->multicell(100,35, "",1,'C',0,0,140,240);
$this->multicell(46,35, (implode(',', $deliveries_date)),0,'C',0,0,170,240);

/*while ($myrow1=db_fetch($get1)){


    $des11 .= sql2date($myrow1['tran_date']);
	$this->NewLine();
    //$des11 .= $myrow['tran_date'].'';



}*/


//$this->multicell(100,35,"",1,'C',0,0,40,235);
$deliveries = get_sales_parent_numbers(ST_SALESINVOICE, $this->formData['trans_no']);
if ($print_invoice_no == 0)
{
    foreach($deliveries as $n => $delivery) {
        $deliveries[$n] = get_reference(ST_CUSTDELIVERY, $delivery);
    }
}
$this->multicell(46,55,implode(',', $deliveries),0,'C',0,0,70,240);//values
// $this->multicell(61,25,"ansar",$des11,1,'l',0,0,423,88);

$deliveries_location = get_sales_parent_numbers(ST_SALESINVOICE, $this->formData['trans_no']);
if ($print_invoice_no == 0)
{
    foreach($deliveries_location as $n => $delivery_loc) {
        $deliveries_location[$n] = get_location_through_dn(ST_CUSTDELIVERY, $delivery_loc);
    }
}




$get_location = get_location_through_dn($this->formData['order_']);
$des_ref = '';

while ($myrow2=db_fetch($get_location)){


    $des_ref .= $myrow2['customer_ref']."      ";

    //$des11 .= $myrow['tran_date'].'';


}
//dispatch ref of sir ansar
$this->SetFontSize(+8.75);

$this->multicell(100,13,"    Dispatch REF",1,'L',0,0,240,227);
$this->multicell(100,35,"",1,'L',0,0,240,240);

$this->multicell(70,50, $this->formData['customer_ref'],0,'C',0,1,245,240);

$this->SetFontSize(+8);

$get_location = get_invoice_location_through_dn($this->formData['order_']);
$des_company = '';

while ($myrow2=db_fetch($get_location)){


    $des_company.= get_shipper_name($myrow2['ship_via'])."      ";

    //$des11 .= $myrow['tran_date'].'';


}


//// Customer Order No : of sir ansar
$this->SetFontSize(+8.75);

$this->multicell(240,86,"   Customer Order No : ",1,'L',0,0,300,139);

$this->multicell(120,20,"" .$this->formData['customer_order_no'],0,'C',0,0,300,152);

$this->multicell(120,86,"    Customer Order Date : ",1,'L',0,0,420,139);

$this->multicell(100,10, sql2date($this->formData['customer_order_date']),0,'L',0,0,445,155);


//
$this->Font();
//$this->multicell(220,85,"Buyer (if other than consignee)",1,'l',0,0,345,185);
$this->multicell(290,60,"",0,'l',0,0,45,190);
$this->multicell(210,60,"",0,'l',0,0,350,190);

// Last  Multicells of Total
$this->multicell(525,15,"",1,'C',1,2,40,677);


$this->Font();





$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>