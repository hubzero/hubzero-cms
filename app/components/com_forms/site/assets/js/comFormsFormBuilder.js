
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

class ComFormsFormBuilder extends HUB.FORMS.FormBuilder {

	constructor(args) {
		super(args)
		this._domHelper = new HUB.FORMS.DomHelper()
		this._pageId = args.pageId
	}

	setFields(fields) {
		fields = this._sortFields(fields)

		super.setFields(fields)

		this._domHelper.removeCollidingClasses()
		this._domHelper.addCmsIdsToDom(fields)
	}

	_sortFields(fields) {
		fields.sort((field, fieldNext) => {
			return field.order - fieldNext.order
		})

		return fields
	}

	getFields() {
		const fieldsData = super.getFields()

		const supplementedFieldsData = this._addSupplementaryFieldsData(fieldsData)

		return supplementedFieldsData
	}

	_addSupplementaryFieldsData(fieldsData) {
		let supplementedFieldsData = fieldsData.map((fieldData, order) => {
			return this._addSupplementaryFieldData(fieldData, order)
		})

		this._addCmsIds(supplementedFieldsData)

		return supplementedFieldsData
	}

	_addSupplementaryFieldData(field, order) {
		let supplementedFieldData = {
			...field,
			order,
			page_id: this._pageId
		}

		return supplementedFieldData
	}

	_addCmsIds(virtualFields) {
		const domFields = this._domHelper.getFieldDomElements()

		domFields.each((i, field) => {
			const cmsId = this._domHelper.getCmsIdFromDom($(field))
			virtualFields[i].id = cmsId
		})
	}

}

HUB.FORMS.ComFormsFormBuilder = ComFormsFormBuilder
