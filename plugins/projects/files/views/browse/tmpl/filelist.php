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

$class = $this->case == 'apps' ? 'apps' : 'files';
$publishing = $this->publishing && $this->case == 'files' ? 1 : 0;
$subdirlink = $this->subdir ? a . 'subdir=' . urlencode($this->subdir) : '';

// Check used space against quota (percentage)
$inuse = round(($this->dirsize * 100 ) / $this->quota);
if ($inuse < 1) 
{
	$inuse = round((($this->dirsize * 100 ) / $this->quota), 1);
	if ($inuse < 0.1) 
	{
		$inuse = 0.0;
	}
}
$inuse = ($inuse > 100) ? 100 : $inuse;
$approachingQuota = $this->config->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

$lastsync = '';

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
			$lastsync = $this->rSync['status'] == 'complete' ? time() : $this->params->get($service . '_sync', '');
			if ($lastsync)
			{
				$lastsync = '<span class="faded">Last sync: ' . ProjectsHtml::timeAgo($lastsync) . ' ' . JText::_('COM_PROJECTS_AGO') . '</span>' ;
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
			<?php if($this->task == 'newdir') { echo ' &raquo; <span class="indlist">' . JText::_('COM_PROJECTS_FILES_ADD_NEW_FOLDER') . '</span>'; } ?>		</h3>
	</div>
	<?php 
	} ?>
	
	<?php if ($this->app && $this->app->name ) 
	{ 
		// App-only tab menu 
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'apps',
				'name'=>'view'
			)
		);
		
		// Load plugin parameters
		$app_plugin 	= JPluginHelper::getPlugin( 'projects', 'apps' );
		$view->plgparams = new JParameter($app_plugin->params);
		
		$view->route 	= 'index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=apps';
		$view->url 		= JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=apps');
		$view->app 		= $this->app;
		$view->active 	= 'source';
		$view->title 	= 'Apps';
		
		// Get path for app thumb image
		$projectsHelper = new ProjectsHelper( $this->database );
		
		$p_path 			= ProjectsHelper::getProjectPath($this->project->alias, 
			$this->config->get('imagepath'), 1, 'images');			
		$imagePath 			=  $p_path . DS . 'apps';
		$view->projectPath 	= $imagePath;
		$view->path_bc 		= $path_bc;
		$view->ih 			= new ProjectsImgHandler();				
		echo $view->loadTemplate();
		
	 } ?>
	<?php if (!$this->app) { ?>
		<?php 
			// NEW: connections to external services
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'connect',
					'layout' => 'link'
				)
			);
			$view->option 		= $this->option;
			$view->project 		= $this->project;
			$view->uid 			= $this->uid;
			$view->database 	= $this->database;
			$view->connect 		= $this->connect;
			$view->oparams 		= $this->oparams;
			$view->params		= $this->fileparams;
			$view->sizelimit	= $this->sizelimit;
			echo $view->loadTemplate();
		 ?>
	<?php } ?>
	<div class="list-editing">
		<p>			
			<span id="manage_assets">
				<a href="<?php echo $this->url . '/?' . $this->do . '=upload' . $subdirlink; ?>" class="fmanage" id="a-upload" title="<?php echo JText::_('COM_PROJECTS_UPLOAD_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_UPLOAD'); ?></span></a>
				<a href="<?php echo $this->url . '/?' . $this->do . '=newdir' . $subdirlink; ?>" id="a-folder" title="<?php echo JText::_('COM_PROJECTS_FOLDER_TOOLTIP'); ?>" class="fmanage<?php if($this->task == 'newdir') { echo ' inactive'; } ?>"><span><?php echo JText::_('COM_PROJECTS_NEW_FOLDER'); ?></span></a>
				<a href="<?php echo $this->url . '/?' . $this->do . '=download' . $subdirlink; ?>" class="fmanage js" id="a-download" title="<?php echo JText::_('COM_PROJECTS_DOWNLOAD_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_DOWNLOAD'); ?></span></a>
				<a href="<?php echo $this->url . '/?' . $this->do . '=move' . $subdirlink; ?>" class="fmanage js" id="a-move" title="<?php echo JText::_('COM_PROJECTS_MOVE_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_MOVE'); ?></span></a>		
				<a href="<?php echo $this->url . '/?' . $this->do . '=delete' . $subdirlink; ?>" class="fmanage js" id="a-delete" title="<?php echo JText::_('COM_PROJECTS_DELETE_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_DELETE'); ?></span></a>
				<?php if ($this->sharing && in_array('google', $this->services)) { ?>
				<a href="<?php echo $this->url . '/?' . $this->do . '=share' . $subdirlink; ?>" id="a-share" title="<?php echo JText::_('COM_PROJECTS_SHARE_TOOLTIP'); ?>" class="fmanage js" ><span><?php echo JText::_('COM_PROJECTS_FILES_SHARE'); ?></span></a>
				<?php } ?>
				<?php if ($this->fileparams->get('latex')) { ?>				
				<a href="<?php echo $this->url . '/?' . $this->do . '=compile' . $subdirlink; ?>" class="fmanage js" id="a-compile" title="<?php echo JText::_('COM_PROJECTS_COMPILE_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_COMPILE'); ?></span></a>
				<?php } ?>
			</span>	
				<noscript>
					<span class="faded ipadded">Enable JavaScript in your browser for advanced file management.</span>
				</noscript>
			<?php if ($this->sharing) { ?>
			<span class="rightfloat">
				<span id="sync-status"><?php echo $lastsync; ?></span>
			</span>
			<span id="manage_sync">
				<span id="sync-wrap">
				<a href="<?php echo $this->url . '/?' . $this->do . '=sync' . $subdirlink; ?>" id="a-sync" title="<?php echo JText::_('COM_PROJECTS_SYNC_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_SYNC'); ?></span></a>
				</span>
			</span>
			<?php } ?>
		</p>
	</div>
	<table id="filelist" class="listing">
		<thead>
			<tr>
				<th class="checkbox"><input type="checkbox" name="toggle" value="" id="toggle" class="js" /></th>
				<th class="asset_doc <?php if($this->filters['sortby'] == 'filename') { echo ' activesort'; } ?>">
					<a href="<?php echo $this->url . '/?' . $this->do . '=browse' . a . 'sortby=filename'
					. a . 'sortdir='.$sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_NAME'); ?>">
					<?php echo JText::_('COM_PROJECTS_NAME'); ?></a>
				</th>
				<th class="centeralign"></th>
				<th <?php if($this->filters['sortby'] == 'sizes') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo $this->url . '/?' . $this->do . '=browse' . a . 'sortby=sizes' . a . 'sortdir=' . $sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_SIZE'); ?>"><?php echo JText::_('COM_PROJECTS_SIZE'); ?></a>
				</th>
				<th <?php if($this->filters['sortby'] == 'modified') { echo 'class="activesort"'; } ?>>
					<a href="<?php echo $this->url . '/?' . $this->do . '=browse' . a . 'sortby=modified' . a . 'sortdir=' . $sortbyDir . $subdirlink; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . ucfirst(JText::_('COM_PROJECTS_MODIFIED')); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_MODIFIED')); ?></a>
				</th>
				<th><?php echo ucfirst(JText::_('COM_PROJECTS_BY')); ?></th>
				<th class="centeralign nojs"></th>
				<?php if ($publishing) { ?>
				<th><?php echo JText::_('COM_PROJECTS_FILES_PUBLISHED'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>			
			<?php 
			if ($this->task == 'newdir') { ?>
				<tr class="newfolder">
					<td></td>
					<td colspan="<?php echo $publishing ? 7 : 6; ?>">
							<fieldset>
								<input type="hidden" name="<?php echo ($this->app && $this->app->name ) ? 'do' : 'action'; ?>" value="savedir" />
								<label>
									<span class="mini block prominent ipadded"><?php echo JText::_('COM_PROJECTS_NEW_FOLDER'); ?>:</span>
									<img src="/plugins/projects/files/images/folder.gif" alt="" />
									<input type="text" name="newdir" maxlength="100" value="untitled" />
								</label>
								<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE'); ?>" />
								<span class="btn btncancel mini"><a href="<?php echo $this->url . '/?' . $this->do . '=view' . $subdirlink; ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a></span>
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
					<td colspan="<?php echo $publishing ? 7 : 6; ?>" class="mini">
						<a href="<?php echo $this->url . '/?' . $this->do . '=browse' . a . 'subdir=' . $parent; ?>" class="uptoparent"><?php echo JText::_('COM_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a>
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
						$view = new Hubzero_Plugin_View(
							array(
								'folder'=>'projects',
								'element'=>'files',
								'name'=>'item',
								'layout' => 'folder'
							)
						);
						$view->subdir 		= $this->subdir;
						$view->item 		= $dir;
						$view->option 		= $this->option;
						$view->project 		= $this->project;
						$view->juser 		= $this->juser;
						$view->c			= $c;
						$view->connect 		= $this->connect;
						$view->publishing 	= $publishing;
						$view->oparams 		= $this->oparams;
						$view->params		= $this->fileparams;
						$view->case 		= $this->case;
						$view->url			= $this->url;
						$view->do 			= $this->do;
						echo $view->loadTemplate();
					}
					elseif ($type == 'document')
					{
						$file = $item['item'];
						
						// Hide gitignore file
						if($file['name'] == '.gitignore')
						{
							if (count($this->items) == 1)
							{
								$empty = 1;
							}
							continue;
						}
						
						// Document view
						$view = new Hubzero_Plugin_View(
							array(
								'folder'=>'projects',
								'element'=>'files',
								'name'=>'item',
								'layout' => 'document'
							)
						);
						$view->subdir 		= $this->subdir;
						$view->item 		= $file;
						$view->option 		= $this->option;
						$view->project 		= $this->project;
						$view->juser 		= $this->juser;
						$view->c			= $c;
						$view->connect 		= $this->connect;
						$view->publishing 	= $publishing;
						$view->oparams 		= $this->oparams;
						$view->params		= $this->fileparams;
						$view->case 		= $this->case;
						$view->url			= $this->url;
						$view->do 			= $this->do;
						echo $view->loadTemplate();
					}
					elseif ($type == 'remote')
					{						
						// Remote file
						$view = new Hubzero_Plugin_View(
							array(
								'folder'=>'projects',
								'element'=>'files',
								'name'=>'item',
								'layout' => $item['remote']
							)
						);
						$view->subdir 		= $this->subdir;
						$view->item 		= $item['item'];
						$view->option 		= $this->option;
						$view->project 		= $this->project;
						$view->juser 		= $this->juser;
						$view->c			= $c;
						$view->connect 		= $this->connect;
						$view->publishing 	= $publishing;
						$view->oparams 		= $this->oparams;
						$view->params		= $this->fileparams;
						$view->case 		= $this->case;
						$view->url			= $this->url;
						$view->do 			= $this->do;
						echo $view->loadTemplate();
					}
			 		$c++;
				}
			}
			
			// Show directory as empty			
			if (count($this->items) == 0 || $empty == true) { ?>
				<tr>
					<td colspan="<?php echo $publishing ? 7 : 6; ?>" class="mini faded">
						<?php if ($this->subdir || $this->app) 
							{ 
								echo JText::_('COM_PROJECTS_THIS_DIRECTORY_IS_EMPTY'); 
								if (!$this->app)
								{
									echo ' <a href="' . $this->url . '/?' . $this->do . '=deletedir' . a 
									. 'dir='.urlencode($this->subdir) . '" class="delete" id="delete-dir">' 
									. JText::_('COM_PROJECTS_DELETE_THIS_DIRECTORY') . '</a>';
								}
							}
							else 
							{
								echo JText::_('COM_PROJECTS_FILES_PROJECT_HAS_NO_FILES'); 
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
		<?php echo JText::_('COM_PROJECTS_FILES_DISK_SPACE'); ?>
		<a href="<?php echo $this->url . '/?' . $this->do . '=diskspace'; ?>" title="<?php echo JText::_('COM_PROJECTS_FILES_DISK_SPACE_TOOLTIP'); ?>"><span id="indicator-wrapper" <?php if ($warning) { echo 'class="quota-warning"'; } ?>><span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse.'% '.JText::_('COM_PROJECTS_FILES_USED'); ?></span></span></span></a>
			 <span class="show-quota"><?php echo JText::_('COM_PROJECTS_FILES_QUOTA') . ': ' . ProjectsHtml::formatSize($this->quota); ?></span>
		</span>
		<?php } ?>
		<span class="rightfloat">	
			<a href="<?php echo $this->url . '/?' . $this->do . '=status'; ?>" class="showinbox"><?php echo JText::_('COM_PROJECTS_FILES_GIT_STATUS'); ?></a>
		</span>
	</p>
 </form>