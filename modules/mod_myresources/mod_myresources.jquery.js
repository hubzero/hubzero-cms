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
	var HUB = {
		Modules: {}
	};
} else if (!HUB.Modules) {
	HUB.Modules = {};
}

HUB.Modules.MyResources = {
	
	baseURL: '/index.php?option=com_myhub&no_html=1',
	
	initialize: function() {
		var com = this;
		
		this.form = $('#myresources-form');
		this.sort = $('#myresources-sort');
		this.limit = $('#myresources-limit');
		
		this.container = $($(this.form).parent().parent().parent());
		this.id = $(this.container).attr('id').replace('mod_','');
		
		// AJAX: send request to server-side script to generate and return contents of new LIst item
		this.uid = $('#uid').val();

		$(this.sort).change(function() {
			allNodes = $(com.form).serialize();
			
			$.get(HUB.Modules.MyResources.baseURL+'&task=saveparams&update=1&id='+com.id+'&uid='+com.uid+'&'+allNodes, {}, function(data) {
	            $('#myresources-content').html(data);
			});
		});
		
		$(this.limit).change(function() {
			allNodes = $(com.form).serialize();

			$.get(HUB.Modules.MyResources.baseURL+'&task=saveparams&update=1&id='+com.id+'&uid='+com.uid+'&'+allNodes, {}, function(data) {
	            $('#myresources-content').html(data);
			});
		});
	}
	
};

jQuery(document).ready(function($){
	HUB.Modules.MyResources.initialize();
});

