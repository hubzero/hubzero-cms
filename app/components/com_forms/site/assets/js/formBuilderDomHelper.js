
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

class DomHelper {

	static get cmsIdsDataAttribute() {
		return 'data-cms-id'
	}

	constructor() {
		this._fieldActionButtonClass = 'btn'
	}

	removeCollidingClasses() {
		const $fieldActionButtons = this._getAllFieldActionButtons()

		this._removeBtnClass($fieldActionButtons)
	}

	_getAllFieldActionButtons() {
		const $fieldEditor = this._getFieldEditor()

		return $fieldEditor.find(`.${this._fieldActionButtonClass}`)
	}

	_getFieldEditor() {
		return $(`#${anchorId}`)
	}

	_removeBtnClass($fieldActionButtons) {
		$fieldActionButtons.removeClass(this._fieldActionButtonClass)
	}

	addCmsIdsToDom(virutalFields) {
		const domFields = this.getFieldDomElements()

		domFields.each((i, domField) => {
			const cmsId = virutalFields[i].id
			this._addCmsIdToDom($(domField), cmsId)
		})
	}

	getFieldDomElements() {
		const $fieldsContainer = $('[id$=-stage-wrap]').find("ul")
		const $fields = $fieldsContainer.children()

		return $fields
	}

	_addCmsIdToDom($field, cmsId) {
		const dataAttribute = this.constructor.cmsIdsDataAttribute

		$field.attr(dataAttribute, cmsId)
	}

	getCmsIdFromDom($field) {
		const dataAttribute = this.constructor.cmsIdsDataAttribute

		let cmsId = $field.attr(dataAttribute)

		return cmsId
	}

}

HUB.FORMS.DomHelper = DomHelper
