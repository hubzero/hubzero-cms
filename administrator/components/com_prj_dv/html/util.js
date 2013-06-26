/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
