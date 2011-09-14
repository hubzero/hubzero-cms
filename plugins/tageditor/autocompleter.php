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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_tageditor_autocompleter' );

/**
 * Short description for 'plgTageditorAutocompleter'
 * 
 * Long description (if any) ...
 */
class plgTageditorAutocompleter extends JPlugin
{

	/**
	 * Short description for 'plgTageditorAutocompleter'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgTageditorAutocompleter(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tageditor', 'autocompleter' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onTagsEdit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $atts Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function onTagsEdit( $atts )
	{
		$name  = $atts[0];
		$id    = (isset($atts[1])) ? $atts[1] : 'actags';
		$class = (isset($atts[2])) ? $atts[2] : '';
		$value = (isset($atts[3])) ? $atts[3] : '';
		$size  = (isset($atts[4])) ? $atts[4] : '';

		$document =& JFactory::getDocument();
		$document->addScript(DS.'plugins'.DS.'tageditor'.DS.'textboxlist.js');
		$document->addScript(DS.'plugins'.DS.'tageditor'.DS.'observer.js');
		$document->addScript(DS.'plugins'.DS.'tageditor'.DS.'autocompleter.js');
		$document->addStyleSheet(DS.'plugins'.DS.'tageditor'.DS.'autocompleter.css');

		$html  = '<input type="text" name="'.$name.'"';
		$html .= ($id)    ? ' id="'.$id.'"'       : '';
		$html .= ($class) ? ' class="'.$class.'"' : '';
		$html .= ($size)  ? ' size="'.$size.'"'   : '';
		$html .= ' value="'.$value.'" autocomplete="off" />';
		//$html .= '<div style="display: none;" class="autocompleter-loading"></div>';

		return $html;
	}
}
