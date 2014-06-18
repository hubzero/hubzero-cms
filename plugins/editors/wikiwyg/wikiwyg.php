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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * HUBzero plugin class for displaying a wiki editor toolbar
 */
class plgEditorWikiwyg extends JPlugin
{
	/**
	 * Flag for if scripts need to be pushed to the document or not
	 *
	 * @var boolean
	 */
	private $_pushscripts = true;

	/**
	 * Initiate the editor. Push scripts to document if needed
	 *
	 * @return     string
	 */
	public function onInit()
	{
		if ($this->_pushscripts)
		{
			\Hubzero\Document\Assets::addPluginStylesheet('editors', $this->_name);
			\Hubzero\Document\Assets::addPluginScript('editors', $this->_name);

			$this->_pushscripts = false;
		}

		return '';
	}

	/**
	 * Display the editor area.
	 *
	 * @param	string	$name		The control name.
	 * @param	string	$html		The contents of the text area.
	 * @param	string	$width		The width of the text area (px or %).
	 * @param	string	$height		The height of the text area (px or %).
	 * @param	int		$col		The number of columns for the textarea.
	 * @param	int		$row		The number of rows for the textarea.
	 * @param	boolean	$buttons	True and the editor buttons will be displayed.
	 * @param	string	$id			An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param	string	$asset
	 * @param	object	$author
	 * @param	array	$params		Associative array of editor parameters.
	 * @return	string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		if (empty($id))
		{
			$id = $name;
		}
		$id = str_replace(array('[', ']'), '_', $id);

		$col = $col ? (int) $col : 10;
		$row = $row ? (int) $row : 35;

		// Only add "px" to width and height if they are not given as a percentage
		if (is_numeric($width))
		{
			$width .= 'px';
		}
		if (is_numeric($height))
		{
			$height .= 'px';
		}

		if (!isset($params['class']))
		{
			$params['class'] = array();
		}
		if (!is_array($params['class']))
		{
			$params['class'] = array($params['class']);
		}
		if ($cls = $this->params->get('class'))
		{
			$params['class'][] = $cls;
		}
		$params['class'][] = 'wiki-toolbar-content';
		$params['class'] = implode(' ', $params['class']);

		$atts = array();
		foreach ($params as $key => $value)
		{
			$atts[] = $key .'="' . $value . '"';
		}

		$content = preg_replace('/^((?:<|&lt;)!-- \{FORMAT:(?:.*)\} --(?:>|&gt;))/i', '', $content);
		return '<textarea id="' . $id . '" name="' . $name . '" cols="' . $col . '" rows="' . $row . '" ' . implode(' ', $atts) . '>' . $content . '</textarea>' . "\n";
	}
}
