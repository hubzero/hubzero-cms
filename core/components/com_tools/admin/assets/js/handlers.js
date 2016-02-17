/**
 * @package     hubzero-cms
 * @file        components/com_tools/admin/assets/js/handlers.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;
	var counter = $('.rule').length;


	$('.new-rule').click(function ( e ) {
		e.preventDefault();

		var rule = $('.rule-sample').clone().hide().removeClass('rule-sample');
		$('.rules').append(rule);

		counter++;

		rule.find('#field-extension-new')
			.attr('name', 'rules[' + counter + '][extension]')
			.attr('id', 'field-extension-' + counter);
		rule.find('#field-quantity-new')
			.attr('name', 'rules[' + counter + '][quantity]')
			.attr('id', 'field-quantity-' + counter);
		rule.fadeIn();

		// If the template is using uniform, we have to remove
		// and readd because we changed the id above
		if (!!$.prototype.uniform) {
			$.uniform.restore('#field-quantity-' + counter);
			$('#field-quantity-' + counter).uniform();
		}
	});

	$('.rules').on('click', '.delete-rule', function ( e ) {
		e.preventDefault();

		$(this).parent('.rule').fadeOut(function ( e ) {
			$(this).remove();
		});
	});
});
