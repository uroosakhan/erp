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

$upper = $this->row - 1 * $this->lineHeight;
$lower = $this->bottomMargin + 12 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 3 * $this->lineHeight;
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
$this->SetDrawColor(205, 205, 205);
//$this->Line($iline1, 3);
//$this->SetDrawColor(128, 128, 128);
//$this->Line($iline1);
//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//$this->Line($iline2);
//$this->Line($iline3);
//$this->Line($iline4);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//
$col = $this->leftMargin;
for ($i = 0; $i < 5; $i++)
{
//	$this->LineTo($col += $width, $iline2,$col, $iline4);
}
//$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 1] + 3, $iline5 ,$this->cols[$cols - 1] + 3, $iline7);
//$this->LineTo($this->cols[$cols - 3] + 4, $iline5 ,$this->cols[$cols - 3] + 4, $iline7);
//$this->LineTo($this->cols[$cols - 4] + 4, $iline5 ,$this->cols[$cols - 4] + 4, $iline7);
//$this->LineTo($this->cols[$cols - 5] + 4, $iline5 ,$this->cols[$cols - 5] + 4, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
else
{
    $this->NewLine(+5);

    $this->fontSize += 4;
	$this->Font('bold');
//	$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
	$this->fontSize -= 4;
    $this->NewLine(-5);

}
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 7;
$this->Font('bold');

$this->TextWrap($mcol-250, $this->row-58, $this->pageWidth - $this->rightMargin - $mcol + 40, $this->title, 'right');
$this->Font();
$this->fontSize -= 7;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
//$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
//$this->NewLine(-2);
$this->NewLine(+5);
//$this->TextWrapLines(80, 1, $this->company['postal_address']);
$this->NewLine(-5);

if (@$this->company['phone'])
{
    $this->NewLine(+3);

//    $this->Text($mcol -225, _("Phone:"));
//	$this->Text($mcol -180, $this->company['phone']);

    $this->NewLine(-3);


//	$this->Text($ccol, _("Phone"), $c2col);
//
//	$this->Text($c2col, $this->company['phone'], $mcol);
//	$this->NewLine();
}
$this->Font('italic');

if (@$this->company['fax'])
{
	$this->NewLine(+5);
	$this->Text($ccol-1, _("Fax"), $c2col);
	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine(-5);
	$this->NewLine();
}
if (@$this->company['email'])
{

	$this->Text($ccol, _("Email"), $c2col);

	$url = "mailto:" . $this->company['email'];
	$this->SetTextColor(0, 0, 255);
	$this->Text($c2col, $this->company['email'], $mcol);
	$this->SetTextColor(0, 0, 0);
	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

	$this->NewLine();
}
if (@$this->company['gst_no'])
{
    $this->NewLine(+4);

    $this->Text($ccol-5, _("Our GST No."), $c2col);
	$this->Text($c2col, $this->company['gst_no'], $mcol);
	$this->NewLine(-4);
}

if (@$this->company['coy_no'])
{
    $this->NewLine(4);

//    $this->Text($ccol-5, _("NTN No."), $c2col);
//    $this->Text($c2col, $this->company['coy_no'], $mcol);
    $this->NewLine(-4);
}
if (@$this->formData['domicile'])
{
	$this->Text($ccol, _("Province"), $c2col);
	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}
$this->Font();
$this->row = $adrline;
$this->NewLine(5);
$this->Text($mcol + 80, _("Date:"));
$this->Text($mcol + 170, sql2date($this->formData['document_date']));

$this->NewLine(1);
$this->Text($mcol + 80, $this->formData['document_name']);
$this->Text($mcol + 170, ($this->formData['document_number']));
$this->NewLine();
$this->Text($mcol + 80, _("Payment:"));
$this->Text($mcol + 170, get_payment_terms_($this->formData['payment_terms']));
$this->NewLine();
$this->Text($mcol + 80, _("Dispatch REF:"));
$this->Text($mcol + 170, $this->formData['customer_ref']);
$this->NewLine();
$this->Text($mcol + 80, _("Shipping Company:"));
$this->Text($mcol + 170, get_shipper_name($this->formData['ship_via']));
$this->NewLine();
$this->Text($mcol + 80, _("Customer Order No:"));
$this->Text($mcol + 170, $this->formData['customer_order_no']);
$this->NewLine();
$this->Text($mcol + 80, _("Order Date:"));
$this->Text($mcol + 170, sql2date($this->formData['h_date1']));
$this->NewLine();
$this->Text($mcol + 80, _("Location Name:"));
$this->Multicell(105,10, $this->formData['location_name'] ,0,'C',0,0,455,200);

//$this->Text($mcol , $this->formData['location_name']);

//if($this->formData['document_number']=='auto')
//{
//	$this->Text($mcol + 180, get_invoice_through_dn());
//}
//else{
//	$this->Text($mcol + 180, $this->formData['document_number']);
//}


if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 1;
$this->Font('bold');
$this->NewLine(1);

// $this->Text($ccol-6, $Addr1['title'], $icol);
$this->Text($ccol-6, "Customer", $icol);
//$this->multicell(200,10,"Customer (if other than customer)"." ",0,'L',0,0,75,190);
//$this->Text($mcol, $Addr2['name']);
$this->fontSize -= 1;
$this->Font('');

// address1
$temp = $this->row = $this->row - $this->lineHeight - 1;
$this->Text($ccol-5, $Addr1['name'], $icol);
$this->NewLine();
$this->TextWrapLines($ccol-5, $icol - $ccol, $Addr1['address']);
//
//// address2
//$this->row = $temp;
//$this->Text($mcol, $Addr2['name']);
//$this->NewLine();
//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

// Auxiliary document information
//$col = $this->leftMargin;
//foreach($aux_info as $info_header => $info_content)
//{
//
//	$this->row = $iline2 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//	$this->row = $iline3 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//	$col += $width;
//}
// Payment terms
//$this->row -= (2 * $this->lineHeight);
//		$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
//		$this->Font();

//ye kam krna hai
//$this->multicell(240,45," Payment:",0,'l',0,0,403,118);
//$this->multicell(72,25,"".get_payment_terms_($this->formData['payment_terms']),0,'l',0,0,495,118);
//$this->multicell(150,10,"Shipping Company:     ".get_shipper_name($this->formData['ship_via']),0,'L',0,0,405,145);
////$this->multicell(150,10,"Shipping Company:".$this->formData['cust_ref'],0,'L',0,0,405,200);
//$this->multicell(150,10,"Location Name:
//" .$this->formData['location_name'],0,'L',0,0,403,195);
//$this->multicell(70,10,"        ".$this->formData['reference'],0,'L',0,0,476,103);
//$this->multicell(150,10,"Dispatch REF:        ".$this->formData['customer_ref'],0,'L',0,0,405,130);
//
//$this->multicell(160,10,"Customer Order No:    ".$this->formData['customer_order_no'],0,'L',0,0,405,158);
//$this->multicell(150,10,"Order Date:  ",0,'L',0,0,405,175);
//$this->multicell(150,10,"".sql2date($this->formData['customer_order_date']),0,'L',0,0,495,175);
$this->multicell(200,120,"",1,'L',0,0,365,110);


// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');

$this->headers = array(("Description Of Goods"), _("Bonus"),_("Quantity"),
);
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();

// Footer
$this->Font('italic');
$this->row = $iline7 - $this->lineHeight - 6;

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


$this->Font();
$this->Font('bold');
$this->multicell(150,10,"For ".$this->company['coy_name']." ",0,'L',0,0,410,680);
$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>