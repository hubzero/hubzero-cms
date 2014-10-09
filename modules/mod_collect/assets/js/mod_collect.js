/**
 * @package     hubzero-cms
 * @file        modules/mod_collect/assets/js/collect.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

if (!jq) {
	var jq = $;
}

var scrp = null;

jQuery(document).ready(function(jq){
	var $ = jq;

	if ($('.collect-this').length) {
		$('.collect-this').fancybox({
			type: 'ajax',
			width: 500,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			tpl: {
				wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			},
			/*beforeLoad: function() {
				var href = $(this).attr('href').nohtml();
				$(this).attr('href', href);
			},*/
			afterLoad: function(current, previous) {
				scrp = current.content.match(/<script type=\"text\/javascript\">(.*)<\/script>/ig);
				current.content = current.content.replace(/<script(.*)<\/script>/ig, '');
			},
			beforeShow: function() {
				if (scrp && scrp.length) {
					scrp = scrp[0].replace(/<script type=\"text\/javascript\">/ig, '').replace(/<\/script>/ig, '');
					eval(scrp);
				}
			},
			afterShow: function() {
				var el = this.element;
				if ($('#hubForm')) {
					$('#hubForm').on('submit', function(e) {
						e.preventDefault();

						$.post($(this).attr('action'), $(this).serialize(), function(data) {
							var response = jQuery.parseJSON(data);
							if (!response.success) {
								$('#sbox-content').html('<p class="error" style="margin-left: 1em; margin-right: 1em;">' + response.message + '</p>')
							} else {
								$('#sbox-content').html('<p class="passed" style="margin-left: 1em; margin-right: 1em;">' + response.message + '</p>');
							}
							setTimeout(function(){
								$.fancybox.close();
							}, 2 * 1000);
						});
					});
				}
			}
		});
	}
});

