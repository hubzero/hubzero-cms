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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
	<ul class="module-nav">
		<li><a class="icon-browse" href="<?php echo JRoute::_('index.php?option=com_publications&controller=curation'); ?>"><?php echo JText::_('MOD_MYCURATION_ALL_TASKS'); ?></a></li>
	</ul>

	<h4>
		<a href="<?php echo JRoute::_('index.php?option=com_publications&controller=curation&assigned=1'); ?>">
			<?php echo JText::_('MOD_MYCURATION_ASSIGNED'); ?>
			<span><?php echo JText::_('MOD_MYCURATION_VIEW_ALL'); ?></span>
		</a>
	</h4>
	<?php if (count($this->rows) <= 0) { ?>
		<p><em><?php echo JText::_('MOD_MYCURATION_NO_ITEMS'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
		<?php
		foreach ($this->rows as $row)
		{
			$class = $row->state == 5 ? 'status-pending' : 'status-wip';
			?>
			<li class="curation-task <?php echo $class; ?>">
				<a href="<?php echo $row->state == 5 ? JRoute::_('index.php?option=com_publications&controller=curation&id=' . $row->id) : JRoute::_('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number); ?>"><img src="<?php echo JRoute::_('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb'; ?>" alt="" />
				<?php echo $row->title . ' v.' . $row->version_label; ?></a>
				<span><?php if ($row->state == 5) { ?><a href="<?php echo JRoute::_('index.php?option=com_publications&controller=curation&id=' . $row->id); ?>"><?php echo JText::_('MOD_MYCURATION_REVIEW'); ?></a><?php } ?><?php if ($row->state == 7) { echo JText::_('MOD_MYCURATION_PENDING_CHANGES');  } ?></span>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } ?>
</div>