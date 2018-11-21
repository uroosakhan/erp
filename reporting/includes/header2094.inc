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
$lower = $this->bottomMargin + 24 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 3.5 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
$iline8 = $iline5 - 1.5 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin) / 6;
$icol = $this->pageWidth / 2;
$ccol = $this->cols[0] + 4;
$c2col = $ccol + 60;
$ccol2 = $icol / 2;
$mcol = $icol + 8;
$mcol2 = $this->pageWidth - $ccol2;
$cols = count($this->cols);
//		$this->SetDrawColor(205, 205, 205);
//		$this->Line($iline1, 3);
//		$this->SetDrawColor(128, 128, 128);
//		$this->Line($iline1);
//		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//		$this->Line($iline2);
//		$this->Line($iline3);
//		$this->Line($iline4);
		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 6; $i++)
{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
}

//		$this->LineTo($right, $iline2 ,$right, $iline4);
//$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//$this->LineTo($this->cols[$cols - 9] -2, $iline5 ,$this->cols[$cols - 9] -2, $iline7);
//$this->LineTo($this->cols[$cols - 8] -2, $iline5 ,$this->cols[$cols - 8] -2, $iline7);
//$this->LineTo($this->cols[$cols - 7] -2, $iline5 ,$this->cols[$cols - 7] -2, $iline7);
//$this->LineTo($this->cols[$cols - 6] -2, $iline5 ,$this->cols[$cols - 6] -2, $iline7);
//$this->LineTo($this->cols[$cols - 5] -2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
//$this->LineTo($this->cols[$cols - 4] -2, $iline5 ,$this->cols[$cols - 4] -2, $iline7);
//$this->LineTo($this->cols[$cols - 3] -2, $iline5 ,$this->cols[$cols - 3] -2, $iline7);
//$this->LineTo($this->cols[$cols - 2] -2, $iline5 ,$this->cols[$cols - 2] -2, $iline7);
//$this->LineTo($right, $iline5 ,$right, $iline7);

//for left hand side----------------

$this->setfontsize(+9);
$phone=get_phoneno_for_suppliers2091112($this->formData['supplier_id']);

$this->MultiCell(45, 5, "Vendor:" , 0, 'L', 0, 2, 48, 65, true);
$this->MultiCell(180, 5, $this->formData['supp_name'], 0, 'L', 0, 2, 95, 65, true);
$this->MultiCell(45, 5, "Address:" , 0, 'L', 0, 2, 48, 78, true);
$this->MultiCell(230, 22, $this->formData['supp_address'], 0, 'L', 0, 2, 95, 78, true);
$this->MultiCell(45, 5, "Phone:" , 0, 'L', 0, 2, 48, 100, true);
$this->MultiCell(180, 5, $phone['phone'], 0, 'L', 0, 2, 95, 100, true);
$this->MultiCell(45, 5, "E-mail:" , 0, 'L', 0, 2, 48, 113, true);
$this->MultiCell(180, 5, $phone['email'] ,0, 'L', 0, 2, 95, 113, true);
$this->MultiCell(45, 5, "GST No:" , 0, 'L', 0, 2, 48, 125, true);
$this->MultiCell(180, 5, $this->formData['gst_no'] , 0, 'L', 0, 2, 95, 125, true);
$this->MultiCell(45, 5, "SST No:" , 0, 'L', 0, 2, 48, 138, true);
$this->MultiCell(180, 5, $this->formData['service_text'] , 0, 'L', 0, 2, 95, 138, true);
$this->MultiCell(45, 5, "NTN No:" , 0, 'L', 0, 2, 48, 150, true);
$this->MultiCell(180, 5, $phone['ntn_no'] , 0, 'L', 0, 2, 95, 150, true);
$this->MultiCell(80, 5, "Contact Person:" , 0, 'L', 0, 2, 48, 162, true);
$this->MultiCell(145, 5, $this->formData['delivery_term'] , 0, 'L', 0, 2, 130, 162, true);
$this->MultiCell(45, 5, "Phone No:" , 0, 'L', 0, 2, 48, 175, true);
$this->MultiCell(180, 5, $this->formData['sales_tax'] , 0, 'L', 0, 2, 95, 175, true);
$this->MultiCell(90, 5, "Quotation No / Date:" , 0, 'L', 0, 2, 48, 188, true);
$this->MultiCell(145, 5, $this->formData['h_text1'].$this->formData['h_text2'] , 0, 'L', 0, 2, 140, 188, true);
$this->MultiCell(73, 5, "Delivery Address: " , 0, 'L', 0, 2, 48, 202, true);
$this->MultiCell(180, 22, $this->formData['delivery_address'] , 0, 'L', 0, 2, 120, 202, true);

//-----------for right hand side

$this->MultiCell(45, 5, "PO No:" , 0, 'L', 0, 2, 310, 65, true);
$this->MultiCell(135, 5, $this->formData['reference'] , 0, 'L', 0, 2, 410, 65, true);
$this->MultiCell(68, 5, "PO Date" , 0, 'L', 0, 2, 310, 79, true);
$this->MultiCell(135, 5, $this->formData['ord_date'], 0, 'L', 0, 2, 410, 79, true);
$this->MultiCell(100, 5, "Buyer Name/Phone No." , 0, 'L', 0, 2, 310, 93, true);
$this->MultiCell(250, 5,  $this->company['coy_name'] , 0, 'L', 0, 2, 410, 93, true);
$this->MultiCell(68, 5, "Email:" , 0, 'L', 0, 2, 310, 107, true);
$this->MultiCell(135, 5, $this->company['email'] , 0, 'L', 0, 2, 410, 107, true);
$this->MultiCell(68, 5, "Our GST No." , 0, 'L', 0, 2, 310, 121, true);
$this->MultiCell(135, 5, $this->company['gst_no'] , 0, 'L', 0, 2, 410, 121, true);
$this->MultiCell(75, 5, "Ours SST No." , 0, 'L', 0, 2, 310, 135, true);
$this->MultiCell(135, 5, $this->company['sst_no'] , 0, 'L', 0, 2, 410, 135, true);
$this->MultiCell(75, 5, "Our NTN No." , 0, 'L', 0, 2, 310, 150, true);
$this->MultiCell(135, 5, $this->company['coy_no'] , 0, 'L', 0, 2, 410, 150, true);
$this->MultiCell(68, 5, "Currency:" , 0, 'L', 0, 2, 310, 170, true);
$this->MultiCell(135, 5, $this->formData['curr_code'] , 0, 'L', 0, 2, 410, 170, true);
$this->MultiCell(90, 5, "Terms of payment" , 0, 'L', 0, 2, 310, 185, true);
$this->MultiCell(135, 5, get_payment_terms_name_($this->formData['payments']), 0, 'L', 0, 2, 410, 185, true);
$this->MultiCell(90, 5, "Terms of Delivery" , 0, 'L', 0, 2, 310, 200, true);
$this->MultiCell(135, 5, $this->formData['delivery_term'] , 0, 'L', 0, 2, 410, 200, true);
$this->MultiCell(90, 5, "Shipping Instruction:" , 0, 'L', 0, 2, 310, 215, true);
$this->MultiCell(135, 5, $this->formData['h_text3'] , 0, 'L', 0, 2, 410, 215, true);

// Company Logo
$this->NewLine(1.5);
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 55);
}
//else
//{
$this->fontSize += 10;
$this->Font('bold');
//$this->Text($ccol +140, $this->company['coy_name']."cname", $icol +180);
$this->Font();
$this->fontSize -=10;
//}
// Document title
$this->SetTextColor(0,0,0);
$this->fontSize += 10;
//		$this->Font('bold');
$this->NewLine(+7);
//$this->TextWrap($ccol +115, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
//		$this->Font();
$this->fontSize -= 10;
$this->NewLine(-6);
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

$this->Font();
$this->row = $adrline;
$this->NewLine(5);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
$this->fontSize -= 4;

// Payment terms
$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

//$this->headers = array(_(""),_(""), _("Material"),
//	_(""), _(""), _(""), _(""), _(""), _(""));

$this->headers = array(_("S.No."),_("Item No"), _("Description Material"), _("Qty"), _("Unit"),
	_("Price/Unit"), _("Total Price"), _("PR No"), _("Delivery Date"));
// Line headers
$this->Font('bold');

$this->row = $iline5 - $this->lineHeight - 1;
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->NewLine();
$this->row = $iline5 - $this->lineHeight - 12;
//$count = count($this->headers2);
//$this->cols[$count] = $right - 3;
//for ($i = 0; $i < $count; $i++)
//	$this->TextCol($i, $i + 1, $this->headers2[$i], -2);
$this->Font();

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

$this->fontSize += 1;
$this->Font('bold');
$this->MultiCell(150, 10, "PURCHASE ORDER" , 0, 'L', 0, 2, 248, 40, true);
$this->Font();
//$this->MultiCell(250, 5,  $this->company['coy_name'] , 0, 'L', 0, 2, 435, 93, true);
$this->fontSize -= 1;
$temp = $iline6 - $this->lineHeight - 2;
?>
