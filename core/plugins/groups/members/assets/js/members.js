/**
 * @package     hubzero-cms
 * @file        plugins/groups/members/members.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$('.remove-role a').on('click', function(e) {
		e.preventDefault();
		var answer = confirm('Are you sure you want to delete this member role? It will also delete any associations members have with the role.');
		if (answer) { 
			window.location = $(this).attr('href');
		}
	});

	$('.message-member a').fancybox({
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
			href = $(this).attr('href');
			if (href.indexOf('?') == -1) {
				href += '?no_html=1';
			} else {
				href += '&no_html=1';
			}
			$(this).attr('href', href);	
		},
		afterShow: function() {
			if ($('#hubForm-ajax')) {
				$('#hubForm-ajax').submit(function(e) {
					e.preventDefault();
					$.post($(this).attr('action'), $(this).serialize(), function(returndata) {
						$.fancybox.close();
					});
				});
			}
		}
	});
	//end message members pop up

	$('a.assign-role').fancybox({
		type: 'ajax',
		width: 300,
		height: 170,
		autoSize: false,
		fitToView: false,
		titleShow: false,
		tpl: {
			wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
		},
		beforeLoad: function() {
			href = $(this).attr('href');
			if (href.indexOf('?') == -1) {
				href += '?no_html=1';
			} else {
				href += '&no_html=1';
			}
			$(this).attr('href', href);	
		},
		afterShow: function() {
			frm = $('#hubForm-ajax');
			uid = $('#uid').val();
			if (frm) {
				frm.on('submit', function(e) {
					e.preventDefault();
					if ($('#roles').val() == '') {
						alert('You must select a member role.');
						return false;
					}
					$.post($(this).attr('action'), $(this).serialize(), function(returndata) {
						$('#roles-list-' + uid).load(window.location.href + ' #roles-list-'+uid+' > *');
						$.fancybox.close();
					});
				});
			}
		}
	});
	//end assign role pop ups
});
