/**
 * @package     hubzero-cms
 * @file        components/com_answers/vote.js
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
// Thumbs voting
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Vote = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

		$('.vote-button').each(function(i, item) {
			if ($(item).attr('href')) {
				$(item).bind('click', function (e) {
					e.preventDefault();

					href = $(this).attr('href');
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).attr('href', href);

					$.get($(this).attr('href'), {}, function(data) {
			            $(item).closest('.voting').html(data);
					});
				});
			}
		});
	}
}

jQuery(document).ready(function($){
	HUB.Vote.initialize();
});

