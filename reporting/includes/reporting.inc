<?php

// Link to printing single document with bulk report template file.
// Ex. label_cell(print_document_link($myrow['order_no'], _("Print")), $type);
// or display_note(print_document_link($order_no, _("Print this order")));
// You only need full parameter list for invoices/credit notes


function get_reports_id($type_no)
{

	$sql = "SELECT description FROM ".TB_PREF."reflines WHERE trans_type=".db_escape($type_no);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_reports_douple_type($type_no)
{
	$sql = "SELECT description FROM ".TB_PREF."reflines WHERE trans_type=".db_escape($type_no);
	$sql .= " ORDER BY id DESC";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function print_document_link_new($doc_no, $link_text, $link=true, $type_no,
							 $icon=false, $class='printlink', $id='', $email=1, $extra=0,$newcase)
{
	global $path_to_root;
	include_once($path_to_root . "/includes/types.inc");

	$url = $path_to_root.'/sales/inquiry/sales_orders_view.php?';
	$def_orientation = (user_def_print_orientation() == 1 ? 1 : 0);
//display_error($type_no);

	switch ($type_no)
	{
	
		case ST_PURCHORDER12 :
			$rep = 1088992;
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
        case ST_SALESORDER12 :
            $rep = 108899;
            // from, to, currency, email, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;
            
		case ST_SALESQUOTE :
			$rep = get_reports_id($type_no);
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
		case ST_SALESORDER :
			$rep = get_reports_id($type_no);
			// from, to, currency, email, quote, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => 0,
				'PARAM_5' => '',
				'PARAM_6' => $def_orientation);
			break;
		case ST_LOCTRANSFER :
            $rep = get_reports_id($type_no);
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => '',
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => 0,
                'PARAM_5' => '',
                'PARAM_6' => $def_orientation);
            break;
            		case ST_LOCTRANSFER :
            $rep = get_reports_id($type_no);
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => '',
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => 0,
                'PARAM_5' => '',
                'PARAM_6' => $def_orientation);
            break;
		case ST_CUSTDELIVERY :
			$rep = get_reports_id($type_no);
			// from, to, email, packing slip, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => $extra,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
			
			       case ST_CUSTDELIVERY : // Customer Credit Note
            $rep = get_reports_id($type_no);
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 119 ? '' : $def_orientation);

            break;

 case 1190 : // Customer Credit Note
            $rep = get_reports_id($type_no);
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1190 ? '' : $def_orientation);

            break;

        case ST_CUSTDELIVERY : // Customer Credit Note
            $rep = get_reports_id($type_no);
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1191 ? '' : $def_orientation);

            break;

        case ST_CUSTDELIVERY : // Customer Credit Note
            $rep = get_reports_id($type_no);
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1192 ? '' : $def_orientation);

            break;

        case ST_CUSTDELIVERY : // Customer Credit Note
            $rep = get_reports_id($type_no);
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1193 ? '' : $def_orientation);

            break;
            
        case ST_CUSTDELIVERY : // Customer Credit Note
            $rep = get_reports_id($type_no);
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1194 ? '' : $def_orientation);

            break;
			
		case ST_SALESINVOICE : // Sales Invoice
			$rep = get_reports_id($type_no);
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 107 ? '' : $def_orientation);
			if ($rep == 107)
				$ar['PARAM_7'] = $def_orientation;
			break;
				case 10755 : // Sales Invoice
			$rep = 10755;
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 10755 ? '' : $def_orientation);
			if ($rep == 10755)
				$ar['PARAM_7'] = $def_orientation;
			break;
			
				$rep = 10730;
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 10730 ? '' : $def_orientation);
			if ($rep == 10730)
				$ar['PARAM_7'] = $def_orientation;
			break;
			
			case ST_SALESINVOICE1 : // Sales Invoice
			$rep = 10787;
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 10787 ? '' : $def_orientation);
			if ($rep == 10787)
				$ar['PARAM_7'] = $def_orientation;
			break;
		case ST_CUSTCREDIT : // Customer Credit Note
			$rep = get_reports_id($type_no);
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 107 ? '' : $def_orientation);

			break;
		    case ST_SUPPRECEIVE:
            $rep = get_reports_id($type_no);
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;
			case ST_SALESTAX :
		$rep = get_reports_douple_type(10);
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
		case ST_PURCHORDER :
			$rep = get_reports_id($type_no);
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;

            
		case ST_CUSTPAYMENT :
			$rep = get_reports_id($type_no);
			// from, to, currency, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
			
		case ST_CUSTPAYMENT_A5 :

            $rep = 70124;
            // from, to, currency, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $newcase,
                'PARAM_2' => '',
                'PARAM_3' => '',
                'PARAM_4' => $def_orientation);
            break;	
            
                case ST_CUSTPAYMENT_A5_CM :

            $rep = 70125;
            // from, t-o, currency, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $newcase,
                'PARAM_2' => '',
                'PARAM_3' => '',
                'PARAM_4' => $def_orientation);
            break;


        case ST_CUSTPAYMENT_A5_ :

            $rep = 701241;
            // from, to, currency, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $newcase,
                'PARAM_2' => '',
                'PARAM_3' => '',
                'PARAM_4' => $def_orientation);
            break;
		     case ST_SUPPAYMENT :
            $rep = get_reports_id($type_no);
            // from, to, currency, email, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $type_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;
		case ST_WORKORDER :
			//$rep = 409;
				$rep = get_reports_id($type_no);
			// from, to, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
			
			
			case WO_ADVANCED :
			$rep = 5000;

			// from, to, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
			case ST_MANUORDERREQ :
			$rep = 5001;
			// from, to, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		///////////////24-08-17
		case ST_BANKDEPOSIT :
			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		case ST_BANKTRANSFER:

			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
 case ST_PURCHREQ :
            $rep = 207;
            // from, to, currency, email, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;

		case ST_CRV :

			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		case ST_CPV :
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		case ST_BANKPAYMENT :
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;

//		case ST_BANKTRANSFER :
//
//			// case ST_BANKDEPOSIT:
//			$rep = 7013;
//			// from, to, email, comments orientation
//			$ar = array(
//				'PARAM_0' => $doc_no,
//				'PARAM_1' => $doc_no,
//				'PARAM_2' => $email,
//				'PARAM_3' => '',
//				'PARAM_4' => $def_orientation);
//			break;

		case ST_JOURNAL :
			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
			
		case RC_INVENTORY :
            $rep = 30333;
            // from, to, currency, email, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;

		default:
			return null;
	}

	return print_link($link_text, $rep, $ar, "", $icon, $class, $id);
}
function print_document_link($doc_no, $link_text, $link=true, $type_no,
							 $icon=false, $class='printlink', $id='', $email=0, $extra=0,$newcase)
{
	global $path_to_root;
	include_once($path_to_root . "/includes/types.inc");

	$url = $path_to_root.'/reporting/prn_redirect.php?';
	$def_orientation = (user_def_print_orientation() == 1 ? 1 : 0);

	switch ($type_no)
	{
		case ST_SALESQUOTE :
			$rep = get_reports_id($type_no);
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
			
			case 7899 :
			$rep = 211;
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
			
	   case RC_INVENTORY :
            $rep = 30333;
            // from, to, currency, email, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $rep == 30333 ? '' : $def_orientation);
            break;
			
		case ST_SALESORDER :
			$rep = get_reports_id($type_no);
			// from, to, currency, email, quote, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => 0,
				'PARAM_5' => '',
				'PARAM_6' => $def_orientation);
			break;
	case ST_LOCTRANSFER :
            $rep = get_reports_id($type_no);
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => '',
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => 0,
                'PARAM_5' => '',
                'PARAM_6' => $def_orientation);
            break;
            		case ST_LOCTRANSFER :
            $rep = get_reports_id($type_no);
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => '',
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => 0,
                'PARAM_5' => '',
                'PARAM_6' => $def_orientation);
            break;
            
            
            case 789 :
    $rep = 70123;
    // from, to, email, comments orientation
    $ar = array(
        'PARAM_0' => $doc_no,
        'PARAM_1' => $doc_no,
        'PARAM_2' => $email,
        'PARAM_3' => '',
        'PARAM_4' => $def_orientation);
    break;
    
    
        case 10921 :
            $rep = 10921;
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => 0,
                'PARAM_5' => '',
                'PARAM_6' => $def_orientation);
            break;
        case ST_SALESORDER2:
            $rep = 1099;
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => 0,
                'PARAM_5' => '',
                'PARAM_6' => $def_orientation);
            break;
            			
			case ST_PURCHORDER12 :
			$rep = 1088992;
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;

        case ST_SALESORDER6:
            $rep = 19099;
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => 0,
                'PARAM_5' => '',
                'PARAM_6' => $def_orientation);
            break;
		case ST_CUSTDELIVERY :
			$rep = get_reports_id($type_no);
			// from, to, email, packing slip, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => $extra,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
		case ST_SALESORDER12 :
            $rep = 108899;
            // from, to, currency, email, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;
		case ST_SALESINVOICE : // Sales Invoice
			$rep = get_reports_id($type_no);

			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 107 ? '' : $def_orientation);
			if ($rep == 107)
				$ar['PARAM_7'] = $def_orientation;
			break;


        case 10730 : // Sales Invoice
            $rep = 10730;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 10730 ? '' : $def_orientation);
            if ($rep == 10730)
                $ar['PARAM_7'] = $def_orientation;
            break;
            
             case 10733 : // Sales Invoice
               $rep = 10733;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 10733 ? '' : $def_orientation);
            if ($rep == 10733)
                $ar['PARAM_7'] = $def_orientation;
            break;

				case 10755 : // Sales Invoice
			$rep = 10755;
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 10755 ? '' : $def_orientation);
			if ($rep == 10755)
				$ar['PARAM_7'] = $def_orientation;
			break;
		case ST_CUSTCREDIT : // Customer Credit Note
			$rep = get_reports_id($type_no);
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 113 ? '' : $def_orientation);

			break;
			case ST_SALESINVOICE1 : // Sales Invoice
			$rep = 10787;
			// from, to, currency, email, paylink, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => '',
				'PARAM_6' => $rep == 10787 ? '' : $def_orientation);
			if ($rep == 10787)
				$ar['PARAM_7'] = $def_orientation;
			break;
			case ST_SALESTAX :
		$rep = get_reports_douple_type(10);
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
		case ST_PURCHORDER :
			$rep = get_reports_id($type_no);
			// from, to, currency, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => '',
				'PARAM_3' => $email,
				'PARAM_4' => '',
				'PARAM_5' => $def_orientation);
			break;
		case ST_CUSTPAYMENT :
			$rep = get_reports_id($type_no);
			// from, to, currency, comments, orientation
			$ar = array(
		        'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
			
        case ST_BANKDEPOSIT :
        
            $rep = get_reports_id($type_no);
            // from, to, email, comments orientation
            $ar = array(
            'PARAM_0' => $doc_no,
            'PARAM_1' => $type_no,
            'PARAM_2' => $email,
            'PARAM_3' => '',
            'PARAM_4' => $def_orientation);
            break;
			
			       case ST_CUSTPAYMENT_A5_CM :

            $rep = 70125;
            // from, t-o, currency, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $newcase,
                'PARAM_2' => '',
                'PARAM_3' => '',
                'PARAM_4' => $def_orientation);
            break;
			
		case ST_CUSTPAYMENT_A5 :

            $rep = 70124;
            // from, to, currency, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $newcase,
                'PARAM_2' => '',
                'PARAM_3' => '',
                'PARAM_4' => $def_orientation);
            break;	
            
             case ST_CUSTPAYMENT_A5_ :

            $rep = 701241;
            // from, to, currency, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $newcase,
                'PARAM_2' => '',
                'PARAM_3' => '',
                'PARAM_4' => $def_orientation);
            break;
		     case ST_SUPPAYMENT :
            $rep = get_reports_id($type_no);
            global $db_connections;
            // from, to, currency, email, comments, orientation
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"]!='DEMO')
            {$ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $type_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            }else
             {   $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            }
            break;
		case ST_WORKORDER :
			//$rep = 409;
				$rep = get_reports_id($type_no);
			// from, to, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
			case WO_ADVANCED :
			$rep = 5000;

			// from, to, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
						case ST_MANUORDERREQ :
			$rep = 5001;
			// from, to, email, comments, orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		///////////////24-08-17
		case ST_BANKDEPOSIT :
			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		case ST_BANKTRANSFER:

			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
 case ST_PURCHREQ :
           $rep = get_reports_id($type_no);
            // from, to, currency, email, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;

		case ST_CRV :

			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		case ST_CPV :
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
		case ST_BANKPAYMENT :
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $type_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;
    case ST_SUPPRECEIVE:
            $rep = get_reports_id($type_no);
            // from, to, currency, email, quote, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => $def_orientation);
            break;
            	       case 119 : // Customer Credit Note
            $rep = 119;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 119 ? '' : $def_orientation);

            break;

 case 1190 : // Customer Credit Note
            $rep = 1190;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1190 ? '' : $def_orientation);

            break;

        case 1191 : // Customer Credit Note
            $rep = 1191;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1191 ? '' : $def_orientation);

            break;

        case 1192 : // Customer Credit Note
            $rep = 1192;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1192 ? '' : $def_orientation);

            break;

        case 1193 : // Customer Credit Note
            $rep = 1193;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1193 ? '' : $def_orientation);

            break;
            
            
        case 1194 : // Customer Credit Note
            $rep = 1194;
            // from, to, currency, email, paylink, comments, orientation
            $ar = array(
                'PARAM_0' => $doc_no,
                'PARAM_1' => $doc_no,
                'PARAM_2' => '',
                'PARAM_3' => $email,
                'PARAM_4' => '',
                'PARAM_5' => '',
                'PARAM_6' => $rep == 1194 ? '' : $def_orientation);

            break;
//		case ST_BANKTRANSFER :
//
//			// case ST_BANKDEPOSIT:
//			$rep = 7013;
//			// from, to, email, comments orientation
//			$ar = array(
//				'PARAM_0' => $doc_no,
//				'PARAM_1' => $doc_no,
//				'PARAM_2' => $email,
//				'PARAM_3' => '',
//				'PARAM_4' => $def_orientation);
//			break;

		case ST_JOURNAL :
			// case ST_BANKDEPOSIT:
			$rep = get_reports_id($type_no);
			// from, to, email, comments orientation
			$ar = array(
				'PARAM_0' => $doc_no,
				'PARAM_1' => $doc_no,
				'PARAM_2' => $email,
				'PARAM_3' => '',
				'PARAM_4' => $def_orientation);
			break;

		default:
			return null;
	}
    if($type_no==ST_SALESORDER6)
        return print_link_price($link_text, $rep, $ar, "", $icon, $class, $id);
    else
        return print_link($link_text, $rep, $ar, "", $icon, $class, $id);

//	return print_link($link_text, $rep, $ar, "", $icon, $class, $id);
}
//
//	Universal link to any kind of report.
//
//---
function print_link_price($link_text, $rep, $pars = array(), $dir = '',
                          $icon=false, $class='printlink', $id='')
{
    global $path_to_root, $pdf_debug;

    $url = $dir == '' ?  $path_to_root.'/reporting/prn_redirect.php?' : $dir;

    $id = default_focus($id);
    foreach($pars as $par => $val) {
        $pars[$par] = "$par=".urlencode($val);
    }
    $pars[] = 'REP_ID='.urlencode($rep);
    $url .= implode ('&', $pars);

    if ($class != '')
        $class = $pdf_debug ? '' : " class='$class'";
    if ($id != '')
        $id = " id='$id'";
    $pars = access_string($link_text);
    if (user_graphic_links() && $icon)
        $pars[0] = set_icon($icon, $pars[0]);
    return "<a  target='_blank' href='$url'$id$class $pars[1] style='color: #fff; '>$pars[0]</a>";
}

function print_link($link_text, $rep, $pars = array(), $dir = '',
					$icon=false, $class='printlink', $id='')
{
	global $path_to_root, $SysPrefs;

	$url = $dir == '' ?  $path_to_root.'/reporting/prn_redirect.php?' : $dir;

	$id = default_focus($id);
	foreach($pars as $par => $val) {
		$pars[$par] = "$par=".urlencode($val);
	}
	$pars[] = 'REP_ID='.urlencode($rep);
	$url .= implode ('&', $pars);

	if ($class != '')
		$class = $SysPrefs->pdf_debug ? '' : " class='$class'";
	if ($id != '')
		$id = " id='$id'";
	$pars = access_string($link_text);
	if (user_graphic_links() && $icon)
		$pars[0] = set_icon($icon, $pars[0]);
	return "<a target='_blank' href='$url'$id$class $pars[1]>$pars[0]</a>";
}

/*
// Purpose: Function to parse a string into parameters
// Release Date: 2014-12-26
// Author: ApMuthu <apmuthu@usa.net>
// Usage:
$str = "PPFrt#2000 CID#6378465 TaxEx#2345-038 abcde ertrgdert COD#4253 jdegtd PIN#6473654";
$p = parse_notes_params($str);
echo print_r($p, true);

An example of usage will be in the reporting/rep110.php file at near the end just before the last $rep-Font(); statement: 


    $notes_params = parse_notes_params($branch['notes']);
    if ($packing_slip == 0 && array_key_exists('CID', $notes_params)) {
        $rep->NewLine(1);
        $rep->TextCol(1, 7, "Old Customer# : " . $notes_params['CID'], - 2);
    }
*/

function parse_notes_params($str, $sep=" ", $delim="#") {
	$str_params = explode($sep, $str);
	$param_array=Array('notes' => '');
	foreach ($str_params AS $str_param) {
		$param_set=explode($delim, trim($str_param));
		$key = (array_key_exists(0, $param_set) ? trim($param_set[0]) : '');
		$val = (array_key_exists(1, $param_set) ? trim($param_set[1]) : '');
		if (strlen($key) > 0 && strlen($val) > 0) {
			$param_array[$key]=$val;
		} else {
			// stop at first missing parameter set
			// break;
			// Collect the rest into notes
			$param_array['notes'] .= (" " .  $str_param);
		}
	}
	$param_array['notes'] = trim($param_array['notes']);
	return $param_array;
}

