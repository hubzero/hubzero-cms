<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Component;

/**
 * @see JView
 */
jimport('joomla.application.component.view');

/**
 * Base class for a View
 *
 * Class holding methods for displaying presentation data.
 */
class View extends \JView
{
	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 * Returns $this so set() can be chained
	 * 
	 *    $object->set('foo', $bar)
	 *           ->set('bar', $foo)
	 *           ->doSomething();
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 * @return  object
	 */
	public function set($property, $value = null)
	{
		$this->$property = $value;
		return $this; // So we can do method chaining!
	}

	/**
	 * Get the base path.
	 *
	 * @return  string
	 */
	public function getBasePath()
	{
		return $this->_basePath;
	}

	/**
	 * Set the base path
	 *
	 * @param  string  The path to set
	 * @return object
	 */
	public function setBasePath($path)
	{
		$this->_basePath = $path;
		return $this;
	}

	/**
	 * Set the name
	 *
	 * @param  string  The name to set
	 * @return object
	 */
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * Push CSS to the document
	 * 
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $component  Component name
	 * @param   string  $type       Mime encoding type
	 * @param   string  $media      Media type that this stylesheet applies to
	 * @param   string  $attribs    Attributes to add to the link
	 * @return  void
	 */
	public function css($stylesheet = '', $component = null, $type = 'text/css', $media = null, $attribs = array())
	{
		if (!$component)
		{
			$component = \JRequest::getCmd('option');
		}

		if ($component === true || strstr($stylesheet, '{') || strstr($stylesheet, '@'))
		{
			return \JFactory::getDocument()->addStyleDeclaration($stylesheet);
		}

		if ($stylesheet && substr($stylesheet, -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		if ($component == 'system')
		{
			return \Hubzero_Document::addSystemStylesheet($stylesheet);
		}

		if (substr($component, 0, strlen('com_')) !== 'com_')
		{
			$component = 'com_' . $component;
		}

		return \Hubzero_Document::addComponentStylesheet($component, $stylesheet, $type, $media, $attribs);
	}

	/**
	 * Push javascript to the document
	 *
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $component  Component name
	 * @param   string  $type       Mime encoding type
	 * @param   string  $media      Media type that this stylesheet applies to
	 * @param   string  $attribs    Attributes to add to the link
	 * @return  void
	 */
	public function js($script = '', $component = null, $type = "text/javascript", $defer = false, $async = false)
	{
		if (!$component)
		{
			$component = \JRequest::getCmd('option');
		}

		if ($component === true || strstr($script, '(') || strstr($script, ';'))
		{
			return \JFactory::getDocument()->addScriptDeclaration($script);
		}

		if ($component == 'system')
		{
			return \Hubzero_Document::addSystemScript($script);
		}

		if (substr($component, 0, strlen('com_')) !== 'com_')
		{
			$component = 'com_' . $component;
		}

		return \Hubzero_Document::addComponentScript($component, $script, $type, $defer, $async);
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->loadTemplate();
	}
}
