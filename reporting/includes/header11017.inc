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
$lower = $this->bottomMargin + 29 * $this->lineHeight;
$iline1 = $upper - 0.5 * $this->lineHeight;
$iline2 = $iline1 - 3 * $this->lineHeight;
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
$this->SetDrawColor(61, 172, 227);
//$this->Line($iline1, 3);
$this->SetDrawColor(0,0,0);
//$this->Line($iline1);
//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//$this->Line($iline2);
//$this->Line($iline3);
//$this->Line($iline4);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(61, 172, 227));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;

$this->SetTextColor(0, 0, 0);
for ($i = 0; $i < 5; $i++)
{
//	$this->LineTo($col += $width, $iline2,$col, $iline4);
}
//$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

$this->Font('bold');
$this->fontSize -= 20;
//51, 204, 255
$this->MultiCell(825, 30, "UNIVERSAL CO." ,0, 'L', 0, 2, 60,112, true);//S.no
$this->MultiCell(825, 30, "TERMS  ".$Payment_Terms1 ,0, 'L', 0, 2, 45,723, true);//S.no
$this->MultiCell(825, 30, "INVOICE" ,0, 'L', 0, 2, 405,90, true);//S.no

$this->SetTextColor(61, 172, 227);
//$this->MultiCell(825, 30, "".$this->company['coy_name'] ,0, 'L', 0, 2, 85,620, true);//S.no
$this->MultiCell(825, 30, "Thank You For Your Business !" ,0, 'L', 0, 2, 45,700, true);//S.no
$this->MultiCell(825, 30, "___________________________ " ,0, 'L', 0, 2, 45,707, true);//S.no
$this->MultiCell(825, 30, "INVOICE TO" ,0, 'L', 0, 2, 80,90, true);//S.no
$this->MultiCell(825, 30, "_________________________________" ,0, 'L', 0, 2, 405,95, true);//S.no
$this->fontSize += 20;
// Company Logo
$this->NewLine();
$map = company_path() . "/images/" .'map.jpg';
$this->AddImage($map, 255, 775, 0, 12);
$company = company_path() . "/images/" .'company.png';
$this->AddImage($company, 365, 800, 0, 12);
$contact = company_path() . "/images/" .'contact.png';
$this->AddImage($contact, 255, 800, 0, 12);
$invoice_to = company_path() . "/images/" .'invoice_to.jpg';
$this->AddImage($invoice_to, 60, 737, 0, 15);
$phone = company_path() . "/images/" .'phone.png';
$this->AddImage($phone, 485, 800, 0, 17);
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
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
//$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 80, $this->title, 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->TextWrapLines($ccol+226, $icol, $this->company['postal_address']);
//$this->Font('italic');
if (@$this->company['phone'])
{
	$this->NewLine(-3);
	$this->Text($ccol, _(""), $c2col);
	$this->Text($c2col+390, $this->company['phone'], $mcol);
	$this->NewLine(+3);
}
if (@$this->company['fax'])
{
	$this->Text($ccol, _("Fax"), $c2col);
	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
if (@$this->company['email'])
{
//	$this->Text($ccol, _("Email"), $c2col);
	$this->NewLine(-3);
//	$url = "mailto:" . $this->company['email'];
	$this->SetTextColor(0, 0, 255);
//	$this->Text($c2col+160, $this->company['email'], $mcol);
	$this->SetTextColor(0, 0, 0);
	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

	$this->NewLine(+3);
}
if (@$this->company['gst_no'])
{
	$this->Text($ccol, _("Our GST No."), $c2col);
		$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
		$this->Text($ccol, _("Our NTN No."), $c2col);
			$this->Text($c2col, $this->company['coy_no'], $mcol);

	$this->NewLine();
}
if (@$this->formData['domicile'])
{
	$this->Text($ccol, _("Province"), $c2col);
	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}
$this->Font();
$this->row = $adrline;
$this->NewLine(5);
$this->Text($mcol + 100, _("Invoice Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));

$this->NewLine();
$this->Text($mcol + 100, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['document_number']);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 4;
//$this->Text($ccol, $Addr1['title'], $icol);
//$this->Text($mcol, $Addr2['title']);
$this->fontSize -= 4;

// address1
$this->NewLine(+4);
$temp = $this->row = $this->row - $this->lineHeight - 5;
$this->Text($ccol+13, $Addr1['name'], $icol);
$this->NewLine();
$this->TextWrapLines($ccol+13, $icol - $ccol, $Addr1['address']);
$this->NewLine(-4);

// address2
$this->row = $temp;
//$this->Text($mcol, $Addr2['name']);
$this->NewLine();
//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

// Auxiliary document information
$col = $this->leftMargin;
$aux_info = array(
//	_("Sales Order No") => $this->formData["customer_ref"],
//	_("Sales Person") => get_salesman_name($this->formData['salesman']),
//	_("Your GST no.") => $this->formData['tax_id'],
//	_("Delivery Note No.") => implode(',', $deliveries),
//	_("Due Date") => sql2date($this->formData['due_date']),
);

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

//255, 255, 255
$this->Font('bold');
$this->SetTextColor(255, 255, 255);
$this->headers = array(_("Ref"), _("Item Description"),
	 _("Quantity"), _("Unit"),  _("Price"), _("Total"));
$this->row = $iline5 - $this->lineHeight - 1;

$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();
//0, 0, 255
$this->SetTextColor(0, 0, 0);
// Footer
//$this->Font('italic');
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

$this->MultiCell(825, 30, "".$this->company['email'] ,0, 'L', 0, 2, 270,30, true);//S.no
$this->MultiCell(825, 30, "www.company.com" ,0, 'L', 0, 2, 390,30, true);//S.no

$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
