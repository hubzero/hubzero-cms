<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('mailinglist');

Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS'), 'list');
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
	Toolbar::deleteList('COM_NEWSLETTER_MAILINGLIST_DELETE_CHECK', 'delete');
	Toolbar::spacer();
}
Toolbar::custom('manage', 'user', '', 'COM_NEWSLETTER_TOOLBAR_MANAGE');
Toolbar::custom('export', 'export', '', 'COM_NEWSLETTER_TOOLBAR_EXPORT');
if ($canDo->get('core.admin'))
{
	Toolbar::spacer();
	Toolbar::preferences($this->option, '550');
}
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_NEWSLETTER_GO'); ?>" />
		<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NAME'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ACTIVE_SUBSCRIBERS'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_TOTAL_SUBSCRIBERS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php
				// initiate paging
				echo $this->lists->pagination;
				$k = 0;
				?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($this->lists) > 0) { ?>
				<?php foreach ($this->lists as $list) { ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k; ?>" value="<?php echo $list->id; ?>" class="checkbox-toggle" />
						</td>
						<td>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $list->id); ?>">
								<?php echo $this->escape($list->name); ?>
							</a>
						</td>
						<td class="priority-3">
							<span class="access <?php echo ($list->private) ? 'private' : 'public'; ?>">
								<?php echo ($list->private) ? Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PRIVATE') : Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PUBLIC'); ?>
							</span>
						</td>
						<td class="priority-2">
							<?php echo $list->emails()->whereEquals('status', 'active')->total(); ?>
						</td>
						<td class="priority-2">
							<?php echo $list->emails()->total(); ?>
						</td>
					</tr>
					<?php
					$k++;
				}
				?>
			<?php } else if (!$this->filters['search']) { ?>
				<tr>
					<td colspan="5">
						<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NO_LISTS', "javascript:submitbutton('add');"); ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
