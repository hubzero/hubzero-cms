
FORMS.populateSortForm = (sortingData) => {
	FORMS.populateDirection(sortingData)
	FORMS.populateField(sortingData)
	FORMS.populateResponseIds()
}

FORMS.populateResponseIds = () => {
	const $responseIdInputs = $('input[name^="response_ids"]')

	FORMS.$sortForm.append($responseIdInputs)
}

$(document).ready(() => {

	FORMS.$responsesList = FORMS.getResponsesList()

	FORMS.registerSortHandlers(FORMS.$responsesList)

})
