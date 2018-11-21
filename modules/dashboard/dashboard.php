<?php

$page_security = 'SS_DASHBOARD';

?>
	<h3>&nbsp;&nbsp;<?php echo _('Dashboard'); ?> -  <?php echo $_SESSION["wa_current_user"]->username ?>&nbsp;<span id="errorMsg">&nbsp;</span></h3>
	<div align="left">&nbsp;&nbsp;<a href="modules/dashboard/widgets-add.php?height=450&amp;width=600" title="<?php echo _('Add Widgets to Dashboard'); ?>" class="thickbox tooltip"><img src="themes/<?php echo user_theme(); ?>/images/add-widget.png" border="0"/></a></div><br/>
			
		<?php
			$columnid = 0;			
 
			$checkColumns=db_query("SELECT DISTINCT column_id FROM ".TB_PREF."widgets WHERE user_id = " . db_escape($_SESSION["wa_current_user"]->user) . " ORDER BY column_id");
			
			if(db_num_rows($checkColumns) == 1){ 
				while($column=db_fetch($checkColumns))
				{
					$columnid = $column['column_id'];
				}
			}
			
			if ($columnid == '2') {
				echo '<div class="column" id="column1"></div>';					
			}
			
			$columns=db_query("SELECT DISTINCT column_id FROM ".TB_PREF."widgets WHERE  user_id = " . db_escape($_SESSION["wa_current_user"]->user) . " ORDER BY column_id");
			
			while($column=db_fetch($columns))
			{
				echo '<div class="column" id="column'.$column['column_id'].'" >';
				
				$items=db_query("SELECT * FROM ".TB_PREF."widgets WHERE  user_id = " . db_escape($_SESSION["wa_current_user"]->user) . " AND column_id=" . db_escape($column['column_id']) . " ORDER BY sort_no");
				while($widget=db_fetch($items))
				{					
					echo '<div class="dragbox" id="item'.$widget['id'].'">';
					if ( $widget['is_system'] == '1') {
						echo '<h2>'. _($widget['title']).'<span class="configure"><a href="#" onclick="onRemoveClick(' . $widget['id'] . ');return false;"><img src="themes/'.user_theme(). '/images/remove-widget.png" border="0"/></a></span></h2>';	
					} else {
						echo '<h2>'. _($widget['title']).'<span class="configure"><a href="' . 'modules/dashboard/widgets-edit.php?Id=' . $widget["id"] .  '&height=450&amp;width=600" title="' . _($widget['title']) . '" class="thickbox"><img src="themes/'.user_theme(). '/images/edit-widget.png" border="0"/></a>&nbsp;<a href="#" onclick="onRemoveClick(' . $widget['id'] . ');return false;"><img src="themes/'.user_theme(). '/images/remove-widget.png" border="0"/></a></span></h2>';
					}
					
					echo '<div class="dragbox-content" ';
					if($widget['collapsed']==1)
						echo 'style="display:none;" ';
						echo '>';
						if ( $widget['is_system'] == '1') {
							echo '<iframe src="' . 'modules/dashboard/charts/' . $widget['url'] . "?source=" . $widget['source'] . "&width=".$widget['width']."&height=".$widget['height']. '" width="'.$widget['width'].'" height="'.$widget['height'].'" scrolling="no" frameBorder="0" marginheight="0" marginwidth="0"></iframe>';
						} else {
							echo '<iframe src="' . 'modules/dashboard/charts/' . $widget['url'] . $widget['source'] . "&width=".$widget['width']."&height=".$widget['height']. '" width="'.$widget['width'].'" height="'.$widget['height'].'" scrolling="no" frameBorder="0" marginheight="0" marginwidth="0"></iframe>';
						}
						echo '</div>';
						echo '</div>';
				}				
				echo '</div>';
			}
			
			if ($columnid == '1') {
				echo '<div class="column" id="column2"></div>';		
			} 
		?>