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

//import helper class
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Document');

$database = JFactory::getDBO();
$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option;
?>
<div id="content-header">
	<h2><?php echo JText::_('Collections'); ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="icon-info about btn" href="<?php echo JRoute::_($base . '&task=about'); ?>">
				<span><?php echo JText::_('Getting started'); ?></span>
			</a>
		</li>
	</ul>
</div>

<form method="get" action="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<fieldset class="filters">
		<div class="filters-inner">
		<ul>
			<li>
				<a class="collections count" href="<?php echo JRoute::_($base . '&task=all'); ?>">
					<span><?php echo JText::sprintf('<strong>%s</strong> collections', $this->collections); ?></span>
				</a>
			</li>
			<li>
				<a class="posts count" href="<?php echo JRoute::_($base . '&task=posts'); ?>">
					<span><?php echo JText::sprintf('<strong>%s</strong> posts', $this->total); ?></span>
				</a>
			</li>
		</ul>
		<div class="clear"></div>
		<p>
			<label for="filter-search">
				<span><?php echo JText::_('Search'); ?></span>
				<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search posts'); ?>" />
			</label>
			<input type="submit" class="filter-submit" value="<?php echo JText::_('Go'); ?>" />
		</p>
		</div>
	</fieldset>

	<div class="main section">

		<p class="tagline">A quick and easy way to share, favorite, and organize information on a hub.</p>

		<div class="about-odd posts">
			<h3>Post</h3>
			<p>
				A post starts with an image, link, or file you want to share. You can add a post by collecting content on the site or upload a file right from your computer. Any post can be reposted, and all posts link back to their source.
			</p>
		</div>

		<div class="about-even collections">
			<h3>Collection</h3>
			<p>
				A collection is where you organize your posts by topic. You could collect resources for creating an introduction to Nanotechnology, for example. Collections can be secret or public, and you can even put a collection inside another collection!
			</p>
		</div>

		<div class="about-odd following">
			<h3>Follow</h3>
			<p>
				When you follow someone, their posts show up in your <a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount/collections'); ?>">live feed</a>. You can follow all of someone's collections or just the ones you like best. To manage who you're following, go to your <a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount'); ?>">profile</a>, find <a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount/collections'); ?>">Collections</a>, and click Following.
			</p>
		</div>

		<div class="about-even unfollowing">
			<h3>Unfollow</h3>
			<p>
				When you unfollow someone, their collections won't show up in your <a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount/collections'); ?>">live feed</a> anymore. You can unfollow all of someone's collections, or just the ones you're not that interested in. <!-- Nobody will get notified if you unfollow them. -->
			</p>
		</div>

		<div class="about-odd livefeed">
			<h3>Live Feed</h3>
			<p>
				Your <a href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount/collections'); ?>">live feed</a> is a collection of posts from collectors and collections you follow. It's updated every time someone you follow adds a post.
			</p>
		</div>

	</div>
</form>