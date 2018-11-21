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
$lower = $this->bottomMargin + 21 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 5 * $this->lineHeight;
$iline6 = $iline5 - 1.8 * $this->lineHeight;
$iline8 = $iline5 - 26 * $this->lineHeight;
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

$this->Font('bold');
$this->MultiCell(32, 5, "DATE:" , 0, 'L', 0, 2, 48, 77, true);
$this->Font('');
$this->MultiCell(90, 5, $this->formData['ord_date'], 0, 'L', 0, 2, 82, 77, true);
$this->Font('bold');
$this->MultiCell(32, 5, "Ref #" , 0, 'L', 0, 2, 465, 77, true);
$this->Font('');
$this->MultiCell(100, 5, $this->formData['reference'], 0, 'L', 0, 2, 495, 77, true);

$this->Font('bold');
$this->MultiCell(25, 5, "Attn:" , 0, 'L', 0, 2, 48, 130, true);
$this->MultiCell(250, 5, "________________________" , 0, 'L', 0, 2, 75, 131, true);
$this->Font('');
$this->MultiCell(250, 5, $this->formData['customer_ref'], 0, 'L', 0, 2, 75, 130, true);

$this->Font('bold');
$this->MultiCell(25, 5, "M/S:" , 0, 'L', 0, 2, 48, 150, true);
$this->MultiCell(450, 5, "____________________________________________" , 0, 'L', 0, 2, 75, 151, true);
$this->Font('');
$this->MultiCell(450, 5, $this->formData['name'], 0, 'L', 0, 2, 75, 150, true);

$this->Font('bold');
$this->MultiCell(26, 5, "ADD:" , 0, 'L', 0, 2, 48, 170, true);
$this->MultiCell(450, 5, "_________________________________________________" , 0, 'L', 0, 2, 75, 171, true);
$this->Font('');
$this->MultiCell(450, 5, $this->formData['delivery_address'], 0, 'L', 0, 2, 75, 170, true);

$this->Font('bold');
$this->MultiCell(52, 5, "SUBJECT:" , 0, 'L', 0, 2, 48, 190, true);
$this->MultiCell(450, 5, "_________________________________________________" , 0, 'L', 0, 2, 100, 191, true);
$this->Font('');
$this->MultiCell(450, 5, $this->formData['h_text4'], 0, 'L', 0, 2, 100, 190, true);

//-----------for footer

$this->setfontsize(+10);

$this->Font('bold');
$this->NewLine(+46);
$this->TextWrapLines($ccol -2, $icol,  _("PAYMENT TERMS"));
$this->Font('');
$this->NewLine(-46);

//$this->NewLine(-24);
//$Hello = $this->formData['term_cond'];
//$this->TextWrapLines($ccol -2, $icol,  $this->formData['term_cond']);
//$this->NewLine(+24);

$this->NewLine(-17);
$this->Font('bold');
$this->TextWrapLines($ccol -2, $icol,  _("DELIVERY / PROJECT TIME"));
$this->Font('');
$this->TextWrapLines($ccol -2 , $icol,  $this->formData['f_text1']);
$this->NewLine(+17);

$this->NewLine(-13);
$this->Font('bold');
$this->TextWrapLines($ccol -2, $icol,  _("NOTE:"));
$this->Font('');
$this->TextWrapLines($ccol -2, $icol, $this->formData['f_text2']);
$this->NewLine(+13);

// Company Logo
//$this->NewLine(+3);
//$logo = company_path() . "/images/" . $this->company['coy_logo'];
//if ($this->company['coy_logo'] != '' && file_exists($logo))
//{
//	$this->AddImage($logo, $ccol, $this->row, 500, 70);
//}
//$this->NewLine(-3);
// Document title
$this->SetTextColor(0,0,0);
$this->fontSize += 10;
		$this->Font('bold');
$this->NewLine();
$this->TextWrap($ccol +115, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
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

$this->headers = array(_("SR.NO"),_("       DESCRIPTIONS"), _("QTY"), _("UNIT PRICE"), _("TOTAL"));

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
