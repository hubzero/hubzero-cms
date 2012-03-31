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
defined('_JEXEC') or die( 'Restricted access' );

$posts 		= $modlatestdiscussions->posts;
$limit	 	= $modlatestdiscussions->limit;
$charlimit	= $modlatestdiscussions->charlimit;
$cls 		= $modlatestdiscussions->cls;
$feedlink 	= $modlatestdiscussions->feedlink;
$morelink 	= $modlatestdiscussions->morelink;

$c = 0;
?>

<div id="latest_discussions_module" class="<?php echo $cls; ?>">
	<?php if(count($posts > 0)) : ?>
		<ul class="discussions">
			<?php foreach($posts as $post) : ?>
				<?php if($c < $limit) : ?>
					<?php
						if($post['group_id'] == 0) {
							$url = "index.php?option=com_forum&task=topic&topic=".$post['id'];
							$location = "<a href=\"/forum\">Site-Wide Forum</a>";
						} else {
							ximport("Hubzero_Group");
							$group = Hubzero_Group::getInstance( $post['group_id'] );
							$url = "index.php?option=com_groups&gid=".$group->get("cn")."&active=forum&task=topic&topic=".$post['id'];
							$location = "<a href=\"/groups/{$group->get("cn")}\">{$group->get("description")}</a>";
						}
					?>
					<li>
						<h4>
							<a href="<?php echo JRoute::_($url); ?>" title=""><?php echo $post['title']; ?></a>
						</h4>
						<span class="discussion-author">
							<?php 
								if($post['anonymous']) {
									echo "<i>".JText::_('Anonymous')."</i>";
								} else {
									$juser =& JUser::getInstance($post['created_by']); 
									echo "<a href=\"/members/{$juser->get("id")}\">{$juser->get("name")}</a>";
								}
								echo ", in&nbsp;"
							?>
						</span>
						<span class="discussion-location"><?php echo $location; ?></span>
						<span class="discussion-date"><?php echo date("F jS, Y, g:ia", strtotime($post['created'])); ?></span>
						<?php if($charlimit > 0) : ?>
							<span class="discussion-comment"><?php echo substr($post['comment'],0, $charlimit); if(strlen($post['comment']) > $charlimit) { echo "&hellip;"; } ?></span>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php $c++; ?>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p><?php echo JText::_('Currently there are no discussions'); ?></p>
	<?php endif; ?>
	
	<?php if($morelink != "") : ?>
		<p class="more"><a href="<?php echo $morelink; ?>">More Discussions &rsaquo;</a></p>
	<?php endif; ?>
	
	<?php if($feedlink == "yes") : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_forum&task=latest.rss', true, -1); ?>" class="newsfeed" title="Latest Discussion's Feed">Latest Discussion's Feed</a>
		<br class="clear" />
	<?php endif; ?>
</div>



