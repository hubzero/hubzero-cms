/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(document).ready(function() {

	$("input.numericOnly").numericOnly();

});

jQuery.fn.numericOnly = function()
{
	return this.each(function()
	{
		$(this).keydown(function(e)
		{
			var key = e.charCode || e.keyCode || 0;

			if (e.shiftKey || e.ctrlKey || e.altKey) {
				// only allow shift tab
				if (!((e.shiftKey) && key == 9)) {
					return false;
				}
			}

			// allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
			return (
				key == 8 || 
				key == 9 ||
				key == 46 ||
				(key >= 37 && key <= 40) ||
				(key >= 48 && key <= 57) ||
				(key >= 96 && key <= 105)
			);
		});
	});
};