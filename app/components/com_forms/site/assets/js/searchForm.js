var FORMS = FORMS || {}

FORMS.searchForm = FORMS.searchForm || {}

searchForm = FORMS.searchForm

searchForm.CARET_CLASS = 'caret'
searchForm.CONTENT_CLASS = 'content'
searchForm.MASTER_CARET_CLASS = 'master-caret'
searchForm.ROW_CLASS = 'row'
searchForm.sidebarClass = '.landing-sidebar'

searchForm.init = () => {
	// Collect row content wrappers
	searchForm.rowContentWrappers = $(`${searchForm.sidebarClass} .${searchForm.CONTENT_CLASS}`)

	// Collect carets
	searchForm.carets = $(`.${searchForm.CARET_CLASS}`)

	// Collect master caret
	searchForm.masterCaret = $(`.${searchForm.MASTER_CARET_CLASS}`)
}

searchForm.caretHandler = (e) => {
	const $caret = $(e.target)
	const $rowContent = searchForm.findCaretRowContent($caret)

	if ($rowContent.is(':visible')) {
		$rowContent.slideUp(null, () => {
			searchForm.toggleCaretDirection($caret, $rowContent)
		})
	} else if ($rowContent.is(':hidden')) {
		$rowContent.slideDown(null, () => {
			searchForm.toggleCaretDirection($caret, $rowContent)
		})
	}
}

searchForm.toggleCaretDirection = ($caret, $rowContent = null) => {
	if (!$rowContent) {
		$rowContent =	searchForm.findCaretRowContent($caret)
	}
	let html

	if ($rowContent.is(':visible')) {
		html = '&#x2303;'
	} else if ($rowContent.is(':hidden')) {
		html = '&#x2304;'
	}

	$caret.html(html)
}

searchForm.findCaretRowContent = ($caret) => {
	const $row = $caret.closest(`.${searchForm.ROW_CLASS}`)
	const $rowContent = $row.find(`.${searchForm.CONTENT_CLASS}`)

	return $rowContent
}

searchForm.masterCaretHandler = (e) => {
	const $masterCaret = searchForm.masterCaret
	const $rowContentWrappers = searchForm.rowContentWrappers
	const visibleKey = 'visible'
	const rowsVisible = !!$masterCaret.data(visibleKey)

	if (rowsVisible) {
		searchForm.hide($rowContentWrappers, visibleKey)
	} else if (!rowsVisible) {
		searchForm.show($rowContentWrappers, visibleKey)
	}
}

searchForm.hide = ($rowContentWrappers, visibleKey) => {
	$rowContentWrappers.slideUp(null, () => {
		searchForm.toggleAllCarets('&#xf0d7;', {[visibleKey]: false})
	})
}

searchForm.show = ($rowContentWrappers, visibleKey) => {
	$rowContentWrappers.slideDown(null, () => {
		searchForm.toggleAllCarets('&#xf0d8;', {[visibleKey]: true})
	})
}

searchForm.toggleAllCarets = (html, data) => {
	const $masterCaret = searchForm.masterCaret

	$masterCaret.html(html)
	$masterCaret.data(data)
	searchForm.toggleAllCaretDirections()
}

searchForm.toggleAllCaretDirections = () => {
	const $carets = searchForm.carets

	$.each($carets, (_, caret) => {
		searchForm.toggleCaretDirection($(caret))
	})
}

$(document).ready(() => {

	// initialize search form
	searchForm.init()

	// add click handler to carets
	searchForm.carets.click(searchForm.caretHandler)

	// add click handler to master caret
	searchForm.masterCaret.click(searchForm.masterCaretHandler)

})

