/**
 * @package     hubzero-cms
 * @file        core/assets/js/hubcore.js
 * @copyright   Copyright 2005-2015 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Only define the Hubzero namespace if not defined
if (typeof(Hubzero) === 'undefined') {
	var Hubzero = {};
}

/**
 * Session based api initialization
 */
Hubzero.initApi = function ( callback )
{
	// Get session token for oauth calls
	$.ajax({
		url      : '/developer/oauth/token',
		data     : 'grant_type=session',
		dataType : 'json',
		type     : 'POST',
		cache    : 'false',
		success  : function ( data, textStatus, jqXHR )
		{
			var token = data.access_token;

			// Set defaults for ajax calls
			$.ajaxSetup({
				headers : {
					'Authorization' : 'Bearer ' + token
				}
			});

			if ($.type(callback) === 'function')
			{
				callback();
			}
		}
	});
};