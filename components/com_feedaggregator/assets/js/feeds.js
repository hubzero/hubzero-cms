jQuery(document).ready(function() 
{	
	jQuery('.required-field').blur(function(){
		if (jQuery(this).val() == '')
		{
			jQuery(this).attr('style', 'background-color: #FFD6CC;');
		}
		else
		{
			jQuery(this).attr('style', 'background-color: #FFFF;');
		}
	});
	
	jQuery('#submitBtn').click(function(){
		event.preventDefault();
		var submitToken = 0;
		var numItems = jQuery('.required-field').length;
		
		jQuery('.required-field').each(function()
		{
			if(jQuery(this).val() == '')
			{
				jQuery(this).attr('style', 'background-color: #FFD6CC;');

			}
			else if(jQuery(this).val() != '')
			{
				submitToken = submitToken + 1;
			}
		});
		
		if(submitToken == numItems)
		{
			jQuery('#hubForm').submit();
		}
		else
		{
			alert("Please check all required fields.");
		}
		
	});
	
}); 
