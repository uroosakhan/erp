<?php
$path_to_root = "././.";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/dashboard/charts/charts_utils.php");
include_once($path_to_root . "/themes/premium/dashboard_db.php");

class dashboard
{

    public function renderDash()
    {
    global $db_connections;
        $path_to_root = ".";
        echo "<section class='content'>";

// 1st Line widgets
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'AXEN') {
    echo '<div class="" style="background-color: ;">';
    include_once("$path_to_root/themes/" . user_theme() . "/Dashboard_Widgets/day_wise_newdashboard.php");
    $_carosal = new carosal_view();
    $_carosal->renderCarosal();
    echo '</div>';
}
else{
    echo '<div class="" style="background-color: ;">';
    include_once("$path_to_root/themes/" . user_theme() . "/Dashboard_Widgets/carosal_for_monthly_views.php");
    $_carosal = new carosal_view();
    $_carosal->renderCarosal();
    echo '</div>';
}

        // 2nd Line widgets
        echo '<div class="">
      <div class="row">
        <a data-toggle=\'control-sidebar\' class="bell_icon" >
        <div class="col-md-2 col-sm-6 col-xs-12">         
          <div class="info-box-sm">
           <div style="float: left;">
              <img src="' . $path_to_root . '/themes/' . user_theme() . '/images/img1.png" width="10px;" height="60px;" >
           </div>
            <span class="info-box-icon-sm bg-aqua"><i class="fa fa-line-chart" style="margin-top: 14px;"></i></span>

            <div class="info-box-content-sm">
              <span class="info-box-text-sm " style="font-size: 11px;margin-left: 8px;">Sales</span>
              <span class="info-box-number-sm"  style="margin-left: 8px;">' . number_format(get_todays_sales(null,Today(),Today())) . '</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        
        <a data-toggle=\'control-sidebar\' class="bell_icon" >
        <div class="col-md-2 col-sm-6 col-xs-12">
          <div class="info-box-sm">
            <span class="info-box-icon-sm bg-green"><i class="ion ion-android-sync" style="margin-top: 14px;"></i></span>

            <div class="info-box-content-sm">
              <span class="info-box-text-sm" style="font-size: 11px;">Recovery</span>
              <span class="info-box-number-sm">' . number_format(get_todays_recovery(null,Today(),Today())) . '</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        <!-- /.col -->
        
        <a data-toggle=\'control-sidebar\' class="bell_icon" >
        <div class="col-md-2 col-sm-6 col-xs-12">
          <div class="info-box-sm">
            <span class="info-box-icon-sm bg-orange"><i class="fa fa-bar-chart" style="margin-top: 14px;"></i></span>

            <div class="info-box-content-sm">
              <span class="info-box-text-sm" style="font-size: 11px;">Sales Order</span>
              <span class="info-box-number-sm">' . number_format(get_todays_sales_order(null,Today(),Today())) . '</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        <!-- /.col -->
        
        <a data-toggle=\'control-sidebar\' class="bell_icon" >
        <div class="col-md-2 col-sm-6 col-xs-12">
          <div class="info-box-sm">
            <span class="info-box-icon-sm bg-blue"><i class="fa fa-shopping-cart" style="margin-top: 14px;"></i></span>

            <div class="info-box-content-sm">
              <span class="info-box-text-sm" style="font-size: 11px;">Purchase Order</span>
              <span class="info-box-number-sm">' . number_format(get_todays_purchase_orders(null,Today(),Today())) . '</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        <!-- /.col -->
        
        <a data-toggle=\'control-sidebar\' class="bell_icon" >
        <div class="col-md-2 col-sm-6 col-xs-12">
          <div class="info-box-sm">
            <span class="info-box-icon-sm bg-yellow"><i class="ion ion-ios-calculator" style="margin-top: 14px;"></i></span>

            <div class="info-box-content-sm">
              <span class="info-box-text-sm" style="font-size: 11px;">Vendor Payment
              </br>
              </span>
              <span class="info-box-number-sm">' . number_format(get_vendor_payments(null,Today(),Today())) . '</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        <!-- /.col -->
        
        <a data-toggle=\'control-sidebar\' class="bell_icon" >
        <div class="col-md-2 col-sm-6 col-xs-12">
          <div class="info-box-sm">
            <span class="info-box-icon-sm bg-red"><i class="fa fa-exchange" style="margin-top: 14px;"></i></span>

            <div class="info-box-content-sm">
              <span class="info-box-text-sm" style="font-size: 11px;">Sales Return</span>
              <span class="info-box-number-sm">' . number_format(get_todays_sales_return(null,Today(),Today())) . '</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </a>
        <!-- /.col -->
      </div>';

        echo "</div>"; //row

// 3rd line widgets
        //**Alerts Boxes ***/
        echo '<div class="row">';
        echo '<div class="">';
        echo '<div class="col-lg-3">';

        echo '<div class="box box-danger">
            <div class="box-header with-border bg-red" >
              <h3 class="box-title" >Sales Alerts</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
                <li class="item" >
                 <a href="' . $path_to_root . '/sales/inquiry/sales_orders_view.php?OutstandingOnly=1" target="_blank" ><i class="icon fa fa-warning"> </i> <span style="font-size: 12px;color: #000000">SO\'s waiting approval</span><span class="pull-right text-red">'.get_total_so_approval_waiting().'</span></a> 
                </li>
                <!-- /.item -->
                
                <li class="item" >
                 <a href="' . $path_to_root . '/sales/inquiry/sales_orders_view.php?OutstandingOnly=1" target="_blank"><i class="icon fa fa-truck"> </i> <span style="font-size: 12px;color: #000000"> Pending Sales Deliveries</span><span class="pull-right text-red">' . get_total_pending_sales_deliveries() . '</span></a> 
                </li>
                <!-- /.item -->
                                
                <li class="item" >
                 <a href="' . $path_to_root . '/sales/inquiry/sales_deliveries_view.php?OutstandingOnly=1" target="_blank" ><i class="icon fa fa-clone"> </i> <span style="font-size: 12px;color: #000000">Not Invoiced Deliveries </span><span class="pull-right text-red">' . get_total_pending_invoice() . '</span></a> 
                </li>
                <!-- /.item -->
                 <li class="item" >
                 <a href="' . $path_to_root . '/sales/inquiry/customer_allocation_inquiry.php?" target="_blank" ><i class="icon fa fa-money"> </i> <span style="font-size: 12px;color: #000000">Pending Inv. Allocations </span><span class="pull-right text-red">' . get_pending_inv_alloc() . '</span></a> 
                </li> 
                <!-- /.item -->
                
              </ul>
            </div>
          </div>
         </div>'; //first alert end

        echo '<div class="col-lg-3">';
        echo '<div class="box box-danger">
            <div class="box-header with-border bg-red" >
              <h3 class="box-title" >Purchase Alerts</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
                <li class="item" >
                 <a href="' . $path_to_root . '/purchasing/inquiry/po_search.php?" target="_blank" ><i class="icon fa fa-hourglass-3"> </i> <span style="font-size: 12px;color: #000000">PO\'s waiting approval</span><span class="pull-right text-red">'.get_total_po_approval_waiting().'</span></a> 
                </li>
                <!-- /.item -->
                
                <li class="item" >
                 <a href="' . $path_to_root . '/purchasing/inquiry/po_search.php?" target="_blank"><i class="icon fa fa-truck"> </i> <span style="font-size: 12px;color: #000000">Outstanding GRNs</span><span class="pull-right text-red">'.get_count_grn_approval_waiting().'</span></a> 
                </li>
                <!-- /.item -->
                <li class="item" >
                 <a href="' . $path_to_root . '/purchasing/supplier_invoice.php?New=1" target="_blank"><i class="icon fa fa-user"> </i> <span style="font-size: 12px;color: #000000">Pending Supplier Bills </span><span class="pull-right text-red">'.get_count_pending_invoiced().'</span></a> 
                </li>
                <!-- /.item -->
                
              </ul>
            </div>
          </div>';
        echo '</div>'; //2nd Alert box end


        echo '<div class="col-lg-3">';
        echo '<div class="box box-danger">
            <div class="box-header with-border bg-red" >
              <h3 class="box-title" >General Ledger Alerts</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
                <li class="item" >
                 <a href="' . $path_to_root . '/gl/inquiry/journal_inquiry.php?" target="_blank" ><i class="icon fa fa-check-square-o"> </i> <span style="font-size: 12px;color: #000000">Vouchers waiting approval </span><span class="pull-right text-red">' . get_total_voucher_count() . '</span></a> 
                </li>
                <!-- /.item -->
                
                <li class="item" >
                 <a href="' . $path_to_root . '/sales/inquiry/sales_orders_view.php?type=32" target="_blank"><i class="icon fa fa-copy"> </i> <span style="font-size: 12px;color: #000000">Outstanding Qoutes </span><span class="pull-right text-red">' . get_pending_qoutes() . '</span></a> 
                </li>
                <!-- /.item -->
                                
                <li class="item" >
                 <a href="' . $path_to_root . '/gl/bank_account_reconcile.php?" target="_blank"><i class="icon fa fa-bank"> </i> <span style="font-size: 12px;color: #000000">Unreconciled Bank Trans.</span><span class="pull-right text-red">' . get_unreconsiled_bank_trans() . '</span></a> 
                </li>
                <!-- /.item -->
                    
                <li class="item" >
                 <a href="' . $path_to_root . '/manufacturing/search_work_orders.php?outstanding_only=1" target="_blank" > <i class="icon fa fa-industry"></i> <span style="font-size: 12px;color: #000000">Outstanding Workorders</span><span class="pull-right text-red">' . get_outstanding_work_order() . '</span></a> 
                </li>
                <!-- /.item -->
                
              </ul>
            </div>
          </div>';
        echo '</div>'; // 3rd Alert box End


        echo '<div class="col-lg-3">';
        echo '<div class="box box-primary ">
            <div class="box-header with-border bg-aqua" >
              <h3 class="box-title"> KPI\'s</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
                <li class="item" >
                 <a href="#" data-toggle="modal" data-target="#sales_income_modal" ><span style="font-size: 12px;color: #000000">Purchase And Payment</span></a> 
                </li>
                <li class="item" >
                 <a href="#" data-toggle="modal" data-target="#sales_funnel" ><span style="font-size: 12px;color: #000000">Sales Funnel</span></a> 
                </li>
                <!-- /.item -->
                <li class="item" >
                 <a href="#" data-toggle="modal" data-target="#global_ratios" ><span style="font-size: 12px;color: #000000">Global Financial Ratios</span></a> 
                </li>
                <!-- /.item -->
                <li class="item" >
                 <a href="#" data-toggle="modal" data-target="#sales_and_recovery" ><span style="font-size: 12px;color: #000000">Sales and Rcovery</span></a> 
                </li>
                <!-- /.item --> 
                <li class="item" >
                 <a href="#" data-toggle="modal" data-target="#eightytwentyscale" ><span style="font-size: 12px;color: #000000">80/20 Customer Analysis</span></a> 
                </li>
                
              </ul>
            </div>
          </div>';
        echo '</div>'; // 4th Box for KPI's


        echo '</div>';
        echo '</div>';// row end div for alerts

// 4th line widgets
        //Taps Panel
        echo "<div class='row hidden-xs' >";
        include_once("$path_to_root/themes/" . user_theme() . "/Dashboard_Widgets/tabspanel.php");
        $_tab = new tabs();
        $_tab->Alltabs();
        echo "</div>";


// widgets only for Mobile
        echo "<div class='row hidden-md hidden-sm hidden-lg'>";
        echo '
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">Donust charts</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body table-responsive no-padding">
						<table class="table">
							<tbody><tr>
								<th style="border-right:1px solid #CCC;">Client</th>
								<th style="border-right:1px solid #CCC;">Client Balances</th>
								<th style="border-right:1px solid #CCC;">Client Profitability</th>
								<th style="border-right:1px solid #CCC;">Salesman Balances</th>
								<th style="border-right:1px solid #CCC;">Top 10 Zones</th>
								<th style="border-right:1px solid #CCC;">Salesmen</th>
								<th style="border-right:1px solid #CCC;">Zone Balances</th>
								<th style="border-right:1px solid #CCC;">Supplier Balances</th>
								<th style="border-right:1px solid #CCC;">Top 10 Suppliers</th>
								<th style="border-right:1px solid #CCC;">Top 10 Sold items</th>
								<th style="border-right:1px solid #CCC;">Items Profitability</th>
								<th style="border-right:1px solid #CCC;">To 10 Bank Position</th>
								<th style="border-right:1px solid #CCC;">To 10 Cost Centres</th>

							</tr>
							<tr>
								<td style="border-right:1px solid #CCC;">';
        include_once("$path_to_root/themes/" . user_theme() . "/Dashboard_Widgets/MBcharts.php");
        $_M = new MBcahrts();
        $_M->Customer();

        echo '</td>
								<td style="border-right:1px solid #CCC;">';

        $_M->CustomerBalances();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Customerprofibility();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->salesbalances();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->tenzone();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Top10Salesmen();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Zonbalances();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->SupplierBalances();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Top10Suppliers();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Top10SoldItems();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Top10ItemsProfitability();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Top10BankPosition();
        echo '</td>
								<td style="border-right:1px solid #CCC;">';
        $_M->Top10CostCentres();
        echo '</td>

							</tr>
							</tbody></table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			';

        echo "</div>";


// Data model
        echo '
		<!-- Modal -->
		<div class="modal fade" id="OverduePurchaseInvoices" role="dialog">';

        include_once("$path_to_root/themes/" . user_theme() . "/InvoicesModals.php");
        $_invoices = new InvoicesModals();

        $_invoices->OverduePurchaseInvoices();
        echo '</div>';

        //**********************************
        echo '
		<!-- Modal -->
		<div class="modal fade" id="OverdueSalesInvoices" role="dialog">';

        include_once("$path_to_root/themes/" . user_theme() . "/InvoicesModals.php");
        $_invoices = new InvoicesModals();

        $_invoices->OverdueSalesInvoices();
        echo '</div>';

        //**********************************

        echo '
		<!-- Modal -->
		<div class="modal fade" id="RecentSalesInvoices" role="dialog">';

        include_once("$path_to_root/themes/" . user_theme() . "/InvoicesModals.php");
        $_invoices = new InvoicesModals();

        $_invoices->RecentSalesInvoices();
        echo '</div>';

        //**********************************

        echo '
		<!-- Modal -->
		<div class="modal fade" id="RecentSaleOrder" role="dialog">';

        include_once("$path_to_root/themes/" . user_theme() . "/InvoicesModals.php");
        $_invoices = new InvoicesModals();
        $_invoices->RecentSaleOrder();
        echo '</div>';

        echo "</section>";

        // Modals For KPI's
        //income and expence
        echo '<div class="modal fade" id="sales_income_modal">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Monthly Purchase And Payment</h4>
              </div>
              <div class="modal-body">';
        //Graph Start
        echo '<span>Select A Month</span>';
        echo '<div class="form-group col-lg-4">
                  <select class="form-control" onchange="myfunc(this);">';
        echo '<option >Months</option >';
        global $all_items;
        $f_year = get_current_fiscalyear();
        $year1 = date('Y', strtotime($f_year['end']));
        $year2 = date('Y', strtotime($f_year['begin']));
        $sql = "SELECT id, IF(`description` ='Jan' || `description` ='Feb' || `description` ='March' || `description` ='Apr' || `description` ='May' || `description` ='June',  CONCAT(description, '-', $year1),  CONCAT(description, '-', $year2)) as month  FROM " . TB_PREF . "month";
        $result = db_query($sql);
        while ($dt = db_fetch($result)) {
            echo '<option value="' . $dt['id'] . '">' . $dt['month'] . '</option>';
        }
        echo '</select>';

        echo '</div>';

        echo ' <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>';

        $begin = begin_fiscalyear();
        $f_year = get_current_fiscalyear();
        $mo = $_POST['month_'];

        if ($_POST['month_'] = 7 || $_POST['month_'] = 8 || $_POST['month_'] = 9 || $_POST['month_'] = 10 || $_POST['month_'] = 11 || $_POST['month_'] = 12) {
            $yr = date('Y', strtotime($f_year['end']));
        }
        if ($_POST['month_'] = 1 || $_POST['month_'] = 2 || $_POST['month_'] = 3 || $_POST['month_'] = 4 || $_POST['month_'] = 5 || $_POST['month_'] = 6) {
            $yr = date('Y', strtotime($f_year['begin']));
        }

        $today = Today();
        if ($_POST['month_'] = 7 || $_POST['month_'] = 8 || $_POST['month_'] = 9 || $_POST['month_'] = 10 || $_POST['month_'] = 11 || $_POST['month_'] = 12) {
            $yr = date('Y', strtotime($f_year['end']));
        }
        if ($_POST['month_'] = 1 || $_POST['month_'] = 2 || $_POST['month_'] = 3 || $_POST['month_'] = 4 || $_POST['month_'] = 5 || $_POST['month_'] = 6) {
            $yr = date('Y', strtotime($f_year['begin']));
        }
        $show1 = date('d', mktime(0, 0, 0, $mo, 1, $yr));
        $show2 = date('d', mktime(0, 0, 0, $mo, 2, $yr));
        $show3 = date('d', mktime(0, 0, 0, $mo, 3, $yr));
        $show4 = date('d', mktime(0, 0, 0, $mo, 4, $yr));
        $show5 = date('d', mktime(0, 0, 0, $mo, 5, $yr));
        $show6 = date('d', mktime(0, 0, 0, $mo, 6, $yr));
        $show7 = date('d', mktime(0, 0, 0, $mo, 7, $yr));
        $show8 = date('d', mktime(0, 0, 0, $mo, 8, $yr));
        $show9 = date('d', mktime(0, 0, 0, $mo, 9, $yr));
        $show10 = date('d', mktime(0, 0, 0, $mo, 10, $yr));
        $show11 = date('d', mktime(0, 0, 0, $mo, 11, $yr));
        $show12 = date('d', mktime(0, 0, 0, $mo, 12, $yr));
        $show13 = date('d', mktime(0, 0, 0, $mo, 13, $yr));
        $show14 = date('d', mktime(0, 0, 0, $mo, 14, $yr));
        $show15 = date('d', mktime(0, 0, 0, $mo, 15, $yr));
        $show16 = date('d', mktime(0, 0, 0, $mo, 16, $yr));
        $show17 = date('d', mktime(0, 0, 0, $mo, 17, $yr));
        $show18 = date('d', mktime(0, 0, 0, $mo, 18, $yr));
        $show19 = date('d', mktime(0, 0, 0, $mo, 19, $yr));
        $show20 = date('d', mktime(0, 0, 0, $mo, 20, $yr));
        $show21 = date('d', mktime(0, 0, 0, $mo, 21, $yr));
        $show22 = date('d', mktime(0, 0, 0, $mo, 22, $yr));
        $show23 = date('d', mktime(0, 0, 0, $mo, 23, $yr));
        $show24 = date('d', mktime(0, 0, 0, $mo, 24, $yr));
        $show25 = date('d', mktime(0, 0, 0, $mo, 25, $yr));
        $show26 = date('d', mktime(0, 0, 0, $mo, 26, $yr));
        $show27 = date('d', mktime(0, 0, 0, $mo, 27, $yr));
        $show28 = date('d', mktime(0, 0, 0, $mo, 28, $yr));
        $show29 = date('d', mktime(0, 0, 0, $mo, 29, $yr));
        $show30 = date('d', mktime(0, 0, 0, $mo, 30, $yr));
        $show31 = date('d', mktime(0, 0, 0, $mo, 31, $yr));

        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        $date2 = date('Y-m-d', mktime(0, 0, 0, $mo, 2, $yr));
        $date3 = date('Y-m-d', mktime(0, 0, 0, $mo, 3, $yr));
        $date4 = date('Y-m-d', mktime(0, 0, 0, $mo, 4, $yr));
        $date5 = date('Y-m-d', mktime(0, 0, 0, $mo, 5, $yr));
        $date6 = date('Y-m-d', mktime(0, 0, 0, $mo, 6, $yr));
        $date7 = date('Y-m-d', mktime(0, 0, 0, $mo, 7, $yr));
        $date8 = date('Y-m-d', mktime(0, 0, 0, $mo, 8, $yr));
        $date9 = date('Y-m-d', mktime(0, 0, 0, $mo, 9, $yr));
        $date10 = date('Y-m-d', mktime(0, 0, 0, $mo, 10, $yr));
        $date11 = date('Y-m-d', mktime(0, 0, 0, $mo, 11, $yr));
        $date12 = date('Y-m-d', mktime(0, 0, 0, $mo, 12, $yr));
        $date13 = date('Y-m-d', mktime(0, 0, 0, $mo, 13, $yr));
        $date14 = date('Y-m-d', mktime(0, 0, 0, $mo, 14, $yr));
        $date15 = date('Y-m-d', mktime(0, 0, 0, $mo, 15, $yr));
        $date16 = date('Y-m-d', mktime(0, 0, 0, $mo, 16, $yr));
        $date17 = date('Y-m-d', mktime(0, 0, 0, $mo, 17, $yr));
        $date18 = date('Y-m-d', mktime(0, 0, 0, $mo, 18, $yr));
        $date19 = date('Y-m-d', mktime(0, 0, 0, $mo, 19, $yr));
        $date20 = date('Y-m-d', mktime(0, 0, 0, $mo, 20, $yr));
        $date21 = date('Y-m-d', mktime(0, 0, 0, $mo, 21, $yr));
        $date22 = date('Y-m-d', mktime(0, 0, 0, $mo, 22, $yr));
        $date23 = date('Y-m-d', mktime(0, 0, 0, $mo, 23, $yr));
        $date24 = date('Y-m-d', mktime(0, 0, 0, $mo, 24, $yr));
        $date25 = date('Y-m-d', mktime(0, 0, 0, $mo, 25, $yr));
        $date26 = date('Y-m-d', mktime(0, 0, 0, $mo, 26, $yr));
        $date27 = date('Y-m-d', mktime(0, 0, 0, $mo, 27, $yr));
        $date28 = date('Y-m-d', mktime(0, 0, 0, $mo, 28, $yr));
        $date29 = date('Y-m-d', mktime(0, 0, 0, $mo, 29, $yr));
        $date30 = date('Y-m-d', mktime(0, 0, 0, $mo, 30, $yr));
        $date31 = date('Y-m-d', mktime(0, 0, 0, $mo, 31, $yr));

        $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date = '$date3'  AND trans.tran_date < '$date4'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per31
		FROM
			" . TB_PREF . "supp_trans trans
		WHERE  trans.type IN(22,1,41)

		";
        $result = db_query($sql, "Transactions could not be calculated");

        ////////Purchasing
        $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver02,
SUM(CASE WHEN trans.tran_date = '$date3' AND trans.tran_date < '$date4'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver30,
SUM(CASE WHEN trans.tran_date = '$date31'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver31
		FROM
			" . TB_PREF . "supp_trans trans
		WHERE  trans.type = 20

		";
        $result2 = db_query($sql, "Transactions could not be calculated");
        echo '<div id="linechart"><canvas id="line-chart" width="800" height="450" style="margin-top: -50px;"></canvas></div>';

        $i = 0;
        while ($myrow = db_fetch($result)) {

            $per01[$i] = $myrow['per01'];
            $per02[$i] = $myrow['per02'];
            $per03[$i] = $myrow['per03'];
            $per04[$i] = $myrow['per04'];
            $per05[$i] = $myrow['per05'];
            $per06[$i] = $myrow['per06'];
            $per07[$i] = $myrow['per07'];
            $per08[$i] = $myrow['per08'];
            $per09[$i] = $myrow['per09'];
            $per10[$i] = $myrow['per10'];
            $per11[$i] = $myrow['per11'];
            $per12[$i] = $myrow['per12'];
            $per13[$i] = $myrow['per13'];
            $per14[$i] = $myrow['per14'];
            $per15[$i] = $myrow['per15'];
            $per16[$i] = $myrow['per16'];
            $per17[$i] = $myrow['per17'];
            $per18[$i] = $myrow['per18'];
            $per19[$i] = $myrow['per19'];
            $per20[$i] = $myrow['per20'];
            $per21[$i] = $myrow['per21'];
            $per22[$i] = $myrow['per22'];
            $per23[$i] = $myrow['per23'];
            $per24[$i] = $myrow['per24'];
            $per25[$i] = $myrow['per25'];
            $per26[$i] = $myrow['per26'];
            $per27[$i] = $myrow['per27'];
            $per28[$i] = $myrow['per28'];
            $per29[$i] = $myrow['per29'];
            $per30[$i] = $myrow['per30'];
            $per31[$i] = $myrow['per31'];
            $i++;
        }


//For Purchasing
        $k = 0;
        $data2 = array();
        $string2 = array();
        while ($myrow = db_fetch($result2)) {
            $ver01[$k] = $myrow['ver01'];
            $ver02[$k] = $myrow['ver02'];
            $ver03[$k] = $myrow['ver03'];
            $ver04[$k] = $myrow['ver04'];
            $ver05[$k] = $myrow['ver05'];
            $ver06[$k] = $myrow['ver06'];
            $ver07[$k] = $myrow['ver07'];
            $ver08[$k] = $myrow['ver08'];
            $ver09[$k] = $myrow['ver09'];
            $ver10[$k] = $myrow['ver10'];
            $ver11[$k] = $myrow['ver11'];
            $ver12[$k] = $myrow['ver12'];
            $ver13[$k] = $myrow['ver13'];
            $ver14[$k] = $myrow['ver14'];
            $ver15[$k] = $myrow['ver15'];
            $ver16[$k] = $myrow['ver16'];
            $ver17[$k] = $myrow['ver17'];
            $ver18[$k] = $myrow['ver18'];
            $ver19[$k] = $myrow['ver19'];
            $ver20[$k] = $myrow['ver20'];
            $ver21[$k] = $myrow['ver21'];
            $ver22[$k] = $myrow['ver22'];
            $ver23[$k] = $myrow['ver23'];
            $ver24[$k] = $myrow['ver24'];
            $ver25[$k] = $myrow['ver25'];
            $ver26[$k] = $myrow['ver26'];
            $ver27[$k] = $myrow['ver27'];
            $ver28[$k] = $myrow['ver28'];
            $ver29[$k] = $myrow['ver29'];
            $ver30[$k] = $myrow['ver30'];
            $ver31[$k] = $myrow['ver31'];
            $data2[$k] = $myrow['total'];
            $string2[$k] = $myrow['class_name'];
            $k++;;
        }

        echo "<script>
function myfunc(a) {
    $.ajax({
        url : './themes/premium/Dashboard_Widgets/helper.php',
        type: 'POST',
        data :{vale:a.value},
        success : function(result) {
          $('#linechart').html(result);
        }
    });
    
    
//  $('#wa').load('./themes/premium/Dashboard_Widgets/helper.php');
}";
        echo 'new Chart(document.getElementById("line-chart"), {
        type: \'line\',
        data: {

             labels: ["' . $show1 . '", "' . $show2 . '", "' . $show3 . '", "' . $show4 . '", "' . $show5 . '", "' . $show6 . '",
     "' . $show7 . '", "' . $show8 . '", "' . $show9 . '", "' . $show10 . '", "' . $show11 . '", "' . $show12 . '",
     "' . $show13 . '", "' . $show14 . '", "' . $show15 . '", "' . $show16 . '", "' . $show17 . '", "' . $show18 . '",
     "' . $show19 . '", "' . $show20 . '", "' . $show21 . '", "' . $show22 . '", "' . $show23 . '", "' . $show24 . '",
     "' . $show25 . '", "' . $show26 . '", "' . $show27 . '", "' . $show28 . '", "' . $show29 . '", "' . $show30 . '",
     "' . $show31 . '"
     ],
     
           datasets:   [{ 
         data: [' . $per01[0] . ', ' . $per02[0] . ', ' . $per03[0] . ', ' . $per04[0] . ', ' . $per05[0] . ',
         ' . $per06[0] . ', ' . $per07[0] . ', ' . $per08[0] . ', ' . $per09[0] . ', ' . $per10[0] . ',
         ' . $per11[0] . ', ' . $per12[0] . ', ' . $per13[0] . ', ' . $per14[0] . ', ' . $per15[0] . ',
         ' . $per16[0] . ', ' . $per17[0] . ', ' . $per18[0] . ', ' . $per19[0] . ', ' . $per20[0] . ',
         ' . $per21[0] . ', ' . $per22[0] . ', ' . $per23[0] . ', ' . $per24[0] . ', ' . $per25[0] . ',
         ' . $per26[0] . ', ' . $per27[0] . ', ' . $per28[0] . ', ' . $per29[0] . ', ' . $per30[0] . ',
         ' . $per31[0] . '
        ],
        label: "Payment",
        borderColor: "#3e95cd",
        fill: false
      }, { 
       data: [' . $ver01[0] . ', ' . $ver02[0] . ', ' . $ver03[0] . ', ' . $ver04[0] . ', ' . $ver05[0] . ', 
         ' . $ver06[0] . ', ' . $ver07[0] . ', ' . $ver08[0] . ', ' . $ver09[0] . ', ' . $ver10[0] . ', 
         ' . $ver11[0] . ', ' . $ver12[0] . ', ' . $ver13[0] . ', ' . $ver14[0] . ', ' . $ver15[0] . ', 
         ' . $ver16[0] . ', ' . $ver17[0] . ', ' . $ver18[0] . ', ' . $ver19[0] . ', ' . $ver20[0] . ', 
         ' . $ver21[0] . ', ' . $ver22[0] . ', ' . $ver23[0] . ', ' . $ver24[0] . ', ' . $ver25[0] . ', 
         ' . $ver26[0] . ', ' . $ver27[0] . ', ' . $ver28[0] . ', ' . $ver29[0] . ', ' . $ver30[0] . ', 
         ' . $ver31[0] . '],
        label: "Purchase",
        borderColor: "#8e5ea2",
        fill: false
      }
    ]
        },
        options: {
            title: {
                display: true
            }
        }
    });


</script>';
        echo '</div>
              <div class="modal-footer">
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->';

        //sales Funnel Modal
        echo "<script src='$path_to_root/themes/" . user_theme() . "/all_js/canvasjs.min.js'></script>";
        echo '<div class="modal fade" id="sales_funnel">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Sales Funnel</h4>
              </div>
              <div class="modal-body">';
        //Graph Start
        echo '
                <div id="chartContainer" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>';
//                 echo'<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>';
        echo '</div>
              <div class="modal-footer">';
        //quatation
        $today = Today();
        $begin = begin_fiscalyear();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT COUNT(order_no) AS quatation,SUM(total) AS qtotal 
                from " . TB_PREF . "sales_orders WHERE trans_type=32 AND  `ord_date`>='$begin1'";
        $quotaresult = db_query($sql);
        $quatmyrow = db_fetch($quotaresult);
        //order
        $sql = "SELECT DISTINCT COUNT(" . TB_PREF . "sales_order_details.order_no) AS orders, SUM(" . TB_PREF . "sales_order_details.quantity * " . TB_PREF . "sales_order_details.unit_price) AS Ototal 
    FROM " . TB_PREF . "sales_orders, " . TB_PREF . "sales_order_details
    WHERE " . TB_PREF . "sales_orders.trans_type=30 
    AND  " . TB_PREF . "sales_orders.ord_date>='$begin1' 
    AND " . TB_PREF . "sales_orders.reference !='auto' 
    AND " . TB_PREF . "sales_orders.total > 0
    AND " . TB_PREF . "sales_order_details.quantity > 0
    AND " . TB_PREF . "sales_orders.order_no = " . TB_PREF . "sales_order_details.order_no

";
        $orderresult = db_query($sql);
        $ordermyrow = db_fetch($orderresult);
        //delivery
        $sql = "SELECT count(trans.type) as delivery, SUM(trans.ov_amount + trans.ov_gst +
     trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh)
      AS Dtotal FROM " . TB_PREF . "debtor_trans as trans ," . TB_PREF . "debtors_master as debtor
      WHERE (debtor.debtor_no = trans.debtor_no AND trans.tran_date >= '$begin1' 
       AND trans.type = 13 AND trans.ov_amount > 0)";
        $deliveryresult = db_query($sql);
        $deliverymyrow = db_fetch($deliveryresult);
        //invoice
        $sql = "SELECT count(trans.type) as invoice, SUM(trans.ov_amount + trans.ov_gst +
     trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh)
      AS Itotal FROM " . TB_PREF . "debtor_trans as trans ," . TB_PREF . "debtors_master as debtor 
      WHERE (debtor.debtor_no = trans.debtor_no AND trans.tran_date >= '$begin1' 
       AND trans.type = 10 AND trans.ov_amount > 0)";
        $invoiceresult = db_query($sql);
        $invoicemyrow = db_fetch($invoiceresult);
        //payment
        $sql = "SELECT count(trans.type) as payment, SUM(trans.ov_amount + trans.ov_gst +
     trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh)
      AS Ptotal FROM " . TB_PREF . "debtor_trans as trans ," . TB_PREF . "debtors_master as debtor 
      WHERE (debtor.debtor_no = trans.debtor_no AND trans.tran_date >= '$begin1' 
       AND trans.type = 12 AND trans.ov_amount > 0)";
        $paymentsresult = db_query($sql);
        $paymentsmyrow = db_fetch($paymentsresult);
//quatation
        $quatation = $quatmyrow['quatation'];
        if ($quatation > 0) {
            $quatation1 = $quatation;
        } else {
            $quatation1 = 0;
        }
        $qtotal = $quatmyrow['qtotal'];

        if ($qtotal > 0) {
            $qtotal1 = $qtotal;
        } else {
            $qtotal1 = 0;
        }
//order
        $order = $ordermyrow['orders'];
        if ($order > 0) {
            $order1 = $order;
        } else {
            $order1 = 0;
        }
        $Ototal = $ordermyrow['Ototal'];
        if ($Ototal > 0) {
            $Ototal1 = $Ototal;
        } else {
            $Ototal1 = 0;
        }
//delivery
        $delivery = $deliverymyrow['delivery'];
        if ($delivery > 0) {
            $delivery1 = $delivery;
        } else {
            $delivery1 = 0;
        }
        $Dtotal = $deliverymyrow['Dtotal'];
        if ($Dtotal > 0) {
            $Dtotal1 = $Dtotal;
        } else {
            $Dtotal1 = 0;
        }
//invoice
        $invoice = $invoicemyrow['invoice'];
        if ($invoice > 0) {
            $invoice1 = $invoice;
        } else {
            $invoice1 = 0;
        }
        $Itotal = $invoicemyrow['Itotal'];
        if ($Itotal > 0) {
            $Itotal1 = $Itotal;
        } else {
            $Itotal1 = 0;
        }
//payment
        $payment = $paymentsmyrow['payment'];
        if ($payment > 0) {
            $payment1 = $payment;
        } else {
            $payment1 = 0;
        }
        $Ptotal = $paymentsmyrow['Ptotal'];
        if ($Ptotal > 0) {
            $Ptotal1 = $Ptotal;
        } else {
            $Ptotal1 = 0;
        }

        echo '<script>
            var chart = new CanvasJS.Chart("chartContainer", {
                	animationEnabled: true,
	theme: "light2", //"light1", "dark1", "dark2"
	title:{
		text: ""
	},
	data: [{
		type: "funnel",
		indexLabelPlacement: "inside",
		indexLabelFontColor: "white",
		toolTipContent: "<b>{label}</b>: {y}",
		indexLabel: "{label}",
		dataPoints: [
			{ y: ' . $qtotal1 . ', label: "Quotation(' . $quatation1 . ')"},
			{ y: ' . $Ototal1 . ', label:"Sale Order(' . $order1 . ')"},
			{ y: ' . $Dtotal1 . ', label: "Delivery(' . $delivery1 . ')" },
			{ y: ' . $Itotal1 . ', label: "Invoice(' . $invoice1 . ')" },
			{ y: ' . $Ptotal1 . ', label: "Payment(' . $payment1 . ')" }
		],
	}]
});
calculatePercentage();
chart.render();

function calculatePercentage() {
	var dataPoint = chart.options.data[0].dataPoints;
	var total = dataPoint[0].y;
	for(var i = 0; i < dataPoint.length; i++) {
		if(i == 0) {
			chart.options.data[0].dataPoints[i].percentage = dataPoint[i].y;
		} else {
			chart.options.data[0].dataPoints[i].percentage = dataPoint[i].y;
		}
	}

            }
            </script>';
        //Sales Funnel ends

        echo '</div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->';

        //Global Financial Ratio
        echo '<div class="modal fade" id="global_ratios">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Global Financial Ratios</h4>
              </div>
              <div class="modal-body">';
        //Graph Start
        include_once("$path_to_root/themes/" . user_theme() . "/Dashboard_Widgets/GlobalFinancialRatios.php");
        $_ratios = new GlobalFinancialRatios();

        $_ratios->RatiosTable();
        echo '</div>
              <div class="modal-footer">
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->';

        function moth_wise_sales_invoice($startdate, $enddate)
        {
            $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS sales  
                 FROM 0_debtor_trans AS trans, 0_debtors_master AS d 
                 WHERE trans.debtor_no=d.debtor_no 
                 AND (trans.type = 10 ) 
                 AND tran_date >= '$startdate' and tran_date <= '$enddate'";
            $r = db_query($sql);
            $b = db_fetch($r);
            return $b['sales'];
        }

        function month_wise_cust_payment($startdate, $enddate)
        {
            $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
                    + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS recovery
                    FROM " . TB_PREF . "debtor_trans AS trans, " . TB_PREF . "debtors_master AS d
                    WHERE trans.debtor_no=d.debtor_no
                    AND (trans.type = " . ST_BANKDEPOSIT . " OR trans.type = " . ST_CUSTPAYMENT . " OR trans.type = " . ST_CRV . ")
                    AND tran_date >= '$startdate' AND tran_date <= '$enddate'";
            $r = db_query($sql);
            $b = db_fetch($r);
            return $b['recovery'];
        }

        //sales Funnel Modal
        echo '<div class="modal fade" id="sales_and_recovery">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Sales And Recovery</h4>';
        echo '</div>
              <div class="modal-body">';
        //Graph Start
        echo '<div id="chartContainer_2" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>';
        echo '</div>
              <div class="modal-footer">';

        //sales_invoices
        $july = moth_wise_sales_invoice($yr . '-07-01', $yr . '-07-31');
        if ($july == '') {
            $july = '0';
        }
        $august = moth_wise_sales_invoice($yr . '-08-01', $yr . '-08-31');
        if ($august == '') {
            $august = '0';
        }
        $sept = moth_wise_sales_invoice($yr . '-09-01', $yr . '-09-30');
        if ($sept == '') {
            $sept = '0';
        }
        $oct = moth_wise_sales_invoice($yr . '-10-01', $yr . '-10-31');
        if ($oct == '') {
            $oct = '0';
        }
        $nov = moth_wise_sales_invoice($yr . '-11-01', $yr . '-11-30');
        if ($nov == '') {
            $nov = '0';
        }
        $dec = moth_wise_sales_invoice($yr . '-12-01', $yr . '-12-31');
        if ($dec == '') {
            $dec = '0';
        }
        $january = moth_wise_sales_invoice(($yr + 1) . '-01-01', ($yr + 1) . '-01-31');
        if ($january == '') {
            $january = '0';
        }
        $febuary = moth_wise_sales_invoice(($yr + 1) . '-02-01', ($yr + 1) . '-02-29');
        if ($febuary == '') {
            $febuary = '0';
        }
        $march = moth_wise_sales_invoice(($yr + 1) . '-03-01', ($yr + 1) . '-03-31');
        if ($march == '') {
            $march = '0';
        }
        $april = moth_wise_sales_invoice(($yr + 1) . '-04-01', ($yr + 1) . '-04-30');
        if ($april == '') {
            $april = '0';
        }
        $may = moth_wise_sales_invoice(($yr + 1) . '-05-01', ($yr + 1) . '-05-31');
        if ($may == '') {
            $may = '0';
        }
        $june = moth_wise_sales_invoice(($yr + 1) . '-06-01', ($yr + 1) . '-06-30');
        if ($june == '') {
            $june = '0';
        }

        //customer_payment
        $july_c_p = month_wise_cust_payment($yr . '-07-01', $yr . '-07-31');
        if ($july_c_p == '') {
            $july_c_p = '0';
        }
        $august_c_p = month_wise_cust_payment($yr . '-08-01', $yr . '-08-31');
        if ($august_c_p == '') {
            $august_c_p = '0';
        }
        $sept_c_p = month_wise_cust_payment($yr . '-09-01', $yr . '-09-30');
        if ($sept_c_p == '') {
            $sept_c_p = '0';
        }
        $oct_c_p = month_wise_cust_payment($yr . '-10-01', $yr . '-10-31');
        if ($oct_c_p == '') {
            $oct_c_p = '0';
        }
        $nov_c_p = month_wise_cust_payment($yr . '-11-01', $yr . '-11-30');
        if ($nov_c_p == '') {
            $nov_c_p = '0';
        }
        $dec_c_p = month_wise_cust_payment($yr . '-12-01', $yr . '-12-31');
        if ($dec_c_p == '') {
            $dec_c_p = '0';
        }
        $january_c_p = month_wise_cust_payment(($yr + 1) . '-01-01', ($yr + 1) . '-01-31');
        if ($january_c_p == '') {
            $january_c_p = '0';
        }
        $febuary_c_p = month_wise_cust_payment(($yr + 1) . '-02-01', ($yr + 1) . '-02-29');
        if ($febuary_c_p == '') {
            $febuary_c_p = '0';
        }
        $march_c_p = month_wise_cust_payment(($yr + 1) . '-03-01', ($yr + 1) . '-03-31');
        if ($march_c_p == '') {
            $march_c_p = '0';
        }
        $april_c_p = month_wise_cust_payment(($yr + 1) . '-04-01', ($yr + 1) . '-04-30');
        if ($april_c_p == '') {
            $april_c_p = '0';
        }
        $may_c_p = month_wise_cust_payment(($yr + 1) . '-05-01', ($yr + 1) . '-05-31');
        if ($may_c_p == '') {
            $may_c_p = '0';
        }
        $june_c_p = month_wise_cust_payment(($yr + 1) . '-06-01', ($yr + 1) . '-06-30');
        if ($june_c_p == '') {
            $june_c_p = '0';
        }


        //sales and recovery
        echo '<script>
            var chart = new CanvasJS.Chart("chartContainer_2", {
                animationEnabled: true,
                title:{
                    text: ""
                },	
                axisY: {
                    title: "",
                    titleFontColor: "#4F81BC",
                    lineColor: "#4F81BC",
                    labelFontColor: "#4F81BC",
                    tickColor: "#4F81BC"
                },
                axisY2: {
                    title: "",
                    titleFontColor: "#C0504E",
                    lineColor: "#C0504E",
                    labelFontColor: "#C0504E",
                    tickColor: "#C0504E"
                },	
                toolTip: {
                    shared: true
                },
                legend: {
                    cursor:"pointer",
                    itemclick: toggleDataSeries
                },
                data: [{
                    type: "column",
                    name: "Total Sales Invoices",
                    legendText: "Sales Invoices",
                    showInLegend: true, 
                    dataPoints:[
                        { label: "July", y: ' . $july . ' },
                        { label: "Aug", y:  ' . $august . ' },
                        { label: "sept", y: ' . $sept . ' },
                        { label: "Oct", y: ' . $oct . ' },
                        { label: "Nov", y: ' . $nov . '},
                        { label: "Dec", y: ' . $dec . '},
                        { label: "Jan", y: ' . $january . ' },
                        { label: "Feb", y: ' . $febuary . ' },
                        { label: "Mar", y: ' . $march . '},
                        { label: "Apr", y: ' . $april . ' },
                        { label: "May", y: ' . $may . '},
                        { label: "June", y: ' . $june . ' }
                    ]
                },
                {
                    type: "column",	
                    name: "Total Customer Payment",
                    legendText: "Cust Payment",
                    axisYType: "secondary",
                    showInLegend: true,
                    dataPoints:[
                       { label: "July", y: ' . $july_c_p . ' },
                        { label: "Aug", y:  ' . $august_c_p . ' },
                        { label: "sept", y: ' . $sept_c_p . ' },
                        { label: "Oct", y: ' . $oct_c_p . ' },
                        { label: "Nov", y: ' . $nov_c_p . '},
                        { label: "Dec", y: ' . $dec_c_p . '},
                        { label: "Jan", y: ' . $january_c_p . ' },
                        { label: "Feb", y: ' . $febuary_c_p . ' },
                        { label: "Mar", y: ' . $march_c_p . '},
                        { label: "Apr", y: ' . $april_c_p . ' },
                        { label: "May", y: ' . $may_c_p . '},
                        { label: "June", y: ' . $june_c_p . ' }
                    ]
                }]
            });
            chart.render();
            
            function toggleDataSeries(e) {
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                }
                else {
                    e.dataSeries.visible = true;
                }
                chart.render();
            }

            </script>';
        echo ' </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->';

        //eightytwentyscale
        echo '<div class="modal fade" id="eightytwentyscale">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">80/20 Customer Analysis</h4>';
        echo '</div>
              <div class="modal-body">';
        //Graph Start
echo'<div id="chartContainer_8" style="height: 360px;"></div>';
//        echo '<div id="chartContainer_2" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>';
        echo '</div>
              <div class="modal-footer">';
        $today = Today();
        $today1 = date2sql($today);
        $mo = date('m',strtotime($today1));
        $yr = date('Y',strtotime($today1));
        $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);

        $startdate = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));

        $begin = begin_fiscalyear();
        $begin1 = date2sql($begin);

        if($mon_days==30)
            $date_end = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
        else
            $date_end = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));

        $sql = "SELECT SUM((ov_amount + ov_discount) * rate*IF(trans.type = ".ST_CUSTCREDIT.", -1, 1)) AS total,d.debtor_no, d.name FROM
		".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d WHERE trans.debtor_no=d.debtor_no
		AND (trans.type = ".ST_SALESINVOICE." OR trans.type = ".ST_CUSTCREDIT.")
		AND tran_date >= '$begin1' AND tran_date <= '$date_end' GROUP by d.debtor_no ORDER BY total DESC, d.debtor_no ";

        $result = db_query($sql);
        $label_data = array();
        $i = 0;

        while ($myrow = db_fetch($result))
        {
            $y = $myrow['total'];
            $num = (int)$y;
            array_push($label_data,['label'=> $myrow['name'],'y'=>$num]);
            $i++;
        }
        echo "<script> var label_and_data =".json_encode($label_data) ."</script>";

                echo'<script>
var chart = new CanvasJS.Chart("chartContainer_8", {
	title:{
		text: ""
	},
	 width: 800,
	axisY: {
		title: "",
		lineColor: "#4F81BC",
		tickColor: "#4F81BC",
		labelFontColor: "#4F81BC"
	},
	axisY2: {
		title: "",
		suffix: "%",
		lineColor: "#C0504E",
		tickColor: "#C0504E",
		labelFontColor: "#C0504E"
	},
	data: [{
		type: "column",
		dataPoints:label_and_data
	}]
});
chart.render();
createPareto();	

function createPareto(){
	var dps = [];
	var yValue, yTotal = 0, yPercent = 0;

	for(var i = 0; i < chart.data[0].dataPoints.length; i++)
		yTotal += chart.data[0].dataPoints[i].y;

	for(var i = 0; i < chart.data[0].dataPoints.length; i++){
		yValue = chart.data[0].dataPoints[i].y;
		yPercent += (yValue / yTotal * 100);
		dps.push({label: chart.data[0].dataPoints[i].label, y: yPercent});
	}
	
	chart.addTo("data",{type:"line", yValueFormatString: "0.##\"%\"", dataPoints: dps});
	chart.data[1].set("axisYType", "secondary", false);
	chart.axisY[0].set("maximum", yTotal);
	chart.axisY2[0].set("maximum", 100);
}

</script>';
        echo ' </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->';

    }
}
?>