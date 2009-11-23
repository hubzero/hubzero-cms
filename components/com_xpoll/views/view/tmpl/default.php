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

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="content-header">
	<h2><?php echo JText::_('COM_XPOLL'); ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<p id="tagline"><a class="stats" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=latest'); ?>"><?php echo JText::_('COM_XPOLL_TAKE_THE_LATEST_POLL'); ?></a></p>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } else {
	
$votes = $this->votes;
if ($votes) {
	$j = 0;
	$data_arr['text'] = null;
	$data_arr['hits'] = null;
	foreach ($votes as $vote) 
	{
		$data_arr['text'][$j] = trim($vote->text);
		$data_arr['hits'][$j] = $vote->hits;
		$j++;
	}
	
	$polls_graphwidth = 200;
	$polls_barheight  = 2;
	$polls_maxcolors  = 5;
	$polls_barcolor   = 0;

	$tabcnt = 0;
	$colorx = 0;
	$maxval = 0;

	array_multisort( $data_arr['hits'], SORT_NUMERIC, SORT_DESC, $data_arr['text'] );

	foreach ($data_arr['hits'] as $hits) 
	{
		if ($maxval < $hits) {
			$maxval = $hits;
		}
	}
	$sumval = array_sum( $data_arr['hits'] );
?>
	<div class="aside">
		<p>
			<strong><?php echo JText::_('COM_XPOLL_FIRST_VOTE'); ?></strong><br />
			<?php echo ($this->first_vote) ? $this->first_vote : '--'; ?>
		</p>
		<p>
			<strong><?php echo JText::_('COM_XPOLL_LAST_VOTE'); ?></strong><br />
			<?php echo ($this->last_vote) ? $this->last_vote : '--'; ?>
		</p>
	</div><!-- / .aside -->
	<div class="subject">
		<table class="pollresults" summary="<?php echo JText::_('COM_XPOLL_TABLE_SUMMARY'); ?>">
			<thead>
				<tr>
					<th colspan="3">
						<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="poll"> 
							<fieldset> 
								<select name="id" id="pollid">
									<option value=""><?php echo JText::_('COM_XPOLL_SELECT_POLL'); ?></option>
<?php 
									foreach ($this->polls as $poll) 
									{
?>
									<option value="<?php echo $poll->id; ?>"<?php echo ($poll->id == intval( $this->poll->id ) ? ' selected="selected"' : ''); ?>><?php echo stripslashes($poll->title); ?></option>
<?php
									}
?>
								</select>
								<input type="submit" name="submit" value="<?php echo JText::_('GO'); ?>" />
								<input type="hidden" name="task" value="view" />
							</fieldset> 
						</form>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"><span><?php echo JText::_('COM_XPOLL_NUM_VOTERS'); ?>:</span> <?php echo $sumval; ?></td>
				</tr>
			</tfoot>
			<tbody>
<?php
		for ($i=0, $n=count($data_arr['text']); $i < $n; $i++) 
		{
			$text =& $data_arr['text'][$i];
			$hits =& $data_arr['hits'][$i];
			if ($maxval > 0 && $sumval > 0) {
				$width = ceil( $hits*$polls_graphwidth/$maxval );
				$percent = round( 100*$hits/$sumval, 1 );
			} else {
				$width = 0;
				$percent = 0;
			}
			$tdclass='';
			if ($polls_barcolor==0) {
				if ($colorx < $polls_maxcolors) {
					$colorx = ++$colorx;
				} else {
					$colorx = 1;
				}
				$tdclass = 'color'.$colorx;
			} else {
				$tdclass = 'color'.$polls_barcolor;
			}
?> 
				<tr>
					<td>
						<div class="graph">
							<strong class="bar <?php echo $tdclass; ?>" style="width: <?php echo $percent; ?>%;"><span><?php echo $percent; ?>%</span></strong>
						</div>
					</td>
					<td><?php echo stripslashes($text); ?></td>
					<td class="votes"><?php echo $hits; ?></td>
				</tr>
<?php
			$tabcnt = 1 - $tabcnt;
		}
?> 
			</tbody>
		</table>
	</div><!-- / .subject -->
<?php } else { ?>
	<p class="warning"><?php echo JText::_('COM_XPOLL_NO_RESULTS'); ?></p>
<?php } 
}
?>
<div class="clear"></div>
</div><!-- / .main section -->