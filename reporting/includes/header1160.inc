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
		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
		$this->Line($iline5);
		$this->Line($iline6);
		$this->Line($iline7);
		//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
		$col = $this->leftMargin;
		for ($i = 0; $i < 5; $i++)
		{
			//$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
		//$this->LineTo($right, $iline2 ,$right, $iline4);
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
		$this->LineTo($this->cols[$cols - 1] + 1, $iline5 ,$this->cols[$cols - 1] + 1, $iline7);
		//$this->LineTo($this->cols[$cols - 2] -10, $iline5 ,$this->cols[$cols - 2] -10, $iline7);
		//$this->LineTo($this->cols[$cols - 3] - 2, $iline5 ,$this->cols[$cols - 3] - 2, $iline7);
		//$this->LineTo($this->cols[$cols - 4] -2, $iline5 ,$this->cols[$cols - 4] - 2, $iline7);
		//$this->LineTo($this->cols[$cols - 5] - 2, $iline5 ,$this->cols[$cols - 5] -2, $iline7);
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
			$this->fontSize += 10;
			$this->Font('bold');
			$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 10;
		}
		// Document title
		//$this->SetTextColor(190, 190, 190);
		$this->fontSize += 12;
		$this->Font('bold');
		$this->NewLine(+11);
		$this->TextWrap($mcol-150, $this->row, $this->pageWidth - $this->rightMargin - $mcol , _("Sales Tax Invoice"), 'center');
		$this->Font();
		$this->NewLine(-11);
		$this->fontSize -= 12;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		$this->Font('italic');
		if (@$this->company['phone'])
		{
			$this->Text($ccol, _("Phone"), $c2col);
			$this->Text($c2col-30, $this->company['phone'], $mcol);
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
			$this->Text($c2col-30, $this->company['email'], $mcol);
			$this->SetTextColor(0, 0, 0);
			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

			$this->NewLine();
		}
		if (@$this->company['gst_no'])
		{
			$this->Text($ccol, _("S.tax Reg No."), $c2col);
			$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
			$this->Text($ccol, _("Province"), $c2col);
			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
$this->Text($ccol, _("NTN#  "));
		$this->Text($c2col, $this->company['coy_no']);
		$this->Font('');
		$this->row = $adrline;
$this->NewLine(15);
$this->Text($mcol + 100, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['trans_no']);
$this->NewLine(-1);
$this->Text($mcol + 100, _("Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));
$this->NewLine(+1);

$this->NewLine(1);
//$this->NewLine(1);
$so=get_sales_order_header($this->formData['trans_no'],30);
  
               $this->Text($mcol + 100, "Order No.");
		$this->Text($mcol + 180, $so['order_no']);
//$this->Text($mcol + 100, _("Cust.Order#"));
//$this->Text($mcol + 180, $this->formData['order_']);
$this->NewLine(1);
$this->Font('bold');


//$this->MultiCell(150, 10, '______________' , 0, '', 0, 2, 76,282, true);


//$this->Text($mcol - 65, _("Attn:"));
//$this->NewLine(0.2);
//$this->Text($mcol - 65, _("____"));
//$this->NewLine(-0.2);
//$this->Text($mcol - 40, $this->formData['attn']);
//$this->NewLine(0.2);
//$this->Text($mcol - 40, _("______________"));
//$this->NewLine(-0.2);
$this->Font('');

//$this->Text($mcol + 100, _("Acc Ref."));
//$this->Text($mcol + 180, $this->formData['reference']);


if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		//$this->fontSize += 8;
$this->NewLine(3);
//$this->fontSize += 10;
		$this->Text($ccol, $Addr1['title'], $icol);
	//	$this->Text($mcol, $Addr2['title']);
		//$this->fontSize -= 10;

// address1


$temp = $this->row = $this->row - $this->lineHeight - 5;
		$this->Text($ccol, $Addr1['name'], $icol);
		$this->NewLine();
		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

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

$this->MultiCell(100, 10, "Notes:" , 0, 'L', 0, 2, 36,660, true);
$this->MultiCell(100, 10, "Special Notes:" , 0, 'L', 0, 2, 366,660, true);
$this->Font('');
$this->MultiCell(220, 35, $this->formData['memo_'] , 0, 'L', 0, 2, 366,672, true);


$this->MultiCell(300, 10, "Authorized signature and stamp" , 0, 'C', 0, 2, 160,795, true);


$this->Font('');
$this->MultiCell(250, 20, "Payment Should be made on A/c of   " , 0, 'L', 0, 2, 36,670, true);
$this->Font('B');
$this->MultiCell(250, 20, " Petro Trade & Industry." , 0, 'L', 0, 2, 176,670, true);
$this->Font('');
$this->MultiCell(250, 20, "Payment Terms :  ".get_payment_terms_name($this->formData['payment_terms']) , 0, 'L', 0, 2, 36,682, true);
$this->Font('');
$this->MultiCell(170, 10, "Payment Due Date : " , 0, 'L', 0, 2, 36,694, true);
$this->Font('B');
$this->MultiCell(170, 10, sql2date($this->formData['due_date']) , 0, 'L', 0, 2, 130,694, true);
$this->Font('');
//$this->MultiCell(250, 20, "GST # ".($this->formData['tax_id']) , 0, 'L', 0, 2, 50,270, true);
$this->MultiCell(250, 20, "GST No . ".($this->formData['tax_id']) , 0, 'L', 0, 2, 50,272, true);
$this->MultiCell(250, 20, "NTN No .".($this->formData['ntn_id']) , 0, 'L', 0, 2, 50,282, true);
$this->MultiCell(100, 10,"Attn :  " , 0, 'L', 0, 2, 50,293, true);
$this->Font('u');
$this->MultiCell(150, 10,$this->formData['f_text1'] , 0, 'L', 0, 2, 76,293, true);
$this->Font('');

//--------top right box-----------

$this->MultiCell(175, 35, "" , 1, 'C', 0, 2, 389,42, true);
$this->font('B');

$this->MultiCell(180, 150, "Authorized Industrial distributor " , 0, 'L', 0, 2, 410,44, true);
$this->MultiCell(170, 150, "Total Parco Pakistan LTD" , 0, 'L', 0, 2, 420,59, true);
		$this->Font();
		$temp = $iline6 - $this->lineHeight - 2;
?>
