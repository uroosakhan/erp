<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");

?>

<script type="text/javascript">
	$(function(){
		$('#tabs').tabs();
	});
</script>


<div id="drawer">&nbsp;</div>

<form id="FormParam" name="FormParam" action="#" method="post">

	<div id="tabs">

		<ul id="status">
			<li><a href="#tabs-1"><strong><?php  echo _("Add Widgets") ?></strong></a></li>
		</ul>

			<div id="tabs-1" class="page">

				<h2>
					<em><?php echo _("Please choose your Widgets"); ?></em>
				</h2>

				<ul>
					<?php
						$sql = "SELECT * FROM ".TB_PREF."widgets_template ORDER BY is_system ASC,title ASC";

						$queryResult = db_query($sql);
							$_recNum = 0;
							
							$_renderClass =  'clsOdd';
														
							while($row = db_fetch($queryResult))
							{
								$_recNum ++;								
								
								if ($_recNum%2==0) {
									$_renderClass =  'clsEven';
								} else {
									$_renderClass =  'clsOdd';
								}
								
								?>
								<li class="<?php echo $_renderClass ?>">
									<a href="#" class="savebutton <?php echo $_renderClass ?>" rel="<?php echo $row['id'] ?>" title="<?php echo _(stripslashes(trim(htmlspecialchars_decode($row['title'], ENT_QUOTES)))) ?>"><?php echo _("Add") ?> - <?php echo _(stripslashes(trim(htmlspecialchars_decode($row['title'], ENT_QUOTES)))) ?></a>									
								</li>
							<?php
							} //End while loop
					?>
					
				</ul>

			</div>
			
	</div><!--tabs-->
	
</form>


<script>

$(function() {

	$(".savebutton").click(function(){		
		  $.ajax({
	            type: "post",  
			   url: "modules/dashboard/widgets-save-act.php",
	            data:  { Id: $(this).attr('rel') },
	            dataType: 'json',
	            success: function (data) {
	            	window.location = 'index.php?application=dashboard';
	            }
	        });
	});
});

</script>
