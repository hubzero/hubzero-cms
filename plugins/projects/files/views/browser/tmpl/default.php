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

// Build url
$route = $this->project->provisioned
	? 'index.php?option=com_publications' . a . 'task=submit'
	: 'index.php?option=com_projects' . a . 'alias=' . $this->project->alias;
$p_url = JRoute::_($route . a . 'active=files');

// Directory path breadcrumbs
$desect_path = explode(DS, $this->subdir);
$path_bc = '';
$url = '';
$parent = '';
if (count($desect_path) > 0) {
	for ($p = 0; $p < count($desect_path); $p++) {
		$parent .= count($desect_path) > 1 && $p != count($desect_path)  ? $url  : '';
		$url .= DS.$desect_path[$p];
		$path_bc .= ' &raquo; <span><a href="'.$p_url.'/?subdir='.urlencode($url).'" class="folder">'.$desect_path[$p].'</a></span> ';
	}
}

$shown = array();
$skipped = 0;

?>
<form action="<?php echo JRoute::_($route).'?active=publications'; ?>" method="post" enctype="multipart/form-data" id="upload-form" >
	<ul id="c-browser" 	<?php if (count($this->files) == 0 && isset($this->attachments) && count($this->attachments) == 0) { echo 'class="hidden"'; } ?>>
		<?php
		if ($this->subdir) { ?>
			<li><a href="<?php echo $p_url.'/?action=browser'.a.'subdir='.$parent; ?>" class="uptoparent gotodir"><?php echo JText::_('COM_PROJECTS_FILES_BACK_TO_PARENT_DIR'); ?></a></li>
		<?php }
		if (count($this->files) > 0) {
			$i = 0;
			foreach ($this->files as $file) {
				if ($this->images)
				{
					// Skip non-image/video files
					if (!in_array(strtolower($file['ext']), $this->image_ext) && !in_array(strtolower($file['ext']), $this->video_ext)) {
						continue;
					}
				}
				// Skip files attached in another role
				if (in_array($file['fpath'], $this->exclude)) {
					continue;
				}

				// Ignore hidden files
				if (substr(basename($file['fpath']), 0, 1) == '.')
				{
					continue;
				}
				$shown[] = $file['fpath'];

				 ?>
			<li class="c-click" id="file::<?php echo urlencode($file['fpath']); ?>"><img src="<?php echo ProjectsHtml::getFileIcon($file['ext']); ?>" alt="<?php echo $file['ext']; ?>" /><?php echo ProjectsHtml::shortenFileName($file['fpath'], 50); ?></li>
		<?php
			$i++;
		?>
		<?php }
		}

	$missing = array();

	// Check for missing items
	// Primary content / Supporting docs
	if (isset($this->attachments)) {
		if (count($this->attachments) > 0) {
			foreach ($this->attachments as $attachment) {
				if (!in_array($attachment->path, $shown)) {
					// Found missing
					$miss = array();
					$miss['fpath'] = $attachment->path;
					$miss['ext'] = ProjectsHtml::getFileAttribs( $attachment->path, '', 'ext' );
					$missing[] = $miss;
				}
			}
		}
	}

	// Screenshots
	if ($this->images) {
		if (count($this->shots) > 0) {
			foreach ($this->shots as $shot) {
				if (!in_array($shot->filename, $shown)) {
					// Found missing
					$miss = array();
					$miss['fpath'] = $shot->filename;
					$miss['ext'] = ProjectsHtml::getFileAttribs( $shot->filename, '', 'ext' );
					$missing[] = $miss;
				}
			}
		}
	}

	// Add missing items
	if (count($missing) > 0) {
		foreach ($missing as $miss) { ?>
			<li class="c-click i-missing" id="file::<?php echo urlencode($miss['fpath']); ?>"><img src="<?php echo ProjectsHtml::getFileIcon($miss['ext']); ?>" alt="<?php echo $miss['ext']; ?>" /><?php echo ProjectsHtml::shortenFileName($miss['fpath'], 50); ?><span class="c-missing"><?php echo JText::_('PLG_PROJECTS_FILES_MISSING_FILE'); ?></span></li>
	<?php	}
	}
	 ?>
	</ul>
	<?php // if ($this->project->provisioned) { ?>
		<label class="addnew">
			<input name="upload[]" type="file" size="20" class="option" id="uploader" />
		</label>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pid; ?>" />
			<input type="hidden" name="images" id="images" value="<?php echo $this->images; ?>" />
			<input type="hidden" name="active" value="files" />
			<input type="hidden" name="action" value="<?php echo $this->project->provisioned == 1 && !$this->pid  ? 'saveprov' : 'save'; ?>" />
			<input type="hidden" name="view" value="pub" />
			<input type="hidden" name="ajax" value="0" />
			<input type="hidden" name="no_html" value="0" />
			<input type="hidden" name="return_status" id="return_status" value="0" />
			<input type="hidden" name="expand_zip" id="expand_zip" value="0" />
			<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
			<?php if ($this->project->provisioned == 1 ) { ?>
			<input type="hidden" name="task" value="submit" />
			<?php } ?>
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_UPLOAD'); ?>" class="btn yesbtn" id="b-upload" />
			<p id="statusmsg"></p>
			<p class="hint"><?php echo JText::_('COM_PROJECTS_UPLOAD_HINT'); ?></p>
	<?php //} ?>
</form>

<?php if ((count($shown) + count($missing)) == 0) { ?>
	<p class="noresults"><?php echo $this->images ? JText::_('PLG_PROJECTS_PUBLICATIONS_NO_SELECTION_ITEMS_FOUND_IMAGES') : JText::_('PLG_PROJECTS_PUBLICATIONS_NO_SELECTION_ITEMS_FOUND_FILES'); ?></p>
<?php } ?>

<?php /* if (!$this->project->provisioned) { ?>
	<p class="addnew">Go to <a href="<?php echo JRoute::_($route).'?active=files'; ?>">Files</a> browser to upload <?php if (count($shown) > 0) { echo 'more'; } ?> files</p>
<?php } */ ?>