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

JToolBarHelper::title(JText::_('CITATION') . ': ' . JText::_('CITATION_STATS'), 'citation.png');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('YEAR'); ?></th>
				<th><?php echo JText::_('AFFILIATED'); ?></th>
				<th><?php echo JText::_('NONAFFILIATED'); ?></th>
				<th><?php echo JText::_('TOTAL'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
	foreach ($this->stats as $year => $amt)
	{
?>
			<tr>
				<th><?php echo $year; ?></th>
				<td><?php echo $amt['affiliate']; ?></td>
				<td><?php echo $amt['non-affiliate']; ?></td>
				<td><span style="color:#c00;"><?php echo (intval($amt['affiliate']) + intval($amt['non-affiliate'])); ?></span></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
</form>