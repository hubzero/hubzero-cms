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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<?php if ($this->params->get('access-view-comment')) { ?>
	<section class="below section">
		<div class="section-inner">
			<div class="thread">

				<?php if ($this->params->get('access-create-comment')) { ?>
					<h3 class="post-comment-title" id="post-comment">
						<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_A_COMMENT'); ?>
					</h3>
					<form method="post" action="<?php echo Route::url($this->url); ?>" id="commentform" enctype="multipart/form-data">
						<p class="comment-member-photo">
							<img src="<?php echo User::picture(); ?>" alt="" />
						</p>
						<fieldset>
							<label for="commentcontent">
								<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_YOUR_COMMENTS'); ?>:
								<?php
								if (!User::isGuest())
								{
									echo $this->editor('comment[content]', '', 35, 5, 'commentcontent', array('class' => 'minimal no-footer'));
								}
								?>
							</label>

							<div class="file-inputs">
								<label for="comment_file">
									<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_ATTACH_FILE'); ?>
									<input type="file" name="comment_file" id="comment_file" />
								</label>
								<a href="#" class="detach_file" style="display: none;"></a>
							</div>

							<label id="comment-anonymous-label">
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
								<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_ANONYMOUSLY'); ?>
							</label>

							<p class="submit">
								<input type="submit" class="btn btn-success" name="submit" value="<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_COMMENT'); ?>" />
							</p>

							<input type="hidden" name="comment[id]" value />
							<input type="hidden" name="comment[item_id]" value="<?php echo $this->obj_id; ?>" />
							<input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
							<input type="hidden" name="comment[parent]" value="0" />
							<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape(User::get('id')); ?>" />
							<input type="hidden" name="comment[state]" value="1" />
							<input type="hidden" name="comment[access]" value="1" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="id" value="<?php echo $this->obj->get('id'); ?>" />
							<input type="hidden" name="v" value="<?php echo $this->obj->get('version_number'); ?>" />
							<input type="hidden" name="active" value="comments" />
							<input type="hidden" name="action" value="commentsave" />
							<input type="hidden" name="no_html" value="1" />

							<?php echo Html::input('token'); ?>

							<div class="sidenote">
								<p>
									<strong><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_KEEP_RELEVANT'); ?></strong>
								</p>
							</div>
						</fieldset>
					</form>
				<?php } ?>

				<?php if ($this->params->get('comments_locked', 0) == 1) { ?>
					<p class="info"><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_LOCKED'); ?></p>
				<?php } ?>

				<div class="container">
					<h3 class="post-comment-title">
						<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS'); ?>
					</h3>
					<nav class="entries-filters">
						<ul class="entries-menu order-options">
							<li><a<?php echo ($this->sortby == 'likes') ? ' class="active"' : ''; ?> data-url="<?php echo Route::url($this->obj->link('comments')) . '?sortby=likes'; ?>" title="<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_SORT_BY_LIKES'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_SORT_BY_LIKES'); ?></a></li>
							<li><a<?php echo ($this->sortby == 'created') ? ' class="active"' : ''; ?> data-url="<?php echo Route::url($this->obj->link('comments')) . '?sortby=created'; ?>"  title="<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_SORT_BY_DATE'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_SORT_BY_DATE'); ?></a></li>
						</ul>
					</nav>
				</div>
				<?php if ($this->comments->count()) {
					$this->view('list')
						->set('option', $this->option)
						->set('comments', $this->comments)
						->set('obj_type', $this->obj_type)
						->set('obj_id', $this->obj_id)
						->set('obj', $this->obj)
						->set('params', $this->params)
						->set('depth', $this->depth)
						->set('sortby', $this->sortby)
						->set('url', $this->url)
						->set('cls', 'odd')
						->display();
				} elseif ($this->depth <= 1) { ?>
					<div class="results-none">
						<p class="no-comments">
							<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_NO_COMMENTS'); ?>
						</p>
					</div>
				<?php } ?>

			</div><!-- / .thread -->
		</div>
	</section><!-- / .below section -->
<?php } else { ?>
	<p class="warning">
		<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_MUST_BE_LOGGED_IN'); ?>
	</p>
<?php } ?>