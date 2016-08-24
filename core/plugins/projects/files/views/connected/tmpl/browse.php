<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('uploader')
     ->js();

$metadata = Plugin::byType('metadata');

$subdirlink = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
$sortbyDir  = $this->sortdir == 'ASC' ? 'DESC' : 'ASC';
?>

<div id="preview-window"></div>

<form action="<?php echo Route::url($this->model->link('files')); ?>" method="post" enctype="multipart/form-data" id="plg-form" class="file-browser submit-ajax">
	<div id="plg-header">
		<h3 class="files">
			<a href="<?php echo Route::url($this->model->link('files')); ?>">
				<?php echo Lang::txt('Connections'); ?>
			</a>
			&nbsp;&raquo;
			<?php $imgRel = '/plugins/filesystem/' . $this->connection->provider->alias . '/assets/img/icon.png'; ?>
			<?php $img = (is_file(PATH_APP . DS . $imgRel)) ? '/app' . $imgRel : '/core' . $imgRel; ?>
			<img src="<?php echo $img; ?>" alt="" height="20" width="20" />
			<a href="<?php echo Route::url($this->model->link('files') . '&action=browse&connection=' . $this->connection->id); ?>">
				<?php echo $this->connection->name; ?>
			</a>
			&nbsp;
			<?php echo \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->model->link('files') . '&action=browse&connection=' . $this->connection->id, $parent); ?>
		</h3>
	</div>
	<fieldset>
		<input type="hidden" name="subdir"  id="subdir"    value="<?php echo urlencode($this->subdir); ?>" />
		<input type="hidden" name="sortby"  id="sortby"    value="<?php echo $this->sortby; ?>" />
		<input type="hidden" name="sortdir" id="sortdir"   value="<?php echo $this->sortdir; ?>" />
		<input type="hidden" name="id"      id="projectid" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="uid"     id="uid"       value="<?php echo User::get('id'); ?>" />
	</fieldset>
	<div class="list-editing">
		<p>
			<?php if ($this->model->access('content')) : ?>
				<span id="manage_assets">
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=upload'   . $subdirlink); ?>" class="fmanage" id="a-upload" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD'); ?></span></a>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=newdir'   . $subdirlink); ?>" id="a-folder" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_FOLDER_TOOLTIP'); ?>" class="fmanage"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_NEW_FOLDER'); ?></span></a>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=download' . $subdirlink . '&a=1'); ?>" class="fmanage js" id="a-download" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD'); ?></span></a>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=move'     . $subdirlink); ?>" class="fmanage js" id="a-move" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE'); ?></span></a>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=delete'   . $subdirlink); ?>" class="fmanage js" id="a-delete" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE'); ?></span></a>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=rename'   . $subdirlink); ?>" class="fmanage js" id="a-rename" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME'); ?></span></a>
					<?php if (count($metadata)) : ?>
						<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=annotate' . $subdirlink); ?>" class="fmanage js" id="a-annotate" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_ANNOTATE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_ANNOTATE'); ?></span></a>
					<?php endif;
					if (!$this->connection->id) : ?>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=compile'  . $subdirlink); ?>" class="fmanage js" id="a-handle" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILE'); ?></span></a>
					<?php endif; ?>
				</span>
				<noscript>
					<span class="faded ipadded"><?php echo Lang::txt('Enable JavaScript in your browser for advanced file management.'); ?></span>
				</noscript>
			<?php endif; ?>
		</p>
	</div>
	<table id="filelist" class="listing">
		<thead>
			<tr>
				<?php if ($this->model->access('content')) : ?>
					<th class="checkbox"><input type="checkbox" name="toggle" value="" id="toggle" class="js" /></th>
				<?php endif; ?>
				<th class="asset_doc <?php if ($this->sortby == 'basename') { echo ' activesort'; } ?>">
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=browse' . $subdirlink . '&sortby=basename&sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_FILES_NAME'); ?>">
						<?php echo Lang::txt('PLG_PROJECTS_FILES_NAME'); ?>
					</a>
				</th>
				<th class="centeralign"></th>
				<th <?php if ($this->sortby == 'size') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=browse' . $subdirlink . '&sortby=size&sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_FILES_SIZE'); ?>">
						<?php echo Lang::txt('PLG_PROJECTS_FILES_SIZE'); ?>
					</a>
				</th>
				<th <?php if ($this->sortby == 'timestamp') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo Route::url($this->model->link('files') . '&connection=' . $this->connection->id . '&action=browse' . $subdirlink . '&sortby=timestamp&sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . ucfirst(Lang::txt('PLG_PROJECTS_FILES_MODIFIED')); ?>">
						<?php echo ucfirst(Lang::txt('PLG_PROJECTS_FILES_MODIFIED')); ?>
					</a>
				</th>
				<th><?php echo ucfirst(Lang::txt('PLG_PROJECTS_FILES_BY')); ?></th>
				<th class="centeralign nojs"></th>
			</tr>
		</thead>
		<tbody>
			<?php if ($this->subdir) : ?>
				<tr class="updir">
					<td></td>
					<?php $min = $this->model->access('content') ? 1 : 0; ?>
					<td colspan="<?php echo 6 - $min; ?>" class="mini">
						<a href="<?php echo Route::url($this->model->link('files') . '&action=browse&connection=' . $this->connection->id . '&subdir=' . $parent); ?>" class="uptoparent"><?php echo Lang::txt('PLG_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a>
					</td>
				</tr>
			<?php endif; ?>
			<?php
				// Display contents
				if (count($this->items) > 0)
				{
					$this->view('_items')
					     ->set('option', $this->option)
					     ->set('model', $this->model)
					     ->set('subdir', $this->subdir)
					     ->set('items', $this->items)
					     ->set('connection', $this->connection)
					     ->set('config', $this->fileparams)
					     ->display();
				}
			?>
		</tbody>
	</table>
	<?php if (count($this->items) == 0 ) : ?>
		<p class="noresults">
			<?php echo ($this->subdir) ? Lang::txt('PLG_PROJECTS_FILES_THIS_DIRECTORY_IS_EMPTY') : Lang::txt('PLG_PROJECTS_FILES_PROJECT_HAS_NO_FILES'); ?>
		</p>
	<?php endif; ?>
 </form>
