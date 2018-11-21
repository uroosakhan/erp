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

		$upper = $this->row - 0.2 * $this->lineHeight;
		$lower = $this->bottomMargin + 24 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 - 10 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 - 1 * $this->lineHeight;
		$iline6 = $iline5 - 1.5 * $this->lineHeight;
		$iline7 = $lower;
		$right = $this->pageWidth - $this->rightMargin;
		$width = ($right - $this->leftMargin) / 6;
		
		$widthh = ($right - $this->leftMargin) / 2; // SHARIQ
		
		$widthher = ($right - $this->leftMargin) / 4;  //shariq
		
		$icol = $this->pageWidth / 2;
		$ccol = $this->cols[0] + 4;
		$c2col = $ccol + 60;
		$ccol2 = $icol / 2;
		$mcol = $icol + 8;
		$mcol2 = $this->pageWidth - $ccol2;
		$cols = count($this->cols);
		$this->SetDrawColor(205, 205, 205);
		//$this->Line($iline1, 3);
		$this->SetDrawColor(128, 128, 128);
		//$this->Line($iline1);
	$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));   
		$this->Line($iline2);
		$this->Line($iline3);
		$this->Line($iline4);
	$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));   
		$this->Line($iline5);
		$this->Line($iline6);
		$this->Line($iline7+100);
		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
		$col = $this->leftMargin;
		for ($i = 0; $i < 4; $i++)
		{
			$this->LineTo($col += $widthher, $iline2,$col, $iline4);
		}
			
		
		for ($i = 0; $i < 2; $i++)
		{
			$this->LineTo($col += $widthh, $iline2,$col, $iline4);
		}
		$this->LineTo($right, $iline2 ,$right, $iline4);
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7+100);
		$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7+100);
//		$this->LineTo($this->cols[$cols - 2] + 10, $iline5 ,$this->cols[$cols - 2] + 10, $iline7+100);
		$this->LineTo($this->cols[$cols - 2] -50, $iline5 ,$this->cols[$cols - 2] -50, $iline7+100);
		$this->LineTo($this->cols[$cols - 2] -127, $iline5 ,$this->cols[$cols - 2] -127, $iline7+100);
		$this->LineTo($this->cols[$cols - 2] -220, $iline5 ,$this->cols[$cols - 2]-220, $iline7+100);
		$this->LineTo($this->cols[$cols - 2] -355, $iline5 ,$this->cols[$cols - 2]-355, $iline7+100);
		$this->LineTo($this->cols[$cols - 2] -415, $iline5 ,$this->cols[$cols - 2]-415, $iline7+100);
		$this->LineTo($right, $iline5 ,$right, $iline7+100);

		
	
	
	
		// Company Logo
		$this->NewLine();
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			//$this->AddImage($logo, $ccol, $this->row, 0, 40);
//			$this->NewLine(8);
//            $this->Text($ccol, $this->company['coy_name'], $icol);
////			$this->AddImage($logo, $ccol, $this->row, 0, 120);
//			$this->NewLine(-8);
		}
//		else
//		{
			$this->fontSize += 7;
//			$this->Font('bold');
			$this->Text($ccol+5, $this->company['coy_name'], $icol);
//			$this->Font();
			$this->fontSize -= 7;
//		}
		
		// Company Logo2
	
	/*  $logo1 = company_path() . "/images/aps_footer.JPG"; 
		//if ($this->company['coy_logo'] != '' && file_exists($logo1))
		//{
			$this->NewLine(63);
			$this->AddImage($logo1, $ccol, $this->row, 0, 80);
			$this->NewLine(-63);
		//} */
		
		
		
		
		// Document title
//		$this->NewLine(8);
		//$this->SetTextColor(190, 190, 190);
		$this->fontSize += 7;
		$this->Font('bold');
		$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol -15, "Delivery Challan", 'right');
		$this->Font();
		$this->fontSize -= 7;
		$this->NewLine();
		//$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;
//		$this->NewLine(-8);
		
		

	
	
//	$this->NewLine(0.5);
	
		// Company data
		$this->TextWrapLines($ccol+5, $icol, $this->company['postal_address']);
		$this->Font('italic');
//$this->NewLine(2);
		if (@$this->company['phone'])
		{
			$this->Text($ccol+5, _("Phone"), $c2col);
			$this->Text($c2col, $this->company['phone'], $mcol);
			$this->NewLine();
		}

	
	
	

	
		if (@$this->company['fax'])
		{
			$this->Text($ccol+5, _("Fax"), $c2col);
			$this->Text($c2col, $this->company['fax'], $mcol);
			$this->NewLine();
		}
	
//	$this->NewLine(0.5);
	
		if (@$this->company['phone'])
		{
			$this->Text($ccol+5, _("Mobile"), $c2col);
			$this->Text($c2col, $this->company['phone'], $mcol);
			$this->NewLine();
		}
	
	
//	$this->NewLine(0.5);
	
		if (@$this->company['email'])
		{
			$this->Text($ccol+5, _("Email"), $c2col);

			$url = "mailto:" . $this->company['email'];
			$this->SetTextColor(0, 0, 255);
			$this->Text($c2col, $this->company['email'], $mcol);
			$this->SetTextColor(0, 0, 0);
			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

			$this->NewLine();
		}
//		$this->NewLine();
		
		if (@$this->company['gst_no'])
		{
			$this->Text($ccol+5, _("NTN No."), $c2col);
			$this->Text($c2col , $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
			$this->Text($ccol, _("Province"), $c2col);
			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
		$this->Font();
		$this->row = $adrline;
		
		

	
		$this->NewLine(0.5);
		$this->Text($mcol + 100, _("Dated : "));
		$this->Text($mcol + 180, sql2date($this->formData['document_date']));
		$this->NewLine();		
		
		$this->Text($mcol + 100, $this->formData['document_name']);
		$this->Text($mcol + 180, $this->formData['document_number']);
		
		$this->NewLine(-9.5);
			
		
		
		if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize -= 4;
//		$this->Text($ccol, $Addr2['title'], $icol);
		$this->fontSize += 4;

		// Name		
		$temp = $this->row = $this->row - $this->lineHeight+5;
//		$this->Text($ccol+350, _('Ship to:'), $icol);
$this->NewLine(1);
		$this->Text($ccol+310, $Addr2['name'], $icol);
		$this->Text($ccol+10, $Addr1['name'], $icol);
		$this->NewLine(1.5);

     	// address1		
		$temp = $this->row = $this->row - $this->lineHeight+15;
//		$this->Text($ccol+5, _('Address:'), $icol);
		$this->TextWrapLines($ccol+310, $icol - $ccol, $Addr2['address']);
$this->Text($ccol+310 , "NTN.No: ".$this->formData['ntn_no']);
// $this->NewLine(-3);
		$this->TextWrapLines($ccol+10, $icol - $ccol, $Addr1['address']);

$this->Text($ccol+10 , "NTN.No: ".$this->formData['ntn_no']);

$this->multicell(210,100,"  Ship To",1,'L',0,0,350,130);
$this->multicell(210,20,"  ",1,'L',1,0,350,130);

$this->multicell(210,100,"  To",1,'L',0,0,50,130);
$this->multicell(210,20,"  ",1,'L',1,0,50,130);

// Auxiliary document information -- 1
$aux_info = array(
    _("Customer's Ref") => $this->formData["customer_ref"],
    _("S.Order No:") => $ref,
    _("Challan No") => $this->formData['h_text6'],
    _("Vehicle Number") => $this->formData['h_tehxt2'],
);
		$col = $this->leftMargin;

		foreach($aux_info as $info_header => $info_content)
		{

			$this->row = $iline2 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $widthher, $info_header, 'C');
			$this->row = $iline3 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $widthher, $info_content, 'C');
			$col += $widthher;
		}
				
		// Auxiliary document information  
		$col = $this->leftMargin;
//		foreach($aux_info2 as $info_header => $info_content)
//		{
//
//			$this->row = $iline2 - $this->lineHeight + 82;
//			$this->TextWrap($col-23, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight + 100;
//			$this->TextWrap($col+50, $this->row, $width, $info_content, 'C');
//			$col += $width;
//		}
//
//		// Auxiliary document information
//		$col = $this->leftMargin;
//		foreach($aux_info3 as $info_header => $info_content)
//		{
//
//			$this->row = $iline2 - $this->lineHeight - 380;
//			$this->TextWrap($col+415, $this->row, $width+50, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight - 380;
//			$this->TextWrap($col+430, $this->row, $width+50, $info_content, 'C');
//			$col += $width;
//		}
//
//		// Auxiliary document information
//		$col = $this->leftMargin;
//		foreach($aux_info4 as $info_header => $info_content)
//		{
//
//			$this->row = $iline2 - $this->lineHeight + 57;
//			$this->TextWrap($col-36, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight + 75;
//			$this->TextWrap($col+50, $this->row, $width, $info_content, 'C');
//			$col += $width;
//		}
//
//
//		// Auxiliary document information
//		$col = $this->leftMargin;
//		foreach($aux_info5 as $info_header => $info_content)
//		{
//
//			$this->row = $iline2 - $this->lineHeight + 44;
//			$this->TextWrap($col-28, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight + 61;
//			$this->TextWrap($col+52, $this->row, $width, $info_content, 'C');
//			$col += $width;
//		}
//
		// Auxiliary document information  
		
		$col = $this->leftMargin;
		foreach($aux_info6 as $info_header => $info_content)
		{

			$this->row = $iline2 - $this->lineHeight + 30;
			$this->TextWrap($col-30, $this->row, $width, $info_header, 'C');
			$this->row = $iline3 - $this->lineHeight + 47;
			$this->TextWrap($col+65, $this->row, $width, $Addr2['address'], 'C');
			$col += $width;
		}
		
		
		
			
	/*	// Payment terms
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();
	*/
		// Line headers
$this->headers = array(_("Sr.No"), _("Item Code"), _("Item Name"), _("No Of Packing"), _("Packing Size"), _("Quantity"), _("UOM"));

//$this->title = ($this->params['Sr.No'] ? _("Item Code") : _("Item Name"));
//$this->formData['document_name'] = _("Delivery Note No.");
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
?>