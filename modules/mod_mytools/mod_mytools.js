/**
 * @package     hubzero-cms
 * @file        modules/mod_mytools/mod_mytools.js
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

if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Trouble Report form
//----------------------------------------------------------
HUB.Modules.MyTools	 = {

	jQuery: jq,

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
		
		HUB.Modules.MyTools.toolSearch();
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
		
		$.get(settings.ajax.url + 'saveparams&mid='+id+'&id='+uid, {'params[myhub_favs]': f.join(',')}, function(data){
			$.get(settings.ajax.url + 'refresh&mid='+id+'&id='+uid+'&fav='+f.join(','), {}, function(data) {
	            $('#favtools').html(data);
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
	
	toolSearch: function()
	{
		var $ = this.jQuery;
		
		jQuery.expr[':'].caseInsensitiveContains = function(a,i,m) {
			return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0; 
		};
		
		if( $('#filter-mytools').length )
		{
			$('#filter-mytools input').on("keyup", function(event){
				var search = $(this).val();
				if(search != '')
				{
					$("#alltools ul li a:not(:caseInsensitiveContains('"+search+"'))").parent().hide();
					$("#alltools ul li a:caseInsensitiveContains('"+search+"')").parent().show();
					
					//add no results node
					if( $("#alltools ul li:visible").length == 0 )
					{
						$("#alltools ul").append("<li id=\"none\">Sorry No Tools Matching Your Search.</li>");
					}
					
					//remove no results node if we have results
					if( $("#alltools ul li:visible").length > 1 && $("#alltools ul #none").length == 1 )
					{
						$("#alltools ul #none").remove();
					}
				}
				else
				{
					$("#alltools ul li").show();
				}
			});
		}
	}
};

jQuery(document).ready(function($){
	HUB.Modules.MyTools.initialize();
});