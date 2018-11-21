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

$upper = $this->row - 15 * $this->lineHeight;
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
//$this->Line($iline1, 3);
$this->SetDrawColor(128, 128, 128);
//$this->Line($iline1);
//$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
//$this->Line($iline2);
//$this->Line($iline3);
//$this->Line($iline4);
$this->rectangle($this->leftMargin, $iline5, $right - $this->leftMargin, $iline5 - $iline6, "F", null, array(222, 231, 236));
$this->Line($iline5);
$this->Line($iline6);
$this->Line($iline7);
//$this->LineTo($this->leftMargin, $iline2 ,$this->leftMargin, $iline4);
//$col = $this->leftMargin;
for ($i = 0; $i < 4; $i++)
{
//    $this->LineTo($col += $width, $iline2,$col, $iline4);
}
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
    $this->LineTo($this->cols[$cols - 1], $iline5 ,$this->cols[$cols - 1], $iline7);
else
    $this->LineTo($this->cols[$cols - 1] - 3, $iline5 ,$this->cols[$cols - 1] - 3, $iline7);
$this->LineTo($right, $iline5 ,$right, $iline7);

// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
// 		global $SysPrefs;

$logo1 = company_path() . "/images/ALCHEMIST STAMP.jpeg";
$this->NewLine(55);





//	$this->AddImage($logo1, $ccol, $this->row,0, 100);
$this->NewLine(-55);


if ($this->company['coy_logo'] != '' && file_exists($logo))
{

    $this->AddImage($logo, $ccol , $this->row,$this->company['logo_w'], $this->company['logo_h']);
// 	if ($this->row - $SysPrefs->pic_height < $this->bottomMargin)
    // $this->NewPage();
    // $rep->AddImage($logo, $cols[1] +300, $this->row +570, null,$this->company['logo_w'], $this->company['logo_h']);
    $this->NewLine();

}
else
{
    $this->NewLine(-2);
    $this->fontSize += 4;
    $this->Font('bold');
    $this->Text($ccol + 300, $this->company['coy_name'], $icol);
    $this->Font();
    $this->fontSize -= 4;
    $this->NewLine(2);
}
$this->NewLine();
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->NewLine(+4);
$this->TextWrap($ccol + 80 , $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20,"PROFORMA INVOICE", 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine(-4);
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->NewLine(-3);
$this->TextWrapLines($ccol + 300, $icol, $this->company['postal_address']);
$this->Font('italic');
if (@$this->company['phone'])
{
    $this->Text($ccol + 300, _("Phone"), $c2col);
    $this->Text($c2col + 320, $this->company['phone'], $mcol);
    $this->NewLine();
}
if (@$this->company['fax'])
{
    $this->Text($ccol + 300, _("Fax"), $c2col);
    $this->Text($c2col + 320, $this->company['fax'], $mcol);
    $this->NewLine();
}
if (@$this->company['email'])
{
    $this->Text($ccol + 300, _("Email"), $c2col);

    $url = "mailto:" . $this->company['email'];
    $this->SetTextColor(0, 0, 255);
    $this->Text($c2col + 320, $this->company['email'], $mcol);
    $this->SetTextColor(0, 0, 0);
    $this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

    $this->NewLine();
}
if (@$this->company['gst_no'])
{
    $this->Text($ccol + 300, _("Our GST No."), $c2col);
    $this->Text($c2col + 320, $this->company['gst_no'], $mcol);
    $this->NewLine();
}
if (@$this->formData['domicile'])
{
    $this->Text($ccol, _("Domicile"), $c2col);
    $this->Text($c2col, $this->company['domicile'], $mcol);
    $this->NewLine();
}
$this->NewLine(+3);
$this->Font();
$this->row = $adrline;
$this->NewLine(3);
//$this->Text($mcol + 100, _("Date"));
//$this->Text($mcol + 180, sql2date($this->formData['document_date']));

$this->NewLine();
//$this->Text($mcol + 100, $this->formData['document_name']);
//$this->Text($mcol + 180, $this->formData['document_number']);
$this->NewLine(2);

if ($this->pageNumber > 1)
    $this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 2;

$this->MultiCell(523, 330, "", 1, 'L', 0, 2, 40,135, true);
//-----------first-line box-------------------------------
$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 40,135, true);
$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 40,217, true);
$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 40,299, true);

$this->font('b');
$this->MultiCell(174, 22, "Shipper:", 1, 'C', 1, 2, 40,135, true);
$this->MultiCell(174, 22, "Consignee:
", 1, 'C', 1, 2, 40,217, true);
$this->MultiCell(174, 22, "Notify Party:
", 1, 'C', 1 , 2, 40,299, true);
$this->font('');

//-------------values----------------------------
$this->MultiCell(174, 22, $this->formData['f_text4'], 0, 'L', 0, 2, 50,165, true);
$this->MultiCell(174, 22,  $this->formData['name'], 0, 'L', 0, 2, 50,247, true);
$this->MultiCell(174, 22,  $this->formData['delivery_address'], 0, 'L', 0, 2, 50,257, true);
$this->MultiCell(174, 22, $this->formData['f_text3'], 0, 'L', 0, 2, 50,329, true);
//--------------------first-line box end ----------------------------------------


//-----------mid-line box------------
$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 214,135, true);
$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 214,217, true);
//$this->MultiCell(194, 82, "", 1, 'L', 0, 2, 214,299, true);
$this->font('b');
$this->MultiCell(174, 22, "Proforma Invoice Number:", 1, 'C', 0, 2, 214,135, true);
$this->MultiCell(174, 22,"Consignee Order Number: ", 0, 'L', 0, 2, 218,165, true);
$this->MultiCell(174, 22,"Date : ", 0, 'L', 0, 2, 218,185, true);
$this->font('');
$this->MultiCell(174, 22,  $this->formData['document_number'], 0, 'L', 0, 2, 214,142, true);
$this->font('b');
$this->MultiCell(174, 22, "Port of Loading:", 1, 'C', 0, 2, 214,217, true);
$this->MultiCell(174, 22, "Country of Origin:", 0, 'L', 0, 2, 218,241, true);
$this->MultiCell(174, 22, "Terms of Delivery:", 1, 'C', 0, 2, 214,299, true);
$this->MultiCell(174, 22, "Note:", 0, 'L', 0, 2, 218,322, true);
$this->font('');
//-----value------------------
$this->font('b');

$this->font('');
$this->MultiCell(174, 22,$this->formData['customer_ref'], 0, 'L', 0, 2, 218,175, true);
$this->MultiCell(174, 22,$this->formData['po_date'], 0, 'L', 0, 2, 218,195, true);
$this->MultiCell(174, 22,$this->formData['h_text3']."", 0, 'L', 0, 2, 218,227, true);
$this->MultiCell(174, 22, $this->formData['h_text4'], 0, 'L', 0, 2, 218,251, true);

$this->MultiCell(314, 42, $this->formData['comments'], 0, 'L', 0, 2, 218,332, true);
$this->MultiCell(174, 22, get_f_combo1_name($this->formData['f_combo1']), 0, 'L', 0, 2, 218,312, true);
//--------------------------

//-----------last-line box------------
$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 388,135, true);
$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 388,217, true);
//$this->MultiCell(174, 82, "", 1, 'L', 0, 2, 388,299, true);
$this->font('b');
$this->MultiCell(174, 22, "Date:", 1, 'C', 0, 2, 388,135, true);
$this->MultiCell(174, 22, "Other References:", 0, 'L', 0, 2, 392,164, true);
$this->MultiCell(174, 22, "Port of Discharge:", 1, 'C', 0, 2, 388,217, true);
$this->MultiCell(174, 22, "Final Destination:", 0, 'L', 0, 2, 390,241, true);
$this->MultiCell(174, 22, "Terms of Payment:", 1, 'C', 0, 2, 388,299, true);



$this->font('');
//-----value------------------
$this->MultiCell(174, 22,$this->formData['ord_date'], 0, 'L', 0, 2, 390,145, true);
$this->MultiCell(174, 22, $this->formData['f_comment2'], 0, 'L', 0, 2, 392,174, true);
$this->MultiCell(174, 22, $this->formData['h_text2'], 0, 'L', 0, 2, 390,227, true);
$this->MultiCell(174, 22, $this->formData['h_text5'], 0, 'L', 0, 2, 390,251, true);
$this->MultiCell(174, 22, get_payment_terms_name_($this->formData['payment_terms']), 0, 'L', 0, 2, 390,312, true);
//-----------------------------------


$this->MultiCell(523 , 82, "", 1, 'L', 0, 2, 40,381, true);
$this->font('b');
$this->MultiCell(174 , 82, "Additional Information", 1, 'L', 0, 2, 40,381, true);

$this->MultiCell(174 , 82, "Currency", 0, 'L', 0, 2, 240,381, true);
$this->MultiCell(314, 42, $this->formData['curr_code'], 0, 'L', 0, 2, 249,392, true);

$this->MultiCell(174 , 82, "Bank Name and Address", 0, 'L', 0, 2, 310,381, true);

$this->MultiCell(174 , 82, "Account Details", 0, 'L', 0, 2, 440,381, true);
$this->font('');


$this->MultiCell(174 , 82, "Declaration
1. Shipment within 1 month after receipt of Signed Contract / Letter of Credit whichever applies.
2. To be advised through Santander UK PLC bank, swift code ABBYGB2L
3. To allow Trans-shipment and Partial shipment
4. To be open for negotiation with any UK Bank
5. Quantity tolerance +/- 10%
6. Reimbursement charges on account of applicant
7. Proforma valid for 7 working days"
    , 0, 'L', 0, 2, 40,720, true);

//$this->MultiCell(179, 85, "", 1, 'L', 0, 2, 216,155, true);
//$this->MultiCell(171, 85, "", 1, 'L', 0, 2, 395,155, true);
//$this->fontSize -= 2;
//$this->Text($ccol, "Invoice To", $icol);
//$this->NewLine();
//$this->font('bold');
//$this->Text($ccol, $this->formData['deliver_to'], 10);
//$this->font('');
//$this->NewLine();
//$this->Text($ccol, "STRN :".$this->formData['tax_id'], 10);
//$this->NewLine();
//$this->Text($ccol, "NTN :" .$this->formData['ntn_no'], 10);
//
//$this->fontSize +=1;
// $this->MultiCell(171, 85, $this->formData['invoice_to'], 0, 'L', 0, 2,225,175//, true);
$this->NewLine(-3);

//$this->Text($mcol + 95, $Addr2['title']);


// address1

$temp = $this->row = $this->row - $this->lineHeight - 5;

$this->NewLine();
//$this->TextWrapLines($ccol, 150, $Addr1['address']);
$this->fontSize -= 1;

// address2
$this->fontSize += 1;
$this->row = $temp;
//	$this->Text($mcol + 95, $Addr2['name']);
$this->NewLine();
//$this->TextWrapLines($mcol + 95, 150, $Addr2['address'], 'left', 0, 0, NULL, 1);
$this->fontSize -= 1;

// Auxiliary document information


$col = $this->leftMargin;
//$aux_info = array(
//    _("Term's Of Delivery") => ($this->formData['f_combo1']),
//    _("Contact Person") => $this->formData['name'],
//    // _("Your VAT no.") => $this->formData['tax_id'],
//    _("Customer's Reference") => @$this->formData['customer_ref'],
//    _("Order Date") => sql2date($this->formData['document_date']),
//);
foreach($aux_info as $info_header => $info_content)
{

//    $this->row = $iline2 - $this->lineHeight - 1;
//    $this->TextWrap($col, $this->row, $width, $info_header, 'C');
//    $this->row = $iline3 - $this->lineHeight - 1;
//    $this->TextWrap($col, $this->row, $width, $info_content, 'C');
//    $col += $width;
}
// Payment terms
$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
//$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$this->headers = array(_("Item Code"), _("Item Description"), _("Width"), _("Length"), _("Thickness"),
    _("Qty"), _("UOM"), _("Price"), _("Total (".$this->formData['curr_code'].")"), _("Delivery Date"));
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
