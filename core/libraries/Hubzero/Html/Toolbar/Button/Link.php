<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Toolbar\Button;

use Hubzero\Html\Toolbar\Button;

/**
 * Renders a link button
 */
class Link extends Button
{
	/**
	 * Button type
	 *
	 * @var  string
	 */
	protected $_name = 'Link';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string  $type  Unused string.
	 * @param   string  $name  Name to be used as apart of the id
	 * @param   string  $text  Button text
	 * @param   string  $url   The link url
	 * @return  string  HTML string for the button
	 */
	public function fetchButton($type = 'Link', $name = 'back', $text = '', $url = null, $target = null)
	{
		$text   = \Lang::txt($text);
		$class  = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($url);

		$html  = "<a data-title=\"$text\" href=\"$doTask\"";
		if ($target)
		{
			$html .= " target=\"$target\"";
		}
		$html .= ">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "$text\n";
		$html .= "</span>\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @param   string  $type  The button type.
	 * @param   string  $name  The name of the button.
	 * @return  string  Button CSS Id
	 */
	public function fetchId($type = 'Link', $name = '')
	{
		return $this->_parent->getName() . '-' . $name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   object  $url  Button definition
	 * @return  string  JavaScript command string
	 */
	protected function _getCommand($url)
	{
		return $url;
	}
}
