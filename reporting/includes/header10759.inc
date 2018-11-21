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

$upper = $this->row - 6 * $this->lineHeight;
$lower = $this->bottomMargin + 6 * $this->lineHeight;
$iline1 = $upper - 2.5 * $this->lineHeight;
$iline2 = $iline1 + 10 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 3 * $this->lineHeight;
$iline6 = $iline5 - 1.9 * $this->lineHeight;
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
//$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
//$this->NewLine(-1);

for ($i = 0; $i < 5; $i++)
{
//	$this->LineTo($col += $width, $iline2,$col, $iline4);
}

$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 2] + 75, $iline5 ,$this->cols[$cols - 2] + 75 , $iline7);
$this->LineTo($this->cols[$cols - 2] + 34, $iline5 ,$this->cols[$cols - 2] + 34, $iline7);
$this->LineTo($this->cols[$cols - 2] +120, $iline5 ,$this->cols[$cols - 2] +120, $iline7);
$this->LineTo($this->cols[$cols - 2] - 2, $iline5 ,$this->cols[$cols - 2] - 2, $iline7);
$this->LineTo($this->cols[$cols - 2] - 182, $iline5 ,$this->cols[$cols - 2] - 182, $iline7);
$this->LineTo($this->cols[$cols - 2] - 208, $iline5 ,$this->cols[$cols - 2] - 208, $iline7);


// Company Logo
$this->NewLine(-1);
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
else
{
	$this->fontSize += 10;
	$this->Font('bold');
	$this->Text($ccol+80, $this->company['coy_name']);
	$this->Font();
	$this->fontSize -= 10;
}
$this->NewLine(+3);
$this->Font('bold');
$this->fontSize += 10;
$this->SetTextColor(163, 163, 163);
$this->Text($mcol + 115, _("Invoice"));
$this->SetTextColor(0, 0, 0);
$this->fontSize -= 10;
$this->Font();
$this->NewLine(-3);

$this->NewLine(+1);

// Document title
$this->NewLine(-1);

$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
// $this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
// $this->NewLine(1);
$this->fontSize += 2;
$this->TextWrapLines($ccol+60, $icol, $this->company['postal_address']);
	$this->Text($ccol+120, _("Phone:"), $c2col);
	$this->Text($c2col+100, $this->company['phone']);
$this->fontSize -= 2;

$this->Font('italic');

// if (@$this->company['phone'])
// {
//     $this->NewLine(1);
// 	$this->Text($ccol, _("Phone"), $c2col);
// 	$this->Text($c2col, $this->company['phone'], $mcol);
// 	$this->NewLine();
// }
/*
if (@$this->company['fax'])
{
	$this->Text($ccol, _("Fax"), $c2col);
	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
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
}
if (@$this->formData['domicile'])
{
	$this->Text($ccol, _("Province"), $c2col);
	$this->Text($c2col, $this->company['domicile'], $mcol);
	$this->NewLine();
}*/



$this->Font();
$this->row = $adrline;
$this->NewLine(3);
$this->Text($mcol + 60, _("Date :"));
$this->Text($mcol + 126, sql2date($this->formData['document_date']));

$this->NewLine();
$this->font(b);
$this->fontSize += 2;
$this->Text($mcol + 35, _("Invoice No"));
$this->Text($mcol + 90, $this->formData['document_number']);
$this->fontSize -= 2;

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize -= 4;
//$this->Text($ccol, $Addr1['title'], $icol);
//$this->Text($mcol, $Addr2['title']);
$this->fontSize += 4;

// address1
$this->NewLine(-8);
$this->fontSize += 3;
$temp = $this->row = $this->row - $this->lineHeight - 5;
$this->Text($mcol-188 , _("Customer :"));
$this->Text($ccol +49 , $Addr1['name'], $icol);
$this->NewLine();


$this->NewLine();
$this->fontSize -= 3;
//$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
$this->NewLine(+8);

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
$this->headers = array(_("S.No"), _("Qty"), _("Description"),
	_("Units"), _("Price"), _(""), _("Total"));
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
