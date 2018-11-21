<?php

	class renderer
	{
		function wa_header()
		{
			page(_($help_context = " "), false, true);
		}
			
		function wa_footer()
		{
			end_page(false, true);
			
		}

		function menu_header($title, $no_menu, $is_index)
		{

			global $path_to_root,$SysPrefs, $help_base_url,$img,$img2,$img3,$img4, $systypes_array, $db_connections;
			$local_path_to_root = $path_to_root;
			global $leftmenu_save, $app_title, $version;
            
  		

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
			echo " <div class='wrapper'>\n";
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
		//	echo "<div id='topsection'> \n";
		//	echo "  <div class='innertube'> \n";
      
      
      // images for notification icons
        $img = "$path_to_root/themes/".user_theme()."/images/receipt-plus-icon.png";
        $img2 = "$path_to_root/themes/".user_theme()."/images/payment-icon.png";        
        $img3 = "$path_to_root/themes/".user_theme()."/images/Inventory-maintenance-icon.png"; 
        $img4 = "$path_to_root/themes/".user_theme()."/images/help-browser.png"; 
          
            echo"<header class='main-header navbar-static-top'>";
        
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
            </li>
         
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
                <li><a href='$path_to_root/gl/gl_bankCV.php?NewPayment=Yes'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
                Cash Payment Voucher</a></li>
                <li><a href='$path_to_root/purchasing/supplier_payment.php?'>
                <i class='fa fa-arrow-circle-right text-blue' ></i>
                Supplier Payment Voucher</a></li>
                <li class='divider'></li>
                <li><a href='$path_to_root/inventory/inquiry/stock_status.php?'>
                <i class='fa fa-barcode text-green' ></i>
                Inventory Item Status</a></li>
                <li><a href='$path_to_root/sales/credit_note_entry.php?'>
                <i class='fa fa-line-chart text-blue' ></i>
                Credit Note</a></li>
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
            </li>
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
                
                <li><a href='http://dys-solutions.com/support' target=_blank >
                <i class='fa fa-support text-blue' ></i>Support Ticket</a></li>
                
                <li><a href='http://support.hisaab.pk/faq/accounting-software/'  target=_blank >
                <i class='fa fa-question text-yellow' ></i>FAQ's</a></li>
                <li><a href='http://hisaab.pk/contact-us/' target=_blank >
                <i class='fa fa-phone text-green' ></i>Contact Us</a></li>
                </ul>
            </li>
        ";

            $today = date2sql(Today());
            $fromdate = date2sql(Today()) . " 00:00:00";
        	$todate = date2sql(Today()). " 23:59.59";

    		$sql = "SELECT COUNT(r.id) AS count
            		FROM ".TB_PREF."refs AS r, ".TB_PREF."audit_trail AS a
            	    WHERE r.id=a.trans_no        			
                    AND r.type=a.type
                    AND a.stamp >= '$today 00:00:00'

    		    	";
    		$result = db_query($sql);
    		$trans_count = db_fetch($result);    
    		
    function get_sales_amount($trans_no,$trans_type)
{
	$sql = "SELECT ov_amount FROM ".TB_PREF."debtor_trans WHERE trans_no=".db_escape($trans_no) ." AND type =".db_escape($trans_type) ;

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

      if ($_SESSION['wa_current_user']->can_access('SS_AUDIT_TRAIL_VIEW'))
            echo "
                <!-- Audit Trail -->
                <li class='dropdown'>
                <a href='#' data-toggle='dropdown' class='fa fa-bell'><span class='caret'></span>
                <span class='label label-info'>$trans_count[count]</span>
                
                </a>
                <ul class='dropdown-menu' role='menu'>
                ";



    		$sql = "SELECT a.*,
            		SUM(IF(ISNULL(g.amount), NULL, IF(g.amount > 0, g.amount, 0))) AS amount,
            		u.user_id,
            		UNIX_TIMESTAMP(a.stamp) as unix_stamp
            		FROM ".TB_PREF."refs AS r, ".TB_PREF."audit_trail AS a
            		JOIN ".TB_PREF."users AS u
            		LEFT JOIN ".TB_PREF."gl_trans AS g ON (g.type_no=a.trans_no
            			AND g.type=a.type)
            		WHERE a.user = u.id
                    AND r.id=a.trans_no        			
                    AND r.type=a.type
                   AND g.type=a.type

            		GROUP BY a.trans_no	,
            	 a.type
		            ORDER BY a.stamp DESC LIMIT 30
    		    	";
    		$result = db_query($sql);
    		  if ($_SESSION['wa_current_user']->can_access('SS_AUDIT_TRAIL_VIEW'))

            while ($myrow = db_fetch($result))
            {
               
             if ($myrow['gl_seq'] == null)
            {
                $icon =  'fa fa-edit';
            	$color = 'text-aqua';
            }
            else
            {
            	$icon = 'fa fa-check-square';
            	$color = 'text-green';

            }

     if ($_SESSION['wa_current_user']->can_access('SS_AUDIT_TRAIL_VIEW'))

$sales_amount=get_sales_amount($myrow['trans_no'], $myrow['type']);
if($myrow['type'] == 10 || $myrow['type'] == 13)
{
    $amount = $sales_amount;
}
else
{
    $amount = $myrow['amount'];
}
        echo '<li>';
           $label = "<i class='$icon $color '  ></i> <b>".
                      $systypes_array[$myrow['type']] .' </b>'.
                      _('#') .' '.
                      $myrow['trans_no'] .' '.
                      _('Ref:') .' '.
                      get_reference($myrow['type'], $myrow['trans_no']) .' '.
                    _('Amount') .' <font color="red" >'. 
                      number_format2($amount, 0) .' </font>'. 
                      $myrow['description'] .' '.
                      _('by') .' <i>'.
                      $myrow['user_id'] .' </i>'. 
                      _('at') .' '.
                    date("H:i:s", $myrow['unix_stamp']) .' '.
                    _('on') .' '.
                    date('d-m-Y', $myrow['unix_stamp'])
                      ."
          ";
          
        echo get_trans_view_str($myrow['type'], $myrow['trans_no'], $label, false);
        echo '</li>';
            
            }
        
          echo "
               </ul>
            </li>        
          </ul>
          ";
         
         
       
 echo "
<div   
style='display: inline;' id='my_form1' name='my_form1' accept-charset=\"ISO-8859-1\">
        <!-- Search Box -->
        
            <div class='navbar-form navbar-left' role='search' >
            <div class='form-group' >
              <input type='text' dirname=''  class='form-control' id='navbar-search-input'  style='width:200px;'
      
            name='search_id'  >

            </div>
                    
            

            
            </div>
            </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        
</div> ";

echo"
<script type='text/javascript'>

var input = document.getElementById('navbar-search-input');
input.addEventListener('keyup', function(event) {

    event.preventDefault();
    if (event.keyCode === 13) {
    var url = '$path_to_root/gl/inquiry/journal_inquiry2.php?search_id='+ input.value  ;
   window.location = url;
   document.my_form1.submit();
    }
});


</script>";


         
          
        //   echo "
        // <!-- Search Box -->
        //     <div class='navbar-form navbar-left' role='search'>
        //     <div class='form-group'>
        //       <input type='text' class='form-control' id='navbar-search-input' placeholder='Search'>
        //     </div>
        //     </div>
        //     </div>  
        // <!-- /.navbar-collapse -->
        // <!-- Navbar Right Menu -->
        // "; 
        
                 
            //  <!-- Navbar Right Menu -->
			
              echo"<div class='navbar-custom-menu'>";
                echo"<ul class='nav navbar-nav'>";
                     echo"<li></li>";
                     
                     
               //<!-- Messages: style can be found in dropdown.less-->
            include_once("$path_to_root/themes/".user_theme()."/notification_ui.php");                    
                            $_dash= new notification();
                            
                            $_dash->All_notification();
			


//              <!-- User Account: style can be found in dropdown.less -->

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
               echo "  </li>";
              echo "  <li>";
              echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
              echo "  </li>";
              echo "  <!-- Control Sidebar Toggle Button -->";
                echo" <li>
                <a href='#' data-toggle='control-sidebar'><i class='fa fa-warning'></i></a>
              </li>";
              echo "  <li>";
             
                echo"</ul>"; 
              echo"</div>";    
  
          echo"  </nav>";
          echo"</header>";
          
				
           //echo viewer_link("Last entry was done $diff ago by $last_user_name", "");
          	//echo "    <h1 style='background-color:red;width:220px'>" . $app_title . " " . $version . "</h1>\n";
          			//	echo "  <div id='lowerinfo'>" . $app_title . " " . $version . "</div>\n";

			//echo "<h1 id='his' style='margin-left:50px;font-size:23px;'>Hisaab <span id='hisaabpk'>ERP : Hisaab.pk <i style='font-size:12px;'>Simple Cloud Accounting</i></span><h1>\n";

		//	echo "  </div>\n";

		//	echo "  <div id='topinfo'>" . $db_connections[$_SESSION["wa_current_user"]->company]["name"] ."</div>\n";

		/*	echo "  <div id='iconlink'>";
	   		// Logout on main window only
	   	/*	if (!$no_menu) {
	   	//	echo "<a id='btnlog' href='$local_path_to_root/access/logout.php?'>Log out</a>";	
        //echo "    <a class='' href='$local_path_to_root/access/logout.php?'><img src='$local_path_to_root/themes/grayblue/images/logoukost2.png' style='margin-top:-5px' width='120px' title='"._("Logout")."' /></a>";
     		}
  			// Popup help
     		if ($help_base_url != null) {
			  echo "<a target = '_blank' onclick=" .'"'."javascript:openWindow(this.href,this.target); return false;".'" '. "href='". help_url()."'><img src='$local_path_to_root/themes/grayblue/images/help-browser.png' title='"._("Help")."' /></a>\n";
	   		}
     		echo "  </div>\n"; // iconlink
     		echo "  </div>\n";
*/
        // <!-- Left side column. contains the logo and sidebar -->
             echo"<aside class='main-sidebar'>";
              // <!-- sidebar: style can be found in sidebar.less -->
              
                echo"<section class='sidebar'>";
                echo"<div class='user-panel'>";
                echo"<div class='pull-left image'>";
               // echo"<img src='".$User_logo."' class='img-circle' alt='User Image'>";
            echo"</div>";
            echo"<div class='pull-left info'>";
            /* echo" <p> " . $db_connections[$_SESSION["wa_current_user"]->company]["name"] ."</p>";
             //echo" <a href='#'><i class='fa fa-circle text-success'></i> Welcome</a>";
             
             /*$user_name = $_SESSION["wa_current_user"]->name;
             echo" <a href='#'><i class='fa fa-circle text-success'></i> $user_name</a>";*/
             
                                

           echo" </div>";
          echo"</div>";
    
       echo "
       <!-- search form 
      <form action='$path_to_root/gl/inquiry/journal_inquiry.php' method='get' class='sidebar-form'>
        <div class='input-group'>
          <input type='text' name='Ref' class='form-control' placeholder='Search...'>
              <span class='input-group-btn'>
                <button type='submit' name='search' id='search-btn' class='btn btn-flat'><i class='fa fa-search'></i>
                </button>
              </span>
        </div>
      </form>-->
            <!-- /.search form -->
      ";
      
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                        } //Sales
                        else if ($app->name == get_company_pref_display('sales_text')) {
                            $gly = "fa fa-line-chart";
                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                        } //Purchase
                        else if ($app->name == get_company_pref_display('purchase_text')) {
                            $gly = "fa fa-shopping-cart";

                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                        } else if ($app->name == "Human Resources") {
                            $gly = "fa fa-users";

                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                        } else if ($app->name == get_company_pref_display('setup_text')) {
                            $gly = "fa fa-wrench";

                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>
                              
                            </a>\n";
                        } else {
                            $gly = "fa fa-circle-o";
                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                            $gly = "fa fa-dashboard";
                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                        } //Sales
                        else if ($app->name == get_company_pref_display('sales_text')) {
                            $gly = "fa fa-line-chart";
                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                        } //Purchase
                        else if ($app->name == get_company_pref_display('purchase_text')) {
                            $gly = "fa fa-shopping-cart";

                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                        } else if ($app->name == "Human Resources") {
                            $gly = "fa fa-users";

                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                        } else if ($app->name == get_company_pref_display('setup_text')) {
                            $gly = "fa fa-wrench";

                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
                                SID . "'$acc[1]><i class='$gly'></i><span>" . $acc[0] . "</span>

                            </a>\n";
                        } else {
                            $gly = "fa fa-circle-o";
                            $leftmenu_save .= "      <li class='treeview'>";
                            $leftmenu_save .= "
                            <a style='' class='"
                                . ($sel_app == $app->id ? '' : '')
                                . "'href='$local_path_to_root/index.php?application=" . $app->id .
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
                echo "</ul>";
                echo "</section>";
                //<!-- /.sidebar -->
                echo "</aside>";
            }
		echo "	<div class='content-wrapper' style='background-color:white' >";
		

        echo"<section>";
			if ($title && !$no_menu)
             {
		
                    //<!-- Content Header (Page header) -->
                echo"<section class='content-header' style='background-color:;padding-bottom:4px;'>";
                  echo"<h1>";
                   	echo " <a style='' href='$local_path_to_root/index.php?application=".$curr_app_link. SID ."'>" . $curr_app_name . "</a>";
                           if ($no_menu)
        					echo "<br>";
        				   elseif ($title && !$is_index)
        					echo "<small><a id='mname' href='#'>" . $title . "</a></small>";
                            	$indicator = "$path_to_root/themes/".user_theme(). "/images/ajax-loader.gif";
               	echo " <span style=''><img id='ajaxmark' src='$indicator' style='visibility:hidden;'></span>";
               // echo"<small>Version 2.0</small>";
               
                 echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                 ';
                 
               if ($SysPrefs->help_base_url != null)
				{
				    $gly = "fas fa-life-ring  ";
				    
					echo "<a target = '_blank' onclick=" .'"'."javascript:openWindow(this.href,this.target); return false;".'" '. "href='". help_url()."'><i class='$gly' float:right;></i>" . _("Help")."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				
                          echo '
                
    
    
                 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     
                <a href="https://api.whatsapp.com/send?phone=923008115737&text=Hi Hisaab.pk Support i want to ask that" class="fa fa-whatsapp" target="blank"></a>
                <a href="https://m.me/HisaabErp" class="fab fa-facebook-messenger" target="blank"></a>                
                <a href="skype:hisaab.pk?call" class="fa fa-skype" target="blank"></a>                
                <a href="https://www.youtube.com/c/HisaabERP" class="fa fa-youtube" target="blank"></a>

                <a href="https://twitter.com/HisaabERP" class="fa fa-twitter" target="blank"></a>                
                <!--
                <a href="https://play.google.com/store/apps/details?id=com.Hisaabpk" class="fa fa-android" target="blank"></a>
                -->
                ';      
                echo"  </h1>";
          
                echo"  <ol class='breadcrumb'>";
               echo" <li><a href='$local_path_to_root/index.php?'><i class='fa fa-dashboard'></i>Home";
                
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
           
  
        
    echo"</section>";
		}

		function display_applications(&$waapp)
		{
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
		
      echo"<div class='control-sidebar-bg'></div>";
		  echo"</div>";//<!-- ./wrapper -->
    
              echo'  
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
 
 
       <div class="tab-pane" id="control-sidebar-home-tab">
';
	

    	global $systypes_array;
    	global $voucher_count;
  //	echo '<td>';
  
    	$types = $systypes_array;
    	$sr_no = 1;    
    	// exclude quotes, orders and dimensions
        	foreach (array(ST_PURCHORDER, ST_SALESORDER, ST_DIMENSION, ST_SALESQUOTE, ST_LOCTRANSFER) as $excl)
        			unset($types[$excl]);
        
            foreach
            ($types as $type_no => $gl_types)
            {
             $voucher_count = get_pending_vouchers($type_no);
           if($voucher_count == 0) continue;
            echo " $sr_no";
            echo " $gl_types";
            echo "    $voucher_count";  
            echo "</br>";
            $sr_no += 1;  
            }
            //	echo '</td>\n';
echo'
                </ul>
            </div>
    
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebars background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>   ';
                            }
                            
       
	
		   }
		  
          
         
         echo " </div>";//<!-- /.content-wrapper -->
         
         
		/*	$selected_app = $waapp->get_selected_application();

			foreach ($selected_app->modules as $module)
			{
				echo "      <div class='shiftcontainer'>\n";

					echo "        <div class='shadowcontainer'>\n";
								echo "          <div class='' style='background-color:;'>\n";
			echo "<div style='background-color:#357ca5 !important;text-align:center;padding:8px;'>";
				echo "            <b style='font-size:15px;color:white;'>" . str_replace(' ','&nbsp;',$module->name) . "</b><br />\n";
			echo "</div>";	

				echo "            <div class='buttonwrapper'>\n";

				foreach ($module->lappfunctions as $appfunction)
				{
					$this->renderButtonsForAppFunctions($appfunction);
				}

				foreach ($module->rappfunctions as $appfunction)
				{
					$this->renderButtonsForAppFunctions($appfunction);
				}

				echo "            </div>\n";
				echo "          </div>\n";
				echo "        </div>\n";
				echo "      </div>\n";
				echo "      <br />\n";
			
		}*/
        }
        
		function renderButtonsForAppFunctions($appfunction)
		{
            {
                $sql = "SELECT COUNT(DISTINCT sorder.order_no) 
                FROM ".TB_PREF."sales_orders as sorder,
    			".TB_PREF."sales_order_details as line
    			WHERE sorder.order_no = line.order_no
    			AND sorder.trans_type = line.trans_type
    			AND line.trans_type IN (30, 31)
    			AND line.quantity != 0
    			AND line.qty_sent=0
            	";
            	//AND line.quantity>0

            	$result = db_query($sql, "could not get debtors");
            	$row = db_fetch_row($result);
                $pending_dn_count = $row[0];
    			
                
                 $sql = "SELECT COUNT(DISTINCT d.trans_no) 
                FROM ".TB_PREF."debtor_trans as d,
    			".TB_PREF."debtor_trans_details as line
    			WHERE d.trans_no = line.debtor_trans_no
    			AND d.type = line.debtor_trans_type
    			AND d.type = 13
    			AND line.quantity !=0
    			AND line.qty_done=0
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

                $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."chart_types
                WHERE inactive != 1
                ";
            	$result = db_query($sql, "could not get chart types");
            	$row = db_fetch_row($result);
                $coa_group_count = $row[0];                
                        
                $sql = "SELECT COUNT(id)
                FROM ".TB_PREF."bank_trans
                WHERE reconciled IS NULL
                ";
            	$result = db_query($sql, "could not get bank reconciliations");
            	$row = db_fetch_row($result);
                $unreconcile_count = $row[0]; 
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
			        if($appfunction->link == 'sales/inquiry/customer_allocation_inquiry.php?')
        			{
            			$badge = $pending_cust_alloc_count;
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
			        if($appfunction->link == 'purchasing/manage/suppliers_inquiry.php?')
        			{
            			$badge = $supp_count;
                        $bgcolor = 'bg-gray';
            			}       
            		if($appfunction->link == 'inventory/manage/items_inquiry.php?')
        			{
            			$badge = $stock_count;
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
            		if($appfunction->link == 'gl/manage/gl_accounts.php?')
        			{
            			$badge = $coa_count;
                        $bgcolor = 'bg-gray';
            			}  	   
            		if($appfunction->link == 'gl/manage/gl_account_types.php?')
        			{
            			$badge = $coa_group_count;
                        $bgcolor = 'bg-gray';
            			}  	               			
            			
            		if($appfunction->link == 'gl/bank_account_reconcile.php?')
        			{
            			$badge = $unreconcile_count;
                        $bgcolor = 'bg-red';
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
            			
				// 	echo "<a class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;' 
				// 	href='$appfunction->link'$lnk[1]>" .$lnk[0] . " 
    // 					<span class='label ".$bgcolor." '>$badge</span>
    // 					</a>\n";
      echo "<a  onmousedown='AddHistoryTab(this, \"$lnk[0]\"); return true;' class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;'
    href='$appfunction->link'$lnk[1]>" .$lnk[0] . "
        <span class='label ".$bgcolor." '>$badge</span>
        </a>\n";

				}
			}
		    	else	
    				echo "<a class='btn  btn-primary col-lg-3 col-sm-3 col-xs-12' style='margin-top:3px;margin-right:2px;' disabled=''  href='#' title='"._("Inactive")."' alt='"._("Inactive")."'><span style='color:#cccccc;'>".access_string($appfunction->label, true)."</span></a>\n";
		}
    		function menu_footer($no_menu, $is_index)
		{
			global $leftmenu_save, $db_connections;

//            ".show_users_online()."
			if (!$no_menu)
			{
               

                // echo"<center>";
echo" <footer  style='position: fixed; height: 50px; float: left; bottom: 0; width: 96%; margin: 0 auto; left: 0; ' class='main-footer'>";
                // echo"<center>";
                echo"<div  class='pull-left '>";
        echo "  <div><span style='color:#3c8dbc;'></span>
  <strong> " .  get_company_pref('coy_name') ."
<b>  <span class='sp' style='color:#3c8dbc'>  &nbsp; Date : </span> <span class='sp'>".Today() . "</span> &nbsp;" ."<span class='sp' style='color:#3c8dbc'>Time : </span> <span class='sp'>". Now()."</span>
</b>
                </strong></div>\n";
                echo"";

                echo"</div>";
                
                
           echo"<div class='pull-right hidden-xs'>";
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
                 
                    echo "<a  style='  border-radius: 5px; height: 30px;   margin-top: -5px;
    margin-left: 3px; padding: 4px; float: right;' class='btn-primary' id='btn' href=$URL>
             <div>
                    <!--<span id='close'>X</span>-->
                    <p>$TabName</p>
                    </div>
                    </a>";
                }
                echo"</footer>";
                echo "    </div>\n";
                echo "  </div>\n";
                /*echo "string";*/
			}
            echo"</div>"; // <!-- ./main wrapper div's e -->
            echo "</div>\n";
/*
				if (isset($_SESSION['wa_current_user']))
					echo "<td class=bottomBarCell>" . Today() . " | " . Now() . "</td>\n";
				echo "<td align='center' class='footer'><a target='_blank' href='$power_url'><font color='#ffffff'>$app_title $version - " . _("Theme:") . " " . user_theme() . "</font></a></td>\n";
				echo "<td align='center' class='footer'><a target='_blank' href='$power_url'><font color='#ffff00'>$power_by</font></a></td>\n";
*/
		}                  
// 		function menu_footer($no_menu, $is_index)
// 		{
// 			global $leftmenu_save, $db_connections;


// 			if (!$no_menu)
// 			{
			
//          echo" <footer style=' position: fixed; float: left; bottom: 0; width: 95%; margin:0 auto; left: 0; ' class='main-footer'>";
         
//           // echo"<center>";
        
//           echo"<div class='pull-left '>";
//           	echo "  <div><span style='color:#3c8dbc'></span> <strong> " . $db_connections[$_SESSION["wa_current_user"]->company]["name"] ."
//           	".show_users_online()."
//           	</strong></div>\n";
//           echo"</div>";
        
//           echo"<div class='pull-right hidden-xs'>";
//             echo"<b><span style='color:#3c8dbc'>Date : </span> ".Today() . "&nbsp;" ."<span style='color:#3c8dbc'>Time : </span> ". Now()."</b>";
//           echo"</div>";
          
//         echo"<strong style='margin-left:20%;'>" . $_SESSION["wa_current_user"]->name . "</strong>";
        
//         //echo"</center>";
        
//          echo"</footer>";
          
        
// 				echo "    </div>\n";
// 				echo "  </div>\n";
// 					/*echo "string";*/
      

// 			}
            
            

//         echo"</div>"; // <!-- ./main wrapper div's e -->


// 			echo "</div>\n";

// /*
// 				if (isset($_SESSION['wa_current_user']))
// 					echo "<td class=bottomBarCell>" . Today() . " | " . Now() . "</td>\n";
// 				echo "<td align='center' class='footer'><a target='_blank' href='$power_url'><font color='#ffffff'>$app_title $version - " . _("Theme:") . " " . user_theme() . "</font></a></td>\n";
// 				echo "<td align='center' class='footer'><a target='_blank' href='$power_url'><font color='#ffff00'>$power_by</font></a></td>\n";
// */
// 		}

	}




echo '<style>

@media screen and (max-width: 680px) {
    #btn {
       display: none;
    }
    #recen{
    display: none;

    }
    .sp{
    display: none;

    }
}
</style>';
?>
<?php
/*
 echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/jQuery-2.1.4.min.js' defer></script>";			
  
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/bootstrap.min.js' defer></script>";			
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/fastclick.min.js' defer></script>";			
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/app.min.js'  defer></script>";			

    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/jquery.sparkline.min.js' defer></script>";			
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/jquery-jvectormap-1.2.2.min.js' defer></script>";
    
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/jquery-jvectormap-world-mill-en.js' defer></script>";
    			
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/jquery.slimscroll.min.js' defer></script>";
    //echo"<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'></script>";
		
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/Chart.min.js' defer></script>";
   echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/dashboard2.js' defer></script>";
   echo "<script async src='$path_to_root/themes/".user_theme()."/all_css/newdash.js'   defer></script>";
   
echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/select.js'      defer ></script>";
    			
    echo "<script async src='$path_to_root/themes/".user_theme()."/all_js/demo.js' defer></script>";
*/

?>
<?php
/*
echo "<link href='$path_to_root/themes/".user_theme()."/all_css/select.css' rel='stylesheet' type='text/css'> \n";

   echo '<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">';

 echo "<link href='$path_to_root/themes/".user_theme()."/all_css/style.css' rel='stylesheet' type='text/css'> \n";
 
   echo "<link href='$path_to_root/themes/".user_theme()."/all_css/bootstrap.min.css' rel='stylesheet' type='text/css'> \n";

 echo "<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'> \n";
 echo "<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'> \n";
 
   echo "<link href='$path_to_root/themes/".user_theme()."/all_css/jquery-jvectormap-1.2.2.css' rel='stylesheet' type='text/css'> \n";
  echo "<link href='$path_to_root/themes/".user_theme()."/all_css/AdminLTE.min.css' rel='stylesheet' type='text/css'> \n";
   echo "<link href='$path_to_root/themes/".user_theme()."/all_css/_all-skins.min.css' rel='stylesheet' type='text/css'> \n";
*/
?>

<?php
 echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jQuery-2.1.4.min.js'></script>";			
  
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/bootstrap.min.js'></script>";			
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/fastclick.min.js'></script>";			
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/app.min.js'></script>";			

    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jquery.sparkline.min.js'></script>";			
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jquery-jvectormap-1.2.2.min.js'></script>";
    
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jquery-jvectormap-world-mill-en.js'></script>";
    			
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/jquery.slimscroll.min.js'></script>";
    //echo"<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'></script>";
		
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/Chart.min.js'></script>";
   echo "<script src='$path_to_root/themes/".user_theme()."/all_js/dashboard2.js'></script>";
   echo "<script src='$path_to_root/themes/".user_theme()."/all_css/newdash.js'></script>";
   
echo "<script src='$path_to_root/themes/".user_theme()."/all_js/select.js'></script>";
    			
    echo "<script src='$path_to_root/themes/".user_theme()."/all_js/demo.js'></script>";



?>
<?php

echo "<link href='$path_to_root/themes/".user_theme()."/all_css/select.css' rel='stylesheet' type='text/css'> \n";

   echo '<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">';

 echo "<link href='$path_to_root/themes/".user_theme()."/all_css/style.css' rel='stylesheet' type='text/css'> \n";
 
   echo "<link href='$path_to_root/themes/".user_theme()."/all_css/bootstrap.min.css' rel='stylesheet' type='text/css'> \n";

 echo "<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'> \n";
 echo "<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'> \n";
 
   echo "<link href='$path_to_root/themes/".user_theme()."/all_css/jquery-jvectormap-1.2.2.css' rel='stylesheet' type='text/css'> \n";
  echo "<link href='$path_to_root/themes/".user_theme()."/all_css/AdminLTE.min.css' rel='stylesheet' type='text/css'> \n";
   echo "<link href='$path_to_root/themes/".user_theme()."/all_css/_all-skins.min.css' rel='stylesheet' type='text/css'> \n";


?>