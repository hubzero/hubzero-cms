
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
