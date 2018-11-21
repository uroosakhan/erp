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

$upper = $this->row - 0.5 * $this->lineHeight;
$lower = $this->bottomMargin + 10 * $this->lineHeight;
$lower8 = $this->bottomMargin + 11.5 * $this->lineHeight;
$lower9 = $this->bottomMargin + 9.5 * $this->lineHeight;
$lower10 = $this->bottomMargin + 8 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 6 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
$iline7 = $lower;
$iline8 = $lower8;
$iline9 = $lower9;
$iline10 = $lower10;
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
//		$this->Line($iline1, 3);
$this->SetDrawColor(128, 128, 128);
//		$this->Line($iline1);

// $this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
// $this->Line($iline2);
// $this->Line($iline3);
// $this->Line($iline4);

$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);

$this->rectangle($this->leftMargin, $iline7, $right - $this->leftMargin, $iline7 - $iline8, "F", null, array(222, 231, 236));
$this->Line($iline8);
$this->rectangle($this->leftMargin, $iline8, $right - $this->leftMargin, $iline8 - $iline9);
$this->Line($iline9);
$this->rectangle($this->leftMargin, $iline9, $right - $this->leftMargin, $iline9 - $iline10, "F", null, array(222, 231, 236));
$this->Line($iline10);
// $this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 5; $i++)
{
// 	$this->LineTo($col += $width, $iline2,$col, $iline4);
}
// $this->LineTo($right, $iline2 ,$right, $iline4);
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline10);
//$this->LineTo($this->cols[$cols - 2] +4, $iline5 ,$this->cols[$cols - 2] +4, $iline10);
$this->LineTo($this->cols[$cols - 3] +2, $iline5 ,$this->cols[$cols - 3] +2, $iline8);
$this->LineTo($this->cols[$cols - 4] -1, $iline5 ,$this->cols[$cols - 4] -1, $iline8);
$this->LineTo($this->cols[$cols - 5] +1, $iline5 ,$this->cols[$cols - 5] +1, $iline8);
$this->LineTo($this->cols[$cols - 6] +2, $iline5 ,$this->cols[$cols - 6] +2, $iline8);
$this->LineTo($this->cols[$cols - 7] -4, $iline5 ,$this->cols[$cols - 7] -4, $iline8);
$this->LineTo($right, $iline5 ,$right, $iline10);

// Company Logo
$this->NewLine(4);
//$text = company_path() . "/images/amac_erp.jpg";
//$this->AddImage($text, 30, $this->row + 50, 540, 35);


$this->Font('bold');
$this->Font();
	$this->NewLine(2);
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			$this->AddImage($logo, $ccol, $this->row, 0, 50);
		}
		else
		{
			$this->fontSize += 4;
			$this->Font('bold');
// 			$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 4;
		}
			$this->NewLine(-1);

$this->SetTextColor(0, 0, 0);

$this->MultiCell(200, 10, 'COMMERCIAL '.$this->title, 0, 'C', 0, 1, 380, 100 , true);


$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->setfontsize(18);
$this->SetTextColor(0, 184, 230);
$this->MultiCell(545, 38, $this->company['coy_name'] ,0, 'L', 0, 0, 190,40, true);
$this->SetTextColor(0, 0, 0);
$this->font('');
$this->setfontsize(9);
$this->MultiCell(150, 35, "Sincerely," ,1, 'L', 0, 0, 40,720, true);
$this->MultiCell(200, 35, "Molecular Biology Products." ,0, 'L', 0, 0, 40,740, true);

$this->MultiCell(270, 20, "Please make all cheques payable to Molecular Biology Products. Thank you very much for your business" ,1, 'L', 0, 0, 40,760, true);
$this->setfontsize(12);
$this->MultiCell(165, 17, "Phone No:". $this->company['phone'] , 0 , 'L', 0, 0, 190,102, true);

$this->MultiCell(808, 38, $this->company['postal_address'] ,0, 'L', 0, 0, 190,70, true);
$this->setfontsize(7);
$this->MultiCell(545, 38, $this->company['legal_text'] ,0, 'C', 0, 0, 38,65, true);

$this->NewLine(2);

$this->row = $adrline;
$this->NewLine(3);
$this->Text($mcol + 165, _("Date"));
$this->Text($mcol + 210, sql2date($this->formData['document_date']));

$this->NewLine(-1.5);
$this->Text($mcol + 155, $this->formData['document_name']);
$this->Text($mcol + 200, $this->formData['document_number']);
$this->NewLine(+1.5);

if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
// address1
// $this->NewLine(- 17);
$temp = $this->row = $this->row - $this->lineHeight - 5;
$this->Text(42, _("Bill To: "), $icol);
$this->fontSize -= 1;
$this->Text(75, $Addr1['name'], $icol);
$this->fontSize += 1;
$this->NewLine();
$this->Text(42, _("Address:"), $icol);
$this->fontSize -= 1;
$this->multicell(400, 40,  $Addr1['address'], 0, 'L', 0, 0, 78, 172);
$this->fontSize += 1;
$this->NewLine(4.5);
$this->Text(42, _("Phone#: "), $icol);
$this->fontSize -= 1;
$this->Text(75, get_phone($this->formData['debtor_no']), $icol);
$this->fontSize += 1;
$this->NewLine();
$this->Text(42, _("Fax #:"), $icol);
$this->fontSize -= 1;
$this->Text(75, get_fax($this->formData['debtor_no']), $icol);
$this->fontSize += 1;
$this->NewLine();
$this->Text(42, _("GST #:"), $icol);
$this->fontSize -= 1;
$this->Text(75, $this->formData['tax_id'], $icol);
$this->fontSize += 1;
$this->NewLine();
$this->Text(42, _("NTN #:"), $icol);
$this->fontSize -= 1;
//$this->Text(75, get_customer_ntn($this->formData['debtor_no']), $icol);
$this->fontSize += 1;
$this->NewLine(-2);
// address2
$this->row = $temp;
$this->Text($mcol,  _("Ship To : "));
$this->fontSize -= 1;
$this->Text(356, $Addr2['name']);
$this->fontSize += 1;
$this->NewLine();

$pattern = "^&amp;^";
$interUrl = preg_replace($pattern, '&',  $Addr2['address']);
$this->Text($mcol ,  _("Address : "));
$this->fontSize -= 1;
$this->multicell(400, 40,  $interUrl, 0, 'L', 0, 0, 358, 172);
$this->fontSize += 1;
$this->NewLine(4.5);
$this->Text($mcol,  _("Contact Person: "));

$this->fontSize -= 1;
$this->Text(365, $Addr2['name']);
$this->fontSize += 1;

$this->NewLine();
$this->Text($mcol,  _("Department : "));
$this->fontSize -= 1;
$this->Text(365, get_depart_deb($this->formData['debtor_no']));
$this->fontSize += 1;
$this->Text(370, '');
$this->NewLine();
$this->Text($mcol, _("Phone No: "));
$this->fontSize -= 1;
$this->Text(365, get_phone($this->formData['debtor_no']));
$this->fontSize += 1;
$this->NewLine();
$this->Text($mcol, _("Email Address: "));
$this->fontSize -= 1;
$this->Text(365, get_email_deb($this->formData['debtor_no']));
$this->fontSize += 1;

// Auxiliary document information
$col = $this->leftMargin;

foreach($aux_info as $info_header => $info_content)
{

// 	$this->row = $iline2 - $this->lineHeight - 1;
// 	$this->TextWrap($col, $this->row, $width, $info_header, 'C');
// 	$this->row = $iline3 - $this->lineHeight - 1;
// 	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
// 	$col += $width;
}
$this->MultiCell(525, 18, "" ,1, 'L', 1, 0, 40,280, true);

$this->MultiCell(110, 18, "" ,1, 'L', 0, 0, 455,137, true);
$this->MultiCell(110, 18, "" ,1, 'L', 0, 0, 455,119, true);

$this->MultiCell(47, 36, "" ,1, 'L', 0, 0, 455,119, true);

// Payment terms

$this->row -= (2 * $this->lineHeight);
// Line headers
	$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
$this->headers = array(_("Qty"), _("Item Description"),
				_("Unit Price"), _("Val Excl. Sales Tax"), _("Sales Tax"),_(""), _("Inc.
Sales Tax"));
		$count = count($this->headers);

        $this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);
		$this->Font();

// Footer
//		$this->Font('italic');
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
	//else
	//	$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
	$this->row -= $this->lineHeight;
}

//		$this->Font('');
$temp = $iline6 - $this->lineHeight - 2;
// $this->MultiCell(100, 18, get_salesman_name($this->formData['salesman']), 0, 'C', 0, 1, 40, 302, true);
// //=============
// $this->MultiCell(525, 18, "", 1, 'L', 0, 1, 40, 748, true);
// //
// $this->MultiCell(180, 36, "", 1, 'L', 0, 1, 40, 730, true);
// $this->MultiCell(180, 18, "Bank/Branch", 0, 'C', 0, 1, 40, 730, true);
// //
// //
// $this->MultiCell(110, 36, "", 1, 'L', 0, 1, 220, 730, true);
// $this->MultiCell(110, 18, "Inst. type", 0, 'C', 0, 1, 220, 730, true);

// $this->MultiCell(70, 36, "", 1, 'L', 0, 1, 330, 730, true);
// $this->MultiCell(70, 18, "Inst. No", 0, 'C', 0, 1, 330, 730, true);

// $this->MultiCell(80, 36, "", 1, 'L', 0, 1, 400, 730, true);
// $this->MultiCell(80, 18, "Inst. Date", 0, 'C', 0, 1, 400, 730, true);


// $this->MultiCell(85, 18, "Amount (Rs)", 0, 'C', 0, 1, 480, 730, true);

//Sales Order

$this->MultiCell(525, 18, "", 1, 'L', 0, 1, 40, 298, true);

$this->MultiCell(170, 36, "", 1, 'L', 0, 1, 40, 280, true);
$this->MultiCell(100, 36, "P.O Number", 0, 'C', 0, 1, 60, 285, true); //
$this->MultiCell(700, 36,$this->formData['f_text10'], 0, 'L', 0, 1, 50, 302, true); //

// $this->MultiCell(105, 36, "", 1, 'L', 0, 1, 145, 280, true); //
$this->MultiCell(110, 36, "Rep", 0, 'C', 0, 0, 240, 285, true); //
//
$this->MultiCell(100, 36, $this->formData['h_text2'], 0, 'C', 0, 1, 220, 302, true); //


// $this->MultiCell(105, 36, "", 1, 'L', 0, 1, 250, 280, true);

// $this->MultiCell(100, 36, "Ship", 0, 'C', 0, 1, 250, 285, true);
// $this->MultiCell(100, 36,  $this->formData['h_text3'], 0, 'C', 0, 1, 250, 302, true);
// if($this->formData['quotation_no']!=0)
// {
// function get_quotation_reference($quotation_no,$type)
// {
// 	$sql = "SELECT reference FROM ".TB_PREF."sales_orders 
// 	WHERE order_no=".db_escape($quotation_no)."
// 	AND type =".db_escape($type);
	

	
// 	$result = db_query($sql,"could not get respone.");
// 	$myrow = db_fetch($result);
// 	return $myrow[0];
// }

// $quotation_reference=get_quotation_reference($this->formData['quotation_no'],32);
// }


//$quote_no = get_sales_order_header($myrow3['quotation_no'],32);

// $this->MultiCell(100, 18,$quotation_reference['reference']."" , 0, 'C', 0, 1, 255, 302, true);

$this->MultiCell(160, 36, "", 1, 'L', 0, 1, 405, 280, true);


// $this->MultiCell(100, 36, "Via", 0, 'C', 0, 1, 355, 285, true);
// $this->MultiCell(100, 18, $this->formData['h_text4'], 0, 'C', 0, 1, 355, 302, true);

$this->MultiCell(100, 36, "P.O Date", 0, 'C', 0, 1, 430, 285, true);


$this->MultiCell(100, 18, "".sql2date($this->formData['po_date']), 0, 'C', 0, 1, 420, 302, true);

$this->MultiCell(263,120, "" ,1, 'L', 0, 0, 40,155, true);
$this->MultiCell(262.5, 120, "" ,1, 'L', 0, 0, 302.5,155, true);

?>
