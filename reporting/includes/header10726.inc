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

$upper = $this->row - 5 * $this->lineHeight;
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
//$this->Line($iline21);
//$this->Line($iline22);
//$this->Line($iline23);
//$this->Line($iline24);
//$this->Line($iline25);
//$this->LineTo($this->leftMargin, $iline21 ,$this->leftMargin, $iline25);

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
//$crm_data=get_phoneno_for_suppliers_($this->formData['debtor_no']);
//$this->MultiCell(140,70,$this->company['postal_address'],0,'L', 0, 2,158,120,true);
//$this->MultiCell(110,10,sql2date($this->formData['document_date']),0,'L', 0, 2,224,94,true);
//$this->MultiCell(110,10,$this->company['coy_no'],0,'L', 0, 2,160,196,true);
//$this->MultiCell(130,10,$this->company['gst_no'],0,'L', 0, 2,158,219,true);
//$this->MultiCell(140,60,$Addr1['address'],0,'L', 0, 2,425,130,true);
//$this->MultiCell(140,10,"Telephone # " . $crm_data['phone'],0,'L', 0, 2,425,180,true);
//$this->MultiCell(200,10,$this->formData['ntn_no'],0,'L', 0, 2,425,196,true);
//$this->MultiCell(200,10,$this->formData['tax_id'],0,'L', 0, 2,425,219,true);
//footer
$a = "Declaration : (1) We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct. (2) Certified that goods specified herein are of German manufacturer (3) We Certify that all Terms & Conditions as per PO		
		
		
		
		
		
		
		";
$this->MultiCell(350,120,$a,1,'L', 0, 2,40,700,true);
$this->MultiCell(180,120,"",1,'L', 0, 2,390,700,true);
$this->font('b');
$this->MultiCell(180,120,"FOR PROFACT AUTOMATION FZCO",1,'L', 0, 2,390,700,true);
$this->MultiCell(150,10,"Port of Discharge:",0,'L', 0, 2,40,277,true);
$this->MultiCell(150,10,"Pre-carriage by:",0,'L', 0, 2,40,237,true);
$this->MultiCell(150,10,"Final Destination:",0,'L', 0, 2,190,277,true);
$this->MultiCell(150,10,"Port of Loading:",0,'L', 0, 2,190,257,true);
$this->MultiCell(150,10,"Place of receipt by:",0,'L', 0, 2,190,237,true);
$this->MultiCell(225,10,"TERMS OF DELIVERY & PAYMENTS:",0,'L', 0, 2,340,237,true);
$this->MultiCell(300,10,"Consignee:",0,'L', 0, 2,40,157,true);
$this->MultiCell(20,20,"Attn:",0,'L', 0, 2,40,217,true);
$this->MultiCell(150,20,"M#",0,'L', 0, 2,190,217,true);
$this->MultiCell(150,20,"F#",0,'L', 0, 2,190,197,true);
$this->MultiCell(150,20,"T#",0,'L', 0, 2,190,177,true);
$this->MultiCell(225,10,"Country of Final Destination:",0,'L', 0, 2,340,157,true);
$this->MultiCell(225,10,"Invoice No & Date:",0,'L', 0, 2,340,77,true);
$this->MultiCell(225,10,"Delivery Note No. & Date:",0,'L', 0, 2,340,102,true);
$this->MultiCell(300,10,"Exporter Name & Address:",0,'L', 0, 2,40,77,true);
$this->MultiCell(300,20,$this->company['coy_name'],0,'L', 0, 2,40,87,true);
$this->MultiCell(225,10,"Buyer's Order No. & Date:",0,'L', 0, 2,340,122,true);
$this->MultiCell(75, 10, 'Total Net Weight:' ,0, 'L', 0, 2, 40,630, true);
$this->MultiCell(75, 10, 'Total Gross Weight:' ,0, 'L', 0, 2, 40,645, true);

$this->font('');
$this->MultiCell(180,10,"Authorized Signature",0,'C', 0, 2,390,810,true);
//header
$this->MultiCell(150,20,"",1,'L', 0, 2,40,277,true);

$this->MultiCell(150,10,$this->formData['f_text5'],0,'L', 0, 2,40,286,true);
$this->MultiCell(150,20,"",1,'L', 0, 2,40,257,true);
$this->MultiCell(150,20,"",1,'L', 0, 2,40,237,true);

$this->MultiCell(150,10,$this->formData['f_text4'],0,'L', 0, 2,40,246,true);
$this->MultiCell(150,20,"",1,'L', 0, 2,190,277,true);
$this->MultiCell(150,10,$this->formData['f_text8'],0,'L', 0, 2,190,286,true);
$this->MultiCell(150,20,"",1,'L', 0, 2,190,257,true);

$this->MultiCell(150,10,$this->formData['f_text7'],0,'L', 0, 2,190,266,true);
$this->MultiCell(150,20,"",1,'L', 0, 2,190,237,true);
$this->MultiCell(150,10,$this->formData['f_text6'],0,'L', 0, 2,190,246,true);

$this->MultiCell(225,60,"",1,'L', 0, 2,340,237,true);
$this->MultiCell(225,50,"",0,'L', 0, 2,340,246,true);

$this->MultiCell(300,80,"",1,'L', 0, 2,40,157,true);
$this->MultiCell(300,10,$this->formData['h_text2'],0,'L', 0, 2,40,167,true);

$this->MultiCell(300,60,"PO Box:",0,'L', 0, 2,40,177,true);
$this->MultiCell(150,30,$this->formData['h_text3'],0,'L', 0, 2,40,187,true);

$this->MultiCell(130,20,$this->formData['f_text1'],0,'L', 0, 2,60,217,true);

$this->MultiCell(150,20,$this->formData['h_text6'],0,'L', 0, 2,202,217,true);
$this->MultiCell(150,20,$this->formData['h_text5'],0,'L', 0, 2,202,197,true);
$this->MultiCell(150,20,$this->formData['h_text4'],0,'L', 0, 2,202,177,true);

$this->MultiCell(225,80,"",1,'L', 0, 2,340,157,true);
$this->MultiCell(225,70,$this->formData['f_text9'],0,'L', 0, 2,340,167,true);

$this->MultiCell(225,80,"",1,'L', 0, 2,340,77,true);
$this->MultiCell(225,15,$this->formData['document_number']."/".sql2date($this->formData['document_date']),0,'L', 0, 2,340,87,true);
$this->MultiCell(225,10,implode(',', $deliveries),0,'L', 0, 2,340,112,true);
$this->MultiCell(225,35,"",1,'L', 0, 2,340,122,true);
$this->MultiCell(225,25,"",0,'L', 0, 2,340,132,true);

$this->MultiCell(300,80,"",1,'L', 0, 2,40,77,true);

$this->MultiCell(300,10,"Registered Office:",0,'L', 0, 2,40,97,true);
$this->MultiCell(300,40,$this->company['postal_address'],0,'L', 0, 2,40,107,true);
//$this->MultiCell(300,30,"address",0,'L', 0, 2,40,117,true);
$this->MultiCell(300,10,"TEL:".$this->company['phone'].","."FAX:".$this->company['fax'],0,'L', 0, 2,40,147,true);
$this->SetFontSize(8);
$this->MultiCell(70,10,$this->formData['f_comment1']." ".$this->formData['f_text10'],0,'L', 0, 2,88,335,true);
$this->Font('b');
$this->SetFontSize(7);

//$this->MultiCell(140,10,$this->company['coy_name'],0,'L', 0, 2,156,105,true);
$this->SetFontSize(13);
$this->MultiCell(150,20,"COMMERCIAL INVOICE",0,'L', 0, 2,245,60,true);


//$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//		$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] -2, $iline7);
//$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
$this->LineTo($this->cols[$cols - 3] + 0, $iline5 ,$this->cols[$cols - 3] + 0, $iline7);
$this->LineTo($this->cols[$cols - 4] + 2, $iline5 ,$this->cols[$cols - 4] + 2, $iline7);
$this->LineTo($this->cols[$cols - 5] + 2, $iline5 ,$this->cols[$cols - 5] + 2, $iline7);
$this->LineTo($this->cols[$cols - 2] + 2, $iline5 ,$this->cols[$cols - 2] + 2, $iline7);
$this->LineTo($this->cols[$cols - 1] + 2, $iline5 ,$this->cols[$cols - 1] + 2, $iline7);
//		$this->LineTo($this->cols[$cols - 2] -420, $iline5 ,$this->cols[$cols - 2] -420, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);
//$this->LineTo($right, $iline21 ,$right, $iline25);
//$this->LineTo($this->cols[$cols - 6] +193, $iline21 ,$this->cols[$cols - 6] +193, $iline25);
//$this->LineTo($this->cols[$cols -6] +70, $iline21 ,$this->cols[$cols -6] +70, $iline25);
//$this->LineTo($this->cols[$cols -9] +156, $iline21 ,$this->cols[$cols -9] +156, $iline25);
//$this->LineTo($this->cols[$cols -9] +100, $iline21 ,$this->cols[$cols -9] +100, $iline22);
//$this->LineTo($this->cols[$cols -9] +223, $iline21 ,$this->cols[$cols -9] +223, $iline22);
//$this->LineTo($right, $iline21 ,$right, $iline25);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
else
{
	$this->fontSize += 4;
	$this->Font('bold');
	$this->Text($ccol, $this->company['coy_name'], $icol);
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
//		$id = $this->formData['payment_terms'];
//		$sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);
//		$result = db_query($sql,"could not get paymentterms");
//		$row = db_fetch($result);
//		$Payment_Terms_ = _("Terms of Sales") . ': ' . $row["terms"];
	}
$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms_);
$this->Font();
$this->headers = array(_("Marks "), _(" Nos & Kinds of"), _(" Description of Goods"), _(" Quantity"),
	_(" Rate(AED)"), _(" Total(AED)"));
$this->headers2 = array(_("& Nos."), _(" Packages"), _(""), _("(Nos)"), _(""), _(""));
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
