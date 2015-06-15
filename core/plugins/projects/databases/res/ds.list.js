/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License,
 * version 3 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
