/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2012 by Purdue Research Foundation, West Lafayette, IN 47906.
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

dv_jQuery(document).ready(function($) {
	$('.dv-admin-view-dd').click(function() {
		$('#dv-admin-dd').html('Loading...');
		$('#dv-admin-dd').dialog({
			'title': 'Data Definition [' + $(this).data('dd') + '.php]',
			'modal': true
		});

		$.ajax({
			'url': '/administrator/index.php?option=com_' + com_name,
			'data': {
				'task': 'get_dd',
				'name': $(this).data('dd')
			},
			'success': function(data) {
				$('#dv-admin-dd').dialog('destroy');
				$('#dv-admin-dd').html('<textarea id="dv-admin-dd-src" style="height: 500px; width: 100%; font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;" wrap="off">' + data + '</textarea>');
				$('#dv-admin-dd').dialog({
					'title': 'Data Definition [' + $(this).data('dd') + '.php]',
					'height': $('#dv-admin-dd-src').height() + 40,
					'width': $(window).width() * .95,
					'modal': true
				});
			},
			'dataType': 'json',
			'type': 'POST',
			'error': function () {
				alert( 'Error!' );
			}
		});
		return false;
	});
});
