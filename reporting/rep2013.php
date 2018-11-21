<?php
$page_security = 'SA_SUPPLIERANALYTIC';
// ----------------------------------------------------------------
// $ Revision: 2.0 $
// Creator:    Joe Hunt
// date_:  2005-05-19
// Title:  Supplier Balances
// ----------------------------------------------------------------
$path_to_root="..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------


print_supplier_balances();


function custom( $from ,$to,$supplier_id)
{

    $from = date2sql($from);
    $to = date2sql($to);


    $sql =  "SELECT ".TB_PREF."supp_trans.*,".TB_PREF."supp_invoice_items.*
      FROM ".TB_PREF."supp_trans
            INNER JOIN ".TB_PREF."supp_invoice_items  ON ".TB_PREF."supp_trans.type = ".TB_PREF."supp_invoice_items.supp_trans_type 
            AND ".TB_PREF."supp_trans.trans_no = ".TB_PREF."supp_invoice_items.supp_trans_no 
            AND ".TB_PREF."supp_trans.tran_date >= '$from'
            AND ".TB_PREF."supp_trans.tran_date <= '$to' 
         
            WHERE ".TB_PREF."supp_trans.type=21";
    if ($supplier_id != '' )

    {
        $sql .= " AND ".TB_PREF."supp_trans.supplier_id=".db_escape($supplier_id);
    }
  $bb= db_query($sql,"query");
    return $bb;


}

function get_memo_($type,$type_no)
{
    $sql = "SELECT * FROM ".TB_PREF."gl_trans
       WHERE type = ".db_escape($type) ." AND type_no = ".db_escape($type_no)  ." ORDER BY counter LIMIT 1";
    $result = db_query($sql,"No transactions were returned");
    $data = db_fetch($result);
    return $data;
}





//----------------------------------------------------------------------------------------------------

/**
 *
 */
function print_supplier_balances()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $fromsupp = $_POST['PARAM_2'];
    $currency = $_POST['PARAM_3'];
    $no_zeros = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];
    $destination = $_POST['PARAM_7'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    if ($fromsupp == ALL_TEXT)
        $supp = _('All');
    else
        $supp = get_supplier_name($fromsupp);
    $dec = user_price_dec();

    if ($currency == ALL_TEXT)
    {
        $convert = true;
        $currency = _('Balances in Home currency');
    }
    else
        $convert = false;

    if ($no_zeros) $nozeros = _('Yes');
    else $nozeros = _('No');
    
    $cols = array(4, 150, 250, 400, 480);
    $headers = array(_('No.'), _('Unit Price'), _('Quantity'), _('Total'));

    $aligns = array('left',    'right',   'center',  'right');
    $params =   array(     0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Supplier'), 'from' => $supp, 'to' => ''),
        3 => array(  'text' => _('Currency'),'from' => $currency, 'to' => ''),
        4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Purchase Return Summary'), "PurchaseReturnSummary", user_pagesize());
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $total = array();
    $grandtotal = array(0,0,0,0);


    $grandtotal=0;


    $quantity=0;


    $sql = "SELECT supplier_id, supp_name AS name, curr_code FROM ".TB_PREF."suppliers";
    if ($fromsupp != ALL_TEXT)
        $sql .= " WHERE supplier_id=".db_escape($fromsupp);
    $sql .= " ORDER BY supp_name";
    $result = db_query($sql, "The customers could not be retrieved");


   while ($myrow=db_fetch($result))
   {

       $data=custom($from,$to,$myrow['supplier_id']);
       while($my_row=db_fetch($data)){

           $rep->TextCol(0, 1, $my_row['reference'].'  '.$my_row['description']);
           $rep->AmountCol(1, 2, $my_row['unit_price'], $dec);
           $rep->AmountCol(2, 3, $my_row['quantity'], $dec);

           $total=$my_row['unit_price'] * $my_row['quantity'];
           $quantity += $my_row['quantity'];
           $rep->AmountCol(3,4, $total,$dec);
           $grandtotal += $total;
           $rep->NewLine();


       }

   }

    $rep->fontSize += 2;
    $rep->Font('bold');
    $rep->TextCol(0, 3,    _('Grand Total'));
    $rep->fontSize -= 2;
    $rep->AmountCol(2, 3, abs($quantity), $dec);
    $rep->AmountCol(3, 4,abs($grandtotal), $dec);
    $rep->Font();
    $rep->Line($rep->row  - 5);
    $rep->NewLine();
    $rep->End();
}
?>