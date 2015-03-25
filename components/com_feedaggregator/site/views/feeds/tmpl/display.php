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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="icon-browse btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=posts'); ?>"><?php echo JText::_('COM_FEEDAGGREGATOR_VIEW_POSTS'); ?></a>
			</li>
			<li class="last">
				<a class="icon-add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo JText::_('COM_FEEDAGGREGATOR_ADD_FEED'); ?></a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<form class="contentForm">
		<div id="page-main">
		<?php if (count($this->feeds) > 0): ?>
			<table class="entries">
				<thead class="table-head">
					<tr>
						<th scope="col"><?php echo JText::_('COM_FEEDAGGREGATOR_COL_NAME'); ?></th>
						<th scope="col"><?php echo JText::_('COM_FEEDAGGREGATOR_COL_URL'); ?></th>
						<th scope="col"><?php echo JText::_('COM_FEEDAGGREGATOR_COL_ACTIONS'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->feeds as $feed): ?>
					<tr class='shade-table'>
						<td><?php echo $feed->name; ?></td>
						<td><a href="<?php echo $feed->url; ?>"><?php echo $feed->url; ?></a></td>
						<td>
							<a class="btn" href="index.php?option=com_feedaggregator&amp;controller=posts&amp;task=PostsById&amp;id=<?php echo $feed->id; ?>"><?php echo JText::_('COM_FEEDAGGREGATOR_VIEW_POST'); ?>s</a>
							<a class="btn" href="index.php?option=com_feedaggregator&amp;controller=feeds&amp;task=edit&amp;id=<?php echo $feed->id;?>"><?php echo JText::_('COM_FEEDAGGREGATOR_EDIT'); ?></a>
							<?php if ($feed->enabled == '1'):?>
								<a class="btn disableBtn" href="index.php?option=com_feedaggregator&amp;controller=feeds&amp;task=status&amp;action=disable&amp;id=<?php echo $feed->id; ?>"><?php echo JText::_('COM_FEEDAGGREGATOR_DISABLE'); ?></a>
							<?php elseif ($feed->enabled == '0'): ?>
								<a class="btn enableBtn" href="index.php?option=com_feedaggregator&amp;controller=feeds&amp;task=status&amp;action=enable&amp;id=<?php echo $feed->id; ?>"><?php echo JText::_('COM_FEEDAGGREGATOR_ENABLE'); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<p>
				<?php echo JText::_('COM_FEEDAGGREGATOR_NO_RESULTS'); ?><br />
				<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo JText::_('COM_FEEDAGGREGATOR_ADD_FEED'); ?></a>
			</p>
		<?php endif; ?>
		</div>
	</form>
</section><!-- /.main section -->
