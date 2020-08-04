<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Toolbar\Button;

use Hubzero\Html\Toolbar\Button;
use Hubzero\Html\Builder\Behavior;

/**
 * Renders a popup window button
 */
class Popup extends Button
{
	/**
	 * Button type
	 *
	 * @var  string
	 */
	protected $_name = 'Popup';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string   $type     Unused string, formerly button type.
	 * @param   string   $name     Button name
	 * @param   string   $text     The link text
	 * @param   string   $url      URL for popup
	 * @param   integer  $width    Width of popup
	 * @param   integer  $height   Height of popup
	 * @param   integer  $top      Top attribute.
	 * @param   integer  $left     Left attribute
	 * @param   string   $onClose  JavaScript for the onClose event.
	 * @return  string   HTML string for the button
	 */
	public function fetchButton($type = 'Popup', $name = '', $text = '', $url = '', $width = 640, $height = 480, $top = 0, $left = 0, $onClose = '')
	{
		Behavior::modal();

		$text  = \Lang::txt($text);
		$class = $this->fetchIconClass($name);
		$url   = $this->_getCommand($name, $url, $width, $height, $top, $left);

		$html  = "<a data-title=\"$text\" class=\"modal\" href=\"$url\" data-width=\"$width\" data-height=\"$height\" rel=\"{size: {width: $width, height: $height}, onClose: function() {" . $onClose . "}}\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "$text\n";
		$html .= "</span>\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button id
	 *
	 * @param   string  $type  Button type
	 * @param   string  $name  Button name
	 * @return  string  Button CSS Id
	 */
	public function fetchId($type, $name)
	{
		return $this->_parent->getName() . '-popup-' . $name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $name    Button name
	 * @param   string   $url     URL for popup
	 * @param   integer  $width   Unused formerly width.
	 * @param   integer  $height  Unused formerly height.
	 * @param   integer  $top     Unused formerly top attribute.
	 * @param   integer  $left    Unused formerly left attribure.
	 * @return  string   Command string
	 */
	protected function _getCommand($name, $url, $width, $height, $top, $left)
	{
		if (substr($url, 0, 4) !== 'http')
		{
			$root = rtrim(\Request::root(true), '/');
			if (substr($url, 0, strlen($root)) != $root)
			{
				$url = $root . '/' . ltrim($url, '/');
			}
		}

		return $url;
	}
}
