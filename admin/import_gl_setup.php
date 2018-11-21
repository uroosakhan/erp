<?php
$page_security = 'SA_GLSETUP';
$path_to_root="..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "System and General GL Setup"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/admin/db/gl_set_db.inc");

simple_page_mode(true);


if (isset($_POST['submit']))
{

    $input_error = 0;

    if ($input_error != 1)
    {
        if ($account != -1)
        {
            update_sys_pay($_POST['Landing_Amt'], $_POST['account'],$_POST['text_field'],$_POST['text_field_n'],
                $_POST['gl_entry'],$_POST['unit_cost'],$_POST['header_enable'],$_POST['as_per_be_enable']);
            update_sys_pay_account($_POST['INS_Amt'], $_POST['account'],$_POST['text_field_1'],$_POST['text_field_n1'],
                $_POST['gl_entry_1'],$_POST['unit_cost_1'],$_POST['header_enable1'],$_POST['as_per_be_enable1']);
            update_sys($_POST['F_E_D_Amt'], $_POST['account'],$_POST['text_field_2'],$_POST['text_field_n2'],
                $_POST['gl_entry_2'],$_POST['unit_cost_2'],$_POST['header_enable2'],$_POST['as_per_be_enable2']);

            update_sys_pay_list($_POST['Duty_Amt'], $_POST['account'],$_POST['text_field_3'],$_POST['text_field_n3'],
                $_POST['gl_entry_3'],$_POST['unit_cost_3'],$_POST['header_enable3'],$_POST['as_per_be_enable3']);


            update_sys_pay_taxes($_POST['S_T_Amt'], $_POST['account'],$_POST['text_field_4'],$_POST['text_field_n4'],
                $_POST['gl_entry_4'],$_POST['unit_cost_4'],$_POST['header_enable4'],$_POST['as_per_be_enable4']);


            update_sys_pay_i_taxes($_POST['I_Tax_Amt'], $_POST['account'],$_POST['text_field_5'],$_POST['text_field_n5'],
                $_POST['gl_entry_5'],$_POST['unit_cost_5'],$_POST['header_enable5'],$_POST['as_per_be_enable5']);


            update_sys_pay_add_taxes($_POST['Add_S_T_Amt'], $_POST['account'],$_POST['text_field_6'],
                $_POST['text_field_n6'],$_POST['gl_entry_6'],$_POST['unit_cost_6'],$_POST['header_enable6'],$_POST['as_per_be_enable6']);


            update_sys_pay_other_expense($_POST['Other_Expense'], $_POST['account'],$_POST['text_field_7'],
                $_POST['text_field_n7'],$_POST['gl_entry_7'],$_POST['unit_cost_7'],$_POST['header_enable7'],$_POST['as_per_be_enable7']);
            $note = _('Selected gl accounts has been updated');
        }
        else
        {
            //add_emp_dept($_POST['description']);
            //$note = _('New sales group has been added');
        }

        display_notification($note);
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete')
{

    $cancel_delete = 0;

    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    if ($sav) $_POST['show_inactive'] = 1;
}
?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Shadow and Glow Transitions</title>

        <style>

            [class^="hvr-"] {
                background:#3c8dbc;
                color: #FFFFFF;
                cursor: pointer;
                margin: 0;
                padding:10px;
                text-decoration: none;

            }


            /* SHADOW/GLOW TRANSITIONS */
            /* Glow */
            .hvr-glow {

                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-glow:hover, .hvr-glow:focus, .hvr-glow:active {
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.6);
            }

            /* Shadow */
            .hvr-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-shadow:hover, .hvr-shadow:focus, .hvr-shadow:active {
                box-shadow: 0 10px 10px -10px rgba(0, 0, 0, 0.5);
            }

            /* Grow Shadow */
            .hvr-grow-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow, transform;
                transition-property: box-shadow, transform;
            }
            .hvr-grow-shadow:hover, .hvr-grow-shadow:focus, .hvr-grow-shadow:active {
                box-shadow: 0 10px 10px -10px rgba(0, 0, 0, 0.5);
                -webkit-transform: scale(1.1);
                transform: scale(1.1);
            }

            /* Box Shadow Outset */
            .hvr-box-shadow-outset {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-box-shadow-outset:hover, .hvr-box-shadow-outset:focus, .hvr-box-shadow-outset:active {    color: #000203;
                box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.6);
            }

            /* Box Shadow Inset */
            .hvr-box-shadow-inset {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
                box-shadow: inset 0 0 0 rgba(0, 0, 0, 0.6), 0 0 1px rgba(0, 0, 0, 0);
                /* Hack to improve aliasing on mobile/tablet devices */
            }
            .hvr-box-shadow-inset:hover, .hvr-box-shadow-inset:focus, .hvr-box-shadow-inset:active {    color: #000203;
                box-shadow: inset 2px 2px 2px rgba(0, 0, 0, 0.6), 0 0 1px rgba(0, 0, 0, 0);
                /* Hack to improve aliasing on mobile/tablet devices */
            }


            /* Float Shadow */
            .hvr-float-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                position: relative;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: transform;
                transition-property: transform;
            }
            .hvr-float-shadow:before {
                pointer-events: none;
                position: absolute;
                z-index: -1;
                content: '';
                top: 100%;
                left: 5%;
                height: 10px;
                width: 90%;
                opacity: 0;
                background: -webkit-radial-gradient(center, ellipse, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
                /* W3C */
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: transform, opacity;
                transition-property: transform, opacity;
            }

            .hvr-float-shadow:hover, .hvr-float-shadow:focus, .hvr-float-shadow:active {   background:#006699;   color: #000203;
                -webkit-transform: translateY(-5px);
                transform: translateY(-5px);
                /* move the element up by 5px */
            }



            .hvr-float-shadow:hover:before, .hvr-float-shadow:focus:before, .hvr-float-shadow:active:before {
                opacity: 1;
                -webkit-transform: translateY(5px);
                transform: translateY(5px);
                /* move the element down by 5px (it will stay in place because it's attached to the element that also moves up 5px) */
            }

            /* Shadow Radial */
            .hvr-shadow-radial {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                position: relative;
            }
            .hvr-shadow-radial:before, .hvr-shadow-radial:after {
                pointer-events: none;
                position: absolute;
                content: '';
                left: 0;
                width: 100%;
                box-sizing: border-box;
                background-repeat: no-repeat;
                height: 5px;
                opacity: 0;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: opacity;
                transition-property: opacity;
            }
            .hvr-shadow-radial:before {
                bottom: 100%;
                background: -webkit-radial-gradient(50% 150%, ellipse, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at 50% 150%, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
            }
            .hvr-shadow-radial:after {
                top: 100%;
                background: -webkit-radial-gradient(50% -50%, ellipse, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at 50% -50%, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
            }
            .hvr-shadow-radial:hover:before, .hvr-shadow-radial:focus:before, .hvr-shadow-radial:active:before, .hvr-shadow-radial:hover:after, .hvr-shadow-radial:focus:after, .hvr-shadow-radial:active:after {
                opacity: 1;
            }

        </style>
    </head>

    <body>



    <center>
        <td><a class="hvr-float-shadow" href="gl_setup.php"><i class="fa fa-dashboard " style="margin-right: 5px; font-size: large;">  </i> MAIN</a></td>

        <td><a class="hvr-float-shadow" href="hf_pref.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i>HEADER/FOOTER</a></td>

        <td><a class="hvr-float-shadow" href="item_pref.php"><i class="fa fa-barcode" style="margin-right: 5px; font-size: large;"></i> ITEM PREF</a></td>
        <td><a class="hvr-float-shadow" href="company_preferences_new.php"><i class="fa fa-circle-o" style="font-size: large; margin-right: 5px;"></i> FORM DISPLAY</a></td>

        <td><a class="hvr-float-shadow" href="meta_forward.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT PREFERENCES</a></td>

        <td><a class="hvr-float-shadow" href="import_gl_setup.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> IMPORT GL</a></td>
        <td><a class="hvr-float-shadow" href="cashflow_gl.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> CASH FLOW</a></td>
        <td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> WHT GL</a></td>

    </center>



    </body>
    </html>


<?php
start_form();

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);
echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry', get_sys_pay_pref_gl_entry('Landing_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost', get_sys_pay_pref_cost_unit('Landing_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Show in Form"), 'header_enable', get_sys_pay_pref_header('Landing_Amt'));
echo'</td>';


text_cells_ex(_("Type 1:"),'text_field',20,50,get_sys_pay_pref_field('Landing_Amt'));
text_row_ex(_("Default Value"),'text_field_n',20,50,null,get_sys_pay_pref_field_n('Landing_Amt'));
gl_all_accounts_list_row(_(""), 'Landing_Amt', get_sys_pay_pref('Landing_Amt'));




echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry_1', get_sys_pay_pref_gl_entry('INS_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost_1', get_sys_pay_pref_cost_unit('INS_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Show in Form"), 'header_enable1', get_sys_pay_pref_header('INS_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Calculate as per B.E"), 'as_per_be_enable1', get_sys_pay_pref_as_per_be('INS_Amt'));
echo'</td>';

text_cells_ex(_("Type 2:"),'text_field_1',20,50,get_sys_pay_pref_field('INS_Amt'));
text_row_ex(_("Default Value"),'text_field_n1',20,50,null,get_sys_pay_pref_field_n('INS_Amt'));
gl_all_accounts_list_row(_(""), 'INS_Amt', get_sys_pay_pref('INS_Amt'));




echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry_2',get_sys_pay_pref_gl_entry('F_E_D_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost_2',get_sys_pay_pref_cost_unit('F_E_D_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Show in Form"), 'header_enable2',get_sys_pay_pref_header('F_E_D_Amt'));
echo'</td>';


echo'<td>';
check_row(_("Calculate as per B.E"), 'as_per_be_enable2', get_sys_pay_pref_as_per_be('F_E_D_Amt'));
echo'</td>';

text_cells_ex(_("Type 3:"),'text_field_2',20,50,get_sys_pay_pref_field('F_E_D_Amt'));
text_row_ex(_("Default Value"),'text_field_n2',20,50,null,get_sys_pay_pref_field_n('F_E_D_Amt'));
gl_all_accounts_list_row(_(""), 'F_E_D_Amt', get_sys_pay_pref('F_E_D_Amt'));



echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry_3', get_sys_pay_pref_gl_entry('Duty_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost_3', get_sys_pay_pref_cost_unit('Duty_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Show in Form"), 'header_enable3', get_sys_pay_pref_header('Duty_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Calculate as per B.E"), 'as_per_be_enable3', get_sys_pay_pref_as_per_be('Duty_Amt'));
echo'</td>';

text_cells_ex(_("Type 4:"),'text_field_3',20,50,get_sys_pay_pref_field('Duty_Amt'));
text_row_ex(_("Default Value"),'text_field_n3',20,50,null,get_sys_pay_pref_field_n('Duty_Amt'));
gl_all_accounts_list_row(_(""), 'Duty_Amt', get_sys_pay_pref('Duty_Amt'));




echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry_4',get_sys_pay_pref_gl_entry('S_T_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost_4', get_sys_pay_pref_cost_unit('S_T_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Show in Form"), 'header_enable4', get_sys_pay_pref_header('S_T_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Calculate as per B.E"), 'as_per_be_enable4', get_sys_pay_pref_as_per_be('S_T_Amt'));
echo'</td>';

text_cells_ex(_("Type 5:"),'text_field_4',20,50,get_sys_pay_pref_field('S_T_Amt'));
text_row_ex(_("Default Value"),'text_field_n4',20,50,null,get_sys_pay_pref_field_n('S_T_Amt'));
gl_all_accounts_list_row(_(""), 'S_T_Amt', get_sys_pay_pref('S_T_Amt'));



echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry_5',get_sys_pay_pref_gl_entry('I_Tax_Amt'));
echo'</td>';
echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost_5', get_sys_pay_pref_cost_unit('I_Tax_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Show in Form"), 'header_enable5', get_sys_pay_pref_header('I_Tax_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Calculate as per B.E"), 'as_per_be_enable5', get_sys_pay_pref_as_per_be('I_Tax_Amt'));
echo'</td>';

text_cells_ex(_("Type 6:"),'text_field_5',20,50,get_sys_pay_pref_field('I_Tax_Amt'));
text_row_ex(_("Default Value"),'text_field_n5',20,50,null,get_sys_pay_pref_field_n('I_Tax_Amt'));
gl_all_accounts_list_row(_(""), 'I_Tax_Amt', get_sys_pay_pref('I_Tax_Amt'));




echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry_6', get_sys_pay_pref_gl_entry('Add_S_T_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost_6', get_sys_pay_pref_cost_unit('Add_S_T_Amt'));
echo'</td>';

echo'<td>';
check_row(_("Show in Form"), 'header_enable6', get_sys_pay_pref_header('Add_S_T_Amt'));
echo'</td>';


echo'<td>';
check_row(_("Calculate as per B.E"), 'as_per_be_enable6', get_sys_pay_pref_as_per_be('Add_S_T_Amt'));
echo'</td>';

text_cells_ex(_("Type 7:"),'text_field_6',20,50,get_sys_pay_pref_field('Add_S_T_Amt'));
text_row_ex(_("Default Value"),'text_field_n6',20,50,null,get_sys_pay_pref_field_n('Add_S_T_Amt'));
gl_all_accounts_list_row(_(""), 'Add_S_T_Amt', get_sys_pay_pref('Add_S_T_Amt'));




echo'<td>';
check_row(_("Don't Add in GL Entry"), 'gl_entry_7', get_sys_pay_pref_gl_entry('Other_Expense'));
echo'</td>';
echo'<td>';
check_row(_("Don't Add in Unit cost"), 'unit_cost_7', get_sys_pay_pref_cost_unit('Other_Expense'));
echo'</td>';

echo'<td>';
check_row(_("Show in Form"), 'header_enable7', get_sys_pay_pref_header('Other_Expense'));
echo'</td>';


echo'<td>';
check_row(_("Calculate as per B.E"), 'as_per_be_enable7', get_sys_pay_pref_as_per_be('Other_Expense'));
echo'</td>';

text_cells_ex(_("Type 8:"),'text_field_7', 20, 50, get_sys_pay_pref_field('Other_Expense'));
text_row_ex(_("Default Value"),'text_field_n7',20,50,null,get_sys_pay_pref_field_n('Other_Expense'));
gl_all_accounts_list_row(_(""), 'Other_Expense', get_sys_pay_pref('Other_Expense'));

end_table(1);

submit_center('submit', _("Update"), true, '', 'default');
end_form();

end_page();
?>