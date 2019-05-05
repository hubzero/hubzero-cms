/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

jQuery(document).ready(function(jq){
	var $ = jq,
		pc = $('#page_container');

	if (pc.length) {
		$('#page_menu a.guide').fancybox({
			type: 'ajax',
			width: '100%',
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			closeBtn: false,
			closeClick: true,
			topRatio: 0,
			tpl: {
				wrap:'<div class="fancybox-wrap" id="guide-content"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>'
			},
			beforeLoad: function() {
				this.href = this.element.attr('href').nohtml();
			},
			beforeShow: function() {
				var guide = $('div.guide-content'),
					top = $('#page_container').offset().top;
				if (guide.length > 0) {
					guide.css('margin-top', (top - 86) + 'px'); /* 15 20 50 */
				}
			},
			afterShow: function() {
				$('#guide-content').css('position', 'absolute');
				$('html').removeClass('fancybox-lock');
			},
			onUpdate: function() {
				$('#guide-content').css('position', 'absolute');
			},
			helpers: {
				overlay: {
					css: {position: 'absolute', height: $('body').height() }
				}
			}
		});

		if ($('#guide-overlay').length > 0) {
			$.fancybox.open(
				[{
					href: '#guide-overlay'
				}],
				{
					type: 'inline',
					width: '100%',
					height: 'auto',
					autoSize: false,
					fitToView: false,
					titleShow: false,
					closeBtn: false,
					closeClick: true,
					topRatio: 0,
					tpl: {
						wrap:'<div class="fancybox-wrap" id="guide-content"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>'
					},
					beforeShow: function() {
						var guide = $('div.guide-content'),
							top = $('#page_container').offset().top;
						if (guide.length > 0) {
							guide.css('margin-top', (top - 86) + 'px'); /* 15 20 50 */
						}
					},
					afterShow: function() {
						$('#guide-content').css('position', 'absolute');
						$('html').removeClass('fancybox-lock');
					},
					beforeClose: function() {
						$.get($('#guide-overlay').attr('data-action').nohtml(), {}, function(response){
							// Nothing to see here
							//console.log(response);
						});
					},
					onUpdate: function() {
						$('#guide-content').css('position', 'absolute');
					},
					helpers: {
						overlay: {
							css: {position: 'absolute', height: $('body').height() }
						}
					}
				}
			);
		}

		$(window).resize(function() {
			var guide = $('div.guide-content');
			if (guide.length > 0) {
				guide.css('margin-top', ($('#page_container').offset().top - 86) + 'px');
			}
		});
	}
});