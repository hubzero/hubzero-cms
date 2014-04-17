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

$root = str_replace('/administrator', '', JURI::base(true));

$total = $this->draftInternal + $this->draftUser + $this->pending + $this->published + $this->unpublished + $this->removed;
$this->draft = $this->draftInternal + $this->draftUser;
?>
<div class="mod_resources">
	<div id="resources-container<?php echo $this->module->id; ?>" style="min-width: 200px; height: 200px;"></div>
	<script src="<?php echo $root; ?>/media/system/js/flot/jquery.flot.min.js"></script>
	<script src="<?php echo $root; ?>/media/system/js/flot/jquery.flot.tooltip.min.js"></script>
	<script src="<?php echo $root; ?>/media/system/js/flot/jquery.flot.resize.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo $root; ?>/media/system/js/excanvas/excanvas.min.js"></script><![endif]-->
	<script src="<?php echo $root; ?>/media/system/js/flot/jquery.flot.pie.min.js"></script>
	<script type="text/javascript">
	if (jQuery()) {
		var $ = jq,
			resolutionPie;

		$(document).ready(function() {
			resolutionPie = $.plot($("#resources-container<?php echo $this->module->id; ?>"), [
				{label: 'published', data: <?php echo round(($this->published / $total)*100, 2); ?>, color: '#656565'},
				{label: 'draft', data: <?php echo round(($this->draft / $total)*100, 2); ?>, color: '#999'}, //#7c94c2
				{label: 'pending', data: <?php echo round(($this->pending / $total)*100, 2); ?>, color: '#f9d180'}, //#c67c6b
				{label: 'removed', data: <?php echo round(($this->removed / $total)*100, 2); ?>, color: '#ccc'}, //#d8aa65
				{label: 'unpublished', data: <?php echo round(($this->unpublished / $total)*100, 2); ?>, color: '#eee'} //#5f9c63
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

	<p class="resources-total"><?php echo $total; ?></p>
<!-- 
	<table class="resources-stats-overview">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('Draft (internal)'); ?></th>
				<th scope="col"><?php echo JText::_('Draft (user)'); ?></th>
				<th scope="col"><?php echo JText::_('Pending'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="draft-internal"><a href="index.php?option=com_resources&amp;c=resources&amp;status=5" title="<?php echo JText::_('View draft (internal) resources'); ?>"><?php echo $this->escape($this->draftInternal); ?></a></td>
				<td class="draft-user"><a href="index.php?option=com_resources&amp;c=resources&amp;status=2" title="<?php echo JText::_('View draft (user) resources'); ?>"><?php echo $this->escape($this->draftUser); ?></a></td>
				<td class="pending"><a href="index.php?option=com_resources&amp;c=resources&amp;status=3" title="<?php echo JText::_('View pending resources'); ?>"><?php echo $this->escape($this->pending); ?></a></td>
			</tr>
		</tbody>
	</table>

	<table class="resources-stats-overview">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('Published'); ?></th>
				<th scope="col"><?php echo JText::_('Unpublished'); ?></th>
				<th scope="col"><?php echo JText::_('Removed'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="published"><a href="index.php?option=com_resources&amp;c=resources&amp;status=1" title="<?php echo JText::_('View published resources'); ?>"><?php echo $this->escape($this->published); ?></a></td>
				<td class="unpublished"><a href="index.php?option=com_resources&amp;c=resources&amp;status=0" title="<?php echo JText::_('View unpublished resources'); ?>"><?php echo $this->escape($this->unpublished); ?></a></td>
				<td class="removed"><a href="index.php?option=com_resources&amp;c=resources&amp;status=4" title="<?php echo JText::_('View removed resources'); ?>"><?php echo $this->escape($this->removed); ?></a></td>
			</tr>
		</tbody>
	</table>
	-->
	<table class="resources-stats-overview">
		<tbody>
			<tr>
				<td>
					<a href="index.php?option=com_resources&amp;c=resources&amp;status=1" title="<?php echo JText::_('View published resources'); ?>"><?php echo $this->escape($this->published); ?></a>
					<span><?php echo JText::_('Published'); ?></span>
				</td>
				<td class="pending-items">
					<a href="index.php?option=com_resources&amp;c=resources&amp;status=3" title="<?php echo JText::_('View pending resources'); ?>"><?php echo $this->escape($this->pending); ?></a>
					<span><?php echo JText::_('Pending'); ?></span>
				</td>
				<td>
					<a href="index.php?option=com_resources&amp;c=resources&amp;status=2" title="<?php echo JText::_('View draft resources'); ?>"><?php echo $this->escape($this->draft); ?></a>
					<span><?php echo JText::_('Draft'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>

</div>