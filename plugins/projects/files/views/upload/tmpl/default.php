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

?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_FILES_UPLOAD_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) { 
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) { 
?>
<form id="hubForm-ajax" method="post" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option='.$this->option.a.'id='.$this->project->id); ?>">
	<fieldset class="uploader">
		<p><?php echo JText::_('COM_PROJECTS_FILES_PICK_FILES_UPLOAD') . ' '; 
			echo $this->subdir ? JText::_('COM_PROJECTS_FILES_PICK_FILES_UPLOAD_SUBDIR') . ' <span class="prominent">' . $this->subdir . '</span>:' : ' <span class="prominent">' . JText::_('COM_PROJECTS_FILES_PICK_FILES_UPLOAD_HOME') . '</span>:'; ?></p>
		<label class="addnew">
			<input name="upload[]" type="file" class="option uploader" id="uploader" multiple="multiple" /> 
			<p class="hint ipadded"><?php echo JText::_('COM_PROJECTS_FILES_MAX_UPLOAD').' '.$this->sizelimit; ?></p>
		</label>
		<div id="drop-area"></div>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->config->get('maxUpload', '104857600'); ?>" />	
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="case" value="<?php echo $this->case; ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<div class="sharing-option-extra">
			<label class="sharing-option">
				<input type="checkbox" name="expand_zip" id="expand_zip" value="1" />
				<?php echo JText::_('COM_PROJECTS_FILES_UPLOAD_UNZIP_ARCHIVES'); ?>
			</label>
		</div>
		<p class="submitarea">
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_UPLOAD'); ?>" class="btn yesbtn" id="f-upload"  />
			<input type="reset" id="cancel-action" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
		</p>		
	</fieldset>
</form>
<?php } ?>
</div>