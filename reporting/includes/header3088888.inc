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
$pagesize=$_SESSION["wa_current_user"]->prefs->get_pagesize();
$this->row = $this->pageHeight - $this->topMargin;
//if($pagesize=='A5') {
$upper = $this->row + 1 * $this->lineHeight;
$lower = $this->bottomMargin + 4.9 * $this->lineHeight;
$iline1 = $upper - 10.5 * $this->lineHeight;
$iline2 = $iline1 - 10 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 +6.5 * $this->lineHeight;
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
//}
//elseif($pagesize=='A4'){
//    $upper = $this->row - 2 * $this->lineHeight;
//    $lower = $this->bottomMargin +25 * $this->lineHeight;
//    $iline1 = $upper - 7.5 * $this->lineHeight;
//    $iline2 = $iline1 - 10 * $this->lineHeight;
//    $iline3 = $iline2 - 1.5 * $this->lineHeight;
//    $iline4 = $iline3 - 1.5 * $this->lineHeight;
//    $iline5 = $iline4 +9.5 * $this->lineHeight;
//    $iline6 = $iline5 - 1.5 * $this->lineHeight;
//    $iline7 = $lower;
//    $right = $this->pageWidth - $this->rightMargin;
//    $width = ($right - $this->leftMargin) / 5;
//    $icol = $this->pageWidth / 2;
//    $ccol = $this->cols[0] + 4;
//    $c2col = $ccol + 60;
//    $ccol2 = $icol / 2;
//    $mcol = $icol + 8;
//    $mcol2 = $this->pageWidth - $ccol2;
//    $cols = count($this->cols);
//}

//$this->SetDrawColor(205, 205, 205);
//$this->Line($iline1, 3);
//	$this->SetDrawColor(128, 128, 128);
//$this->Line($iline1);
//		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//		$this->Line($iline2);
//$this->Line($iline3);
//$this->Line($iline4);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
		$this->Line($iline5);
$this->Line($iline6);
		$this->Line($iline7);
$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
//		for ($i = 0; $i < 5; $i++)
//		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
//		}
//$this->LineTo($right, $iline2 ,$right, $iline4);
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 1] - 8, $iline5 ,$this->cols[$cols - 1]- 8, $iline7);
$this->LineTo($this->cols[$cols - 2] - 8, $iline5 ,$this->cols[$cols - 2]- 8, $iline7);
		$this->LineTo($this->cols[$cols - 0] -2, $iline5 ,$this->cols[$cols - 0] -2, $iline7);
		$this->LineTo($this->cols[$cols - 3] - 8, $iline5 ,$this->cols[$cols - 3]- 8, $iline7);
		$this->LineTo($this->cols[$cols - 4] -2, $iline5 ,$this->cols[$cols - 4] -2, $iline7);
		$this->LineTo($this->cols[$cols - 5] -2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
$this->LineTo($this->cols[$cols - 6 ] -2, $iline5 ,$this->cols[$cols - 6] -2, $iline7);
$this->LineTo($this->cols[$cols - 7 ] -2, $iline5 ,$this->cols[$cols - 7] -2, $iline7);
$this->LineTo($this->cols[$cols - 8 ] -2, $iline5 ,$this->cols[$cols - 8] -2, $iline7);
$this->LineTo($this->cols[$cols - 9 ] -2, $iline5 ,$this->cols[$cols - 9] -2, $iline7);
//$this->LineTo($this->cols[$cols - 10 ] -2, $iline5 ,$this->cols[$cols - 10] -2, $iline7);
//$this->LineTo($this->cols[$cols - 11] -2, $iline5 ,$this->cols[$cols - 11] -2, $iline7);
//$this->LineTo($this->cols[$cols - 5] -2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
    $this->AddImage($logo, $ccol, $this->row, 0, 30);


//    $this->Text($ccol+125, $this->company['coy_name']);
$this->NewLine(+1);
    $this->fontSize += 8;
    $this->Font('bold');
    $this->Text($ccol+215, "PROFORMA INVOICE");
    $this->Font();
    $this->fontSize -= 8;
    $this->NewLine(-1);
}
else
{
    
}
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;


if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk")) {
    $this->NewLine(-3);
    $this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
}
$this->row = $iline1 - $this->lineHeight;

// Auxiliary document information
$col = $this->leftMargin;
foreach($aux_info as $info_header => $info_content)
{

    $this->row = $iline2 - $this->lineHeight - 1;
    $this->TextWrap($col, $this->row, $width, $info_header, 'C');
    $this->row = $iline3 - $this->lineHeight - 1;
    $this->TextWrap($col, $this->row, $width, $info_content, 'C');
    $col += $width;
}

// Line headers
$this->row = $iline5 - $this->lineHeight -  3;
$this->Font('bold');
$this->headers = array(_("Item Code"), _("Item Description"), _("Width"), _("Length"), _("Thickness"),
    _("Qty"), _("UOM"), _("Price"), _("Total (".$this->formData['curr_code'].")"), _("Delivery Date"));
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();

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

//if($pagesize=='A5')
//{



//$this->MultiCell(48, 38, "Account",1, 'C', 0, 2, 40,265, true);
//$this->MultiCell(132, 18, "Title Account",0, 'C', 0, 2, 88,274, true);
//$this->MultiCell(159, 38, "",1, 'C', 0, 2, 220,265, true);
//$this->MultiCell(100, 15, "Cost",1, 'C', 0, 2, 379,265, true);
//$this->MultiCell(100, 15, "",1, 'C', 0, 2, 379,265, true);
//$this->MultiCell(41, 15, "Debit",1, 'C', 0, 2, 479,265, true);
//$this->MultiCell(41, 38, "",1, 'C', 0, 2, 479,265, true);
//$this->MultiCell(45, 15, "Credit",1, 'C', 0, 2, 520,265, true);
//$this->MultiCell(45, 15, "",1, 'C', 0, 2, 520,265, true);
////
//$this->MultiCell(42, 113, "",1, 'C', 0, 2, 88,302, true);
//$this->MultiCell(159, 113, "",1, 'C', 0, 2, 220,302, true);
//$this->MultiCell(100, 113, "",1, 'C', 0, 2, 379,302, true);
//$this->MultiCell(45, 135, "",1, 'C', 0, 2, 520,280, true);
//$this->MultiCell(525, 150, "",1, 'C', 0, 2, 40,265, true);
$this->font('');

date_default_timezone_set("Asia/karachi");
$date =date('d/m/y ');
$time =date('h:m:s a' ,time());


$temp = $iline6 - $this->lineHeight - 2;
?>