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
if (!HUB.Modules) {
	HUB.Modules = {};
}

HUB.Modules.MyResources = {
	baseURL: 'index.php?option=com_myhub&no_html=1',
	
	initialize: function() {
		this.form = $('myresources-form');
		this.sort = $('myresources-sort');
		this.limit = $('myresources-limit');
		
		this.container = $(this.form.getParent().getParent().getParent());
		this.id = this.container.getProperty('id').replace('mod_','');
		
		// AJAX: send request to server-side script to generate and return contents of new LIst item
		this.uid = $('uid').value;

		this.sort.addEvent('change', function() {
			//var myAjax1 = new Ajax(HUB.Myhub.baseURL+'&task=addmodule&uid='+uid+'&id='+id,{update:'myresources-content'}).request();
			//HUB.Myhub.saveModule(this.form, this.id);
			allNodes = this.form.toQueryString();
			//var myAjax1 = new Ajax(HUB.Modules.MyResources.baseURL+'&task=saveparams&update=1&id='+id+'&uid='+uid+'&'+allNodes,{update:'myresources-content'}).request();
			var myAjax1 = new Ajax(
				HUB.Modules.MyResources.baseURL+'&task=saveparams&update=1&id='+this.id+'&uid='+this.uid+'&'+allNodes,
				{update:'myresources-content'}
			).request();
		}.bind(this));
			
		this.limit.addEvent('change', function() {
			//var myAjax1 = new Ajax(HUB.Myhub.baseURL+'&task=addmodule&uid='+uid+'&id='+id,{update:'myresources-content'}).request();
			//HUB.Myhub.saveModule(this.form, this.id);
			allNodes = this.form.toQueryString();
			var myAjax1 = new Ajax(
				HUB.Modules.MyResources.baseURL+'&task=saveparams&update=1&id='+this.id+'&uid='+this.uid+'&'+allNodes,
				{update:'myresources-content'}
			).request();
		}.bind(this));
	}
};

//----------------------------------------------------------

window.addEvent('domready', HUB.Modules.MyResources.initialize);
