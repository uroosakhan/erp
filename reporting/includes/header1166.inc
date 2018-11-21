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
$iline5 = $iline4 - 5 * $this->lineHeight;
$iline6 = $iline5 - 2 * $this->lineHeight;
$iline21 = $upper - 2 * $this->lineHeight;
$iline22 = $upper -3.5 * $this->lineHeight;
$iline23 = $upper - 10.5 * $this->lineHeight;
$iline24 = $upper -12 * $this->lineHeight;
$iline25 = $upper - 14 * $this->lineHeight;
//$this->Line($iline21);
//$this->Line($iline22);
//$this->Line($iline23);
//$this->Line($iline24);
//$this->Line($iline25);
//$this->LineTo($this->leftMargin, $iline21 ,$this->leftMargin, $iline25);
$iline17 =  $upper - 46.8 * $this->lineHeight;
$iline7 =  $upper - 48 * $this->lineHeight;
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
//$this->Line($iline7);
//$this->Line($iline17);
$col = $this->leftMargin;
$this->Font('b');
$this->SetFontSize(15);
$this->SetFont('','bu');
$this->MultiCell(200,10,"SALES TAX INVOICE",0,'L', 0, 2,250,50,true);
//$this->SetFontSize(-15);
$this->Font('');
$this->SetFontSize(10);

//$this->MultiCell(140,18,"Supplier Name:",0,'L', 0, 2,40,93,true);
//$this->MultiCell(140,44,"Address:",0,'L', 0, 2,40,118,true);
//$this->MultiCell(140,15,"Phone Numbers:",0,'L', 0, 2,40,162,true);
//$this->MultiCell(140,15,"S.T Registration No.:",0,'L', 0, 2,40,177,true);
//$this->MultiCell(140,15,"National Tax No.:",0,'L', 0, 2,40,192,true);
//$this->MultiCell(140,23,"Buyer's Name:",0,'L', 0, 2,40,225,true);
$dc = get_delivery_date($this->formData['order_']);
$phone=buyers_phone10771($this->formData['debtor_no']);
$this->MultiCell(140,10,"Address:",0,'L', 0, 2,40,125,true);
$this->MultiCell(140,10,"Customer:",0,'L', 0, 2,40,100,true);
$this->MultiCell(140,10,"".$this->company['coy_name'],0,'L', 0, 2,100,100,true);
$this->MultiCell(140,10,"Customer GST No:",0,'L', 0, 2,40,170,true);
$this->MultiCell(140,10,"".$this->formData['tax_id'],0,'L', 0, 2,130,170,true);
$this->MultiCell(140,10,"Customer SST No:",0,'L', 0, 2,40,185,true);
$this->MultiCell(140,10,"".$this->formData['service_text'],0,'L', 0, 2,130,185,true);
$this->MultiCell(140,10,"Customer NTN No:",0,'L', 0, 2,40,200,true);
$this->MultiCell(140,10,"".$this->formData['ntn_no'],0,'L', 0, 2,130,200,true);
$this->MultiCell(140,10,"Attn:",0,'L', 0, 2,40,215,true);
$this->MultiCell(140,10,$this->formData['f_text6'],0,'L', 0, 2,130,215,true);
$this->MultiCell(140,10,"Tel:",0,'L', 0, 2,40,227,true);
$this->MultiCell(140,10,$phone['phone'],0,'L', 0, 2,130,227,true);
$this->MultiCell(140,10,"Fax:",0,'L', 0, 2,40,240,true);
$this->MultiCell(140,10,$phone['fax'],0,'L', 0, 2,130,240,true);
$this->MultiCell(140,10,"Method Of Delivery:",0,'L', 0, 2,40,264,true);
$this->MultiCell(140,10,$this->formData['f_text4'],0,'L', 0, 2,130,264,true);
$this->MultiCell(140,10,"Terms Of Delivery:",0,'L', 0, 2,40,278,true);
$this->MultiCell(140,10,$this->formData['f_text3'],0,'L', 0, 2,130,278,true);
$this->MultiCell(140,10,"Terms Of Payment:",0,'L', 0, 2,40,290,true);
$this->MultiCell(140,10,"".get_payment_terms_names_10771($this->formData['payment_terms']),0,'L', 0, 2,130,290,true);
$this->MultiCell(140,10,"Please Note:",0,'L', 0, 2,40,310,true);
$this->MultiCell(140,10,$this->formData['term_cond'],0,'L', 0, 2,130,310,true);
$this->MultiCell(140,10,"SO Number:",0,'L', 0, 2,40,327,true);
$this->MultiCell(140,10,$this->formData['order_'],0,'L', 0, 2,120,327,true);
$this->MultiCell(140,10,"PO No:",0,'L', 0, 2,200,327,true);
$this->MultiCell(140,10,$this->formData['f_text2'],0,'L', 0, 2,250,327,true);
$this->MultiCell(140,10,"Remarks:",0,'L', 0, 2,390,327,true);
$this->MultiCell(140,10,$this->formData['f_text5'],0,'L', 0, 2,435,327,true);
$this->MultiCell(140,10,"Invoice Date:",0,'L', 0, 2,350,118,true);
$this->MultiCell(140,10,"".sql2date($this->formData['document_date']),0,'L', 0, 2,430,118,true);
$this->MultiCell(140,10,"Invoice Number:",0,'L', 0, 2,350,133,true);
$this->MultiCell(140,10,$this->formData['document_number'],0,'L', 0, 2,430,133,true);
$this->MultiCell(140,10,"Our Contact:",0,'L', 0, 2,350,155,true);
$this->MultiCell(140,10,$this->company['phone'],0,'L', 0, 2,430,155,true);
$this->MultiCell(140,10,"DC No:",0,'L', 0, 2,350,170,true);
$this->MultiCell(140,10,$this->formData['f_text7'],0,'L', 0, 2,430,170,true);
$this->MultiCell(140,10,"DC Date:",0,'L', 0, 2,350,185,true);
$this->MultiCell(140,10,sql2date($dc['tran_date']),0,'L', 0, 2,430,185,true);
$this->MultiCell(140,10,"NTN No:",0,'L', 0, 2,350,200,true);
$this->MultiCell(140,10,"".$this->company['coy_no'],0,'L', 0, 2,430,200,true);
$this->MultiCell(140,10,"GST No:",0,'L', 0, 2,350,215,true);
$this->MultiCell(140,10,"".$this->company['gst_no'],0,'L', 0, 2,430,215,true);
$this->MultiCell(140,10,"SST No:",0,'L', 0, 2,350,230,true);
$this->MultiCell(140,10,"".$this->company['sst_no'],0,'L', 0, 2,430,230,true);
$this->MultiCell(140,10,"Delivery Address:",0,'L', 0, 2,350,250,true);
$this->MultiCell(140,10,"".$Addr2['address'],0,'L', 0, 2,430,250,true);

$this->Font('b');
$this->SetFontSize(9);
$this->Font('');

$this->Font('b');

$this->Font('');
$this->MultiCell(200,42,$this->company['postal_address'],0,'L', 0, 2,100,125,true);

$this->Font('b');
$this->setfontsize(+10);

$this->Font('');

$this->setfontsize(+8);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	//$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
else
{
	$this->fontSize += 4;
	$this->Font('bold');
	//$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
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
		//$Payment_Terms_ = _("Terms of Sales") . ': ' . $row["terms"];
	}
$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms_);
$this->Font();
$this->headers = array(_("S#"),_("Qty"), _("Description Of Goods"), _("Unit"),
	_("S. Tax Excluding"), _("S. Tax"), _("S. Tax"), _("Value Including"));
$this->headers2 = array(_(""),_(""), _(""), _("Price"), _("Value(Rs.)"), _("(%)"), _("    (Rs.)"),  _("All Tax"));
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
//$deliveries_date = get_sales_parent_numbers(ST_SALESINVOICE, $this->formData['trans_no']);
//if ($print_invoice_no == 0)
//{
//	foreach($deliveries_date as $n => $delivery_date)
//	{
//		$deliveries_date[$n] = get_invoice_date_through_dn(ST_CUSTDELIVERY, $delivery_date);
//	}
//}

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
