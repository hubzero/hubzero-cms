<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

/**
 * Migration macro to install a template entry
 **/
class InstallTemplateEntry extends AddTemplateEntry
{
	/**
	 * Install a template, adding it if needed
	 *
	 * @param   string  $element    Template element
	 * @param   string  $name       Template name
	 * @param   int     $client     Admin or site client
	 * @param   array   $styles     Template styles
	 * @param   int     $protected  Whether or not the template is a core one or not
	 * @return  bool
	 **/
	public function __invoke($element, $name=null, $client=1, $styles=null, $protected=0)
	{
		$enabled = 1;
		$home = 1;

		return parent::__invoke($element, $name, $client, $enabled, $home, $styles, $protected);
	}
}
