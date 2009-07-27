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

//----------------------------------------------------------
// Store js
//----------------------------------------------------------
HUB.Store = {
	form: 'myCart',
	hubform: 'hubForm',
	
	initialize: function() {

		if($('updatecart')) {
			
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
		if($('change_address')) {
			
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
