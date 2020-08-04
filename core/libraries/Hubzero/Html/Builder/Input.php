<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Hubzero\Utility\Date;

/**
 * Utility class for form elements
 */
class Input
{
	/**
	 * Create a form input field.
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $options
	 * @return  string
	 */
	public static function input($type, $name, $value = null, $options = array())
	{
		//if (!isset($options['name'])) $options['name'] = $name;

		// We will get the appropriate value for the given field. We will look for the
		// value in the session for the value in the old input data then we'll look
		// in the model instance if one is set. Otherwise we will just use empty.
		$id = self::getIdAttribute($name, $options);

		// Once we have the type, value, and ID we can merge them into the rest of the
		// attributes array so we can convert them into their HTML attribute format
		// when creating the HTML element. Then, we will return the entire input.
		$merge = compact('type', 'name', 'value', 'id');

		$options = array_merge($options, $merge);

		return '<input' . self::attributes($options) . ' />';
	}

	/**
	 * Displays a hidden token field to reduce the risk of CSRF exploits
	 *
	 * Use in conjunction with Session::checkToken
	 *
	 * @return  string  A hidden input field with a token
	 */
	public static function token()
	{
		return self::input('hidden', \App::get('session')->getFormToken(), 1, array('id' => null)) . "\n";
	}

	/**
	 * Create a text input field.
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $options
	 * @return  string
	 */
	public static function text($name, $value = null, $options = array())
	{
		return self::input('text', $name, $value, $options);
	}

	/**
	 * Create a password input field.
	 *
	 * @param   string  $name
	 * @param   array   $options
	 * @return  string
	 */
	public static function password($name, $options = array())
	{
		return self::input('password', $name, '', $options);
	}

	/**
	 * Create a hidden input field.
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $options
	 * @return  string
	 */
	public static function hidden($name, $value = null, $options = array())
	{
		return self::input('hidden', $name, $value, $options);
	}

	/**
	 * Create an e-mail input field.
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $options
	 * @return  string
	 */
	public static function email($name, $value = null, $options = array())
	{
		return self::input('email', $name, $value, $options);
	}

	/**
	 * Create a url input field.
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $options
	 * @return  string
	 */
	public static function url($name, $value = null, $options = array())
	{
		return self::input('url', $name, $value, $options);
	}

	/**
	 * Create a file input field.
	 *
	 * @param   string  $name
	 * @param   array   $options
	 * @return  string
	 */
	public static function file($name, $options = array())
	{
		return self::input('file', $name, null, $options);
	}

	/**
	 * Displays a calendar control field
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $options
	 * @return  string  HTML markup for a calendar field
	 */
	public static function calendar($name, $value = null, $options = array())
	{
		static $done;

		if ($done === null)
		{
			$done = array();
		}

		$readonly = isset($options['readonly']) && $options['readonly'] == 'readonly';
		$disabled = isset($options['disabled']) && $options['disabled'] == 'disabled';
		$time     = isset($options['time']) ? (bool)$options['time'] : true;

		$format = 'yy-mm-dd';
		if (isset($options['format']))
		{
			$format = $options['format'] ? $options['format'] : $format;
			unset($options['format']);
		}

		if (!isset($options['class']))
		{
			$options['class'] = 'calendar-field';
		}
		else
		{
			$options['class'] = ' calendar-field';
		}

		if (!$readonly && !$disabled)
		{
			// Load the calendar behavior
			Behavior::calendar();
			Behavior::tooltip();

			$id = self::getIdAttribute($name, $options);

			// Only display the triggers once for each control.
			if (!in_array($id, $done))
			{
				if ($format == 'Y-m-d H:i:s' || $format == '%Y-%m-%d %H:%M:%S')
				{
					$time = true;
				}
				$altformats = array('Y-m-d H:i:s', '%Y-%m-%d %H:%M:%S', 'Y-m-d', '%Y-%m-%d');

				$format = (in_array($format, $altformats) ? 'yy-mm-dd' : $format);

				\App::get('document')->addScriptDeclaration("
					jQuery(document).ready(function($){
						" . ($time ? "$('#" . $id . "').datetimepicker({" : "$('#" . $id . "').datepicker({") . "
							duration: '',
							showTime: true,
							constrainInput: false,
							stepMinutes: 1,
							stepHours: 1,
							altTimeField: '',
							time24h: true,
							dateFormat: '" . $format . "'" . ($time ? ", timeFormat: 'HH:mm:00'" : "") . "
						});
					});
				");

				$done[] = $id;
			}

			return '<span class="input-datetime">' . self::text($name, $value, $options) . '</span>';
		}
		else
		{
			$value = (0 !== (int) $value ? with(new Date($value))->format('Y-m-d H:i:s') : '');

			return self::text($name . 'disabled', (0 !== (int) $value ? with(new Date($value))->format('Y-m-d H:i:s') : ''), $options) .
				   self::hidden($name, $value, $options);
		}
	}

	/**
	 * Displays a color picker control field
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $options
	 * @return  string  HTML markup for a calendar field
	 */
	public static function colorpicker($name, $value = null, $options = array())
	{
		static $done;

		if ($done === null)
		{
			$done = array();
		}

		$readonly = isset($options['readonly']) && $options['readonly'] == 'readonly';
		$disabled = isset($options['disabled']) && $options['disabled'] == 'disabled';

		$options['class'] = 'input-colorpicker';

		$value = $value ? '#' . ltrim($value, '#') : '';

		if (!$readonly && !$disabled)
		{
			$id = self::getIdAttribute($name, $options);

			// Only display the triggers once for each control.
			if (!in_array($id, $done))
			{
				// Load the calendar behavior
				Behavior::colorpicker();

				$done[] = $id;
			}

			return '<span class="input-color">' . self::text($name, $value, $options) . '</span>';
		}

		return self::text($name . 'disabled', $value, $options) . self::hidden($name, $value, $options);
	}

	/**
	 * Displays an input field that should be left empty by the
	 * real users of the application but will most likely be
	 * filled out by spam bots.
	 *
	 * Use in conjunction with Request::checkHoneypot()
	 *
	 * @param   string   $name
	 * @param   integer  $delay
	 * @return  string
	 */
	public static function honeypot($name = null)
	{
		return \Hubzero\Spam\Honeypot::generate($name);
	}

	/**
	 * Get the ID attribute for a field name.
	 *
	 * @param   string  $name
	 * @param   array   $attributes
	 * @return  string
	 */
	protected static function getIdAttribute($name, $attributes)
	{
		if (array_key_exists('id', $attributes))
		{
			return $attributes['id'];
		}

		return self::transformKey($name);
	}

	/**
	 * Transform key from array to dot syntax.
	 *
	 * @param   string  $key
	 * @return  string
	 */
	protected static function transformKey($key)
	{
		return str_replace(array('.', '[]', '[', ']'), array('_', '', '-', ''), $key);
	}

	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @param   array  $attributes
	 * @return  string
	 */
	protected static function attributes($attributes)
	{
		$html = array();

		// For numeric keys we will assume that the key and the value are the same
		// as this will convert HTML attributes such as "required" to a correct
		// form like required="required" instead of using incorrect numerics.
		foreach ((array) $attributes as $key => $value)
		{
			$element = null;

			if (is_numeric($key))
			{
				$key = $value;
			}

			if (!is_null($value))
			{
				$element = $key . '="' . htmlentities($value, ENT_COMPAT, 'UTF-8') . '"';
			}

			if (!is_null($element))
			{
				$html[] = $element;
			}
		}

		return count($html) > 0 ? ' ' . implode(' ', $html) : '';
	}
}
