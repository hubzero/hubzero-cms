
var HUB = HUB || {}

HUB.FORMS = HUB.FORMS || {}

class Page {

	static get apiEndpoint() {
		return 'v1.0/forms/pagefields/'
	}

	static get api() {
		return HUB.FORMS.Api
	}

	constructor({id, fields = []}) {
		this.id = id
		this._fields = fields
	}

	save() {
		const endpoint = `${this.constructor.apiEndpoint}update`
		const pageData = {
			page_id: this.id,
			fields: this._fields
		}

		const promise = this.constructor.api.post(endpoint, pageData)

		return promise
	}

	fetchFields() {
		const endpoint = `${this.constructor.apiEndpoint}getByPage?page_id=${this.id}`
		const promise = this.constructor.api.get(endpoint)

		return promise
	}

	setFields(fields) {
		this._fields = fields
	}

}

HUB.FORMS.Page = Page
