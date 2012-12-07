<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<form method="get" action="<?php echo JRoute::_($base); ?>" id="collections">
	<fieldset class="filters">
		<span class="collections count">
			<strong><?php echo $this->total; ?></strong> boards
		</span>
		<span class="posts count">
			<strong><?php echo $this->posts; ?></strong> posts
		</span>
<?php if ($this->params->get('access-create-collection')) { ?>
		<a class="add btn" href="<?php echo JRoute::_($base . '&scope=new'); ?>">
			<?php echo JText::_('New collection'); ?>
		</a>
<?php } ?>
		<div class="clear"></div>
	</fieldset>

	<div id="posts">
<?php 
if ($this->rows->total() > 0) 
{
	foreach ($this->rows as $row)
	{
?>
		<div class="post collection <?php echo ($row->get('access') == 4) ? 'private' : 'public'; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>">
			<div class="content">
				<?php
						$view = new Hubzero_Plugin_View(
							array(
								'folder'  => 'members',
								'element' => $this->name,
								'name'    => 'post',
								'layout'  => 'default_collection'
							)
						);
						$view->row        = $row;
						$view->collection = $row;
						$view->display();
				?>
				<div class="meta">
					<p class="stats">
						<span class="likes">
							<?php echo JText::sprintf('%s likes', $row->get('positive', 0)); ?>
						</span>
						<span class="reposts">
							<?php echo JText::sprintf('%s posts', $row->get('posts', 0)); ?>
						</span>
					</p>
				<?php if (!$this->juser->get('guest')) { ?>
					<div class="actions">
					<?php if ($this->params->get('access-edit-collection')) { ?>
						<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $row->get('alias') . '/edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
					<?php } ?>
					<?php if (!$row->get('is_default') && $this->params->get('access-delete-collection')) { ?>
						<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $row->get('alias') . '/delete'); ?>">
							<span><?php echo JText::_('Delete'); ?></span>
						</a>
					<?php } ?>
					</div><!-- / .actions -->
				<?php } ?>
				</div><!-- / .meta -->
			</div><!-- / .content -->
		</div><!-- / .post -->
<?php
	}
}
else
{
?>
		<div id="collection-introduction">
<?php if ($this->params->get('access-create-collection')) { ?>
			<div class="instructions">
				<ol>
					<li>Click on the "new collection" button.</li>
					<li>Add a title and maybe a description.</li>
					<li>Done!</li>
				</ol>
			</div><!-- / .instructions -->
<?php } else { ?>
			<div class="instructions">
				<p>No collections available.</p>
			</div><!-- / .instructions -->
<?php } ?>
		</div><!-- / #collection-introduction -->
<?php
}
?>
		<div class="clear"></div>
	</div><!-- / #posts -->
</form>