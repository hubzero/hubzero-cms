/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

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
	return this.replace(/^\s+|\s+$/g, "");
};
