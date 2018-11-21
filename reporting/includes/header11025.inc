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

$upper = $this->row + 2 * $this->lineHeight;
$lower = $this->bottomMargin + 15 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 3 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin) / 4;
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
for ($i = 0; $i < 4; $i++)
{
//	$this->LineTo($col += $width, $iline2,$col, $iline4);
}
//$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols -3] +35, $iline5 ,$this->cols[$cols - 3] +35, $iline7);
$this->LineTo($this->cols[$cols -1] -65, $iline5 ,$this->cols[$cols - 1] -65, $iline7);
$this->LineTo($this->cols[$cols - 4] -2, $iline5 ,$this->cols[$cols - 4] -2, $iline7);
$this->LineTo($this->cols[$cols - 5] -2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
    $this->NewLine(+4);
 	$this->AddImage($logo, $ccol, $this->row, 0, 40);
 	$this->NewLine(-4);
$this->setfontsize(18);
$this->SetTextColor(0, 184, 230);
$this->MultiCell(545, 38, $this->company['coy_name'] ,0, 'L', 0, 0, 177,40, true);
$this->SetTextColor(0, 0, 0);
}
else
{
	$this->fontSize += 4;
	$this->Font('bold');
 	$this->Text($ccol+130, $this->company['coy_name']);
	$this->Font();
	$this->fontSize -= 4;
}
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 15, "Delivery Challan", 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->NewLine(+1);
 $this->TextWrapLines($ccol+130, $icol, $this->company['postal_address']);
 $this->Font('italic');
 if (@$this->company['phone'])
 {
 	$this->Text($ccol+130, _("Phone"), $c2col);
 	$this->Text($c2col+150, $this->company['phone']);
 	$this->NewLine();
 }
 if (@$this->company['fax'])
 {
 	$this->Text($ccol+130, _("Fax"), $c2col);
 	$this->Text($c2col+150, $this->company['fax']);
 	$this->NewLine();
 }
 if (@$this->company['email'])
 {
 	$this->Text($ccol+130, _("Email"), $c2col);

 	$url = "mailto:" . $this->company['email'];
 	$this->SetTextColor(0, 0, 255);
 	$this->Text($c2col+150, $this->company['email']);
 	$this->SetTextColor(0, 0, 0);
 	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

 	$this->NewLine();
 }
 if (@$this->company['gst_no'])
 {
 	$this->Text($ccol+130, _("Our GST No."), $c2col);
 		$this->Text($c2col+150, $this->company['gst_no']);
 			$this->NewLine();
 		$this->Text($ccol, _("Our NTN No."), $c2col);
 			$this->Text($c2col+150, $this->company['coy_no']);

 	$this->NewLine();
 }
 if (@$this->formData['domicile'])
 {
 	$this->Text($ccol+130, _("Province"), $c2col);
 	$this->Text($c2col+150, $this->company['domicile']);
 	$this->NewLine();
 }
$this->NewLine(-1);
$this->Font();
$this->row = $adrline;
$this->NewLine(3);
//$this->Text($mcol + 100, _("Date"));
//$this->Text($mcol + 180, sql2date($this->formData['document_date']));
$reference = get_dn_no_11025($this->formData['trans_no']);
$this->NewLine();
//$this->Text($mcol + 100, "No.");
//$this->Text($mcol + 180, $reference);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 1;
	$this->Font('bold');
//$this->Text($ccol, "To,", $icol);
	$this->Font('');
// $this->Text($mcol, $Addr2['title']);
$this->fontSize -= 1;

// address1

$temp = $this->row = $this->row - $this->lineHeight - 5;
// $salesorder = get_sales_order_header($this->formData['order_']);

// $this->Text($ccol, $this->formData['deliver_to'], $icol);
$this->MultiCell(250, 10, "".$this->formData['delivery_address']."", 0, 'L', 0, 2, 60,179, true);
$this->MultiCell(250, 10, "".$this->formData['name']."", 0, 'L', 0, 2, 60,165, true);
$this->MultiCell(220, 20, "", 1, 'L', 0, 2, 47,140, true);
$this->MultiCell(175, 20, "", 1, 'L', 0, 2, 390,200, true);

$this->MultiCell(220, 20, "Ship To", 0, 'L', 0, 2, 55,145, true);
$this->MultiCell(220, 70, "", 1, 'L', 0, 2, 47,163, true);


$this->MultiCell(150, 20, "Date", 0, 'L', 0, 2, 400,144, true);
$this->MultiCell(150, 20, sql2date($this->formData['document_date']), 0, 'L', 0, 2, 465,144, true);

$this->MultiCell(145, 20, "P.O No", 0, 'L', 0, 2, 400,185, true);
$this->MultiCell(145, 20, "".$this->formData['f_text10'], 0, 'L', 0, 2, 465,185, true);

$this->MultiCell(145, 20, "P.O Date", 0, 'L', 0, 2, 400,205, true);
$this->MultiCell(145, 20, "".sql2date($this->formData['po_date']), 0, 'L', 0, 2, 465,205, true);

$this->MultiCell(60, 80, "", 1, 'L', 0, 2, 390,140, true);


$this->MultiCell(145, 20, "Invoice #", 0, 'L', 0, 2, 400,165, true);
$this->MultiCell(145, 20, "".$reference, 0, 'L', 0, 2, 465,165, true);
$this->MultiCell(175, 20, "", 1, 'L', 0, 2, 390,140, true);
$this->MultiCell(175, 20, "", 1, 'L', 0, 2, 390,160, true);
$this->MultiCell(175, 20, "", 1, 'L', 0, 2, 390,180, true);


// $this->MultiCell(80, 20, "Ship", 1, 'C', 0, 2, 47,239, true);
// $this->MultiCell(80, 20, "".$this->formData['h_text3'], 1, 'C', 0, 2, 47,263, true);
// $this->MultiCell(80, 20, "Via", 1, 'C', 0, 2, 140,239, true);
// $this->MultiCell(80, 20, "".$this->formData['h_text4'], 1, 'C', 0, 2, 140,263, true);



// $this->MultiCell(80, 20, "F.O.B", 1, 'C', 0, 2, 390,239, true);
// $this->MultiCell(80, 20, "".$this->formData['h_text5'], 1, 'C', 0, 2, 390,263, true);
// $this->MultiCell(75, 20, "Project", 1, 'C', 0, 2, 470,239, true);
// $this->MultiCell(75, 20, "".$this->formData['h_text6'], 1, 'C', 0, 2, 470,263, true);



$this->MultiCell(100, 30, "Delivered By:", 1, 'L', 0, 2, 70,640, true);
$this->MultiCell(100, 30, "Name:".$this->formData['f_text1'], 1, 'L', 0, 2, 189,640, true);
$this->MultiCell(100, 30, "Designation:".$this->formData['f_text2'], 1, 'L', 0, 2, 309,640, true);
$this->MultiCell(100, 30, "Contact:".$this->formData['f_text3'], 1, 'L', 0, 2, 429,640, true);


$this->MultiCell(100, 30, "Received by:", 1, 'L', 0, 2, 70,673, true);
$this->MultiCell(100, 30, "Name:".$this->formData['f_text4'], 1, 'L', 0, 2, 189,673, true);
$this->MultiCell(100, 30, "Designation:".$this->formData['f_text5'], 1, 'L', 0, 2, 309,673, true);
$this->MultiCell(100, 30, "Contact:".$this->formData['f_text6'], 1, 'L', 0, 2, 429,673, true);


// $this->NewLine();
// $this->Text($ccol, $Addr1['name'], $icol);
// $this->NewLine();
// $this->TextWrapLines($ccol-200,  $Addr1['address']);
// $phone = get_customer_number_11025($this->formData['debtor_no']);
$this->Font('bold');
//$this->Text($ccol, "Contact No.", $icol);
$this->Font('');
//$this->Text($mcol - 200,  $phone);

// address2
$this->row = $temp;
// $this->Text($mcol, $Addr2['name']);
// $this->NewLine();
// $this->TextWrapLines($mcol-200,  $Addr2['add,ress']);


// Auxiliary document information
$col = $this->leftMargin;
	$ref = get_reference(ST_SALESORDER, $this->formData['order_']);
$aux_info = array(
	_("Sales Order No") => $ref,
	_("Sales Person") => get_salesman_name($this->formData['salesman']),
	_("Delivery Note No.") => $this->formData['reference'],
	_("Due Date") => sql2date($this->formData['due_date']),
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
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right - 3;
	$this->headers = array(_('Qty'), _('Part No'), _('Item Description'), _('Lot No'), _('Remarks'));
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
