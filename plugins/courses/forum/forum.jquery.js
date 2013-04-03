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
	
	updateComments: function(data) {
		if (data) {
			if (typeof(wykiwygs) !== 'undefined') {
				if (wykiwygs.length) {
					for (i=0; i<wykiwygs.length; i++)
					{
						wykiwygs[i].t.value = '';
						wykiwygs[i].e.body.innerHTML = '';
					}
				}
			}
			$('input[type=file]').val('');

			$($('#comments-container ol.comments')[0]).replaceWith(data);
		}
	},
	
	initialize: function() {
		var $ = this.jQuery;

		if ($('#comments-container').length > 0) {
			var limit = parseInt($('#limit').val()),
				start = 0; // + limit,
				url = $('#comments-container').attr('data-action') + '?no_html=1&limit=0&start=';

			if ($('#commentform').length > 0) {
				//$('#commentform').on('submit', function(e) {  !! This line breaks the WYSIWYG editor's ability to do a final conversion before form submission
				$('<iframe src="about:blank?nocache=' + Math.random() + '" id="upload_target" name="upload_target" style="display:none;"></iframe>').appendTo($('#comments-container')); //width:0px;height:0px;border:0px solid #fff;

				var act = $('#commentform').attr('action');
				$('#commentform')
					.attr('target', 'upload_target')
					.attr('action', act.nohtml());
			}

			// Attach a click event for Iframe file upload
			$('#comments-container').on('click', 'input[type=submit]', function (e) {
				var frm = $($(this).closest('form')),
					id = frm.attr('id') + '-iframe';

				$('<iframe src="about:blank?nocache=' + Math.random() + '" id="' + id + '" name="' + id + '" style="display:none;"></iframe>').appendTo(frm.parent());

				frm.attr('target', id)
					.attr('action', frm.attr('action').nohtml());
			});

			$('#comments-container').on('click', '.reply', function (e) {
				e.preventDefault();
				var frm = '#' + $(this).attr('rel');
				if ($(frm).hasClass('hide')) {
					$(frm).removeClass('hide');
				} else {
					$(frm).addClass('hide');
				}
			});
			$('#comments-container').on('click', '.cancelreply', function (e) {
				e.preventDefault();
				$(this).closest('.comment-add').addClass('hide');
			});
			$('#comments-container').on('click', '.delete', function (e) {
				var res = confirm('Are you sure you wish to delete this item?');
				if (!res) {
					e.preventDefault();
				}
				return res;
			});

			$('#comments-container .list-footer')
				.css('display', 'none')
				.after('<a id="loadmore" href="' + url + start + '">Show more comments</a>');

			$('#loadmore').on('click', function(e){
					e.preventDefault();
					$('#limit').val('0');
					//console.log('click!');
					$.get(url + start, {}, function(data) {
						start += limit;

						$('#comments-container ol.comments').replaceWith(data);
						$('#loadmore').hide();
					});
				});
		} else {
			$('a.delete').each(function(i, el) {
				$(el).on('click', function(e) {
					var res = confirm('Are you sure you wish to delete this item?');
					if (!res) {
						e.preventDefault();
					}
					return res;
				});
			});
			$('.reply').each(function(i, item) {
				$(item).click(function (e) {
					e.preventDefault();
					var frm = '#' + $(this).attr('rel');
					if ($(frm).hasClass('hide')) {
						$(frm).removeClass('hide');
					} else {
						$(frm).addClass('hide');
					}
				});
			});
			$('.cancelreply').each(function(i, item) {
				$(item).click(function (e) {
					e.preventDefault();
					$(this).closest('.comment-add').addClass('hide');
				});
			});
		}
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.CoursesForum.initialize();
});