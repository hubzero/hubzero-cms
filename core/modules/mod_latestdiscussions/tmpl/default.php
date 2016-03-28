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

// no direct access
defined('_HZEXEC_') or die();

$c = 0;
?>
<div class="latest_discussions_module <?php echo $this->params->get('moduleclass_sfx'); ?>">
	<?php if (count($this->posts) > 0) : ?>
		<ul class="discussions">
			<?php foreach ($this->posts as $post) : ?>
					<?php
						if ($c >= $this->limit)
						{
							break;
						}
						$c++;

						$post->set('section', $this->categories[$post->get('category_id')]->section);
						$post->set('category', $this->categories[$post->get('category_id')]->alias);

						if ($post->get('scope_id') == 0) {
							$location = '<a href="' . Route::url('index.php?option=com_forum') . '">' . Lang::txt('MOD_LATESTDISCUSSIONS_SITE_FORUM') . '</a>';
						} else {
							$location = '<a href="' . Route::url('index.php?option=com_groups&cn=' . $post->get('group_alias')) . '">' . $this->escape(stripslashes($post->get('group_title'))) . '</a>';
						}
					?>
					<li>
						<h4>
							<a href="<?php echo Route::url($post->link()); ?>">
								<?php
								echo ($post->get('parent') && isset($this->threads[$post->get('parent')]))
									? $this->escape(stripslashes($this->threads[$post->get('parent')]))
									: $this->escape(stripslashes($post->get('title')));
								?>
							</a>
						</h4>
						<span class="discussion-author">
							<?php
								if ($post->get('anonymous')) {
									echo '<em>' . Lang::txt('MOD_LATESTDISCUSSIONS_ANONYMOUS') . '</em>';
								} else {
									echo '<a href="' . Route::url('index.php?option=com_members&id=' . $post->creator()->get('id')) . '">' . $this->escape(stripslashes($post->creator()->get('name'))) . '</a>';
								}
								echo ', in&nbsp;'
							?>
						</span>
						<span class="discussion-location">
							<?php echo $location; ?>
						</span>
						<span class="discussion-date">
							<time datetime="<?php echo $post->get('created'); ?>"><?php echo Lang::txt('MOD_LATESTDISCUSSIONS_AT_TIME_ON_DATE', $post->created('time'), $post->created('date')); ?></time>
						</span>
					<?php if ($this->charlimit > 0) : ?>
						<span class="discussion-comment">
							<?php echo \Hubzero\Utility\String::truncate(strip_tags($post->get('comment')), $this->charlimit); ?>
						</span>
					<?php endif; ?>
					</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p><?php echo Lang::txt('MOD_LATESTDISCUSSIONS_NO_RESULTS'); ?></p>
	<?php endif; ?>

	<?php if ($more = $this->params->get('morelink', '')) : ?>
		<p class="more">
			<a href="<?php echo $more; ?>">
				<?php echo Lang::txt('MOD_LATESTDISCUSSIONS_MORE_RESULTS'); ?>
			</a>
		</p>
	<?php endif; ?>

	<?php if ($this->params->get('feedlink', 'yes') == 'yes') : ?>
		<p>
			<a href="<?php echo Route::url('index.php?option=com_forum&task=latest.rss', true, -1); ?>" class="newsfeed">
				<?php echo Lang::txt('MOD_LATESTDISCUSSIONS_FEED'); ?>
			</a>
		</p>
	<?php endif; ?>
</div><!-- / #latest_discussions_module -->