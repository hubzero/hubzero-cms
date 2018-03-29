<?php
header('Content-Type: application/javascript');

$variableNames =['INPUT_ID'];

foreach ($variableNames as $name)
{
	echo "const $name = " . json_encode($_GET[$name] ? $_GET[$name] : null) . "\n";
}
?>

const htmlConverter = new HUB.PageDown.HtmlConverter()

$(document).ready(() => {
	const $editorTextarea = $(`#${INPUT_ID}`)
	const $inputParentForm = $editorTextarea.closest('form')

	// Convert HTML to Markdown for editing
	const html = $editorTextarea.val()
	const markdown = htmlConverter.toMarkdown(html)
	$editorTextarea.val(markdown)

	// Convert Markdown to HTML before form submission
	$inputParentForm.on('submit', (e) => {
		e.preventDefault()

		const markdown = $editorTextarea.val()
		const html = converter.makeHtml(markdown)
		$editorTextarea.val(html)

		$inputParentForm.unbind('submit').submit()
	})
})
