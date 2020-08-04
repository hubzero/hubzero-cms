<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Form\Form;
use Hubzero\Base\ClientManager;
use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Filesystem\Util;
use Exception;
use App;

/**
 * Form Field to display a list of the layouts for a component view from
 * the extension or template overrides.
 */
class Componentlayout extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Componentlayout';

	/**
	 * Method to get the field input for a component layout field.
	 *
	 * @return  string  The field input.
	 */
	protected function getInput()
	{
		// Initialize variables.

		// Get the client id.
		$clientId = $this->element['client_id'];

		if (is_null($clientId) && $this->form instanceof Form)
		{
			$clientId = $this->form->getValue('client_id');
		}
		$clientId = (int) $clientId;

		$client = ClientManager::client($clientId);

		// Get the extension.
		$extn = (string) $this->element['extension'];

		if (empty($extn) && ($this->form instanceof Form))
		{
			$extn = $this->form->getValue('extension');
		}

		$extn = preg_replace('#\W#', '', $extn);

		// Get the template.
		$template = (string) $this->element['template'];
		$template = preg_replace('#\W#', '', $template);

		// Get the style.
		if ($this->form instanceof Form)
		{
			$template_style_id = $this->form->getValue('template_style_id');
		}

		$template_style_id = preg_replace('#\W#', '', $template_style_id);

		// Get the view.
		$view = (string) $this->element['view'];
		$view = preg_replace('#\W#', '', $view);

		// If a template, extension and view are present build the options.
		if ($extn && $view && $client)
		{
			// Load language file
			$lang = App::get('language');
			$lang->load($extn . '.sys', App::get('component')->path($extn) . '/admin', null, false, true);

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
				throw new Exception(500, $db->getErrorMsg());
			}

			$paths = array(PATH_APP, PATH_CORE);

			$filesystem = App::get('filesystem');

			foreach ($paths as $path)
			{
				if (is_dir($path . '/components/' . $extn))
				{
					break;
				}
			}

			// Build the search paths for component layouts.
			$component_path = Util::normalizePath($path . '/components/' . $extn . '/views/' . $view . '/tmpl');

			// Prepare array of component layouts
			$component_layouts = array();

			// Prepare the grouped list
			$groups = array();

			// Add a Use Global option if useglobal="true" in XML file
			if ($this->element['useglobal'] == 'true')
			{
				$groups[$lang->txt('JOPTION_FROM_STANDARD')]['items'][] = Dropdown::option('', $lang->txt('JGLOBAL_USE_GLOBAL'));
			}

			// Add the layout options from the component path.
			if (is_dir($component_path) && ($component_layouts = $filesystem->files($component_path, '^[^_]*\.xml$', false, true)))
			{
				// Create the group for the component
				$groups['_'] = array();
				$groups['_']['id'] = $this->id . '__';
				$groups['_']['text'] = $lang->txt('JOPTION_FROM_COMPONENT');
				$groups['_']['items'] = array();

				foreach ($component_layouts as $i => $file)
				{
					// Attempt to load the XML file.
					if (!$xml = simplexml_load_file($file))
					{
						unset($component_layouts[$i]);

						continue;
					}

					// Get the help data from the XML file if present.
					if (!$menu = $xml->xpath('layout[1]'))
					{
						unset($component_layouts[$i]);

						continue;
					}

					$menu = $menu[0];

					// Add an option to the component group
					$value = $filesystem->name($file);
					$component_layouts[$i] = $value;
					$text = isset($menu['option']) ? $lang->txt($menu['option']) : (isset($menu['title']) ? $lang->txt($menu['title']) : $value);
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

					$template_path = Util::normalizePath($template->path . '/html/' . $extn . '/' . $view);

					// Add the layout options from the template path.
					if (is_dir($template_path) && ($files = $filesystem->files($template_path, '^[^_]*\.php$', false, true)))
					{
						// Files with corresponding XML files are alternate menu items, not alternate layout files
						// so we need to exclude these files from the list.
						$xml_files = $filesystem->files($template_path, '^[^_]*\.xml$', false, true);
						for ($j = 0, $count = count($xml_files); $j < $count; $j++)
						{
							$xml_files[$j] = $filesystem->name($xml_files[$j]);
						}
						foreach ($files as $i => $file)
						{
							// Remove layout files that exist in the component folder or that have XML files
							if (in_array($filesystem->name($file), $component_layouts) || in_array($filesystem->name($file), $xml_files))
							{
								unset($files[$i]);
							}
						}
						if (count($files))
						{
							// Create the group for the template
							$groups[$template->name] = array();
							$groups[$template->name]['id'] = $this->id . '_' . $template->element;
							$groups[$template->name]['text'] = $lang->txt('JOPTION_FROM_TEMPLATE', $template->name);
							$groups[$template->name]['items'] = array();

							foreach ($files as $file)
							{
								// Add an option to the template group
								$value = $filesystem->name($file);
								$text = $lang->hasKey($key = strtoupper('TPL_' . $template->name . '_' . $extn . '_' . $view . '_LAYOUT_' . $value))
									? $lang->txt($key)
									: $value;
								$groups[$template->name]['items'][] = Dropdown::option($template->element . ':' . $value, $text);
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
					'id' => $this->id,
					'group.id' => 'id',
					'list.attr' => $attr,
					'list.select' => $selected
				)
			);

			return implode($html);
		}

		return '';
	}
}
