
String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function ($) {
	$('a.simple')
		.on('click', function (e) {
			e.preventDefault();

			var container = $($(this).attr('data-target'));

			if (container.length) {
				// This example uses jQuery's $.get(), which is a sortcut
				// $.ajax(url, {method: "get"}). See the full example below.

				// We get the link's URL and append "no_html=1" to it
				// with the nohtml() function. This tells the CMS to
				// NOT render the surrounding template and only output
				// the contents of the component.
				$.get($(this).attr('href').nohtml(), function(result){
					// We take the returned results, which should be just the
					// HTML from the component and insert it into the container.
					container.html(result);
				});

				$('.sub-menu a').removeClass('active');
				$(this).addClass('active');
			}
		});


	$('a.verbose')
		.on('click', function (e) {
			e.preventDefault();

			var container = $($(this).attr('data-target'));

			if (container.length) {
				// We get the link's URL and append "no_html=1" to it
				// with the nohtml() function. This tells the CMS to
				// NOT render the surrounding template and only output
				// the contents of the component.
				$.ajax($(this).attr('href').nohtml(), {
					// The type of data that you're expecting back from the server.
					dataType: 'html',
					// A function to be called if the request succeeds.
					success: function(response, status, xhr) {
						container.html(response);
					},
					// A function to be called if the request fails.
					error: function(xhr, status, error) {
						console.log("An error occured while trying to load the page.");
					},
					// A function to be called when the request finishes (after success and error callbacks are executed).
					complete: function(xhr, status) {
						console.log("We're done now.");
					}
				});
			}
		});

	$('a.api')
		.on('click', function (e) {
			e.preventDefault();

			var container = $($(this).attr('data-target'));

			if (container.length) {
				$.ajax($(this).attr('data-source'), {
					// The type of data that you're expecting back from the server.
					dataType: 'json',
					// A function to be called if the request succeeds.
					success: function(response, status, xhr) {
						var source   = $('#new-row').html(),
							template = Handlebars.compile(source),
							html = '';

						$.each(response.posts, function(i, el){
							context  = {
								"id"   : el.id,
								"title"  : el.title,
								"url" : el.url
							},
							html += template(context);
						});

						container.html(html);
					},
					// A function to be called if the request fails.
					error: function(xhr, status, error) {
						console.log("An error occured while trying to load the page.");
					},
					// A function to be called when the request finishes (after success and error callbacks are executed).
					complete: function(xhr, status) {
						console.log("We're done now.");
					}
				});
			}
		});
});
