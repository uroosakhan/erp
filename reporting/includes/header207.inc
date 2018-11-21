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
$lower = $this->bottomMargin + 10 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 + 3 * $this->lineHeight;
$iline6 = $iline5 + 5.5 * $this->lineHeight;
$iline9 = $iline5 + 9 * $this->lineHeight;
$iline8 = $iline5 - 3 * $this->lineHeight;
$iline10 = 37.3 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin) / 6;
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
//$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
//$this->Line($iline5);
$this->Line($iline9);
//		$this->Line($iline8);
$this->Line($iline6);
//$this->Line($iline7);
//$this->Line(37.3 * $this->lineHeight);
$this->font('b');
$this->setfontsize(9);
$this->multicell(172,15,"PRODUCT",1,'C',0,0,40,132);
$this->multicell(90,15,"SALES TAX",1,'C',0,0,402,132);
$this->multicell(180,15,"LAST PURCHASE",1,'C',0,0,562,132);

$this->font('');
//		$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
$col = $this->leftMargin;
for ($i = 0; $i < 6; $i++)
{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
}
//		$this->LineTo($right, $iline2 ,$right, $iline4);
//$this->LineTo($this->leftMargin, $iline9 ,$this->leftMargin, $iline7);

//$this->LineTo($this->cols[$cols - 1] -2, $iline9 ,$this->cols[$cols - 1] -2, $iline7);
//$this->LineTo($this->cols[$cols - 2] -2, $iline9 ,$this->cols[$cols - 2] -2, $iline7);
//$this->LineTo($this->cols[$cols - 3] -2, $iline9 ,$this->cols[$cols - 3] -2, $iline7);
//$this->LineTo($this->cols[$cols - 4] -2, $iline9 ,$this->cols[$cols - 4] -2, $iline7);
//$this->LineTo($this->cols[$cols - 5] -2, $iline9 ,$this->cols[$cols - 5] -2, $iline7);
//$this->LineTo($this->cols[$cols - 6] -2, $iline9 ,$this->cols[$cols - 6] -2, $iline7);
//$this->LineTo($this->cols[$cols - 7] -2, $iline9 ,$this->cols[$cols - 7] -2, $iline7);
//$this->LineTo($this->cols[$cols - 8] -2, $iline9 ,$this->cols[$cols - 8] -2, $iline7);
//$this->LineTo($this->cols[$cols - 9] -2, $iline9 ,$this->cols[$cols - 9] -2, $iline7);
//$this->LineTo($this->cols[$cols - 10] -2, $iline9 ,$this->cols[$cols - 10] -2, $iline7);
//$this->LineTo($this->cols[$cols - 11] -2, $iline9 ,$this->cols[$cols - 11] -2, $iline7);
//$this->LineTo($this->cols[$cols - 12] -2, $iline9 ,$this->cols[$cols - 12] -2, $iline7);
//$this->LineTo($right, $iline9 ,$right, $iline7);
//$this->LineTo($right-70, $iline6 ,$right-70, $iline7);
//$this->LineTo($right-140, $iline6 ,$right-140, $iline7);
//$this->LineTo($right-200, $iline6 ,$right-200, $iline7);
//$this->LineTo($right-250, $iline6 ,$right-250, $iline7);
//$this->LineTo($right-320, $iline6 ,$right-320, $iline7);
//$this->LineTo($right-370, $iline6 ,$right-370, $iline7);
//$this->LineTo($right-410, $iline6 ,$right-410, $iline7);
//$this->LineTo($right-470, $iline6 ,$right-470, $iline7);
//$this->LineTo($right-520, $iline6 ,$right-520, $iline7);
//$this->LineTo($right-550, $iline6 ,$right-550, $iline7);
//$this->LineTo($right-600, $iline6 ,$right-600, $iline7);
//$this->LineTo($right-725, $iline6 ,$right-725, $iline7);
//$this->LineTo($right-725, $iline10 ,$right-725, $iline7);
//$this->LineTo($right-370, $iline10 ,$right-370, $iline7);
//$this->LineTo($right-140, $iline10 ,$right-140, $iline7);
//$this->LineTo($right-200, $iline10 ,$right-200, $iline7);
//$this->Font('b');
//$this->MultiCell(150,15,"Terms and Conditions:",0,'L',0,0,45,718);
//$this->Font('');
//$this->SetFontSize(9);
//$this->MultiCell(500,58,$this->formData['term_cond'],0,'L',0,0,45,733);
//$this->MultiCell(500,58,"______________________",0,'L',0,0,45,810);
//$this->MultiCell(500,58,"______________________",0,'L',0,0,420,810);
//$this->MultiCell(500,58,"Prepared By",0,'L',0,0,70,824);
//$this->MultiCell(500,58,"Approved By",0,'L',0,0,455,824);
//$this->MultiCell(500,58,$this->formData['term_cond'],1,'L',0,0,45,733);
$this->SetFontSize(9);


//$this->MultiCell(100,13,$this->formData['requisition_id'],0,'L',0,0,150,100);


//$this->MultiCell(110,10,"Supplier Contact Person:",0,'L',0,0,47,250);
//$this->MultiCell(180,15,"",0,'L',0,0,165,252);
//$this->MultiCell(120,10,"Supplier Contact Person Ph:",0,'L',0,0,47,280);
//$this->MultiCell(180,15,"",0,'L',0,0,165,280);

//$this->MultiCell(100,13,$gst_nos["application_date"],0,'L',0,0,550,100);

//$this->MultiCell(130,60,$gst_nos['narrative']."26",0,'L',0,0,570,115);
//$this->MultiCell(110,10,"Sales Tax Reg. No.",0,'L',0,0,350,230);
//$this->MultiCell(110,15,$gst_no["gst_no"],0,'L',0,0,460,230);
//$this->MultiCell(110,10,"NTN No.",0,'L',0,0,350,250);
//$this->MultiCell(140,15,$gst_no["ntn_no"],0,'L',0,0,420,250);
//
$gst_nos = get_pr_header($this->formData['requisition_id']);
$this->font('b');
$this->MultiCell(170,10,"P.R.#"."                                ". $this->formData['requisition_id'],0,'L',0,0,47,75);
$this->MultiCell(90,10,"PARTY A/C.",0,'L',0,0,47,90);
$this->MultiCell(190,13,$this->formData['supplier_id'],0,'L',0,0,150,90);

$this->font('');
$this->MultiCell(300,10,"REMARKS :".htmlspecialchars_decode($gst_nos['narrative']),0,'L',0,0,500,75);
$this->MultiCell(300,10,"DATE :"."        ".sql2date($gst_nos['application_date']),0,'L',0,0,500,90);
$this->MultiCell(90,10,"PARTY NAME:",0,'L',0,0,47,105);
$this->MultiCell(180,10,htmlspecialchars_decode($this->formData['supp_name']),0,'L',0,0,148,105);



// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
$this->AliasNbPages();
$str = _("                Page") . '  ' . $this->pageNumber . '/' . $this->getAliasNbPages();
$this->Text($str, $str, 0, 0, 0, 'right', 0, 0, NULL, 1);
if ($this->company['coy_logo'] != '' && file_exists($logo))
{
	$this->AddImage($logo, $ccol, $this->row, 0, 40);
}
//else
//{
$this->fontSize += 10;
$this->Font('bold');
$this->Text($ccol +250, $this->company['coy_name'], $icol +160);
$this->Font();
$this->fontSize -=10;
//}
// Document title
$this->SetTextColor(0,0,0);
$this->fontSize += 10;
//		$this->Font('bold');
$this->NewLine(+2);
$this->TextWrap($ccol +137, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20,_("PURCHASE REQUISITION"), 'right');
//		$this->Font();
$this->fontSize -= 10;
$this->NewLine(-2);
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
//		$this->TextWrapLines($ccol+130, $icol +100, $this->company['postal_address']);
$this->Font('italic');
if (@$this->company['phone'])
{
//			$this->Text($ccol +180, _("Phone"), $c2col );
//			$this->Text($c2col+150, $this->company['phone'], $mcol);
	//$this->NewLine();
}
if (@$this->company['fax'])
{
//			$this->Text($ccol +280, _("Fax"), $c2col);
//			$this->Text($c2col+240 , $this->company['fax'], $mcol);
	$this->NewLine();
}
//		if (@$this->company['email'])
//		{
//			$this->Text($ccol, _("Email"), $c2col);
//
//			$url = "mailto:" . $this->company['email'];
//			$this->SetTextColor(0, 0, 255);
//			$this->Text($c2col, $this->company['email'], $mcol);
//			$this->SetTextColor(0, 0, 0);
//			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);
//
//			$this->NewLine();
//		}
if (@$this->company['gst_no'])
{
//			$this->Text($ccol +160, _("Our GST No."), $c2col);
//			$this->Text($c2col +160, $this->company['gst_no'], $mcol);
//			$this->NewLine();
}
if (@$this->company['coy_no'])
{
//			$this->Text($ccol +280, _("NTN No."), $c2col);
//			$this->Text($c2col +260, $this->company['coy_no'], $mcol);
	$this->NewLine();
}
//		if (@$this->formData['domicile'])
//		{
//			$this->Text($ccol, _("Province"), $c2col);
//			$this->Text($c2col, $this->company['domicile'], $mcol);
//			$this->NewLine();
//		}
$this->Font();
$this->row = $adrline;
$this->NewLine(7);

//$this->Text($ccol +2 , $this->formData['document_name']);
//$this->Text($ccol +118 , $this->formData['document_number']);
//$this->NewLine();
//		$this->Text($ccol +2, _("P.O.Date"));
//		$this->Text($ccol +118 , sql2date($this->formData['document_date']));



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
//$this->NewLine();
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
//$this->Text($ccol,"GST: ". $this->formData['tax_id'], $icol);
//$this->NewLine();
//$this->Text($ccol,"NTN: ". $this->formData['ntn_id'], $icol);


// address2
$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
//$this->NewLine();
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

// Line headers
$this->Font('bold');
$this->headers = array(_(""),_(""), _("QTY"),
	_("UOM"), _("RATE"), 	_("GROSS"), _(" "), _("          "), _("NET"), _(""),_(""),_(""), _("BALANCE"));
$this->row = $iline9 - $this->lineHeight - 1;
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();
// Line headers2
$this->row = $iline9 - $this->lineHeight - 15;
$this->Font('bold');
$this->headers2 = array(_("CODE"), _("NAME"),_(""),_(""),_(""),_("AMOUNT"),
	_("%       "), _("AMOUNT"), _("AMOUNT"), 	_("QTY"), _("RATE"), _("DATE      "), _("STOCK"));
$count = count($this->headers2);
$this->cols[$count] = $right - 1;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers2[$i], -1);
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
