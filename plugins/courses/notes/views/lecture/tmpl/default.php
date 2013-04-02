<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
jQuery(document).ready(function(jQuery){
	var $ = jQuery;

	//var url = "<?php echo '/index.php?option=com_courses&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias') . '&no_html=1&note='; ?>";
	var url = "<?php echo '/index.php?option=com_courses&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=notes&scope=lecture&scope_id=' . $this->lecture->get('id') . '&no_html=1&note='; ?>";
//var response = jQuery.parseJSON(data);
	var options = {
		notes: <?php 
			$n = array();
			if ($notes = $this->model->notes(array('scope' => 'lecture', 'scope_id' => $this->lecture->get('id'))))
			{
				foreach ($notes as $note)
				{
					$obj = new stdClass;
					$obj->id     = $note->get('id');
					$obj->dataId = $note->get('id');
					$obj->text   = str_replace('  ', ' &nbsp;', nl2br(stripslashes($note->get('content'))));
					$obj->pos_x  = $note->get('pos_x') . 'px';
					$obj->pos_y  = $note->get('pos_y') . 'px';
					$obj->width  = $note->get('width');
					$obj->height = $note->get('height');
					$obj->timestamp   = $note->get('timestamp');

					$n[] = $obj;
				}
			}
			echo json_encode($n);
		?>,
		resizable: true,
		controls: true,
		editCallback: function(note) {
			var id = $('#note-' + note.id).attr('data-id');
			//console.log(url + id + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height + '&txt=' + note.text);
			$.getJSON(url + id + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height + '&txt=' + note.text, {}, function(data) {
				//console.log(id + '_' + note.id);
				if (id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
				//console.log(data);
			});
		},
		createCallback: function(note) {
			var tme = null;
			if (typeof HUB.Presenter !== 'undefined') {
				tme += '&time=' + HUB.Presenter.formatTime(HUB.Presenter.getCurrent());
			}
			//console.log(url + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height + '&txt=New%20note');
			$.getJSON(url + tme + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height, {}, function(data) {
				//$('#note' + note.id).attr('data-id', data);
				if (id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
				//console.log(data);
			});
		},
		deleteCallback: function(note) {
			//console.log('delete');
			var id = note.id;//$('#note-' + note.id).attr('data-id');
			//console.log(url + id + '&action=delete');
			$.getJSON(url + id + '&action=delete', {}, function(data) {
				/*if (id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}*/
				//console.log(data);
			});
		},
		moveCallback: function(note) {
			var id = $('#note-' + note.id).attr('data-id');
			//console.log(url + id + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height);
			$.getJSON(url + id + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height, {}, function(data) {
				if (id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
				//console.log(data);
			});
		},
		resizeCallback: function(note) {
			var id = $('#note-' + note.id).attr('data-id');
			$.getJSON(url + id + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height, {}, function(data) {
				if (id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
				//console.log(data);
			});
		}
	};
	jQuery("#content").stickyNotes(options);
});
</script>