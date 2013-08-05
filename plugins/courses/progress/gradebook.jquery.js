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

	loadData: function ( )
	{
		var $ = this.jQuery,
			g = $('.gradebook'),
			f = $('.gradebook-form');

		// Render template
		var source   = $('#gradebook-template').html(),
			template = Handlebars.compile(source);

		// Add helpers
		Handlebars.registerHelper('getGrade', function ( grades, member_id, asset_id ) {
			return grades[member_id]['assets'][asset_id]['score'];
		});
		Handlebars.registerHelper('ifAreEqual', function ( val1, val2 ) {
			return (val1 === val2) ? ' selected="selected"' : '';
		});
		Handlebars.registerHelper('evenOrOdd', function ( idx ) {
			return (idx & 1) ? 'odd' : 'even';
		});
		Handlebars.registerHelper('evenOrOdd', function ( idx ) {
			return (idx & 1) ? 'odd' : 'even';
		});
		Handlebars.registerHelper('shorten', function ( title, length ) {
			return (title.length < length) ? title : title.substring(0, length)+'...';
		});
		Handlebars.registerHelper('ifIsOverride', function ( grades, member_id, asset_id ) {
			return (grades[member_id]['assets'][asset_id]['override']) ? ' active' : '';
		});

		// Get data
		$.ajax({
			url      : f.attr('action') + '&action=getData',
			dataType : 'json',
			success  : function ( data, textStatus, jqXHR ) {
				var context = {assets: data.assets, members: data.members, grades: data.grades};
				var html    = template(context);

				// Remove loading icon
				g.find('.loading').hide();
				f.html(html);
				$('.navigation').show();

				$(window).resize(HUB.Plugins.CoursesProgress.resizeTable);

				HUB.Plugins.CoursesProgress.resizeTable();
				HUB.Plugins.CoursesProgress.initialize();
			}
		});
	},

	initialize: function ( )
	{
		var $ = this.jQuery,
			g = $('.gradebook'),
			f = $('.gradebook-form');

		// Add tool tips to form title and student names
		$('.form-name').tooltip({
			position : 'top center',
			offset   : [-5, 0],
			tipClass : 'tooltip-top'
		});
		$('.cell-title').tooltip({
			position : 'center right',
			offset   : [0, 5],
			tipClass : 'tooltip-right'
		});

		// Add fancy select boxes
		//$('.form-type select').HUBfancyselect();
		var s      = g.find('.slidable-inner'),
			slider = $('.slider');

		$('.nxt').click(function() {
			if (Math.round(Math.abs(s.css('left').replace('px', ''))) < Math.round(HUB.Plugins.CoursesProgress.offset)) {
				var sv = slider.slider('value');
				slider.slider('value', sv+=1);
				s.animate({left:'-='+HUB.Plugins.CoursesProgress.colWidth+'px'}, function() {
					if (Math.abs(Math.round(s.css('left').replace('px', ''))) == Math.round(HUB.Plugins.CoursesProgress.offset)) {
						$('.nxt').addClass('disabled');
					}

					$('.prv').removeClass('disabled');
				});
			} else {
				$('.nxt').addClass('disabled');
			}
		});

		$('.prv').click(function() {
			if (Math.round(s.css('left').replace('px', '')) < 0) {
				var sv = slider.slider('value');
				slider.slider('value', sv-=1);
				s.animate({left:'+='+HUB.Plugins.CoursesProgress.colWidth+'px'}, function() {
					if (Math.round(s.css('left').replace('px', '')) == 0) {
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
		g.on('keydown', '.cell-entry', function ( e ) {
			var t = $(this);
				s = t.find('.cell-score'),
				c = t.data('init-val');

			// Tab key
			if (e.keyCode === 9) {
				e.preventDefault();
				// Shift + tab
				if (e.shiftKey) {
					var r = t.data('rownum').match(/cell-row([0-9]*)/i);
					var p = t.parent('.gradebook-column').prev('.gradebook-column').find('.'+r[0]);
					var n = t.parent('.gradebook-column').siblings().last().find('.cell-row'+(parseInt(r[1], 10)-1));
					if (p.length) {
						p.trigger('click');
					} else if (n.length) {
						n.trigger('click');
					} else {
						t.find('.edit-grade').blur();
					}
				// Tab
				} else {
					var r = t.data('rownum').match(/cell-row([0-9]*)/i);
					var n = t.parent('.gradebook-column').next('.gradebook-column').find('.'+r[0]);
					var p = t.parent('.gradebook-column').siblings().first().find('.cell-row'+(parseInt(r[1], 10)+1));
					if (n.length) {
						n.trigger('click');
					} else if (p.length) {
						p.trigger('click');
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

		// Add click event to cells to enter edit mode
		$('.gradebook').on('click', '.cell-entry', function ( e ) {
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

		$('.gradebook').on('click', '.override.active', function ( e ) {
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

		// Add a new gradebook item
		/*$('.add').click(function() {
			// Clone and existing row, strip values, and insert
			var tr = $('table tbody tr').last().clone();
			var html  = '<input class="edit-title" type="text" name="name" placeholder="Name" />';

			var newClass = (tr.hasClass('odd')) ? 'even' : 'odd';
			tr.removeClass('even odd').addClass(newClass);

			tr.find('.cell-title').html(html);
			tr.find('.cell-entry').html('');
			$('table tbody').append(tr);

			var newItemTitle = $('table tbody .cell-title input').last();
			newItemTitle.focus();
			newItemTitle.focusout(function() {
				if (newItemTitle.val() === '') {
					newItemTitle.parents('tr').fadeOut(function() {
						newItemTitle.parents('tr').remove();
					});
				} else {
					var form = $('.gradebook-form');

					// Submit save
					$.ajax({
						type     : "POST",
						url      : form.attr('action'),
						data     : form.serializeArray(),
						dataType : 'json',
						success  : function ( data, textStatus, jqXHR ) {
							newItemTitle.parents('.cell-title').html(newItemTitle.val());
							newItemTitle.remove();
						}
					});
				}
			});
		});*/

		// Search/filter by student name
		if ($('.search-box input').length) {
			jQuery.expr[':'].caseInsensitiveContains = function ( a, i, m ) {
				return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0; 
			};

			$('.search-box input').on("keyup", function ( e ) {
				var search = $(this).val();
				if(search != '') {
					var neg = $(".gradebook-container .cell-title:not(:caseInsensitiveContains('"+search+"'))");
					var pos = $(".gradebook-container .cell-title:caseInsensitiveContains('"+search+"')");

					neg.each(function() {
						$($('.'+$(this).data('rownum'))).hide();
					});
					pos.each(function() {
						$($('.'+$(this).data('rownum'))).show();
					});

					// Add no results node
					if(pos.length == 0 && $('#none').length == 0) {
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

	resizeTable: function ( ) 
	{
		// Reset slidable 'right' before doing width calculations
		$('.slidable').css({right : 0});

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
		if (offset === 0) {
			$('.nxt').addClass('disabled');
		} else {
			$('.nxt').removeClass('disabled');
		}

		$('.slider').slider({
			min     : 0,
			max     : (rowCnt - cnt),
			value   : 0,
			animate : true,
			slide : function( event, ui ) {
				HUB.Plugins.CoursesProgress.move(HUB.Plugins.CoursesProgress.colWidth * ui.value);
			}
		});

		// Initialize the rest of the page
		HUB.Plugins.CoursesProgress.colWidth = width;
		HUB.Plugins.CoursesProgress.offset   = offset;
	},

	move: function ( loc )
	{
		var s = $('.slidable-inner');

		s.stop().animate({left:'-'+(loc)+'px'}, function ( e ) {
			var l = Math.round(Math.abs(s.css('left').replace('px', ''))),
				o = Math.round(HUB.Plugins.CoursesProgress.offset);

			if (l == 0 && o == 0) {
				$('.prv').addClass('disabled');
				$('.nxt').addClass('disabled');
			} else if (l != 0 && l == o) {
				$('.nxt').addClass('disabled');
				$('.prv').removeClass('disabled');
			} else if (l != 0 && l < o) {
				$('.nxt').removeClass('disabled');
				$('.prv').removeClass('disabled');
			} else {
				$('.prv').addClass('disabled');
				$('.nxt').removeClass('disabled');
			}
		});
	},

	isValid: function ( newVal, curVal )
	{
		var $ = this.jQuery;

		if (newVal == curVal) {
			return false;
		}

		if (newVal == '') {
			return false;
		}

		if (newVal > 100.00 || newVal < 0.00) {
			return false;
		}

		return true;
	}
};

jQuery(document).ready(function($){
	HUB.Plugins.CoursesProgress.loadData();
});