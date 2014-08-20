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

<?php
	$this->view('_submenu')
	     ->display();
?>

<div id="item-form">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<div class="col width-70 fltlft">
		<form action="index.php" method="post" name="adminForm">
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('COM_MEMBERS_QUOTA_IMPORT_LEGEND'); ?></span></legend>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="processImport" />

				<div class="input-wrap">
					<label for="conf_text"><?php echo JText::_('COM_MEMBERS_QUOTA_CONF_TEXT'); ?>:</label>
					<p class="info conf-text-note"><?php echo JText::_('COM_MEMBERS_QUOTA_CONF_TEXT_NOTE'); ?></p>
					<textarea name="conf_text" id="conf_text" cols="30" rows="10"></textarea>
				</div>
				<div class="input-wrap">
					<label for="overwrite_existing"><?php echo JText::_('COM_MEMBERS_QUOTA_OVERWRITE_EXISTING'); ?></label>
					<input type="checkbox" name="overwrite_existing" id="overwrite_existing" value="1" />
				</div>
				<p class="submit-button">
					<input class="btn btn-primary" type="submit" value="<?php echo JText::_('COM_MEMBERS_QUOTA_IMPORT_SUBMIT'); ?>" />
				</p>
			</fieldset>
			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
	<div class="col width-30 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_IMPORT_MISSING_USERS'); ?>:</th>
					<td>
						<form action="index.php" method="post">
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="task" value="importMissing" />
							<input type="submit" value="<?php echo JText::_('COM_MEMBERS_QUOTA_IMPORT_SUBMIT'); ?>" />
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p>
							<?php echo JText::_('COM_MEMBERS_QUOTA_MISSING_USERS_IMPORT_DESCRIPTION'); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>
</div>