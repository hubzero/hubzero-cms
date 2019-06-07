<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$class = $this->element['class'] ? ' class="radio addresses-' . $this->id . ' ' . (string) $this->element['class'] . '"' : ' class="radio addresses-' . $this->id . '"';

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
			if (is_string($value))
			{
				$value = json_decode((string)$value, true);
			}

			if (!$value || json_last_error() !== JSON_ERROR_NONE)
			{
				$value = array();
				$value['address1']  = '';
				$value['address2']  = '';
				$value['postal']    = '';
				$value['city']      = '';
				$value['region']    = '';
				$value['country']   = '';
				$value['latitude']  = '';
				$value['longitude'] = '';
			}

			$html[] = '<div class="address-field-wrap">';
			$html[] = '<ul class="address-field">';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id .'-'.  $i . '-address1">' . $lang->txt('Street') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . '-'. $i . '-address1" name="' . $this->name . '[' . $i . '][address1]" placeholder="Street" value="' . htmlspecialchars($value['address1'], ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id . '-'. $i . '-address2">' . $lang->txt('Street 2') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . '-'. $i . '-address2" name="' . $this->name . '[' . $i . '][address2]" placeholder="Street 2" value="' . htmlspecialchars($value['address2'], ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id . '-'. $i . '-city">' . $lang->txt('City') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . '-'. $i . '-city" name="' . $this->name . '[' . $i . '][city]" placeholder="City" value="' . htmlspecialchars($value['city'], ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<div class="grid">';
			$html[] = '<div class="col span6">';
			$html[] = '<label for="' . $this->id . '-'. $i . '-postal">' . $lang->txt('Postal code') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . '-'. $i . '-postal" name="' . $this->name . '[' . $i . '][postal]" placeholder="Postal code" value="' . htmlspecialchars($value['postal'], ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</div>';
			$html[] = '<div class="col span6 omega">';
			$html[] = '<label for="' . $this->id . '-'. $i . '-region">' . $lang->txt('State/Region') . '</label>';
			$html[] = '<input type="text" id="' . $this->id . '-'. $i . '-region" name="' . $this->name . '[' . $i . '][region]" placeholder="State/Region" value="' . htmlspecialchars($value['region'], ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = '<label for="' . $this->id . '-'. $i . '-country">' . $lang->txt('Country') . '</label>';
			$html[] = Dropdown::genericlist($options, $this->name . '[' . $i . '][country]', '', 'value', 'text', $value['country'], $this->id . '-'. $i . '-country');
			$html[] = '<input type="hidden" id="' . $this->id . '-'. $i . '" name="' . $this->name . '[' . $i . '][latitude]" value="' . htmlspecialchars($value['latitude'], ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '<input type="hidden" id="' . $this->id . '-'. $i . '" name="' . $this->name . '[' . $i . '][longitude]" value="' . htmlspecialchars($value['longitude'], ENT_COMPAT, 'UTF-8') . '" />';
			$html[] = '</li>';
			$html[] = '</ul>';
			$html[] = '</div>';
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		Behavior::framework(true);
		App::get('document')->addScriptDeclaration("
			function manageProfileAddresses() {
				if ($('.addresses-" . $this->id . "').length > 0) {
					var fieldset = $('.addresses-" . $this->id . "');
					var btn = $('<p class=\"address-add\"><a class=\"icon-add\" href=\"#\">Add another address</a></p>').on('click', function(e){
						e.preventDefault();

						var grp = fieldset
							.find('.address-field-wrap')
							.last()
							.clone();
						grp.find('input').each(function(){
							this.name = this.name.replace(/\[(\d+)\]/,function(str,p1){return '[' + (parseInt(p1,10)+1) + ']';});
							this.id = this.id.replace(/\-(\d+)\-/,function(str,p1){return '-' + (parseInt(p1,10)+1) + '-';});
							this.value = '';
						});
						grp.find('select').each(function(){
							this.name = this.name.replace(/\[(\d+)\]/,function(str,p1){return '[' + (parseInt(p1,10)+1) + ']';});
							this.id = this.id.replace(/\-(\d+)\-/,function(str,p1){return '-' + (parseInt(p1,10)+1) + '-';});
							this.selectedIndex = 0;
						});
						grp.find('label').each(function(){
							$(this).attr('for', $(this).attr('for').replace(/\-(\d+)\-/,function(str,p1){return '-' + (parseInt(p1,10)+1) + '-';}));
						});
						if (!grp.find('.address-remove').length) {
							var rmv = $('<a class=\"address-remove icon-remove\" href=\"#\">Remove</a>');
							grp.append(rmv);
						}
						grp.appendTo(fieldset);

						fieldset.find('.address-remove').off('click').on('click', function(e){
							e.preventDefault();
							$(this).parent().remove();
						});
					});
					fieldset.after(btn);
					fieldset.find('.address-field-wrap').each(function(i, grp){
						if (i === 0) {
							return;
						}
						grp = $(grp);
						if (!grp.find('.address-remove').length) {
							var rmv = $('<a class=\"address-remove icon-remove\" href=\"#\">Remove</a>').on('click', function(e){
								e.preventDefault();
								$(this).parent().remove();
							});
							grp.append(rmv);
						}
					});
				}
			};
		");
		App::get('document')->addScriptDeclaration("jQuery(document).ready(function($){\nmanageProfileAddresses();\n});");
		App::get('document')->addScriptDeclaration("jQuery(document).on('ajaxLoad', function($){\nmanageProfileAddresses();\n});");

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
