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

// Paging & sorting
$next = $this->filters['start'] + $this->filters['limit'];
$prev = $this->filters['start'] - $this->filters['limit'];
$prev = $prev < 0 ? 0 : $prev;
$whatsleft = $this->total - $this->filters['start'] - $this->filters['limit'];
$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$empty = 0;

// Directory path breadcrumbs
$desect_path = explode(DS, $this->subdir);
$path_bc = '';
$url = '';
$parent = '';
if ($this->subdir && count($desect_path) > 0) {
	for ($p = 0; $p < count($desect_path); $p++) {
		$parent .= count($desect_path) > 1 && $p != count($desect_path)  ? $url  : '';
		$url .= DS.$desect_path[$p];
		$path_bc .= ' &raquo; <span><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=files').'/?subdir='.urlencode($url).'" class="folder">'.$desect_path[$p].'</a></span> ';
	}
}

// Remote folder?
if ($this->remotedir)
{
	$bits = explode(':', $this->remoteid);
	$remoteservice = $bits[0];
	
	$path_bc .= ' &raquo; <span><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=files').'/?subdir='.urlencode($this->subdir). a . 'remotedir=' . $this->remotedir . a. 'remoteid=' . $this->remoteid.'" class="remotedir"><span class="s-google"> '.$this->remotedir.' [' . JText::_('COM_PROJECTS_FILES_REMOTE_FOLDER') . ']</span></a></span> ';
}

$class = $this->case == 'tools' ? 'tools' : 'files';
$publishing = $this->publishing && $this->case == 'files' ? 1 : 0;

$goto  = 'alias='.$this->project->alias;

$subdirlink = $this->subdir ? a . 'subdir=' . urlencode($this->subdir) : '';

// Check used space against quota (percentage)
$inuse = round(($this->dirsize * 100 )/ $this->quota);
if($this->total > 0 && $inuse < 1) {
	$inuse = round((($this->dirsize * 100 )/ $this->quota), 1);
	if($inuse < 0.1) {
		$inuse = 0.1;
	}
}
$inuse = ($inuse > 100) ? 100 : $inuse;
$approachingQuota = $this->config->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

// Determine which external services are enabled
$google 	= $this->connect->getConfigs('google');
$dropbox 	= $this->connect->getConfigs('dropbox');
$gConnected = $google && $google['active'] && $this->oparams->get('google_token') ? 1 : 0;
$dConnected = $dropbox && $dropbox['active'] && $this->oparams->get('dropbox_token') ? 1 : 0;
$sharing	= !$this->tool ? $this->remote_active : 0;

$services = $this->connect->getActive();

?>
<div id="preview-window"></div>
<form action="<?php echo JRoute::_('index.php?option=' . $this->option . a . $goto . '&active=files'); ?>" method="post" enctype="multipart/form-data" id="plg-form" class="file-browser submit-ajax" >
	<input type="hidden" name="case" id="case" value="<?php echo $this->case; ?>" />
	<input type="hidden" name="subdir" id="subdir" value="<?php echo urlencode($this->subdir); ?>" />
	<input type="hidden" name="remotedir" id="remotedir" value="<?php echo urlencode($this->remotedir); ?>" />
	<input type="hidden" name="remoteid" id="remoteid" value="<?php echo urlencode($this->remoteid); ?>" />
	<input type="hidden" name="id" id="projectid" value="<?php echo $this->project->id; ?>" />	
	<input type="hidden" name="sync" id="sync" value="<?php echo $this->sync; ?>" />
	<input type="hidden" name="sharing" id="sharing" value="<?php echo $sharing; ?>" />
	<?php if($this->case == 'files') { ?>
	<div id="plg-header">
		<h3 class="<?php echo $class; ?>"><?php if($this->subdir || $this->sdir) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active='.$this->case); ?>"><?php } ?><?php echo $this->title; ?><?php if($this->subdir || $this->sdir) { ?></a> <?php echo $path_bc; ?><?php } ?><?php if($this->task == 'newdir') { echo ' &raquo; <span class="indlist">' . JText::_('COM_PROJECTS_FILES_ADD_NEW_FOLDER') . '</span>'; } ?></h3>
	</div>
	<?php } ?>
	<?php if($this->tool && $this->tool->name ) { 
		echo ProjectsHtml::toolDevHeader( $this->option, $this->config, $this->project, $this->tool, 'source', $path_bc);
	?>

	<?php } ?>
	<?php if (!$this->tool) { ?>
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
			$view->option = $this->option;
			$view->project = $this->project;
			$view->uid = $this->uid;
			$view->database = $this->database;
			$view->connect = $this->connect;
			$view->oparams = $this->oparams;
			echo $view->loadTemplate();
		 ?>
	<?php } ?>

	<div class="list-editing">
		<p>			
		<span id="manage_assets">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'/?action=upload' . $subdirlink . a . 'case=' . $this->case; ?>" class="fmanage" id="a-upload" title="<?php echo JText::_('COM_PROJECTS_UPLOAD_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_UPLOAD'); ?></span></a>
			<?php if (!$this->remotedir)
			{ ?>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'/?action=newdir' . $subdirlink . a . 'case=' . $this->case; ?>" id="a-folder" title="<?php echo JText::_('COM_PROJECTS_FOLDER_TOOLTIP'); ?>" class="fmanage<?php if($this->task == 'newdir') { echo ' inactive'; } ?>"><span><?php echo JText::_('COM_PROJECTS_NEW_FOLDER'); ?></span></a>
			<?php } ?>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'/?action=download'; ?>" class="fmanage js" id="a-download" title="<?php echo JText::_('COM_PROJECTS_DOWNLOAD_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_DOWNLOAD'); ?></span></a>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . a.'task=view'.a.$goto.a.'active=files').'/?action=move'; ?>" class="fmanage js" id="a-move" title="<?php echo JText::_('COM_PROJECTS_MOVE_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_MOVE'); ?></span></a>		
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'/?action=delete'; ?>" class="fmanage js" id="a-delete" title="<?php echo JText::_('COM_PROJECTS_DELETE_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_DELETE'); ?></span></a>
		</span>	
	</p>
	</div>
	<table id="filelist" class="listing">
		<thead>
			<tr>
				<th class="checkbox"><input type="checkbox" name="toggle" value="" id="toggle" class="js" /></th>
				<th class="asset_doc <?php if($this->filters['sortby'] == 'filename') { echo ' activesort'; } ?>">
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . a
					. 'task=view' . a . $goto . a . 'active=' . $this->case) . '?action=browse' . a . 'sortby=filename'
					. a . 'sortdir='.$sortbyDir . a . 'subdir='.urlencode($this->subdir) . a . 'sync=1'; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_NAME'); ?>">
					<?php echo JText::_('COM_PROJECTS_NAME'); ?></a></th>
				<th <?php if($this->filters['sortby'] == 'sizes') { echo 'class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active='.$this->case).'?action=browse'.a.'sortby=sizes'.a.'sortdir='.$sortbyDir.a.'subdir='.urlencode($this->subdir) . a . 'sync=1'; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_SIZE'); ?>"><?php echo JText::_('COM_PROJECTS_SIZE'); ?></a></th>
				<th <?php if($this->filters['sortby'] == 'modified') { echo 'class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active='.$this->case).'?action=browse'.a.'sortby=modified'.a.'sortdir='.$sortbyDir.a.'subdir='.urlencode($this->subdir); ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . ucfirst(JText::_('COM_PROJECTS_MODIFIED')); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_MODIFIED')); ?></a></th>
				<th><?php echo ucfirst(JText::_('COM_PROJECTS_BY')); ?></th>
				<th class="centeralign nojs"></th>
				<?php if($publishing) { ?>
				<th><?php echo JText::_('COM_PROJECTS_FILES_PUBLISHED'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>			
			<?php 
			if ($this->task == 'newdir') { ?>
				<tr class="newfolder">
					<td></td>
					<td colspan="<?php echo $publishing ? 6 : 5; ?>">
							<fieldset>
								<input type="hidden" name="action" value="savedir" />
								<label>
									<span class="mini block prominent ipadded"><?php echo JText::_('COM_PROJECTS_NEW_FOLDER'); ?>:</span>
									<img src="/plugins/projects/files/images/folder.gif" alt="" />
									<input type="text" name="newdir" maxlength="100" value="untitled" />
								</label>
								<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE'); ?>" />
								<span class="btn btncancel mini"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . $goto . a . 'active=files') . '?subdir='.urlencode($this->subdir) . a . 'case=' . $this->case; ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a></span>
							</fieldset>
					</td>				
				</tr>	
			<?php } ?>
			<?php 
			if ($this->subdir) { ?>
				<tr>
					<td></td>
					<td colspan="<?php echo $publishing ? 6 : 5; ?>" class="mini"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active='.$this->case).'/?action=browse'.a.'subdir='.$parent; ?>" class="uptoparent"><?php echo JText::_('COM_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a></td>
				</tr>
			<?php }
			if (count($this->combined) > 0) {
				$c = 1;
				foreach ($this->combined as $combined) {
					
					if ($combined['type'] == 'folder')
					{
						$dir = $combined['item'];
						$dirpath = $this->subdir ? $this->subdir . DS . $dir : $dir;
					?>
						<tr class="mini faded mline">
							<td><input type="checkbox" value="<?php echo urlencode($dir); ?>" name="folder[]" class="checkasset js dirr" /></td>
							<td class="top_valign nobsp"><img src="/plugins/projects/files/images/folder.gif" alt="<?php echo $dir; ?>" />
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'active='.$this->case.a.$goto).'/?action=browse'.a.'subdir='.urlencode($dirpath); ?>" class="dir:<?php echo urlencode($dir); ?>" title="<?php echo JText::_('COM_PROJECTS_FILES_GO_TO_DIR') . ' ' . $dir; ?>" id="edit-c-<?php echo $c; ?>"><?php echo $dir; ?></a>
								<span id="rename-c-<?php echo $c; ?>" class="rename js" title="<?php echo JText::_('COM_PROJECTS_FILES_RENAME_DIR_TOOLTIP'); ?>">&nbsp;</span>
							</td>
							<td colspan="<?php echo $publishing ? 5 : 4; ?>"></td>
						</tr>					
					<?php
					}
					elseif ($combined['type'] == 'document')
					{	
						$file = $combined['item'];
						$remote = $combined['remote'];
						
						// Hide gitignore file
						if($file['name'] == '.gitignore')
						{
							if (count($this->combined) == 1)
							{
								$empty = 1;
							}
							continue;
						}
						
					?>
					<?php 
						if (!$remote) 
						{ 	
							// Local file
							$view = new Hubzero_Plugin_View(
								array(
									'folder'=>'projects',
									'element'=>'files',
									'name'=>'item',
									'layout' => 'document'
								)
							);
							$view->subdir 		= $this->subdir;
							$view->item 		= $combined['item'];
							$view->option 		= $this->option;
							$view->project 		= $this->project;
							$view->juser 		= $this->juser;
							$view->gConnected 	= $gConnected;
							$view->c			= $c;
							$view->connect 		= $this->connect;
							$view->publishing 	= $publishing;
							$view->oparams 		= $this->oparams;
							$view->case 		= $this->case;
							echo $view->loadTemplate();
						} 
				 	}	
					elseif ($combined['type'] == 'remote')
					{
						// Google file
						$view = new Hubzero_Plugin_View(
							array(
								'folder'=>'projects',
								'element'=>'files',
								'name'=>'item',
								'layout' => $combined['remote']
							)
						);
						$view->subdir 		= $this->subdir;
						$view->item 		= $combined['item'];
						$view->option 		= $this->option;
						$view->project 		= $this->project;
						$view->sync 		= $this->sync;
						$view->connected 	= $gConnected;
						$view->connect 		= $this->connect;
						$view->publishing 	= $publishing;
						$view->oparams 		= $this->oparams;
						echo $view->loadTemplate();
					}
					
			 		$c++;
				}
			}
			
			// Show directory as empty			
			if(count($this->combined) == 0 || $empty) { ?>
				<tr>
					<td colspan="<?php echo $publishing ? 7 : 6; ?>" class="mini faded">
						<?php if ($this->subdir || $this->tool || $this->remotedir) 
							{ 
								echo JText::_('COM_PROJECTS_THIS_DIRECTORY_IS_EMPTY'); 
								if (!$this->tool && !$this->remotedir)
								{
									echo ' <a href="' . JRoute::_('index.php?option=' . $this->option . a . 'active=files' 
									. a . $goto) . '/?action=deletedir' . a . 'case='.$this->case . a 
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
		<span class="leftfloat">
		<?php echo JText::_('COM_PROJECTS_FILES_DISK_SPACE'); ?>
		<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'?case='.$this->case.a.'action=diskspace'; ?>" title="<?php echo JText::_('COM_PROJECTS_FILES_DISK_SPACE_TOOLTIP'); ?>"><span id="indicator-wrapper" <?php if($warning) { echo 'class="quota-warning"'; } ?>><span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse.'% '.JText::_('COM_PROJECTS_FILES_USED'); ?></span></span></span></a>
			 <span class="show-quota"><?php echo JText::_('COM_PROJECTS_FILES_QUOTA') . ': ' . ProjectsHtml::formatSize($this->quota); ?></span>
		</span>
		<span class="rightfloat">	
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=files').'/?action=status'.a.'case='.$this->case; ?>" class="showinbox"><?php echo JText::_('COM_PROJECTS_FILES_GIT_STATUS'); ?></a>
		</span>
	</p>
</form>
<div id="sync-msg" class="hidden"><?php echo $this->sync_message; ?></div>