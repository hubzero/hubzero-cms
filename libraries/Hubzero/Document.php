<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.document.document');

/**
 * Class for adding stylesheets from components, modules, and plugins to the document
 *
 * @package       hubzero-cms
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright     Copyright 2005-2011 Purdue University. All rights reserved.
 * @license       http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
class Hubzero_Document
{

	/**
	 * Adds a linked stylesheet from a component to the page
	 *
	 * @param	string  $component	Component name
	 * @param	string  $stylesheet	Stylesheet name (optional, uses component name if left blank)
	 * @param	string  $type   	Mime encoding type
	 * @param	string  $media  	Media type that this stylesheet applies to
	 * @param	string  $attribs  	Attributes to add to the link
	 * @param      boolean $augment Parameter description (if any) ...
	 * @return  void
	 */
	public static function addComponentStylesheet($component, $stylesheet = '', $type = 'text/css', $media = null, $attribs = array())
	{
		$mainframe =& JFactory::getApplication();

		$jdocument = &JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($stylesheet)) {
			$stylesheet = substr($component,4) . '.css';
		}

		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . $stylesheet;

		$componentcss = DS . 'components' . DS . $component . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) {
			$jdocument->addStyleSheet($templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss), $type, $media, $attribs);
        } else if (file_exists(JPATH_SITE . $componentcss)) {
		    $jdocument->addStyleSheet($componentcss . '?v=' . filemtime(JPATH_SITE . $componentcss), $type, $media, $attribs);
		}
	}

	/**
	 * Gets the path to a component image
	 * checks template overrides first, then component
	 *
	 * @param	string  $component	Component name
	 * @param	string  $image		Image to look for
	 * @return  string	Path to an image file
	 */
	public static function getComponentImage($component, $image)
	{
		$mainframe =& JFactory::getApplication();

		$template  = $mainframe->getTemplate();

		$templateimage = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . 'images' . DS . $image;

		$componentimage = DS . 'components' . DS . $component . DS . 'images' . DS . $image;

		if (file_exists(JPATH_SITE . $templateimage)) {
			return $templateimage;
		} else {
			return $componentimage;
		}
	}

	/**
	 * Gets the path to a component stylesheet
	 * checks template overrides first, then component
	 *
	 * @param	string  $component	Component name
	 * @param	string  $stylesheet	Stylesheet to look for
	 * @return  string	Path to a stylesheet
	 */
	public static function getComponentStylesheet($component, $stylesheet)
	{
		$mainframe =& JFactory::getApplication();

		$template  = $mainframe->getTemplate();

		$templateimage = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . 'css' . DS . $stylesheet;

		$componentimage = DS . 'components' . DS . $component . DS . 'css' . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templateimage)) {
			return $templateimage;
		} else {
			return $componentimage;
		}
	}

	/**
	 * Gets the path to a module image
	 * checks template overrides first, then module
	 *
	 * @param	string  $module	Module name
	 * @param	string  $image	Image to look for
	 * @return  string	Path to an image file
	 */
	public static function getModuleImage($module, $image)
	{
		$mainframe =& JFactory::getApplication();

		$template  = $mainframe->getTemplate();

		$templateimage = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . 'images' . DS . $image;

		$moduleimage = DS . 'modules' . DS . $module . DS . 'images' . DS . $image;

		if (file_exists(JPATH_SITE . $templateimage)) {
			return $templateimage;
		} else {
			return $moduleimage;
		}
	}

	/**
	 * Adds a linked stylesheet from a module to the page
	 *
	 * @param	string  $module		Module name
	 * @param	string  $stylesheet	Stylesheet name (optional, uses module name if left blank)
	 * @param	string  $type   	Mime encoding type
	 * @param	string  $media  	Media type that this stylesheet applies to
	 * @param	string  $attribs  	Attributes to add to the link
	 * @return  void
	 */
	public static function addModuleStyleSheet($module, $stylesheet = '', $type = 'text/css', $media = null, $attribs = array())
	{
		$mainframe =& JFactory::getApplication();

		$jdocument = &JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($stylesheet)) {
			$stylesheet = $module . '.css';
		}

		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . $stylesheet;

		$modulecss = DS . 'modules' . DS . $module . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) {
			$jdocument->addStyleSheet($templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss), $type, $media, $attribs);
        } else {
			$jdocument->addStyleSheet($modulecss . '?v=' . filemtime(JPATH_SITE . $modulecss), $type, $media, $attribs);
		}
	}
	
	/**
	 * Adds a linked script to the page
	 *
	 * @param   string  $module  	URL to the linked script
	 * @param	string  $script  	Script name (optional, uses module name if left blank)
	 * @param   string  $type		Type of script. Defaults to 'text/javascript'
	 * @param   bool    $defer		Adds the defer attribute.
	 * @param   bool    $async		Adds the async attribute.
	 * @return  void
	 */
	public static function addModuleScript($module, $script = '', $type = "text/javascript", $defer = false, $async = false)
	{
		$mainframe = JFactory::getApplication();

		$jdocument = JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($script)) {
			$script = $module;
		}
		
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$script .= '.jquery';
		}
		
		$url = DS . 'modules' . DS . $module . DS . $script . '.js';

		if (file_exists(JPATH_SITE . $url)) {
			$jdocument->addScript($url . '?v=' . filemtime(JPATH_SITE . $url), $type, $defer, $async);
		}
	}

	/**
	 * Adds a linked stylesheet from a plugin to the page
	 *
	 * @param	string  $folder		Plugin folder name
	 * @param	string  $plugin		Plugin name
	 * @param	string  $stylesheet	Stylesheet name (optional, uses module name if left blank)
	 * @param	string  $type   	Mime encoding type
	 * @param	string  $media  	Media type that this stylesheet applies to
	 * @param	string  $attribs  	Attributes to add to the link
	 * @return  void
	 */
	public static function addPluginStyleSheet($folder, $plugin, $stylesheet = '', $type = 'text/css', $media = null, $attribs = array())
	{
		$mainframe =& JFactory::getApplication();

		$jdocument =& JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($stylesheet)) {
			$stylesheet = $plugin . '.css';
		}
		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . 'plg_' . $folder . '_' . $plugin . DS . $stylesheet;

		$plugincss = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) {
			$jdocument->addStyleSheet($templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss), $type, $media, $attribs);
        } else {
			$jdocument->addStyleSheet($plugincss . '?v=' . filemtime(JPATH_SITE . $plugincss), $type, $media, $attribs);
		}
	}
	
	/**
	 * Adds a linked script to the page
	 *
	 * @param	string  $folder		Plugin folder name
	 * @param	string  $plugin		Plugin name
	 * @param	string  $script  	Script name (optional, uses module name if left blank)
	 * @param   string  $type		Type of script. Defaults to 'text/javascript'
	 * @param   bool    $defer		Adds the defer attribute.
	 * @param   bool    $async		Adds the async attribute.
	 * @return  void
	 */
	public static function addPluginScript($folder, $plugin, $script = '', $type = "text/javascript", $defer = false, $async = false)
	{
		$mainframe = JFactory::getApplication();

		$jdocument = JFactory::getDocument();

		$template  = $mainframe->getTemplate();
		
		if (empty($script)) {
			$script = $plugin;
		}
		
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$script .= '.jquery';
		}
		
		$url = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $script . '.js';
		
		if (file_exists(JPATH_SITE . $url)) {
			$jdocument->addScript($url . '?v=' . filemtime(JPATH_SITE . $url), $type, $defer, $async);
		}
	}
}
