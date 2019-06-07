/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class Api {

	get(url, data = {}) {
		const promise = this._makeApiRequest(url, data, 'GET')

		return promise
	}

	post(url, data) {
		const promise = this._makeApiRequest(url, data, 'POST')

		return promise
	}

	delete(url, data) {
		const promise = this._makeApiRequest(url, data, 'DELETE')

		return promise
	}

	_makeApiRequest(url, data, method) {
		const baseApiUrl = '/api'

		const promise = $.ajax({
			url: `${baseApiUrl}${url}`,
			data,
			method
		})

		return promise
	}

}

var HUB = HUB || {}

HUB.Api = Api
