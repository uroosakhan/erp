<?php


function add_item_customer_data($customer_id, $stock_id, $price)
{
	$sql = "INSERT INTO ".TB_PREF."item_customers (customer_id, stock_id, price) VALUES (";
	$sql .= db_escape($customer_id).", ".db_escape($stock_id).", ".db_escape($price).")";

	db_query($sql,"The supplier purchasing details could not be added");
}

function update_item_customer_data($selected_id, $stock_id,
                                   $customer_id, $price)
{
	$sql = "UPDATE ".TB_PREF."item_customers 
	SET customer_id=" . $customer_id . ",
	 price=" . $price . "
		
	WHERE stock_id=".db_escape($stock_id) . " AND
		id=".db_escape($selected_id);
	db_query($sql,"The supplier purchasing details could not be updated");
}

function delete_item_customer_data($selected_id, $stock_id)
{
	$sql = "DELETE FROM ".TB_PREF."item_customers WHERE id=".db_escape($selected_id)."
		AND stock_id=".db_escape($stock_id);
	db_query($sql,"could not delete purchasing data");
}

function get_items_customer_data($stock_id)
{
    $sql = "SELECT cdata.*, customer.name
		FROM ".TB_PREF."item_customers cdata
			INNER JOIN ".TB_PREF."debtors_master customer ON cdata.customer_id=customer.debtor_no
		WHERE stock_id = ".db_escape($stock_id);

    return db_query($sql, "The supplier purchasing details for the selected part could not be retrieved");
}

function get_item_customer_data($selected_id, $stock_id)
{
	$sql = "SELECT cdata.*, customer.name
		FROM ".TB_PREF."item_customers cdata
			INNER JOIN ".TB_PREF."debtors_master customer ON cdata.customer_id=customer.debtor_no
		WHERE cdata.id=".db_escape($selected_id)."
			AND cdata.stock_id=".db_escape($stock_id);

	$result = db_query($sql, "The supplier purchasing details for the selected supplier and item could not be retrieved");

	return db_fetch($result);
}
