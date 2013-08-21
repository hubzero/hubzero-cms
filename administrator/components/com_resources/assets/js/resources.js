/**
 * @package     hubzero-cms
 * @file        administrator/components/com_resources/resources.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
/*
//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------

HUB.Resources = {
	removeAuthor: function(el) {
		$(el).parentNode.parentNode.removeChild($(el).parentNode);
		
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

		if (!authid.value) {
			return;
		}
		
		var selectedr = $('authrole').selectedIndex;
		var selectedRole = $('authrole').options[selectedr].value;
		var selectedId = authid.value.split(' ').join('_');

		// create the LI element and attach it to the UL
		var newlistitem = new Element('li',{
			'id': 'author_' + selectedId
		});

		authorlist.appendChild(newlistitem);

		var myAjax = new Ajax('index.php?option=com_resources&controller=items&task=author&no_html=1&u='+selectedId+'&role='+selectedRole+'&rid='+$('id').value,{
			update:'author_' + selectedId
		}).request();
		myAjax.addEvent('onComplete', function(){
			var id = selectedId;
			if ($$('#author_' + selectedId + ' .authid')) {
				$$('#author_' + selectedId + ' .authid').each(function(el){
					id = $(el).value;
				});
			}
			$('author_' + selectedId).setProperty('id', 'author_' + id);
			// re-apply the sorting script so the new LIst item becoems sortable
			authsorts.reinitialize();

			// get the new serials
			$('new_authors').value = authsorts.serialize('', function(element, index){
				return element.getProperty('id').replace('author_','');
			});
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
*/
