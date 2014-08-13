//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-----------------------------------------------------------
//  Highlight table rows when clicking checkbox
//-----------------------------------------------------------

if (!jq) {
	var jq = $;
}

HUB.Newsletter = {
	jQuery: jq,
	
	unsubscribe: function()
	{
		var $ = this.jQuery;
		
		if ($("#reason").length)
		{
			$("#reason-alt").hide();
			
			$("#reason").on('change', function(event) {
				var value = $(this).val();
				if (value == 'Other')
				{
					$("#reason-alt").show();
				}
				else
				{
					$("#reason-alt").hide();
				}
			});
		}
	},
	
	iframe: function()
	{
		var $ = this.jQuery,
			$iframe = $("#newsletter-iframe");
		
		//if we have the iframe
		if ($iframe.length )
		{
			//on iframe load
			$iframe.load(function(){
				
				//make links open in new window
				$iframe.contents().find('a').attr('target', '_blank');
				
				//hide view in browser link meant for email
				$iframe.contents().find('.display-browser').hide();
				
				//set height of iframe to height of contents
				var height = $iframe.contents().find('html').innerHeight();
				$iframe.attr('height', height + 15);
			});
			
		}
	},
	
	initialize: function() {
		var $ = this.jQuery;
		
		//unsubscribe reason
		HUB.Newsletter.unsubscribe();
		
		//view newsletter iframe
		HUB.Newsletter.iframe();
	}
}

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Newsletter.initialize();
});