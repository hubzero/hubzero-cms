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

$database = \App::get('db');

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" id="resourcesform" method="get">
	<section class="main section">
		<div class="subject">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
				<fieldset class="entry-search">
					<legend></legend>
					<label for="entry-search-field"><?php echo Lang::txt('Enter keyword or phrase'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="tag" value="<?php echo $this->escape($this->filters['tag']); ?>" />
				</fieldset>
				<?php if ($this->filters['tag']) { ?>
					<fieldset class="applied-tags">
						<ol class="tags">
						<?php
						$url  = 'index.php?option=' . $this->option . '&task=browse';
						$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
						$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
						$url .= ($this->filters['category']   ? '&category=' . $this->escape($this->filters['category'])     : '');

						$rt = new \Components\Publications\Helpers\Tags($database);
						$tags = $rt->parseTopTags($this->filters['tag']);
						foreach ($tags as $tag)
						{
							?>
							<li>
								<a href="<?php echo Route::url($url . '&tag=' . implode(',', $rt->parseTopTags($this->filters['tag'], $tag))); ?>">
									<?php echo $this->escape(stripslashes($tag)); ?>
									<span class="remove">x</a>
								</a>
							</li>
							<?php
						}
						?>
						</ol>
					</fieldset>
				<?php } ?>
			</div><!-- / .container -->
			<?php if (isset($this->filters['tag_ignored']) && count($this->filters['tag_ignored']) > 0) { ?>
				<div class="warning">
					<p><?php echo Lang::txt('Searching only allows up to 5 tags. The following tags were ignored:'); ?></p>
					<ol class="tags">
					<?php
					$url  = 'index.php?option=' . $this->option . '&task=browse';
					$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
					$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
					$url .= ($this->filters['type']   ? '&category=' . $this->escape($this->filters['category'])     : '');

					foreach ($this->filters['tag_ignored'] as $tag)
					{
						?>
						<li>
							<a href="<?php echo Route::url($url . '&tag=' . $tag); ?>">
								<?php echo $this->escape(stripslashes($tag)); ?>
							</a>
						</li>
						<?php
					}
					?>
					</ol>
				</div>
			<?php } ?>
			<div class="container">
			<nav class="entries-filters">
				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				$qs .= ($this->filters['category']   ? '&category=' . $this->escape($this->filters['category'])     : '');
				$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
				?>
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=title' . $qs); ?>" title="<?php echo Lang::txt('Sort by title'); ?>">&darr; <?php echo Lang::txt('Title'); ?></a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'date') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=date' . $qs); ?>" title="<?php echo Lang::txt('Sort by date published'); ?>">&darr; <?php echo Lang::txt('Published'); ?></a></li>
					<?php if ($this->config->get('show_ranking')) { ?>
					<li><a<?php echo ($this->filters['sortby'] == 'ranking') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=ranking' . $qs); ?>" title="<?php echo Lang::txt('Sort by ranking'); ?>">&darr; <?php echo Lang::txt('Ranking'); ?></a></li>
					<?php } ?>
				</ul>
				<?php if (count($this->categories) > 0) { ?>
					<ul class="entries-menu filter-options">
						<li>
							<label for="filter-type"><?php echo Lang::txt('Category'); ?></label>
							<select name="category" id="filter-type">
								<option value="" <?php echo (!$this->filters['category']) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('All Categories'); ?></option>
								<?php foreach ($this->categories as $item) { ?>
									<option value="<?php echo $item->id; ?>"<?php echo ($this->filters['category'] == $item->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($item->name)); ?></option>
								<?php } ?>
							</select>
						</li>
					</ul>
				<?php } ?>
			</nav>

			<?php
			if ($this->results && $this->results->count() > 0)
			{
				// Display List of items
				$this->view('_list')
				     ->set('results', $this->results)
				     ->set('filters', $this->filters)
				     ->set('config', $this->config)
				     ->display();

				echo '<div class="clear"></div>';
			} else { ?>
				<p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_NO_RESULTS'); ?></p>
			<?php } ?>

			<?php

			$this->pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
			$this->pageNav->setAdditionalUrlParam('category', $this->filters['category']);
			$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

			echo $this->pageNav->render();
			?>
			<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="aside">
			<div class="container">
				<h3>Popular Tags</h3>
				<?php
				$rt = new \Components\Publications\Helpers\Tags($database);
				echo $rt->getTopTagCloud(20, $this->filters['tag']);
				?>
				<p><?php echo Lang::txt('Click a tag to see only publications with that tag.'); ?></p>
			</div>
		</div><!-- / .aside -->
	</section><!-- / .main section -->
</form>