<?php

class renderer
{
    function wa_header()
    {

        page(_($help_context = " "), false, true);
    }

    function wa_footer($title)
    {
        end_page(false, true);

    }

    function menu_header($title, $no_menu, $is_index)
    {

        global $path_to_root,$SysPrefs, $help_base_url,$img,$img2,$img3,$img4, $systypes_array, $db_connections;
        $local_path_to_root = $path_to_root;
        global $leftmenu_save, $app_title, $version;

        include_once($path_to_root ."/themes/premium/dashboard_db.php");



// mycode here


//   if($db_connections[$_SESSION["wa_current_user"]->company]["name"] != 'DEMO' ) {


// echo'

// <style>
// /* .modal{
//     display:none;
//     position: fixed;
//     z-index:1;
//     left: 0;
//     top:0;
//     height: 100%;
//     width:100%;
//     overflow: auto;
//     background-color: rgba(0,0,0,0.5);
//   } */

//   .modal-content{
//     background-color:#f4f4f4;
//     margin:auto;
//     width:50%;
//     border:1px solid #ccc;
//     border-radius:5px;
//     box-shadow: 0 5px 8px 0 rgba(0,0,0,0.2),0 7px 20px 0 rgba(0,0,0,0.17);
//     animation-name:modalopen;
//     animation-duration:1s;
//   }

//   .modal-header h2, .modal-footer h4{
//     margin:0;
//   }

//   .modal-header{
//  text-align:center;
//     background:#4899dd;
//     padding:10px;
//     color:#fff;
//   }

//   .modal-body{

//     text-align:center;
//     /* padding:10px 20px; */
//   }
//   /* #img > img{
// width:auto;
// background:transparent;
// margin-top:-12px;
// height:50px;
//   } */



//   .modal-footer{
//     background:#4899dd;
//     padding:10px;
//     color:#fff;
//     text-align: center;
//   }

//   .closeBtn{
//     color:#ccc;
//     float: right;
//     font-size:30px;
//     color:#fff;
//   }

//   .closeBtn:hover,.closeBtn:focus{
//     color:#000;
//     text-decoration: none;
//     cursor:pointer;
//   }

//   @keyframes modalopen{
//     from{ opacity: 0}
//     to {opacity: 1}

// </style>


// <!-- <button id="modalBtn" class="button">Click Here</button> -->
// <div id="simpleModal" class="modal">
//   <div class="modal-content">
//     <div class="modal-header">
//         <span class="closeBtn">&times;</span>
//         <h2>Welcome To Hisaab.pk</h2>
//     </div>
//     <div class="modal-body">
//       <h4 class="first" id="first"></h4>

//     </div>
//     <div class="modal-footer">
//       <h4>Support Numbers: 021-3433 0907, 021-3433 0999, 0300-811 57 37 </h4>
//     </div>
//   </div>
// </div>


//   <script>

//   // Get modal element
//             var modal = document.getElementById(\'simpleModal\');
// // Get open modal button
//             var modalBtn = document.getElementById(\'modalBtn\');
// // Get close button
//             var closeBtn = document.getElementsByClassName(\'closeBtn\')[0];
// // Listen for open click
// //modalBtn.addEventListener(\'click\', openModal);
// // Listen for close click
//             closeBtn.addEventListener(\'click\', closeModal);
// // Listen for outside click
//             window.addEventListener(\'click\', outsideClick);

// // Function to open modal
//             function openModal(){
//                 modal.style.display = \'block\';
//             }

// // Function to close modal
//             function closeModal(){
//                 modal.style.display = \'none\';
//             }

// // Function to close modal if outside click
//             function outsideClick(e){
//                 if(e.target == modal){
//                     modal.style.display = \'none\';
//                 }
//             }



//             function postform(){

//                 var xhr=new XMLHttpRequest();

//                 //  one way
//                 xhr.onreadystatechange = function() {
//                     if (this.readyState == 4 && this.status == 200) {

//                         console.log(this.responseText);

//                         document.getElementById("first").innerHTML =this.responseText;
// //                            alert(\'ready111\');
//                     }
//                 };


//                 xhr.open(\'POST\',\'output.php\',true);
//                 xhr.send();



//             }

// if(window.location.href=="https://erp30.com/index.php"||window.location.href=="https://erp30.com/index.php?application=dashboard"){
//             window.onload=function(){

//                 setTimeout(function(){
//             postform();


//                  openModal();

//                 },1000)

//             }

// }



//   </script>


// ';}



//mycode end here
//Code written by purpledesign.in Jan 2014
        function dateDiff($date)
        {
            $mydate= date("Y-m-d H:i:s");
            $theDiff="";
            //echo $mydate;//2014-06-06 21:35:55
            $datetime1 = date_create($date);
            $datetime2 = date_create($mydate);
            $interval = date_diff($datetime1, $datetime2);
            //echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
            $min=$interval->format('%i');
            $sec=$interval->format('%s');
            $hour=$interval->format('%h');
            $mon=$interval->format('%m');
            $day=$interval->format('%d');
            $year=$interval->format('%y');
            if($interval->format('%i%h%d%m%y')=="00000")
            {
                //echo $interval->format('%i%h%d%m%y')."<br>";
                return $sec." Seconds";

            }

            else if($interval->format('%h%d%m%y')=="0000"){
                return $min." Minutes";
            }


            else if($interval->format('%d%m%y')=="000"){
                return $hour." Hours";
            }


            else if($interval->format('%m%y')=="00"){
                return $day." Days";
            }

            else if($interval->format('%y')=="0"){
                return $mon." Months";
            }

            else{
                return $year." Years";
            }

        }

        function get_last_update()
        {
            $sql = "SELECT UNIX_TIMESTAMP(stamp) as unix_stamp
    , user, id, trans_no as trans_no, type as type
	FROM ".TB_PREF."audit_trail
	ORDER BY id DESC
	LIMIT 1
	";
            $result = db_query($sql, "could not get last update");
            //$row = db_fetch_row($result);
            return $result;
        }

        $history_row = get_last_update();
        $get_last_history = db_fetch_row($history_row);
        $last_user_id = $get_last_history[1];
        $last_user_name = get_user_name($last_user_id);

        $stamp = date("Y-m-d H:i:s", $get_last_history[0]);
        $diff = dateDiff($stamp);


        $sql = "SELECT value FROM ".TB_PREF."sys_prefs WHERE `name`='coy_logo' ";
        $result = db_query($sql, "could not get sales type");
        $row = db_fetch_row($result);

        $User_logo ;
        //var_dump($row[0]);

        // Build screen header
        $leftmenu_save = "";
        $sel_app = $_SESSION['sel_app'];
        if($no_menu != 1)
        {
        echo " <div class='wrapper'>\n";

        //	echo "<div id='topsection'> \n";
        //	echo "  <div class='innertube'> \n";


        // images for notification icons
        $img = "$path_to_root/themes/".user_theme()."/images/receipt-plus-icon.png";
        $img2 = "$path_to_root/themes/".user_theme()."/images/payment-icon.png";
        $img3 = "$path_to_root/themes/".user_theme()."/images/Inventory-maintenance-icon.png";
        $img4 = "$path_to_root/themes/".user_theme()."/images/help-browser.png";

        echo"<header class='main-header' style='position: fixed;width: 100%'>";

        //  <!-- Logo -->
        echo"<a style='background-color:#fff;cursor:Default' href='#' class='logo'>";
        // <!-- mini logo for sidebar mini 50x50 pixels -->
        echo"<span class='logo-mini'><img src='$path_to_root/themes/".user_theme()."/images/SM-logo.png' style='margin-top:12px;' ></span>";
//              <!-- logo for regular state and mobile devices -->
        echo"<img src='$path_to_root/themes/".user_theme()."/images/hisaab_logo_new.png' class='' height='40px;' width='210px;'>";
        echo"</a>";
        // <!-- Header Navbar: style can be found in header.less -->
        echo"<nav class='navbar navbar-static-top' role='navigation'>";

        // <!-- Sidebar toggle button-->
        echo"<a href='#' class='sidebar-toggle' data-toggle='offcanvas' role='button'>";
        echo"<span class='sr-only'>Toggle navigation</span>";
        echo"</a>";


        // Reports
        echo "
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class='collapse navbar-collapse pull-left' id='navbar-collapse'>
          <ul class='nav navbar-nav'>

            <li><a href='$path_to_root/admin/display_prefs.php?' class='fa fa-cogs'> </a></li>
            <!--<li><a href='javascript:location.reload(true)' class='fa fa-refresh'> </a></li>-->
            
             <li class='dropdown'>
              <a href='#' data-toggle='dropdown' class='fa fa-pie-chart'><span class='caret'></span></a>
              <ul class='dropdown-menu' role='menu'>
                <li><a href='$path_to_root/POS/inquiry/pos_inquiry.php?'>
                <i class='fa fa-calendar-check-o text-green'></i>
                Today's Sales Invoices</a></li>
                <li class='divider'></li>
                <li><a href='$path_to_root/reporting/reports_main.php?Class=0'>
                <i class='fa fa-bar-chart text-blue'></i>
                Customer Reports</a></li>
                <li><a href='$path_to_root/reporting/reports_main.php?Class=1'>
                <i class='fa fa-line-chart text-blue'></i>
                Supplier Reports</a></li>
                <li><a href='$path_to_root/reporting/reports_main.php?Class=2'>
                <i class='fa fa-pie-chart text-blue'></i>
                Inventory Reports</a></li>
                <li><a href='$path_to_root/reporting/reports_main.php?Class=6'>
                <i class='fa fa-area-chart text-blue'></i>                
                General Ledger Reports</a></li>
                <li class='divider'></li>
                <li><a href='$path_to_root/gl/inquiry/gl_trial_balance.php?'>
                <i class='fa fa-balance-scale text-red'></i>                
                Trial Balance</a></li>
                <li><a href='$path_to_root/gl/inquiry/balance_sheet.php?'>
                <i class='fa fa-calculator text-green'></i>
                Balance Sheet</a></li>
                <li><a href='$path_to_root/gl/inquiry/profit_loss.php?'>
                <i class='glyphicon glyphicon-sort text-aqua'></i>                
                Profit and Loss</a></li>
                </ul>
            </li>";

        global $leftmenu_save, $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'JUNCTIONZ') {

            echo"
          <!-- Quick Links-->
            <li class='dropdown'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Quick Links <span class='caret'></span></a>
              <ul class='dropdown-menu' role='menu'>
                <li><a href='$path_to_root/sales/sales_order_entry.php?NewInvoice=0'>
                <i class='fa fa-line-chart text-blue' ></i>
                Inbound - Invoice</a></li>
                <li><a href='$path_to_root/sales/credit_note_entry.php?'>
                <i class='fa fa-line-chart text-blue' ></i>
                Inbound - Credit Note</a></li>
                <li><a href='$path_to_root/sales/customer_payments.php?'>
                <i class='fa fa-arrow-circle-left text-blue' ></i>
                Customer Payment</a></li>
                <li><a href='$path_to_root/purchasing/po_entry_items.php?NewInvoice=Yes'>
                <i class='fa fa-shopping-cart text-blue' ></i>
                Outbound - Supplier Invoice</a></li>
                <li><a href='$path_to_root/purchasing/supplier_credit.php?New=1'>
                <i class='fa fa-line-chart text-blue' ></i>
               Outbound - Supplier Credit Note</a></li>
                <li><a href='$path_to_root/gl/gl_bank.php?NewPayment=Yes'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
                Bank Payment Voucher</a></li>
                <li><a href='$path_to_root/gl/gl_bankCV.php?NewPayment=Yes'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
                Cash Payment Voucher</a></li>
                <li><a href='$path_to_root/purchasing/supplier_payment.php?'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
                Supplier Payment Voucher</a></li>
                <li class='divider'></li>
<!--
                <li><a href='$path_to_root/inventory/inquiry/stock_status.php?'>
                <i class='fa fa-barcode text-green' ></i>
                Inventory Item Status</a></li>-->
                
                <li><a href='$path_to_root/reporting/reports_main.php?Class=6&REP_ID=7045'>
                <i class='fa fa-bar-chart text-green' ></i>
                SOA</a></li>
                
                <li><a href='$path_to_root/gl/inquiry/gl_account_inquiry.php?'>
                <i class='fa fa-bar-chart text-green' ></i>
                GL Inquiry</a></li>
                <li><a href='$path_to_root/gl/inquiry/journal_inquiry.php?'>
                <i class='fa fa-search text-green' ></i>
                Voucher Inquiry</a></li>
                <li class='divider'></li>
                <li><a href='$path_to_root/admin/void_transaction.php?'>
                <i class='fa fa-times-circle text-red' ></i>
                Void a Transaction</a></li>
              </ul>
            </li>";
        }
        else
        {

            echo"
          <!-- Quick Links-->
            <li class='dropdown'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Quick Links <span class='caret'></span></a>
              <ul class='dropdown-menu' role='menu'>
                <li><a href='$path_to_root/sales/sales_order_entry.php?NewInvoice=0'>
                <i class='fa fa-line-chart text-blue' ></i>
                Direct Sales Invoice</a></li>
                <li><a href='$path_to_root/sales/customer_payments.php?'>
                <i class='fa fa-arrow-circle-left text-blue' ></i>
                Customer Payment</a></li>
                <li><a href='$path_to_root/purchasing/po_entry_items.php?NewInvoice=Yes'>
                <i class='fa fa-shopping-cart text-blue' ></i>
                Direct Purchase Invoice</a></li>
                <li><a href='$path_to_root/purchasing/supplier_payment.php?'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
                Supplier Payment</a></li>
                <li><a href='$path_to_root/purchasing/supplier_credit.php?New=1'>
                <i class='fa fa-line-chart text-blue' ></i>
               Supplier Credit Note</a></li>
               
               
                <li><a href='$path_to_root/gl/gl_bankCV.php?NewPayment=Yes'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
                Cash Payment Voucher</a></li>
                
                 <li><a href='$path_to_root/gl/gl_bankCV.php?NewDeposit=Yes'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
               Cash Receipt  Voucher</a></li>
                
                
                
                <li class='divider'></li>
                <li><a href='$path_to_root/inventory/inquiry/stock_status.php?'>
                <i class='fa fa-barcode text-green' ></i>
                Inventory Item Status</a></li>
                <li><a href='$path_to_root/gl/inquiry/gl_account_inquiry.php?'>
                <i class='fa fa-bar-chart text-green' ></i>
                GL Inquiry</a></li>
                <li><a href='$path_to_root/gl/inquiry/journal_inquiry.php?'>
                <i class='fa fa-search text-green' ></i>
                Voucher Inquiry</a></li>
                <li class='divider'></li>
                <li><a href='$path_to_root/admin/void_transaction.php?'>
                <i class='fa fa-times-circle text-red' ></i>
                Void a Transaction</a></li>
              </ul>
            </li>";
        }
        echo"
        <!-- Setups -->
              <li class='dropdown'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Setup<span class='caret'></span></a>
              <ul class='dropdown-menu' role='menu'>
                <li><a href='$path_to_root/sales/manage/customers.php?' >
                <i class='fa fa-users text-aqua' ></i> 
                Add Customer</a></li>
                <li><a href='$path_to_root/purchasing/manage/suppliers.php?''>
                <i class='fa fa-user-plus text-green' ></i> 
                Add Supplier</a></li>
                <li><a href='$path_to_root/inventory/manage/items.php'>
                <i class='fa fa-barcode text-yellow' ></i>
                Add Item</a></li>
                <li><a href='$path_to_root/gl/manage/gl_accounts.php'>
                <i class='fa fa-plus text-red' ></i>
                Add GL Account</a></li>
                </ul>
            </li>
             <li class='dropdown'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Help<span class='caret'></span></a>
              <ul class='dropdown-menu' role='menu'>
                <li><a href='http://support.hisaab.pk' target=_blank >
                <i class='fa fa-lightbulb-o text-aqua' ></i>Knowledge Base</a></li>
                <li><a href='https://www.youtube.com/channel/UCzDWb6v8k88MlLclvKjYYZw/featured' target=_blank >
                <i class='fa fa-youtube-play text-red' ></i>Videos Tutorials</a></li>                
                <li><a href='https://anydesk.com/download' target=_blank >
                <i class='fa fa-desktop text-red' ></i>Download AnyDesk</a></li>                
                <li><a href='Https://remotedesktop.google.com/support' target=_blank >
                <i class='fa fa-google text-aqua' ></i>Google Remote Desktop</a></li>                
                <li><a href='http://dys-solutions.com/support' target=_blank >
                <i class='fa fa-support text-blue' ></i>Support Ticket</a></li>                 
                <li><a href='http://hisaab.pk/income-tax-calculator-pakistan-2015-2016/' target=_blank >
                <i class='fa fa-calculator text-aqua'></i>Check Tax Calculator</a></li>
                               
                <li><a href='http://support.hisaab.pk/faq/accounting-software/'  target=_blank >
                <i class='fa fa-question text-yellow' ></i>FAQ's</a></li>

                <li><a href='http://hisaab.pk/terms-of-service/' target=_blank >
                <i class='fa fa-info-circle text-blue' ></i>Terms of Service</a></li>

                <li><a href='http://hisaab.pk/privacy_policy/' target=_blank >
                <i class='fa fa-lock text-red' ></i>Privacy Policy</a></li>

                <li><a href='http://hisaab.pk/blog/' target=_blank >
                <i class='fa fa-wordpress text-blue' ></i>Check our Blog</a></li>

                <li><a href='http://hisaab.pk/contact-us/' target=_blank >
                <i class='fa fa-phone text-green' ></i>Contact Us</a></li>


                
                </ul>
            </li>";

        $today = date2sql(Today());
        $fromdate = date2sql(Today()) . " 00:00:00";
        $todate = date2sql(Today()). " 23:59.59";

        echo "
               </ul>
            </li>        
          </ul>
          ";



        echo "
<div   
style='display: inline;' id='my_formnew' name='my_formnew' accept-charset=\"ISO-8859-1\">
        <!-- Search Box -->
        
            <div class='navbar-form navbar-left' role='search' >
            <div class='form-group' >
              <input type='text' dirname=''  class='form-control' id='navbar-search-inputnew'  
            name='search_id'  placeholder='Search Transactions...' >
            </div>
            </div>
            </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        
</div> ";

        echo"
<script type='text/javascript'>

var input1 = document.getElementById('navbar-search-inputnew');
input1.addEventListener('keyup', function(event) {

    event.preventDefault();
    if (event.keyCode === 13) {
 var url = '$path_to_root/gl/inquiry/journal_inquiry.php?search_id='+input1.value  ;
   window.location = url;
   document.my_formnew.submit();
    }
});


</script>";

        //  <!-- Navbar Right Menu -->

        echo"<div class='navbar-custom-menu'>";
        echo"<ul class='nav navbar-nav'>";

        //<!-- Messages: style can be found in dropdown.less-->
        include_once("$path_to_root/themes/".user_theme()."/notification_ui.php");
        $_dash= new notification();
        $_dash->All_notification();


// <!-- User Account: style can be found in dropdown.less -->
        echo"<li class='dropdown user user-menu' >";
        echo"<a href='#' class='dropdown-toggle' data-toggle='dropdown'>";
        if ($row[0] == "")
        {
            $User_logo = "$path_to_root/themes/".user_theme()."/images/No_Image_Available.png";
            echo " <img src='".$User_logo."' class='user-image' alt='User Image'>";
            /*   $img ="$path_to_root/themes/grayblue/images/$row[0]";
            return $img;*/
        }else
        {
            $User_logo = company_path() . "/images/" .$row[0];
            echo " <img src='".$User_logo."' class='user-image' alt='User Image'>";

        }

        echo " <span class='hidden-xs'> " . $db_connections[$_SESSION["wa_current_user"]->company]["name"] ."</span>";
        echo " </a>";
        echo"<ul class='dropdown-menu'>";
        echo"<!-- User image -->";
        echo " <li style='height: 200px;' class='user-header'>";
        //$logo = company_path() . "/images/" . $this->company['coy_logo'];
        echo "<img src='".$User_logo."' class='img-circle' alt='User Image'>";
        echo " <p>";
        echo   $db_connections[$_SESSION["wa_current_user"]->company]["name"] ;
        $begin = begin_fiscalyear();
        $end=end_fiscalyear();
        $begin1 = date2sql($begin);
        $end1= date2sql($end);
        echo "  <small>Current Fiscal Year</small>";
        echo "  <small>".$begin." / ".$end."</small>";
        echo"" . $_SESSION["wa_current_user"]->name . "/".show_users_online()."";
        echo " </p>";

        echo "  </li>";
        echo"<!-- Menu Body -->";
        echo"<!-- Menu Footer-->";
        echo "  <li class='user-footer'>";
        echo "    <div class='pull-left'>";
        echo"<a class='btn btn-default btn-flat' href='$local_path_to_root/admin/display_prefs.php?'><i class='fa fa-cogs'></i> <span>"._("Configuration")."</span></a>";
        echo "   </div>";

        echo "    <div class='pull-left'>";
        echo"<a class='btn btn-default btn-flat' href='$local_path_to_root/admin/change_current_user_password.php?selected_id=".$_SESSION["wa_current_user"]->username."''><i class='fa fa-key'></i> <span>"._("Change Password")."</span></a>";
        echo "   </div>";

        echo "   <div class='pull-right'>";
        echo"<a class='btn btn-default btn-flat' href='$local_path_to_root/access/logout.php?'><i class='fa fa-sign-out'></i> <span>"._("Logout")."</span></a>";
        echo "   </div>";
        echo " </li>";
        echo"</ul>";
        echo "</li>";
        if ($_SESSION['wa_current_user']->can_access('SS_RECIEPT_ORDER')||($_SESSION['wa_current_user']->can_access('SS_PAYMENTS')) || ($_SESSION['wa_current_user']->can_access('SS_AUDIT_TRAIL_VIEW')))
        {
            echo" <li class='bell_icon'>
                <a href='#' data-toggle='control-sidebar'><i style='margin-top: 4px;' class='fa fa-bell'><span class='caret'></span></i>
                 <span class='label label-info'>".trans_count($today)."</span>
                </a>
              </li>";

        }

        echo"</ul>";
        echo"</div>";
        echo"  </nav>";
        echo"</header>";


        // <!-- Left side column. contains the logo and sidebar -->
        echo"<aside class='main-sidebar' style='margin-top: 10px;'>";
        // <!-- sidebar: style can be found in sidebar.less -->

        echo"<section class='sidebar'>";
//        echo"<div class='user-panel'>";
//        echo"<div class='pull-left image'>";
        // echo"<img src='".$User_logo."' class='img-circle' alt='User Image'>";
//        echo"</div>";
//        echo"<div class='pull-left info'>";

        /* echo" <p> " . $db_connections[$_SESSION["wa_current_user"]->company]["name"] ."</p>";
         //echo" <a href='#'><i class='fa fa-circle text-success'></i> Welcome</a>";

         /*$user_name = $_SESSION["wa_current_user"]->name;
         echo" <a href='#'><i class='fa fa-circle text-success'></i> $user_name</a>";*/



//        echo" </div>";
//        echo"</div>";

        echo"
        <div id='my_forms' name='my_forms' class='sidebar-form' >
          <input type='text' id='navbar-search-inputs' name='search_id' class='form-control' placeholder='Search...'>
              
        </div>";

        echo"
<script type='text/javascript'>

var input = document.getElementById('navbar-search-inputs');
input.addEventListener('keyup', function(event) {

    event.preventDefault();
    if (event.keyCode === 13) {
    var url = '$path_to_root/gl/inquiry/journal_inquiry.php?search_id='+ input.value  ;
   window.location = url;
   document.my_forms.submit();
    }
});
</script>";

        //     echo "

        // //   <form action='$path_to_root/gl/inquiry/journal_inquiry.php' method='get' class='sidebar-form'>
        // //     <div class='input-group'>
        // //       <input type='text' name='search_id' class='form-control' placeholder='Search...'>
        // //           <span class='input-group-btn'>
        // //             <button type='submit'  id='search-btn' class='btn btn-flat'><i class='fa fa-search'></i>
        // //             </button>
        // //           </span>
        // //     </div>
        // //   </form>
        //         <!-- /.search form -->
        //   ";

        global $leftmenu_save, $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'BNW') {

            echo " <ul class='sidebar-menu'>";
            //echo" <li class='header'>MAIN NAVIGATION</li>";

            if (!$no_menu) {

                $applications = $_SESSION['App']->applications;

                // $leftmenu_save .= " <li >";
                foreach ($applications as $app) {


                    $acc = access_string($app->name);
                    // var_dump($acc);

                    if ($app->name == "Dash&Board") {
                        $gly = "fa fa-dashboard";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span> " . $acc[0] . "</span>
                              
                            </a>\n";
                    } //Sales
                    else if ($app->name == get_company_pref_display('sales_text')) {
                        $gly = "fa fa-line-chart";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                    } //Purchase
                    else if ($app->name == get_company_pref_display('purchase_text')) {



                        $gly = "fa fa-shopping-cart";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                    }//Inventory
                    else if ($app->name == get_company_pref_display('item_text')) {
                        $sql = "SELECT COUNT(stock_id) FROM " . TB_PREF . "stock_master
                            WHERE inactive != 1
                            AND mb_flag  != 'F'";
                        $result = db_query($sql, "could not get items");
                        $row = db_fetch_row($result);
                        $stock_count = $row[0];

                        $gly = "fa fa-barcode";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                            <small class='label pull-right bg-primary'></small>
                                </span>
                              
                            </a>\n";
                    } //Manufacturing
                    else if ($app->name == get_company_pref_display('manufacture_text')) {
                        $sql = "SELECT COUNT(stock_id) FROM " . TB_PREF . "workorders
                            WHERE closed != 1";
                        $result = db_query($sql, "could not get items");
                        $row = db_fetch_row($result);
                        $open_wo = $row[0];

                        $gly = "fa fa-industry";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                            <small class='label pull-right bg-red'></small>
                                </span>
                              
                            </a>\n";
                    } else if ($app->name == "Fixed Assets") {

                        $sql = "SELECT COUNT(stock_id) FROM " . TB_PREF . "stock_master
                            WHERE inactive != 1
                            AND mb_flag = 'F' ";
                        $result = db_query($sql, "could not get items");
                        $row = db_fetch_row($result);
                        $fixed_asset_count = $row[0];

                        $gly = "fa fa-institution";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                            <small class='label pull-right bg-primary'></small>
                                </span>
                              
                            </a>\n";
                    } else if ($app->name == get_company_pref_display('dim_text')) {
                        $sql = "SELECT COUNT(id) FROM " . TB_PREF . "dimensions
                            WHERE closed != 1";
                        $result = db_query($sql, "could not get dimensions");
                        $row = db_fetch_row($result);
                        $open_proj_count = $row[0];

                        $gly = "fa fa-object-ungroup";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                                <small class='label pull-right bg-red'></small>
                                </span>
  
                            </a>\n";
                    } else if ($app->name == get_company_pref_display('ledger_text')) {
                        $gly = "fa fa-bar-chart";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                    } else if ($app->name == "Human Resources") {
                        $gly = "fa fa-users";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                                <small class='label pull-right bg-green'></small>
                                </span>
                            </a>\n";
                    } else if ($app->name == "Payroll") {
                        $gly = "fa fa-calculator";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                                 <small class='label pull-right bg-green'></small>
                                </span>
                            </a>\n";
                    } else if ($app->name == "Reporting") {
                        $gly = "fa fa-pie-chart";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                    } else if ($app->name == get_company_pref_display('setup_text')) {
                        $gly = "fa fa-wrench";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                    } else {
                        $gly = "fa fa-circle-o";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                    }

                    if ($sel_app == $app->id) {
                        $curr_app_name = $acc[0];
                        $curr_app_link = $app->id;
                    }
                    $leftmenu_save .= "      </li>";
                }

                // $leftmenu_save .= "    </li>";

                /*
                  $leftmenu_save .= " <li class='header'>" . $_SESSION["wa_current_user"]->name . "</li>";
                  $leftmenu_save .= " <li ><a href='$local_path_to_root/admin/display_prefs.php?'><i class='fa fa-cogs'></i> <span>"._("Configuration")."</span></a></li>";
                  $leftmenu_save .= " <li ><a href='$local_path_to_root/admin/change_current_user_password.php?selected_id=".$_SESSION["wa_current_user"]->username."'><i class='fa fa-key'></i> <span>"._("Change password")."</span></a></li>";
                  $leftmenu_save .= " <li ><a href='$local_path_to_root/access/logout.php?'><i class='fa fa-sign-out'></i> <span>"._("Logout")."</span></a></li>\n"; */

                if (!$no_menu)
                    echo $leftmenu_save;

            }
            echo'<li class="header">Contact us on : </li>';
            echo'<li><a href="https://api.whatsapp.com/send?phone=923008115737&text=Hi Hisaab.pk Support i want to ask that" target="_blank"><i style="color:#00cc00;" class="fab fa-whatsapp" target="_blank"></i> <span>What\'s App. </span></a></li>';
            echo'<li><a href="https://m.me/HisaabErp" target="_blank"><i style="color:#0084ff;" class="fab fa-facebook-messenger" ></i> <span>FB Messenger. </span></a></li>';
            echo'<li><a href="skype:hisaab.pk?call" target="_blank"><i  style="color:#2ac7e1;" class="fab fa-skype" ></i> <span>Skype. </span></a></li>';
            echo'<li><a href="https://www.youtube.com/c/HisaabERP" target="_blank"><i  style="color:red;"  class="fab fa-youtube" ></i> <span>Youtube. </span></a></li>';
            echo'<li><a href="https://twitter.com/HisaabERP" target="_blank"><i style="color: #0d6aad;" class="fab fa-twitter"></i> <span>Twitter. </span></a></li>';
            echo "</ul>";
            echo "</section>";
            //<!-- /.sidebar -->
            echo "</aside>";

        }
        else
        {
            echo " <ul class='sidebar-menu'>";
            //echo" <li class='header'>MAIN NAVIGATION</li>";

            if (!$no_menu) {

                $applications = $_SESSION['App']->applications;

                // $leftmenu_save .= " <li >";
                foreach ($applications as $app) {


                    $acc = access_string($app->name);
                    // var_dump($acc);

                    if ($app->name == "Dash&Board") {
                        $gly = "glyphicon glyphicon-dashboard";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                    } //Sales
                    else if ($app->name == get_company_pref_display('sales_text')) {
                        $gly = "fa fa-line-chart";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                    } //Purchase
                    else if ($app->name == get_company_pref_display('purchase_text')) {
                        $gly = "fa fa-shopping-cart";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                    }//Inventory
                    else if ($app->name == get_company_pref_display('item_text')) {
                        $sql = "SELECT COUNT(stock_id) FROM " . TB_PREF . "stock_master
                            WHERE inactive != 1
                            AND mb_flag  != 'F'";
                        $result = db_query($sql, "could not get items");
                        $row = db_fetch_row($result);
                        $stock_count = $row[0];

                        $gly = "fa fa-barcode";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                            <small class='label pull-right bg-primary'>$stock_count</small>
                                </span>

                            </a>\n";
                    } //Manufacturing
                    else if ($app->name == get_company_pref_display('manufacture_text')) {
                        $sql = "SELECT COUNT(stock_id) FROM " . TB_PREF . "workorders
                            WHERE closed != 1";
                        $result = db_query($sql, "could not get items");
                        $row = db_fetch_row($result);
                        $open_wo = $row[0];

                        $gly = "fa fa-industry";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                            <small class='label pull-right bg-red'>$open_wo</small>
                                </span>

                            </a>\n";
                    } else if ($app->name == "Fixed Assets") {

                        $sql = "SELECT COUNT(stock_id) FROM " . TB_PREF . "stock_master
                            WHERE inactive != 1
                            AND mb_flag = 'F' ";
                        $result = db_query($sql, "could not get items");
                        $row = db_fetch_row($result);
                        $fixed_asset_count = $row[0];

                        $gly = "fa fa-institution";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                            <small class='label pull-right bg-primary'>$fixed_asset_count</small>
                                </span>

                            </a>\n";
                    } else if ($app->name == get_company_pref_display('dim_text')) {
                        $sql = "SELECT COUNT(id) FROM " . TB_PREF . "dimensions
                            WHERE closed != 1";
                        $result = db_query($sql, "could not get dimensions");
                        $row = db_fetch_row($result);
                        $open_proj_count = $row[0];

                        $gly = "fa fa-object-ungroup";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                                <small class='label pull-right bg-red'>$open_proj_count</small>
                                </span>

                            </a>\n";
                    } else if ($app->name == get_company_pref_display('ledger_text')) {
                        $gly = "fa fa-bar-chart";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                    } else if ($app->name == "Human Resources") {
                        $gly = "fa fa-users";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                                <small class='label pull-right bg-green'>Beta</small>
                                </span>
                            </a>\n";
                    } else if ($app->name == "Payroll") {
                        $gly = "fa fa-calculator";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "
                                 <small class='label pull-right bg-green'>Beta</small>
                                </span>
                            </a>\n";
                    } else if ($app->name == "Reporting") {
                        $gly = "fa fa-pie-chart";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                    } else if ($app->name == get_company_pref_display('setup_text')) {
                        $gly = "fa fa-wrench";

                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                    } else {
                        $gly = "fa fa-circle-o";
                        $leftmenu_save .= "      <li class='treeview'>";
                        $leftmenu_save .= "
                            <a style='' class='"
                            . ($sel_app == $app->id ? '' : '')
                            . "'href='$local_path_to_root/index.php?application=" . $app->id ."&".
                            SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                    }

                    if ($sel_app == $app->id) {
                        $curr_app_name = $acc[0];
                        $curr_app_link = $app->id;
                    }
                    $leftmenu_save .= "      </li>";
                }


                // $leftmenu_save .= "    </li>";

                /*
                  $leftmenu_save .= " <li class='header'>" . $_SESSION["wa_current_user"]->name . "</li>";
                  $leftmenu_save .= " <li ><a href='$local_path_to_root/admin/display_prefs.php?'><i class='fa fa-cogs'></i> <span>"._("Configuration")."</span></a></li>";
                  $leftmenu_save .= " <li ><a href='$local_path_to_root/admin/change_current_user_password.php?selected_id=".$_SESSION["wa_current_user"]->username."'><i class='fa fa-key'></i> <span>"._("Change password")."</span></a></li>";
                  $leftmenu_save .= " <li ><a href='$local_path_to_root/access/logout.php?'><i class='fa fa-sign-out'></i> <span>"._("Logout")."</span></a></li>\n"; */

                if (!$no_menu)
                    echo $leftmenu_save;

            }
            echo'<li class="header">Contact us on : </li>';
            echo'<li><a href="https://api.whatsapp.com/send?phone=923008115737&text=Hi Hisaab.pk Support i want to ask that" target="_blank"><i style="color:#00cc00;" class="fab fa-whatsapp" target="_blank"></i> <span>What\'s App. </span></a></li>';
            echo'<li><a href="https://m.me/HisaabErp" target="_blank"><i style="color:#0084ff;" class="fab fa-facebook-messenger" ></i> <span>FB Messenger. </span></a></li>';
            echo'<li><a href="skype:hisaab.pk?call" target="_blank"><i  style="color:#2ac7e1;" class="fab fa-skype" ></i> <span>Skype. </span></a></li>';
            echo'<li><a href="https://www.youtube.com/c/HisaabERP" target="_blank"><i  style="color:red;"  class="fab fa-youtube" ></i> <span>Youtube. </span></a></li>';
            echo'<li><a href="https://twitter.com/HisaabERP" target="_blank"><i style="color: #0d6aad;" class="fab fa-twitter"></i> <span>Twitter. </span></a></li>';
            echo "</ul>";
            echo "</section>";
            //<!-- /.sidebar -->

            echo "</aside>";
        }
        }
        echo "	<div class='content-wrapper' style='background-color:white' >";


        echo"<section>";
        if ($title && !$no_menu)
        {

            //<!-- Content Header (Page header) -->
            echo"<section class='content-header' style='padding-bottom:;margin-top: 50px;'>";

            echo"<h1>";
            echo " <a style='' href='$local_path_to_root/index.php?application=".$curr_app_link. SID ."'>" . $curr_app_name . "</a>";
            if ($no_menu)
                echo "<br>";
            elseif ($title && !$is_index)
                echo "<small><a id='mname' href='#'>" . $title . "</a></small>";
            $indicator = "$path_to_root/themes/".user_theme(). "/images/ajax-loader.gif";
            echo " <span style=''><img id='ajaxmark' src='$indicator' style='visibility:hidden;'></span>";
            // echo"<small>Version 2.0</small>";


//            if ($SysPrefs->help_base_url != null)
//            {
//                $gly = "fas fa-life-ring  ";
//                echo "<a target = '_blank' onclick=" .'"'."javascript:openWindow(this.href,this.target); return false;".'" '. "href='". help_url()."'><i class='$gly' float:right;></i>" . _("Help")."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
//            }
//                <a href="https://api.whatsapp.com/send?phone=923008115737&text=Hi Hisaab.pk Support i want to ask that" class="fab fa-whatsapp" target="blank"></a>
//                <a href="https://m.me/HisaabErp" class="fab fa-facebook-messenger" target="blank"></a>
//                <a href="skype:hisaab.pk?call" class="fab fa-skype" target="blank"></a>
//                <a href="https://www.youtube.com/c/HisaabERP" class="fab fa-youtube" target="blank"></a>
//                <a href="https://twitter.com/HisaabERP" class="fab fa-twitter" target="blank"></a>
//                <!--
//                <a href="https://play.google.com/store/apps/details?id=com.Hisaabpk" class="fab fa-android" target="blank"></a>
//                -->
//                ';


            echo"  </h1>";

            echo"  <ol class='breadcrumb'>";
            echo" <li><a href='$local_path_to_root/index.php?'><i class='fas fa-tachometer-alt'></i>&nbsp; Home";

            echo" <li><a href='$local_path_to_root/index.php?application=".$curr_app_link. SID ."'>" .$curr_app_name. "  </a></li>";


            if ($no_menu)
                echo "<br>";
            elseif ($title && !$is_index)
                echo"&nbsp; > ";
            echo "<a id='mname' href='' > " . $title . "</a>";
            echo"&nbsp;". (user_hints() ? "<span id=''></span>" : "");

            echo '</br>';

            if($diff != 0 && db_num_rows($history_row) > 0)
            {
                echo "<small><u><b>";
                //  echo viewer_link("Last entry was done $diff ago by $last_user_name", "");

                if($get_last_history[4]==ST_JOURNAL)
                    echo get_trans_view_str($get_last_history[4], $get_last_history[3], "Last entry was done $diff ago by $last_user_name", false, 'Last entry was', '', $row['approval']);
                else
                    echo get_trans_view_str($get_last_history[4], $get_last_history[3], "Last entry was done $diff ago by $last_user_name", false, '', '', 0);
                echo "</b></u></small>";
            }

            echo" </ol>";
            echo"  </section>";



            //	echo "  <div id='contentcolumn'>\n";
            //	echo "    <div class='innertube'>\n";
            //	echo (user_hints() ? "<span id='hints' style='float:right;'>sfssff</span>" : "");
            //	echo "      <p class='breadcrumb' style='padding:14px;'>\n";
            //	echo "        <a class='shortcut' id='mname' style='' href='$local_path_to_root/index.php?application=".$curr_app_link. SID ."'>" . $curr_app_name . "&nbsp</a>\n";
            //	if ($no_menu)
            //		echo "<br>";
            //	elseif ($title && !$is_index)
            //		echo "        <a id='mname' href='#'>" . $title . "</a>\n";
            //	$indicator = "$path_to_root/themes/".user_theme(). "/images/ajax-loader.gif";
            //	echo " <span style=''><img id='ajaxmark' src='$indicator' align='center' style='visibility:hidden;'></span>";
            //	echo " </p>\n";

        }

        echo"<section>";

        echo'
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">';

        include_once("$path_to_root/themes/".user_theme()."/Datamodal.php");
        $_data= new dashboardmodal();

        $_data->DataModal();
        echo'</div>';

        echo'
<!-- Modal -->
  <div class="modal fade" id="myPayModel" role="dialog">';

        include_once("$path_to_root/themes/".user_theme()."/Datamodal.php");
        $_data= new dashboardmodal();

        $_data->myPayModel();
        echo'</div>';

//Reorder level
        echo'
<!-- Modal -->
  <div class="modal fade" id="myReOModal" role="dialog">';

        include_once("$path_to_root/themes/".user_theme()."/Datamodal.php");
        $_data= new dashboardmodal();

        $_data->myREOModel();
        echo'</div>';

        echo"</section>";
    }

    function display_applications(&$waapp)
    {
        global  $systypes_array;

        $path_to_root = ".";
        $sel_app = $_SESSION['sel_app'];
        $applications = $_SESSION['App']->applications;
        foreach($applications as $app)
        {
            if ($sel_app == $app->id)
            {
                //$curr_app_name = ;
                //echo "this is :".$app->id;
                //echo"<br /><br /><br />";
                if($app->id == 'dashboard')
                {

                    include_once("$path_to_root/themes/".user_theme()."/Dashboard.php");
                    $_dash= new dashboard();

                    $_dash->renderDash();


                }else{

                    echo "<section class='content' style='padding:5px;margin-top:3px;'>";
                    $selected_app = $waapp->get_selected_application();
                    foreach ($selected_app->modules as $module)
                    {
                        echo"<div class='box box-success '  style='padding-bottom:10px;'>";
                        echo"<div class='box-header with-border '>";
                        echo"<h3 class='box-title '>" . str_replace(' ','&nbsp;',$module->name) . "</h3>";
                        echo"<div class='box-tools pull-right'>";
                        echo"<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>";
                        //echo"<button class='btn btn-box-tool' data-widget='remove'><i class='fa fa-times'></i></button>";
                        echo"</div>";
                        echo"</div>";//<!-- /.box-header -->

                        echo"<div class='box-body no-padding'>";
                        echo"<div class='row'>";

                        echo"<div class='col-md-12 col-sm-11' >";
                        echo"<div class='pad col-md-12 col-sm-12 col-lg-12' >";
                        //<!-- text will go here  -->

                        foreach ($module->lappfunctions as $appfunction)
                        {
                            $this->renderButtonsForAppFunctions($appfunction);
                        }

                        foreach ($module->rappfunctions as $appfunction)
                        {
                            $this->renderButtonsForAppFunctions($appfunction);
                        }
                        echo"</div>";
                        echo"</div>";//<!-- /.col -->

                        echo"</div>";//<!-- /.row -->
                        echo"</div>";
                        echo"</div>";
                    }


                    echo"</section>";


                }
                function get_pending_vouchers($type)
                {
                    $sql = "SELECT COUNT(DISTINCT type_no) FROM ".TB_PREF."gl_trans
                 WHERE type = $type
                 AND approval = 1
                 ";
                    $result = db_query($sql, "could not get vouchers");
                    $row = db_fetch_row($result);
                    $voucher_count = $row[0];
                    return $voucher_count;
                }


            }
        }

    }

    function renderButtonsForAppFunctions($appfunction)
    {
        {  $sql = "SELECT COUNT(DISTINCT sorder.order_no) 
                FROM ".TB_PREF."sales_orders as sorder,
    			".TB_PREF."sales_order_details as line
    			WHERE sorder.order_no = line.order_no
    			AND sorder.trans_type = line.trans_type
    			AND line.trans_type =30
    		
    			AND line.qty_sent < line.quantity
            	";
            //AND line.quantity>0

            $result = db_query($sql, "could not get debtors");
            $row = db_fetch_row($result);
            $pending_dn_count = $row[0];


            $sql = "SELECT COUNT(DISTINCT d.trans_no) 
                FROM "
                .TB_PREF."sales_orders as sorder ,".TB_PREF."debtor_trans as d,
    			".TB_PREF."debtor_trans_details as line
    			WHERE 	sorder.order_no = d.order_
    			AND d.trans_no = line.debtor_trans_no
    			AND d.type = line.debtor_trans_type
    			AND d.type = 13
    		
    			AND line.qty_done < line.quantity
    			
    			";
            //AND line.quantity>0
            $result = db_query($sql, "could not get deliveries");
            $row = db_fetch_row($result);
            $pending_invoice_count = $row[0];

            $today = date2sql(Today());

            $sql = "SELECT COUNT(DISTINCT sorder.order_no) 
                FROM ".TB_PREF."sales_orders as sorder,
    			".TB_PREF."sales_order_details as line
    			WHERE sorder.order_no = line.order_no
    			AND sorder.trans_type = line.trans_type
            	AND sorder.delivery_date>='$today'
    			AND line.trans_type = 32
                AND line.qty_sent=0

            	";

            $result = db_query($sql, "could not get qoutations");
            $row = db_fetch_row($result);
            $pending_qoutation_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT sorder.order_no) 
                FROM ".TB_PREF."sales_orders as sorder,
    			".TB_PREF."sales_order_details as line
    			WHERE sorder.order_no = line.order_no
    			AND sorder.trans_type = line.trans_type
               AND line.trans_type = 30";

            $result = db_query($sql, "could not get sales orders");
            $row = db_fetch_row($result);
            $pending_sales_order_count = $row[0];






            $sql = "SELECT COUNT(DISTINCT d.trans_no) 
                FROM ".TB_PREF."debtor_trans as d,
                ".TB_PREF."debtors_master as debtor
    			WHERE debtor.debtor_no = d.debtor_no
    			AND (d.ov_amount + d.ov_gst + d.ov_freight 
				+ d.ov_freight_tax + d.ov_discount - d.discount1 - d.discount2 != 0)
    			AND (round(IF(d.prep_amount,d.prep_amount, abs(d.ov_amount + d.ov_gst + "
                ."d.ov_freight + d.ov_freight_tax + "
                ."d.ov_discount - d.discount1 - d.discount2)) - d.alloc,6) != 0)
		
                ";
            $result = db_query($sql, "could not get allocations");
            $row = db_fetch_row($result);
            $pending_cust_alloc_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT trans.trans_no) 
                FROM "
                .TB_PREF."supp_trans as trans
			LEFT JOIN ".TB_PREF."voided as v
				ON trans.trans_no=v.id AND trans.type=v.type,"
                .TB_PREF."suppliers as supplier
			
    		WHERE  supplier.supplier_id = trans.supplier_id
    			 
		
			
			AND (round(abs(ov_amount + ov_gst + ov_discount) - alloc) != 0) ";
            $result = db_query($sql, "could not get allocations");
            $row = db_fetch_row($result);
            $pending_supp_alloc_count = $row[0];




            $sql = "SELECT COUNT(debtor_no) FROM ".TB_PREF."debtors_master
                WHERE inactive != 1";
            $result = db_query($sql, "could not get debtors");
            $row = db_fetch_row($result);
            $cust_count = $row[0];


            $sql = "SELECT COUNT(branch_code) FROM ".TB_PREF."cust_branch
                WHERE inactive != 1";
            $result = db_query($sql, "could not get branches");
            $row = db_fetch_row($result);
            $branch_count = $row[0];

            $sql = "SELECT COUNT(id) FROM ".TB_PREF."groups
                WHERE inactive != 1";
            $result = db_query($sql, "could not get groups");
            $row = db_fetch_row($result);
            $groups_count = $row[0];

            $sql = "SELECT COUNT(id) FROM ".TB_PREF."sales_types
                WHERE inactive != 1";
            $result = db_query($sql, "could not get sales type");
            $row = db_fetch_row($result);
            $stypes_count = $row[0];

            $sql = "SELECT COUNT(salesman_code) FROM ".TB_PREF."salesman
                WHERE inactive != 1";
            $result = db_query($sql, "could not get salesman");
            $row = db_fetch_row($result);
            $salesman_count = $row[0];

            $sql = "SELECT COUNT(area_code) FROM ".TB_PREF."areas
                WHERE inactive != 1";
            $result = db_query($sql, "could not get areas");
            $row = db_fetch_row($result);
            $areas_count = $row[0];

            $sql = "SELECT COUNT(id) FROM ".TB_PREF."sales_email
                WHERE inactive != 1";
            $result = db_query($sql, "could not get email");
            $row = db_fetch_row($result);
            $email_count = $row[0];

            $sql = "SELECT COUNT(gate_pass_no) FROM ".TB_PREF."multiple_gate_pass
               ";
            $result = db_query($sql, "could not get gate pass");
            $row = db_fetch_row($result);
            $gate_pass_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT cat_code) FROM ".TB_PREF."stock_category
               
               ";
            $result = db_query($sql, "could not get items");
            $row = db_fetch_row($result);
            $stock_category_count = $row[0];


            $sql = "SELECT COUNT(supplier_id) FROM ".TB_PREF."suppliers
                WHERE inactive != 1";
            $result = db_query($sql, "could not get suppliers");
            $row = db_fetch_row($result);
            $supp_count = $row[0];

            $sql = "SELECT COUNT(stock_id) FROM ".TB_PREF."stock_master
                WHERE inactive != 1
                AND mb_flag != 'F'";
            $result = db_query($sql, "could not get items");
            $row = db_fetch_row($result);
            $stock_count = $row[0];

            $sql = "SELECT COUNT(stock_id) FROM ".TB_PREF."stock_master
                WHERE inactive != 1
                AND mb_flag = 'F' ";
            $result = db_query($sql, "could not get items");
            $row = db_fetch_row($result);
            $fixed_asset_count = $row[0];

            $sql = "SELECT COUNT(id) FROM ".TB_PREF."dimensions
                WHERE closed != 1";
            $result = db_query($sql, "could not get dimensions");
            $row = db_fetch_row($result);
            $open_proj_count = $row[0];

            $sql = "SELECT COUNT(id) FROM ".TB_PREF."dimensions";
            $result = db_query($sql, "could not get dimensions");
            $row = db_fetch_row($result);
            $all_proj_count = $row[0];

            $sql = "SELECT COUNT(id) FROM ".TB_PREF."workorders
                WHERE closed != 1";
            $result = db_query($sql, "could not get work oders");
            $row = db_fetch_row($result);
            $open_wo_count = $row[0];

            $sql = "SELECT COUNT(id) FROM ".TB_PREF."workorders";
            $result = db_query($sql, "could not get work orders");
            $row = db_fetch_row($result);
            $all_wo_count = $row[0];

            $sql = "SELECT COUNT(DISTINCT parent)
                FROM ".TB_PREF."bom
                ";
            $result = db_query($sql, "could not get bom");
            $row = db_fetch_row($result);
            $bom_count = $row[0];

            $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."workcentres
                ";
            $result = db_query($sql, "could not get workcentres");
            $row = db_fetch_row($result);
            $workcentres_count = $row[0];

            $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."bank_accounts
                WHERE inactive != 1
                ";
            $result = db_query($sql, "could not get banks");
            $row = db_fetch_row($result);
            $banks_count = $row[0];

            $sql = "SELECT COUNT(curr_abrev)
                FROM ".TB_PREF."currencies
                WHERE inactive != 1
                ";
            $result = db_query($sql, "could not get currencies");
            $row = db_fetch_row($result);
            $currencies_count = $row[0];

            $sql = "SELECT COUNT(account_code)
                FROM ".TB_PREF."chart_master
                WHERE inactive != 1
                ";
            $result = db_query($sql, "could not get chart master");
            $row = db_fetch_row($result);
            $coa_count = $row[0];


            $sql = "SELECT COUNT(supp_code)
                FROM ".TB_PREF."supp_category
                WHERE inactive != 1
                ";
            $result = db_query($sql, "could not get supplier category");
            $row = db_fetch_row($result);
            $category_count = $row[0];

            $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."purchase_email
                WHERE inactive != 1
                ";
            $result = db_query($sql, "could not get purchase email");
            $row = db_fetch_row($result);
            $email_purch_count = $row[0];



            $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."source_status
               
                ";
            $result = db_query($sql, "could not get purchase email");
            $row = db_fetch_row($result);
            $source_status_count = $row[0];



            $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."chart_types
                WHERE inactive != 1
                ";
            $result = db_query($sql, "could not get chart types");
            $row = db_fetch_row($result);
            $coa_group_count = $row[0];

            $sql = "SELECT COUNT(trans.id)
                FROM 0_bank_trans trans , 0_bank_accounts banks
                WHERE 
                trans.`bank_act` = banks.id AND                 
                banks.account_type!=3 AND trans.reconciled IS NULL AND trans.amount!=0 ";
            $result = db_query($sql, "could not get bank reconciliations");
            $row = db_fetch_row($result);
            $unreconcile_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT porder.order_no)
                FROM ".TB_PREF."purch_orders as porder,"
                .TB_PREF."purch_order_details as line
                WHERE  porder.order_no = line.order_no
                AND line.quantity_ordered !=0
                AND (line.quantity_ordered > line.quantity_received) 
                ";
            $result = db_query($sql, "could not get po search");
            $row = db_fetch_row($result);
            $po_search_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT prorder.requisition_id)
                FROM ".TB_PREF."requisitions as prorder, "
                .TB_PREF."requisition_details as line
                WHERE  prorder.requisition_id = line.requisition_id
               
                ";
            $result = db_query($sql, "could not get po search");
            $row = db_fetch_row($result);
            $pr_search_count = $row[0];





            $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."batch
          
               
                ";
            $result = db_query($sql, "could not get batch");
            $row = db_fetch_row($result);
            $batch_count = $row[0];


            $sql = "SELECT COUNT(name)
                FROM ".TB_PREF."item_units            ";
            $result = db_query($sql, "could not get units");
            $row = db_fetch_row($result);
            $units_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT 	batch.id)
               	FROM ".TB_PREF."grn_batch as batch,
        ".TB_PREF."grn_items as items, ".TB_PREF."purch_orders as po
               		WHERE batch.id = items.grn_batch_id
                   AND batch.transaction_type=1
AND batch.purch_order_no = po.order_no
AND items.qty_recd !=0
AND items.qty_recd > items.quantity_inv  ";
            $result = db_query($sql, "could not get grn search");
            $row = db_fetch_row($result);
            $grn_search_count = $row[0];



            $sql = "SELECT COUNT( trans_no)
                FROM ".TB_PREF."debtor_trans  ";
            $result = db_query($sql, "could not get search");
            $row = db_fetch_row($result);
            $cust_search_count = $row[0];



            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."task 
                WHERE task_type=1";
            $result = db_query($sql, "could not get task");
            $row = db_fetch_row($result);
            $task_count = $row[0];

            $sql = "SELECT COUNT( shipper_id)
                FROM ".TB_PREF."shippers 
                WHERE inactive!=1";
            $result = db_query($sql, "could not get task");
            $row = db_fetch_row($result);
            $shippers_count = $row[0];

            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."sales_pos 
                WHERE inactive!=1";
            $result = db_query($sql, "could not get task");
            $row = db_fetch_row($result);
            $pos_count = $row[0];


            $sql = "SELECT COUNT(terms_indicator)
                FROM ".TB_PREF."payment_terms 
                WHERE inactive!=1";
            $result = db_query($sql, "could not get task");
            $row = db_fetch_row($result);
            $terms_count = $row[0];


            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."task 
                WHERE task_type=2";
            $result = db_query($sql, "could not get task");
            $row = db_fetch_row($result);
            $log_count = $row[0];

            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."query  ";
            $result = db_query($sql, "could not get query");
            $row = db_fetch_row($result);
            $query_count = $row[0];



            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."query_status  ";
            $result = db_query($sql, "could not get query");
            $row = db_fetch_row($result);
            $querystatus_count = $row[0];



            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."call_type  ";
            $result = db_query($sql, "could not get query");
            $row = db_fetch_row($result);
            $call_type_count = $row[0];

            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."category  ";
            $result = db_query($sql, "could not get query");
            $row = db_fetch_row($result);
            $categories_count = $row[0];



            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."printers  ";
            $result = db_query($sql, "could not get query");
            $row = db_fetch_row($result);
            $printers_count = $row[0];


            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."crm_categories  ";
            $result = db_query($sql, "could not get crm categories count");
            $row = db_fetch_row($result);
            $crm_categories_count = $row[0];

            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."pstatus  ";
            $result = db_query($sql, "could not get query");
            $row = db_fetch_row($result);
            $status_count = $row[0];

            $sql = "SELECT COUNT(DISTINCT id)
                FROM ".TB_PREF."user_locations
                WHERE inactive!=1  ";
            $result = db_query($sql, "could not get location");
            $row = db_fetch_row($result);
            $user_locations_count = $row[0];

            $sql = "SELECT COUNT(DISTINCT id)
                FROM ".TB_PREF."users
                WHERE inactive!=1  ";
            $result = db_query($sql, "could not get users");
            $row = db_fetch_row($result);
            $users_count = $row[0];


            $sql = "SELECT COUNT(DISTINCT id)
                FROM ".TB_PREF."item_tax_types   ";
            $result = db_query($sql, "could not get users");
            $row = db_fetch_row($result);
            $tax_types_count = $row[0];

            $sql = "SELECT COUNT(DISTINCT id)
                FROM ".TB_PREF."fiscal_year ";
            $result = db_query($sql, "could not get fiscal year");
            $row = db_fetch_row($result);
            $fiscal_year_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT id)
                FROM ".TB_PREF."tax_groups
                WHERE inactive!=1  ";
            $result = db_query($sql, "could not get tax");
            $row = db_fetch_row($result);
            $tax_group_count = $row[0];

            $sql = "SELECT COUNT(DISTINCT id)
                FROM ".TB_PREF."tax_types
                WHERE inactive!=1  ";
            $result = db_query($sql, "could not get location");
            $row = db_fetch_row($result);
            $tax_count = $row[0];



            $sql = "SELECT COUNT(DISTINCT id)
                FROM ".TB_PREF."user_banks
                WHERE inactive!=1  ";
            $result = db_query($sql, "could not get location");
            $row = db_fetch_row($result);
            $user_banks_count = $row[0];



            $sql = "SELECT COUNT( id)
                FROM ".TB_PREF."duration  ";
            $result = db_query($sql, "could not get query");
            $row = db_fetch_row($result);
            $duration_count = $row[0];


            $sql = "SELECT COUNT( cid)
                FROM ".TB_PREF."chart_class  ";
            $result = db_query($sql, "could not get search");
            $row = db_fetch_row($result);
            $chart_class_count = $row[0];

            $sql = "SELECT COUNT(DISTINCT loc_code)
                FROM ".TB_PREF."locations  ";
            $result = db_query($sql, "could not get search");
            $row = db_fetch_row($result);
            $location_count = $row[0];


            $sql = "SELECT COUNT(DISTINCT porder.order_no)
               	FROM ".TB_PREF."purch_orders as porder LEFT JOIN ( SELECT order_no, SUM(quantity_ordered-quantity_received + quantity_ordered-qty_invoiced) isopen 
               	FROM ".TB_PREF."purch_order_details GROUP BY order_no ) chk ON chk.order_no=porder.order_no,
               	".TB_PREF."purch_order_details as line, ".TB_PREF."suppliers as supplier, ".TB_PREF."locations as location 
               	WHERE porder.order_no = line.order_no AND porder.supplier_id = supplier.supplier_id 
               	AND location.loc_code = porder.into_stock_location  AND isopen ";
            $result = db_query($sql, "could not get po search");
            $row = db_fetch_row($result);
            $po_order_count = $row[0];

        }


        $badge = "";
        $bgcolor = "";
        if ($_SESSION["wa_current_user"]->can_access_page($appfunction->access))
        {
            if ($appfunction->label != "")
            {
                $lnk = access_string($appfunction->label);

                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?OutstandingOnly=1')
                {
                    $badge = $pending_dn_count;
                    $bgcolor = 'bg-red';
                }



                if($appfunction->link == 'sales/inquiry/sales_deliveries_view.php?OutstandingOnly=1')
                {
                    $badge = $pending_invoice_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'sales/manage/customers_inquiry.php?')
                {
                    $badge = $cust_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?type=32')
                {
                    $badge = $pending_qoutation_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?type=30')
                {
                    $badge = $pending_sales_order_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'sales/inquiry/customer_allocation_inquiry.php?')
                {
                    $badge = $pending_cust_alloc_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'sales/manage/customers.php?')
                {
                    $badge = $cust_search_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'sales/inquiry/customer_inquiry.php?')
                {
                    $badge = $cust_search_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'sales/manage/customer_branches.php?')
                {
                    $badge = $branch_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'sales/manage/sales_groups.php?')
                {
                    $badge = $groups_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'sales/manage/sales_types.php?')
                {
                    $badge = $stypes_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'sales/manage/sales_people.php?')
                {
                    $badge = $salesman_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'sales/manage/sales_areas.php?')
                {
                    $badge = $areas_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?type=30')
                {
                    $badge = $pending_sales_order_count;
                    $bgcolor = 'bg-red';
                }

                if($appfunction->link == 'sales/manage/sales_email.php?')
                {
                    $badge = $email_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'sales/manage/gate_pass_dashboard.php?')
                {
                    $badge = $gate_pass_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'purchasing/manage/suppliers_inquiry.php?')
                {
                    $badge = $supp_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'purchasing/manage/purchase_email.php?')
                {
                    $badge = $email_purch_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'purchasing/manage/supplier_category.php?')
                {
                    $badge = $category_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'purchasing/inquiry/po_search.php?')
                {
                    $badge = $po_search_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'purchasing/inquiry/pr_search_completed.php?')
                {
                    $badge = $pr_search_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'purchasing/inquiry/grn_search_completed.php?')
                {
                    $badge = $grn_search_count;
                    $bgcolor = 'bg-red';
                }

                if($appfunction->link == 'purchasing/inquiry/po_search_completed.php?')
                {
                    $badge = $po_order_count;
                    $bgcolor = 'bg-red';
                }

                if($appfunction->link == 'purchasing/inquiry/supplier_allocation_inquiry.php?')
                {
                    $badge = $pending_supp_alloc_count;

                    $bgcolor = 'bg-red';
                }





                if($appfunction->link == 'inventory/manage/item_categories.php?')
                {
                    $badge = $stock_category_count;
                    $bgcolor = 'bg-gray';
                }




                if($appfunction->link == 'inventory/manage/item_units.php?')
                {
                    $badge = $units_count;
                    $bgcolor = 'bg-gray';
                }




                if($appfunction->link == 'inventory/manage/items_inquiry.php?')
                {
                    $badge = $stock_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'inventory/manage/locations.php?')
                {
                    $badge = $location_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'inventory/manage/batches.php?')
                {
                    $badge = $batch_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'manufacturing/search_work_orders.php?outstanding_only=1')
                {
                    $badge = $open_wo_count;
                    $bgcolor = 'bg-red';
                }
                if($appfunction->link == 'manufacturing/search_work_orders.php?')
                {
                    $badge = $all_wo_count;
                    $bgcolor = 'bg-orange';
                }
                if($appfunction->link == 'manufacturing/manage/bom_edit.php?')
                {
                    $badge = $bom_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'manufacturing/manage/work_centres.php?')
                {
                    $badge = $workcentres_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'inventory/manage/items.php?FixedAsset=1')
                {
                    $badge = $fixed_asset_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'dimensions/inquiry/search_dimensions.php?outstanding_only=1')
                {
                    $badge = $open_proj_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'dimensions/inquiry/search_dimensions.php?')
                {
                    $badge = $all_proj_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'gl/manage/bank_accounts.php?')
                {
                    $badge = $banks_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'gl/manage/currencies.php?')
                {
                    $badge = $currencies_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'gl/inquiry/gl_account_types.php?')
                {
                    $badge = $coa_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'gl/manage/gl_account_types.php?')
                {
                    $badge = $coa_group_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'gl/manage/gl_account_classes.php?')
                {
                    $badge = $chart_class_count;
                    $bgcolor = 'bg-gray';
                }




                if($appfunction->link == 'gl/bank_account_reconcile.php?')
                {
                    $badge = $unreconcile_count;
                    $bgcolor = 'bg-red';
                }

                if($appfunction->link == 'project/inquiry/task_inquiry.php?status=112')
                {
                    $badge = $task_count;
                    $bgcolor = 'bg-red';
                }

                if($appfunction->link == 'project/inquiry/call_log.php?')
                {
                    $badge = $log_count;
                    $bgcolor = 'bg-red';
                }




                if($appfunction->link == 'sales/manage/sales_points.php?')
                {
                    $badge = $pos_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'admin/printers.php?')
                {
                    $badge = $printers_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'project/inquiry/query_inquiry.php?status=112')
                {
                    $badge = $query_count;
                    $bgcolor = 'bg-red';
                }


                if($appfunction->link == 'project/manage/query_status.php?')
                {
                    $badge = $querystatus_count;
                    $bgcolor = 'bg-gray';
                }
                if($appfunction->link == 'project/manage/call_type.php?')
                {
                    $badge = $call_type_count;
                    $bgcolor = 'bg-gray';
                }



                if($appfunction->link == 'project/manage/duration.php?')
                {
                    $badge = $duration_count;
                    $bgcolor = 'bg-gray';
                }



                if($appfunction->link == 'project/manage/category.php?')
                {
                    $badge = $categories_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'project/manage/status.php?')
                {
                    $badge = $status_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'project/manage/source_status.php?')
                {
                    $badge = $source_status_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'admin/payment_terms.php?')
                {
                    $badge = $terms_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'admin/shipping_companies.php?')
                {
                    $badge = $shippers_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'admin/crm_categories.php?')
                {
                    $badge = $crm_categories_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'admin/user_locations.php?')
                {
                    $badge = $user_locations_count;
                    $bgcolor = 'bg-gray';
                }



                if($appfunction->link == ' admin/user_banks.php?')
                {
                    $badge = $user_banks_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'taxes/tax_types.php?')
                {
                    $badge = $tax_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'taxes/tax_groups.php?')
                {
                    $badge = $tax_group_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'admin/users.php?')
                {
                    $badge = $users_count;
                    $bgcolor = 'bg-gray';
                }

                if($appfunction->link == 'admin/fiscalyears.php?')
                {
                    $badge = $fiscal_year_count;
                    $bgcolor = 'bg-gray';
                }


                if($appfunction->link == 'taxes/item_tax_types.php?')
                {
                    $badge = $tax_types_count;
                    $bgcolor = 'bg-gray';
                }



                //-------------------FOR ICONS-------------------
                if($appfunction->link == 'sales/sales_order_entry.php?NewQuotation=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/sales_order_entry.php?NewQuotation=Yes'$lnk[1]><i class='fa fa-quote-left'></i>&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span> 
        </a>\n";
                }

                if($appfunction->link == 'sales/sales_order_entry.php?NewOrder=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/sales_order_entry.php?NewOrder=Yes'$lnk[1]><i class='fa fa-shopping-cart'></i>  &nbsp;&nbsp;&nbsp;" .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/sales_order_entry_mobile.php?NewOrder=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/sales_order_entry_mobile.php?NewOrder=Yes'$lnk[1]><i class='fa fa-shopping-cart'></i> &nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span> 
        </a>\n";
                }


                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?OutstandingOnly=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/sales_orders_view.php?OutstandingOnly=1'$lnk[1]> <i class='fa fa-truck'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'sales/inquiry/sales_deliveries_view.php?OutstandingOnly=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/sales_deliveries_view.php?OutstandingOnly=1'$lnk[1]><i class='fa fa-file-text-o'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'sales/sales_order_entry.php?NewDelivery=0')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/sales_order_entry.php?NewDelivery=0'$lnk[1]><i class='fa fa-truck'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'sales/pos_order_entry.php?NewInvoice=0')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/pos_order_entry.php?NewInvoice=0'$lnk[1]> <i class='fa fa-desktop'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/sales_order_entry.php?NewInvoice=0')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/sales_order_entry.php?NewInvoice=0'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?PrepaidOrders=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/sales_orders_view.php?PrepaidOrders=Yes'$lnk[1]><i class='fa fa-file-text-o'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      &nbsp; &nbsp;&nbsp;&nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/customer_payments.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/customer_payments.php?'$lnk[1]><i class='fa  fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      &nbsp; &nbsp;&nbsp;&nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'sales/credit_note_entry.php?NewCredit=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/credit_note_entry.php?NewCredit=Yes'$lnk[1]> <i class='fa fa-pencil-square-o'>   
</i>&nbsp;&nbsp;&nbsp;&nbsp;  " .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/allocations/customer_allocation_main.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/allocations/customer_allocation_main.php?'$lnk[1]><i class='glyphicon glyphicon-retweet'></i>&nbsp; " .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if (get_company_pref('discount_offer'))
                    if($appfunction->link == 'sales/offer_order_entry.php?AddOffers=Yes')
                    {
                        echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
                        class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
                        href='sales/offer_order_entry.php?AddOffers=Yes'$lnk[1]><i class='glyphicon glyphicon-retweet'></i>&nbsp; " .$lnk[0] . "
                        <span class='label ".$bgcolor." '>$badge</span>
                        </a>\n";
                    }

                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?type=32')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/sales_orders_view.php?type=32'$lnk[1]>  <i class='glyphicon glyphicon-dashboard'></i> &nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }



                if($appfunction->link == 'sales/inquiry/sales_orders_view.php?type=30')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12'  style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/sales_orders_view.php?type=30'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
      &nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/inquiry/customer_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/customer_inquiry.php?'$lnk[1]> <i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label '></span>
        </a>\n";
                }

                if($appfunction->link == 'sales/inquiry/customer_allocation_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/customer_allocation_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp;  " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'reporting/reports_main.php?Class=0')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=0'$lnk[1]><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
      &nbsp;    <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/customers_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/customers_inquiry.php?'$lnk[1]> <i class='fa fa-user-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      &nbsp;  <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if (get_company_pref('discount_offer'))
                    if($appfunction->link == 'sales/inquiry/offer_inquiry.php?')
                    {
                        echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/inquiry/offer_inquiry.php?'$lnk[1]> <i class='fa fa-user-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      &nbsp;  <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                    }



                if($appfunction->link == 'sales/manage/customer_branches.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/customer_branches.php?'$lnk[1]><i class='fa fa fa-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp;  " .$lnk[0] . "
      &nbsp;  <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'sales/merge_customers.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/merge_customers.php?'$lnk[1]><i class='fa fa-users'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      &nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }if($appfunction->link == 'sales/manage/sales_groups.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sales_groups.php?'$lnk[1]><i class='fa fa-users'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      &nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }if($appfunction->link == 'sales/manage/sales_types.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sales_types.php?'$lnk[1]><i class='fa fa-percent'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      &nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }

                if($appfunction->link == 'sales/manage/sales_people.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sales_people.php?'$lnk[1]><i class='fa fa-percent'></i>  " .$lnk[0] . "
      &nbsp;  <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/sales_areas.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sales_areas.php?'$lnk[1]> <i class='fa fa-area-chart'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/sales_email.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sales_email.php?'$lnk[1]><i class='fa fa-envelope'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/gate_pass_dashboard.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/gate_pass_dashboard.php?'$lnk[1]>  <i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
     &nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'modules/import_items/import_customers.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_items/import_customers.php?'$lnk[1]><i class='glyphicon glyphicon-import'></i> &nbsp;&nbsp; " .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'modules/import_cust_OB/import_cust_invoice.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_cust_OB/import_cust_invoice.php'$lnk[1]><i class='glyphicon glyphicon-import'></i> &nbsp;&nbsp;" .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'modules/import_cust_OB/import_cust_opening.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_cust_OB/import_cust_opening.php'$lnk[1]><i class='glyphicon glyphicon-import'></i> &nbsp;&nbsp;" .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'modules/import_cust_sales_return/import_sales_return')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_cust_sales_return/import_sales_return'$lnk[1]>" .$lnk[0] . "
      &nbsp; <i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/credit_status.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/credit_status.php?'$lnk[1]>  <i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
     &nbsp;   <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/sales_sms.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sales_sms.php?'$lnk[1]>  <i class='fa fa-mobile-phone'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/sms_template.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sms_template.php?'$lnk[1]>  <i class='fa fa fa-bars'></i>&nbsp;&nbsp;&nbsp;&nbsp;   " .$lnk[0] . "
     <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if (get_company_pref('discount_offer')){
                    if($appfunction->link == 'sales/manage/sales_offers.php?')
                    {
                        echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
                        class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
                        href='sales/manage/sales_offers.php?'$lnk[1]>  <i class='fa fa fa-bars'></i>&nbsp;&nbsp;&nbsp;&nbsp;   " .$lnk[0] . "
                        <span class='label ".$bgcolor." '>$badge</span>
                        </a>\n";
                    }
                    if($appfunction->link == 'sales/manage/dist_network.php?')
                    {
                        echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
                        class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
                        href='sales/manage/dist_network.php?'$lnk[1]>  <i class='fa fa fa-bars'></i>&nbsp;&nbsp;&nbsp;&nbsp;   " .$lnk[0] . "
                        <span class='label ".$bgcolor." '>$badge</span>
                        </a>\n";
                    }
                    if($appfunction->link == 'sales/manage/cust_info.php?')
                    {
                        echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
                        class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
                        href='sales/manage/cust_info.php?'$lnk[1]>  <i class='fa fa fa-bars'></i>&nbsp;&nbsp;&nbsp;&nbsp;   " .$lnk[0] . "
                        <span class='label ".$bgcolor." '>$badge</span>
                        </a>\n";
                    }
                    if($appfunction->link == 'sales/manage/transportation.php?')
                    {
                        echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
                        class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
                        href='sales/manage/transportation.php?'$lnk[1]>  <i class='fa fa fa-bars'></i>&nbsp;&nbsp;&nbsp;&nbsp;   " .$lnk[0] . "
                        <span class='label ".$bgcolor." '>$badge</span>
                        </a>\n";
                    }
                }

                //---------------------purchasing---------

                if($appfunction->link == '/modules/requisitions/requisitions.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='/modules/requisitions/requisitions.php'$lnk[1]><i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == '/modules/requisitions/requisition_allocations.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='/modules/requisitions/requisition_allocations.php'$lnk[1]><i class='glyphicon glyphicon-retweet'></i>&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;" .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'purchasing/po_entry_items.php?NewOrder=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/po_entry_items.php?NewOrder=Yes'$lnk[1]> <i class='fa fa-shopping-bag'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'purchasing/inquiry/po_search.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/inquiry/po_search.php?'$lnk[1]> <i class='fa fa-search-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/po_entry_items.php?NewGRN=Yes')
                {


                    echo "  <link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.0.13/css/all.css\" integrity=\"sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp\" crossorigin=\"anonymous\">
<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/po_entry_items.php?NewGRN=Yes'$lnk[1]> <i class='fas fa-receipt'></i>&nbsp;&nbsp;&nbsp" .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/supplier_invoice.php?New=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/supplier_invoice.php?New=1'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/inquiry/grn_search_completed.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/inquiry/grn_search_completed.php?'$lnk[1]><i class='glyphicon glyphicon-import'></i>  &nbsp;  &nbsp;" .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }



                if($appfunction->link == 'purchasing/inquiry/grn_search.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/inquiry/grn_search.php?'$lnk[1]><i class='glyphicon glyphicon-import'></i>  &nbsp;  &nbsp;" .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/po_entry_items.php?NewInvoice=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/po_entry_items.php?NewInvoice=Yes'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/supplier_payment.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/supplier_payment.php?'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp;  " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/supplier_credit.php?New=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/supplier_credit.php?New=1'$lnk[1]><i class='fa fa-pencil-square-o'>   
</i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/allocations/supplier_allocation_main.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/allocations/supplier_allocation_main.php?' $lnk[1]><i class='glyphicon glyphicon-retweet'></i>&nbsp;&nbsp" .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/inquiry/po_search_completed.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/inquiry/po_search_completed.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;" .$lnk[0] . "
    <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/inquiry/pr_search_completed.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/inquiry/pr_search_completed.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'purchasing/inquiry/supplier_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/inquiry/supplier_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;" .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'purchasing/inquiry/supplier_allocation_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/inquiry/supplier_allocation_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;" .$lnk[0] . "
     <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'reporting/reports_main.php?Class=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=1'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/manage/suppliers_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/manage/suppliers_inquiry.php?'$lnk[1]><i class='fa fa-user-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp;" .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'purchasing/manage/supplier_category.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/manage/supplier_category.php?'$lnk[1]><i class='fa fa-caret-down'></i>&nbsp;&nbsp;&nbsp;&nbsp;  " .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'modules/import_items/import_suppliers.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_items/import_suppliers.php?'$lnk[1]><i class='glyphicon glyphicon-import'></i>  &nbsp;  &nbsp;  &nbsp; " .$lnk[0] . "
      <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'modules/import_supp_OB/import_supp_OB.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_supp_OB/import_supp_OB.php?'$lnk[1]> <i class='glyphicon glyphicon-import'></i> &nbsp;   &nbsp;" .$lnk[0] . "
  <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'modules/import_purchase_return/import_purchase_return.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_purchase_return/import_purchase_return.php?'$lnk[1]> <i class='glyphicon glyphicon-import'></i> &nbsp;   &nbsp;" .$lnk[0] . "
<span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'purchasing/manage/purchase_email.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/manage/purchase_email.php?'$lnk[1]><i class='fa fa-envelope'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                ///------------------------------------forinventoryicons-----------------------///


                if($appfunction->link == 'inventory/transfers.php?NewTransfer=1')
                {
                    echo " 
<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/transfers.php?NewTransfer=1'$lnk[1]><i class='fa fa-refresh'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/adjustments.php?NewAdjustment=1')
                {
                    echo "<link href=\"/static/fontawesome/fontawesome-all.css\" rel=\"stylesheet\"> 
  <a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/adjustments.php?NewAdjustment=1'$lnk[1]><i class=\"fas fa-archive\"></i>&nbsp;&nbsp;&nbsp;&nbsp; 
    " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'modules/import_items_adjustment/import_items_adjustment.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_items_adjustment/import_items_adjustment.php?'$lnk[1]><i  class='glyphicon glyphicon-import'></i>
    &nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/inquiry/stock_movements.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/inquiry/stock_movements.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/inquiry/stock_status_customize.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/inquiry/stock_status_customize.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/inquiry/stock_status.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/inquiry/stock_status.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'inventory/inquiry/daily_stock_movements.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/inquiry/daily_stock_movements.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                
                if($appfunction->link == 'inventory/inquiry/location_transfer_dashboard.php?')
             {
                 echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
 href='inventory/inquiry/location_transfer_dashboard.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
    <span class='label ".$bgcolor." '>$badge</span>
     </a>\n";
             }

                if($appfunction->link == 'modules/import_items/import_items.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_items/import_items.php?'$lnk[1]><i class='glyphicon glyphicon-import'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }




                if($appfunction->link == 'reporting/reports_main.php?Class=2')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=2'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/manage/items_inquiry.php?')
                {
                    echo " <a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/items_inquiry.php?'$lnk[1]><i class='fa fa fa-plus'></i> &nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'sales/manage/item_code.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/item_code.php?'$lnk[1]><i class='fa fa-barcode'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }





                if($appfunction->link == 'inventory/manage/item_codes.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/item_codes.php?'$lnk[1]><i class='fa fa-barcode'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/manage/sales_kits.php?')
                {
                    echo "<link href=\"/static/fontawesome/fontawesome-all.css\" rel=\"stylesheet\"><a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/sales_kits.php?'$lnk[1]><i class='fas fa-box'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'purchasing/manage/purchase_email.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/manage/purchase_email.php?'$lnk[1]><i class='fa fa-envelope'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/manage/item_categories.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/item_categories.php?'$lnk[1]><i class='fa fa-caret-down'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }



                if($appfunction->link == 'inventory/manage/locations.php?')
                {
                    echo " <a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/locations.php?'$lnk[1]><i class='fa fa-map-marker'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/manage/item_units.php?')
                {
                    echo "<link href=\"/static/fontawesome/fontawesome-all.css\" rel=\"stylesheet\"><a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/item_units.php?'$lnk[1]><i class='fab fa-uniregistry'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/reorder_level.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/reorder_level.php?'$lnk[1]><i class='fa fa-pie-chart'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'inventory/manage/batches.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/batches.php?'$lnk[1]><i class='fa fa-database'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }



                if($appfunction->link == 'inventory/prices.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/prices.php?'$lnk[1]><i class='fa fa-shopping-cart'></i> &nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/purchasing_data.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/purchasing_data.php?'$lnk[1]><i class='fa fa-shopping-bag'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/cost_update.php?')
                {
                    echo "<link href=\"/static/fontawesome/fontawesome-all.css\" rel=\"stylesheet\"><a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/cost_update.php?'$lnk[1]><i class='fas fa-money-bill-alt'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                //------------------Manufacturing----------------------------------//////////
              if($appfunction->link == 'manufacturing/work_order_entry.php?WOType=0')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/work_order_entry.php?WOType=0'$lnk[1]><i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                
                
                
                     if($appfunction->link == 'manufacturing/work_order_entry.php?WOType=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/work_order_entry.php?WOType=1'$lnk[1]><i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }



                if($appfunction->link == 'manufacturing/work_order_entry.php?WOType=2')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/work_order_entry.php?WOType=2'$lnk[1]><i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
//----------------------work order entry types end--------------------------------//

                
                
                
                
                if($appfunction->link == 'manufacturing/work_order_entry_cart.php?NewWorkOrder=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/work_order_entry_cart.php?NewWorkOrder=1'$lnk[1]><i class='fa fa-cart-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'manufacturing/work_order_entry_cart_req.php?NewWorkOrder=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/work_order_entry_cart_req.php?NewWorkOrder=1'$lnk[1]><i class='fa fa-cart-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'manufacturing/search_work_orders.php?outstanding_only=1')
                {
                    $badge = $open_wo_count;
                    $bgcolor = 'bg-red';

                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/search_work_orders.php?outstanding_only=1'$lnk[1]><i class='fa fa-search-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'manufacturing/inquiry/bom_cost_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/inquiry/bom_cost_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'manufacturing/inquiry/where_used_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/inquiry/where_used_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'manufacturing/search_work_orders.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/search_work_orders.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'manufacturing/search_work_orders_req.php?outstanding_only=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/search_work_orders_req.php?outstanding_only=1'$lnk[1]><i class='fa fa-search-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'reporting/reports_main.php?Class=3')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=3'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'manufacturing/manage/bom_edit.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/manage/bom_edit.php?'$lnk[1]><i class='fa fa-file-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'manufacturing/manage/work_centres.php?')
                {
                    echo "<link href=\"/static/fontawesome/fontawesome-all.css\" rel=\"stylesheet\"><a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='manufacturing/manage/work_centres.php?'$lnk[1]><i class='far fa-building'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                //--------------------Fixed Assets------------------------------------------//
                if($appfunction->link == 'purchasing/po_entry_items.php?NewInvoice=Yes&FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='purchasing/po_entry_items.php?NewInvoice=Yes&FixedAsset=1'$lnk[1]>
    <i class='fa fa-shopping-bag'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/transfers.php?NewTransfer=1&FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/transfers.php?NewTransfer=1&FixedAsset=1'$lnk[1]><i class=' fa fa-refresh'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/adjustments.php?NewAdjustment=1&FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/adjustments.php?NewAdjustment=1&FixedAsset=1'$lnk[1]><i class='fa fa-archive'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'sales/sales_order_entry.php?NewInvoice=0&FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/sales_order_entry.php?NewInvoice=0&FixedAsset=1'$lnk[1]><i class='fa fa-shopping-cart'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'fixed_assets/process_depreciation.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='fixed_assets/process_depreciation.php'$lnk[1]><i class='fa fa-recycle'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/inquiry/stock_movements.php?FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/inquiry/stock_movements.php?FixedAsset=1'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'fixed_assets/inquiry/stock_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='fixed_assets/inquiry/stock_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'reporting/reports_main.php?Class=7')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=7'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/manage/items.php?FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/items.php?FixedAsset=1'$lnk[1]><i class='fa fa-home'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/manage/locations.php?FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/locations.php?FixedAsset=1'$lnk[1]><i class='fa fa-map-marker'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'inventory/manage/item_categories.php?FixedAsset=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/item_categories.php?FixedAsset=1'$lnk[1]><i class='fa fa-caret-down'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'inventory/manage/category.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='inventory/manage/category.php?'$lnk[1]><i class='fa fa-caret-down'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'fixed_assets/fixed_asset_classes.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='fixed_assets/fixed_asset_classes.php'$lnk[1]><i class='device_hub'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

//-----------------------------------------------dimensions------------------------------------------//
                if($appfunction->link == 'dimensions/dimension_entry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='dimensions/dimension_entry.php?'$lnk[1]><i class='fa fa-object-ungroup'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'dimensions/inquiry/search_dimensions.php?outstanding_only=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='dimensions/inquiry/search_dimensions.php?outstanding_only=1'$lnk[1]><i class='fa fa-search-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'dimensions/inquiry/search_dimensions.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='dimensions/inquiry/search_dimensions.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'reporting/reports_main.php?Class=4')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=4'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'admin/tags.php?type=dimension')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/tags.php?type=dimension'$lnk[1]><i class='fa fa-tags'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                //-------------------------------------------------GL-----------------------------------------/
                if($appfunction->link == 'gl/gl_bank.php?NewPayment=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bank.php?NewPayment=Yes'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/gl_bank.php?NewDeposit=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bank.php?NewDeposit=Yes'$lnk[1]><i class='fa fa-university'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/gl_bankCV.php?NewPayment=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bankCV.php?NewPayment=Yes'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'gl/gl_bankCV.php?NewDeposit=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bankCV.php?NewDeposit=Yes'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'gl/bank_transfer.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/bank_transfer.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/gl_journal.php?NewJournal=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_journal.php?NewJournal=Yes'$lnk[1]><i class='fa fa-newspaper-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/gl_budget.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_budget.php?'$lnk[1]><i class='fa fa-dollar'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/bank_account_reconcile.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/bank_account_reconcile.php?'$lnk[1]><i class='fa fa-copy'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/accruals.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/accruals.php?'$lnk[1]><i class='fa fa-pie-chart'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'gl/inquiry/journal_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/journal_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/inquiry/gl_account_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/gl_account_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                
//                  if($appfunction->link == 'gl/inquiry/gl_account_types.php?')
//                 {
//                     echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
//   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
//     href='gl/inquiry/gl_account_types.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
//       <span class='label ".$bgcolor." '>$badge</span>
//         </a>\n";
//                 }

                if($appfunction->link == 'gl/manage/gl_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/gl_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/inquiry/bank_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/bank_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

if($appfunction->link == 'gl/inquiry/outstanding_cheques.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/outstanding_cheques.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/inquiry/tax_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/tax_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/inquiry/gl_trial_balance.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/gl_trial_balance.php?'$lnk[1]><i class='fa fa-balance-scale'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/inquiry/balance_sheet.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/balance_sheet.php?'$lnk[1]><i class='fa fa-calculator'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/inquiry/profit_loss.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/profit_loss.php?'$lnk[1]><i class='fa fa-bar-chart'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/inquiry/cash_flow_statement.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/cash_flow_statement.php?'$lnk[1]><i class='fa fa-line-chart'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'reporting/reports_main.php?Class=5')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=5'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'reporting/reports_main.php?Class=6')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=6'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/manage/bank_accounts.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/bank_accounts.php?'$lnk[1]><i class='fa fa-university'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/manage/gl_quick_entries.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/gl_quick_entries.php?'$lnk[1]><i class='fa fa-edit'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'admin/tags.php?type=account')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/tags.php?type=account'$lnk[1]><i class='fa fa-tag'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/manage/currencies.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/currencies.php?'$lnk[1]><i class='fa fa-eur'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }  if($appfunction->link == 'gl/manage/exchange_rates.php?')
            {
                echo "<link href=\"/static/fontawesome/fontawesome-all.css\" rel=\"stylesheet\"><a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/exchange_rates.php?'$lnk[1]><i class='fas fa-donate'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }
                if($appfunction->link == 'gl/inquiry/gl_account_types.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/gl_account_types.php?'$lnk[1]><i class='fa fa-university'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/manage/gl_account_types.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/gl_account_types.php?'$lnk[1]><i class='fa fa-object-group'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }  if($appfunction->link == 'gl/manage/gl_account_classes.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/gl_account_classes.php?'$lnk[1]><i class='fa fa-code-fork'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }  if($appfunction->link == 'gl/manage/close_period.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/close_period.php?'$lnk[1]><i class='fa fa-file-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }
                if($appfunction->link == 'gl/manage/revaluate_currencies.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/manage/revaluate_currencies.php?'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'gl/inquiry/gl_account_transfer_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/inquiry/gl_account_transfer_inquiry.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
//---------------------------setup-----------------------------------////
                if($appfunction->link == 'admin/company_preferences.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/company_preferences.php?'$lnk[1]><i class='far fa-building'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'admin/users.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/users.php?'$lnk[1]><i class='fas fa-user-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'admin/security_roles.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/security_roles.php?'$lnk[1]><i class='fa fa-unlock-alt'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'admin/user_locations.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/user_locations.php?'$lnk[1]><i class='fas fa-map-marker'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'admin/user_banks.php?')
                {
                    $badge = $user_banks_count;
                    $bgcolor = 'bg-gray';

                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/user_banks.php?'$lnk[1]><i class='fas fa-university'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'admin/display_prefs.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/display_prefs.php?'$lnk[1]><i class='fa fa-desktop'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'admin/forms_setup.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/forms_setup.php?'$lnk[1]><i class='fa fa-file-text-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'taxes/tax_types.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='taxes/tax_types.php?'$lnk[1]><i class='fa fa-percent'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'taxes/tax_groups.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='taxes/tax_groups.php?'$lnk[1]><i class='fa fa-object-group'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'taxes/item_tax_types.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='taxes/item_tax_types.php?'$lnk[1]><i class='fa fa-code-fork'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                } if($appfunction->link == 'admin/gl_setup.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/gl_setup.php?'$lnk[1]><i class='fa fa-bar-chart-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/fiscalyears.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/fiscalyears.php?'$lnk[1]><i class='fa fa-calendar-check-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }
                if($appfunction->link == 'admin/print_profiles.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/print_profiles.php?'$lnk[1]><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                } if($appfunction->link == 'admin/print_from_setup.php')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/print_from_setup.php'$lnk[1]><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/payment_terms.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/payment_terms.php?'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/shipping_companies.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/shipping_companies.php?'$lnk[1]><i class='fas fa-ship'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'sales/manage/sales_points.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/sales_points.php?'$lnk[1]><i class='fas fa-street-view'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/printers.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/printers.php?'$lnk[1]><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/crm_categories.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/crm_categories.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }
                if($appfunction->link == 'sales/manage/reports_preference.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/reports_preference.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'admin/void_transaction.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/void_transaction.php?'$lnk[1]><i class='fas fa-trash-alt'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                } if($appfunction->link == 'admin/view_print_transaction.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/view_print_transaction.php?'$lnk[1]><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/attachments.php?filterType=20')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/attachments.php?filterType=20'$lnk[1]><i class='fas fa-paperclip'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/system_diagnostics.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/system_diagnostics.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/backups.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/backups.php?'$lnk[1]><i class='fas fa-sync-alt'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }

                if($appfunction->link == 'admin/create_coy.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/create_coy.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'admin/inst_lang.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/inst_lang.php?'$lnk[1]><i class='fa fa-language	
'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'admin/inst_module.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/inst_module.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                } if($appfunction->link == 'admin/inst_theme.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/inst_theme.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            } if($appfunction->link == 'admin/inst_chart.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/inst_chart.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }
                if($appfunction->link == 'admin/inst_upgrade.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='admin/inst_upgrade.php?'$lnk[1]><i class='fa fa-exchange'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                //--------------------------crm----------------------//
                if($appfunction->link == 'project/task.php?type=task')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/task.php?type=task'$lnk[1]><i class='fa fa-tasks'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/demo.php?type=task')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/demo.php?type=task'$lnk[1]><i class='fa fa-paperclip'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/task.php?type=call')
                {
                    echo "<link href='/static/fontawesome/fontawesome-all.css' rel='stylesheet'><a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/task.php?type=call'$lnk[1]><i class='fas fa-phone'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/task.php?type=event')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/task.php?type=event'$lnk[1]><i class='fa fa-calendar-times-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/query.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/query.php?'$lnk[1]><i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/inquiry/task_inquiry.php?status=112')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/inquiry/task_inquiry.php?status=112'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/inquiry/task_grid.php?status=112')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/inquiry/task_grid.php?status=112'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/inquiry/kb_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/inquiry/kb_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/inquiry/query_inquiry.php?status=112')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/inquiry/query_inquiry.php?status=112'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/inquiry/call_log.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/inquiry/call_log.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/inquiry/calender.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/inquiry/calender.php?'$lnk[1]><i class='fa fa-calendar-plus-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'sales/manage/customers.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='sales/manage/customers.php?'$lnk[1]><i class='fa fa-user-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/manage/category.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/manage/category.php?'$lnk[1]><i class='fa fa-caret-down'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'project/manage/status.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/manage/status.php?'$lnk[1]><i class='fas fa-battery-quarter'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }if($appfunction->link == 'project/manage/duration.php?')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/manage/duration.php?'$lnk[1]><i class='far fa-clock'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }

                if($appfunction->link == 'project/manage/call_type.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/manage/call_type.php?'$lnk[1]><i class='fa fa-object-group'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'project/manage/query_status.php?')
                {
                    echo "
                    
                    <a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/manage/query_status.php?'$lnk[1]><i class='fas fa-battery-quarter'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }   if($appfunction->link == 'project/manage/source_status.php?')
            {
                echo "
                    
                    <a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/manage/source_status.php?'$lnk[1]><i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }

                if($appfunction->link == 'project/manage/setting.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='project/manage/setting.php?'$lnk[1]><i class='fa fa-wrench'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                //-------------------------payroll-----------------------------////
                if($appfunction->link == 'payroll/manage/suppliers2.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/suppliers2.php?'$lnk[1]><i class='fa fa-user-plus'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }   if($appfunction->link == 'payroll/manage/man_month_view_new.php')
            {
                echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/man_month_view_new.php'$lnk[1]><i class='fa  fa-calendar-plus-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
            }
                if($appfunction->link == 'modules/import_items/import_emp_attendance.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='modules/import_items/import_emp_attendance.php?'$lnk[1]><i class='glyphicon glyphicon-import'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/daily_attendance.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/daily_attendance.php'$lnk[1]><i class='fa  fa-calendar-plus-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/attendance.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/attendance.php'$lnk[1]><i class='fa  fa-calendar-plus-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/emp_info.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/emp_info.php?'$lnk[1]><i class='fa fa-user'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/inquiry/supplier_inquiry2.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/supplier_inquiry2.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/inquiry/supplier_inquiry_log.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/supplier_inquiry_log.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/inquiry/leave_inquery.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/leave_inquery.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/inquiry/attendance_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/attendance_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/category2.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/category2.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/dashboardemp.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/dashboardemp.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/dept.php?')
                {
                    echo "<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.0.13/css/all.css' integrity='sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp' crossorigin='anonymous'>

<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/dept.php?'$lnk[1]><i class='fas fa-door-closed'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/desg.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/desg.php?'$lnk[1]><i class='fa fa-graduation-cap'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/grade.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/grade.php?'$lnk[1]><i class='fa fa-plus-circle'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/allowance.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/allowance.php?'$lnk[1]><i class='far fa-money-bill-alt'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/deduction.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/deduction.php?'$lnk[1]><i class='fa fa-minus-circle'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/attendance_policy.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/attendance_policy.php?'$lnk[1]><i class='fa  fa-edit'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/leave_types.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/leave_types.php?'$lnk[1]><i class='fas fa-procedures'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/gazetted_holiday.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/gazetted_holiday.php?'$lnk[1]><i class='fas fa-tree'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/payment_mode.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/payment_mode.php?'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'payroll/manage/document_type.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/document_type.php?'$lnk[1]><i class='fa fa-file-pdf-o'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                if($appfunction->link == 'payroll/payroll_currpre_salary.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/payroll_currpre_salary.php'$lnk[1]><i class='fa fa-calculator'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/payroll.php')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/payroll.php'$lnk[1]><i class='fa fa-calculator'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/advance.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/advance.php?'$lnk[1]><i class='fa fa-user'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/leave_encashment.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/leave_encashment.php?'$lnk[1]><i class='fas fa-hand-holding-usd'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/gratuity.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/gratuity.php?'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/bulk_wise_gratuity_entry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/bulk_wise_gratuity_entry.php?'$lnk[1]><i class='fa fa-database'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/bulk_leave_encashment_entry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/bulk_leave_encashment_entry.php?'$lnk[1]><i class='fa  fa-object-group'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/increment.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/increment.php?'$lnk[1]><i class='fa fa-plus-circle'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/gl_bank_bulk.php?NewPayment=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bank_bulk.php?NewPayment=Yes'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/gl_bank_bulk_tax.php?NewPayment=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bank_bulk_tax.php?NewPayment=Yes'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'gl/gl_bank_bulk_eobi.php?NewPayment=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bank_bulk_eobi.php?NewPayment=Yes'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }




                if($appfunction->link == 'gl/gl_bank_bulk_emp.php?NewPayment=Yes')
                {
                    echo "<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.0.13/css/all.css' integrity='sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp' crossorigin='anonymous'>

<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='gl/gl_bank_bulk_emp.php?NewPayment=Yes'$lnk[1]><i class='fas fa-receipt'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }




                if($appfunction->link == 'payroll/inquiry/advance_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/advance_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/inquiry/leave_encashment_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/leave_encashment_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/inquiry/gratuity_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/gratuity_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/inquiry/payroll_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/payroll_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/inquiry/increament_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/inquiry/increament_inquiry.php?'$lnk[1]><i class='glyphicon glyphicon-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'reporting/reports_main.php?Class=8')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='reporting/reports_main.php?Class=8'$lnk[1]><i class='fa fa fa-print'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/gl_setup.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/gl_setup.php?'$lnk[1]><i class='fa fa-bar-chart'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/emp_grade.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/emp_grade.php?'$lnk[1]><i class='fa fa-plus-circle'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'payroll/manage/taxrate.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='payroll/manage/taxrate.php?'$lnk[1]><i class='fa fa-percent'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                //---------------------POS-------------------------///////
                if($appfunction->link == 'POS/sales_modify_entry.php?NewOrder=Yes')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/sales_modify_entry.php?NewOrder=Yes'$lnk[1]><i class='fa fa-desktop'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'POS/inquiry/sales_deliveries_view.php?OutstandingOnly=1')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/inquiry/sales_deliveries_view.php?OutstandingOnly=1'$lnk[1]><i class='fa fa-long-arrow-right'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }

                if($appfunction->link == 'POS/sales_order_entry.php?NewInvoice=0')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/sales_order_entry.php?NewInvoice=0'$lnk[1]><i class='fa fa-money'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'POS/customer_payments.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/customer_payments.php?'$lnk[1]><i class='fa fa-credit-card'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'POS/inquiry/sales_orders_view.php?type=30')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/inquiry/sales_orders_view.php?type=30'$lnk[1]><i class='fa fa-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'POS/inquiry/customer_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/inquiry/customer_inquiry.php?'$lnk[1]><i class='fa fa-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label'></span>
        </a>\n";
                }

                if($appfunction->link == 'POS/manage/customers.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/manage/customers.php?'$lnk[1]><i class='fa fa-table'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'POS/manage/sales_types.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/manage/sales_types.php?'$lnk[1]><i class='fa fa-object-group'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'POS/manage/sales_people.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/manage/sales_people.php?'$lnk[1]><i class='fa fa-users'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }
                if($appfunction->link == 'POS/manage/discount.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='POS/manage/discount.php?'$lnk[1]><i class='fa fa-percent'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }




                if($appfunction->link == 'complete_voucher/inquiry/complete_voucher_inquiry.php?')
                {
                    echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
   class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='complete_voucher/inquiry/complete_voucher_inquiry.php?'$lnk[1]><i class='fa fa-dashboard'></i>&nbsp;&nbsp;&nbsp;&nbsp; " .$lnk[0] . "
       <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";
                }


                $user = $_SESSION["wa_current_user"]->user;
                ?>

                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


                <script type='text/javascript'>
                    function AddHistoryTab(del, tab_name) {
                        var ajx = new XMLHttpRequest();
                        <?php
                        $myString = "&user_id=$user";
                        ?>
                        ajx.open("GET","admin/AddNewHistory.php?id="+del+"-"+tab_name+"<?= $myString ?>", true);
                        ajx.send();
                    }
                </script>
                <?php

//      echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;'
//class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
//    href='$appfunction->link'$lnk[1]>" .$lnk[0] . "
//        <span class='label ".$bgcolor." '>$badge</span>
//        </a>\n";

            }
        }
        else
            echo "<a class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12'
    	style='margin-top:3px;margin-right:2px;' disabled=''  href='#' title='"._("Inactive")."'
    	 alt='"._("Inactive")."'><span style='color:#cccccc;'>".access_string($appfunction->label, true)."
    	 </span></a>\n";
    }

    function menu_footer($no_menu, $is_index,$title)
    {
        global $leftmenu_save, $db_connections,$SysPrefs,$help_context,$path_to_root,$systypes_array;
//            ".show_users_online()."
        if (!$no_menu)
        {
            echo"<div class='hidden-xs' id='mydiv' style='background-color: #00cc00;position: fixed;height: 90px; bottom: 0; width: 0%; margin: 0 auto; left: 99%;'>
<span class='pull-right' style='margin-top:5px;margin-right:0;'> ";
            ?>
            <script>
                function expand()
                {

                    var a = document.getElementById('mydiv').style.height;
                    if(a == '200px' )
                    {
                        document.getElementById('mydiv').style.height ='90px';
                        document.getElementById('ex_div').style.height ='90px';
                    }else {
                        document.getElementById('mydiv').style.height ='200px';
                        document.getElementById('ex_div').style.height ='200px';
                    }
                }
                function closed()
                {
                    document.getElementById('mydiv').style.height = '90px';
                    document.getElementById('ex_div').style.height = '90px';
                }

            </script>
            <div class="box-header with-border" style="background-color:#3c8dbc !important ;width: 250px;border-radius:10px 10px 0 0 ; " id="ex_div">
                <div style="border-bottom: 2px solid #ecf0f5;height: 28px;color: whitesmoke"><h3 style="width: 87%;cursor: -webkit-grab;" class="box-title with-border" onclick="expand()">Need Help ?</h3></div>

                <div class="box-tools">
                    <i onclick="closed()"  class="fa fa-minus" style="margin-right: 5px;"></i>
                    <i onclick="expand()"  class="fa fa-plus" ></i>
                </div>
                <div style="width: 100%;height:30px;margin-top: 5px;">
                    <input class="form-control" type="text" id="kb_search" placeholder="Search in Knowledge base.." style="width:80%;height: 26px;float: left;">
                    <button class="btn btn-green" style="margin-left: 3px;height: 25px;" ><i onclick="search_on_click()" class="fa fa-search"></i></button>
                </div>
                <div style="background-color:#fff;width: 100%;height: 100%;margin-top: 5px;">
                    <?php

                    if ($help_context =='')
                    {
                        $gly = "fab fa-youtube";
                        echo "<a style='' target = '_blank' onclick=" .'"'."javascript:openWindow(this.href,this.target); return false;".'" '. "href='". help_url()."'><i class='$gly' style='margin-left: 3px;'></i>" . _(" Knowledge Base")."</a>";
                    }else
                    {
                        $gly = "fab fa-youtube";
                        echo "<a style='' target = '_blank' onclick=" .'"'."javascript:openWindow(this.href,this.target); return false;".'" '. "href='". help_url()."'><i class='$gly' style='margin-left: 3px;'></i>" . _(" Watch Video For ")."". $help_context."</a>";
                    }
                    ?>

                </div>
                <!-- /.box-tools -->
            </div>
            <script type='text/javascript'>
                var search_txt = document.getElementById('kb_search');
                function search_on_click()
                {
                    var url = 'http://support.hisaab.pk?s='+search_txt.value+'&post_type=st_kb' ;
                    // window.location = url;
                    window.open(url,'_blank')
                    document.my_formnew.submit();
                }

                search_txt.addEventListener('keyup', function(event) {
                    event.preventDefault();
                    if (event.keyCode === 13) {
                        var url = 'http://support.hisaab.pk?s='+search_txt.value+'&post_type=st_kb' ;
                        // window.location = url;
                        window.open(url,'_blank')
                        document.my_formnew.submit();
                    }
                });


            </script>";

            <?php
            echo"</span>  </div>";


            echo" <footer  style='position: fixed; height: 47px; float: left; bottom: 0; width: 100%; margin: 0 auto; left: 0; ' class='main-footer'>";
            echo"<div  class='pull-left '>";
            echo "  <div><span style='color:#3c8dbc;'></span>
  <strong> " .  get_company_pref('coy_name') ."
<b>  <span class='sp' style='color:#3c8dbc'>  &nbsp; Date : </span> <span class='sp'>".Today() . "</span> &nbsp;" ."<span class='sp' style='color:#3c8dbc'>Time : </span> <span class='sp'>". Now()."</span>
</b>
                </strong>
                </div>\n";
            echo"</div>";
            echo"<div style='margin-right: 50px;' class='pull-right hidden-xs'>";
            echo"<div class='pull-left hidden-xs'>";
            echo '  <b  id="recen">Recently Viewed :</b>';
            echo"</div>";
            $users = $_SESSION["wa_current_user"]->user;
            $sql = "SELECT DISTINCT  tab_name, url  FROM ".TB_PREF."history_tabs
          WHERE users_id = '$users'
          AND id NOT IN (SELECT MAX(id) FROM ".TB_PREF."history_tabs
          WHERE users_id = '$users'
         )
          
          ORDER BY id DESC LIMIT 3";
            $query = db_query($sql, "Error");
            while($Fetch = db_fetch($query)) {
                $TabName = get_company_pref_display($Fetch['tab_name']);
                $URL = $Fetch['url'];
                echo "<a style='  border-radius: 5px; height: 30px;   margin-top: -5px;
    margin-left: 3px; padding: 4px; float: right;' class='btn-primary' id='btn' href=$URL>
             <div>
                    <!--<span id='close'>X</span>-->
                    <p>$TabName</p>
                    </div>
                    </a>";
            }

            echo"</footer>";
            echo'
 <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark" >
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-calendar"></i> Todays Summary</a></li>
      <li id="daily_entries_routine">
  
      <a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-list"></i></i>
                <span class="label label-info">'. trans_count($today).'</span></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content" style="height: 720px;overflow-y: scroll;">
      <!-- Home tab content -->
      <div class="tab-pane active " id="control-sidebar-home-tab" >
        ';

//  <p class="control-sidebar-heading">Todays Summary</p>
            echo'<div class="col-lg-4">';
            echo'<label>SELECT Dimension</label>
        <select class="form-control"  id="dimension_filter" onchange="dimension_filter(this)">';
            echo'<option value="0">All</option>';
            $sql = "SELECT id,name FROM 0_dimensions WHERE type_='1' AND closed!='1'";
            $result = db_query($sql);
            while ($dt = db_fetch($result)) {
                echo'<option value="'.$dt['id'].'">'.$dt['name'].'</option>';
            }
            echo'</select>';
            echo'</div>';

            echo'<div class="col-lg-4">';
            echo'<label>Day</label>
       <select class="form-control" id="day_filter" onchange="day_filter(this)">
            <option value="1">Today</option>
            <option value="2">YesterDay</option>
            <option value="3">Last 7 Days</option>
            <option value="4">Last 14 Days</option>
            <option value="5">Last 30 Days</option>
            <option value="6">This week</option>
            <option value="7">Last week</option>
            <option value="8">This Month</option>
            <option value="9">Last Month</option>
        </select> ';
            echo'</div><div class="clearfix"></div>';

            echo'<style>.ab:hover{background-color: #1a2226;color: white;}
                .ab {padding: 10px 8px; margin-top: 10px;}
                .ab a{color: #edeeef;}
                </style>';

            echo'<div id="changed_data">';
            echo'<div class="control-sidebar-menu " >';
                echo'<div id="load_aside_menu_ds_data"></div>';

            echo'</div>';
            echo'</div>';

             echo'<div class="clearfix"></div>';

//************************
   echo'<div id="data_load">';
   echo'</div>';
   //************************

echo'</div>';

//      <!-- Settings tab content -->
            echo'<div class="tab-pane " id="control-sidebar-settings-tab" >';
            echo'  <div class="tab-pane " id="control-sidebar-settings-tab">';
        echo'<div id="daily_routine_entries">';


  echo'</div>'; //daily_routine_entries
            echo'
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>';
?>
                    <script>
                       $(".bell_icon").click(function(){

                           $.ajax({
                               url: '<?php global $path_to_root; echo $path_to_root . '/themes/premium/aside_bar_data/aside_menu_ds_data.php';?>',
                               type: 'POST',
                               // data :{dimension_id:dimension_id,day_id:day_id},
                               success: function (data) {
                                   $('#load_aside_menu_ds_data').html(data);
                               }
                           });

                           $.ajax({
                               url: '<?php global $path_to_root; echo $path_to_root . '/themes/premium/aside_bar_data/aside_menu_data.php';?>',
                               type: 'POST',
                               // data :{dimension_id:dimension_id,day_id:day_id},
                               success: function (data) {
                                   $('#data_load').html(data);
                               }
                           });
                       });
                       $("#daily_entries_routine").click(function(){

                           $.ajax({
                               url: '<?php global $path_to_root; echo $path_to_root . '/themes/premium/aside_bar_data/daily_entries_routine.php';?>',
                               type: 'POST',
                               // data :{dimension_id:dimension_id,day_id:day_id},
                               success: function (data) {
                                   $('#daily_routine_entries').html(data);
                               }
                           });

                       });
                    </script>

<?php

//            echo"<div class='control-sidebar-bg' style='background-color: yellow;'></div>";
            echo"</div>";//<!-- ./wrapper -->

        }
    }
}



echo "<script src='$path_to_root/themes/premium/all_js/jQuery-2.1.4.min.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jquery.sparkline.min.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jquery-jvectormap-1.2.2.min.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/fastclick.min.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jquery-jvectormap-world-mill-en.js'></script>";
//echo"<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/Chart.min.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/dashboard2.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_css/newdash.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/select.js'></script>";
//echo "<script src='$path_to_root/themes/".user_theme()."/all_js/demo.js'></script>";

//echo "<link href='$path_to_root/themes/".user_theme()."/all_css/select.css' rel='stylesheet' type='text/css'> \n";
echo '<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">';
echo "<link href='$path_to_root/themes/".user_theme()."/all_css/style.css' rel='stylesheet' type='text/css'> \n";
if($no_menu != 1)
{
    echo "<link href='$path_to_root/themes/" . user_theme() . "/all_css/bootstrap.min.css' rel='stylesheet' type='text/css'> \n";
}
else
echo "<link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' rel='stylesheet' type='text/css'> \n";

echo "<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'> \n";
echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">';
echo'<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

echo "<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'> \n";
//echo "<link href='$path_to_root/themes/".user_theme()."/all_css/jquery-jvectormap-1.2.2.css' rel='stylesheet' type='text/css'> \n";
echo "<link href='$path_to_root/themes/".user_theme()."/all_css/AdminLTE.min.css' rel='stylesheet' type='text/css'> \n";
echo "<link href='$path_to_root/themes/".user_theme()."/all_css/_all-skins.min.css' rel='stylesheet' type='text/css'> \n";


?>