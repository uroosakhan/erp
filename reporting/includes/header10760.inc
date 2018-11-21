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
$iline2 = $iline1 - 12 * $this->lineHeight;
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
// $this->Line($iline1, 3);
$this->SetDrawColor(128, 128, 128);
// $this->Line($iline1);
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
$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine(4);
$logo = company_path() . "/images/" . $this->company['coy_logo'];
global $db_connections;
// if($db_connections[$_SESSION["wa_current_user"]->company]["name"]!='AHN')
// {
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row,$this->company['logo_w'], $this->company['logo_h']);
}
else
{
	$this->fontSize += 4;
	$this->Font('bold');
	$this->Text($ccol, $this->company['coy_name'], $icol);
	$this->Font();
	$this->fontSize -= 4;
}
// }
// else
// {
// 	$this->fontSize += 4;
// 	$this->Font('bold');
// 	$this->Text($ccol, $this->company['coy_name'], $icol);
// 	$this->Font();
// 	$this->fontSize -= 4;
// }
// Document title
$this->MultiCell(515, 10, "Sale Tax Invoice", 0, 'C', 0, 2, 47,90, true);
$this->MultiCell(515, 10, "".$this->company['postal_address']."", 0, 'C', 0, 2, 47,107, true);

//$this->SetTextColor(190, 190, 190);
$this->fontSize += 7;
$this->Font('bold');
// $this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 100, "Sale Tax Invoice", 'centre');
$this->Font();
$this->fontSize -= 7;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->NewLine();

$this->TextWrapLines($ccol, $icol, $this->company['pogjstal_address']);
$this->Font('italic');
$this->NewLine();
if (@$this->company['phone'])
{
	$this->Text($ccol, _("Phone"), $c2col);
	$this->Text($c2col, $this->company['phone'], $mcol);
// 	$this->NewLine();
}
// $this->NewLine(-2);
$this->NewLine();

if (@$this->company['fax'])
{
	$this->Text($ccol, _("Fax"), $c2col);
	$this->Text($c2col, $this->company['fax'], $mcol);
// 	$this->NewLine();
}
// $this->NewLine();
if (@$this->company['email'])
{
	$this->Text($ccol, _("Email"), $c2col);

	$url = "mailto:" . $this->company['email'];
	$this->SetTextColor(0, 0, 255);
	$this->Text($c2col, $this->company['email'], $mcol);
	$this->SetTextColor(0, 0, 0);
	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

	$this->NewLine();
}
if (@$this->company['gst_no'])
{
	$this->Text($ccol, _("Our GST No."), $c2col);
		$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
	
	$this->NewLine();
}
	$this->Text($ccol, _("Our NTN No."), $c2col);
			$this->Text($c2col, $this->company['coy_no'], $mcol);

if (@$this->formData['domicile'])
{
	$this->Text($ccol, _("Province"), $c2col);
	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}


$this->Font();
$this->row = $adrline;
$this->NewLine(4);
$this->Text($mcol + 130, _("Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));

$this->NewLine(1);
$this->Text($mcol + 130, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['document_number']);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 4;
$this->NewLine(+4);
$this->NewLine();
$this->Text($ccol, $Addr1['title'], $icol);
// $this->Text($mcol+45, $Addr2['title']);
$this->NewLine(-4);
$this->fontSize -= 4;

// address1

$temp = $this->row = $this->row - $this->lineHeight - 5;


$this->NewLine();
$this->Text($ccol, $Addr1['nameui'], $icol);
$this->MultiCell(250, 10, $this->formData['DebtorName'], 0, 'L', 0, 2, 47,230, true);
$this->MultiCell(250, 10, $this->formData['contact_phone'], 0, 'L', 0, 2, 47,280, true);
$this->MultiCell(350, 10, "".$Addr1['address'], 0, 'L', 0, 2, 47,240, true);

$this->MultiCell(1000, 10, "_________________________________________________________________________________________________________________", 0, 'L', 0, 2, 47,190, true);

if($this->formData['tax_id'] != 0 || $this->formData['ntn_no'] != 0 || $this->formData['service_text'] != 0)
{
$this->MultiCell(200, 10, "STRN       : ".$this->formData['tax_id'], 0, 'L', 0, 2, 47,260, true);
$this->MultiCell(200, 10, "NTN No    : ".$this->formData['ntn_no'], 0, 'L', 0, 2, 47,270, true);
}

$this->NewLine();
// $this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);


// address2
$this->row = $temp;
//$this->Text($mcol, $Addr2['name']);
$this->NewLine();
//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

// Auxiliary document information
// $this->NewLine();
$col = $this->leftMargin;

$aux_info = array(
	_("Customer References") => $this->formData["customer_ref"],
	_("Sales Person") => get_salesman_name($this->formData['salesman']),
	_("Sale Order No.") => $this->formData['order_'],
	_("Delivery Note No.") => implode(',', $deliveries),
	_("Due Date") => sql2date($this->formData['due_date']),
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
$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

// Line headers
$this->headers = array(_("Item Code"), _("Item Description"),
    _("Cartons"), _("Pcs"), _(""), _("Price/units"), _("Discount"), _("Total"));
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
?>
