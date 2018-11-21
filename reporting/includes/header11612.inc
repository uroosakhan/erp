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
$iline5 = $iline4 - 4 * $this->lineHeight;
$iline6 = $iline5 - 2 * $this->lineHeight;
$iline7 =  $upper - 45 * $this->lineHeight;
$iline8 =  $upper - 20* $this->lineHeight;
$iline9 =  $upper - 22.5* $this->lineHeight;
$iline10 =  $upper - 25* $this->lineHeight;
$iline11 =  $upper - 27.5* $this->lineHeight;
$iline12 =  $upper - 30* $this->lineHeight;
$iline13 =  $upper - 32.5* $this->lineHeight;
$iline14 =  $upper - 35* $this->lineHeight;
$iline15 =  $upper - 37.5* $this->lineHeight;
$iline16 =  $upper - 40* $this->lineHeight;
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
$this->SetDrawColor(128, 128, 128);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
$col = $this->leftMargin;
$this->Font('');
$this->LineTo($this->leftMargin + 1, $iline5 ,$this->leftMargin, $iline7);
$this->LineTo($this->cols[$cols - 2] + 2, $iline5 ,$this->cols[$cols - 2] + 2, $iline7);
$this->LineTo($this->cols[$cols - 3] + 2, $iline5 ,$this->cols[$cols - 3] + 2, $iline7);
$this->LineTo($this->cols[$cols - 4] + 2, $iline5 ,$this->cols[$cols - 4] + 2, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

$this->SetFontSize(16);
$this->Font('b');
$this->MultiCell(250,30,"Sales Tax Invoice",0,'L', 0, 2,250,30,true);
$this->MultiCell(250,30,"_______________",0,'L', 0, 2,249,32,true);
$this->Font('');
$this->SetFontSize(-16);

$this->Font('b');
$this->SetFontSize(18);
$this->MultiCell(350,30,$this->company['coy_name'],0,'L', 0, 2,160,55,true);
$this->MultiCell(350,30,"______________________________",0,'L', 0, 2,160,56,true);
$this->SetFontSize(-18);
$this->Font('');

$this->SetFontSize(9);

$this->MultiCell(350,30,$this->company['postal_address'],0,'L', 0, 2,160,80,true);
$this->MultiCell(450,30,"Phone: Lahore: ".$this->company['phone'].", Vehri: 067-3362065 E-mail: ".$this->company['email'],0,'L', 0, 2,160,102,true);

$this->SetFontSize(9);

$this->Font('bold');
$this->MultiCell(184,15,"National Tax No. ".$this->company['coy_no'],1,'C', 0, 2,160,120,true);
$this->MultiCell(167,15,"Sales Tax No. ".$this->company['gst_no'],1,'C', 0, 2,400,120,true);
$this->Font('');

$this->Font('bold');
$this->MultiCell(300,15,"Dealer's Information",1,'C', 0, 2,45,140,true);
$this->MultiCell(300,58,$Addr1['name'],1,'L', 0, 2,45,155,true);
$this->Font('');
$this->MultiCell(300,20,$Addr1['address'],0,'L', 0, 2,45,167,true);
$this->MultiCell(300,12,"NTN No."."          ".$this->formData['ntn_no'],0,'L', 0, 2,45,190,true);
$this->MultiCell(300,12,"CNIC No."."          ".$this->formData['service_text'],0,'L', 0, 2,45,202,true);


$this->Font('bold');
$this->MultiCell(67,20,"Invoice No.",1,'C', 0, 2,400,140,true);
$this->MultiCell(100,20,$this->formData['document_number'],1,'C', 0, 2,467,140,true);
$this->MultiCell(67,20,"Invoice Date",1,'C', 0, 2,400,160,true);
$this->Font('');
$this->MultiCell(100,20,sql2date($this->formData['document_date']),1,'C', 0, 2,467,160,true);

$this->Font('bold');
$this->MultiCell(80,20,"Supply Point",1,'C', 0, 2,45,220,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['h_text5'],1,'C', 0, 2,45,240,true);
$this->Font('bold');
$this->MultiCell(80,20,"Despatch Date",1,'C', 0, 2,125,220,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['tran_date'],1,'C', 0, 2,125,240,true);
$this->Font('bold');
$this->MultiCell(80,20,"Order No.",1,'C', 0, 2,205,220,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['order_'],1,'C', 0, 2,205,240,true);
$this->Font('bold');
$this->MultiCell(80,20,"Tank Lorry",1,'C', 0, 2,285,220,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['h_text6'],1,'C', 0, 2,285,240,true);
$this->Font('bold');
$this->MultiCell(100,20,"HAULER",1,'C', 0, 2,365,220,true);
$this->Font('');
$this->MultiCell(100,25,get_ship_via_laurry($this->formData['ship_via']),1,'C', 0, 2,365,240,true);
$this->Font('bold');
$this->MultiCell(100,20,"Destination Depot",1,'C', 0, 2,465,220,true);
$this->Font('');
$this->MultiCell(100,25,$this->formData['f_text1'],1,'C', 0, 2,465,240,true);

$this->Font('bold');
$this->MultiCell(80,20,"Chamber-1",1,'C', 0, 2,45,275,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['h_text1'],1,'C', 0, 2,45,295,true);
$this->Font('bold');
$this->MultiCell(80,20,"Chamber-2",1,'C', 0, 2,125,275,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['h_text2'],1,'C', 0, 2,125,295,true);
$this->Font('bold');
$this->MultiCell(80,20,"Chamber-3",1,'C', 0, 2,205,275,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['h_text3'],1,'C', 0, 2,205,295,true);
$this->Font('bold');
$this->MultiCell(80,20,"Chamber-4",1,'C', 0, 2,285,275,true);
$this->Font('');
$this->MultiCell(80,25,$this->formData['h_text4'],1,'C', 0, 2,285,295,true);

$this->MultiCell(315,25," "."Acknowledge that the Goods are recevied in the right quality and quantity.",1,'L', 0, 2,42,680,true);
$this->MultiCell(315,25," "."Drivers's Name:",1,'L', 0, 2,42,705,true);
$this->MultiCell(315,25," "."Driver's Signature:",1,'L', 0, 2,42,730,true);
$this->MultiCell(315,25," "."Recepients Signature:",1,'L', 0, 2,42,755,true);

$this->MultiCell(145,100,"",1,'C', 0, 2,422,680,true);
$this->Font('bold');
$this->MultiCell(145,25,$this->company['coy_name'],0,'C', 0, 2,422,680,true);
$this->Font('');
$this->MultiCell(145,75,"",0,'C', 0, 2,422,705,true);

// Company Logo
$this->NewLine(7);
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
    $this->AddImage($logo, $ccol + 20, $this->row, 0, 80);
    $this->NewLine();
}
else
{
    $this->fontSize += 4;
    $this->Font('bold');
    $this->Text($ccol, $this->company['coy_name'], $icol);
    $this->NewLine();
    $this->Font();
    $this->fontSize -= 4;
}

$this->Font();
if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
    $this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize -= 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
$this->fontSize += 4;


// Auxiliary document information
$col = $this->leftMargin;
// Payment terms
$this->row -= (7 * $this->lineHeight);
$this->Font('italic');
//if (!isset($Payment_Terms))
{
    $id = $this->formData['payment_terms'];
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);
    $result = db_query($sql,"could not get paymentterms");
    $row = db_fetch($result);
}
$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms_);
$this->Font();
$this->headers = array(_("Description"), _("  Quantity"), _("Rate"), _("Amount"));
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
