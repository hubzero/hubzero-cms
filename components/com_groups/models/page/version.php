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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// include needed jtables
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'page.version.php';

class GroupsModelPageVersion extends \Hubzero\Model
{
	/**
	 * GroupsTablePageCategory
	 * 
	 * @var object
	 */
	protected $_tbl = null;
	
	/**
	 * Table name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'GroupsTablePageVersion';
	
	/**
	 * Constructor
	 * 
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct($oid = null)
	{
		// create database object
		$this->_db = JFactory::getDBO();
		
		// create page cateogry jtable object
		$this->_tbl = new $this->_tbl_name($this->_db);
		
		// load object 
		if (is_numeric($oid))
		{
			$this->_tbl->load( $oid );
		}
		else if(is_object($oid) || is_array($oid))
		{
			$this->bind( $oid );
		}
	}
	
	/**
	 * Overload Store method so we can run some purifying before save
	 *
	 * @param    bool    $check              Run the Table Check Method
	 * @param    bool    $trustedContent     Is content trusted
	 * @return   void
	 */
	public function store($check = true, $trustedContent = false)
	{
		//get content
		$content = $this->get('content');
		
		// if content is not trusted, strip php and scripts
		if (!$trustedContent)
		{
			$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
			$content = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
		}
		
		// purify content
		$content = $this->purify($content, $trustedContent);
		
		// set the purified content
		$this->set('content', $content);
		
		// call parent store
		if (!parent::store($check))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Purify the HTML content via HTML Purifier
	 * 
	 * @param     string    $content           Unpurified HTML content
	 * @param     bool      $trustedContent    Is the content trusted?
	 * @return    string
	 */
	private function purify( $content, $trustedContent = false )
	{
		// load html purifier
		require_once JPATH_ROOT . DS . 'vendor' . DS .'ezyang' . DS . 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php';
		
		// create config
		$config = HTMLPurifier_Config::createDefault();
		$config->set('AutoFormat.Linkify', true);
		$config->set('AutoFormat.RemoveEmpty', true);
		$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
		$config->set('Output.CommentScriptContents', false);
		$config->set('Output.TidyFormat', true);
		
		//create array of custom filters
		$filters = array(
			new HTMLPurifier_Filter_GroupInclude()
		);
		
		// is this trusted content
		if ($trustedContent)
		{
			$config->set('CSS.Trusted', true);
			$config->set('HTML.Trusted', true);
			
			$filters[] = new HTMLPurifier_Filter_ExternalScripts();
			$filters[] = new HTMLPurifier_Filter_Php();
		}
		
		// set filter configs
		$config->set('Filter.Custom', $filters);
		
		// purify and return
		$purifier = new HTMLPurifier( $config );
		return $purifier->purify( $content );
	}
}