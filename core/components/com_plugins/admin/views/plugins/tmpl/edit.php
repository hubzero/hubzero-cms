<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Request::setVar('hidemainmenu', true);

$canDo = \Components\Plugins\Helpers\Plugins::getActions();

Toolbar::title(Lang::txt('COM_PLUGINS_MANAGER_PLUGIN', Lang::txt($this->item->name)), 'plugin');
// If not checked out, can save the item.
if ($canDo->get('core.edit'))
{
	Toolbar::apply('apply');
	Toolbar::save('save');
}
Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
Toolbar::divider();
Toolbar::help('plugin');

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=com_plugins'); ?>" method="post" name="adminForm" id="item-form" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS') ?></span></legend>

				<!--
				<div class="input-wrap">
					<?php echo $this->form->getLabel('name'); ?><br />
					<?php echo $this->form->getInput('name'); ?>
					<span class="readonly plg-name"><?php echo Lang::txt($this->item->name);?></span>
				</div>
				-->

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('enabled'); ?><br />
							<?php echo $this->form->getInput('enabled'); ?>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('access'); ?><br />
							<?php echo $this->form->getInput('access'); ?>
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('ordering'); ?><br />
					<?php echo $this->form->getInput('ordering'); ?>
				</div>
				<?php /*
				<div class="input-wrap">
					<?php echo $this->form->getLabel('folder'); ?><br />
					<?php echo $this->form->getInput('folder'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('element'); ?><br />
					<?php echo $this->form->getInput('element'); ?>
				</div>

				<?php if ($this->item->extension_id) : ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('extension_id'); ?><br />
						<?php echo $this->form->getInput('extension_id'); ?>
					</div>
				<?php endif; ?>
				*/ ?>
			</fieldset>

			<table class="meta">
				<tbody>
					<tr>
						<th>
							<?php echo Lang::txt('COM_PLUGINS_FIELD_NAME_LABEL'); ?>
						</th>
						<td>
							<?php echo $this->escape($this->item->name); ?>
							<?php echo $this->form->getInput('name'); ?>
						</td>
					</tr>
					<?php if ($this->item->extension_id) : ?>
						<tr>
							<th>
								<?php echo Lang::txt('JGLOBAL_FIELD_ID_LABEL'); ?>
							</th>
							<td>
								<?php echo $this->escape($this->item->extension_id); ?>
								<input type="hidden" name="fields[extension_id]" id="field_extension_id" value="<?php echo $this->item->extension_id; ?>" />
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<th>
							<?php echo Lang::txt('COM_PLUGINS_FIELD_FOLDER_LABEL'); ?>
						</th>
						<td>
							<?php echo $this->escape($this->item->folder); ?>
							<input type="hidden" name="fields[folder]" id="field_folder" value="<?php echo $this->escape($this->item->folder); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php echo Lang::txt('COM_PLUGINS_FIELD_ELEMENT_LABEL'); ?>
						</th>
						<td>
							<?php echo $this->escape($this->item->element); ?>
							<input type="hidden" name="fields[element]" id="field_element" value="<?php echo $this->escape($this->item->element); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php echo Lang::txt('JGLOBAL_DESCRIPTION'); ?>
						</th>
						<td>
							<?php if ($this->item->xml) : ?>
								<?php if ($text = trim($this->item->xml->description)) : ?>
									<?php echo Lang::txt($text); ?>
								<?php endif; ?>
							<?php else : ?>
								<p class="error"><?php echo Lang::txt('COM_PLUGINS_XML_ERR'); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<?php if ($this->item->modified && $this->item->modified != '0000-00-00 00:00:00') : ?>
						<tr>
							<th>
								<?php echo Lang::txt('JGLOBAL_FIELD_MODIFIED_LABEL'); ?>
							</th>
							<td>
								<time datetime="<?php echo $this->escape($this->item->modified); ?>"><?php echo $this->escape(Date::of($this->item->modified)->toLocal()); ?></time>
							</td>
						</tr>
					<?php endif; ?>
					<?php if ($this->item->modified_by) : ?>
						<tr>
							<th>
								<?php echo Lang::txt('JGLOBAL_FIELD_MODIFIED_BY_LABEL'); ?>
							</th>
							<td>
								<?php
								$modifier = User::getInstance($this->item->modified_by);
								echo $this->escape($modifier->get('name', Lang::txt('COM_PLUGINS_UNKNOWN')) . ' (' . $this->item->modified_by . ')');
								?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col span5">
			<?php echo Html::sliders('start', 'plugin-sliders-' . $this->item->extension_id); ?>

				<?php echo $this->loadTemplate('options'); ?>

				<div class="clr"></div>

			<?php echo Html::sliders('end'); ?>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo (int) $this->item->extension_id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
	<input type="hidden" name="component" value="<?php echo Request::getCmd('component', ''); ?>" />
</form>
