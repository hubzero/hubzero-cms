<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('Custom Fields'), 'form');
$canDo = Components\Groups\Helpers\Permissions::getActions();
if ($canDo->get('core.manage'))
{
	Toolbar::apply();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::divider();
Toolbar::help('form');

$elements = array();

foreach ($this->fields as $field)
{
	$element = new stdClass;
	$element->label = (string)$field->get('label');
	$element->name  = (string)$field->get('name');
	$element->type  = (string)$field->get('type');
	/*if ($element->type == 'select')
	{
		$element->type = 'dropdown';
	}*/
	if ($element->type == 'paragraph')
	{
		$element->label = (string)$field->get('description');
		$field->set('description', null);
	}
	if ($element->type == 'radio')
	{
		$element->type = 'radio-group';
	}
	if ($element->type == 'checkboxes')
	{
		$element->type = 'checkbox-group';
	}
	if ($element->type == 'calendar')
	{
		$element->type = 'date';
	}
	if ($element->type == 'address')
	{
		$element->type = 'textarea';
		$element->subtype = 'address';
	}
	if ($element->type == 'facultyadvisor')
	{
		$element->type = 'textarea';
		$element->subtype = 'facultyadvisor';
	}
	$element->required = (bool)$field->get('required');
	$element->readonly = (bool)$field->get('readonly');
	$element->disabled = (bool)$field->get('disabled');
	$element->access   = (int)$field->get('access');
	$element->id = (string)$field->get('id');
	$element->description = (string)$field->get('description');
	$element->placeholder = (string)$field->get('placeholder');
	$element->other = (bool)$field->get('option_other');
	$element->blank = (bool)$field->get('option_blank');
	$element->min = (int)$field->get('min');
	$element->max = (int)$field->get('max');
	$element->rows = (int)$field->get('rows');
	$element->access = (string)$field->get('access');
	$element->value = (string)$field->get('default_value');

	$options = $field->options()->ordered()->rows();

	if ($options->count())
	{
		$element->values = array();
		foreach ($options as $option)
		{
			$opt = new stdClass;
			$opt->field_id = (int)$option->get('id');
			$opt->label    = (string)$option->get('label');
			$opt->value    = (string)$option->get('value', $option->get('label'));
			$opt->checked  = (bool)$option->get('checked');
			$dependents = $option->get('dependents', '[]');
			$dependents = $dependents ? $dependents : '[]';
			$dependents = json_decode($dependents);
			$opt->dependents = is_array($dependents) ? implode(', ', $dependents) : $dependents;

			$element->values[] = $opt;
		}
	}

	$elements[] = $element;
}

Html::behavior('framework', true);

$this//->css('jquery.ui.css', 'system')
	->css('formbuilder.css')
	->js('form-builder.min.js')
	->js('customfields.js');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div id="page-1" class="fb-editor">
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="questions" id="form-schema" value="<?php echo $this->escape(json_encode($elements)); ?>" />

	<?php echo Html::input('token'); ?>
</form>

<script type="application/json" id="schema">
	<?php echo json_encode($elements); ?>
</script>
