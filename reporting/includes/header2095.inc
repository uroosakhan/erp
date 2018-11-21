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
$lower = $this->bottomMargin + 20.5 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 4 * $this->lineHeight;
$iline6 = $iline5 - 1.8 * $this->lineHeight;
$iline8 = $iline5 - 25.5 * $this->lineHeight;
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

$this->Line($iline5);

$this->Line($iline6);
$this->Line($iline8);

$this->Line($iline7);

$col = $this->leftMargin;
for ($i = 0; $i < 6; $i++)
{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
}

$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 1] +2, $iline5 ,$this->cols[$cols - 1] +2, $iline7);
$this->LineTo($this->cols[$cols - 2] +2, $iline5 ,$this->cols[$cols - 2] +2, $iline7);
$this->LineTo($this->cols[$cols - 3] +2, $iline5 ,$this->cols[$cols - 3] +2, $iline7);
$this->LineTo($this->cols[$cols - 4] +2, $iline5 ,$this->cols[$cols - 4] +2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

//for left hand side----------------

$this->setfontsize(+10);
//$phone=get_phoneno_for_suppliers209($this->formData['supplier_id']);
$this->Font('bold');
$this->MultiCell(25, 5, "Attn:" , 0, 'L', 0, 2, 48, 120, true);
$this->MultiCell(250, 5, "________________________" , 0, 'L', 0, 2, 75, 121, true);
$this->Font('');
$this->MultiCell(250, 5, $this->formData['supp_name'], 0, 'L', 0, 2, 75, 120, true);

$this->Font('bold');
$this->MultiCell(25, 5, "M/S:" , 0, 'L', 0, 2, 48, 145, true);
$this->Font('');
$this->MultiCell(450, 5, $this->formData['address'], 0, 'L', 0, 2, 75, 145, true);

$this->MultiCell(170, 45, "" , 1, 'L', 0, 2, 393, 160, true);

$this->MultiCell(50, 15, " Date" , 1, 'L', 0, 2, 393, 160, true);
$this->MultiCell(120, 15, "   ".$this->formData['ord_date'] ,1, 'L', 0, 2, 443, 160, true);
$this->MultiCell(50, 15, " Depart" , 1, 'L', 0, 2, 393, 175, true);
$this->MultiCell(120, 15, "   Business Development" , 1, 'L', 0, 2, 443, 175, true);
$this->MultiCell(50, 15, " P.O. No" , 1, 'L', 0, 2, 393, 190, true);
$this->MultiCell(120, 15, "   ".$this->formData['reference'] , 1, 'L', 0, 2, 443, 190, true);

//$this->Font('italic');
$this->Font('italic');
$this->MultiCell(480, 10, "Please make the following for"." ".$this->company['coy_name'] , 0, 'L', 0, 2, 48, 215, true);
$this->Font('');

//-----------for footer
$this->setfontsize(+9);

$this->Font('bold');
$this->NewLine(+46);
$this->TextWrapLines($ccol -2, $icol,  _("PAYMENT TERMS"));
$this->Font('');
$this->NewLine(-46);

$this->NewLine(-17);
$this->Font('bold');
$this->TextWrapLines($ccol -2, $icol,  _("DELIVERY / PROJECT TIME"));
$this->Font('');
$this->TextWrapLines($ccol -2 , $icol,  $this->formData['Comments3']);
$this->NewLine(+17);

$this->NewLine(-13);
$this->Font('bold');
$this->TextWrapLines($ccol -2, $icol,  _("NOTE:"));
$this->Font('');
$this->TextWrapLines($ccol -2, $icol, $this->formData['Comments2']);
$this->NewLine(+13);



// Company Logo
//$this->NewLine(+3);
//$logo = company_path() . "/images/" . $this->company['coy_logo'];
//if ($this->company['coy_logo'] != '' && file_exists($logo))
//{
//	$this->AddImage($logo, $ccol, $this->row, 500, 70);
//}
//$this->NewLine(-3);
//else
//{
//$this->fontSize += 10;
//$this->Font('bold');
//$this->Text($ccol +140, $this->company['coy_name']."", $icol +180);
//$this->Font();
//$this->fontSize -=10;
//}
// Document title
$this->SetTextColor(0,0,0);
$this->fontSize += 10;
		$this->Font('bold');
$this->NewLine(-1);
$this->TextWrap($ccol +110, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
		$this->Font();
$this->fontSize -= 10;
$this->NewLine(5);
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

$this->headers = array(_("S.No."),_("     DESCRIPTION"), _("QYT"), _("UNIT PRICE"), _("AMOUNT"));

// Line headers
$this->Font('bold');

$this->row = $iline5 - $this->lineHeight - 1;
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
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

$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
