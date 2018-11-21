<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Items Setup - Pref "));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/access_levels.inc");
include_once($path_to_root . "/admin/db/company_db.inc");
//-------------------------------------------------------------------------------------------------

function get_duplication_record1($value)
{
    $sql = "SELECT COUNT(*) FROM `0_item_pref` WHERE `s_position` ='$value'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_duplication_record2($value)
{
    $sql = "SELECT COUNT(*) FROM `0_item_pref` WHERE `s_position` ='$value'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_duplication_record3($value)
{
    $sql = "SELECT COUNT(*) FROM `0_item_pref` WHERE `s_position` ='$value'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_duplication_record4($value)
{
    $sql = "SELECT COUNT(*) FROM `0_item_pref` WHERE `s_position` ='$value'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_duplication_record5($value)
{
    $sql = "SELECT COUNT(*) FROM `0_item_pref` WHERE `s_position` ='$value'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_duplication_record6($value)
{
    $sql = "SELECT COUNT(*) FROM `0_item_pref` WHERE `s_position` ='$value'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_duplication_record7($value)
{
    $sql = "SELECT COUNT(*) FROM `0_item_pref` WHERE `s_position` ='$value'";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
if (isset($_POST['update']) && $_POST['update'] != "")
{
    global $Ajax;

    {

                $pos1=get_duplication_record1(get_post('s_position1'));
              //  display_error($pos1<2);
                if($pos1<2)
                {
                    $sql = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value'))." ,
                                item_enable =".db_escape(get_post('item_enable1')).",
                                sale_enable =".db_escape(get_post('sale_enable1'))." ,
                                purchase_enable =".db_escape(get_post('purchase_enable1')).",
                                s_position =".db_escape(get_post('s_position1'))." ,
                                p_position =".db_escape(get_post('p_position1')).",
                                s_width =".db_escape(get_post('s_width1')).",
                                p_width =".db_escape(get_post('p_width1'))."
                                WHERE  name='amount1'";
                    db_query($sql,"The sales group could not be updated");
                }

                $pos2=get_duplication_record2(get_post('s_position2'));
                if($pos2<2)
                {
                    $sql1 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value2'))." ,
                                item_enable =".db_escape(get_post('item_enable2'))." ,
                                sale_enable =".db_escape(get_post('sale_enable2'))." ,
                                purchase_enable =".db_escape(get_post('purchase_enable2')).",
                                s_position =".db_escape(get_post('s_position2'))." ,
                                p_position =".db_escape(get_post('p_position2')).",
                                s_width =".db_escape(get_post('s_width2')).",
                                p_width =".db_escape(get_post('p_width2'))."
                               WHERE  name='amount2'";
                    db_query($sql1,"The sales group could not be updated");
                }


                $pos3 = get_duplication_record3(get_post('s_position3'));
                if($pos3 < 2) {

                    $sql2 = "UPDATE `0_item_pref` SET
                                label_value =" . db_escape(get_post('label_value3')) . " ,
                                item_enable =" . db_escape(get_post('item_enable3')) . ",
                                sale_enable =" . db_escape(get_post('sale_enable3')) . ",
                                purchase_enable =" . db_escape(get_post('purchase_enable3')) . ",
                                s_position =" . db_escape(get_post('s_position3')) . ",
                                p_position =" . db_escape(get_post('p_position3')) . ",
                                s_width =" . db_escape(get_post('s_width3')) . ",
                                p_width =" . db_escape(get_post('p_width3')) . "
                                WHERE  name='amount3'";
                    db_query($sql2, "The sales group could not be updated");
                }

                $pos4=get_duplication_record4(get_post('s_position4'));
                if($pos4<2) {
                    $sql3 = "UPDATE `0_item_pref` SET
                                label_value =" . db_escape(get_post('label_value4')) . " ,
                                item_enable =" . db_escape(get_post('item_enable4')) . ",
                                sale_enable =" . db_escape(get_post('sale_enable4')) . ",
                                purchase_enable =" . db_escape(get_post('purchase_enable4')) . ",
                                s_position =" . db_escape(get_post('s_position4')) . ",
                                p_position =" . db_escape(get_post('p_position4')) . ",
                                s_width =" . db_escape(get_post('s_width4')) . ",
                                p_width =" . db_escape(get_post('p_width4')) . "
                                WHERE  name='amount4'";
                    db_query($sql3, "The sales group could not be updated");
                }


                $pos5=get_duplication_record5(get_post('s_position5'));
                if($pos5<2) {
                    $sql4 = "UPDATE `0_item_pref` SET
                                label_value =" . db_escape(get_post('label_value5')) . ",
                                item_enable =" . db_escape(get_post('item_enable5')) . ",
                                sale_enable =" . db_escape(get_post('sale_enable5')) . ",
                                purchase_enable =" . db_escape(get_post('purchase_enable5')) . ",
                                s_position =" . db_escape(get_post('s_position5')) . ",
                                p_position =" . db_escape(get_post('p_position5')) . ",
                                s_width =" . db_escape(get_post('s_width5')) . ",
                                p_width =" . db_escape(get_post('p_width5')) . "
                                WHERE  name='amount5'";
                    db_query($sql4, "The sales group could not be updated");
                }


                $pos6=get_duplication_record6(get_post('s_position6'));
                if($pos6<2) {
                    $sql5 = "UPDATE `0_item_pref` SET
                                label_value =" . db_escape(get_post('label_value6')) . " ,
                                item_enable =" . db_escape(get_post('item_enable6')) . ",
                                sale_enable =" . db_escape(get_post('sale_enable6')) . ",
                                purchase_enable =" . db_escape(get_post('purchase_enable6')) . ",
                                s_position =" . db_escape(get_post('s_position6')) . ",
                                p_position =" . db_escape(get_post('p_position6')) . ",
                                s_width =" . db_escape(get_post('s_width6')) . ",
                                p_width =" . db_escape(get_post('p_width6')) . "
                                WHERE  name='amount6'";
                    db_query($sql5, "The sales group could not be updated");

                }


                $pos7=get_duplication_record7(get_post('s_position7'));
                if($pos7<2) {
                    $sql6 = "UPDATE `0_item_pref` SET
                                label_value =" . db_escape(get_post('label_value7')) . " ,
                                item_enable =" . db_escape(get_post('item_enable7')) . ",
                                sale_enable =" . db_escape(get_post('sale_enable7')) . ",
                                purchase_enable =" . db_escape(get_post('purchase_enable7')) . ",
                                s_position =" . db_escape(get_post('s_position7')) . ",
                                p_position =" . db_escape(get_post('p_position7')) . ",
                                s_width =" . db_escape(get_post('s_width7')) . ",
                                p_width =" . db_escape(get_post('p_width7')) . "
                                WHERE  name='text1'";
                    db_query($sql6, "The sales group could not be updated");

                }

                $sql7 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value8'))." ,
                                item_enable =".db_escape(get_post('item_enable8')).",
                                sale_enable =".db_escape(get_post('sale_enable8')).",
                                purchase_enable =".db_escape(get_post('purchase_enable8')).",
                                s_position =".db_escape(get_post('s_position8')).",
                                p_position =".db_escape(get_post('p_position8')).",
                                s_width =".db_escape(get_post('s_width8')).",
                                p_width =".db_escape(get_post('p_width8'))."
                                WHERE  name='text2'";
                db_query($sql7,"The sales group could not be updated");

                $sql8 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value9')).",
                                item_enable =".db_escape(get_post('item_enable9')).",
                                sale_enable =".db_escape(get_post('sale_enable9')).",
                                purchase_enable =".db_escape(get_post('purchase_enable9')).",
                                s_position =".db_escape(get_post('s_position9')).",
                                p_position =".db_escape(get_post('p_position9')).",
                                s_width =".db_escape(get_post('s_width9')).",
                                p_width =".db_escape(get_post('p_width9'))."
                                WHERE  name='text3'";
                db_query($sql8,"The sales group could not be updated");

                $sql9 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value10')).",
                                item_enable =".db_escape(get_post('item_enable10')).",
                                sale_enable =".db_escape(get_post('sale_enable10')).",
                                purchase_enable =".db_escape(get_post('purchase_enable10')).",
                                s_position =".db_escape(get_post('s_position10')).",
                                p_position =".db_escape(get_post('p_position10')).",
                                s_width =".db_escape(get_post('s_width10')).",
                                p_width =".db_escape(get_post('p_width10'))."
                                WHERE  name='text4'";

                db_query($sql9,"The sales group could not be updated");
                $sql10 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value110')).",
                                item_enable =".db_escape(get_post('item_enable110')).",
                                sale_enable =".db_escape(get_post('sale_enable110')).",
                                purchase_enable =".db_escape(get_post('purchase_enable110')).",
                                s_position =".db_escape(get_post('s_position110')).",
                                p_position =".db_escape(get_post('p_position110')).",
                                s_width =".db_escape(get_post('s_width110')).",
                                p_width =".db_escape(get_post('p_width110'))."
                                WHERE  name='text5'";
                db_query($sql10,"The sales group could not be updated");

                $sql11 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value12')).",
                                item_enable =".db_escape(get_post('item_enable12')).",
                                sale_enable =".db_escape(get_post('sale_enable12')).",
                                purchase_enable =".db_escape(get_post('purchase_enable12')).",
                                s_position =".db_escape(get_post('s_position12')).",
                                p_position =".db_escape(get_post('p_position12')).",
                                s_width =".db_escape(get_post('s_width12')).",
                                p_width =".db_escape(get_post('p_width12'))."
                                WHERE  name='text6'";
                db_query($sql11,"The sales group could not be updated");

                $sql12 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value34')).",
                                item_enable =".db_escape(get_post('item_enable34')).",
                                sale_enable =".db_escape(get_post('sale_enable34')).",
                                purchase_enable =".db_escape(get_post('purchase_enable34')).",
                                s_position =".db_escape(get_post('s_position34')).",
                                p_position =".db_escape(get_post('p_position34')).",
                                s_width =".db_escape(get_post('s_width34')).",
                                p_width =".db_escape(get_post('p_width34'))."
                                WHERE  name='combo4'";
                db_query($sql12,"The sales group could not be updated");

                $sql13 = "UPDATE `0_item_pref` SET
                                label_value = ".db_escape(get_post('label_value13')).",
                                item_enable = ".db_escape(get_post('item_enable13')).",
                                sale_enable = ".db_escape(get_post('sale_enable13')).",
                                purchase_enable = ".db_escape(get_post('purchase_enable13')).",
                                s_position =".db_escape(get_post('s_position13')).",
                                p_position =".db_escape(get_post('p_position13')).",
                                s_width =".db_escape(get_post('s_width13')).",
                                p_width =".db_escape(get_post('p_width13'))."
                                WHERE  name='combo1'";
                db_query($sql13,"The sales group could not be updated");

                $sql14 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value14')).",
                                item_enable =".db_escape(get_post('item_enable14')).",
                                sale_enable =".db_escape(get_post('sale_enable14')).",
                                purchase_enable =".db_escape(get_post('purchase_enable14')).",
                                s_position =".db_escape(get_post('s_position14')).",
                                p_position =".db_escape(get_post('p_position14')).",
                                s_width =".db_escape(get_post('s_width14')).",
                                p_width =".db_escape(get_post('p_width14'))."
                                WHERE  name='combo2'";
                db_query($sql14,"The sales group could not be updated");

//        display_error("asdasdsad");
                $sql15 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value15')).",
                                item_enable =".db_escape(get_post('item_enable15')).",
                                sale_enable =".db_escape(get_post('sale_enable15')).",
                                purchase_enable =".db_escape(get_post('purchase_enable15')).",
                                s_position =".db_escape(get_post('s_position15')).",
                                p_position =".db_escape(get_post('p_position15')).",
                                s_width =".db_escape(get_post('s_width15')).",
                                p_width =".db_escape(get_post('p_width15'))."
                                WHERE  name='combo3'";
                db_query($sql15, "The sales group could not be updated");

                $sql16 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value35')).",
                                item_enable =".db_escape(get_post('item_enable35')).",
                                sale_enable =".db_escape(get_post('sale_enable35')).",
                                purchase_enable =".db_escape(get_post('purchase_enable35')).",
                                s_position =".db_escape(get_post('s_position35')).",
                                p_position =".db_escape(get_post('p_position35')).",
                                s_width =".db_escape(get_post('s_width35')).",
                                p_width =".db_escape(get_post('p_width35'))."
                                WHERE  name='combo5'";
                db_query($sql16,"The sales group could not be updated");
        //
        // display_error(get_post('item_enable36')."++".$_POST['item_enable36']);
                $sql17 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value36')).",
                                item_enable =".db_escape(get_post('item_enable36')).",
                                sale_enable =".db_escape(get_post('sale_enable36')).",
                                purchase_enable =".db_escape(get_post('purchase_enable36')).",
                                s_position =".db_escape(get_post('s_position36')).",
                                p_position =".db_escape(get_post('p_position36')).",
                                s_width =".db_escape(get_post('s_width36')).",
                                p_width =".db_escape(get_post('p_width36'))."
                                WHERE  name='combo6'";
                db_query($sql17,"The sales group could not be updated");

                $sql18 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value102')).",
                                item_enable =".db_escape(get_post('item_enable102')).",
                                sale_enable =".db_escape(get_post('sale_enable102')).",
                                purchase_enable =".db_escape(get_post('purchase_enable102')).",
                                s_position =".db_escape(get_post('s_position102')).",
                                p_position =".db_escape(get_post('p_position102')).",
                                s_width =".db_escape(get_post('s_width102')).",
                                p_width =".db_escape(get_post('p_width102'))."
                                WHERE  name='date3'";
                db_query($sql18,"The sales group could not be updated");

                $sql19 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value101')).",
                                item_enable =".db_escape(get_post('item_enable101')).",
                                sale_enable =".db_escape(get_post('sale_enable101')).",
                                purchase_enable =".db_escape(get_post('purchase_enable101')).",
                                s_position =".db_escape(get_post('s_position101')).",
                                p_position =".db_escape(get_post('p_position101')).",
                                s_width =".db_escape(get_post('s_width101')).",
                                p_width =".db_escape(get_post('p_width101'))."
                                WHERE  name='date2'";
                db_query($sql19,"The sales group could not be updated");

                $sql20 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value100')).",
                                item_enable =".db_escape(get_post('item_enable100')).",
                                sale_enable =".db_escape(get_post('sale_enable100')).",
                                purchase_enable =".db_escape(get_post('purchase_enable100')).",
                                s_position =".db_escape(get_post('s_position100')).",
                                p_position =".db_escape(get_post('p_position100')).",
                                s_width =".db_escape(get_post('s_width100')).",
                                p_width =".db_escape(get_post('p_width100'))."
                                WHERE  name='date1'";
                db_query($sql20,"The sales group could not be updated");


                $sql21 = "UPDATE `0_item_pref` SET
                                label_value =".db_escape(get_post('label_value666')).",
                                item_enable =".db_escape(get_post('item_enable666')).",
                                sale_enable =".db_escape(get_post('sale_enable666')).",
                                purchase_enable =".db_escape(get_post('purchase_enable666'))."
                                WHERE  name='total_date'";
                db_query($sql21,"The sales group could not be updated");



        $sql212 = "UPDATE `0_item_pref` SET
						item_enable =".db_escape(get_post('item_enable300')).",
						sale_enable =".db_escape(get_post('sale_enable300')).",
						purchase_enable =".db_escape(get_post('purchase_enable300'))."
						WHERE  name='sales_persons'";
        db_query($sql212,"The sales group could not be updated");


        $sql213 = "UPDATE `0_item_pref` SET
						item_enable =".db_escape(get_post('item_enable301')).",
						sale_enable =".db_escape(get_post('sale_enable301')).",
						purchase_enable =".db_escape(get_post('purchase_enable301'))."
						WHERE  name='con_factor'";
        db_query($sql213,"The sales group could not be updated");


        $sql2133 = "UPDATE `0_item_pref` SET
						item_enable =".db_escape(get_post('item_enable3011')).",
						sale_enable =".db_escape(get_post('sale_enable3011')).",
						purchase_enable =".db_escape(get_post('purchase_enable3011'))."
						WHERE  name='formula'";
        db_query($sql2133,"The sales group could not be updated");




//		update_purch_pref(
//			get_post( array('cart1','grn_enable','po_enable','label_value'
//
//
//
//				)
//			)
//
//		);

//		$_SESSION['wa_current_user']->timeout = $_POST['login_tout'];
        display_notification_centered(_("Items setup has been updated."));
    }
    set_focus('label_value');
    $Ajax->activate('_page_body');
// 	meta_forward($_SERVER['PHP_SELF']);

} /* end of if submit */
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
                padding:20px;
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

        <td><a class="hvr-float-shadow" href="hf_pref.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> Header/Footer PREF</a></td>

        <td><a class="hvr-float-shadow" href="item_pref.php"><i class="fa fa-barcode" style="margin-right: 5px; font-size: large;"></i> ITEM PREF</a></td>
        <td><a class="hvr-float-shadow" href="company_preferences_new.php"><i class="fa fa-circle-o" style="font-size: large; margin-right: 5px;"></i> FORM DISPLAY</a></td>
        <td><a class="hvr-float-shadow" href="print_from_setup.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT DISPLAY</a></td>

        <td><a class="hvr-float-shadow" href="import_gl_setup.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> IMPORT GL</a></td>
        <td><a class="hvr-float-shadow" href="cashflow_gl.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> CASH FLOW</a></td>
        <td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> WHT GL</a></td>


    </center>



    </body>
    </html>


<?php
start_form(true);
div_start('_page_body');
$myrow1 = get_company_item_pref('amount1');
//display_error($myrow1['label_value']);
$_POST['label_value'] = $myrow1["label_value"];
$_POST['item_enable1'] = $myrow1["item_enable"];
$_POST['sale_enable1'] = $myrow1["sale_enable"];
$_POST['purchase_enable1'] = $myrow1["purchase_enable"];
$_POST['s_position1'] = $myrow1["s_position"];
$_POST['p_position1'] = $myrow1["p_position"];
$_POST['s_width1'] = $myrow1["s_width"];
$_POST['p_width1'] = $myrow1["p_width"];

$myrow2 = get_company_item_pref('amount2');
$_POST['label_value2'] = $myrow2["label_value"];
$_POST['item_enable2'] = $myrow2["item_enable"];
$_POST['sale_enable2'] = $myrow2["sale_enable"];
$_POST['purchase_enable2'] = $myrow2["purchase_enable"];
$_POST['s_position2'] = $myrow2["s_position"];
$_POST['p_position2'] = $myrow2["p_position"];

$_POST['s_width2'] = $myrow2["s_width"];
$_POST['p_width2'] = $myrow2["p_width"];

$myrow3 = get_company_item_pref('amount3');
$_POST['label_value3'] = $myrow3["label_value"];
$_POST['item_enable3'] = $myrow3["item_enable"];
$_POST['sale_enable3'] = $myrow3["sale_enable"];
$_POST['purchase_enable3'] = $myrow3["purchase_enable"];
$_POST['s_position3'] = $myrow3["s_position"];
$_POST['p_position3'] = $myrow3["p_position"];

$_POST['s_width3'] = $myrow3["s_width"];
$_POST['p_width3'] = $myrow3["p_width"];

$myrow4 = get_company_item_pref('amount4');
$_POST['label_value4'] = $myrow4["label_value"];
$_POST['item_enable4'] = $myrow4["item_enable"];
$_POST['sale_enable4'] = $myrow4["sale_enable"];
$_POST['purchase_enable4'] = $myrow4["purchase_enable"];
$_POST['s_position4'] = $myrow4["s_position"];
$_POST['p_position4'] = $myrow4["p_position"];

$_POST['s_width4'] = $myrow4["s_width"];
$_POST['p_width4'] = $myrow4["p_width"];

$myrow5 = get_company_item_pref('amount5');
$_POST['label_value5'] = $myrow5["label_value"];
$_POST['item_enable5'] = $myrow5["item_enable"];
$_POST['sale_enable5'] = $myrow5["sale_enable"];
$_POST['purchase_enable5'] = $myrow5["purchase_enable"];
$_POST['s_position5'] = $myrow5["s_position"];
$_POST['p_position5'] = $myrow5["p_position"];

$_POST['s_width5'] = $myrow5["s_width"];
$_POST['p_width5'] = $myrow5["p_width"];

$myrow6 = get_company_item_pref('amount6');
$_POST['label_value6'] = $myrow6["label_value"];
$_POST['item_enable6'] = $myrow6["item_enable"];
$_POST['sale_enable6'] = $myrow6["sale_enable"];
$_POST['purchase_enable6'] = $myrow6["purchase_enable"];
$_POST['s_position6'] = $myrow6["s_position"];
$_POST['p_position6'] = $myrow6["p_position"];


$_POST['s_width6'] = $myrow6["s_width"];
$_POST['p_width6'] = $myrow6["p_width"];

//////////////////////for footer

$myrow7 = get_company_item_pref('text1');
$_POST['label_value7'] = $myrow7["label_value"];
$_POST['item_enable7'] = $myrow7["item_enable"];
$_POST['sale_enable7'] = $myrow7["sale_enable"];
$_POST['purchase_enable7'] = $myrow7["purchase_enable"];
$_POST['s_position7'] = $myrow7["s_position"];
$_POST['p_position7'] = $myrow7["p_position"];

$_POST['s_width7'] = $myrow7["s_width"];
$_POST['p_width7'] = $myrow7["p_width"];
$myrow8 = get_company_item_pref('text2');
$_POST['label_value8'] = $myrow8["label_value"];
$_POST['item_enable8'] = $myrow8["item_enable"];
$_POST['sale_enable8'] = $myrow8["sale_enable"];
$_POST['purchase_enable8'] = $myrow8["purchase_enable"];
$_POST['s_position8'] = $myrow8["s_position"];
$_POST['p_position8'] = $myrow8["p_position"];

$_POST['s_width8'] = $myrow8["s_width"];
$_POST['p_width8'] = $myrow8["p_width"];
$myrow9 = get_company_item_pref('text3');
$_POST['label_value9'] = $myrow9["label_value"];
$_POST['item_enable9'] = $myrow9["item_enable"];
$_POST['sale_enable9'] = $myrow9["sale_enable"];
$_POST['purchase_enable9'] = $myrow9["purchase_enable"];
$_POST['s_position9'] = $myrow9["s_position"];
$_POST['p_position9'] = $myrow9["p_position"];

$_POST['s_width9'] = $myrow9["s_width"];
$_POST['p_width9'] = $myrow9["p_width"];
$myrow10 = get_company_item_pref('text4');
$_POST['label_value10'] = $myrow10["label_value"];
$_POST['item_enable10'] = $myrow10["item_enable"];
$_POST['sale_enable10'] = $myrow10["sale_enable"];
$_POST['purchase_enable10'] = $myrow10["purchase_enable"];
$_POST['s_position10'] = $myrow10["s_position"];
$_POST['p_position10'] = $myrow10["p_position"];


$_POST['s_width10'] = $myrow10["s_width"];
$_POST['p_width10'] = $myrow10["p_width"];
$myrow11 = get_company_item_pref('text5');
$_POST['label_value110'] = $myrow11["label_value"];
$_POST['item_enable110'] = $myrow11["item_enable"];
$_POST['sale_enable110'] = $myrow11["sale_enable"];
$_POST['purchase_enable110'] = $myrow11["purchase_enable"];
$_POST['s_position110'] = $myrow11["s_position"];
$_POST['p_position110'] = $myrow11["p_position"];


$_POST['s_width110'] = $myrow11["s_width"];
$_POST['p_width110'] = $myrow11["p_width"];

$myrow12 = get_company_item_pref('text6');
$_POST['label_value12'] = $myrow12["label_value"];
$_POST['item_enable12'] = $myrow12["item_enable"];
$_POST['sale_enable12'] = $myrow12["sale_enable"];
$_POST['purchase_enable12'] = $myrow12["purchase_enable"];
$_POST['s_position12'] = $myrow12["s_position"];
$_POST['p_position12'] = $myrow12["p_position"];

$_POST['s_width12'] = $myrow12["s_width"];
$_POST['p_width12'] = $myrow12["p_width"];
$myrow33 = get_company_item_pref('combo1');
$_POST['label_value13'] = $myrow33["label_value"];
$_POST['item_enable13'] = $myrow33["item_enable"];
$_POST['sale_enable13'] = $myrow33["sale_enable"];
$_POST['purchase_enable13'] = $myrow33["purchase_enable"];
$_POST['s_position13'] = $myrow33["s_position"];
$_POST['p_position13'] = $myrow33["p_position"];

$_POST['s_width13'] = $myrow33["s_width"];
$_POST['p_width13'] = $myrow33["p_width"];
$myrow44 = get_company_item_pref('combo2');
$_POST['label_value14'] = $myrow44["label_value"];
$_POST['item_enable14'] = $myrow44["item_enable"];
$_POST['sale_enable14'] = $myrow44["sale_enable"];
$_POST['purchase_enable14'] = $myrow44["purchase_enable"];
$_POST['s_position14'] = $myrow44["s_position"];
$_POST['p_position14'] = $myrow44["p_position"];

$_POST['s_width14'] = $myrow44["s_width"];
$_POST['p_width14'] = $myrow44["p_width"];
$myrow55 = get_company_item_pref('combo3');
$_POST['label_value15'] = $myrow55["label_value"];
$_POST['item_enable15'] = $myrow55["item_enable"];
$_POST['sale_enable15'] = $myrow55["sale_enable"];
$_POST['purchase_enable15'] = $myrow55["purchase_enable"];

$_POST['s_width15'] = $myrow55["s_width"];
$_POST['p_width15'] = $myrow55["p_width"];
$myrow13 = get_company_item_pref('combo4');
$_POST['label_value34'] = $myrow13["label_value"];
$_POST['item_enable34'] = $myrow13["item_enable"];
$_POST['sale_enable34'] = $myrow13["sale_enable"];
$_POST['purchase_enable34'] = $myrow13["purchase_enable"];
$_POST['s_position34'] = $myrow13["s_position"];
$_POST['p_position34'] = $myrow13["p_position"];

$_POST['s_width34'] = $myrow13["s_width"];
$_POST['p_width34'] = $myrow13["p_width"];
$myrow35 = get_company_item_pref('combo5');
$_POST['label_value35'] = $myrow35["label_value"];
$_POST['item_enable35'] = $myrow35["item_enable"];
$_POST['sale_enable35'] = $myrow35["sale_enable"];
$_POST['purchase_enable35'] = $myrow35["purchase_enable"];
$_POST['s_position35'] = $myrow35["s_position"];
$_POST['p_position35'] = $myrow35["p_position"];

$_POST['s_width35'] = $myrow35["s_width"];
$_POST['p_width35'] = $myrow35["p_width"];
$myrow36 = get_company_item_pref('combo6');
$_POST['label_value36'] = $myrow36["label_value"];
$_POST['item_enable36'] = $myrow36["item_enable"];
$_POST['sale_enable36'] = $myrow36["sale_enable"];
$_POST['purchase_enable36'] = $myrow36["purchase_enable"];
$_POST['s_position36'] = $myrow36["s_position"];
$_POST['p_position36'] = $myrow36["p_position"];


$_POST['s_width36'] = $myrow36["s_width"];
$_POST['p_width36'] = $myrow36["p_width"];
$myrow666 = get_company_sales_pref('total_date');
$_POST['label_value666'] = $myrow666["label_value"];


$myrow14 = get_company_item_pref('date1');
$_POST['label_value100'] = $myrow14["label_value"];
$_POST['item_enable100'] = $myrow14["item_enable"];
$_POST['sale_enable100'] = $myrow14["sale_enable"];
$_POST['purchase_enable100'] = $myrow14["purchase_enable"];
$_POST['s_position100'] = $myrow14["s_position"];
$_POST['p_position100'] = $myrow14["p_position"];

$_POST['s_width100'] = $myrow14["s_width"];
$_POST['p_width100'] = $myrow14["p_width"];
$myrow15 = get_company_item_pref('date2');
$_POST['label_value101'] = $myrow15["label_value"];
$_POST['item_enable101'] = $myrow15["item_enable"];
$_POST['sale_enable101'] = $myrow15["sale_enable"];
$_POST['purchase_enable101'] = $myrow15["purchase_enable"];
$_POST['s_position101'] = $myrow15["s_position"];
$_POST['p_position101'] = $myrow15["p_position"];

$_POST['s_width101'] = $myrow15["s_width"];
$_POST['p_width101'] = $myrow15["p_width"];
$myrow16 = get_company_item_pref('date3');
$_POST['label_value102'] = $myrow16["label_value"];
$_POST['item_enable102'] = $myrow16["item_enable"];
$_POST['sale_enable102'] = $myrow16["sale_enable"];
$_POST['purchase_enable102'] = $myrow16["purchase_enable"];
$_POST['s_position102'] = $myrow16["s_position"];
$_POST['p_position102'] = $myrow16["p_position"];

$_POST['s_width102'] = $myrow16["s_width"];
$_POST['p_width102'] = $myrow16["p_width"];

$myrow300 = get_company_item_pref('sales_persons');
$_POST['item_enable300'] = $myrow300["item_enable"];
$_POST['sale_enable300'] = $myrow300["sale_enable"];
$_POST['purchase_enable300'] = $myrow300["purchase_enable"];

$myrow301 = get_company_item_pref('con_factor');
$_POST['item_enable301'] = $myrow301["item_enable"];
$_POST['sale_enable301'] = $myrow301["sale_enable"];
$_POST['purchase_enable301'] = $myrow301["purchase_enable"];


$myrow3011 = get_company_item_pref('formula');
$_POST['item_enable3011'] = $myrow3011["item_enable"];
$_POST['sale_enable3011'] = $myrow3011["sale_enable"];
$_POST['purchase_enable3011'] = $myrow3011["purchase_enable"];


start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Customs Item Numeric Fields"));

echo
"<tr><td colspan='' class='tableheader'> &nbsp;  &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Item Enable &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Sale Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Purchase Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp;   &nbsp;</td>
		</tr>
		";

echo
"<tr><td colspan='' class='tableheader'> &nbsp; Label &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp;Sales Position &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Purchase Position &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Sales Width &nbsp;</td>
	<td colspan='' class='tableheader'>&nbsp; Purchase Width&nbsp;</td>
		</tr>
		";



echo"<tr>";

check_cells(_("Formula"), 'item_enable3011', $_POST['item_enable3011']);
check_cells(_(""), 'sale_enable3011', $_POST['sale_enable3011']);
check_cells(_(""), 'purchase_enable3011', $_POST['purchase_enable3011']);
echo"<tr>";



echo"<tr>";

check_cells(_("Conversion Factor"), 'item_enable301', $_POST['item_enable301']);
check_cells(_(""), 'sale_enable301', $_POST['sale_enable301']);
check_cells(_(""), 'purchase_enable301', $_POST['purchase_enable301']);
echo"<tr>";

check_cells(_("Amount 1"), 'item_enable1', $_POST['item_enable1']);
check_cells(_(""), 'sale_enable1', $_POST['sale_enable1']);
check_cells(_(""), 'purchase_enable1', $_POST['purchase_enable1']);
echo"<tr>";
text_cells(_(""), 'label_value', $_POST['label_value'], 20);
text_cells5(_(""), 's_position1', $_POST['s_position1'], 10);
text_cells4(_(""), 'p_position1', $_POST['p_position1'], 10);
text_cells2(_(""), 's_width1', $_POST['s_width1'], 10);
text_cells3(_(""), 'p_width1', $_POST['p_width1'], 10);
echo"</tr>";

check_cells(_("Amount 2"),'item_enable2', null);
check_cells(_(""),'sale_enable2', null);
check_cells(_(""),'purchase_enable2', null);
echo"<tr>";

text_cells(_(""), 'label_value2', $_POST['label_value2'], 20);
text_cells5(_(""), 's_position2', $_POST['s_position2'], 10);
text_cells4(_(""), 'p_position2', $_POST['p_position2'], 10);
text_cells2(_(""), 's_width2', $_POST['s_width2'], 10);
text_cells3(_(""), 'p_width2', $_POST['p_width2'], 10);
echo"</tr>";



check_cells(_("Amount 3"), 'item_enable3', null);
check_cells(_(""),'sale_enable3', null);
check_cells(_(""),'purchase_enable3', null);
echo"<tr>";
text_cells(_(""), 'label_value3', $_POST['label_value3'], 20);
text_cells5(_(""), 's_position3', $_POST['s_position3'], 10);
text_cells4(_(""), 'p_position3', $_POST['p_position3'], 10);
text_cells2(_(""), 's_width3', $_POST['s_width3'], 10);
text_cells3(_(""), 'p_width3', $_POST['p_width3'], 10);
echo"</tr>";


check_cells(_("Amount 4"), 'item_enable4', null);
check_cells(_(""),'sale_enable4', null);
check_cells(_(""),'purchase_enable4', null);
echo"<tr>";
text_cells(_(""), 'label_value4', $_POST['label_value4'], 20);
text_cells5(_(""), 's_position4', $_POST['s_position4'], 10);
text_cells4(_(""), 'p_position4', $_POST['p_position4'], 10);
text_cells2(_(""), 's_width4', $_POST['s_width4'], 10);
text_cells3(_(""), 'p_width4', $_POST['p_width4'], 10);
echo"</tr>";



check_cells(_("Amount 5"), 'item_enable5', null);
check_cells(_(""),'sale_enable5', null);
check_cells(_(""),'purchase_enable5', null);
echo"<tr>";
text_cells(_(""), 'label_value5', $_POST['label_value5'], 20);
text_cells5(_(""), 's_position5', $_POST['s_position5'], 10);
text_cells4(_(""), 'p_position5', $_POST['p_position5'], 10);
text_cells2(_(""), 's_width5', $_POST['s_width5'], 10);
text_cells3(_(""), 'p_width5', $_POST['p_width5'], 10);
echo"</tr>";


check_cells(_("Amount 6"), 'item_enable6', null);
check_cells(_(""),'sale_enable6', null);
check_cells(_(""),'purchase_enable6', null);
echo"<tr>";
text_cells(_(""), 'label_value6', $_POST['label_value6'], 20);
text_cells5(_(""), 's_position6', $_POST['s_position6'], 10);
text_cells4(_(""), 'p_position6', $_POST['p_position6'], 10);
text_cells2(_(""), 's_width6', $_POST['s_width6'], 10);
text_cells3(_(""), 'p_width6', $_POST['p_width6'], 10);
echo"</tr>";



table_section_title(_("Custom Item Text Fields"));
echo
"<tr><td colspan='' class='tableheader'> &nbsp;  &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Item Enable &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Sale Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Purchase Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp;   &nbsp;</td>
		</tr>
		";

echo
"<tr><td colspan='' class='tableheader'> &nbsp; Label &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp;Sales Position &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Purchase Position &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Sales Width &nbsp;</td>
	<td colspan='' class='tableheader'>&nbsp; Purchase Width&nbsp;</td>
		</tr>
		";


echo"<tr>";
check_cells(_("Text Field 1"), 'item_enable7', null);
check_cells(_(""),'sale_enable7', null);
check_cells(_(""),'purchase_enable7', null);
echo"<tr>";
text_cells(_(""), 'label_value7', $_POST['label_value7'], 20);
text_cells5(_(""), 's_position7', $_POST['s_position7'], 10);
text_cells4(_(""), 'p_position7', $_POST['p_position7'], 10);
text_cells2(_(""), 's_width7', $_POST['s_width7'], 10);
text_cells3(_(""), 'p_width7', $_POST['p_width7'], 10);
echo"</tr>";



check_cells(_("Text Field 2"), 'item_enable8', null);
check_cells(_(""),'sale_enable8', null);
check_cells(_(""),'purchase_enable8', null);
echo"<tr>";
text_cells(_(""), 'label_value8', $_POST['label_value8'], 20);
text_cells5(_(""), 's_position8', $_POST['s_position8'], 10);
text_cells4(_(""), 'p_position8', $_POST['p_position8'], 10);
text_cells2(_(""), 's_width8', $_POST['s_width8'], 10);
text_cells3(_(""), 'p_width8', $_POST['p_width8'], 10);
echo"</tr>";

check_cells(_("Text Field 3"), 'item_enable9', null);
check_cells(_(""),'sale_enable9', null);
check_cells(_(""),'purchase_enable9', null);
echo"<tr>";
text_cells(_(""), 'label_value9', $_POST['label_value9'], 20);
text_cells5(_(""), 's_position9', $_POST['s_position9'], 10);
text_cells4(_(""), 'p_position9', $_POST['p_position9'], 10);
text_cells2(_(""), 's_width9', $_POST['s_width9'], 10);
text_cells3(_(""), 'p_width9', $_POST['p_width9'], 10);
echo"</tr>";


check_cells(_("Text Field 4"), 'item_enable10', null);
check_cells(_(""),'sale_enable10', null);
check_cells(_(""),'purchase_enable10', null);
echo"<tr>";
text_cells(_(""), 'label_value10', $_POST['label_value10'], 20);
text_cells5(_(""), 's_position10', $_POST['s_position10'], 10);
text_cells4(_(""), 'p_position10', $_POST['p_position10'], 10);
text_cells2(_(""), 's_width10', $_POST['s_width10'], 10);
text_cells3(_(""), 'p_width10', $_POST['p_width10'], 10);
echo"</tr>";

check_cells(_("Text Field 5"), 'item_enable110', null);
check_cells(_(""),'sale_enable110', null);
check_cells(_(""),'purchase_enable110', null);
echo"<tr>";
text_cells(_(""), 'label_value110', $_POST['label_value110'], 20);
text_cells5(_(""), 's_position110', $_POST['s_position110'], 10);
text_cells4(_(""), 'p_position110', $_POST['p_position110'], 10);
text_cells2(_(""), 's_width110', $_POST['s_width110'], 10);
text_cells3(_(""), 'p_width110', $_POST['p_width110'], 10);
echo"</tr>";


check_cells(_("Text Field 6"), 'item_enable12', null);
check_cells(_(""),'sale_enable12', null);
check_cells(_(""),'purchase_enable12', null);
echo"<tr>";
text_cells(_(""), 'label_value12', $_POST['label_value12'], 20);
text_cells5(_(""), 's_position12', $_POST['s_position12'], 10);
text_cells4(_(""), 'p_position12', $_POST['p_position12'], 10);
text_cells2(_(""), 's_width12', $_POST['s_width12'], 10);
text_cells3(_(""), 'p_width12', $_POST['p_width12'], 10);
echo"</tr>";

table_section_title(_("Custom Item Date Fields"));
echo
"<tr><td colspan='' class='tableheader'> &nbsp;  &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Item Enable &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Sale Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Purchase Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp;   &nbsp;</td>
		</tr>
		";

echo
"<tr><td colspan='' class='tableheader'> &nbsp; Label &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp;Sales Position &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Purchase Position &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Sales Width &nbsp;</td>
	<td colspan='' class='tableheader'>&nbsp; Purchase Width&nbsp;</td>
		</tr>
		";


//text_cells(_(""), 'label_value666', $_POST['label_value666'], 10);

echo"<tr>";
check_cells(_("Show Date Box 1"), 'item_enable100', null);
check_cells(_(""),'sale_enable100', null);
check_cells(_(""),'purchase_enable100', null);
echo"<tr>";
text_cells(_(""), 'label_value100', $_POST['label_value100'], 20);
text_cells5(_(""), 's_position100', $_POST['s_position100'], 10);
text_cells4(_(""), 'p_position100', $_POST['p_position100'], 10);
text_cells2(_(""), 's_width100', $_POST['s_width100'], 10);
text_cells3(_(""), 'p_width100', $_POST['p_width100'], 10);
echo"</tr>";

check_cells(_("Show Date Box 2"), 'item_enable101', null);
check_cells(_(""),'sale_enable101', null);
check_cells(_(""),'purchase_enable101', null);
echo"<tr>";
text_cells(_(""), 'label_value101', $_POST['label_value101'], 20);
text_cells5(_(""), 's_position101', $_POST['s_position101'], 10);
text_cells4(_(""), 'p_position101', $_POST['p_position101'], 10);
text_cells2(_(""), 's_width101', $_POST['s_width101'], 10);
text_cells3(_(""), 'p_width101', $_POST['p_width101'], 10);
echo"</tr>";

check_cells(_("Show Date Box 3"), 'item_enable102', null);
check_cells(_(""),'sale_enable102', null);
check_cells(_(""),'purchase_enable102', null);
echo"<tr>";

text_cells(_(""), 'label_value102', $_POST['label_value102'], 20);
text_cells5(_(""), 's_position102', $_POST['s_position102'], 10);
text_cells4(_(""), 'p_position102', $_POST['p_position102'], 10);
text_cells2(_(""), 's_width102', $_POST['s_width102'], 10);
text_cells3(_(""), 'p_width102', $_POST['p_width102'], 10);
echo"</tr>";

table_section_title(_("Custom Item Combo Boxes"));
echo
"<tr><td colspan='' class='tableheader'> &nbsp;  &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Item Enable &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Sale Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Purchase Enable &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp;   &nbsp;</td>
		</tr>
		";

echo
"<tr><td colspan='' class='tableheader'> &nbsp; Label &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp;Sales Position &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Purchase Position &nbsp;</td>
			<td colspan='' class='tableheader'>&nbsp; Sales Width &nbsp;</td>
	<td colspan='' class='tableheader'>&nbsp; Purchase Width&nbsp;</td>
		</tr>
		";

check_cells(_("Sales Person"), 'item_enable300', $_POST['item_enable300']);
check_cells(_(""), 'sale_enable300', $_POST['sale_enable300']);
check_cells(_(""), 'purchase_enable300', $_POST['purchase_enable300']);
//echo"<tr>";

//text_cells(_(""), 'label_value21', $_POST['label_value21'], 10);
echo"<tr>";

//label_cell(_("Combo 1"));
echo '<td>';
echo'<h4>Combo 1</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_1.php?", _("Combo 1 Setup"));
//echo '<td>';
check_cells(_(""), 'item_enable13', null);
check_cells(_(""),'sale_enable13', null);
check_cells(_(""),'purchase_enable13', null);
echo"<tr>";

text_cells(_(""), 'label_value13', $_POST['label_value13'], 20);
text_cells5(_(""), 's_position13', $_POST['s_position13'], 10);
text_cells4(_(""), 'p_position13', $_POST['p_position13'], 10);
text_cells2(_(""), 's_width13', $_POST['s_width13'], 10);
text_cells3(_(""), 'p_width13', $_POST['p_width13'], 10);
echo"</tr>";


echo '<td>';
echo'<h4>Combo 2</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_2.php?", _("Combo 2 Setup"));
//echo '<td>';
check_cells(_(""), 'item_enable14', null);
check_cells(_(""),'sale_enable14', null);
check_cells(_(""),'purchase_enable14', null);
echo"<tr>";
text_cells(_(""), 'label_value14', $_POST['label_value14'], 20);
text_cells5(_(""), 's_position14', $_POST['s_position14'], 10);
text_cells4(_(""), 'p_position14', $_POST['p_position14'], 10);
text_cells2(_(""), 's_width14', $_POST['s_width14'], 10);
text_cells3(_(""), 'p_width14', $_POST['p_width14'], 10);
echo"</tr>";



echo '<td>';
echo'<h4>Combo 3</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_3.php?", _("Combo 3 Setup"));
//echo '<td>';
check_cells(_(""), 'item_enable15', null);
check_cells(_(""),'sale_enable15', null);
check_cells(_(""),'purchase_enable15', null);
echo"<tr>";
text_cells(_(""), 'label_value15', $_POST['label_value15'], 20);
text_cells5(_(""), 's_position15', $_POST['s_position15'], 10);
text_cells4(_(""), 'p_position15', $_POST['p_position15'], 10);
text_cells2(_(""), 's_width15', $_POST['s_width15'], 10);
text_cells3(_(""), 'p_width15', $_POST['p_width15'], 10);
echo"</tr>";

echo '<td>';
echo'<h4>Combo 4</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_4.php?", _("Combo 4 Setup"));
//echo '<td>';
check_cells(_(""), 'item_enable34', null);
check_cells(_(""),'sale_enable34', null);
check_cells(_(""),'purchase_enable34', null);
echo '<tr>';
text_cells(_(""), 'label_value34', $_POST['label_value34'], 20);
text_cells5(_(""), 's_position34', $_POST['s_position34'], 10);
text_cells4(_(""), 'p_position34', $_POST['p_position34'], 10);
text_cells2(_(""), 's_width34', $_POST['s_width34'], 10);
text_cells3(_(""), 'p_width34', $_POST['p_width34'], 10);
echo"</tr>";

echo '<td>';
echo'<h4>Combo 5</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_5.php?", _("Combo 5 Setup"));
//echo '<td>';
check_cells(_(""), 'item_enable35', null);
check_cells(_(""),'sale_enable35', null);
check_cells(_(""),'purchase_enable35', null);
echo"<tr>";
text_cells(_(""), 'label_value35', $_POST['label_value35'], 20);
text_cells5(_(""), 's_position35', $_POST['s_position35'], 10);
text_cells4(_(""), 'p_position35', $_POST['p_position35'], 10);
text_cells2(_(""), 's_width35', $_POST['s_width35'], 10);
text_cells3(_(""), 'p_width35', $_POST['p_width35'], 10);
echo"</tr>";

echo '<td>';
echo'<h4>Combo 6</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_6.php?", _("Combo 6 Setup"));
//echo '<td>';
check_cells(_(""), 'item_enable36', $_POST['item_enable36']);
check_cells(_(""),'sale_enable36', null);
check_cells(_(""),'purchase_enable36', null);
echo"<tr>";
text_cells(_(""), 'label_value36', $_POST['label_value36'], 20);
text_cells5(_(""), 's_position36', $_POST['s_position36'], 10);
text_cells4(_(""), 'p_position36', $_POST['p_position36'], 10);
text_cells2(_(""), 's_width36', $_POST['s_width36'], 10);
text_cells3(_(""), 'p_width36', $_POST['p_width36'], 10);
echo"</tr>";










end_outer_table(1);

//hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

div_end();

end_form(2);

//-------------------------------------------------------------------------------------------------

end_page();

