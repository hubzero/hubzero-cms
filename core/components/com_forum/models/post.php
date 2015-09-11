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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Models;

use Components\Forum\Tables;
use Hubzero\Base\ItemList;
use Hubzero\Utility\String;
use Lang;
use Date;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'post.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'attachment.php');

/**
 * Forum model class for a forum post
 */
class Post extends Base
{
	/**
	 * Table class name
	 *
	 * @var object
	 */
	protected $_tbl_name = '\\Components\\Forum\\Tables\\Post';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_forum.post.comment';

	/**
	 * Attachment
	 *
	 * @var object
	 */
	protected $_attachment = null;

	/**
	 * Returns a reference to a forum post model
	 *
	 * @param   mixed  $oid  ID (int) or array or object
	 * @return  object
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get a post attachment
	 *
	 * @return  object
	 */
	public function attachment()
	{
		if (!isset($this->_attachment))
		{
			$this->_attachment = Attachment::getInstance(0, $this->get('id'));
		}
		return $this->_attachment;
	}

	/**
	 * Has this post been reported?
	 *
	 * @return  boolean True if reported, False if not
	 */
	public function isReported()
	{
		if ($this->get('state') == self::APP_STATE_FLAGGED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string $rtrn Format to return
	 * @return  string
	 */
	public function modified($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return Date::of($this->get('modified'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('modified'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('modified');
			break;
		}
	}

	/**
	 * Determine if record was modified
	 *
	 * @return  boolean True if modified, false if not
	 */
	public function wasModified()
	{
		if ($this->get('modified') && $this->get('modified') != '0000-00-00 00:00:00')
		{
			return true;
		}
		return false;
	}

	/**
	 * Store changes to this entry
	 *
	 * @param   boolean $check Perform data validation check?
	 * @return  boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$new = true;
		if ($this->get('id'))
		{
			$old = new self($this->get('id'));
			$new = false;
		}

		if (!$this->get('anonymous'))
		{
			$this->set('anonymous', 0);
		}

		if (!parent::store($check))
		{
			return false;
		}

		if (!$new)
		{
			$fields = array();

			// If this is a thread (first post), update the access levels
			// of all posts in this thread.
			if (!$this->get('parent') && $old->get('access') != $this->get('access'))
			{
				$fields['access'] = $this->get('access');
			}

			// If the category has changed
			if ($old->get('category_id') != $this->get('category_id'))
			{
				$fields['category_id'] = $this->get('category_id');
			}

			if (!empty($fields))
			{
				$this->_tbl->updateReplies(
					$fields,
					$this->get('id')
				);
			}
		}

		return true;
	}

	/**
	 * Get tags on the entry
	 * Optional first agument to determine format of tags
	 *
	 * @param   string  $as    Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param   integer $admin Include amdin tags? (defaults to no)
	 * @return  boolean
	 */
	public function tags($as='cloud', $admin=0)
	{
		if (!$this->exists())
		{
			switch (strtolower($as))
			{
				case 'array':
					return array();
				break;

				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}

		$cloud = new Tags($this->get('thread'));

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('thread'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string $type   The type of link to return
	 * @param   mixed  $params Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->adapter()->build($type, $params);
	}

	/**
	 * Get the adapter
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!$this->_adapter)
		{
			$this->_adapter = $this->_adapter();
			$this->_adapter->set('thread', $this->get('thread'));
			$this->_adapter->set('parent', $this->get('parent'));
			$this->_adapter->set('post', $this->get('id'));

			if (!$this->get('category'))
			{
				$category = Category::getInstance($this->get('category_id'));
				$this->set('category', $category->get('alias'));
			}
			$this->_adapter->set('category', $this->get('category'));

			if (!$this->get('section'))
			{
				$category = Category::getInstance($this->get('category_id'));
				$this->set('section', Section::getInstance($category->get('section_id'))->get('alias'));
			}
			$this->_adapter->set('section', $this->get('section'));
		}

		return $this->_adapter;
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  mixed   String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('content.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_forum',
						'scope'    => 'forum',
						'pagename' => 'forum',
						'pageid'   => $this->get('thread'),
						'filepath' => '',
						'domain'   => $this->get('thread')
					);

					$attach = new Tables\Attachment($this->_db);

					$content = (string) stripslashes($this->get('comment', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content.parsed', (string) $this->get('comment', ''));
					$this->set('content.parsed', $this->get('content.parsed') . $attach->getAttachment(
						$this->get('id'),
						$this->link('download'),
						$this->_config
					));
					$this->set('comment', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = $this->get('comment');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}
		return $content;
	}
}

