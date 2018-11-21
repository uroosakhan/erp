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
$lower = $this->bottomMargin + 13* $this->lineHeight;
$iline1 = $upper - 10* $this->lineHeight;
$iline2 = $iline1 - 2 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 3 * $this->lineHeight;
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

		$this->SetDrawColor(205, 205, 205);
		$this->SetDrawColor(128, 128, 128);
		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
		$this->Line($iline2);
		$this->Line($iline3);
		$this->Line($iline4);
		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 4; $i++)
{
			$this->LineTo($col += $width, $iline2,$col, $iline4);
}
		$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 1] +1, $iline5 ,$this->cols[$cols - 1] +1, $iline7);
$this->LineTo($this->cols[$cols - 2] +60, $iline5 ,$this->cols[$cols - 2] +60, $iline7);
$this->LineTo($this->cols[$cols - 3] -8, $iline5 ,$this->cols[$cols - 3] -8, $iline7);
$this->LineTo($this->cols[$cols - 4] -30, $iline5 ,$this->cols[$cols - 4] -30, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

$this->Font('b');
$this->MultiCell(150,15,"Terms and Condition",0,'L',0,0,250,665);

$this->SetFontSize(9);
$this->MultiCell(250,15,"Payment Terms:",0,'L',0,0,40,690);
$this->Font('');
$this->MultiCell(420,40,$this->formData['term_cond'],0,'L',0,0,115,690);

$this->Font('b');
$this->MultiCell(250,15,"Delivery:",0,'L',0,0,40,732);
$this->Font('');
// $this->MultiCell(420,20,$this->formData[''],0,'L',0,0,115,732);
$this->SetFontSize(9);

$this->Font('b');
$this->MultiCell(250,15,"Warranty",0,'L',0,0,40,755);
$this->Font('');
// $this->MultiCell(420,15,$this->formData[''],0,'L',0,0,115,755);
$this->SetFontSize(9);


$this->multicell(250,15,"________________________",0,'L',0,0,60,798);
$this->Font('b');
$this->multicell(250,15,"Supplier Sign & Stamp",0,'L',0,0,70,810);
$this->Font('');
$this->multicell(250,15,"______________________________________",0,'L',0,0,360,798);
$this->Font('b');
$this->multicell(250,15,"For & behalf of WORK TRUCK PVT LTD",0,'L',0,0,370,810);
$this->Font('');

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
//else
//{
//$this->fontSize += 10;
//$this->Font('bold');
//$this->Text($ccol +140, $this->company['coy_name'], $icol +180);
//$this->Font();
//$this->fontSize -=10;
//}
// Document title
$this->SetTextColor(0,0,0);
$this->fontSize += 10;
$this->NewLine(+3);
$this->TextWrap($ccol +115, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 30, $this->title, 'right');
$this->fontSize -= 10;

$this->NewLine(2);
$this->Font('bold');
$this->Text($mcol + 120, _("DELIVER TO"));
$this->Text($mcol + 120, _("___________"));

$this->Text($mcol - 250, _("ORDER TO"));
$this->Text($mcol - 250, _("_________"));
$this->Font('');
$this->NewLine(1);
$this->MultiCell(150, 15, $this->company['coy_name'], 0, 'L', 0, 2, 427, 120, true);
$this->MultiCell(150, 15, $this->formData['supp_name'], 0, 'L', 0, 2, 55, 120, true);
$this->NewLine(1);
$this->MultiCell(150, 30, $this->company['postal_address'], 0, 'L', 0, 2, 427, 135, true);
$this->MultiCell(150, 30, $this->formData['address'], 0, 'L', 0, 2, 55, 135, true);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;

// address2
//$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
//$this->NewLine();
//		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

// Auxiliary document information
$col = $this->leftMargin;
$aux_info = array(
    _("Our Ref:") => $this->formData['reference'],
    _("Contact Person") => $this->formData['contact'],
    _("Your GST No / Vat No.") => $this->company['gst_no'],
    _("Date") => sql2date($this->formData['ord_date']),
    _("P.O No") => $this->formData['reference'],
);
		foreach($aux_info as $info_header => $info_content)
		{
			$this->row = $iline2 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
			$this->row = $iline3 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
			$col += $width;
		}

$this->Font('bold');

$this->headers = array(_("#"), _("    Description"), _("QTY"), _("Unit Price. PKR"), _("Total Price. PKR"));
$this->row = $iline5 - $this->lineHeight - 1;
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();

// Footer
$this->Font('italic');
$this->row = $iline7 - $this->lineHeight - 2;
//$this->Font('bold');
//$this->MultiCell(500, 5, $this->company['postal_address'], 0, 'C', 0, 2,  70, 35, true);
//$this->MultiCell(350, 10, "GST#    ".$this->company['gst_no']."        "."NTN#    ".$this->company['coy_no'], 0, 'C', 0, 2, 130,73, true);
//$this->MultiCell(500, 10, "E-Mail:     ".$this->company['email'], 1, 'R', 0, 2, 65,67, true);
//$this->Font('');
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
