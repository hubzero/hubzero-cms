<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
$this->js();
?>
<script type="text/javascript">
jQuery(document).ready(function(jQuery) {
	var $ = jQuery,
		_DEBUG = false,
		url = "<?php echo Request::base(true) . '/' . $this->offering->link() . '&active=notes&scope=lecture&scope_id=' . $this->lecture->get('id') . '&no_html=1&note='; ?>";

	var options = {
		notes: <?php
			$n = array();
			$access = array(0);
			if ($this->course->access('manage'))
			{
				$access = array(0, 1);
			}
			$notes = \Plugins\Courses\Notes\Models\Note::all()
				->whereEquals('scope', 'lecture')
				->whereEquals('scope_id', $this->lecture->get('id'))
				->whereIn('access', $access)
				->whereEquals('section_id', $this->offering->section()->get('id'))
				->whereEquals('created_by', User::get('id'))
				->whereEquals('state', 1)
				->rows();
			if ($notes->count())
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
				$notes = \Plugins\Courses\Notes\Models\Note::all()
					->whereEquals('scope', 'lecture')
					->whereEquals('scope_id', $this->lecture->get('id'))
					->whereEquals('access', 1)
					->whereEquals('state', 1)
					->rows();
				if ($notes->count())
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
