<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->css('features');

$html  = '';

$wishlist = Component::isEnabled('com_wishlist');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="btn icon-add" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=start'); ?>"><?php echo Lang::txt('COM_PROJECTS_START_NEW'); ?></a></li>
			<li><a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_PROJECTS_BROWSE_PUBLIC_PROJECTS'); ?></a></li>
		</ul>
	</div>
</header>

<section id="feature-section">
	<div class="feature">
		<div id="feature-blog" class="grid">
			<div class="col span3">
				<h3><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .col -->
			<div class="col span6 about">
				<p class="f-about"><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_ABOUT'); ?></p>

				<h4><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_ABOUT_LEARN'); ?></h4>
				<ul class="f-updates">
					<li class="team"><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_TEAM'); ?></li>
					<li class="blog"><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_BLOG'); ?></li>
					<li class="todo"><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_TODO'); ?></li>
					<li class="notes"><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_NOTES'); ?></li>
					<li class="files"><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_FILES'); ?></li>
					<?php if ($this->publishing) { ?>
					<li class="publications"><?php echo Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_PUB'); ?></li>
					<?php } ?>
				</ul>
				<?php if ($wishlist && $this->config->get('suggest_feature', 1)) { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?></h4>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&task=add&category=general&id=1').'/?tag=projects,projects:microblog,com_projects'; ?>" class="btn btn-success"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&category=general&id=1').'/?tags=projects,projects:microblog,com_projects'; ?>" class="btn"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a>
					</p>
				<?php } ?>
			</div><!-- / .col -->

			<div class="col span3 omega">
			</div><!-- / .col -->
		</div>
	</div>

	<div class="feature">
		<div id="feature-todo" class="grid">
			<div class="col span3">
				<h3><?php echo Lang::txt('COM_PROJECTS_FEATURES_TODO'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .col -->
			<div class="col span6 about">
				<p class="f-about"><?php echo Lang::txt('COM_PROJECTS_FEATURES_TODO_ABOUT'); ?></p>
				<?php if ($wishlist && $this->config->get('suggest_feature', 1)) { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?></h4>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&task=add&category=general&id=1').'/?tag=projects,projects:todo,com_projects'; ?>" class="btn btn-success"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&category=general&id=1').'/?tags=projects,projects:todo,com_projects'; ?>" class="btn"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a>
					</p>
				<?php } ?>
			</div><!-- / .col -->
			<div class="col span3 omega">
			</div><!-- / .col -->
		</div>
	</div>

	<div class="feature">
		<div id="feature-notes" class="grid">
			<div class="col span3">
				<h3><?php echo Lang::txt('COM_PROJECTS_FEATURES_NOTES'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .col -->
			<div class="col span6 about">
				<p class="f-about"><?php echo Lang::txt('COM_PROJECTS_FEATURES_NOTES_ABOUT'); ?></p>
				<?php if ($wishlist && $this->config->get('suggest_feature', 1)) { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?></h4>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&task=add&category=general&id=1').'/?tag=projects,projects:notes,com_projects'; ?>" class="btn btn-success"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&category=general&id=1').'/?tags=projects,projects:notes,com_projects'; ?>" class="btn"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a>
					</p>
				<?php } ?>
			</div><!-- / .col -->
			<div class="col span3 omega">
			</div><!-- / .col -->
		</div>
	</div>

	<div class="feature">
		<div id="feature-team" class="grid">
			<div class="col span3">
				<h3><?php echo Lang::txt('COM_PROJECTS_FEATURES_TEAM'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .col -->
			<div class="col span6 about">
				<p class="f-about"><?php echo Lang::txt('COM_PROJECTS_FEATURES_TEAM_ABOUT'); ?></p>
				<?php if ($wishlist && $this->config->get('suggest_feature', 1)) { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?></h4>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&task=add&category=general&id=1').'/?tag=projects,projects:team,com_projects'; ?>" class="btn btn-success"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&category=general&id=1').'/?tags=projects,projects:team,com_projects'; ?>" class="btn"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a>
					</p>
				<?php } ?>
			</div><!-- / .col -->
			<div class="col span3 omega">
			</div><!-- / .col -->
		</div>
	</div>

	<div class="feature">
		<div id="feature-files" class="grid">
			<div class="col span3">
				<h3><?php echo Lang::txt('COM_PROJECTS_FEATURES_FILES'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .col -->
			<div class="col span6 about">
				<p class="f-about"><?php echo Lang::txt('COM_PROJECTS_FEATURES_FILES_ABOUT_START'); ?> <a href="http://git-scm.com/" rel="external"><?php echo Lang::txt('COM_PROJECTS_FEATURES_FILES_ABOUT_GIT'); ?></a> <?php echo Lang::txt('COM_PROJECTS_FEATURES_FILES_ABOUT_END'); ?></p>
				<?php if ($wishlist && $this->config->get('suggest_feature', 1)) { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?></h4>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&task=add&category=general&id=1') . '/?tag=projects,projects:files,com_projects'; ?>" class="btn btn-success"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&category=general&id=1') . '/?tags=projects,projects:files,com_projects'; ?>" class="btn"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a>
					</p>
				<?php } ?>
			</div><!-- / .col -->
			<div class="col span3 omega">
			</div><!-- / .col -->
		</div>
	</div>

	<div class="feature">
		<div id="feature-publications" class="grid<?php if (!$this->publishing) { echo ' in-the-works'; } ?>">
			<div class="col span3">
				<h3><?php echo Lang::txt('COM_PROJECTS_FEATURES_PUBLICATIONS'); ?><?php if (!$this->publishing) { echo '*'; } ?></h3>
				<?php if (!$this->publishing) { ?>
				<p class="wip"><?php echo Lang::txt('COM_PROJECTS_FEATURES_IN_THE_WORKS'); ?></p>
				<?php } ?>
				<p class="ima">&nbsp;</p>
			</div><!-- / .col -->
			<div class="col span6 about">
				<p class="f-about"><?php echo $this->publishing ? Lang::txt('COM_PROJECTS_FEATURES_PUBLICATIONS_ABOUT') : Lang::txt('COM_PROJECTS_FEATURES_PUBLICATIONS_ABOUT_WIP'); ?> </p>
				<?php if ($wishlist && $this->config->get('suggest_feature', 1)) { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?></h4>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&task=add&category=general&id=1').'/?tag=projects,projects:publications,com_projects'; ?>" class="btn btn-success"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&category=general&id=1').'/?tags=projects,projects:publications,com_projects'; ?>" class="btn"><?php echo Lang::txt('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a>
					</p>
				<?php } ?>
			</div><!-- / .col -->
			<div class="col span3 omega">
			</div><!-- / .col -->
		</div>
	</div>
</section>
