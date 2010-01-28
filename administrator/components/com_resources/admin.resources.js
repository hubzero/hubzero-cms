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

HUB.Resources = {
	removeAuthor: function(el) {
		el.parentNode.parentNode.removeChild(el.parentNode);
		
		// get the new serials
		$('new_authors').value = authsorts.serialize('', function(element, index){
			return element.getProperty('id').replace('author_','');
		});
		
		return false;
	},
	
	addAuthor: function() {

		var authid = $('authid');
		if (!authid) {
			alert('Author select not found');
			return;
		}

		var authorlist = $('author-list');
		if (!authorlist) {
			alert('Author list not found');
			return;
		}

		/*var selected = authid.selectedIndex;
		var selectedId = authid.options[selected].value;
		var selectedText = authid.options[selected].text;*/
		if (!authid.value) {
			return;
		}
		
		var selectedr = $('authrole').selectedIndex;
		var selectedRole = $('authrole').options[selectedr].value;
		
		var selected = authid.value;
		var selectedId = authid.value;
		var selectedText = 'Author: '+selectedId;

		var role = '<select name="'+ selectedId +'_role"><option value="">Role...</option><option value="submitter"';
		if (selectedRole == 'submitter') {
			role += ' selected="selected"';
		}
		role += '>submitter</option><option value="editor"';
		if (selectedRole == 'editor') {
			role += ' selected="selected"';
		}
		role += '>editor</option></select>';
		role += '<input type="hidden" name="'+ selectedId +'_name" value="" />';

		// create the LI element and attach it to the UL
		var newlistitem = new Element('li',{'id': 'author_' + selectedId}).setHTML(
				'<span class="handle">DRAG HERE</span><span id="n_a_' + selectedId + '">' + selectedText + '</span> [ <a href="#" onclick="HUB.Resources.removeAuthor(this);return false;" title="Remove this contributor">remove</a> ]<br />Affiliation: <input type="text" name="'+ selectedId +'_organization" value="" />'+
				role
			);

		authorlist.appendChild(newlistitem);

		var myAjax = new Ajax('index.php?option=com_resources&task=getauthor&no_html=1&u='+selectedText,{update:'n_a_' + selectedId}).request();

		// re-apply the sorting script so the new LIst item becoems sortable
		authsorts.reinitialize();
		
		// get the new serials
		$('new_authors').value = authsorts.serialize('', function(element, index){
			return element.getProperty('id').replace('author_','');
		});
	}
};

// a global variable to hold our sortable object
// done so the Myhub singleton can access the sortable object easily
var authsorts;

window.addEvent('domready', function(){
	authsorts = new xSortables(['author-list'], {handle:'span[class=handle]',onComplete:function() {
		$('new_authors').value = this.serialize('', function(element, index){
			return element.getProperty('id').replace('author_','');
		});
	}});
});
