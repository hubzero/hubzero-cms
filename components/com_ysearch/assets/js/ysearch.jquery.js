/**
 * @package     hubzero-cms
 * @file        components/com_ysearch/ysearch.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

HUB.YSearch = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		// Collapse nested search results on javascript-aware browsers
		$('.child-result').each(function(i, el) {
			$(el).hide();
		});

		// Enable controls to expand nested results again
		$('.expand').each(function(i, el) {
			$(el).show();
			$(el).bind('click', function() {
				var list = this.parentNode.nextSibling;
				while (list.tagName != 'UL')
					list = list.nextSibling;
				if ($(list).css('display') == 'block')
				{
					$(list).hide();
					$(this).css('background', 'url(\'/components/com_ysearch/assets/img/expand.gif\') no-repeat 0 0');
				}
				else
				{
					$(list).show();
					$(this).css('background', 'url(\'/components/com_ysearch/assets/img/expand.gif\') no-repeat -20px 0');
				}
			});
		});

		// Enable auto-submit of per-page setting form by ...
		//  ... hiding the submit button
		$('.search-per-page-submitter').each(function(i, el) { 
			$(el).hide(); 
		});
		
		//  ... and making the select element submit its parent form on change
		$('.search-per-page-selector').each(function(i, el) {
			$(el).bind('change', function() {
				var par = this.parentNode;
				while (par.tagName != 'FORM')
					par = par.parentNode;
				par.submit();
			});
		});
	}
}

jQuery(document).ready(function($){
	HUB.YSearch.initialize();
});

