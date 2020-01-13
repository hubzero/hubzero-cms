
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

class ComFormsFieldTranslator {

	constructor() {
		this._destination = null
	}

	forBuilder(fieldsData) {
		const translatedFields = this._for('builder', fieldsData)

		return translatedFields
	}

	forServer(fieldsData) {
		const translatedFields = this._for('server', fieldsData)

		return translatedFields
	}

	_for(destination, fieldsData) {
		this._destination = destination

		const translatedFields = fieldsData.map((field) => {
			return this._translate(field)
		})

		return translatedFields
	}

	_translate(fieldData) {
		const formedData = this._form(fieldData)
		const mappedData = this._map(formedData, fieldData)
		const translatedData = this._parse(mappedData)

		return translatedData
	}

	_form(fieldData) {
		const formedData = {}
		const destinationStateList = this.constructor.stateList(this._destination)

		destinationStateList.forEach((attr) => {
			formedData[attr] = fieldData[attr]
		})

		return formedData
	}

	_map(formedData, sourceData) {
		const mappedData = {...formedData}
		let sourceAttr

		for (const destinationAttr in formedData) {
			sourceAttr = this._sourceAttr(destinationAttr)
			mappedData[destinationAttr] = sourceData[sourceAttr]
		}

		return mappedData
	}

	_parse(fieldData) {
		let fieldValue
		let parsedValue
		const parsedData = {}

		for (let attr in fieldData) {
			fieldValue = fieldData[attr]
			parsedValue = this._parseAttr(attr, fieldValue)
			parsedData[attr] = parsedValue
		}

		return parsedData
	}

	_parseAttr(name, value) {
		let parsedValue

		if (this._destination == 'builder') {
			parsedValue = this._parseForBuilder(name, value)
		} else {
			parsedValue = this._parseForServer(name, value)
		}

		return parsedValue
	}

	_parseForBuilder(name, value) {
		let parsedValue

		switch(name) {
			case 'inline':
			case 'multiple':
			case 'other':
			case 'required':
			case 'toggle':
				parsedValue = !!parseInt(value)
				break;
			default:
				parsedValue = value
		}

		return parsedValue
	}

	_parseForServer(name, value) {
		let parsedValue

		switch(name) {
			case 'default_value':
				parsedValue = this._parseUndefinedForServer(value)
				break;
			case 'inline':
			case 'multiple':
			case 'other':
			case 'required':
			case 'toggle':
				parsedValue = value ? 1 : 0
				break;
			case 'values':
				parsedValue = this._parseValuesForServer(value)
				break;
			default:
				parsedValue = value
		}

		return parsedValue
	}

	_parseUndefinedForServer(value)
	{
		let parsedValue = value

		if (value === undefined) {
			parsedValue = null
		}

		return parsedValue
	}

	_parseValuesForServer(values)
	{
		values = values || []

		let parsedValues = values.map((value, i) => {
			value.id = i + 1
			return value
		})

		return JSON.stringify(parsedValues)
	}

	_sourceAttr(destinationAttr) {
		const stateMap = this.constructor.stateMap(this._destination)

		const sourceAttr = stateMap[destinationAttr]

		return sourceAttr
	}

	static stateList(destination) {
		let stateList

		if (destination == 'builder') {
			stateList = Object.keys(this.builderStateMap)
		} else {
			stateList = Object.values(this.builderStateMap)
		}

		return stateList
	}

	static stateMap(destination) {
		let stateMap

		if (destination == 'builder') {
			stateMap = this.builderStateMap
		} else {
			stateMap = this._objectHelper.invert(this.builderStateMap)
		}

		return stateMap
	}

	static get builderStateMap() {
		return {
			value: 'default_value',
			description: 'help_text',
			id: 'id',
			inline: 'inline',
			label: 'label',
			max: 'max',
			maxlength: 'max_length',
			min: 'min',
			multiple: 'multiple',
			name: 'name',
			order: 'order',
			other: 'other',
			page_id: 'page_id',
			required: 'required',
			rows: 'rows',
			step: 'step',
			toggle: 'toggle',
			type: 'type',
			values: 'values'
		}
	}

	static set objectHelper(objectHelper) {
		this._objectHelper = objectHelper
	}

}

HUB.FORMS.ComFormsFieldTranslator = ComFormsFieldTranslator
