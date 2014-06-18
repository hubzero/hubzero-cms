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

$juser = JFactory::getUser();

$app = JRequest::getVar( 'app', '', 'request', 'object' );

?>
	<ul class="sub-menu">
		<li class="page-text<?php if ($this->controller == 'page' && ($this->task == 'display' || !$this->task)) { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->get('scope').'&pagename='.$this->page->get('pagename')); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_ARTICLE'); ?></span>
			</a>
		</li>
<?php if ($this->page->exists() && $this->page->get('namespace') != 'special') { ?>
	<?php if (($this->page->isLocked() && $this->page->access('manage')) || (!$this->page->isLocked() && $this->page->access('edit'))) { ?>
		<li class="page-edit<?php if ($this->controller == 'page' && $this->task == 'edit') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->get('scope').'&pagename='.$this->page->get('pagename').'&task=edit'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_EDIT'); ?></span>
			</a>
		</li>
	<?php } ?>
	<?php if ($this->page->access('view', 'comment')) { ?>
		<li class="page-comments<?php if ($this->controller == 'comments') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->get('scope').'&pagename='.$this->page->get('pagename').'&task=comments'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_COMMENTS'); ?></span>
			</a>
		</li>
	<?php } ?>
		<li class="page-history<?php if ($this->controller == 'history') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->get('scope').'&pagename='.$this->page->get('pagename').'&task=history'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_HISTORY'); ?></span>
			</a>
		</li>
<?php } ?>
	</ul>