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
$lower = $this->bottomMargin + 8 * $this->lineHeight;
$lower8 = $this->bottomMargin + 6.5 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 5 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 1 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
$iline7 = $lower;
$iline8 = $lower8;
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
$this->rectangle($this->leftMargin, $iline7, $right - $this->leftMargin, $iline7 - $iline8, "F", null, array(222, 231, 236));
$this->Line($iline8);
//		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 5; $i++)
{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
}
//		$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline8);
//$this->LineTo($this->cols[$cols - 2] + 40, $iline5 ,$this->cols[$cols - 2] + 40, $iline8);
//$this->LineTo($this->cols[$cols - 3] + 40, $iline5 ,$this->cols[$cols - 3] + 40, $iline8);
//$this->LineTo($this->cols[$cols - 4] + 70, $iline5 ,$this->cols[$cols - 4] + 70, $iline8);
//$this->LineTo($this->cols[$cols - 5] + 40, $iline5 ,$this->cols[$cols - 5] + 40, $iline8);
//$this->LineTo($this->cols[$cols - 6] + 40, $iline5 ,$this->cols[$cols - 6] + 40, $iline8);
//$this->LineTo($this->cols[$cols - 7] + 40, $iline5 ,$this->cols[$cols - 7] + 40, $iline8);
//$this->LineTo($this->cols[$cols - 8] - 10, $iline5 ,$this->cols[$cols - 8] - 10, $iline8);
//$this->LineTo($this->cols[$cols - 9] + 140, $iline5 ,$this->cols[$cols - 9] + 140, $iline8);
$this->LineTo($right, $iline5 ,$right, $iline8);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
else
{
	$this->fontSize += 4;
	$this->Font('bold');
	$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
	$this->fontSize -= 4;
}
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, 'SALES TAX INVOICE', 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
$this->Font('italic');
if (@$this->company['phone'])
{
	$this->Text($ccol, _("Phone"), $c2col);
	$this->Text($c2col, $this->company['phone'], $mcol);
	$this->NewLine();
}
if (@$this->company['fax'])
{
	$this->Text($ccol, _("Fax"), $c2col);
	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
if (@$this->company['coy_no'])
{
	$this->Text($ccol, _("NTN"), $c2col);
	$this->Text($c2col, $this->company['coy_no'], $mcol);
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
	$this->Text($ccol, _("Our GST No."), $c2col);
	$this->Text($c2col, $this->company['gst_no'], $mcol);
	$this->NewLine();
}
if (@$this->formData['domicile'])
{
	$this->Text($ccol, _("Province"), $c2col);
	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}
$this->Font();
$this->row = $adrline;
$this->NewLine(3);
$this->Text($mcol + 100, _("Serial #"));
$this->Text($mcol + 180, $this->formData['document_number']);
$this->NewLine();
$this->Text($mcol + 100, _("Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));


if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize -= 1;
$this->Font('bold');
$this->Text($ccol, _("Customer"), $icol);
$this->Text($mcol, _("Address"), $icol);
$this->Text($mcol+150, _("Salesman"), $icol);
$this->Font('');
$this->MultiCell(150, 10, get_salesman_name($this->formData['salesman']) , 0, 'L', 0, 2, 455,175, true);
$this->fontSize += 1;

	//	$this->Text($mcol, $Addr2['title']);


// address1
$temp = $this->row = $this->row - $this->lineHeight - 5;
$this->Text($ccol, $Addr2['name'], $icol);
$this->NewLine(3);
$this->MultiCell(525, 80, '' , 1, 'L', 0, 2, 40,150, true);
$this->Font('bold');
$this->Text($ccol, _("S.T Regn # "), $icol);
$this->Font('');
$this->MultiCell(150, 10, $this->formData['tax_id'] , 0, 'L', 0, 2, 100,210, true);

$this->Font('bold');
$this->Text($mcol, _("Tel # "), $icol);
$this->Font('');
$this->MultiCell(150, 10, $this->formData['contact_phone'] , 0, 'L', 0, 2, 340,210, true);

$this->Font('bold');
$this->Text($mcol+150, _("NTN #"), $icol);
$this->Font('');
$this->MultiCell(150, 10, $this->formData['ntn_id'] , 0, 'L', 0, 2, 500,210, true);

//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2

//$this->NewLine();
		//$this->Text($mcol, $Addr2['name']);

$this->row = $temp;
		$this->TextWrapLines($mcol, $this->rightMargin - $mcol+50, $Addr2['address']);
//$this->NewLine(-1);
 //$this->TextWrapLines($mcol+150, $this->rightMargin - $mcol+50 ,get_salesman_name($this->formData['salesman']));                          
//$this->NewLine(+1);

// Auxiliary document information
$col = $this->leftMargin;
foreach($aux_info as $info_header => $info_content)
{

//			$this->row = $iline2 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//			$col += $width;
}
// Payment terms
$this->row -= (2 * $this->lineHeight);

//$this->NewLine();
//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
//$this->TextWrap($mcol, $this->row, $right-$ccol, 'Time Of Supply : '. date("h:i:sa"));
$this->MultiCell(150, 10, "$Payment_Terms" , 0, 'L', 0, 2, 40, 245, true);
$this->MultiCell(150, 10, 'Time Of Supply : '. date("h:i:sa"), 0, 'L', 0, 2, 305, 245, true);


// Line headers
$this->headers = array(_("Item #"),_("Item Description"),_("Qty"), _("Rate"), _("Discount"),  _("Val Ex.SalesTax"),
	_("SalesTax"), _("Tot SalesTax"), _("Val Inc.SalesTax "));
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();

$this->row = $iline7 - $this->lineHeight - 1;
$this->Font('bold');
//$count = count($this->headers);
//$this->cols[$count] = $right - 3;
//for ($i = 0; $i < $count; $i++)
//$this->TextCol(7, 8, _("Total"), -2);
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




$temp = $iline6 - $this->lineHeight - 2;
?>
