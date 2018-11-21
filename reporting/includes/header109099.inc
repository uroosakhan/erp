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
		$lower = $this->bottomMargin + 18 * $this->lineHeight;
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
		$this->SetDrawColor(128, 128, 128);
		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));   
		$this->Line($iline2);
		$this->Line($iline3);
		$this->Line($iline4);
		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));   
		$this->Line($iline5);
		$this->Line($iline6);
		$this->Line($iline7);
		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
		$col = $this->leftMargin;
		for ($i = 0; $i < 5; $i++)
		{
			$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
		$this->LineTo($right, $iline2 ,$right, $iline4);
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
		if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
			$this->LineTo($this->cols[$cols - 2], $iline5 ,$this->cols[$cols - 2], $iline7);
		else	
			$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine();
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			$this->AddImage($logo, $ccol +430, $this->row, 0, 40);
		}
		else
		{
			$this->fontSize += 4;
			$this->Font('bold');
//			$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 4;
		}

//marina----------
	$this->headers = array(_("S.No"), _("PO Pos No"), _("Material No"), _("Material Description"),
		_("Qty"), _("Rate"), _("Value"), _("Part Data"), _("Remarks"));

$this->formData['document_name'] =_("Order No.");
$this->formData['document_date'] = $this->formData['ord_date'];
$this->formData['document_number'] = $SysPrefs->print_invoice_no() == 0 && isset($this->formData['reference'])
	? $this->formData['reference'] : $this->formData['order_no'];
$this->formData['document_amount'] = $this->formData['order_no'];

$aux_info = array(
	_("Purchase Order no") => $this->formData[""],
	_("Purchase Order Date") => get_salesman_name($this->formData['']),
	_("Quotation Ref") => $this->formData[''],
	_("Quotation Date") => $this->formData[''],
	_("Delivery Date") => sql2date($this->formData['delivery_date']),
);
//---------for left hand side
$this->setfontsize(+9);
$this->MultiCell(45, 5, "Customer" , 0, 'L', 0, 2, 48, 65, true);
$this->MultiCell(180, 5, "" , 0, 'L', 0, 2, 95, 65, true);
$this->MultiCell(45, 5, "Address" , 0, 'L', 0, 2, 48, 76, true);
$this->MultiCell(235, 22, "" , 0, 'L', 0, 2, 95, 76, true);
$this->MultiCell(45, 5, "Phone" , 0, 'L', 0, 2, 48, 100, true);
$this->MultiCell(180, 5, "" , 0, 'L', 0, 2, 95, 100, true);
$this->MultiCell(45, 5, "Email" , 0, 'L', 0, 2, 48, 111, true);
$this->MultiCell(180, 5, "" ,0, 'L', 0, 2, 95, 111, true);
$this->MultiCell(70, 5, "Contact Person" , 0, 'L', 0, 2, 48, 122, true);
$this->MultiCell(155, 5, "" , 0, 'L', 0, 2, 120, 122, true);
$this->MultiCell(45, 5, "Phone" , 0, 'L', 0, 2, 48, 133, true);
$this->MultiCell(180, 5, "" , 0, 'L', 0, 2, 95, 133, true);
$this->MultiCell(80, 5, "Customer GST No." , 0, 'L', 0, 2, 48, 144, true);
$this->MultiCell(145, 5, "" , 0, 'L', 0, 2, 130, 144, true);
$this->MultiCell(80, 5, "Customer SST No." , 0, 'L', 0, 2, 48, 155, true);
$this->MultiCell(145, 5, "" , 0, 'L', 0, 2, 130, 155, true);
$this->MultiCell(80, 5, "Customer NTN No." , 0, 'L', 0, 2, 48, 166, true);
$this->MultiCell(145, 5, "" , 0, 'L', 0, 2, 130, 166, true);
$this->MultiCell(70, 5, "Delivery Address" , 0, 'L', 0, 2, 48, 177, true);
$this->MultiCell(215, 22, "" , 0, 'L', 0, 2, 120, 177, true);
$this->MultiCell(45, 5, "Remarks:" , 0, 'L', 0, 2, 48, 201, true);
$this->MultiCell(180, 5, "" , 0, 'L', 0, 2, 95, 201, true);

//-----------for right hand side

$this->MultiCell(45, 5, "Date" , 0, 'L', 0, 2, 350, 65, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 65, true);
$this->MultiCell(68, 5, "Sales Order No." , 0, 'L', 0, 2, 350, 79, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 79, true);
$this->MultiCell(68, 5, "Our GST No." , 0, 'L', 0, 2, 350, 93, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 93, true);
$this->MultiCell(68, 5, "Our SST No." , 0, 'L', 0, 2, 350, 107, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 107, true);
$this->MultiCell(68, 5, "Our NTN No." , 0, 'L', 0, 2, 350, 121, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 121, true);
$this->MultiCell(75, 5, "Terms of Payment" , 0, 'L', 0, 2, 350, 135, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 135, true);
$this->MultiCell(75, 5, "Terms of Delivery" , 0, 'L', 0, 2, 350, 150, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 150, true);
$this->MultiCell(68, 5, "Sales Person" , 0, 'L', 0, 2, 350, 165, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 165, true);
$this->MultiCell(68, 5, "Currency" , 0, 'L', 0, 2, 350, 180, true);
$this->MultiCell(135, 5, "" , 0, 'L', 0, 2, 435, 180, true);

//-----------for footer

$this->MultiCell(150, 5, "Total Value Exclusive of Sales Tax" ,0, 'L', 0, 2, 220, 620, true);
$this->MultiCell(100, 5, "Sales Tax" ,0, 'L', 0, 2, 220, 635, true);
$this->MultiCell(150, 5, "Total Value Inclusive of Sales Tax" ,0, 'L', 0, 2, 220, 650, true);
$this->MultiCell(100, 5, "Transportation" ,0, 'L', 0, 2, 220, 665, true);
$this->MultiCell(100, 5, "Grand Total" ,0, 'L', 0, 2, 220, 680, true);

		// Document title
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
		$this->TextWrap($ccol , $this->row, $this->pageWidth - $this->rightMargin - $mcol , $this->title, 'left');
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

//		$this->Font();
//		$this->row = $adrline;
//		$this->NewLine(3);
//		$this->Text($ccol , _("Date"));
//		$this->Text($ccol +20, sql2date($this->formData['document_date']));
//
//		$this->NewLine();
//		$this->Text($ccol , $this->formData['document_name']);
//		$this->Text($ccol +40, $this->formData['document_number']);
//		$this->NewLine(2);

		// Auxiliary document information
		$col = $this->leftMargin;
		foreach($aux_info as $info_header => $info_content)
		{

			$this->row = $iline2 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
			$this->row = $iline3 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
			$col += $width;
		}
		// Payment terms
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
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

		$this->Font();
		$temp = $iline6 - $this->lineHeight - 2;
