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

if (!HUB) {
	var HUB = {};
}

HUB.YSearch = {
	initialize: function() {
		// Collapse nested search results on javascript-aware browsers
		document.body.getElements('.child-result').each(function(el)
		{
			el.style.display = 'none';
		});

		// Enable controls to expand nested results again
		document.body.getElements('.expand').each(function(el)
		{
			el.style.display = 'block';
			el.addEvent('click', function()
			{
				var list = el.parentNode.nextSibling;
				while(list.tagName != 'UL')
					list = list.nextSibling;
				if (list.style.display == 'block')
				{
					list.style.display = 'none';
					el.style.background = 'url(\'/components/com_ysearch/expand.gif\') no-repeat 0 0';
				}
				else
				{
					list.style.display = 'block';
					el.style.background = 'url(\'/components/com_ysearch/expand.gif\') no-repeat -20px 0';
				}
			});
		});

		// Enable auto-submit of per-page setting form by ...
		//  ... hiding the submit button
		document.body.getElements('.search-per-page-submitter').each(function(el) { el.style.display = 'none'; });
		//  ... and making the select element submit its parent form on change
		document.body.getElements('.search-per-page-selector').each(function(el) 
		{
			el.addEvent('change', function()
			{
				var par = el.parentNode;
				while (par.tagName != 'FORM')
					par = par.parentNode;
				par.submit();
			});
		});
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.YSearch.initialize);
