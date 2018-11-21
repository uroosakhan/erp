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
		$iline2 = $iline1 - 4 * $this->lineHeight;
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
		//$this->Line($iline1);
		//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
		//$this->Line($iline2);
		//$this->Line($iline3);
		//$this->Line($iline4);
		//$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));   
		//$this->Line($iline5);
		//$this->Line($iline6);
		//$this->Line($iline7);
		//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
		$col = $this->leftMargin;
		for ($i = 0; $i < 5; $i++)
		{
			//$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
		//$this->LineTo($right, $iline2 ,$right, $iline4);
		//$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
		//$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
		//$this->LineTo($right, $iline5 ,$right, $iline7);

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
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
$this->NewLine(+8);

		//$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
$this->Font('bold');


$this->Font();
$this->NewLine(-8);
$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		$this->Font('italic');
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
			//$this->Text($ccol, _("S.Tax Reg#"), $c2col);
			//$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
			$this->Text($ccol, _("Province"), $c2col);
			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}

		//$this->Text($ccol, _("NTN#  "));
		//$this->Text($c2col, '1460708-5');
                $this->NewLine();
		$this->Font();
		$this->row = $adrline;
		$this->NewLine(10);
		$this->Text($mcol + 100, _("Date"));
		$this->Text($mcol + 180, sql2date($this->formData['document_date']));

		$this->NewLine();
		$this->Text($mcol + 100, $this->formData['document_name']);
		$this->Text($mcol + 180, $this->formData['document_number']);
  $this->NewLine();


$this->Text($mcol + 100, _("Customer Ref:"));
$this->Text($mcol + 180, $this->formData['customer_ref']);
 

		if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize += 4;
$this->NewLine();
		$this->Text($ccol, $Addr1['title'], $icol);
		//$this->Text($mcol, $Addr2['title']);
		$this->fontSize -= 4;
$this->font('b');


//$this->MultiCell(150, 150, "QUOTATION", 1, 'L', 0, 2, 230,150, true);
$this->fontSize += 10;
$this->NewLine(-1);
$this->Text(230,  "QUOTATION", 110);
$this->NewLine(+1);
$this->fontSize -= 10;
$this->font('');
// address1
		$temp = $this->row = $this->row - $this->lineHeight - 5;
		$this->Text($ccol, $Addr1['name'], $icol);
		$this->NewLine();
		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
$this->font('');
		$this->TextWrapLines($ccol, $icol - $ccol, "Tel:  ".get_phone_($this->formData['debtor_no']));
$this->font('b');
$this->Text($mcol - 257, "Attn:");
$this->font('U');
$this->Text($mcol - 230, $this->formData['attn']);
$this->font('');

$this->NewLine();


// address2
		$this->row = $temp;
		//$this->Text($mcol, $Addr2['name']);
//		$this->NewLine();
		//$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address']);

		// Auxiliary document information
		$col = $this->leftMargin;
		foreach($aux_info as $info_header => $info_content)
		{

			$this->row = $iline2 - $this->lineHeight - 1;
			//$this->TextWrap($col, $this->row, $width, $info_header, 'C');
			$this->row = $iline3 - $this->lineHeight - 1;
		//	$this->TextWrap($col, $this->row, $width, $info_content, 'C');
			$col += $width;
		}
		// Payment terms
//		$this->row -= (2 * $this->lineHeight);
//		$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
//		$this->Font();
//$this->MultiCell(250, 20, "NTN#  1460708-5", 0, 'L', 0, 2, 45,140, true);
//$this->newline();
$this->Font('bold');

$this->setfontsize(12);

$this->MultiCell(250, 20, "Terms:", 0, 'L', 0, 2, 45,708, true);
$this->Font('');
$this->setfontsize(10);
$this->MultiCell(250, 20,$this->formData['comments'], 0, 'L', 0, 2, 45,720, true);
//$this->MultiCell(250, 20,"Payment: Against Delivery", 0, 'L', 0, 2, 45,720, true);
//$this->MultiCell(250, 20,"Availability: Prompt/ Subject to availability", 0, 'L', 0, 2, 45,732, true);
//$this->MultiCell(250, 20,"Validity:".get_payment_terms_name($this->formData['payment_terms']), 0, 'L', 0, 2, 45,744, true);
$this->Font('bold');


$this->Font('');
//$this->MultiCell(250, 20,$this->formData['comments']."12" , 0, 'L', 0, 2, 45,730, true);



$this->Font('');
function get_payment_terms_name($selected_id)
{
	$sql = "SELECT  terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

	$result = db_query($sql,"could not get payment term");
        $row =db_fetch_row($result);
	return $row[0];
}
//$this->MultiCell(250, 20, "Payment terms: ".get_payment_terms_name($this->formData['payment_terms']), 0, 'L', 0, 2, 45,770, true);
//$this->MultiCell(250, 20, "Delivery: Prompt", 0, 'L', 0, 2, 45,783, true);
//$this->MultiCell(250, 20, "Validity: Seven days", 0, 'L', 0, 2, 45,798, true);

$this->font('b');
$this->MultiCell(300, 20,"Authorized Signature & Stamp", 0, 'C', 0, 2, 150,800, true);
//$this->MultiCell(300, 20,"This is system generated quotation & does not require signature", 0, 'C', 0, 2, 150,800, true);

$this->setfontsize(10);
//$this->MultiCell(300, 20, $_SESSION["wa_current_user"]->username, 0, 'L', 0, 2, 45,800, true);
$this->setfontsize(10);
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
//--------top right box-----------

$this->MultiCell(185, 35, "" , 1, 'C', 0, 2, 379,42, true);
$this->font('B');

$this->MultiCell(180, 150, "Authorized Industrial Distributor " , 0, 'L', 0, 2, 400,44, true);
$this->MultiCell(170, 150, "Total Parco Pakistan LTD" , 0, 'L', 0, 2, 410,59, true);
		$this->Font();
		$temp = $iline6 - $this->lineHeight - 2;
?>
