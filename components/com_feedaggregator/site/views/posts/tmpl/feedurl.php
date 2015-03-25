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

$this->js('posts')
     ->css('posts');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section id="introduction" class="section">
	<div class="subject">
		<div class="grid">
			<div class="col span-half">
				<h3><?php echo JText::_('COM_FEEDAGGREGATOR_WHAT_IS_AGGREGATOR'); ?></h3>
				<p><?php echo JText::_('COM_FEEDAGGREGATOR_WHAT_IS_AGGREGATOR_DESC'); ?></p>
			</div>
			<div class="col span-half omega">
				<h3><?php echo JText::_('COM_FEEDAGGREGATOR_HOW_TO_READ_AGGREGATOR'); ?></h3>
				<p><?php echo JText::_('COM_FEEDAGGREGATOR_HOW_TO_READ_AGGREGATOR_DESC'); ?></p>
				<p><a href="#feedbox" class="feed-btn btn-success fancybox-inline"><?php echo JText::_('COM_FEEDAGGREGATOR_GENERATE_FEED'); ?></a></p>
			</div>
		</div>
	</div><!-- / .subject -->
	<aside class="aside">
		<h3><?php echo JText::_('COM_FEEDAGGREGATOR_QUESTIONS'); ?></h3>
		<ul>
			<li>
				<a class="fancybox-inline" href="#helpbox"><?php echo JText::_('COM_FEEDAGGREGATOR_NEED_HELP'); ?></a>
			</li>
		</ul>
	</aside><!-- / .aside -->
</section>

<section class="main section">
	<div id="page-main">
		<!-- Help Dialog -->
		<div class="postpreview-container">
			<div id="helpbox">
				<h1><?php echo JText::_('COM_FEEDAGGREGATOR_FEED_INFO'); ?></h1>
				<p><?php echo JText::_('COM_FEEDAGGREGATOR_FEED_INFO_ABOUT'); ?></p>

				<h2 id="userPermTitle" class="helpExpander"><?php echo JText::_('COM_FEEDAGGREGATOR_USER_PERMISSIONS'); ?></h2>
				<p class="helpbox"><?php echo JText::_('COM_FEEDAGGREGATOR_HELP_USERPERMS1')?></p>
				<p><?php echo JText::_('COM_FEEDAGGREGATOR_HELP_USERPERMS2'); ?></p>
				<ol>
					<li>
						<?php echo JText::_('COM_FEEDAGGREGATOR_HELP_LOGIN'); ?>
					</li>
					<li>
						<?php echo JText::_('COM_FEEDAGGREGATOR_HELP_USER_MANAGER'); ?>
						<img src="<?php echo $this->img('step1-usermanager.png'); ?>" alt="" />
					</li>
					<li>
						<?php echo JText::_('COM_FEEDAGGREGATOR_HELP_FIND_USER'); ?>
						<img src="<?php echo $this->img('step2-usermanager.png'); ?>" alt=""/>
					</li>
					<li>
						<?php echo JText::_('COM_FEEDAGGREGATOR_HELP_SELECT_PERMISSION'); ?>
					</li>
					<li>
						<?php echo JText::_('COM_FEEDAGGREGATOR_HELP_SAVE'); ?>
						<img src="<?php echo $this->img('step3-usermanager.png'); ?>" alt=""/>
					</li>
					<li>
						<?php echo JText::_('COM_FEEDAGGREGATOR_HELP_FINISHED'); ?>
					</li>
				</ol>
			</div>
		</div>

		<!--  Generate Feed -->
		<div class="postpreview-container">
			<div class="postpreview" id="feedbox">
				<h2><?php echo JText::_('COM_FEEDAGGREGATOR_GENERATE_HEADER'); ?></h2>
				<p><?php echo JText::_('COM_FEEDAGGREGATOR_GENERATE_INSTRUCTIONS'); ?></p>
				<p>
					<a href="<?php echo rtrim(JURI::base(), '/') . JRoute::_('index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?>">
						<?php echo rtrim(JURI::base(), '/') . JRoute::_('index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?>
					</a>
				</p>
			</div>
		</div>
	</div> <!--  main page -->
</section><!-- /.main section -->