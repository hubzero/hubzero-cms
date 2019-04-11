/**
 * @package     hubzero-cms
 * @file        modules/mod_mytools/assets/js/mod_mytools.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
		mouseOverClass: 'over',
		ajax: {
			enabled: false,
			url: $('#myToolsTabs').attr('data-api'),
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
			$(item).on('click', function (e) {
				$(this).removeClass(settings.mouseOverClass);
				mod.activate($(this));
			});
		});

		$(settings.favs).each(function(i, item) {
			$(item).on('click', function (e) {
				e.preventDefault();

				if ($(this).parent().hasClass('favd')) {
					$(this).parent().removeClass('favd');
				} else {
					$(this).parent().addClass('favd');
				}
				mod.updateFavs($(this).parents('.module').first());
				return false;
			});
		});

		// move current favs to settings cont
		if ($('.mytools_favs').length) {
			// add settings pane if doesnt exist
			var settingsPaneExists = $(mod).find('.module-settings').length;
			if (settingsPaneExists == 0) {
				$('.mod_mytools').find('.module-main').prepend('<div class="module-settings"><form></form></div>');
			}

			// get favs
			var favs = $('.mytools_favs').val();

			// only have one input
			$('.mod_mytools').find('.mytools_favs').remove();
			$('.mod_mytools').find('.module-settings form').append('<input class="mytools_favs" type="hidden" name="params[favs]" value="' + favs + '" />');
		}

		// init tool search
		HUB.Modules.MyTools.toolSearch();
	},

	updateFavs: function(mod) {
		var $ = this.jQuery,
			settings = this.settings,
			f = [],
			uid = $('#uid').val();

		$(settings.favd).each(function(i, elm) {
			f.push($(elm).attr('id'));
		});

		// add settings pane if doesnt exist
		var settingsPaneExists = $(mod).find('.module-settings').length;
		if (settingsPaneExists == 0) {
			$(mod).find('.module-main').prepend('<div class="module-settings"><form></form></div>');
		}

		// only have one input
		$(mod).find('.mytools_favs').remove();
		$(mod).find('.module-settings form').append('<input class="mytools_favs" type="hidden" name="params[favs]" value="' + f.join(',') + '" />');

		// save dashboard
		if (HUB.Plugins.MemberDashboard) {
			HUB.Plugins.MemberDashboard.save(HUB.Modules.MyTools.getFavs);
		}
	},

	getFavs: function() {
		/*var mod = this,
			$ = this.jQuery,
			settings = this.settings;*/
		var settings = HUB.Modules.MyTools.settings;

		if (!settings.ajax.url) {
			settings.ajax.url = $('#myToolsTabs').attr('data-api');
		}
		settings.ajax.url += '&fav=' + $($('.mytools_favs')[0]).val();

		$.ajax({
			type: settings.ajax.options.method,
			url: settings.ajax.url,
			dataType: 'json',
			success: function(data, status, jqXHR)
			{
				dta = $(data.html);

				$('#favtools').html(dta.find('.module-content').html());
			},
			error: function(jqXHR, status, error)
			{
				console.log(jqXHR);
				console.log(status);
				console.log(error);
			}
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

	toolSearch: function() {
		var $ = this.jQuery;

		jQuery.expr[':'].caseInsensitiveContains = function(a,i,m) {
			return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0; 
		};

		if ($('#filter-mytools').length) {
			$('#filter-mytools input').on("keyup", function(event){
				var search = $(this).val();
				if (search != '') {
					$("#alltools ul li a:not(:caseInsensitiveContains('"+search+"'))").parent().hide();
					$("#alltools ul li a:caseInsensitiveContains('"+search+"')").parent().show();

					//add no results node
					if ($("#alltools ul li:visible").length == 0) {
						$("#alltools ul").append("<li id=\"none\">Sorry No Tools Matching Your Search.</li>");
					}

					// remove no results node if we have results
					if ($("#alltools ul li:visible").length > 1 && $("#alltools ul #none").length == 1) {
						$("#alltools ul #none").remove();
					}
				} else {
					$("#alltools ul li").show();
				}
			});
		}
	}
};

jQuery(document).ready(function($){
	HUB.Modules.MyTools.initialize();
});