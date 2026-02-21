/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Tag Browser
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.TagBrowser = {

	jQuery: jq,

	settings: {
	},

	col1active: '',
	col2active: '',
	baseURI: '/index.php?option=com_resources&task=browser&no_html=1',

	nextLevel: function(type, input, input2, level, id, rid, moveFocusToResult) {
		var com = this,
			$ = this.jQuery,
			sortby = '',
			filterby = '';

		if (level == 2) {
			if (HUB.TagBrowser.col2active != '' && $(HUB.TagBrowser.col2active)) {
				$(HUB.TagBrowser.col2active).removeClass('open');
				$(HUB.TagBrowser.col2active).closest('[role="option"]').attr('aria-selected', 'false');
			}
			var curractive = $('#'+id);
			curractive.addClass('open');
			curractive.closest('[role="option"]').attr('aria-selected', 'true');
			HUB.TagBrowser.col2active = '#'+id;
			// Reset column 3 since column 2 is being reloaded
			HUB.TagBrowser.resetLevel3();
		} else {
			if (HUB.TagBrowser.col1active!='') {
				var prevactive = $(HUB.TagBrowser.col1active);
				if (prevactive) {
					prevactive.removeClass('open');
					prevactive.closest('[role="option"]').attr('aria-selected', 'false');
				}
			}
			var curractive = $('#'+id);
			curractive.addClass('open');
			curractive.closest('[role="option"]').attr('aria-selected', 'true');
			HUB.TagBrowser.col1active = '#'+id;
		}

		if ($('#sortby').length > 0) {
			sortby = $('#sortby').val();
		}

		var frm = document.getElementById('tagBrowserForm');
		if (frm && frm.filter) {
			for (var i=0; i < frm.filter.length; i++){
				if (frm.filter[i].checked) {
					filterby += '&filter[]='+frm.filter[i].value;
				}
			}
		}

		$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level='+level+'&input='+input+'&input2='+input2+'&id='+rid+'&sortby='+sortby+filterby, {}, function(data) {
			$('#level-'+level).html(data);

			// Rebind events on new content
			HUB.TagBrowser.bindClickHandlers($('#level-'+level));

			// Only move focus to the new column if explicitly requested (e.g. Right arrow)
			if (moveFocusToResult) {
				var firstLink = $('#level-'+level).find('ul a[data-level]').first();
				if (firstLink.length > 0) {
					firstLink.addClass('open');
					firstLink.closest('[role="option"]').attr('aria-selected', 'true');
					firstLink.focus();

					// Auto-activate: load the next column for the focused item
					HUB.TagBrowser.nextLevel(
						firstLink.data('type'),
						firstLink.data('input'),
						firstLink.data('input2'),
						firstLink.data('level'),
						firstLink.data('col'),
						firstLink.data('rid')
					);
				} else {
					// No drill-down links (e.g. column 3 info) — focus first link
					var anyLink = $('#level-'+level).find('ul a').first();
					if (anyLink.length > 0) {
						anyLink.focus();
					}
				}
			}

			if ($('#rid').length > 0) {
				var r = $('#rid').val();
				if ($('#col2_'+r).length > 0) {
					$('#col2_'+r).addClass('open');
				}
			}
		});
	},

	changeSort: function() {
		var com = this,
			$ = this.jQuery,
			p = null;

		var type = $('#pretype').val(),
			k = $('#preinput2').val();

		$("#level-1 .open").each(function(i, el) {
			p = $(el).attr('id');
		});
		var i = p.replace('col1_', '');
		i = (i == 'all') ? '' : i;

		HUB.TagBrowser.nextLevel(type, i, k, 2, p, 0);
	},

	sc: 0,

	setScroll: function() {
		var com = this,
			$ = this.jQuery;

		if ($('#d').length > 0) {
			atg = $('#atg').val();
			if ($("#col1_"+atg).length > 0) {
				var dist = $("#col1_" + atg).offsetHeight;
				$("#ultags").scrollTop = ((dist * $('#d').val()) - dist);
				clearTimeout(HUB.TagBrowser.sc);
			}
		}
	},

	// Reset downstream columns to their initial empty state
	resetLevel2: function() {
		var $ = this.jQuery;
		$('#level-2').html(
			'<h3>Resources</h3>' +
			'<ul id="ulitems" role="listbox" tabindex="0" aria-label="Resources"><li><span>Select a tag to see resources.</span></li></ul>'
		);
		HUB.TagBrowser.resetLevel3();
	},

	resetLevel3: function() {
		var $ = this.jQuery;
		$('#level-3').html(
			'<h3>Info</h3>' +
			'<ul id="ulinfo" tabindex="0" aria-label="Resource details"><li>Select a resource to see details.</li></ul>'
		);
	},

	// Bind click handlers to data-attribute links (replaces javascript: hrefs)
	bindClickHandlers: function(container) {
		var $ = this.jQuery;

		container.find('a[data-level]').off('click.tagbrowser').on('click.tagbrowser', function(e) {
			e.preventDefault();
			var el = $(this);
			HUB.TagBrowser.nextLevel(
				el.data('type'),
				el.data('input'),
				el.data('input2'),
				el.data('level'),
				el.data('col'),
				el.data('rid')
			);
		});

		// Bind sort select change event
		container.find('#sortby').off('change.tagbrowser').on('change.tagbrowser', function() {
			HUB.TagBrowser.changeSort();
		});

		// Bind filter checkbox change events
		container.find('input[name="filter"]').off('change.tagbrowser').on('change.tagbrowser', function() {
			HUB.TagBrowser.changeSort();
		});
	},

	// Keyboard navigation within the tag browser
	handleKeyboard: function(e) {
		var $ = HUB.TagBrowser.jQuery,
			target = $(e.target),
			key = e.which || e.keyCode;

		// Only handle keys on links within the tag browser lists
		if (!target.is('#tagbrowser ul a')) {
			return;
		}

		var currentLi = target.closest('li'),
			list = target.closest('ul'),
			levelDiv = target.closest('[id^="level-"]'),
			levelNum = parseInt(levelDiv.attr('id').replace('level-', ''), 10);

		// Helper: move focus, highlight, and auto-activate (load next column without moving to it)
		var moveFocus = function(link) {
			// Move .open class from previous item to the new one within this column
			levelDiv.find('ul a.open').removeClass('open');
			levelDiv.find('[role="option"]').attr('aria-selected', 'false');
			link.addClass('open');
			link.closest('[role="option"]').attr('aria-selected', 'true');
			link.focus();

			// Auto-activate: load the next column for this item (without moving focus)
			if (link.is('a[data-level]')) {
				// Reset columns further downstream before loading the next one
				if (levelNum === 1) {
					HUB.TagBrowser.resetLevel3();
				}
				HUB.TagBrowser.nextLevel(
					link.data('type'),
					link.data('input'),
					link.data('input2'),
					link.data('level'),
					link.data('col'),
					link.data('rid')
				);
			}
		};

		switch (key) {
			case 38: // Up arrow
				e.preventDefault();
				var prevLi = currentLi.prev('li');
				if (prevLi.length > 0) {
					var prevLink = prevLi.find('a').first();
					if (prevLink.length > 0) {
						moveFocus(prevLink);
					}
				}
				break;

			case 40: // Down arrow
				e.preventDefault();
				var nextLi = currentLi.next('li');
				if (nextLi.length > 0) {
					var nextLink = nextLi.find('a').first();
					if (nextLink.length > 0) {
						moveFocus(nextLink);
					}
				}
				break;

			case 39: // Right arrow - activate current item and move to next column
				e.preventDefault();
				if (levelNum <= 2 && target.is('a[data-level]')) {
					HUB.TagBrowser.nextLevel(
						target.data('type'),
						target.data('input'),
						target.data('input2'),
						target.data('level'),
						target.data('col'),
						target.data('rid'),
						true // move focus to the new column
					);
				}
				break;

			case 37: // Left arrow - move to previous column
				e.preventDefault();
				var prevLevel = levelNum - 1;
				if (prevLevel >= 1) {
					// Focus the currently active item in the previous column, or the first item
					var prevCol = $('#level-' + prevLevel).find('ul a.open');
					if (prevCol.length === 0) {
						prevCol = $('#level-' + prevLevel).find('ul a').first();
					}
					if (prevCol.length > 0) {
						prevCol.focus();
					}
				}
				break;

			case 32: // Space - activate link (Enter already works natively)
				e.preventDefault();
				target.trigger('click');
				break;

			case 36: // Home - go to first item in list
				e.preventDefault();
				var firstLink = list.find('a').first();
				if (firstLink.length > 0) {
					moveFocus(firstLink);
				}
				break;

			case 35: // End - go to last item in list
				e.preventDefault();
				var lastLink = list.find('a').last();
				if (lastLink.length > 0) {
					moveFocus(lastLink);
				}
				break;
		}
	},

	initialize: function() {
		var com = this,
			$ = this.jQuery,
			browser = $('#tagbrowser'),
			imgpath = browser.attr('data-loader');

		if (browser.length <= 0) {
			return;
		}

		var input  = $('#preinput').val();
		var input2 = $('#preinput2').val();
		var type   = $('#pretype').val();
		var id     = $('#id').val();

		browser.show();
		$('#tbh2').show();
		$('#viewalltools').hide();

		if (input != '') {
			HUB.TagBrowser.col2active = '#col1_'+input;
		} else {
			HUB.TagBrowser.col2active = '#col1_all';
		}

		$('<img src="'+imgpath+'" id="loading-img1" />').appendTo($('#level-1-loading'));
		$('<img src="'+imgpath+'" id="loading-img2" />').appendTo($('#level-2-loading'));

		// Bind keyboard navigation
		browser.on('keydown', HUB.TagBrowser.handleKeyboard);


		$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level=1&input='+input+'&input2='+input2+'&id='+id, {}, function(data) {
			$('#level-1').html(data);
			HUB.TagBrowser.bindClickHandlers($('#level-1'));
			HUB.TagBrowser.sc = setTimeout("HUB.TagBrowser.setScroll()", 500);
			$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level=2&input='+input+'&input2='+input2+'&id='+id, {}, function(data) {
				$('#level-2').html(data);
				HUB.TagBrowser.bindClickHandlers($('#level-2'));
			});
		});
	}
}

jQuery(document).ready(function($){
	HUB.TagBrowser.initialize();
});
