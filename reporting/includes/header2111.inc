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
$lower = $this->bottomMargin + 6.5* $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 +13 * $this->lineHeight;
$iline6 = $iline5 -2 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin) / 3;
$icol = $this->pageWidth / 2;
$ccol = $this->cols[0] + 4;
$c2col = $ccol + 60;
$ccol2 = $icol / 2;
$mcol = $icol + 8;
$mcol2 = $this->pageWidth - $ccol2;
$cols = count($this->cols);
//		$this->SetDrawColor(205, 205, 205);
//		$this->Line($iline1, 3);
//		$this->SetDrawColor(128, 128, 128);
//		$this->Line($iline1);
//		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//		$this->Line($iline2);
//		$this->Line($iline3);
//		$this->Line($iline4);
//		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
//		for ($i = 0; $i < 3; $i++)
//		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
//		}
//		$this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
    $this->LineTo($this->cols[$cols - 2], $iline5 ,$this->cols[$cols - 2], $iline7);
else
    //for vertical lines in table report
//$this->LineTo($this->cols[$cols - 1] +1 , $iline5 ,$this->cols[$cols - 1] +1, $iline7);

    $this->LineTo($this->cols[$cols - 2] + 1, $iline5 ,$this->cols[$cols - 2] + 1, $iline7);
$this->LineTo($this->cols[$cols - 3] + 1, $iline5 ,$this->cols[$cols - 3] + 1, $iline7);
$this->LineTo($this->cols[$cols - 4] + 1, $iline5 ,$this->cols[$cols - 4] + 1, $iline7);
$this->LineTo($this->cols[$cols - 5] + 1, $iline5 ,$this->cols[$cols - 5] + 1, $iline7);
$this->LineTo($this->cols[$cols - 6] + 1, $iline5 ,$this->cols[$cols - 6] + 1, $iline7);
$this->LineTo($this->cols[$cols - 7] + 1, $iline5 ,$this->cols[$cols - 7] + 1, $iline7);
$this->LineTo($this->cols[$cols - 8] + 1, $iline5 ,$this->cols[$cols - 8] + 1, $iline7);
$this->LineTo($this->cols[$cols - 9] + 1, $iline5 ,$this->cols[$cols - 9] + 1, $iline7);
$this->LineTo($this->cols[$cols - 10] + 1, $iline5 ,$this->cols[$cols - 10] + 1, $iline7);
$this->LineTo($this->cols[$cols - 11] + 1, $iline5 ,$this->cols[$cols - 11] + 1, $iline7);
$this->LineTo($this->cols[$cols - 12] + 1, $iline5 ,$this->cols[$cols - 12] + 1, $iline7);
$this->LineTo($this->cols[$cols - 13] + 1, $iline5 ,$this->cols[$cols - 13] + 1, $iline7);
$this->LineTo($this->cols[$cols - 14] + 1, $iline5 ,$this->cols[$cols - 14] + 1, $iline7);
//$this->LineTo($this->cols[$cols - 15] + 1, $iline5 ,$this->cols[$cols - 15] + 1, $iline7);
$this->LineTo($this->cols[$cols - 16] + 1, $iline5 ,$this->cols[$cols - 16] + 1, $iline7);
//$this->LineTo($this->cols[$cols - 17] + 1, $iline5 ,$this->cols[$cols - 17] + 1, $iline7);
$this->LineTo($this->cols[$cols - 18] + 1, $iline5 ,$this->cols[$cols - 18] + 1, $iline7);
$this->LineTo($this->cols[$cols - 19] + 1, $iline5 ,$this->cols[$cols - 19] + 1, $iline7);
$this->LineTo($this->cols[$cols - 20] + 1, $iline5 ,$this->cols[$cols - 20] + 1, $iline7);
$this->LineTo($this->cols[$cols - 21] - 1, $iline5 ,$this->cols[$cols - 21] - 1, $iline7);
$this->LineTo($this->cols[$cols - 22] - 1, $iline5 ,$this->cols[$cols - 22] - 1, $iline7);

$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//	$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
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
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, "Import Purchase Invoice", 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
//		$adrline = $this->row;

// Company data
//		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
//		$this->Font('italic');
//		if (@$this->company['phone'])
//		{
$this->NewLine(+1);
$this->fontSize += 2;
$this->Text($ccol, _("Supplier's Reference: "), $mcol);
$this->Text($ccol+110, "_________________", $mcol);
$this->Text($ccol+115, $this->formData['supp_reference'], $mcol);
$this->Text($ccol+263, _("Invoice Date :"), $mcol);
$this->Text($mcol-50, "_________________", $ccol);
$this->Text($ccol+340,  sql2date($this->formData['document_date']), $mcol);
$this->Text($ccol+440, _("GD No:"), $c2col);
$this->Text($ccol+480, "_________________", $mcol);
//$this->Text($ccol+580,  sql2date($this->formData['due_date']), $mcol);
$this->Text($ccol+600, _("AWS No:"), $c2col);
$this->Text($ccol+650, "_________________", $mcol);

$this->fontSize -= 2;
//	$this->NewLine();
//		}
//		if (@$this->company['fax'])
//		{
//			$this->Text($ccol, _("Fax"), $c2col);
//			$this->Text($c2col, $this->company['fax'], $mcol);
//			$this->NewLine();
//		}
//		if (@$this->company['email'])
//		{
//			$this->Text($ccol, _("Email"), $c2col);
//
//			$url = "mailto:" . $this->company['email'];
//			$this->SetTextColor(0, 0, 255);
//			$this->Text($c2col, $this->company['email'], $mcol);
//			$this->SetTextColor(0, 0, 0);
//			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);
//
//			$this->NewLine();
//		}
//		if (@$this->company['gst_no'])
//		{
//			$this->Text($ccol, _("Our GST No."), $c2col);
//			$this->Text($c2col, $this->company['gst_no'], $mcol);
//			$this->NewLine();
//		}
//		if (@$this->formData['domicile'])
//		{
//			$this->Text($ccol, _("Province"), $c2col);
//			$this->Text($c2col, $this->company['domicile'], $mcol);
//			$this->NewLine();
//		}
//		$this->Font();
//		$this->row = $adrline;
//		$this->NewLine(3);
//		$this->Text($mcol + 100, _("Date"));
//		$this->Text($mcol + 180, sql2date($this->formData['document_date']));
//
//		$this->NewLine();
//		$this->Text($mcol + 100, "Invoice No");
//		$this->Text($mcol + 180, $this->formData['document_number']);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
    $this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
//		$this->fontSize -= 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
//		$this->fontSize += 4;

// address1
//		$temp = $this->row = $this->row - $this->lineHeight - 5;
//		$this->Text($ccol, $Addr1['name'], $icol);
//		$this->NewLine();
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
//		$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
//		$this->NewLine();
//		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);
//$gst_no=get_po2($this->formData['order_no']);
// Auxiliary document information
//		$col = $this->leftMargin;
//$aux_info = array(
//    _("Supplier's Reference") => $this->formData['supp_reference'],
//    _("NTN") => $this->formData['ntn_no'],
//    _("GST") => $this->formData['tax_id'],
//
//);
//		foreach($aux_info as $info_header => $info_content)
//		{
//
//			$this->row = $iline2 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//			$col += $width;
//		}
// Payment terms
$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();
$this->headers = array(_('S#'), _('Desc'), _('Qty '), _('Unit Price'),  _('Total Price')
, _('Gross'),
    _(''),_('Val.Incl'),  _(''),
    _('Val.Incl'),  _('FED'),  _('Duty'),  _('Val. Incl Duty')
,  _('Val.Excl'),  _('Sales'),
    _('Amt.Incl'),  _('Income'),
    _('Add.'),  _('Total'),_('Other'),_('Net'),_('Unit')
);
$this->headers2 = array(_(''), _(''), _(''), _('before Tax'),_('before Tax')
, _('Am.'), _(''),_(' Land. Am.'),  _(''),
    _('INS Am.'),  _(''),_('Am.'),  _('Am.+FED'),  _('Sales Tax Am.'),
    _('Tax Am.'),
    _('Sales Tax Am.'),  _('tax Am') ,_('Tax'),  _('Charges'),_('Expense'),_('Am.'),_('Cost')
);

// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');

$count = count($this->headers);

$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();


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
