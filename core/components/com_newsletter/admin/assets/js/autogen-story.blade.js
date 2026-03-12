/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (typeof HUB === 'undefined') {
	var HUB = {};
}

if (!HUB.Administrator) {
	HUB.Administrator = {};
}

//----------------------------------------------------------
//  Newsletter AutoGen scripts
//----------------------------------------------------------

HUB.Administrator.NewsletterAutoGen = {

	initialize: function () {
		this.updateTitle();
		this.changeContentSource();
		this.changeItemCount();
		this.changeStoryLayout();
	},

	updateTitle: function () {
		var titleField = document.getElementById('story-title');
		if (titleField) {
			titleField.addEventListener('keyup', function () {
				var preview = document.getElementById('previewStoryTitle');
				if (preview) {
					preview.innerHTML = '<h1>' + titleField.value + '</h1>';
				}
			});
		}
	},

	updateContent: function () {
		var selectedSource = document.getElementById('contentSource');
		var selectedLayout = document.getElementById('storyLayout');
		var itemCount = document.getElementById('itemCount');

		if (!selectedSource || !selectedLayout || !itemCount) {
			return;
		}

		var params = new URLSearchParams({
			source: selectedSource.value,
			layout: selectedLayout.value,
			itemCount: itemCount.value
		});

		fetch('index.php?option=com_newsletter&controller=story&task=fetchautocontent&no_html=1', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: params.toString()
		})
		.then(function (response) { return response.text(); })
		.then(function (data) {
			var preview = document.getElementById('previewContentArea');
			if (preview) {
				preview.innerHTML = data;
			}
			var storyInput = document.querySelector('input[name="story"]');
			if (storyInput) {
				storyInput.value = data;
			}
		});
	},

	changeContentSource: function () {
		var el = document.getElementById('contentSource');
		if (el) {
			el.addEventListener('change', function () {
				HUB.Administrator.NewsletterAutoGen.updateContent();
			});
		}
	},

	changeItemCount: function () {
		var el = document.getElementById('itemCount');
		if (el) {
			el.addEventListener('keyup', function () {
				HUB.Administrator.NewsletterAutoGen.updateContent();
			});
		}
	},

	changeStoryLayout: function () {
		var el = document.getElementById('storyLayout');
		if (el) {
			el.addEventListener('change', function () {
				HUB.Administrator.NewsletterAutoGen.updateContent();
			});
		}
	}
};

//-----------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
	HUB.Administrator.NewsletterAutoGen.initialize();
});
