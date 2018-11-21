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
$lower = $this->bottomMargin + 17 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 0 * $this->lineHeight;
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
//$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
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
}

	$this->fontSize += 12;
	$this->Font('bold');
	$this->Text($ccol + 120, $this->company['coy_name'], $icol+180);
	$this->Font();
	$this->fontSize -= 12;

// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');

//$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine(9);
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->Text($ccol, _("Client:"), $c2col);
$this->TextWrapLines($ccol +40, $icol, $Addr1['name']);
$this->NewLine();
$this->Text($ccol, _("Name:"), $c2col);
$this->TextWrapLines($ccol +40, $icol, $this->formData['DebtorName']);
$this->Text($ccol, _("Address:"), $c2col);
//AA
$cust = get_customer_info( $this->formData['debtor_no']);
$customer=get_customer($this->formData['debtor_no']);
$this->TextWrapLines($ccol +40, $icol, $customer['address']);
//$this->NewLine();
$this->Text($ccol, _("City:"), $c2col);
$this->TextWrapLines($ccol +40, $icol, "");
$this->Font('italic');
//if (@$this->company['phone'])
//{
	$this->Text($ccol, _("Phone"), $c2col);
	$this->Text($ccol+40, $cust['phone'], $mcol);
	$this->NewLine();
//}
//$this->Text($ccol, _("State:"), $c2col);
//$this->TextWrapLines($ccol +40, $icol, "");
//$this->Text($ccol, _("Postal Code:"), $c2col);
//$this->TextWrapLines($ccol +60, $icol, "");

if (@$this->company['fax'])
{
//	$this->Text($ccol, _("Fax"), $c2col);
//	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
if (@$this->company['email'])
{
//	$this->Text($ccol, _("Email"), $c2col);

//	$url = "mailto:" . $this->company['email'];
	$this->SetTextColor(0, 0, 255);
//	$this->Text($c2col, $this->company['email'], $mcol);
	$this->SetTextColor(0, 0, 0);
//	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

	$this->NewLine();
}
if (@$this->company['gst_no'])
{
//	$this->Text($ccol, _("Our GST No."), $c2col);
//	$this->Text($c2col, $this->company['gst_no'], $mcol);
	$this->NewLine();
}
if (@$this->formData['domicile'])
{
//	$this->Text($ccol, _("Province"), $c2col);
//	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}
$this->Font();
$this->row = $adrline;
$this->NewLine(-5);
$so=get_sales_order_header($this->formData['order_'],30);
//$this->Text($mcol + 100, _("Supply No"));
//$this->Text($mcol + 180,$so['h_text2']);

$this->NewLine(3);
//$this->Text($mcol + 100, _("Misc."));
//$this->Text($mcol + 180, "");
$this->NewLine(3);
$this->Text($mcol + 130, _("Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));
$this->NewLine(1.5);
//$this->Text($mcol + 100, _("Code #"));
//$this->Text($mcol + 180, $so['h_text3']);
$this->NewLine(1.5);
//$this->Text($mcol + 100, _("Vendor #"));
//$this->Text($mcol + 180, "");

$this->NewLine(-12);
$this->font('b');
$this->NewLine(4);
$this->fontSize += 14;
$this->Text($mcol - 80 , "Sales Invoice");
$this->fontSize -= 14;
$this->NewLine(-4);
$this->NewLine(8);
$this->Text($mcol + 130, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['document_number']);
$this->NewLine(-8);
$this->font('');
if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize -= 4;
//$this->Text($ccol, $Addr1['title'], $icol);
//$this->Text($mcol, $Addr2['title']);
$this->fontSize += 4;

// address1
$temp = $this->row = $this->row - $this->lineHeight - 5;
//$this->Text($ccol, $Addr1['name'], $icol);
$this->NewLine();
//$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
$this->row = $temp;
//$this->Text($mcol, $Addr2['name']);
$this->NewLine();
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
$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$pref=get_company_prefs();
if($pref['batch'] == 1) {
	$this->headers = array(_("Sr #"), _("Description"),/*_("Lot"),_("Expiry Date"),*/
		_("Batch"),_("Exp.Date"),	_("Qty"), _("Unit Rate"), _("Amount (PKR)"));
}
else{
	$this->headers = array(_("Sr #"), _("Description"),/*_("Lot"),_("Expiry Date"),*/
		_("Qty"), _("Unit Rate"), _("Amount (PKR)"));
}
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
//		$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
	$this->row -= $this->lineHeight;

}

$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
