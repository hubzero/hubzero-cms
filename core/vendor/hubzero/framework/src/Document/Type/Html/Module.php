<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document\Type\Html;

use Hubzero\Document\Renderer;
use Hubzero\Config\Registry;
use stdClass;

/**
 * Module renderer
 *
 * Inspired by Joomla's JDocumentRendererModule class
 */
class Module extends Renderer
{
	/**
	 * Renders a module script and returns the results as a string
	 *
	 * @param   string  $module   The name of the module to render
	 * @param   array   $attribs  Associative array of values
	 * @param   string  $content  If present, module information from the buffer will be used
	 * @return  string  The output of the script
	 */
	public function render($module, $attribs = array(), $content = null)
	{
		if (!is_object($module))
		{
			$title = isset($attribs['title']) ? $attribs['title'] : null;

			$module = \App::get('module')->byName($module, $title);

			if (!is_object($module))
			{
				if (is_null($content))
				{
					return '';
				}
				else
				{
					// If module isn't found in the database but data has been pushed in the buffer
					// we want to render it
					$tmp = $module;

					$module = new stdClass;
					$module->params = null;
					$module->module = $tmp;
					$module->id     = 0;
					$module->user   = 0;
				}
			}
		}

		// Set the module content
		if (!is_null($content))
		{
			$module->content = $content;
		}

		// Get module parameters
		$params = new Registry($module->params);

		// Use parameters from template
		if (isset($attribs['params']))
		{
			$template_params = new Registry(html_entity_decode($attribs['params'], ENT_COMPAT, 'UTF-8'));

			$params->merge($template_params);

			$module = clone $module;
			$module->params = (string) $params;
		}

		return \App::get('module')->render($module, $attribs);
	}
}
