/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
