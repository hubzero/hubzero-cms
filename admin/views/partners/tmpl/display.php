<?php

// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = \Components\Partners\Helpers\Permissions::getActions('partners');

// Toolbar is a helper class to simplify the creation of Toolbar 
// titles, buttons, spacers and dividers in the Admin Interface.
//
// Here we'll had the title of the component and various options
// for adding/editing/etc based on if the user has permission to
// perform such actions.
Toolbar::title(Lang::txt('COM_PARTNERS'));
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
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('partners');

// This line makes sure we're including the javascript framework
Html::behavior('framework');
?>
<!--This section of html is the main view for this page, need to add some code here so that clicking on Patners wont mess up-->
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm">
	<!-- for the search bar -->
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_PARTNERS_FILTER_SEARCH_PLACEHOLDER'); ?>" />
		<input type="submit" value="<?php echo Lang::txt('COM_PARTNERS_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<!--our big table -->
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_PARTNER_TYPE', 'Type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_QUBES_LIASON', 'Liason', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row) : ?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>


				<td class="priority-5">
					<?php echo $row->get('id'); ?>
				</td>

				<!-- can make name linkable or not depending if admin has edit capabilities -->
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('name')); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($row->get('name')); ?>
						</span>
					<?php } ?>
				</td>
				<!-- seeing if state is published, unpublished or trashed-->
				<td class="priority-3">
					<?php
					if ($row->get('state') == 1)
					{
						$alt  = Lang::txt('JPUBLISHED');
						$cls  = 'publish';
						$task = 'unpublish';
					}
					else if ($row->get('state') == 0)
					{
						$alt  = Lang::txt('JUNPUBLISHED');
						$task = 'publish';
						$cls  = 'unpublish';
					}
					else if ($row->get('state') == 2)
					{
						$alt  = Lang::txt('JTRASHED');
						$task = 'publish';
						$cls  = 'trash';
					}
					//makes link clickable if we have edit capabilities
					if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>

				<td class="priority-4">					
						<span>
							<?php echo $this->escape($row->get('partner_type')); ?>
						</span>
				</td>

				<td class="priority-4">
					<span>
						<?php echo $this->escape($row->get('QUBES_liason')); ?>
					</span>
				</td>

			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		endforeach;
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>