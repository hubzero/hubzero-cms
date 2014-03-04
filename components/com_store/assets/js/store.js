/**
 * @package     hubzero-cms
 * @file        components/com_store/store.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Store js
//----------------------------------------------------------
HUB.Store = {
	form: 'myCart',

	hubform: 'hubForm',

	initialize: function() {
		if ($('updatecart')) {
			$('updatecart').addEvent('click', function(){
					var form = document.getElementById(HUB.Store.form);
					form.task.value = 'cart';
					form.action.value = 'update';
					//alert(form.action.value);
					form.submit( );
					return false;
					
				}
			);
		}
		if ($('change_address')) {
			$('change_address').addEvent('click', function(){
					var form = document.getElementById(HUB.Store.hubform);
					form.task.value = 'checkout';
					form.submit( );
					return false;
					
				}
			);
		}
	},

	hide: function(obj) {
		$(obj).style.display = 'none';
	},

	show: function(obj) {
		$(obj).style.display = 'block';
	}
}


//----------------------------------------------------------

window.addEvent('domready', HUB.Store.initialize);

