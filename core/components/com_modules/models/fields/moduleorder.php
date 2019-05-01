<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Lang;
use App;

/**
 * Form Field class for module ordering
 */
class ModuleOrder extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'ModuleOrder';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		$html[] = '<script type="text/javascript">';

		$ordering = $this->form->getValue('ordering');
		$position = $this->form->getValue('position');
		$clientId = $this->form->getValue('client_id');

		$html[] = 'var originalOrder = "'.$ordering.'";';
		$html[] = 'var originalPos = "'.$position.'";';
		$html[] = 'var orders = new Array();';

		$db = App::get('db');
		$query = $db->getQuery()
			->select('position')
			->select('ordering')
			->select('title')
			->from('#__modules')
			->whereEquals('client_id', (int) $clientId)
			->order('ordering', 'asc');

		$db->setQuery($query->toString());
		$orders = $db->loadObjectList();
		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		$orders2 = array();
		for ($i = 0, $n = count($orders); $i < $n; $i++)
		{
			if (!isset($orders2[$orders[$i]->position]))
			{
				$orders2[$orders[$i]->position] = 0;
			}
			$orders2[$orders[$i]->position]++;
			$ord = $orders2[$orders[$i]->position];
			$title = Lang::txt('COM_MODULES_OPTION_ORDER_POSITION', $ord, addslashes($orders[$i]->title));

			$html[] = 'orders['.$i.'] =  new Array("'.$orders[$i]->position.'","'.$ord.'","'.$title.'");';
		}

		$html[] = 'writeDynaList(\'name="'.$this->name.'" id="'.$this->id.'"'.$attr.'\', orders, originalPos, originalPos, originalOrder);';
		$html[] = '</script>';

		return implode("\n", $html);
	}
}
