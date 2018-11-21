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

$upper = $this->row - 2 * $this->lineHeight;
$lower = $this->bottomMargin + 8 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 3 * $this->lineHeight;
$iline6 = $iline5 - 2.5 * $this->lineHeight;
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
$this->Line($iline1, 3);
$this->SetDrawColor(128, 128, 128);
$this->Line($iline1);
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
for ($i = 0; $i < 4; $i++)
{
    $this->LineTo($col += $width, $iline2,$col, $iline4);
}
$this->LineTo($this->leftMargin, $iline5 ,$this->leftMargin, $iline7);
if ($this->l['a_meta_dir'] == 'rtl') // avoid line overwrite in rtl language
    $this->LineTo($this->cols[$cols - 1], $iline5 ,$this->cols[$cols - 1], $iline7);
else
    $this->LineTo($this->cols[$cols - 2] + 2, $iline5 ,$this->cols[$cols - 2] + 2, $iline7);
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
    $this->fontSize += 4;
    $this->Font('bold');
    $this->Text($ccol, $this->company['coy_name'], $icol);
    $this->Font();
    $this->fontSize -= 4;
}
else
{

}
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20,"Sales Tax Invoice", 'right');
$this->Font();
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
$this->Font();
$this->row = $adrline;
$this->NewLine(3);
$this->Text($mcol + 100, _("Date"));
$this->Text($mcol + 180, sql2date($this->formData['document_date']));

$this->NewLine();
$this->Text($mcol + 100, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['document_number']);
$this->NewLine(2);

if ($this->pageNumber > 1)
    $this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;
$this->fontSize += 2;
$this->MultiCell(175, 85, "", 1, 'L', 0, 2, 40,155, true);
//$this->MultiCell(179, 85, "", 1, 'L', 0, 2, 216,155, true);
$this->MultiCell(171, 85, "", 1, 'L', 0, 2, 395,155, true);



$this->MultiCell(172, 15,  "", 1, 'L', 1, 2,41,155, true);
$this->MultiCell(169, 15,  "", 1, 'L', 1, 2,395,155, true);
$this->fontSize -= 2;

$this->MultiCell(522, 15,  "This is a system generated invoice and therefore does not require any signature .", 0, 'C', 0, 2,41,725, true);
$this->font('bold');
$this->MultiCell(522, 15,  "Payment should be made through crossed cheques/drafts marked ' Payees Account only ' ", 0, 'L', 0, 2,41,775, true);

$this->Text($ccol, "Invoice To", $icol);
$this->NewLine();
$this->font('bold');
$this->Text($ccol, $this->formData['deliver_to'], 10);
$this->font('');
$this->NewLine();
$this->Text($ccol, "STRN :".$this->formData['tax_id'], 10);
$this->NewLine();
$this->Text($ccol, "NTN :" .$this->formData['ntn_no'], 10);

$this->fontSize +=1;
// $this->MultiCell(171, 85, $this->formData['invoice_to'], 0, 'L', 0, 2,225,175//, true);
$this->NewLine(-3);

$this->Text($mcol + 95, $Addr2['title']);


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
$this->TextWrapLines($mcol + 95, 150, $Addr2['address'], 'left', 0, 0, NULL, 1);
$this->fontSize -= 1;

// Auxiliary document information
function get_combo1_names208888($stock_id)
{
    $sql = "SELECT description FROM ".TB_PREF."f_combo1  WHERE combo_code =".db_escape($stock_id)."
	";

    $result = db_query($sql, "could not retreive the location name for $stock_id");


    $row = db_fetch_row($result);
    return $row[0];

}
$dn = get_dn_no($this->formData['order_']);

$col = $this->leftMargin;
$aux_info = array(
    _("Term's Of Delivery") => get_combo1_names208888($this->formData['f_combo1']),
    _("Contact Person") => $this->formData['name'],
    // _("Your VAT no.") => $this->formData['tax_id'],
    _("Customer's Reference") => @$this->formData['customer_ref'],
    _("DN Reference") => $dn['reference'],
);
foreach($aux_info as $info_header => $info_content)
{

    $this->row = $iline2 - $this->lineHeight - 1;
    $this->TextWrap($col, $this->row, $width, $info_header, 'C');
    $this->row = $iline3 - $this->lineHeight - 1;
    $this->TextWrap($col, $this->row, $width, $info_content, 'C');
    $col += $width;
}
// Payment terms
$this->row -= (2 * $this->lineHeight);
$this->Font('italic');
$this->TextWrap($ccol, $this->row, $right-$ccol, $Payment_Terms);
$this->Font();

// Line headers
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$this->headers = array(_(" S#"),_(" DESCRIPTION OF GOODS"), _("QTY"), _("UNIT"),
    _("   S. TAX EXCLUDING"), _("S. TAX"), _("S. TAX"), _("VALUE INCLUDING"));
$this->headers2 = array(_(""),_(""), _(""), _("PRICE"), _("VALUE(Rs.)"), _("(%)"), _("(Rs.)"),  _("ALL TAX"));

// Line headers1
$this->row = $iline5 - $this->lineHeight - 1;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();
// Line headers2
$this->row = $iline5 - $this->lineHeight - 10;
$this->Font('bold');
$count = count($this->headers2);
$this->cols[$count] = $right - 3;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers2[$i], -2);
$this->Font();
//$deliveries_date = get_sales_parent_numbers(ST_SALESINVOICE, $this->formData['trans_no']);
//if ($print_invoice_no == 0)
//{
//	foreach($deliveries_date as $n => $delivery_date)
//	{
//		$deliveries_date[$n] = get_invoice_date_through_dn(ST_CUSTDELIVERY, $delivery_date);
//	}
//}

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