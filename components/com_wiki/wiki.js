/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
			
			if ($('maininput') && jo) {
				var ul = $($('maininput').getParent().getParent());
				var label = $($('maininput').getParent().getParent().getParent());
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
			$($('params_allow_changes').parentNode).addClass('hide');
			$($('params_allow_comments').parentNode).addClass('hide');
		} else {
			if ($($('params_authors').parentNode).hasClass('hide')) {
				$($('params_authors').parentNode).removeClass('hide');
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
