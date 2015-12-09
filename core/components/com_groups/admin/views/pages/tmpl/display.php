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

// create title
Toolbar::title($this->group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES'), 'groups.png');

// create toolbar buttons
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
	Toolbar::deleteList('COM_GROUPS_PAGES_DELETE_CONFIRM', 'delete');
}
Toolbar::spacer();
Toolbar::custom('manage', 'config','config','COM_GROUPS_MANAGE',false);
Toolbar::spacer();
Toolbar::help('pages');

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

<?php
	require_once __DIR__ . DS . 'menu.php';
?>

<?php if ($this->needsAttention->count() > 0) : ?>
	<table class="adminlist attention">
		<thead>
			<tr>
				<th>(<?php echo $this->needsAttention->count(); ?>) <?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION'); ?></th>
				<th><?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_VIEW'); ?></th>
				<th><?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_CHECKS'); ?></th>
				<th><?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_APPROVE'); ?></th>
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
									<?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_VIEW_RAW'); ?>
								</a>
							</li>
							<?php if ($needsAttention->version()->get('checked_errors') && $needsAttention->version()->get('scanned')) : ?>
								<li class="preview">
									<a class="preview" href="<?php echo $base; ?>&amp;task=preview&amp;pageid=<?php echo $needsAttention->get('id'); ?>" class="btn">
										<?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_RENDER_PREVIEW'); ?>
									</a>
								</li>
							<?php else : ?>
								<li class="preview">
									<?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_RENDER_PREVIEW_HINT'); ?>
								</li>
							<?php endif; ?>
							<li class="edit">
								<a href="<?php echo $base; ?>&amp;task=edit&amp;id[]=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_EDIT'); ?>
								</a>
							</li>
						</ol>
					</td>
					<td>
						<ol class="attention-actions">
							<li class="<?php if ($needsAttention->version()->get('checked_errors')) { echo 'completed'; } ?>">
								<a href="<?php echo $base; ?>&amp;task=errors&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_CHECK_FOR_ERRORS'); ?>
								</a>
							</li>
							<li class="<?php if ($needsAttention->version()->get('scanned')) { echo 'completed'; } ?>">
								<a href="<?php echo $base; ?>&amp;task=scan&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
									<?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_SCAN_CONTENT'); ?>
								</a>
							</li>
						</ol>
					</td>
					<td width="20%">
						<ol class="attention-actions">
							<?php if ($needsAttention->version()->get('checked_errors') && $needsAttention->version()->get('scanned')) : ?>
								<li class="approve">
									<a href="<?php echo $base; ?>&amp;task=approve&amp;id=<?php echo $needsAttention->get('id'); ?>" class="btn">
										<strong><?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_APPROVE'); ?></strong>
									</a>
								</li>
							<?php else: ?>
								<span><em><?php echo Lang::txt('COM_GROUPS_PAGES_NEEDING_ATTENTION_APPROVE_HINT'); ?></em></span>
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
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->pages->count();?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_PAGES_TITLE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_PAGES_STATE'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_GROUPS_PAGES_HOME'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS'); ?></th>
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
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&gid=' . $this->group->cn . '&id=' . $page->get('id')); ?>">
						<?php echo $this->escape(stripslashes($page->get('title'))); ?> <br />
					</a>
					<?php
						// add /groups/{{group_cname}}
						$segments = array('groups', $this->group->get('cn'));

						// get parent aliases
						$parents  = $page->getRecursiveParents($page);
						$segments = array_merge($segments, $parents->lists('alias'));

						// remove home page
						$search = array_search('overview', $segments);
						if ($search !== false)
						{
							unset($segments[$search]);
						}

						// add this page alias
						$segments[] = $page->get('alias');
					?>
					<span class="hint" tabindex="-1"><?php echo DS . implode(DS, $segments); ?></span>
				</td>
				<td>
					<?php
					switch ($page->get('state'))
					{
						case 0:
							echo '<span class="state unpublish"><span>' . Lang::txt('COM_GROUPS_PAGES_STATE_UNPUBLISHED') . '</span></span>';
						break;
						case 1:
							echo '<span class="state publish"><span>' . Lang::txt('COM_GROUPS_PAGES_STATE_PUBLISHED') . '</span></span>';
						break;
						case 2:
							echo '<span class="state trashed"><span>' . Lang::txt('COM_GROUPS_PAGES_STATE_DELETED') . '</span></span>';
						break;
					}
					?>
				</td>
				<td class="priority-3">
					<?php
						if ($page->get('home'))
						{
							echo '<span class="home">'.Lang::txt('JYES').'</span>';
						}
					?>
				</td>
				<td class="priority-4"><?php echo $page->versions()->count(); ?></td>
			</tr>
	<?php endforeach; ?>
<?php else : ?>
			<tr>
				<td colspan="6"><?php echo Lang::txt('COM_GROUPS_PAGES_NO_PAGES'); ?></td>
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