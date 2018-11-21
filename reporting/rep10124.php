<?php

$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision: 2.0 $
// Creator:    Joe Hunt
// date_:  2005-05-19
// Title:  Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");

//----------------------------------------------------------------------------------------------------

print_customer_balances();

function get_open_balance($debtorno, $to,$convert,$dimension,$dimension2)
{
   
   if($to)
      $to = date2sql($to);

     $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0) OR t.type = ". ST_BANKPAYMENT." OR t.type = ". ST_CPV.",
       -abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";
     $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0) AND NOT (t.type = ". ST_BANKPAYMENT.") AND NOT (t.type = ". ST_CPV."),
       abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * -1, 0)) AS credits,";
    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0), t.alloc * -1, t.alloc)) AS Allocated,";

   $sql .=    "SUM(IF(t.type = ".ST_SALESINVOICE.", 1, -1) *
         (abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) - abs(t.alloc))) AS OutStanding
      FROM ".TB_PREF."debtor_trans t
       WHERE t.debtor_no = ".db_escape($debtorno)
      ." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
       $sql .= " AND t.tran_date < '$to'";

    if ($dimension != 0 )
    {
        $sql .= " AND t.dimension_id=".db_escape($dimension);
    }
    if ($dimension2 != 0 )
    {
        $sql .= " AND t.dimension2_id=".db_escape($dimension2);
    }
   $sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to,$dimension,$dimension2)
{
   $from = date2sql($from);
   $to = date2sql($to);

   $allocated_from = 
         "(SELECT trans_type_from as trans_type, trans_no_from as trans_no, date_alloc, sum(amt) amount
         FROM ".TB_PREF."cust_allocations alloc
            WHERE person_id=".db_escape($debtorno)."
               AND date_alloc <= '$to'
            GROUP BY trans_type_from, trans_no_from) alloc_from";
   $allocated_to = 
         "(SELECT trans_type_to as trans_type, trans_no_to as trans_no, date_alloc, sum(amt) amount
         FROM ".TB_PREF."cust_allocations alloc
            WHERE person_id=".db_escape($debtorno)."
               AND date_alloc <= '$to'
            GROUP BY trans_type_to, trans_no_to) alloc_to";

     $sql = "SELECT trans.*,
      (trans.ov_amount + trans.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2) AS TotalAmount,
      IFNULL(alloc_from.amount, alloc_to.amount) AS Allocated,
      ((trans.type = ".ST_SALESINVOICE.")    AND trans.due_date < '$to') AS OverDue
       FROM ".TB_PREF."debtor_trans trans
         LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
         LEFT JOIN $allocated_from ON alloc_from.trans_type = trans.type AND alloc_from.trans_no = trans.trans_no
         LEFT JOIN $allocated_to ON alloc_to.trans_type = trans.type AND alloc_to.trans_no = trans.trans_no

       WHERE trans.tran_date >= '$from'
         AND trans.tran_date <= '$to'
         AND trans.debtor_no = ".db_escape($debtorno)."
         AND trans.type = 11
         AND ISNULL(voided.id)
       ";

    if ($dimension != 0 )

    {
        $sql .= " AND trans.dimension_id=".db_escape($dimension);
    }
    if ($dimension2 != 0 )

    {
        $sql .= " AND trans.dimension2_id=".db_escape($dimension2);
    }

    $sql .= " ORDER BY trans.tran_date " ;
    return db_query($sql,"No transactions were returned");
}
/////////////for detail
function get_transactions2($debtorno, $from, $to, $transno)
{
   $from = date2sql($from);
   $to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*, ".TB_PREF."debtor_trans_details.*,
    ".TB_PREF."stock_master.units
       FROM ".TB_PREF."debtor_trans, ".TB_PREF."debtor_trans_details, ".TB_PREF."stock_master
       WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
      AND ".TB_PREF."debtor_trans.tran_date <= '$to'
      AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
      AND ".TB_PREF."debtor_trans_details.debtor_trans_type  =  ".TB_PREF."debtor_trans.type
      AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".TB_PREF."debtor_trans.trans_no
      AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".db_escape($transno)."
        AND ".TB_PREF."debtor_trans_details.stock_id =  ".TB_PREF."stock_master.stock_id      
   AND ".TB_PREF."debtor_trans_details.debtor_trans_type = 11
       ORDER BY ".TB_PREF."debtor_trans.tran_date";

    return db_query($sql,"No transactions were returned");
}




//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
       global $path_to_root, $systypes_array;

       $from = $_POST['PARAM_0'];
       $to = $_POST['PARAM_1'];
       $fromcust = $_POST['PARAM_2'];
       $dimension = $_POST['PARAM_3'];
       $dimension2 = $_POST['PARAM_4'];
        $area = $_POST['PARAM_5'];
        $folk = $_POST['PARAM_6'];
       $show_balance = $_POST['PARAM_7'];
       $currency = $_POST['PARAM_8'];
       $no_zeros = $_POST['PARAM_9'];
       $comments = $_POST['PARAM_10'];
        $orientation = $_POST['PARAM_11'];
        $destination = $_POST['PARAM_12'];
   if ($destination)
      include_once($path_to_root . "/reporting/includes/excel_report.inc");
   else
      include_once($path_to_root . "/reporting/includes/pdf_report.inc");

   $orientation = ($orientation ? 'L' : 'P');
   if ($fromcust == ALL_TEXT)
      $cust = _('All');
   else
      $cust = get_customer_name($fromcust);
       $dec = user_price_dec();

    if ($area == ALL_NUMERIC)
        $area = 0;

    if ($area == 0)
        $sarea = _('All Areas');
    else
        $sarea = get_area_name($area);

    if ($folk == ALL_NUMERIC)
        $folk = 0;

    if ($folk == 0)
        $salesfolk = _('All Sales Man');
    else
        $salesfolk = get_salesman_name($folk);

   if ($currency == ALL_TEXT)
   {
      $convert = true;
      $currency = _('Balances in Home Currency');
   }
   else
      $convert = false;

   if ($no_zeros) $nozeros = _('Yes');
   else $nozeros = _('No');

         $cols = array(0, 80, 150, 200,  230, 260, 290, 335, 400, 460, 530, 600);
   $headers = array(_('No.'), _('Date/Narration'), _(''), _('Rate'), _('Qty'), _('Disc.'),
      _('Total'),    _('Dr'), _('Cr'), _('Balance'));
   $aligns = array('left',    'left',    'left',    'left',    'right', 'right', 'right', 'right', 'right', 'right', 'right');

    $params =   array(     0 => $comments,
                    1 => array('text' => _('Period'), 'from' => $from,        'to' => $to),
                    2 => array('text' => _('Customer'), 'from' => $cust,       'to' => ''),
                        3 => array('text' => _('Zone'),       'from' => $sarea,     'to' => ''),
                        4 => array('text' => _('Sales Man'),      'from' => $salesfolk,  'to' => ''),
                    5 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
                  6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Sales Return Summary Report'), "CustomerBalances", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
       recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

   $grandtotal = array(0,0,0,0);

    $sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
         ".TB_PREF."debtors_master.name AS Name
      FROM ".TB_PREF."debtors_master
      INNER JOIN ".TB_PREF."cust_branch
         ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
      INNER JOIN ".TB_PREF."areas
         ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code
      INNER JOIN ".TB_PREF."salesman
         ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";

    $sql .= " WHERE ".TB_PREF."cust_branch.inactive != 1";

    if ($fromcust != ALL_TEXT )
    {
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
    }
    if ($area != 0)
    {
            $sql .= " AND ".TB_PREF."areas.area_code=".db_escape($area);
    }
    if ($folk != 0 )
    {
        $sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk);
    }

    $sql .= " GROUP BY ".TB_PREF."debtors_master.debtor_no ORDER BY Name"; 
    $result = db_query($sql, "The customers could not be retrieved");



   while ($myrow = db_fetch($result))
   {

      if (!$convert && $currency != $myrow['curr_code']) continue;
      
      $accumulate = 0;
      $rate = $convert ? get_exchange_rate_from_home_currency($myrow['curr_code'], Today()) : 1;
      $bal = get_open_balance($myrow['DebtorNo'], $from, $convert,$dimension,$dimension2);

      $init[0] = $init[1] = 0.0;
      $init[0] = round2(abs($bal['charges']*$rate), $dec);
      $init[1] = round2(Abs($bal['credits']*$rate), $dec);
      $init[2] = round2($bal['Allocated']*$rate, $dec);

      if($bal){
          

        }

      if ($show_balance)
      {
         $init[3] = $init[0] - $init[1];
         $accumulate += $init[3];
      }



      else
         $init[3] = round2($bal['OutStanding']*$rate, $dec);



      $res = get_transactions($myrow['DebtorNo'], $from, $to,$dimension,$dimension2);
        if($res){


        }


      if ($no_zeros && db_num_rows($res) == 0) continue;

        //edit use supress logic above

       if($cust = _('All') && db_num_rows($res) == 0)continue;

      $rep->fontSize += 2;
      $rep->TextCol(0, 2, $myrow['Name']);
      if ($convert)
         $rep->TextCol(2, 3,    $myrow['curr_code']);
      $rep->fontSize -= 2;
      $rep->TextCol(3, 5,    _("Open Balance"));
//        $rep->AmountCol(7, 8, $init[0], $dec);
//        $rep->AmountCol(8, 9, $init[1], $dec);
      $rep->AmountCol(9, 10, $init[3], $dec);
      $total = array(0,0,0,0);
      for ($i = 0; $i < 4; $i++)
      {
         $total[$i] += $init[$i];
         $grandtotal[$i] += $init[$i];
      }
      $rep->NewLine(1, 2);
      
      $rep->Line($rep->row + 4);
      if (db_num_rows($res)==0) {
         $rep->NewLine(1, 2);
         continue;
      }
      
      while ($trans = db_fetch($res))
      {
          
         
         if ($no_zeros && floatcmp($trans['TotalAmount'] == 0)) continue;
         $rep->NewLine(1, 2);
         $rep->TextCol(2, 3, $systypes_array[$trans['type']]);
         $rep->Font('bold');
          $rep->TextCol(0, 1,    $trans['reference']);
            $rep->Font('');
            $rep->DateCol(1, 2,    $trans['tran_date'], true);
         if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == 11)
         {

         $res2 = get_transactions2($myrow['DebtorNo'], $from, $to, $trans['trans_no']);

         ////////////
         /////////////for detail
                $pref = get_company_prefs();
            while ($trans2 = db_fetch($res2))
            {

            $rep->NewLine(1);  
            $rep->TextCol(0, 4, $trans2['description'], $dec);
            $rep->TextCol(0, 10, "...........................................................................................................................................................................................................................................", $dec);
            $rep->AmountCol(3, 4, $trans2['unit_price'], $dec);

            //if($trans2['con_factor'] == 0 || $trans2['con_factor'] == 1)
                    if($trans2['units_id'] != $trans2['units'] && $trans2['units_id']!='' && $pref['alt_uom'] == 1)
                    {
                        $quantity = $trans2['quantity']*$trans2['con_factor'];

                    }
                    else{
                        $quantity = $trans2['quantity'];
                    }

                    $rep->AmountCol(4, 5, $quantity, $dec);
            $DiscountAmount= (($trans2['unit_price'] * $quantity) * $trans2['discount_percent']/100);
               $rep->AmountCol(5, 6, $DiscountAmount , $dec);
            
            //if($trans2['con_factor'] == 0 || $trans2['con_factor'] == 1)
//          if($trans2['units_id'] != $trans2['units'])
//          {
//                 $TotalAmount = (($trans2['unit_price'] * $trans2['quantity']*$trans2['con_factor']) );
//          }
//          else
//          {
                $TotalAmount = (($trans2['unit_price'] * $quantity) );
             
//          }
             $rep->AmountCol(6, 7, $TotalAmount, $dec);
            $sum_totalamount += $TotalAmount;

            $ship_charges =$trans2['ov_freight']+$trans2['ov_freight_tax'];
            $tax_charges =$trans2['ov_gst'];
            
            $disc1 =$trans2['discount1'];
            $disc2 =$trans2['discount2'];
            
              }//while end 
              ///////////for total 
               if($ship_charges!=0)
              { 
                  $rep->NewLine();
                  $rep->TextCol(0, 1, "Shiping Charges", $dec);
               $rep->AmountCol(6, 7, $ship_charges, $dec);
              }
              $rep->NewLine();
               $rep->TextCol(0, 1, "Sub Total", $dec);
          $rep->AmountCol(6, 7, $sum_totalamount, $dec);
          
          if($disc1)
              { 
                  $rep->NewLine();
                  $rep->TextCol(0, 1, "Discount 1", $dec);
               $rep->AmountCol(6, 7, $disc1, $dec);
               $rep->TextCol(5, 6, ($disc1/$sum_totalamount)*100 ."%");
              }
          
           if($disc2)
               {
                   $rep->NewLine();
                   $rep->TextCol(0, 1, "Discount 2", $dec);
                $rep->AmountCol(6, 7,  $disc2, $dec);
                   
               }
               if($tax_charges!=0)
               {
                   $rep->NewLine();
                   $rep->TextCol(0, 1, "Tax Amount", $dec);
                $rep->AmountCol(6, 7,  $tax_charges, $dec);
                   
               }
               $g_total=$sum_totalamount - $disc1 - $disc2+$ship_charges+$tax_charges;
               if($g_total!=0)
               {
            $rep->NewLine();
               $rep->TextCol(0, 1, "Grand Total", $dec);
          $rep->AmountCol(6, 7, $sum_totalamount - $disc1 - $disc2+$ship_charges+$tax_charges, $dec);
          $rep->Font('bold');
          $rep->AmountCol(7, 8, $g_total, $dec);
          $rep->Font('');
               }
         $g_total=$sum_totalamount=0;
         }
         //////////////////
         
         $item[0] = $item[1] = 0.0;
         if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV)
            $trans['TotalAmount'] *= -1;
            
         if ($trans['TotalAmount'] > 0.0)
         {
            $item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
         // $rep->AmountCol(7, 8, $item[0], $dec);
            $accumulate += $item[0];
            
         }//if end
         else
         {
         
          $item[1] = round2(Abs($trans['TotalAmount']) * $rate , $dec);
          $rep->Font('bold');
            $rep->AmountCol(8, 9, $item[1], $dec);
            $rep->Font('');
            $accumulate -= $item[1];
         }
        
          // $item[2] = round2(Abs($trans['Allocated']) * $rate, $dec) ;
         //$rep->AmountCol(6, 7, $item[2], $dec);
         
          if ($trans['type'] == ST_CUSTPAYMENT)
            {
                if ($trans['supply_disc'] != 0 )
                    $wh1 = 'WHT on Supplies Amount: ' . $trans['supply_disc']." ";
                if ($trans['service_disc'] != 0)
                    $wh2 = 'WHT on Services Amount: '.$trans['service_disc']." ";
                if ($trans['fbr_disc'] != 0)
                    $wh3 = 'ST WH FBR Amount: '.$trans['fbr_disc']." ";
                if ($trans['srb_disc'] != 0)
                    $wh4 = 'ST WH SRB/PRA Amount: '.$trans['srb_disc']." ";
                    $rep->NewLine();
                    $rep->TextCollines(0, 9, $wh1.$wh2.$wh3.$wh4, -2);

            }
            $memo = get_comments_string($trans['type'], $trans['trans_no']);
            if ($memo != "")
            {
                $rep->NewLine();
                $rep->TextCol(0, 8, "Memo : ".$memo, -2);
            }
               $rep->Line($rep->row  - 4);
         if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT ||    $trans['type'] == ST_CPV||
                $trans['type'] == ST_JOURNAL)
            $item[3] = $item[0] + $item[1] - $item[2];
         else   
            $item[3] = $item[0] - $item[1] + $item[2];
            $rep->Font('bold');
         if ($show_balance) 
         
            $rep->AmountCol(9, 10, $accumulate, $dec);
         else   
            $rep->AmountCol(9, 10, $item[3], $dec);
            $rep->Font('');
            
         for ($i = 0; $i < 4; $i++)
         {
            $total[$i] += $item[$i];
            $grandtotal[$i] += $item[$i];
         }
            $total[3] = $total[0] - $total[1];
      }
   
      $rep->Line($rep->row - 8);
      $rep->NewLine(2);
      $rep->TextCol(0, 3, _('Total'));
        $rep->AmountCol(7, 8, $total[0], $dec);
        $rep->AmountCol(8, 9, $total[1], $dec); 
        $rep->AmountCol(9, 10, $total[3], $dec); 
          $rep->Line($rep->row  - 4);
          $rep->NewLine(2);
   }
   $rep->fontSize += 2;
   $rep->TextCol(0, 3, _('Grand Total'));
   $rep->fontSize -= 2;
      $grandtotal[3] =  $grandtotal[0]- $grandtotal[1];
        $rep->AmountCol(7, 8, $grandtotal[0], $dec);
        $rep->AmountCol(8, 9, $grandtotal[1], $dec); 
        $rep->AmountCol(9, 10, $grandtotal[3], $dec);  
            
   $rep->Line($rep->row  - 4);
   $rep->NewLine();
       $rep->End();
}