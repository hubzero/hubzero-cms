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

$base  = 'index.php?option=' . $this->option;
$mode  = Request::getWord('mode', 'grid');

// This needs to be called to ensure scripts are pushed to the document
if (!User::isGuest())
{
	$foo = $this->editor('description', '', 35, 5, 'field_description', array('class' => 'minimal no-footer'));
}

$this->css()
     ->js('jquery.masonry')
     ->js('jquery.infinitescroll')
     ->js();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_COLLECTIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
				<span><?php echo Lang::txt('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
			</a>
		</p>
	</div>
</header>

<form method="get" action="<?php echo Route::url($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<?php
	$this->view('_submenu')
	     ->set('option', $this->option)
	     ->set('active', 'posts')
	     ->set('collections', $this->collections)
	     ->set('posts', $this->total)
	     ->display();
	?>

	<section class="section filters">
		<field class="input-group">
			<span class="input-cell">
				<label for="filter-search">
					<span><?php echo Lang::txt('COM_COLLECTIONS_SEARCH_LABEL'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COLLECTIONS_SEARCH_PLACEHOLDER'); ?>" />
				</label>
			</span>
			<span class="input-cell">
				<input type="submit" class="btn" value="<?php echo Lang::txt('COM_COLLECTIONS_GO'); ?>" />
			</span>
		</fieldset>
	</section>

	<section class="main section">
		<?php if ($this->rows->total() > 0) { ?>
			<div id="posts" data-base="<?php echo Request::base(true); ?>" class="view-as <?php echo $mode . ' ' . (User::isGuest() ? 'loggedout' : 'loggedin'); ?>">
				<?php if (!User::isGuest() && !Request::getInt('no_html', 0)) { ?>
					<div class="post new-post">
						<a class="icon-add add" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=collections&task=post/new'); ?>">
							<?php echo Lang::txt('COM_COLLECTIONS_NEW_POST'); ?>
						</a>
					</div>
				<?php } ?>
				<?php
					foreach ($this->rows as $row)
					{
						$item = $row->item();
				?>
					<div class="post <?php echo $item->type(); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&controller=posts&post=' . $row->get('id')); ?>">
						<div class="content">
							<?php
								$this->view('display_' . $item->type(), 'posts')
								     ->set('option', $this->option)
								     ->set('params', $this->config)
								     ->set('row', $row)
								     ->display();
							?>
							<?php if ($tags = $item->tags('cloud')) { ?>
								<div class="tags-wrap">
									<?php echo $tags; ?>
								</div>
							<?php } ?>
							<div class="meta" data-metadata-url="<?php echo Route::url('index.php?option=com_collections&controller=posts&task=metadata&post=' . $row->get('id')); ?>">
								<p class="stats">
									<span class="likes">
										<?php echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
									</span>
									<span class="comments">
										<?php echo Lang::txt('COM_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)); ?>
									</span>
									<span class="reposts">
										<?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)); ?>
									</span>
								</p>
								<div class="actions">
									<?php if (!User::isGuest()) { ?>
										<?php if ($row->get('created_by') == User::get('id')) { ?>
											<a class="btn edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=edit'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_EDIT'); ?></span>
											</a>
										<?php } else { ?>
											<a class="btn vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $item->get('id'); ?>" data-text-like="<?php echo Lang::txt('COM_COLLECTIONS_LIKE'); ?>" data-text-unlike="<?php echo Lang::txt('COM_COLLECTIONS_UNLIKE'); ?>" href="<?php echo Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=vote'); ?>">
												<span><?php echo ($item->get('voted')) ? Lang::txt('COM_COLLECTIONS_UNLIKE') : Lang::txt('COM_COLLECTIONS_LIKE'); ?></span>
											</a>
										<?php } ?>
											<a class="btn comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=comment'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_COMMENT'); ?></span>
											</a>
											<a class="btn repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=collect'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
											</a>
											<a class="btn" href="https://www.facebook.com/sharer/sharer.php?u&amp;src=sdkpreparse">Share</a>
											<?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
											<a class="btn" href="<?php echo 'https://twitter.com/intent/tweet?hashtags=HUBzero&text=HUBzero&url='. $actual_link; ?>">Tweet</a>
									<?php } else { ?>
											<a class="btn vote like tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&controller=' . $this->controller . '&task=' . $this->task, false, true)), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_LIKE'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_LIKE'); ?></span>
											</a>
											<a class="btn comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=comment'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_COMMENT'); ?></span>
											</a>
											<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&controller=' . $this->controller . '&task=' . $this->task, false, true)), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
											</a>
									<?php } ?>
								</div><!-- / .actions -->
							</div><!-- / .meta -->
							<div class="convo attribution">
								<?php
								$name = $this->escape(stripslashes($row->creator()->get('name')));

								if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
									<a href="<?php echo Route::url($row->creator()->link() . '&active=collections'); ?>" title="<?php echo $name; ?>" class="img-link">
										<img src="<?php echo $row->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
									</a>
								<?php } else { ?>
									<span class="img-link">
										<img src="<?php echo $row->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
									</span>
								<?php } ?>
								<p>
									<?php
									$who = $name;
									if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels()))
									{
										$who = '<a href="' . Route::url($row->creator()->link() . '&active=collections') . '">' . $name . '</a>';
									}

									$where = '<a href="' . Route::url($row->link()) . '">' . $this->escape(stripslashes($row->get('title'))) . '</a>';

									echo Lang::txt('COM_COLLECTIONS_ONTO', $who, $where);
									?>
									<br />
									<span class="entry-date">
										<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
										<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
										<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
										<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
									</span>
								</p>
							</div><!-- / .attribution -->
						</div><!-- / .content -->
					</div><!-- / .post -->
				<?php } ?>
			</div><!-- / #posts -->
			<?php
			if ($this->total > $this->filters['limit'])
			{
				// Initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
			}
			?>
			<div class="clear"></div>
		<?php } else { ?>
			<div id="collection-introduction">
				<?php if ($this->config->get('access-create-post')) { ?>
					<div class="instructions">
						<ol>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP1'); ?></li>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP2'); ?></li>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP3'); ?></li>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP4'); ?></li>
						</ol>
					</div>
				<?php } else { ?>
					<div class="instructions">
						<p><?php echo Lang::txt('COM_COLLECTIONS_NO_POSTS_FOUND'); ?></p>
					</div>
				<?php } ?>
			</div><!-- / #collections-introduction -->
		<?php } ?>
	</section><!-- / .main section -->
</form>
