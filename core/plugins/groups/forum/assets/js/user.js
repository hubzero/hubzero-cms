/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

'use strict';

var _createClass = function () {
	function defineProperties(target, props) {
		for (var i = 0; i < props.length; i++) {
			var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true;
			if ("value" in descriptor) {
				descriptor.writable = true;
			}
			Object.defineProperty(target, descriptor.key, descriptor);
		}
	}
	return function (Constructor, protoProps, staticProps) {
		if (protoProps) {
			defineProperties(Constructor.prototype, protoProps);
		}
		if (staticProps) {
			defineProperties(Constructor, staticProps);
		}
		return Constructor;
	};
}();

function _classCallCheck(instance, Constructor) {
	if (!(instance instanceof Constructor)) {
		throw new TypeError("Cannot call a class as a function");
	}
}

var User = function () {
	_createClass(User, null, [{
		key: 'BASE_API_URL',
		get: function get() {
			return '/users/currentuser';
		}
	}]);

	function User() {
		_classCallCheck(this, User);

		this.api = new Api();
	}

	_createClass(User, [{
		key: 'isAuthenticated',
		value: function isAuthenticated() {
			var authenticationEndpoint = this._buildApiEndpoint('isAuthenticated');
			var isAuthenticated = this.api.get(authenticationEndpoint);
			return isAuthenticated;
		}
	}, {
		key: '_buildApiEndpoint',
		value: function _buildApiEndpoint(apiTask) {
			var baseApiEndpoint = this.constructor.BASE_API_URL;
			var apiEndpoint = baseApiEndpoint + '/' + apiTask;
			return apiEndpoint;
		}
	}]);

	return User;
}();

var HUB = HUB || {};
HUB.User = User || {};