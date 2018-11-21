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
		$iline2 = $iline1 - 12 * $this->lineHeight;
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
		$this->SetDrawColor(0, 0, 0);
		$this->Line($iline1);
		$this->SetDrawColor(0, 0, 0);
		$this->Line($iline1);
		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
		$this->Line($iline2);
		$this->Line($iline3);
		$this->Line($iline4);
		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
		$this->Line($iline5);
		$this->Line($iline6);
//		$this->Line($iline7);
		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
		$col = $this->leftMargin;
		for ($i = 0; $i < 5; $i++)
		{
			$this->LineTo($col += $width, $iline2,$col, $iline4);
		}

// lower box lines goes here

//		$this->LineTo($right, $iline2 ,$right, $iline4);
//		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
////		$this->LineTo($this->cols[$cols - 1] -2, $iline5 ,$this->cols[$cols - 1] -2, $iline7);
////		$this->LineTo($this->cols[$cols - 2] -2, $iline5 ,$this->cols[$cols - 2] -2, $iline7);
////		$this->LineTo($this->cols[$cols - 3] -2, $iline5 ,$this->cols[$cols - 3] -2, $iline7);
//		$this->LineTo($right, $iline5 ,$right, $iline7);
//		$this->LineTo($right-70, $iline5 ,$right-70, $iline7);
//		$this->LineTo($right-130, $iline5 ,$right-130, $iline7);
//		$this->LineTo($right-450, $iline5 ,$right-450, $iline7);
//		$this->LineTo($right-502, $iline5 ,$right-502, $iline7);





		// Company Logo
$this->NewLine(-2);
//$this->MultiCell(58, 394, "", 1, 'R', 0, 2, 40, 322, true);
//$this->MultiCell(320, 394, "", 1, 'R', 0, 2, 98, 322, true);
//$this->MultiCell(120, 394, "", 1, 'R', 0, 2, 418, 322, true);
$this->AliasNbPages();
$str = _("                Page") . '  ' . $this->pageNumber . '/' . $this->getAliasNbPages();
$this->Text($str, $str, 0, 0, 0, 'right', 0, 0, NULL, 1);
		$this->NewLine(2);		$logo = company_path() . "/images/" . $this->company['coy_logo'];
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
		$this->SetTextColor(0, 0, 0);
		$this->fontSize += 10;
		$this->Font('bold');
		$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
// 		$this->Font('italic');
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
		}

		$this->row = $adrline;
		$this->NewLine(3);
			$this->Text($mcol + 100, $this->formData['document_name']);
					$this->Font('b');
		$this->Text($mcol + 180, $this->formData['document_number']);
			$this->Font('');

		$this->NewLine();
		$this->Text($mcol + 100, _("Date"));
			$this->Font('b');
		$this->Text($mcol + 180, sql2date($this->formData['document_date']));
	$this->Font('');
	//	if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
	//		$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber//);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize -= 0;
		$this->Text($ccol, $Addr1['title'], $icol);
			$this->Font('b');
		$this->Text($mcol, $Addr2['title']);
			$this->Font('');
		$this->fontSize += 0;

// address1
		$temp = $this->row = $this->row - $this->lineHeight - 5;
		$this->Text($ccol, $Addr1['name'], $icol);
		$this->NewLine();
		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
	$this->Font('b');
		$this->row = $temp;
		$this->Text($mcol, $Addr2['name']);
		$this->NewLine();
		$this->TextWrapLines($mcol, 600 - $mcol, $Addr2['address']);
// 		$this->NewLine();
			
        $this->TextWrapLines($mcol, 600 - $mcol, "Contact Person: ".$this->formData['f_text1']);
        $this->NewLine(0.2);
        $this->TextWrapLines($mcol, 600 - $mcol, "Contact No.: ".$this->formData['contact_phone']);
	$this->Font('');
		// Auxiliary document information
		$col = $this->leftMargin;
$aux_info = array(
	_("Customer's Ref") => $this->formData["customer_ref"],
	_("Sales Person") => get_salesman_name($this->formData['salesman']),
	_("NTN #") => $this->formData['ntn_no'],
	_("Your GST#.") => $this->formData['tax_id'],
	_("Our Order #") => $ref,

);
		foreach($aux_info as $info_header => $info_content)
		{

			$this->row = $iline2 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
			$this->row = $iline3 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
			$col += $width;
		}
		// Payment terms
//$this->NewLine(4);
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
		//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();

		// Line headers
		$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
$this->headers = array(_("Sr.#"), _("Item Code"), _("Item Description"), _("Quantity"),
	_("Unit"));
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