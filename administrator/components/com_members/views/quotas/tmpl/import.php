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
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('COM_MEMBERS_QUOTAS_IMPORT'), 'user.png');
?>

<style>
	.submit-button {
		text-align: center;
	}
</style>

<div role="navigation" class="sub-navigation">
	<ul id="subsubmenu">
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>">Members</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=displayClasses">Quota Classes</a></li>
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=import" class="active">Import</a></li>
	</ul>
</div>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-100 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_MEMBERS_QUOTA_IMPORT_LEGEND'); ?></span></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="processImport" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" width="300px">
							<label for="conf_text"><?php echo JText::_('COM_MEMBERS_QUOTA_CONF_TEXT'); ?>:</label>
							<p class="info conf-text-note"><?php echo JText::_('COM_MEMBERS_QUOTA_CONF_TEXT_NOTE'); ?></p>
						</td>
						<td>
							<textarea name="conf_text" id="conf_text" cols="30" rows="10"></textarea>
						</td>
					</tr>
					<tr>
						<td class="key" width="300px">
							<label for="overwrite_existing"><?php echo JText::_('COM_MEMBERS_QUOTA_OVERWRITE_EXISTING'); ?></label>
						</td>
						<td>
							<input type="checkbox" name="overwrite_existing" value="1" />
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit-button">
				<input class="btn btn-primary" type="submit" value="<?php echo JText::_('COM_MEMBERS_QUOTA_IMPORT_SUBMIT'); ?>" />
			</p>
		</fieldset>
	</div>
	<?php echo JHTML::_('form.token'); ?>
</form>