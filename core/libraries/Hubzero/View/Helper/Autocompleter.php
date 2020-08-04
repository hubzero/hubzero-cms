<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

/**
 * Instantiate and return a form field for autocompleting some value
 */
class Autocompleter extends AbstractHelper
{
	/**
	 * Output the autocompleter
	 *
	 * @param   string  $what   The component to call
	 * @param   string  $name   Name of the input field
	 * @param   string  $value  The value of the input field
	 * @param   string  $id     ID of the input field
	 * @param   string  $class  CSS class(es) for the input field
	 * @param   string  $size   The size of the input field
	 * @param   string  $wsel   AC autopopulates a select list based on choice?
	 * @param   string  $type   Allow single or multiple entries
	 * @param   string  $dsabl  Readonly input
	 * @return  string
	 * @throws  \InvalidArgumentException  If wrong type passed
	 */
	public function __invoke($what=null, $name=null, $value=null, $id=null, $class=null, $size=null, $wsel=false, $type='multi', $dsabl=false)
	{
		if (!in_array($what, array('tags', 'members', 'groups')))
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); ' . \Lang::txt('Autocompleter for "%s" not supported.', $what));
		}

		$id = ($id ?: str_replace(array('[', ']'), '', $name));

		switch ($type)
		{
			case 'multi':
				$event = 'onGetMultiEntry';
			break;
			case 'single':
				$event = 'onGetSingleEntry';
				if ($wsel)
				{
					$event = 'onGetSingleEntryWithSelect';
				}
			break;
			default:
				throw new \InvalidArgumentException(__METHOD__ . '(); ' . \Lang::txt('Autocompleter type "%s" not supported.', $type));
			break;
		}

		$results = \Event::trigger(
			'hubzero.' . $event,
			array(
				array($what, $name, $id, $class, $value, $size, $wsel, $type, $dsabl)
			)
		);

		if (count($results) > 0)
		{
			$results = implode("\n", $results);
		}
		else
		{
			$results = '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" />';
		}

		return $results;
	}
}
