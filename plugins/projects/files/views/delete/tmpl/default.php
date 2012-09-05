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
$f = 1;
$i = 1;
?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_DELETE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) { 
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) { 
?>
<form id="hubForm-ajax" method="post" class="" action="<?php echo JRoute::_('index.php?option='.$this->option.a.'id='.$this->project->id); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="removeit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="case" value="<?php echo $this->case; ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<!--<p class="anote"><?php echo JText::_('COM_PROJECTS_DELETE_FILES_NOTE'); ?></p> -->
		<p><?php echo JText::_('COM_PROJECTS_DELETE_FILES_CONFIRM'); ?></p>
		<?php if(count($this->folders) > 0 && $this->folders[0] != '') { ?>
			<p class="warning"><?php echo JText::_('COM_PROJECTS_DELETE_FILES_CONFIRM_WARNING'); ?></p>
		<?php } ?>
		<ul>
		<?php if(count($this->folders) > 0 && $this->folders[0] != '') { foreach ($this->folders as $folder) {  ?>
		<li>	<img src="/plugins/projects/files/images/folder.gif" alt="<?php echo urldecode($folder); ?>" />
		<?php	
			echo urldecode($folder); 
			$f++; 
			echo '<input type="hidden" name="folder[]" value="'.$folder.'" /></li>';
		} } ?></ul>
		<ul>
	<?php if(count($this->checked) > 0 && $this->checked[0] != '') { foreach ($this->checked as $checked) { 
		$path = $this->subdir ? $this->path.DS.$this->subdir : $this->path;
		$ext = ProjectsHtml::getFileAttribs( $checked, $path, 'ext' );
		 ?>
		<li>	<img src="<?php echo ProjectsHtml::getFileIcon($ext); ?>" alt="<?php echo urldecode($checked); ?>" />
		<?php
			echo urldecode($checked); 
			$f++; 
			echo '<input type="hidden" name="asset[]" value="'.$checked.'" /></li>';
		} } ?></ul>
		<p class="submitarea">
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_DELETE'); ?>" id="submit-ajaxform" />
			<input type="reset" id="cancel-action" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
		</p>		
	</fieldset>
</form>
<?php } ?>
</div>