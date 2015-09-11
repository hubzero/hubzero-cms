<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
				<h3><?php echo Lang::txt('COM_FEEDAGGREGATOR_WHAT_IS_AGGREGATOR'); ?></h3>
				<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_WHAT_IS_AGGREGATOR_DESC'); ?></p>
			</div>
			<div class="col span-half omega">
				<h3><?php echo Lang::txt('COM_FEEDAGGREGATOR_HOW_TO_READ_AGGREGATOR'); ?></h3>
				<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_HOW_TO_READ_AGGREGATOR_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_feedaggregator#feedbox'); ?>" class="feed-btn btn-success fancybox-inline"><?php echo Lang::txt('COM_FEEDAGGREGATOR_GENERATE_FEED'); ?></a></p>
			</div>
		</div>
	</div><!-- / .subject -->
	<aside class="aside">
		<h3><?php echo Lang::txt('COM_FEEDAGGREGATOR_QUESTIONS'); ?></h3>
		<ul>
			<li>
				<a class="fancybox-inline" href="#helpbox"><?php echo Lang::txt('COM_FEEDAGGREGATOR_NEED_HELP'); ?></a>
			</li>
		</ul>
	</aside><!-- / .aside -->
</section>

<section class="main section">
	<div id="page-main">
		<!-- Help Dialog -->
		<div class="postpreview-container">
			<div id="helpbox">
				<h1><?php echo Lang::txt('COM_FEEDAGGREGATOR_FEED_INFO'); ?></h1>
				<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_FEED_INFO_ABOUT'); ?></p>

				<h2 id="userPermTitle" class="helpExpander"><?php echo Lang::txt('COM_FEEDAGGREGATOR_USER_PERMISSIONS'); ?></h2>
				<p class="helpbox"><?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_USERPERMS1')?></p>
				<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_USERPERMS2'); ?></p>
				<ol>
					<li>
						<?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_LOGIN'); ?>
					</li>
					<li>
						<?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_USER_MANAGER'); ?>
						<img src="<?php echo $this->img('step1-usermanager.png'); ?>" alt="" />
					</li>
					<li>
						<?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_FIND_USER'); ?>
						<img src="<?php echo $this->img('step2-usermanager.png'); ?>" alt=""/>
					</li>
					<li>
						<?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_SELECT_PERMISSION'); ?>
					</li>
					<li>
						<?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_SAVE'); ?>
						<img src="<?php echo $this->img('step3-usermanager.png'); ?>" alt=""/>
					</li>
					<li>
						<?php echo Lang::txt('COM_FEEDAGGREGATOR_HELP_FINISHED'); ?>
					</li>
				</ol>
			</div>
		</div>

		<!--  Generate Feed -->
		<div class="postpreview-container">
			<div class="postpreview" id="feedbox">
				<h2><?php echo Lang::txt('COM_FEEDAGGREGATOR_GENERATE_HEADER'); ?></h2>
				<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_GENERATE_INSTRUCTIONS'); ?></p>
				<p>
					<a href="<?php echo rtrim(Request::base(), '/') . Route::url('index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?>">
						<?php echo rtrim(Request::base(), '/') . Route::url('index.php?option=com_feedaggregator&task=generateFeed&no_html=1'); ?>
					</a>
				</p>
			</div>
		</div>
	</div> <!--  main page -->
</section><!-- /.main section -->