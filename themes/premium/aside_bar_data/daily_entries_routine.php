<?php
global $path_to_root;
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root ."/themes/premium/dashboard_db.php");

$today = date2sql(Today());

// $sql = "SELECT a.*,
//             		SUM(IF(ISNULL(g.amount), so.total, IF(g.amount > 0, g.amount, 0))) AS amount,
//             		u.user_id,
//             		UNIX_TIMESTAMP(a.stamp) as unix_stamp
//             		FROM ".TB_PREF."refs AS r, 
//             		".TB_PREF."sales_orders AS so,
//             		".TB_PREF."audit_trail AS a
//             		JOIN ".TB_PREF."users AS u
//             		LEFT JOIN ".TB_PREF."gl_trans AS g ON (g.type_no=a.trans_no
//             			AND g.type=a.type)
//             		WHERE a.user = u.id
//                     AND r.id=a.trans_no
//                     AND a.gl_seq = 0
//                     AND a.trans_no = a.trans_no         			
//                     AND r.type=a.type
//                     AND so.order_no = a.`trans_no`
//                     AND DATE(a.stamp) = '$today'

//             		GROUP BY a.trans_no	,
//             	 a.type
// 		            ORDER BY a.stamp DESC LIMIT 30
//     		    	";
$sql="

SELECT a.*, SUM(IF(ISNULL(g.amount), NULL, IF(g.amount > 0, g.amount, 0))) AS amount, u.user_id,
 UNIX_TIMESTAMP(a.stamp) AS unix_stamp 
 
 
 FROM 0_audit_trail AS a JOIN 0_users AS u LEFT JOIN 0_gl_trans AS g ON (g.type_no=a.trans_no AND g.type=a.type) 
 
 
 WHERE a.user = u.id AND a.stamp >= '$today 00:00:00' 
 AND a.stamp <= '$today 23:59.59' 
 
 GROUP BY a.trans_no,a.gl_seq,a.stamp ORDER BY a.stamp DESC";

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
//                        echo"<p><i class='$icon $color'></i>
//                                Some information about this general settings option</p>";


        $label = "<p style='color: whitesmoke'><i class='$icon $color ' ></i> <b>".
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
            date('d-m-Y', $myrow['unix_stamp'])."
                            </p>";
        echo get_trans_view_str($myrow['type'], $myrow['trans_no'], $label, false);
    }

?>