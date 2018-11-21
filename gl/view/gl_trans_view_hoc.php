<?php

$page_security = 'SA_GLTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "General Ledger Transaction Details"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/gl/includes/gl_db.inc");

if (!isset($_GET['type_id']) || !isset($_GET['trans_no'])) 
{ /*Script was not passed the correct parameters */

	display_note(_("The script must be called with a valid transaction type and transaction number to review the general ledger postings for."));
	end_page();
}

$height = get_company_pref('logo_h');
$width = get_company_pref('logo_w');
$logo = get_company_pref('coy_logo');

$logo = company_path() . "/images/" . $logo;



echo '<center><img src='.$logo.' width='.$width.' height='.$height.' ></center>';




////////for address

$sql1 = "SELECT value FROM ".TB_PREF."sys_prefs WHERE `name`='postal_address' ";
$result1 = db_query($sql1, "could not get sales type");
$row = db_fetch_row($result1);

echo '<b><center><h3>'.$row[0].'</h3></center></b>';



function display_gl_heading($myrow)
{
	global $systypes_array;

	$trans_name = $systypes_array[$_GET['type_id']];
	$journal = $_GET['type_id'] == ST_JOURNAL;
    start_table(TABLESTYLE, "width='95%'");
    $th = array(_("General Ledger Transaction Details"), _("Reference"),
    	_("Transaction Date"), _("Journal #"));

	if ($_GET['type_id'] == ST_JOURNAL)
		array_insert($th, 3, array(_("Document Date"), _("Event Date")));
	else
		array_insert($th, 3, array(_("Counterparty")));
	
	if($myrow['supp_reference'])
	{
		array_insert($th, 2, array(_("Supplier Reference")));
	}
    table_header($th);	
    start_row();	
    label_cell("$trans_name #" . $_GET['trans_no']);
    label_cell($myrow["reference"], "align='center'");
	if($myrow['supp_reference'])
	{
	label_cell($myrow["supp_reference"], "align='center'");
	}
	label_cell(sql2date($myrow["doc_date"]), "align='center'");
	if ($journal)
	{
		$header = get_journal($myrow['type'], $_GET['trans_no']);
		label_cell($header["doc_date"] == '0000-00-00' ? '-' : sql2date($header["doc_date"]), "align='center'");
		label_cell($header["event_date"] == '0000-00-00' ? '-' : sql2date($header["event_date"]), "align='center'");
	} else
		label_cell(get_counterparty_name($_GET['type_id'],$_GET['trans_no']));
	label_cell( get_journal_number($myrow['type'], $_GET['trans_no']), "align='center'");
	end_row();

	start_row();
	label_cells(_('Entered By'), $myrow["real_name"], "class='tableheader2'", "colspan=" .
		 ($journal ? ($header['rate']==1 ? '3':'1'):'6'));
	if ($journal)
	{
		if ($header['rate'] != 1)
			label_cells(_('Exchange rate'), $header["rate"].' ', "class='tableheader2'");
		label_cells(_('Source document'), $header["source_ref"], "class='tableheader2'");
	}
	end_row();
	comments_display_row($_GET['type_id'], $_GET['trans_no']);
    end_table(1);
}
$result = get_gl_trans($_GET['type_id'], $_GET['trans_no'], $_GET['unapproved'], 2);

if (db_num_rows($result) == 0)
{
    echo "<p><center>" . _("No general ledger transactions have been created for") . " " .$systypes_array[$_GET['type_id']]." " . _("number") . " " . $_GET['trans_no'] . "</center></p><br><br>";
	end_page(true);
	exit;
}

/*show a table of the transactions returned by the sql */
$dim = get_company_pref('use_dimension');

if ($dim == 2)
	$th = array( _("Code"), _("Title"), _("Narration"), _("Dimension")." 1", _("Dimension")." 2",
		_("Debit"), _("Credit"), _("Cheque#"));
elseif ($dim == 1)
	$th = array(_("Journal Date"), _("Account Code"), _("Account Name"), _("Dimension"),
		_("Debit"), _("Credit"), _("Cheque#"), _("Memo"));
else		
	$th = array(_("Journal Date"), _("Account Code"), _("Account Name"),
		_("Debit"), _("Credit"), _("Cheque#"), _("Memo"));

$k = 0; //row colour counter
$heading_shown = false;

$credit = $debit = 0;
while ($myrow = db_fetch($result)) 
{
	if ($myrow['amount'] == 0) continue;
	if (!$heading_shown)
	{
		display_gl_heading($myrow);
		start_table(TABLESTYLE, "width='95%'");
		table_header($th);
		$heading_shown = true;
	}

//	alt_table_row_color($k);

	$counterpartyname = get_subaccount_name($myrow["account"], $myrow["person_id"]);
	$counterparty_id = $counterpartyname ? sprintf(' %05d', $myrow["person_id"]) : '';

    label_cell(sql2date($myrow['tran_date']));
    label_cell($myrow['account'].$counterparty_id);
	label_cell($myrow['account_name'] . ($counterpartyname ? ': '.$counterpartyname : ''));
	if ($dim >= 1)
		label_cell(get_dimension_string($myrow['dimension_id'], true));
	if ($dim > 1)
		label_cell(get_dimension_string($myrow['dimension2_id'], true));

	display_debit_or_credit_cells($myrow['amount']);
	label_cell($myrow['cheque']);
	label_cell($myrow['memo_']);

	end_row();
    if ($myrow['amount'] > 0 ) 
    	$debit += $myrow['amount'];
    else 
    	$credit += $myrow['amount'];
}

if ($heading_shown)
{
    start_row("class='inquirybg' style='font-weight:bold'");
    label_cell(_("Total"), "colspan=3");
    if ($dim >= 1)
        label_cell('');
    if ($dim > 1)
        label_cell('');
    amount_cell($debit);
    amount_cell(-$credit);
    label_cell('');
    end_row();
	end_table(1);
}
if ($_GET['type_id'] == 10 || $_GET['type_id']==13) {
	start_table(TABLESTYLE, "width='95%'");
	$th = array(_("Product "), _("Quantity"),
		_("Price"), _("Total"));
	start_row("class='inquirybg' style='font-weight:bold'");
	table_header($th);

	if ($_GET['type_id'] == 20 || $_GET['type_id'] == 21 || $_GET['type_id'] == 25) {
		$gl_trans = get_sup_trans($_GET['trans_no'], $_GET['type_id']);
	} else {
		$gl_trans = get_debtor_datass($_GET['trans_no'], $_GET['type_id']);
	}

	while ($myrow2 = db_fetch($gl_trans)) {
		$total = $myrow2['unit_price'] + $myrow2['unit_tax'];

		label_cell($myrow2['description']);
		label_cell($myrow2['quantity']);
		label_cell($myrow2['unit_price']);
		label_cell($total);
	}
}

//end of while loop

is_voided_display($_GET['type_id'], $_GET['trans_no'], _("This transaction has been voided."));

end_page(true, true, true, $_GET['type_id'], $_GET['trans_no']);
?>
<style>
	#vv
	{
		float: left;
		margin-left: 100px;

	}
	#vvv
	{
		float: left;
		margin-left: 130px;

	}
	#v1
	{
		float: left;
		margin-left: 220px;

	}
	#v3
	{
		float: left;
		margin-left: 200px;

	}

</style>
<table bgcolor="#fff"  >

	<tr>
		<td id="vv">

			_____________________
		</td>
		<td id="vv">
			______________________


		</td>
		<td id="vv" >
			_______________________
		</td>

		<td id="vv" >
			______________________
		</td>

	</tr>
	<tr>
		<td id="vvv">
			<p>Prepared By</p>


		</td>

		<td id="v1">
			<p>Checked By</p>


		</td>
		<td id="v3">
			<p>Approved By</p>


		</td>
		<td id="v3">
		<p>Request By</p>


		</td>
	</tr>
</table>