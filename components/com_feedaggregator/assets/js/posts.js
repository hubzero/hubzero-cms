jQuery(document).ready(function() 
{	
	jQuery('.fancybox-inline').fancybox(
			{
				'transitionIn' : 'elastic',
				'transitionOut' : 'elastic'
			});
	
	jQuery('.actionBtn').click(function()
	{
		var x = jQuery(this).attr('id');
		jQuery(this).each(function()
				{
					jQuery(this).addClass("active");
				});
		var record_id = x.split("-").pop();
		var action = x.split("-");
		action = action[0];
		 if(action == 'remove')
		 {
			jQuery("#row-"+record_id).attr('style','background-color:red');
		 	jQuery("#row-"+record_id).remove();
		 	jQuery.fancybox.close();
		 	jQuery.post("<?php echo JRoute::_('index.php?option=' . $this->option . '&task=updateStatus&no_html=1'); ?>",
			         {'id': record_id,
		         	  'action': action },
				     function(data) 
				     {
			         });
	         
		 }
		 else
		 {
			 jQuery.fancybox.close();
			 jQuery.post("<?php echo JRoute::_('index.php?option=' . $this->option . '&task=updateStatus&no_html=1'); ?>",
			         {'id': record_id,
		         	  'action': action },
				     function(data) 
				     {
			         });
     		 if(action == "mark")
				{
     				jQuery('#status-'+record_id).text('under review');
	         		jQuery('#status-'+record_id).attr('style','color: purple');
	     						
				}
			else if(action == "approve")
			{
	         		 jQuery('#status-'+record_id).text('approved');	
	         		 jQuery('#status-'+record_id).attr('style','color: green');
			}
			 
		 }
		
	});
	
}); 
