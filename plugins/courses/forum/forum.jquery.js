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
	
	initialize: function() {
		var $ = this.jQuery;

		if ($('#comments-container').length > 0) {
			var limit = parseInt($('#limit').val()),
				start = 0; // + limit,
				url = $('#comments-container').attr('data-action') + '?no_html=1&limit=0&start='; //' + limit + '

			if ($('#commentform').length > 0) {
				//$('#commentform').on('submit', function(e) {  !! This line breaks the WYSIWYG editor's ability to do a final conversion before form submission
				$('#comments-container').on('submit', '#commentform', function (e) {
					e.preventDefault();
					$.post($(this).attr('action').nohtml(), $(this).serialize(), function(data) {
						if (typeof(wykiwygs) !== 'undefined') {
							console.log('editors');
							if (wykiwygs.length) 
							{
								for (i=0; i<wykiwygs.length; i++)
								{
									wykiwygs[i].t.value = '';
									wykiwygs[i].e.body.innerHTML = '';
								}
							}
						}
						else
						{
							console.log(wykiwygs);
						}
						$('#comments-container ol.comments').hide().html(data).fadeIn(500);
					});
				});
			}

			$('#comments-container').on('submit', '.comment-add form', function (e) {
				e.preventDefault();
				$.post($(this).attr('action').nohtml(), $(this).serialize(), function(data) {
					$('#comments-container ol.comments').hide().html(data).fadeIn(500);
				});
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
						//console.log($(data).eq(1).find('ol.comments').html());
						$('#comments-container ol.comments').hide().html(data).fadeIn(500);
						//$('#comments-container ol.comments').append(data);
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