/**
 * @package     hubzero-cms
 * @file        components/com_support/support.js
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
// Support
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Support = {
	jQuery: jq,
	
	getMessage: function() {
		var $ = HUB.Support.jQuery;
		
		var id = $('#messages');
		if (id.val() != 'mc') {
			var hi = $('#'+id.val()).val();
			$('#comment').val(hi);
		} else {
			$('#comment').val('');
		}
	},
	
	initialize: function() {
		var $ = this.jQuery;

		HUB.Support.addDeleteQueryEvent();
		HUB.Support.addEditQueryEvent();

		if ($('#messages').length > 0) {
			$('#messages').on('change', HUB.Support.getMessage);
		}

		if ($('#make-private').length > 0) {
			$('#make-private').on('click', function() {
				var es = $('#email_submitter');
				if ($('#make-private').attr('checked')) {
					if ($('#email_submitter').attr('checked')) {
						$('#email_submitter').removeAttr('checked').attr('disabled', 'disabled');
					}
					$('#commentform').addClass('private');
				} else {
					$('#email_submitter').removeAttr('disabled').attr('checked', 'checked');
					$('#commentform').removeClass('private');
				}
			});
		}
	},

	addEditQueryEvent: function() {
		$('a.modal').fancybox({
			type: 'ajax',
			width: 600,
			height: 550,
			autoSize: false,
			fitToView: false,
			titleShow: false,
			tpl: {
				wrap:'<div class="fancybox-wrap"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div>'
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
				Conditions.addqueryroot('.query', true);

				if ($('#queryForm').length > 0) {
					$('#queryForm').submit(function(e) {
						e.preventDefault();

						if (!$('#field-title').val()) {
							alert('Please provide a title.');
							return false;
						}

						query = Conditions.getCondition('.query > fieldset');
						$('#field-conditions').val(JSON.stringify(query));

						$.post($(this).attr('action'), $(this).serialize(), function(data) {
							$('#custom-views').html(data);
							HUB.Support.addEditQueryEvent();
							$.fancybox().close();
						});
					});
				}
			}
		});
	},

	addDeleteQueryEvent: function() {
		var $ = HUB.Support.jQuery;

		$('.views').on('click', '.delete', function(e){
			e.preventDefault();

			var res = confirm('Are you sure you wish to delete this item?');
			if (!res) {
				return false;
			}

			var href = $(this).attr('href');
			if (href.indexOf('?') == -1) {
				href += '?no_html=1';
			} else {
				href += '&no_html=1';
			}

			$.get(href, {}, function(data){
				$('#custom-views').html(data);
				HUB.Support.addEditQueryEvent();
			});

			return false;
		});
	}
}

jQuery(document).ready(function($){
	HUB.Support.initialize();
});
