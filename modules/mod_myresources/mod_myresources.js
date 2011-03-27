/**
 * @package     hubzero-cms
 * @file        modules/mod_myresources/mod_myresources.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

