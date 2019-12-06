
var HUB = HUB || {}

HUB.TAGS = HUB.TAGS || {}

class Api {

	static get(url, data) {
		return this._makeApiRequest(url, data, 'GET')
	}

	static post(url, data) {
		return this._makeApiRequest(url, data, 'POST')
	}

	static delete(url, data) {
		return this._makeApiRequest(url, data, 'DELETE')
	}

	static _makeApiRequest(url, data, method) {
		const baseApiUrl = '/api/'

		return $.ajax({
					url: `${baseApiUrl}${url}`,
					data,
					method
		})
	}

}

HUB.TAGS.Api = Api

