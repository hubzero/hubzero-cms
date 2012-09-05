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
if(count($desect_path) > 0) {
	for($p = 0; $p < count($desect_path); $p++) {
		$parent .= count($desect_path) > 1 && $p != count($desect_path)  ? $url  : '';
		$url .= DS.$desect_path[$p];
		$path_bc .= ' &raquo; <span><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=files').'/?subdir='.urlencode($url).'" class="folder">'.$desect_path[$p].'</a></span> ';
	}
}

$class = $this->case == 'apps' ? 'apps' : 'files';
$publishing = $this->publishing && $this->case == 'files' ? 1 : 0;

// Use alias or id in urls?
$use_alias = $this->config->get('use_alias', 0);
$goto  = $use_alias ? 'alias='.$this->project->alias : 'id='.$this->project->id;

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

?>
<div id="preview-window"></div>
<form action="<?php echo JRoute::_('index.php?option=' . $this->option . a . $goto . '&active=files'); ?>" method="post" enctype="multipart/form-data" id="plg-form" class="file-browser submit-ajax" >	
	<?php if($this->case == 'files') { ?>
	<div id="plg-header">
		<h3 class="<?php echo $class; ?>"><?php if($this->subdir) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active='.$this->case); ?>"><?php } ?><?php echo $this->title; ?><?php if($this->subdir) { ?></a> <?php echo $path_bc; ?><?php } ?><?php if($this->task == 'newdir') { echo ' &raquo; <span class="indlist">' . JText::_('COM_PROJECTS_FILES_ADD_NEW_FOLDER') . '</span>'; } ?></h3>
	</div>
	<?php } ?>
	<?php if($this->app && $this->app->name ) { 
		// App-only tab menu 
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'apps',
				'name'=>'view'
			)
		);
		
		// Load plugin parameters
		$app_plugin 		= JPluginHelper::getPlugin( 'projects', 'apps' );
		$view->plgparams 	= new JParameter($app_plugin->params);
		
		$view->route = 'index.php?option=' . $this->option . a . $goto . '&active=apps';
		$view->url = JRoute::_('index.php?option=' . $this->option . a . $goto . '&active=apps');
		$view->app = $this->app;
		$view->active = 'source';
		$view->title = 'Apps';
		
		// Get path for app thumb image
		$projectsHelper = new ProjectsHelper( $this->database );
		
		$p_path = ProjectsHelper::getProjectPath($this->project->alias, 
			$this->config->get('imagepath'), 1, 'images');
			
		$imagePath =  $p_path . DS . 'apps';
		$view->projectPath = $imagePath;

		$view->ih = new ProjectsImgHandler();
				
		echo $view->loadTemplate();
	?>

	<?php } ?>
	<?php if(!$this->app) { ?>
	<p class="editing mini pale"><?php echo JText::_('COM_PROJECTS_FILES_MAX_UPLOAD').' '.$this->sizelimit; ?></p>
	<?php } ?>
	<div class="list-editing">
	 <p>	
		<span id="file-manage" class="manage-options">			
			<span id="manage_assets">
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'?action=newdir' . $subdirlink . a . 'case=' . $this->case; ?>" id="a-folder" title="<?php echo JText::_('COM_PROJECTS_FOLDER_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_NEW_FOLDER'); ?></span></a>
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'?action=download'; ?>" class="fmanage js" id="a-download" title="<?php echo JText::_('COM_PROJECTS_DOWNLOAD_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_DOWNLOAD'); ?></span></a>
				<a href="<?php echo JRoute::_('index.php?option=' . $this->option . a.'task=view'.a.$goto.a.'active=files').'?action=move'; ?>" class="fmanage js" id="a-move" title="<?php echo JText::_('COM_PROJECTS_MOVE_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_MOVE'); ?></span></a>		
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active=files').'?action=delete'; ?>" class="fmanage js" id="a-delete" title="<?php echo JText::_('COM_PROJECTS_DELETE_TOOLTIP'); ?>"><span><?php echo JText::_('COM_PROJECTS_DELETE'); ?></span></a>
				<noscript>
					<span class="nojs"><?php echo JText::_('COM_PROJECTS_JS_OPTIONS'); ?></span>
				</noscript>
				<label class="addnew">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->config->get('maxUpload', '104857600'); ?>" />
					<input name="upload[]" type="file" size="20" class="option uploader" id="uploader" multiple="multiple" /> 
				</label>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" id="pid" value="<?php echo $this->project->id; ?>" />
					<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
					<input type="hidden" name="active" value="files" />
					<input type="hidden" name="case" id="case" value="<?php echo $this->case; ?>" />
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="subdir" id="subdir" value="<?php echo $this->subdir; ?>" />
					<input type="hidden" name="expand_zip" id="expand_zip" value="0" />
					<input type="submit" value="<?php echo JText::_('COM_PROJECTS_UPLOAD'); ?>" class="btn yesbtn" id="f-upload" />
			</span>	
		</span>
		<span id="u-hd">
	<?php if(!$this->subdir) { ?><span class="prominent"> <?php echo $this->total; ?></span> <?php echo JText::_('COM_PROJECTS_FILES_S'); ?> <?php echo JText::_('COM_PROJECTS_TOTAL'); ?> <?php } ?>
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
					. a . 'sortdir='.$sortbyDir . a . 'subdir='.urlencode($this->subdir); ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_NAME'); ?>">
					<?php echo JText::_('COM_PROJECTS_NAME'); ?></a></th>
				<th class="js"></th>
				<th <?php if($this->filters['sortby'] == 'sizes') { echo 'class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active='.$this->case).'?action=browse'.a.'sortby=sizes'.a.'sortdir='.$sortbyDir.a.'subdir='.urlencode($this->subdir); ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_SIZE'); ?>"><?php echo JText::_('COM_PROJECTS_SIZE'); ?></a></th>
				<th <?php if($this->filters['sortby'] == 'modified') { echo 'class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto.a.'active='.$this->case).'?action=browse'.a.'sortby=modified'.a.'sortdir='.$sortbyDir.a.'subdir='.urlencode($this->subdir); ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . ucfirst(JText::_('COM_PROJECTS_MODIFIED')); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_MODIFIED')); ?></a></th>
				<th><?php echo ucfirst(JText::_('COM_PROJECTS_BY')); ?></th>
				<th class="centeralign"><?php echo JText::_('COM_PROJECTS_REVISIONS'); ?></th>
				<?php if($publishing) { ?>
				<th class="asset_resource"><?php echo JText::_('COM_PROJECTS_ASSOCIATED_PUBLICATION'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php 
			if($this->task == 'newdir') { ?>
				<tr class="newfolder">
					<td></td>
					<td colspan="<?php echo $publishing ? 7 : 6; ?>">
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
			if($this->subdir) { ?>
				<tr>
					<td></td>
					<td colspan="<?php echo $publishing ? 7 : 6; ?>" class="mini"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active='.$this->case).'/?action=browse'.a.'subdir='.$parent; ?>" class="uptoparent"><?php echo JText::_('COM_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a></td>
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
							<td><input type="checkbox" value="<?php echo urlencode($dir); ?>" name="folder[]" class="checkasset js dir" /></td>
							<td class="top_valign nobsp"><img src="/plugins/projects/files/images/folder.gif" alt="<?php echo $dir; ?>" />
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'active='.$this->case.a.$goto).'/?action=browse'.a.'subdir='.urlencode($dirpath); ?>" class="dir:<?php echo urlencode($dir); ?>" title="<?php echo JText::_('COM_PROJECTS_FILES_GO_TO_DIR') . ' ' . $dir; ?>" id="edit-c-<?php echo $c; ?>"><?php echo $dir; ?></a>
							</td>
							<td class="js"><span id="rename-c-<?php echo $c; ?>" class="rename" title="<?php echo JText::_('COM_PROJECTS_FILES_RENAME_DIR_TOOLTIP'); ?>">&nbsp;</span></td>
							<td colspan="<?php echo $publishing ? 5 : 4; ?>"></td>
						</tr>					
					<?php
					}
					elseif ($combined['type'] == 'document')
					{	
						$file = $combined['item'];
						
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
							<tr class="mini faded mline">
								<td><input type="checkbox" value="<?php echo urlencode($file['name']); ?>" name="asset[]" class="checkasset js <?php if($publishing && $file['pid']) { echo 'publ'; } ?>" /></td>
								<td class="top_valign nobsp">
									<img src="<?php echo ProjectsHtml::getFileIcon($file['ext']); ?>" alt="<?php echo $file['ext']; ?>" />
									<a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'active=files' . a . $goto) 
									. '/?action=download' . a . 'case='.$this->case . a . 'subdir='.urlencode($this->subdir) 
									. a . 'file='.urlencode($file['name']); ?>" 
									class="preview file:<?php echo urlencode($file['name']); ?>" id="edit-c-<?php echo $c; ?>">
									<?php echo ProjectsHtml::shortenFileName($file['name'], 20); ?></a>
								</td>
								<td class="js"><span id="rename-c-<?php echo $c; ?>" class="rename" title="<?php echo JText::_('COM_PROJECTS_FILES_RENAME_FILE_TOOLTIP'); ?>">&nbsp;</span></td>
								<td><?php echo $file['size']; ?></td>
								<td><?php echo $file['date']; ?></td>
								<td><?php echo $file['author']; ?></td>
								<td class="centeralign"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'active=files' . a . $goto) 
								. '/?action=history' . a . 'case='.$this->case . a . 'subdir='.urlencode($this->subdir) 
								. a . 'asset[]='.urlencode($file['name']); ?>" 
								class="showinbox" title="<?php echo JText::_('COM_PROJECTS_HISTORY_TOOLTIP'); ?>"><?php echo $file['revisions']; ?></a></td>
								<?php if($publishing) { ?>
								<td><?php if($file['pid'] && $file['pub_title']) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'active=publications' . a . $goto . a . 'pid='.$file['pid']).'?section=content'; ?>" title="<?php echo $file['pub_title'] . ' (v.' . $file['pub_version_label'] . ')' ; ?>"><?php echo Hubzero_View_Helper_Html::shortenText($file['pub_title'], 30, 0); ?></a><?php } ?></td>
								<?php } ?>
							</tr>
					<?php }	
			 		$c++;
				}
			}
						
			if(count($this->combined) == 0 || $empty) { ?>
				<tr>
					<td colspan="<?php echo $publishing ? 8 : 7; ?>" class="mini faded">
						<?php if($this->subdir || $this->app) 
							{ 
								echo JText::_('COM_PROJECTS_THIS_DIRECTORY_IS_EMPTY'); 
								if (!$this->app)
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