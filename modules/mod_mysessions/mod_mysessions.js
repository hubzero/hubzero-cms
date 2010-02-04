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

//-------------------------------------------------------------
// My sessions module
//-------------------------------------------------------------

var MySessionsTabs = new Class({
	
	initialize: function(element, options) {
		this.options = Object.extend({
			changeTransition:	Fx.Transitions.Bounce.easeOut,
			duration:			1000,
			mouseOverClass:		'active',
			activateOnLoad:		'first'
		}, options || {});
		
		this.el = $(element);
		this.elid = element;
		
		this.titles = $$('#' + this.elid + ' ul.session_tab_titles li');
		this.panels = $$('#' + this.elid + ' .session_tab_panel');
		
		this.titles.each(function(item) {
			item.addEvent('click', function(){
					item.removeClass(this.options.mouseOverClass);
					this.activate(item);
				}.bind(this)
			);
			
			item.addEvent('mouseover', function() {
				if (item != this.activeTitle) {
					item.addClass(this.options.mouseOverClass);
				}
			}.bind(this));
			
			item.addEvent('mouseout', function() {
				if (item != this.activeTitle) {
					item.removeClass(this.options.mouseOverClass);
				}
			}.bind(this));
		}.bind(this));
	},
	
	activate: function(tab) {
		if ($type(tab) == 'string') {
			myTab = $$('#' + this.elid + ' ul li').filterByAttribute('title', '=', tab)[0];
			tab = myTab;
		}
		
		if ($type(tab) == 'element') {
			var newTab = tab.getProperty('title');
			this.panels.removeClass('active');
			this.activePanel = this.panels.filterById(newTab)[0];
			this.activePanel.addClass('active');
			this.titles.removeClass('active');
			
			tab.addClass('active');
			
			this.activeTitle = tab;
		}
	}
});

HUB.Mod_MySessions = {
	initialize: function() {
		HUB.Mod_MySessions.diskMonitor();
		MTT = new MySessionsTabs('mySessionsTabs', {changeTransition: 'none', mouseOverClass: 'over'});
	},

	diskMonitor: function() {
		if ($('diskusage')) {
			function fetch() {			
				new Ajax('index.php?option=com_tools&task=diskusage&no_html=1&msgs=1',{
					'method' : 'get',
					'update' : $('diskusage')
				}).request();
			}

			fetch.periodical(60000);
		}
	}
}

//-------------------------------------------------------------
// Add functions to load event
//-------------------------------------------------------------

window.addEvent('domready', HUB.Mod_MySessions.initialize);
