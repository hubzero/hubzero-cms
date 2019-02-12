/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/autogen-story.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!HUB.Administrator)
{
	HUB.Administrator = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Administrator.NewsletterAutoGen = {
	jQuery: jq,

	initialize: function() {
		this.updateTitle();
		this.changeContentSource();
		this.changeItemCount();
		this.changeStoryLayout();
	},
	updateTitle: function() {
		$('#story-title').on('keyup', function() {
			$('#previewStoryTitle').html("<h1>"+$('#story-title').val()+"</h1>");
		});
	},
	updateContent: function() {
			var selectedSource = $('#contentSource').val();
			var selectedLayout = $('#storyLayout').val();
			var itemCount 		 = $('#itemCount').val();
		$.ajax({url:'index.php?option=com_newsletter&controller=story&task=fetchautocontent&no_html=1',
				data:{source:selectedSource,
							layout:selectedLayout,
							itemCount:itemCount,
							}
			})
			.done(function(data) {
				$('#previewContentArea').html(data);
				$("input[name='story']").val(data);
			});
	},
	changeContentSource: function() {
		$('#contentSource').on('change', function() {
				HUB.Administrator.NewsletterAutoGen.updateContent();
			});
	},
	changeItemCount: function() {
		$('#itemCount').on('keyup', function() {
				HUB.Administrator.NewsletterAutoGen.updateContent();
			});
	},
	changeStoryLayout: function() {
		$('#storyLayout').on('change', function() {
				this.updateContent();
		});
	},
}

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Administrator.NewsletterAutoGen.initialize();
});
