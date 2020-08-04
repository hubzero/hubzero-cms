<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Rules;

use Hubzero\Access\Access;
use Hubzero\Form\Rule;

/**
 * Form Rule class for rules.
 */
class Rules extends Rule
{
	/**
	 * Method to test for a valid color in hexadecimal.
	 *
	 * @param   object   &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed    $value     The form field value to validate.
	 * @param   string   $group     The field name group control value. This acts as as an array container for the field.
	 *                              For example if the field has name="foo" and the group value is set to "bar" then the
	 *                              full field name would end up being "bar[foo]".
	 * @param   object   &$input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   object   &$form     The form object for which the field is being tested.
	 * @return  boolean  True if the value is valid, false otherwise.
	 */
	public function test(&$element, $value, $group = null, &$input = null, &$form = null)
	{
		// Get the possible field actions and the ones posted to validate them.
		$fieldActions = self::getFieldActions($element);
		$valueActions = self::getValueActions($value);

		// Make sure that all posted actions are in the list of possible actions for the field.
		foreach ($valueActions as $action)
		{
			if (!in_array($action, $fieldActions))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get the list of permission action names from the form field value.
	 *
	 * @param   mixed  $value  The form field value to validate.
	 * @return  array  A list of permission action names from the form field value.
	 */
	protected function getValueActions($value)
	{
		// Initialise variables.
		$actions = array();

		// Iterate over the asset actions and add to the actions.
		foreach ((array) $value as $name => $rules)
		{
			$actions[] = $name;
		}

		return $actions;
	}

	/**
	 * Method to get the list of possible permission action names for the form field.
	 *
	 * @param   object  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @return  array   A list of permission action names from the form field element definition.
	 */
	protected function getFieldActions($element)
	{
		// Initialise variables.
		$actions = array();

		// Initialise some field attributes.
		$section   = $element['section']   ? (string) $element['section']   : '';
		$component = $element['component'] ? (string) $element['component'] : '';

		// Get the asset actions for the element.
		$component = $component ? \App::get('component')->path($component) . '/config/access.xml' : '';
		$section   = $section ? "/access/section[@name='" . $section . "']/" : '';

		$elActions = Access::getActionsFromFile($component, $section);

		if (is_array($elActions))
		{
			// Iterate over the asset actions and add to the actions.
			foreach ($elActions as $item)
			{
				$actions[] = $item->name;
			}
		}

		// Iterate over the children and add to the actions.
		foreach ($element->children() as $el)
		{
			if ($el->getName() == 'action')
			{
				$actions[] = (string) $el['name'];
			}
		}

		return $actions;
	}
}
