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

$upper = $this->row + 4 * $this->lineHeight;
$lower = $this->bottomMargin + 23 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 3 * $this->lineHeight;
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
$this->SetDrawColor(128, 128, 128);
//$this->Line($iline1);
//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//$this->Line($iline2);
//$this->Line($iline3);
//$this->Line($iline4);
//$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 5; $i++)
{
//	$this->LineTo($col += $width, $iline2,$col, $iline4);
}
//$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 2] - 12, $iline5 ,$this->cols[$cols - 2] -12, $iline7);
$this->LineTo($this->cols[$cols - 2] + 55, $iline5 ,$this->cols[$cols - 2] + 55, $iline7);
$this->LineTo($this->cols[$cols - 2] - 54, $iline5 ,$this->cols[$cols - 2] - 54, $iline7);
$this->LineTo($this->cols[$cols - 2] - 102, $iline5 ,$this->cols[$cols - 2] - 102, $iline7);
$this->LineTo($this->cols[$cols - 2] - 153, $iline5 ,$this->cols[$cols - 2] - 153, $iline7);
$this->LineTo($this->cols[$cols - 2] - 200, $iline5 ,$this->cols[$cols - 2] - 200, $iline7);
$this->LineTo($this->cols[$cols - 2] - 232, $iline5 ,$this->cols[$cols - 2] - 232, $iline7);
$this->LineTo($this->cols[$cols - 2] - 387, $iline5 ,$this->cols[$cols - 2] - 387, $iline7);
$this->LineTo($this->cols[$cols - 2] - 263, $iline5 ,$this->cols[$cols - 2] - 263, $iline7);
$this->LineTo($this->cols[$cols - 2] - 358, $iline5 ,$this->cols[$cols - 2] - 358, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);
$this->setfontsize(+9);
$this->MultiCell(100, 5, "N.T.N #     ".$this->company['coy_no'] , 0, 'L', 0, 2, 394,170, true);
$this->MultiCell(180, 5, "Purchase Order No.".$this->formData['h_text2'] , 0, 'L', 0, 2, 394,185, true);
$this->MultiCell(100, 5, "DC #            ".get_reference(ST_CUSTDELIVERY, $delivery) , 0, 'L', 0, 2, 394,115, true);
$this->MultiCell(100, 5, "__________________" , 0, 'L', 0, 2, 394,172, true);
$this->MultiCell(150, 70, "" , 1, 'L', 0, 2, 394,144, true);
$this->MultiCell(100, 5, "For GRATON PHARMA" , 0, 'L', 0, 2, 60,800, true);
$this->MultiCell(200, 5, "_____________________" , 0, 'L', 0, 2, 60,785, true);
$this->MultiCell(200, 5, "___________________________" , 0, 'L', 0, 2, 210,785, true);
$this->MultiCell(200, 5, "___________________________" , 0, 'L', 0, 2, 452,785, true);
$this->MultiCell(200, 5, "Accounts & Finance Manager" , 0, 'L', 0, 2, 220,800, true);
$this->MultiCell(200, 5, "Receiver's Signature & Stamp" , 0, 'L', 0, 2, 452,800, true);
$this->Font('bold');

$this->MultiCell(300, 5, "Goods once sold can not be talen back or exchange." , 0, 'L', 0, 2, 40,609, true);
$this->MultiCell(300, 5, "RECEIVED ABOVE ITEMS" , 0, 'L', 0, 2, 450,679, true);
$this->MultiCell(300, 5, "IN GOOD CONDITION" , 0, 'L', 0, 2, 459,689, true);
$this->MultiCell(300, 5, "______________________" , 0, 'L', 0, 2, 452,691, true);
// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol+185, $this->row, $this->company['logo_w'], $this->company['logo_h']);
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
$this->SetTextColor(0, 0, 0);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($ccol +390, $this->row, $this->pageWidth - $this->rightMargin - $mcol ,"INVOICE", 'left');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
//$this->NewLine(3);

$this->Font('italic');
if (@$this->company['phone'])
{
	$this->Text($ccol, _("Tel"), $c2col);
	$this->Text($c2col, $this->company['phone'], $mcol);
	$this->NewLine();
}
if (@$this->company['fax'])
{
	$this->Text($ccol, _("Fax"), $c2col);
	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
if (@$this->company['email'])
{
	$this->Text($ccol, _("Email"), $c2col);

	$url = "mailto:" . $this->company['email'];
	$this->SetTextColor(0, 0, 255);
	$this->Text($c2col, $this->company['email'], $mcol);
	$this->SetTextColor(0, 0, 0);
//	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

	$this->NewLine(+4);
}
if (@$this->company['gst_no'])
{
	$this->Text($ccol + 350, _("G.S.T No."), $c2col);
	$this->Text($ccol + 350, _("____________________________"), $c2col);
	$this->Text($c2col + 350, $this->company['gst_no'], $mcol);
	$this->NewLine();
}
$this->NewLine(-4);

if (@$this->formData['domicile'])
{
//	$this->Text($ccol, _("Province"), $c2col);
//	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}
$this->Font();
$this->row = $adrline;
$this->NewLine(3);
$this->Text($ccol + 350, _("Date"));
$this->Text($ccol + 400, sql2date($this->formData['document_date']));

$this->NewLine();
$this->Text($ccol + 350, $this->formData['document_name']);
$this->Text($ccol + 400, $this->formData['document_number']);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;

//$this->Text($ccol, $Addr1['title'], $icol);
//$this->Text($mcol, $Addr2['title']);


// address1

$this->fontSize += 6;
$this->NewLine(+5);
$this->Font('bold');
$temp = $this->row = $this->row - $this->lineHeight - 5;
$this->Text($ccol , _("Billing Address:"));
//$this->Text($ccol , _("___________________"));
$this->fontSize -= 6;
$this->NewLine(-5);
$this->NewLine(7);
$this->fontSize += 11;
$this->Text($ccol, $Addr1['name'], $icol);
$this->fontSize -=11;
$this->NewLine(1);
$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
$this->MultiCell(200, 5, "______________________" , 0, 'L', 0, 2, 45,165, true);
$this->MultiCell(250, 100, "" , 1, 'L', 0, 2, 40,145, true);

// address2
//$this->row = $temp;
//$this->Text($mcol, $Addr2['name']);
//$this->NewLine();
//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

// Auxiliary document information
$col = $this->leftMargin;
foreach($aux_info as $info_header => $info_content)
{

//	$this->row = $iline2 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//	$this->row = $iline3 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//	$col += $width;
}
// Payment terms
//$this->row -= (2 * $this->lineHeight);
//$this->Font('italic');
//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
//$this->Font();

// Line headers
$this->headers = array(_("S.no"),_("Reg.#"), _("Description"), _("Qty"), _("Rate"), _("Batch"),
	_("Expiry"), _("Amount"), _("Special"), _("Manufacturer"), _("Total Amount"));

$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
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
$temp = $iline6 - $this->lineHeight - 2;
?>
