<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	 If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	  hubzero-cms
 * @author	  Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	  http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'comment.php');

use Hubzero\Base\Model;

/**
 * Comments model class for a project
 */
class Comment extends Model
{
	/**
	 * ForumTablePost
	 *
	 * @var object
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\Comment';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_projects.comment.comment';

	/**
	 * User
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
	 *
	 * @param	   mixed $oid ID (int) or alias (string)
	 * @return	   object Comment
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
	 * Return a formatted timestamp
	 *
	 * @param	   string $as What format to return
	 * @return	   boolean
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
	 * it returns the entire User object
	 *
	 * @param	   string $property What data to return
	 * @param	   mixed  $default	Default value
	 * @return	   mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\User))
		{
			$this->_creator = \Hubzero\User\User::oneOrNew($this->get('created_by'));
		}
		if ($property)
		{
			$property = ($property == 'uidNumber') ? 'id' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->picture($this->get('anonymous'));
			}
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Get the content of the entry
	 *
	 * @param	   string  $as		Format to return state in [text, number]
	 * @param	   integer $shorten Number of characters to shorten text to
	 * @return	   string
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
						'option'   => $this->get('option', 'com_projects'),
						'scope'	   => $this->get('scope'),
						'pagename' => $this->get('alias'),
						'pageid'   => 0,
						'filepath' => $this->get('path'),
						'domain'   => ''
					);

					$content = str_replace(array('\"', "\'"), array('"', "'"), (string) $this->get('comment', ''));
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
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = str_replace(array('\"', "\'"), array('"', "'"), $this->get('comment'));
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
	 * Get a list or count of comments
	 *
	 * @param	   string  $rtrn	Data format to return
	 * @param	   array   $filters Filters to apply to data fetch
	 * @param	   boolean $clear	Clear cached data?
	 * @return	   mixed
	 */
	public function replies($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('entry_id');
		}
		if (!isset($filters['parent']))
		{
			$filters['parent'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count) || $clear)
				{
					$this->_comments_count = 0;

					if (!$this->_comments)
					{
						$c = $this->comments('list', $filters);
					}
					foreach ($this->_comments as $com)
					{
						$this->_comments_count++;
						if ($com->replies())
						{
							foreach ($com->replies() as $rep)
							{
								$this->_comments_count++;
								if ($rep->replies())
								{
									$this->_comments_count += $rep->replies()->total();
								}
							}
						}
					}
				}
				return $this->_comments_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_comments instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($this->get('replies', null) !== null)
					{
						$results = $this->get('replies');
					}
					else
					{
						$results = $this->_tbl->getAllComments($this->get('entry_id'), $this->get('id'));
					}

					if ($results)
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Comment($result);
							$results[$key]->set('option', $this->get('option'));
							$results[$key]->set('scope', $this->get('scope'));
							$results[$key]->set('alias', $this->get('alias'));
							$results[$key]->set('path', $this->get('path'));
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new \Hubzero\Base\ItemList($results);
				}
				return $this->_comments;
			break;
		}
	}
}
