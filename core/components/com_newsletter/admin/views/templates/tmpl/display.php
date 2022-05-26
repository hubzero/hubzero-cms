<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('template');

//set the title
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES'), 'template.png');

//add toolbar buttons
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
	Toolbar::spacer();
	Toolbar::deleteList('COM_NEWSLETTER_TEMPLATE_DELETE_CHECK', 'delete');
}
if ($canDo->get('core.admin'))
{
	Toolbar::spacer();
	Toolbar::preferences($this->option, '550');
}
?>
<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->templates) > 0) : ?>
				<?php foreach ($this->templates as $k => $template) :
					if ($template->deleted):
						continue;
					endif;
					?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $template->id; ?>" class="checkbox-toggle" />
							<label for="cb<?php echo $k; ?>" class="sr-only visually-hidden"><?php echo $template->id; ?></label>
						</td>
						<td>
							<?php if (!$template->editable) : ?>
								<?php echo $template->name; ?>
								<br/>
								<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_NOT_EDITABLE_OR_DELETABLE'); ?></span>
							<?php else: ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $template->id); ?>">
									<?php echo $template->name; ?>
								</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2">
						<?php echo Lang::txt('COM_NEWSLETTER_NO_TEMPLATES'); ?>
						<a onclick="javascript:submitbutton('add')" href="#"><?php echo Lang::txt('COM_NEWSLETTER_NO_TEMPLATES_CREATE'); ?></a>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="add" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
