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
		$lower = $this->bottomMargin + 9 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 +4 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 4 * $this->lineHeight;
		$iline5 = $iline4 - 5 * $this->lineHeight;
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
		//$this->Line($iline1,3);
		$this->SetDrawColor(128, 128, 128);
		///$this->Line($iline1);
		//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
		//$this->Line($iline2);
		//$this->Line($iline3);
		//$this->Line($iline4);
		//$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
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
//		if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
//			$this->LineTo($this->cols[$cols - 2], $iline5 ,$this->cols[$cols - 2], $iline7);
//		else
$this->LineTo($this->cols[$cols - 1] + 4, $iline5 ,$this->cols[$cols - 1] + 4, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine(3);
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			$this->AddImage($logo, 480, $this->row, 0, 40);
		}
		else
		{
			$this->fontSize += 4;
			$this->Font('bold');
			$this->Text($ccol-6, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 4;
		}
		// Document title
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
		//$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
		//$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		//$this->Font('italic');
$this->NewLine(2.5);
//		if (@$this->company['phone'])
//		{
//			$this->NewLine(2.5);
//			$this->Text($ccol, _("Tel No:"), $c2col);
//			$this->Text($c2col -8, $this->company['phone'], $mcol);
//			$this->NewLine(-8);
//			$this->NewLine();
//		}
		if (@$this->company['fax'])
		{
			$this->Text($ccol, _("Fax"), $c2col);
			$this->Text($c2col, $this->company['fax'], $mcol);
			$this->NewLine();
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
		if (@$this->company['gst_no'])
		{
			$this->Text($ccol, _("Our VAT No."), $c2col);
			$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
			$this->Text($ccol, _("Domicile"), $c2col);
			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
		$this->Font();
		$this->row = $adrline;
		$this->NewLine(3.2);
		//$this->Text($mcol - 255, _("Date:"));
		//$this->Text($mcol - 209, sql2date($this->formData['document_date']));



		$this->NewLine();
		//$this->Text($mcol - 255, $this->formData['document_name']);
		//$this->Text($mcol - 470, $this->formData['document_number']);
		$this->NewLine(2);

		if ($this->pageNumber > 1)
			$this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		//$this->fontSize -= 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
		//$this->fontSize += 4;
$this->SetFontSize(12);
$this->MultiCell(250, 188,$this->company['postal_address'], 0, 'L', 0, 2, 45,55, true); // 3
$this->MultiCell(250, 188,"Phone:", 0, 'L', 0, 2, 45,85, true); // 3
$this->MultiCell(250, 188,$this->company['phone'], 0, 'L', 0, 2, 85,85, true); // 3

function get_location1100($loc_code)
{
	$sql = "SELECT location_name FROM ".TB_PREF."locations WHERE loc_code=".db_escape($loc_code);
	$result = db_query($sql,"Customer Record Retreive");
	$row = db_fetch_row($result);
	return $row[0];
	display_db_error("could not retreive the location name for $loc_code", $sql, true);
}
function get_customer_name12($customer_id)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_purchase_cart1()
{
    $sql = "SELECT `po_enable`  FROM `0_purch_pref` WHERE `po_enable`=1 AND `name`='cart1'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
// address1
		$temp = $this->row = $this->row - $this->lineHeight - 5;
		//$this->NewLine(-4.8);
		//$this->Text($mcol - 255, _("Customer:"));
		//$this->Text($ccol +50, $Addr1['name'], $icol);
		//$this->NewLine(1);
		//$this->Text($mcol - 255, _("Address:"));
		//$this->TextWrapLines($ccol +50, $icol - $ccol, $Addr1['address']);
//palwasha
$this->SetFontSize(10);
$this->Font('b');
$this->MultiCell(250, 188,"Customer", 0, 'L', 0, 2, 45,130, true); // 3
$this->MultiCell(770, 30, "Delivery Challan no:", 0, 'L', 0,1,367,100, true);
$this->MultiCell(250, 188,"Misc", 0, 'L', 0, 2, 360,130, true); // 3
$this->MultiCell(250, 188,"Name:", 0, 'L', 0, 2, 45,150, true); // 3
$this->MultiCell(250, 188,"Date:", 0, 'L', 0, 2, 445,150, true); // 3
$this->MultiCell(790, 2,"______________________________________________________________________________________________", 0, 'L', 0, 2, 45,110, true);
$this->MultiCell(250, 188,"Address:", 0, 'L', 0, 2, 45,163, true); // 3
$this->MultiCell(250, 188,"Phone:", 0, 'L', 0, 2, 45,205, true);
$this->MultiCell(250, 188,"City:", 0, 'L', 0, 2, 45,190, true); // 3
$this->MultiCell(250, 188,"Location", 0, 'L', 0, 2, 445,163, true);
$this->MultiCell(250, 188,"S.O No", 0, 'L', 0, 2, 445,180, true);
$this->MultiCell(250, 188,"P.O No", 0, 'L', 0, 2, 445,210, true);
$this->Font('');

$this->SetFontSize(9);
$sales_order = get_sales_order_header($this->formData['order_'], ST_SALESORDER);



$this->MultiCell(250, 188,"".sql2date($this->formData['document_date']), 0, 'L', 0, 2, 495,150, true); // 3
$this->MultiCell(770, 30, "". $this->formData['document_number'], 0, 'L', 0,1,490,100, true);
$this->MultiCell(300, 188,"".get_customer_address($this->formData['trans_no']), 0, 'L', 0, 2, 105,163, true); // 3
$this->MultiCell(250, 188,"".get_customer_name12($this->formData['trans_no']), 0, 'L', 0, 2, 105,150, true); // 3

$get_phone = get_phoneno_for_crm_persons($this->formData['debtor_no']);

$this->MultiCell(250, 188,"".get_location1100($sales_order['from_stk_loc']), 0, 'L', 0, 2, 495,163, true);
$this->MultiCell(250, 188,"".$sales_order['reference'], 0, 'L', 0, 2, 495,180, true);
$this->MultiCell(250, 188,"".$get_phone['phone'], 0, 'L', 0, 2, 105,205, true);
$this->MultiCell(250, 188,"", 0, 'L', 0, 2, 495,210, true);
// address2
//		$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
//		$this->NewLine();
//		$this->TextWrapLines($mcol,  $this->rightMargin - $mcol, $Addr2['address'], 'left', 0, 0, NULL, 1);

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
//		$this->row -= (2 * $this->lineHeight);
//		$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
//		$this->Font();

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
