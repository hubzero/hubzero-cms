<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-main main-page btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_KB_MAIN'); ?></a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
		<div class="subject">
			<form action="<?php echo Route::url($this->category->link()); ?>" method="post">

				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_KB_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<legend><?php echo Lang::txt('COM_KB_SEARCH_LEGEND'); ?></legend>
						<label for="entry-search-field"><?php echo Lang::txt('COM_KB_SEARCH_LABEL'); ?></label>
						<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_KB_SEARCH_PLACEHOLDER'); ?>" />
						<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
						<input type="hidden" name="section" value="<?php echo $this->escape($this->category->get('alias')); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					</fieldset>
				</div><!-- / .container -->

				<div class="container">
					<ul class="entries-menu">
						<li>
							<a<?php echo ($this->filters['sort'] == 'popularity') ? ' class="active"' : ''; ?> href="<?php echo Route::url($this->category->link() . '&sort=popularity'); ?>" title="<?php echo Lang::txt('COM_KB_SORT_BY_POPULAR'); ?>">
								<?php echo Lang::txt('COM_KB_SORT_POPULAR'); ?>
							</a>
						</li>
						<li>
							<a<?php echo ($this->filters['sort'] == 'recent') ? ' class="active"' : ''; ?> href="<?php echo Route::url($this->category->link() . '&sort=recent'); ?>" title="<?php echo Lang::txt('COM_KB_SORT_BY_RECENT'); ?>">
								<?php echo Lang::txt('COM_KB_SORT_RECENT'); ?>
							</a>
						</li>
					</ul>

					<table class="articles entries">
						<caption>
							<?php
							$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
							$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
							if ($this->filters['search'] != '')
							{
								echo Lang::txt('COM_KB_SEARCH_FOR_IN', $this->filters['search'], $this->escape(stripslashes($this->category->get('title'))));
							} else {
								echo $this->escape(stripslashes($this->category->get('title')));
							} ?>
							<span>(<?php echo Lang::txt('COM_KB_NUM_OF_TOTAL', $s . '-' . $e, $this->total); ?>)</span>
						</caption>
						<tbody>
						<?php foreach ($this->articles as $row) { ?>
							<tr>
								<th>
									<span class="entry-id"><?php echo $row->get('id'); ?></span>
								</th>
								<td>
									<a class="entry-title" href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(stripslashes($row->get('title'))); ?></a><br />
									<span class="entry-details">
										<?php if ($this->catid <= 0) { echo Lang::txt('COM_KB_IN_CATEGORY', $this->escape(stripslashes($row->get('ctitle')))); } ?>
										<?php echo Lang::txt('COM_KB_LAST_MODIFIED'); ?>
										<span class="entry-time-at"><?php echo Lang::txt('COM_KB_DATETIME_AT'); ?></span>
										<span class="entry-time"><?php echo $row->modified('time'); ?></span>
										<span class="entry-date-on"><?php echo Lang::txt('COM_KB_DATETIME_ON'); ?></span>
										<span class="entry-date"><?php echo $row->modified('date'); ?></span>
									</span>
								</td>
								<td class="voting">
									<?php
									$view = $this->view('_vote')
											 ->set('option', $this->option)
											 ->set('item', $row)
											 ->set('type', 'entry')
											 ->set('vote', '')
											 ->set('id', '');
									if (!User::isGuest())
									{
										if ($row->get('user_id') == User::get('id'))
										{
											$view->set('vote', $row->get('vote'));
											$view->set('id', $row->get('id'));
										}
									}
									$view->display();
									?>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<?php
					// Initiate paging
					$pageNav = $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
					$pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
					echo $pageNav->render();
					?>
					<div class="clearfix"></div>
				</div><!-- / .container -->
			</form>
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_KB_CATEGORIES'); ?></h3>
				<ul class="categories">
					<li>
						<a<?php if ($this->catid <= 0) { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&section=all'); ?>">
							<?php echo Lang::txt('COM_KB_ALL_ARTICLES'); ?>
						</a>
					</li>
				<?php foreach ($this->categories as $row) { ?>
					<li>
						<a <?php if ($this->catid == $row->get('id')) { echo 'class="active" '; } ?> href="<?php echo Route::url($row->link()); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?> <span class="item-count"><?php echo $row->get('articles', 0); ?></span>
						</a>
					<?php if ($row->children('count') > 0 && $this->catid == $row->get('id')) { ?>
						<ul class="categories">
						<?php foreach ($row->children() as $cat) { ?>
							<li>
								<a <?php if ($this->catid  == $cat->get('id')) { echo 'class="active" '; } ?> href="<?php echo Route::url($cat->link()); ?>">
									<?php echo $this->escape(stripslashes($cat->get('title'))); ?> <span class="item-count"><?php echo $cat->get('articles', 0); ?></span>
								</a>
							</li>
						<?php } ?>
						</ul>
					<?php } ?>
					</li>
				<?php } ?>
				</ul>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
