/**
 * @package     hubzero-cms
 * @file        templates/welcome/js/main.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

jQuery(document).ready(function() {
	if (!Modernizr.svg) {
		jQuery("img").each(function() {
			var src = this.src;
			if (src.match(/svg$/)) {
				// Replace "svg" by "png"
				this.src = src.slice(0,-3) + 'png'
			}
		});
	}
	if (typeof(skrollr) !== 'undefined') {
		var s = skrollr.init({
			mobileCheck: function() {
				// Hack - forces mobile version to be off
				return false;
			},
			render: function(data) {
				// Debugging - Log the current scroll position.
				//console.log(data.curTop);
			}
		});
	}
});