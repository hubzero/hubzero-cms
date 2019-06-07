<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$isNew      = ($this->item->id == 0);
$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == User::get('id'));
$canDo      = Components\Modules\Helpers\Modules::getActions();
$item       = $this->get('Item');

Toolbar::title(Lang::txt('COM_MODULES_MANAGER_MODULE', Lang::txt($this->item->module)), 'module');

// If not checked out, can save the item.
if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create')))
{
	Toolbar::apply();
	Toolbar::save();
}
if (!$checkedOut && $canDo->get('core.create'))
{
	Toolbar::save2new();
}
	// If an existing item, can save to a copy.
if (!$isNew && $canDo->get('core.create'))
{
	Toolbar::save2copy();
}
Toolbar::cancel();
Toolbar::help('module');

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('combobox');

$this->js();

$hasContent = empty($this->item->get('module')) || $this->item->module == 'custom' || $this->item->module == 'mod_custom';
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('title'); ?><br />
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('showtitle'); ?><br />
					<?php echo $this->form->getInput('showtitle'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('position'); ?><br />
					<?php echo $this->form->getInput('position'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('ordering'); ?><br />
					<?php echo $this->form->getInput('ordering'); ?>
				</div>

				<?php if ($this->item->xml && (string) $this->item->xml->name != 'Login Form'): ?>
					<div class="grid">
						<div class="col span6">
							<div class="input-wrap">
								<?php echo $this->form->getLabel('published'); ?><br />
								<?php echo $this->form->getInput('published'); ?>
							</div>
						</div>
						<div class="col span6">
				<?php endif; ?>
							<div class="input-wrap">
								<?php echo $this->form->getLabel('access'); ?><br />
								<?php echo $this->form->getInput('access'); ?>
							</div>
				<?php if ($this->item->xml && (string) $this->item->xml->name != 'Login Form'): ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($this->item->xml && (string) $this->item->xml->name != 'Login Form'): ?>
					<div class="grid">
						<div class="col span6">
							<div class="input-wrap">
								<?php echo $this->form->getLabel('publish_up'); ?><br />
								<?php echo $this->form->getInput('publish_up'); ?>
							</div>
						</div>
						<div class="col span6">
							<div class="input-wrap">
								<?php echo $this->form->getLabel('publish_down'); ?><br />
								<?php echo $this->form->getInput('publish_down'); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('language'); ?><br />
					<?php echo $this->form->getInput('language'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('note'); ?><br />
					<?php echo $this->form->getInput('note'); ?>
				</div>

				<?php if ($this->item->id) : ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('id'); ?><br />
						<?php echo $this->form->getInput('id'); ?>
					</div>
				<?php endif; ?>
			</fieldset>

			<?php if ($hasContent) : ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_MODULES_CUSTOM_OUTPUT'); ?></span></legend>

					<div class="input-wrap">
						<?php echo $this->form->getLabel('content'); ?><br />
						<?php echo $this->form->getInput('content'); ?>
					</div>
				</fieldset>
			<?php endif; ?>

			<?php if ($this->item->client_id == 0) :?>
				<?php echo $this->loadTemplate('assignment'); ?>
			<?php endif; ?>
		</div>

		<div class="col span5">
			<table class="meta">
				<tbody>
				<?php if ($this->item->id) : ?>
				<?php endif; ?>
					<tr>
						<th scope="row">
							<?php echo Lang::txt('COM_MODULES_HEADING_MODULE'); ?>
							<?php echo $this->form->getLabel('module'); ?>
						</th>
						<td>
							<?php echo $this->form->getInput('module'); ?>
							<?php
							if ($this->item->xml)
							{
								echo ($text = (string) $this->item->xml->name) ? Lang::txt($text) : $this->item->module;
							}
							else
							{
								echo Lang::txt('COM_MODULES_ERR_XML');
							}
							?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo Lang::txt('Client'); ?>
							<?php echo $this->form->getLabel('client_id'); ?>
						</th>
						<td>
							<?php echo $this->form->getInput('client_id'); ?>
							<?php echo $this->item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR'); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo Lang::txt('COM_MODULES_MODULE_DESCRIPTION'); ?>
						</th>
						<td>
							<?php if ($this->item->xml) : ?>
								<?php if ($text = trim($this->item->xml->description)) : ?>
									<?php echo Lang::txt($text); ?>
								<?php endif; ?>
							<?php else : ?>
								<p class="error"><?php echo Lang::txt('COM_MODULES_ERR_XML'); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php echo Html::sliders('start', 'module-sliders'); ?>
				<?php echo $this->loadTemplate('options'); ?>
			<?php echo Html::sliders('end'); ?>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
