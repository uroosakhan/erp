<?php

include_once($path_to_root . "/includes/db/manufacturing_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/work_centres_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/work_orders_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/work_order_costing_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/work_order_issues_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/work_order_produce_items_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/work_order_requirements_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/generate_db.inc");
include_once($path_to_root . "/manufacturing/includes/db/work_orders_db_cart_req.inc");

// function get_sql_for_bom_inquiry
// ($items)
// {
   
//     $sql = "SELECT 
//   parent,'',loc_code,workcentre_added,SUM(quantity)as qty,id
// 		";

//     $sql .= "
// 		FROM ".TB_PREF."bom 
		
// 		WHERE id!=0
// 		AND parent !=''";


// if ($items != '')
//         $sql .= " AND  parent = ".db_escape($items);
   

//     $sql .= " GROUP BY parent ";

//     return $sql;
// }