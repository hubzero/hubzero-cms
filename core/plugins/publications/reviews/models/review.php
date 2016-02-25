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

// No direct access
defined('_HZEXEC_') or die();

require_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'review.php');
require_once(__DIR__ . '/comment.php');

/**
 * Publications review mdoel
 */
class PublicationsModelReview extends \Hubzero\Base\Model
{
	/**
	 * ResourcesReview
	 *
	 * @var object
	 */
	protected $_tbl_name = '\\Components\\Publications\\Tables\\Review';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_publications.review.comment';

	/**
	 * USer
	 *
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_comments = NULL;

	/**
	 * Commen count
	 *
	 * @var integer
	 */
	private $_comments_count = NULL;

	/**
	 * Returns a reference to a blog comment model
	 *
	 * @param      mixed $oid ID (int) or alias (string)
	 * @return     object
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * HAs this comment been reported
	 *
	 * @return     boolean True if reported, False if not
	 */
	public function isReported()
	{
		if ($this->get('reports', -1) > 0)
		{
			return true;
		}
		// Reports hasn't been set
		if ($this->get('reports', -1) == -1)
		{
			if (is_file(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php'))
			{
				include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');
				$ra = new \Components\Support\Tables\ReportAbuse($this->_db);
				$val = $ra->getCount(array(
					'id'       => $this->get('id'),
					'category' => 'pubreview'
				));
				$this->set('reports', $val);
				if ($this->get('reports') > 0)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What format to return
	 * @return     boolean
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->getPicture($this->get('anonymous'));
			}
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get a list or count of comments
	 *
	 * @param      string  $rtrn    Data format to return
	 * @param      array   $filters Filters to apply to data fetch
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function replies($rtrn='list', $filters=array(), $clear=false)
	{
		if (is_array($rtrn))
		{
			$filters = $rtrn;
			$rtrn = 'list';
		}

		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('id');
		}
		if (!isset($filters['item_type']))
		{
			$filters['item_type'] = 'pubreview';
		}
		if (!isset($filters['parent']))
		{
			$filters['parent'] = 0;
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = array(self::APP_STATE_PUBLISHED, self::APP_STATE_FLAGGED);
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count) || $clear)
				{
					$this->_comments_count = Components\Publications\Reviews\Models\Comment::all()
						->whereEquals('item_id', $filters['item_id'])
						->whereEquals('item_type', $filters['item_type'])
						->whereIn('state', $filters['state'])
						->total();
				}
				return $this->_comments_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_comments || $clear)
				{
					$results = Components\Publications\Reviews\Models\Comment::all()
						->whereEquals('parent', $filters['parent'])
						->whereEquals('item_id', $filters['item_id'])
						->whereEquals('item_type', $filters['item_type'])
						->whereIn('state', $filters['state'])
						->ordered()
						->rows();

					$this->_comments = $results;
				}
				return $this->_comments;
			break;
		}
	}

	/**
	 * Get the content of the entry
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('comment.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => $this->get('option', Request::getCmd('option', 'com_publications')),
						'scope'    => 'reviews',
						'pagename' => $this->get('publication_id'),
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get('comment', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('comment.parsed', (string) $this->get('comment', ''));
					$this->set('comment', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('comment.parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('comment'));
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
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			$this->_base = 'index.php?option=com_publications&id=' . $this->get('item_id') . '&active=reviews';
		}
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&action=edit&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&action=delete&comment=' . $this->get('id');
			break;

			case 'reply':
				$link .= '&action=reply&category=review&refid=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=pubreview&id=' . $this->get('id') . '&parent=' . $this->get('publication_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}
}

