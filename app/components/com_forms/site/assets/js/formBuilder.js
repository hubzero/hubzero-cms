
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

class FormBuilder {

	constructor({$anchor}) {
		this.$anchor = $anchor
		this._builder = undefined
		this._defaultOptions = {
			disabledActionButtons: ['clear', 'data', 'save'],
			disabledAttrs: ['access', 'className', 'placeholder', 'style', 'subtype'],
			disableFields: ['autocomplete', 'button', 'file'],
		}
	}

	render(options = {}) {
		const combinedOptions = {...this._defaultOptions, ...options}

		this._builder = this.$anchor.formBuilder(combinedOptions)
	}

	getFields() {
		let fieldsState = this._builder.actions.getData('js')

		return fieldsState
	}

	setFields(fields) {
		const fieldsJson = JSON.stringify(fields)

		this._builder.actions.setData(fieldsJson)
	}

}

HUB.FORMS.FormBuilder = FormBuilder
