<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$cls = '';
if (!empty($this->active)):
	$cls = ' active';
endif;

$tmpl = Request::getCmd('tmpl', '');
?>
<div class="media-files media-list<?php echo $cls ?>" id="media-list">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&folder=' . $this->folder); ?>" method="post" id="media-form-list" name="media-form-list">
		<div class="manager">
			<table>
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_NAME'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_SIZE'); ?></th>
					<?php if ($tmpl != 'component'): ?>
						<th scope="col"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_TYPE'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_MODIFIED'); ?></th>
					<?php endif; ?>
					<?php if ($tmpl != 'component' || User::authorise('core.delete', 'com_media')): ?>
						<th scope="col"></th>
					<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					// Group files and folders
					$folders = array();
					$files = array();
					foreach ($this->children as $child):
						if ($child['type'] == 'dir'):
							$folders[] = $child;
						elseif ($child['type'] == 'file' || $child['type'] == 'img'):
							$files[] = $child;
						endif;
					endforeach;

					// Display folders first
					foreach ($folders as $child):
						$this->currentFolder = $child;
						echo $this->loadTemplate('folder');
					endforeach;

					// Display files
					foreach ($files as $child):
						if ($tmpl == 'component' && $child['type'] != 'img'):
							continue;
						endif;

						$this->currentDoc = $child;
						echo $this->loadTemplate('doc');
					endforeach;
					?>
				</tbody>
			</table>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="username" value="" />
			<input type="hidden" name="password" value="" />
			<?php echo Html::input('token'); ?>
			<input type="hidden" name="folder" value="<?php echo $this->escape($this->folder); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="tmpl" value="<?php echo $this->escape($tmpl); ?>" />
		</div>
	</form>
</div>
