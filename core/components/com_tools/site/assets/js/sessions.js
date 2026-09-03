/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function(jq){
	var $ = jq;

	Hubzero.initApi();

	// Session countdown (hard-timeout tools only). Reads /api/tools/{sess}/timeout;
	// idle-timeout tools reset on activity, so they get no countdown.
	(function () {
		var el = document.getElementById('session-timer');
		if (!el) { return; }

		var sess = parseInt(el.getAttribute('data-sess'), 10);
		var base = (el.getAttribute('data-base') || '').replace(/\/+$/, '');
		if (!sess) { return; }

		var url  = base + '/api/tools/' + sess + '/timeout';
		var WARN = 15 * 60, CRIT = 5 * 60;
		var remaining = null, tick = null;

		function pad(n) { return (n < 10 ? '0' : '') + n; }
		function fmt(s) {
			s = Math.max(0, s | 0);
			return pad(Math.floor(s / 3600)) + ':' + pad(Math.floor((s % 3600) / 60)) + ':' + pad(s % 60);
		}
		function render() {
			$('#session-timer .st-clock').text(fmt(remaining));
			el.classList.toggle('warn', remaining <= WARN && remaining > CRIT);
			el.classList.toggle('crit', remaining <= CRIT);
		}
		function fetchTimeout() {
			$.ajax({ url: url, dataType: 'json', xhrFields: { withCredentials: true } })
				.done(function (data) {
					if (!data || !data.hard || data.seconds_remaining == null) {
						if (tick) { clearInterval(tick); tick = null; }
						el.hidden = true;
						return;
					}
					remaining = parseInt(data.seconds_remaining, 10);
					el.hidden = false;
					render();
					if (!tick) {
						tick = setInterval(function () {
							if (remaining == null) { return; }
							remaining = Math.max(0, remaining - 1);
							render();
							if (remaining === 0) { clearInterval(tick); tick = null; }
						}, 1000);
					}
				});
			// A failure (not the owner, or session gone) simply leaves it hidden.
		}

		fetchTimeout();
		setInterval(fetchTimeout, 60 * 1000);  // re-sync to correct drift
	})();

	if ($('#app-btn-options').length) {
		$('#app-btn-options').on('click', function (e){
			e.preventDefault();

			var par = $($(this).parent());
			if (par.hasClass('active')) {
				par.removeClass('active');
			} else {
				par.addClass('active');
			}
			$('#app-options').slideToggle();
		});
	}

	var pollDiskUsage = function(){
			/*$.get('/index.php?option=com_tools&controller=storage&task=diskusage&no_html=1&msgs=0', {}, function(data) {
				$('#diskusage').html(data);
			}, 'html');*/
			/*$.get($('#diskusage').attr('data-base') + '/api/members/tools/diskusage', {}, function(data) {
				if (data && $.type(data.amount) === "number" && $.type(data.total) === "number") {
					$('#diskusage .du-amount-bar').css('width', data.amount+'%');
					$('#diskusage .du-amount-text').html(data.amount+'% of '+data.total+'GB');
				}
			}, 'JSON');*/
			$.ajax({
                                url: $('#diskusage').attr('data-base') + '/api/members/tools/diskusage',
                                data : {}, 
                                dataType: "json"
                        }).done(function(data) {
                                if (data && $.type(data.amount) === "number" && $.type(data.total) === "number") {
                                        $('#diskusage .du-amount-bar').css('width', data.amount+'%');
                                        $('#diskusage .du-amount-text').html(data.amount+'% of '+data.total+'GB');
                                }
                        }).fail(function(jqXHR){
                                // Stop on a rejected or rate-limited token (a renewal
                                // restarts us below); a transient error is left alone
                                if (jqXHR.status == 401 || jqXHR.status == 403 || jqXHR.status == 429) {
                                        clearInterval(holdTheInterval);
                                        holdTheInterval = null;
                                }
                        });
		};

	var holdTheInterval = setInterval(pollDiskUsage, 60 * 1000);

	if (Hubzero.api) {
		Hubzero.api.onRenewed(function(){
			if (holdTheInterval === null) {
				holdTheInterval = setInterval(pollDiskUsage, 60 * 1000);
			}
		});
	}

	var shrbtn = $('#share-btn');

	if (shrbtn.length) {
		shrbtn.on('click', function(event){
			event.preventDefault();

			var shareConfirm = $('#confirm-share');

			if (!shareConfirm.attr('value')) {
				alert('Please check the acknowledgement checkbox indicating your acceptance of the fact that shared sessions may be altered and controlled by the users or groups you share the session with.');
				return;
			}

			// disable button
			$(this).attr('disabled', 'disabled');

			// get the form data
			var share = $("#app-share"),
				url   = share.attr("action").nohtml(),
				data  = share.serialize();

			// show message to user
			share
				.css('position', 'relative')
				.prepend('<div id="app-share-overlay" data-message="Hold on while we make the connections!" class="open" />')
				.hide()
				.fadeIn();

			// make ajax call to add share
			$.ajax({
				url: url,
				type: 'POST',
				data: data,
				//dataType: 'json',
				error: function(jqXHR, textStatus, errorThrown) {
					alert('We have experienced a server error while trying to share this tool session.\n\nYou could be seeing this error if you are trying to share with someone who already has sharing privledges.');

					// fade out message
					$("#app-share-overlay").delay(2000).fadeOut('slow', function(){
						// enable button
						shrbtn.removeAttr('disabled');

						// remove items from token list and clear actual hidden input
						$("#acmembers").tokenInput('clear');

						// reset group select box
						$("#group").val(0);

						// uncheck readonly 
						$("#readonly").removeAttr('checked');

						// uncheck readonly 
						$("#confirm-share").removeAttr('checked');
					});
				},
				success: function(data, status, jqXHR) {
					// reload share table
					$("#app-share .entries").html($(data).find('.entries > *'));

					// fade out message
					$("#app-share-overlay").delay(2000).fadeOut('slow', function(){
						// enable button
						shrbtn.removeAttr('disabled');

						// remove items from token list and clear actual hidden input
						$("#acmembers").tokenInput('clear');

						// reset group select box
						$("#group").val(0);

						// uncheck readonly 
						$("#readonly").removeAttr('checked');
					});
				}
			});
		});
	}

	$(".entries").on('click', '.entry-remove', function(event) {
		event.preventDefault();

		// get the url from link
		var url = $(this).attr('href').nohtml();

		// show message to user
		$("#app-share")
			.css('position', 'relative')
			.prepend('<div id="app-share-overlay" data-message="Closing Connections..." class="close" />')
			.hide()
			.fadeIn();

		$.ajax({
			url: url,
			type: 'GET',
			success: function(data, status, jqXHR) {
				//reload share table
				$("#app-share .entries").html($(data).find('.entries > *'));

				//fade out message
				$("#app-share-overlay").delay(1500).fadeOut('slow');
			}
		});
	});
});

