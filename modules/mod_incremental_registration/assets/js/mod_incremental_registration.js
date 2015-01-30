/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

/* For compatibility mode */
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq,
		timer = null;

	if ($('#curl').length > 0) {
		var img = $('#curl-img');

		$('#curl')
			.on('mouseover', function() {
				if (timer) {
					clearInterval(timer);
				}
				img.attr('src', img.attr('data-img-big'));
			})
			.on('mouseout', function() {
				timer = setTimeout(function() {
					img.attr('src', img.attr('data-img-small'));
				}, 800);
			});
	}
});
