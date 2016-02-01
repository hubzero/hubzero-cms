<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// define base link
$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn;

// create toolbar title
Toolbar::title($this->group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_MODULES'), 'groups.png');

//add buttons to toolbar
$canDo = \Components\Groups\Helpers\Permissions::getActions('group');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_GROUPS_MODULES_CONFIRM_DELETE', 'delete');
}
Toolbar::spacer();
Toolbar::custom('manage', 'config','config','COM_GROUPS_MANAGE', false);

$this->css();

// include modal for raw version links
Html::behavior('modal', 'a.version, a.preview', array('handler' => 'iframe', 'fullScreen'=>true));
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform(pressbutton);
}
</script>

<?php require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<?php if ($this->needsAttention->count() > 0) : ?>
	<table class="adminlist attention">
		<thead>
		 	<tr>
				<th scope="col">(<?php echo $this->needsAttention->count(); ?>) <?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_VIEW'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_CHECKS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->needsAttention as $needsAttention) : ?>
				<tr>
					<td>
						<?php echo $this->escape($needsAttention->get('title')); ?>
					</td>
					<td>
						<ol class="attention-view">
							<li class="raw">
								<a class="version" href="<?php echo $base; ?>&amp;task=raw&amp;moduleid=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_VIEW_RAW'); ?>
								</a>
							</li>
							<?php if ($needsAttention->get('checked_errors') && $needsAttention->get('scanned')) : ?>
								<li class="preview">
									<a class="preview" href="<?php echo $base; ?>&amp;task=preview&amp;moduleid=<?php echo $needsAttention->get('id'); ?>" class="btn">
										<?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_RENDER_PREVIEW'); ?>
									</a>
								</li>
							<?php else : ?>
								<li class="preview">
									<?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_RENDER_PREVIEW_HINT'); ?>
								</li>
							<?php endif; ?>
							<li class="edit">
								<a href="<?php echo $base; ?>&amp;task=edit&amp;id[]=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_EDIT'); ?>
								</a>
							</li>
						</ol>
					</td>
					<td>
						<ol class="attention-actions">
							<li class="<?php if ($needsAttention->get('checked_errors')) { echo 'completed'; } ?>">
								<a href="<?php echo $base; ?>&amp;task=errors&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_CHECK_FOR_ERRORS'); ?>
								</a>
							</li>
							<li class="<?php if ($needsAttention->get('scanned')) { echo 'completed'; } ?>">
								<a href="<?php echo $base; ?>&amp;task=scan&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_SCAN_CONTENT'); ?>
								</a>
							</li>

						</ol>
					</td>
					<td width="20%">
						<ol class="attention-actions">
							<?php if ($needsAttention->get('checked_errors') && $needsAttention->get('scanned')) : ?>
								<li class="approve">
									<a href="<?php echo $base; ?>&amp;task=approve&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
										<strong><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE'); ?></strong>
									</a>
								</li>
							<?php else: ?>
								<span><em><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE_HINT'); ?></em></span>
							<?php endif; ?>
						</ol>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<br />
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn); ?>" name="adminForm" id="adminForm" method="post">
	<table class="adminlist">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->modules->count();?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_TITLE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_STATUS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_POSITION'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php if ($this->modules->count() > 0) : ?>
	<?php foreach ($this->modules as $k => $module) : ?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $module->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&gid=' . $this->group->cn . '&id=' . $module->get('id')); ?>">
						<?php echo $this->escape($module->get('title')); ?>
					</a><br />
					<span class="hint">
						<?php
							$pages = array();
							$menus = $module->menu('list');
							foreach ($menus as $menu)
							{
								$pages[] = $menu->getPageTitle();
							}
							echo Lang::txt('COM_GROUPS_MODULES_INCLUDED_ON', implode(', ', $pages));
						?>
					</span>
				</td>
				<td>
					<?php
						switch ($module->get('state'))
						{
							case 0:
								echo  Lang::txt('COM_GROUPS_MODULES_STATUS_UNPUBLISHED');
							break;
							case 1:
								echo  Lang::txt('COM_GROUPS_MODULES_STATUS_PUBLISHED');
							break;
							case 2:
								echo Lang::txt('COM_GROUPS_MODULES_STATUS_DELETED');
							break;
						}
					?>
				</td>
				<td><?php echo $this->escape($module->get('position')); ?></td>
			</tr>
	<?php endforeach; ?>
<?php else : ?>
			<tr>
				<td colspan="4"><?php echo Lang::txt('COM_GROUPS_MODULES_NO_MODULES'); ?></td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>