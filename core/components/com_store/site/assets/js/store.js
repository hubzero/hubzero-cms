/**
 * @package     hubzero-cms
 * @file        components/com_store/assets/js/store.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

jQuery(document).ready(function($){
	if ($('#updatecart')) {
		$('#updatecart').on('click', function(){
			var form = document.getElementById('myCart');
			form.task.value = 'cart';
			form.action.value = 'update';
			form.submit();
			return false;
		});
	}
	if ($('#change_address')) {
		$('#change_address').on('click', function(){
			var form = document.getElementById('hubForm');
			form.task.value = 'checkout';
			form.submit( );
			return false;
		});	
	}
});

