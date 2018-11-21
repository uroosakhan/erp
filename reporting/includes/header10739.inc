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

$upper = $this->row + 6.5 * $this->lineHeight;
$lower = $this->bottomMargin + 30 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 1 * $this->lineHeight;
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
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
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
$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] - 2, $iline7);
$this->LineTo($this->cols[$cols - 2] - 2, $iline5 ,$this->cols[$cols - 2] - 2, $iline7);
$this->LineTo($this->cols[$cols - 3] - 2, $iline5 ,$this->cols[$cols - 3] - 2, $iline7);
$this->LineTo($this->cols[$cols - 4] - 2, $iline5 ,$this->cols[$cols - 4] - 2, $iline7);
$this->LineTo($this->cols[$cols - 5] - 2, $iline5 ,$this->cols[$cols - 5] - 2, $iline7);
$this->LineTo($this->cols[$cols - 6] - 2, $iline5 ,$this->cols[$cols - 6] - 2, $iline7);
$this->LineTo($this->cols[$cols - 7] - 2, $iline5 ,$this->cols[$cols - 7] - 2, $iline7);
$this->LineTo($this->cols[$cols - 8] - 2, $iline5 ,$this->cols[$cols - 8] - 2, $iline7);
$this->LineTo($this->cols[$cols - 9] - 2, $iline5 ,$this->cols[$cols - 9] - 2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

function get_cust_name_for_rep($row)
{
    $sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no =".db_escape($row);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}

$this->setfontsize(+11);
$this->Font('bold');
$this->MultiCell(100, 10, "Invoice to :-" , 0, 'L', 0, 2, 40,60, true);
$this->MultiCell(100, 10, "__________" , 0, 'L', 0, 2, 40,62, true);
$this->Font('');

$this->setfontsize(+9);
$this->MultiCell(100, 10, "Customer Name" , 0, 'L', 0, 2, 40,78, true);
$this->MultiCell(100, 10, get_cust_name_for_rep($this->formData['debtor_no']) , 0, 'L', 0, 2, 120,78, true);
$this->MultiCell(100, 10, "Address" , 0, 'L', 0, 2, 40,90, true);
$this->MultiCell(100, 10, $this->formData['address'] , 0, 'L', 0, 2, 120,90, true);

$this->MultiCell(100, 10, "Ship To :" , 0, 'L', 0, 2, 350,78, true);
$this->MultiCell(100, 10, $this->formData['']."Direct" , 0, 'L', 0, 2, 400,78, true);

$this->MultiCell(100, 10, $this->formData['document_name'] , 0, 'L', 0, 2, 40,103, true);
$this->MultiCell(100, 10, $this->formData['document_number'] , 0, 'L', 0, 2, 120,103, true);

$this->MultiCell(100, 10, "Date", 0, 'L', 0, 2, 40,117, true);
$this->MultiCell(100, 10, sql2date($this->formData['document_date']) , 0, 'L', 0, 2, 120,117, true);

$this->MultiCell(100, 10, "T.R. Co. ", 0, 'L', 0, 2, 280,103, true);
$this->MultiCell(250, 10, "SINDH GUL ARIF GOODS" , 0, 'L', 0, 2, 280,117, true);

$this->MultiCell(100, 10, "Builty # ", 0, 'L', 0, 2, 430,103, true);
$this->MultiCell(100, 10, "1315" , 0, 'L', 0, 2, 430,117, true);

$this->MultiCell(100, 10, "# of Crtn ", 0, 'L', 0, 2, 500,103, true);
$this->MultiCell(100, 10, "   9" , 0, 'L', 0, 2, 500,117, true);

$this->MultiCell(100, 10, "Remarks", 0, 'L', 0, 2, 40,135, true);
$this->MultiCell(550, 10, $this->formData[''] , 0, 'L', 0, 2, 100,135, true);
$this->MultiCell(550, 10, "_______________________________________________________________________________________" , 0, 'L', 0, 2, 100,137, true);

$this->MultiCell(550, 10,"Prepared By :  " , 0, 'L', 0, 2, 40,780, true);
$this->MultiCell(550, 10, "____________________________" , 0, 'L', 0, 2, 100,782, true);

$this->MultiCell(550, 10,"Varified By :  " , 0, 'L', 0, 2, 360,780, true);
$this->MultiCell(550, 10, "____________________________" , 0, 'L', 0, 2, 410,782, true);
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
//	$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
	$this->fontSize -= 4;
}
// Document title
$this->SetTextColor(0, 0, 0);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($ccol - 8, $this->row, $this->pageWidth - $this->rightMargin - $mcol ,"SALE INVOICE", 'left');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;


$this->Font();
$this->row = $adrline;



if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;


// address1


// Auxiliary document information
$col = $this->leftMargin;
foreach($aux_info as $info_header => $info_content)
{

}

// Line headers
$this->headers = array(_("S.No"),_("Product Name"), _("Pack."), _("TP"), _("Schm."), _("On"),
	_("Qty."), _("Gross Amount"), _("Distb.(%)"), _("Net Amount."));

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
