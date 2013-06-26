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

jQuery(document).ready(function($) {

	$('#dv_gallery_list img').click(function() {
		var idx = $('#dv_gallery_list img').index(this);
		$('#dv_gallery_viewer img').hide();
		$('#dv_gallery_viewer img:eq(' + idx + ')').fadeIn('slow');
		if($(this).attr('title') !== '') {
			$('#dv_gallery_desc').html($(this).attr('title'));
		} else {
			$('#dv_gallery_desc').html('No description available.');
		}
		$('#dv_gallery_dl_image a').attr('href', $('#dv_gallery_viewer img:eq(' + idx + ')').parent().attr('href'));
	});

	$('#dv_gallery_list img:eq(0)').trigger('click');

	$("#color").buttonset();
	$("#description").button();

	$('input[name=color]:radio').click(function() {
		$('#dv_gallery_viewer').css('background-color', $('input[name=color]:radio:checked').val());
	});

	$('#description').click(function() {
		if ($(this).attr('checked') === true) {
			$('#dv_gallery_desc').show('fade');
		} else {
			$('#dv_gallery_desc').hide('fade');
		}
	});

});
