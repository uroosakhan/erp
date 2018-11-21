<?php
function add_sales_type_pos($name, $tax_included, $factor)
{
	$sql = "INSERT INTO ".TB_PREF."sales_types (sales_type,tax_included,factor) VALUES (".db_escape($name).","
		.db_escape($tax_included).",".db_escape($factor).")";
	db_query($sql, "could not add sales type");		
}

function update_sales_type_pos($id, $name, $tax_included, $factor)
{
	$sql = " UPDATE ".TB_PREF."sales_types SET sales_type = ".db_escape($name).",
	tax_included =".db_escape($tax_included).", factor=".db_escape($factor)." WHERE id = ".db_escape($id);
	db_query($sql, "could not update sales type");			
}
function get_all_sales_orderss()
{
    $sql = "SELECT *, Sum(details.unit_price*details.quantity*(1-details.discount_percent))+freight_cost-orders.discount1-orders.discount2 as TotalAmount
    FROM ".TB_PREF."sales_orders orders
    INNER JOIN ".TB_PREF."sales_order_details details
    ON orders.order_no = details.order_no
    WHERE details.qty_sent < details.quantity";
    $sql .= " GROUP BY details.order_no";
    $sql .= " ORDER BY orders.order_no desc limit 10";
    return db_query($sql, "could not get all sales types");
}
function get_all_sales_order_details($order_no)
{
    $sql = "SELECT  count(*) as records
    FROM ".TB_PREF."sales_order_details
    WHERE qty_sent > 0
    AND order_no=".db_escape($order_no);
    $sql.=" ORDER BY  order_no  ";
    $getquery=   db_query($sql, "could not get all sales types");
    return db_fetch($getquery);
}

function get_all_sales_types_pos($all=false)
{
	$sql = "SELECT * FROM ".TB_PREF."sales_types";
	if (!$all)
		$sql .= " WHERE !inactive";
	
	return db_query($sql, "could not get all sales types");
} 

function get_sales_type_pos($id)
{
	$sql = "SELECT * FROM ".TB_PREF."sales_types WHERE id=".db_escape($id);
	
	$result = db_query($sql, "could not get sales type");
	
	return db_fetch($result);
}

function get_sales_type_name_pos($id)
{
	$sql = "SELECT sales_type FROM ".TB_PREF."sales_types WHERE id=".db_escape($id);
	
	$result = db_query($sql, "could not get sales type");
	
	$row = db_fetch_row($result);
	return $row[0];
}

function delete_sales_type_pos($id)
{
	$sql="DELETE FROM ".TB_PREF."sales_types WHERE id=".db_escape($id);
	db_query($sql,"The Sales type record could not be deleted");

	$sql ="DELETE FROM ".TB_PREF."prices WHERE sales_type_id=".db_escape($id);
	db_query($sql,"The Sales type prices could not be deleted");
}
function get_today_invoices_for_reprint()
{
    $sql = "SELECT *, (orders.total+freight_cost-orders.discount1-orders.discount2) as TotalAmount FROM ".TB_PREF."debtor_trans trans
            LEFT JOIN ".TB_PREF."sales_orders orders ON orders.order_no = trans.order_";
    $sql .= " WHERE trans.type = ".ST_SALESINVOICE;
    $sql .= " AND trans.tran_date = CURDATE()   ";
    $sql .= " ORDER BY orders.order_no DESC limit 10";
    return db_query($sql, "COULD NOT BE GET INVOICES");
}
?>