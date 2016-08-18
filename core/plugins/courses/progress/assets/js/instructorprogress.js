/**
 * @package     hubzero-cms
 * @file        plugins/courses/progress/gradebook.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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

jQuery(document).ready(function($){
	HUB.Plugins.CoursesProgress.initHandlebarsHelpers();
	HUB.Plugins.CoursesProgress.initializeButtons();
	HUB.Plugins.CoursesProgress.loadProgressData();
});

HUB.Plugins.CoursesProgress = {
	jQuery    : jq,
	colWidth  : 0,
	offset    : 0,
	cnt       : 0,
	rowCnt    : 0,
	members   : {},
	assets    : {},
	canManage : false,
	views     : ['progress', 'gradebook', 'reports'],

	loadProgressData: function ( )
	{
		var $                 = this.jQuery,
			form              = $('.progress-form'),
			units             = [],
			start_time        = 0,
			end_time          = 0,
			average_time      = 0,
			course_id         = 0,
			members_cnt       = false,
			limit             = 50,
			start             = 0,
			fetchProgressData = function ( ) {
				if (members_cnt !== false) {
					start_time = new Date().getTime();

					// Get data
					$.ajax({
						url      : form.attr('action'),
						dataType : 'json',
						data     : [
							{'name': 'action', 'value': 'getprogressrows'},
							{'name': 'limit', 'value': limit},
							{'name': 'limitstart', 'value': start}
						],
						success  : function ( data, textStatus, jqXHR ) {
							if (data.members && data.members.length > 0) {
								var source   = $('#progress-template-row').html(),
									template = Handlebars.compile(source),
									context  = {
										members      : data.members,
										grades       : data.grades,
										progress     : data.progress,
										passing      : data.passing,
										recognitions : data.recognitions,
										units        : units,
										course_id    : course_id
									},
									html = template(context);
									html = html.replace(/src\=\"\/data/g, 'src="data');

								$('.students').append(html);

								// Setup navbar
								var cTop      = $('.controls-wrap').offset().top,
									cMax      = $('.student').last().offset().top - 104;

								$(window).unbind('scroll').scroll({
									offset    : cTop,
									offsetMax : cMax
								}, HUB.Plugins.CoursesProgress.stickyNavigationProgress);
								$(window).trigger('scroll');

								end_time = new Date().getTime() - start_time;
								$('.fetching-rows-bar').stop(false, false).animate({'width':(((start+data.members.length)/members_cnt)*100)+'%'}, end_time, 'linear');
								start += limit;

								if (start < members_cnt) {
									fetchProgressData();
								} else {
									setTimeout(function() {
										$('.fetching-rows-inner').fadeOut(function ( ) {
											$(this).hide();
										});
									}, end_time + 1000);

									HUB.Plugins.CoursesProgress.afterProgressDataLoaded();
								}
							} else {
								$('.fetching-rows-inner').hide();
								$('.students').html('<p class="info">The section does not currently have anyone enrolled</p>');
							}
						}
					});
				}
			};

		// Get initial data
		$.ajax({
			url      : form.attr('action'),
			dataType : 'json',
			data     : [{'name': 'action', 'value': 'getprogressdata'}],
			success  : function ( data, textStatus, jqXHR ) {
				// Render template - main portion
				var source    = $('#progress-template-main').html(),
					template  = Handlebars.compile(source),
					context   = {gradepolicy : data.gradepolicy},
					html      = template(context);

				HUB.Plugins.CoursesProgress.toggleView(html, 'progress');
				HUB.Plugins.CoursesProgress.initializeProgress();

				units       = data.units;
				course_id   = data.course_id;
				members_cnt = data.members_cnt;

				fetchProgressData();
			}
		});
	},

	loadGradebookData: function ( )
	{
		var $    = this.jQuery,
			form = $('.progress-form');

		// Get data
		$.ajax({
			url      : form.attr('action'),
			dataType : 'json',
			data     : [{'name': 'action', 'value': 'getgradebookdata'}],
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
				context  = {
					assets    : data.assets,
					members   : data.members,
					grades    : data.grades,
					canManage : data.canManage
				};
				html     = template(context);

				// Insert the assets into their place
				$('.slidable-inner').html(html);

				HUB.Plugins.CoursesProgress.toggleView('', 'gradebook');

				// Resize table on resize window
				$(window).resize(HUB.Plugins.CoursesProgress.resizeTable);

				// Do initial resize and setup of events
				HUB.Plugins.CoursesProgress.resizeTable();
				HUB.Plugins.CoursesProgress.initializeGradebook();

				HUB.Plugins.CoursesProgress.members   = data.members;
				HUB.Plugins.CoursesProgress.assets    = data.assets;
				HUB.Plugins.CoursesProgress.canManage = data.canManage;
			}
		});
	},

	loadReportsData: function ( )
	{
		var $    = this.jQuery,
			form = $('.progress-form');

		// Get data
		$.ajax({
			url      : form.attr('action'),
			dataType : 'json',
			data     : [{'name': 'action', 'value': 'getreportsdata'}],
			success  : function ( data, textStatus, jqXHR ) {
				// Render template - main portion
				var source    = $('#reports-template-main').html(),
					template  = Handlebars.compile(source),
					context   = {stats: data.stats, assets: data.assets},
					html      = template(context);

				HUB.Plugins.CoursesProgress.toggleView(html, 'reports');
				HUB.Plugins.CoursesProgress.initializeReports();
			}
		});
	},

	toggleView: function ( html, active )
	{
		var $         = this.jQuery,
			form      = $('.progress-form'),
			controls  = $('.controls-wrap'),
			loading   = $('.loading'),
			container = $('.main-container'),
			inactive  = [];

		inactive = $.grep(HUB.Plugins.CoursesProgress.views, function ( v ) {
			return v != active;
		});

		// Insert into page
		if (html !== '') {
			form.html(html);
		}

		// Set proper class for styling
		$.each(inactive, function ( idx, val ) {
			container.removeClass(val+'-container');
			controls.find('.'+val+'_button').hide();
		});
		container.addClass(active+'-container');
		controls.find('.'+active+'_button').show();

		// Remove loading icon
		loading.fadeOut();

		// Fade in table and navigation
		form.fadeIn();
		container.css('opacity', 1);
		container.removeClass('maxHeaders fixedHeaders');
		controls.fadeIn();

		if (active === 'progress') {
			$('.fetching-rows-bar').css('width', 0);
			$('.fetching-rows-inner').fadeIn();
			container.css({
				'margin-left'  : 0,
				'margin-right' : 0
			});
		}
	},

	initializeButtons: function ( )
	{
		var $   = this.jQuery,
			c   = $('.main-container'),
			clk = function ( t, button ) {
				if (t.hasClass('active')) {
					return false;
				}

				c.css('opacity', 0.4);
				$('.navigation').hide();
				$('.controls .button').removeClass('active');
				$('.controls .'+button+'-button').addClass('active');
				$('.loading').show();
				$("#none").remove();

				if ($(window).scrollTop() > $('#page_main').offset().top) {
					$(window).scrollTop($('#page_main').offset().top);
				}

				return true;
			};


		c.off('click', '.controls .gradebook-button').on('click', '.controls .gradebook-button', function ( e ) {
			var t = $(this);

			if (clk(t, 'gradebook')) {
				HUB.Plugins.CoursesProgress.loadGradebookData();
			}
		});

		c.off('click', '.controls .reports-button').on('click', '.controls .reports-button', function ( e ) {
			var t = $(this);

			if (clk(t, 'reports')) {
				HUB.Plugins.CoursesProgress.loadReportsData();
			}
		});

		c.off('click', '.controls .progress-button').on('click', '.controls .progress-button', function ( e ) {
			var t = $(this);

			if (clk(t, 'progress')) {
				HUB.Plugins.CoursesProgress.loadProgressData();
			}
		});
	},

	initializeProgress: function ( )
	{
		var $  = this.jQuery,
			p  = $('.progress-container'),
			nl = p.offset().left,
			nr = ($(window).width() - (nl + p.outerWidth()));

		$('.controls-wrap').css({
			'right' : nr
		});

		p.off('click', '.student-clickable').on('click', '.student-clickable', function() {
			$(this).siblings('.student-details').slideToggle('slow');
		});

		var sliders = $('.grade-policy .slider');

		sliders.each(function() {
			var t = $(this);
			t.attr('readonly', true);

			var slider = $('<div class="slider"></div>').insertAfter(t).slider({
				min   : 0,
				max   : 100,
				value : $(this).val(),
				slide : function( event, ui ) {
					t.val(ui.value);
				}
			});
		});

		$(window).unbind('resize');

		// Add tooltips to units
		$('.progress-container .headers .cell .details').tooltip({
			position : 'center left',
			predelay : 250,
			tipClass : 'tooltip-left',
			offset   : [0, 5]
		});

		p.off('click', '.controls .policy').on('click', '.controls .policy', function ( e ) {
			if (!$(e.target).hasClass('disabled')) {
				$(this).toggleClass('active');
				$('.grade-policy').slideToggle();
				$('.grade-policy').toggleClass('open');
			}
		});

		p.off('click', '.sorter').on('click', '.sorter', function ( e ) {
			var list      = p.find('.students .student'),
				students  = $('.students'),
				target    = $(e.target),
				dir       = (target.data('sort-dir') == 'asc') ? 'desc' : 'asc',
				val       = (dir == 'asc') ? 1 : -1,
				func      = function () {},
				sortAlpha = function ( a, b ) {
					var ret = ($.trim($(a).find('.name-value').html().toLowerCase()) > $.trim($(b).find('.name-value').html().toLowerCase())) ? val : val*-1;

					return ret;
				},
				sortScore = function ( a, b ) {
					var bar = '.student-progress-bar',
						ret = (parseFloat($(a).find(bar).data('score'), 10) > parseFloat($(b).find(bar).data('score'), 10)) ? val : val*-1;

					return ret;
				};

			switch (target.data('sort-val')) {
				case 'name' :
					func = sortAlpha;
				break;

				case 'score' :
					func = sortScore;
				break;
			}

			result = list.sort(func);
			students.html(result);
			target.data('sort-dir', dir);
		});

		p.off('click', '.grade-policy button[type="submit"]').on('click', '.grade-policy button[type="submit"]', function ( e ) {
			var f   = $('.progress-form'),
				d   = [];

			d.push({"name":"action",          "value":"policysave"});
			d.push({"name":"exam-weight",     "value":f.find('input[name="exam-weight"]').val()});
			d.push({"name":"quiz-weight",     "value":f.find('input[name="quiz-weight"]').val()});
			d.push({"name":"homework-weight", "value":f.find('input[name="homework-weight"]').val()});
			d.push({"name":"threshold",       "value":f.find('input[name="threshold"]').val()});
			d.push({"name":"description",     "value":f.find('textarea[name="description"]').val()});
			d.push({"name":"no_html",         "value":'1'});

			e.preventDefault();

			// Submit save
			$.ajax({
				type     : "POST",
				url      : f.attr('action'),
				data     : d,
				dataType : 'json',
				success  : function ( data, textStatus, jqXHR ) {
					if (data.success) {
						HUB.Plugins.CoursesProgress.message(data.message, 'passed');

						setTimeout(function() {
							$('.controls .policy').trigger('click');
							p.css({'opacity' : 0.4});
							$('.loading').show();

							HUB.Plugins.CoursesProgress.loadProgressData();
						}, 500);
					} else {
						HUB.Plugins.CoursesProgress.message(data.message, 'error');
					}
				}
			});
		});

		p.off('click', '.restore-defaults').on('click', '.restore-defaults', function ( e ) {
			var f   = $('.progress-form'),
				d   = [];

			d.push({"name":"action",  "value":"restoredefaults"});
			d.push({"name":"no_html", "value":'1'});

			e.preventDefault();

			// Submit save
			$.ajax({
				type     : "POST",
				url      : f.attr('action'),
				data     : d,
				dataType : 'json',
				success  : function ( data, textStatus, jqXHR ) {
					if (data.success) {
						HUB.Plugins.CoursesProgress.message(data.message, 'passed');

						f.find('input[name="exam-weight"]').val(data.gradepolicy.exam_weight);
						f.find('input[name="exam-weight"]').siblings('.slider').slider('value', data.gradepolicy.exam_weight);
						f.find('input[name="quiz-weight"]').val(data.gradepolicy.quiz_weight);
						f.find('input[name="quiz-weight"]').siblings('.slider').slider('value', data.gradepolicy.quiz_weight);
						f.find('input[name="homework-weight"]').val(data.gradepolicy.homework_weight);
						f.find('input[name="homework-weight"]').siblings('.slider').slider('value', data.gradepolicy.homework_weight);
						f.find('input[name="threshold"]').val(data.gradepolicy.threshold);
						f.find('input[name="threshold"]').siblings('.slider').slider('value', data.gradepolicy.threshold);
						f.find('textarea[name="description"]').val(data.gradepolicy.description);

						setTimeout(function() {
							$('.controls .policy').trigger('click');
							p.css({'opacity' : 0.4});
							$('.loading').show();

							HUB.Plugins.CoursesProgress.loadProgressData();
						}, 500);
					} else {
						HUB.Plugins.CoursesProgress.message(data.message, 'error');
					}
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

	afterProgressDataLoaded: function ( )
	{
		var $ = this.jQuery;

		// Add tooltips to units
		$('.progress-container .unit-fill').tooltip({
			predelay : 250,
			tipClass : 'tooltip-top',
			offset   : [-10, 0]
		});
	},

	initializeGradebook: function ( )
	{
		var $ = this.jQuery,
			g = $('.gradebook-container'),
			f = $('.progress-form');

		// Add tool tips to form title and student names
		$('.cell-title').tooltip({
			position : 'center right',
			offset   : [0, 5],
			tipClass : 'tooltip-right',
			predelay : 400
		});

		// Add fancy select boxes
		//$('.form-type select').HUBfancyselect();

		var s      = g.find('.slidable-inner'),
			slider = $('.slider'),
			origx  = 0,
			curx   = 0,
			diff   = 0,
			left   = 0,
			move   = 0,
			cols   = 0,
			num    = 0,
			act    = false;

		s.unbind('mousedown').mousedown(function ( e ) {
			origx = e.clientX;
			act   = true;
		});

		s.unbind('mousemove').mousemove(function ( e ) {
			if (act) {
				left = s.css('left').replace('px', '');
				curx = e.clientX;
				diff = (curx - origx) / 10;
				left = Number(left) + Number(diff);

				if (left <= 0 && Math.ceil(Math.abs(left)) <= Math.ceil(HUB.Plugins.CoursesProgress.offset)) {
					s.stop(true, true).animate({left:left}, 50);
				}
			}
		});

		s.unbind('mouseup').mouseup(function ( e ) {
			act = false;

			left = s.css('left').replace('px', '');
			cols = Math.round(Math.abs(left / HUB.Plugins.CoursesProgress.colWidth));
			num  = cols * HUB.Plugins.CoursesProgress.colWidth;

			HUB.Plugins.CoursesProgress.move(num, function() {}, cols);
		});

		var nxt = function() {
			if (Math.ceil(Math.abs(s.css('left').replace('px', ''))) < Math.ceil(HUB.Plugins.CoursesProgress.offset) && !s.is(':animated')) {
				var sv  = slider.slider('value'),
					cur = s.css('left').replace('px', ''),
					loc = Math.abs(Number(cur)) + Number(HUB.Plugins.CoursesProgress.colWidth);
				HUB.Plugins.CoursesProgress.move(loc, false, sv+=1);
			} else {
				$('.nxt').addClass('disabled');
			}
		};

		var prv = function() {
			if (Math.ceil(s.css('left').replace('px', '')) < 0 && !s.is(':animated')) {
				var sv  = slider.slider('value'),
					cur = s.css('left').replace('px', ''),
					loc = Math.abs(Number(cur)) - Number(HUB.Plugins.CoursesProgress.colWidth);
				HUB.Plugins.CoursesProgress.move(loc, false, sv-=1);
			} else {
				$('.prv').addClass('disabled');
			}
		};

		$('.nxt').unbind('click').click(nxt);

		$('.prv').unbind('click').click(prv);

		// Prevent form submission via "enter"
		$('.progress-form').submit(function ( e ) {
			e.preventDefault();
		});

		var emulateExcel = function ( e ) {
			var t      = $(this),
				s      = t.find('.cell-score'),
				c      = t.data('init-val'),
				r      = [],
				p      = {},
				n      = {},
				i      = {},
				item   = {},
				left   = 0,
				colnum = 0,
				hidden = 0,
				num    = 0,
				val    = 0,
				move   = false,
				slide  = $('.slidable-inner'),
				offset = HUB.Plugins.CoursesProgress.offset,
				colwid = HUB.Plugins.CoursesProgress.colWidth,
				rowCnt = HUB.Plugins.CoursesProgress.rowCnt,
				cnt    = HUB.Plugins.CoursesProgress.cnt;

			// Tab key
			if (e.keyCode === 9) {
				e.preventDefault();
				// Shift + tab
				if (e.shiftKey) {
					r = t.data('rownum').match(/cell-row([0-9]*)/i);
					p = t.parent('.gradebook-column').prev('.gradebook-column').find('.'+r[0]+':visible');
					n = t.parent('.gradebook-column').siblings().last().find('.cell-row'+(parseInt(r[1], 10)-1)+':visible');

					if (p.length) {
						// Make sure the next item isn't off the page
						left   = slide.css('left').replace('px', '');
						item   = $(t.parent('.gradebook-column'));
						colnum = item.data('colnum');
						hidden = Math.ceil(Math.abs(left / colwid));

						if (hidden > 0 && (colnum) <= hidden) {
							num = colwid * (colnum - 1);
							val = colnum - 1;

							if (!slide.is(':animated')) {
								HUB.Plugins.CoursesProgress.move(num, function() {
									p.trigger('click');
								}, val);
							}
						} else {
							p.trigger('click');
						}
					} else if (n.length) {
						// Make sure the next item isn't off the page
						var total  = $('.gradebook-container .gradebook-column:not(.gradebook-students)').length;

						if (offset > 0) {
							if (!slide.is(':animated')) {
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
					r = t.data('rownum').match(/cell-row([0-9]*)/i);
					n = t.parent('.gradebook-column').next('.gradebook-column').find('.'+r[0]+':visible');
					p = t.parent('.gradebook-column').siblings().first().find('.cell-row'+(parseInt(r[1], 10)+1)+':visible');

					if (n.length) {
						// Make sure the next item isn't off the page
						left   = slide.css('left').replace('px', '');
						item   = $(t.parent('.gradebook-column'));
						colnum = item.data('colnum');
						hidden = (offset - left) / colwid;
						move   = (hidden <= 0) ? false : true;
						hidden = Math.ceil(Math.abs(hidden));

						if (move && (colnum + 2) > (rowCnt - hidden)) {
							num = colwid * (colnum - cnt + 2);
							val = colnum - cnt + 2;

							if (!slide.is(':animated')) {
								HUB.Plugins.CoursesProgress.move(num, function() {
									n.trigger('click');
								}, val);
							}
						} else {
							n.trigger('click');
						}
					} else if (p.length) {
						// Make sure the next item isn't off the page
						left = slide.css('left').replace('px', '');

						if (left !== 0) {
							if (!slide.is(':animated')) {
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
				i = $(t.prev('.cell-entry'));
				if (i.length) {
					i.trigger('click');
				}
			// Down key
			} else if (e.keyCode === 40) {
				i = $(t.next('.cell-entry'));
				if (i.length) {
					i.trigger('click');
				}
			// Enter key
			} else if (e.keyCode === 13) {
				$('.edit-grade').blur();
			}
		};

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
						var f = $('.progress-form'),
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

		// Overload certain keys to emulate excel-like behavior
		g.off('keydown', '.cell-entry').on('keydown', '.cell-entry', emulateExcel);

		// Add click event to cells to enter edit mode
		g.off('click', '.form-title').on('click', '.form-title', function ( e ) {
			var t = $(this);

			if (!HUB.Plugins.CoursesProgress.canManage) {
				return;
			}

			if (!t.find('input').length) {
				var val = $.trim(t.parents('.form-name').attr('title'));

				// Store initial value
				t.data('init-val', $.trim(t.html()));

				// Edit form title and focus
				t.html('<input class="edit-title" type="text" name="title" value="'+val+'" />');
				t.find('input').focus();

				t.find('input').focusout(function() {
					if ($(this).val() != val) {
						var f = $('.progress-form'),
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

								if (max > 0) {
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
				f = $('.progress-form'),
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
						$('.gradebook-column[data-asset-id="'+t.parents('.gradebook-column').data('asset-id')+'"]').find('select').val(t.val());
					}
				});
		});

		g.off('click', '.override.active').on('click', '.override.active', function ( e ) {
			var t = $(this),
				f = $('.progress-form'),
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
				g = $('.gradebook-container');

			g.css('opacity', 0.4);
			$('.navigation').hide();
			$('.loading').show();
			$(".gradebook #none").remove();

			if ($(window).scrollTop() > $('#page_main').offset().top) {
				$(window).scrollTop($('#page_main').offset().top);
			}

			HUB.Plugins.CoursesProgress.loadGradebookData();
		});

		g.off('click', '.controls .export').on('click', '.controls .export', function ( e ) {
			var t = $(this),
				f = $('.progress-form'),
				a = f.attr('action');

			a += (a.search('/?/')) ? '&action=exportcsv' : '?action=exportcsv';

			window.open(a);
		});

		// Add a new gradebook item
		g.off('click', '.controls .addrow').on('click', '.controls .addrow',function() {
			var t = $(this),
				f = $('.progress-form'),
				d = [];

			d.push({"name":"action", "value":"savegradebookitem"});

			// Submit save
			$.ajax({
				type     : "POST",
				url      : f.attr('action'),
				data     : d,
				dataType : 'json',
				success  : function ( data, textStatus, jqXHR ) {
					var assets   = [],
						executed = false;
					assets.push({
						id    : data.id,
						title : data.title,
					});

					var insertColumn = function ( ) {
						if (!executed) {
							executed = true;
							// Render template
							var source    = $('#gradebook-template-asset').html(),
								template  = Handlebars.compile(source),
								context   = {
									members   : HUB.Plugins.CoursesProgress.members,
									assets    : assets,
									canManage : HUB.Plugins.CoursesProgress.canManage
								},
								html      = template(context),
								cnt       = HUB.Plugins.CoursesProgress.cnt;

							$('.slidable-inner').append(html);
							var slider  = $('.slider'),
								gbc     = $('.gradebook-container .gradebook-column:not(.gradebook-students)'),
								numCols = gbc.length,
								fsi     = $('.header-fixed-slidable-inner'),
								gbcl    = gbc.last();

							gbcl.attr('data-colnum', numCols-1);

							var val = parseInt(numCols - cnt, 10),
								loc = (numCols - cnt) * HUB.Plugins.CoursesProgress.colWidth;

							HUB.Plugins.CoursesProgress.resizeTable(
								function () {
									var offset = HUB.Plugins.CoursesProgress.offset;

									if (offset >= 0) {
										HUB.Plugins.CoursesProgress.move(loc, function() {
											if (fsi.length) {
												fsi.append(gbc.last().find('.cell.form-name').clone());
												gbcl.find('.form-name').hide();
												fsi.find('.cell').last().find('.form-title').trigger('click');
											} else {
												gbc.last().find('.form-title').trigger('click');
											}
										}, val);
									} else {
										gbc.last().find('.form-title').trigger('click');
									}
								}, false
							);
						}
					};

					$('html, body').animate({
						scrollTop : ($('#page_main').offset().top - 10)
					}, insertColumn);
				}
			});
		});

		// Delete a gradebook item
		g.off('click', '.form-delete').on('click', '.form-delete', function ( e ) {
			var t = $(this),
				f = $('.progress-form'),
				d = [],
				a = t.parents('.gradebook-column').data('asset-id');

			d.push({"name":"action",   "value":"deletegradebookitem"});
			d.push({"name":"asset_id", "value":a});

			// Make sure there aren't any overrides. If there are, warn that they will be lost
			var active  = false;
			var entries = t.parents('.gradebook-column').find('.cell-entry .override');
			entries.each(function ( idx, itm) {
				if ($(itm).hasClass('active')) {
					active = true;
				}
			});

			if (active) {
				var res = confirm('Deleting this gradebook item will delete all active overrides as well as mark the associated asset as not graded. Are you sure?');

				if (!res) {
					return false;
				}
			}

			// Submit save
			$.ajax({
				type     : "POST",
				url      : f.attr('action'),
				data     : d,
				dataType : 'json',
				success  : function ( data, textStatus, jqXHR ) {
					if ($(window).scrollTop() > $('#page_main').offset().top) {
						$(window).scrollTop($('#page_main').offset().top);
					}

					var column = $('.gradebook-container .gradebook-column[data-asset-id="'+a+'"]');
					column.hide('slide', {direction: 'up'}, 500, function () {
						$(this).remove();

						// Reset indices
						list = $('.gradebook-container .gradebook-column:not(.gradebook-students)');
						list.each(function ( idx, itm ) {
							$(itm).attr('data-colnum', idx);
						});

						HUB.Plugins.CoursesProgress.resizeTable(false, false);
					});
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
						$("#none").remove();
					}
				} else {
					$("#none").remove();
					$(".gradebook-container .cell").show();
				}
			});
		}
	},

	initializeReports: function ( )
	{
		var $ = this.jQuery,
			r = $('.reports-container');

		$(window).unbind('resize').unbind('scroll');

		//$('.checkbox input').uniform();

		// Remove margins if coming from gradebook tab and margins were present
		$('.main-container').css({
			'margin-right' : 0,
			'margin-left'  : 0
		});

		r.off('click', '.checkall').on('click', '.checkall', function ( e ) {
			var checkboxes = $('.download-checkbox');
			var checkall   = $('.checkall');
			checkboxes.prop("checked", checkall.prop("checked"));
		});

		r.off('click', '.controls .download').on('click', '.controls .download', function ( e ) {
			var f  = $('.progress-form'),
				a  = f.attr('action'),
				cb = $('.download-checkbox'),
				p  = false;
				d  = [];

			cb.each(function ( idx, val ) {
				if ($(val).prop('checked')) {
					p = true;
					d.push($(val).val());
				}
			});

			if (!p) {
				alert("Please select the assessments for which detailed results should be downloaded");
				return;
			}

			a += (a.search(/\?/) >= 0) ? '&action=downloadresponses&assets='+d.join('-') : '?action=downloadresponses&assets='+d.join('-');

			window.open(a);
		});

		r.off('click', '.result-details').on('click', '.result-details', function ( e ) {
			var t       = $(this),
				assetId = t.data('asset-id'),
				src     = window.location.href + ((window.location.href.search(/\?/) >= 0) ? '&' : '?') + 'action=assessmentdetails&asset_id='+assetId+'&tmpl=component';

			$.contentBox({
				src   : src,
				title : $(this).siblings('.form-title').html()
			});
		});
	},

	resizeTable: function ( callback, scroll )
	{
		var $ = this.jQuery;

		if ($.type(scroll) !== 'boolean') {
			scroll = true;
		}

		// Reset slidable 'right' before doing width calculations
		$('.slidable').css({right : 0});
		$('.main-container').css({
			'margin-right' : 0,
			'margin-left'  : 0
		});
		$('.slidable-outer').removeClass('nodata');

		// Calculate width of each column (range 100 - 150 px)
		var w      = $('.slidable-outer').width(),
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
		//$('.slidable').css({right :'-'+width+'px'});
		$('.slidable').css({right :'-999999em'});
		$('.slidable-inner').css({left : 0});

		// Disable prev button by default
		$('.prv').addClass('disabled');

		// Check if all columns are showing and disable next button as necessary
		if (offset <= 0) {
			$('.nxt').addClass('disabled');
			if (rowCnt > 0) {
				$('.main-container').css({
					'margin-right' : ((offset*-1)/2)+'px',
					'margin-left'  : ((offset*-1)/2)+'px'
				});
			} else {
				$('.slidable-outer').addClass('nodata');
			}
			$('.navigation').fadeOut();
		} else {
			$('.nxt').removeClass('disabled');
			$('.navigation').fadeIn();
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

		// Setup navbar
		var container = $('.main-container'),
			navLeft   = container.offset().left,
			navRight  = ($(window).width() - (navLeft + container.outerWidth())),
			cTop      = $('.controls').offset().top,
			cMax      = $('.gradebook-column .cell:last-child').offset().top - 115,
			nMax      = $('.gradebook-column .cell:last-child').offset().top + 52;

		$('.navigation').css({
			'left'     : navLeft,
			'right'    : navRight
		});

		$('.controls-wrap').css({
			'right'    : navRight
		});

		if (scroll === true) {
			$(window).unbind('scroll').scroll({
				offset    : cTop,
				offsetMax : cMax,
				navMax    : nMax
			}, HUB.Plugins.CoursesProgress.stickyNavigationGradebook);
		}

		// Initialize the rest of the page
		HUB.Plugins.CoursesProgress.cnt      = cnt;
		HUB.Plugins.CoursesProgress.colWidth = width;
		HUB.Plugins.CoursesProgress.offset   = offset;
		HUB.Plugins.CoursesProgress.rowCnt   = rowCnt;

		if ($.type(callback) === 'function') {
			callback();
		}

		if (scroll === true) {
			$(window).trigger('scroll');
		}
	},

	stickyNavigationProgress: function ( e )
	{
		var $ = this.jQuery,
			scroll     = $(this).scrollTop(),
			container  = $('.main-container'),
			navHeaders = $('.nav-header-fixed'),
			offset     = scroll - container.offset().top + 60;

		if (scroll >= e.data.offsetMax) {
			container.removeClass('fixedHeaders').addClass('maxHeaders');
			$('.controls-wrap .policy').addClass('disabled');
			$('.maxHeaders .main-headers').css('top', (e.data.offsetMax - container.offset().top + 60));

			$('.maxHeaders .controls-wrap').css({
				'top'   : (e.data.offsetMax - container.offset().top)
			});

			$('.nav-header-fixed').remove();
			$('.controls-wrap').show();
			$('.main-headers').show();
		} else if (scroll >= e.data.offset && !navHeaders.length) {
			var html = $('<div class="nav-header-fixed"></div>'),
				cntr = $('.controls-wrap'),
				head = $('.main-headers'),
				offl = $('.students').offset().left,
				offr = ($(window).width() - (offl + $('.students').outerWidth()));

			container.removeClass('maxHeaders').addClass('fixedHeaders');

			if ($('.grade-policy').hasClass('open')) {
				cntr.find('.policy').trigger('click');
			}
			cntr.find('.policy').addClass('disabled');
			if (cntr.css('opacity') !== 1) {
				cntr.css('opacity', 1);
			}


			html.css({
				left  : offl,
				right : offr
			});

			html.append(cntr.clone());
			html.append(head.clone());
			cntr.hide();
			head.hide();

			$('.progress-form').append(html);
		} else if (scroll <= e.data.offset && navHeaders.length) {
			$('.main-container').removeClass('fixedHeaders maxHeaders');
			$('.nav-header-fixed').remove();
			$('.controls-wrap').show();
			$('.main-headers').show();

			$('.controls-wrap .policy').removeClass('disabled');
		}
	},

	stickyNavigationGradebook: function ( e )
	{
		var $ = this.jQuery,
			scroll     = $(this).scrollTop(),
			container  = $('.main-container'),
			navigation = $('.navigation'),
			navHeaders = $('.nav-header-fixed'),
			offset     = scroll - container.offset().top + 60;

		// Only worry about this if there are students in the list
		if ($('.cell-entry').length) {
			if (scroll >= e.data.offsetMax) {
				container.removeClass('fixedHeaders').addClass('maxHeaders');
				$('.maxHeaders .gradebook-column .cell:first-child').css('top', (e.data.offsetMax - container.offset().top + 60));

				$('.maxHeaders .controls-wrap').css({
					'top'   : (e.data.offsetMax - container.offset().top)
				});

				$('.nav-header-fixed').remove();
				$('.controls-wrap').show();
				$('.gradebook-column .cell:first-child').show();
			} else if (scroll >= e.data.offset && !navHeaders.length) {
				var html = $('<div class="nav-header-fixed"></div>'),
					cntr = $('.controls-wrap'),
					head = $('.gradebook-column .cell:first-child'),
					offl = $('.gradebook-container-inner').offset().left,
					offr = ($(window).width() - (offl + $('.gradebook-container-inner').outerWidth()));

				container.removeClass('maxHeaders').addClass('fixedHeaders');

				headers = '';
				head.each(function ( i, v ) {
					var orig = v;
					v = $(v).clone();
					$(v).css('top', 0);

					if (i === 0) {
						$(v).css('top', 60);
					}

					if (i === 1) {
						headers += '<div class="header-fixed-slidable"><div class="header-fixed-slidable-inner">';
					}

					if (i !== 0) {
						headers += '<div class="gradebook-column" data-asset-id="'+$(orig).parent().data('asset-id')+'">';
					}

					headers += v[0].outerHTML;

					if (i !== 0) {
						headers += '</div>';
					}

					if (i === head.length) {
						headers += '</div></div>';
					}
				});

				html.css({
					left  : offl,
					right : offr
				});

				html.append(cntr.clone());
				html.append('<div class="main-headers">'+headers+'</div>');
				cntr.hide();
				head.hide();

				html.find('.header-fixed-slidable-inner').css('left', $('.slidable-inner').css('left'));
				container.append(html);
			} else if (scroll <= e.data.offset && navHeaders.length) {
				container.removeClass('fixedHeaders maxHeaders');
				$('.nav-header-fixed').remove();
				$('.controls-wrap').show();
				$('.gradebook-column .cell:first-child').css('top', 0).show();
			}

			if (navigation.offset().top >= e.data.navMax && navigation.hasClass('fixed')) {
				navigation.removeClass('fixed');
			} else if ((navigation.offset().top + 40 - window.innerHeight) >= window.pageYOffset ) {
				navigation.addClass('fixed');
			}
		}
	},

	move: function ( loc, callback, val )
	{
		var $ = this.jQuery,
			s = $('.slidable-inner');

		if ($.type(val) === 'number') {
			$('.slider').slider('value', val);
		}

		var fsi = $('.header-fixed-slidable-inner');

		if (fsi.length) {
			fsi.stop(true).animate({left:'-'+(loc)+'px'}, 'fast');
		}
		s.stop(true).animate({left:(loc*-1)+'px'}, 'fast', function ( e ) {
			var l = Math.round(Math.abs(loc)),
				o = Math.round(HUB.Plugins.CoursesProgress.offset);

			if ((l === 0 && o === 0) || (l === 0 && o < 0)) {
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
	},

	message: function ( text, type, timeout )
	{
		var $ = this.jQuery,
			m = $('#message-container');

		m.html(text);
		m.attr('class', type);

		m.show('slide', {'direction':'up'});

		if ($.type(timeout) !== 'number') {
			timeout = 4000;
		}

		setTimeout(function() {
			m.fadeOut('slow');
		}, timeout);
	},

	initHandlebarsHelpers: function ( )
	{
		var $ = this.jQuery;

		Handlebars.registerHelper('getFill', function ( progress, member_id ) {
			var fill = 0;
			if ($.type(progress) !== 'object' ||
				$.type(progress[member_id]) === 'undefined' ||
				$.type(progress[member_id][this.id]) === 'undefined' ||
				$.type(progress[member_id][this.id]['percentage_complete']) === 'undefined') {
					fill = 0;
			} else {
				fill = progress[member_id][this.id]['percentage_complete'];
			}

			var classname = (fill == 100) ? ' complete' : '',
				margin    = (100 - fill),
				buffer    = [
					'<div class="unit-fill" title="'+this.title+' ('+fill+'%)">',
					'<div class="unit-fill-inner'+classname+'" style="height:'+fill+'%;margin-top:'+margin+'%;"></div>',
					'</div>'
			].join("");

			return new Handlebars.SafeString(buffer);
		});
		Handlebars.registerHelper('getBar', function ( grades, passing, course_id ) {
			var classname = '',
				grade     = 0;
			if ($.type(grades) !== 'object' ||
				$.type(grades[this.id]) === 'undefined' ||
				$.type(grades[this.id]['course']) === 'undefined' ||
				$.type(grades[this.id]['course'][course_id]) === 'undefined') {
				// Do nothing - use defaults
			} else {
				if (grades[this.id]['course'][course_id] !== null) {
					grade = grades[this.id]['course'][course_id];
				}

				if($.type(passing[this.id]) !== 'undefined' && passing[this.id] === 1) {
					classname = ' go';
				} else if ($.type(passing[this.id]) !== 'undefined' && passing[this.id] === 0) {
					classname = ' stop';
				}
			}

			var buffer = [
				'<div class="student-progress-bar'+classname+'" style="width:'+grade+'%;" data-score="'+grade+'">',
				'<div class="score-text">'+grade+'</div>',
				'</div>'
			].join("");

			return new Handlebars.SafeString(buffer);
		});
		Handlebars.registerHelper('getScore', function ( type, grades, member_id, type_id ) {
			var grade = '--';
			if ($.type(grades) !== 'object' ||
				$.type(grades[member_id]) === 'undefined' ||
				$.type(grades[member_id][type]) === 'undefined' ||
				$.type(grades[member_id][type][type_id]) === 'undefined') {
				// Do nothing - use defaults
			} else {
				if (grades[member_id][type][type_id] !== null) {
					grade = grades[member_id][type][type_id]+'%';
				}
			}

			return grade;
		});
		Handlebars.registerHelper('countUnits', function ( units ) {
			return units.length;
		});
		Handlebars.registerHelper('getGrade', function ( grades, member_id, asset_id ) {
			if ($.type(grades) !== 'object' || $.type(grades[member_id]) === 'undefined' || $.type(grades[member_id]['assets']) === 'undefined' || $.type(grades[member_id]['assets'][asset_id]) === 'undefined') {
				return '';
			} else {
				return grades[member_id]['assets'][asset_id]['score'];
			}
		});
		Handlebars.registerHelper('hasEarned', function ( recognitions, id ) {
			return ($.inArray(id.toString(), recognitions) >= 0) ? new Handlebars.SafeString(' earned') : '';
		});
		Handlebars.registerHelper('ifAreEqual', function ( val1, val2 ) {
			return (val1 === val2) ? new Handlebars.SafeString(' selected="selected"') : '';
		});
		Handlebars.registerHelper('ifIsForm', function ( val1, val2 ) {
			return (this.type === 'form') ? new Handlebars.SafeString('<input type="checkbox" class="download-checkbox" name="download['+this.id+']" value="'+this.id+'" />') : '';
		});
		Handlebars.registerHelper('resultDetails', function ( val1, val2 ) {
			return (this.type === 'form') ? new Handlebars.SafeString('<div class="result-details" data-asset-id="'+this.id+'"></div>') : '';
		});
		Handlebars.registerHelper('shorten', function ( title, length ) {
			return (title.length < length) ? title : title.substring(0, length)+'...';
		});
		Handlebars.registerHelper('ifIsOverride', function ( grades, member_id, asset_id ) {
			if ($.type(grades) !== 'object' || $.type(grades[member_id]) === 'undefined' || $.type(grades[member_id]['assets']) === 'undefined' || $.type(grades[member_id]['assets'][asset_id]) === 'undefined') {
				return '';
			} else if (grades[member_id]['assets'][asset_id]['override']) {
				return ' active';
			} else {
				return '';
			}
		});
		Handlebars.registerHelper('getStat', function ( stats, asset_id, type ) {
			var stat = '--';
			if ($.type(stats) !== 'object' ||
				$.type(stats[asset_id]) === 'undefined' ||
				$.type(stats[asset_id][type]) === 'undefined') {
				// Do nothing - use defaults
			} else {
				if (stats[asset_id][type] !== null) {
					stat = stats[asset_id][type];
				}
				if (type !== "responses") {
					stat = stat + '%';
				}
			}

			return stat;
		});
	}
};
