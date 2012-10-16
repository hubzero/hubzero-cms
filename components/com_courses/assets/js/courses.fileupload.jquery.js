/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/courses.jquery.js
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

HUB.CoursesFileUpload = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;
		
		if($("#ajax-uploader").length)
		{
			var uploader = new qq.FileUploader({
					element: $("#ajax-uploader")[0],
					action: $("#ajax-uploader").attr("data-action"),
					multiple: true,
					showDrop: true,
					template: '<div class="qq-uploader">' +
								'<div class="qq-upload-button">Upload a file</div>' + 
								'<div class="qq-upload-drop-area" style="display:block;"><span>or drop files here to upload</span></div>' +
								'<ul class="qq-upload-list"></ul>' + 
							   '</div>',
					onSubmit: function(id, file)
					{
						if(!$(".qq-uploading").length)
						{
							$("#themanager").append("<div class=\"qq-uploading\">Uploading...</div>");
						}
					},
					onComplete: function(id, file, response)
					{
						$("#imgManager").attr("src", $("#imgManager").attr("src"));
						$(".qq-uploading").fadeOut("slow", function() {
							$(".qq-uploading").remove();
						});
					}
				});
		}
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.CoursesFileUpload.initialize();
});