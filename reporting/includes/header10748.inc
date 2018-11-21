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
$lower = $this->bottomMargin + 30 * $this->lineHeight;
$iline1 = $upper - 11.5 * $this->lineHeight;
$iline2 = $iline1 - 6.3 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 7 * $this->lineHeight;
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
//$this->SetDrawColor(205, 205, 205);
//$this->Line($iline1, 3);
//$this->SetDrawColor(128, 128, 128);
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
//$this->LineTo($this->cols[$cols - 2] + 100, $iline5 ,$this->cols[$cols - 2] +100, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);
$this->font('bold');
//$this->multicell(300,100,"",1,'C',0,0,40,70);
//$this->multicell(300,20,"CUSTOMER",1,'C',0,0,40,70);
//$this->multicell(120,20,"Shipment Through:",0,'L',0,0,40,530);

//$this->multicell(100,20,"Bilty No.:",0,'L',0,0,40,550);
$this->font('');
//$this->multicell(160,20,$this->formData['f_text1'],1,'L',0,0,150,550);
//$this->Font('bold');
//$this->multicell(100,20,"Carton:",1,'L',0,0,350,550);
//$this->font('');
//$this->multicell(160,20,$this->formData['f_text2'],1,'L',0,0,400,550);
//$this->Font('bold');
//$this->multicell(100,20,"Remarks:",0,'L',0,0,40,570);

//$this->multicell(100,20,"Prepared By:",0,'L',0,0,40,650);

//$this->multicell(100,20,"Approved By:",0,'L',0,0,300,650);
//$this->Font('');
//$this->multicell(180,20,"",1,'L',0,0,380,650);
$this->Font('bold');
//$this->multicell(60,20,"E. & O.E.",0,'L',0,0,40,790);

$this->Font('');
//$this->multicell(525,70,"",1,'C',0,0,40,451);
//$this->Font('b');
//$this->multicell(100,20,"Gross Total:",0,'C',0,0,100,451);
//$this->multicell(100,20,"Balance After:",0,'L',0,0,390,488);
//$this->Font('');
$this->setfontsize(9);
//$this->multicell(200,10,implode(',', $deliveries),0,'L',0,0,424,130);
$this->Font('bold');
//$this->multicell(200,10,"Supplier Address: ".$this->company['postal_address'],0,'L',0,0,344,180);

$this->Font();
// Company Logo
$this->NewLine(2);
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol+200, $this->row, $this->company['logo_w'], $this->company['logo_h']);
}
else
{
	$this->fontSize += 4;
	$this->Font('bold');
	$this->Text($ccol+200, $this->company['coy_name'], $icol);
	$this->Font();
	$this->fontSize -= 4;
}
$this->NewLine(-1);
// Document title
$this->SetTextColor(0, 0, 0);
$this->fontSize += 10;
//$this->Font('bold');
$this->NewLine(+4);
$this->TextWrap($ccol+70, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 10, "PACKING LIST", 'right');
//$this->Font();
$this->fontSize -= 10;
$this->NewLine(-4);
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
//$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
//$this->Font('italic');
if (@$this->company['phone'])
{
//	$this->Text($ccol, _("Phone"), $c2col);
//	$this->Text($c2col, $this->company['phone'], $mcol);
	$this->NewLine();
}
if (@$this->company['fax'])
{
//	$this->Text($ccol, _("Fax"), $c2col);
//	$this->Text($c2col, $this->company['fax'], $mcol);
	$this->NewLine();
}
if (@$this->company['email'])
{
//	$this->Text($ccol, _("Email"), $c2col);
//
//	$url = "mailto:" . $this->company['email'];
//	$this->SetTextColor(0, 0, 255);
//	$this->Text($c2col, $this->company['email'], $mcol);
//	$this->SetTextColor(0, 0, 0);
//	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

	$this->NewLine();
}
if (@$this->company['gst_no'])
{
	$this->NewLine(7);
//	$this->Text($ccol-8, _("GST No."), $c2col);
//	$this->Text($c2col, $this->company['gst_no'], $mcol);
	$this->NewLine();
//	$this->Text($ccol-8, _("NTN No."), $c2col);
//	$this->Text($c2col, $this->company['coy_no'], $mcol);
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
$this->NewLine(+5.5);
$this->font('b');
$this->Text($mcol - 260, $this->formData['document_name']);
$this->font('');
$this->Text($mcol - 210, $this->formData['trans_no']);
$this->NewLine(-5.5);
//
$this->NewLine();
$this->font('b');
$this->NewLine(4.5);
$this->Text($mcol + 38, _("Date:"));
$this->font('');
$this->Text($mcol + 118, sql2date($this->formData['document_date']));


$this->font('b');
//$this->NewLine(+1);
//$this->Text($mcol + 38, _("Ref No:"));
$this->font('');
//$this->Text($mcol + 118, $this->formData['document_number']);

//$this->NewLine();
//$this->Text($mcol + 38, _("Prev. Balance:"));
//$this->Text($mcol + 110, ($this->formData['document_date']));

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
//$this->NewLine();
//$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
$this->row = $temp;
//$this->Text($mcol, $Addr2['name']);
$this->NewLine();
//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

// Auxiliary document information
$col = $this->leftMargin;
//$aux_info = array(
//	_("Sales Order No") => $this->formData["customer_ref"],
//	_("Sales Person") => get_salesman_name($this->formData['salesman']),
//	_("Your GST no.") => $this->formData['tax_id'],
//	_("Delivery Note No.") => implode(',', $deliveries),
//	_("Due Date") => sql2date($this->formData['due_date']),
//);

foreach($aux_info as $info_header => $info_content)
{

//	$this->row = $iline2 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//	$this->row = $iline3 - $this->lineHeight - 1;
//	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//	$col += $width;
}
// Payment terms
//$this->multicell(300,20,$Addr1['name'] ,0,'L',0,0,40,70);
//
//$this->multicell(300,19,$Addr1['address'],0,'L',0,0,40,138);
//$this->Font('bold');
$this->multicell(800,19,"".$this->company['postal_address'],0,'L',0,0,160,60);
//
//$this->multicell(200,19, $this->company['postal_address'],0,'L',0,0,40,85);
//$this->row -= (2 * $this->lineHeight);
//$this->Font('italic');
//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
//$this->Font();
//
//// Line headers
//$this->row = $iline5 - $this->lineHeight - 1;
//$this->Font('bold');
//$this->multicell(525,25,"" ,1,'L',0,0,40,80);
$this->multicell(800,20,"TERMS                                  :".$Payment_Terms ,0,'L',0,0,45,119);

$this->multicell(300,20,"FROM                                    :".$Addr1['address'] ,0,'L',0,0,45,129);
$this->multicell(300,20,"Manufacturer and Exporter    :".$this->company['coy_name'] ,0,'L',0,0,45,138);
$this->multicell(300,20,"TO                                          :".$Addr2['address'] ,0,'L',0,0,45,150);
$this->multicell(300,20,"APPLICANT                           :" ,0,'L',0,0,45,160);
$this->multicell(300,20,"IMPORTER & BUYER ADDRESS :".$Addr2['address'] ,0,'L',0,0,45,170);
$this->multicell(300,20,"Consignement #                      :" ,0,'L',0,0,45,180);
$this->multicell(300,20,"Form E #                                 :" ,0,'L',0,0,45,190);
$this->multicell(300,20,"Gross Weight                          :" ,0,'L',0,0,45,200);
$this->multicell(300,20,"Net Weight                              :" ,0,'L',0,0,45,210);
$this->multicell(100,20,"FOR: ". $this->company['coy_name'],0,'L',0,0,50,460);
$this->multicell(800,20,"Certified that above mention goods are of Pakistan	",0,'L',0,0,220,460);

$this->headers = array(_("SR.NO"), _("PARTICULARS"), _("PACKING STYLE"),
	_("QUANTITY"), _("CTN #"), _("PALLET QTY"), _("N.W(KG)"));

//$this->headers = array(_("Art#"), _("Art#"),
//	_("Name"), _(""), _(""),_(""), _("SKU"), _("Price/SKU"), _("Amount"), _("17%"), _("1%"), _("Total"));
////


$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();

//$count = count($this->headers1);
//$this->cols[$count] = $right - 3;
//for ($i = 0; $i < $count; $i++)
//	$this->TextCol($i, $i + 1, $this->headers1[$i], -2);
$this->Font();
// Footer
$this->Font('italic');
$this->row = $iline7 - $this->lineHeight - 6;
/*
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
*/
$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>
