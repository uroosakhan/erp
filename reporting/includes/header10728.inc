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

$upper = $this->row + 5 * $this->lineHeight;
$lower = $this->bottomMargin + 25 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 3 * $this->lineHeight;
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
//		$this->Line($iline1, 3);
$this->SetDrawColor(128, 128, 128);
//		$this->Line($iline1);
//		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//		$this->Line($iline2);
//		$this->Line($iline3);
//		$this->Line($iline4);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
//		for ($i = 0; $i < 5; $i++)
//		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
//		}
//		$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->MultiCell(520,80,"",1,'L',0,0,40,120);
$this->MultiCell(280,80,"",1,'L',0,0,40,120);
$this->setfontsize(+9);
$this->MultiCell(120,20,"Performa Invoice No",1,'L',0,0,320,120);
$this->MultiCell(120,20,$this->formData['document_number'],1,'L',0,0,440,120);
$this->MultiCell(120,20,"Date",1,'L',0,0,320,140);
$this->MultiCell(120,20,sql2date($this->formData['document_date']),1,'L',0,0,440,140);
$this->MultiCell(120,20,"Your Ref No.",1,'L',0,0,320,160);
$this->MultiCell(120,20,$this->formData['customer_ref'],1,'L',0,0,440,160);
$this->MultiCell(120,20,"Our Contact",1,'L',0,0,320,180);
$this->MultiCell(120,20,$this->formData['contact_phone'],1,'L',0,0,440,180);
$phone=customer_phone_no1111($this->formData['debtor_no']);
$this->MultiCell(120,20,"P.O.Box",0,'L',0,0,40,140);
$this->MultiCell(160,20,"M#".$phone['email'],0,'L',0,0,160,140);
$this->MultiCell(120,28,$Addr1['address'],0,'L',0,0,40,152);
$this->MultiCell(160,20,"T#".$phone['phone'],0,'L',0,0,160,160);
$this->MultiCell(120,20,"ATTN:".$this->formData['f_text1'],0,'L',0,0,40,180);
$this->MultiCell(160,20,"F#".$phone['fax'],0,'L',0,0,160,180);

$this->MultiCell(250,140,"",1,'L',0,0,40,550);
$this->MultiCell(250,20,"Terms and Conditions",1,'C',1,0,40,550);
$this->MultiCell(125,20,"Delivery",1,'L',0,0,40,570);
$this->MultiCell(125,20,$this->formData['f_text2'],1,'L',0,0,165,570);
$this->MultiCell(125,40,"Delivery Period",1,'L',0,0,40,590);
$this->MultiCell(125,40,$this->formData['f_text3'],1,'L',0,0,165,590);
//$this->MultiCell(125,20,"Validity",1,'L',0,0,40,610);
//$this->MultiCell(125,20,"",1,'L',0,0,165,610);
$this->MultiCell(125,20,"Shipment",1,'L',0,0,40,630);
$this->MultiCell(125,20,get_shipment1111($this->formData['ship_via']),1,'L',0,0,165,630);
$this->MultiCell(125,20,"Payment Terms",1,'L',0,0,40,650);

$this->MultiCell(125,20,get_payment_terms_name1111($this->formData['payment_terms']),1,'L',0,0,165,650);

$this->MultiCell(250,140,"",1,'L',0,0,317,550);
$this->MultiCell(250,20,"Bank Details",1,'C',1,0,317,550);
$this->MultiCell(125,20,"Bank Name",1,'L',0,0,317,570);
$this->MultiCell(125,20,"Account Name",1,'L',0,0,317,590);
$this->MultiCell(125,20,"Account No",1,'L',0,0,317,610);
$this->MultiCell(125,20,"Currency",1,'L',0,0,317,630);
$this->MultiCell(125,20,"SWIFT Code",1,'L',0,0,317,650);
$this->MultiCell(125,20,"IBAN",1,'L',0,0,317,670);


if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
	$this->LineTo($this->cols[$cols - 2], $iline5 ,$this->cols[$cols - 2], $iline7);
else
//			$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
	$this->LineTo($right, $iline5 ,$right, $iline7);

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
// Document title
$this->NewLine(4);

$this->SetTextColor(0, 0, 0);
$this->fontSize += 10;
$this->Font('bold');
//$this->TextWrap($ccol +70, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, 'QUOTATION', 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
//		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
$this->Font('italic');
if (@$this->company['phone'])
{
//			$this->Text($ccol, _("Phone"), $c2col);
//			$this->Text($c2col, $this->company['phone'], $mcol);
	$this->NewLine();
}
if (@$this->company['fax'])
{
//			$this->Text($ccol, _("Fax"), $c2col);
//			$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
if (@$this->company['email'])
{
//			$this->Text($ccol, _("Email"), $c2col);

//			$url = "mailto:" . $this->company['email'];
	$this->SetTextColor(0, 0, 255);
//			$this->Text($c2col, $this->company['email'], $mcol);
	$this->SetTextColor(0, 0, 0);
	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

	$this->NewLine();
}
if (@$this->company['gst_no'])
{
//			$this->Text($ccol, _("Our VAT No."), $c2col);
//			$this->Text($c2col, $this->company['gst_no'], $mcol);
	$this->NewLine();
}
if (@$this->formData['domicile'])
{
	$this->Text($ccol, _("Domicile"), $c2col);
	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}
$this->Font();
$this->row = $adrline;
$this->NewLine(3);
//		$this->Text($mcol + 100, _("Date"));
//		$this->Text($mcol + 180, sql2date($this->formData['document_date']));

$this->NewLine();
//		$this->Text($mcol + 100, $this->formData['document_name']);
//		$this->Text($mcol + 180, $this->formData['document_number']);
$this->NewLine(2);

if ($this->pageNumber > 1)
	$this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize -= 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
$this->fontSize += 4;

// address1
$temp = $this->row = $this->row - $this->lineHeight - 5;
//		$this->Text($ccol, $Addr1['name'], $icol);
$this->NewLine();
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
$this->NewLine();
//		$this->TextWrapLines($col, $this->rightMargin - $mcol, $Addr2['address'], 'left', 0, 0, NULL, 1);

// Auxiliary document information
$col = $this->leftMargin;
$aux_info = array(
//				_("Customeggghr's Reference") => $this->formData["customer_ref"],
//				_("Sales Person") => get_salesman_name($this->formData['salesman']),
//				_("Your VAT no.") => $this->formData['tax_id'],
//				_("Our Quotation No") => $this->formData['order_no'],
//				_("Valid until") => sql2date($this->formData['delivery_date']),
);
foreach($aux_info as $info_header => $info_content)
{

	$this->row = $iline2 - $this->lineHeight - 1;
	$this->TextWrap($col, $this->row, $width, $info_header, 'C');
	$this->row = $iline3 - $this->lineHeight - 1;
	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
	$col += $width;
}
// Payment terms
$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$this->headers = array(_("No"), _("Part No."), _("Brand"), _("Item Description"),
	_("Qty"),_("Unit Price (AED)"), _("Total Amount (AED)"));
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
