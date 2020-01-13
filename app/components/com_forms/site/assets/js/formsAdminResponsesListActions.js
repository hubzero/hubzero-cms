
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

FORMS = HUB.FORMS

FORMS.listActionsClass = 'list-action'
FORMS.responseIdFieldsName = 'response_ids[]'

FORMS.getResponseActionButtons = () => {
	return $(`.${FORMS.listActionsClass}`)
}

FORMS.registerListActionHandlers = ($listActions) => {
	$listActions.on('click', FORMS.submitActionForm)
}

FORMS.submitActionForm = (e) => {
	const $form = FORMS.getActionForm(e.target)

	if (FORMS.responsesSelected()) {
		FORMS.populateActionForm($form)
		$form.submit()
	} else {
		FORMS.adviseUserToSelectResponses()
	}
}

FORMS.getActionForm = (eventTarget) => {
	const $actionSpan = $(eventTarget).closest(`.${FORMS.listActionsClass}`)

	return $actionSpan.find('form')
}

FORMS.responsesSelected = () => {
	return FORMS.getSelectedResponsesIds().length > 0
}

FORMS.adviseUserToSelectResponses = () => {
	FORMS.Notify['warn']('Select at least one response from the list below')
}

FORMS.populateActionForm = ($form) => {
	const selectedResponsesIds = FORMS.getSelectedResponsesIds()

	FORMS.addSelectedResponsesIds($form, selectedResponsesIds)
}

FORMS.getSelectedResponsesIds = () => {
	const $selectedResponsesCheckboxes = FORMS.getSelectedResponsesCheckboxes()
	const selectedResponsesIds = []

	$selectedResponsesCheckboxes.each((i, checkbox) => {
		selectedResponsesIds.push($(checkbox).val())
	})

	return selectedResponsesIds
}

FORMS.getSelectedResponsesCheckboxes = () => {
	const responseCheckboxes = $(`input[name="${FORMS.responseIdFieldsName}"]`)

	const $selectedCheckboxes = responseCheckboxes.filter((i, checkbox) => {
		return $(checkbox).is(':checked')
	})

	return $selectedCheckboxes
}

FORMS.addSelectedResponsesIds = ($form, selectedResponsesIds) => {
	selectedResponsesIds.forEach((responseId) => {
		FORMS.appendResponseIdInput(responseId, $form)
	})
}

FORMS.appendResponseIdInput = (responseId, $form) => {
	const responseIdInput = $(`<input type="hidden" name="response_ids[${responseId}]" value="${responseId}">`)

	$form.append(responseIdInput)
}
