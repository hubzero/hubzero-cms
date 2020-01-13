
var $pageItem
const destroyButtonClass = 'destroy-button'
const destroyConfirmText = 'Permanently destroy page and associated fields?'
const pageItemClass = 'page-item'
const pagesListAreaClass = 'pages-list-area'
const pagesNoneNotice = 'There are no pages associated with this form at the moment.'
const pagesUpdateButtonClass = 'pages-update-button'

$(document).ready(() => {
	Hubzero.initApi(() => {

		const $destroyButtons = getDestroyButtons()

		registerDestroySubmitHandler($destroyButtons)
	})
})

const getDestroyButtons = () => {
	const $destroyButtons = $(`.${destroyButtonClass}`)

	return $destroyButtons
}

const registerDestroySubmitHandler = ($destroyButtons) => {
	$destroyButtons.click(destroyPage)
}

const destroyPage = (e) => {
	e.preventDefault()

	if (confirm(destroyConfirmText)) {
		const page = getPage(e)

		page.destroy().then(handlePageDestroy)
	}
}

const getPage = (e) => {
	const id = getPageId(e)

	return new HUB.FORMS.FormPage({ id })
}

const getPageId = (e) => {
	const $destroyButton = $(e.target)
	$pageItem = $destroyButton.closest('li')
	const pageId = $pageItem.data('id')

	return pageId
}

const handlePageDestroy = (response) => {
	if (response.status === 'success') {
		$pageItem.remove()
		updateDomPagesForm()
	}

	notifyUser(response)
}

const updateDomPagesForm = () => {
	const $pageItems = $(`.${pageItemClass}`)

	if ($pageItems.length === 0) {
		insertNoneNotice()
		removeUpdateButton()
	}
}

const insertNoneNotice = () => {
	const $pagesListArea = $(`.${pagesListAreaClass}`);

	$pagesListArea.html(`<h2 class="none-notice">${pagesNoneNotice}</h2>`)
}

const removeUpdateButton = () => {
	const $udpateButton = $(`.${pagesUpdateButtonClass }`)

	$udpateButton.remove()
}

const notifyUser = ({message, status}) => {
	HUB.FORMS.Notify[status](message)
}
