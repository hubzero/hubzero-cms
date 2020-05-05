/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ($) {
	var s = $('#plg_resources_coins');
	var input = s.parent();

	if (input.length > 0) {
		var btn = $('#add-resource-type');

		btn.on('click', function(e){
			e.preventDefault();

			var source   = s.html(),
				template = Handlebars.compile(source),
				context  = {
					"index"  : input.find('.coinstypes').length
				},
				html = template(context);
				//input.before(btn);
				$(html).insertBefore(btn);
		});

		//input.after(btn);
	}
});
