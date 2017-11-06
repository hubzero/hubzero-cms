<?php

// No direct access
defined('_HZEXEC_') or die();
	
// Get the permissions helper
$canDo = \Components\Partners\Helpers\Permissions::getActions('partner_types');

// Toolbar is a helper class to simplify the creation of Toolbar 
// titles, buttons, spacers and dividers in the Admin Interface.
//
// Here we'll had the title of the component and various options
// for adding/editing/etc based on if the user has permission to
// perform such actions.
Toolbar::title(Lang::txt('COM_PARTNERS_PARTNER_TYPES'));
if ($canDo->get('core.admin'))
	{
	JToolBarHelper::preferences($this->option, '550');
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
Toolbar::help('partner_types');

// This line makes sure we're including the javascript framework
Html::behavior('framework');
?>
<!--This section of html is the main view for this page-->
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
			<!--VERY IMPORTANT MESSAGE: where there is id, internal_name, external_name.., this is how it is sorted, make sure name is same as database-->
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_INTERNAL_NAME', 'internal', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_EXTERNAL_NAME', 'external', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_PARTNERS_COL_DESCRIPTION', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		// our for loop, i is incremented, k is weird, not sure of its use
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

				<!--This makes the internal name clickable or not, if the user has edit abilities, we can click the name to take us to the edit page -->
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('internal')); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($row->get('internal')); ?>
						</span>
					<?php } ?>
				</td>

				<!--External name -->
				<td class="priority-4">					
						<span>
							<?php echo $this->escape($row->get('external')); ?>
						</span>
				</td>
				<!-- this calls the description method in the partner type model and passes 'parsed' which echos the description without the format tag-->
				<td class="priority-4">
					<span>
						<?php echo $this->escape($row->description('clean')); ?>
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
	<!--These are important, especially box checked!!, allows you to use the edit/publish/delete buttons once you have checked something -->
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo Html::input('token'); ?>
</form>