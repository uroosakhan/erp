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
		$lower = $this->bottomMargin + 40 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 - 8 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 + 3 * $this->lineHeight;
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
		for ($i = 0; $i < 5; $i++)
		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
//		$this->LineTo($right, $iline2 ,$right, $iline4);
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
//		if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
//			$this->LineTo($this->cols[$cols - 2], $iline5 ,$this->cols[$cols - 2], $iline7);
//		else
//			$this->LineTo($this->cols[$cols - 0] + 2, $iline5 ,$this->cols[$cols - 0] + 2, $iline7);
			$this->LineTo($this->cols[$cols - 1] - 2, $iline5 ,$this->cols[$cols - 1] - 2, $iline7);
			$this->LineTo($this->cols[$cols - 2] - 2, $iline5 ,$this->cols[$cols - 2] - 2, $iline7);
			$this->LineTo($this->cols[$cols - 3] - 2, $iline5 ,$this->cols[$cols - 3] - 2, $iline7);
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
			$this->fontSize += 4;
			$this->Font('bold');
			$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 4;
		}
		// Document title
//		$this->SetTextColor(190, 190, 190);
        $this->NewLine(+7);
		$this->fontSize += 10;
		$this->Font('bold');
		$this->TextWrap($ccol +190, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 150, "AGREEMENT", 'right');
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine(-6);
//		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
//		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
//		$this->Font('italic');
		if (@$this->company['phone'])
		{
//			$this->Text($ccol, _("Phone"), $c2col);
//			$this->Text($c2col, $this->company['phone'], $mcol);
//			$this->NewLine();
		}
		if (@$this->company['fax'])
		{
//			$this->Text($ccol, _("Fax"), $c2col);
//			$this->Text($c2col, $this->company['fax'], $mcol);
//			$this->NewLine();
		}
		if (@$this->company['email'])
		{
//			$this->Text($ccol, _("Email"), $c2col);
//
//			$url = "mailto:" . $this->company['email'];
//			$this->SetTextColor(0, 0, 255);
//			$this->Text($c2col, $this->company['email'], $mcol);
//			$this->SetTextColor(0, 0, 0);
//			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

//			$this->NewLine();
		}
		if (@$this->company['gst_no'])
		{
//			$this->Text($ccol, _("Our VAT No."), $c2col);
//			$this->Text($c2col, $this->company['gst_no'], $mcol);
//			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
//			$this->Text($ccol, _("Domicile"), $c2col);
//			$this->Text($c2col, $this->company['domicile'], $mcol);
//			$this->NewLine();
		}
//		$this->Font();
		$this->row = $adrline;
		$this->NewLine(3);
		$this->Text($ccol, _("Date"));
		$this->Text($ccol +50, sql2date($this->formData['document_date']));

		$this->NewLine();
		$this->Text($ccol, $this->formData['document_name']);
		$this->Text($ccol +50, $this->formData['document_number']);
		$this->NewLine(2);
	
		if ($this->pageNumber > 1)
			$this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize -= 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
		$this->fontSize += 4;

// address1
		$temp = $this->row = $this->row - $this->lineHeight - 5;
//		$this->Text($ccol, $Addr1['name'], $icol);
		$this->NewLine();
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
		$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
		$this->NewLine();
//		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address'], 'left', 0, 0, NULL, 1);

		// Auxiliary document information
		$col = $this->leftMargin;
		$aux_info = array(
//    _("Model") => $this->formData["customer_ref"],
//    _("Channels") =>$this->formData[""],
//    _("Unit Price") => $this->formData['tax_id'],
//    _("Amounts") => $this->formData['order_no'],
//    _("Delivery Date") => sql2date($this->formData['delivery_date']),
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
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();

		// Line headers
		$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
$this->headers = array(_("Model"), _("Channels"), _("Unit Price"), _("Amounts"));
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
$text = "This Agreement is being signed for the sales and purchase of TAIHO. S.Precision LED CCD Color
Sorter Machine (Factory Delivery) on the following payment terms and conditions.";
$this->MultiCell(500,30,$text,0,'L', 0, 2,40,150,true);
$this->MultiCell(500,10,"PARTY A (SELLER)"." ".$this->company['coy_name']." "."BEARING (NTN#" . $this->formData['ntn_no'].")",0,'l', 0, 2,40,185,true);
$this->MultiCell(500,10,"PARTY B (PURCHASER)"." ".$Addr1['name']." "."BEARING (NTN#" . $this->company['coy_no'].")",0,'l', 0, 2,40,200,true);
$this->MultiCell(500,50,$this->formData['f_comment1'],0,'l', 0, 2,40,365,true);
$this->MultiCell(500,50,$this->formData['f_comment2'],0,'l', 0, 2,40,435,true);
$this->MultiCell(500,50,"",0,'l', 0, 2,40,505,true);
$this->MultiCell(425,13,"",0,'l', 0, 2,115,560,true);
$this->MultiCell(425,50,$this->formData['f_comment3'],0,'l', 0, 2,40,595,true);
$this->MultiCell(120,10,"_______________________",0,'l', 0, 2,40,750,true);
$this->MultiCell(120,10,"_______________________",0,'l', 0, 2,370,750,true);
$this->MultiCell(500,10,$Addr2['address']." ".$this->company['phone'],0,'C', 0, 2,40,800,true);
$this->MultiCell(500,10,"EMAIL: ".$this->company['email']." "."WEB: ".$this->formData['email'],0,'C', 0, 2,40,810,true);


$this->Font('bold');
$this->MultiCell(180,10,"PARTS WITH EACH MACHINES:",0,'l', 0, 2,40,350,true);
$this->MultiCell(250,10,"ESSENTIAL PARTS INCLUDED WITH MACHINE:",0,'l', 0, 2,40,420,true);
$this->MultiCell(250,10,"PAYMENT TERMS:",0,'l', 0, 2,40,490,true);
$this->MultiCell(250,10,"DELIVERY TIME:",0,'l', 0, 2,40,560,true);
$this->MultiCell(250,10,"WARRANTY:",0,'l', 0, 2,40,580,true);
$this->MultiCell(120,10,"SELLER",0,'L', 0, 2,40,765,true);
$this->MultiCell(120,10,"PURCHASER",0,'L', 0, 2,370,765,true);
$this->MultiCell(200,10,$this->company['coy_name'],0,'L', 0, 2,40,780,true);
$this->MultiCell(120,10,$Addr1['name'],0,'L', 0, 2,370,780,true);

		$this->Font();
		$temp = $iline6 - $this->lineHeight - 2;
