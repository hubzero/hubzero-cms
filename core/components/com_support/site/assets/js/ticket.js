jQuery(document).ready(function($) {
	$('.copy-link').click(function(e) {
		var me = $(this);
		e.preventDefault();
		copyTextToClipboard(me.attr('href'));

		$('.copy-link').removeClass('show-hover');
		$('.copy-link span.lbl').html('Copy link');
		me.addClass('show-hover').find('span.lbl').html('Copied');
	});
});

function fallbackCopyTextToClipboard(text) {
	var textArea = document.createElement("textarea");
	textArea.value = text;
	textArea.style.position = "fixed";
	textArea.style.dispaly = "none";
	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();

	try {
		document.execCommand('copy');
	}
	catch (err) {
	}

	document.body.removeChild(textArea);
}
function copyTextToClipboard(text) {
	if (!navigator.clipboard) {
		fallbackCopyTextToClipboard(text);
		return;
	}
	navigator.clipboard.writeText(text);
}