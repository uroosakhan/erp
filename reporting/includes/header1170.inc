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
$iline1 = $upper - 10.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 2 * $this->lineHeight;
$iline6 = $iline5 - 2.5 * $this->lineHeight;
$iline21 = $upper - 2 * $this->lineHeight;
$iline22 = $upper -3.5 * $this->lineHeight;
$iline23 = $upper - 10.5 * $this->lineHeight;
$iline24 = $upper -12 * $this->lineHeight;
$iline25 = $upper - 14 * $this->lineHeight;
$iline7 =  $upper - 42.5 * $this->lineHeight;
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
$this->setfontsize(+9);
//$this->MultiCell(100, 5, "Version 1.0" , 0, 'L', 0, 2, 40,45, true);
//$this->MultiCell(100, 5, "Doc #: ST-FR-23" , 0, 'L', 0, 2, 40,58, true);
$this->SetDrawColor(205, 205, 205);
$this->SetDrawColor(128, 128, 128);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
// $this->Line($iline5);
// $this->Line($iline6);
// $this->Line($iline7);
$col = $this->leftMargin;
$this->Font('');
$this->SetFontSize(10);
// $this->MultiCell(400,10,$this->company['postal_address'],0,'L', 0, 2,40,30,true);

// $this->MultiCell(200,10,"Phone :".$this->company['phone'],0,'L', 0, 2,40,75,true);
// $this->MultiCell(200,10,"S.T#  :".$this->company['gst_no'],0,'L', 0, 2,40,45,true);
// $this->MultiCell(200,10,"N.T.N :".$this->company['coy_no'],0,'L', 0, 2,40,60,true);
// $this->MultiCell(200,10,"Fax   : ".$this->company['fax'],0,'L', 0, 2,40,88,true);
// $this->MultiCell(200,10,"Email : ".$this->company['email'],0,'L', 0, 2,40,100,true);



// $this->MultiCell(200,10,"GST. ".$this->company['gst_no'],0,'L', 0, 2,45,30,true);
//  $this->MultiCell(200,70,$Addr1['address'],0,'L', 0, 2,60,40,true);
//  $this->MultiCell(200,70,"Party Details :",0,'L', 0, 2,60,150,true);
//  $this->MultiCell(200,70,"Party GST No :".$this->company['tax_id'],0,'L', 0, 2,60,230,true);
// //$this->MultiCell(200,10,$this->formData['ntn_id'],0,'L', 0, 2,461,196,true);
// //$this->MultiCell(200,10,$this->formData['customer_ref'],0,'L', 0, 2,458,180,true);


$this->Font('b');

$this->NewLine(+2);
$this->Text($mcol + 110, _("Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));
$this->NewLine();

$this->Text($mcol + 110, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['document_number']);


//$this->MultiCell(110,10,$this->company['coy_name'],0,'L', 0, 2,264,64,true);
$this->SetFontSize(7);

$this->SetFontSize(15);
// $this->MultiCell(200,10,$this->company['coy_name'],0,'L', 0, 2,40,10,true);

// $this->MultiCell(200,20,"SALES TAX INVOICE",0,'L', 0, 2,240,70,true);
$this->SetFontSize(-15);
$this->SetFontSize(7);
//$this->MultiCell(142,10,"Duplicate",1,'L', 0, 2,423,72,true);
//$this->MultiCell(142,10,"Original",1,'L', 0, 2,423,57,true);
$this->SetFontSize(10);
// $this->MultiCell(800,10,"Receiver's Signature:",0,'L', 0, 2,320,650,true);
$this->MultiCell(800,10,"Authorised Signatory",0,'L', 0, 2,60,650,true);

$this->SetFontSize(-10);
$this->SetFontSize(7);
// $this->MultiCell(110,10,"_________________________",0,'L', 0, 2,400,650,true);
//$this->MultiCell(110,10,"Terms Of Sale:",0,'L', 0, 2,40,236,true);
// $this->MultiCell(110,10,"Invoice No.",0,'L', 0, 2,400,158,true);
$this->MultiCell(110,10,$Addr1['name'],0,'L', 0, 2,50,148,true);
$this->MultiCell(110,10,$Addr1['address'],0,'L', 0, 2,50,165,true);

$this->MultiCell(110,10,$Addr2['name'],0,'L', 0, 2,350,148,true);
$this->MultiCell(110,10,$Addr2['address'],0,'L', 0, 2,350,165,true);

// $this->MultiCell(525 ,140 ,"",1,'L', 0, 2,40,574,true);
// $this->MultiCell(270 ,80 ,"",1,'L', 0, 2,40,634,true);
// $this->MultiCell(255 ,40 ,"",1,'L', 0, 2,310,634,true);



//$this->MultiCell(110,10,"Supplier's Name & Address",0,'L', 0, 2,41,110,true);
//$this->MultiCell(110,10,"Buyer's Name & Address",0,'L', 0, 2,351,110,true);
//$this->MultiCell(110,10,$Addr1['name'],0,'L', 0, 2,461,110,true);
//$this->MultiCell(110,10,"Telephone No.",0,'L', 0, 2,41,196,true);
//$this->MultiCell(110,10,"NTN",0,'L', 0, 2,351,196,true);
//$this->MultiCell(150,10,"S T Reg. No. (if any)",0,'L', 0, 2,351,219,true);
//$this->MultiCell(120,10,"S T Registration No.",0,'', 0, 2,41,219,true);
//$this->LineTo($right, $iline2 ,$right, $iline4);
// $this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//		$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] -2, $iline7);

/////
// $this->LineTo($this->cols[$cols - 2] +25, $iline5 ,$this->cols[$cols - 2] + 25, $iline7);
// $this->LineTo($this->cols[$cols - 3] - 30, $iline5 ,$this->cols[$cols - 3] - 30 , $iline7);
// $this->LineTo($this->cols[$cols - 3] -250, $iline5 ,$this->cols[$cols - 3] -250, $iline7);
// $this->LineTo($this->cols[$cols - 4] -110, $iline5 ,$this->cols[$cols - 4] -110, $iline7);
// $this->LineTo($this->cols[$cols - 5] -350, $iline5 ,$this->cols[$cols - 5] -350, $iline7);
// $this->LineTo($this->cols[$cols - 6] -90, $iline5 ,$this->cols[$cols - 6] -90, $iline7);
// $this->LineTo($this->cols[$cols - 7] - 215, $iline5 ,$this->cols[$cols - 7] - 215, $iline7);
///
//		$this->LineTo($this->cols[$cols - 2] -420, $iline5 ,$this->cols[$cols - 2] -420, $iline7);
$this->MultiCell(270,80,"",0,'L', 0, 2,40,120,true);
$this->MultiCell(270,20,"Bill To",0,'L', 1, 2,40,120,true);

$this->MultiCell(235,80,"",0,'L', 0, 2,325,120,true);
$this->MultiCell(235,20,"Ship To",0,'L', 1, 2,325,120,true);


$this->MultiCell(520,40,"",0,'L', 0, 2,40,210,true);
$this->MultiCell(520,20,"",0,'L', 1, 2,40,210,true);


$this->MultiCell(520,20,"",0,'L', 1, 2,40,250,true);
$this->MultiCell(520,20,"",0,'L', 0, 2,40,270,true);

$this->MultiCell(60,20,"Customer ID",0,'C', 0, 2,80,215,true);
$this->MultiCell(60,20,"".$this->formData['debtor_no'],0,'C', 0, 2,80,235,true);
$this->MultiCell(140,40,"",0,'L', 0, 2,40,210,true);


$this->MultiCell(60,20,"Customer PO",0,'C', 0, 2,220,215,true);
$this->MultiCell(60,20,"".$this->formData['po_no'],0,'C', 0, 2,220,235,true);

$this->MultiCell(140,40,"",0,'L', 0, 2,180,210,true);



$this->MultiCell(60,20,"Contact Person",0,'C', 0, 2,80,254,true);
$this->MultiCell(140,40,"",0,'L', 0, 2,40,250,true);
$this->MultiCell(60,20,"".$this->formData['h_text3'],0,'C', 0, 2,80,275,true);



$this->MultiCell(100,20,"Delivery Challan No",0,'C', 0, 2,200,254,true);
$this->MultiCell(140,40,"",0,'L', 0, 2,180,250,true);



$this->MultiCell(100,20,"Delivery Date",0,'C', 0, 2,330,254,true);
$this->MultiCell(120,40,"",0,'L', 0, 2,320,250,true);
// $this->MultiCell(100,20, "".$this->formData['delivery_date'],0,'C', 0, 2,340,275,true);

$this->MultiCell(100,20,"Due Date",0,'C', 0, 2,440,254,true);
$this->MultiCell(100,20,"".sql2date($this->formData['due_date']),0,'C', 0, 2,440,275,true);

$this->MultiCell(60,20,"Payment Term",0,'C', 0, 2,405,215,true);
$this->MultiCell(140,40,"",0,'L', 0, 2,180,210,true);


// $this->LineTo($right, $iline5 ,$right, $iline7);
//$this->LineTo($right, $iline21 ,$right, $iline25);
//$this->LineTo($this->cols[$cols - 6] +228, $iline21 ,$this->cols[$cols - 6] +228, $iline25);
//$this->LineTo($this->cols[$cols -6] +120, $iline21 ,$this->cols[$cols -6] +120, $iline25);
//$this->LineTo($this->cols[$cols -9] +156, $iline21 ,$this->cols[$cols -9] +156, $iline25);
//$this->LineTo($this->cols[$cols -9] +100, $iline21 ,$this->cols[$cols -9] +100, $iline22);
//$this->LineTo($this->cols[$cols -9] +223, $iline21 ,$this->cols[$cols -9] +223, $iline22);
//$this->LineTo($right, $iline21 ,$right, $iline25);

// Company Logo
$this->NewLine();
//$logo = company_path() . "/images/" . $this->company['coy_logo'];
//if ($this->company['coy_logo'] != '' && file_exists($logo))
//{
//	$this->Text($ccol+185, "Flavaro Pakistan");
//}
//else
{
	$this->NewLine(1.3);
	$this->fontSize += 10;
	$this->Font('bold');
// 	$this->Text($ccol+185, "Flavaro Pakistan");
	$this->Font();
	$this->fontSize -=10;
}

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
		$Payment_Terms_ = _("Terms of Sales") . ': ' . $row["terms"];
	}
	$this->MultiCell(200,20,"".$Payment_Terms_,0,'C', 0, 2,320,235,true);

//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms_);
$this->Font();
$this->headers = array(_("Qty"), _("Item"), _("Description."), _("Unit Price"),
 _("Amount(Rs.)"));
//$this->headers2 = array(_(""), _(""), _(""), _("Sales Tax"), _("S.Tax"), _("Sales Tax"), _("Sales Tax"));
// Line headers1
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();
// Line headers2
$this->row = $iline5 - $this->lineHeight - 10;
$this->Font('bold');
//$count = count($this->headers2);
$this->cols[$count] = $right - 3;
// for ($i = 0; $i < $count; $i++)
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
