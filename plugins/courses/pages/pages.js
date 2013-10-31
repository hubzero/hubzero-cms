/**
 * @package     hubzero-cms
 * @file        plugins/courses/pages/pages.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

window.addEvent('domready', function() {
	$$('a.delete').each(function(el) {
		el.addEvent('click', function(e) {
			var val = confirm('Are you sure you wish to delete this item?');
			if (!val) {
				new Event(e).stop();
			}
			return val;
		});
	});
});