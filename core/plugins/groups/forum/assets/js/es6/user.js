/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class User {

	static get BASE_API_URL() {
		return '/users/currentuser'
	}

	constructor() {
		this.api = new Api()
	}

	isAuthenticated() {
		const authenticationEndpoint = this._buildApiEndpoint('isAuthenticated')
		const isAuthenticated = this.api.get(authenticationEndpoint)

		return isAuthenticated
	}

	_buildApiEndpoint(apiTask) {
		const baseApiEndpoint = this.constructor.BASE_API_URL
		const apiEndpoint = `${baseApiEndpoint}/${apiTask}`

		return apiEndpoint
	}

}

var HUB = HUB || {}

HUB.User = User || {}
