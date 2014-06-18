<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
							$location = '<a href="' . JRoute::_('index.php?option=com_forum') . '">' . JText::_('MOD_LATESTDISCUSSIONS_SITE_FORUM') . '</a>';
						} else {
							$location = '<a href="' . JRoute::_('index.php?option=com_groups&cn=' . $post->get('group_alias')) . '">' . $this->escape(stripslashes($post->get('group_title'))) . '</a>';
						}
					?>
					<li>
						<h4>
							<a href="<?php echo JRoute::_($post->link()); ?>">
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
									echo '<em>' . JText::_('MOD_LATESTDISCUSSIONS_ANONYMOUS') . '</em>';
								} else {
									echo '<a href="' . JRoute::_('index.php?option=com_members&id=' . $post->creator('id')) . '">' . $this->escape(stripslashes($post->creator('name'))) . '</a>';
								}
								echo ', in&nbsp;'
							?>
						</span>
						<span class="discussion-location">
							<?php echo $location; ?>
						</span>
						<span class="discussion-date">
							<time datetime="<?php echo $post->get('created'); ?>"><?php echo JText::sprintf('MOD_LATESTDISCUSSIONS_AT_TIME_ON_DATE', $post->created('time'), $post->created('date')); ?></time>
						</span>
					<?php if ($this->charlimit > 0) : ?>
						<span class="discussion-comment">
							<?php echo $post->content('clean', $this->charlimit); ?>
						</span>
					<?php endif; ?>
					</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p><?php echo JText::_('MOD_LATESTDISCUSSIONS_NO_RESULTS'); ?></p>
	<?php endif; ?>

	<?php if ($more = $this->params->get('morelink', '')) : ?>
		<p class="more">
			<a href="<?php echo $more; ?>">
				<?php echo JText::_('MOD_LATESTDISCUSSIONS_MORE_RESULTS'); ?>
			</a>
		</p>
	<?php endif; ?>

	<?php if ($this->params->get('feedlink', 'yes') == 'yes') : ?>
		<p>
			<a href="<?php echo JRoute::_('index.php?option=com_forum&task=latest.rss', true, -1); ?>" class="newsfeed">
				<?php echo JText::_('MOD_LATESTDISCUSSIONS_FEED'); ?>
			</a>
		</p>
	<?php endif; ?>
</div><!-- / #latest_discussions_module -->