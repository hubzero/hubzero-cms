<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
