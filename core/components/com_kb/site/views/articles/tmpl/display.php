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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
	<?php } ?>
		<div class="subject">
			<form action="<?php echo Route::url('index.php?option=' . $this->option . '&section=all'); ?>" method="get">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_KB_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<legend><?php echo Lang::txt('COM_KB_SEARCH_LEGEND'); ?></legend>
						<label for="entry-search-field"><?php echo Lang::txt('COM_KB_SEARCH_LABEL'); ?></label>
						<input type="text" name="search" id="entry-search-field" value="" placeholder="<?php echo Lang::txt('COM_KB_SEARCH_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- / .container -->

				<div class="container">
					<div class="container-block">
						<h3>Articles</h3>
						<div class="grid">
							<div class="col span-half">
								<h4>
									<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=article&section=all&order=popularity'); ?>">
										<?php echo Lang::txt('COM_KB_POPULAR_ARTICLES'); ?> <span class="more">&raquo;</span>
									</a>
								</h4>
							<?php
							$popular = $this->archive->articles('popular', array('limit' => 5, 'access' => (User::isGuest() ? 0 : -1)));
							if ($popular->total() > 0) { ?>
								<ul class="articles">
								<?php foreach ($popular as $row) { ?>
									<li>
										<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo Lang::txt('COM_KB_READ_ARTICLE'); ?>">
											<?php echo $this->escape(stripslashes($row->get('title'))); ?>
										</a>
									</li>
								<?php } ?>
								</ul>
							<?php } else { ?>
								<p><?php echo Lang::txt('COM_KB_NO_ARTICLES'); ?></p>
							<?php } ?>
							</div><!-- / .col span-half -->
							<div class="col span-half omega">
								<h4>
									<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=article&section=all&order=recent'); ?>">
										<?php echo Lang::txt('COM_KB_RECENT_ARTICLES'); ?> <span class="more">&raquo;</span>
									</a>
								</h4>
							<?php
							$recent = $this->archive->articles('recent', array('limit' => 5, 'access' => (User::isGuest() ? 0 : -1)));
							if ($recent->total() > 0) { ?>
								<ul class="articles">
								<?php foreach ($recent as $row) { ?>
									<li>
										<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo Lang::txt('COM_KB_READ_ARTICLE'); ?>">
											<?php echo $this->escape(stripslashes($row->get('title'))); ?>
										</a>
									</li>
								<?php } ?>
								</ul>
							<?php } else { ?>
								<p><?php echo Lang::txt('COM_KB_NO_ARTICLES'); ?></p>
							<?php } ?>
							</div><!-- / .col span-half -->
						</div><!-- / .grid -->

						<h3><?php echo Lang::txt('COM_KB_CATEGORIES'); ?></h3>
						<div class="grid">
						<?php
							$i = 0;
							$filters = array(
								'limit'    => Request::getInt('limit', 3),
								'start'    => Request::getInt('limitstart', 0),
								'order'    => Request::getWord('order', 'recent'),
								'category' => 0,
								'state'    => 1,
								'access'   => (User::isGuest() ? 0 : -1)
							);
							foreach ($this->archive->categories('list', array('sort' => 'title', 'sort_Dir' => 'ASC')) as $row)
							{
								$i++;
								switch ($i)
								{
									case 1: $cls = ''; break;
									case 2: $cls = ' omega'; break;
								}
								$filters['section'] = $row->get('id');
								$articles = $row->articles('list', $filters);
								?>
							<div class="col span-half<?php echo $cls; ?>">
								<h4>
									<a href="<?php echo Route::url($row->link()); ?>">
										<?php echo $this->escape(stripslashes($row->get('title'))); ?> <span>(<?php echo $row->get('articles', 0); ?>)</span> <span class="more">&raquo;</span>
									</a>
								</h4>
							<?php if ($articles->total() > 0) { ?>
								<ul class="articles">
								<?php foreach ($articles as $article) { ?>
									<li>
										<a href="<?php echo Route::url($article->link()); ?>">
											<?php echo $this->escape(stripslashes($article->get('title'))); ?>
										</a>
									</li>
								<?php } ?>
								</ul>
							<?php } else { ?>
								<p><?php echo Lang::txt('COM_KB_NO_ARTICLES'); ?></p>
							<?php } ?>
							</div><!-- / .col span-half <?php echo $cls; ?> -->
							<?php //echo ($i >= 2) ? '<div class="clearfix"></div>' : ''; ?>
								<?php
								if ($i >= 2)
								{
									$i = 0;
								}
							}
						?>
						</div><!-- / .grid -->
					</div><!-- / .container-block -->
				</div><!-- / .container -->
			</form>
		</div><!-- / .subject -->

		<aside class="aside">
		<?php if (Component::isEnabled('com_answers')) { ?>
			<div class="container">
				<h3><?php echo Lang::txt('COM_KB_COMMUNITY'); ?></h3>
				<p>
					<?php echo Lang::txt('COM_KB_COMMUNITY_CANT_FIND'); ?> <?php echo Lang::txt('COM_KB_COMMUNITY_TRY_ANSWERS', '<a href="' . Route::url('index.php?option=com_answers') . '">' . Lang::txt('COM_ANSWERS') . '</a>'); ?>
				</p>
			</div><!-- / .container -->
		<?php } ?>
		<?php if (Component::isEnabled('com_wishlist')) { ?>
			<div class="container">
				<h3><?php echo Lang::txt('COM_KB_FEATURE_REQUEST'); ?></h3>
				<p>
					<?php echo Lang::txt('COM_KB_HAVE_A_FEATURE_REQUEST'); ?> <a href="<?php echo Route::url('index.php?option=com_wishlist'); ?>"><?php echo Lang::txt('COM_KB_FEATURE_TELL_US'); ?></a>
				</p>
			</div><!-- / .container -->
		<?php } ?>
		<?php if (Component::isEnabled('com_support')) { ?>
			<div class="container">
				<h3><?php echo Lang::txt('COM_KB_TROUBLE_REPORT'); ?></h3>
				<p>
					<?php echo Lang::txt('COM_KB_TROUBLE_FOUND_BUG'); ?> <a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=new'); ?>"><?php echo Lang::txt('COM_KB_TROUBLE_TELL_US'); ?></a>
				</p>
			</div><!-- / .container -->
		<?php } ?>
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</div><!-- / .main section -->
