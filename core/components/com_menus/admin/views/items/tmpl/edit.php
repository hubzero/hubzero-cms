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

defined('_HZEXEC_') or die();

$isNew      = ($this->item->id == 0);
$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == User::get('id'));
$canDo      = Components\Menus\Helpers\Menus::getActions($this->item->get('parent_id'));

Toolbar::title(Lang::txt($isNew ? 'COM_MENUS_VIEW_NEW_ITEM_TITLE' : 'COM_MENUS_VIEW_EDIT_ITEM_TITLE'), 'menu-add');

// If a new item, can save the item.  Allow users with edit permissions to apply changes to prevent returning to grid.
if ($isNew && $canDo->get('core.create'))
{
	if ($canDo->get('core.edit'))
	{
		Toolbar::apply('items.apply');
	}
	Toolbar::save('items.save');
}

// If not checked out, can save the item.
if (!$isNew && !$checkedOut && $canDo->get('core.edit'))
{
	Toolbar::apply('items.apply');
	Toolbar::save('items.save');
}

// If the user can create new items, allow them to see Save & New
if ($canDo->get('core.create'))
{
	Toolbar::save2new('items.save2new');
}

// If an existing item, can save to a copy only if we have create rights.
if (!$isNew && $canDo->get('core.create'))
{
	Toolbar::save2copy('items.save2copy');
}

if ($isNew)
{
	Toolbar::cancel('items.cancel');
}
else
{
	Toolbar::cancel('items.cancel', 'JTOOLBAR_CLOSE');
}

Toolbar::divider();

// Get the help information for the menu item.
$lang = App::get('language');

$help = $this->item->help;
if ($lang->hasKey($help->url))
{
	$debug = $lang->setDebug(false);
	$url = Lang::txt($help->url);
	$lang->setDebug($debug);
}
else
{
	$url = $help->url;
}
Toolbar::help('item'); //$help->key, $help->local, $url);

// Include the component HTML helpers.
Html::addIncludePath(Component::path($this->option) . '/helpers/html');

// Load the tooltip behavior.
Html::behavior('framework');
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('modal');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task, type)
	{
		if (task == 'items.setType' || task == 'items.setMenuType') {
			if (task == 'items.setType') {
				$('#item-form').find('input[name="fields[type]"]').val(type);
				$('#fieldtype').val('type');
			} else {
				$('#item-form').find('input[name="fields[menutype]"]').val(type);
			}
			Joomla.submitform('items.setType', $('#item-form'));
		} else if (task == 'items.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, $('#item-form'));
		} else {
			// special case for modal popups validation response
			$('#item-form .modal-value.invalid').each(function(i, field){
				var idReversed = field.id.split("").reverse().join("");
				var separatorLocation = idReversed.indexOf('_');
				var name = idReversed.substr(separatorLocation).split("").reverse().join("")+'name';
				$('#'+name).addClass('invalid');
			});
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&cid[]='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MENUS_ITEM_DETAILS');?></span></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('type'); ?>
					<?php echo $this->form->getInput('type'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<?php if ($this->item->type =='url'): ?>
					<?php $this->form->setFieldAttribute('link', 'readonly', 'false');?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('link'); ?>
						<?php echo $this->form->getInput('link'); ?>
					</div>
				<?php endif; ?>

				<?php if ($this->item->type == 'alias'): ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('aliastip'); ?>
					</div>
				<?php endif; ?>

				<?php if ($this->item->type !='url'): ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('alias'); ?>
						<?php echo $this->form->getInput('alias'); ?>
					</div>
				<?php endif; ?>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('note'); ?>
					<?php echo $this->form->getInput('note'); ?>
				</div>

				<?php if ($this->item->type !=='url'): ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('link'); ?>
						<?php echo $this->form->getInput('link'); ?>
					</div>
				<?php endif ?>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('published'); ?>
							<?php echo $this->form->getInput('published'); ?>
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('menutype'); ?>
					<?php echo $this->form->getInput('menutype'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('parent_id'); ?>
					<?php echo $this->form->getInput('parent_id'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('menuordering'); ?>
					<?php echo $this->form->getInput('menuordering'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('browserNav'); ?>
					<?php echo $this->form->getInput('browserNav'); ?>
				</div>

				<?php if ($this->item->type == 'component') : ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('home'); ?>
						<?php echo $this->form->getInput('home'); ?>
					</div>
				<?php endif; ?>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('template_style_id'); ?>
							<?php echo $this->form->getInput('template_style_id'); ?>
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<?php echo Html::sliders('start', 'menu-sliders-' . $this->item->id); ?>
			<?php //Load  parameters.
				echo $this->loadTemplate('options'); ?>

				<div class="clr"></div>

				<?php if (!empty($this->modules)): ?>
					<?php echo Html::sliders('panel', Lang::txt('COM_MENUS_ITEM_MODULE_ASSIGNMENT'), 'module-options'); ?>
					<fieldset>
						<?php echo $this->loadTemplate('modules'); ?>
					</fieldset>
				<?php endif; ?>

			<?php echo Html::sliders('end'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="" />
			<?php echo $this->form->getInput('component_id'); ?>
			<?php echo Html::input('token'); ?>
			<input type="hidden" id="fieldtype" name="fieldtype" value="" />
		</div>
	</div>
</form>
