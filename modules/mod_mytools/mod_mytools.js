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

var MyToolsTabs = new Class({
	
	initialize: function(element, options) {
		this.options = Object.extend({
			width:				'300px',
			height:				'200px',
			changeTransition:	Fx.Transitions.Bounce.easeOut,
			duration:			1000,
			mouseOverClass:		'active',
			activateOnLoad:		'first',
			useAjax: 			false,
			ajaxUrl: 			'',
			ajaxOptions: 		{method:'get'},
			ajaxLoadingText: 	'Loading...'
		}, options || {});
		
		this.el = $(element);
		this.elid = element;
		
		this.titles = $$('#' + this.elid + ' ul.tab_titles li');
		this.panels = $$('#' + this.elid + ' .tab_panel');
		
		this.titles.each(function(item) {
			item.addEvent('click', function(){
					item.removeClass(this.options.mouseOverClass);
					this.activate(item);
				}.bind(this)
			);
			
			item.addEvent('mouseover', function() {
				if(item != this.activeTitle)
				{
					item.addClass(this.options.mouseOverClass);
				}
			}.bind(this));
			
			item.addEvent('mouseout', function() {
				if(item != this.activeTitle)
				{
					item.removeClass(this.options.mouseOverClass);
				}
			}.bind(this));
		}.bind(this));
		
		this.favs = $$('.fav');
		this.favs.each(function(item) {
			item.addEvent('click', function(){
					this.toggle(item);
					return false;
				}.bind(this)
			);
		}.bind(this));
	},
	
	toggle: function(lnk) {
		var li = $(lnk.parentNode);
		if (li.hasClass('favd')) {
			li.removeClass('favd');
		} else {
			li.addClass('favd');
		}
		this._updateFavs();
	},
	
	_updateFavs: function() {
		var favd = $$('.favd');
		var f = [];
		for (i = 0; i < favd.length; i++) 
		{
			f.push(favd[i].id);
		}
		var fs = f.join(',');
		var uid = $('uid').value;
		var id = $(this.el.parentNode.parentNode.parentNode).getProperty('id').replace('mod_','');

		var myAjax2 = new Ajax('/index.php?option=com_members&active=dashboard&no_html=1&init=1&action=saveparams&id='+id+'&uid='+uid+'&params[myhub_favs]='+fs).request();
		var myAjax2 = new Ajax('/index.php?option=com_members&active=dashboard&no_html=1&init=1&action=refresh&id='+id+'&uid='+uid+'&fav='+fs,{update:'favtools'}).request();
	},
	
	activate: function(tab){

		if($type(tab) == 'string') 
		{
			myTab = $$('#' + this.elid + ' ul li').filterByAttribute('title', '=', tab)[0];
			tab = myTab;
		}
		
		if($type(tab) == 'element')
		{
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

function initMyToolsTabs() {
	MTT = new MyToolsTabs('myToolsTabs', {height: '27em', width: 'auto', changeTransition: 'none', mouseOverClass: 'over'});
}

window.addEvent('domready', initMyToolsTabs);
