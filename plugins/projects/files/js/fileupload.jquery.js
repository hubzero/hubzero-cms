/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectFilesFileUpload = {
	jQuery: jq,
	uploaded: 0,
	updated: 0,
	failed: 0,
	queued: 0,
	processed: 0,

	initialize: function() {
		var $ = this.jQuery;
		
		var isMSIE = /*@cc_on!@*/0;
		if (isMSIE) {
		  	// Turn it off in IE
		 	return false;
		} 
		
		if ($("#archiveCheck").length) 
		{
			$("#archiveCheck").remove();
		}
				
		if ($("#ajax-uploader").length) 
		{
			var uploader = new qq.ButtonFileUploader({
				element: $("#ajax-uploader")[0],
				action: $("#ajax-uploader").attr("data-action"),
				params: {test: 1},
				multiple: true,
				debug: true,
				maxChunkSize: 10000000,
				template: '<div class="qq-uploader">' +
							'<div class="qq-upload-button"><span>Click or drop file</span></div>' + 
							'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
							'<ul class="qq-upload-list"></ul>' + 
						   '</div>',
				fileTemplate: '<li>' +
						'<span class="qq-upload-icon"></span>' +
		                '<span class="qq-upload-file"></span>' +
						'<span class="qq-upload-name"></span>' +
						'<span class="qq-upload-status"></span>' +
						'<a class="qq-upload-cancel" href="#">Cancel</a>' +
						'<span class="qq-upload-ext"></span>' +
		                '<span class="qq-upload-size"></span>' +	
						'<span class="qq-upload-spinner"></span>' +
						'<span class="qq-upload-error"></span>' +	                
		            '</li>',
				button: null,

				onComplete: function(id, file, response) {
					
					if (response.error)
					{
						HUB.ProjectFilesFileUpload.failed = HUB.ProjectFilesFileUpload.failed + 1;
					}
										
					if (response.success > 0)
					{
						HUB.ProjectFilesFileUpload.uploaded = HUB.ProjectFilesFileUpload.uploaded + response.success;
						
						if (response.isNew == false)
						{
							HUB.ProjectFilesFileUpload.updated = HUB.ProjectFilesFileUpload.updated + response.success;
						}
					}
					
					// All files processed?
					HUB.ProjectFilesFileUpload.processed = HUB.ProjectFilesFileUpload.processed + 1;
					if (HUB.ProjectFilesFileUpload.processed == HUB.ProjectFilesFileUpload.queued)
					{
						HUB.ProjectFilesFileUpload.uploadComplete();
					}
					
				}
			});
		}
						
		if ($('#f-upload').length)
		{
			$('#f-upload').addClass('btnaction');
			$('#f-upload').addClass('disabled');
			
			$('#f-upload').on('click', function(e) {
				e.preventDefault();

				var queue = uploader.checkQueue();
				var files = uploader.checkFiles();
				
				// Record number of items in queue
				HUB.ProjectFilesFileUpload.queued = queue.length;
				
				if (queue.length == 0)
				{
					// do nothing
				}
				else
				{					
					// Archive file present?
					var arch = uploader._checkArchive();
					
					if (arch && !$('#f-upload').hasClass('started'))
					{
						var question  = 'Do you wish to expand selected archive file(s)?';
						var yesanswer = 'yes, expand';
						var noanswer  = 'no, upload as an archive';

						// Add confirmation
						$('#f-upload').parent().after('<div class="confirmaction" id="confirm-box" style="display:block;">' + 
							'<p>' + question + '</p>' + 
							'<p>' + 
								'<a href="#" class="confirm" id="confirm-yes">' + yesanswer + '</a>' + 
								'<a href="#" class="confirm c-no" id="confirm-no">' + noanswer + '</a>' + 
								'<a href="#" class="cancel" id="confirm-box-cancel">cancel</a>' +
							'</p>' + 
						'</div>');

						$('#confirm-box-cancel').on('click', function(e){
							e.preventDefault();
							$('#confirm-box').remove();
						});
						
						$('#confirm-yes').on('click', function(e){
							e.preventDefault();
							$('#confirm-box').remove();
							
							// Start upload
							uploader.startUploads(1);
						});

						$('#confirm-no').on('click', function(e){
							e.preventDefault();
							$('#confirm-box').remove();
							
							// Start upload
							uploader.startUploads(0);
						});

						// Move close to item
						var coord = $('#f-upload').position();		
						$('#confirm-box').css('left', coord.left - 50).css('top', coord.top + 100 );
					}
					else
					{
						// Start upload						
						uploader.startUploads(0);
					}					
				}
							
				return false;
			});
		}
	},

	addConfirm: function () 
	{	
		var $ = this.jQuery;
		if ($('#confirm-box')) {
			$('#confirm-box').remove();
		}

		var href = $(link).attr('href');

		// Add confirmation
		var ancestor = $(link).parent().parent();
		$(ancestor).after('<div class="confirmaction" id="confirm-box" style="display:block;">' + 
			'<p>' + question + '</p>' + 
			'<p>' + 
				'<a href="' + href + '" class="confirm">' + yesanswer + '</a>' + 
				'<a href="#" class="cancel" id="confirm-box-cancel">' + noanswer + '</a>' + 
			'</p>' + 
		'</div>');
		
		$('#confirm-box-cancel').on('click', function(e){
			e.preventDefault();
			$('#confirm-box').remove();
		});
		
		// Move close to item
		var coord = $($(link).parent()).position();
		
		$('html, body').animate({
			scrollTop: $(link).offset().top
		}, 2000);
		
		$('#confirm-box').css('left', coord.left).css('top', coord.top + 200);
	},
			
	uploadComplete: function() 
	{
		var $ = this.jQuery;

		var form = $('#hubForm-ajax');
		if (form.length)
		{			
			// Redirect back to file list
			//form.submit();
			window.location.replace(form.attr('action'));
		}
	}
};

jQuery(document).ready(function($){
	HUB.ProjectFilesFileUpload.initialize();
});