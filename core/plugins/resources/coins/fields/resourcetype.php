<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Fields\Select;
use Html;
use App;

/**
 * Renders a list of resource types
 */
class Resourcetype extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'resourcetype';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		/*if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = Dropdown::genericlist($options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';
		}
		// Create a regular list.
		else
		{
			$html[] = Dropdown::genericlist($options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);

			if ($this->element['option_other'])
			{
				$found = false;

				foreach ($options as $option)
				{
					if ($option->value == $this->value)
					{
						$found = true;
					}
				}
				$html[] = '<input type="text" name="' . $this->getName($this->fieldname . '_other') . '" value="' . ($found ? '' : htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')) . '" placeholder="' . (empty($this->placeholder) ?  App::get('language')->txt('Other...') : htmlspecialchars($this->placeholder, ENT_COMPAT, 'UTF-8')) . '" />';
			}
		}*/
		if (empty($this->value))
		{
			$this->value = array(
				array(
					'resource' => 0,
					'genre' => 'article'
				)
			);
		}

		foreach ($this->value as $i => $value)
		{
			$html[] = '<fieldset class="coinstypes">
				<div class="input-wrap">
					<label id="fields_params_type_0-lbl" for="fields_params_type_0">' . Lang::txt('PLG_RESOURCES_COINS_RESOURCETYPE_LABEL') . '</label><br>
					<select id="fields_params_type_0" name="fields[params][type][0][resource]">
						<option value="">' . Lang::txt('PLG_RESOURCES_COINS_RESOURCETYPE_SELECT') . '</option>';
						foreach ($options as $option)
						{
							$html[] = '<option value="' . $option->value . '"' . ($value['resource'] == $option->value ? ' selected="selected"' : '') . '>' . $option->text . '</option>';
						}
						$html[] = '
					</select>
				<div class="input-wrap">
				</div>
					<label id="fields_params_genre_0-lbl" for="fields_params_genre_0">' . Lang::txt('PLG_RESOURCES_COINS_COINTYPE_LABEL') . '</label><br>
					<select id="fields_params_genre_0" name="fields[params][type][0][genre]">
						<option value="article"' . ($value['genre'] == 'article' ? ' selected="selected"' : '') . '>article</option>
						<option value="book"' . ($value['genre'] == 'book' ? ' selected="selected"' : '') . '>book</option>
						<option value="bookitem"' . ($value['genre'] == 'bookitem' ? ' selected="selected"' : '') . '>bookitem</option>
					</select>
				</div>
				</fieldset>';
		}

		$html[] = '
		<script id="plg_resources_coins" type="text/x-handlebars-template">
			<fieldset class="coinstypes">
				<div class="input-wrap">
					<label id="fields_params_type_{{index}}-lbl" for="fields_params_type_{{index}}">' . Lang::txt('PLG_RESOURCES_COINS_RESOURCETYPE_LABEL') . '</label><br>
					<select id="fields_params_type_{{index}}" name="fields[params][type][{{index}}][resource]">
						<option value="">' . Lang::txt('PLG_RESOURCES_COINS_RESOURCETYPE_SELECT') . '</option>';
						foreach ($options as $option)
						{
							$html[] = '<option value="' . $option->value . '">' . $option->text . '</option>';
						}
						$html[] = '
					</select>
				<div class="input-wrap">
				</div>
					<label id="fields_params_genre_{{index}}-lbl" for="fields_params_genre_{{index}}">' . Lang::txt('PLG_RESOURCES_COINS_COINTYPE_LABEL') . '</label><br>
					<select id="fields_params_genre_{{index}}" name="fields[params][type][{{index}}][genre]">
						<option value="article">article</option>
						<option value="book">book</option>
						<option value="bookitem">bookitem</option>
					</select>
				</div>
			</fieldset>
		</script>';

		$html[] = '<button class="button" id="add-resource-type">' . Lang::txt('PLG_RESOURCES_COINS_ADD_TYPE') . '</button>';

		/*$html[] = '
		<script id="plg_resources_coins" type="text/x-handlebars-template">
			<div class="input-wrap ">
				<label id="fields_params_type_coins_-lbl" for="fields_params_type_coins_" class="hasTip" title="<div class=&quot;tip-title&quot;>PLG_RESOURCES_COINS_PAYLOAD_LABEL</div><div class=&quot;tip-text&quot;>PLG_RESOURCES_COINS_PAYLOAD_DESC</div>">PLG_RESOURCES_COINS_PAYLOAD_LABEL</label><br>
				<select id="fields_params_type_coins_" name="fields[params][type][{{index}}][coin]">
				</select>
				<select id="fields_params_type_coins_" name="fields[params][type][{{index}}][field]">';
		$types = \Components\Resources\Models\Type::getMajorTypes();

		foreach ($types as $type)
		{
			$fields = json_decode($type->get('customFields'));

			if (!empty($fields))
			{
				$html[] = '<opgroup label="' . stripslashes($type->type)) . '">';
				foreach ($fields->fields as $field)
				{
					$html[] = '<option value="' . $type->id . ':' . $field->name . '">' . $field->label . '</option>';
				}
				$html[] = '</opgroup>';
			}
		}
		$html[] = '</select>
				</select>
			</div>
		</script>';*/

		Html::behavior('framework', true);

		App::get('document')->addScript(\Request::root() . 'core/assets/js/handlebars.js');
		App::get('document')->addScript(\Request::root() . 'core/plugins/resources/coins/assets/js/params.js?v=' . filemtime(dirname(__DIR__) . '/assets/js/params.js'));

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		include_once \Component::path('com_resources') . DS . 'models' . DS . 'type.php';

		$types = \Components\Resources\Models\Type::getMajorTypes();

		$options = array();

		foreach ($types as $type)
		{
			$options[] = Html::select('option', $type->id, $type->type, 'value', 'text');
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
