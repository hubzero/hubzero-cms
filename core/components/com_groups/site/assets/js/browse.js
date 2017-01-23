/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/browse.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function(jq){
	var $ = jq;

	$('.entries-filters select').on('change', function(e){
		$(this).closest('form').submit();
	});

	// Create the dropdown base
	$('.order-options').each(function(i, el){
		el = $(el);
		el.addClass('js');

		var select = $("<select />").on('change', function() {
			window.location = $(this).find("option:selected").val();
		});

		$("<option />", {
			"value"   : "",
			"text"    : el.attr('data-label')
		}).appendTo(select);

		el.find("a").each(function() {
			var elm = $(this);
			var opts = {
				"value"   : elm.attr("href"),
				"text"    : elm.text()
			};
			if (elm.hasClass('active')) {
				opts.selected = 'selected';
			}
			$("<option />", opts).appendTo(select);
		});

		var li = $("<li />").addClass('option-select');

		select.appendTo(li);
		li.appendTo(el);
	});
});

