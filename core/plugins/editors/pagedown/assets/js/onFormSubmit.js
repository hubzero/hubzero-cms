
const INPUT_ID = 'wmd-input'

$(document).ready(() => {
	const $editorTextarea = $(`#${INPUT_ID}`)
	const $inputParentForm = $editorTextarea.closest('form')

	$inputParentForm.on('submit', (e) => {
		e.preventDefault()

		const markdown = $editorTextarea.val()
		const html = converter.makeHtml(markdown)
		$editorTextarea.val(html)

		$inputParentForm.unbind('submit').submit()
	})
})
