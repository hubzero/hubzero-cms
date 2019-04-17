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

$this->css('introduction.css', 'system')
     ->css()
     ->js();

$rows = $this->model->entries('list', $this->filters);

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<nav id="content-header-extra">
		<ul id="useroptions">
			<li><a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_PROJECTS_BROWSE_PUBLIC_PROJECTS'); ?></a></li>
		</ul>
	</nav><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php
	// Display status message
	$this->view('_statusmsg', 'projects')
	     ->set('error', $this->getError())
	     ->set('msg', $this->msg)
	     ->display();
?>

<section id="introduction" class="section">
	<div id="introbody">
		<div class="grid">
			<div class="col span5">
				<h3><?php echo Lang::txt('COM_PROJECTS_INTRO_COLLABORATION_MADE_EASY'); ?></h3>
				<p><?php echo Lang::txt('COM_PROJECTS_INTRO_COLLABORATION_HOW'); ?></p>
				<?php if (User::authorise('core.create', $this->option)): ?>
					<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=start'); ?>" id="projects-intro-start" class="btn icon-next"><?php echo Lang::txt('COM_PROJECTS_START_PROJECT'); ?></a></p>
				<?php endif; ?>
			</div>
			<div class="col span4 omega">
				<h3><?php echo Lang::txt('COM_PROJECTS_INTRO_WHAT_YOU_GET'); ?></h3>
				<ul>
					<li><?php echo Lang::txt('COM_PROJECTS_INTRO_GET_WIKI'); ?></li>
					<li><?php echo Lang::txt('COM_PROJECTS_INTRO_GET_TODO'); ?></li>
					<li><?php echo Lang::txt('COM_PROJECTS_INTRO_GET_BLOG'); ?></li>
					<?php if ($this->publishing): ?>
						<li><?php echo Lang::txt('COM_PROJECTS_INTRO_GET_PUBLISHING'); ?></li>
					<?php endif; ?>
				</ul>
				<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=features'); ?>" id="projects-intro-features" class="btn"><?php echo Lang::txt('COM_PROJECTS_LEARN_MORE'); ?></a></p>
			</div>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section myprojects">
	<div class="grid">
		<div class="col span2">
			<h2><?php echo Lang::txt('COM_PROJECTS_MY_PROJECTS'); ?></h2>
		</div>
		<div class="col span10 omega">
			<?php if (count($rows) > 0):
				// Display List of items
				$this->view('_list')
				     ->set('rows', $rows)
				     ->set('filters', array())
				     ->set('option', $this->option)
				     ->display();
				?>
			<?php else: ?>
				<div class="noresults">
					<?php echo (User::isGuest()) ? Lang::txt('COM_PROJECTS_PLEASE').' <a href="' . Route::url('index.php?option=' . $this->option . '&task=intro&action=login') . '" id="projects-intro-login">' . Lang::txt('COM_PROJECTS_LOGIN') . '</a> ' . Lang::txt('COM_PROJECTS_TO_VIEW_YOUR_PROJECTS') : Lang::txt('COM_PROJECTS_YOU_DONT_HAVE_PROJECTS'); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
