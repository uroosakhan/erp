
DROP TABLE IF EXISTS `0_widgets_template`;
CREATE TABLE `0_widgets_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column_id` int(11) NOT NULL,
  `sort_no` int(11) NOT NULL,
  `collapsed` tinyint(4) NOT NULL,
  `url` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `source` varchar(50) NOT NULL,
  `width` varchar(50) NOT NULL,
  `height` varchar(50) NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` varchar(150) NOT NULL,
  `dt_created` datetime NOT NULL,
  `updated_by` varchar(150) DEFAULT NULL,
  `dt_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `0_widgets_template` VALUES 
(1,2,0,0,'top-10-customer-in-fiscal-year.php','Top 10 customers in fiscal year','top10customerinfiscalyear','100%', '630px',1,'system',now(),'system',now()),
(2,2,0,0,'top-10-supplier-in-fiscal-year.php','Top 10 suppliers in fiscal year','top10supplierinfiscalyear','100%', '630px',1,'system',now(),'system',now()),
(3,2,0,0,'top-10-stock-in-fiscal-year.php','Top 10 Sold Items in fiscal year','top10iteminfiscalyear','100%', '630px',1,'system',now(),'system',now()),
(4,2,0,0,'top-10-dimension-in-fiscal-year.php','Top 10 Dimensions in fiscal year','top10dimensionfiscalyear','100%', '370px',1,'system',now(),'system',now()),
(5,2,0,0,'top-10-overdue-sales-invoices.php','Top 10 Overdue Sales Invoices','top10overduesalesinvoice','100%', '300px',1,'system',now(),'system',now()),
(6,2,0,0,'top-10-overdue-purchase-invoices.php','Top 10 Overdue Purchase Invoices','top10overduepurchaseinvoice','100%', '300px',1,'system',now(),'system',now()),
(7,2,0,0,'gl-in-fiscal-year.php','Profit and Loss','glfiscalyear','100%', '370px',1,'system',now(),'system',now()),
(8,2,0,0,'top-10-recent-sales-order.php','Top 10 Recent Sales Order','top10recentsalesorder','100%', '300px',1,'system',now(),'system',now()),
(9,2,0,0,'top-10-recent-sales-invoices.php','Top 10 Recent Sales Invoices','top10recentsalesinvoice','100%', '300px',1,'system',now(),'system',now());



DROP TABLE IF EXISTS `0_widgets`;
CREATE TABLE `0_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` smallint(6) COLLATE utf8_bin NOT NULL,
  `column_id` int(11) NOT NULL,
  `sort_no` int(11) NOT NULL,
  `collapsed` tinyint(4) NOT NULL,
  `url` varchar(100) COLLATE utf8_bin NOT NULL,
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `source` varchar(50) COLLATE utf8_bin NOT NULL,
  `width` varchar(50) NOT NULL,
  `height` varchar(50) NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` varchar(150) COLLATE utf8_bin NOT NULL,
  `dt_created` datetime NOT NULL,
  `updated_by` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `dt_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `0_widgets` VALUES 
(1,'2',2,0,0,'editor.php','Editor','editorchart','100%', '400px',1,'system',now(),'system',now()),
(2,'2',1,0,0,'line.php','Line Chart','linechart','100%', '400px',1,'system',now(),'system',now()),
(3,'2',2,0,0,'pie.php','Pie Chart','piechart','100%', '400px',1,'system',now(),'system',now()),
(4,'2',2,0,0,'bar.php','Bar Chart','barchart','100%', '400px',1,'system',now(),'system',now()),
(5,'2',2,0,0,'bar-option.php','Bar Option Chart','bartoptionchart','100%', '400px',1,'system',now(),'system',now()),
(6,'2',1,0,0,'bar-table.php','Bar Table Chart','bartablechart','100%', '600px',1,'system',now(),'system',now()),
(7,'2',1,0,0,'motion.php','Motion Chart','motionchart','100%', '600px',1,'system',now(),'system',now());




