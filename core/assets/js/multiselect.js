/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * JavaScript behavior to allow shift select in administrator grids
 */
(function() {
	Hubzero = Hubzero || {};

	Hubzero.MultiSelect = function(table) {

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
