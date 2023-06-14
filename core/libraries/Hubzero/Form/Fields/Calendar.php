<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Input;
use Hubzero\Utility\Date;
use Hubzero\Form\Field;
use DateTimeZone;
use App;

/**
 * Provides a pop up date picker linked to a button.
 * Optionally may be filtered to use user's or server's time zone.
 */
class Calendar extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Calendar';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$format = $this->element['format'] ? (string) $this->element['format'] : 'yy-mm-dd';

		// Build the attributes array.
		$attributes = array();
		if ($this->element['size'])
		{
			$attributes['size'] = (int) $this->element['size'];
		}
		if ($this->element['maxlength'])
		{
			$attributes['maxlength'] = (int) $this->element['maxlength'];
		}
		if ($this->element['class'])
		{
			$attributes['class'] = (string) $this->element['class'];
		}
		if ((string) $this->element['readonly'] == 'true')
		{
			$attributes['readonly'] = 'readonly';
		}
		if ((string) $this->element['disabled'] == 'true')
		{
			$attributes['disabled'] = 'disabled';
		}
		$attributes['time'] = false;
		if ((string) $this->element['time'] == 'true')
		{
			$attributes['time'] = true;
		}
		if ($this->element['onchange'])
		{
			$attributes['onchange'] = (string) $this->element['onchange'];
		}

		// Handle the special case for "now".
		if (strtoupper($this->value) == 'NOW')
		{
			$this->value = strftime($format);
		}

		// If a known filter is given use it.
		switch (strtoupper((string) $this->element['filter']))
		{
			case 'SERVER_UTC':
				// Convert a date to UTC based on the server timezone.
				if (intval($this->value))
				{
					// Get a date object based on the correct timezone.
					$date = new Date($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone(App::get('config')->get('offset')));

					// Transform the date string.
					$this->value = $date->format('Y-m-d H:i:s', true, false);
				}
				break;

			case 'USER_UTC':
				// Convert a date to UTC based on the user timezone.
				if (intval($this->value))
				{
					// Get a date object based on the correct timezone.
					$date = new Date($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone(App::get('user')->getParam('timezone', App::get('config')->get('offset'))));

					// Transform the date string.
					$this->value = $date->format('Y-m-d H:i:s', true, false);
				}
				break;
		}

		$attributes['id'] = $this->id;
		$attributes['format'] = $format;

		return Input::calendar($this->name, $this->value, $attributes);
	}
}
