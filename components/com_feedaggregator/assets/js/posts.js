jQuery(document).ready(function() 
{	
	//enable fancyboxes
	makeFancy();
	
	jQuery('.actionBtn').on('click', function()
	{
		var post = changeState(this);
		buttonSelect(post);
		
	}); //end button pressing
}); // end ready
	

/*
 * handles the fancybox wrappers
 */
function makeFancy() {
 	jQuery('.fancybox-inline').fancybox(
			{
				'transitionIn' : 'elastic',
				'transitionOut' : 'elastic'
			});
}

function changeState(actionBtn)
{
	//determine the id and the action
	var post = {"record_id":jQuery(actionBtn).data('id'), "action": jQuery(actionBtn).data('action')};
	
	if(post.action == 'remove')
	{
		jQuery.post("/index.php?option=com_feedaggregator&task=updateStatus&no_html=1",
		{'id': post.record_id,
		 'action': post.action 
		},
		function(data) 
		{
			jQuery.fancybox.next();
			jQuery("#row-"+post.record_id).attr('style','background-color:red');
			jQuery("#row-"+post.record_id).remove();
		});
	}
	else
	{
		jQuery.post("/index.php?option=com_feedaggregator&task=updateStatus&no_html=1",
		{
		 'id': post.record_id,
		 'action': post.action 
		},
		function(data) 
		{
			jQuery.fancybox.next();
			if(post.action == "mark")
			{
				jQuery('#status-'+post.record_id).text('under review');
				jQuery('#status-'+post.record_id).attr('style','color: purple');				
			}
			else if(post.action == "approve")
			{
				jQuery('#status-'+post.record_id).text('approved');	
				jQuery('#status-'+post.record_id).attr('style','color: green');
			}
		});
	}
	
	return post;
} //function changeState()

/*
 * handles the changing of state of the button
 * returns: JSON object with record_id, action
 */
function buttonSelect(post)
{
		
	jQuery('.btnGrp' + post.record_id).each(function(){
		if(jQuery(this).prop('disabled'))
		{
			jQuery(this).removeAttr('disabled');
		}		
	});
	jQuery('#' + post.action + '-' + post.record_id).attr('disabled','disabled');
	jQuery('#' + post.action + '-prev-' + post.record_id).attr('disabled','disabled');
	
	return post;
}