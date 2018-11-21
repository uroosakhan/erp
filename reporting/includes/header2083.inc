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
		$upper = $this->row + 8 * $this->lineHeight;
		$lower = $this->bottomMargin + 38 * $this->lineHeight;
		$lower8 = $this->bottomMargin + 40 * $this->lineHeight;
		$iline1 = $upper - 5 * $this->lineHeight;
		$iline2 = $iline1 - 8 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 - 0 * $this->lineHeight;
		$iline6 = $iline5 - 2.5 * $this->lineHeight;
		$iline7 = $lower;
		$iline8 = $lower8;
		$right = $this->pageWidth - $this->rightMargin;
		$width = ($right - $this->leftMargin) / 5;
		$icol = $this->pageWidth / 2;
		$ccol = $this->cols[0] + 4;
		$c2col = $ccol + 60;
		$ccol2 = $icol / 2;
		$mcol = $icol + 8;
		$mcol2 = $this->pageWidth - $ccol2;
		$cols = count($this->cols);
		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));   
		$this->Line($iline5);
		$this->Line($iline6);
		$this->Line($iline8);
		$this->Line($iline7);
		$this->rectangle($this->leftMargin, $iline8, $right - $this->leftMargin, $iline8 - $iline7, "F", null, array(222, 231, 236));

		$col = $this->leftMargin;
		for ($i = 0; $i < 5; $i++)
		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
	$this->NewLine(3);
		$this->SetTextColor(0,0,0);
		$this->fontSize += 10;
//		$this->Font('bold');
	$this->NewLine(-2);
		$this->TextWrap($ccol +80, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 10, 'Goods Receipt Note', 'right');
//		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
//		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		$this->Font('italic');
		if (@$this->company['phone'])
		{
//			$this->Text($ccol, _("Phone"), $c2col);
//			$this->Text($c2col, $this->company['phone'], $mcol);
			$this->NewLine();
		}
		if (@$this->company['fax'])
		{
//			$this->Text($ccol, _("Fax"), $c2col);
//			$this->Text($c2col, $this->company['fax'], $mcol);
			$this->NewLine();
		}
		if (@$this->company['email'])
		{
//			$this->Text($ccol, _("Email"), $c2col);
//			$url = "mailto:" . $this->company['email'];
//			$this->SetTextColor(0, 0, 255);
//			$this->Text($c2col, $this->company['email'], $mcol);
//			$this->SetTextColor(0, 0, 0);
//			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

			$this->NewLine();
		}
		if (@$this->company['gst_no'])
		{
//			$this->Text($ccol, _("Our GST No."), $c2col);
//			$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
//			$this->Text($ccol, _("Province"), $c2col);
//			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
		$this->Font();
		$this->row = $adrline;
// 		$this->NewLine(3);
		$this->Text($mcol + 90, _("GRN #"));
		$this->Text($mcol + 115, $this->formData['reference']);

		$this->NewLine(1.3);
		$this->Text($mcol + 90, _("GRN DATE:"));
		$this->Text($mcol + 130,sql2date($this->formData['delivery_date']));
		$this->Text($mcol + 180, '' );
$this->NewLine(1.3);
$this->Text($mcol + 90, _("DC No :"));
$this->Text($mcol + 115,($this->formData['dc_no']));
$this->Text($mcol + 180, '' );
		$this->NewLine(2);
		$this->NewLine();
		$this->NewLine(4);
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
		//$this->Text($mcol -100 ,$this->title);
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;
		if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize -= 4;
//		$this->Text($ccol, $Addr2['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
		$this->fontSize += 4;

// 		address1
		$temp = $this->row = $this->row - $this->lineHeight - 2;
//		$this->Text($ccol, "To: M/s    ", $icol);
// 		$this->NewLine(-1);
	//	$this->Text($ccol, "PARTY CODE:         " .$this->formData['supplier_id'], $icol);
		$this->NewLine(2.8);
		$this->TextWrapLines($ccol, $icol - $ccol, "Vendor :");
		$this->MultiCell(240, 20, $this->formData['supp_name'] , 0, 'L', 0, 2, 90,56, true);
		$this->NewLine(0.1);
		$this->Text($ccol, "Address :  ".$this->formData['supp_address']);
		$this->NewLine(1.2);
		$this->Text($ccol, "Purchase Order No :   ".$this->formData['reference']);
		$this->NewLine(1.1);
		$this->Text($ccol, "Purchase Order Date :  ".sql2date($this->formData['delivery_date']));
		$this->NewLine(1.2);
		$this->Text($ccol, "REMARKS  ".$this->formData['Comments2']);
        // $supplier_info = get_phoneno_for_suppliers($this->formData['supplier_id']);







$this->NewLine(28);

//$this->SetTextColor(0,0,0);
$this->fontSize += 10;
//		$this->Font('bold');
//$this->NewLine(-2);
$this->TextWrap($ccol +80, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 10, 'Goods Receipt Note', 'right');
//		$this->Font();
$this->fontSize -= 10;
$this->NewLine();
//$this->SetTextColor(0, 0, 0);
$adrline = $this->row;
$this->NewLine(1.3);
$this->Text($mcol + 90, _("GRN #"));
$this->Text($mcol + 115, $this->formData['reference']);

$this->NewLine(1.3);
$this->Text($mcol + 90, _("GRN DATE:"));
$this->Text($mcol + 130,sql2date($this->formData['delivery_date']));
$this->Text($mcol + 180, '' );
$this->NewLine(-1.3);
$this->NewLine(2.3);
$this->Text($mcol + 90, _("DC No :"));
$this->Text($mcol + 115,($this->formData['dc_no']));
$this->Text($mcol + 180, '' );
$this->NewLine(-2.3);
$this->TextWrapLines($ccol, $icol - $ccol, "Vendor :");
$this->MultiCell(240, 20, $this->formData['supp_name'] , 0, 'L', 0, 2, 90,475, true);
$this->NewLine(0.1);
$this->Text($ccol, "Address :  ".$this->formData['supp_address']);
$this->NewLine(1.2);
$this->Text($ccol, "Purchase Order No :    ".$this->formData['reference']);
$this->NewLine(1.1);
$this->Text($ccol, "Purchase Order Date :  ".sql2date($this->formData['delivery_date']));
$this->NewLine(1.2);
$this->Text($ccol, "REMARKS  ".$this->formData['Comments2']);

/// second box
$this->MultiCell(525, 215,"", 1, 'L', 0, 2, 40,542, true); //second
$this->MultiCell(525, 30,"", 1, 'L', 1, 2, 40,542, true); //second
$this->MultiCell(525, 20,"", 1, 'L', 1, 2, 40,740, true); //second
//$this->MultiCell(80, 180,"", 1, 'L', 0, 2, 360,570, true); //second
//$this->MultiCell(70, 180,"", 1, 'L', 0, 2, 440,570, true); //second
		$this->row = $temp;

		$col = $this->leftMargin;

		$aux_info = array(
			_("Customer's Reference") => $this->formData["customer_ref"],
			_("Sales Person") => get_salesman_name($this->formData['salesman']),
			_("Your GST no.") => $this->formData['tax_id'],
			_("Delivery Note No.") => implode(',', $deliveries),
			_("Due Date") => sql2date($this->formData['due_date']),
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
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms); 
		$this->Font();

		// Line headers
		$this->row = $iline5 - $this->lineHeight - 1;

		$this->Font('bold');


        $this->headers = array(_("S"), _("P.O."), _("Material"),
        _("Material "), _("BALANCE STOCK"), _("QTY"), _("BALANCE STOCK"), _("UOM"));
        
        $this->headers2 = array(_("No"), _("NUMBER"), _("No"), _("Description"),
        _("BEFORE"),_(""),_("AFTER"),_(""));

		$count = count($this->headers);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);
		$this->Font();
		$this->row = $iline5 - $this->lineHeight - 12;
		$this->Font('bold');
		$count = count($this->headers2);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers2[$i], -2);
		$this->Font();
$this->row = $iline5 - $this->lineHeight - 405;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers[$i], -5);
$this->Font();
$this->row = $iline5 - $this->lineHeight - 415;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers2[$i], -5);
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