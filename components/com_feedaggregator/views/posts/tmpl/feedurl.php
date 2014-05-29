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
				<h3>What is the Feed Aggregator?</h3>
				<p>The Feed Aggregator is a component which allows feed managers 
				to collect articles from several RSS feeds and to selectively combine them into one site-sponsored RSS feed.</p>
			</div>
			<div class="col span-half omega">
				<h3>How do I read the aggregated feed?</h3>
				<p>Registered users may read the feed by simply clicking the button below. <br>The URL is importable into any RSS feed reader.</p>
				<p><a href="#feedbox" style="color: white; background-color: green;" class="feed-btn fancybox-inline">Generate RSS Feed</a></p>
			</div>
		</div>
	</div><!-- / .subject -->
	<aside class="aside">
		<h3>Questions?</h3>
		<ul>
			<li>
				<a class="fancybox-inline" href="#helpbox">Need Help?</a>
			</li>
		</ul>
	</aside><!-- / .aside -->
</section>

<section class="main section">
	<div id="page-main">
		<!-- Help Dialog -->
		<div class="postpreview-container">
			<div id="helpbox">
				<h1>Feed Aggregator Info</h1>
				<p>A brief quick-start guide for using the Feed Aggregator Component.</p>

				<h2 id="userPermTitle" class="helpExpander">User Permissions</h2>
				<p class="helpbox"><?php echo JText::_('COM_FEEDAGGREGATOR_HELP_USERPERMS1')?></p>
				<?php echo JText::_('COM_FEEDAGGREGATOR_HELP_USERPERMS2')?>
				<ol>
					<li>
						Login into the administrative dashboard.
					</li>
					<li>
						Click on 'User Manager' under the Users menu.
						<img src="<?php echo JURI::root();?>components/com_feedaggregator/assets/img/step1-usermanager.png" alt="" />
					</li>
					<li>
						Check the box next to the user you would like to promote.
						<img src="<?php echo JURI::root();?>components/com_feedaggregator/assets/img/step2-usermanager.png" alt=""/>
					</li>
					<li>
						In the drop-down box, select the permission level for the user.
						<ul>
							<li>This user can be an author, editor, or publisher.</li>
						</ul>
					</li>
					<li>
						Click on the 'Process' button to save the permission level
						<img src="<?php echo JURI::root();?>components/com_feedaggregator/assets/img/step3-usermanager.png" alt=""/>
					</li>
					<li>
						The user will now have access to sort posts from the feeds.
					</li>
				</ol>
			</div>
		</div>

		<!--  Generate Feed -->
		<div class="postpreview-container">
			<div class="postpreview" id="feedbox">
				<h2><?php echo JText::_('COM_FEEDAGGREGATOR_GENERATE_HEADER'); ?></h2>
				<p><?php echo JText::_('COM_FEEDAGGREGATOR_GENERATE_INSTRUCTIONS'); ?></p>
				<p><a href="<?php echo JRoute::_(JFactory::getURI()->base().'index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?>"><?php echo JRoute::_(JFactory::getURI()->base().'index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?></a></p>
			</div>
		</div>
	</div> <!--  main page -->
</section><!-- /.main section -->