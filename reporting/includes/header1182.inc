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

		$upper = $this->row - 3 * $this->lineHeight;
		$lower = $this->bottomMargin + 8 * $this->lineHeight;
		$iline1 = $upper - 2 * $this->lineHeight;
		$iline2 = $iline1 - 4.5 * $this->lineHeight; //decription heaader
		$iline3 = $iline2 - 0 * $this->lineHeight;
		$iline4 = $iline3 - 0 * $this->lineHeight;
		$iline5 = $iline4 - 1 * $this->lineHeight;
		$iline6 = $iline5 - 0.5 * $this->lineHeight;
		$iline7 = $lower;
		$right = $this->pageWidth - $this->rightMargin;
		$width = ($right - $this->leftMargin) / 5;
		$icol = $this->pageWidth / 2;
		$icol = $this->pageWidth;
		$ccol = $this->cols[0] + 3;
		$c2col = $ccol + 60;
		$ccol2 = $icol / 2;
		$mcol = $icol + 8;
		$mcol2 = $this->pageWidth - $ccol2;
		$cols = count($this->cols);
		$this->SetDrawColor(205, 205, 205);
	//	$this->Line($iline1, 3);
		$this->SetDrawColor(128, 128, 128);
	/*	$this->Line($iline1);
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
		$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);
*/
		// Company Logo

$this->NewLine(3);
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
$this->NewLine(+1);
	$this->AddImage($logo, $ccol+80, $this->row, 0, 40);
$this->NewLine(-1);
}
else
{
	$this->fontSize += 6;
	$this->Font('bold');

		$this->Text($ccol, $this->company['coy_name']);
	
		$this->NewLine();
	//	$this->Text($ccol, get_location_name($this->formData['from_stk_loc']), $icol);
		$this->NewLine();
	$this->Font();
	$this->fontSize -= 6;
}
	/*	$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			$this->AddImage($logo, $ccol, $this->row, 0, 100);
		}
		else
		{
			$this->fontSize += 6;
			$this->Font('bold');
		//	$this->Text($ccol, $this->company['coy_name']."(".get_location_name//($this->formData['from_stk_loc']).")", $icol);
				
		//	$this->Text($ccol, $this->formData['from_stk_loc'], $icol);
			$this->Font();
			$this->fontSize -= 6;
		}
	*/
		//$this->NewLine(-2);
		// Document title
		//$this->NewLine();
		//$this->SetTextColor(190, 190, 190);
$this->NewLine(2);
		$this->fontSize += 5;
		$this->Font('bold');
/*		$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');*/
$loc=get_item_location($this->formData['from_stk_loc']);
$this->NewLine(-2);
		$this->Text($ccol,$loc['phone'] , $icol);
		$this->NewLine(-1);

		$this->Font();
		$this->fontSize -= 5;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

/*		$this->NewLine(3);
		$this->Font('bold');
$this->Text($ccol, _("Phone"), $c2col);
$this->Font('bold');
$this->NewLine(-3); */
		// Company data
	/*	$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		$this->Font('italic');
		$this->fontSize -= 1;
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
			//$this->SetTextColor(0, 0, 255);
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
			$this->Text($ccol, _("Domicile"), $c2col);
			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
		$this->fontSize += 1;
		$this->Font();
		$this->row = $adrline;
		$this->NewLine(3);
*/

		$this->NewLine();
		$this->fontSize += 1;
		$this->Font(b);
		$this->Text($ccol - 10 ,_("Customer Name:"));
		$this->Font();
		$this->Text($ccol + 62, $Addr2['name'], $icol);
		$this->fontSize -= 1;
		$this->NewLine();

		$this->fontSize += 1;
		$this->Font(b);
		$this->Text($ccol - 10, _("Date:"));
		$this->Font();
		$this->Text($ccol + 62, sql2date($this->formData['document_date'])." Time ".date("h:i:sa"));

		$this->NewLine();
		$this->Font(b);
		$this->Text($ccol- 10, $this->formData['document_name']);
		$this->Font();
		$this->Text($ccol + 62, $this->formData['document_number']);
		$this->NewLine();
		$this->Font(b);
		$this->Text($ccol- 10 , _("User:"));
		$this->Font();
		$this->Text($ccol + 62, $_SESSION["wa_current_user"]->username);

		$this->fontSize -= 1;

		if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
	//	$this->fontSize -= 4;
	//	$this->Text($ccol, $Addr1['title'], $icol);
	//	$this->Text($mcol, $Addr2['title']);
	//	$this->fontSize += 4;

		$this->NewLine(2);

// address1
		$temp = $this->row = $this->row - $this->lineHeight - 5;
//		$this->Text($ccol, $Addr1['name'], $icol);
		//$this->NewLine();
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);

// address2
/*
		$this->row = $temp;
		$this->Text($mcol, $Addr2['name']);
		$this->NewLine();
		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);
*/
		// Auxiliary document information
		$col = $this->leftMargin;
	/*	foreach($aux_info as $info_header => $info_content)
		{

			$this->row = $iline2 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
			$this->row = $iline3 - $this->lineHeight - 1;
			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
			$col += $width;
		} */
	/*	// Payment terms
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();
*/
		// Line headers

$this->NewLine(0.3);
$this->Text($ccol -10 , _("______________________________________________________"));
$this->NewLine(1.3);
$this->Text($ccol -10, _("________________________________________________________"));
		
		$this->SetFontSize(7);
	$this->headers = array( _("BARCODES"), _("ITEM"), _(""), _("QTY"), _("PRICE"), _("TOTAL"), _("DISC."), _("SUB-TOTAL"));
		
		$this->NewLine(-1.4);

	//$this->headers = array(_(""));



		$this->row = $iline2 - $this->lineHeight - 1;
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