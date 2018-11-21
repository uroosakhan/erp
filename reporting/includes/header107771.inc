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
$this->MultiCell(140,42,"Address:",0,'L', 0, 2,40,118,true);
$this->MultiCell(140,42,"Customer:",0,'L', 0, 2,40,100,true);
$this->MultiCell(140,42,"".$this->company['coy_name'],0,'L', 0, 2,100,100,true);
$this->MultiCell(140,42,"Customer GST No:",0,'L', 0, 2,40,170,true);
$this->MultiCell(140,42,"".$this->formData['tax_id'],0,'L', 0, 2,130,170,true);
$this->MultiCell(140,42,"Customer SST No:",0,'L', 0, 2,40,185,true);
$this->MultiCell(140,42,"".$this->formData['service_text'],0,'L', 0, 2,40,185,true);
$this->MultiCell(140,42,"Customer NTN No:",0,'L', 0, 2,40,200,true);
$this->MultiCell(140,42,"".$this->formData['ntn_no'],0,'L', 0, 2,130,200,true);
$this->MultiCell(140,42,"Attn:",0,'L', 0, 2,40,215,true);
$this->MultiCell(140,42,"Tel:",0,'L', 0, 2,40,227,true);
$this->MultiCell(140,42,"Fax:",0,'L', 0, 2,40,240,true);
$this->MultiCell(140,42,"Method Of Delivery:",0,'L', 0, 2,40,264,true);
$this->MultiCell(140,42,"Terms Of Delivery:",0,'L', 0, 2,40,278,true);
$this->MultiCell(140,42,"Terms Of Payment:",0,'L', 0, 2,40,290,true);
$this->MultiCell(140,42,"".$this->formData['payment_terms'],0,'L', 0, 2,130,290,true);
$this->MultiCell(140,42,"Please Note:",0,'L', 0, 2,40,310,true);
$this->MultiCell(140,42,"SO Number:",0,'L', 0, 2,40,327,true);
$this->MultiCell(140,42,"".$this->formData['order_'],0,'L', 0, 2,120,327,true);
$this->MultiCell(140,42,"PO No:",0,'L', 0, 2,200,327,true);
$this->MultiCell(140,42,"Remarks:",0,'L', 0, 2,390,327,true);
$this->MultiCell(140,42,"Invoice Date:",0,'L', 0, 2,400,118,true);
$this->MultiCell(140,42,"".sql2date($this->formData['document_date']),0,'L', 0, 2,460,118,true);
$this->MultiCell(140,42,"Invoice Number:",0,'L', 0, 2,400,133,true);
$this->MultiCell(140,42,"Our Contact:",0,'L', 0, 2,400,155,true);
$this->MultiCell(140,42,"DC No:",0,'L', 0, 2,400,170,true);
$this->MultiCell(140,42,"DC Date:",0,'L', 0, 2,400,185,true);
$this->MultiCell(140,42,"NTN No:",0,'L', 0, 2,400,200,true);
$this->MultiCell(140,42,"".$this->company['coy_no'],0,'L', 0, 2,440,200,true);
$this->MultiCell(140,42,"GST No:",0,'L', 0, 2,400,215,true);
$this->MultiCell(140,42,"".$this->company['gst_no'],0,'L', 0, 2,440,215,true);
$this->MultiCell(140,42,"SST No:",0,'L', 0, 2,400,230,true);
$this->MultiCell(140,42,"".$this->company['sst_no'],0,'L', 0, 2,440,230,true);
$this->MultiCell(140,42,"Delivery Address:",0,'L', 0, 2,400,250,true);
$this->MultiCell(140,42,"".$Addr2['address'],0,'L', 0, 2,490,250,true);
//$this->MultiCell(140,15,"Phone No:",0,'L', 0, 2,40,290,true);
//$this->MultiCell(140,15,"S.T Registration No:",0,'L', 0, 2,40,305,true);
//$this->MultiCell(140,15,"National Tax No.:",0,'L', 0, 2,40,320,true);
//$this->MultiCell(90,15,"Invoice No.:",0,'L', 0, 2,360,100,true);
//$this->MultiCell(90,15,"Date:",0,'L', 0, 2,360,115,true);
//$this->MultiCell(90,15,"Your Order No.:",0,'L', 0, 2,360,148,true);
//$this->MultiCell(90,15,"Your Order Date:",0,'L', 0, 2,360,163,true);
//$this->MultiCell(90,15,"Our D.O. No.:",0,'L', 0, 2,360,193,true);
//$this->MultiCell(90,15,"D.O. Date:",0,'L', 0, 2,360,208,true);
//$this->MultiCell(90,15,"Account No.:",0,'L', 0, 2,360,250,true);
$this->Font('b');
$this->SetFontSize(9);
//$this->MultiCell(200,18,$this->company['coy_name'],0,'L', 0, 2,140,93,true);
$this->Font('');
//$this->MultiCell(200,44,$this->company['postal_address'],0,'L', 0, 2,140,118,true);
//$this->MultiCell(200,15,$this->company['phone'],0,'L', 0, 2,140,162,true);
//$this->MultiCell(200,15,$this->company['gst_no'],0,'L', 0, 2,140,177,true);
//$this->MultiCell(200,15,$this->company['coy_no'],0,'L', 0, 2,140,192,true);
$this->Font('b');
//truncate_text((htmlspecialchars_decode(get_customer_name($row['debtor_no']))), 12, '') ;
//$phone=buyers_phone($this->formData['debtor_no']);
//$dn=get_do_no($this->formData['order_']);
//$this->MultiCell(200,15,htmlspecialchars_decode($Addr2['name']),0,'L', 0, 2,140,225,true);
$this->Font('');
//$this->MultiCell(200,42,$Addr2['address'],0,'L', 0, 2,140,118,true);
$this->MultiCell(200,42,$this->company['postal_address'],0,'L', 0, 2,100,118,true);
//$this->MultiCell(200,15,$phone['phone'],0,'L', 0, 2,140,290,true);
//$this->MultiCell(200,15,$this->formData['tax_id'],0,'L', 0, 2,140,305,true);
//$this->MultiCell(200,15,$this->formData['ntn_no'],0,'L', 0, 2,140,320,true);
//$this->MultiCell(90,15,$this->formData['document_number'],0,'L', 0, 2,450,100,true);
//$this->MultiCell(90,15,sql2date($this->formData['document_date']),0,'L', 0, 2,450,115,true);
//$this->MultiCell(90,15,$this->formData["customer_ref"],0,'L', 0, 2,450,148,true);
//$this->MultiCell(90,15,sql2date($this->formData['ord_date']),0,'L', 0, 2,450,163,true);

//$a = get_delivery_date($this->formData['trans_no']);
//$bank = get_bank_details();
//$this->MultiCell(90,15,sql2date($a),0,'L', 0, 2,450,208,true);
//$this->MultiCell(90,15,$bank["bank_account_number"],0,'L', 0, 2,450,250,true);
//$this->MultiCell(150,10,$bank["bank_name"],0,'L', 0, 2,365,270,true);
//$this->MultiCell(150,40,$bank["bank_address"],0,'L', 0, 2,365,283,true);
$this->Font('b');
$this->setfontsize(+10);
//$this->MultiCell(170,15,$this->company['coy_name'],0,'L', 0, 2,40,670,true);
$this->Font('');
//$this->MultiCell(180,15,"__________________________",0,'L',0,0,40,720);
//$this->MultiCell(180,15,"__________________________",0,'L',0,0,220,720);
//$this->MultiCell(180,15,"__________________________",0,'L',0,0,400,720);
//$this->MultiCell(80,15,"Prepared by",0,'L',0,0,75,731);
//$this->MultiCell(80,15,"Checked by",0,'L',0,0,255,731);
//$this->MultiCell(80,15,"Approved by",0,'L',0,0,448,731);
$this->setfontsize(+8);
//$this->MultiCell(200,10,$this->company['postal_address'],0,'L', 0, 2,158,120,true);
//$this->MultiCell(110,10,sql2date($this->formData['document_date']),0,'L', 0, 2,224,94,true);
//$this->MultiCell(110,10,$this->company['phone'],0,'L', 0, 2,160,196,true);
//$this->MultiCell(110,10,$this->company['gst_no'],0,'L', 0, 2,160,219,true);
//$this->MultiCell(100,70,$Addr1['address'],0,'L', 0, 2,460,120,true);
//$this->MultiCell(200,10,$this->formData['ntn_id'],0,'L', 0, 2,461,196,true);
//$this->MultiCell(200,10,$this->formData['tax_id'],0,'L', 0, 2,461,219,true);

//$this->Font('b');
//$this->SetFontSize(7);

//$this->MultiCell(200,10,$this->company['coy_name'],0,'L', 0, 2,156,110,true);
//$this->SetFontSize(13);
//$this->MultiCell(150,20,"SALES TAX INVOICE",0,'L', 0, 2,245,22,true);
//$this->SetFontSize(-13);
//$this->SetFontSize(7);
//$this->MultiCell(142,10,"Duplicate",1,'L', 0, 2,423,72,true);
//$this->MultiCell(71,10,"",1,'L', 0, 2,494,72,true);
//$this->MultiCell(142,10,"Original",1,'L', 0, 2,423,57,true);
//$this->MultiCell(71,10,"",1,'L', 0, 2,494,57,true);
//$this->SetFontSize(10);
//$this->MultiCell(70,10,"Signature:",0,'L', 0, 2,40,650,true);
//$this->SetFontSize(-10);
//$this->SetFontSize(7);
//$this->MultiCell(110,10,"_________________________",0,'L', 0, 2,95,650,true);
////$this->MultiCell(110,10,"Terms Of Sale:",0,'L', 0, 2,40,236,true);
//$this->MultiCell(110,10,"Serial No.",0,'L', 0, 2,41,94,true);
//$this->MultiCell(110,10,$this->formData['document_number'],0,'L', 0, 2,100,94,true);
//$this->MultiCell(110,10,"Date:",0,'L', 0, 2,157,94,true);
//
//$this->MultiCell(110,10,"Time of Supply:",0,'L', 0, 2,351,94,true);
//$this->MultiCell(110,10,"N/A.",0,'L', 0, 2,461,94,true);
//$this->MultiCell(110,10,"Supplier's Name & Address",0,'L', 0, 2,41,110,true);
//$this->MultiCell(110,10,"Buyer's Name & Address",0,'L', 0, 2,351,110,true);
//$this->MultiCell(110,10,$Addr1['name'],0,'L', 0, 2,461,110,true);
//$this->MultiCell(110,10,"Telephone No.",0,'L', 0, 2,41,196,true);
//$this->MultiCell(110,10,"NTN",0,'L', 0, 2,351,196,true);
//$this->MultiCell(150,10,"S T Reg. No. (if any)",0,'L', 0, 2,351,219,true);
//$this->MultiCell(120,10,"S T Registration No.",0,'', 0, 2,41,219,true);
//$this->LineTo($right, $iline2 ,$right, $iline4);
//$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] -2, $iline7);
//$this->LineTo($this->cols[$cols - 2] + 20, $iline5 ,$this->cols[$cols - 2] + 20, $iline7);
//$this->LineTo($this->cols[$cols - 2] + 10, $iline5 ,$this->cols[$cols - 2] + 10, $iline7);
//$this->LineTo($this->cols[$cols - 3] + 0, $iline5 ,$this->cols[$cols - 3] + 0, $iline7);
//$this->LineTo($this->cols[$cols - 4] + 2, $iline5 ,$this->cols[$cols - 4] + 2, $iline7);
//$this->LineTo($this->cols[$cols - 5] + 2, $iline5 ,$this->cols[$cols - 5] + 2, $iline7);
//$this->LineTo($this->cols[$cols - 5] - 60, $iline5 ,$this->cols[$cols - 5] - 60, $iline7);
//$this->LineTo($this->cols[$cols - 2] -395, $iline5 ,$this->cols[$cols - 2] -395, $iline7);
//$this->LineTo($this->cols[$cols - 2] -415, $iline5 ,$this->cols[$cols - 2] -415, $iline7);
//$this->LineTo($right, $iline5 ,$right, $iline7);
//$this->LineTo($right, $iline21 ,$right, $iline25);
//$this->LineTo($this->cols[$cols - 6] +228, $iline21 ,$this->cols[$cols - 6] +228, $iline25);
//$this->LineTo($this->cols[$cols -6] +120, $iline21 ,$this->cols[$cols -6] +120, $iline25);
//$this->LineTo($this->cols[$cols -9] +156, $iline21 ,$this->cols[$cols -9] +156, $iline25);
//$this->LineTo($this->cols[$cols -9] +100, $iline21 ,$this->cols[$cols -9] +100, $iline22);
//$this->LineTo($this->cols[$cols -9] +223, $iline21 ,$this->cols[$cols -9] +223, $iline22);
//$this->LineTo($right, $iline21 ,$right, $iline25);

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
