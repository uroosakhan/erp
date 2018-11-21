<?php
global $path_to_root;
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root ."/themes/premium/dashboard_db.php");
$today = Today();


$sales_amnt = get_todays_sales(null,$today,$today);
$recovery = get_todays_recovery(null,$today,$today);
$sales_order = get_todays_sales_order(null,$today,$today);
$purchase_order =   get_todays_purchase_orders(null,$today,$today);
$vendors_payment=     get_vendor_payments(null,$today,$today);
$sales_return = get_todays_sales_return(null,$today,$today);
$journal_entry = get_data_from_systypes(null,$today,$today,ST_JOURNAL);
$bank_payment =  get_data_from_systypes(null,$today,$today,ST_BANKPAYMENT);
$cust_credit_note = get_data_from_systypes(null,$today,$today,ST_CUSTCREDIT);
$bank_deposit = get_data_from_systypes(null,$today,$today,ST_BANKDEPOSIT);
$cash_payment = get_data_from_systypes(null,$today,$today,ST_CPV);
$cash_receipt =get_data_from_systypes(null,$today,$today,ST_CRV);
$funds_transfer = get_data_from_systypes(null,$today,$today,ST_BANKTRANSFER);
$loc_transfer = get_data_from_systypes(null,$today,$today,ST_LOCTRANSFER);
$inv_adjustment = get_data_from_systypes(null,$today,$today,ST_INVADJUST);
$supplier_inv = get_data_from_systypes(null,$today,$today,ST_SUPPINVOICE);
$supp_payment = get_data_from_systypes(null,$today,$today,ST_SUPPAYMENT);
$grn = get_data_from_systypes(null,$today,$today,ST_SUPPRECEIVE);
$supp_credit_note = get_data_from_systypes(null,$today,$today,ST_SUPPCREDIT);
$imp_invoice = get_data_from_systypes(null,$today,$today,ST_SUPPCREDIT_IMPORT);
$work_order = get_data_from_systypes(null,$today,$today,ST_WORKORDER);
$wrok_ord_issue = get_data_from_systypes(null,$today,$today,ST_MANUISSUE);
$sales_quotation = get_data_from_systypes(null,$today,$today,ST_SALESQUOTE);
$dimension = get_data_from_systypes(null,$today,$today,ST_DIMENSION);
$fixed_assets_adj = get_data_from_systypes(null,$today,$today,ST_FAADJUST);
$purch_req = get_data_from_systypes(null,$today,$today,ST_PURCHREQ);


if($sales_amnt != 0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-line-chart bg-aqua"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Sales</h4><p>'.number_format2($sales_amnt,2).'</p></div></a></div>';

if($recovery !=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-sync bg-green"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Recovery</h4><p>'.number_format2( $recovery,2).'</p></div></a></div>';

if($sales_order!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-bar-chart bg-orange"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Sales Order</h4><p>'.number_format2($sales_order,2).'</p></div></a></div>';

if($purchase_order!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-shopping-cart bg-blue"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Purchase Order</h4><p>'.number_format2($purchase_order,2).'</p></div></a></div>';

if($vendors_payment!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-calculator bg-yellow"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Vendor Payment</h4><p>'.number_format2($vendors_payment,2).'</p></div></a></div>';

if($sales_return!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa fa-exchange bg-red"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Sales Return</h4><p>'.number_format2($sales_return,2).'</p></div></a></div>';

if($journal_entry!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-book bg-light-blue-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Journal Entry</h4><p>'.number_format2($journal_entry,2).'</p></div></a></div>';

if($bank_payment !=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fas fa-money-bill-alt bg-teal-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Bank Payment</h4><p>'.number_format2($bank_payment,2).'</p></div></a></div>';

if($cust_credit_note!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon far fa-credit-card bg-purple-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Customer Credit Note</h4><p>'.number_format2($cust_credit_note,2).'</p></div></a></div>';

if($bank_deposit!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-university bg-teal-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Bank Deposit</h4><p>'.number_format2($bank_deposit,2).'</p></div></a></div>';

if($cash_payment!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-money bg-yellow-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Cash Payment</h4><p>'.number_format2($cash_payment,2).'</p></div></a></div>';

if($cash_receipt!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-money bg-green-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Cash Receipt</h4><p>'.number_format2($cash_receipt,2).'</p></div></a></div>';

if($funds_transfer!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-dollar bg-orange-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Funds Transfer</h4><p>'.number_format2($funds_transfer,2).'</p></div></a></div>';

if($loc_transfer!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-map bg-aqua"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Location Transfer</h4><p>'.number_format2($loc_transfer,2).'</p></div></a></div>';

if($inv_adjustment!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fas fa-archive bg-blue-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Inventory Adjustment</h4><p>'.number_format2($inv_adjustment,2).'</p></div></a></div>';

if($supplier_inv!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-money bg-olive-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Supplier Invoice</h4><p>'.number_format2($supplier_inv,2).'</p></div></a></div>';

if($supp_payment!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-credit-card bg-teal-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Supplier Payment</h4><p>'.number_format2($supp_payment,2).'</p></div></a></div>';

if($grn!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-long-arrow-left bg-blue-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">GRN</h4><p>'.number_format2($grn,2).'</p></div></a></div>';

if($supp_credit_note!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-pencil-square-o bg-fuchsia-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Supplier Credit Note</h4><p>'.number_format2($supp_credit_note,2).'</p></div></a></div>';

if($imp_invoice!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon glyphicon glyphicon-import bg-red"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Import Invoice</h4><p>'.number_format2($imp_invoice,2).'</p></div></a></div>';

if($work_order!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-pencil-square-o bg-purple-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Work Order</h4><p>'.number_format2($work_order,2).'</p></div></a></div>';

if($wrok_ord_issue!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-wrench bg-aqua-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Work Order Issue</h4><p>'.number_format2($wrok_ord_issue,2).'</p></div></a></div>';

if($sales_quotation!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-quote-left bg-light-blue-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Sales Quotation</h4><p>'.number_format2($sales_quotation,2).'</p></div></a></div>';

if($dimension!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-object-ungroup bg-green-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Dimension</h4><p>'.number_format2($dimension,2).'</p></div></a></div>';

if($fixed_assets_adj!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-file-text bg-green-gradient"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Fixed Assets Adjustment</h4><p>'.number_format2($fixed_assets_adj,2).'</p></div></a></div>';

if($purch_req!=0)
    echo'<div class="col-lg-4 ab" ><a><i class="menu-icon fa fa-cart-arrow-down bg-teal-active"></i><div class="menu-info"><h4 class="control-sidebar-subheading">Purchase Requisition</h4><p>'.number_format2($purch_req,2).'</p></div></a></div>';



?>