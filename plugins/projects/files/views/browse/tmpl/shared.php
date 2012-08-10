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

$class = 'files';
$publishing = $this->publishing ? 1 : 0;

// Use alias or id in urls?
$use_alias = $this->config->get('use_alias', 0);
$goto  = $use_alias ? 'alias='.$this->project->alias : 'id='.$this->project->id;

$subdirlink = $this->subdir ? a . 'subdir=' . urlencode($this->subdir) : '';

?>
<div id="preview-window"></div>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=files'); ?>" method="post" enctype="multipart/form-data" id="plg-form" class="file-browser submit-ajax" >	
	<div id="plg-header">
		<h3 class="<?php echo $class; ?>"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=files'); ?>"><?php echo $this->title; ?></a>
		&raquo; <span class="indlist shared"> <?php echo JText::_('COM_PROJECTS_FILES_SHARED'); ?></span></h3>
	</div>
</form>
<?php 
// Display error or success message
if ($this->getError()) { 
	echo ('<p class="witherror">' . $this->getError().'</p>');
}
else if($this->msg) {
	echo ('<p>' . $this->msg . '</p>');
} ?>

<?php if(count($this->files) > 0) { ?>
<table id="filelist" class="listing">
	<thead>
		<tr>
			<th class="checkbox"><input type="checkbox" name="toggle" value="" id="toggle" class="js" /></th>
			<th class="asset_doc"><?php echo JText::_('COM_PROJECTS_NAME'); ?></th>
			<th></th>
			<th><?php echo JText::_('COM_PROJECTS_SIZE'); ?></th>
			<th><?php echo ucfirst(JText::_('COM_PROJECTS_MODIFIED')); ?></th>
			<th><?php echo ucfirst(JText::_('COM_PROJECTS_BY')); ?></th>
			<th class="centeralign"><?php echo JText::_('COM_PROJECTS_REVISIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>
<?php } ?>