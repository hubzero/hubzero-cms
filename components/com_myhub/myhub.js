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
// My Hub (singleton)
//-------------------------------------------------------------

HUB.Myhub = {
	baseURL: 'index.php?option=com_myhub&no_html=1',
	
	removeModule: function(el) {
		// remove the list item from its parent list
		el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);
		
		// get the new serials
		var ids = HUB.Sorts.serialize('', function(element, index){
			return element.getProperty('id').replace('mod_','');
		}).join(';');
	
		var uid = $('uid').value;
		$('serials').value = ids;

		// AJAX: send the serials to the server-side script so it can update the list of available modules
		var myAjax = new Ajax(HUB.Myhub.baseURL+'&task=rebuild&uid='+uid+'&ids='+ids,{update:'available'}).request();

		return false;
	},

	addModule: function(id) {
		if(id) {
			// create and attach a new LIst item to a list
			modList = $('sortcol_1');
			modItem = new Element('div',{
				id: 'mod_'+id
			})
			modItem.addClass('draggable').injectInside(modList);
			
			// AJAX: send request to server-side script to generate and return contents of new LIst item
			var uid = $('uid').value;
			var myAjax1 = new Ajax(HUB.Myhub.baseURL+'&task=addmodule&uid='+uid+'&id='+id,{update:modItem.id}).request();
			
			// re-apply the sorting script so the new LIst item becoems sortable
			HUB.Sorts.reinitialize();
			
			// get the new serials
			var ids = HUB.Sorts.serialize('', function(element, index){
				return element.getProperty('id').replace('mod_','');
			}).join(';');
			$('serials').value = ids;
			
			// AJAX: send the serials to the server-side script so it can update the list of available modules
			var myAjax2 = new Ajax(HUB.Myhub.baseURL+'&task=rebuild&uid='+uid+'&ids='+ids,{update:'available'}).request();
		}
	},
	
	editModule: function(el, id) {
		f = $(id);
		// toggle display of editable items
		if(f.style.display == 'none' || f.style.display == '') {
			f.style.display = 'block';
			el.innerHTML = 'close edit';
		} else {
			f.style.display = 'none';
			el.innerHTML = 'edit';
		}
		return false;
	},
	
	saveModule: function(theForm, id) {
		var uid = $('uid').value;

		allNodes = Form.serialize(theForm);
	
		// AJAX: send request to server-side script to save all the user's settings
		var myAjax1 = new Ajax(HUB.Myhub.baseURL+'&task=saveparams&id='+id+'&uid='+uid+'&'+allNodes,{update:'mod_'+id}).request();

		// turn off/hide the editing features
		HUB.Myhub.editModule($('e_'+id), 'f_'+id);
		
		// AJAX: send request to server-side script to generate and return contents of updated module
		var myAjax2 = new Ajax(HUB.Myhub.baseURL+'&task=refresh&id='+id,{update:'mod_'+id}).request();
		return false;
	},
	
	_setHandles: function(w) {
		var v = w ? 'move' : 'auto';
		var handles = document.getElementsByClassName('handle');
		for (var i=0; i < handles.length; i++)
		{
			handles[i].style.cursor = v;
		}
	}
};

// a global variable to hold our sortable object
// done so the Myhub singleton can access the sortable object easily
HUB.Sorts = null;

window.addEvent('domready', function(){
	document.ondragstart = function() { return false; };
	
	HUB.Sorts = new xSortables(['sortcol_0', 'sortcol_1', 'sortcol_2'], {handle:'h3[class=handle]',onComplete:function() {
		var ids = this.serialize('', function(element, index){
			return element.getProperty('id').replace('mod_','');
		}).join(';');
		$('serials').value = ids;
		var uid = $('uid').value;
		var myAjax = new Ajax(HUB.Myhub.baseURL+'&task=save&uid='+uid+'&ids='+ids).request();
	}});
	HUB.Myhub._setHandles(1);
});
