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

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Careerplans\Helpers\Permissions::getActions('form');

Toolbar::title(Lang::txt('COM_CAREERPLANS') . ': ' . Lang::txt('COM_CAREERPLANS_MENU_FORMS'), 'form');
if ($canDo->get('core.edit') || $canDo->get('core.create'))
{
	Toolbar::apply();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::divider();
Toolbar::help('form');

$data = array();

foreach ($this->fieldsets as $fieldset)
{
	$elements = array();

	foreach ($fieldset->fields as $field)
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
		/*if ($element->type == 'scale')
		{
			$element->type = 'radio-group';
			$element->subtype = 'scale';
		}*/
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
				$opt->dependents = implode(', ', $dependents);

				$element->values[] = $opt;
			}
		}

		$elements[] = $element;
	}

	$data[$fieldset->get('name')] = $elements;
}

Html::behavior('framework', true);

$this//->css('jquery.ui.css', 'system')
	->css('formbuilder.css')
	->js('form-builder.min.js')
	->js('control_plugins/scale.min.js')
	->js('control_plugins/goals.min.js');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<ul id="tabs">
		<?php
		$i = 1;
		foreach ($this->fieldsets as $fieldset)
		{
			?>
			<li>
				<a href="#page-<?php echo $fieldset->get('name'); ?>"><?php echo Lang::txt('Page %s', $i); ?></a>
			</li>
			<?php
			$i++;
		}
		?>
		<li id="add-page-tab"><a href="#new-page"><?php echo Lang::txt('+ Page'); ?></a></li>
	</ul>
	<?php
	$i = 1;
	foreach ($this->fieldsets as $fieldset)
	{
		?>
		<div id="page-<?php echo $fieldset->get('name'); ?>" class="fb-editor">
			<div class="form-page-header">
				<?php if ($i > 1) { ?>
					<div class="fieldset-actions">
						<a data-page="page-<?php echo $fieldset->get('name'); ?>" class="del-button btn icon-cancel delete-page" title="<?php echo Lang::txt('Remove page'); ?>">
							<?php echo Lang::txt('Remove page'); ?>
						</a>
					</div>
				<?php } ?>
				<div class="input-wrap">
					<label for="fieldset-title<?php echo ($i - 1); ?>"><?php echo Lang::txt('Page Title'); ?></label>
					<input type="text" name="fieldset[][title]" id="fieldset-title<?php echo ($i - 1); ?>" value="<?php echo $this->escape($fieldset->get('label')); ?>" />
				</div>
			</div>
		</div>
		<?php
		$i++;
	}
	?>
	<div id="new-page">
		<div class="form-page-header">
			<div class="fieldset-actions">
				<a data-page="page-0" class="del-button btn icon-cancel delete-page" title="<?php echo Lang::txt('Remove page'); ?>">
					<?php echo Lang::txt('Remove page'); ?>
				</a>
			</div>
			<div class="input-wrap">
				<label><?php echo Lang::txt('Page Title'); ?></label>
				<input type="text" name="fieldset[][title]" value="" />
			</div>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="questions" id="form-schema" value="<?php echo $this->escape(json_encode($data)); ?>" />

	<?php echo Html::input('token'); ?>
</form>

<script type="text/javascript">
	var fbInstances = [];
	var colors = ['#f0e7f4', '#e9f1fa', '#f9fbe5', '#ecf8f6', '#fbf7dc', '#FEEFB2', '#FFDDDC', '#DEEDFF', '#DCCFFC'];
	var current = 0;

	jQuery(document).ready(function($){
		var $fbPages = $(document.getElementById("item-form"));
		var addPageTab = document.getElementById("add-page-tab");

		var options = {
			disableFields: ['autocomplete', 'file', 'button', 'header'],
			disabledActionButtons: ['clear', 'data', 'save'],
			editOnAdd: true,
			//disableInjectedStyle: true,
			disabledAttrs: ['inline', 'style', 'access', 'className', 'subtype'],
			typeUserAttrs: {
				'radio-group': {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				'checkbox-group': {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				textarea: {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				select: {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				date: {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				paragraph: {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				hidden: {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				number: {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				},
				text: {
					id: {
						type: 'hidden',
						label: '&nbsp;'
					}
				}
			},
			/*fields: [
				{
					label: 'Likert Scale',
					name: 'scale',
					attrs: {
						type: 'scale'
					},
					icon: '1-5'
				},
				{
					label: 'Goals',
					name: 'goals',
					attrs: {
						type: 'goals'
					},
					icon: 'FA'
				}
			]*/
		};

		/*var templates = {
			scale: function(fieldData) {
				return {
					field: '<span id="' + fieldData.name + '">',
					onRender: function() {
						$(document.getElementById(fieldData.name)).rateYo({
							rating: 3.6
						});
					}
				};
			}
		};*/

		$fbPages.tabs();

		addPageTab.onclick = function() {
			var tabCount = document.getElementById("tabs").children.length,
			tabId = "page-" + tabCount.toString(), //(title ? title : tabCount.toString()),
			$newPageTemplate = $(document.getElementById("new-page")),
			$newPage = $newPageTemplate
				.clone()
				.attr("id", tabId)
				.addClass("fb-editor"),
			$newTab = $(this).clone().removeAttr("id"),
			$tabLink = $("a", $newTab)
				.attr("href", "#" + tabId)
				.text("Page " + tabCount);

			$newPage.insertBefore($newPageTemplate);
			$newTab.insertBefore(this);

			$fbPages.tabs("refresh");
			$fbPages.tabs("option", "active", tabCount - 1);

			fbInstances.push($newPage.formBuilder(options));
		};

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

		<?php foreach ($data as $fs => $elements) { ?>

			var options<?php echo $fs; ?> = jQuery.extend(true, {}, options);

			options<?php echo $fs; ?>.defaultFields = <?php echo json_encode($elements); ?>;

			fb = $('#page-<?php echo $fs; ?>').formBuilder(options<?php echo $fs; ?>);

			fb.promise.then(function(formbuilder) {
				$('.option-dependents').each(function (i, el){
					var de = $(el);

					setDependentColors(de);
				});
			});

			fbInstances.push(fb);

		<?php } ?>

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

		$('#form-schema').val('[' + allData + ']');

		submitform(pressbutton);
	}
</script>
