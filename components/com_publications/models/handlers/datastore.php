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

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');


/**
 * DataStore Lite Handler
 */
class PublicationsModelHandlerDataStore extends PublicationsModelHandler
{
	/**
	* Handler type name
	*
	* @var		string
	*/
	protected	$_name 		= 'datastore';

	/**
	* Configs
	*
	* @var
	*/
	protected	$_config 	= NULL;

	/**
	 * Get default params for the handler
	 *
	 * @return  void
	 */
	public function getConfig()
	{
		// Defaults
		$configs = array(
			'name' 			=> 'datastore',
			'label' 		=> 'Data Viewer',
			'title' 		=> 'Interactive data explorer',
			'about'			=> 'Selected CSV file will be viewed as a database',
			'params'	=> array(
				'allowed_ext' 		=> array('csv'),
				'required_ext' 		=> array('csv'),
				'min_allowed' 		=> 1,
				'max_allowed' 		=> 1
			)
		);

		$this->_config = json_decode(json_encode($this->_parent->parseConfig($this->_name, $configs)), FALSE);
		return $this->_config;
	}

	/**
	 * Clean-up related files
	 *
	 * @return  void
	 */
	public function cleanup( $path )
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		return true;
	}

	/**
	 * Draw list of included items
	 *
	 * @return  void
	 */
	public function drawList($attachments, $attConfigs, $pub, $authorized )
	{
		// No special treatment for this handler
		return;
	}

	/**
	 * Draw attachment
	 *
	 * @return  void
	 */
	public function drawAttachment($data, $params)
	{
		// No special treatment for this handler
		return;
	}
}