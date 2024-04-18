<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('campaign');

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER_CAMPAIGNS'), 'campaigns');

// toolbar
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
	Toolbar::spacer();
	Toolbar::deleteList('COM_NEWSLETTER_CAMPAIGN_DELETE_CHECK', 'delete');
}
Toolbar::spacer();
Toolbar::help('campaign');

$this->js();
?>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="admin-form">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_NEWSLETTER_GO'); ?>" />
		<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_ID', 'id', @$this->filters['sort_Dir'],
					@$this->filters['sort']);?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_NAME', 'title', @$this->filters['sort_Dir'],
					@$this->filters['sort']);?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_EXPIRE_DATE', 'expire_date',
					@$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_CAMPAIGN_DESCRIPTION');?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_DATE', 'campaign_date',
					@$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_MOD_DATE', 'modified',
					@$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_MOD_BY', 'modified_by',
					@$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan="5"><?php
				// initiate paging
				echo $this->campaigns->pagination;
				$k = 0;

				?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($this->campaigns) > 0) { ?>
				<?php foreach ($this->campaigns as $campaign) { ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k; ?>" value="<?php echo $campaign->id; ?>" class="checkbox-toggle" />
							<label for="cb<?php echo $k; ?>" class="sr-only visually-hidden"><?php echo $campaign->id; ?></label>
						</td>
						<td>
							<?php echo $campaign->id; ?>
						</td>
						<td>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $campaign->id); ?>">
								<?php echo $this->escape($campaign->title); ?>
							</a>
						</td>
						<td class="priority-3">
							<?php echo Date::of($campaign->expire_date)->toLocal("Y-m-d H:ia"); ?>
						</td>
						<td class="priority-3">
							<?php echo $campaign->description; ?>
						</td>
						<td class="priority-3">
							<?php echo Date::of($campaign->campaign_date)->toLocal("Y-m-d H:ia"); ?>
						</td>
						<td class="priority-3">
							<?php echo Date::of($campaign->modified)->toLocal("Y-m-d H:ia"); ?>
						</td>
						<td class="priority-3">
							<?php echo User::one($campaign->modified_by)->name; ?>
						</td>
					</tr>
				<?php $k++; } ?>
			<?php } else { ?>
				<tr>
					<td colspan="5">
						<?php echo Lang::txt('COM_NEWSLETTER_NO_CAMPAIGNS'); ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="display" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
