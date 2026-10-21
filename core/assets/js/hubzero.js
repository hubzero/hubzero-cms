/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Only define the Hubzero namespace if not defined
if (typeof(Hubzero) === 'undefined') {
	var Hubzero = {};
}

/**
 * Get root URL
 *
 * @return  string
 */
Hubzero.root = function ()
{
	var scripts = document.getElementsByTagName('script'),
		root = null,
		i;

	if (root === null) {
		root = '';

		for (i = 0; 1 <= scripts.length; i++)
		{
			if (scripts[i].src.toLowerCase().indexOf('/core') > -1) {
				root = scripts[i].src.substr(0, scripts[i].src.indexOf('/core'));
				break;
			}

			if (scripts[i].src.toLowerCase().indexOf('/app') > -1) {
				root = scripts[i].src.substr(0, scripts[i].src.indexOf('/app'));
				break;
			}
		}
	}

	return root;
}

/**
 * Api token state, for scripts that poll the api
 *
 * onRenewed() handlers run after every successful grant, including
 * renewals; onFailed() handlers run with the jqXHR when a grant is refused.
 */
Hubzero.api = {
	token   : null,
	renewed : [],
	failed  : [],

	onRenewed : function (fn) {
		this.renewed.push(fn);
	},

	onFailed : function (fn) {
		this.failed.push(fn);
	},

	fire : function (handlers, arg) {
		for (var i = 0; i < handlers.length; i++) {
			handlers[i](arg);
		}
	}
};

/**
 * Session based api initialization
 *
 * @param   object  Callback function
 * @return  void
 */
Hubzero.initApi = function (callback)
{
	// The grant must not carry the previous token: once that has expired
	// the api guard rejects the bearer header before the grant is seen,
	// so the token could never be renewed. Lift the global header while
	// the request's settings are built; anything in flight is unaffected.
	var headers = $.ajaxSettings.headers || {},
	    bearer  = headers['Authorization'];

	delete headers['Authorization'];

	// Get session token for oauth calls
	var grant = $.ajax({
		url      : Hubzero.root() + '/api/developer/oauth/token',
		data     : 'grant_type=session',
		dataType : 'json',
		type     : 'POST',
		cache    : false
	});

	if (bearer) {
		headers['Authorization'] = bearer;
	}

	grant.done(function (data) {
		var token = data.access_token;

		Hubzero.api.token = token;

		// Set defaults for ajax calls
		$.ajaxSetup({
			headers : {
				'Authorization' : 'Bearer ' + token
			}
		});

		if ($.type(callback) === 'function') {
			callback();
		}

		Hubzero.api.fire(Hubzero.api.renewed, data);

		// Renew a minute before the token expires: on the dot, timer
		// drift alone would have the server rejecting it already
		var lifetime = parseInt(data.expires_in, 10) || 3600;

		setTimeout(function () {
			Hubzero.initApi();
		}, Math.max(lifetime - 60, 30) * 1000);
	}).fail(function (jqXHR) {
		Hubzero.api.fire(Hubzero.api.failed, jqXHR);

		// A refused grant means the session is gone; anything else
		// (network, 5xx, rate limit) is worth another try later
		if (jqXHR.status != 401 && jqXHR.status != 403) {
			setTimeout(function () {
				Hubzero.initApi();
			}, 60 * 1000);
		}
	});
};
