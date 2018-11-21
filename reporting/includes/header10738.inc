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
 
 function get_custt_name_rep($row)
{
   $sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no =".db_escape($row);

   $result = db_query($sql, "could not get customer");

   $row = db_fetch_row($result);

   return $row[0];
}


$this->row = $this->pageHeight - $this->topMargin;

$upper = $this->row + 4 * $this->lineHeight;
$lower = $this->bottomMargin + 13 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 5 * $this->lineHeight;
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
$this->LineTo($this->cols[$cols - 2] - 2, $iline5 ,$this->cols[$cols - 2] - 2, $iline7);
$this->LineTo($this->cols[$cols - 3] - 2, $iline5 ,$this->cols[$cols - 3] - 2, $iline7);
$this->LineTo($this->cols[$cols - 4] - 2, $iline5 ,$this->cols[$cols - 4] - 2, $iline7);
$this->LineTo($this->cols[$cols - 5] - 2, $iline5 ,$this->cols[$cols - 5] - 2, $iline7);
$this->LineTo($this->cols[$cols - 6] - 2, $iline5 ,$this->cols[$cols - 6] - 2, $iline7);
$this->LineTo($this->cols[$cols - 7] - 2, $iline5 ,$this->cols[$cols - 7] - 2, $iline7);
$this->LineTo($this->cols[$cols - 8] - 2, $iline5 ,$this->cols[$cols - 8] - 2, $iline7);
$this->LineTo($this->cols[$cols - 9] - 2, $iline5 ,$this->cols[$cols - 9] - 2, $iline7);
$this->LineTo($this->cols[$cols - 10] - 2, $iline5 ,$this->cols[$cols - 10] - 2, $iline7);
$this->LineTo($this->cols[$cols - 11] - 2, $iline5 ,$this->cols[$cols - 11] - 2, $iline7);
$this->LineTo($this->cols[$cols - 12] - 2, $iline5 ,$this->cols[$cols - 12] - 2, $iline7);
$this->LineTo($this->cols[$cols - 13] - 2, $iline5 ,$this->cols[$cols - 13] - 2, $iline7);
$this->LineTo($this->cols[$cols - 14] - 2, $iline5 ,$this->cols[$cols - 14] - 2, $iline7);
$this->LineTo($this->cols[$cols - 15] - 2, $iline5 ,$this->cols[$cols - 15] - 2, $iline7);
$this->LineTo($this->cols[$cols - 16] - 2, $iline5 ,$this->cols[$cols - 16] - 2, $iline7);
$this->LineTo($this->cols[$cols - 17] - 2, $iline5 ,$this->cols[$cols - 17] - 2, $iline7);
$this->LineTo($this->cols[$cols - 18] - 2, $iline5 ,$this->cols[$cols - 18] - 2, $iline7);
$this->LineTo($this->cols[$cols - 19] - 2, $iline5 ,$this->cols[$cols - 19] - 2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
//	$this->AddImage($logo, $ccol+185, $this->row, $this->company['logo_w'], $this->company['logo_h']);
}
else
{
	$this->fontSize += 4;
	$this->Font('bold');
//	$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
	$this->fontSize -= 4;
}

//$this->MultiCell(415, 587, "" , 1, 'L', 0, 2, 4,4, true);
//$this->MultiCell(415, 587, "" , 1, 'L', 0, 2, 423,4, true);

// Document title---for left side
$this->SetTextColor(0, 0, 0);
$this->fontSize += 8;
$this->Font('bold');
$this->TextWrap($ccol + 263, $this->row, $this->pageWidth - $this->rightMargin - $mcol ,"Sales Invoice", 'left');
$this->Font();
$this->fontSize -= 8;
//$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Document title---for left side
$this->SetTextColor(0, 0, 0);
$this->fontSize += 8;
$this->Font('bold');
$this->TextWrap($ccol + 670, $this->row, $this->pageWidth - $this->rightMargin - $mcol ,"Sales Invoice", 'left');
$this->Font();
$this->fontSize -= 8;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

$this->MultiCell(300, 5, "OFFICE COPY" , 0, 'L', 0, 2, 40,10, true);
$this->MultiCell(300, 5, "ORIGINAL" , 0, 'L', 0, 2, 550,10, true);

$this->setfontsize(+10);
$this->MultiCell(300, 5, "No. :   ".$this->formData['trans_no'] , 0, 'L', 0, 2, 345,35, true);
$this->MultiCell(300, 5, "No. :   ".$this->formData['trans_no'] , 0, 'L', 0, 2, 768,35, true);
$this->MultiCell(300, 5, "Dated  ".sql2date($this->formData['document_date']) , 0, 'L', 0, 2, 329,50, true);
$this->MultiCell(300, 5, "Dated  ".sql2date($this->formData['document_date']) , 0, 'L', 0, 2, 748,50, true);
$this->MultiCell(300, 5, "Due On : ".sql2date($this->formData['due_date']), 0, 'L', 0, 2, 318,65, true);
$this->MultiCell(300, 5, "Due On : ".sql2date($this->formData['due_date']), 0, 'L', 0, 2, 737,65, true);
$this->MultiCell(300, 5, "Region   "."Hydrabad" , 0, 'L', 0, 2, 328,80, true);
$this->MultiCell(300, 5, "Region   "."Hydrabad" , 0, 'L', 0, 2, 748,80, true);
$this->MultiCell(300, 5, "T. R. #  "."2323" , 0, 'L', 0, 2, 345,95, true);
$this->MultiCell(300, 5, "T. R. #  "."2323" , 0, 'L', 0, 2, 768,95, true);
$this->MultiCell(300, 5, "Dated:  ".sql2date($this->formData['document_date']) , 0, 'L', 0, 2, 327,110, true);
$this->MultiCell(300, 5, "Dated:  ".sql2date($this->formData['document_date']) , 0, 'L', 0, 2, 745,110, true);
$this->MultiCell(500, 5, "M/s. :  ".get_custt_name_rep($this->formData['debtor_no']) , 0, 'L', 0, 2, 40,60, true);
$this->MultiCell(500, 5, "M/s. :  ".get_custt_name_rep($this->formData['debtor_no']) , 0, 'L', 0, 2, 430,60, true);
$this->MultiCell(500, 5, "BOLAJ ROAD" , 0, 'L', 0, 2, 70,75, true);
$this->MultiCell(500, 5, "BOLAJ ROAD" , 0, 'L', 0, 2, 460,75, true);
$this->MultiCell(500, 5, "T.R. Co. "."SINDH GUL ARIF GOODS" , 0, 'L', 0, 2, 40,110, true);
$this->MultiCell(500, 5, "T.R. Co. "."SINDH GUL ARIF GOODS" , 0, 'L', 0, 2, 430,110, true);


$this->Font('bold');
$this->row = $adrline;
$this->NewLine(-6);
$this->Text($ccol + 10, "_________________________"."                                 _________________________"."                                 _________________________"."                                 _________________________");
$this->NewLine(+1);
$this->Text($ccol + 40, "Prepared By"."                                                             Authorized By"."                                                             Prepared By"."                                                             Authorized By");

//$this->NewLine();
//$this->Text($ccol + 290, _("Dated"));
//$this->Text($ccol + 320, sql2date($this->formData['document_date']));
//
//$this->NewLine();
//$this->Text($ccol + 280, _("Due On:"));
//$this->Text($ccol + 320, sql2date($this->formData['document_date']));
//
//$this->NewLine();
//$this->Text($ccol + 280, _("Region:"));
//$this->Text($ccol + 320, $this->formData['document_date']);


if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;

//$this->Text($ccol, $Addr1['title'], $icol);
//$this->Text($mcol, $Addr2['title']);


// address1

//$this->fontSize += 6;
//$this->NewLine(+5);
//$this->Font('bold');
//$temp = $this->row = $this->row - $this->lineHeight - 5;
//$this->Text($ccol , _("Billing Address:"));
////$this->Text($ccol , _("___________________"));
//$this->fontSize -= 6;
//$this->NewLine(-5);
//$this->NewLine(7);
//$this->fontSize += 11;
//$this->Text($ccol, $Addr1['name'], $icol);
//$this->fontSize -=11;
//$this->NewLine(1);
//$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
//$this->MultiCell(200, 5, "______________________" , 0, 'L', 0, 2, 45,165, true);
//$this->MultiCell(250, 100, "" , 1, 'L', 0, 2, 40,145, true);

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
$this->headers = array(_("Sr"),_("Item Description"), _("Packing"), _("T.P."), _("Scheme"), _("Qty."),
	_("Gross Amt"), _("Disc"), _("Net Amount"),
    _("  "),
    _("Sr"),_("Item Description"), _("Packing"), _("T.P."), _("Scheme"), _("Qty."),
    _("Gross Amt"), _("Disc"), _("Net Amount"));

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
