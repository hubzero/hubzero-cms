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

//import helper class
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Document');

/*$posts = 0;
if ($this->rows) 
{
	foreach ($this->rows as $row)
	{
		$posts += $row->posts;
	}
}*/
?>

<?php /*if ($this->rows && $this->params->get('access-create-bulletin')) { ?>
<ul id="page_options">
	<li>
		<a class="add btn" href="<?php echo JRoute::_('index.php?option=com_groups&gid=' . $this->group->get('cn').'&active=' . $this->name . '&scope=posts/new'); ?>">
			<?php echo JText::_('New post'); ?>
		</a>
	</li>
</ul>
<?php }*/ ?>

<form method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name); ?>" id="bulletinboards">
	<fieldset class="filters">
		<span class="board count">
			<strong><?php echo $this->rows->total(); ?></strong> collections
		</span>
		<span class="post count">
			<strong><?php echo $this->posts; ?></strong> posts
		</span>
		<span class="like count">
			<strong><?php echo $this->likes; ?></strong> likes
		</span>
<?php if ($this->params->get('access-create-collection')) { ?>
		<a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name . '&task=new'); ?>">
			<?php echo JText::_('New collection'); ?>
		</a>
<?php } ?>
		<div class="clear"></div>
	</fieldset>

	<div id="boards">
<?php 
if ($this->rows) 
{
	$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name;

	//$board = new CollectionsTableCollection(JFactory::getDBO());

	foreach ($this->rows as $row)
	{
		//$counts = $board->getPostTypeCount($row->get('id'));
?>
		<div class="bulletin board <?php echo ($row->get('access') == 4) ? 'private' : 'public'; echo ($row->get('is_default')) ? ' default' : ''; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>">
			<div class="content">
				<?php
						$view = new Hubzero_Plugin_View(
							array(
								'folder'  => 'members',
								'element' => $this->name,
								'name'    => 'entry',
								'layout'  => '_board'
							)
						);
						//$view->name       = $this->name;
						//$view->option     = $this->option;
						//$view->member     = $this->member;
						//$view->params     = $this->params;

						//$view->dateFormat = $this->dateFormat;
						//$view->timeFormat = $this->timeFormat;
						//$view->tz         = $this->tz;

						$view->row        = $row;
						$view->collection = $row;

						$view->display();
				?>
				<div class="meta">
					<p class="stats">
						<span class="likes">
							<?php echo JText::sprintf('%s likes', $row->get('positive')); ?>
						</span>
						<!-- <span class="comments">
<?php /*if (isset($row->comments) && $row->comments) { ?>
							<?php echo JText::sprintf('%s comments', $row->comments); ?>
<?php } else { ?>
							<?php echo JText::sprintf('%s comments', 0); ?>
<?php }*/ ?>
						</span> -->
						<span class="reposts">
<?php if ($row->get('posts')) { ?>
							<?php echo JText::sprintf('%s reposts', $row->get('posts')); ?>
<?php } else { ?>
							<?php echo JText::sprintf('%s reposts', 0); ?>
<?php } ?>
						</span>
					</p>
<?php if (!$this->juser->get('guest')) { ?>
					<div class="actions">
<?php if ($this->params->get('access-edit-collection')) { ?>
						<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=' . $row->get('alias') . '/edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
<?php } ?>
<?php if (!$row->get('is_default') && $this->params->get('access-delete-collection')) { ?>
						<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=' . $row->get('alias') . '/delete'); ?>">
							<span><?php echo JText::_('Delete'); ?></span>
						</a>
<?php } ?>
					</div><!-- / .actions -->
<?php } ?>
				</div><!-- / .meta -->
			</div><!-- / .content -->
		</div><!-- / .board -->
<?php
	}
}
else
{
?>
		<div id="bb-introduction">
<?php if ($this->params->get('access-create-collection')) { ?>
			<div class="instructions">
				<ol>
					<li>Click on the "new board" button.</li>
					<li>Add a title and maybe a description.</li>
					<li>Done!</li>
				</ol>
			</div>
<?php } else { ?>
			<div class="instructions">
				<p>No boards available.</p>
			</div>
<?php } ?>
		</div>
<?php
}
?>
		<div class="clear"></div>
	</div>
</form>