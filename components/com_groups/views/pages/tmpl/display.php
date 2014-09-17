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
<ul class="toolbar toolbar-pages">
	<li class="new">
		<a class="btn icon-add" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=add'); ?>">
			<?php echo JText::_('COM_GROUPS_PAGES_NEW_PAGE'); ?>
		</a>
	</li>
	<li class="filter">
		<select>
			<option value=""><?php echo JText::_('COM_GROUPS_PAGES_PAGE_FILTER'); ?></option>
			<?php foreach ($this->categories as $category) : ?>
				<option data-color="#<?php echo $category->get('color'); ?>" value="<?php echo $category->get('id'); ?>"><?php echo $category->get('title'); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li class="filter-search-divider"><?php echo JText::_('COM_GROUPS_PAGES_PAGE_OR'); ?></li>
	<li class="search">
		<input type="text" placeholder="<?php echo JText::_('COM_GROUPS_PAGES_PAGE_SEARCH'); ?>" />
	</li>
</ul>

<?php
	$this->view('list')
		 ->set('level', 0)
		 ->set('pages', $this->pages)
		 ->set('categories', $this->categories)
		 ->set('group', $this->group)
		 ->set('config', $this->config)
		 ->display();
?>