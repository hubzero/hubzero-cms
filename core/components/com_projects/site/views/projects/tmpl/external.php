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
     ->js()
     ->css('external')
     ->css('extended.css');

// Get project params
$params = $this->model->params;
$theme = $params->get('theme', $this->config->get('theme', 'light'));

// Include theme CSS
$this->css('theme' . $theme . '.css');

?>
<div id="project-wrap" class="theme publicview">
	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_PROJECTS_ALL_PROJECTS'); ?></a></li>
			<?php if (User::authorise('core.create', $this->option)) { ?>
				<li><a class="btn icon-add" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=start'); ?>"><?php echo Lang::txt('COM_PROJECTS_START_NEW'); ?></a></li>
			<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->

	<?php if ($this->model->access('member') && !$this->reviewer) { // Public preview for authorized users ?>
		<div id="project-preview">
			<p><?php echo Lang::txt('COM_PROJECTS_THIS_IS_PROJECT_PREVIEW'); ?> <span><?php echo Lang::txt('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>"><?php echo Lang::txt('COM_PROJECTS_PROJECT_PAGE'); ?></a></span></p>
		</div>
	<?php } else if ($this->reviewer) { ?>
		<div id="project-preview">
			<p><?php echo Lang::txt('COM_PROJECTS_REVIEWER_PROJECT_PREVIEW'); ?> <span><?php echo Lang::txt('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&reviewer=' . $this->reviewer); ?>"><?php echo Lang::txt('COM_PROJECTS_PROJECT_LIST'); ?></a></span></p>
		</div>
	<?php } ?>

	<?php
	// Draw top header
	$this->view('_topheader')
	     ->set('model', $this->model)
	     ->set('publicView', true)
	     ->set('option', $this->option)
	     ->display();

	// Draw top menu
	$this->view('_topmenu', 'projects')
	     ->set('model', $this->model)
	     ->set('active', $this->active)
	     ->set('tabs', $this->tabs)
	     ->set('option', $this->option)
	     ->set('guest', User::isGuest())
	     ->set('publicView', true)
	     ->display();
	?>

	<section class="main section">
		<div class="project-inner-wrap">
			<?php if ($this->model->about('parsed')) { ?>
				<div class="public-list-header">
					<h3><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></h3>
				</div>
				<div class="public-list-wrap">
					<?php echo $this->model->about('parsed'); ?>
				</div>
			<?php } ?>
			<?php
			// Side blocks from plugins?
			$sections = Event::trigger('projects.onProjectPublicList', array($this->model));

			if (!empty($sections))
			{
				foreach ($sections as $section)
				{
					echo !empty($section) ? $section : NULL;
				}
			}
			?>
		</div>
	</section><!-- / .main section -->
</div>