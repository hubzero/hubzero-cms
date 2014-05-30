<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

$this->css('introduction.css', 'system')
     ->css();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<div class="grid">
				<div class="col span-half">
					<h3>Getting Help</h3>
					<p>We offer several ways of finding content and encourage exploring our knowledge base and engaging the community for support.</p>
				</div><!-- / .col span-half -->
				<div class="col span-half omega">
					<h3>When All Else Fails</h3>
					<p>Report problems to us directly and track their progress. We will try our best to answer your questions and work with you to resolve any issues you may have.</p>
				</div><!-- / .col span-half -->
			</div>
		</div>
		<div class="col span3 omega">
			<h3>Quick Links</h3>
			<ul>
				<li><a class="ticket-help popup" href="<?php echo JRoute::_('index.php?option=com_help&component=support&page=faqs'); ?>">Support FAQ's</a></li>
				<li><a class="com-kb" href="<?php echo JRoute::_('index.php?option=com_kb'); ?>">Knowledge Base</a></li>
				<li><a class="ticket-report" href="<?php echo JRoute::_('index.php?option=com_support&task=new'); ?>">Report Problems</a></li>
				<li><a class="ticket-track" href="<?php echo JRoute::_('index.php?option=com_support&task=tickets'); ?>">Track Tickets</a></li>
			</ul>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">
	<div class="grid">
		<div class="col span3">
			<h2>Finding Content</h2>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<div class="presentation">
				<h3><a class="com-resources" href="<?php echo JRoute::_('index.php?option=com_resources'); ?>">Resources</a></h3>
				<p>Find the latest cutting-edge research in our <a class="com-resources" href="<?php echo JRoute::_('index.php?option=com_resources'); ?>">resources</a>.</p>
			</div><!-- / .presentations -->
		</div><!-- / .col span3 -->
		<div class="col span3">
			<div class="tag">
				<h3><a class="com-tags" href="<?php echo JRoute::_('index.php?option=com_tags'); ?>">Tags</a></h3>
				<p>Explore all our content through <a class="com-tags" href="<?php echo JRoute::_('index.php?option=com_tags'); ?>">tags</a> or even tag content yourself.</p>
			</div><!-- / .tag -->
		</div><!-- / .col span3 -->
		<div class="col span3 omega">
			<div class="search">
				<h3><a class="com-search" href="<?php echo JRoute::_('index.php?option=com_search'); ?>">Search</a></h3>
				<p>Try <a class="com-search" href="<?php echo JRoute::_('index.php?option=com_search'); ?>">searching</a> for a title, author, tag, phrase, or keywords.</p>
			</div><!-- / .search -->
		</div><!-- / .col span3 -->
	</div><!-- / .grid -->

	<div class="grid">
		<div class="col span3">
			<h2>Community Help</h2>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<div class="feedback">
				<h3><a class="com-answers" href="<?php echo JRoute::_('index.php?option=com_answers'); ?>">Questions &amp; Answers</a></h3>
				<p>Get your <a class="com-answers" href="<?php echo JRoute::_('index.php?option=com_answers'); ?>">questions answered</a> and help others find the clue.</p>
			</div><!-- / .feedback -->
		</div><!-- / .col span3 -->
		<div class="col span3">
			<div class="idea">
				<h3><a href="<?php echo JRoute::_('index.php?option=com_wishlist'); ?>">Wish List</a></h3>
				<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'); ?>">Tell everyone</a> your ideas or features you would like to see.</p>
			</div><!-- / .idea -->
		</div><!-- / .col span3 -->
		<div class="col span3 omega">
			<div class="wiki">
				<h3><a class="com-wiki" href="<?php echo JRoute::_('index.php?option=com_wiki'); ?>">Wiki</a></h3>
				<p>Take a look at our user-generated <a class="com-wiki" href="<?php echo JRoute::_('index.php?option=com_wiki'); ?>">wiki pages</a> or write your own.</p>
			</div><!-- / .wiki -->
		</div><!-- / .col span3 -->
	</div><!-- / .grid -->

	<div class="grid">
		<div class="col span3">
			<h2>Getting Support</h2>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<div class="series">
				<h3><a class="com-kb" href="<?php echo JRoute::_('index.php?option=com_kb'); ?>">Knowledge Base</a></h3>
				<p><a class="com-kb" href="<?php echo JRoute::_('index.php?option=com_kb'); ?>">Find</a> answers to frequently asked questions, helpful tips, and any other information we thought might be useful.</p>
			</div><!-- / .series -->
		</div><!-- / .col span3 -->
		<div class="col span3">
			<div class="note">
				<h3><a class="ticket-report" href="<?php echo JRoute::_('index.php?option=com_support&task=new'); ?>">Report Problems</a></h3>
				<p><a class="ticket-report" href="<?php echo JRoute::_('index.php?option=com_support&task=new'); ?>">Report problems</a> with our form and have your problem entered into our <a class="ticket-track" href="<?php echo JRoute::_('index.php?option=com_support&task=tickets'); ?>">ticket tracking system</a>. We guarantee a response!</p>
			</div><!-- / .note -->
		</div><!-- / .col span3 -->
		<div class="col span3 omega">
			<div class="ticket">
				<h3><a class="ticket-track" href="<?php echo JRoute::_('index.php?option=com_support&task=tickets'); ?>">Track Tickets</a></h3>
				<p>Have a problem entered into our <a class="ticket-track" href="<?php echo JRoute::_('index.php?option=com_support&task=tickets'); ?>">ticket tracking system</a>? Track its progress, add comments and notes, or close resolved issues.</p>
			</div><!-- / .ticket -->
		</div><!-- / .col span3 -->
	</div><!-- / .grid -->
</section><!-- / .section -->