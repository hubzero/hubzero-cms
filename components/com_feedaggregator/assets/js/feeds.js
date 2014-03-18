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
		
		jQuery('.required-field').each(function()
		{
			if(jQuery(this).val() == '')
			{
				submitToken = 0;
				jQuery(this).attr('style', 'background-color: #FFD6CC;');

			}
			else if(jQuery(this).val() != '')
			{
				submitToken = 1;
			}
		});
		
		if(submitToken == 1)
		{
			jQuery('#hubForm').submit();
		}
		else
		{
			alert("Please check all required fields.");
		}
		
	});
	
}); 
