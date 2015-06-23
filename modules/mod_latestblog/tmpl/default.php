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

// no direct access
defined('_HZEXEC_') or die();

$c = 0;
?>
<div class="latest_discussions_module <?php echo $this->params->get('moduleclass_sfx'); ?>">
	<?php if (count($this->posts) > 0) : ?>
		<ul class="blog-entries">
		<?php 
		foreach ($this->posts as $post)
		{
			if ($c < $this->limit)
			{
				?>
					<li>
						<?php if ($this->params->get('details', 1)) { ?>
						<p class="entry-author-photo">
							<img src="<?php echo $post->creator()->getPicture(); ?>" alt="" />
						</p>
						<?php } ?>
						<div class="entry-content">
							<h4>
								<a href="<?php echo Route::url($post->link()); ?>"><?php echo $this->escape(stripslashes($post->get('title'))); ?></a>
							</h4>
						<?php if ($this->params->get('details', 1)) { ?>
							<dl class="entry-meta">
								<dt>
									<span>
										<?php echo Lang::txt('MOD_LATESTBLOG_ENTRY_NUMBER', $post->get('id')); ?>
									</span>
								</dt>
								<dd class="date">
									<time datetime="<?php echo $post->published(); ?>">
										<?php echo $post->published('date'); ?>
									</time>
								</dd>
								<dd class="time">
									<time datetime="<?php echo $post->published(); ?>">
										<?php echo $post->published('time'); ?>
									</time>
								</dd>
								<dd class="author">
									<a href="<?php echo Route::url('index.php?option=com_members&id=' . $post->get('created_by')); ?>">
										<?php echo $this->escape(stripslashes($post->creator('name'))); ?>
									</a>
								</dd>
								<dd class="location">
									<a href="<?php echo $post->link('base'); ?>">
										<?php 
										switch ($post->get('scope'))
										{
											case 'site':
												echo Lang::txt('MOD_LATESTBLOG_LOCATION_BLOG_SITE');
											break;

											case 'member':
												echo Lang::txt('MOD_LATESTBLOG_LOCATION_BLOG_MEMBER');
											break;

											case 'group':
												echo $this->escape(stripslashes($post->item('title')));
											break;
										}
										?>
									</a>
								</dd>
							</dl>
						<?php if ($this->params->get('preview', 1)) { ?>
						<?php } ?>
							<div class="entry-body">
								<?php 
								if ($this->pullout && $c == 0)
								{
									echo $post->content('clean', $this->params->get('pulloutlimit', 500));
								}
								else
								{
									echo $post->content('clean', $this->params->get('charlimit', 100));
								}
								?>
							</div>
						<?php } ?>
						</div>
					</li>
				<?php 
			}
			$c++;
		}
		?>
		</ul>
	<?php else : ?>
		<p><?php echo Lang::txt('MOD_LATESTBLOG_NO_RESULTS'); ?></p>
	<?php endif; ?>
	
	<?php if ($more = $this->params->get('morelink', '')) : ?>
		<p class="more">
			<a href="<?php echo $more; ?>"><?php echo Lang::txt('MOD_LATESTBLOG_MORE_RESULTS'); ?></a>
		</p>
	<?php endif; ?>
</div>