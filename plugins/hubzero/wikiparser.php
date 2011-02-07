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
JPlugin::loadLanguage( 'plg_hubzero_wikiparser' );

//-----------

class plgHubzeroWikiparser extends JPlugin
{
	public $parser;
	
	//-----------
	
	public function plgHubzeroWikiparser(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'hubzero', 'wikiparser' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------

	public function onGetWikiParser( $config, $getnew=false ) 
	{
		if (!is_object($this->parser) || $getnew) {
			$path = dirname(__FILE__);
			if (is_file($path.DS.'wikiparser'.DS.'parser.php')) {
				include_once($path.DS.'wikiparser'.DS.'parser.php');
			} else {
				return null;
			}
			
			$option   = (isset($config['option']))   ? $config['option']   : 'com_wiki';
			$scope    = (isset($config['scope']))    ? $config['scope']    : '';
			$pagename = (isset($config['pagename'])) ? $config['pagename'] : '';
			$pageid   = (isset($config['pageid']))   ? $config['pageid']   : 0;
			$filepath = (isset($config['filepath'])) ? $config['filepath'] : '';
			$domain   = (isset($config['domain']))   ? $config['domain']   : null;
			
			$this->parser = new WikiParser( $option, $scope, $pagename, $pageid, $filepath, $domain );
		}
		return $this->parser;
	}
	
	//-----------
	
	public function onWikiParseText( $text, $config, $fullparse=true, $getnew=false )
	{
		$parser = $this->onGetWikiParser( $config, $getnew );
		
		return is_object($parser) ? $parser->parse( "\n".stripslashes($text), $fullparse ) : $text;
	}
}