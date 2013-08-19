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

ximport('Hubzero_User_Profile_Helper');
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

$c = 0;
?>
<div id="latest_discussions_module" class="<?php echo $this->cls; ?>">
	<?php if (count($this->posts > 0)) : ?>
		<ul class="blog-entries">
			<?php 
			foreach ($this->posts as $post) 
			{ 
				if ($post->group_id == 0) 
				{
					$wikiconfig = array(
						'option'   => 'com_blog',
						'scope'    => 'blog',
						'pagename' => $post->alias,
						'pageid'   => 0,
						'filepath' => '/site/blog',
						'domain'   => ''
					);
				}
				else 
				{
					$wikiconfig = array(
						'option'   => 'com_groups',
						'scope'    => 'blog',
						'pagename' => $post->alias,
						'pageid'   => 0,
						'filepath' => '/site/groups/' . $post->group_id . '/blog',
						'domain'   => ''
					);
				}
				$post->content = $p->parse(stripslashes($post->content), $wikiconfig);
				?>
				<?php if ($c < $this->limit) : ?>
					<?php
						if ($post->group_id == 0) {
							$url = 'index.php?option=com_blog&task=' . JHTML::_('date', $post->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $post->publish_up, $this->monthFormat, $this->tz) . '/' . $post->alias;
							$location = '<a href="' . JRoute::_('index.php?option=com_blog') . '">' . JText::_('Site-Wide Blog') . '</a>';
						} else {
							ximport('Hubzero_Group');
							$group = Hubzero_Group::getInstance($post->group_id);
							$url = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=blog&scope=' .  JHTML::_('date', $post->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $post->publish_up, $this->monthFormat, $this->tz) . '/' . $post->alias;
							$location = '<a href="' . JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn')) . '">' . stripslashes($group->get("description")) . '</a>';
						}
					?>
					<li>
						<?php
						$author = Hubzero_User_Profile::getInstance($post->created_by);
						if (is_object($author) && $author->get('name')) 
						{
						?>
							<p class="entry-author-photo"><img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($author, 0); ?>" alt="" /></p>
						<?php
							}
						?>
						<div class="entry-content">
							<h4>
								<a href="<?php echo JRoute::_($url); ?>"><?php echo stripslashes($post->title); ?></a>
							</h4>
							<dl class="entry-meta">
								<dt>
									<span>
										<?php echo JText::sprintf('Entry #%s', $post->id); ?>
									</span>
								</dt>
								<dd class="date">
									<time datetime="<?php echo $post->publish_up; ?>">
										<?php echo JHTML::_('date', $post->publish_up, $this->dateFormat, $this->tz); ?>
									</time>
								</dd>
								<dd class="time">
									<time datetime="<?php echo $post->publish_up; ?>">
										<?php echo JHTML::_('date', $post->publish_up, $this->timeFormat, $this->tz); ?>
									</time>
								</dd>
								<dd class="author">
									<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $post->created_by); ?>">
										<?php echo stripslashes($post->name); ?>
									</a>
								</dd>
								<dd class="location">
									<?php echo $location; ?>
								</dd>
							</dl>
							<div class="entry-body">
								<?php 
								if ($this->pullout && $c == 0)
								{
									$post->content = Hubzero_View_Helper_Html::shortenText($post->content, $this->params->get('pulloutlimit', 500), 0, 1);
								}
								else
								{
									$post->content = Hubzero_View_Helper_Html::shortenText($post->content, $this->charlimit, 0, 1);
								}
								if (substr($post->content, -7) == '&#8230;') 
								{
									$post->content .= '</p>';
								}
								echo $post->content; ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
				<?php $c++; ?>
			<?php } ?>
		</ul>
	<?php else : ?>
		<p><?php echo JText::_('Currently there are no posts.'); ?></p>
	<?php endif; ?>
	
	<?php if ($this->morelink != '') : ?>
		<p class="more"><a href="<?php echo $this->morelink; ?>">More posts &rsaquo;</a></p>
	<?php endif; ?>
</div>