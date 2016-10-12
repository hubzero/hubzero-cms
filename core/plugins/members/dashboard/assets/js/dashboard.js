/**
 * @package	 hubzero-cms
 * @file		plugins/members/dashboard/dashboard.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	 http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

//-------------------------------------------------------------

if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins)
{
	HUB.Plugins = {};
}

//-------------------------------------------------------------

HUB.Plugins.MemberDashboard = {
	jQuery: jq,

	modules: null,

	settings: {
		max_cols: 3,
		col_margin_vert: 10,
		col_margin_horz: 10,
		col_width: 300,
		col_height: 150,
		remove_timeout: 4000
	},

	initialize: function()
	{
		var $ = this.jQuery;

		if (window.innerWidth <= 800) {
			return;
		}

		// tell the modules we have js
		$('.member_dashboard').addClass('js-enabled');

		// calculate working area
		this._calculateWorkingArea();

		// init grid
		this.grid();

		// init add modal
		this.add();

		// handle module events events
		this.moduleClickEvents();

		// do we have any modules?
		this.emptyStateCheck();
	},

	grid: function()
	{
		// store vars for later
		var $         = this.jQuery,
			dashboard = this;

		// instantiate gridster
		dashboard.modules = $('.modules').gridster({
			// static_class: 'module-static',
			widget_selector: '.module',
			widget_margins: [dashboard.settings.col_margin_horz, dashboard.settings.col_margin_vert],
			widget_base_dimensions: [dashboard.settings.col_width, dashboard.settings.col_height],
			max_cols: dashboard.settings.max_cols,
			serialize_params: function(element, specs) {
				var params = {};
				$.each($(element).find('.module-settings form').serializeArray(), function() {
					var name = this.name.replace(/params\[([^\]]*)\]/g, "$1");
					params[name] = this.value;
				});

				// return module id, col, row, and sizes
				return { 
					module: $(element).data('moduleid'),
					col: specs.col,
					row: specs.row,
					size_x: specs.size_x,
					size_y: specs.size_y,
					parameters: params
				}
			},
			draggable: {
				handle: 'h3',
				items: '.gs-w:not(.static)',
				stop: function(event, ui) {
					dashboard.save();
				}
			},
			resize: {
				enabled: true, 
				stop: function (event, ui, widget) {
					dashboard.save();
				}
			}
		}).data('gridster');

		// is the dashboard customizable?
		// if not disable dragging and resize
		if (!$('.modules').hasClass('customizable'))
		{
			dashboard.modules.disable();
			dashboard.modules.disable_resize();
		}

		// store for later
		this.modules = dashboard.modules;

		// handle window resize events
		this.windowResize();
	},

	add: function()
	{
		var $        = this.jQuery,
			dasboard = this;

		$('.add-module').fancybox({
			type: 'ajax',
			width: 800,
			height: 'auto',
			autoSize: false,
			fitToView: false,  
			titleShow: false,
			tpl: {
				wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			},
			beforeLoad: function() {
				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);
			},
			afterShow: function() {
				$('.module-list-triggers a').first().trigger('click');
			}
		});

		$('body').on('click', '.module-list-triggers a', function(event) {
			event.preventDefault();
			var module = $(this).attr('data-module');

			$('.module-list-triggers a').removeClass('active');
			$(this).addClass('active');

			$('.module-list-content li').hide();
			$('.module-list-content li.' + module).show();
		});

		$('body').on('click', '.install-module', function(event) {
			event.preventDefault();

			// load module by id
			var moduleid = $(this).attr('data-module');
			dasboard.loadModule(moduleid);
		});
	},

	loadModule: function( moduleid )
	{
		var $         = this.jQuery,
			userid    = $('.modules').attr('data-userid'),
			dashboard = this;

		$.ajax({
			type: 'post',
			url: 'index.php?option=com_members&id=' + userid + '&active=dashboard&action=module', 
			dataType: 'json',
			data: {
				moduleid: moduleid
			},
			success: function(data, status, jqXHR)
			{
				dashboard.addModuleAssets(data.assets);
				dashboard.addModule(data.html);
			},
			error: function(jqXHR, status, error)
			{
				//console.log(status);
				//console.log(error);
			}
		});
	},

	refreshModule: function( moduleid )
	{
		var $         = this.jQuery,
			userid    = $('.modules').attr('data-userid'),
			dashboard = this;

		$.ajax({
			type: 'post',
			url: 'index.php?option=com_members&id=' + userid + '&active=dashboard&action=module', 
			dataType: 'json',
			data: {
				moduleid: moduleid
			},
			success: function(data, status, jqXHR)
			{
				dashboard.addModuleAssets(data.assets);
				dashboard.modules.replace_widget($('.module[data-moduleid='+moduleid+']'), data.html);
			},
			error: function(jqXHR, status, error)
			{
				//console.log(status);
				//console.log(error);
			}
		});
	},

	addModule: function( moduleHtml )
	{
		var $         = this.jQuery
			dashboard = this;

		// calculate column to add module to
		var colRow = dashboard._calculateColumnRow();
		
		// close fancybox
		$.fancybox.close();

		// add module and save prefs
		dashboard.modules.add_widget(moduleHtml, 1, 2, colRow[0], colRow[1]);
		dashboard.emptyStateCheck();
		dashboard.save();
	},

	addModuleAssets: function(assets)
	{
		var $ = this.jQuery;

		var head = document.getElementsByTagName('head')[0];
		$.each(assets.scripts, function(index, s) {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = s;
			head.appendChild(script);
		});
		$.each(assets.stylesheets, function(index, s) {
			var link = document.createElement('link');
			link.rel = 'stylesheet';
			link.href = s;
			link.media = 'screen'
			head.appendChild(link);
		});
	},

	removeModule: function( module ) 
	{
		var $         = this.jQuery
			dashboard = this;

		dashboard.modules.remove_widget(module, function() {
			dashboard.save();
			dashboard.emptyStateCheck();
		});
	},

	windowResize: function()
	{
		var $         = this.jQuery
			dashboard = this;

		// on window resize end
		// resizeEnd event provided with 3rd party extension
		$(window).resizeEnd(function() {
			dashboard._calculateWorkingArea();
			dashboard.modules.resize_widget_dimensions({
				widget_base_dimensions: [dashboard.settings.col_width,  dashboard.settings.col_height]
			});
		});

		// trigger resize right away
		$(window).trigger('resize');
	},

	save: function(callback)
	{
		var $      = this.jQuery,
			userid = $('.modules').attr('data-userid'),
			params = dashboard.modules.serialize(),
			module_data = JSON.stringify(params);

		$.ajax({
			type: 'post',
			url: 'index.php?option=com_members&id=' + userid + '&active=dashboard&action=save&no_html=1&' + $('.modules').attr('data-token') + '=1',
			dataType: 'json',
			data: {
				modules: module_data
			},
			complete: function(){
				if (callback)
				{
					callback.call();
				}
			},
			success: function(data, status, jqXHR)
			{
				//console.log(data);
			},
			error: function(jqXHR, status, error)
			{
				console.log(status);
				console.log(error);
			}
		});
	},

	emptyStateCheck: function()
	{
		var $     = this.jQuery,
			count = $('.modules .module').length;

		// hide/show empty state
		if (count == 0)
		{
			$('.modules').height(0);
			$('.modules-empty').show();
		}
		else
		{
			$('.modules-empty').hide();
		}
	},

	moduleClickEvents: function()
	{
		var $         = this.jQuery,
			dashboard = this;

		$('.modules')
			.on('click', '.module-links .remove', function(event) {
				event.preventDefault();
				var $this = $(this);
				if (!$this.hasClass('confirm'))
				{
					$this.toggleClass('confirm');
					setTimeout(function(){
						$this.removeClass('confirm');
					}, dashboard.settings.remove_timeout);
				}
			})
			.on('click', '.module-links .confirm', function(event) {
				event.preventDefault();

				// get the module we are wanting to remvoe
				var module = $(this).parents('.module');

				// remove module
				dashboard.removeModule(module);
			})
			.on('click', '.module-links .settings', function(event) {
				event.preventDefault();

				$(this).parents('.module')
					.toggleClass('modifying-settings')
					.find('.module-settings')
					.slideToggle("fast");
			})
			.on('click', '.module-settings .save', function(event) {
				event.preventDefault();
				var button = $(this);
				button
					.attr('disabled', 'disabled')
					.html('Saving...');

				dashboard.save(function(){
					button
						.parents('.module')
						.toggleClass('modifying-settings')
						.find('.module-settings')
						.slideToggle("fast");

					button.removeAttr('disabled')
						.html('Save')

					var moduleid = button.parents('.module').attr('data-moduleid');
					dashboard.refreshModule(moduleid);
				});
			})
			.on('click', '.module-settings .cancel', function(event) {
				event.preventDefault();

				$(this).parents('.module')
					.removeClass('modifying-settings')
					.find('.module-settings')
					.slideToggle("fast");
			});
	},

	_calculateColumnRow: function()
	{
		var $         = this.jQuery,
			dashboard = this,
			map       = dashboard.modules.gridmap;

		var max = [];

		for (var i=1; i < map.length; i++)
		{
			var col = map[i];
			for (var n=0; n < col.length; n++)
			{
				if (col[n] == false)
				{
					max.push(n);
					break;
				}
			}
		}

		// determine row and col
		var row = Math.min.apply(Math, max);
		if (max[0] == row)
		{
			col = 1;
		}
		else if (max[1] == row)
		{
			col = 2;
		}
		else
		{
			col = 3;
		}

		// return co/row
		return [col, row];
	},

	_calculateWorkingArea: function()
	{
		var $                  = this.jQuery,
			modulesAreasWidth  = 0,
			moduleBaseWidth    = 0,
			moduleBaseHeight   = 0,
			innerWrapWidth     = $('#page_main').width(),
			pageContentMargins = 20;

		// calculate total working area width
		modulesAreasWidth = innerWrapWidth - pageContentMargins;

		// get module width
		moduleBaseWidth = parseInt(modulesAreasWidth / this.settings.max_cols);

		// subtract margins
		moduleBaseWidth -= (this.settings.col_margin_horz * 2);

		// module height
		moduleBaseHeight = parseInt(moduleBaseWidth / 2);

		// set our col width & height
		this.settings.col_width  = moduleBaseWidth;
		this.settings.col_height = moduleBaseHeight;
	}
};

//-------------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Plugins.MemberDashboard.initialize();
});