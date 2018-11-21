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
		$lower = $this->bottomMargin + 24 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 - 8 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 + 8 * $this->lineHeight;
		$iline6 = $iline5 - 2.5 * $this->lineHeight;
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
		$this->SetDrawColor(128, 128, 128);
		$this->Line($iline5);
		$this->Line($iline6);
		$this->Line($iline7);

		$col = $this->leftMargin;
		for ($i = 0; $i < 5; $i++)
		{
			//$this->LineTo($col += $width, $iline2,$col, $iline4);
		}
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
		$this->LineTo($this->cols[$cols - 1] + 2, $iline5 ,$this->cols[$cols - 1] + 2, $iline7);
		$this->LineTo($this->cols[$cols - 2] + 2, $iline5 ,$this->cols[$cols - 2] + 2, $iline7);
		$this->LineTo($this->cols[$cols - 3] + 2, $iline5 ,$this->cols[$cols - 3] + 2, $iline7);
		$this->LineTo($this->cols[$cols - 4] + 2, $iline5 ,$this->cols[$cols - 4] + 2, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine();
			$this->fontSize += 10;
			$this->Font('bold');
			$this->Text($mcol - 200, $this->company['coy_name']);
			$this->Font();
			$this->fontSize -= 10;
//		}
		// Document title
		$this->fontSize += 12;
		$this->Font('bold');
		$this->NewLine(+7);
		$this->TextWrap($mcol-150, $this->row, $this->pageWidth - $this->rightMargin - $mcol , _("SALES CONTRACT"), 'center');
		$this->Font();
		$this->NewLine(-7);
		$this->fontSize -= 12;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
		$this->TextWrapLines($ccol + 80, $icol, $this->company['postal_address']);
		$this->Font('italic');
		if (@$this->company['phone'])
		{
		//	$this->Text($ccol + 120, _("Tel:"), $c2col);
		//	$this->Text($c2col + 80, $this->company['phone'], $mcol);
		}
		if (@$this->company['fax'])
		{
		//	$this->Text($ccol + 310, _("Fax:"), $c2col);
		//	$this->Text($c2col + 275, $this->company['fax'], $mcol);
			$this->NewLine();
		}

$this->Font('bold');
$this->NewLine(+2);
$this->Text($mcol + 120, _("Invoice No:"));
$this->Text($mcol + 180, $this->formData['reference']);
$this->NewLine(-2);
$this->Font('');

$this->NewLine(+3);
$this->Font('bold');
$this->Text($mcol + 120, _("PAYMENT:"));
$this->Text($mcol + 180, $this->formData['']);
$this->NewLine(-3);
$this->Font('');

$this->NewLine(+4);
$this->Font('bold');
$this->Text($mcol + 120, _("Date:"));
$this->Text($mcol + 180, $this->formData['document_date']);
$this->NewLine(-4);
$this->Font('');

$this->Font('bold');
$this->MultiCell(25, 10,"TO: " , 0, 'L', 0, 2, 50,170, true);
$this->Font('');
//$this->MultiCell(300, 10,$this->company['coy_name'] , 0, 'L', 0, 2, 73,170, true);



		// Auxiliary document information
		$col = $this->leftMargin;

		// Payment terms
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
	//	$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();

		// Line headers
$this->headers = array(_('MARKS AND'), _('DESCRIPTION OF GOODS'), _('QUANTITY'), _('UNIT PRICE'), _('AMOUNT'));

		$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
		$count = count($this->headers);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);

$this->NewLine();
$this->headers2 = array(_('NUMBERS'), _(''), _(''), _(''), _(''));

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
$this->Font('b');

function get_payment_terms_name($selected_id)
{
	$sql = "SELECT  terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

	$result = db_query($sql,"could not get payment term");
	$row =db_fetch_row($result);
	return $row[0];
}
$this->Font('');


$this->Font('B');
$this->MultiCell(100, 10, "BANK DETAIL:" , 0, 'L', 0, 2, 40,552, true);
$this->MultiCell(100, 10, "_____________" , 0, 'L', 0, 2, 40,553, true);
$this->MultiCell(220, 35, "ACCOUNT NAME :"."      ".$this->formData['bank_account_name'] , 0, 'L', 0, 2, 40,570, true);
$this->MultiCell(220, 35, "ACCOUNT NUMBER :"."      ".$this->formData['bank_account_number'] , 0, 'L', 0, 2, 40,585, true);
$this->MultiCell(220, 35, "BANK NAME :"."     ".$this->formData['bank_name'] , 0, 'L', 0, 2, 40,600, true);
$this->MultiCell(220, 35, "INTERMEDIARY BANK :" , 0, 'L', 0, 2, 40,615, true);
$this->MultiCell(220, 35, "SWIFT CODE :"."     ".$this->formData['swift_code'] , 0, 'L', 0, 2, 40,630, true);

$this->MultiCell(100, 10, "TERMS OF PAYMENT :" , 0, 'L', 0, 2, 40,660, true);
$this->Font('');
$this->MultiCell(400, 30, $this->formData['term_cond'] , 0, 'L', 0, 2, 140,660, true);




//--------top right box-----------

		$temp = $iline6 - $this->lineHeight - 2;
?>
