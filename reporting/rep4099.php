<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_MANUFTRANSVIEW' : 'SA_MANUFBULKREP';
// ----------------------------------------------------------------
// Title:	Work Orders
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");

//----------------------------------------------------------------------------------------------------

print_workorders();

//----------------------------------------------------------------------------------------------------
$woid = 0;
if ($_GET['trans_no'] != "")
{
    $woid = $_GET['trans_no'];
}

function get_work_order_productions1($woid)
{
    $sql = "SELECT * FROM ".TB_PREF."wo_manufacture WHERE workorder_id="
        .db_escape($woid)." ORDER BY id";
    return db_query($sql, "The work order issues could not be retrieved");
    //return db_fetch($result);
}
function get_work_order_issues1($woid)
{
    $sql = "SELECT * FROM ".TB_PREF."wo_issues WHERE workorder_id=".db_escape($woid)
        ." ORDER BY issue_no";
    return db_query($sql, "The work order issues could not be retrieved");

}
function get_sale_order_ref($ref)
{
    $sql = "SELECT debtor_no FROM ".TB_PREF."sales_orders 
    WHERE reference=".db_escape($ref) ."";
    $query =  db_query($sql, "The work order issues could not be retrieved");
    $fetch = db_fetch_row ($query);
    return $fetch [0];
}

function get_units_4099($units_id)
{
    $sql = "SELECT units FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($units_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_work_order_produce1($id)
{
    $sql = "SELECT prod.*, wo.stock_id, item.description AS StockDescription, wo.closed
			FROM ".TB_PREF."wo_manufacture prod,"
        .TB_PREF."workorders wo,"
        .TB_PREF."stock_master item
		WHERE prod.workorder_id=wo.id
		AND item.stock_id=wo.stock_id
		AND prod.id=".db_escape($id);
    $result = db_query($sql, "The work order production could not be retrieved");

    return db_fetch($result);
}

function get_wo_costing1($workorder_id)
{
    $sql="SELECT * 
		FROM ".TB_PREF."wo_costing cost,
			".TB_PREF."journal jl,".TB_PREF."gl_trans gl
		WHERE
			cost.trans_type=gl.type
			 AND cost.trans_no=jl.trans_no
			 AND jl.trans_no=gl.type_no
			 AND jl.type=gl.type
			  AND gl.amount < 0
			AND workorder_id=".db_escape($workorder_id);

    return db_query($sql, "The work order issues could not be retrieved");
    //return db_fetch($result);
}

function get_user_name_4099($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_id_time4099($trans_no,$type)
{
    $sql= "SELECT user,  UNIX_TIMESTAMP(stamp) as unix_stamp
    FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}

function get_wo_cust_name_4099($row)
{
    $sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no =".db_escape($row);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}

function get_work_order_cust($cust_id)
{
    $sql = "SELECT so.debtor_no
		FROM ".TB_PREF."sales_orders so
			LEFT JOIN ".TB_PREF."workorders wo ON wo.sale_order = so.order_no
			WHERE wo.id = $cust_id
		";
    $result = db_query($sql, "The work order production could not be retrieved");
    $res =  db_fetch_row($result);
    return $res[0];
}
function get_unit($id)
{
    $sql = "SELECT units FROM ".TB_PREF."stock_master WHERE stock_id =" .db_escape($id);
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
}
function print_workorders()
{
    global $path_to_root, $dflt_lang;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $email = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

    $cols = array(4, 52, 300, 330, 390, 455, 520);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'right', 'right', 'right');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('WORK ORDER'), "WorkOrderBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++) {
        $myrow = get_work_order($i);

        $user =get_user_id_time4099($myrow['id'],ST_WORKORDER);

        if ($myrow === false)
            continue;

        if ($email == 1) {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('WORK ORDER');
            $rep->filename = "WorkOrder" . $myrow['wo_ref'] . ".pdf";
        }
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contact = array('email' => $myrow['email'], 'lang' => $dflt_lang, // ???
            'name' => $myrow['contact'], 'name2' => '', 'contact');

        $rep->SetCommonData($myrow, null, null, '', ST_WORKORDER, $contact);
        $rep->SetHeaderType('Header4099');
        $rep->NewPage();
        ///units

        $result = get_wo_requirements($i);
        $rep->TextCol(0, 5, _("Work Order Requirements"), -2);
        $rep->NewLine(2);
        while ($myrow2 = db_fetch($result)) {
            $rep->TextCol(0, 1, $myrow2['stock_id'], -2);
            $rep->TextCol(2, 3, $myrow2['units'], -2);
//			$rep->TextCol(3, 4, $myrow2['WorkCentreDescription'], -2);
            $dec = get_qty_dec($myrow2["stock_id"]);
            $rep->AmountCol(3, 4, $myrow2['units_req'], $dec, -2);
            $total_unit_quantity +=  $myrow2['units_req'];
            $rep->AmountCol(4, 5, $myrow2['units_req'] * $myrow['units_issued'], $dec, -2);
            $total_quantity += $myrow2['units_req'] * $myrow['units_issued'];
//			$rep->AmountCol(5, 6, $myrow2['units_issued'], $dec, -2);
//			$rep->NewLine(1);
            $date =sql2date($to);

            $balance_stock = get_qoh_on_date($myrow2['stock_id'], $myrow2['loc_code'],$date );
            $rep->AmountCol(5, 6, $balance_stock, $dec);
            $total_balance_stock +=  $balance_stock;

            $rep->TextColLines(1, 2, $myrow2['description'], -2);

            //    $rep->MultiCell(300, 20, $myrow2['units'] ,0, 'L', 0, 2, 490,235, true);

            if ($rep->row < $rep->bottomMargin + (9 * $rep->lineHeight))
                $rep->NewPage();
        }

        if($rep->formData["type"] == 2) {
            $result1 = get_work_order_productions1($i);
            $rep->NewLine(9);
            $rep->font('b');
            $rep->TextCol(1, 2, "PRODUCTION");

            $rep->NewLine(1.1);
            $rep->TextCol(0, 1, "Id", -2);
            $rep->TextCol(1, 2, "Reference", -2);
            $rep->TextCol(2, 3, "date", -2);
            $rep->TextCol(4, 5, "Quantity", -2);
            $rep->font('');
            while ($myrow1 = db_fetch($result1)) {

                $rep->NewLine(1);

                $rep->TextCol(0, 1, $myrow1['id'], -2);
                $rep->TextCol(1, 2, $myrow1['reference'], -2);

                $rep->TextCol(2, 3, sql2date($myrow1['date_']), -2);
//			display_error(get_work_order_produce1($myrow1['quantity']));
                $rep->TextCol(4, 5, $myrow1['quantity'], -2);
//			qty_cell(get_work_order_produce1($myrow1['quantity']), -2);
                end_row();
            }//end of while

            $result2 = get_additional_issues($i);

//		$rep->TextCol($cols+200, "Issues", -2);
            $rep->NewLine(5);
            $rep->font('b');
            $rep->TextCol(1, 2, "ISSUE");
            $rep->font('');
            $rep->NewLine(1.1);
            $rep->TextCol(0, 1, "Id", -2);
            $rep->TextCol(1, 2, "Reference", -2);
            $rep->TextCol(2, 3, "date", -2);
            $rep->TextCol(4, 5, "Quantity", -2);
            while ($myrow3 = db_fetch($result2)) {


                alt_table_row_color($i);
                $rep->NewLine(1);

                $rep->TextCol(0, 1, $myrow3["issue_no"], -2);
                $rep->TextCol(1, 2, $myrow3['name'], -2);
                $rep->TextCol(2, 3, sql2date($myrow3["issue_date"]), -2);
                $rep->TextCol(4, 5, $myrow3["qty_issued"], -2);
                end_row();

                end_table();
            }

        }


$rep->SetFillColor(222, 231, 236);
$rep->font('b');
$rep->MultiCell(328, 15, "Total  ", 1, 'R', 1, 1, 40, 725, true, 0, false, true, 60, 'M', true);
$rep->MultiCell(64, 15, $total_unit_quantity."  ",  1, 'R', 1, 1, 368, 725, true, 0, false, true, 60, 'M', true);
$rep->MultiCell(63, 15,  $total_quantity."  ", 1, 'R', 1, 1, 432, 725, true, 0, false, true, 60, 'M', true);
$rep->MultiCell(70, 15, '', 1, 'R', 1, 1, 495, 725, true, 0, false, true, 60, 'M', true);
$rep->font('');
$rep->SetDrawColor(0, 0, 0);
  $rep->MultiCell(225, 60,"       ".get_user_name_4099($user['user'])."   ".  sql2date(date("Y-m-d", $user['unix_stamp']))."  ".  date("H:i:s", $user['unix_stamp']), 0, 'L', 0, 2, 45,780, true);

    $rep->MultiCell(225, 60, "_______________________________" , 0, 'L', 0, 2, 45,790, true);
    $rep->MultiCell(225, 60, "Prepared by"  , 0, 'L', 0, 2, 100,810, true);

    $rep->MultiCell(225, 60, "_______________________________" , 0, 'L', 0, 2, 410,790, true);
    $rep->MultiCell(225, 60, "Approved by"  , 0, 'L', 0, 2, 460,810, true);
 
        global $path_to_root, $wo_cost_types;
        $result11 = get_wo_costing1($i);
//		$rep->TextCol(3, 4, "Quantity", -2);
        $rep->NewLine(3.7);
        $rep->font('b');
        $rep->TextCol(1, 2, "ADDITIONAL COST");

        $rep->NewLine(1.1);
        $rep->TextCol(0, 1, "Id", -2);
        $rep->TextCol(1, 2, "Date", -2);
        $rep->TextCol(2, 4, "Account Name", -2);
        $rep->TextCol(4, 5, "Amount", -2);
        $rep->font('');
        while ($myrow4 = db_fetch($result11)) {
            alt_table_row_color($i);
            $rep->NewLine(1);


            $rep->TextCol(0, 1, $myrow4["id"], -2);
//			$rep->TextCol(1, 2, $wo_cost_types[$myrow4['stock_id']], -2);
//			$rep->NewLine(4.5);
            $rep->TextCol(1, 2, sql2date($myrow4["tran_date"]), -2);
//
            $rep->TextCol(4, 6, $myrow4['amount'], -2);
            $rep->TextCol(2, 3, get_gl_account_name($myrow4['account']), -2);
            end_row();
            end_table();
        }


        //en
        $memo = get_comments_string(ST_WORKORDER, $i);
        if ($memo != "")
        {
            $rep->NewLine();
//			$rep->TextColLines(1, 5, $memo, -2);
        }

//        $rep->Font('italic');
//        $rep->MultiCell(290, 20, $memo ,0, 'L', 0, 2, 50,238, true);


        if ($email == 1)
        {
            $myrow['DebtorName'] = $myrow['contact'];
            $myrow['reference'] = $myrow['wo_ref'];
            $rep->End($email);
        }
    }

    function display_wo_productions($woid)
    {
        global $path_to_root;


        if (db_num_rows($result) == 0)
        {
            display_note(_("There are no Productions for this Order."), 1, 1);
        }
        else
        {
            start_table(TABLESTYLE);
            $th = array(_("#"), _("Reference"), _("Date"), _("Quantity"));

            table_header($th);

            $k = 0; //row colour counter
            $total_qty = 0;

            label_row(_("Total"), number_format2($total_qty,user_qty_dec()),
                "colspan=3", "nowrap align=right");

            end_table();
        }

    }
  



    if ($email == 0)

        $rep->End();
}

