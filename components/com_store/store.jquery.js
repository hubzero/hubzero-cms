/**
 * @package     hubzero-cms
 * @file        components/com_store/store.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Store js
//----------------------------------------------------------
HUB.Store = {
	form: 'myCart',
	hubform: 'hubForm',
	
	initialize: function() {
		if ($('#updatecart')) {
			$('#updatecart').bind('click', function(){					
				var form = document.getElementById(HUB.Store.form);
				form.task.value = 'cart';
				form.action.value = 'update';
				form.submit( );
				return false;
			});
		}
		if ($('#change_address')) {
			$('#change_address').bind('click', function(){	
				var form = document.getElementById(HUB.Store.hubform);
				form.task.value = 'checkout';
				form.submit( );
				return false;
			});	
		}
	},
	
			
	hide: function(obj) {
		$(obj).hide();
	},
	
	show: function(obj) {
		$(obj).show();
	}
}


jQuery(document).ready(function($){
	HUB.Store.initialize();
});

