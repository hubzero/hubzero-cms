/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

	// Managers drag member roles into the order the sidebar lists them
	var roles = $('.aside ul.roles.sortable');
	if (roles.length && jQuery.ui && jQuery.ui.sortable) {
		roles.sortable({
			handle: '.role-mover',
			items: '> li',
			axis: 'y',
			cursor: 'move',
			update: function (e, ui) {
				var frm = $('form.roles-order');
				var data = frm.serialize() + '&' + roles.sortable('serialize', { key: 'roles[]' });

				$.post(frm.attr('action'), data, function (response) {
					if (!response || !response.success) {
						roles.sortable('cancel');
						alert((response && response.message) || frm.attr('data-error'));
					}
				}, 'json').fail(function () {
					roles.sortable('cancel');
					alert(frm.attr('data-error'));
				});
			}
		});
	}

	// Sorting replaces an order a manager may have arranged by hand
	$('.roles-sort').on('submit', function (e) {
		if (!confirm($(this).find('.sort-roles').attr('data-confirm'))) {
			e.preventDefault();
		}
	});

	// Fill the deny reply from a canned response
	var denyReason = $('#reason');
	var lastResponse = '';
	$('#deny-response').on('change', function () {
		var text = $(this).find('option:selected').attr('data-text');

		if (typeof text === 'undefined') {
			return;
		}

		// Don't silently throw away a reply the manager has written or edited
		var current = $.trim(denyReason.val());
		if (current !== '' && current !== $.trim(lastResponse) && !confirm($(this).attr('data-confirm'))) {
			return;
		}

		denyReason.val(text);
		lastResponse = text;
	});

	// Add and remove rows on the canned response form
	$('.deny-responses').on('click', '.deny-response-add', function (e) {
		e.preventDefault();

		var list = $('.deny-response-list');
		var next = parseInt(list.attr('data-next'), 10);
		var row  = list.find('.deny-response').last().clone();

		row.find('input, textarea').each(function () {
			var field = $(this);
			field.val('');
			field.attr('name', field.attr('name').replace(/\[\d+\]/, '[' + next + ']'));
			field.attr('id', field.attr('id').replace(/-\d+-/, '-' + next + '-'));
		});
		row.find('label').each(function () {
			$(this).attr('for', $(this).attr('for').replace(/-\d+-/, '-' + next + '-'));
		});

		list.append(row).attr('data-next', next + 1);
		row.find('input').first().focus();
	});

	$('.deny-responses').on('click', '.deny-response-remove button', function (e) {
		e.preventDefault();

		var row = $(this).closest('.deny-response');

		// Keep one row on the page to clone from; an emptied row isn't saved
		if ($('.deny-response-list .deny-response').length > 1) {
			row.remove();
		} else {
			row.find('input, textarea').val('');
		}
	});
});
