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

// check to make sure we have some member dashboard params
$count = 0;
foreach ($this->fields as $field)
{
	if ($field->getAttribute('member_dashboard', 0) == 1)
	{
		$count++;
	}
}

// make sure we have at least one
if ($count < 1 || $this->admin)
{
	return '';
}
?>
<div class="module-settings">
	<h4><?php echo JText::sprintf('%s Settings', $this->escape($this->module->title)); ?></h4>
	<form action="index.php" method="post">
		<?php foreach ($this->fields as $field) : ?>
			<?php
				if (strtolower($field->type) == 'spacer')
				{
					continue;
				}

				if (!$field->getAttribute('member_dashboard', 0))
				{
					continue;
				}

				// set value based on hub & user pref
				$name = trim(str_replace('params[', '', rtrim($field->name, ']')));
				if (isset($this->params[$name]))
				{
					$field->setValue($this->params[$name]);
				}
			?>
			<label>
				<span class="tooltipss" title="<?php echo $field->description; ?>">
					<?php echo $field->title; ?>:
				</span>
				<?php echo $field->input; ?>
			</label>
		<?php endforeach; ?>

		<div class="form-controls">
			<button class="btn btn-success save" type="submit"><?php echo JText::_('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS_SAVE'); ?></button>
			<button class="btn cancel" type="button"><?php echo JText::_('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS_CANCEL'); ?></button>
		</div>
	</form>
</div>