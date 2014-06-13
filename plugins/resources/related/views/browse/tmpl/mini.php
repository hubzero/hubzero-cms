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
?>
<div class="container" id="whatsrelated">
	<h3><?php echo JText::_('PLG_RESOURCES_RELATED_HEADER'); ?></h3>

	<?php if ($this->related) { ?>
		<ul>
		<?php foreach ($this->related as $line) { ?>
			<?php
			if ($line->section != 'Topic')
			{
				// Get the SEF for the resource
				if ($line->alias)
				{
					$sef = JRoute::_('index.php?option=' . $this->option . '&alias=' . $line->alias);
				}
				else
				{
					$sef = JRoute::_('index.php?option=' . $this->option . '&id=' . $line->id);
				}
				$class = 'series';
			}
			else
			{
				if ($line->group_cn != '' && $line->scope != '')
				{
					$sef = JRoute::_('index.php?option=com_groups&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
				else
				{
					$sef = JRoute::_('index.php?option=com_wiki&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
				$class = 'wiki';
			}
			?>
			<li class="<?php echo $class; ?>">
				<a href="<?php echo $sef; ?>">
					<?php echo ($line->section == 'Series') ? '<span>' . JText::_('PLG_RESOURCES_RELATED_PART_OF') . '</span> ' : ''; ?>
					<?php echo $this->escape(stripslashes($line->title)); ?>
				</a>
			</li>
		<?php } ?>
		</ul>
	<?php } else { ?>
		<p><?php echo JText::_('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND'); ?></p>
	<?php } ?>
</div>
