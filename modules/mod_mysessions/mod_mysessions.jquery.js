/**
 * @package     hubzero-cms
 * @file        modules/mod_reportproblems/mod_reportproblems.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {
		Modules: {}
	};
} else if (!HUB.Modules) {
	HUB.Modules = {};
}

//----------------------------------------------------------
// Trouble Report form
//----------------------------------------------------------
HUB.Modules.MySessions = {

	jQuery: $,

	settings: { 
		mouseOverClass:	'over',
		titles: '#myToolsTabs ul.session_tab_titles li',
		container: '#mySessionsTabs',
		panels: '#mySessionsTabs .session_tab_panel'
	},

	initialize: function() {
		var mod = this,
			$ = this.jQuery,
			settings = this.settings;

		if (!$(settings.container)) {
			return;
		}

		$(settings.titles).each(function(i, item) {
			$(item).click(function () {
				$(this).removeClass(settings.mouseOverClass);
				mod.activate($(this));
			});
		});
	},
	
	activate: function(tab) {
		var mod = this,
			$ = this.jQuery,
			settings = this.settings;

		var newTab = $(tab).attr('title');
			
		$(settings.panels).each(function(i, item) {
			$(this).removeClass('active');
		});
		mod.activePanel = $('#'+newTab+'');
		$(mod.activePanel).addClass('active');
			
		$(settings.titles).each(function(i, item) {
			$(this).removeClass('active');
		});
		mod.activeTitle = $(tab);
		$(tab).addClass('active');
	},
	
	diskMonitor: function() {
		var mod = this,
			$ = this.jQuery,
			settings = this.settings;
			
		if ($('#diskusage')) {
			var holdTheInterval = setInterval(HUB.Modules.MySessions.fetch, 60000); 
			//fetch.periodical(60000);
		}
	},
	
	fetch: function() {
		var mod = this,
			$ = this.jQuery,
			settings = this.settings;
			
		$.get('index.php?option=com_tools&task=diskusage&no_html=1&msgs=1', {}, function(data) {
            $('#diskusage').html(data);
		});
	}
		
};

jQuery(document).ready(function($){
	HUB.Modules.MySessions.initialize();
});