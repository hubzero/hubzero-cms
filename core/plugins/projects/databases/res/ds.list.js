/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

(function(window, document, ds, $, undefined) {

	$(document).ready(function() {
		$('a.delete-db').on('click', function() {
			var link = this;
			$("#confirm-file-delete").dialog({
				resizable: false,
				height: 160,
				modal: true,
				buttons: {
					"Delete Database": function() {
						window.location = $(link).attr('href');
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				}
			});

			return false;
		});

		$('table.listing tr').mouseenter(function() {
			$(this).find('.db-update').show();
		}).mouseleave(function() {
			$(this).find('.db-update').hide();
		});

		$('table.listing span.db-update').on('click', function() {
			var $td = $(this).closest('td');
			var $form = $('#prj-db-update-form');

			$form.find('input[name="db_id"]').val($td.data('db-id'));
			$form.find('input[name="db_title"]').val($td.data('db-title'));
			$form.find('textarea[name="db_description"]').val($td.attr('title'));

			$('#prj-db-update-dialog').dialog({
				width: 560,
				modal: true,
				buttons: {
					'Update': function() {
						$form.trigger('submit');
					},
					Cancel: function() {
						$(this).dialog( "close" );
					}
				}
			});
		});

		$('.update-help-icon').on('click', function() {
			$('#update-db-help-dialog').dialog({
				height: 620,
				width: 740,
				modal: true
			});
		});

	});

}) (this, document, DataStore, jQuery);
