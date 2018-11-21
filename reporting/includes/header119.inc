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

$upper = $this->row + 1 * $this->lineHeight;
$lower = $this->bottomMargin + 11 * $this->lineHeight;
$iline1 = $upper-8 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 4 * $this->lineHeight;
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

$this->SetDrawColor(128, 128, 128);

$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);

$col = $this->leftMargin;
for ($i = 0; $i < 5; $i++)
{
	//$this->LineTo($col += $width, $iline2,$col, $iline4);
}
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 2] -2, $iline5 ,$this->cols[$cols - 2] -2, $iline7);
$this->LineTo($this->cols[$cols - 3] -2, $iline5 ,$this->cols[$cols - 3] -2, $iline7);
$this->LineTo($this->cols[$cols - 4] -2, $iline5 ,$this->cols[$cols - 4] -2, $iline7);
$this->LineTo($this->cols[$cols - 5] -2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
$this->LineTo($this->cols[$cols - 6] -2, $iline5 ,$this->cols[$cols - 6] -2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	//$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
else
{
	//$this->fontSize += 10;
	//$this->Font('bold');

//			$this->Text($ccol, $this->company['coy_name'], $icol);

	$this->Font();
	//$this->fontSize -= 10;
}
// Document title
$this->fontSize += 12;
$this->Font('bold');
$this->NewLine();
$this->TextWrap($mcol-140, $this->row, $this->pageWidth - $this->rightMargin - $mcol , _("GATE PASS"), 'center');
$this->MultiCell(200, 20, "___________", 0, 'L', 0, 2, 232,45, true);
$this->Font();
$this->fontSize -= 12;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;

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

// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->headers = array(_("S.NO"), _("ITEM DESCRIPTION"), _("PACKING(PCS)"), _("DO NO"), _("QTY IN CARTONS"), _("QTY IN PACK"));
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
$this->Font('b');

function get_payment_terms_name($selected_id)
{
	$sql = "SELECT  terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

	$result = db_query($sql,"could not get payment term");
	$row =db_fetch_row($result);
	return $row[0];
}
//--------top right box-----------//
$this->setfontsize(12);

$this->MultiCell(180, 150, "GP No:                      ".$this->formData['gate_pass_no'], 0, 'L', 0, 2, 40,100, true);
$this->MultiCell(180, 150, "_____________________" , 0, 'L', 0, 2, 110,100, true);
$this->MultiCell(180, 150, "Date:            ".sql2date($this->formData['gate_pass_date']) , 0, 'L', 0, 2, 350,100, true);
$this->MultiCell(180, 150, "_____________________" , 0, 'L', 0, 2, 400,100, true);

$this->MultiCell(180, 150, "Driver Name:            " .$this->formData['driver_name'], 0, 'L', 0, 2, 40,130, true);
$this->MultiCell(400, 150, " __________________________________________" , 0, 'L', 0, 2, 114,130, true);

$this->MultiCell(180, 150, "Vehicle No:              ".$this->formData['vehicle_no'] , 0, 'L', 0, 2, 40,160, true);
$this->MultiCell(400, 150, " __________________________________________" , 0, 'L', 0, 2, 114,160, true);
$this->setfontsize(9);


$this->MultiCell(180, 150, "Remarks:" , 0, 'L', 0, 2, 40,700, true);
$this->MultiCell(465, 20, "" , 0, 'L', 0, 2, 100,700, true);

$this->MultiCell(130, 150, "Prepared by", 0, 'L', 0, 2, 110,750, true);
$this->MultiCell(130, 150, "_________________________", 0, 'L', 0, 2, 75,740, true);

$this->MultiCell(130, 150, "Verified by", 0, 'L', 0, 2, 420,750, true);
$this->MultiCell(130, 150, "_________________________", 0, 'L', 0, 2, 380,740, true);

$this->MultiCell(130, 150, "Transporter", 0, 'C', 0, 2, 73,800, true);
$this->MultiCell(130, 150, "_________________________", 0, 'L', 0, 2, 75,790, true);


$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
