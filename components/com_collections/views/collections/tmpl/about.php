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
defined('_JEXEC') or die('Restricted access');

$this->css();

$base = 'index.php?option=' . $this->option;
?>
<header id="content-header">
	<h2><?php echo JText::_('COM_COLLECTIONS'); ?></h2>

	<div id="content-header-extra">
		<ul>
			<li>
				<a class="icon-info about btn" href="<?php echo JRoute::_($base . '&task=about'); ?>">
					<span><?php echo JText::_('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
				</a>
			</li>
		</ul>
	</div>
</header>

<form method="get" action="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<fieldset class="filters">
		<div class="filters-inner">
			<ul>
				<li>
					<a class="collections count" href="<?php echo JRoute::_($base . '&task=all'); ?>">
						<span><?php echo JText::sprintf('COM_COLLECTIONS_HEADER_NUM_COLLECTIONS', $this->collections); ?></span>
					</a>
				</li>
				<li>
					<a class="posts count" href="<?php echo JRoute::_($base . '&task=posts'); ?>">
						<span><?php echo JText::sprintf('COM_COLLECTIONS_HEADER_NUM_POSTS', $this->total); ?></span>
					</a>
				</li>
			</ul>
			<div class="clear"></div>
			<p>
				<label for="filter-search">
					<span><?php echo JText::_('COM_COLLECTIONS_SEARCH_LABEL'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COLLECTIONS_SEARCH_PLACEHOLDER'); ?>" />
				</label>
				<input type="submit" class="filter-submit" value="<?php echo JText::_('COM_COLLECTIONS_GO'); ?>" />
			</p>
		</div>
	</fieldset>

	<section class="main section about">

		<p class="tagline"><?php echo JText::_('COM_COLLECTIONS_TAGLINE'); ?></p>

		<div class="about-odd posts">
			<h3><?php echo JText::_('COM_COLLECTIONS_POST'); ?></h3>
			<p>
				<?php echo JText::_('COM_COLLECTIONS_POST_EXPLANATION'); ?>
			</p>
		</div>

		<div class="about-even collections">
			<h3><?php echo JText::_('COM_COLLECTIONS_COLLECTION'); ?></h3>
			<p>
				<?php echo JText::_('COM_COLLECTIONS_COLLECTION_EXPLANATION'); ?>
			</p>
		</div>

		<div class="about-odd following">
			<h3><?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?></h3>
			<p>
				<?php echo JText::sprintf('COM_COLLECTIONS_FOLLOW_EXPLANATION', JRoute::_('index.php?option=com_members&task=myaccount/collections'), JRoute::_('index.php?option=com_members&task=myaccount'), JRoute::_('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

		<div class="about-even unfollowing">
			<h3><?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?></h3>
			<p>
				<?php echo JText::sprintf('COM_COLLECTIONS_UNFOLLOW_EXPLANATION', JRoute::_('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

		<div class="about-odd livefeed">
			<h3><?php echo JText::_('COM_COLLECTIONS_LIVE_FEED'); ?></h3>
			<p>
				<?php echo JText::sprintf('COM_COLLECTIONS_LIVE_FEED_EXPLANATION', JRoute::_('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

	</section>
</form>