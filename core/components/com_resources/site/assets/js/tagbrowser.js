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

	// Announce status messages to screen readers via the live region
	announce: function(message) {
		var $ = this.jQuery;
		var region = $('#tagbrowser-status');
		if (region.length > 0) {
			region.text(message);
		}
	},

	nextLevel: function(type, input, input2, level, id, rid, moveFocusToResult) {
		var com = this,
			$ = this.jQuery,
			sortby = '',
			filterby = '';

		var levelNames = {1: 'Tags', 2: 'Resources', 3: 'Info'};

		if (level == 2) {
			if (HUB.TagBrowser.col2active != '' && $(HUB.TagBrowser.col2active)) {
				$(HUB.TagBrowser.col2active).removeClass('open');
			}
			var curractive = $('#'+id);
			curractive.addClass('open');
			HUB.TagBrowser.col2active = '#'+id;
			// Reset column 3 since column 2 is being reloaded
			HUB.TagBrowser.resetLevel3();
		} else {
			if (HUB.TagBrowser.col1active!='') {
				var prevactive = $(HUB.TagBrowser.col1active);
				if (prevactive) {
					prevactive.removeClass('open');
				}
			}
			var curractive = $('#'+id);
			curractive.addClass('open');
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

		HUB.TagBrowser.announce('Loading ' + (levelNames[level] || '') + '…');

		$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level='+level+'&input='+input+'&input2='+input2+'&id='+rid+'&sortby='+sortby+filterby, {}, function(data) {
			$('#level-'+level).html(data);

			// Ensure listbox role is present after dynamic HTML injection
			$('#level-'+level).find('ul[id^="ul"]').each(function() {
				if (!$(this).attr('role')) {
					$(this).attr('role', 'listbox');
				}
			});

			// Rebind events on new content
			HUB.TagBrowser.bindClickHandlers($('#level-'+level));

			// Announce loaded item count to screen readers
			var itemCount = $('#level-'+level).find('ul li').length;
			HUB.TagBrowser.announce((levelNames[level] || 'Column') + ' loaded, ' + itemCount + ' item' + (itemCount !== 1 ? 's' : ''));

			// Only move focus to the new column if explicitly requested (e.g. Right arrow)
			if (moveFocusToResult) {
				HUB.TagBrowser.enterColumn(level);
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
			var atg = $('#atg').val();
			var el = document.getElementById('col1_' + atg);
			var listEl = document.getElementById('ultags');
			if (el && listEl) {
				var dist = el.offsetHeight;
				listEl.scrollTop = ((dist * $('#d').val()) - dist);
				clearTimeout(HUB.TagBrowser.sc);
			}
		}
	},

	// Reset downstream columns to their initial empty state
	resetLevel2: function() {
		var $ = this.jQuery;
		$('#level-2').html(
			'<h3>Resources</h3>' +
			'<ul id="ulitems" role="listbox" tabindex="0" aria-label="Resources"><li role="option" aria-selected="false"><span>Select a tag to see resources.</span></li></ul>'
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

		container.find('[data-level]').off('click.tagbrowser').on('click.tagbrowser', function(e) {
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

		// Keep the keyboard's idea of where it is in step with the mouse, so
		// that arrowing after a click carries on from what was clicked
		container.find('[data-level]').off('click.tagbrowserkbd').on('click.tagbrowserkbd', function() {
			var li = $(this).closest('li[role="option"]');

			if (li.length) {
				HUB.TagBrowser.setActive(li.closest('ul'), li, false);
			}
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

	// Which option in a column the keyboard is on
	//
	// The options are spans inside li[role="option"], and a span cannot take
	// focus - that is the whole reason they are spans, so that an option does
	// not contain a focusable descendant. So focus stays on the ul[role=
	// "listbox"] and the option it is on is named by aria-activedescendant,
	// which is the pattern ARIA provides for exactly this.
	//
	// Everything here used to be written against focused <a> elements. When
	// those became spans the handler kept testing for them, so from that day
	// the arrow keys did nothing at all.
	setActive: function(list, li, announce) {
		var $ = this.jQuery;

		if (!list.length || !li.length) {
			return;
		}

		list.find('li[role="option"]').attr('aria-selected', 'false');
		list.find('.hi').removeClass('hi');

		li.attr('aria-selected', 'true');
		li.children('[data-level], span').addClass('hi');

		if (li.attr('id')) {
			list.attr('aria-activedescendant', li.attr('id'));
		}

		// Into view, but without dragging the page around it
		if (li[0] && li[0].scrollIntoView) {
			li[0].scrollIntoView({ block: 'nearest', inline: 'nearest' });
		}

		if (announce) {
			HUB.TagBrowser.announce($.trim(li.text()));
		}
	},

	// The option the keyboard is on, or the chosen one, or the first
	options: function(list) {
		return list.find('li[role="option"]').not('[aria-disabled="true"]');
	},

	active: function(list) {
		var $ = this.jQuery,
			id = list.attr('aria-activedescendant'),
			options = HUB.TagBrowser.options(list);

		if (id) {
			var named = options.filter('#' + id);

			if (named.length) {
				return named;
			}
		}

		var open = options.has('.open');

		return open.length ? open.first() : options.first();
	},

	// Move the keyboard to a column and pick up where it left off there
	enterColumn: function(levelNum) {
		var $ = this.jQuery,
			list = $('#level-' + levelNum).find('ul[role="listbox"]').first();

		if (!list.length) {
			return false;
		}

		list.focus();
		HUB.TagBrowser.setActive(list, HUB.TagBrowser.active(list), true);

		return true;
	},

	// Keyboard navigation within the tag browser
	handleKeyboard: function(e) {
		var $ = HUB.TagBrowser.jQuery,
			list = $(e.target),
			key = e.which || e.keyCode;

		// Only the lists themselves take focus
		if (!list.is('#tagbrowser ul[role="listbox"]')) {
			return;
		}

		var levelDiv = list.closest('[id^="level-"]'),
			levelNum = parseInt(levelDiv.attr('id').replace('level-', ''), 10),
			options = HUB.TagBrowser.options(list),
			current = HUB.TagBrowser.active(list),
			at = options.index(current);

		if (!options.length) {
			return;
		}

		var item = current.children('[data-level]').first();

		switch (key) {
			case 38: // Up
				e.preventDefault();
				HUB.TagBrowser.setActive(list, options.eq(Math.max(0, at - 1)), true);
				break;

			case 40: // Down
				e.preventDefault();
				HUB.TagBrowser.setActive(
					list,
					options.eq(Math.min(options.length - 1, at < 0 ? 0 : at + 1)),
					true
				);
				break;

			case 39: // Right - open this one and go to the column it fills
				e.preventDefault();
				if (levelNum <= 2 && item.length) {
					HUB.TagBrowser.nextLevel(
						item.data('type'),
						item.data('input'),
						item.data('input2'),
						item.data('level'),
						item.data('col'),
						item.data('rid'),
						true
					);
				}
				break;

			case 37: // Left - back to the column that filled this one
				e.preventDefault();
				if (levelNum > 1) {
					HUB.TagBrowser.enterColumn(levelNum - 1);
				}
				break;

			case 13: // Enter
			case 32: // Space
				e.preventDefault();
				if (item.length) {
					item.trigger('click');
				}
				break;

			case 36: // Home
				e.preventDefault();
				HUB.TagBrowser.setActive(list, options.first(), true);
				break;

			case 35: // End
				e.preventDefault();
				HUB.TagBrowser.setActive(list, options.last(), true);
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

		browser.addClass('tagbrowser-ready').show();
		$('#tbh2').show();
		$('#viewalltools').hide();

		// Create a live region for screen reader announcements
		if ($('#tagbrowser-status').length === 0) {
			browser.append('<div id="tagbrowser-status" role="status" aria-live="polite" aria-atomic="true" class="sr-only visually-hidden"></div>');
		}

		if (input != '') {
			HUB.TagBrowser.col2active = '#col1_'+input;
		} else {
			HUB.TagBrowser.col2active = '#col1_all';
		}

		$('<img src="'+imgpath+'" id="loading-img1" alt="Loading tags…" />').appendTo($('#level-1-loading'));
		$('<img src="'+imgpath+'" id="loading-img2" alt="Loading resources…" />').appendTo($('#level-2-loading'));

		// Bind keyboard navigation
		browser.on('keydown', HUB.TagBrowser.handleKeyboard);

		// Arriving at a column by Tab, with nothing chosen in it yet: put the
		// keyboard on something, or the first arrow press has nowhere to go
		// from and the reader is told nothing about where it has landed.
		browser.on('focusin', 'ul[role="listbox"]', function() {
			var list = $(this);

			if (!list.attr('aria-activedescendant')) {
				HUB.TagBrowser.setActive(list, HUB.TagBrowser.active(list), true);
			}
		});

		$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level=1&input='+input+'&input2='+input2+'&id='+id, {}, function(data) {
			$('#level-1').html(data);
			$('#level-1').find('ul[id^="ul"]').each(function() {
				if (!$(this).attr('role')) { $(this).attr('role', 'listbox'); }
			});
			HUB.TagBrowser.bindClickHandlers($('#level-1'));
			HUB.TagBrowser.sc = setTimeout(function() { HUB.TagBrowser.setScroll(); }, 500);
			$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level=2&input='+input+'&input2='+input2+'&id='+id, {}, function(data) {
				$('#level-2').html(data);
				$('#level-2').find('ul[id^="ul"]').each(function() {
					if (!$(this).attr('role')) { $(this).attr('role', 'listbox'); }
				});
				HUB.TagBrowser.bindClickHandlers($('#level-2'));
			});
		});
	}
}

jQuery(document).ready(function($){
	HUB.TagBrowser.initialize();
});
