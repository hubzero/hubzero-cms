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
	->js('form-builder.min.js');
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

<script type="text/javascript">
	var fbInstances = [];
	var colors = ['#f0e7f4', '#e9f1fa', '#f9fbe5', '#ecf8f6', '#fbf7dc', '#FEEFB2', '#FFDDDC', '#DEEDFF', '#DCCFFC'];
	var current = 0;

	jQuery(document).ready(function($){
		var $fbPages = $(document.getElementById("item-form"));
		var addPageTab = document.getElementById("add-page-tab");
		var extraFields = { 
			'access': {
				label: 'Privacy',
				options: {
					'0': 'public',
					'1': 'registered',
					'2': 'private'
				},
				name: 'access'
			},
			'id': {
				type: 'hidden',
				label: '&nbsp;'
			}
		};

		var options = {
			disableFields: ['autocomplete', 'file', 'button', 'header'],
			disabledActionButtons: ['clear', 'data', 'save'],
			editOnAdd: true,
			//disableInjectedStyle: true,
			disabledAttrs: ['inline', 'style', 'access', 'className', 'subtype'],
			typeUserAttrs: {
				'radio-group': extraFields, 
				'checkbox-group': extraFields, 
				'textarea': extraFields, 
				'select': extraFields, 
				'date': extraFields, 
				'paragraph': extraFields,
				'hidden': extraFields, 
				'number': extraFields, 
				'text': extraFields 
			}
		};

		$fbPages.tabs();

		$('#item-form').on('click', '.delete-page', function (e){
			e.preventDefault();

			//$fbPages.tabs('remove', );

			var par = $($(this).parent().parent().parent());
			var tabIdStr = par.attr('id');
			var idx = $('.fb-editor').index(par);

			var hrefStr = "a[href='#" + tabIdStr + "']";
			$(hrefStr).closest("li").remove();
			par.remove();

			var tabCount = document.getElementById("tabs").children.length;

			$fbPages.tabs("refresh");
			$fbPages.tabs("option", "active", tabCount - 2);

			fbInstances.splice(idx, 1);
		});


			var options1 = jQuery.extend(true, {}, options);

			options1.defaultFields = <?php echo json_encode($elements); ?>;
			fb = $('#page-1').formBuilder(options1);

			fb.promise.then(function(formbuilder) {
				$('.option-dependents').each(function (i, el){
					var de = $(el);

					setDependentColors(de);
				});
			});

			fbInstances.push(fb);


		$('#item-form').on('change focus blur', '.option-dependents', function (e){
			var de = $(this);

			setDependentColors(de);
		});
	});

	function setDependentColors(de)
	{
		if (de.val()) {
			var clr = de.attr('data-color');

			if (!clr) {
				clr = colors[current]; //colors[Math.floor(Math.random() * colors.length)];
				current++;
				current = current >= colors.length ? 0 : current;

				de.css('background-color', clr);
				de.attr('data-color', clr);
			}

			de.attr('data-fields', de.val());

			var fields = de.val().split(',');
			for (var i = 0; i < fields.length; i++)
			{
				var field = fields[i].replace(/\s/, '');
				var fld = $('.field-' + field + '-preview');
				if (fld.length) {
					var li = $(fld).closest('li.form-field').css('background-color', clr);
				}
			}
		} else if (de.attr('data-color')) {
			var fields = de.attr('data-fields').split(',');
			for (var i = 0; i < fields.length; i++)
			{
				var field = fields[i].replace(/\s/, '');
				var fld = $('.field-' + field + '-preview');
				if (fld.length) {
					var li = $(fld).closest('li.form-field').css('background-color', '#fff');
				}
			}

			de.attr('data-fields', '');
			de.attr('data-color', '');
			de.css('background-color', '#fff');
		}
	}

	function submitbutton(pressbutton)
	{
		var form = document.getElementById('adminForm');

		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}

		var sbmt = true;
		$('.fb-editor .form-page-header input').each(function (i, el){
			if (!$(el).val()) {
				alert('Please provide a title for each page.');
				sbmt = false;
			}
		});
		if (!sbmt) {
			return;
		}

		var allData = fbInstances.map(function(fb) {
			return fb.formData;
		});

		$('#form-schema').val(allData);

		submitform(pressbutton);
	}
</script>
