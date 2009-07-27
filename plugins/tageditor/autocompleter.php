<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_tageditor_autocompleter' );

//-----------

class plgTageditorAutocompleter extends JPlugin
{
	function plgTageditorAutocompleter(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tageditor', 'autocompleter' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function onTagsEdit( $atts ) 
	{
		$name  = $atts[0];
		$id    = (isset($atts[1])) ? $atts[1] : 'actags';
		$class = (isset($atts[2])) ? $atts[2] : '';
		$value = (isset($atts[3])) ? $atts[3] : '';
		$size  = (isset($atts[4])) ? $atts[4] : '';

		$document =& JFactory::getDocument();
		$document->addScript('plugins'.DS.'tageditor'.DS.'textboxlist.js');
		$document->addScript('plugins'.DS.'tageditor'.DS.'observer.js');
		$document->addScript('plugins'.DS.'tageditor'.DS.'autocompleter.js');
		$document->addStyleSheet('plugins'.DS.'tageditor'.DS.'autocompleter.css');

		$html  = '<input type="text" name="'.$name.'"';
		$html .= ($id)    ? ' id="'.$id.'"'       : '';
		$html .= ($class) ? ' class="'.$class.'"' : '';
		$html .= ($size)  ? ' size="'.$size.'"'   : '';
		$html .= ' value="'.$value.'" autocomplete="off" />';
		//$html .= '<div style="display: none;" class="autocompleter-loading"></div>';

		return $html;
	}
}