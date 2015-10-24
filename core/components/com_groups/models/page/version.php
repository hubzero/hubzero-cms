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

namespace Components\Groups\Models\Page;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;
use Request;

// Include needed tables
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'page.version.php';

/**
 * Group page version model class
 */
class Version extends Model
{
	/**
	 * Table object
	 *
	 * @var  object
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Groups\\Tables\\PageVersion';

	/**
	 * Model context
	 *
	 * @var  string
	 */
	protected $_context = 'com_groups.page_version.content';

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Integer, array, or object
	 * @return  void
	 */
	public function __construct($oid = null)
	{
		// Create database object
		$this->_db = \App::get('db');

		// Create page cateogry jtable object
		$this->_tbl = new $this->_tbl_name($this->_db);

		// Load object
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
	 * Overload Store method so we can run some purifying before save
	 *
	 * @param   boolean  $check           Run the Table Check Method
	 * @param   boolean  $trustedContent  Is content trusted
	 * @return  void
	 */
	public function store($check = true, $trustedContent = false)
	{
		// We must have a page id to save the page
		if ($this->get('pageid') < 1)
		{
			return true;
		}

		// Get content
		$content = $this->get('content');

		// If content is not trusted, strip php and scripts
		if (!$this->get('page_trusted', 0))
		{
			if (!$trustedContent)
			{
				$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
				$content = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
			}

			// Purify content
			$content = $this->purify($content, $trustedContent);
		}

		// Set the purified content
		$this->set('content', $content);

		// Call parent store
		if (!parent::store($check))
		{
			return false;
		}
		return true;
	}

	/**
	 * Get the content of the page version
	 *
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
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
					// Get group
					$group = \Hubzero\User\Group::getInstance(Request::getVar('cn', Request::getVar('gid', '')));

					// Get base path
					$basePath = \Component::params( 'com_groups' )->get('uploadpath');

					// Build config
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

		require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'filters' . DS . 'GroupInclude.php';

		// Create array of custom filters
		$filters = array(
			new \HTMLPurifier_Filter_GroupInclude()
		);

		// Is this trusted content
		if ($trustedContent)
		{
			require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'filters' . DS . 'ExternalScripts.php';
			require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'filters' . DS . 'Php.php';

			$options['CSS.Trusted']  = true;
			$options['HTML.Trusted'] = true;

			$filters[] = new \HTMLPurifier_Filter_ExternalScripts();
			$filters[] = new \HTMLPurifier_Filter_Php();
		}

		// Add our custom filters
		$options['Filter.Custom'] = $filters;

		// Turn OFF linkify
		$options['AutoFormat.Linkify'] = false;

		// Run hubzero html sanitize
		return \Hubzero\Utility\Sanitize::html($content, $options);
	}

	/**
	 * Return a URL to this page
	 *
	 * @param   string  $to  Format
	 * @return  string
	 */
	public function url($to = 'raw')
	{
		$page  = new Page($this->get('pageid'));
		$group = \Hubzero\User\Group::getInstance($page->get('gidNumber'));

		$url = 'index.php?option=com_groups&cn=' . $group->get('cn');
		switch ($to)
		{
			case 'restore':
				$url .= '&controller=pages&task=restore&pageid=' . $page->get('id') . '&version=' . $this->get('version');
			break;
			case 'raw':
			default:
				$url .= '&controller=pages&task=raw&pageid=' . $page->get('id') . '&version=' . $this->get('version');
		}

		return \Route::url($url);
	}
}