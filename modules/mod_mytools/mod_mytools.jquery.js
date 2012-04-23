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
HUB.Modules.MyTools	 = {

	jQuery: $,

	settings: { 
		mouseOverClass:	'over',
		ajax: {
			enabled: false,
			url: '/index.php?option=com_members&active=dashboard&no_html=1&init=1&action=',
			options: {
				method:'get'
			},
			loadingText: 'Loading...'
		},
		titles: '#myToolsTabs ul.tab_titles li',
		container: '#myToolsTabs',
		panels: '#myToolsTabs .tab_panel',
		favs: '.fav',
		favd: '.favd'
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
		
		$(settings.favs).each(function(i, item) {
			$(item).toggle(function (e) {
				e.preventDefault();
				if ($(this).parent().hasClass('favd')) {
					$(this).parent().removeClass('favd');
				} else {
					$(this).parent().addClass('favd');
				}
				mod.updateFavs();
                return false;
			},function (e) {
                e.preventDefault();
				if ($(this).parent().hasClass('favd')) {
					$(this).parent().removeClass('favd');
				} else {
					$(this).parent().addClass('favd');
				}
				mod.updateFavs();
                return false;
            })
		});
	},

	updateFavs: function() {
		var mod = this,
			$ = this.jQuery,
			settings = this.settings,
			f = [],
			uid = $('#uid').val(),
			id = $($(settings.container).parent().parent().parent()).attr('id').replace('mod_','');
		
		$(settings.favd).each(function(i, elm) {
			f.push($(elm).attr('id'));
		});
		
		$.get(settings.ajax.url + 'saveparams&id='+id+'&uid='+uid, {'params[myhub_favs]': f.join(',')});
		$.get(settings.ajax.url + 'refresh&id='+id+'&uid='+uid+'&fav='+f.join(','), {}, function(data) {
            $('#favtools').html(data);
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
	}
		
};

jQuery(document).ready(function($){
	HUB.Modules.MyTools.initialize();
});