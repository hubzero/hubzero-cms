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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// define base link
$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn;

// create title
JToolBarHelper::title($this->group->get('description') . ': ' . JText::_('Group Pages'), 'groups.png');

// create toolbar buttons
$canDo = GroupsHelper::getActions('group');
if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList('Delete group page(s)?', 'delete');
}
JToolBarHelper::spacer();
JToolBarHelper::custom('manage', 'config','config','Manage',false);
JToolBarHelper::spacer();
JToolBarHelper::help('pages');

$this->css();

// include modal for raw version links
JHtml::_('behavior.modal', 'a.version, a.preview', array('handler' => 'iframe', 'fullScreen'=>true));
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform(pressbutton);
}
</script>

<?php
	require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php';
?>

<?php if ($this->needsAttention->count() > 0) : ?>
	<table class="adminlist attention">
		<thead>
		 	<tr>
				<th>(<?php echo $this->needsAttention->count(); ?>) Pages Needing Approval</th>
				<th>View</th>
				<th>Checks</th>
				<th>Approve</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->needsAttention as $needsAttention) : ?>
				<tr>
					<td>
						<?php echo $this->escape($needsAttention->get('title')); ?> <br />
						<span class="hint" tabindex="-1"><?php echo '/groups/' . $this->group->get('cn') . '/' . $this->escape($needsAttention->get('alias')); ?></span>
					</td>
					<td>
						<ol class="attention-view">
							<li class="raw">
								<a class="version" href="<?php echo $base; ?>&amp;task=raw&amp;pageid=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo JText::_('View Raw'); ?>
								</a>
							</li>
							<?php if($needsAttention->version()->get('checked_errors') && $needsAttention->version()->get('scanned')) : ?>
								<li class="preview">
									<a class="preview" href="<?php echo $base; ?>&amp;task=preview&amp;pageid=<?php echo $needsAttention->get('id'); ?>" class="btn">
										<?php echo JText::_('Render Preview'); ?>
									</a>
								</li>
							<?php else : ?>
								<li class="preview">
									<?php echo JText::_('Render Preview (must run check first)'); ?>
								</li>
							<?php endif; ?>
							<li class="edit">
								<a href="<?php echo $base; ?>&amp;task=edit&amp;id[]=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo JText::_('Edit'); ?>
								</a>
							</li>
						</ol>
					</td>
					<td>
						<ol class="attention-actions">
							<li class="<?php if($needsAttention->version()->get('checked_errors')) { echo 'completed'; } ?>">
								<a href="<?php echo $base; ?>&amp;task=errors&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo JText::_('Check for Errors'); ?>
								</a>
							</li>
							<li class="<?php if($needsAttention->version()->get('scanned')) { echo 'completed'; } ?>">
								<a href="<?php echo $base; ?>&amp;task=scan&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo JText::_('Scan Content'); ?>
								</a>
							</li>

						</ol>
					</td>
					<td width="20%">
						<ol class="attention-actions">
							<?php if($needsAttention->version()->get('checked_errors') && $needsAttention->version()->get('scanned')) : ?>
								<li class="approve">
									<a href="<?php echo $base; ?>&amp;task=approve&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
										<strong><?php echo JText::_('Approve'); ?></strong>
									</a>
								</li>
							<?php else: ?>
								<span><em><?php echo JText::_('You must check for errors and scan before you can approve'); ?></em></span>
							<?php endif; ?>
						</ol>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<br />
<?php endif; ?>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>" name="adminForm" id="adminForm" method="post">
	<table class="adminlist">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->pages->count();?>);" /></th>
				<th scope="col">Title</th>
				<th scope="col">Order</th>
				<th scope="col">State</th>
				<th scope="col">Home</th>
				<th scope="col"># of Versions</th>
			</tr>
		</thead>
		<tbody>
<?php if ($this->pages->count() > 0) : ?>
	<?php foreach ($this->pages as $k => $page) : ?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $page->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape(stripslashes($page->get('title'))); ?> <br />
					<span class="hint" tabindex="-1"><?php echo '/groups/' . $this->group->get('cn') . '/' . $this->escape($page->get('alias')); ?></span>
				</td>
				<td><input type="text" style="width:30px;text-align:center;" disabled="disabled" value="<?php echo ($page->get('ordering') + 0); ?>" /></td>
				<td>
					<?php
					switch($page->get('state'))
					{
						case 0:
							echo  JText::_('Unpublished');
						break;
						case 1:
							echo  JText::_('Published');
						break;
						case 2:
							echo JText::_('Deleted');
						break;
					}
					?>
				</td>
				<td>
					<?php
						if ($page->get('home'))
						{
							echo '<span class="home">Yes</span>';
						}
					?>
				</td>
				<td><?php echo $page->versions()->count(); ?></td>
			</tr>
	<?php endforeach; ?>
<?php else : ?>
			<tr>
				<td colspan="6"><?php echo JText::_('Currently there are no pages for this group.'); ?></td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>