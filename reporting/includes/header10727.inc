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
		$lower = $this->bottomMargin + 13 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 - 8 * $this->lineHeight;
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
	//	$this->Line($iline1, 3);
		$this->SetDrawColor(128, 128, 128);
	//	$this->Line($iline1);
	//	$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
	//	$this->Line($iline2);
	//$this->Line($iline3);
		//$this->Line($iline4);
//	$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
		$this->Line($iline5);
		$this->Line($iline6);
//		$this->Line($iline7);
		//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
		$col = $this->leftMargin;
		for ($i = 0; $i < 5; $i++)
		{
			//$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
		//$this->LineTo($right, $iline2 ,$right, $iline4);
		//$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
	//	$this->LineTo($this->cols[$cols - 1] - 2 , $iline5 ,$this->cols[$cols - 1] - 2, $iline7);
		//$this->LineTo($this->cols[$cols - 2] -10, $iline5 ,$this->cols[$cols - 2] -10, $iline7);
		//$this->LineTo($this->cols[$cols - 3] - 2, $iline5 ,$this->cols[$cols - 3] - 2, $iline7);
		//$this->LineTo($this->cols[$cols - 4] -2, $iline5 ,$this->cols[$cols - 4] - 2, $iline7);
		//$this->LineTo($this->cols[$cols - 5] - 2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
	//	$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine();
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
		//	$this->AddImage($logo, $ccol, $this->row, 0, 40);
		}
		else
		{
			$this->fontSize += 10;
			$this->Font('bold');
		//	$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 10;
		}
		// Document title
		//$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
		$this->NewLine(+4);
		$this->TextWrap($mcol-150, $this->row, $this->pageWidth - $this->rightMargin - $mcol , _("Performa Invoice"), 'center');
		$this->Font();
		$this->NewLine(-4);
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
	//	$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		$this->Font('');
		if (@$this->company['phone'])
		{
		//	$this->Text($ccol, _("Phone"), $c2col);
		//	$this->Text($c2col-30, $this->company['phone'], $mcol);
			$this->NewLine();
		}
		if (@$this->company['fax'])
		{
		//	$this->Text($ccol, _("Fax"), $c2col);
//$this->Text($c2col, $this->company['fax'], $mcol);
			$this->NewLine();
		}
		if (@$this->company['email'])
		{
		//	$this->Text($ccol, _("Email"), $c2col);

			$url = "mailto:" . $this->company['email'];
			$this->SetTextColor(0, 0, 255);
		//	$this->Text($c2col-30, $this->company['email'], $mcol);
		//	$this->SetTextColor(0, 0, 0);
		//	$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this//->lineHeight);

			$this->NewLine();
		}
		if (@$this->company['coy_no'])
		{
		//	$this->Text($ccol, _("NTN."), $c2col);
		//	$this->Text($c2col, $this->company['coy_no'], $mcol);
			$this->NewLine();
		}
if (@$this->company['gst_no'])
{
//	$this->Text($ccol, _("S.tax Reg No."), $c2col);
//	$this->Text($c2col, $this->company['gst_no'], $mcol);
	$this->NewLine();
}
		if (@$this->formData['domicile'])
		{
			$this->Text($ccol, _("Province"), $c2col);
		//	$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
		$this->Font('');
		$this->row = $adrline;
$this->NewLine(12);
//$this->Text($mcol + 100, $this->formData['document_name']);
//$this->Text($mcol + 180, $this->formData['trans_no']);
$this->NewLine(-1);
//$this->Text($mcol + 100, _("Date"));
//$this->Text($mcol + 180, sql2date($this->formData['document_date']));
$this->NewLine(+1);


//$this->Text($mcol + 100, "Performa Invoice No.:");
//$this->Text($mcol + 190, $this->formData['document_number']);

$this->NewLine();

//$this->Text($mcol + 100, _("Date"));
//$this->Text($mcol + 190, sql2date($this->formData['document_date']));
//$this->NewLine();


//$this->Text($mcol + 100, _("Customer Ref:"));
//$this->Text($mcol + 190, $this->formData['customer_ref']);
$this->NewLine();
//$this->Text($mcol + 100, _("Cust. Code:"));
//$this->Text($mcol + 180, $this->formData['debtor_no']);
$this->NewLine(1);
$this->Font('bold');

//$this->MultiCell(100, 10,"Attn :  " , 0, 'L', 0, 2, 50,305, true);
$this->Font('u');
//$this->MultiCell(150, 10,$this->formData['f_text1'] , 0, 'L', 0, 2, 84,305, true);
$this->Font('');
//$this->MultiCell(150, 10, '______________' , 0, '', 0, 2, 76,282, true);


//$this->Text($mcol - 65, _("Attn:"));
//$this->NewLine(0.2);
//$this->Text($mcol - 65, _("____"));
//$this->NewLine(-0.2);
//$this->Text($mcol - 40, $this->formData['attn']);
//$this->NewLine(0.2);
//$this->Text($mcol - 40, _("______________"));
//$this->NewLine(-0.2);
//$this->Font('');

//$this->Text($mcol + 100, _("Acc Ref."));
//$this->Text($mcol + 180, $this->formData['reference']);


if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		//$this->fontSize += 8;
$this->NewLine(3);
//$this->fontSize += 10;
	//	$this->Text($ccol, $Addr1['title'], $icol);
	//	$this->Text($mcol, $Addr2['title']);
		//$this->fontSize -= 10;




//---------for left hand side
$customer_information=get_phone_10903($this->formData['debtor_no']);

$cust_info = get_informations($this->formData['debtor_no']);
$this->setfontsize(+9);
$this->MultiCell(45, 5, "Customer" , 0, 'L', 0, 2, 45, 105, true);
$this->MultiCell(180, 5, $Addr1['name'] , 0, 'L', 0, 2, 95, 105, true);
$this->MultiCell(45, 5, "Address:" , 0, 'L', 0, 2, 45, 120, true);
$this->MultiCell(235, 22, $Addr1['address'] , 0, 'L', 0, 2, 95, 120, true);

//$this->MultiCell(45, 5, "Phone" , 0, 'L', 0, 2, 48, 145, true);
//$this->MultiCell(180, 5, $myrow1["phone"] , 0, 'L', 0, 2, 95, 145, true);
$this->MultiCell(80, 5, "Customer GST No:" , 0, 'L', 0, 2, 48, 158, true);
$this->MultiCell(145, 5, $customer_information['tax_id'] , 0, 'L', 0, 2, 130, 158, true);
$this->MultiCell(80, 5, "Customer SST No:" , 0, 'L', 0, 2, 48, 170, true);
$this->MultiCell(145, 5, $customer_information['service_text'], 0, 'L', 0, 2, 130, 170, true);
$this->MultiCell(80, 5, "Customer NTN No:" , 0, 'L', 0, 2, 48, 183, true);
$this->MultiCell(145, 5, $customer_information['ntn_no'], 0, 'L', 0, 2, 130, 183, true);



$this->MultiCell(45, 5, "Attn" , 0, 'L', 0, 2, 48, 195, true);
$this->MultiCell(180, 5,$this->formData['f_text1'] , 0, 'L', 0, 2, 95, 195, true);


$this->MultiCell(45, 5, "Tel" , 0, 'L', 0, 2, 48, 205, true);
$this->MultiCell(180, 5,$cust_info['phone'], 0, 'L', 0, 2, 95, 205, true);


$this->MultiCell(45, 5, "Fax" , 0, 'L', 0, 2, 48, 215, true);
$this->MultiCell(180, 5,$cust_info['fax'], 0, 'L', 0, 2, 95, 215, true);



$this->MultiCell(70, 5, "Delivery Address" , 0, 'L', 0, 2, 380, 200, true);
$this->MultiCell(215, 22, $myrow1['delivery_address'] , 0, 'L', 0, 2, 350, 200, true);
//$this->MultiCell(600, 5, "Remarks:               ".$myrow1['comments'] , 0, //'L', 0, 2, 48, 201, true);
//$this->MultiCell(180, 5, "" , 0, 'L', 0, 2, 95, 201, true);

//-----------for right hand side


$this->MultiCell(100, 5, "Invoice Date" , 0, 'L', 0, 2, 380, 120, true);
$this->MultiCell(135, 5,sql2date($this->formData['document_date']) , 0, 'L', 0, 2, 435, 120, true);
$this->MultiCell(200, 5, "Invoice No." , 0, 'L', 0, 2, 380, 130, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 130, true);



$this->MultiCell(68, 5, "DC No." , 0, 'L', 0, 2, 380, 140, true);
$this->MultiCell(135, 5,$myrow1['f_text7'] , 0, 'L', 0, 2, 435, 140, true);

$this->MultiCell(68, 5, "DC Date" , 0, 'L', 0, 2, 380, 150, true);
$this->MultiCell(135, 5,$myrow1['f_date1'], 0, 'L', 0, 2, 435, 150, true);




$this->MultiCell(68, 5, "NTN No." , 0, 'L', 0, 2, 380, 160, true);
$this->MultiCell(135, 5,$this->company['coy_no'] , 0, 'L', 0, 2, 435, 160, true);

$this->MultiCell(68, 5, "GST No." , 0, 'L', 0, 2, 380, 170, true);
$this->MultiCell(135, 5, $this->company['gst_no'] , 0, 'L', 0, 2, 435, 170, true);
$this->MultiCell(68, 5, "SST No." , 0, 'L', 0, 2, 380, 180, true);
$this->MultiCell(135, 5, $this->company['sst_no'] , 0, 'L', 0, 2, 435, 180, true);


$this->MultiCell(200, 5, "Method of Delivery" , 0, 'L', 0, 2, 45, 230, true);
$this->MultiCell(135, 5, $myrow1['f_text4'], 0, 'L', 0, 2, 150, 230, true);



$this->MultiCell(75, 5, "Terms of Delivery" , 0, 'L', 0, 2, 45, 245, true);
$this->MultiCell(135, 5, $myrow1['f_text3'] , 0, 'L', 0, 2, 150, 245, true);


$this->MultiCell(75, 5, "Terms of Payment" , 0, 'L', 0, 2, 45, 260, true);
$this->MultiCell(135, 5, get_payment_terms_names10903($this->formData['payment_terms']), 0, 'L', 0, 2, 150, 260, true);

	$this->Font('bold');
$this->MultiCell(500, 5, "Please Note:        WHEN PAYING PLEASE QUOTE OUR INVOICE NUMBER AND PROVIDE PAYMENT ADVICE:" , 0, 'L', 0, 2, 45, 280, true);
	
$this->MultiCell(75, 5, "SO Number:" , 0, 'L', 0, 2, 45, 300, true);
$this->MultiCell(135, 5, $this->formData['reference'], 0, 'L', 0, 2, 115, 300, true);

$this->MultiCell(75, 5, "PO Number:" , 0, 'L', 0, 2, 250, 300, true);
$this->MultiCell(75, 5, $myrow1['f_text2'] , 0, 'L', 0, 2, 435, 300, true);

$this->MultiCell(75, 5, "Remarks:" , 0, 'L', 0, 2, 450, 300, true);
$this->MultiCell(75, 5, "FBR" , 0, 'L', 0, 2, 500, 300, true);
	$this->Font('');
// address1


$temp = $this->row = $this->row - $this->lineHeight - 5;
		//$this->Text($ccol, $Addr1['name'], $icol);
		$this->NewLine();
		//$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
//		$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
//		$this->NewLine();
//		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

		// Auxiliary document information
		$col = $this->leftMargin;
		foreach($aux_info as $info_header => $info_content)
		{

//			$this->row = $iline2 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//			$col += $width;
		}
		// Payment terms
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
	//	$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();

		// Line headers
			$this->Font('bold');
$this->headers = array(_("S.No"), _("Part #
Description"), _("QTY"), _("U.Price"),
	_("Value Excl. of S.Tax"),
	_("Rate of S.Tax %"),
	_("S.Tax Amt"),_("Value Incl. of S.Tax"));
		$this->row = $iline5 - $this->lineHeight - 1;
	
		$count = count($this->headers);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);
		$this->Font();

		// Footer
		$this->Font('');
		$this->row = $iline7 - $this->lineHeight - 6;
$this->MultiCell(220, 35, $this->formData['memo_'] , 0, 'L', 0, 2, 56,692, true);
$this->Font('italic');
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
			//	$this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
			$this->row -= $this->lineHeight;

		}
$this->Font('b');

function get_payment_terms_name($selected_id)
{
	$sql = "SELECT  terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

	$result = db_query($sql,"could not get payment term");
        $row =db_fetch_row($result);
	return $row[0];
}
//
//$this->MultiCell(100, 10, "Terms:" , 0, 'L', 0, 2, 36,680, true);
//$this->MultiCell(100, 10, "Note:" , 0, 'L', 0, 2, 336,680, true);
//$this->Font('');





//--------top right box-----------

//$this->MultiCell(175, 35, "" , 1, 'C', 0, 2, 389,42, true);
$this->font('b');
$this->setfontsize(10);
//$this->MultiCell(180, 150, "Authorized Industrial distributor " , 0, 'L', 0, 2//, 405,44, true);
//$this->MultiCell(170, 150, "Total Oil Pakistan LTD" , 0, 'L', 0, 2, 420,59, //true);
$this->setfontsize(10);
$this->Font();
$this->setfontsize(10);
//$this->MultiCell(300, 20, $_SESSION["wa_current_user"]->username, 0, 'L', 0, 2//, 57,800, true);

//$user=get_user_by_login($_SESSION["wa_current_user"]->username);
//$this->MultiCell(300, 20, $user['phone'], 0, 'L', 0, 2, 45,810, true);
$this->setfontsize(8);
		$temp = $iline6 - $this->lineHeight - 2;
?>