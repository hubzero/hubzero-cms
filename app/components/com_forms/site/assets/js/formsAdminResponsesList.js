
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

FORMS = HUB.FORMS

$(document).ready(() => {

	FORMS.$responseActionButtons = FORMS.getResponseActionButtons()
	FORMS.$masterCheckbox = FORMS.getMasterCheckbox()
	FORMS.$responsesList = FORMS.getResponsesList()

	FORMS.registerCheckboxHandlers(FORMS.$masterCheckbox)
	FORMS.registerListActionHandlers(FORMS.$responseActionButtons)
	FORMS.registerSortHandlers(FORMS.$responsesList)

})
