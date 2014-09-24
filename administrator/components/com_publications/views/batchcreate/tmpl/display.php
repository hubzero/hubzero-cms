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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATIONS') . ': [' . JText::_('COM_PUBLICATIONS_BATCH_CREATE') . ']', 'addedit.png');

$this->css('batchcreate');
$this->js('batchcreate');

?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=process" method="post" name="adminForm" id="adminForm" class="batchupload" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_PUBLICATIONS_BATCH_IMPORT'); ?></span></legend>
		<div class="input-wrap">
			<div class="col width-70 fltlft">
				<div class="input-wrap">
					<label for="projectid"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ADD_IN_PROJECT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
					<?php echo $this->lists['projects']; ?>
				</div>
				<div class="input-wrap file-import" data-hint="<?php echo JText::_('COM_PUBLICATIONS_FIELD_ATTACH_HINT'); ?>">
					<label for="field-file">
						<?php echo JText::_('COM_PUBLICATIONS_FIELD_DATA'); ?><span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span>
					</label>
					<input type="file" name="file" id="field-file" />
				</div>
				<div class="input-wrap">
					<input type="submit" name="batch_submit" id="batch_submit" value="<?php echo JText::_('COM_PUBLICATIONS_UPLOAD_AND_PREPROCESS'); ?>" />
				</div>
			</div>
			<div class="input-wrap col width-30 fltrt">
				<p><?php echo JText::_('COM_PUBLICATIONS_BATCH_XSD_INSTRUCT'); ?> <a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=xsd"><?php echo JText::_('COM_PUBLICATIONS_BATCH_XSD'); ?></a></p>
			</div>
		</div>
		<div class="clr"></div>
		<div class="output-wrap" id="results">
		</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="process" />
	<input type="hidden" name="base" value="files" />
	<?php echo JHTML::_('form.token'); ?>
	</fieldset>
</form>