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
		$this->LineTo($this->cols[$cols - 1] + 4, $iline5 ,$this->cols[$cols - 1] + 4, $iline7);
				$this->LineTo($this->cols[$cols - 1] - 65, $iline5 ,$this->cols[$cols - 1] - 65, $iline7);
		$this->LineTo($this->cols[$cols - 1] -412, $iline5 ,$this->cols[$cols - 1] -412, $iline7);
		$this->LineTo($this->cols[$cols - 1] -434, $iline5 ,$this->cols[$cols - 1] -434, $iline7);

		$this->LineTo($this->cols[$cols - 1] -385, $iline5 ,$this->cols[$cols - 1] -385, $iline7);

		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine();
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
		    $this->NewLine();
			$this->AddImage($logo, $ccol+355, $this->row,$this->company['logo_w'], $this->company['logo_h']);
		}
		else
		{
			$this->fontSize += 10;
			$this->Font('bold');
			$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 10;
		}
// 		$chk_mark = company_path() . "/images/" .'bullet.png';
// 			$this->AddImage($chk_mark, 45, 78, 0, 4);
// // 			$this->AddImage($chk_mark, 45, 64, 0, 4);
// 			$this->AddImage($chk_mark, 45, 92, 0, 4);
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
		$this->TextWrapLines($ccol+355, $icol, $this->company['postal_address']);
		$this->Font('italic');
		if (@$this->company['phone'])
		{
			$this->Text($ccol+355, _("Phone"), $c2col);
			$this->Text($c2col+370, $this->company['phone']);
			$this->NewLine();
		}
		if (@$this->company['fax'])
		{
			$this->Text($ccol+355, _("Fax"), $c2col);
			$this->Text($c2col+370, $this->company['fax']);
			$this->NewLine();
		}
		if (@$this->company['email'])
		{
			$this->Text($ccol+355, _("Email"), $c2col);

			$url = "mailto:" . $this->company['email'];
			$this->SetTextColor(0, 0, 255);
			$this->Text($c2col+370, $this->company['email']);
			$this->SetTextColor(0, 0, 0);
			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

			$this->NewLine();
		}
		if (@$this->company['gst_no'])
		{
// 			$this->Text($ccol+355, _("S.Tax Reg#"), $c2col);
// 			$this->Text($c2col+370, $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
// 			$this->Text($ccol+355, _("Province"), $c2col);
// 			$this->Text($c2col+370, $this->company['domicile'], $mcol);
			$this->NewLine();
		}

// 		$this->Text($ccol+355, _("NTN#  "));
// 		$this->Text($c2col+370, $this->company['coy_no']);
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
              // $this->Text($mcol + 100, "Customer Order No");
		//$this->Text($mcol + 180, $this->formData['document_number']);
           //$this->NewLine();


$this->Text($mcol + 100, _("P.O #"));
$this->Text($mcol + 180, $this->formData['h_text2']);
   $this->NewLine();
$this->Text($mcol + 100, _("P.O Date"));
$this->Text($mcol + 180, sql2date($this->formData['po_date']));

		if ($this->pageNumber > 1 && !strstr($this->filename, "Bulk"))
			$this->Text($this->endLine - 35, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize += 4;
$this->NewLine(-1);
		$this->Text($ccol, $Addr1['title'], $icol);
		//$this->Text($mcol, $Addr2['title']);
		$this->fontSize -= 4;
$this->font('b');


//$this->MultiCell(150, 150, "QUOTATION", 1, 'L', 0, 2, 230,150, true);
$this->fontSize += 10;
$this->NewLine(-3);
$this->Text(230,  $this->title, 110);
$this->NewLine(+3);
$this->fontSize -= 10;
$this->font('');
// address1
		$temp = $this->row = $this->row - $this->lineHeight - 5;
		$this->Text($ccol, $Addr1['name'], $icol);
		$this->NewLine();
		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
$this->font('');
		$this->TextWrapLines($ccol, $icol - $ccol, "Tel:  ".get_phone_1($this->formData['debtor_no']));
// $this->font('b');
// $this->fontSize += 2;
// $this->NewLine(+0.5);
// $this->Text($mcol - 257, "Kind Attention:");
// $this->font('U');
// $this->Text($mcol - 170, $this->formData['f_text1']);
// //$this->font('');

// $this->NewLine();
// $this->font('b');
// $this->Text($mcol - 257, "Subject:");
// $this->font('U');
// $this->Text($mcol - 170, $this->formData['f_text4']);
// $this->font('');
// $this->NewLine(-0.5);
// $this->fontSize -= 2;
// $this->NewLine();


// address2
		$this->row = $temp;
		//$this->Text($mcol, $Addr2['name']);
		$this->NewLine();
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
$this->newline();
$this->Font('bold');

$this->fontSize -= 11;
// $this->setfontsize(14);
// $this->MultiCell(250, 20, "Note:", 0, 'L', 0, 2, 45,693, true);
// $this->Font('');
// $this->setfontsize(10);
// $this->MultiCell(800, 20,"".$this->formData['comments'] , 0, 'L', 0, 2, 90,696, true);
// 

// $this->MultiCell(250, 20, "Term and Conditions:", 0, 'L', 0, 2, 45,722, true);
// $this->MultiCell(250, 20, "_________________", 0, 'L', 0, 2, 45,722, true);
// $this->Font('');
// $this->setfontsize(10);
// $this->MultiCell(800, 20,"Payment Terms:       ".$this->formData['f_text2'] , 0, 'L', 0, 2, 55,742, true);
// $this->MultiCell(800, 20,"Quotation Validity:    ".$this->formData['f_text3'] , 0, 'L', 0, 2, 55,756, true);
// $this->MultiCell(800, 20,"Delivery Time:          ".$this->formData['f_text5'] , 0, 'L', 0, 2, 55,770, true);



$this->fontSize += 11;

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

//$this->MultiCell(300, 20,"This is system generated quotation & does not require signature", 0, 'C', 0, 2, 150,820, true);
//$this->MultiCell(300, 20, $_SESSION["wa_current_user"]->username, 0, 'L', 0, 2, 45,800, true);

		// Line headers
$this->headers = array( _("S.No"),_("Qty"),_("Unit"),_("Product Description"), _("Unit Price (Rs.)")
, _("Total (Rs.)"));

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

//$this->MultiCell(185, 35, "" , 1, 'C', 0, 2, 379,42, true);
$this->font('b');

$this->MultiCell(170, 150, "________________________", 0, 'L', 0, 2, 420,748, true);
$this->MultiCell(170, 150, "For :  ", 0, 'L', 0, 2, 420,760, true);
$this->fontSize += 2;
$this->NewLine(+5);
$this->Text($ccol+400, $this->company['coy_name'], $icol);
$this->NewLine(-5);
$this->fontSize -= 2;
		$this->Font();
		$temp = $iline6 - $this->lineHeight - 2;
?>
