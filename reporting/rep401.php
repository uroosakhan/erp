<?php

$page_security = 'SA_BOMREP';
// ----------------------------------------------------------------
// $ Revision: 2.0 $
// Creator:    Joe Hunt
// date_:  2005-05-19
// Title:  Bill Of Material
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");

//----------------------------------------------------------------------------------------------------

print_bill_of_material();

function getTransactions($from, $to)
{
   $sql = "SELECT bom.parent,
         bom.component,
         item.description as CompDescription,
         item.material_cost as StandardCost,
         bom.quantity,
         bom.loc_code,
         bom.workcentre_added
      FROM "
         .TB_PREF."stock_master item,"
         .TB_PREF."bom bom
      WHERE item.stock_id=bom.component
      AND bom.parent >= ".db_escape($from)."
      AND bom.parent <= ".db_escape($to)."
      ORDER BY
         bom.parent,
         bom.component";

    return db_query($sql,"No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_bill_of_material()
{
    global $path_to_root;

    $frompart = $_POST['PARAM_0'];
    $topart = $_POST['PARAM_1'];
    $comments = $_POST['PARAM_2'];
    $show_amt = $_POST['PARAM_3'];
   $orientation = $_POST['PARAM_4'];
   $destination = $_POST['PARAM_5'];


   if ($destination)
      include_once($path_to_root . "/reporting/includes/excel_report.inc");
   else
      include_once($path_to_root . "/reporting/includes/pdf_report.inc");

   $orientation = ($orientation ? 'L' : 'P');

    if($show_amt == 0) {

        $cols = array(4, 60, 310, 350, 390, 420,475, 520);

        $headers = array(_('Component'), _('Description'), _('Loc'), _('Wrk Ctr'), _('Quantity'), _('Cost'),  _('Amount'));

        $aligns = array('left', 'left', 'left', 'left', 'right', 'right','right');
}
else {

    $cols = array(0, 50, 305, 375, 445,490,520);

    $headers = array(_('Component'), _('Description'), _('Loc'), _('Wrk Ctr'), _('Quantity'),_('Cost'));

    $aligns = array('left', 'left', 'left', 'left','left', 'right');
}

    $params =   array(     0 => $comments,
                    1 => array('text' => _('Component'), 'from' => $frompart, 'to' => $topart));

    $rep = new FrontReport(_('Bill of Material Listing'), "BillOfMaterial", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
       recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

   $res = getTransactions($frompart, $topart);
   $parent = '';
    $Total = $GrandTotal= 0.0;
    $cost=$Grandcost=0.0;
   while ($trans=db_fetch($res))
   { $dec = get_qty_dec($trans['stock_id']);
      if ($parent != $trans['parent'])
      {
         if ($parent != '')
         {
                $rep->font('b');
                $rep->NewLine(1, 2);
                $rep->TextCol(0, 1, "Total");
                $rep->AmountCol(4, 5, $Total, $dec);
            $rep->Line($rep->row - 2);
            $rep->NewLine(2, 3);
                $rep->font('');
         }
            $Total = 0.0;
         $rep->TextCol(0, 1, $trans['parent']);
         $desc = get_item($trans['parent']);
         $rep->TextCol(1, 2, $desc['description']);
         $parent = $trans['parent'];
         $rep->NewLine();
      }

      $rep->NewLine();
      $dec = get_qty_dec($trans['component']);
      $rep->TextCol(0, 1, $trans['component']);
      $rep->TextCol(1, 2, $trans['CompDescription']);
      $wc = get_work_centre($trans['workcentre_added']);
      $rep->TextCol(2, 3, get_location_name($trans['loc_code']));
      $rep->TextCol(3, 4, $wc['name']);
      $rep->AmountCol(4, 5, $trans['quantity'], $dec);


//        $rep->AmountCol(4, 5, number_format($trans['quantity'], 3, '.', ''));
        $rep->AmountCol(5,6, $trans['StandardCost'], $dec);

        if($show_amt == 0) {
            $amount = $trans['StandardCost'] * $trans['quantity'];
            $rep->AmountCol(6,7, $amount, $dec);
        }


        $Total += $trans['quantity'];
        $cost+=$trans['StandardCost'];
        $AmountTotal += $amount;
        $GrandTotal += $trans['quantity'];
        $Grandcost+=$trans['StandardCost'];
   }




        $rep->font('b');
        $rep->NewLine(1);
        $rep->TextCol(0, 4, _('Total'));
        $rep->AmountCol(4, 5, $Total, $dec);
        $rep->AmountCol(5,6, $cost, $dec);
    if($show_amt == 0) {
        $rep->AmountCol(6,7, $AmountTotal, $dec);
    }
        $rep->font('');
        $rep->Line($rep->row - 4);
        $rep->NewLine();

    $rep->NewLine(2, 1);
    $rep->font('b');
    $rep->TextCol(0, 4, _('Grand Total'));
    $rep->AmountCol(4, 5, $GrandTotal, $dec);
    $rep->AmountCol(5,6,$Grandcost, $dec);

    if($show_amt == 0) {
        $rep->AmountCol(6,7, $AmountTotal, $dec);
    }
    $rep->font('');
   $rep->Line($rep->row - 4);
   $rep->NewLine();
    $rep->End();


}