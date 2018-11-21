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
$lower = $this->bottomMargin + 14 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 9.5 * $this->lineHeight;
$iline6 = $iline5 - 2.4 * $this->lineHeight;
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
//		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
//		$this->Line($iline8);
$this->Line($iline6);
$this->Line($iline7);
//		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 6; $i++)
{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
}

//		$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 12] -2, $iline5 ,$this->cols[$cols - 12] -2, $iline7);
$this->LineTo($this->cols[$cols - 11] -2, $iline5 ,$this->cols[$cols - 11] -2, $iline7);
$this->LineTo($this->cols[$cols - 10] -2, $iline5 ,$this->cols[$cols - 10] -2, $iline7);
$this->LineTo($this->cols[$cols - 9] -2, $iline5 ,$this->cols[$cols - 9] -2, $iline7);
$this->LineTo($this->cols[$cols - 8] -2, $iline5 ,$this->cols[$cols - 8] -2, $iline7);
$this->LineTo($this->cols[$cols - 7] -2, $iline5 ,$this->cols[$cols - 7] -2, $iline7);
$this->LineTo($this->cols[$cols - 6] -2, $iline5 ,$this->cols[$cols - 6] -2, $iline7);
$this->LineTo($this->cols[$cols - 5] -2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
$this->LineTo($this->cols[$cols - 4] -2, $iline5 ,$this->cols[$cols - 4] -2, $iline7);
$this->LineTo($this->cols[$cols - 3] -2, $iline5 ,$this->cols[$cols - 3] -2, $iline7);
$this->LineTo($this->cols[$cols - 2] -2, $iline5 ,$this->cols[$cols - 2] -2, $iline7);
$this->LineTo($this->cols[$cols - 1] -2, $iline5 ,$this->cols[$cols - 1] -2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

//for left hand side----------------
$this->setfontsize(+14);
$this->MultiCell(400, 5, "ORDER CONFIRMATION" , 0, 'L', 0, 2, 320, 65, true);
$this->setfontsize(-14);

$this->setfontsize(+8.5);


$this->MultiCell(100, 5, "YOUR REF:" , 0, 'L', 0, 2, 48, 48, true);
$this->MultiCell(180, 5, $this->formData['reference'], 0, 'L', 0, 2, 110, 48, true);
$this->MultiCell(100, 5, "DATE:" , 0, 'L', 0, 2, 48, 60, true);
$this->MultiCell(230, 22, $this->formData['tran_date'], 0, 'L', 0, 2, 110, 60, true);

$this->MultiCell(100, 5, "OC #:" , 0, 'L', 0, 2, 660, 48, true);
$this->MultiCell(180, 5, $this->formData['reference'], 0, 'L', 0, 2, 722, 48, true);
$this->MultiCell(100, 5, "DATE:" , 0, 'L', 0, 2, 660, 60, true);
$this->MultiCell(230, 22, $this->formData['tran_date'], 0, 'L', 0, 2, 722, 60, true);


$this->MultiCell(100, 5, "SOLD TO:" , 0, 'L', 0, 2, 48, 90, true);
$this->MultiCell(160, 5, $this->company['coy_name'] ,0, 'L', 0, 2, 48, 103, true);
$this->MultiCell(160, 5, $this->company['postal_address'], 0, 'L', 0, 2, 48, 115, true);
$this->MultiCell(160, 5, $this->company['phone'], 0, 'L', 0, 2, 48, 135, true);
$this->MultiCell(160, 5, get_salesman_name($this->formData['Salesman_']) , 0, 'L', 0, 2, 48, 146, true);

$this->MultiCell(100, 5, "DISPATCH TO:" , 0, 'L', 0, 2, 630, 90, true);
$this->MultiCell(160, 5, $this->company['coy_name'] ,0, 'L', 0, 2, 630, 103, true);
$this->MultiCell(160, 5, $this->company['postal_address'], 0, 'L', 0, 2, 630, 115, true);
$this->MultiCell(160, 5, $this->company['phone'], 0, 'L', 0, 2, 630, 135, true);
$this->MultiCell(160, 5, get_salesman_name($this->formData['Salesman_']), 0, 'L', 0, 2, 630, 146, true);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
//else
//{
$this->fontSize += 10;
$this->Font('bold');
$this->Text($ccol +300, $this->company['coy_name'], $icol +180);
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

$this->headers2 = array(_("No."),_(""), _(""), _("(u)"), _("(mm)"), _("(Meters/mm)"), _("(Kgs)"),
	_("Delivery Date"), _("(Rs)"), _("Sales Tax (Rs)"), _("Sales Tax (Rs.)"), _(""));

$this->headers = array(_("Item"),_("Description of Goods"), _("Film Code"), _("Thickness"), _("Width"),
	_("Length"), _("Quantity"), _("Requested"), _("Base Price/Kg"), _("Add. 17%"), _("Price/Kg including"),
	_("Amount"));
// Line headers
$this->Font('bold');

$this->row = $iline5 - $this->lineHeight - 1;
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->NewLine();
$this->row = $iline5 - $this->lineHeight - 12;
$count = count($this->headers2);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers2[$i], -2);
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

$this->Font('bold');
$this->MultiCell(140,10,"Packing Mode:",0,'L',0,0,48,402);

$this->MultiCell(590,20, $this->formData['f_comment1'],0,'L',0,0,218,402);
//$this->MultiCell(50,10,"HPP",0,'L',0,0,230,405);
//$this->MultiCell(100,10,"(Items # 2,3,4,5,6)",0,'L',0,0,218,418);
//$this->MultiCell(50,10,"CARTONS",0,'L',0,0,380,405);
//$this->MultiCell(100,10,"(Items # 2,3,4,5,6)",0,'L',0,0,365,418);

$this->MultiCell(140,10,"Packed & Delivered:",0,'L',0,0,48,423);
$this->Font('');
$this->MultiCell(590,10,$this->formData['deliver_to'],0,'L',0,0,218,423);

$this->Font('bold');
$this->MultiCell(140,10,"Terms of Payment:",0,'L',0,0,48,435);
$this->Font('');
$this->MultiCell(590,25,$this->formData['term_cond'],0,'L',0,0,218,435);

$this->Font('bold');
$this->MultiCell(650,10,"Delivery is estimated to commmence within 4 weeks after placing the final order and finalizing all details or as committed by our representative.",0,'L',0,0,48,460);


$this->MultiCell(140,10,"OTHER TERMS:",0,'L',0,0,48,483);
$this->Font('');
$this->MultiCell(650,50,$this->formData['f_comment2'],0,'L',0,0,48,495);
//$this->MultiCell(650,10,"(+/-) 10% Quantity Variation is acceptable.",0,'L',0,0,48,495);
//$this->MultiCell(700,10,"Overdue payment must be cleared before dispatch.",0,'L',0,0,48,508);
//$this->MultiCell(700,10,"Dispatches are subject to payment realization in the bank and cann't be affected against cheques.",0,'L',0,0,48,520);


$this->MultiCell(250,10,"___________________________",0,'L',0,0,48,565);
$this->MultiCell(250,10,"Customer's Sign & Stamp",0,'L',0,0,60,580);

$this->MultiCell(250,10,"___________________________",0,'L',0,0,665,565);
$this->MultiCell(250,10,"Concerned Sales Person",0,'L',0,0,682,580);


$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
