<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="module-list">
	<h2 class="section-header">
		<?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_TITLE'); ?>
	</h2>
	<p class="warning"><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_WARNING'); ?></p>
	<form action="index.php" method="post">
		<fieldset class="adminform">
			<div class="input-wrap">
				<label><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_TITLE'); ?> <span class="required">required</span></label><br />
				<select name="module">
					<option value="">- Select Module to Push &mdash;</option>
					<?php foreach ($this->modules as $module) : ?>
						<option value="<?php echo $module->id; ?>"><?php echo $module->title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_COLUMN'); ?></label><br />
					<select name="column">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_POSITION'); ?></label><br />
					<select name="position">
						<option value="first">First</option>
						<option value="last">Last</option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_WIDTH'); ?></label><br />
					<select name="width">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap" data-hint="<?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_HEIGHT_HINT'); ?>">
					<label><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_HEIGHT'); ?></label><br />
					<select name="height">
						<option value="1">1</option>
						<option selected="selected" value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
					</select>
					<span class="hint"><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_HEIGHT_HINT'); ?></span>
				</div>
			</div>
			<p class="submit">
				<button class="button dopush" type="submit"><?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_BUTTON'); ?></button>
			</p>

		</fieldset>
		<input type="hidden" name="option" value="com_members" />
		<input type="hidden" name="controller" value="plugins" />
		<input type="hidden" name="plugin" value="dashboard" />
		<input type="hidden" name="task" value="dopush" />
	</form>
</div>