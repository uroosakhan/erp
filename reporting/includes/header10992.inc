<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/

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

		$upper = $this->row + 4 * $this->lineHeight;
		$lower = $this->bottomMargin + 40 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 - 8 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 + 10 * $this->lineHeight;
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
//		for ($i = 0; $i < 5; $i++)
//		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
//		}
		$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
		if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
			$this->LineTo($this->cols[$cols - 2], $iline5 ,$this->cols[$cols - 2], $iline7);
		else	
//			$this->LineTo($this->cols[$cols - 2] + 4, $iline5 ,$this->cols[$cols - 2] + 4, $iline7);
		$this->LineTo($right, $iline5 ,$right, $iline7);

		// Company Logo
		$this->NewLine();
		$logo = company_path() . "/images/" . $this->company['coy_logo'];
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			$this->AddImage($logo, $ccol, $this->row, 0, 40);
            $this->NewLine(+36);
            $this->AddImage($logo, $ccol, $this->row, 0, 40);
            $this->NewLine(-36);
		}
		else
		{
			$this->fontSize += 4;
			$this->Font('bold');
            $this->Text($ccol, $this->company['coy_name'], $icol);
            $this->NewLine(+34);
			$this->Text($ccol, $this->company['coy_name'], $icol);
            $this->NewLine(-34);
			$this->Font();
			$this->fontSize -= 4;
		}
		// Document title
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
		$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, "Inventory Adjustment", 'right');
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
//		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
//		$this->Font('italic');
//		if (@$this->company['phone'])
//		{
//			$this->Text($ccol, _("Phone"), $c2col);
//			$this->Text($c2col, $this->company['phone'], $mcol);
//			$this->NewLine();
//		}
//		if (@$this->company['fax'])
//		{
//			$this->Text($ccol, _("Fax"), $c2col);
//			$this->Text($c2col, $this->company['fax'], $mcol);
//			$this->NewLine();
//		}
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
//		if (@$this->company['gst_no'])
//		{
//			$this->Text($ccol, _("Our VAT No."), $c2col);
//			$this->Text($c2col, $this->company['gst_no'], $mcol);
//			$this->NewLine();
//		}
//		if (@$this->formData['domicile'])
//		{
//			$this->Text($ccol, _("Domicile"), $c2col);
//			$this->Text($c2col, $this->company['domicile'], $mcol);
		$this->NewLine();
//		}

        $this->Text($ccol + 20, _("At Location:"));
        $this->Text($ccol + 70, $this->formData['location_name']);

        $this->Text($ccol + 180, _("Reference:"));
        $this->Text($ccol + 230, $this->formData['reference']);

        $this->Text($ccol + 310, _("Date:"));
        $this->Text($ccol + 340, sql2date($this->formData['tran_date']));
        $this->Font();
		$this->row = $adrline;
		$this->NewLine(33);
//


$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, "Inventory Adjustment", 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;
$this->NewLine();

$this->Text($ccol + 20, _("At Location:"));
$this->Text($ccol + 70, $this->formData['location_name']);

$this->Text($ccol + 180, _("Reference:"));
$this->Text($ccol + 230, $this->formData['reference']);

$this->Text($ccol + 310, _("Date:"));
$this->Text($ccol + 340, sql2date($this->formData['tran_date']));
$this->NewLine(-34);

$this->Font();
//		$this->NewLine();
//		$this->Text($mcol + 100, $this->formData['document_name']);
//		$this->Text($mcol + 180, $this->formData['document_number']);
//		$this->NewLine(2);
	
		if ($this->pageNumber > 1)
			$this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize -= 4;
//		$this->Text($ccol, $Addr1['title'], $icol);
//		$this->Text($mcol, $Addr2['title']);
		$this->fontSize += 4;
//
//// address1
//		$temp = $this->row = $this->row - $this->lineHeight - 5;
//		$this->Text($ccol, $Addr1['name'], $icol);
//		$this->NewLine();
//		$this->TextWrapLines($ccol, $icol - $ccol, $Addr1['address']);
//
//// address2
//		$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
//		$this->NewLine();
//		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address'], 'left', 0, 0, NULL, 1);

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
		$this->headers = array(_("Item Code"), _("Item Description"), _("Quantity"),
    _("Unit"), _("Unit Cost"));
		$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
		$count = count($this->headers);

		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);


$this->row = $iline5 - $this->lineHeight - 408;
$this->Font('bold');
$count = count($this->headers);

$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
	$this->TextCol($i, $i + 1, $this->headers[$i], -408);
		$this->Font();

		// Footer
		$this->Font('italic');
		$this->row = $iline7 - $this->lineHeight - 6;

//		foreach ($Footer as $line => $txt)
//		{
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

		//}
$this->MultiCell(520, 230, "", 1, 'L', 0, 2, 43, 500, true);
$this->MultiCell(520, 20, "", 1, 'L', 1, 2, 43, 500, true);


		$this->Font();
		$temp = $iline6 - $this->lineHeight - 2;