/**
 * @package     hubzero-cms
 * @file        plugins/members/messages/messages.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

jQuery(document).ready(function(jq){
	var $ = jq;

	$('#msgall').on('change', function(e) {
		if (this.checked) {
			var val = true;
		} else {
			var val = false;
		}

		$('input.chkbox').each(function(i, el) {
			if (val && !$(el).attr('checked')) {
				$(el).attr('checked', 'checked');
			} else if (!val && $(el).attr('checked')) {
				$(el).removeAttr('checked');
			}
		});
	});

	$('a.message-link').fancybox({
		type: 'ajax',
		width: 700,
		height: 'auto',
		autoSize: false,
		fitToView: false,  
		titleShow: false,
		tpl: {
			wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
		},
		beforeLoad: function() {
			href = $(this).attr('href').nohtml();
			$(this).attr('href', href);
		},
		afterShow: function() {
			$(this.element).removeClass('unread');
			$(this.element)
				.parents('tr')
				.find('td.status span')
				.removeClass('unread');
		}
	}); 

	$('#message-toolbar a.new').fancybox({
		type: 'ajax',
		width: 700,
		height: 'auto',
		autoSize: false,
		fitToView: false,
		titleShow: false,
		tpl: {
			wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
		},
		beforeLoad: function() {
			href = $(this).attr('href').nohtml()
			$(this).attr('href', href);
		},
		afterLoad: function(upcomingObject, currentObject) {
			var dom = $(upcomingObject.content);
			dom.filter('script').each(function() {
				$.globalEval(this.text || this.textContent || this.innerHTML || '');
			});
		},
		afterShow: function() {
			if ($('#hubForm-ajax')) {
				$('#hubForm-ajax').on('submit', function(e) {
					e.preventDefault();

					members = $('#members').val();
					message = $('#msg-message').val();

					if (!members) {
						alert("Must select a message recipient.");
						return false;
					}

					if (!message) {
						alert("You must enter a message.");
						return false;
					}

					$.post($(this).attr('action'),$(this).serialize(), function(data) {
						$.fancybox.close();
					});
				});
			}
		}
	});
});
