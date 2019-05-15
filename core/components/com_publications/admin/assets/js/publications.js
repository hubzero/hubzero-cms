/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'resetrating') {
			if (confirm(frm.getAttribute('data-confirmreset'))) {
				Hubzero.submitform(task, frm);
				return;
			} else {
				return;
			}
		}

		var admin_action = document.getElementById('admin_action');

		if (task == 'saveorder') {
			Hubzero.submitform('saveauthororder', frm);
			return;
		}

		if (task == 'publish') {
			admin_action.value = 'publish';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'revert') {
			admin_action.value = 'revert';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'message') {
			admin_action.value = 'message';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'unpublish') {
			admin_action.value = 'unpublish';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'republish') {
			admin_action.value = 'republish';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

if (typeof(HUB) === 'undefined') {
	var HUB = {};
}

HUB.Publications = {

	popratings: function() {
		window.open("<?php echo Route::url('index.php?option=' . $this->option . '&task=ratings&id=' . $this->model->id . '&no_html=1'); ?>", 'ratings', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no');
		return false;
	},

	reorder: function(list) {
		if ($('.reorder').length == 0 || $(list).length == 0 || $(list).hasClass('noedit')) {
			return false;
		}

		// Drag items
		$(list).sortable({
			items: "> li.reorder",
			update: function() {
				HUB.Publications.saveOrder();
			}
		});
	},

	saveOrder: function() {
		var items = $('.pick');
		var selections = '';

		if (items.length > 0) {
			items.each(function(i, item) {
				var id = $(item).attr('id');
				id = id.replace('author_', '');

				if (id != '' && id != ' ') {
					selections = selections + id + '-';
				}
			});
		}

		HUB.Publications.displayOrdering();

		$('#neworder').val(selections);
	},

	displayOrdering: function() {
		var nums = $('.ordernum');
		var o = 1;

		if (nums.length > 0) {
			nums.each(function(i, item) {
				$(item).html(o);
				o++;
			});
		}
	}
};

jQuery(document).ready(function($){
	// Enable author reordering
	HUB.Publications.reorder($('#author-list'));

	$('#reset_rating').on('click', function(e){
		Hubzero.submitbutton('resetrating');
	});

	$('#reset_ranking').on('click', function(e){
		Hubzero.submitbutton('resetranking');
	});

	$('#do-message').on('click', function(e){
		Hubzero.submitbutton('message');
	});

	$('#do-unpublish').on('click', function(e){
		Hubzero.submitbutton('unpublish');
	});

	$('#do-republish').on('click', function(e){
		Hubzero.submitbutton('republish');
	});

	$('#do-publish').on('click', function(e){
		Hubzero.submitbutton('publish');
	});

	$('#do-revert').on('click', function(e){
		Hubzero.submitbutton('revert');
	});
});
