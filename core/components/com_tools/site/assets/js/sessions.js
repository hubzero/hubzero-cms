/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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

	var holdTheInterval = setInterval(function(){
			/*$.get('/index.php?option=com_tools&controller=storage&task=diskusage&no_html=1&msgs=0', {}, function(data) {
				$('#diskusage').html(data);
			}, 'html');*/
			$.get($('#diskusage').attr('data-base') + '/api/members/tools/diskusage', {}, function(data) {
				if (data && $.type(data.amount) === "number" && $.type(data.total) === "number") {
					$('#diskusage .du-amount-bar').css('width', data.amount+'%');
					$('#diskusage .du-amount-text').html(data.amount+'% of '+data.total+'GB');
				}
			}, 'JSON');
		}, 60 * 1000);

	var shrbtn = $('#share-btn');

	if (shrbtn.length) {
		shrbtn.on('click', function(event){
			event.preventDefault();

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

