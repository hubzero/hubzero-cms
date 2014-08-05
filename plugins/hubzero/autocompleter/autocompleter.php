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

/**
 * HUBzero plugin class for autocompletion
 */
class plgHubzeroAutocompleter extends \Hubzero\Plugin\Plugin
{
	/**
	 * Flag for if scripts need to be pushed to the document or not
	 *
	 * @var boolean
	 */
	private $_pushscripts = true;

	/**
	 * Display the autocompleter. Defaults to multi-entry for tags
	 *
	 * @param      array $atts Attributes for setting up the autocomplete
	 * @return     string HTML
	 */
	public function onGetAutocompleter($atts)
	{
		// Ensure we have an array
		if (!is_array($atts))
		{
			$atts = array();
		}

		//var to hold scripts
		$scripts = '';

		// Set some parameters
		$opt   = (isset($atts[0])) ? $atts[0] : 'tags';  // The component to call
		$name  = (isset($atts[1])) ? $atts[1] : 'tags';  // Name of the input field
		$id    = (isset($atts[2])) ? $atts[2] : 'act';   // ID of the input field
		$class = (isset($atts[3])) ? 'autocomplete ' . $atts[3] : 'autocomplete';  // CSS class(es) for the input field
		$value = (isset($atts[4])) ? $atts[4] : '';      // The value of the input field
		$size  = (isset($atts[5])) ? $atts[5] : '';      // The size of the input field
		$wsel  = (isset($atts[6])) ? $atts[6] : '';      // AC autopopulates a select list based on choice?
		$type  = (isset($atts[7])) ? $atts[7] : 'multi'; // Allow single or multiple entries
		$dsabl = (isset($atts[8])) ? $atts[8] : '';      // Readonly input

		$base = rtrim(JURI::getInstance()->base(true), '/');
		$datascript = $base . '/index.php';

		$base = str_replace('/administrator', '', $base);

		if ($opt != 'members')
		{
			$datascript = str_replace('/administrator', '', $datascript);
		}

		// Push some needed scripts and stylings to the template but ensure we do it only once
		if ($this->_pushscripts)
		{
			$scripts .= '<script type="text/javascript">var plgAutocompleterCss = "';

			$app = JFactory::getApplication();
			$templatecss = DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . 'plg_hubzero_autocompleter' . DS . 'autocompleter.css';
			$plugincss = DS . 'plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'autocompleter.css';
			if (file_exists(JPATH_SITE . $templatecss))
			{
				$scripts .= $base . $templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss);
			}
			else
			{
				$scripts .= $base . $plugincss . '?v=' . filemtime(JPATH_SITE . $plugincss);
			}

			$scripts .= '";</script>' . "\n";
			$scripts .= '<script type="text/javascript" src="' . $base . DS . 'plugins' . DS . $this->_type . DS . $this->_name . DS . $this->_name . '.js"></script>' . "\n";

			$this->_pushscripts = false;
		}

		// Build the input tag
		$html  = '<input type="text" name="' . $name . '" data-options="' . $opt . ',' . $type . ',' . $wsel . '"';
		$html .= ($id)    ? ' id="' . $id . '"'             : '';
		$html .= ($class) ? ' class="' . trim($class) . '"' : '';
		$html .= ($size)  ? ' size="' . $size . '"'         : '';
		$html .= ($dsabl) ? ' readonly="readonly"'          : '';
		$html .= ' value="' . htmlentities($value, ENT_COMPAT, 'UTF-8') . '" autocomplete="off" data-css="" data-script="' . $datascript . '" />' . "\n";
		$html .= $scripts;

		/*$json = '';
		if ($value)
		{
			$items = array();
			$data = explode(',', $value);
			$data = array_map('trim', $data);
			foreach ($data as $item)
			{
				if ($type != 'tags')
				{
					if (preg_match('/(.*)\((.*)\)/U', $item, $matched)) {
						$itemId = htmlentities(stripslashes($matched[2]), ENT_COMPAT, 'UTF-8');
						$itemName = htmlentities(stripslashes($matched[1]), ENT_COMPAT, 'UTF-8');
					} else {
						$itemId = htmlentities(stripslashes($item), ENT_COMPAT, 'UTF-8');
						$itemName = $itemId;
					}
				}
				$items[] = "{'id':'".$itemId."','name':'".$itemName."'}";
			}
			$json = '['.implode(',',$items).']';
		}

		$html .= '<input type="hidden" name="pre-'.$name.'" rel="'.$id.'"';
		$html .= ($id)    ? ' id="pre-'.$id.'"'       : '';
		$html .= ($class) ? ' class="pre-'.trim($class).'"' : '';
		$html .= ' value="'. $json .'" />';*/

		// Return the Input tag
		return $html;
	}

	/**
	 * Display the autocompleter for a multi-entry field
	 *
	 * @param      array $atts Attributes for setting up the autocomplete
	 * @return     string HTML
	 */
	public function onGetMultiEntry($atts)
	{
		if (!is_array($atts))
		{
			$atts = array();
		}
		$params   = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'tags';
		$params[] = (isset($atts[1])) ? $atts[1] : 'tags';
		$params[] = (isset($atts[2])) ? $atts[2] : 'act';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = '';
		$params[] = 'multi';
		$params[] = (isset($atts[6])) ? $atts[6] : '';

		return $this->onGetAutocompleter($params);
	}

	/**
	 * Display the autocompleter for a single entry field
	 *
	 * @param      array $atts Attributes for setting up the autocomplete
	 * @return     string HTML
	 */
	public function onGetSingleEntry($atts)
	{
		if (!is_array($atts))
		{
			$atts = array();
		}
		$params   = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'tags';
		$params[] = (isset($atts[1])) ? $atts[1] : 'tags';
		$params[] = (isset($atts[2])) ? $atts[2] : 'act';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = '';
		$params[] = 'single';
		$params[] = (isset($atts[6])) ? $atts[6] : '';

		return $this->onGetAutocompleter($params);
	}

	/**
	 * Display the autocompleter for a single entry field with accompanying select
	 *
	 * @param      array $atts Attributes for setting up the autocomplete
	 * @return     string HTML
	 */
	public function onGetSingleEntryWithSelect($atts)
	{
		if (!is_array($atts))
		{
			$atts = array();
		}
		$params   = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'groups';
		$params[] = (isset($atts[1])) ? $atts[1] : 'groups';
		$params[] = (isset($atts[2])) ? $atts[2] : 'acg';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = (isset($atts[6])) ? $atts[6] : 'ticketowner';
		$params[] = 'single';
		$params[] = (isset($atts[7])) ? $atts[7] : '';

		return $this->onGetAutocompleter($params);
	}
}
