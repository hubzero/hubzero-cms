<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	 ->css('uploader')
	 ->css('diskspace')
	 ->js('diskspace')
     ->js();

$subdirlink = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
$sortbyDir = $this->params['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

// Directory path breadcrumbs
$bc = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, Route::url($this->model->link('files') . '&action=browse'), $parent);

$min = $this->model->access('content') ? 1 : 0;

// Remote connections
$connected = $this->oparams->get('google_token') ? true : false;
$sharing   = isset($this->sharing) && $this->sharing ? true : false;
$sync      = isset($this->sync) ? $this->sync : 0;

?>

<div id="preview-window"></div>

<form action="<?php echo Route::url($this->model->link('files')); ?>" method="post" enctype="multipart/form-data" id="plg-form" class="file-browser submit-ajax">
	<div id="plg-header">
		<h3 class="files">
			<?php if ($this->subdir) { ?><a href="<?php echo Route::url($this->model->link('files') . '&action=browse'); ?>"><?php } ?>
			<?php echo $this->title; ?>
			<?php if ($this->subdir) { ?></a><?php echo $bc; ?><?php } ?>
		</h3>
	</div>
	<fieldset>
		<input type="hidden" name="subdir" id="subdir" value="<?php echo urlencode($this->subdir); ?>" />
		<input type="hidden" name="sortby" id="sortby" value="<?php echo $this->params['sortby']; ?>" />
		<input type="hidden" name="sortdir" id="sortdir" value="<?php echo $this->params['sortdir']; ?>" />
		<input type="hidden" name="id" id="projectid" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="sync" id="sync" value="<?php echo $sync; ?>" />
		<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
		<input type="hidden" name="sharing" id="sharing" value="<?php echo $sharing; ?>" />
		<?php if ($sharing && $this->model->access('content'))
		{
			foreach ($this->services as $service)
			{
				$lastsync = $this->rSync->get('status') == 'complete' ? date("c") : $this->model->params->get($service . '_sync', '');
				if ($lastsync)
				{
					$lastsync = '<span class="faded">' . Lang::txt('PLG_PROJECTS_FILES_LAST_SYNC') . ' ' . \Components\Projects\Helpers\Html::timeAgo($lastsync, false) . ' ' . Lang::txt('COM_PROJECTS_AGO') . ' </span>' ;
				}
		?>
		<input type="hidden" name="service-<?php echo $service; ?>" id="service-<?php echo $service; ?>" value="<?php echo !empty($this->connections) && isset($this->connections[$service]) ? 1 : 0; ?>" />
		<input type="hidden" name="sync-lock-<?php echo $service; ?>" id="sync-lock-<?php echo $service; ?>" value="<?php echo $this->model->params->get($service . '_sync_lock'); ?>" />
		<?php }
	} ?>
	</fieldset>
	<?php
		// Connections to external services
		$this->view('link', 'connect')
		     ->set('option', $this->option)
		     ->set('model', $this->model)
		     ->set('uid', $this->uid)
		     ->set('database', $this->database)
		     ->set('connect', $this->connect)
		     ->set('oparams', $this->oparams)
		     ->set('params', $this->fileparams)
		     ->set('sizelimit', \Hubzero\Utility\Number::formatBytes($this->fileparams->get('maxUpload', '104857600')))
		     ->display();
	 ?>
	<div class="list-editing">
		<p>
			<?php if ($this->model->access('content')) { ?>
			<span id="manage_assets">
				<a href="<?php echo Route::url($this->model->link('files') . '&action=upload' . $subdirlink); ?>" class="fmanage" id="a-upload" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD'); ?></span></a>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=newdir' . $subdirlink); ?>" id="a-folder" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_FOLDER_TOOLTIP'); ?>" class="fmanage"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_NEW_FOLDER'); ?></span></a>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=download' . $subdirlink . '&a=1'); ?>" class="fmanage js" id="a-download" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD'); ?></span></a>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=move' . $subdirlink); ?>" class="fmanage js" id="a-move" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE'); ?></span></a>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=delete' . $subdirlink); ?>" class="fmanage js" id="a-delete" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE'); ?></span></a>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=rename' . $subdirlink); ?>" class="fmanage js" id="a-rename" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME'); ?></span></a>
				<?php if ($sharing && $connected) { ?>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=share' . $subdirlink); ?>" id="a-share" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SHARE_TOOLTIP'); ?>" class="fmanage js" ><span><?php echo Lang::txt('PLG_PROJECTS_FILES_SHARE'); ?></span></a>
				<?php } ?>
				<?php if ($this->fileparams->get('latex')) { ?>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=compile' . $subdirlink); ?>" class="fmanage js" id="a-compile" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILE'); ?></span></a>
				<?php } ?>
			</span>
			<noscript>
				<span class="faded ipadded"><?php echo Lang::txt('Enable JavaScript in your browser for advanced file management.'); ?></span>
			</noscript>
			<?php if ($sharing) { ?>
			<span class="rightfloat">
				<span id="sync-status"><?php echo $lastsync; ?></span>
			</span>
			<span id="manage_sync">
				<span id="sync-wrap">
				<a href="<?php echo Route::url($this->model->link('files') . '&action=sync' . $subdirlink); ?>" id="a-sync" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SYNC_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_SYNC'); ?></span></a>
				</span>
			</span>
			<?php } ?>
		<?php } ?>
		</p>
	</div>
	<table id="filelist" class="listing">
		<thead>
			<tr>
				<?php if ($this->model->access('content')) { ?>
				<th class="checkbox"><input type="checkbox" name="toggle" value="" id="toggle" class="js" /></th>
				<?php } ?>
				<th class="asset_doc <?php if ($this->params['sortby'] == 'name') { echo ' activesort'; } ?>">
					<a href="<?php echo Route::url($this->model->link('files') . '&action=browse' . $subdirlink . '&sortby=name&sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_FILES_NAME'); ?>">
					<?php echo Lang::txt('PLG_PROJECTS_FILES_NAME'); ?></a>
				</th>
				<th class="centeralign"></th>
				<th <?php if ($this->params['sortby'] == 'size') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo Route::url($this->model->link('files') . '&action=browse' . $subdirlink . '&sortby=size&sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_FILES_SIZE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_SIZE'); ?></a>
				</th>
				<th <?php if ($this->params['sortby'] == 'modified') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo Route::url($this->model->link('files') . '&action=browse' . $subdirlink . '&sortby=modified&sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . ucfirst(Lang::txt('PLG_PROJECTS_FILES_MODIFIED')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_FILES_MODIFIED')); ?></a>
				</th>
				<th><?php echo ucfirst(Lang::txt('PLG_PROJECTS_FILES_BY')); ?></th>
				<th class="centeralign nojs"></th>
				<?php if ($this->publishing) { ?>
				<th><?php echo Lang::txt('PLG_PROJECTS_FILES_PUBLISHED'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php
			// Go back one level to parent directory
			if ($this->subdir)
			{ ?>
				<tr class="updir">
					<td></td>
					<td colspan="<?php echo $this->publishing ? 7 - $min : 6 - $min; ?>" class="mini">
						<a href="<?php echo Route::url($this->model->link('files') . '&action=browse&subdir=' . $parent); ?>" class="uptoparent"><?php echo Lang::txt('PLG_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a>
					</td>
				</tr>
			<?php
			} ?>
			<?php
				// Display contents
				if (count($this->items) > 0)
				{
					$this->view('_items')
						 ->set('option', $this->option)
						 ->set('model', $this->model)
						 ->set('subdir', $this->subdir)
						 ->set('items', $this->items)
					     ->set('params', $this->params)
					     ->set('publishing', $this->publishing)
					     ->set('fileparams', $this->fileparams)
						 ->display();
				}
			?>
		</tbody>
	</table>
	<?php
	// Show directory as empty
	if (count($this->items) == 0 ) { ?>
		<p class="noresults">
		<?php echo ($this->subdir) ? Lang::txt('PLG_PROJECTS_FILES_THIS_DIRECTORY_IS_EMPTY') : Lang::txt('PLG_PROJECTS_FILES_PROJECT_HAS_NO_FILES'); ?>
		</p>
	<?php } ?>
	<p class="extras">
		<?php if ($this->repo->get('name') == 'local') { ?>
		<span class="leftfloat">
		<?php
		// Used disk space and remaining quota
		$dirsize = $this->repo->call('getDiskUsage',
			$params = array( 'git' => $this->fileparams->get('disk_usage'))
		);
		$quota = $this->model->params->get('quota')
			? $this->model->params->get('quota')
			: \Components\Projects\Helpers\Html::convertSize(floatval($this->model->config()->get('defaultQuota', '1')), 'GB', 'b');

			// Disc space indicator
			$this->view('_mini', 'diskspace')
			     ->set('quota', $quota)
			     ->set('dirsize', $dirsize)
			     ->set('config', $this->model->config())
			     ->set('url', Route::url($this->model->link('files')))
			     ->display();
		?>
		</span>
		<?php } ?>
		<?php if ($this->model->access('content')) { ?>
		<span class="rightfloat">
			<a href="<?php echo Route::url($this->model->link('files')) . '?action=trash'; ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_FILES_SHOW_TRASH'); ?></a>
		</span>
		<?php } ?>
	</p>
 </form>