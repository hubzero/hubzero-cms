
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

FORMS = HUB.FORMS

FORMS.$sortForm
FORMS.sortFormId = 'sort-form'
FORMS.directionDataAttribute = 'sort-direction'
FORMS.directionOpposites = { asc: 'desc', desc: 'asc' }
FORMS.fieldDataAttribute = 'sort-field'
FORMS.responsesListId = 'response-list'
FORMS.sortDirectionInputName="sort_direction"
FORMS.sortFieldInputName="sort_field"

FORMS.getResponsesList = () => {
	return $(`#${FORMS.responsesListId}`)
}

FORMS.registerSortHandlers = ($list) => {
	$list.on('click', '.sortable', FORMS.sortByField)
}

FORMS.sortByField = (e) => {
	const sortData = FORMS.getSortData(e)

	FORMS.submitSortForm(sortData)
}

FORMS.getSortData = (e) => {
	const $columnHeader = FORMS.getColumnHeader(e)

	return {
		direction: $columnHeader.data(FORMS.directionDataAttribute),
		field: $columnHeader.data(FORMS.fieldDataAttribute)
	}
}

FORMS.getColumnHeader = (e) => {
	const $target = $(e.target)

	return $target.closest('td')
}

FORMS.submitSortForm = (sortingData) => {
	FORMS.setSortForm()
	FORMS.populateSortForm(sortingData)
	FORMS.$sortForm.submit()
}

FORMS.setSortForm = () => {
	if (FORMS.$sortForm == undefined) {
		FORMS.$sortForm = $(`#${FORMS.sortFormId}`)
	}
}

FORMS.populateSortForm = (sortingData) => {
	FORMS.populateDirection(sortingData)
	FORMS.populateField(sortingData)
}

FORMS.populateDirection = ({direction}) => {
	const newDirection = FORMS.getNewDirection(direction)
	const $directionInput = FORMS.getInput(FORMS.sortDirectionInputName)

	$directionInput.val(newDirection)
}

FORMS.getNewDirection = (direction) => {
	return FORMS.directionOpposites[direction]
}

FORMS.populateField = ({field}) => {
	const $fieldInput = FORMS.getInput(FORMS.sortFieldInputName)

	$fieldInput .val(field)
}

FORMS.getInput = (inputName) => {
	return FORMS.$sortForm.find(`[name="${inputName}"]`)
}
