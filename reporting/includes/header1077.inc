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
$iline5 = $iline4 - 3 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin*2) / 2;
$icol = $this->pageWidth / 2;
$ccol = $this->cols[0] + 4;
$c2col = $ccol + 60;
$ccol2 = $icol / 2;
$mcol = $icol + 8;
$mcol2 = $this->pageWidth - $ccol2;
$cols = count($this->cols);
$this->SetDrawColor(205, 205, 205);
$this->rectangle($this->leftMargin*2, $iline2-20, $right - $this->leftMargin*2, $iline2 - $iline3, "F", null, array(220, 231, 236));
$this->rectangle($this->leftMargin*2, $iline5, $right - $this->leftMargin*2, $iline5 - $iline6, "F", null, array(220, 231, 236));
$col = $this->leftMargin*2;

for ($i = 0; $i < 5; $i++)
{
    $this->LineTo($col += $width, $iline2-20,$col, $iline4-20);
}
//$this->LineTo($right, $iline2 ,$right, $iline4);
//$this->LineTo($this->leftMargin*2, $iline5 ,$this->leftMargin*2, $iline7);//bary box ki left
$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7+23);      //bary box inside line
//$this->LineTo($right, $iline5 ,$right, $iline7); //bary box right line

// Company Logo
$this->NewLine(1);
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
    $this->AddImage($logo, $ccol, $this->row, 0, 40);
    $this->NewLine(-1);
}
else
{
    $this->fontSize += 4;
    $this->Font('bold');
    //$this->Text($ccol, $this->company['coy_name'], $icol);
    $this->Font();
    $this->fontSize -= 4;
}
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->NewLine(3);
$this->TextWrap($mcol-200, $this->row, $this->pageWidth - $this->rightMargin - $mcol + 130, $this->title, 'right');
$this->NewLine(-3);
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;
/*
		// Company data
     	$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		$this->Font('italic');
		if (@$this->company['phone'])
		{
			$this->Text($ccol, _("Phone"), $c2col);
			$this->Text($c2col, $this->company['phone'], $mcol);
			$this->NewLine();
		}
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
$this->NewLine(5);
$this->Text($mcol + 100, _("Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));
$this->formData['document_name'] =_("Invoice No.");
$this->NewLine();
$this->Text($mcol + 100, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['document_number']);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
    $this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 2;
$this->NewLine(1);
$this->Text($ccol+10, $Addr1['title'], $icol);
// 		$this->Text($mcol+20, $Addr2['title']);
$this->fontSize -= 2;

// address1
$temp = $this->row = $this->row - $this->lineHeight - 5;
$this->Text($ccol+10,  $this->formData['DebtorName'], $icol);
$this->NewLine();
$this->TextWrapLines($ccol+10, $icol - $ccol, $Addr1['address']);
// 		$this->TextWrapLines($ccol+10, $icol - $ccol, $this->formData['debtor_ref']);
//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr1['phone']);

// address2
// 		$this->row = $temp;
// 		$this->Text($mcol+20, $Addr2['name']);
// 		$this->NewLine();
// 		$this->TextWrapLines($mcol+20, $this->rightMargin - $mcol+20, $Addr2['address']);
// 		$this->TextWrapLines($mcol+20, $this->rightMargin - $mcol+20, $this->formData['debtor_ref']);

$this->NewLine(-1);
// Auxiliary document information
$col = $this->leftMargin*2;
$deliveries = get_sales_parent_numbers(ST_SALESINVOICE, $this->formData['trans_no']);
foreach($deliveries as $n => $delivery) {
    $deliveries[$n] = get_reference(ST_CUSTDELIVERY, $delivery);
}
$aux_info = array(
    _("Customer's Reference") => $this->formData["customer_ref"],
    //_("Your GST no.") => $this->formData['branch_ref'],
    _("Delivery Note No.") => implode(',', $deliveries),
);
foreach($aux_info as $info_header => $info_content)
{

    $this->row = $iline2-20 - $this->lineHeight - 1;
    $this->TextWrap($col, $this->row, $width, $info_header, 'C');
    $this->row = $iline3-20 - $this->lineHeight - 1;
    $this->TextWrap($col, $this->row, $width, $info_content, 'C');
    $col += $width;
}
// Payment terms
$this->NewLine(+.2);
//	$this->row -= (1 * $this->lineHeight);
$this->Font('italic');
//	$this->TextWrap($ccol*1, $this->row, $right-$ccol, $Payment_Terms);
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
$this->MultiCell(486,19.8, "" , 1, 'L', 0, 2, 79,269, true);
$this->MultiCell(486,17, "" , 1, 'L', 0, 2, 79,289, true);

//for big box
$this->MultiCell(487, 372, "" , 1, 'L', 0, 2, 79,321, true);
$this->MultiCell(487, 20, "" , 1, 'L', 0, 2, 79,321, true);
$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
