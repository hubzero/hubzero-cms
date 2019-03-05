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

namespace Components\Menus\Models;

use Components\Menus\Helpers\Menus as MenusHelper;
use Hubzero\Base\Obj;
use Filesystem;
use Component;
use Lang;
use App;

/**
 * Menu type model
 */
class Menutype extends Obj
{
	/**
	 * A reverse lookup of the base link URL to Title
	 *
	 * @var  array
	 */
	protected $rlu = array();

	/**
	 * Method to get the reverse lookup of the base link URL to Title
	 *
	 * @return  array  Array of reverse lookup of the base link URL to Title
	 */
	public function getReverseLookup()
	{
		if (empty($this->rlu))
		{
			$this->getTypeOptions();
		}
		return $this->rlu;
	}

	/**
	 * Method to get the available menu item type options.
	 *
	 * @return  array  Array of groups with menu item types.
	 */
	public function getTypeOptions()
	{
		// Initialise variables.
		$lang = Lang::getRoot();
		$list = array();

		// Get the list of components.
		$db = App::get('db');
		$query = $db->getQuery()
			->select('name')
			->select('protected')
			->select('element', '`option`')
			->from('#__extensions')
			->whereEquals('type', 'component')
			->whereEquals('enabled', 1)
			->order('name', 'asc');

		$db->setQuery($query->toString());
		$components = $db->loadObjectList();

		foreach ($components as $component)
		{
			if ($options = $this->getTypeOptionsByComponent($component))
			{
				$list[$component->name] = $options;

				// Create the reverse lookup for link-to-name.
				foreach ($options as $option)
				{
					if (isset($option->request))
					{
						$this->rlu[MenusHelper::getLinkKey($option->request)] = $option->get('title');

						if (isset($option->request['option']))
						{
								$lang->load($option->request['option'] . '.sys', PATH_APP, null, false, true)
							||	$lang->load($option->request['option'] . '.sys', Component::path($option->request['option']) . '/admin', null, false, true);
						}
					}
				}
			}
		}

		return $list;
	}

	/**
	 * Method to get type options by component
	 *
	 * @param   object  $component
	 * @return  array
	 */
	protected function getTypeOptionsByComponent($component)
	{
		// Initialise variables.
		$options = array();

		$mainXML = Component::path($component->option) . '/site/metadata.xml';

		if (is_file($mainXML))
		{
			$options = $this->getTypeOptionsFromXML($mainXML, $component);
		}

		if (empty($options))
		{
			$options = $this->getTypeOptionsFromMVC($component);
		}

		return $options;
	}

	/**
	 * Method to get type options from XML
	 *
	 * @param   string  $file
	 * @param   string  $component
	 * @return  array
	 */
	protected function getTypeOptionsFromXML($file, $component)
	{
		// Initialise variables.
		$options = array();

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($file))
		{
			return false;
		}

		// Look for the first menu node off of the root node.
		if (!$menu = $xml->xpath('menu[1]'))
		{
			return false;
		}
		else
		{
			$menu = $menu[0];
		}

		// If we have no options to parse, just add the base component to the list of options.
		if (!empty($menu['options']) && $menu['options'] == 'none')
		{
			// Create the menu option for the component.
			$o = new Obj;
			$o->title       = (string) $menu['name'];
			$o->description = (string) $menu['msg'];
			$o->request     = array('option' => $component);

			$options[] = $o;

			return $options;
		}

		// Look for the first options node off of the menu node.
		if (!$optionsNode = $menu->xpath('options[1]'))
		{
			return false;
		}
		else
		{
			$optionsNode = $optionsNode[0];
		}

		// Make sure the options node has children.
		if (!$children = $optionsNode->children())
		{
			return false;
		}
		else
		{
			// Process each child as an option.
			foreach ($children as $child)
			{
				if ($child->getName() == 'option')
				{
					// Create the menu option for the component.
					$o = new Obj;
					$o->title       = (string) $child['name'];
					$o->description = (string) $child['msg'];
					$o->request     = array('option' => $component, (string) $optionsNode['var'] => (string) $child['value']);

					$options[] = $o;
				}
				elseif ($child->getName() == 'default')
				{
					// Create the menu option for the component.
					$o = new Obj;
					$o->title       = (string) $child['name'];
					$o->description = (string) $child['msg'];
					$o->request     = array('option' => $component);

					$options[] = $o;
				}
			}
		}

		return $options;
	}

	/**
	 * Method to get type options from MVC
	 *
	 * @param   object  $component
	 * @return  array
	 */
	protected function getTypeOptionsFromMVC($component)
	{
		// Initialise variables.
		$options = array();

		// Get the views for this component.
		$path = Component::path($component->option) . '/site/views';

		if (Filesystem::exists($path))
		{
			$views = Filesystem::directories($path);
		}
		else
		{
			return false;
		}

		foreach ($views as $view)
		{
			$view = trim($view, '/');
			// Ignore private views.
			if (strpos($view, '_') !== 0)
			{
				// Determine if a metadata file exists for the view.
				$file = $path . '/' . $view . '/metadata.xml';

				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('view[1]'))
						{
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
							{
								unset($xml);
								continue;
							}

							// Do we have an options node or should we process layouts?
							// Look for the first options node off of the menu node.
							if ($optionsNode = $menu->xpath('options[1]'))
							{
								$optionsNode = $optionsNode[0];

								// Make sure the options node has children.
								if ($children = $optionsNode->children())
								{
									// Process each child as an option.
									foreach ($children as $child)
									{
										if ($child->getName() == 'option')
										{
											// Create the menu option for the component.
											$o = new Obj;
											$o->title       = (string) $child['name'];
											$o->description = (string) $child['msg'];
											$o->request     = array(
												'option' => $component->option,
												'view'   => $view,
												(string) $optionsNode['var'] => (string) $child['value']
											);

											$options[] = $o;
										}
										elseif ($child->getName() == 'default')
										{
											// Create the menu option for the component.
											$o = new Obj;
											$o->title       = (string) $child['name'];
											$o->description = (string) $child['msg'];
											$o->request     = array(
												'option' => $component->option,
												'view'   => $view
											);

											$options[] = $o;
										}
									}
								}
							}
							else
							{
								$options = array_merge($options, (array) $this->getTypeOptionsFromLayouts($component, $view));
							}
						}
						unset($xml);
					}
				}
				else
				{
					$options = array_merge($options, (array) $this->getTypeOptionsFromLayouts($component, $view));
				}
			}
		}

		return $options;
	}

	/**
	 * Method to get type options from layouts
	 *
	 * @param   object  $component
	 * @param   string  $view
	 * @return  array
	 */
	protected function getTypeOptionsFromLayouts($component, $view)
	{
		// Initialise variables.
		$options = array();
		$layouts = array();
		$layoutNames = array();
		$templateLayouts = array();
		$lang = Lang::getRoot();

		// Get the layouts from the view folder.
		$path = Component::path($component->option) . '/site/views/' . $view . '/tmpl';

		if (Filesystem::exists($path))
		{
			$layouts = array_merge($layouts, Filesystem::files($path, '.xml$', false, true));
		}
		else
		{
			return $options;
		}

		// build list of standard layout names
		foreach ($layouts as $layout)
		{
			$layout = trim($layout, '/');
			// Ignore private layouts.
			if (strpos(basename($layout), '_') === false)
			{
				$file = $layout;
				// Get the layout name.
				$layoutNames[] = Filesystem::name(basename($layout));
			}
		}

		// get the template layouts
		// TODO: This should only search one template -- the current template for this item (default of specified)
		$folders = Filesystem::directories(PATH_CORE . '/templates', '', false, true);
		// Array to hold association between template file names and templates
		$templateName = array();
		foreach ($folders as $folder)
		{
			if (Filesystem::exists($folder . '/html/' . $component->option . '/' . $view))
			{
				$template = basename($folder);

					$lang->load('tpl_' . $template . '.sys', PATH_CORE, null, false, true)
				||	$lang->load('tpl_' . $template . '.sys', PATH_CORE . '/templates/' . $template, null, false, true);

				$templateLayouts = Filesystem::files($folder . '/html/' . $component->option . '/' . $view, '.xml$', false, true);

				foreach ($templateLayouts as $layout)
				{
					$file = trim($layout, '/');
					// Get the layout name.
					$templateLayoutName = Filesystem::name(basename($layout));

					// add to the list only if it is not a standard layout
					if (array_search($templateLayoutName, $layoutNames) === false)
					{
						$layouts[] = $layout;
						// Set template name array so we can get the right template for the layout
						$templateName[$layout] = basename($folder);
					}
				}
			}
		}

		// Process the found layouts.
		foreach ($layouts as $layout)
		{
			$layout = rtrim($layout, '/');
			// Ignore private layouts.
			if (strpos(basename($layout), '_') === false)
			{
				$file = $layout;
				// Get the layout name.
				$layout = Filesystem::name(basename($layout));

				// Create the menu option for the layout.
				$o = new Obj;
				$o->title       = ucfirst($layout);
				$o->description = '';
				$o->request     = array(
					'option' => $component->option,
					'view'   => $view
				);

				// Only add the layout request argument if not the default layout.
				if ($layout != 'default')
				{
					// If the template is set, add in format template:layout so we save the template name
					$o->request['layout'] = (isset($templateName[$file])) ? $templateName[$file] . ':' . $layout : $layout;
				}

				// Load layout metadata if it exists.
				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('layout[1]'))
						{
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
							{
								unset($xml);
								unset($o);
								continue;
							}

							// Populate the title and description if they exist.
							if (!empty($menu['title']))
							{
								$o->title = trim((string) $menu['title']);
							}

							if (!empty($menu->message[0]))
							{
								$o->description = trim((string) $menu->message[0]);
							}
						}
					}
				}

				// Add the layout to the options array.
				$options[] = $o;
			}
		}

		return $options;
	}
}
