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

		// review ratings
		$('.thumbsvote').each(function(i, item) {
			$(item).bind('onmouseover', function () {
				$(this).last().last().css('display', 'inline');
			});
			$(item).bind('onmouseout', function () {
				$(this).last().last().css('display', 'none');
			});
		});
		
		$('.revvote').each(function(i, item) {
			$(item).bind('click', function () {
				pn = $($(this).parent().parent().parent());
				if ($($(this).parent()).hasClass('gooditem')) {
					var s = 'yes';
				} else {
					var s = 'no';
				}
							
				var cat = pn.attr('class');
				if (!cat) { 
					cat = 'com_answers'; 
				}
				var itemlabel = cat.replace('com_','');
				
				var id = pn.attr('id').replace(itemlabel+'_','');
				
				$.get('index.php?option='+cat+'&no_html=1&task=rateitem&refid='+id+'&ajax=1&vote='+s, {}, function(data) {
		            $(pn).html(data);
				});
			});
		});
	}
}

jQuery(document).ready(function($){
	HUB.Vote.initialize();
});

