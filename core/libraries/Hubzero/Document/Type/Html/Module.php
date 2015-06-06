<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Document\Type\Html;

use Hubzero\Document\Renderer;
use Hubzero\Config\Registry;
use stdClass;

/**
 * Module renderer
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

		/*$contents = '';

		// Default for compatibility purposes. Set cachemode parameter or use Module::cache from within the
		// module instead
		
		// Get the configuration object
		$conf = \App::get('config');

		$cachemode = $params->get('cachemode', 'oldstatic');

		if ($params->get('cache', 0) == 1 && $config->get('caching') >= 1 && $cachemode != 'id' && $cachemode != 'safeuri')
		{
			// Default to itemid creating method and workarounds on
			$cacheparams = new stdClass;
			$cacheparams->cachemode    = $cachemode;
			$cacheparams->class        = '\\Hubzero\\Module\\Loader';
			$cacheparams->method       = 'render';
			$cacheparams->methodparams = array($module, $attribs);

			$contents = \App::get('module')::cache($module, $params, $cacheparams);
		}
		else
		{
			$contents = \App::get('module')->render($module, $attribs);
		}*/

		return \App::get('module')->render($module, $attribs);
	}
}
