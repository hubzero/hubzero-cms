/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(function($) {
	if ($('#time-left').length) {
		window.timeLeft = $('#time-left').attr('data-time')
	}
	var timeDiff = function(secs) {
		var seconds = [1,               'second'];
		var minutes = [60 * seconds[0], 'minute'];
		var hours   = [60 * minutes[0], 'hour'];
		var days    = [24 * hours[0],   'day'];
		var weeks   = [7  * days[0],    'week'];

		var rv = [];
		var units = [weeks, days, hours, minutes, seconds];

		for (var idx = 0; idx < units.length; ++idx) {
			var sec  = units[idx][0];
			var unit = units[idx][1];
			var times = Math.floor(secs / sec);

			if (times > 0) {
				secs -= sec * times;
				rv.push(times + ' ' + unit + (times == 1 ? '' : 's'));
				if (rv.length == 2) {
					break;
				}
			} else if (rv.length) {
				break;
			}
		}

		return rv.length ? rv.join(', ') : '0 seconds';
	};

	if (window.timeLeft !== undefined) {
		var counter = $('<div id="time-left"></div>');
		var over    = false;
		$(document.body).append(counter);
		setInterval(function() {
			window.timeLeft = Math.max(0, window.timeLeft - 1);
			counter.text(timeDiff(window.timeLeft) + ' remaining');
			if (window.timeLeft === 0 && !over) {
				$('#time-left').addClass('ending-soon');
				$('.placeholder').attr('disabled', 'disabled');
				over = true;

				var btn = $('button[type="submit"]');
				btn.removeClass('btn-primary').addClass('btn-warning icon-warning');
				$('html, body').animate({
					scrollTop : (btn.offset().top)
				});
			} else if (window.timeLeft < 60) {
				$('#time-left').addClass('ending-soon');
			}
		}, 1000);
	}
	$('.placeholder').change(function(evt) {
		var inp = $(evt.target);
		$.post(window.location.href.match(/(.*)form.complete/)[1], {
			'task'       : 'saveProgress',
			'controller' : 'form',
			'crumb'      : window.location.search.toString().match(/crumb=([^&]+)/)[1],
			'question'   : inp.attr('name').match(/\d+/)[0],
			'answer'     : inp.val(),
			'attempt'    : $('form input[name="attempt"]').val()
		});
	});
	$('form').submit(function() {
		$('.placeholder').removeAttr('disabled');
	});
});
