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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

if ($this->getError()) { ?>
	<li class="error">Error: <?php echo $this->getError(); ?></li>
<?php } ?>
<?php
	if ($this->queries)
	{
		foreach ($this->queries as $query)
		{
?>
	<li<?php if (intval($this->show) == $query->id) { echo ' class="active"'; }?>>
		<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=tickets&task=display&show=' . $query->id); ?>">
			<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
		</a>
		<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id); ?>" title="<?php echo JText::_('JACTION_DELETE'); ?>">
			<?php echo JText::_('JACTION_DELETE'); ?>
		</a>
		<a class="modal edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
			<?php echo JText::_('JACTION_EDIT'); ?>
		</a>
	</li>
<?php
		}
	}
?>