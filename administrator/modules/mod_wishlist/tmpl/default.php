<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();

//JHTML::_('behavior.chart', 'resize');
JHTML::_('behavior.chart', 'pie');

$total = $this->granted + $this->accepted + $this->pending + $this->removed + $this->withdrawn + $this->removed + $this->rejected;
?>
<div class="<?php echo $this->module->module; ?>">
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
					{label: '<?php echo strtolower(JText::_('MOD_WISHLIST_PENDING')); ?>', data: <?php echo round(($this->pending / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_pending", "#656565"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_WISHLIST_GRANTED')); ?>', data: <?php echo round(($this->granted / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_granted", "#999"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_WISHLIST_ACCEPTED')); ?>', data: <?php echo round(($this->accepted / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_accepted", "#f9d180"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_WISHLIST_REMOVED')); ?>', data: <?php echo round(($this->removed / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_removed", "#cccccc"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_WISHLIST_WITHDRAWN')); ?>', data: <?php echo round(($this->withdrawn / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_withdrawn", "#ffffff"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_WISHLIST_REJECTED')); ?>', data: <?php echo round(($this->rejected / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_rejected", "#333333"); ?>'}
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
						<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->wishlist; ?>&amp;filterby=pending" title="<?php echo JText::_('MOD_WISHLIST_PENDING_TITLE'); ?>"><?php echo $this->escape($this->pending); ?></a>
						<span><?php echo JText::_('MOD_WISHLIST_PENDING'); ?></span>
					</td>
					<td class="accepted-items">
						<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->wishlist; ?>&amp;filterby=accepted" title="<?php echo JText::_('MOD_WISHLIST_ACCEPTED_TITLE'); ?>"><?php echo $this->escape($this->accepted); ?></a>
						<span><?php echo JText::_('MOD_WISHLIST_ACCEPTED'); ?></span>
					</td>
					<td class="granted-items">
						<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->wishlist; ?>&amp;filterby=granted" title="<?php echo JText::_('MOD_WISHLIST_GRANTED_TITLE'); ?>"><?php echo $this->escape($this->granted); ?></a>
						<span><?php echo JText::_('MOD_WISHLIST_GRANTED'); ?></span>
					</td>
				</tr>
				<tr>
					<td class="rejected-items">
						<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->wishlist; ?>&amp;filterby=rejected" title="<?php echo JText::_('MOD_WISHLIST_REJECTED_TITLE'); ?>"><?php echo $this->escape($this->rejected); ?></a>
						<span><?php echo JText::_('MOD_WISHLIST_REJECTED'); ?></span>
					</td>
					<td class="withdrawn-items">
						<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->wishlist; ?>&amp;filterby=withdrawn" title="<?php echo JText::_('MOD_WISHLIST_WITHDRAWN_TITLE'); ?>"><?php echo $this->escape($this->withdrawn); ?></a>
						<span><?php echo JText::_('MOD_WISHLIST_WITHDRAWN'); ?></span>
					</td>
					<td class="removed-items">
						<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->wishlist; ?>&amp;filterby=deleted" title="<?php echo JText::_('MOD_WISHLIST_REMOVED_TITLE'); ?>"><?php echo $this->escape($this->removed); ?></a>
						<span><?php echo JText::_('MOD_WISHLIST_REMOVED'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
