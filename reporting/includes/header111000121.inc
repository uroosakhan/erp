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

		$upper = $this->row +6 * $this->lineHeight;
		$lower = $this->bottomMargin + 37 * $this->lineHeight;
		$iline1 = $upper - 9.5 * $this->lineHeight;
		$iline2 = $iline1 - 8 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 - 1 * $this->lineHeight;
		$iline6 = $iline5 - 1.5 * $this->lineHeight;
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
		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
		$this->Line($iline5);
		$this->Line($iline6);
		$this->Line($iline7);
//		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
		$col = $this->leftMargin;
		for ($i = 0; $i < 3; $i++)
		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
//		$this->LineTo($right, $iline2 ,$right, $iline4);
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
		$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] - 2, $iline7);
		$this->LineTo($this->cols[$cols - 1] - 73, $iline5 ,$this->cols[$cols - 1] - 73, $iline7);
		$this->LineTo($this->cols[$cols - 1] - 130, $iline5 ,$this->cols[$cols - 1] - 130, $iline7);
//		$this->LineTo($this->cols[$cols - 1] - 205, $iline5 ,$this->cols[$cols - 1] - 205, $iline7);
//		$this->LineTo($this->cols[$cols - 1] - 265, $iline5 ,$this->cols[$cols - 1] - 265, $iline7);
		$this->LineTo($this->cols[$cols - 1] - 430, $iline5 ,$this->cols[$cols - 1] - 430, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine(3);
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			//$this->AddImage($logo, $ccol, $this->row, $this->company['logo_w'], $this->company['logo_h']);
		}
		else
		{
			$this->fontSize += 4;
			$this->Font('bold');
//			$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 4;
		}
		// Document title
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
$this->NewLine(+5);
//		$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol + 10, $this->title, 'left');
$this->NewLine(-5);
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
// $this->Font('bold');

//		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
// 		$this->Font('italic');
// $this->Font('bold');

if (@$this->company['phone'])
		{
//			$this->Text($ccol, _("Phone:"), $c2col);
//			$this->Text($c2col-25,_("0314 278 8874 "), $mcol);
			$this->NewLine();
		}
		//if (@$this->company['fax'])
		{
//			$this->Text($ccol, _("Fax"), $c2col);
//			$this->Text($c2col, $this->company['fax'], $mcol);
			$this->NewLine();
		}
		if (@$this->company['email'])
		{
//			$this->Text($ccol, _("Email:"), $c2col);

		//	$url = "mailto:" . $this->company['email'];
			$this->SetTextColor(0, 0, 255);
//			$this->Text($c2col-25,  _("sales@algronic.com"), $mcol);
			$this->SetTextColor(0, 0, 0);
			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

			$this->NewLine();
		}
		if (@$this->company['gst_no'])
		{
		//	$this->Text($ccol, _("Our GST No."), $c2col);
		//	$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
		//	$this->Text($ccol, _("Province"), $c2col);
		//	$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
		$this->fontSize += 1.5;
	
		$this->row = $adrline;
		$this->NewLine(+3);
			$this->Font('b');
$this->Text($mcol + 150, _("Date:"));
				$this->Text($mcol + 180, sql2date($this->formData['document_date']));
// 			$this->Font('b');
	
		

$this->NewLine(-8);

		$this->NewLine(8);
//		$this->Text($mcol + 84, $this->formData['document_number']);
//		$this->Font('b');
//		$this->Text($mcol + 10, $this->formData['document_name'].":");

		
		
$this->fontSize -= 1.5;
		if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		//$this->fontSize -= 4;
// 		$this->Text($mcol, $Addr2['title']);

		$this->fontSize += 2;
//		$this->Text($ccol, $Addr1['title'], $icol);

// address1
// $this->MultiCell(500, 5, "dummy".$Addr1['name'], 0, 'C', 0, 2, 80, 35, true);

		$temp = $this->row = $this->row - $this->lineHeight - 5;
//		$this->Text($ccol, $this->formData['name'], $icol);
		$this->NewLine(1);
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
		$this->fontSize -= 2;

// address2
// 		$this->row = $temp;
// 		$this->Text($mcol, $Addr2['name']);
// 		$this->NewLine();
// 		$this->TextWrapLines($mcol, 100 - 200, $Addr2['address']);

		// Auxiliary document information
		$col = $this->leftMargin;
$aux_info = array(
//	_("Project Name") => $this->formData["f_text2"],
//	_("Sales Person") => get_salesman_name($this->formData['salesman']),
//	_("Attention To") => $this->formData['f_text1'],
	
);
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
		//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();
$this->MultiCell(525, 170, "" ,1, 'L', 0, 2, 40,390, true);//S.no
$this->MultiCell(90, 40, "" ,1, 'L', 0, 2, 452,109, true);//S.no
$this->MultiCell(90, 19, "" ,1, 'L', 0, 2, 452,130, true);//S.no
//
$this->MultiCell(525, 30, "" ,1, 'L', 0, 2, 40,410, true);//S.no
$this->MultiCell(525, 30, "" ,1, 'L', 0, 2, 40,470, true);//S.no
$this->MultiCell(525, 30, "" ,1, 'L', 0, 2, 40,530, true);//S.no
$this->MultiCell(90, 150, "" ,1, 'L', 0, 2, 60,410, true);//S.no
$this->Font('bold');
$this->MultiCell(90, 140, "Price" ,0, 'L', 0, 2, 70,418, true);//S.no
$this->MultiCell(90, 140, "Delivery" ,0, 'L', 0, 2, 70,449, true);//S.no
$this->MultiCell(90, 140, "Payment" ,0, 'L', 0, 2, 70,477, true);//S.no
$this->MultiCell(90, 140, "Warranty" ,0, 'L', 0, 2, 70,507, true);//S.no
$this->MultiCell(90, 140, "Validity" ,0, 'L', 0, 2, 70,537, true);//S.no
$this->MultiCell(265, 130, "" ,0, 'L', 0, 2, 40,90, true);//S.no
$this->MultiCell(265, 130, "" ,0, 'L', 0, 2, 40,105, true);//S.no
$this->MultiCell(265, 130, "" ,0, 'L', 0, 2, 40,121, true);//S.no
$this->MultiCell(265, 130, "" ,0, 'L', 0, 2, 40,135, true);//S.no
// $this->MultiCell(265, 130, "Quotation for-------.." ,0, 'L', 0, 2, 64,160, true);//S.no
$this->MultiCell(265, 130, "" ,0, 'L', 0, 2, 40,170, true);//S.no

$this->MultiCell(265, 130, "".$this->company['coy_name'] ,0, 'L', 0, 2, 40,637, true);//S.no

		// Line headers
		$this->headers = array(_("S.No  "), _("Item Description"), _(""),
	_(""), _("QTY"),_("Unit Price"),   _("Amount(Rs)"));
		$this->row = $iline5 - $this->lineHeight - 1;
			$this->SetTextColor(43, 106, 139);
			$this->MultiCell(525, 130, "Terms and Condition" ,0, 'L', 0, 2, 250,394, true);//S.no
			$this->MultiCell(265, 130, "Sub:" ,0, 'L', 0, 2, 40,185, true);//S.no
				$this->fontSize += 10;
					$this->Text($ccol, _(""), $c2col);
					$this->MultiCell(265, 130, " Sales Quotation" ,0, 'L', 0, 2, 230,88, true);//S.no
						$this->fontSize -= 10;
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
//				$this->SetTextColor(0, 0, 255);
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
