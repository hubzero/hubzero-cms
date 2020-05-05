/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	var bdy = $('#preview-body');

	if (bdy.length) {
		var form = window.top.document.adminForm
		var title = form.title.value;

		var alltext = form[bdy.attr('data-parent-text')];

		$('#preview-title').val(title);
		$('#preview-text').val(alltext);
	}
});
