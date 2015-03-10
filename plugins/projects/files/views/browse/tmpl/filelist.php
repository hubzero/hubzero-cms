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

// Sorting
$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

// Empty directory?
$empty = false;

// Directory path breadcrumbs
$desect_path = explode(DS, $this->subdir);
$path_bc = '';
$url = '';
$parent = '';
if ($this->subdir && count($desect_path) > 0)
{
	for ($p = 0; $p < count($desect_path); $p++)
	{
		$parent   = count($desect_path) > 1 && $p != count($desect_path)  ? $url  : '';
		$url  	 .= DS . $desect_path[$p];
		$path_bc .= ' &raquo; <span><a href="'. $this->url . '/?subdir='
			. urlencode($url) . '" class="folder">' . $desect_path[$p].'</a></span> ';
	}
}

$class = $this->case == 'tools' ? 'tools' : 'files';
$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

$lastsync = '';
$connected = $this->oparams->get('google_token') ? true : false;

?>
<div id="sync_output" class="hidden"></div>

<div id="preview-window"></div>
 <form action="<?php echo $this->url; ?>" method="post" enctype="multipart/form-data" id="plg-form" class="file-browser submit-ajax" >
	<input type="hidden" name="case" id="case" value="<?php echo $this->case; ?>" />
	<input type="hidden" name="subdir" id="subdir" value="<?php echo urlencode($this->subdir); ?>" />
	<input type="hidden" name="sortby" id="sortby" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="sortdir" id="sortdir" value="<?php echo $this->filters['sortdir']; ?>" />
	<input type="hidden" name="id" id="projectid" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="sync" id="sync" value="<?php echo $this->sync; ?>" />
	<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
	<input type="hidden" name="sharing" id="sharing" value="<?php echo $this->sharing; ?>" />
<?php if ($this->sharing && !empty($this->services)) {

		foreach ($this->services as $service)
		{
			$lastsync = $this->rSync['status'] == 'complete' ? date("c") : $this->params->get($service . '_sync', '');
			if ($lastsync)
			{
				$lastsync = '<span class="faded">' . \JText::_('PLG_PROJECTS_FILES_LAST_SYNC') . ' ' . \Components\Projects\Helpers\Html::timeAgo($lastsync, false) . ' ' . JText::_('COM_PROJECTS_AGO') . ' </span>' ;
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
			<?php if ($this->subdir) { ?></a><?php echo $path_bc; ?><?php } ?>
			<?php if ($this->task == 'newdir') { echo ' &raquo; <span class="indlist">' . JText::_('PLG_PROJECTS_FILES_ADD_NEW_FOLDER') . '</span>'; } ?>
		</h3>
	</div>
	<?php
	} ?>

	<?php
		// NEW: connections to external services
		$this->view('link', 'connect')
		     ->set('option', $this->option)
		     ->set('project', $this->project)
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
			<span id="manage_assets">
				<a href="<?php echo $this->url . '/?' . $this->do . '=upload' . $subdirlink; ?>" class="fmanage" id="a-upload" title="<?php echo JText::_('PLG_PROJECTS_FILES_UPLOAD_TOOLTIP'); ?>"><span><?php echo JText::_('PLG_PROJECTS_FILES_UPLOAD'); ?></span></a>
				<a href="<?php echo $this->url . '/?' . $this->do . '=newdir' . $subdirlink; ?>" id="a-folder" title="<?php echo JText::_('PLG_PROJECTS_FILES_FOLDER_TOOLTIP'); ?>" class="fmanage<?php if ($this->task == 'newdir') { echo ' inactive'; } ?>"><span><?php echo JText::_('PLG_PROJECTS_FILES_NEW_FOLDER'); ?></span></a>
				<a href="<?php echo $this->url . '/?' . $this->do . '=download' . $subdirlink; ?>" class="fmanage js" id="a-download" title="<?php echo JText::_('PLG_PROJECTS_FILES_DOWNLOAD_TOOLTIP'); ?>"><span><?php echo JText::_('PLG_PROJECTS_FILES_DOWNLOAD'); ?></span></a>
				<a href="<?php echo $this->url . '/?' . $this->do . '=move' . $subdirlink; ?>" class="fmanage js" id="a-move" title="<?php echo JText::_('PLG_PROJECTS_FILES_MOVE_TOOLTIP'); ?>"><span><?php echo JText::_('PLG_PROJECTS_FILES_MOVE'); ?></span></a>
				<a href="<?php echo $this->url . '/?' . $this->do . '=delete' . $subdirlink; ?>" class="fmanage js" id="a-delete" title="<?php echo JText::_('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>"><span><?php echo JText::_('PLG_PROJECTS_FILES_DELETE'); ?></span></a>
				<?php if ($this->sharing && in_array('google', $this->services) && $connected) { ?>
				<a href="<?php echo $this->url . '/?' . $this->do . '=share' . $subdirlink; ?>" id="a-share" title="<?php echo JText::_('PLG_PROJECTS_FILES_SHARE_TOOLTIP'); ?>" class="fmanage js" ><span><?php echo JText::_('PLG_PROJECTS_FILES_SHARE'); ?></span></a>
				<?php } ?>
				<?php if ($this->fileparams->get('latex')) { ?>
				<a href="<?php echo $this->url . '/?' . $this->do . '=compile' . $subdirlink; ?>" class="fmanage js" id="a-compile" title="<?php echo JText::_('PLG_PROJECTS_FILES_COMPILE_TOOLTIP'); ?>"><span><?php echo JText::_('PLG_PROJECTS_FILES_COMPILE'); ?></span></a>
				<?php } ?>
			</span>
				<noscript>
					<span class="faded ipadded"><?php echo JText::_('Enable JavaScript in your browser for advanced file management.'); ?></span>
				</noscript>
			<?php if ($this->sharing) { ?>
			<span class="rightfloat">
				<span id="sync-status"><?php echo $lastsync; ?></span>
			</span>
			<span id="manage_sync">
				<span id="sync-wrap">
				<a href="<?php echo $this->url . '/?' . $this->do . '=sync' . $subdirlink; ?>" id="a-sync" title="<?php echo JText::_('PLG_PROJECTS_FILES_SYNC_TOOLTIP'); ?>"><span><?php echo JText::_('PLG_PROJECTS_FILES_SYNC'); ?></span></a>
				</span>
			</span>
			<?php } ?>
		</p>
	</div>
	<table id="filelist" class="listing">
		<thead>
			<tr>
				<th class="checkbox"><input type="checkbox" name="toggle" value="" id="toggle" class="js" /></th>
				<th class="asset_doc <?php if ($this->filters['sortby'] == 'filename') { echo ' activesort'; } ?>">
					<a href="<?php echo $this->url . '/?' . $this->do . '=browse&amp;sortby=filename&amp;sortdir='.$sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo JText::_('PLG_PROJECTS_FILES_SORT_BY') . ' ' . JText::_('PLG_PROJECTS_FILES_NAME'); ?>">
					<?php echo JText::_('PLG_PROJECTS_FILES_NAME'); ?></a>
				</th>
				<th class="centeralign"></th>
				<th <?php if ($this->filters['sortby'] == 'sizes') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo $this->url . '/?' . $this->do . '=browse&amp;sortby=sizes&amp;sortdir=' . $sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo JText::_('PLG_PROJECTS_FILES_SORT_BY') . ' ' . JText::_('PLG_PROJECTS_FILES_SIZE'); ?>"><?php echo JText::_('PLG_PROJECTS_FILES_SIZE'); ?></a>
				</th>
				<th <?php if ($this->filters['sortby'] == 'modified') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo $this->url . '/?' . $this->do . '=browse&amp;sortby=modified&amp;sortdir=' . $sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo JText::_('PLG_PROJECTS_FILES_SORT_BY') . ' ' . ucfirst(JText::_('PLG_PROJECTS_FILES_MODIFIED')); ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_FILES_MODIFIED')); ?></a>
				</th>
				<th><?php echo ucfirst(JText::_('PLG_PROJECTS_FILES_BY')); ?></th>
				<th class="centeralign nojs"></th>
				<?php if ($this->publishing) { ?>
				<th><?php echo JText::_('PLG_PROJECTS_FILES_PUBLISHED'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($this->task == 'newdir') { ?>
				<tr class="newfolder">
					<td></td>
					<td colspan="<?php echo $this->publishing ? 7 : 6; ?>">
							<fieldset>
								<input type="hidden" name="<?php echo ($this->tool && $this->tool->name ) ? 'do' : 'action'; ?>" value="savedir" />
								<label>
									<span class="mini block prominent ipadded"><?php echo JText::_('PLG_PROJECTS_FILES_NEW_FOLDER'); ?>:</span>
									<img src="/plugins/projects/files/images/folder.gif" alt="" />
									<input type="text" name="newdir" maxlength="100" value="untitled" />
								</label>
								<input type="submit" value="<?php echo JText::_('PLG_PROJECTS_FILES_SAVE'); ?>" />
								<span class="btn btncancel mini"><a href="<?php echo $this->url . '/?' . $this->do . '=view' . $subdirlink; ?>"><?php echo JText::_('PLG_PROJECTS_FILES_CANCEL'); ?></a></span>
							</fieldset>
					</td>
				</tr>
			<?php } ?>
			<?php
			// Go back one level to parent directory
			if ($this->subdir)
			{ ?>
				<tr>
					<td></td>
					<td colspan="<?php echo $this->publishing ? 7 : 6; ?>" class="mini">
						<a href="<?php echo $this->url . '/?' . $this->do . '=browse&amp;subdir=' . $parent; ?>" class="uptoparent"><?php echo JText::_('PLG_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a>
					</td>
				</tr>
			<?php
			}

			// Display contents
			if (count($this->items) > 0)
			{
				$c = 1;
				foreach ($this->items as $item)
				{
					$type = $item['type'];

					if ($type == 'folder')
					{
						$dir = $item['item'];

						// Folder view
						$this->view('folder', 'item')
						     ->set('subdir', $this->subdir)
						     ->set('item', $dir)
						     ->set('option', $this->option)
						     ->set('project', $this->project)
						     ->set('juser', $this->juser)
						     ->set('c', $c)
						     ->set('connect', $this->connect)
						     ->set('publishing', $this->publishing)
						     ->set('oparams', $this->oparams)
						     ->set('params', $this->fileparams)
						     ->set('case', $this->case)
						     ->set('url', $this->url)
						     ->set('do', $this->do)
						     ->display();
					}
					elseif ($type == 'document')
					{
						$file = $item['item'];

						// Hide gitignore file
						if ($file['name'] == '.gitignore')
						{
							if (count($this->items) == 1)
							{
								$empty = true;
							}
							continue;
						}

						// Document view
						$this->view('document', 'item')
						     ->set('subdir', $this->subdir)
						     ->set('item', $file)
						     ->set('option', $this->option)
						     ->set('project', $this->project)
						     ->set('juser', $this->juser)
						     ->set('c', $c)
						     ->set('connect', $this->connect)
						     ->set('publishing', $this->publishing)
						     ->set('oparams', $this->oparams)
						     ->set('params', $this->fileparams)
						     ->set('case', $this->case)
						     ->set('url', $this->url)
						     ->set('do', $this->do)
						     ->display();
					}
					elseif ($type == 'remote')
					{
						// Remote file
						$this->view($item['remote'], 'item')
						     ->set('subdir', $this->subdir)
						     ->set('item', $item['item'])
						     ->set('option', $this->option)
						     ->set('project', $this->project)
						     ->set('juser', $this->juser)
						     ->set('c', $c)
						     ->set('connect', $this->connect)
						     ->set('publishing', $this->publishing)
						     ->set('oparams', $this->oparams)
						     ->set('params', $this->fileparams)
						     ->set('case', $this->case)
						     ->set('url', $this->url)
						     ->set('do', $this->do)
						     ->display();
					}
			 		$c++;
				}
			}

			// Show directory as empty
			if (count($this->items) == 0 || $empty == true) { ?>
				<tr>
					<td colspan="<?php echo $this->publishing ? 7 : 6; ?>" class="mini faded">
						<?php if ($this->subdir || $this->tool)
							{
								echo JText::_('PLG_PROJECTS_FILES_THIS_DIRECTORY_IS_EMPTY');
								if (!$this->tool)
								{
									echo ' <a href="' . $this->url . '/?' . $this->do . '=deletedir&amp;dir='.urlencode($this->subdir) . '" class="delete" id="delete-dir">'
									. JText::_('PLG_PROJECTS_FILES_DELETE_THIS_DIRECTORY') . '</a>';
								}
							}
							else
							{
								echo JText::_('PLG_PROJECTS_FILES_PROJECT_HAS_NO_FILES');
							}
						?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

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
			     ->set('do', $this->do)
			     ->display();
		?>
		</span>
		<?php } ?>
		<span class="rightfloat">
			<a href="<?php echo $this->url . '/?' . $this->do . '=trash'; ?>" class="showinbox"><?php echo JText::_('PLG_PROJECTS_FILES_SHOW_TRASH'); ?></a>
			|
			<a href="<?php echo $this->url . '/?' . $this->do . '=status'; ?>" class="showinbox"><?php echo JText::_('PLG_PROJECTS_FILES_GIT_STATUS'); ?></a>
		</span>
	</p>
 </form>