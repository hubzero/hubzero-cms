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

JHTML::_('behavior.chart', 'resize');
JHTML::_('behavior.chart', 'pie');

$total = $this->granted + $this->accepted + $this->pending + $this->removed + $this->withdrawn + $this->removed + $this->rejected;
?>
<div class="mod_wishlist">
	<div class="overview-container">
		<div id="wishlist-container<?php echo $this->module->id; ?>" style="min-width: 200px; height: 200px;"></div>

		<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				wishlistPie;

			$(document).ready(function() {
				wishlistPie = $.plot($("#wishlist-container<?php echo $this->module->id; ?>"), [
					{label: 'pending', data: <?php echo round(($this->pending / $total)*100, 2); ?>, color: '#656565'},
					{label: 'granted', data: <?php echo round(($this->granted / $total)*100, 2); ?>, color: '#999'}, //#7c94c2
					{label: 'accepted', data: <?php echo round(($this->accepted / $total)*100, 2); ?>, color: '#f9d180'}, //#c67c6b
					{label: 'removed', data: <?php echo round(($this->removed / $total)*100, 2); ?>, color: '#ccc'}, //#d8aa65
					{label: 'withdrawn', data: <?php echo round(($this->withdrawn / $total)*100, 2); ?>, color: '#eee'}, //#5f9c63
					{label: 'rejected', data: <?php echo round(($this->rejected / $total)*100, 2); ?>, color: '#333'} //#5f9c63
				], {
					legend: { 
						show: true
					},
					series: {
						pie: { 
							innerRadius: 0.5,
							show: true,
							stroke: {
								color: '#efefef'
							}
						}
					},
					grid: {
						hoverable: false
					}
				});
			});
		}
		</script>

		<p class="wishlist-total"><?php echo $total; ?></p>
	</div>
	<div class="overview-container">
		<table class="wishlist-stats-overview">
			<tbody>
				<tr>
					<td class="pending-items">
						<a href="index.php?option=com_wishlist&amp;status=0&amp;accepted=0" title="<?php echo JText::_('View pending wishes'); ?>"><?php echo $this->escape($this->pending); ?></a>
						<span><?php echo JText::_('Pending'); ?></span>
					</td>
					<td class="accepted-items">
						<a href="index.php?option=com_wishlist&amp;status=0&amp;accepted=1" title="<?php echo JText::_('View accepted wishes'); ?>"><?php echo $this->escape($this->accepted); ?></a>
						<span><?php echo JText::_('Accepted'); ?></span>
					</td>
					<td class="granted-items">
						<a href="index.php?option=com_wishlist&amp;status=1" title="<?php echo JText::_('View granted wishes'); ?>"><?php echo $this->escape($this->granted); ?></a>
						<span><?php echo JText::_('Granted'); ?></span>
					</td>
				</tr>
				<tr>
					<td class="rejected-items">
						<a href="index.php?option=com_wishlist&amp;status=3" title="<?php echo JText::_('View rejected wishes'); ?>"><?php echo $this->escape($this->rejected); ?></a>
						<span><?php echo JText::_('Rejected'); ?></span>
					</td>
					<td class="withdrawn-items">
						<a href="index.php?option=com_wishlist&amp;status=4" title="<?php echo JText::_('View withdrawn wishes'); ?>"><?php echo $this->escape($this->withdrawn); ?></a>
						<span><?php echo JText::_('Withdrawn'); ?></span>
					</td>
					<td class="removed-items">
						<a href="index.php?option=com_wishlist&amp;status=2" title="<?php echo JText::_('View removed wishes'); ?>"><?php echo $this->escape($this->removed); ?></a>
						<span><?php echo JText::_('Removed'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
