/**
 * @package     hubzero-cms
 * @file        plugins/courses/progress/gradebook.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
//  Forum scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.CoursesProgress = {
	jQuery   : jq,
	colWidth : 0,
	offset   : 0,
	cnt      : 0,
	members  : {},
	assets   : {},

	loadData: function ( )
	{
		var $         = this.jQuery,
			gradebook = $('.gradebook'),
			form      = $('.gradebook-form');

		// Add helpers
		Handlebars.registerHelper('getGrade', function ( grades, member_id, asset_id ) {
			return ($.type(grades) === 'object' && $.type(grades[member_id]['assets'][asset_id]) !== 'undefined') ? grades[member_id]['assets'][asset_id]['score'] : '';
		});
		Handlebars.registerHelper('ifAreEqual', function ( val1, val2 ) {
			return (val1 === val2) ? ' selected="selected"' : '';
		});
		Handlebars.registerHelper('shorten', function ( title, length ) {
			return (title.length < length) ? title : title.substring(0, length)+'...';
		});
		Handlebars.registerHelper('ifIsOverride', function ( grades, member_id, asset_id ) {
			return ($.type(grades) === 'object' && $.type(grades[member_id]['assets'][asset_id]) !== 'undefined' && grades[member_id]['assets'][asset_id]['override']) ? ' active' : '';
		});

		// Get data
		$.ajax({
			url      : form.attr('action'),
			dataType : 'json',
			data     : [{'name': 'action', 'value': 'getData'}],
			success  : function ( data, textStatus, jqXHR ) {
				// Render template - main portion
				var source    = $('#gradebook-template-main').html(),
					template  = Handlebars.compile(source),
					context   = {members: data.members},
					html      = template(context);

				// Insert into page (it is currently hidden)
				form.html(html);

				// Now render assets portion
				source   = $('#gradebook-template-asset').html();
				template = Handlebars.compile(source);
				context  = {assets: data.assets, members: data.members, grades: data.grades};
				html     = template(context);

				// Inser the assets into their place
				$('.slidable-inner').html(html);

				// Remove loading icon
				gradebook.find('.loading').fadeOut();
				// Fade in table and navigation
				form.fadeIn();
				$('.navigation').fadeIn();
				$('.controls').fadeIn();

				// Resize table on resize window
				$(window).resize(HUB.Plugins.CoursesProgress.resizeTable);

				// Do initial resize and setup of events
				HUB.Plugins.CoursesProgress.resizeTable();
				HUB.Plugins.CoursesProgress.initialize();

				HUB.Plugins.CoursesProgress.members = data.members;
				HUB.Plugins.CoursesProgress.assets  = data.assets;
			}
		});
	},

	initialize: function ( )
	{
		var $ = this.jQuery,
			g = $('.gradebook'),
			f = $('.gradebook-form');

		// Add tool tips to form title and student names
		/*$('.form-name').tooltip({
			position : 'top center',
			offset   : [-5, 0],
			tipClass : 'tooltip-top',
			predelay : 400
		});*/
		$('.cell-title').tooltip({
			position : 'center right',
			offset   : [0, 5],
			tipClass : 'tooltip-right',
			predelay : 400
		});

		// Add fancy select boxes
		//$('.form-type select').HUBfancyselect();

		var s      = g.find('.slidable-inner'),
			slider = $('.slider');

		$('.nxt').unbind('click').click(function() {
			if (Math.ceil(Math.abs(s.css('left').replace('px', ''))) < Math.ceil(HUB.Plugins.CoursesProgress.offset) && !s.is(':animated')) {
				var sv = slider.slider('value');
				slider.slider('value', sv+=1);
				s.animate({left:'-='+HUB.Plugins.CoursesProgress.colWidth+'px'}, 'fast', function() {
					if (Math.ceil(Math.abs(s.css('left').replace('px', ''))) == Math.ceil(HUB.Plugins.CoursesProgress.offset)) {
						$('.nxt').addClass('disabled');
					}
					$('.prv').removeClass('disabled');
				});
			} else {
				$('.nxt').addClass('disabled');
			}
		});

		$('.prv').unbind('click').click(function() {
			if (Math.ceil(s.css('left').replace('px', '')) < 0 && !s.is(':animated')) {
				var sv = slider.slider('value');
				slider.slider('value', sv-=1);
				s.animate({left:'+='+HUB.Plugins.CoursesProgress.colWidth+'px'}, 'fast', function() {
					if (Math.ceil(s.css('left').replace('px', '')) == 0) {
						$('.prv').addClass('disabled');
					}
					$('.nxt').removeClass('disabled');
				});
			} else {
				$('.prv').addClass('disabled');
			}
		});

		// Prevent form submission via "enter"
		$('.gradebook-form').submit(function ( e ) {
			e.preventDefault();
		});

		// Overload certain keys to emulate excel-like behavior
		g.off('keydown', '.cell-entry').on('keydown', '.cell-entry', function ( e ) {
			var t = $(this),
				s = t.find('.cell-score'),
				c = t.data('init-val');

			// Tab key
			if (e.keyCode === 9) {
				e.preventDefault();
				// Shift + tab
				if (e.shiftKey) {
					var r = t.data('rownum').match(/cell-row([0-9]*)/i);
					var p = t.parent('.gradebook-column').prev('.gradebook-column').find('.'+r[0]+':visible');
					var n = t.parent('.gradebook-column').siblings().last().find('.cell-row'+(parseInt(r[1], 10)-1)+':visible');
					if (p.length) {
						// Make sure the next item isn't off the page
						var s      = $('.slidable-inner'),
							left   = s.css('left').replace('px', ''),
							offset = HUB.Plugins.CoursesProgress.offset,
							colwid = HUB.Plugins.CoursesProgress.colWidth,
							cnt    = HUB.Plugins.CoursesProgress.cnt,
							item   = $(t.parent('.gradebook-column')),
							colnum = item.data('colnum'),
							hidden = Math.ceil(Math.abs(left / colwid));

						if (colnum <= hidden) {
							var num = colwid * (hidden - 1),
								val = hidden - 1;

							if (!s.is(':animated')) {
								HUB.Plugins.CoursesProgress.move(num, function() {
									p.trigger('click');
								}, val);
							}
						} else {
							p.trigger('click');
						}
					} else if (n.length) {
						// Make sure the next item isn't off the page
						var s      = $('.slidable-inner'),
							offset = HUB.Plugins.CoursesProgress.offset,
							colwid = HUB.Plugins.CoursesProgress.colWidth,
							cnt    = HUB.Plugins.CoursesProgress.cnt,
							total  = $('.gradebook-container .gradebook-column:not(.gradebook-students)').length;

						if (offset) {
							if (!s.is(':animated')) {
								HUB.Plugins.CoursesProgress.move((colwid * (total - cnt)), function() {
									n.trigger('click');
								}, (total - cnt));
							}
						} else {
							n.trigger('click');
						}
					} else {
						t.find('.edit-grade').blur();
					}
				// Tab
				} else {
					var r = t.data('rownum').match(/cell-row([0-9]*)/i),
						n = t.parent('.gradebook-column').next('.gradebook-column').find('.'+r[0]+':visible'),
						s = $('.slidable-inner'),
						p = t.parent('.gradebook-column').siblings().first().find('.cell-row'+(parseInt(r[1], 10)+1)+':visible');

					if (n.length) {
						// Make sure the next item isn't off the page
						var left   = s.css('left').replace('px', ''),
							offset = HUB.Plugins.CoursesProgress.offset,
							colwid = HUB.Plugins.CoursesProgress.colWidth,
							cnt    = HUB.Plugins.CoursesProgress.cnt,
							item   = $(t.parent('.gradebook-column')),
							colnum = item.data('colnum'),
							hidden = Math.ceil(Math.abs((offset - left) / colwid));

						if (colnum >= hidden) {
							var num = colwid * (colnum - cnt + 2),
								val = colnum - cnt + 2;

							if (!s.is(':animated')) {
								HUB.Plugins.CoursesProgress.move(num, function() {
									n.trigger('click');
								}, val);
							}
						} else {
							n.trigger('click');
						}
					} else if (p.length) {
						// Make sure the next item isn't off the page
						var left = s.css('left').replace('px', '');

						if (left != 0) {
							if (!s.is(':animated')) {
								HUB.Plugins.CoursesProgress.move(0, function() {
									p.trigger('click');
								}, 0);
							}
						} else {
							p.trigger('click');
						}
					} else {
						t.find('.edit-grade').blur();
					}
				}
			// Esc key
			} else if (e.keyCode === 27) {
				t.removeClass('editing');
				s.html(c);
			// Up key
			} else if (e.keyCode === 38) {
				var i = $(t.prev('.cell-entry'));
				if (i.length) {
					i.trigger('click');
				}
			// Down key
			} else if (e.keyCode === 40) {
				var i = $(t.next('.cell-entry'));
				if (i.length) {
					i.trigger('click');
				}
			// Enter key
			} else if (e.keyCode === 13) {
				$('.edit-grade').blur();
			}
		});

		// Overload certain keys to emulate excel-like behavior
		g.off('keydown', '.form-title').on('keydown', '.form-title', function ( e ) {
			var t   = $(this),
				val = t.data('init-val');

			// Esc key
			if (e.keyCode === 27) {
				t.html(val);
			// Enter key
			} else if (e.keyCode === 13) {
				t.find('input').blur();
			}
		});

		// Add click event to cells to enter edit mode
		g.off('click', '.cell-entry').on('click', '.cell-entry', function ( e ) {
			var t = $(this);

			if (!t.find('input').length) {
				var s = t.find('.cell-score'),
					c = $.trim(s.html());

				// Store initial value
				t.data('init-val', c);

				// Add an input box and focus on it
				s.html('<input class="edit-grade" type="text" name="grade" value="'+c+'" />');
				s.find('input').focus();
				t.addClass('editing');

				t.find('input').focusout(function() {
					if (HUB.Plugins.CoursesProgress.isValid(t.find('input').val(), c)) {
						var f = $('.gradebook-form'),
							d = [];

						d.push({"name":"action",     "value":"savegradebookentry"});
						d.push({"name":"asset_id",   "value":t.data('asset-id')});
						d.push({"name":"student_id", "value":t.data('student-id')});
						d.push({"name":"grade",      "value":t.find('.edit-grade').val()});
						// Submit save
						$.ajax({
							type     : "POST",
							url      : f.attr('action'),
							data     : d,
							dataType : 'json',
							success  : function ( data, textStatus, jqXHR ) {
								t.removeClass('editing');
								t.find('.override').addClass('active');
								s.html(parseFloat(t.find('.edit-grade').val()).toFixed(2));
							}
						});
					} else {
						t.removeClass('editing');
						s.html(c);
					}
				});
			}
		});

		// Add click event to cells to enter edit mode
		g.off('click', '.form-title').on('click', '.form-title', function ( e ) {
			var t = $(this);

			if (!t.find('input').length) {
				var val = $.trim(t.parents('.form-name').attr('title'));

				// Store initial value
				t.data('init-val', $.trim(t.html()));

				// Edit form title and focus
				t.html('<input class="edit-title" type="text" name="title" value="'+val+'" />');
				t.find('input').focus();

				t.find('input').focusout(function() {
					if ($(this).val() != val) {
						var f = $('.gradebook-form'),
							d = [];

						d.push({"name":"action",     "value":"savegradebookitem"});
						d.push({"name":"asset_id",   "value":t.parents('.gradebook-column').data('asset-id')});
						d.push({"name":"title",      "value":t.find('.edit-title').val()});

						// Submit save
						$.ajax({
							type     : "POST",
							url      : f.attr('action'),
							data     : d,
							dataType : 'json',
							success  : function ( data, textStatus, jqXHR ) {
								var title = (data.title.length < 10) ? data.title : data.title.substring(0, 10)+'...';
								t.html(title);
								t.parents('.form-name').attr('title', data.title);

								// Move based on alphabetic list
								var list = $('.gradebook-container .gradebook-column:not(.gradebook-students)');
								function sortAlpha ( a, b ) {
									return ($.trim($(a).find('.form-title').html().toLowerCase()) > $.trim($(b).find('.form-title').html().toLowerCase())) ? 1 : -1;
								}

								result = list.sort(sortAlpha);
								$('.slidable-inner').html(result);

								// Reset indices
								list = $('.gradebook-container .gradebook-column:not(.gradebook-students)');
								list.each(function ( idx, itm ) {
									$(itm).attr('data-colnum', idx);
								});

								// Make sure the next item isn't off the page
								var s      = $('.slidable-inner'),
									left   = s.css('left').replace('px', ''),
									offset = HUB.Plugins.CoursesProgress.offset,
									colwid = HUB.Plugins.CoursesProgress.colWidth,
									cnt    = HUB.Plugins.CoursesProgress.cnt,
									item   = $(t.parents('.gradebook-column')),
									colnum = item.data('colnum'),
									max    = $('.slider').slider('option', 'max'),
									num    = 0,
									val    = 0;

								if (colnum > max) {
									num = colwid * max;
									val = max;
								} else {
									num = colwid * colnum;
									val = colnum;
								}

								if (!s.is(':animated')) {
									HUB.Plugins.CoursesProgress.move(num, function() {
										$('.gradebook-column[data-colnum="'+colnum+'"]').css({'background-color' : "#FFFF99"});
										$('.gradebook-column[data-colnum="'+colnum+'"]').animate({'background-color' : "#F9F9F9"}, 2000);
									}, val);
								}
							}
						});
					} else {
						t.html(t.data('init-val'));
					}
				});
			}
		});

		// Add click event to cells to enter edit mode
		g.off('change', '.form-type select').on('change', '.form-type select', function ( e ) {
			var t = $(this),
				f = $('.gradebook-form'),
				d = [];

				d.push({"name":"action",     "value":"savegradebookitem"});
				d.push({"name":"asset_id",   "value":t.parents('.gradebook-column').data('asset-id')});
				d.push({"name":"type",       "value":t.val()});

				// Submit save
				$.ajax({
					type     : "POST",
					url      : f.attr('action'),
					data     : d,
					dataType : 'json',
					success  : function ( data, textStatus, jqXHR ) {
						// Success
					}
				});
		});

		g.off('click', '.override.active').on('click', '.override.active', function ( e ) {
			var t = $(this),
				f = $('.gradebook-form'),
				d = [],
				p = t.parent('.cell-entry');

			// Don't propagate click up to edit
			e.stopPropagation();

			d.push({"name":"action",     "value":"resetgradebookentry"});
			d.push({"name":"asset_id",   "value":p.data('asset-id')});
			d.push({"name":"student_id", "value":p.data('student-id')});

			// Submit save
			$.ajax({
				type     : "POST",
				url      : f.attr('action'),
				data     : d,
				dataType : 'json',
				success  : function ( data, textStatus, jqXHR ) {
					p.find('.cell-score').html(data.score);
					p.find('.override').removeClass('active');
				}
			});
		});

		g.off('click', '.controls .refresh').on('click', '.controls .refresh', function ( e ) {
			var t = $(this),
				f = $('.gradebook-form');

			f.html('').hide();
			$('.navigation').hide();
			$('.controls').hide();
			$('.loading').show();
			$(".gradebook #none").remove();

			HUB.Plugins.CoursesProgress.loadData();
		});

		g.off('click', '.controls .progress-button').on('click', '.controls .progress-button', function ( e ) {
			var t = $(this),
				f = $('.gradebook-form');

			window.location.href = f.attr('action');
		});

		g.off('click', '.controls .export').on('click', '.controls .export', function ( e ) {
			var t = $(this),
				f = $('.gradebook-form'),
				a = f.attr('action');

			a += (a.search('/?/')) ? '&action=exportcsv' : '?action=exportcsv';

			window.open(a);
		});

		// Add a new gradebook item
		$('.addrow').unbind('click').click(function() {
			var t = $(this),
				f = $('.gradebook-form'),
				d = [];

			d.push({"name":"action", "value":"savegradebookitem"});

			// Submit save
			$.ajax({
				type     : "POST",
				url      : f.attr('action'),
				data     : d,
				dataType : 'json',
				success  : function ( data, textStatus, jqXHR ) {
					var assets = [];
					assets.push({
						id    : data.id,
						title : data.title,
					});

					// Render template
					var source    = $('#gradebook-template-asset').html(),
						template  = Handlebars.compile(source),
						context   = {members: HUB.Plugins.CoursesProgress.members, assets: assets},
						html      = template(context),
						cnt       = HUB.Plugins.CoursesProgress.cnt;

					$('.slidable-inner').append(html);
					var slider = $('.slider');

					// Fix up index
					var numCols = $('.gradebook-container .gradebook-column:not(.gradebook-students)').length;
					$('.gradebook-container .gradebook-column:not(.gradebook-students)').last().attr('data-colnum', numCols-1);

					var val = parseInt(numCols - cnt, 10),
						loc = (numCols - HUB.Plugins.CoursesProgress.cnt) * HUB.Plugins.CoursesProgress.colWidth;

					HUB.Plugins.CoursesProgress.resizeTable(
						function () {
							HUB.Plugins.CoursesProgress.move(loc, function() {
								var item = $('.gradebook-container .gradebook-column:not(.gradebook-students)').last().find('.form-title');
								item.trigger('click');
							}, val);
						}
					);
				}
			});
		});

		// Search/filter by student name
		if ($('.search-box input').length) {
			jQuery.expr[':'].caseInsensitiveContains = function ( a, i, m ) {
				return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
			};

			$('.search-box input').on("keyup", function ( e ) {
				var search = $(this).val();
				if(search !== '') {
					var neg = $(".gradebook-container .cell-title:not(:caseInsensitiveContains('"+search+"'))");
					var pos = $(".gradebook-container .cell-title:caseInsensitiveContains('"+search+"')");

					neg.each(function() {
						$($('.'+$(this).data('rownum'))).hide();
					});
					pos.each(function() {
						$($('.'+$(this).data('rownum'))).show();
					});

					// Add no results node
					if(pos.length === 0 && $('#none').length === 0) {
						$(".navigation").before("<div id=\"none\" class=\"warning clear\">Sorry, no students match your search.</div>");
					}

					// Remove no results node if we have results
					if(pos.length >= 1 && $("#none").length == 1 ) {
						$(".gradebook #none").remove();
					}
				} else {
					$(".gradebook #none").remove();
					$(".gradebook-container .cell").show();
				}
			});
		}
	},

	resizeTable: function ( callback )
	{
		// Reset slidable 'right' before doing width calculations
		$('.slidable').css({right : 0});
		$('.gradebook').css({
			'margin-right' : 0,
			'margin-left'  : 0
		});

		// Calculate width of each column (range 100 - 150 px)
		var w      = $('.slidable-inner').width(),
			rLow   = Math.ceil(w / 150),
			rhigh  = Math.floor(w / 100),
			cnt    = Math.min(rLow, rhigh),
			width  = w / cnt,
			rowCnt = $('.gradebook-container .gradebook-column:not(.gradebook-students)').length;
			offset = (rowCnt - cnt) * width;

		// Set CSS
		$('.gradebook-container .gradebook-column:not(.gradebook-students) .cell').css({
			'width'     : width + 'px',
			'min-width' : width + 'px',
			'max-width' : width + 'px'
		});
		$('.slidable').css({right :'-'+width+'px'});
		$('.slidable-inner').css({left : 0});

		// Disable prev button by default
		$('.prv').addClass('disabled');

		// Check if all columns are showing and disable next button as necessary
		if (offset <= 0) {
			$('.nxt').addClass('disabled');
			$('.gradebook').css({
				'margin-right' : ((offset*-1)/2)+'px',
				'margin-left'  : ((offset*-1)/2)+'px'
			});
		} else {
			$('.nxt').removeClass('disabled');
		}

		$('.slider').slider({
			min     : 0,
			max     : (rowCnt - cnt),
			value   : 0,
			animate : 'fast',
			slide : function( event, ui ) {
				HUB.Plugins.CoursesProgress.move(HUB.Plugins.CoursesProgress.colWidth * ui.value);
			}
		});

		// Initialize the rest of the page
		HUB.Plugins.CoursesProgress.cnt      = cnt;
		HUB.Plugins.CoursesProgress.colWidth = width;
		HUB.Plugins.CoursesProgress.offset   = offset;

		if ($.type(callback) === 'function') {
			callback();
		}
	},

	move: function ( loc, callback, val )
	{
		var s = $('.slidable-inner');

		if ($.type(val) === 'number') {
			$('.slider').slider('value', val);
		}

		s.animate({left:'-'+(loc)+'px'}, 'fast', function ( e ) {
			var l = Math.ceil(Math.abs(s.css('left').replace('px', ''))),
				o = Math.ceil(HUB.Plugins.CoursesProgress.offset);

			if (l === 0 && o === 0) {
				$('.prv').addClass('disabled');
				$('.nxt').addClass('disabled');
			} else if (l !== 0 && l === o) {
				$('.nxt').addClass('disabled');
				$('.prv').removeClass('disabled');
			} else if (l !== 0 && l < o) {
				$('.nxt').removeClass('disabled');
				$('.prv').removeClass('disabled');
			} else {
				$('.prv').addClass('disabled');
				$('.nxt').removeClass('disabled');
			}

			if ($.type(callback) === 'function') {
				callback();
			}
		});
	},

	isValid: function ( newVal, curVal )
	{
		var $ = this.jQuery;

		if (newVal == curVal) {
			return false;
		}

		if (newVal === '') {
			return false;
		}

		if (newVal > 100.00 || newVal < 0.00) {
			return false;
		}

		return true;
	}
};

jQuery(document).ready(function($){
	HUB.Plugins.CoursesProgress.loadData( true );
});