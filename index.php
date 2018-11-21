<?php
echo '
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-123413378-1"></script>
<script> 
';

echo"  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-123413378-1');
</script>
";

	$path_to_root=".";
	if (!file_exists($path_to_root.'/config_db.php'))
		header("Location: ".$path_to_root."/install/index.php");

	$page_security = 'SA_OPEN';
	ini_set('xdebug.auto_trace',1);
	include_once("includes/session.inc");

	add_access_extensions();
	$app = &$_SESSION["App"];
	if (isset($_GET['application']))
		$app->selected_application = $_GET['application'];

	$app->display();
