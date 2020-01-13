
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

class FormPage {

	static get apiEndpoint() {
		return 'v1.0/forms/formpages/'
	}

	static get api() {
		return HUB.FORMS.Api
	}

	constructor({ id }) {
		this.id = id
	}

	destroy() {
		const endpoint = `${this.constructor.apiEndpoint}destroy`
		const requestData = { id: this.id }

		const promise = this.constructor.api.delete(endpoint, requestData)

		return promise
	}

}

HUB.FORMS.FormPage = FormPage
