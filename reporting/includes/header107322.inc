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
$lower = $this->bottomMargin +15 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;

//BOX TOP
$iline5 = $iline4 + 5 * $this->lineHeight;



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

//TOP LINE
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



//for ($i = 0; $i < 5; $i++)
//{
//    $this->LineTo($col += $width, $iline2,$col, $iline4);
//}
//$this->LineTo($right, $iline2 ,$right, $iline4);


$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
$this->LineTo($this->cols[$cols - 3] + 4, $iline5 ,$this->cols[$cols - 3] + 4, $iline7);
$this->LineTo($this->cols[$cols - 4] - 3, $iline5 ,$this->cols[$cols - 4] - 3, $iline7);	//indent price
$this->LineTo($this->cols[$cols - 5] - 0, $iline5 ,$this->cols[$cols - 5] - 0, $iline7);	//unit
$this->LineTo($this->cols[$cols - 6] -0, $iline5 ,$this->cols[$cols - 6] - 0, $iline7);	//quantity
$this->LineTo($this->cols[$cols - 7] - 0, $iline5 ,$this->cols[$cols - 7] - 0, $iline7); //particulars
$this->LineTo($this->cols[$cols - 8] - 3, $iline5 ,$this->cols[$cols - 8] -3, $iline7);//description
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine(5);


//TOP COMPANY ANME
//$this->fontSize += 1;
if (@$this->company['coy_name']) {
    $this->Font('bold');
    // $this->MultiCell(100, 10, " " . $this->company['coy_name'], 0, 'C', 0, 2, 250, 20, true);
    $this->Font('');
//$this->fontSize -= 1;
}





//TOP COMPANY ADDRESS

if (@$this->company['postal_address']) {
    $this->Font('bold');
    $this->MultiCell(400, 20,$this->company['postal_address'], 0, 'L', 0, 2, 150, 40, true);
    $this->Font('');
}

//TOP COMPANY EMAIL
if (@$this->company['phone']) {
    $this->Font('bold');
    // $this->MultiCell(600, 10, "PTCL:" . $this->company['phone'] . "  " . $this->company['email'], 0, 'C', 0, 2, 60, 55, true);
    $this->Font('');

}




$logo = company_path() . "/images/" . $this->company['coy_logo'];

if ($this->company['coy_logo'] != '' && file_exists($logo))
{

    $this->AddImage($logo, $ccol, $this->row, 0, 95);
}

else
{


//$this->fontSize += 10;
//    $this->MultiCell(100, 18,"COMPANY NAME", 0, 'L', 0, 2, 80,160, true);
//    $this->fontSize += 1;

   // $this->Text($ccol +100, htmlspecialchars_decode($this->company['coy_name']), $icol);
}


//DATE


//$this->Text($ccol +430, "   ".$this->formData['document_date'], $icol);

//NAME
$this->fontSize += 1;
$this->Text($ccol , "COMPANY NAME", $icol);
$this->NewLine();
$this->Text($ccol , "NAME", $icol);
//$this->Text($ccol +100, '', $icol);
$this->Text($ccol +329 ,"DATE", $icol);

//INVOICE NO

//$this->Text($ccol +430,"  ".$this->formData['document_number'], $icol);



//ADDRESS
$this->NewLine();
$this->Text($ccol , "ADDRESS", $icol);
// $this->Text($ccol +100, '', $icol);
$this->Text($ccol +329 ,"INVOICE NO", $icol);

//SALES MAN

//$this->Text($ccol +430, '', $icol);


//PHONE

if (@$this->company['phone']) {
    $this->NewLine(2);
    $this->Text($ccol, "PHONE NO", $icol);
    // $this->Text($ccol + 100, $this->company['phone'], $icol);

}





//SHIPPER
$this->NewLine();
$this->Text($ccol , "AREA", $icol);
$this->Text($ccol +100, '', $icol);
$this->NewLine(-2);
$this->Text($ccol +329 ,"SALES MAN", $icol);
$this->NewLine();
$this->Text($ccol +329 ,"SHIPPER", $icol);
//$this->Text($ccol +430, '', $icol);



//AREA




//SHIPPER
$this->NewLine();
$this->Text($ccol +329 ,"BUILTY #", $icol);
//$this->Text($ccol +430, $this->formData['h_text2'], $icol);
$this->fontSize -= 1;


//$this->fontSize += 4;
//$this->Font('bold');
//$this->Text($ccol, $this->company['coy_name'], $icol);
//
//
//$this->Font();
//$this->fontSize -= 4;

// Document title

$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin  - $mcol - 20, '', 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
//$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
//$this->Font('italic');
if (@$this->company['phone'])
{
//    $this->Text($ccol, _("Phone"), $c2col);
//    $this->Text($c2col, $this->company['phone'], $mcol);
//    $this->NewLine();
}





if (@$this->company['fax'])
{
//    $this->Text($ccol, _("Fax"), $c2col);
//    $this->Text($c2col, $this->company['fax'], $mcol);
//    $this->NewLine();
}

if (@$this->company['email'])
{
//    $this->Text($ccol, _("Email"), $c2col);
//
//    $url = "mailto:" . $this->company['email'];
//    $this->SetTextColor(0, 0, 255);
//    $this->Text($c2col, $this->company['email'], $mcol);
//    $this->SetTextColor(0, 0, 0);
//    $this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

    $this->NewLine();
}
if (@$this->company['gst_no'])
{
//    $this->Text($ccol, _("Our GST No."), $c2col);
//    $this->Text($c2col, $this->company['gst_no'], $mcol);
//    $this->NewLine();
}




if (@$this->formData['domicile'])
{
//    $this->Text($ccol, _("Domicile"), $c2col);
//    $this->Text($c2col, $this->company['domicile'], $mcol);
//    $this->NewLine();
}

//$this->Font();
//$this->row = $adrline;
//$this->NewLine();
//$this->Text($mcol + 100, _("Date"));
//$this->Text($mcol + 180, sql2date($this->formData['document_date']));
//
//$this->NewLine();
//$this->Text($mcol + 100, $this->formData['document_name']);
//$this->Text($mcol + 180, $this->formData['document_number']);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
    $this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
//$this->row = $iline1 - $this->lineHeight;
//$this->fontSize -= 4;
//$this->Text($ccol, $Addr1['title'], $icol);
//	$this->Text($mcol, $Addr2['title']);
//$this->fontSize += 4;







// address1
//$temp = $this->row = $this->row - $this->lineHeight - 5;
//$this->Text($ccol, $Addr1['name'], $icol);
//$this->NewLine();
//$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);



// address2
//$this->row = $temp;
//	$this->Text($mcol, $Addr2['name']);
//$this->NewLine();
//	$this->TextWrapLines($mcol, $this->rightMargin - $mcol,
// $Addr2['address']);

// Auxiliary document information
$col = $this->leftMargin;

$aux_info = array(
    // _("Bilty Number") => $this->formData["customer_ref"],
    // _("Shipping Company") => get_shippers_name_rep($this->formData['ship_via']),
    // _("S/Rep") => get_salesman_name($this->formData['salesman']),
    // _("Delivery Note No.") => $this->formData["reference"],
    // _("Due Date") => sql2date($this->formData['due_date']),
);





foreach($aux_info as $info_header => $info_content)
{

//    $this->row = $iline2 - $this->lineHeight - 1;
//    $this->TextWrap($col, $this->row, $width, $info_header, 'C');
//    $this->row = $iline3 - $this->lineHeight - 1;
//    $this->TextWrap($col, $this->row, $width, $info_content, 'C');
//    $col += $width;
}
// Payment terms
//$this->row -= (2 * $this->lineHeight);
//$this->Font('italic');
//	$this->TextWrap($ccol, $this->row, $right-$ccol, '');
//$this->Font();


// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$this->headers = array(_("S.No"), _("ITEM DESCRIPTION"), _("UNIT"),
    _("PACK"), _("TRADE PRICE"), _("DISCOUNT"), _("AMOUNT"), _("NET PRICE"));
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
//        $this->fontSize -= 2;
        $this->TextWrap($ccol, $this->row, $right - $ccol, $line, 'C');
        $this->row -= $this->lineHeight;
        $this->SetTextColor(0, 0, 255);
        $this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->addLink($txt, $ccol, $this->row, $this->pageWidth - $this->rightMargin, $this->row + $this->lineHeight);
//        $this->fontSize += 2;
    }
    else
        //		$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
        $this->row -= $this->lineHeight;

}


$this->Font();
$temp = $iline6 - $this->lineHeight - 2;

//$rep->TextColLines(1, 2, $myrow2['description'], -2);
//$rep->TextCol(0, 1,	$myrow2["quantity"], -2);
?>
