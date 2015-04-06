<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
	 ->css('css/uploader')
	 ->css('css/diskspace')
	 ->js('js/diskspace')
     ->js();

// Sorting
$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

// Empty directory?
$empty = false;

// Directory path breadcrumbs
$bc = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent);

$class = 'files';
$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

$lastsync = '';
$connected = $this->oparams->get('google_token') ? true : false;

$min = $this->model->access('content') ? 1 : 0;

?>
<div id="sync_output" class="hidden"></div>

<div id="preview-window"></div>
 <form action="<?php echo $this->url; ?>" method="post" enctype="multipart/form-data" id="plg-form" class="file-browser submit-ajax" >
	<input type="hidden" name="case" id="case" value="<?php echo $this->case; ?>" />
	<input type="hidden" name="subdir" id="subdir" value="<?php echo urlencode($this->subdir); ?>" />
	<input type="hidden" name="sortby" id="sortby" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="sortdir" id="sortdir" value="<?php echo $this->filters['sortdir']; ?>" />
	<input type="hidden" name="id" id="projectid" value="<?php echo $this->model->get('id'); ?>" />
	<input type="hidden" name="sync" id="sync" value="<?php echo $this->sync; ?>" />
	<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
	<input type="hidden" name="sharing" id="sharing" value="<?php echo $this->sharing; ?>" />
<?php if ($this->sharing && !empty($this->services) && $this->model->access('content')) {

		foreach ($this->services as $service)
		{
			$lastsync = $this->rSync['status'] == 'complete' ? date("c") : $this->params->get($service . '_sync', '');
			if ($lastsync)
			{
				$lastsync = '<span class="faded">' . Lang::txt('PLG_PROJECTS_FILES_LAST_SYNC') . ' ' . \Components\Projects\Helpers\Html::timeAgo($lastsync, false) . ' ' . Lang::txt('COM_PROJECTS_AGO') . ' </span>' ;
			}
			?>
	<input type="hidden" name="service-<?php echo $service; ?>" id="service-<?php echo $service; ?>" value="<?php echo !empty($this->connections) && isset($this->connections[$service]) ? 1 : 0; ?>" />
	<input type="hidden" name="sync-lock-<?php echo $service; ?>" id="sync-lock-<?php echo $service; ?>" value="<?php echo $this->params->get($service . '_sync_lock'); ?>" />
	<?php }
	 } ?>

	<?php if ($this->case == 'files')
	{ ?>
	<div id="plg-header">
		<h3 class="<?php echo $class; ?>">
			<?php if ($this->subdir) { ?><a href="<?php echo $this->url; ?>"><?php } ?>
			<?php echo $this->title; ?>
			<?php if ($this->subdir) { ?></a><?php echo $bc; ?><?php } ?>
			<?php if ($this->task == 'newdir') { echo ' &raquo; <span class="indlist">' . Lang::txt('PLG_PROJECTS_FILES_ADD_NEW_FOLDER') . '</span>'; } ?>
		</h3>
	</div>
	<?php
	} ?>

	<?php
		// NEW: connections to external services
		$this->view('link', 'connect')
		     ->set('option', $this->option)
		     ->set('model', $this->model)
		     ->set('uid', $this->uid)
		     ->set('database', $this->database)
		     ->set('connect', $this->connect)
		     ->set('oparams', $this->oparams)
		     ->set('params', $this->fileparams)
		     ->set('sizelimit', $this->sizelimit)
		     ->display();
	 ?>

	<div class="list-editing">
		<p>
			<?php if ($this->model->access('content')) { ?>
			<span id="manage_assets">
				<a href="<?php echo $this->url . '/?action=upload' . $subdirlink; ?>" class="fmanage" id="a-upload" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD'); ?></span></a>
				<a href="<?php echo $this->url . '/?action=newdir' . $subdirlink; ?>" id="a-folder" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_FOLDER_TOOLTIP'); ?>" class="fmanage<?php if ($this->task == 'newdir') { echo ' inactive'; } ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_NEW_FOLDER'); ?></span></a>
				<a href="<?php echo $this->url . '/?action=download' . $subdirlink; ?>" class="fmanage js" id="a-download" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD'); ?></span></a>
				<a href="<?php echo $this->url . '/?action=move' . $subdirlink; ?>" class="fmanage js" id="a-move" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE'); ?></span></a>
				<a href="<?php echo $this->url . '/?action=delete' . $subdirlink; ?>" class="fmanage js" id="a-delete" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE'); ?></span></a>
				<?php if ($this->sharing && in_array('google', $this->services) && $connected) { ?>
				<a href="<?php echo $this->url . '/?action=share' . $subdirlink; ?>" id="a-share" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SHARE_TOOLTIP'); ?>" class="fmanage js" ><span><?php echo Lang::txt('PLG_PROJECTS_FILES_SHARE'); ?></span></a>
				<?php } ?>
				<?php if ($this->fileparams->get('latex')) { ?>
				<a href="<?php echo $this->url . '/?action=compile' . $subdirlink; ?>" class="fmanage js" id="a-compile" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILE_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILE'); ?></span></a>
				<?php } ?>
			</span>
				<noscript>
					<span class="faded ipadded"><?php echo Lang::txt('Enable JavaScript in your browser for advanced file management.'); ?></span>
				</noscript>
			<?php if ($this->sharing) { ?>
			<span class="rightfloat">
				<span id="sync-status"><?php echo $lastsync; ?></span>
			</span>
			<span id="manage_sync">
				<span id="sync-wrap">
				<a href="<?php echo $this->url . '/?action=sync' . $subdirlink; ?>" id="a-sync" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SYNC_TOOLTIP'); ?>"><span><?php echo Lang::txt('PLG_PROJECTS_FILES_SYNC'); ?></span></a>
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
				<th class="asset_doc <?php if ($this->filters['sortby'] == 'filename') { echo ' activesort'; } ?>">
					<a href="<?php echo $this->url . '/?action=browse&amp;sortby=filename&amp;sortdir=' .$sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_FILES_NAME'); ?>">
					<?php echo Lang::txt('PLG_PROJECTS_FILES_NAME'); ?></a>
				</th>
				<th class="centeralign"></th>
				<th <?php if ($this->filters['sortby'] == 'sizes') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo $this->url . '/?action=browse&amp;sortby=sizes&amp;sortdir=' . $sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_FILES_SIZE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_SIZE'); ?></a>
				</th>
				<th <?php if ($this->filters['sortby'] == 'modified') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo $this->url . '/?action=browse&amp;sortby=modified&amp;sortdir=' . $sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_SORT_BY') . ' ' . ucfirst(Lang::txt('PLG_PROJECTS_FILES_MODIFIED')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_FILES_MODIFIED')); ?></a>
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
						<a href="<?php echo $this->url . '/?action=browse&amp;subdir=' . $parent; ?>" class="uptoparent"><?php echo Lang::txt('PLG_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a>
					</td>
				</tr>
			<?php
			} ?>

<?php
			// Display contents
			if (count($this->items) > 0)
			{
				$c = isset($this->c) ? $this->c : 1;
				$this->view('_list')
					 ->set('option', $this->option)
					 ->set('model', $this->model)
					 ->set('subdir', $this->subdir)
					 ->set('items', $this->items)
				     ->set('c', $c)
				     ->set('connect', $this->connect)
				     ->set('publishing', $this->publishing)
				     ->set('fileparams', $this->fileparams)
				     ->set('case', $this->case)
				     ->set('url', $this->url)
					 ->display();
			}
?>			
		</tbody>
	</table>
	<?php
	// Show directory as empty
	if (count($this->items) == 0 || $empty == true) { ?>
		<p class="noresults">
		<?php echo ($this->subdir) ? Lang::txt('PLG_PROJECTS_FILES_THIS_DIRECTORY_IS_EMPTY') : Lang::txt('PLG_PROJECTS_FILES_PROJECT_HAS_NO_FILES'); ?>
		</p>
	<?php } ?>

	<p class="extras">
		<?php if ($this->case == 'files') { ?>
		<span class="leftfloat">
		<?php 
			// Disc space indicator
			$this->view('_mini', 'diskspace')
			     ->set('quota', $this->quota)
			     ->set('dirsize', $this->dirsize)
			     ->set('config', $this->config)
			     ->set('url', $this->url)
			     ->display();
		?>
		</span>
		<?php } ?>
		<?php if ($this->model->access('content')) { ?>
		<span class="rightfloat">
			<a href="<?php echo $this->url . '/?action=trash'; ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_FILES_SHOW_TRASH'); ?></a>
			|
			<a href="<?php echo $this->url . '/?action=status'; ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_FILES_GIT_STATUS'); ?></a>
		</span>
		<?php } ?>
	</p>
 </form>