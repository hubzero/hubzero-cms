<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$cls = '';
if (!empty($this->active)):
	$cls = ' active';
endif;
?>
<div class="media-files media-list<?php echo $cls ?>" id="media-list">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&folder=' . $this->folder); ?>" method="post" id="media-form-list" name="media-form-list">
		<div class="manager">
			<table>
				<thead>
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Size</th>
					<?php if (Request::getCmd('tmpl') != 'component'): ?>
						<th scope="col">Type</th>
						<th scope="col">Last modified</th>
					<?php endif; ?>
						<th scope="col"></th>
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
		</div>
	</form>
</div>
