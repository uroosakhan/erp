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
$lower = $this->bottomMargin + 8 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 2 * $this->lineHeight;
$iline6 = $iline5 - 2.5 * $this->lineHeight;
$iline21 = $upper - 2 * $this->lineHeight;
$iline22 = $upper -3.5 * $this->lineHeight;
$iline23 = $upper - 10.5 * $this->lineHeight;
$iline24 = $upper -12 * $this->lineHeight;
$iline25 = $upper - 14 * $this->lineHeight;
$iline26 = $upper - 5.5 * $this->lineHeight;
$this->Line($iline21);
$this->Line($iline22);
$this->Line($iline26);
$this->Line($iline23);
$this->Line($iline24);
$this->Line($iline25);
$this->LineTo($this->leftMargin, $iline21 ,$this->leftMargin, $iline25);

$iline7 =  $upper - 42.5 * $this->lineHeight;
$iline8 =  $upper - 20* $this->lineHeight;
$iline9 =  $upper - 22.5* $this->lineHeight;
$iline10 =  $upper - 25* $this->lineHeight;
$iline11 =  $upper - 27.5* $this->lineHeight;
$iline12 =  $upper - 30* $this->lineHeight;
$iline13 =  $upper - 32.5* $this->lineHeight;
$iline14 =  $upper - 35* $this->lineHeight;
$iline15 =  $upper - 37.5* $this->lineHeight;
$iline16 =  $upper - 40* $this->lineHeight;
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
$this->Font('');
$this->SetFontSize(7);
$this->MultiCell(142,60,$this->company['postal_address'],0,'L', 0, 2,158,130,true);
$this->MultiCell(225,12,"  ".$this->company['coy_no'],1,'L', 0, 2,154,178,true);
$this->MultiCell(110,10,sql2date($this->formData['document_date']),0,'L', 0, 2,226,94,true);
$this->MultiCell(110,10,$this->company['phone'],0,'L', 0, 2,160,196,true);
$this->MultiCell(130,10,$this->company['gst_no'],0,'L', 0, 2,158,219,true);
$this->MultiCell(140,60,$Addr1['address'],0,'L', 0, 2,380,130,true);
$this->MultiCell(200,10,$this->formData['ntn_no'],0,'L', 0, 2,380,178,true);
$this->MultiCell(200,10,$this->formData['tax_id'],0,'L', 0, 2,380,219,true);
$this->MultiCell(89,144,"",1,'L', 0, 2,290,88,true);

$this->Font('b');
$this->SetFontSize(7);
$this->SetTextColor(0, 184, 230);
$this->MultiCell(142,10,$this->company['coy_name'],0,'L', 0, 2,158,110,true);
$this->SetFontSize(13);
$this->SetTextColor(0, 0, 0);
$this->MultiCell(150,20,"SALES TAX INVOICE",0,'L', 0, 2,245,22,true);
$this->SetFontSize(-13);
$this->SetFontSize(7);
$this->MultiCell(142,10,"Duplicate",1,'L', 0, 2,423,77,true);
$this->MultiCell(142,10,"Original",1,'L', 0, 2,423,67,true);
$this->SetFontSize(10);
$this->MultiCell(70,10,"Signature:",0,'L', 0, 2,40,720,true);
$this->MultiCell(400,10,"Goods Supplied are  exempt firm WHT U/s 153(1)(a) nor subject to deduction of Sales Tax being Imported goods supplied without any value additions .",0,'C', 0, 2,120,740,true);
$this->SetFontSize(-10);


$this->SetFontSize(7);
$this->MultiCell(110,10,"_________________________",0,'L', 0, 2,95,720,true);
//$this->MultiCell(110,10,"Terms Of Sale:",0,'L', 0, 2,40,236,true);
$this->MultiCell(110,10,"Serial No.",0,'L', 0, 2,41,94,true);
$str=str_replace('ICTC',"",$this->formData['document_number']);
$this->MultiCell(110,10,$str,0,'L', 0, 2,100,94,true);
$this->MultiCell(110,10,"Date:",0,'L', 0, 2,157,94,true);
$this->MultiCell(184,12,"P.O No.",0,'L', 0, 2,300,196,true);
$this->MultiCell(184,12,"P.O Date:  ".sql2date($this->formData['po_date']),0,'L', 0, 2,450,219,true);
$this->MultiCell(110,10,"Time of Supply:",0,'L', 0, 2,301,94,true);
$this->MultiCell(110,10,"N/A.",0,'L', 0, 2,380,94,true);
$this->MultiCell(110,10,"Supplier's Name",0,'L', 0, 2,41,110,true);
$this->MultiCell(110,10,"Address",0,'L', 0, 2,41,133,true);
$this->MultiCell(110,10,"Buyer's Name",0,'L', 0, 2,301,110,true);
$this->MultiCell(110,10,"Address",0,'L', 0, 2,301,133,true);
$this->MultiCell(140,10,htmlspecialchars_decode($Addr1['name']),0,'L', 0, 2,380,110,true);
$this->MultiCell(113,12,"NTN No.",1,'L', 0, 2,41,178,true);
$this->MultiCell(110,10,"Telephone No.",0,'L', 0, 2,41,196,true);
$this->MultiCell(110,10,"NTN",0,'L', 0, 2,301,178,true);
$this->MultiCell(186,12,"",1,'L', 0, 2,379,178,true);

$this->MultiCell(120,10,"S T Reg. No. (if any)",0,'L', 0, 2,301,219,true);
$this->MultiCell(110,10,"S T Registration No.",0,'', 0, 2,41,219,true);
//$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
// 		$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] -2, $iline7);
$this->LineTo($this->cols[$cols - 2] + 2, $iline5 ,$this->cols[$cols - 2] + 2, $iline7);
$this->LineTo($this->cols[$cols - 3] + 2, $iline5 ,$this->cols[$cols - 3] + 2, $iline7);
$this->LineTo($this->cols[$cols - 4] + 2, $iline5 ,$this->cols[$cols - 4] + 2, $iline7);
$this->LineTo($this->cols[$cols - 5] + 2, $iline5 ,$this->cols[$cols - 5] + 2, $iline7);
$this->LineTo($this->cols[$cols - 6] + 10, $iline5 ,$this->cols[$cols - 6] + 10, $iline7);
$this->LineTo($this->cols[$cols - 7] + 12, $iline5 ,$this->cols[$cols - 7] + 12, $iline7);
$this->LineTo($this->cols[$cols - 8] + 35, $iline5 ,$this->cols[$cols - 8] + 35 , $iline7);
$this->LineTo($this->cols[$cols - 9] - 5, $iline5 ,$this->cols[$cols - 9] - 5, $iline7);
// $this->LineTo($this->cols[$cols - 10] + 2, $iline5 ,$this->cols[$cols - 10] + 2, $iline7);
// $this->LineTo($this->cols[$cols - 11] - 2, $iline5 ,$this->cols[$cols - 11] - 2, $iline7);
// 		$this->LineTo($this->cols[$cols - 2] -420, $iline5 ,$this->cols[$cols - 2] -420, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);
$this->LineTo($right, $iline21 ,$right, $iline25);
// $this->LineTo($this->cols[$cols - 6] +99, $iline21 ,$this->cols[$cols - 6] +99, $iline25);
// $this->LineTo($this->cols[$cols -6] -170, $iline21 ,$this->cols[$cols -6] -170, $iline25);
$this->LineTo($this->cols[$cols -9] +89, $iline21 ,$this->cols[$cols -9] +89, $iline25);
$this->LineTo($this->cols[$cols -9] -112, $iline21 ,$this->cols[$cols -9] -112, $iline22);
$this->LineTo($this->cols[$cols -9] +15, $iline21 ,$this->cols[$cols -9] +15, $iline22);
//$this->LineTo($right, $iline21 ,$right, $iline25);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
//else
{
    $this->NewLine();
	$this->fontSize += 4;
	$this->Font('bold');
	$this->SetTextColor(0, 184, 230);
	$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
	$this->SetTextColor(0 ,0,0);
	$this->fontSize -= 4;
}

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize -= 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
$this->fontSize += 4;


// Auxiliary document information
$col = $this->leftMargin;
// Payment terms
$this->row -= (7 * $this->lineHeight);
$this->Font('italic');
//if (!isset($Payment_Terms))
{
	$id = $this->formData['payment_terms'];
	$sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);
	$result = db_query($sql,"could not get paymentterms");
	$row = db_fetch($result);
	$Payment_Terms_ = _("Terms of Sales") . ': ' . $row["terms"];
}
$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms_);
$this->Font();
$this->headers = array(_("S#"), _("Description Of Goods"), _("QTY"),
	_("UNIT"), _("Rate"), _("VALUES"), _("SALES TAX"), _("F.E.D/F.TAX"), _("Total Column"));
$this->headers2 = array(_(""), _(""), _(""), _(""), _(""), _("EXCL.S.T"), _("Rate | Amount"), _("Unregistered"), _(""));
// Line headers1
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();
// Line headers2
$this->row = $iline5 - $this->lineHeight - 10;
$this->Font('bold');
$count = count($this->headers2);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers2[$i], -2);
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
//	else
//		$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
//	$this->row -= $this->lineHeight;

}

$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
