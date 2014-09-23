<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Component;

use Hubzero\View\View as AbstractView;
use Hubzero\Document\Assets;

/**
 * Base class for a View
 *
 * Class holding methods for displaying presentation data.
 */
class View extends AbstractView
{
	/**
	 * Layout name
	 *
	 * @var    string
	 */
	protected $_layout = 'display';

	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.<br/>
	 *                          name: the name (optional) of the view (defaults to the view class name suffix).<br/>
	 *                          charset: the character set to use for display<br/>
	 *                          escape: the name (optional) of the function to use for escaping strings<br/>
	 *                          base_path: the parent path (optional) of the views directory (defaults to the component folder)<br/>
	 *                          template_plath: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br/>
	 *                          helper_path: the path (optional) of the helper files (defaults to base_path + /helpers/)<br/>
	 *                          layout: the layout (optional) to use to display the view<br/>
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Set a base path for use by the view
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath = $config['base_path'];
		}
		else
		{
			$this->_basePath = JPATH_COMPONENT;
		}
	}

	/**
	 * Determine the asset directory
	 *
	 * @param   string $path    File path
	 * @param   string $default Default directory
	 * @return  string
	 */
	private function _assetDir(&$path, $default='')
	{
		if (substr($path, 0, 2) == './')
		{
			$path = substr($path, 2);

			return '';
		}

		if (substr($path, 0, 1) == '/')
		{
			$path = substr($path, 1);

			return '/';
		}

		return $default;
	}

	/**
	 * Push CSS to the document
	 *
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $component  Component name
	 * @return  void
	 */
	public function css($stylesheet = '', $component = null)
	{
		if (!$component)
		{
			$component = $this->get('option', \JRequest::getCmd('option'));
		}

		if ($component === true || strstr($stylesheet, '{') || strstr($stylesheet, '@'))
		{
			\JFactory::getDocument()->addStyleDeclaration($stylesheet);
			return $this;
		}

		if ($stylesheet && substr($stylesheet, -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		$dir = $this->_assetDir($stylesheet, 'css');
		if ($dir == '/')
		{
			Assets::addStylesheet($dir . $stylesheet);
			return $this;
		}

		if ($component == 'system')
		{
			Assets::addSystemStylesheet($stylesheet, $dir);
			return $this;
		}

		if (substr($component, 0, strlen('com_')) !== 'com_')
		{
			$component = 'com_' . $component;
		}

		Assets::addComponentStylesheet($component, $stylesheet, $dir);

		return $this;
	}

	/**
	 * Push javascript to the document
	 *
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $component  Component name
	 * @return  void
	 */
	public function js($script = '', $component = null)
	{
		if (!$component)
		{
			$component = $this->get('option', \JRequest::getCmd('option'));
		}

		if ($component === true || strstr($script, '(') || strstr($script, ';'))
		{
			\JFactory::getDocument()->addScriptDeclaration($script);
			return $this;
		}

		$dir = $this->_assetDir($script, 'js');
		if ($dir == '/')
		{
			Assets::addScript($dir . $script);
			return $this;
		}

		if ($component == 'system')
		{
			Assets::addSystemScript($script, $dir);
			return $this;
		}

		if (substr($component, 0, strlen('com_')) !== 'com_')
		{
			$component = 'com_' . $component;
		}

		Assets::addComponentScript($component, $script, $dir);

		return $this;
	}

	/**
	 * Get the path to an image
	 *
	 * @param   string  $image      Image name
	 * @param   string  $component  Component name
	 * @return  string
	 */
	public function img($image, $component = null)
	{
		if (!$component)
		{
			$component = $this->get('option', \JRequest::getCmd('option'));
		}

		$dir = $this->_assetDir($image, 'img');
		if ($dir == '/')
		{
			return rtrim(\JURI::base(true), '/') . $dir . $image;
		}

		if ($component == 'system')
		{
			return Assets::getSystemImage($image, $dir);
		}

		if (substr($component, 0, strlen('com_')) !== 'com_')
		{
			$component = 'com_' . $component;
		}

		return Assets::getComponentImage($component, $image, $dir);
	}

	/**
	 * Create a component view and return it
	 *
	 * @param   string $layout View layout
	 * @param   string $name   View name
	 * @return	object
	 */
	public function view($layout, $name=null)
	{
		// If we were passed only a view model, just render it.
		if ($layout instanceof AbstractView)
		{
			return $layout;
		}

		$view = new self(array(
			'base_path' => $this->_basePath,
			'name'      => ($name ? $name : $this->_name),
			'layout'    => $layout
		));
		$view->set('option', $this->option)
		     ->set('controller', $this->controller)
		     ->set('task', $this->task);

		return $view;
	}
}
