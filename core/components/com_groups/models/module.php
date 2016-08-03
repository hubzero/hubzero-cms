<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models;

use Components\Groups\Tables;
use Hubzero\Base\Model;
use Hubzero\Base\Model\ItemList;
use Request;

// include needed tables
require_once dirname(__DIR__) . DS . 'tables' . DS . 'module.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'module.menu.php';

/**
 * Group module model class
 */
class Module extends Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Groups\\Tables\\Module';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_groups.module.content';

	/**
	 * Menu Items
	 *
	 * @var array
	 */
	private $_menu_items = null;

	/**
	 * Constructor
	 *
	 * @param      mixed $oid
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Module($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Get module menu
	 *
	 * @param     string  $rtrn    What do we want back
	 * @param     array   $filters Array of filters to use when getting menu
	 * @param     boolean $clear   Fetch an updated list
	 * @return    object  \Hubzero\Base\ItemList
	 */
	public function menu($rtrn = 'list', $filters = array(), $clear = false)
	{
		$tbl = new Tables\ModuleMenu($this->_db);

		// make sure we have a moduleId
		if (!isset($filters['moduleid']))
		{
			$filters['moduleid'] = $this->get('id');
		}

		// get module menu items
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_menu_items instanceof ItemList) || $clear)
				{
					if ($results = $tbl->getMenu($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Module\Menu($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_menu_items = new ItemList($results);
				}
				return $this->_menu_items;
			break;
		}
	}

	/**
	 * Build module menu
	 *
	 * @param   array   $modulesMenu
	 * @return  boolean
	 */
	public function buildMenu($modulesMenu = array())
	{
		// create module menu object
		$tbl = new Tables\ModuleMenu($this->_db);

		// delete any previous menu items
		if (!$tbl->deleteMenus($this->get('id')))
		{
			$this->setError($tbl->getError());
			return false;
		}

		// get module id and array of pages
		$moduleid = $this->get('id');
		$assigned = (isset($modulesMenu['assigned'])) ? $modulesMenu['assigned'] : array();
		$pages    = ($modulesMenu['assignment'] == '0') ? array(0) : $assigned;

		// create new menus
		if (!$tbl->createMenus($moduleid, $pages))
		{
			$this->setError($tbl->getError());
			return false;
		}

		// everything went smoothly
		return true;
	}

	/**
	 * Should we display module on this page?
	 *
	 * @param   integer $pageid
	 * @return  boolean
	 */
	public function displayOnPage($pageid = null)
	{
		// get module menu
		$menus = $this->menu('list');

		// if we only have one menu && menu pageid 0 (display on all pages)
		if ($menus->count() == 1 && $menus->first()->get('pageid') == 0)
		{
			return true;
		}

		// attempt to load menu for this page
		if ($menus->fetch('pageid', $pageid) !== null)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check to see if group owns module
	 *
	 * @param   object  $group \Hubzero\User\Group
	 * @return  boolean
	 */
	public function belongsToGroup($group)
	{
		if ($this->get('gidNumber') == $group->get('gidNumber'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Overload Store method so we can run some purifying before save
	 *
	 * @param   boolean  $check           Run the Table Check Method
	 * @param   boolean  $trustedContent  Is content trusted
	 * @return  void
	 */
	public function store($check = true, $trustedContent = false)
	{
		if (!$this->get('page_trusted', 0))
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
		}

		// call parent store
		if (!parent::store($check))
		{
			return false;
		}
		return true;
	}

	/**
	 * Get the next order value for position
	 *
	 * @param   string  $position Module Position
	 * @return  integer
	 */
	public function getNextOrder($position)
	{
		$order = $this->_tbl->getNextOrder("position='".$position."'");
		return $order;
	}

	/**
	 * Reorder Module for position
	 *
	 * @param   string  $move      Direction and Magnitude
	 * @param   string  $position  Module Position
	 * @return  integer
	 */
	public function move($move, $position)
	{
		// determine if we need to move up or down
		$dir = '';
		if ($move < 0)
		{
			$dir = '-';
			$move = substr($move, 1);
		}

		// move the number of times different
		for ($i=0; $i < $move; $i++)
		{
			$this->_tbl->move($dir.'1', "position='".$position."'");
		}
	}

	/**
	 * Get the content of the page version
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('content_parsed', null);
				if ($content == null)
				{
					// get group
					$group = \Hubzero\User\Group::getInstance(Request::getVar('cn', Request::getVar('gid', '')));

					// get base path 
					$basePath = \Component::params('com_groups')->get('uploadpath');

					// build config
					$config = array(
						'option'         => Request::getCmd('option', 'com_groups'),
						'scope'          => '',
						'pagename'       => $group->get('cn'),
						'pageid'         => 0,
						'filepath'       => $basePath . DS . $group->get('gidNumber') . DS . 'uploads',
						'domain'         => $group->get('cn'),
						'alt_macro_path' => PATH_APP . $basePath . DS . $group->get('gidNumber') . DS . 'macros'
					);

					$content = stripslashes($this->get('content'));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content_parsed', $this->get('content'));
					$this->set('content', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('content'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Purify the HTML content via HTML Purifier
	 *
	 * @param   string   $content         Unpurified HTML content
	 * @param   boolean  $trustedContent  Is the content trusted?
	 * @return  string
	 */
	public static function purify($content, $trustedContent = false)
	{
		// array to hold options
		$options = array();

		//create array of custom filters
		$filters = array(
			new \HTMLPurifier_Filter_GroupInclude()
		);

		// is this trusted content
		if ($trustedContent)
		{
			$options['CSS.Trusted'] = true;
			$options['HTML.Trusted'] = true;

			$filters[] = new \HTMLPurifier_Filter_ExternalScripts();
			$filters[] = new \HTMLPurifier_Filter_Php();
		}

		// add our custom filters
		$options['Filter.Custom'] = $filters;

		// turn OFF linkify
		$options['AutoFormat.Linkify'] = false;

		// run hubzero html sanitize
		return \Hubzero\Utility\Sanitize::html($content, $options);
	}
}