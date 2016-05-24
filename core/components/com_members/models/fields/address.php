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

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Geocode\Geocode;
use Hubzero\Html\Builder\Behavior;
use Hubzero\Html\Builder\Select as Dropdown;
use App;

/**
 * Supports addresses.
 */
class Address extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Address';

	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected static $countries = null;

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio addresses ' . (string) $this->element['class'] . '"' : ' class="radio addresses"';

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		$found = false;

		$values = $this->value;
		$values = is_array($values) ? $values : array($values);

		$lang = App::get('language');

		// Build the radio field output.
		foreach ($values as $i => $value)
		{
			$value = json_decode((string)$value);

			if (!$value || json_last_error() !== JSON_ERROR_NONE)
			{
				$value = new \stdClass;
				$value->address1  = '';
				$value->address2  = '';
				$value->postal    = '';
				$value->city      = '';
				$value->region    = '';
				$value->country   = '';
				$value->latitude  = '';
				$value->longitude = '';
			}

			$html[] = '<ul class="address-field">';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id . $i . '">' . $lang->txt('Street') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . $i . '" name="' . $this->name . '[' . $i . '][address1]" placeholder="Street" value="' . htmlspecialchars($value->address1, ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id . $i . '">' . $lang->txt('Street 2') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . $i . '" name="' . $this->name . '[' . $i . '][address2]" placeholder="Street 2" value="' . htmlspecialchars($value->address2, ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id . $i . '">' . $lang->txt('City') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . $i . '" name="' . $this->name . '[' . $i . '][city]" placeholder="City" value="' . htmlspecialchars($value->city, ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<div class="grid">';
			$html[] = '<div class="col span6">';
			$html[] = '<label for="' . $this->id . $i . '">' . $lang->txt('Postal code') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . $i . '" name="' . $this->name . '[' . $i . '][postal]" placeholder="Postal code" value="' . htmlspecialchars($value->postal, ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</div>';
			$html[] = '<div class="col span6 omega">';
			$html[] = '<label for="' . $this->id . $i . '">' . $lang->txt('State/Region') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . $i . '" name="' . $this->name . '[' . $i . '][region]" placeholder="State/Region" value="' . htmlspecialchars($value->region, ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id . $i . '">' . $lang->txt('Country') . '</label>';
			$html[] = Dropdown::genericlist($options, $this->name . '[' . $i . '][country]', '', 'value', 'text', $value->country, $this->id . $i);
			$html[] = '<input type="hidden" id="' . $this->id . $i . '" name="' . $this->name . '[' . $i . '][latitude]" value="' . htmlspecialchars($value->latitude, ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '<input type="hidden" id="' . $this->id . $i . '" name="' . $this->name . '[' . $i . '][longitude]" value="' . htmlspecialchars($value->longitude, ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '</ul>';
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		Behavior::framework(true);
		App::get('document')->addScriptDeclaration("
			jQuery(document).ready(function($){
				if ($('.addresses').length > 0) {
					var fieldset = $('.addresses');
					var btn = $('<button>Add address</button>').on('click', function(e){
						e.preventDefault();

						var grp = fieldset
							.find('.address-field')
							.last()
							.clone();
						grp.find('input').each(function(){
							this.name = this.name.replace(/\[(\d+)\]/,function(str,p1){return '[' + (parseInt(p1,10)+1) + ']';});
						});
						grp.find('select').each(function(){
							this.name = this.name.replace(/\[(\d+)\]/,function(str,p1){return '[' + (parseInt(p1,10)+1) + ']';});
						});
						grp.appendTo(fieldset);
					});
					fieldset.after(btn);
				}
			});
		");

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		if (!self::$countries)
		{
			self::$countries = array();

			$countries = Geocode::countries();

			if ($countries && !empty($countries))
			{
				self::$countries = $countries;
			}
		}

		$options[] = Dropdown::option('', App::get('language')->txt('- Select -'), 'value', 'text');

		foreach (self::$countries as $option)
		{
			// Create a new option object based on the <option /> element.
			$tmp = Dropdown::option(
				(string) $option->code,
				App::get('language')->alt(trim((string) $option->name), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text'
			);

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
