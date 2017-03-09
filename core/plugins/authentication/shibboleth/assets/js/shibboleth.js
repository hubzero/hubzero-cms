jQuery(function($) {
	var sa = $('.shibboleth.account'),
		sel = $('<select name="idp"">').attr('title', sa.data('placeholder'));

	sel.append($('<option class="placeholder">').text(sa.data('placeholder')));
	sa.find('li').each(function(_, li) {
		li = $(li);
		sel.append($('<option>')
			.data('content', li.data('content'))
			.val(li.data('entityid'))
			.text(li.text())
		);
	});
	sa.empty().append('<div class="default-icon">').append(sel);
		//sel = sa.children('select');
	// if we make the select box multiple-select we can remove the placeholder
	// <option> and let selectpicker manage the label. since we're watching the
	// change event anyway it doesn't matter whether selectpicker thinks it's
	// operating as a single- or multiple-select
	sel
		.prop('multiple', true)
		.children('.placeholder')
			.remove();

	var search = false;
	// let people search if it seems like the list length warrants it
	if ((search = /[&?]shib-search/.test(location.search) || sel.find('option').length > 10)) {
		sel.data('live-search', true);
	}

	var priorVal = null;

	// submit the form when a selection is made
	var sp = sel.selectpicker()
		.change(function(evt) {
			// workaround for using a multi-select control (for the otherwise hard-
			// to-emulate "none-selected")
			//
			// if the value just changed to none-selected it is because the user
			// selected the item that was already active again from the list,
			// possibly because they didn't notice it was filled out by default
			// from a prior occasion.
			//
			// in this case we'd rather submit the form with the value they just
			// clicked, which we will after redrawing the control
			if (!sel.val()) {
				sel.val([priorVal]);
				sp.val([priorVal]);
			}
			else {
				// only possible if there was an insitution prefilled but the user
				// clicked a different one. log in with the recently-clicked one
				// by eliminating the previous value from the selection
				sel.val(sel.val().filter(function(v) { return v != priorVal; }));
				priorVal = sp.val() ? sel.val()[0] : null;
			}
			sel.selectpicker('render');

			// we've hidden the submit button, making a selection is sufficient to
			// proceed
			// setTimeout makes the UI slightly nicer in that it gives a chance for
			// the selectpicker to update the selection before the browser starts
			// to wait on the form submission
			setTimeout(function() {
				var eid = sel.val();
				if (eid && eid[0]) {
					location = location.protocol + '//' + location.host + (location.port ? ':' + location.port : '') + '/login?authenticator=shibboleth&idp=' + encodeURIComponent(eid);
				}
			}, 0);
		})
		;
	priorVal = sp.val() ? sel.val()[0] : null;

	// also submit the form if there is a default institution filled in and the
	// user clicks the control (which does not fire 'change')
	sa.find('.btn-default').click(function(evt) {
		if (sel.val() && sel.val().length) {
			sa.submit();
		}
	});

	sa.find('input').attr('placeholder', 'Search');
	
	// Check to see if having placeholder triggers the input event on focus. If it does, remove placeholders to fix the bug found in IE10/IE11
	// See https://connect.microsoft.com/IE/feedback/details/810538/ie-11-fires-input-event-on-focus for more information about this bug.
	PlaceholderTest(sa);

	// the select control handles submission, the no-js fallback button can go away
	sa
		.addClass('selectpicked')
		.children('button.submit')
			.remove();
});

var PlaceholderTest = function(parent){
	var input = $('<input type="input" value="" placeholder="placeholder"/>');
	input.css('opacity','0')
	     .css('position','absolute')
	     .css('left', '-1000px');
	$('body').append(input);
	input.on('input',function(e){
		parent.find("input[placeholder]").removeAttr("placeholder");
		this.remove();
	});
	input.focus();
};
