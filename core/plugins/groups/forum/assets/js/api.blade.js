/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
'use strict';

class Api {
    get(url, data = {}) {
        return this._makeApiRequest(url, data, 'GET');
    }

    post(url, data) {
        return this._makeApiRequest(url, data, 'POST');
    }

    delete(url, data) {
        return this._makeApiRequest(url, data, 'DELETE');
    }

    _makeApiRequest(url, data, method) {
        var baseApiUrl = '/api';
        var options = { method: method };

        if (method === 'GET') {
            var params = new URLSearchParams(data).toString();
            if (params) {
                url = url + '?' + params;
            }
        } else {
            options.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
            options.body = new URLSearchParams(data).toString();
        }

        return fetch(baseApiUrl + url, options).then(function (response) {
            return response.json();
        });
    }
}

var HUB = HUB || {};
HUB.Api = Api;
