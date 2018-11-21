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

$upper = $this->row +2 * $this->lineHeight;
$lower = $this->bottomMargin + 12 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 10 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 3 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
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
//$this->SetDrawColor(205, 205, 205);
$this->SetFillColor(222, 231, 236);
//$this->multicell(245,10,"",1,'L',1,0,320,107);
$this->SetDrawColor(128, 128, 128);
//$this->Line($iline1);
//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//    $this->Line($iline2);
//    $this->Line($iline3);
//    $this->Line($iline4);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
    $this->Line($iline5);
    $this->Line($iline6);
    $this->Line($iline7);
//    $this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
    $col = $this->leftMargin;
    for ($i = 0; $i < 6; $i++)
    {
//        $this->LineTo($col += $width, $iline2,$col, $iline4);
    }
//    $this->LineTo($right, $iline2 ,$right, $iline4);
    $this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//    $this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
    $this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] -2, $iline7);
    $this->LineTo($this->cols[$cols - 2] - 2, $iline5 ,$this->cols[$cols - 2] -2, $iline7);
    $this->LineTo($this->cols[$cols - 3] - 2, $iline5 ,$this->cols[$cols - 3] -2, $iline7);
    $this->LineTo($this->cols[$cols - 4] - 2, $iline5 ,$this->cols[$cols - 4] -2, $iline7);
    $this->LineTo($this->cols[$cols - 5] - 2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
//    $this->LineTo($this->cols[$cols - 6] - 2, $iline5 ,$this->cols[$cols - 6] -2, $iline7);
    $this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine(3);
$logo = company_path() . "/images/BGF.png"/* . $this->company['coy_logo']*/;
//if ($this->company['coy_logo'] != '' && file_exists($logo))
//{
//	$this->AddImage($logo, $ccol, $this->row, 0, 40);
//}
//else
{
//	$this->fontSize +=5;
//	$this->Font('bold');
	$this->AddImage($logo, $ccol, $this->row, 0, 70);
//	$this->Text($ccol+270, $this->company['coy_name'], $icol+400);
//	$this->NewLine(2);

//	$this->Font();
//	$this->Text($ccol+270, $this->company['postal_address']);
//	$this->fontSize -= 5;
//	$this->NewLine();
}
if (@$this->company['phone'])
{
//	$this->Text($ccol+270, _("Phone:"), $c2col);
//	$this->Text($c2col+270, $this->company['phone'], $mcol+800);
	$this->NewLine();
}
if (@$this->company['fax'])
{
//	$this->Text($ccol+270, _("Fax:"), $c2col);
//	$this->Text($c2col+270, $this->company['fax'], $mcol+800);
//	$this->NewLine();
}
// Document title
/*$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol-300, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 90, $this->title, 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;*/




$this->NewLine(9);

$this->Font('bold');
// Supplied to
$temp = $this->row = $this->row - $this->lineHeight-170;
//$this->Text($ccol+32, _('Supplied to') , $icol);
//$this->Text($mcol - 80, $Addr1['name']);
//		$this->Text($ccol+127, $Addr1['name'], $icol);
$this->NewLine(2);

//$this->Text($mcol - 226, _("Sales Tax Invoice No."));
//$this->Text($mcol - 80, $this->formData['document_number']);

$this->NewLine(2);
//$this->Text($mcol - 226, _("Invoice Dated"));
//$this->Text($mcol - 80, sql2date($this->formData['document_date']));

$this->NewLine(2);

// NTN No

$col = $this->leftMargin;
//foreach($aux_info5 as $info_header => $info_content)
//{

//			$this->row = $iline2 - $this->lineHeight - 170;
//			$this->TextWrap($col, $this->row, $width + 25, _('NTN No'), 'C');
//	$this->Text($mcol - 226, _("NTN No"));
//	$this->Text($mcol - 80, $info_content);
//			$this->row = $iline3 - $this->lineHeight - 141	;
//			$this->TextWrap($col, $this->row, $width + 224, $info_content, 'C');
//			$col += $width;
//}


$this->NewLine(2);
//Bill of entry
//$this->Text($mcol - 226, _("Bill of Entry No.") );
//$this->Text($mcol - 80, _('Attached') );

$this->Font('');
$this->headers = array(_('Leave Type'), _('Approved By'), _('Date From'), _('Date To'), _('Number of Days'), _('Comp.Date'));
	// Line headers
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
//		$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
	$this->row -= $this->lineHeight;

}

$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>