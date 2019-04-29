<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

header('Content-Type: application/javascript');

$variableNames = ['INPUT_ID'];

foreach ($variableNames as $name)
{
	echo "const $name = " . json_encode($_GET[$name] ? $_GET[$name] : null) . "\n";
}
?>

const converter = new Markdown.Converter()
const sanitizer = Markdown.getSanitizingConverter()

$(document).ready(() => {

	const htmlConverter = new HUB.PageDown.HtmlConverter()
	let $editorTextareas = $(`[id^="${INPUT_ID}"]`)
	$editorTextareas = $editorTextareas.toArray().map((textarea) => $(textarea))
	const $parentForm = $editorTextareas[0].closest('form')

	// Instantiate additional Markdown Editors
	$editorTextareas.forEach(($textarea) => {
		const id = $textarea.attr('id')
		const idPostfix = id.replace(INPUT_ID, '')
		const editor = new Markdown.Editor(converter, idPostfix)

		editor.run()
	})

	// Convert HTML to Markdown for editing
	$editorTextareas.forEach(($textarea) => {
		const html = $textarea.val()
		const markdown = htmlConverter.toMarkdown(html)
		$textarea.val(markdown)
	})

	// Convert Markdown to HTML before form submission
	$parentForm.on('submit', (e) => {
		e.preventDefault()

		$editorTextareas.forEach(($textarea) => {
			const markdown = $textarea.val()
			const html = converter.makeHtml(markdown)
			$textarea.val(html)
		})

		$parentForm.unbind('submit').submit()
	})

})
