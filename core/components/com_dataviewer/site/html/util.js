/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

 
//window.onerror = handle_err;
function handle_err(err, url, line) {
	/*
	if (typeof console != 'undefined' && console.log != 'undefined') {
		console.log('Error : ' + err + ' [' + url + ' : ' + line + ']');
	}
	*/
	return true;
}

function linkify(text) {
	var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
	return text.replace(exp,"<a target='_blank' href='$1'>$1</a>");
}

//Remove all HTML tags
String.prototype.stripTags = function () {
   return this.replace(/<([^>]+)>/g,'');
}


/*
* Some ES5 stuff
* From Remedial JavaScript by Douglas Crockford
* http://javascript.crockford.com/remedial.html
*/

String.prototype.supplant = function (o) {
	return this.replace(/{([^{}]*)}/g,
		function (a, b) {
			var r = o[b];
			return typeof r === 'string' || typeof r === 'number' ? r : a;
		}
	);
};

String.prototype.trim = function () {
	return this.replace(/^\s+|\s+$/g, "").replace(/&nbsp;/g, "");
};
