/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var HUB = HUB || {};

HUB.TAGS = HUB.TAGS || {};

var Api = {
	get: function(url, data) {
		return Api._makeApiRequest(url, data, 'GET');
	},

	post: function(url, data) {
		return Api._makeApiRequest(url, data, 'POST');
	},

	delete: function(url, data) {
		return Api._makeApiRequest(url, data, 'DELETE');
	},

	_makeApiRequest: function(url, data, method) {
		var baseApiUrl = '/api/';
		var fullUrl = baseApiUrl + url;

		if (method === 'GET' && data) {
			var params = new URLSearchParams(data).toString();
			if (params) {
				fullUrl += '?' + params;
			}
		}

		var options = {
			method: method,
			headers: {
				'Accept': 'application/json'
			}
		};

		if (method !== 'GET' && data) {
			options.headers['Content-Type'] = 'application/json';
			options.body = JSON.stringify(data);
		}

		return fetch(fullUrl, options).then(function(response) {
			return response.json();
		});
	}
};

HUB.TAGS.Api = Api;
