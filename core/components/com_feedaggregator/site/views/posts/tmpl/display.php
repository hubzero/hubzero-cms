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

if (isset($this->filters['filterby']) != TRUE)
{
	$this->filters['filterby'] = 'all';
}

$this->js('posts')
     ->css('posts');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a href="#feedbox" id="generateFeed" class="fancybox-inline icon-feed btn"><?php echo Lang::txt('COM_FEEDAGGREGATOR_GENERATE_FEED'); ?></a>
			</li>
			<li>
				<a class="icon-download btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&task=RetrieveNewPosts'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_GET_NEW_POSTS'); ?></a>
			</li>
			<li>
				<a class="icon-browse btn" href="<?php echo Route::url('index.php?option='. $this->option . '&controller=feeds'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_VIEW_FEEDS'); ?></a>
			</li>
			<li class="last">
				<a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_ADD_FEED'); ?></a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
		<div id="page-main">

			<?php if (count($this->messages) > 0) : ?>
				<?php foreach ($this->messages as $message) : ?>
					<p class="<?php echo $message['type']; ?>"><?php echo $message['message']; ?></p>
				<?php endforeach; ?>
			<?php endif; ?>

		<?php if ($this->posts->count() > 0):?>
			<div class="container">
				<nav class="entries-filters">
					<ul class="entries-menu filter-options">
						<li><a class="filter-all<?php if ($this->filters['filterby'] == 'all') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&filterby=all'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_FILTER_ALL'); ?></a></li>
						<li><a class="filter-all<?php if ($this->filters['filterby'] == 'new') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&filterby=new'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_FILTER_NEW'); ?></a></li>
						<li><a class="filter-all<?php if ($this->filters['filterby'] == 'review') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&filterby=review'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_FILTER_REVIEW'); ?></a></li>
						<li><a class="filter-all<?php if ($this->filters['filterby'] == 'approved') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&filterby=approved'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_FILTER_APPROVED'); ?></a></li>
						<li><a class="filter-all<?php if ($this->filters['filterby'] == 'removed') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&filterby=removed'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_FILTER_REMOVED'); ?></a></li>
					</ul>
				</nav>

				<table class="ideas entries feedtable">
					<caption><?php echo Lang::txt('COM_FEEDAGGREGATOR_SHOWING_POSTS', $this->filters['filterby']); ?></caption>
					<tbody>
					<?php foreach ($this->posts as $post): ?>
						<?php if (($post->status != "removed" AND $this->filters['filterby'] != "removed") OR
								($post->status == "removed" AND $this->filters['filterby'] == "removed") OR
								($this->task == "PostsById")): ?>
						<tr id="row-<?php echo $post->id; ?>">
							<td><a class="fancybox-inline" rel="group1" href="#content-fancybox<?php echo $post->id; ?>"><?php echo (string) html_entity_decode(strip_tags($post->shortTitle)); ?></a></td>
							<td><?php echo $post->created; ?>
							<td><?php echo $post->name;?></td>
							<td id="status-<?php echo $post->id; ?>">
								<?php if ($post->status == "under review")
								{
									echo '<div class="review-status">';
									echo $post->status;
									echo '</div>';
								}
								else if ($post->status == "approved")
								{
									echo '<div class="approve-status">';
									echo $post->status;
									echo '</div>';
								}
								else if ($post->status == "new")
								{
									echo '<b>';
									echo $post->status;
									echo '</b>';
								}
								else if ($post->status == "removed")
								{
									echo '<div class="remove-status">';
									echo $post->status;
									echo '</div>';
								}
								?>
							</td>
							<td>
								<input type="button" data-id="<?php echo $post->id; ?>" data-action="approve" class="btn-success btn actionBtn <?php echo 'btnGrp' . $post->id; ?>" value="<?php echo Lang::txt('COM_FEEDAGGREGATOR_APPROVE'); ?>" id="approve-<?php echo $post->id;?>" <?php echo ($post->status == 'approved' ? 'disabled' : ''); ?> />
								<input type="button" data-id="<?php echo $post->id; ?>" data-action="mark" class="btn-review btn actionBtn <?php echo 'btnGrp' . $post->id; ?>" value="<?php echo Lang::txt('COM_FEEDAGGREGATOR_MARK_FOR_REVIEW'); ?>" id="mark-<?php echo $post->id; ?>" <?php echo ($post->status == 'under review' ? 'disabled' : ''); ?> />
								<input type="button" data-id="<?php echo $post->id; ?>" data-action="remove" class="btn-danger btn actionBtn <?php echo 'btnGrp' . $post->id; ?>" value="<?php echo Lang::txt('COM_FEEDAGGREGATOR_REMOVE'); ?>" id="remove-<?php echo $post->id;?>" <?php echo ($post->status == 'removed' ? 'disabled' : ''); ?> />
								<div class="postpreview-container">
									<div class="postpreview" id="content-fancybox<?php echo $post->id; ?>">
										<h1><?php echo (string) html_entity_decode(strip_tags($post->title)); ?></h1>
										<p class="description"><?php echo (string) html_entity_decode(strip_tags($post->description)); ?></p>
										<p><a target="_blank" href="<?php echo urldecode($post->url); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_LINK_ORIGINAL_POST'); ?></a></p>
										<div class="button-container">
											<hr />
											<input type="button" data-id="<?php echo $post->id; ?>" data-action="approve" class="btn-success btn actionBtn <?php echo 'btnGrp' . $post->id; ?> " value="<?php echo Lang::txt('COM_FEEDAGGREGATOR_APPROVE'); ?>" id="approve-prev-<?php echo $post->id;?>" <?php echo ($post->status == 'approved' ? 'disabled' : ''); ?> />
											<input type="button" data-id="<?php echo $post->id; ?>" data-action="mark" class="btn-review btn actionBtn <?php echo 'btnGrp' . $post->id; ?> " value="<?php echo Lang::txt('COM_FEEDAGGREGATOR_MARK_FOR_REVIEW'); ?>" id="mark-prev-<?php echo $post->id;?>" <?php echo ($post->status == 'under review' ? 'disabled' : ''); ?> />
											<input type="button" data-id="<?php echo $post->id; ?>" data-action="remove" class="btn-danger btn actionBtn <?php echo 'btnGrp' . $post->id; ?> " value="<?php echo Lang::txt('COM_FEEDAGGREGATOR_REMOVE'); ?>" id="remove-prev-<?php echo $post->id;?>" <?php echo ($post->status == 'removed' ? 'disabled' : ''); ?> />
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php endif; ?>
					<?php endforeach; //end foreach ?>
					</tbody>
				</table>
			</div> <!--  / .container  -->

			<?php
			if ($this->fromfeed != TRUE)
			{
				// Initiate paging
				$pageNav = $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);

				echo $pageNav->render();
			}
			?>
		<?php elseif ($this->filters['filterby'] == 'all' OR $this->filters['filterby'] == 'new') : ?>
			<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_NO_POSTS'); ?></p>
			<p><a class="icon-add add btn" href="<?php echo Route::url('index.php?option='. $this->option . '&controller=posts&task=RetrieveNewPosts'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_GET_NEW_POSTS'); ?></a></p>
		<?php else: ?>
			<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_NEED_TO_REVIEW_POSTS'); ?></p>
			<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&filterby=new'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_VIEW_NEW_POSTS'); ?></a></p>
		<?php endif; ?>

			<!--  Generate Feed -->
			<div class="postpreview-container">
				<div class="postpreview" id="feedbox">
					<h2><?php echo Lang::txt('COM_FEEDAGGREGATOR_GENERATE_HEADER'); ?></h2>
					<p><?php echo Lang::txt('COM_FEEDAGGREGATOR_GENERATE_INSTRUCTIONS'); ?></p>
					<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&task=generateFeed'); ?>">
					<?php echo rtrim(Request::base(), '/') . Route::url('index.php?option=' . $this->option . '&controller=posts&task=generateFeed'); ?></a>
				</div>
			</div>
		</div> <!--  main page -->
	</form>
</section><!-- /.main section -->