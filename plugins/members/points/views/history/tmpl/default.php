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
defined('_JEXEC') or die( 'Restricted access' );

$this->css();
?>

<h3 class="section-header">
	<?php echo JText::_('PLG_MEMBERS_POINTS'); ?>
</h3>

<div class="grid">
	<div class="col span-half">
		<div class="point-balance-container">
			<h4><?php echo JText::_('PLG_MEMBERS_POINTS_BALANCE'); ?></h4>
			<div class="point-balance">
				<strong><?php echo number_format($this->sum); ?> <span><?php echo strtolower(JText::_('PLG_MEMBERS_POINTS')); ?></span></strong>
				<span class="spend">( <?php echo number_format($this->funds) . ' ' . strtolower(JText::_('PLG_MEMBERS_POINTS_AVAILABLE')); ?> )</span>
			</div>
		</div>
	</div>
	<div class="col span-half omega">
		<p class="help">
			<strong><?php echo JText::_('PLG_MEMBERS_POINTS_HOW_ARE_POINTS_AWARDED'); ?></strong><br />
			<?php echo JText::_('PLG_MEMBERS_POINTS_AWARDED_EXPLANATION'); ?>
		</p>
	</div>
</div>

<div class="container">
	<table class="entries transactions">
		<caption><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_CAPTION'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_DATE'); ?></th>
				<th scope="col" class="textual-data"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_DESCRIPTION'); ?></th>
				<th scope="col" class="textual-data"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_TYPE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_AMOUNT'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_BALANCE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ($this->hist) : ?>
				<?php foreach ($this->hist as $item) : ?>
					<tr>
						<td><?php echo JHTML::_('date', $item->created, JText::_('DATE_FORMAT_HZ1')); ?></td>
						<td><?php echo $this->escape($item->description); ?></td>
						<td><?php echo $this->escape($item->type); ?></td>
					<?php if ($item->type == 'withdraw') : ?>
						<td class="numerical-data"><span class="withdraw">-<?php echo $this->escape($item->amount); ?></span></td>
					<?php elseif ($item->type == 'hold') : ?>
						<td class="numerical-data"><span class="hold">(<?php echo $this->escape($item->amount); ?>)</span></td>
					<?php else : ?>
						<td class="numerical-data"><span class="deposit">+<?php echo $this->escape($item->amount); ?></span></td>
					<?php endif; ?>
						<td class="numerical-data"><?php echo $this->escape($item->balance); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr><td colspan="5"><?php echo JText::_('PLG_MEMBERS_POINTS_NO_TRANSACTIONS'); ?></td></tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
