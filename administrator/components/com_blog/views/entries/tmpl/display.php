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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = BlogHelper::getActions('entry');

JToolBarHelper::title(JText::_('COM_BLOG_TITLE'), 'blog.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state'))
{
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList('', 'delete');
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
JToolBarHelper::spacer();
JToolBarHelper::help('entries');

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_BLOG_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<?php if ($this->filters['scope'] == 'group') { ?>
				<?php
				$filters = array();
				$filters['authorized'] = 'admin';
				$filters['fields'] = array('cn','description','published','gidNumber','type');
				$filters['type'] = array(1,3);
				$filters['sortby'] = 'description';
				$groups = \Hubzero\User\Group::find($filters);

				$html  = '<label for="filter_group_id">' . JText::_('COM_BLOG_SCOPE_GROUP') . ':</label> '."\n";
				$html .= '<select name="group_id" id="filter_group_id">'."\n";
				$html .= '<option value="0"';
				if ($this->filters['group_id'] == 0)
				{
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('JNONE').'</option>'."\n";
				if ($groups)
				{
					foreach ($groups as $group)
					{
						$html .= ' <option value="'.$group->gidNumber.'"';
						if ($this->filters['group_id'] == $group->gidNumber)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>' . $this->escape(stripslashes($group->description)) . '</option>'."\n";
					}
				}
				$html .= '</select>'."\n";
				echo $html;
				?>
		<?php } ?>
		<input type="submit" value="<?php echo JText::_('COM_BLOG_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_BLOG_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_BLOG_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_BLOG_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_BLOG_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_BLOG_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" colspan="2"><?php echo JHTML::_('grid.sort', 'COM_BLOG_COL_COMMENTS', 'comments', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			<?php if ($this->filters['scope'] == 'group') { ?>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_BLOG_COL_GROUP', 'group_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			<?php } ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo ($this->filters['scope'] == 'group') ? '9' : '8'; ?>"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$i = 0;
$config	= JFactory::getConfig();
$now	= JFactory::getDate();
$db		= JFactory::getDBO();

$nullDate = $db->getNullDate();

foreach ($this->rows as $row)
{
	$publish_up   = JFactory::getDate($row->get('publish_up'));
	$publish_down = JFactory::getDate($row->get('publish_down'));
	$publish_up->setOffset($config->getValue('config.offset'));
	$publish_down->setOffset($config->getValue('config.offset'));

	if ($now->toUnix() <= $publish_up->toUnix() && $row->get('state') == 1)
	{
		$alt  = JText::_('JPUBLISHED');
		$cls  = 'publish';
		$task = 'unpublish';
	}
	else if (($now->toUnix() <= $publish_down->toUnix() || $row->get('publish_down') == $nullDate) && $row->get('state') == 1)
	{
		$alt  = JText::_('JPUBLISHED');
		$cls  = 'publish';
		$task = 'unpublish';
	}
	else if ($now->toUnix() > $publish_down->toUnix() && $row->get('state') == 1)
	{
		$alt  = JText::_('JLIB_HTML_PUBLISHED_EXPIRED_ITEM');
		$cls  = 'publish';
		$task = 'unpublish';
	}
	else if ($row->get('state') == 0)
	{
		$alt  = JText::_('JUNPUBLISHED');
		$task = 'publish';
		$cls  = 'unpublish';
	}
	else if ($row->get('state') == -1)
	{
		$alt  = JText::_('JTRASHED');
		$task = 'publish';
		$cls  = 'trash';
	}

	$times = '';
	if ($row->get('publish_up'))
	{
		if ($row->get('publish_up') == $nullDate)
		{
			$times .= JText::_('COM_BLOG_START') . ': ' . JText::_('COM_BLOG_ALWAYS');
		}
		else
		{
			$times .= JText::_('COM_BLOG_START') . ': ' . $publish_up->toSql();
		}
	}
	if ($row->get('publish_down'))
	{
		if ($row->get('publish_down') == $nullDate)
		{
			$times .= '<br />' . JText::_('COM_BLOG_FINiSH') . ': ' . JText::_('COM_BLOG_NO_EXPIRY');
		}
		else
		{
			$times .= '<br />' . JText::_('COM_BLOG_FINiSH') . ': ' . $publish_down->toSql();
		}
	}

	if ($row->get('allow_comments') == 0)
	{
		$calt = JText::_('JOFF');
		$cls2 = 'off';
		$state = 1;
	}
	else
	{
		$calt = JText::_('JON');
		$cls2 = 'on';
		$state = 0;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id') ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->get('id'); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->get('name'))); ?>
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_('COM_BLOG_PUBLISH_INFO');?>::<?php echo $times; ?>">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
					</span>
				</td>
				<td>
					<time datetime="<?php echo $row->get('created'); ?>">
						<?php echo $row->published('date'); ?>
					</time>
				</td>
				<td>
					<a class="state <?php echo $cls2; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=setcomments&amp;state=<?php echo $state; ?>&amp;id=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo $calt; ?></span>
					</a>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a class="comment" href="index.php?option=<?php echo $this->option ?>&amp;controller=comments&amp;entry_id=<?php echo $row->get('id'); ?>">
						<?php echo JText::sprintf('COM_BLOG_COMMENTS', $row->get('comments')); ?>
					</a>
				<?php } else { ?>
					<span class="comment">
						<?php echo JText::sprintf('COM_BLOG_COMMENTS', $row->get('comments')); ?>
					</span>
				<?php } ?>
				</td>
			<?php if ($this->filters['scope'] == 'group') { ?>
				<td>
					<span>
						<?php echo $this->escape($row->get('group_id')); ?>
					</span>
				</td>
			<?php } ?>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="scope" value="<?php echo $this->filters['scope']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
