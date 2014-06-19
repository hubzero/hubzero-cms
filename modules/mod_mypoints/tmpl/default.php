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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if ($this->error) {
	echo '<p class="error">' . JText::_('MOD_MYPOINTS_MISSING_TABLE') . '</p>' . "\n";
} else {
	$juser = JFactory::getUser();
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
	<ul class="module-nav">
		<li>
			<a class="icon-points" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=points'); ?>">
				<?php echo JText::_('MOD_MYPOINTS_ALL_TRANSACTIONS'); ?>
			</a>
		</li>
	</ul>
	<p id="point-balance">
		<span><?php echo JText::_('MOD_MYPOINTS_YOU_HAVE'); ?> </span> <?php echo $this->escape($this->summary); ?><small> <?php echo strtolower(JText::_('MOD_MYPOINTS_POINTS')); ?></small>
	</p>
<?php if (count($this->history) > 0) { ?>
	<table class="transactions">
		<caption><?php echo JText::sprintf('MOD_MYPOINTS_TRANSACTIONS_TBL_CAPTION', $this->escape($this->limit)); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_DATE'); ?></th>
				<th scope="col"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_TYPE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_AMOUNT'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_BALANCE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
	$cls = 'even';
	foreach ($this->history as $item)
	{
		$cls = (($cls == 'even') ? 'odd' : 'even');
?>
			<tr class="<?php echo $cls; ?>">
				<td>
					<time datetime="<?php echo $item->created; ?>"><?php echo JHTML::_('date', $item->created, JText::_('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td>
					<?php echo $item->type; ?>
				</td>
				<td class="numerical-data">
<?php if ($item->type == 'withdraw') { ?>
					<span class="withdraw">-<?php echo $this->escape($item->amount); ?></span>
<?php } elseif ($item->type == 'hold') { ?>
					<span class="hold">(<?php echo $this->escape($item->amount); ?>)</span>
<?php } else { ?>
					<span class="deposit">+<?php echo $this->escape($item->amount); ?></span>
<?php } ?>
				</td>
				<td class="numerical-data">
					<?php echo $this->escape($item->balance); ?>
				</td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
<?php } ?>
</div>
<?php } ?>