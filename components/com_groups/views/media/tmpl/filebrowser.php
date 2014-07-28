<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JHTML::_('behavior.modal');

// push scripts and styles
$this->css()
     ->css('media.css')
     ->js()
     ->js('groups.mediabrowser')
     ->js('jquery.fileuploader', 'system')
     ->js('jquery.contextMenu', 'system')
     ->css('jquery.contextMenu.css', 'system');

//get request vars
$type          = JRequest::getWord('type', '', 'get');
$ckeditor      = JRequest::getVar('CKEditor', '', 'get');
$ckeditorFunc  = JRequest::getInt('CKEditorFuncNum', 0, 'get');
$ckeditorQuery = '&type='.$type.'&CKEditor=' . $ckeditor . '&CKEditorFuncNum=' . $ckeditorFunc;
?>

<div class="upload-browser cf">
	<?php
		foreach ($this->notifications as $notification)
		{
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

	<div class="upload-browser-col left">
		<div class="toolbar cf">
			<div class="title"><?php echo JText::_('COM_GROUPS_MEDIA_GROUP_FILES'); ?></div>
			<div class="buttons">
				<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=addfolder&tmpl=component'); ?>" class="icon-add action-addfolder"></a>
			</div>
		</div>
		<div class="foldertree" data-activefolder="<?php echo $this->activeFolder; ?>">
			<?php echo $this->folderTree; ?>
		</div>
		<div class="foldertree-list">
			<?php echo $this->folderList; ?>
		</div>
		<form action="index.php" method="post" enctype="multipart/form-data" class="upload-browser-uploader">
			<fieldset>
				<div id="ajax-uploader" data-action="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=ajaxupload&no_html=1'); ?>">
					<noscript>
						<p><input type="file" name="upload" id="upload" /></p>
						<p><input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" /></p>
					</noscript>
				</div>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="media" />
				<input type="hidden" name="task" value="upload" />
				<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->group->get('gidNumber'); ?>" />
				<input type="hidden" name="tmpl" value="component" />
			</fieldset>
		</form>
	</div>
	<div class="upload-browser-col right">
		<iframe class="upload-browser-filelist-iframe" src="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=listfiles&tmpl=component&type=' . $ckeditorQuery); ?>"></iframe>
	</div>
</div>
