/**
 * @package     hubzero-cms
 * @file        components/com_wiki/wiki.js
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
// Resource Ranking pop-ups
//----------------------------------------------------------

HUB.Wiki = {
	getTemplate: function() {
		var id = $('templates');
		if (id.value != 'tc') {
			var hi = $(id.value).value;
			var co = $('pagetext');
			co.value = hi;
			
			var ji = $(id.value+'_tags').value;
			var jo = $('actags');
			jo.value = ji;

			if ($('maininput-actags') && jo) {
				var ul = $($('maininput-actags').getParent().getParent());
				var label = $($('maininput-actags').getParent().getParent().getParent());
				label.removeChild(ul);
				
				var actags = new AppleboxList(jo, {'hideempty': false, 'resizable': {'step': 8}});

				var actkn = '';
				if ($('actkn')) {
					//actkn = '&'+$('actkn').value+'=1';
					actkn = '&admin=true';
				}

				var completer2 = new Autocompleter.MultiSelectable.Ajax.Json($('maininput'), '/index.php?option=com_tags&no_html=1&task=autocomplete'+actkn, {
					'tagger': actags,
					'minLength': 1, // We wait for at least one character
					'overflow': true, // Overflow for more entries
					'wrapSelectionsWithSpacesInQuotes': false
				});
			}
		} else {
			var co = $('pagetext');
			co.value = '';
		}
	},
	
	initialize: function() {
		if ($('templates')) {
			$('templates').addEvent('change', HUB.Wiki.getTemplate);
		}
		
		var mode = $('params_mode');
		if (mode) {
			mode.addEvent('change', HUB.Wiki.checkMode);
		}
	},

	checkMode: function() {
		var mode = $('params_mode');
		if (mode.value != 'knol') {
			$($('params_authors').parentNode).addClass('hide');
			$($('params_hide_authors').parentNode).addClass('hide');
			$($('params_allow_changes').parentNode).addClass('hide');
			$($('params_allow_comments').parentNode).addClass('hide');
		} else {
			if ($($('params_authors').parentNode).hasClass('hide')) {
				$($('params_authors').parentNode).removeClass('hide');
			}
			if ($($('params_hide_authors').parentNode).hasClass('hide')) {
				$($('params_hide_authors').parentNode).removeClass('hide');
			}
			if ($($('params_allow_changes').parentNode).hasClass('hide')) {
				$($('params_allow_changes').parentNode).removeClass('hide');
			}
			if ($($('params_allow_comments').parentNode).hasClass('hide')) {
				$($('params_allow_comments').parentNode).removeClass('hide');
			}
		}
	}
}

window.addEvent('domready', HUB.Wiki.initialize);

