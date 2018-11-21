$(function(){
	$('.dragbox')
	.each(function(){
		$(this).hover(function(){
			$(this).find('h2').addClass('collapse');
		}, function(){
		$(this).find('h2').removeClass('collapse');
		})
		.find('h2').hover(function(){
			$(this).find('.configure').css('visibility', 'visible');
		}, function(){
			$(this).find('.configure').css('visibility', 'hidden');
		})
		.click(function(){
			$(this).siblings('.dragbox-content').toggle();
				//updateWidgetData();				
		})
		.end()
		.find('.configure').css('visibility', 'hidden');
	});
    
	$('.column').sortable({
		connectWith: '.column',
		handle: 'h2',
		cursor: 'move',
		placeholder: 'placeholder',
		forcePlaceholderSize: true,
		opacity: 0.4,
		start: function(event, ui){
			//Firefox, Safari/Chrome fire click event after drag is complete, fix for that
			if($.browser.mozilla || $.browser.safari) 
				$(ui.item).find('.dragbox-content').toggle();
		},
		stop: function(event, ui){
			ui.item.css({'top':'0','left':'0'}); //Opera fix
			if(!$.browser.mozilla && !$.browser.safari) {
				//updateWidgetData();
			}
		}
		,
		update: function(event, ui) {
			 updateWidgetData();
		}
		
	})
	.disableSelection();
});

function updateWidgetData(){
	var items=[];
	$('.column').each(function(){
		var columnId=$(this).attr('id');
		$('.dragbox', this).each(function(i){
			var collapsed=0;
			if($(this).find('.dragbox-content').css('display')=="none")
				collapsed=1;
			var item={
				id: $(this).attr('id'),
				collapsed: collapsed,
				order : i,
				column: columnId
			};
			items.push(item);
		});
	});
	var sortorder={ items: items };	
	
	if (this.timer) clearTimeout(this.timer);
            
            this.timer = setTimeout(function () {
                $.ajax({
                    url: 'modules/dashboard/dashboard-updt.php',
                    data: 'data='+$.toJSON(sortorder),
                    dataType: 'json',
                    type: 'post',
                    success: function (data) {                    	
                    	if (data.success) {                    		                        		
                        	$("#errorMsg").addClass('success').html(data.message).fadeIn('slow');	  
	                    } else {
			    			$("#errorMsg").addClass('error').html(data.message).fadeIn('slow');
	                    }
                    }
                });
            }, 250);
		
}

function onAddClick() {
	alert('Add Dashlets');
}

function onRemoveClick(itemId) {
	
	if(confirm('This widget will be removed, ok?')) {
		
		if (this.timer) clearTimeout(this.timer);
	            
	            this.timer = setTimeout(function () {
	                $.ajax({
	                    url: 'modules/dashboard/dashboard-del.php',
	                    data: 'id='+$.toJSON(itemId),
	                    dataType: 'json',
	                    type: 'post',
	                    success: function (data) {                    	
	                    	if (data.success) {
	                    		$("#item" + itemId).remove(); 
	                        	$("#errorMsg").addClass('success').html(data.message).fadeIn('slow');	  
		                    } else {
				    			$("#errorMsg").addClass('error').html(data.message).fadeIn('slow');
		                    }
	                    }
	                });
	            }, 250);
	}
}