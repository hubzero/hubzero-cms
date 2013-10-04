/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License,
 * version 3 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

var DataStore = {};

(function(window, document, ds, $, undefined) {
	var noop = function() {};

	ds.log = function() {
		if ('console' in window) return function(msg) {
			window.console.log(msg)
		};
		return noop
	}();

	ds.init = function() {
		ds.log('DataStore: Started...');
	}

	ds.status_msg = function(msg) {
		if (!msg) {
			$('#status-msg').empty().css('opacity', 0);
			return;
		}

		var html
		html = '<span id="fbwrap"><span id="facebookG">';
		html += '<span class="facebook_blockG" id="blockG_1"></span>';
		html += '<span class="facebook_blockG" id="blockG_2"></span>';
		html += '<span class="facebook_blockG" id="blockG_3"></span>' + msg;
		html += '</span></span>';

		$('#status-msg').html(html).css('opacity', 1);
	}

	$(document).ready(function() {
		DataStore.init();
	});

}) (this, document, DataStore, jQuery);



// From Douglas Crockford's remedial javascript
// http://javascript.crockford.com/remedial.html
if (!String.prototype.supplant) {
	String.prototype.supplant = function (o) {
	    return this.replace(
	        /\{([^{}]*)\}/g,
	        function (a, b) {
	            var r = o[b];
	            return typeof r === 'string' || typeof r === 'number' ? r : a;
	        }
	    );
	};
}

if (!String.prototype.trim) {
	String.prototype.trim = function () {
	    return this.replace(/^\s*(\S*(?:\s+\S+)*)\s*$/, "$1");
	};
}

if (!String.prototype.entityify) {
    String.prototype.entityify = function () {
        return this.replace(/&/g, "&amp;").replace(/</g,
            "&lt;").replace(/>/g, "&gt;");
    };
}
