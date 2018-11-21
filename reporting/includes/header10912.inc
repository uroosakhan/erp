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
$lower = $this->bottomMargin + 18 * $this->lineHeight;
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
$this->SetDrawColor(128, 128, 128);
$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
$this->Line($iline2);
$this->Line($iline3);
$this->Line($iline4);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 5; $i++)
{
	$this->LineTo($col += $width, $iline2,$col, $iline4);
}
$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
// if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
// 	$this->LineTo($this->cols[$cols - 1], $iline5 ,$this->cols[$cols - 1], $iline7);
// else
	$this->LineTo($this->cols[$cols + 1] - 1, $iline5 ,$this->cols[$cols + 1] - 1, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol +430, $this->row, 0, 40);
}
 else
 {
	$this->fontSize += 4;
	$this->Font('bold');
			$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
	$this->fontSize -= 4;
	}
$myrow1 = get_sales_order_header($this->formData['order_no'], ST_SALESORDER);
//marina----------
$this->headers = array(_("S.No"), _("PO Pos No"), _("Item No"), _("Description"),
	_("Data Type"), _("Qty"), _("Rate"), _("Value"),_(" "), _("Remarks"));

$this->formData['document_name'] =_("Order No.");
$this->formData['document_date'] = $this->formData['ord_date'];
$this->formData['document_number'] = $SysPrefs->print_invoice_no() == 0 && isset($this->formData['reference'])
	? $this->formData['reference'] : $this->formData['order_no'];
$this->formData['document_amount'] = $this->formData['order_no'];

$aux_info = array(
	_("Purchase Order no") =>$myrow1['f_text2'],
	_("Purchase Order Date") => sql2date($myrow1['po_date']),
	_("Quotation Ref") => $myrow1['f_text1'],
	_("Quotation Date") => sql2date($myrow1['f_date2']),
	_("Delivery Date") => sql2date($this->formData['delivery_date']),
);
//---------for left hand side
$customer_information=get_phone_10902($this->formData['debtor_no']);

$cust_info = get_information($this->formData['debtor_no']);
$this->setfontsize(+9);
$this->MultiCell(75, 5, "Customer Name:" , 0, 'L', 0, 2, 48, 65, true);
$this->MultiCell(180, 5, $Addr2['name'] , 0, 'L', 0, 2, 130, 65, true);
$this->MultiCell(45, 5, "Address:" , 0, 'L', 0, 2, 48, 76, true);
$this->MultiCell(215, 10, $Addr1['address'] , 0, 'L', 0, 2, 130, 76, true);
$this->MultiCell(45, 5, "Phone:" , 0, 'L', 0, 2, 48, 100, true);
$this->MultiCell(180, 5,$cust_info['phone'], 0, 'L', 0, 2, 130, 100, true);
$this->MultiCell(45, 5, "Email:" , 0, 'L', 0, 2, 48, 111, true);
$this->MultiCell(180, 5,$cust_info['email'],0, 'L', 0, 2, 130, 111, true);
$this->MultiCell(70, 5, "Contact Person:" , 0, 'L', 0, 2, 48, 122, true);
$this->MultiCell(155, 5, $myrow1['customer_ref'], 0, 'L', 0, 2, 130, 122, true);
$this->MultiCell(45, 5, "Phone:" , 0, 'L', 0, 2, 48, 133, true);
$this->MultiCell(180, 5, $myrow1["contact_phone	"] , 0, 'L', 0, 2, 130, 133, true);
$this->MultiCell(80, 5, "Customer GST No." , 0, 'L', 0, 2, 48, 144, true);
$this->MultiCell(145, 5, $customer_information['tax_id'] , 0, 'L', 0, 2, 130, 144, true);
$this->MultiCell(80, 5, "Customer SST No." , 0, 'L', 0, 2, 48, 155, true);
$this->MultiCell(145, 5, $customer_information['service_text'] , 0, 'L', 0, 2, 130, 155, true);
$this->MultiCell(80, 5, "Customer NTN No." , 0, 'L', 0, 2, 48, 166, true);
$this->MultiCell(145, 5, $customer_information['ntn_no'], 0, 'L', 0, 2, 130, 166, true);
$this->MultiCell(80, 5, "Delivery Address:" , 0, 'L', 0, 2, 48, 177, true);
$this->MultiCell(215, 22, $myrow1['delivery_address'] , 0, 'L', 0, 2, 130, 177, true);
$this->MultiCell(600, 5, "Remarks:               ".$myrow1['comments'] , 0, 'L', 0, 2, 48, 211, true);
//$this->MultiCell(180, 5, "" , 1, 'L', 0, 2, 95, 201, true);

//-----------for right hand side


$this->MultiCell(45, 5, "Date" , 0, 'L', 0, 2, 350, 65, true);
$this->MultiCell(135, 5,sql2date($this->formData['document_date']) , 0, 'L', 0, 2, 435, 65, true);
$this->MultiCell(68, 5, "Sales Order No." , 0, 'L', 0, 2, 350, 79, true);
$this->MultiCell(135, 5, $this->formData['reference'] , 0, 'L', 0, 2, 435, 79, true);
$this->MultiCell(68, 5, "Our GST No." , 0, 'L', 0, 2, 350, 93, true);
$this->MultiCell(135, 5, $this->company['gst_no'] , 0, 'L', 0, 2, 435, 93, true);
$this->MultiCell(68, 5, "Our SST No." , 0, 'L', 0, 2, 350, 107, true);
$this->MultiCell(135, 5, $this->company['sst_no'] , 0, 'L', 0, 2, 435, 107, true);
$this->MultiCell(68, 5, "Our NTN No." , 0, 'L', 0, 2, 350, 121, true);
$this->MultiCell(135, 5,$this->company['coy_no'] , 0, 'L', 0, 2, 435, 121, true);
$this->MultiCell(75, 5, "Terms of Payment" , 0, 'L', 0, 2, 350, 135, true);
$this->MultiCell(135, 5, get_payment_terms_names($this->formData['payment_terms']), 0, 'L', 0, 2, 435, 135, true);
$this->MultiCell(75, 5, "Terms of Delivery" , 0, 'L', 0, 2, 350, 150, true);
$this->MultiCell(135, 5, $myrow1['f_text3'] , 0, 'L', 0, 2, 435, 150, true);
$this->MultiCell(68, 5, "Sales Person" , 0, 'L', 0, 2, 350, 173, true);
$this->MultiCell(135, 5,get_salesman_name($myrow1['salesman']) , 0, 'L', 0, 2, 435, 173, true);
$this->MultiCell(68, 5, "Currency" , 0, 'L', 0, 2, 350, 188, true);
$this->MultiCell(135, 5, $customer_information['curr_code'] , 0, 'L', 0, 2, 435, 188, true);

// Document title
$this->NewLine(-2);
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($ccol , $this->row, $this->pageWidth - $this->rightMargin - $mcol , $this->title, 'left');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

//		$this->Font();
//		$this->row = $adrline;
//		$this->NewLine(3);
//		$this->Text($ccol , _("Date"));
//		$this->Text($ccol +20, sql2date($this->formData['document_date']));
//
//		$this->NewLine();
//		$this->Text($ccol , $this->formData['document_name']);
//		$this->Text($ccol +40, $this->formData['document_number']);
//		$this->NewLine(2);

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
// Payment terms
$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

// Line headers
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
