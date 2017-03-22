/**
 * @package     hubzero-cms
 * @file        components/com_publications/admin/assets/js/batchcreate.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;
	
	var PublicationImport = new function() {
		this.timer     	= null;
		this.checker   	= null;
		this.output 	= $('#results');
		this.inputFile 	= $('#field-file');
		this.submitBtn	= $('#batch_submit');
	
		this.init = function() {
			this.attachEvents();
			this.output.hide();
		};
		
		this.attachEvents = function() {
			var self = this;
			this.iniUpload();
		};
		
		this.iniUpload = function() {
			var self = this;
			
			if (!this.submitBtn.length)
			{
				return false;
			} 
			this.submitBtn.on('click', function(event) {
				event.preventDefault();
				self.sendData(1);
			});
		};
		
		this.handleResults = function(data) {
			var self = this;
			console.log(data);
			
			// Show output area
			self.output.show();
			
			if (data)
			{
				data = $.parseJSON(data);
			}
			if (data.result == 'success')
			{
				self.output.removeClass('witherror');
				self.output.html(data.records);
				
				// Append submit
				if ($('#recordcount').length)
				{
					$('#recordcount').before(self.drawControls());
					
					$('#dorun').on('click', function(event) {
						event.preventDefault();
						self.sendData(2);
					});
				}
			}
			else
			{
				self.output.addClass('witherror');
				var out = '<p class="general-error">' + data.error + '</p>';
				if (data.records)
				{
					out = out + data.records;
				}
				self.output.html(out);
			}
			if ($('#resultlist').length > 0)
			{
				$('#resultlist').accordion({
					'active' : false,
					'heightStyle' : 'content',
					'collapsible' : true
				});
			}		
		};
		
		this.drawControls = function()
		{
			var self = this;
			var html = '<p id="controls" class="controlarea">' + 
				'<input type="button" class="btn" id="dorun" value="Create record(s)" />' +
				'</p>';
				
			return html;
		};
		
		this.sendData = function(dryrun) {
			var self = this;
			$('form').unbind(); 
			$('#dryrun').val(dryrun);
			$('form').submit(function()
			{
			    var url = $('form').attr('action');
				var formData = new FormData($(this)[0]);
				$.ajax({
			           type: "POST",
			           url: url,
			           data: formData,
					   contentType: false,
					   processData: false,
			           success: function(response)
			           {
							self.handleResults(response);
			           },
					   error: function(response)
	                   {
						    var errorMessage = $(response.responseText).find("#errorbox").html();
							errorMessage = errorMessage.length ? errorMessage: "A server error occurred.";
							var errorObj = {
								"result" : "error",
								"error" : errorMessage,
								"records": "" 
							}; 
					   		self.handleResults(JSON.stringify(errorObj));
							
					   }
			     });

			    return false;
			});	
				
			$('form').submit();
		};
		
		this.showProgress = function() {
			var self = this;
		};
	};
	
	PublicationImport.init();
});
