
const anchorId = 'form-builder-anchor'
const submitId = 'fields-submit'
const notify = HUB.FORMS.Notify
var fieldTranslator
var formBuilder

const getPageId = () => {
	const pageIdInputName = 'page_id'
	const pageIdInput = $('input[name=page_id]')

	const pageId = pageIdInput.val()

	return pageId
}

const getPage = (id) => {
	const page = new HUB.FORMS.Page({id})

	return page
}

const getFormBuilder = (pageId) => {
	const $anchor = $(`#${anchorId}`)

	formBuilder = new HUB.FORMS.ComFormsFormBuilder({$anchor, pageId})

	return formBuilder
}

const getFieldTranslator = () => {
	const objectHelper = new HUB.FORMS.ObjectHelper()

	HUB.FORMS.ComFormsFieldTranslator.objectHelper = objectHelper
	fieldTranslator = new HUB.FORMS.ComFormsFieldTranslator()

	return fieldTranslator
}

const registerSubmitHandler = (page) => {
	const $submitButton = $(`#${submitId}`)

	$submitButton.click((e) => {
		submitForm(e, page)
	})
}

const submitForm = (e, page) => {
	e.preventDefault()

	const fields = formBuilder.getFields()
	const translatedFields = fieldTranslator.forServer(fields)

	page.setFields(translatedFields)
	page.save().then(notifyUser)
}

const notifyUser = ({message, status}) => {
	notify[status](message)
}

$(document).ready(() => {
	Hubzero.initApi(() => {

		const pageId = getPageId()
		const page = getPage(pageId)
		formBuilder = getFormBuilder(pageId)
		fieldTranslator = getFieldTranslator()

		formBuilder.render()

		page.fetchFields().then((response) => {
			const currentFields = response['associations']
			const translatedFields = fieldTranslator.forBuilder(currentFields)

			formBuilder.setFields(translatedFields)
		})

		registerSubmitHandler(page)
	})
})
