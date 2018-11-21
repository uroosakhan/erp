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
$iline5 = $iline4 + 4 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin) / 3;
$icol = $this->pageWidth / 2;
$ccol = $this->cols[0] + 4;
$c2col = $ccol + 60;
$ccol2 = $icol / 2;
$mcol = $icol + 8;
$mcol2 = $this->pageWidth - $ccol2;
$cols = count($this->cols);
$this->SetDrawColor(205, 205, 205);
// $this->Line($iline1, 3);
$this->SetDrawColor(128, 128, 128);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
// $this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 4; $i++)
{
// 	$this->LineTo($col += $width, $iline2,$col, $iline4);
}
// $this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 2] + 2, $iline5 ,$this->cols[$cols - 2] + 2, $iline7);
$this->LineTo($this->cols[$cols - 3] + 2, $iline5 ,$this->cols[$cols - 3] + 2, $iline7);
$this->LineTo($this->cols[$cols - 4] + 2, $iline5 ,$this->cols[$cols - 4] + 2, $iline7);
$this->LineTo($this->cols[$cols - 5] + 2, $iline5 ,$this->cols[$cols - 5] + 2, $iline7);
$this->LineTo($this->cols[$cols - 6] + 2, $iline5 ,$this->cols[$cols - 6] + 2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
    $this->NewLine(+6);
 	$this->AddImage($logo, $ccol + 200, $this->row, $this->company['logo_w'], $this->company['logo_h']);
    $this->NewLine(-6);
}
else
{
	$this->fontSize += 8;
	$this->Text($ccol - 275, $this->company['coy_name'], $icol);
	$this->Text($ccol - 275, "______________________", $icol);
	$this->fontSize -= 8;
}
// Document title
$this->SetTextColor(0,0,0);
$this->fontSize += 7;
$this->Font('bold');
$this->NewLine(+14);
$this->TextWrap($ccol - 420, $this->row, $this->pageWidth - $this->rightMargin - $mcol , $this->title, 'left');
$this->Text($ccol - 420, "_________________", $icol);
$this->NewLine(-14);
$this->Font();
$this->fontSize -= 7;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;
// Company data
$this->NewLine();
$this->Text($ccol - 275, "Head Office :", $icol);
$this->NewLine();
$this->TextWrapLines($ccol - 275, $icol, $this->company['postal_address']);

if (@$this->company['phone'])
{
	$this->Text($ccol - 275, _("Ph : "), $c2col);
	$this->Text($c2col - 310, $this->company['phone'], $mcol);
	$this->NewLine();
}
if (@$this->company['fax'])
{
	$this->Text($ccol - 275, _("Fax : "), $c2col);
	$this->Text($c2col - 309, $this->company['fax'], $mcol);
	$this->NewLine();
}


$this->row = $adrline;
$this->NewLine(6);
$this->Text($ccol - 320, _("Department "));
$this->Text($ccol - 210, $this->formData['h_text1']);

$this->NewLine();
$this->Text($ccol - 320 , "P.Order No: ");
$this->Text($ccol - 210, $this->formData['document_number']);

$this->NewLine();
$this->Text($ccol - 320, _("Date: "));
$this->Text($ccol - 210, sql2date($this->formData['document_date']));

$this->NewLine();
$this->Text($ccol - 320 , "Q.Ref # & Date: ");
$this->Text($ccol - 210, $this->formData['h_text2']);

$this->NewLine();
$this->Text($ccol - 320 , "PR # & Date: ");
$this->Text($ccol - 210, $this->formData['h_text3']);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
// $this->fontSize -= 4;
// $this->Text($ccol, $Addr1['title'], $icol);
// $this->Text($mcol, $Addr2['title']);
// $this->fontSize += 4;
$this->NewLine(-7);
$this->Text($ccol, "NTN # ");
$this->Text($ccol + 70, $this->formData['ntn_no']);
$this->NewLine();
$this->Text($ccol, "Sales Tax # ");
$this->Text($ccol + 70, $this->formData['gst_no']);


// address1
$temp = $this->row = $this->row - $this->lineHeight - 5;
$this->NewLine();
$this->Text($ccol, "Supplier Name: ", $icol);
$this->NewLine();
$this->Font('bold');
$this->Text($ccol, $Addr1['name'], $icol);
$this->NewLine();
$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
$this->Font('');
$this->NewLine(+7);
// address2
// $this->row = $temp;
// $this->Text($mcol, $Addr2['name']);
// $this->NewLine();
// $this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);


// Line headers
$this->headers = array(_("S.No."), _("Description"), _("UOM"), _("Quantity"),  _("Rates"), _("Value"));
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
