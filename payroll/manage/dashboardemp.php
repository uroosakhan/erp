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
}function get_emp_desg_name_new($dept_id)
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

while ($myrow = db_fetch($results)) {

//    alt_table_row_color($k);


//    end_row();


//    end_table(1);
    ?>

    <div style="background-color: ; height: 200px; width: 250px; float: left; margin-left: 20px; margin-top: 30px;">
        <div  class="card" style="background-color:#E8E8E8; ">

            <img class="card-img-top" src="employee.JPG" style="float: left; width: 100px; height: 100px; padding: 20px;">

            <h5 style="padding: 25px;"><b><?php label_cell($myrow["emp_name"]); ?></b><br><?php label_cell(get_emp_desg_name_new($myrow["emp_desig"])); ?><BR><?php label_cell(get_emp_dept_name_new($myrow["emp_dept"])); ?></h5>
        </div>

        <h6  style=" margin-left: 20px; margin-top: -5px;">
            <img src="em.jpg" height="15px" width="15px">
            &nbsp; <?php label_cell($myrow["emp_email"]); ?>
        </h6>
        <h6 style="margin-left: 20px; margin-top: -5px;">
            <img src="tel.jpg" height="15px" width="15px">
            &nbsp; <?php label_cell( $myrow['emp_email']); ?></h6>
        <h6 style="margin-left: 20px; margin-top: -5px;"><img src="em.jpg" height="15px" width="15px">  &nbsp;&nbsp; <?php label_cell($myrow["emp_code"]); ?></h6>
        <h5><a href='empinfodash.php?&&employee_id=<?php echo $myrow[0] ?>' style="margin-left: 180px;"> View Profile</a></h5>





    </div>

    <?php
}
$endpage = ceil($number_of_results/$results_per_page);
$startpage = 1;
$nextpage = $page + 1;
$previouspage = $page - 1;
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<center><nav>
        <ul class="pagination">


            <?php if($page != $startpage){ ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $startpage ?>" tabindex="-1" aria-label="Previous">
                        <span aria-hidden="true">First</span>
                        <span class="sr-only">First</span>
                    </a>
                </li>
            <?php } ?>



            <?php if($page >= 2){ ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $previouspage ?>"><?php echo $previouspage ?></a></li>
            <?php } ?>

            <li class="page-item active"><a class="page-link" href="?page=<?php echo $page ?>"><?php echo $page ?></a></li>

            <?php if($page != $endpage){ ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $nextpage ?>"><?php echo $nextpage ?></a></li>
            <?php } ?>

            <?php if($page != $endpage){ ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $endpage ?>" aria-label="Next">
                        <span aria-hidden="true">Last</span>
                        <span class="sr-only">Last</span>
                    </a>
                </li>
            <?php } ?>


            <!--    -->
            <!--    --><?php //if($page != $startpage){ ?>
            <!--        <li class="page-item">-->
            <!--            <a class="page-link" href="?page=--><?php //echo $startpage ?><!--" tabindex="-1" aria-label="Previous">-->
            <!---->
            <!--                <span aria-hidden="true">&laquo;</span>-->
            <!--                <span class="sr-only">First</span>-->
            <!--            </a>-->
            <!--        </li>-->
            <!--    --><?php //} ?>
            <!--    <li class="page-item"><a class="page-link" href="#">-->
            <!---->
            <!---->
            <!---->
            <!--            --><?php //for ($page=1;$page<=$number_of_pages;$page++)
            //            {
            //
            //                echo '<a href="dashboardemp.php?page='.$page.'">'.$page.'</a>';
            //
            //            }?><!--</a></li>-->
            <!--    --><?php //if($page != $endpage){ ?>
            <!--        <li class="page-item">-->
            <!--            <a class="page-link" href="?page=--><?php //echo $endpage ?><!--" aria-label="Next">-->
            <!--                <span aria-hidden="true">&raquo;</span>-->
            <!--                <span class="sr-only">Last</span>-->
            <!--            </a>-->
            <!--        </li>-->
            <!--    --><?php //} ?>
        </ul>
    </nav></center>

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

