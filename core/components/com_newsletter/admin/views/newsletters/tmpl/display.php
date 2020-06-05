<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('newsletter');

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER'), 'newsletter');

if ($canDo->get('core.create'))
{
	Toolbar::addNew();
	Toolbar::custom('duplicate', 'copy', '', 'COM_NEWSLETTER_TOOLBAR_COPY');
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_NEWSLETTER_DELETE_CHECK', 'delete');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
Toolbar::custom('preview', 'preview', '', 'COM_NEWSLETTER_TOOLBAR_PREVIEW');
Toolbar::custom('sendtest', 'sendtest', '', 'COM_NEWSLETTER_TOOLBAR_SEND_TEST');
Toolbar::custom('sendnewsletter', 'send', '', 'COM_NEWSLETTER_TOOLBAR_SEND');
if ($canDo->get('core.admin'))
{
	Toolbar::spacer();
	Toolbar::preferences($this->option, '550');
}

Html::behavior('modal');

// add js
$this->js();
?>

<?php

	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}

	if (!$this->dependency)
	{
		$this->view('dependency')->display();
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span4">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_NEWSLETTER_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span8 align-right">
				<select name="type" id="filter-type" class="filter filter-submit">
					<option value=""<?php if ($this->filters['type'] === '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_ALL_TYPES'); ?></option>
					<option value="html"<?php if ($this->filters['type'] === 'html') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_TYPE_HTML'); ?></option>
					<option value="plain"<?php if ($this->filters['type'] === 'plain') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_TYPE_PLAIN'); ?></option>
				</select>
			</div>
		</div>
</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_FORMAT', 'type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_TEMPLATE', 'template_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_PUBLIC', 'published', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_SENT', 'sent', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_TRACKING', 'tracking', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
				// initiate paging
				echo $this->rows->pagination;

				$k = 0;
				?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->rows->count() > 0) { ?>
				<?php foreach ($this->rows as $newsletter) { ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k; ?>" value="<?php echo $newsletter->id; ?>" class="checkbox-toggle" />
						</td>
						<td>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $newsletter->id); ?>">
								<?php echo $this->escape($newsletter->name); ?>
							</a>
						</td>
						<td class="priority-3">
							<?php echo ($newsletter->type == 'html') ? Lang::txt('COM_NEWSLETTER_FORMAT_HTML') : Lang::txt('COM_NEWSLETTER_FORMAT_PLAIN'); ?>
						</td>
						<td class="priority-4">
							<?php
								$activeTemplate = '';
								if ($newsletter->get('template_id') == '-1')
								{
									$activeTemplate = Lang::txt('COM_NEWSLETTER_NO_TEMPLATE');
								}
								else
								{
									$activeTemplate = $newsletter->template->name;
								}

								echo ($activeTemplate) ? $activeTemplate : Lang::txt('COM_NEWSLETTER_NO_TEMPLATE_FOUND');
							?>
						</td>
						<td class="priority-2">
							<?php if ($newsletter->published) : ?>
								<a class="state yes" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unpublish&id=' . $newsletter->id . '&' . Session::getFormToken() . '=1'); ?>">
									<span><?php echo Lang::txt('JYES'); ?></span>
								</a>
							<?php else : ?>
								<a class="state no" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $newsletter->id . '&' . Session::getFormToken() . '=1'); ?>">
									<span><?php echo Lang::txt('JYES'); ?></span>
								</a>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($newsletter->sent) : ?>
								<span class="state yes"><span><?php echo Lang::txt('JYES'); ?></span></span>
							<?php else : ?>
								<span class="state no"><span><?php echo Lang::txt('JNO'); ?></span></span>
							<?php endif; ?>
						</td>
						<td class="priority-3">
							<?php if ($newsletter->tracking) : ?>
								<span class="state yes"><span><?php echo Lang::txt('JYES'); ?></span></span>
							<?php else : ?>
								<span class="state no"><span><?php echo Lang::txt('JNO'); ?></span></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php $k++;
					} ?>
			<?php } else { ?>
				<tr>
					<td colspan="7">
						<?php echo Lang::txt('COM_NEWSLETTER_NO_NEWSLETTER'); ?>
						<button id="add-newsletter"><?php echo Lang::txt('COM_NEWSLETTER_CREATE_NEWSLETTER'); ?></button>
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
