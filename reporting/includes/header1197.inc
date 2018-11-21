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
$lower = $this->bottomMargin + 11 * $this->lineHeight;
$iline1 = $upper -12 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 2 * $this->lineHeight;
$iline6 = $iline5 - 2.5 * $this->lineHeight;
$iline21 = $upper - 5 * $this->lineHeight;
$iline22 = $upper -6.5 * $this->lineHeight;
$iline23 = $upper - 15 * $this->lineHeight;
$iline24 = $upper -16.5 * $this->lineHeight;
$iline25 = $upper - 18 * $this->lineHeight;
$this->Line($iline21);
$this->Line($iline22);
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
$this->setfontsize(+9);
$this->MultiCell(100, 5, "Version 1.0" , 0, 'L', 0, 2, 50,58, true);
$this->MultiCell(100, 5, "Doc #: ST-FR-23" , 0, 'L', 0, 2, 50,68, true);
$this->MultiCell(100, 5, "License No. ELI-00126" , 0, 'L', 0, 2, 50,78, true);
$this->MultiCell(100, 5, "Valid up to: 03-08-2023" , 0, 'L', 0, 2,50,88, true);
$this->MultiCell(200, 5, "Issued by: Drug Regulatory Authority of Pakistan" , 0, 'L', 0, 2, 50,98, true);

$this->Font('bold');
$this->MultiCell(200, 5, "".$this->company['coy_name'] , 0, 'L', 0, 2, 50,111, true);
$this->Font('');
$this->SetDrawColor(205, 205, 205);
$this->SetDrawColor(128, 128, 128);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$col = $this->leftMargin;
$this->Font('');
$this->SetFontSize(7);

$this->MultiCell(200,10,$this->company['postal_address'],0,'L', 0, 2,158,160,true);
$this->MultiCell(110,10,sql2date($this->formData['document_date']),0,'L', 0, 2,224,130,true);
$this->MultiCell(110,10,$this->company['coy_no'],0,'L', 0, 2,160,250,true);
$this->MultiCell(110,10,$this->company['gst_no'],0,'L', 0, 2,160,270,true);
$this->MultiCell(175,30,$Addr1['address'],0,'L', 0, 2,390,164,true);
$this->MultiCell(200,10,$this->formData['ntn_no'],0,'L', 0, 2,390,250,true);
$this->MultiCell(200,10,$this->formData['tax_id'],0,'L', 0, 2,390,270,true);
$this->MultiCell(175,28,$Addr2['address'],0,'L', 0, 2,390,213,true);

$this->Font('b');
$this->SetFontSize(7);

$this->MultiCell(200,10,$this->company['coy_name'],0,'L', 0, 2,156,145,true);
$this->SetFontSize(20);
$this->MultiCell(250,20,"SALES TAX INVOICE",0,'L', 0, 2,40,22,true);
$this->SetFontSize(-20);
$this->SetFontSize(7);

$this->MultiCell(110,10,"Invoice No.",0,'L', 0, 2,41,130,true);
$this->MultiCell(110,10,$this->formData['document_number'],0,'L', 0, 2,100,130,true);
$this->MultiCell(110,10,"Date:",0,'L', 0, 2,157,130,true);
$information=get_customer_information($this->formData['debtor_no']);

$this->MultiCell(110,10,"Supplier's Name & Address",0,'L', 0, 2,41,150,true);
$this->MultiCell(110,10,"Charge To",0,'L', 0, 2,311,150,true);
$this->MultiCell(170,20,$Addr1['name'],0,'L', 0, 2,390,145,true);


$this->MultiCell(110,10,"Deliver To",0,'L', 0, 2,311,200,true);
$this->MultiCell(170,20,$Addr2['name'],0,'L', 0, 2,390,195,true);


$this->MultiCell(110,10,"Customer Ref.",0,'L', 0, 2,311,130,true);
$this->MultiCell(110,10,$this->formData["customer_ref"],0,'L', 0, 2,390,130,true);
$this->MultiCell(110,10,"Our NTN No.",0,'L', 0, 2,41,250,true);
$this->MultiCell(110,10,"NTN",0,'L', 0, 2,311,250,true);
$this->MultiCell(150,10,"S T Reg. No. (if any)",0,'L', 0, 2,311,270,true);
$this->MultiCell(120,10,"S T Registration No.",0,'', 0, 2,41,270,true);

$this->LineTo($right, $iline21 ,$right, $iline25);
$this->LineTo($this->cols[$cols - 6] +35, $iline21 ,$this->cols[$cols - 6] +35, $iline25);
$this->LineTo($this->cols[$cols -3]-200, $iline21 ,$this->cols[$cols -3] -200, $iline25);
$this->LineTo($this->cols[$cols -8] -115, $iline21 ,$this->cols[$cols -8] -115, $iline25);
$this->LineTo($this->cols[$cols -9] -155, $iline21 ,$this->cols[$cols -9] -155, $iline22);
$this->LineTo($this->cols[$cols -9] -70, $iline21 ,$this->cols[$cols -9] -70, $iline22);
$this->LineTo($right, $iline21 ,$right, $iline25);



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

$this->headers = array(_(" S.No"),_("  Item code"), _("       Description Of Goods"),_("Qty"), _("Unit   "), _("   Discount"),_("   Value Excl"), _("   Rate Of"), _("  Amount Of"), _("Value Incl"));
$this->headers2 = array(_(""),_(""),_(""), _(""), _("Price  "), _("%      "), _("Sales Tax "), _("S.Tax  "), _("Sales Tax "), _("Sales Tax"));



// Line headers1
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
	else
		$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
	$this->row -= $this->lineHeight;

}

$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>