/**
 * @package     hubzero-cms
 * @file        plugins/members/dashboard/dashboard.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	baseURL: '/index.php?option=com_members&active=dashboard&no_html=1&init=1',
	
	initialize: function() {
		// Add the close module button
		$$('div.cwrap').each(function(el) {
			chref = new Element('a',{
				href: '#',
				title: 'Remove this module'
			}).addEvent('click', function(e){ 
				HUB.Myhub.removeModule(this); 
				e.preventDefault();
			});
			chref.addClass('close');
			chref.injectTop(el);
		});
		
		// Set the drag handle cursor style
		$$('h3.handle').each(function(el) {
			el.addClass('movable');
		});
		
		// Make the modules sortable
		document.ondragstart = function() { return false; };

		HUB.Sorts = new xSortables(['sortcol_0', 'sortcol_1', 'sortcol_2'], {handle:'h3.handle',onComplete:function() {
			var ids = this.serialize('', function(element, index){
				return element.getProperty('id').replace('mod_','');
			}).join(';');
			$('serials').value = ids;
			var uid = $('uid').value;
			var myAjax = new Ajax(HUB.Myhub.baseURL+'&id='+uid+'&action=save&mids='+ids).request();
		}});
	},
	
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
		var myAjax = new Ajax(HUB.Myhub.baseURL+'&action=rebuild&id='+uid+'&mids='+ids,{update:'available'}).request();

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
			var myAjax1 = new Ajax(HUB.Myhub.baseURL+'&action=addmodule&id='+uid+'&mid='+id,{
				update: modItem.id,
				evalScripts: true
			}).request();
			myAjax1.addEvent('onComplete', function(){
				$$('#mod_'+id+' .cwrap').each(function(el) {
					chref = new Element('a',{
						href: '#',
						title: 'Remove this module'
					}).addEvent('click', function(e){ 
						HUB.Myhub.removeModule(this); 
						e.preventDefault();
					});
					chref.addClass('close');
					chref.injectTop(el);
				});
				$$('#mod_'+id+' h3.handle').each(function(el) {
					el.addClass('movable');
				});
				// re-apply the sorting script so the new LIst item becoems sortable
				HUB.Sorts.reinitialize(); 
				
				// get the new serials
				var ids = HUB.Sorts.serialize('', function(element, index){
					return element.getProperty('id').replace('mod_','');
				}).join(';');
				$('serials').value = ids;

				// AJAX: send the serials to the server-side script so it can update the list of available modules
				var myAjax2 = new Ajax(HUB.Myhub.baseURL+'&action=rebuild&id='+uid+'&mids='+ids,{update:'available'}).request();
			});
		}
	},
	
	editModule: function(el, id) {
		f = $(id);
		// toggle display of editable items
		if (f.style.display == 'none' || f.style.display == '') {
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
		var myAjax1 = new Ajax(HUB.Myhub.baseURL+'&action=saveparams&mid='+id+'&id='+uid+'&'+allNodes,{update:'mod_'+id}).request();

		// turn off/hide the editing features
		HUB.Myhub.editModule($('e_'+id), 'f_'+id);
		
		// AJAX: send request to server-side script to generate and return contents of updated module
		var myAjax2 = new Ajax(HUB.Myhub.baseURL+'&action=refresh&mid='+id,{update:'mod_'+id}).request();
		return false;
	}
};

// a global variable to hold our sortable object
// done so the Myhub singleton can access the sortable object easily
HUB.Sorts = null;

window.addEvent('domready', HUB.Myhub.initialize);

