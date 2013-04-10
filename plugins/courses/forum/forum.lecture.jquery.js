/**
 * @package     hubzero-cms
 * @file        plugins/courses/forum/forum.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
//  Forum scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
	//return this;
};

HUB.Plugins.CoursesForum = {
	jQuery: jq,
	
	updateComments: function(data, addto) {
		var $ = this.jQuery;
		if (!addto) {
			addto = 'prepend'
		}
		if (data.response.length > 0) {
			//console.log(data);
			/*if (typeof(wykiwygs) !== 'undefined') {
				if (wykiwygs.length) {
					for (i=0; i<wykiwygs.length; i++)
					{
						wykiwygs[i].t.value = '';
						wykiwygs[i].e.body.innerHTML = '';
					}
				}
			}
			$('#comments-container input[type=file]').val('');*/

			var last = $('#lastchange'),
				last_id = $('#lastid');

			for ( var i = 0; i< data.response.length; i++ ) 
			{
				item = data.response[i];
				if (item.parent && $('#t'+item.parent).length)
				{
					if (item.created > last.val()) {
						last.val(item.created);
					}
					last_id.val(item.id);

					if ($('#c' + item.id).length) {
						// Comment already exists!
						continue;
					}

					if ($('#t'+item.parent).length) {
						//$('#t'+item.parent).prepend($(phtml(item)).hide().fadeIn());
						if (addto == 'prepend') {
							$('#t'+item.parent).prepend($(item.html).hide().fadeIn());
						} else {
							$('#t'+item.parent).append($(item.html).hide().fadeIn());
						}
					}
				}
			}
			
			jQuery(document).trigger('ajaxLoad');
		}
	},
	
	resetForms: function() {
		var $ = this.jQuery;
		/*if (typeof(wykiwygs) !== 'undefined') {
			if (wykiwygs.length) {
				for (i=0; i<wykiwygs.length; i++)
				{
					wykiwygs[i].t.value = '';
					wykiwygs[i].e.body.innerHTML = '';
				}
			}
		}*/
		$('#comments-container input[type=file]').val('');
	},
	
	initialize: function() {
		var $ = this.jQuery;

		if ($('#comments-container').length <= 0) {
			return;
		}

		var limit = $('#limit').val(),
			start = 0, // + limit,
			url = $('#comments-container').attr('data-action') + '?no_html=1&limit=' + limit + '&start=';

		// Do some voodoo to get AJAX file upload working
		if ($('#commentform').length > 0) {
			$('<iframe src="about:blank?nocache=' + Math.random() + '" id="upload_target" name="upload_target" style="display:none;"></iframe>')
				.on('load', function(){
					data = jQuery.parseJSON($(this).contents().text());
					if (data) {
						HUB.Plugins.CoursesForum.resetForms();
						HUB.Plugins.CoursesForum.updateComments(data, 'prepend');
					}
				})
				.appendTo($('#commentform'));

			var act = $('#commentform').attr('action');
			$('#commentform')
				.attr('target', 'upload_target')
				.on('submit', function() {
					$(this).attr('action', $(this).attr('action').nohtml() + '&start_at=' + $('#lastchange').val());
					return true;
				});
		}

		// Attach a click event for Iframe file upload
		$('#comments-container ol.comments').on('click', 'input[type=submit]', function (e) {
			var frm = $($(this).closest('form')),
				id = frm.attr('id') + '-iframe';

			$('<iframe src="about:blank?nocache=' + Math.random() + '" id="' + id + '" name="' + id + '" style="display:none;"></iframe>')
				.on('load', function(){
					data = jQuery.parseJSON($(this).contents().text());
					if (data) {
						HUB.Plugins.CoursesForum.resetForms();
						HUB.Plugins.CoursesForum.updateComments(data, 'prepend');
					}
				})
				.appendTo(frm.parent());

			frm.attr('target', id)
				.on('submit', function() {
					$(frm.parent()).addClass('hide');
					frm.attr('action', frm.attr('action').nohtml() + '&start_at=' + $('#lastchange').val());
					return true;
				});
		});

		// Attach click events for deletion and showing/hiding the reply form
		$('#comments-container')
			.on('click', '.reply', function (e) {
				e.preventDefault();
				var frm = '#' + $(this).attr('rel');
				if ($(frm).hasClass('hide')) {
					$(frm).removeClass('hide');
				} else {
					$(frm).addClass('hide');
				}
			})
			.on('click', '.cancelreply', function (e) {
				e.preventDefault();
				$(this).closest('.comment-add').addClass('hide');
			})
			.on('click', '.delete', function (e) {
				var res = confirm('Are you sure you wish to delete this item?');
				if (!res) {
					e.preventDefault();
				}
				return res;
			});

		// Create a load more button for AJAXy loading of results
		$('#comments-container .list-footer')
			.css('display', 'none')
			.after('<a id="loadmore" href="' + url + start + '">Show more comments</a>');

		$('#loadmore').on('click', function(e){
			e.preventDefault();

			$.getJSON(url + start + '&start_id=' + $('#lastid').val(), {}, function(data) {
				start += limit;

				if (data.code == 0) {
					HUB.Plugins.CoursesForum.updateComments(data, 'append');
					if (data.response.length <= 0 || data.response.length < limit) {
						$('#loadmore').hide();
					}
				}
			});
		});

		// Notifier for new posts
		if (!$('#comments-new').length) {
			$('<div></div>')
				.attr('id', 'comments-new')
				.text('0 new comments. Click to load.')
				.on('click', function() {
					$.getJSON($('#commentform').attr('action').nohtml() + '&start_at=' + $('#lastchange').val(), {}, function(data){
						HUB.Plugins.CoursesForum.updateComments(data, 'prepend');
						$('#comments-new').hide();
					});
				})
				.hide()
				.prependTo('.comments-wrap');
		}

		// Heartbeat for checking for new posts
		setInterval(function () {
			//$('#commentform').attr('action').nohtml() + '&count=1&start_at=' + $('#lastchange').val()
			$.getJSON('/api/forum/thread/?find=count&object_id=' + $('#field-object_id').val() + '&scope=' + $('#field-scope').val() + '&scope_id=' + $('#field-scope_id').val() + '&start_at=' + $('#lastchange').val(), {}, function(data){
				if (data.code == 0 && data.count > 0) {
					$('#comments-new').text(data.count + ' new comments. Click to load.').fadeIn();
				}
			});
		}, 60 * 1000);
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.CoursesForum.initialize();
});