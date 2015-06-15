
/**
 * @package     hubzero-cms
 * @file        plugins/courses/outline/prerequisites.js
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
//  Courses outline javascript
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($) {
	$('.unit, .edit-asset, .wiki-edit')
		.on('change', '.add-prerequisite select', function ( e ) {
			var wrap  = $(this).parents('.prerequisites-wrap'),
				title = $(this).find(':selected').text(),
				id    = $(this).val(),
				t     = $(this);

			// Save item
			$.ajax({
				url      : '/api/courses/prerequisite/new',
				dataType : 'json',
				type     : 'POST',
				data     : $(this).parents('form').serializeArray(),
				success  : function ( data, textStatus, jqXHR ) {

					var source   = $('#prerequisite-item').html(),
						template = Handlebars.compile(source),
						context  = {
							"title"  : title,
							"id"     : id,
							"req_id" : data.id
						},
						html = template(context);

					var item = $(html);
					item.hide();

					wrap.find('ul').append(item);
					item.slideDown();
					t.find(':selected').remove();
				}
			});
		})
		.on('click', '.remove-requisite', function ( e ) {
			var wrap  = $(this).parents('.prerequisites-wrap'),
				title = $(this).siblings('.requisite-item-title').html(),
				id    = $(this).parents('.requisite-list-item').data('id'),
				t     = $(this);

			// Delete item
			$.ajax({
				url      : '/api/courses/prerequisite/delete',
				dataType : 'json',
				type     : 'POST',
				data     : {id: $(this).data('delete-id')},
				success  : function ( data, textStatus, jqXHR ) {
					t.parents('.requisite-list-item').slideUp(function ( ) {
						$(this).remove();
						wrap.find('select').append('<option value="'+id+'">'+title+'</option>');
					});
				}
			});
		});
});