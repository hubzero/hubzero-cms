<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Form\Form;
use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Base\ClientManager;
use Hubzero\Filesystem\Util;
use App;

/**
 * Form Field to display a list of the layouts for module display from the module or template overrides.
 */
class Modulelayout extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Modulelayout';

	/**
	 * Method to get the field input for module layouts.
	 *
	 * @return  string  The field input.
	 */
	protected function getInput()
	{
		// Get the client id.
		$clientId = $this->element['client_id'];

		if (is_null($clientId) && $this->form instanceof Form)
		{
			$clientId = $this->form->getValue('client_id');
		}
		$clientId = (int) $clientId;

		$client = ClientManager::client($clientId);

		// Get the module.
		$module = (string) $this->element['module'];

		if (empty($module) && ($this->form instanceof Form))
		{
			$module = $this->form->getValue('module');
		}

		$module = preg_replace('#\W#', '', $module);

		// Get the template.
		$template = (string) $this->element['template'];
		$template = preg_replace('#\W#', '', $template);

		// Get the style.
		if ($this->form instanceof Form)
		{
			$template_style_id = $this->form->getValue('template_style_id');
		}

		$template_style_id = preg_replace('#\W#', '', $template_style_id);

		// If an extension and view are present build the options.
		if ($module && $client)
		{

			// Load language file
			$lang = App::get('language');
				$lang->load($module . '.sys', PATH_APP . '/modules/' . $module, null, false, true)
			||	$lang->load($module . '.sys', PATH_CORE . '/modules/' . $module, null, false, true);

			// Get the database object and a new query object.
			$db = App::get('db');

			// Build the query.
			$query = $db->getQuery()
				->select('e.element')
				->select('e.name')
				->from('#__extensions', 'e')
				->whereEquals('e.client_id', (int) $clientId)
				->whereEquals('e.type', 'template')
				->whereEquals('e.enabled', '1');

			if ($template)
			{
				$query->whereEquals('e.element', $template);
			}

			if ($template_style_id)
			{
				$query
					->join('#__template_styles as s', 's.template', 'e.element', 'left')
					->whereEquals('s.id', (int) $template_style_id);
			}

			// Set the query and load the templates.
			$db->setQuery($query->toString());
			$templates = $db->loadObjectList('element');

			// Check for a database error.
			if ($db->getErrorNum())
			{
				App::abort(500, $db->getErrorMsg());
			}

			$paths = array(PATH_APP, PATH_CORE);

			foreach ($paths as $path)
			{
				if (is_dir($path . '/modules/' . $module))
				{
					break;
				}
			}

			// Build the search paths for module layouts.
			$module_path = Util::normalizePath($path . '/modules/' . $module . '/tmpl');

			// Prepare array of component layouts
			$module_layouts = array();

			// Prepare the grouped list
			$groups = array();

			// Add the layout options from the module path.
			if (is_dir($module_path) && ($module_layouts = App::get('filesystem')->files($module_path, '^[^_]*\.php$')))
			{
				// Create the group for the module
				$groups['_'] = array();
				$groups['_']['id'] = $this->id . '__';
				$groups['_']['text'] = $lang->txt('JOPTION_FROM_MODULE');
				$groups['_']['items'] = array();

				foreach ($module_layouts as $file)
				{
					// Add an option to the module group
					$value = App::get('filesystem')->name(ltrim($file, DIRECTORY_SEPARATOR));
					$text = $lang->hasKey($key = strtoupper($module . '_LAYOUT_' . $value)) ? $lang->txt($key) : $value;
					$groups['_']['items'][] = Dropdown::option('_:' . $value, $text);
				}
			}

			// Loop on all templates
			if ($templates)
			{
				foreach ($templates as $template)
				{
					$template->path = '';

					foreach ($paths as $p)
					{
						if (is_dir($p . '/templates/' . $template->element))
						{
							$template->path = $p . '/templates/' . $template->element;
							break;
						}
					}

					if (!$template->path)
					{
						continue;
					}

					// Load language file
					$lang->load('tpl_' . $template->element . '.sys', $template->path, null, false, true);

					$template_path = Util::normalizePath($template->path . '/html/' . $module);

					// Add the layout options from the template path.
					if (is_dir($template_path) && ($files = App::get('filesystem')->files($template_path, '^[^_]*\.php$')))
					{
						foreach ($files as $i => $file)
						{
							// Remove layout that already exist in component ones
							if (in_array($file, $module_layouts))
							{
								unset($files[$i]);
							}
						}

						if (count($files))
						{
							// Create the group for the template
							$groups[$template->element] = array();
							$groups[$template->element]['id'] = $this->id . '_' . $template->element;
							$groups[$template->element]['text'] = $lang->txt('JOPTION_FROM_TEMPLATE', $template->name);
							$groups[$template->element]['items'] = array();

							foreach ($files as $file)
							{
								// Add an option to the template group
								$value = App::get('filesystem')->name(ltrim($file, DIRECTORY_SEPARATOR));
								$text = $lang->hasKey($key = strtoupper('TPL_' . $template->element . '_' . $module . '_LAYOUT_' . $value))
									? $lang->txt($key)
									: $value;
								$groups[$template->element]['items'][] = Dropdown::option($template->element . ':' . $value, $text);
							}
						}
					}
				}
			}
			// Compute attributes for the grouped list
			$attr = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

			// Prepare HTML code
			$html = array();

			// Compute the current selected values
			$selected = array($this->value);

			// Add a grouped list
			$html[] = Dropdown::groupedlist(
				$groups,
				$this->name,
				array(
					'id'          => $this->id,
					'group.id'    => 'id',
					'list.attr'   => $attr,
					'list.select' => $selected
				)
			);

			return implode($html);
		}

		return '';
	}
}
