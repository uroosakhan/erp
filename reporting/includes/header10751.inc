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

$upper = $this->row - 2 * $this->lineHeight;
$lower = $this->bottomMargin + 35 * $this->lineHeight;
$iline1 = $upper - 14 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 7 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
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
//$this->SetDrawColor(205, 205, 205);
//$this->Line($iline1, 3);
//$this->SetDrawColor(128, 128, 128);
//$this->Line($iline1);
//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//$this->Line($iline2);
//$this->Line($iline3);
//$this->Line($iline4);
//$this->Line($iline5);
//$this->Line($iline6);
//$this->Line($iline7);
//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
//$col = $this->leftMargin;
for ($i = 0; $i < 5; $i++)
{
//	$this->LineTo($col += $width, $iline2,$col, $iline4);
}

$this->font('bold');
//$this->multicell(525,10,"",0,'C',1,0,40,377);
$this->setfontsize(9);
// $this->multicell(200,10,implode(',', $deliveries),0,'L',0,0,424,130);
$this->Font();
// Company Logo
$this->NewLine();
//$logo = company_path() . "/images/" . $this->company['coy_logo'];
////display_error(company_path());
//if ($this->company['coy_logo'] != '' && file_exists($logo))
//{
//	$this->AddImage($logo, $ccol,770, $this->company['logo_w'], $this->company['logo_h']);
//}
//else
//{
//	$this->fontSize += 4;
//	$this->Font('bold');
////	$this->Text($ccol, $this->company['coy_name'], $icol);
//	$this->Font();
//	$this->fontSize -= 4;
//}
//$image = company_path() .  '/images/image.png';
//	$this->AddImage($ccol,$logo1, $icol);

//$this->TextCol(0, 13, $logo , - 2);
//$this->AddImage($logo, $ccol,770, $this->company['logo_w'], $this->company['logo_h']);
// Document title
//$this->SetTextColor(0, 0, 0);
$this->fontSize += 10;
//$this->Font('bold');
$this->NewLine(-2);

//$this->TextWrap($ccol+70, $this->row, $this->pageWidth - $this->rightMargin - $mcol + 183, "INVOICE", 'right');
//$this->Font();
$this->NewLine(+2);

$this->fontSize -= 10;
$this->NewLine();
//$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
//$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
$this->Font('italic');
if (@$this->company['phone'])
{
//	$this->Text($ccol, _("Phone"), $c2col);
//	$this->Text($c2col, $this->company['phone'], $mcol);
	$this->NewLine();
}
if (@$this->company['fax'])
{
//	$this->Text($ccol, _("Fax"), $c2col);
//	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
if (@$this->company['email'])
{

	$this->NewLine();
}
if (@$this->company['gst_no'])
{
//	$this->Text($ccol, _("Our GST No."), $c2col);
//	$this->Text($c2col, $this->company['gst_no'], $mcol);
	$this->NewLine();
}
if (@$this->formData['domicile'])
{
//	$this->Text($ccol, _("Province"), $c2col);
//	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}
$this->Font();
$this->row = $adrline;
$this->NewLine(2);
$this->font('b');
$this->Text($mcol + 38, $this->formData['documenrt_name']);
//$this->font('');
$this->Text($mcol + 118, $this->formData['transf_no']);
//

//          $myformatdoc_date =date('d F, Y', strtotime($this->formData['document_date']));
//
//$this->multicell(300,20,"Invoice Date: " ,0,'L',0,0,435,80);
//$this->multicell(90,20,"".$myformatdoc_date ,0,'R',0,0,478,80);
//$this->multicell(300,20,"Invoice No:" ,0,'L',0,0,435,65);
//
//$myformatpo_date =date('d F, Y', strtotime($this->formData['po_date']));

$this->NewLine();
//$this->font('b');
$this->Text($mcol + 38, _(""));
//$this->font('');
$this->Text($mcol + 118, sql2date($this->formData['dofcument_date']));
//
//$this->NewLine(1.4);
//$this->font('b');
//$this->Text($mcol + 38, _("Sale Order #:"));
//$this->font('');
// $this->Text($mcol + 118, $this->formData['document_number']);

//$this->NewLine();
//$this->Text($mcol + 38, _("Prev. Balance:"));
//$this->Text($mcol + 110, ($this->formData['document_date']));

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
//$this->fontSize -= 4;
//$this->Text($ccol, $Addr1['title'], $icol);
//$this->Text($mcol, $Addr2['title']);
//$this->fontSize += 4;

// address1
$temp = $this->row = $this->row - $this->lineHeight - 5;
//$this->Text($ccol, $Addr1['name'], $icol);
//$this->NewLine();
//$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
$this->row = $temp;
//$this->Text($mcol, $Addr2['name']);
$this->NewLine();
//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

// Auxiliary document information
$col = $this->leftMargin;
$aux_info = array(
//	_("Sales Order No") => $this->formData["customer_ref"],
//	_("Sales Person") => get_salesman_name($this->formData['salesman']),
//	_("Your GST no.") => $this->formData['tax_id'],
//	_("Delivery Note No.") => implode(',', $deliveries),
//	_("Due Date") => sql2date($this->formData['due_date']),
);

foreach($aux_info as $info_header => $info_content)
{

//	$this->row = $iline2 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//	$this->row = $iline3 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//	$col += $width;
}
// Payment terms
//$this->multicell(300,20,$Addr1['name'] ,0,'L',0,0,40,110);
//

//$this->multicell(135,19, $this->formData['address']."",0,'L',0,0,40,135);
////$this->multicell(200,19, "".get_salesman_name($this->formData['salesman']),0,'L',0,0,90,210);
//$this->multicell(200,19, "".$Payment_Terms1,0,'L',0,0,390,260);
//
//          $myformatdue_date =date('d F, Y', strtotime($this->formData['due_date']));
//          $myformatso_date =date('d F, Y', strtotime($this->formData['ord_date']));
////display_error($this->formData['due_date']);
////display_error($Payment_Terms1);
//$so = get_sales_order_header($this->formData['order_'],30);
//$this->multicell(135,20,"".$so['delivery_address'] ,0,'L',0,0,435,135);
//
//$delivery = get_customer_trans($this->formData['trans_no'],13);
//$this->MultiCell(300,19, "".$so['phone'],0,'L',0,0,130,260);
//
//$this->MultiCell(410, 30, "".$so['h_text3'] ,0, 'L', 0, 2, 294,260, true);//sale officer
//$this->MultiCell(410, 30, "".$so['h_text4'] ,0, 'L', 0, 2, 215,260, true);//sale officer
//$myformatpo_date =date('d F, Y', strtotime($so['po_date']));
//
//// display_error($so['po_date']);
//
//$this->MultiCell(100, 10, "".$myformatpo_date ,0, 'L', 0, 2, 385,215, true);//po date
//
//
//          $myformatdo_date =date('d F, Y', strtotime($delivery['tran_date']));
//
//$this->multicell(200,19, "".$myformatdue_date,0,'L',0,0,479,260);
//$this->MultiCell(90, 20, "".$so['h_text5'] ,0, 'L', 0, 2, 490,215, true);
//
//$this->multicell(200,19, "".$so['reference'],0,'L',0,0,70,215);
//$this->multicell(200,19, "".$delivery['reference'],0,'L',0,0,225,215);
//$this->multicell(200,19, "".$myformatdo_date,0,'L',0,0,300,215);
//$this->multicell(200,19, "".$myformatso_date,0,'L',0,0,138,215);
//$this->multicell(150,19, $this->formData['DebtorName'],0,'L',0,0,120,120);



$this->Font('i');

$this->MultiCell(62, 10, _("Page 1 Of") . ' ' . $this->pageNumber, 0, 'R', 0, 2, 498, 35, true);
$this->Font('');
//$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

// Line headers
//$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
//$this->multicell(100,10,"Invoice To" ,0,'C',1,0,40,80);
//$this->multicell(100,10,"CUSTOMER ID:" ,0,'L',0,0,40,120);
//$this->multicell(130,10,"Shipping Address" ,0,'C',1,0,435,115);
////
////2nd column
//$this->multicell(525,20,"" ,0,'L',1,0,40,190);
//$this->multicell(525,20,"" ,0,'L',1,0,40,235);
//$this->multicell(100,40,"P.O ISSUED BY" ,0,'L',0,0,50,240);
//$this->multicell(120,40,"CONTACT NO" ,0,'L',0,0,130,240);
//$this->multicell(120,40,"SALES OFFICER " ,0,'L',0,0,203,240);
//$this->multicell(115,40,"SALES OFF NO" ,0,'L',0,0,295,240);
//$this->multicell(115,40,"PAYMENT TERMS" ,0,'L',0,0,370,240);
//$this->multicell(115,40,"PAYMENT DUE DATE" ,0,'L',0,0,468,240);
//$this->Font();
//$this->multicell(100,40,$so['f_comment1'],0,'L',0,0,50,260);
//$this->multicell(100,40,$so['h_text2'],0,'L',0,0,135,260);
//$this->multicell(100,40,"S.O NO" ,0,'L',0,0,70,195);
//$this->multicell(120,40,"S.O DATE" ,0,'L',0,0,140,195);
//$this->multicell(120,40,"D.O NO" ,0,'L',0,0,230,195);
//$this->multicell(115,40,"D.O DATE" ,0,'L',0,0,305,195);
//$this->multicell(115,40,"P.O DATE" ,0,'L',0,0,390,195);
//$this->multicell(115,40,"P.O NO" ,0,'L',0,0,490,195);
//////bottom column

if($this->pageNumber == 1) {
    $count = count($this->headers);
    $this->cols[$count] = $right - 3;
    $this->NewLine(1.5);

    for ($i = 0; $i < $count; $i++)
        $this->TextCol($i, $i + 1, $this->headers[$i], -2);
    $this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(169,169,169));
    $this->Font('');
    $this->multicell(90,19, "".$this->formData['document_number'],0,'R',0,0,478,65);

}

$this->Font();
// Footer
$this->Font('italic');
//$this->row = $iline7 - $this->lineHeight - 6;
/*
foreach ($Footer as $line => $txt)
{
	if (!is_numeric($line))	// title => link
	{
		$this->fontSize -= 2;
		$this->TextWrap($ccol, $this->row, $right - $ccol, $line, 'C');
		$this->row -= $this->lineHeight;
		$this->SetTextColor(0, 0, 255);
		$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
		$this->SetTextColor(0, 0, 0);
		$this->addLink($txt, $ccol, $this->row, $this->pageWidth - $this->rightMargin, $this->row + $this->lineHeight);
		$this->fontSize += 2;
	}
	else
		$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
	$this->row -= $this->lineHeight;

}
*/
$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
