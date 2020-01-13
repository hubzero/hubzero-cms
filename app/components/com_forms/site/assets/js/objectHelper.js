
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

class ObjectHelper {

	invert(object) {
		let attr, value
		const invertedObject = {}

		for (attr in object) {
			value = object[attr]
			invertedObject[value] = attr
		}

		return invertedObject
	}

}

HUB.FORMS.ObjectHelper = ObjectHelper
