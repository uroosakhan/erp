<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Mode Of Payment"));

include($path_to_root . "/payroll/includes/db/payment_mode.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

    $input_error = 0;

    if (strlen($_POST['add_notification']) == 0)
    {
        $input_error = 1;
        display_error(_("The sales group description cannot be empty."));
        set_focus('description');
    }

    if ($input_error != 1)
    {
        if ($selected_id != -1)
        {
            update_notification($selected_id, $_POST['add_notification'],$_POST['from_date'], $_POST['to_date']);
            $note = _('Selected Designation has been updated');
        }
        else
        {

            add_notification($_POST['add_notification'],$_POST['from_date'],$_POST['to_date']);
            $note = _('New Designation has been added');
        }

        display_notification($note);
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete')
{

    $cancel_delete = 0;

    // PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
//
//    if (key_in_foreign_table($selected_id, 'notification', 'emp_desig'))
//    {
//        $cancel_delete = 1;
//        display_error(_("Cannot delete this Designation because Employee have been created using this designation."));
//    }
    if ($cancel_delete == 0)
    {
        delete_notification($selected_id);
        display_notification(_('Selected Designation has been deleted'));
    } //end if Delete group
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    if ($sav) $_POST['show_inactive'] = 1;
}

///=======================================================


function get_emp_dept_name_new($dept_id)
{
    $sql = "SELECT LEFT(description,15) FROM ".TB_PREF."dept WHERE id = ".db_escape($dept_id);
    $result = db_query($sql, "could not get group");
    $row = db_fetch($result);
    return $row[0];
}
function get_emp_desg_name_new($dept_id)
{
    $sql = "SELECT LEFT(description,15) FROM ".TB_PREF."desg WHERE id = ".db_escape($dept_id);
    $result = db_query($sql, "could not get group");
    $row = db_fetch($result);
    return $row[0];
}

function get_emp_name_new($show_inactive)
{
    $sql = "SELECT SUBSTRING(emp_name,1,15) as emp_name ,emp_email,emp_mobile,emp_dept,emp_desig,emp_code  FROM ".TB_PREF."employee ";
    if (!$show_inactive) $sql .= " WHERE !inactive limit 10";
//    $sql .= " ORDER BY emp_name limit 10";
    return db_query($sql,"could not get title");
}

$results_per_page=30;

$sql="select * from ".TB_PREF."employee ";
$result = db_query($sql, "could not get group");
 $number_of_results=mysqli_num_rows($result);

//display_error($number_of_results);
 $number_of_pages=ceil($number_of_results/$results_per_page);

//display_error($number_of_pages);


if(!isset($_GET['page']))
{

    $page=1;
}
else
{

    $page=$_GET['page'];
}
if($page!=1)
$this_page_first_result=($page-1) * $results_per_page;
else
    $this_page_first_result=($page);

//
//display_error($this_page_first_result);
//
$sql="SELECT * from ".TB_PREF."employee where  employee_id!='' LIMIT $this_page_first_result,$results_per_page";
$results = db_query($sql, "could not get group");
//$number_of_results=mysqli_num_rows($result);


//var_dump($results);
//$result = get_emp_name_new(check_value('show_inactive'));
//start_form();
//start_table(TABLESTYLE, "width=30%");
//$th = array(_("ID"), _("Notification"));
//inactive_control_column($th);

//table_header($th);
$k = 0;
?>
<!---->


<style>


    /*!
 * bootstrap-vertical-tabs - v1.2.1
 * https://dbtek.github.io/bootstrap-vertical-tabs
 * 2014-11-07
 * Copyright (c) 2014 İsmail Demirbilek
 * License: MIT
 */
    .tabs-left, .tabs-right {
        border-bottom: none;
        padding-top: 2px;

    }
    .tabs-left {
        border-right: 1px solid #E8E8E8;

    }
    .tabs-right {
        border-left: 1px solid #E8E8E8;
    }
    .tabs-left>li, .tabs-right>li {
        float: none;
        margin-bottom: 2px;

    }
    .tabs-left>li {
        margin-right: -1px;
    }
    .tabs-right>li {
        margin-left: -1px;
    }
    .tabs-left>li.active>a,
    .tabs-left>li.active>a:hover,
    .tabs-left>li.active>a:focus {
        border-bottom-color:#E8E8E8;
        border-right-color: transparent;
    }

    .tabs-right>li.active>a,
    .tabs-right>li.active>a:hover,
    .tabs-right>li.active>a:focus {
        border-bottom: 1px solid #E8E8E8;
        border-left-color: transparent;
    }
    .tabs-left>li>a {
        border-radius: 4px 0 0 4px;
        margin-right: 0;
        display:block;
    }
    .tabs-right>li>a {
        border-radius: 0 4px 4px 0;
        margin-right: 0;
    }
    .sideways {
        margin-top:50px;
        border: none;
        position: relative;
    }
    .sideways>li {
        height: 20px;
        width: 120px;
        margin-bottom: 100px;
    }
    .sideways>li>a {
        border-bottom: 1px solid #E8E8E8;
        border-right-color: transparent;
        text-align: center;
        border-radius: 4px 4px 0px 0px;
    }
    .sideways>li.active>a,
    .sideways>li.active>a:hover,
    .sideways>li.active>a:focus {
        border-bottom-color: transparent;
        border-right-color: #E8E8E8;
        border-left-color: #E8E8E8;
    }
    .sideways.tabs-left {
        left: -50px;
    }
    .sideways.tabs-right {
        right: -50px;
    }
    .sideways.tabs-right>li {
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transform: rotate(90deg);
    }
    .sideways.tabs-left>li {
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -ms-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        transform: rotate(-90deg);
    }
</style>
<div class="container">
    <div style="background-color: ; border-style:double; border-color:#E0E0E0 ; width: 100%; height: 200px;">

        <img src="employee.JPG" style="float: left; margin: 20px;" height="150px" width="200px">
        <div style="background-color: ; float: left; margin-left: 50px; margin-top: 20px; width: 500px; height: 200px;">
<?php
//echo $_GET['employee_id'];
//if(isset($_GET['employee_id']))
//{

    $id=$_GET['employee_id'];

    $query=("select * from 0_employee where employee_id= ".$_GET['employee_id']);
    $res=db_query($query);
    $emp_name=db_fetch($res);


//display_error($emp_name);

?>

            <h4 style="border-color: ; border-bottom: ;">Employee Name   : <span style="margin-left: 20px;"><?php echo $emp_name['emp_name'] ?></span></h4>

            <h4 style="border-color: ; border-bottom: ;">Employee Id     :<span style="margin-left: 55px;"><?php echo $emp_name['emp_code'] ?></span></h4>
            <h4 style="border-color: ; border-bottom: ;">Email Id        :<span style="margin-left: 90px;"><?php echo $emp_name['emp_email'] ?></span></h4>
            <h4 style="border-color: ; border-bottom: ;">Contact Number  :<span style="margin-left: 25px;"><a href="#">+ ADD</a></span></h4>

        </div>
    </div>
</div>
<div class="container">


    <div class="row">


        <div class="col-sm-2">
            <ul class="nav nav-tabs tabs-left" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><h4>Employee Basic Info</h4></a></li>
                <li role="presentation"><a href="#attendence" aria-controls="attendence" role="tab" data-toggle="tab"><h4>Attendance</h4></a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><h4>Leave</h4></a></li>
                <li role="presentation"><a href="#allowance" aria-controls="allowance" role="tab" data-toggle="tab"><h4>Allowance</h4></a></li>
                <li role="presentation"><a href="#deduction" aria-controls="deduction" role="tab" data-toggle="tab"><h4>Deduction</h4></a></li>
                <li role="presentation"><a href="#experience" aria-controls="experience" role="tab" data-toggle="tab"><h4>Experience</h4></a></li>
                <li role="presentation"><a href="#qualification" aria-controls="qualification" role="tab" data-toggle="tab"><h4>Qualification</h4></a></li>
                <li role="presentation"><a href="#nomination" aria-controls="nomination" role="tab" data-toggle="tab"><h4>Nomination</h4></a></li>
                <li role="presentation"><a href="#family_details" aria-controls="family_details" role="tab" data-toggle="tab"><h4>Family Details</h4></a></li>
<!--                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><h4>Holidays</h4></a></li>-->
<!--                <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><h4>Salary</h4></a></li>-->
<!--                <li role="presentation"><a href="#settings1" aria-controls="settings" role="tab" data-toggle="tab"><h4>Personal</h4></a></li>-->
<!--                <li role="presentation"><a href="#settings12" aria-controls="settings" role="tab" data-toggle="tab"><h4>Contact</h4></a></li>-->
<!--                <li role="presentation"><a href="#settings123" aria-controls="settings" role="tab" data-toggle="tab"><h4>Skills</h4></a></li>-->
<!--                <li role="presentation"><a href="#settings1234" aria-controls="settings" role="tab" data-toggle="tab"><h4>Job History</h4></a></li>-->
<!---->
<!--                 <li role="presentation"><a href="#Training" aria-controls="settings" role="tab" data-toggle="tab"><h4>Training And Certification</h4></a></li>-->
<!--                <li role="presentation"><a href="#Dependancy" aria-controls="settings" role="tab" data-toggle="tab"><h4>Dependancy</h4></a></li>-->
<!--                <li role="presentation"><a href="#Work" aria-controls="settings" role="tab" data-toggle="tab"><h4>Work Eligibility</h4></a></li>-->
<!---->
<!--                <li role="presentation"><a href="#Medical" aria-controls="settings" role="tab" data-toggle="tab"><h4>Medical Claims</h4></a></li>-->
<!--                <li role="presentation"><a href="#Disability" aria-controls="settings" role="tab" data-toggle="tab"><h4>Disability</h4></a></li>-->
<!---->
<!---->
<!--                <li role="presentation"><a href="#Visa" aria-controls="settings" role="tab" data-toggle="tab"><h4>Visa And Imigration</h4></a></li>-->
<!--                <li role="presentation"><a href="#Corporate" aria-controls="settings" role="tab" data-toggle="tab"><h4>Corporate Card</h4></a></li>-->
<!--                <li role="presentation"><a href="#Additional" aria-controls="settings" role="tab" data-toggle="tab"><h4>Additional Details</h4></a></li>-->
<!---->
<!--                <li role="presentation"><a href="#Pay" aria-controls="settings" role="tab" data-toggle="tab"><h4>Pay Slips</h4></a></li>-->
<!--                <li role="presentation"><a href="#Benefits" aria-controls="settings" role="tab" data-toggle="tab"><h4>Benefits</h4></a></li>-->
<!--                <li role="presentation"><a href="#Remuneration" aria-controls="settings" role="tab" data-toggle="tab"><h4>Remuneration</h4></a></li>-->
<!--                <li role="presentation"><a href="#Security" aria-controls="settings" role="tab" data-toggle="tab"><h4>Security Credentials</h4></a></li>-->


            </ul>
        </div>
        <div class="col-sm-9">
            <div class="tab-content" style="margin-top: 20px; border-color: lightgrey; border-style: double; height: 1400px;">

                <div role="tabpanel" class="tab-pane active" id="home">


                    <table class="table"  border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <td>Employee Code</td>
                            <td><?php echo $emp_name['emp_code'] ?></td>
                            <td>Employee Id</td>
                            <td><?php echo $emp_name['employee_id'] ?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>First Name</td>
                            <td><?php echo $emp_name['emp_name'] ?></td>
                        </tr>
                        <tr>
                            <td scope="row">Last Name</td>
                            <td><?php echo $emp_name['emp_name'] ?></td>
                            <td>Role</td>
                            <td><?php echo get_emp_desg_name_new($emp_name["emp_desig"])?></td>

                        </tr>

                        <tr>
                            <td scope="row">Employee Father Name:</td>
                            <td><?php echo $emp_name['emp_father'] ?></td>
                            <td>Employee Bank A/C No:</td>
                            <td><?php echo $emp_name["emp_bank"]?></td>

                        </tr>

                        <tr>
                            <td scope="row">Employee CNIC:</td>
                            <td><?php echo $emp_name['emp_cnic'] ?></td>
                            <td>CNIC Expiry Date:</td>
                            <td><?php echo sql2date($emp_name["cnic_expiry_date"])?></td>

                        </tr>

                        <tr>
                            <td scope="row">Employee Type:</td>
                            <td><?php echo $emp_name['mb_flag'] ?></td>
                            <td>Employee Bank Branch:</td>
                            <td><?php echo $emp_name["bank_branch"]?></td>
                        </tr>

                        <tr>
                            <td scope="row">Blood Group:</td>
                            <td><?php echo $emp_name['blood_group'] ?></td>
                            <td>Mode Of Salary Payment:</td>
                            <td><?php
                                function get_payment_terms_all1($show_inactive)
                                {
                                    $sql = "SELECT * FROM ".TB_PREF."payment_terms";
                                    if (!$show_inactive) $sql .= " WHERE !inactive";
                                    return db_query($sql,"could not get payment terms");
                                }
                                echo get_payment_terms_all1($emp_name["salary"])?></td>
                        </tr>

                        <tr>
                            <td scope="row">Vehicle Provided To Employee:</td>
                            <td><?php echo $emp_name['vehicle'] ?></td>
                            <td>Company Bank:</td>
                            <td><?php echo $emp_name["company_bank"]?></td>
                        </tr>

                        <tr>
                            <td scope="row">Marital Status:</td>
                            <td><?php
                                if($emp_name['status'] == 0)
                                {
                                    echo"Single";
                                }
                                else
                                {
                                    echo"Married";
                                }
                                ?></td>
                            <td>Initial Salary:</td>
                            <td><?php echo $emp_name["basic_salary"]?></td>
                        </tr>

                        <tr>
                            <td>Previous Salary:</td>
                            <td><?php echo $emp_name["prev_salary"]?></td>
                        </tr>

                        <tr>
                            <td scope="row">Income Tax Deduction:</td>
                            <td><?php
                                if($emp_name['tax_deduction'] == 0)
                                {
                                    echo"NO";
                                }
                                else
                                {
                                    echo"YES";
                                }

                                 ?></td>
                            <td>Duty Hours:</td>
                            <td><?php echo $emp_name["duty_hours"]?></td>
                        </tr>

                        <tr>
                            <td scope="row">Grautuity applicable:</td>
                            <td><?php
                                if($emp_name['tax_deduction'] == 0)
                                {
                                    echo"NO";
                                }
                                else
                                {
                                    echo"YES";
                                }
                                ?></td>
                            <td>OT Hours:</td>
                            <td><?php echo $emp_name["ot_hours"]?></td>
                        </tr>


                        <tr>
                            <td scope="row">Leave encashment applicable:</td>
                            <td><?php
                                if($emp_name['leave_applicable'] == 0)
                                {
                                    echo"NO";
                                }
                                else
                                {
                                    echo"YES";
                                }
                                ?></td>
                            <td>Employee Bank Name.:</td>
                            <td><?php echo $emp_name["bank_name"]?></td>

                        </tr>


                        <tr>
                            <td scope="row">Sessi applicable:</td>
                            <td><?php
                                if($emp_name['sessi_applicable'] == 0)
                                {
                                    echo"NO";
                                }
                                else
                                {
                                    echo"YES";
                                }
                                ?></td>
                            <td>PEC No:</td>
                            <td><?php echo $emp_name["pec_no"]?></td>

                        </tr>

                        <tr>
                            <td scope="row">EOBI applicable:</td>
                            <td><?php
                                if($emp_name['eobi_applicable'] == 0)
                                {
                                    echo"NO";
                                }
                                else
                                {
                                    echo"YES";
                                }
                                ?></td>
                            <td>PEC Expiry Date:</td>
                            <td><?php echo sql2date($emp_name["pec_expiry_date"])?></td>
                        </tr>

                        <tr>
                            <td scope="row">Over Time:</td>
                            <td><?php
                                if($emp_name['over_time'] == 0)
                                {
                                    echo"NO";
                                }
                                else
                                {
                                    echo"YES";
                                }
                                ?></td>
                            <td>Social Security:</td>
                            <td><?php echo $emp_name["social_sec"]?></td>
                        </tr>


                        <tr>
                            <td scope="row">*Gender:</td>
                            <td><?php echo $emp_name['emp_gen'] ?></td>
                            <td>NTN:</td>
                            <td><?php echo $emp_name["emp_ntn"]?></td>
                        </tr>

                        <tr>
                            <td scope="row">Date of Birth:</td>
                            <td><?php echo sql2date($emp_name['DOB']) ?></td>
                            <td>EOBI No:</td>
                            <td><?php echo $emp_name["emp_eobi"]?></td>
                        </tr>

                        <tr>
                            <td scope="row">Age:</td>
                            <td><?php echo $emp_name['age'] ?></td>
                            <td>License No:</td>
                            <td><?php echo $emp_name["license_no"]?></td>
                        </tr>

                        <tr>
                            <td scope="row">Date of joining:</td>
                            <td><?php echo sql2date($emp_name['j_date']) ?></td>
                            <td>License Expiry Date:</td>
                            <td><?php echo sql2date($emp_name["license_expiry_date"])?></td>
                        </tr>

                        <tr>
                            <td scope="row">Date of leaving:</td>
                            <td><?php echo sql2date($emp_name['l_date']) ?></td>
                            <td>Department</td>
                            <td><?php echo get_emp_dept_name_new($emp_name["emp_dept"]) ?></td>
                        </tr>

                        <tr>
                            <td scope="row">Reference:</td>
                            <td><?php echo $emp_name['emp_reference'] ?></td>
                            <td scope="row">Email:</td>
                            <td><?php echo $emp_name['emp_email'] ?></td>

                        </tr>

                        <tr>
                            <td scope="row">Home Phone:</td>
                            <td><?php echo $emp_name['emp_home_phone'] ?></td>
                            <td scope="row">Mobile:</td>
                            <td><?php echo $emp_name['emp_mobile'] ?></td>
                        </tr>

                        <tr>
                            <td scope="row">Department:</td>
                            <td><?php echo get_emp_dept_name_new($emp_name['emp_dept']) ?></td>
                            <td scope="row">Designation:</td>
                            <td><?php echo get_emp_desg_name_new($emp_name['emp_desig']) ?></td>
                        </tr>


                        <tr>
                            <td scope="row"> </td>
                            <td> </td>
                            <td></td>
                            <td></td>
                        </tr>


                        <tr>
                            <td scope="row">Reporting Manager</td>
                            <td><?php echo $emp_name['report'] ?></td>

                        </tr>

                        <tr>


                        </tr>

                        <tr>
                            <td>Work Telephone Number</td>
                            <td><?php echo $emp_name['emp_mobile']?></td>
                        </tr>

                        <tr>
<!--                           <h2 style="border: 10px";>Income Tax Status</h2>-->
                            <td scope="row">Tax Filer:</td>
                            <td><?php
                                if($emp_name['text_filer'] == 0)
                                {
                                    echo"NO";
                                }
                                else
                                {
                                    echo"YES";
                                }

                                ?></td>
                            <td scope="row">Grade:</td>
                            <td><?php
                                function get_grade_name_new($id)
                                {
                                    $sql = "SELECT description FROM ".TB_PREF."grade WHERE id=".db_escape($id);

                                    $result = db_query($sql, "could not get sales type");

                                    $row = db_fetch_row($result);
                                    return $row[0];
                                }
                                echo get_grade_name_new($emp_name['emp_grade'])?></td>
                        </tr>



                        <tr>
                            <td scope="row">Physical Address:</td>
                            <td><?php echo $emp_name['emp_address']?></td>
                            <td scope="row">General Notes:</td>
                            <td><?php echo $emp_name['notes']?></td>
                        </tr>



                        <tr>

                            <td scope="row">Years of Experience</td>
                            <td><?php
                                $stock_img_link .= "<img id='item_img' alt = '[".$_POST['employee_id'].".jpg".
                                    "]' src='".company_path().'/images/'.trim($_POST['employee_id']).
                                    ".jpg?nocache=".rand()."'"." height='$pic_height' border='0'>";

                                $date1 = sql2date($emp_name['j_date']);
                                $day_num = Today();
                                $date_new=date_create($date1);
                                $date2=date_create($day_num);
                                $diff=date_diff($date_new,$date2);

                            echo $diff->format('%y%'); ?></td>
                        </tr>

                        </tbody>
                    </table>

                </div>



                <div role="tabpanel" class="tab-pane" id="profile">


                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>


                            <th >Alotted Leave Limit</th>
                            <th>Used Leave</th>
                            <th>Leave Balance</th>
                            <th>Alloted Year</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        function get_employee_genders($employee_id)
                        {
                            $sql = "SELECT description FROM ".TB_PREF."gen WHERE id=".db_escape($employee_id);

                            $result = db_query($sql, "could not get supplier");

                            $row = db_fetch_row($result);

                            return $row[0];
                        }

                        function get_emp_leave_type_ne($selected_id)
                        {
                            $sql = "SELECT max_accum_leaves FROM ".TB_PREF."leave_type WHERE id=".db_escape($selected_id);
                            $result = db_query($sql,"could not get department");
                            $ft = db_fetch_row($result);
                            return $ft[0];
                        }

                        $queryyyy="select * from 0_leave   where emp_id=".$_GET['employee_id'];
                        $res1=db_query($queryyyy);
                        // $emp_name2=db_fetch($res1);

                        while($myrow = db_fetch($res1)) {
                            echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo '<td >';

                            $accleave =get_emp_leave_type_ne($myrow['leave_type']);
                            //                            var_dump($emp_name1['leave_type']);
                            echo $accleave;

                            echo '</td>';




                            echo '<td>';


                            $leave= $myrow['no_of_leave'];
                            echo $leave;
                            echo'</td>';




                            echo'<td>';

                            $bleave= $accleave - $leave;
                            //                            var_dump($emp_name1['leave_type']);
                            echo $bleave;

                            echo'</td>';





                            echo '<td>';


//                            $fleave= $emp_name1['f_year'];
//                            echo $fleave;
                            echo "2017";
                            echo '</td>';


                            echo '</tr>';
                        }

                        ?>

                        </tbody>
                    </table>




                </div>



                <div role="tabpanel" class="tab-pane" id="attendence">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Project</th>
                            <th>Man Month Value</th>
                            <th>Month</th>

                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        function get_month_name1($month_id)
                        {
                            $sql = "SELECT description AS month_name FROM ".TB_PREF."month WHERE id=".db_escape($month_id);

                            $result = db_query($sql, "could not get month name");

                            $row = db_fetch_row($result);

                            return $row[0];
                        }

                        $query11=("select * from 0_man_month ");
                        $res11=db_query($query11);
                        $emp_name2=db_fetch($res11);

                        echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                        echo'<td >';

                        $accleave= $emp_name2['employee_name'];                        //                            var_dump($emp_name1['leave_type']);
                        echo $accleave;

                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['project_name'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['man_month_value'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= get_month_name1($emp_name2['month_id']);
                        echo $leave;
                        echo'</td>';
                        echo '</tr>';
                        ?>

                        </tbody>
                    </table>

                </div>



                <div role="tabpanel" class="tab-pane" id="allowance">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Allowances Name</th>
                            <th>Amount</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        function get_allow_name1($id)
                        {
                            $sql = "SELECT description FROM ".TB_PREF."allowance WHERE id=".db_escape($id);
                            $result = db_query($sql,"could not get group");
                            $row = db_fetch_row($result);
                            return $row[0];
                        }

                        $query1=("select * from 0_emp_allowance ");
                        $res1=db_query($query1);
                        $emp_name2=db_fetch($res1);

                        echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                        echo'<td >';

                        $accleave= get_allow_name1($emp_name2['allow_id']);                        //                            var_dump($emp_name1['leave_type']);
                        echo $accleave;

                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['amount'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        ?>

                        </tbody>
                    </table>

                </div>




                <div role="tabpanel" class="tab-pane" id="deduction">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Deduction Name</th>
                            <th>Amount</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        function get_deduct_name1($id)
                        {
                            $sql = "SELECT description FROM ".TB_PREF."deduction WHERE id=".db_escape($id);
                            $result = db_query($sql,"could not get group");
                            $row = db_fetch_row($result);
                            return $row[0];
                        }

                        $query1=("select * from 0_emp_deduction ");
                        $res1=db_query($query1);
                        $emp_name2=db_fetch($res1);

                        echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                        echo'<td >';

                        $accleave= get_deduct_name1($emp_name2['deduc_id']);                        //                            var_dump($emp_name1['leave_type']);
                        echo $accleave;

                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['amount'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        ?>

                        </tbody>
                    </table>

                </div>




                <div role="tabpanel" class="tab-pane" id="qualification">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Qualification Degree</th>
                            <th>Passing Year</th>
                            <th>Institute</th>
                            <th>Passing %</th>
                            <th>Remarks</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        $query1=("select * from 0_man_qualification ");
                        $res1=db_query($query1);
                        $emp_name2=db_fetch($res1);

                        echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                        echo'<td >';

                        $accleave= ($emp_name2['degree']);                        //                            var_dump($emp_name1['leave_type']);
                        echo $accleave;

                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['passing_year'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['institute'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['passing_percent'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['remarks'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        ?>

                        </tbody>
                    </table>

                </div>





                <div role="tabpanel" class="tab-pane" id="nomination">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Nominee Name</th>
                            <th>Relation with Nominee</th>
                            <th>Nominee Age</th>
                            <th>Nominee Share</th>
                            <th>Remarks</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        $query1=("select * from 0_employee_nomination ");
                        $res1=db_query($query1);
                        $emp_name2=db_fetch($res1);

                        echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                        echo'<td >';

                        $accleave= ($emp_name2['nominee_name']);                        //                            var_dump($emp_name1['leave_type']);
                        echo $accleave;

                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['relation'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['age'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['share'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['remarks'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        ?>

                        </tbody>
                    </table>

                </div>



                <div role="tabpanel" class="tab-pane" id="family_details">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Nominee Name</th>
                            <th>Relation with Nominee</th>
                            <th>Nominee Age</th>
                            <th>Nominee Share</th>
                            <th>Remarks</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        $query1=("select * from 0_employee_family_details ");
                        $res1=db_query($query1);
                        $emp_name2=db_fetch($res1);

                        echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                        echo'<td >';

                        $accleave= ($emp_name2['nominee_name']);                        //                            var_dump($emp_name1['leave_type']);
                        echo $accleave;

                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['relation'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['age'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['share'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['remarks'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        ?>

                        </tbody>
                    </table>

                </div>




                <div role="tabpanel" class="tab-pane" id="experience">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Date From</th>
                            <th>Date To</th>
                            <th>Designation</th>
                            <th>Remarks</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        $query1=("select * from 0_employment_history ");
                        $res1=db_query($query1);
                        $emp_name2=db_fetch($res1);

                        echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                        echo'<td >';

                        $accleave= ($emp_name2['company_name']);                        //                            var_dump($emp_name1['leave_type']);
                        echo $accleave;

                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['date_from'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['date_to'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['designation'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        $leave= $emp_name2['remarks'];
                        echo $leave;
                        echo'</td>';
                        echo '<td>';


                        ?>

                        </tbody>
                    </table>

                </div>




                <div role="tabpanel" class="tab-pane" id="messages">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Holiday Group</th>
                            <th>Date</th>

                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query1=("select * from 0_gazetted_holidays ");
                        $res1=db_query($query1);
                        $emp_name2=db_fetch($res1);

                       echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo'<td >';

                                $accleave= $emp_name2['description'];
                                //                            var_dump($emp_name1['leave_type']);
                                echo $accleave;

                                echo'</td>';
                            echo '<td>';


                                $leave= $emp_name2['date'];
                                echo $leave;
                                echo'</td>';
                    echo '</tr>';

                        ?>

                        </tbody>
                    </table>

                </div>


                <div role="tabpanel" class="tab-pane" id="settings">
                    <table class="table">
                        <thead>
                        <tr>
                            <td>Account Number</td>
                            <td><?php echo ($emp_name["emp_cnic"])?></td>
                            <td>Salary</td>
                            <td><?php echo ($emp_name["basic_salary"])?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td scope="row"> Account Holder Name</td>
                            <td><?php echo ($emp_name["emp_name"])?></td>
                            <td> Account Type</td>
                            <td><?php echo ($emp_name["emp_name"])?></td>
                        </tr>
                        <tr>
                            <td scope="row">Bank Name</td>
                            <td><?php echo ($emp_name["	bank_name"])?></td>

                        </tr>

                        </tbody>
                    </table>

                </div>



                <div role="tabpanel" class="tab-pane" id="settings1">

                    <table class="table">
                        <thead>
                        <tr>
                            <td>Gender</td>
                            <td><?php  echo get_employee_genders($emp_name['emp_gen']) ?></td>
                            <td>   Marital Status</td>
                            <td>Married</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td scope="row">C.I.N.C</td>
                            <td><?php echo $emp_name['emp_cnic'] ?></td>
                            <td>Ethnic Code</td>
                            <td>American Indian</td>
                        </tr>
                        <tr>
                            <td scope="row">Phone #</td>
                            <td><?php echo $emp_name['emp_home_phone'] ?></td>
                            <td>Language</td>
                            <td>English</td>
                        </tr>
                        <tr>
                            <td scope="row">Date of Birth</td>
                            <td><?php echo ($emp_name["DOB"])?></td>
                            <td>Blood Group</td>
                            <td>O+</td>
                        </tr>


                        <tr>
                            <td scope="row"></td>
                            <td></td>
                            <td> </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="4" style="border-style: double; border-color: lightgrey;" scope="row">IDENTITY DOCUMENTS ...</td>

                        </tr>



                        <tr>

                            <td> </td>
                            <td></td>
                        </tr>


                        <tr>
                            <td colspan="4" style="border-style: double; border-color: lightgrey;" scope="row">passport ...</td>

                        </tr>

                        <tr>

                            <td> </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border-style: double; border-color: lightgrey;" scope="row">passport-Expiry Date ...</td>

                        </tr>

                        <tr>

                            <td> </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border-style: double; border-color: lightgrey;" scope="row">driving licence ...</td>

                        </tr>


                        <tr>

                            <td> </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border-style: double; border-color: lightgrey;" scope="row"> driving licence-Expiry Date ...</td>

                        </tr>


                        </tbody>
                    </table>






                </div>


                <div role="tabpanel" class="tab-pane" id="settings12">

                    <table class="table">
                        <thead>
                        <tr >
                            <td colspan="2" style="border-style: double; border-color: lightgrey;"> Personal Email</td>
                            <td colspan="4" style="border-style: double; border-color: lightgrey;"><?php echo ($emp_name["emp_email"])?></td>

                        </tr>
                        </thead>
                        <tbody>
                        <tr >
                            <td  colspan="4" scope="row"></td>

                        </tr>


                        <tr >
                            <td  colspan="4" scope="row"><h4>IDENTITY DOCUMENTS</h4></td>

                        </tr>
                        <tr   style=" border-top: double;border-top-color: lightgrey;">

                            <td >Street Address</td>
                            <td> <?php echo ($emp_name["emp_address"])?></td>
                             <td>Country </td>
                            <td><?php echo ($emp_name["emp_name"])?></td>

                        </tr>
<tr >
                        <td > State</td>
                        <td> <?php echo ($emp_name["emp_name"])?></td>
                        <td>City </td>
                        <td><?php echo ($emp_name["emp_name"])?></td>

</tr>

                        <tr  style=" border-bottom: double;border-bottom-color: lightgrey;">
                            <td > Pincode</td>
                            <td> <?php echo ($emp_name[""])?></td>
      <td > </td>
                            <td> <?php echo ($emp_name[""])?></td>


                        </tr>


                        <tr >
                            <td  colspan="4" scope="row"></td>

                        </tr>


                        <tr >
                            <td  colspan="4" scope="row"><h4>CURRENT ADDRESS</h4></td>

                        </tr>
                        <tr   style=" border-top: double;border-top-color: lightgrey;">

                            <td >Street Address</td>
                            <td> <?php echo ($emp_name["emp_address"])?></td>
                            <td>Country </td>
                            <td><?php echo ($emp_name["emp_name"])?></td>

                        </tr>
                        <tr >
                            <td > State</td>
                            <td> <?php echo ($emp_name["emp_name"])?></td>
                            <td>City </td>
                            <td><?php echo ($emp_name["emp_name"])?></td>

                        </tr>

                        <tr  style=" border-bottom: double;border-bottom-color: lightgrey;">
                            <td > Pincode</td>
                            <td> <?php echo ($emp_name[""])?></td>
                            <td > </td>
                            <td> <?php echo ($emp_name[""])?></td>


                        </tr>





                        <tr >
                            <td  colspan="4" scope="row"></td>

                        </tr>


                        <tr >
                            <td  colspan="4" scope="row"><h4>EMERGENCY DETAILS</h4></td>

                        </tr>
                        <tr   style=" border-top: double;border-top-color: lightgrey;">

                            <td >Name </td>
                            <td> <?php echo ($emp_name["emp_name"])?></td>
                            <td>Number </td>
                            <td><?php echo ($emp_name["emp_home_phone"])?></td>

                        </tr>
                        <tr style=" border-bottom: double;border-bottom-color: lightgrey;">
                            <td > Email</td>
                            <td> <?php echo ($emp_name["emp_email"])?></td>
                            <td> </td>
                            <td><?php echo ($emp_name[""])?></td>

                        </tr>


                        </tbody>
                    </table>


                </div>
                <div role="tabpanel" class="tab-pane" id="settings123">

                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Holiday Group</th>
                            <th>Date</th>

                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query1="select * from 0_man_qualification  where employee_id=".$_GET['employee_id'];
                        $res1=db_query($query1);
                       // $emp_name2=db_fetch($res1);

                        while($myrow = db_fetch($res1)) {
                            echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo '<td >';

                            $accleave = $myrow['emp_name'];
                            //                            var_dump($emp_name1['leave_type']);
                            echo $accleave;

                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['degree'];
                            echo $leave;
                            echo '</td>';
                            echo '</tr>';
                        }

                        ?>

                        </tbody>
                    </table>





                </div>
                <div role="tabpanel" class="tab-pane" id="settings1234">


                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Department</th>
                            <th>From</th>
                            <th>To</th>

                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query1="select * from 0_employee  where employee_id=".$_GET['employee_id'];
                        $res1=db_query($query1);
                        // $emp_name2=db_fetch($res1);

                        while($myrow = db_fetch($res1)) {
                            echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo '<td >';

                            $accleave =get_emp_dept_name_new($myrow["emp_dept"]);
                            //                            var_dump($emp_name1['leave_type']);
                            echo $accleave;

                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['j_date'];
                            echo $leave;
                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['l_date'];
                            echo $leave;
                            echo '</td>';
                            echo '</tr>';
                        }

                        ?>

                        </tbody>
                    </table>




                </div>

                <div role="tabpanel" class="tab-pane" id="Experience">
                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Designation</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query1="select * from 0_employment_history  where employee_id=".$_GET['employee_id'];
                        $res1=db_query($query1);
                        // $emp_name2=db_fetch($res1);

                        while($myrow = db_fetch($res1)) {
                            echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo '<td >';

                            $accleave =($myrow["company_name"]);
                            //                            var_dump($emp_name1['leave_type']);
                            echo $accleave;

                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['date_from'];
                            echo $leave;
                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['date_to'];
                            echo $leave;
                            echo '</td>';

                            echo '<td>';


                            $leave = $myrow["designation"];
                            echo $leave;
                            echo '</td>';
                            echo '</tr>';
                        }

                        ?>

                        </tbody>
                    </table>


                </div>
                <div role="tabpanel" class="tab-pane" id="Education">
                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Institute Name</th>
                            <th>Passing Year</th>
                            <th>Persentage</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query1="select * from 0_man_qualification  where employee_id=".$_GET['employee_id'];
                        $res1=db_query($query1);
                        // $emp_name2=db_fetch($res1);

                        while($myrow = db_fetch($res1)) {
                            echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo '<td >';

                            $accleave =$myrow["degree"];
                            //                            var_dump($emp_name1['leave_type']);
                            echo $accleave;

                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['institute'];
                            echo $leave;
                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['passing_year'];
                            echo $leave;
                            echo '</td>';

                            echo '<td>';


                            $leave = $myrow["passing_percent"];
                            echo $leave;
                            echo '</td>';
                            echo '</tr>';
                        }

                        ?>

                        </tbody>
                    </table>

                </div>
                <div role="tabpanel" class="tab-pane" id="Training">
                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Institute Name</th>
                            <th>Passing Year</th>
                            <th>Persentage</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query1="select * from 0_man_qualification  where employee_id=".$_GET['employee_id'];
                        $res1=db_query($query1);
                        // $emp_name2=db_fetch($res1);

                        while($myrow = db_fetch($res1)) {
                            echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo '<td >';

                            $accleave =$myrow["degree"];
                            //                            var_dump($emp_name1['leave_type']);
                            echo $accleave;

                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['institute'];
                            echo $leave;
                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['passing_year'];
                            echo $leave;
                            echo '</td>';

                            echo '<td>';


                            $leave = $myrow["passing_percent"];
                            echo $leave;
                            echo '</td>';
                            echo '</tr>';
                        }

                        ?>

                        </tbody>
                    </table>

                </div>
                <div role="tabpanel" class="tab-pane" id="Medical">
                    Medical
                </div>
                <div role="tabpanel" class="tab-pane" id="Disability">
                    Disability
                </div>
                <div role="tabpanel" class="tab-pane" id="Dependancy">
                    <table class="table" border="1" style="border-color: lightgrey; border-style: double;">
                        <thead>
                        <tr>
                            <th>Dependant Name</th>
                            <th>Dependant Relation</th>
                            <th>Dependant Age</th>
                            <th>Share</th>


                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query1="select * from  0_employee_nomination  where employee_id=".$_GET['employee_id'];
                        $res1=db_query($query1);
                        // $emp_name2=db_fetch($res1);

                        while($myrow = db_fetch($res1)) {
                            echo ' <tr style="border-color: lightgrey; border-style:solid;">';

                            echo '<td >';

                            $accleave =$myrow["nominee_name"];
                            //                            var_dump($emp_name1['leave_type']);
                            echo $accleave;

                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['relation'];
                            echo $leave;
                            echo '</td>';
                            echo '<td>';


                            $leave = $myrow['age'];
                            echo $leave;
                            echo '</td>';

                            echo '<td>';


                            $leave = $myrow["share"];
                            echo $leave;
                            echo '</td>';
                            echo '</tr>';
                        }

                        ?>

                        </tbody>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="Visa">
                    Visa
                </div>
                <div role="tabpanel" class="tab-pane" id="Corporate">
                    Corporate
                </div>
                <div role="tabpanel" class="tab-pane" id="Work">
                    <table class="table">

                        <?php

                        $query0_employee_doc=("select * from 0_employee_doc where employee_id= ".$_GET['employee_id']);
                        $res=db_query($query0_employee_doc);
                        $work=db_fetch($res);
                        ?>
                        <thead>
                        <tr>
                            <td>Document Type</td>
                            <td><?php echo ($work["document_type"])?></td>
                            <td>Document Issue Date</td>
                            <td><?php echo ($work["doc_upload_date	"])?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td scope="row"> Document Expiry Date</td>
                            <td><?php echo ($work["expiry_date"])?></td>
                            <td> Issuing Authority Name</td>
                            <td><?php echo ($work["document_name"])?></td>
                        </tr>


                        </tbody>
                    </table>

                </div>
                <div role="tabpanel" class="tab-pane" id="Additional">
                    Additional
                </div>

                <div role="tabpanel" class="tab-pane" id="Pay">
                    Pay
                </div>
                <div role="tabpanel" class="tab-pane" id="Benefits">
                    Benefits
                </div>
                <div role="tabpanel" class="tab-pane" id="Remuneration">
                    Remuneration
                </div>
                <div role="tabpanel" class="tab-pane" id="Security">
                    Security
                </div>
        </div>
    </div>
</div>


<?php


//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

//if ($selected_id != -1)
//{
//    if ($Mode == 'Edit') {
//        //editing an existing group
//        $myrow = get_notification($selected_id);
//
//        $_POST['add_notification']  = $myrow["add_notification"];
//        $_POST['from_date']  = sql2date($myrow["from_date"]);
//        $_POST['to_date']  = sql2date($myrow["to_date"]);
//    }
//    hidden("selected_id", $selected_id);
//    label_row(_("ID"), $myrow["id"]);
//}

//text_row_ex(_("Add Notification"), 'add_notification', 30);
//date_row(_("From Date"),'from_date', null,null, 0, 0, 0, null, true);
//date_row(_("To Date:"),'to_date', null,null, 0, 0, 0, null, true);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
