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

		\Hubzero_Document::addComponentStylesheet($component, $stylesheet, $type, $media, $attribs);

		return $this;
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

		\Hubzero_Document::addComponentScript($component, $script, $type, $defer, $async);

		return $this;
	}
}
