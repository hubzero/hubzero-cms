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

if ($modmypoints->error) {
	echo '<p class="error">'.JText::_('MOD_MYPOINTS_MISSING_TABLE').'</p>'."\n";
} else {
	$juser =& JFactory::getUser();

	$history = $modmypoints->history;
?>
<div<?php echo ($modmypoints->moduleclass) ? ' class="'.$modmypoints->moduleclass.'"' : ''; ?>>
	<p id="point-balance">
		<span><?php echo JText::_('MOD_MYPOINTS_YOU_HAVE'); ?> </span> <?php echo $modmypoints->summary; ?><small> <?php echo strtolower(JText::_('MOD_MYPOINTS_POINTS')); ?></small>
	</p>
<?php if (count($history) > 0) { ?>
	<table class="transactions" summary="<?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_SUMMARY'); ?>">
		<caption><?php echo JText::sprintf('MOD_MYPOINTS_TRANSACTIONS_TBL_CAPTION', $modmypoints->limit); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_DATE'); ?></th>
				<!-- <th scope="col"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_DESCRIPTION'); ?></th> -->
				<th scope="col"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_TYPE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_AMOUNT'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_BALANCE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
	$cls = 'even';
	foreach ($history as $item)
	{
		$cls = (($cls == 'even') ? 'odd' : 'even');
		$html  = "\t\t".'<tr class="'.$cls.'">'."\n";
		$html .= "\t\t\t".'<td>'.JHTML::_('date',$item->created, '%d %b, %Y').'</td>'."\n";
		//$html .= "\t\t\t".'<td>'.$item->description.'</td>'."\n";
		$html .= "\t\t\t".'<td>'.$item->type.'</td>'."\n";
		if ($item->type == 'withdraw') {
			$html .= "\t\t\t".'<td class="numerical-data"><span class="withdraw">-'.$item->amount.'</span></td>'."\n";
		} elseif ($item->type == 'hold') {
			$html .= "\t\t\t".'<td class="numerical-data"><span class="hold">('.$item->amount.')</span></td>'."\n";
		} else {
			$html .= "\t\t\t".'<td class="numerical-data"><span class="deposit">+'.$item->amount.'</span></td>'."\n";
		}
		$html .= "\t\t\t".'<td class="numerical-data">'.$item->balance.'</td>'."\n";
		$html .= "\t\t".'</tr>'."\n";
		echo $html;
	}
?>
		</tbody>
	</table>
<?php } ?>
	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='. $juser->get('id') .'&active=points'); ?>"><?php echo JText::_('MOD_MYPOINTS_ALL_TRANSACTIONS'); ?></a></li>
	</ul>
</div>
<?php } ?>