/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function($){
	var contents = $('#media-items'),
		layout = $('#layout');

	if (!contents.length) {
		return;
	}

	var views = $('.media-files-view');
	$('.media-files-view').on('click', function(e){
		e.preventDefault();

		views.removeClass('active');
		$('.media-files').removeClass('active');

		$(this).addClass('active');

		var view = $(this).attr('data-view');
		$('#media-' + view).addClass('active');

		layout.val(view);
	});

	contents
		.on('click', '.media-item-thumb', function(e){
			e.preventDefault();

			$(this).closest('.media-item').toggleClass('ui-selected');
		})
		.on('click', '.media-options-btn', function(e){
			e.preventDefault();

			var item = $(this).closest('.media-item');

			if (!item.hasClass('ui-activated')) {
				$('.media-item').removeClass('ui-activated');
				item.toggleClass('ui-selected');
			}

			item.toggleClass('ui-activated');
		});

		$('.media-opt-path,.media-opt-info').fancybox({
			type: 'ajax',
			width: 700,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			/*tpl: {
				wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			},*/
			beforeLoad: function() {
				$(this).attr('href', $(this).attr('href').nohtml());
			}
		});

	$('#media-tree').find('a').on('click', function(e){
		e.preventDefault();

		$.get($(this).attr('href').nohtml() + '&layout=' + layout.val(), function(data){
			contents.html(data);
		});
	});

	$('#media-tree').treeview({
		collapsed: true
	});
});
