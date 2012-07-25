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

jQuery(function($) {
	var timer = null;
	$('#curl').mouseover(function() {
		if (timer) {
			clearInterval(timer);
		}
		document.getElementById('curl-img').setAttribute('src', '/modules/mod_incremental_registration/images/bigcurl.png');
	});

	$('#curl').mouseout(function() {
		timer = setTimeout(function() {
			document.getElementById('curl-img').setAttribute('src', '/modules/mod_incremental_registration/images/smallcurl.png');
		}, 800);
	});
});
