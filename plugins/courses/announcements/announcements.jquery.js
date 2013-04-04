/**
 * @package     hubzero-cms
 * @file        plugins/courses/announcements/announcements.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Announcements scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.CoursesAnnouncements = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

		$('.announcements .close').each(function(i, item) {
			$(item).on('click', function(e) {
				e.preventDefault();

				var id = $(this).attr('data-id'),
					days = $(this).attr('data-duration');

				$($(this).parent()).slideUp();

				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));

				document.cookie = 'ancmnt' + id + '=closed; expires=' + date.toGMTString() + ';';
			});
		});

		$('.announcement a.delete').each(function(i, el) {
			$(el).on('click', function(e) {
				var res = confirm('Are you sure you wish to delete this item?');
				if (!res) {
					e.preventDefault();
				}
				return res;
			});
		});
		
		$('.datepicker').datepicker({
			dateFormat: 'yy-mm-dd'
		});
	} //end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.CoursesAnnouncements.initialize();
});
