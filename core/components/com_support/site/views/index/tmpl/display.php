<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

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
				<li><a class="ticket-help popup" href="<?php echo Route::url('index.php?option=com_help&component=support&page=faqs'); ?>">Support FAQ's</a></li>
				<?php if (Component::isEnabled('com_kb')) { ?>
					<li><a class="com-kb" href="<?php echo Route::url('index.php?option=com_kb'); ?>">Knowledge Base</a></li>
				<?php } ?>
				<li><a class="ticket-report" href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>">Report Problems</a></li>
				<li><a class="ticket-track" href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">Track Tickets</a></li>
			</ul>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">
	<?php if (Component::isEnabled('com_resources') || Component::isEnabled('com_tags') || Component::isEnabled('com_search')) { ?>
	<div class="grid">
		<div class="col span3">
			<h2>Finding Content</h2>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<?php if (Component::isEnabled('com_resources')) { ?>
			<div class="content-presentation">
				<h3><a class="com-resources" href="<?php echo Route::url('index.php?option=com_resources'); ?>">Resources</a></h3>
				<p>Find the latest cutting-edge research in our <a class="com-resources" href="<?php echo Route::url('index.php?option=com_resources'); ?>">resources</a>.</p>
			</div><!-- / .presentations -->
			<?php } ?>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<?php if (Component::isEnabled('com_tags')) { ?>
			<div class="content-tag">
				<h3><a class="com-tags" href="<?php echo Route::url('index.php?option=com_tags'); ?>">Tags</a></h3>
				<p>Explore all our content through <a class="com-tags" href="<?php echo Route::url('index.php?option=com_tags'); ?>">tags</a> or even tag content yourself.</p>
			</div><!-- / .tag -->
			<?php } ?>
		</div><!-- / .col span3 -->
		<div class="col span3 omega">
			<?php if (Component::isEnabled('com_search')) { ?>
			<div class="content-search">
				<h3><a class="com-search" href="<?php echo Route::url('index.php?option=com_search'); ?>">Search</a></h3>
				<p>Try <a class="com-search" href="<?php echo Route::url('index.php?option=com_search'); ?>">searching</a> for a title, author, tag, phrase, or keywords.</p>
			</div><!-- / .search -->
			<?php } ?>
		</div><!-- / .col span3 -->
	</div><!-- / .grid -->
	<?php } ?>

	<?php if (Component::isEnabled('com_answers') || Component::isEnabled('com_wishlist') || Component::isEnabled('com_wiki')) { ?>
	<div class="grid">
		<div class="col span3">
			<h2>Community Help</h2>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<?php if (Component::isEnabled('com_answers')) { ?>
			<div class="feedback">
				<h3><a class="com-answers" href="<?php echo Route::url('index.php?option=com_answers'); ?>">Questions &amp; Answers</a></h3>
				<p>Get your <a class="com-answers" href="<?php echo Route::url('index.php?option=com_answers'); ?>">questions answered</a> and help others find the clue.</p>
			</div><!-- / .feedback -->
			<?php } ?>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<?php if (Component::isEnabled('com_wishlist')) { ?>
			<div class="idea">
				<h3><a href="<?php echo Route::url('index.php?option=com_wishlist'); ?>">Wish List</a></h3>
				<p><a href="<?php echo Route::url('index.php?option=com_wishlist'); ?>">Tell everyone</a> your ideas or features you would like to see.</p>
			</div><!-- / .idea -->
			<?php } ?>
		</div><!-- / .col span3 -->
		<div class="col span3 omega">
			<?php if (Component::isEnabled('com_wiki')) { ?>
			<div class="wiki">
				<h3><a class="com-wiki" href="<?php echo Route::url('index.php?option=com_wiki'); ?>">Wiki</a></h3>
				<p>Take a look at our user-generated <a class="com-wiki" href="<?php echo Route::url('index.php?option=com_wiki'); ?>">wiki pages</a> or write your own.</p>
			</div><!-- / .wiki -->
			<?php } ?>
		</div><!-- / .col span3 -->
	</div><!-- / .grid -->
	<?php } ?>

	<div class="grid">
		<div class="col span3">
			<h2>Getting Support</h2>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<?php if (Component::isEnabled('com_kb')) { ?>
			<div class="series">
				<h3><a class="com-kb" href="<?php echo Route::url('index.php?option=com_kb'); ?>">Knowledge Base</a></h3>
				<p><a class="com-kb" href="<?php echo Route::url('index.php?option=com_kb'); ?>">Find</a> answers to frequently asked questions, helpful tips, and any other information we thought might be useful.</p>
			</div><!-- / .series -->
			<?php } ?>
		</div><!-- / .col span3 -->
		<div class="col span3">
			<div class="note">
				<h3><a class="ticket-report" href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>">Report Problems</a></h3>
				<p><a class="ticket-report" href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>">Report problems</a> with our form and have your problem entered into our <a class="ticket-track" href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">ticket tracking system</a>. We guarantee a response!</p>
			</div><!-- / .note -->
		</div><!-- / .col span3 -->
		<div class="col span3 omega">
			<div class="ticket">
				<h3><a class="ticket-track" href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">Track Tickets</a></h3>
				<p>Have a problem entered into our <a class="ticket-track" href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">ticket tracking system</a>? Track its progress, add comments and notes, or close resolved issues.</p>
			</div><!-- / .ticket -->
		</div><!-- / .col span3 -->
	</div><!-- / .grid -->
</section><!-- / .section -->