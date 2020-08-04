<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;

/**
 * Renders a helpsites element
 */
class Helpsites extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Helpsites';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$helpsites = self::createSiteList(PATH_CORE . '/help/helpsites.xml', $value);
		array_unshift($helpsites, Builder\Select::option('', \App::get('language')->txt('local')));

		return Builder\Select::genericlist(
			$helpsites,
			$control_name . '[' . $name . ']',
			array(
				'id' => $control_name . $name,
				'list.attr' => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}

	/**
	 * Builds a list of the help sites which can be used in a select option.
	 *
	 * @param   string  $pathToXml  Path to an XML file.
	 * @param   string  $selected   Language tag to select (if exists).
	 * @return  array   An array of arrays (text, value, selected).
	 */
	public static function createSiteList($pathToXml, $selected = null)
	{
		$list = array();
		$xml = false;

		if (!empty($pathToXml))
		{
			// Disable libxml errors and allow to fetch error information as needed
			libxml_use_internal_errors(true);

			// Try to load the XML file
			$xml = simplexml_load_file($pathToXml);
		}

		if (!$xml)
		{
			$option['text']  = 'English (US) hubzero.org';
			$option['value'] = 'http://hubzero.org/documentation';
			$list[] = $option;
		}
		else
		{
			$option = array();

			foreach ($xml->sites->site as $site)
			{
				$option['text']  = (string) $site;
				$option['value'] = (string) $site->attributes()->url;

				$list[] = $option;
			}
		}

		return $list;
	}
}
