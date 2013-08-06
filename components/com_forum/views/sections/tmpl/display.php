<?php 
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
 
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:i a';
	$tz = true;
}

$juser = JFactory::getUser();
?>
<div id="content-header" class="full">
	<h2><?php echo JText::_('COM_FORUM'); ?></h2>
</div>

<?php foreach ($this->notifications as $notification) { ?>
<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

<div class="main section">
	<div class="aside">
		<div class="container">
			<h3><?php echo JText::_('Statistics'); ?></h3>
			<table summary="<?php echo JText::_('Statistics'); ?>">
				<tbody>
					<tr>
						<th><?php echo JText::_('Categories'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('categories'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Discussions'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('threads'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Posts'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('posts'); ?></span></td>
					</tr>
				</tbody>
			</table>
		</div><!-- / .container -->
		<div class="container">
			<h3><?php echo JText::_('Last Post'); ?></h3>
			<p>
			<?php
			if ($this->model->lastActivity()->exists()) 
			{
				$post = $this->model->lastActivity();

				$lname = JText::_('Anonymous');
				if (!$post->get('anonymous')) 
				{
					$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $post->creator('id')) . '">' . $this->escape(stripslashes($post->creator('name'))) . '</a>';
				}
				foreach ($this->sections as $section)
				{
					if ($section->categories()->total() > 0) 
					{
						foreach ($section->categories() as $row) 
						{
							if ($row->get('id') == $post->get('category_id'))
							{
								$cat = $row->get('alias');
								$sec = $section->get('alias');
								break;
							}
						}
					}
				}
				?>
				<a class="entry-date" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $sec . '&category=' . $cat . '&thread=' . $post->get('thread')); ?>">
					<span class="entry-date-at">@</span>
					<span class="time"><time datetime="<?php echo $post->get('created'); ?>"><?php echo JHTML::_('date', $post->get('created'), $timeFormat, $tz); ?></time></span> <span class="entry-date-on"><?php echo JText::_('COM_FORUM_ON'); ?></span> 
					<span class="date"><time datetime="<?php echo $post->get('created'); ?>"><?php echo JHTML::_('date', $post->get('created'), $dateFormat, $tz); ?></time></span>
				</a>
				<span class="entry-author">
					<?php echo JText::_('by'); ?>
					<?php echo $lname; ?>
				</span>
			<?php } else { ?>
				<?php echo JText::_('none'); ?>
			<?php } ?>
			</p>
		</div><!-- / .container -->
		
<?php if ($this->config->get('access-create-section')) { ?>
		<div class="container">
			<h3><?php echo JText::_('Sections'); ?><span class="starter-point"></span></h3>
			<p>
				<?php echo JText::_('Use sections to group related categories.'); ?>
			</p>
			
			<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
				<fieldset>
					<legend><?php echo JText::_('New Section'); ?></legend>
					<label for="field-title">
						<?php echo JText::_('Section Title'); ?>
						<input type="text" name="fields[title]" id="field-title" value="" />
					</label>
					<p class="submit">
						<input type="submit" value="<?php echo JText::_('Create'); ?>" />
					</p>
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="controller" value="sections" />
					<input type="hidden" name="fields[scope]" value="site" />
					<input type="hidden" name="fields[scope_id]" value="0" />
					<?php echo JHTML::_('form.token'); ?>
				</fieldset>
			</form>
		</div>
<?php } ?>
	</div><!-- / .aside -->

	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search categories'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="categories" />
					<input type="hidden" name="task" value="search" />
				</fieldset>
			</div><!-- / .container -->
		</form>
<?php
foreach ($this->sections as $section)
{
	if (!$section->exists() && $section->categories()->total()) 
	{
		continue;
	}
?>
		<div class="container" id="section-<?php echo $section->get('id'); ?>">
			<table class="entries categories">
				<caption>
				<?php if ($this->config->get('access-edit-section') && $this->edit == $section->get('alias') && $section->get('id')) { ?>
					<a name="s<?php echo $section->get('id'); ?>"></a>
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
						<input type="text" name="fields[title]" value="<?php echo $this->escape(stripslashes($section->get('title'))); ?>" />
						<input type="submit" value="<?php echo JText::_('Save'); ?>" />
						<input type="hidden" name="fields[id]" value="<?php echo $section->get('id'); ?>" />
						<input type="hidden" name="fields[scope]" value="site" />
						<input type="hidden" name="fields[scope_id]" value="0" />
						<input type="hidden" name="controller" value="sections" />
						<input type="hidden" name="task" value="save" />
						<?php echo JHTML::_('form.token'); ?>
					</form>
				<?php } else { ?>
					<?php echo $this->escape(stripslashes($section->get('title'))); ?>
				<?php } ?>

			<?php if (($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) && $section->get('id')) { ?>
				<?php if ($this->config->get('access-delete-section')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_('index.php?option='.$this->option . '&section=' . $section->get('alias') . '&task=delete'); ?>" title="<?php echo JText::_('Delete'); ?>">
						<span><?php echo JText::_('Delete'); ?></span>
					</a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-section') && $this->edit != $section->get('alias') && $section->get('id')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $section->get('alias') . '&task=edit#s' . $section->get('id')); ?>" title="<?php echo JText::_('Edit'); ?>">
						<span><?php echo JText::_('Edit'); ?></span>
					</a>
				<?php } ?>
			<?php } ?>
				</caption>
			<?php if ($this->config->get('access-create-category')) { ?>
				<tfoot>
					<tr>
						<td<?php if ($section->categories()->total() > 0) { echo ' colspan="5"'; } ?>>
							<a class="icon-add add btn" id="addto-<?php echo $section->get('id'); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $section->get('alias') . '&task=new'); ?>">
								<span><?php echo JText::_('Add Category'); ?></span>
							</a>
						</td>
					</tr>
				</tfoot>
			<?php } ?>
				<tbody>
			<?php if ($section->categories()->total() > 0) { ?>
				<?php foreach ($section->categories() as $row) { ?>
					<tr<?php if ($row->get('closed')) { echo ' class="closed"'; } ?>>
						<th scope="row">
							<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $section->get('alias') . '&category=' . $row->get('alias')); ?>">
								<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
							</a>
							<span class="entry-details">
								<span class="entry-description">
									<?php echo $this->escape(stripslashes($row->get('description'))); ?>
								</span>
							</span>
						</td>
						<td>
							<span><?php echo $row->count('threads'); ?></span>
							<span class="entry-details">
								<?php echo JText::_('Discussions'); ?>
							</span>
						</td>
						<td>
							<span><?php echo $row->count('posts'); ?></span>
							<span class="entry-details">
								<?php echo JText::_('Posts'); ?>
							</span>
						</td>
					<?php if ($this->config->get('access-edit-category') || $this->config->get('access-delete-categort')) { ?>
						<td class="entry-options">
							<?php if (($row->get('created_by') == $juser->get('id') || $this->config->get('access-edit-category')) && $section->get('id')) { ?>
								<a class="icon-edit edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $section->get('alias') . '&category=' . $row->get('alias') . '&task=edit'); ?>" title="<?php echo JText::_('Edit'); ?>">
									<span><?php echo JText::_('Edit'); ?></span>
								</a>
							<?php } ?>
							<?php if ($this->config->get('access-delete-category') && $section->get('id')) { ?>
								<a class="icon-delete delete tooltips" title="<?php echo JText::_('COM_FORUM_DELETE_CATEGORY'); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $section->get('alias') . '&category=' . $row->get('alias') . '&task=delete'); ?>" title="<?php echo JText::_('Delete'); ?>">
									<span><?php echo JText::_('Delete'); ?></span>
								</a>
							<?php } ?>
						</td>
					<?php } ?>
					</tr>
				<?php } ?>
			<?php } else { ?>
					<tr>
						<td><?php echo JText::_('There are no categories.'); ?></td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
		</div>
<?php 
} 
?>
	</div><!-- /.subject -->
	<div class="clear"></div>
</div><!-- /.main -->
