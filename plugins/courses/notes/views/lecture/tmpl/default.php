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
	var $ = jQuery,
		_DEBUG = false,
		url = "<?php echo '/index.php?option=com_courses&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=notes&scope=lecture&scope_id=' . $this->lecture->get('id') . '&no_html=1&note='; ?>";

	var options = {
		notes: <?php 
			$n = array();
			$access = 0;
			if ($this->course->access('manage'))
			{
				$access = array(0, 1);
			}
			if ($notes = $this->model->notes(array('scope' => 'lecture', 'scope_id' => $this->lecture->get('id'), 'access' => $access, 'section_id' => $this->offering->section()->get('id'))))
			{
				foreach ($notes as $note)
				{
					$obj = new stdClass;
					$obj->id        = $note->get('id');
					$obj->dataId    = $note->get('id');
					$obj->text      = str_replace('  ', ' &nbsp;', nl2br(stripslashes($note->get('content'))));
					$obj->pos_x     = $note->get('pos_x') . 'px';
					$obj->pos_y     = $note->get('pos_y') . 'px';
					$obj->width     = $note->get('width');
					$obj->height    = $note->get('height');
					$obj->timestamp = $note->get('timestamp');
					$obj->access    = $note->get('access');
					$obj->editable  = true;

					$n[] = $obj;
				}
			}
			if (!$this->course->access('manage'))
			{
				if ($notes = $this->model->notes(array('scope' => 'lecture', 'scope_id' => $this->lecture->get('id'), 'access' => 1, 'created_by' => -1)))
				{
					foreach ($notes as $note)
					{
						$obj = new stdClass;
						$obj->id        = $note->get('id');
						$obj->dataId    = $note->get('id');
						$obj->text      = str_replace('  ', ' &nbsp;', nl2br(stripslashes($note->get('content'))));
						$obj->pos_x     = $note->get('pos_x') . 'px';
						$obj->pos_y     = $note->get('pos_y') . 'px';
						$obj->width     = $note->get('width');
						$obj->height    = $note->get('height');
						$obj->timestamp = $note->get('timestamp');
						$obj->access    = $note->get('access');
						$obj->editable  = false;

						$n[] = $obj;
					}
				}
			}
			echo json_encode($n);
		?>,
		<?php if ($this->course->access('manage')) { ?>
		shareable: true,
		<?php } ?>
		resizable: true,
		controls: true,
		controlBar: true,
		editCallback: function(note) {
			if (!note.editable) {
				return;
			}

			var id = $('#note-' + note.id).attr('data-id');

			if (_DEBUG) {
				window.console && console.log('calling: ' + url + id + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height + '&access=' + note.access + '&txt=' + note.text);
			}

			$.getJSON(url + id + '&time=' + note.timestamp + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height + '&access=' + note.access + '&txt=' + note.text, {}, function(data) {
				if (data.id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
				if (_DEBUG) {
					window.console && console.log(data);
				}
			});
		},
		createCallback: function(note) {
			var id = $('#note-' + note.id).attr('data-id'),
				tme = null;

			if (note.timestamp && note.timestamp != '00:00:00') {
				tme += '&time=' + note.timestamp;
			}

			if (_DEBUG) {
				window.console && console.log('calling: ' + url + tme + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height);
			}

			$.getJSON(url + tme + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height, {}, function(data) {
				if (_DEBUG) {
					window.console && console.log(data);
				}
				if (data.id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
			});
		},
		deleteCallback: function(note) {
			if (!note.editable) {
				return;
			}

			var id = note.id;

			if (_DEBUG) {
				window.console && console.log('calling: ' + url + id + '&action=delete');
			}

			$.getJSON(url + id + '&action=delete', {}, function(data) {
				if (_DEBUG) {
					window.console && console.log(data);
				}
			});
		},
		moveCallback: function(note) {
			if (!note.editable) {
				return;
			}

			var id = $('#note-' + note.id).attr('data-id');

			if (_DEBUG) {
				window.console && console.log('calling: ' + url + id + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height);
			}

			$.getJSON(url + id + '&time=' + note.timestamp + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height, {}, function(data) {
				if (data.id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
				if (_DEBUG) {
					window.console && console.log(data);
				}
			});
		},
		resizeCallback: function(note) {
			if (!note.editable) {
				return;
			}

			var id = $('#note-' + note.id).attr('data-id');

			$.getJSON(url + id + '&time=' + note.timestamp + '&action=save&x=' + note.pos_x + '&y=' + note.pos_y + '&w=' + note.width + '&h=' + note.height, {}, function(data) {
				if (data.id != note.id) {
					$('#note-' + note.id).attr('data-id', data.id);
				}
				if (_DEBUG) {
					window.console && console.log(data);
				}
			});
		}
	};

	jQuery("#content").stickyNotes(options);
});
</script>
