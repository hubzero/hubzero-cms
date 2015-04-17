/**
 * @package	 hubzero-cms
 * @file		plugins/members/dashboard/dashboard.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license	 http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

		// tell the modules we have js
		$('.member_dashboard').addClass('js-enabled');

		// calculate working area
		this._calculateWorkingArea();

		// init grid
		this.grid();

		// init add modal
		this.add();

		// init push modal
		this.push();

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
			static_class: 'module-static',
			widget_selector: '.module',
			widget_margins: [dashboard.settings.col_margin_horz, dashboard.settings.col_margin_vert],
			widget_base_dimensions: [dashboard.settings.col_width, dashboard.settings.col_height],
			max_cols: dashboard.settings.max_cols,
			serialize_params: function(element, specs)
			{
				// return module id, col, row, and sizes
				return { 
					module: $(element).data('moduleid'),
					col: specs.col,
					row: specs.row,
					size_x: specs.size_x,
					size_y: specs.size_y
				}
			},
			draggable: {
				handle: 'h3', 
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
			// tpl: {
			// 	wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			// },
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

	push: function()
	{
		var $        = this.jQuery,
			dasboard = this;

		$('.push-module').fancybox({
			type: 'ajax',
			width: 800,
			height: 'auto',
			autoSize: false,
			fitToView: false,  
			titleShow: false,
			// tpl: {
			// 	wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			// },
			beforeLoad: function() {

				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);
			},
			beforeShow: function() {
				if (jQuery.uniform)
				{
					$("select, input[type=file]").uniform();
				}
				
			},
			afterShow: function() {
				$('body').on('click','.dopush', function(event) {
					event.preventDefault();

					$.ajax({
						type: 'post',
						url: $(this).parents('form').attr('action'), 
						data: $(this).parents('form').serialize(), 
						success: function(){
							$.fancybox.close();
						}
					})
				});
			}
		});
	},

	loadModule: function( moduleid )
	{
		var $        = this.jQuery,
			dashboard = this;

		$.ajax({
			type: 'post',
			url: 'index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&task=module', 
			dataType: 'json',
			data: {
				moduleid: moduleid
			},
			success: function(data, status, jqXHR)
			{
				dashboard.addModule(data.html);
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

	removeModule: function( module ) 
	{
		var $         = this.jQuery
			dashboard = this;

		dashboard.modules.remove_widget(module, function() {
			dashboard.save();
			//$('.modules').height(0);
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
			params = dashboard.modules.serialize(),
			module_data = JSON.stringify(params);
		
		$.ajax({
			type: 'post',
			url: 'index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&task=save', 
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
			innerWrapWidth     = $('.member_dashboard').width(),
			pageContentMargins = 0;

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