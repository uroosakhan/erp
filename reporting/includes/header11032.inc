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

		$upper = $this->row + 7 * $this->lineHeight;
		$lower = $this->bottomMargin + 8 * $this->lineHeight;
		$iline1 = $upper - 7.5 * $this->lineHeight;
		$iline2 = $iline1 - 8 * $this->lineHeight;
		$iline3 = $iline2 - 1.5 * $this->lineHeight;
		$iline4 = $iline3 - 1.5 * $this->lineHeight;
		$iline5 = $iline4 - 3 * $this->lineHeight;
		$iline6 = $iline5 - 1.5 * $this->lineHeight;
		$iline7 = $lower;
		$right = $this->pageWidth - $this->rightMargin;
		$width = ($right - $this->leftMargin) / 4;
		$icol = $this->pageWidth / 2;
		$ccol = $this->cols[0] + 4;
		$c2col = $ccol + 60;
		$ccol2 = $icol / 2;
		$mcol = $icol + 8;
		$mcol2 = $this->pageWidth - $ccol2;
		$cols = count($this->cols);
		$this->SetDrawColor(205, 205, 205);

		$this->SetDrawColor(128, 128, 128);

		$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
		$this->Line($iline5);

		$col = $this->leftMargin;
		for ($i = 0; $i < 4; $i++)
		{
//			$this->LineTo($col += $width, $iline2,$col, $iline4);
		}

        $this->NewLine(-1);
		$this->AliasNbPages();
		$str = _("Page") . ' ' . $this->pageNumber . '/' . $this->getAliasNbPages();
		$this->Text($str, $str, 0, 0, 0, 'right', 0, 0, NULL, 1);
		$this->NewLine(+1);
		
		// Company Logo
		$this->NewLine();
//		$logo = company_path() . "/images/" . $this->company['coy_logo'];
//$imageheader = company_path() . '/images/Footer.png';

// 		global $SysPrefs;

//			$this->NewLine(-55);
//$this->NewLine(4);
		if ($this->company['coy_logo'] != '' && file_exists($logo))
		{
			$this->NewLine(+5);
//			$this->AddImage($logo, $ccol+400, $this->row, 0, 60);
//			$this->AddImage($imageheader, $ccol+1, 10, 0, 25);
 	//if ($this->row - $SysPrefs->pic_height < $this->bottomMargin)
				// $this->NewPage();
//				 $rep->AddImage($imageheader,  40, 10, 0, 10);
		}
		else
		{
			$this->fontSize += 4;
			$this->Font('bold');
//			$this->Text($ccol, $this->company['coy_name'], $icol);
			$this->Font();
			$this->fontSize -= 4;
		}
        $this->NewLine(-5);
		// Document title
		$this->SetTextColor(190, 190, 190);
		$this->fontSize += 10;
		$this->Font('bold');
//		$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'Left');
		$this->Font();
		$this->fontSize -= 10;
		$this->NewLine();
		$this->SetTextColor(0, 0, 0);
		$adrline = $this->row;

		// Company data
//		$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
		$this->Font('italic');
		if (@$this->company['phone'])
		{
//			$this->Text($ccol, _("Phone"), $c2col);
//			$this->Text($c2col, $this->company['phone'], $mcol);
			$this->NewLine();
		}
		if (@$this->company['fax'])
		{
//			$this->Text($ccol, _("Fax"), $c2col);
//			$this->Text($c2col, $this->company['fax'], $mcol);
			$this->NewLine();
		}
		if (@$this->company['email'])
		{

			$this->SetTextColor(0, 0, 0);
			$this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

			$this->NewLine();
		}
		if (@$this->company['gst_no'])
		{
//			$this->Text($ccol, _("Our VAT No."), $c2col);
//			$this->Text($c2col, $this->company['gst_no'], $mcol);
			$this->NewLine();
		}
		if (@$this->formData['domicile'])
		{
//			$this->Text($ccol, _("Domicile"), $c2col);
//			$this->Text($c2col, $this->company['domicile'], $mcol);
			$this->NewLine();
		}
		$this->Font();

		$this->row = $adrline;

		if ($this->pageNumber > 1)
			$this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
		$this->row = $iline1 - $this->lineHeight;
		$this->fontSize += 9;
$this->NewLine(+4);
$this->Font('b');
        $this->Text($ccol - 8, "DELIVERY CHALLAN", $icol);
$this->NewLine(-4);
		$this->fontSize -= 9;
$this->NewLine(+9);
	$this->fontSize +=3;
		$temp = $this->row = $this->row - $this->lineHeight - 5;
		$this->NewLine();
			$this->fontSize -= 3;

// address2
	$this->fontSize += 3;
		$this->row = $temp;
//		$this->Text($mcol, $Addr2['name']);
		$this->NewLine();
//		$this->TextWrapLines($mcol, $this->rightMargin - $mcol, $Addr2['address'], 'left', 0, 0, NULL, 1);
			$this->fontSize -= 3;

		// Auxiliary document information
		$col = $this->leftMargin;
			$aux_info = array(
//				_("Customer's Reference") => $this->formData['supp_account_no'],
//				_("Sales Person") => $this->formData['contact'],
//				// _("Your VAT no.") => $this->formData['tax_id'],
//				_("Supplier's Reference") => @$this->formData['requisition_no'],
//				_("Order Date") => sql2date($this->formData['document_date']),
			);
		foreach($aux_info as $info_header => $info_content)
		{
//
//			$this->row = $iline2 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_header, 'C');
//			$this->row = $iline3 - $this->lineHeight - 1;
//			$this->TextWrap($col, $this->row, $width, $info_content, 'C');
//			$col += $width;
		}
		// Payment terms
		$this->row -= (2 * $this->lineHeight);
		$this->Font('italic');
//		$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
		$this->Font();

		// Line headers
        $this->headers = array(_("S.No"), _("ITEM DESCRIPTION"), _("SPECS"), _("BATCH NO"), _("EXP DATE"), _("PACKING"), _("NO.OF UNIT"));
		$this->row = $iline5 - $this->lineHeight - 1;
		$this->Font('bold');
		$count = count($this->headers);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);
		$this->Font();

		// Footer
		$this->Font('b');
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
