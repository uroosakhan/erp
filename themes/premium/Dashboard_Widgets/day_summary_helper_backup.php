<?php
include_once("././themes/premium/dashboard_db.php");
$dimension = $_POST['dimension_id'];
$day_id = $_POST['day_id'];
$today = date('Y-m-d');

if($day_id == 1)
{
    $date = $today;
}else if($day_id ==2)
{
    $date ='2018-08-03';
}


echo'<ul class="control-sidebar-menu">
          <li>
            <a>
              <i class="menu-icon fa fa-line-chart bg-aqua"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Sales</h4>
                <p>'.get_todays_sales($dimension,$date).'</p>
              </div>
            </a>
          </li>
          <li>
            <a>
              <i class="menu-icon fa fa-sync bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Recovery</h4>
                <p>'.get_todays_recovery().'</p>
              </div>
            </a>
          </li>
          <li>
            <a>
              <i class="menu-icon fa fa-bar-chart bg-orange"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Sales Order</h4>

                <p>'.get_todays_sales_order().'</p>
              </div>
            </a>
          </li>
          <li>
            <a >
              <i class="menu-icon fa fa-shopping-cart bg-blue"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Purchase Order</h4>

                <p>'.get_todays_purchase_orders().'</p>
              </div>
            </a>
          </li>
           <li>
            <a >
              <i class="menu-icon fa fa-calculator bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Vendor Payment</h4>

                <p>'.get_vendor_payments().'</p>
              </div>
            </a>
          </li>
          
           <li>
            <a >
              <i class="menu-icon fa fa fa-exchange bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Sales Return</h4>

                <p>'.get_todays_sales_return().'</p>
              </div>
            </a>
          </li>          
        </ul>
      </div>';

?>