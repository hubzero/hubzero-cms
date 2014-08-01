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

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . JText::_('COM_RESOURCES_PATH_CHECKER'), 'resources.png');

$total   = number_format(count($this->good+$this->warning+$this->missing));
$missing = number_format(count($this->missing));
?>

<form action="" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<tbody>
			<tr>
				<td>
					<h3><?php echo JText::_('COM_RESOURCES_PATH_CHECKER_RESULTS'); ?></h3>
					<p><?php echo JText::sprintf('COM_RESOURCES_PATH_CHECKER_RESULTS_SUMMARY', $total, $missing); ?> </p>
					<?php if (count($this->missing) > 0) : ?>
						<hr / >
						<?php echo implode($this->missing, '<br />'); ?>
					<?php endif; ?>

					<?php if (count($this->warning) > 0) : ?>
						<br /><br /><hr />
						<?php echo implode($this->warning, '<br />'); ?>
					<?php endif; ?>

					<?php if (count($this->good) > 0) : ?>
						<br /><br /><hr />
						<?php echo implode($this->good, '<br />'); ?>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</form>