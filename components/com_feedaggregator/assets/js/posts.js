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
	
		 	jQuery.post("/index.php?option=com_feedaggregator&task=updateStatus&no_html=1",
			         {'id': record_id,
		         	  'action': action },
				     function(data) 
				     {
		      		 	jQuery.fancybox.next();
		      			jQuery("#row-"+record_id).attr('style','background-color:red');
		    		 	jQuery("#row-"+record_id).remove();

			         });
	         
		 }
		 else
		 {
			 jQuery.post("/index.php?option=com_feedaggregator&task=updateStatus&no_html=1",
			         {'id': record_id,
		         	  'action': action },
				     function(data) 
				     {
		     			 jQuery.fancybox.next();
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

			         });
    
			 
		 }
	});
	
}); 
