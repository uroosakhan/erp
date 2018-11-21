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
		$lower = $this->bottomMargin + 15 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 -7 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 - 3 * $this->lineHeight;
		$iline6 = $iline5 - 3 * $this->lineHeight;
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
		$this->Line($iline1, 3);
		$this->SetDrawColor(128, 128, 128);
		$this->Line($iline1);
//		$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//		$this->Line($iline2);
//		$this->Line($iline3);
//		$this->Line($iline4);
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
		//$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine();
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			$this->AddImage($logo, $ccol, $this->row, 0, 40);
		}
		//else
		//{
			$this->fontSize += 4;
			$this->Font('bold');
//			$this->Text($ccol + 300, $this->company['coy_name'], $icol,false,false,'C');
$this->SetFontSize(18);
$this->multicell(250,20,$this->company['coy_name'],0,'C',0,1,320,15);

$this->Font();

			$this->fontSize -= 4;
		//}
		// Document title
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
$this->multicell(250,20,$this->title,0,'C',0,1,320,120);
	//	$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
//		$this->TextWrapLines($ccol + 200, $icol, $this->company['postal_address'],false,false,'C');

$this->SetFontSize(9);
$this->multicell(250,50,$this->company['postal_address'],0,'C',0,1,320,40);
$this->Font('italic');
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

			$this->NewLine();
		}
$this->multicell(250,50,"Our GST No.".$this->company['gst_no'],0,'C',0,1,370,70);
		if (@$this->company['gst_no'])
		{
//			$this->Text($ccol + 100, _("Our GST No."), $c2col +50);
//			$this->Text($c2col + 100, $this->company['gst_no'], $mcol +20);

			$this->NewLine();
		}

$this->multicell(250,50,"NTN No.".$this->company['coy_no'],0,'C',0,1,220,70);
if (@$this->company['coy_no'])
		{
//			$this->Text($ccol, _("NTN No."), $c2col);
//			$this->Text($c2col, $this->company['coy_no'], $mcol);

			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
			$this->Text($ccol, _("Province"), $c2col);
			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
$this->Font('');

//--------left side info
$this->SetFontSize(10);
$this->multicell(115,2, sql2date($this->formData['document_date']),0,'L',0,1,468,195);
$this->multicell(115,2, ($this->formData['trans_no']),0,'L',0,1,478,168);
$this->multicell(115,2,$this->formData['supp_reference'],0,'L',0,1,470,220);

$this->Font('B');
$this->multicell(100,50,"Supplier:",0,'L',0,1,40,170);
$this->multicell(100,50,"Name:",0,'L',0,1,40,185);
$this->multicell(100,50,"Address :",0,'L',0,1,40,200);
//$this->multicell(100,50,"City :",0,'L',0,1,40,215);
//$this->multicell(100,50,"Reg # :",0,'L',0,1,40,230);
$this->multicell(200,50, "_____________________________",0,'L',0,1,80,690);
$this->multicell(200,50, "Authorized Signatures",0,'C',0,1,60,705);
$this->multicell(115,2,"Purchase Invoice No.:",0,'L',0,1,341,170);
$this->multicell(115,2," ___________________",0,'L',0,1,456,170);
$this->multicell(115,2,"Dated:",0,'L',0,1,341,195);

$this->multicell(115,2," ___________________",0,'L',0,1,456,197);
$supplier=get_supplier($this->formData['supplier_id']);
$this->multicell(115,2,"Particular of GD Import:",0,'L',0,1,341,220);

$this->multicell(115,2,"  ___________________",0,'L',0,1,456,221);
$this->Font('');
$this->multicell(100,50,$this->company['supp_name'],0,'L',0,1,100,170);
$this->multicell(200,50,get_supplier_name($this->formData['supplier_id']),0,'L',0,1,100,185);
$this->multicell(200,50,$supplier['address'],0,'L',0,1,100,200);
$this->multicell(200,50,$this->company['supp_name'],0,'L',0,1,100,215);
$this->multicell(200,50,$supplier['gst_no'],0,'L',0,1,100,230);
//------------------------

//--------Right side info
$this->SetFontSize(10);
$this->Font('B');
$this->multicell(100,50,"Gst invoice # :",0,'L',0,1,600,170);
$this->multicell(100,50,"Dated :",0,'L',0,1,600,185);
$this->multicell(100,50,"Ref GD No :",0,'L',0,1,600,200);
$this->multicell(100,50,"Supplier invoice no :",0,'L',0,1,600,215);

$this->Font('');
$this->multicell(100,50,$this->formData['reference'],0,'L',0,1,700,170);
$this->multicell(100,50,sql2date($this->formData['tran_date']),0,'L',0,1,700,185);
$this->multicell(100,50,$this->formData['reference'],0,'L',0,1,700,200);
$this->multicell(100,50,$this->formData['trans_no'],0,'L',0,1,700,215);

//-----------------------


		$this->Font();
		$this->row = $adrline;
		$this->NewLine(3);
//		$this->Text($mcol + 100, _("Date"));
//		$this->Text($mcol + 180, sql2date($this->formData['document_date']));

		$this->NewLine();
//		$this->Text($mcol + 100, $this->formData['document_name']);
//		$this->Text($mcol + 180, $this->formData['document_number']);

		if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize += 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
		$this->fontSize -= 4;

// address1
		$temp = $this->row = $this->row - $this->lineHeight - 5;
//		$this->Text($ccol, $Addr1['name'], $icol);
		$this->NewLine();
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
		$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
		$this->NewLine();
//		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

		// Auxiliary document information
		$col = $this->leftMargin;
//		foreach($aux_info as $info_header => $info_content)
//		{
//
//			$this->row = $iline2 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//			$col += $width;
//		}
		// Payment terms
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();

		$this->headers = array(_("Qty"), _("UOM"),
			_("Description"), _("U.Price"), 	_("Assd. Imp."),
			 _("Custom Duty"), _("Add.")
		, _("Ex.Val."), _("Rate %"), _("Sales"), _("Total"));


		$this->headers2 = array(_(""), _(""),
			_("of goods"), _(""), 	_("Val.(Rs)"),
			 _("Amt.Rs"),  _("Custom Rs")
		, _("of Pur. Rs"), _(""), _("Tax Rs"), _("Pur.Rs"));


		// Line headers
		$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
		$count = count($this->headers);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);
		$this->Font();
$this->NewLine();
// Line headers2
//		$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
		$count = count($this->headers2);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
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
