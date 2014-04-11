/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * JavaScript behavior to allow shift select in administrator grids
 */
(function() {
	Joomla = Joomla || {};

	Joomla.JMultiSelect = function(table) {

		var self = this;
		this.table = $('#' + table);
		if (this.table.length) {
			this.boxes = this.table.find('input[type=checkbox]');
			this.boxes.on('click', function(e){
				self.doselect(e);
			});
		}

		this.doselect = function(e) {
			var current = $(e.target);
			if (e.shiftKey && typeof(this.last) !== 'null') {
				var checked = current.prop('checked') ? 'checked' : '';
				var range = [jQuery.inArray(current[0], this.boxes), jQuery.inArray(this.last[0], this.boxes)].sort(function(a, b) {
					//Shorthand to make sort() sort numerical instead of lexicographic
					return a-b;
				});
				for (var i=range[0]; i <= range[1]; i++) {
					$(this.boxes[i]).prop('checked', checked);
				}
			}
			this.last = current;
		};
	};
})();
