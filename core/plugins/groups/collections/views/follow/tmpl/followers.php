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

$this->js();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo Route::url($base . '&scope=followers'); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('group', $this->group)
	     ->set('params', $this->params)
	     ->set('name', $this->name)
	     ->set('active', 'followers')
	     ->set('collections', $this->collections)
	     ->set('posts', $this->posts)
	     ->set('followers', $this->total)
	     ->set('following', ($this->params->get('access-can-follow') ? $this->following : 0))
	     ->display();
	?>

	<?php if (!User::isGuest() && $this->params->get('access-manage-collection')) { ?>
		<p class="guest-options">
			<a class="icon-config config btn" href="<?php echo Route::url($base . '&scope=settings'); ?>">
				<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS'); ?></span>
			</a>
		</p>
	<?php } ?>

	<?php if ($this->rows->total() > 0) { ?>
		<div class="container">
			<table class="followers entries">
				<caption>
					<?php echo Lang::txt('People following this group'); ?>
				</caption>
				<tbody>
					<?php foreach ($this->rows as $row) { ?>
						<tr class="<?php echo $row->get('follower_type'); ?>">
							<th class="entry-img">
								<img src="<?php echo $row->follower()->image(); ?>" width="40" height="40" alt="Profile picture of <?php echo $this->escape(stripslashes($row->follower()->title())); ?>" />
							</th>
							<td>
								<a class="entry-title" href="<?php echo Route::url($row->follower()->link()); ?>">
									<?php echo $this->escape(stripslashes($row->follower()->title())); ?>
								</a>
								<br />
								<span class="entry-details">
									<span class="follower count"><?php echo Lang::txt('<strong>%s</strong> followers', $row->count('followers')); ?></span>
									<span class="following count"><?php echo Lang::txt('<strong>%s</strong> following', $row->count('following')); ?></span>
								</span>
							</td>
							<td>
								<time datetime="<?php echo $row->get('created'); ?>"><?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'collections');
			$pageNav->setAdditionalUrlParam('scope', 'followers');
			echo $pageNav->render();
			?>
			<div class="clear"></div>
		</div><!-- / .container -->
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-manage-collection')) { ?>
				<div class="instructions">
					<p><?php echo Lang::txt('This group currently does not have anyone following it or any of its collections.'); ?></p>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('What are followers?'); ?></strong></p>
					<p><?php echo Lang::txt('"Followers" are members that have decided to receive all public posts this group makes or all posts in one of this group\'s collections.'); ?><p>
					<p><?php echo Lang::txt('Followers cannot see of your private collections or posts made to private collections.'); ?><p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p>
						<?php echo Lang::txt('This group is not following anyone or any collections.'); ?>
					</p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
	<div class="clear"></div>
</form>