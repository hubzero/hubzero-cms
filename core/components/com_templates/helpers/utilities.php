<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Templates\Helpers;

use Hubzero\Base\Object;
use Hubzero\Access\Access;
use Request;
use Submenu;
use Route;
use Html;
use Lang;
use User;

/**
 * Templates component helper.
 */
class Utilities
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $controller  The name of the active view.
	 * @return  void
	 */
	public static function addSubmenu($controller)
	{
		Submenu::addEntry(
			Lang::txt('COM_TEMPLATES_SUBMENU_STYLES'),
			Route::url('index.php?option=com_templates&controller=styles'),
			$controller == 'styles'
		);
		Submenu::addEntry(
			Lang::txt('COM_TEMPLATES_SUBMENU_TEMPLATES'),
			Route::url('index.php?option=com_templates&controller=templates'),
			$controller == 'templates'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result = new Object;

		$actions = Access::getActionsFromFile(\Component::path('com_templates') . '/config/access.xml');

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, 'com_templates'));
		}

		return $result;
	}

	/**
	 * Get a list of filter options for the application clients.
	 *
	 * @return  array  An array of Option elements.
	 */
	public static function getClientOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '0', Lang::txt('JSITE'));
		$options[] = Html::select('option', '1', Lang::txt('JADMINISTRATOR'));

		return $options;
	}

	/**
	 * Get a list of filter options for the templates with styles.
	 *
	 * @param   mixed  $clientId
	 * @return  array
	 */
	public static function getTemplateOptions($clientId = '*')
	{
		// Build the filter options.
		$db = App::get('db');
		$query = $db->getQuery();

		if ($clientId != '*')
		{
			$query->whereEquals('client_id', (int) $clientId);
		}

		$query
			->select('element', 'value')
			->select('name', 'text')
			->select('extension_id', 'e_id')
			->from('#__extensions')
			->whereEquals('type', 'template')
			->whereEquals('enabled', 1)
			->order('client_id', 'asc')
			->order('name', 'asc');

		$db->setQuery($query->toString());
		return $db->loadObjectList();
	}

	/**
	 * Get values from template manifest
	 *
	 * @param   string  $templateBaseDir
	 * @param   string  $templateDir
	 * @return  object
	 */
	public static function parseXMLTemplateFile($templateBaseDir, $templateDir)
	{
		$data = new Object;

		// Check of the xml file exists
		$filePath = Filesystem::cleanPath($templateBaseDir . '/templates/' . $templateDir . '/templateDetails.xml');

		if (is_file($filePath))
		{
			//$xml = JInstaller::parseXMLInstallFile($filePath);
			// Read the file to see if it's a valid component XML file

			// Disable libxml errors and allow to fetch error information as needed
			libxml_use_internal_errors(true);

			$xml = simplexml_load_file($filePath);

			if (!$xml)
			{
				return $data;
			}

			// Check for a valid XML root tag.
			//
			// Should be 'install', but for backward compatibility we will accept 'extension'.
			// Languages use 'metafile' instead
			if ($xml->getName() != 'install'
			 && $xml->getName() != 'extension'
			 && $xml->getName() != 'metafile')
			{
				unset($xml);
				return $data;
			}

			$meta = array();
			$meta['legacy']       = ($xml->getName() == 'mosinstall' || $xml->getName() == 'install');
			$meta['name']         = (string) $xml->name;

			// Check if we're a language. If so use metafile.
			$meta['type']         = $xml->getName() == 'metafile' ? 'language' : (string) $xml->attributes()->type;
			$meta['creationDate'] = ((string) $xml->creationDate) ? (string) $xml->creationDate : Lang::txt('Unknown');
			$meta['author']       = ((string) $xml->author) ? (string) $xml->author : Lang::txt('Unknown');
			$meta['copyright']    = (string) $xml->copyright;
			$meta['authorEmail']  = (string) $xml->authorEmail;
			$meta['authorUrl']    = (string) $xml->authorUrl;
			$meta['version']      = (string) $xml->version;
			$meta['description']  = (string) $xml->description;
			$meta['group']        = (string) $xml->group;

			if ($meta['type'] != 'template')
			{
				return $data;
			}

			foreach ($meta as $key => $value)
			{
				$data->set($key, $value);
			}
		}

		return $data;
	}

	/**
	 * Display the thumb for the template.
	 *
	 * @param   string   $template   The name of the active view.
	 * @param   integer  $protected
	 * @return  string
	 */
	public static function thumb($template, $protected = 0)
	{
		$basePath = ($protected == 0 ? PATH_APP : PATH_CORE) . '/templates/' . $template;
		$baseUrl  = Request::root(true) . ($protected == 0 ? '/app' : '/core');
		$thumb    = $basePath . '/template_thumbnail.png';
		$preview  = $basePath . '/template_preview.png';
		$html     = '';

		if (file_exists($thumb))
		{
			$html = Html::asset('image', ltrim(substr($thumb, strlen(PATH_ROOT)), DS) , Lang::txt('COM_TEMPLATES_PREVIEW'));

			if (file_exists($preview))
			{
				$preview = $baseUrl . '/templates/' . $template . '/template_preview.png';
				$html    = '<a href="' . $preview . '" class="modal" title="' . Lang::txt('COM_TEMPLATES_CLICK_TO_ENLARGE') . '">' . $html . '</a>';
			}
		}

		return $html;
	}
}
