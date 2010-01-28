<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<h3><a name="points"></a><?php echo JText::_('PLG_MEMBERS_POINTS'); ?></h3>
<div class="aside">
	<p id="point-balance">
		<span><?php echo JText::_('PLG_MEMBERS_POINTS_YOU_HAVE'); ?> </span> <?php echo $this->sum; ?><small> <?php echo strtolower(JText::_('PLG_MEMBERS_POINTS')); ?></small><br />
		<small style="font-size:70%; font-weight:normal">( <?php echo $this->funds; ?> <?php echo strtolower(JText::_('PLG_MEMBERS_POINTS_AVAILABLE')); ?> )</small>
	</p>
	
	<p class="help">
		<strong><?php echo JText::_('PLG_MEMBERS_POINTS_HOW_ARE_POINTS_AWARDED'); ?></strong><br />
		<?php echo JText::_('PLG_MEMBERS_POINTS_AWARDED_EXPLANATION'); ?>
	</p>
</div><!-- / .aside -->
<div class="subject">
	<table class="transactions" summary="<?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_SUMMARY'); ?>">
		<caption><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_CAPTION'); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_DATE'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_DESCRIPTION'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_TYPE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_AMOUNT'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_BALANCE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php 
	if ($this->hist) {
		$cls = 'even';
		foreach ($this->hist as $item)
		{
			$cls = (($cls == 'even') ? 'odd' : 'even');
?>
			<tr class="<?php echo $cls; ?>">
				<td><?php echo JHTML::_('date',$item->created, '%d %b, %Y'); ?></td>
				<td><?php echo $item->description; ?></td>
				<td><?php echo $item->type; ?></td>
<?php if ($item->type == 'withdraw') { ?>
				<td class="numerical-data"><span class="withdraw">-<?php echo $item->amount; ?></span></td>
<?php } elseif ($item->type == 'hold') { ?>
				<td class="numerical-data"><span class="hold">(<?php echo $item->amount; ?>)</span></td>
<?php } else { ?>
				<td class="numerical-data"><span class="deposit">+<?php echo $item->amount; ?></span></td>
<?php } ?>
				<td class="numerical-data"><?php echo $item->balance; ?></td>
			</tr>
<?php
		}
	} else {
?>
			<tr class="odd">
				<td colspan="5"><?php echo JText::_('PLG_MEMBERS_POINTS_NO_TRANSACTIONS'); ?></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
</div><!-- / .subject -->