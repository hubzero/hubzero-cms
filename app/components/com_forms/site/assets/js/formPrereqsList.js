
var $prereqItem
const destroyButtonClass = 'destroy-button'
const prereqItemClass = 'prereq-item'
const prereqsListAreaClass = 'prereqs-list-area'
const prereqsNoneNotice = 'There are no steps associated with this form at the moment.'
const prereqsUpdateButtonClass = 'steps-update-button'

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
	$destroyButtons.click(destroyPrereq)
}

const destroyPrereq = (e) => {
	e.preventDefault()

	const prereq = getPrereq(e)

	prereq.destroy().then(handlePrereqDestroy)
}

const getPrereq = (e) => {
	const id = getPrereqId(e)

	return new HUB.FORMS.FormPrerequisite({ id })
}

const getPrereqId = (e) => {
	const $destroyButton = $(e.target)
	$prereqItem = $destroyButton.closest('li')
	const prereqId = $prereqItem.data('id')

	return prereqId
}

const handlePrereqDestroy = (response) => {
	if (response.status === 'success') {
		$prereqItem.remove()
		updateDomPrereqsForm()
	}

	notifyUser(response)
}

const updateDomPrereqsForm = () => {
	const $prereqItems = $(`.${prereqItemClass}`)

	if ($prereqItems.length === 0) {
		insertNoneNotice()
		removeUpdateButton()
	}
}

const insertNoneNotice = () => {
	const $prereqsListArea = $(`.${prereqsListAreaClass}`);

	$prereqsListArea.html(`<h2 class="none-notice">${prereqsNoneNotice}</h2>`)
}

const removeUpdateButton = () => {
	const $udpateButton = $(`.${prereqsUpdateButtonClass }`)

	$udpateButton.remove()
}

const notifyUser = ({message, status}) => {
	HUB.FORMS.Notify[status](message)
}
