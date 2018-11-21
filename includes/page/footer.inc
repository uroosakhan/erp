<?php

function page_footer($no_menu=false, $is_index=false)
{
	global $path_to_root, $js_lib, $Validate, $Editors, $Ajax, $sql_queries;

	$page_header_args = func_get_args();
	hook_invoke_all('pre_footer', $page_header_args);


	include_once($path_to_root."/themes/".user_theme()."/renderer.php");
	$rend = new renderer();
	$rend->menu_footer($no_menu, $is_index);

	$edits = "editors = ".$Ajax->php2js($Editors).";";
	$Ajax->addScript('editors', $edits);

	echo "<script type='text/javascript'>
		_focus = '" . get_post('_focus') . "';
		_validate = " . $Ajax->php2js($Validate).";
		var $edits
	</script>";

   	add_user_js_data();
	echo "\n<script type=\"text/javascript\"><!--\n";

	foreach($js_lib as $text)
	  	echo $text;
	echo "\n--></script>\n";

	if ($sql_queries) {
		div_start('footer_debug');
		 echo "<div class='note_msg'>$sql_queries</div>";
		div_end();
	}
    ?>

    <?php if(user_theme() =='premium') { ?>
    <script>

        function dimension_filter(d) {
            var day_id = document.getElementById("day_filter").value;
            var dimension_id = d.value;
            // alert(a);

            $.ajax({
                // url : './themes/premium/Dashboard_Widgets/day_summary_helper.php',
                url: '<?php global $path_to_root; echo $path_to_root . '/themes/premium/Dashboard_Widgets/day_summary_helper.php';?>',
                type: 'POST',
                data: {dimension_id: dimension_id, day_id: day_id},
                success: function (result) {
                    $('#changed_data').html(result);
                }
            });
        }

        function day_filter(d) {
            var dimension_id = document.getElementById("dimension_filter").value;
            var day_id = d.value;
            // alert(a);
            $.ajax({
                url: '<?php global $path_to_root; echo $path_to_root . '/themes/premium/Dashboard_Widgets/day_summary_helper.php';?>',
                type: 'POST',
                data: {dimension_id: dimension_id, day_id: day_id},
                success: function (result) {
                    $('#changed_data').html(result);
                }
            });
        }
    </script>
    <?php
    echo '<style>
@media screen and (max-width: 680px) {
    #btn {
       display: none;
    }
    #recen{
    display: none;
    }
    .sp{
    display: none;

    }
}

@media only screen and (max-width:768px) {
div.modal-content{
    max-width:100%;
        min-width:100%;
        width:100%;
  }
  
div.modal-body{
width:100%;
padding:0px;
margin:0px;
text-align:center;
min-width:auto;
max-width:100%;
}
div.modal-header{
min-width:100%;
max-width:500px;
}

div.modal-footer{
min-width:100%;
max-width:500px;
}
.modal-header h2,span.closeBtn
{
font-size:1.2em;
/*border:solid green;*/
}
.modal-header h2, .modal-footer h4{
    margin:0;
    font-size:16px;
  }
.modal-body{
   max-width:100%;
   min-width:100%;
   }
.modal-body h4{
   padding:0px;
  /* border:solid green;*/
   font-size:16px;
   text-align:center;
   /* padding:10px 20px; */
  }

  .modal-footer h4{

  /*border:solid red;*/
    text-align: center;
  }
}
</style>';
    echo "<script src='$path_to_root/themes/premium/all_js/bootstrap.min.js'></script>";
    echo "<script src='$path_to_root/themes/premium/all_js/app.min.js'></script>";
    echo "<script src='$path_to_root/themes/premium/all_js/jquery.slimscroll.min.js'></script>";
}


    echo "</body></html>\n";
}

