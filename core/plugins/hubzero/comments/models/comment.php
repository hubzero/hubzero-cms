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

namespace Plugins\Hubzero\Comments\Models;

use Hubzero\Base\Model;
use Hubzero\Base\ItemList;
use Hubzero\User\Profile;
use Hubzero\Utility\String;
use Hubzero\Item\Comment\File;
use Hubzero\Item\Vote;

include __DIR__ . DS . 'attachment.php';

/**
 * Answers model for a comment
 */
class Comment extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Hubzero\\Item\\Comment';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'plg_hubzero_comments.comment.content';

	/**
	 * \Hubzero\User\Profile
	 *
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_cache = array(
		'attachments'   => null
	);

	/**
	 * URL for this entry
	 *
	 * @var string
	 */
	private $_base = null;

	/**
	 * Was the entry reported?
	 *
	 * @return     boolean True if reported, False if not
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
	 * @param      string $as What format to return
	 * @return     string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return \Date::of($this->get('created'))->toLocal(\Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return \Date::of($this->get('created'))->toLocal(\Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				if ($as)
				{
					return $this->get('created', $as);
				}
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
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!($this->_creator instanceof Profile))
		{
			$this->_creator = Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	public function modified($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return \Date::of($this->get('modified'))->toLocal(\Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return \Date::of($this->get('modified'))->toLocal(\Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('modified');
			break;
		}
	}

	/**
	 * Determine if record was modified
	 * 
	 * @return     boolean True if modified, false if not
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
	 * Get a list or count of comments
	 *
	 * @param      string  $rtrn    Data format to return
	 * @param      array   $filters Filters to apply to data fetch
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function replies($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['parent']) && $this->exists())
		{
			$filters['parent'] = $this->get('id');
		}
		if (!isset($filters['item_type']))
		{
			$filters['item_type'] = $this->get('item_type');
		}
		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('item_id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = array(1, 3);
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->get('replies.count')) || $clear)
				{
					$total = 0;

					foreach ($this->replies() as $com)
					{
						// Increment for this comment
						$total++;
						// Add the comment's replies to the total
						$total = $total + $com->replies('count');
					}

					$this->set('replies.count', $total);
				}
				return $this->get('replies.count');
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->get('replies') instanceof ItemList) || $clear)
				{
					$results = $this->get('replies');

					if (!$results)
					{
						$results = $this->_tbl->find($filters);

						if ($results)
						{
							$children = array(
								0 => array()
							);

							$levellimit = ($filters['limit'] == 0) ? 500 : $filters['limit'];

							foreach ($results as $v)
							{
								$v = new Comment($v);

								$pt   = $v->get('parent');
								$list = @$children[$pt] ? $children[$pt] : array();

								array_push($list, $v);
								$children[$pt] = $list;
							}

							$results = $this->_treeRecurse($children[0], $children);
						}
						else
						{
							$results = array();
						}
					}

					$this->set('replies', new ItemList($results));
				}
				return $this->get('replies');
			break;
		}
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param      integer $id       Parent ID
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @return     void
	 */
	private function _treeRecurse($children, $list, $maxlevel=9999, $level=0)
	{
		if ($level <= $maxlevel)
		{
			foreach ($children as $v => $child)
			{
				if (isset($list[$child->get('id')]))
				{
					$children[$v]->set('replies', $this->_treeRecurse($list[$child->get('id')], $list, $maxlevel, $level+1));
				}
			}
		}
		return $children;
	}

	/**
	 * Get a list of attachments on this comment
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Clear cached data or not?
	 * @return     object  \Hubzero\Base\ItemList
	 */
	public function attachments($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['comment_id']))
		{
			$filters['comment_id'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				return $this->attachments('list')->total();
			break;

			case 'first':
				return $this->attachments('list')->first();
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['attachments'] instanceof ItemList) || $clear)
				{
					$tbl = new File($this->_db);

					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Attachment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['attachments'] = new ItemList($results);
				}
				return $this->_cache['attachments'];
			break;
		}
	}

	/**
	 * Get the contents of this entry in various formats
	 *
	 * @param      string  $as      Format to return state in [raw, parsed]
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
				$content = $this->get('content.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => \Request::getCmd('option', 'com_' . $this->get('item_type')),
						'scope'    => $this->get('item_type'),
						'pagename' => $this->get('item_id'),
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get('content', ''));
					if ($content)
					{
						$this->importPlugin('content')->trigger('onContentPrepare', array(
							$this->_context,
							&$this,
							&$config
						));
					}

					$this->set('content.parsed', (string) $this->get('content', ''));
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
			$content = String::truncate($content, $shorten, $options);
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
			$this->_base = $this->get('url', 'index.php?option=com_' . $this->get('item_type') . '&id=' . $this->get('item_id') . '&active=comments');
		}
		$link = $this->_base;

		// check for page slug  (remove for now)
		$slug = '';
		if (strpos($link, '#') !== false)
		{
			list($link, $slug) = explode('#', $link);
			$slug = "#{$slug}";
		}

		$s = '&';
		if (strstr($link, '?') === false)
		{
			$s = '?';
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				$link .= $slug;
			break;

			case 'edit':
				$link .= $s . 'commentedit=' . $this->get('id') . $slug;
			break;

			case 'delete':
				$link .= $s . 'action=commentdelete&comment=' . $this->get('id') . $slug;
			break;

			case 'reply':
				$link .= $s . 'commentreply=' . $this->get('id') . '#c' . $this->get('id');
			break;

			case 'abuse':
			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=itemcomment&id=' . $this->get('id') . '&parent=' . $this->get('parent');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Vote this entry up or down
	 *
	 * @param   integer  $how  How to vote (up or down)
	 * @return  boolean  False if error, True on success
	 */
	public function vote($how)
	{
		$v = Vote::blank();
		$v->set([
			'created_by' => \User::get('id'),
			'item_type'  => 'comment',
			'vote'       => $how,
			'item_id'    => $this->get('id')
		]);

		// Store new content
		if (!$v->save())
		{
			$this->setError($v->getError());
			return false;
		}

		return true;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$result = parent::store($check);

		if ($result)
		{
			// Check file attachment
			$fieldName = 'comment_file';
			if (!empty($_FILES[$fieldName]) && !empty($_FILES[$fieldName]['name']))
			{
				if ($_FILES[$fieldName]['error'])
				{
					$this->setError(\Lang::txt('PLG_HUBZERO_COMMENTS_ERROR_UPLOADING_FILE'));
				}

				$file = new Attachment();
				$file->set('comment_id', $this->get('id'));

				$fileName = $_FILES[$fieldName]['name'];

				// the name of the file in PHP's temp directory that we are going to move to our folder
				$fileTemp = $_FILES[$fieldName]['tmp_name'];

				// lose any special characters in the filename
				$fileName = preg_replace("/[^A-Za-z0-9.]/i", '-', $fileName);

				// always use constants when making file paths, to avoid the possibilty of remote file inclusion
				$uploadDir = $file->link('base');

				if (!is_dir($uploadDir))
				{
					if (!\Filesystem::makeDirectory($uploadDir))
					{
						$this->setError(\Lang::txt('PLG_HUBZERO_COMMENTS_UNABLE_TO_CREATE_UPLOAD_PATH'));
					}
				}

				if (!$this->getError())
				{
					// check if file exists -- rename if needed
					$ext    = strrchr($fileName, '.');
					$prefix = substr($fileName, 0, -strlen($ext));

					// rename file if exists
					$i = 1;
					while (is_file($uploadDir . DS . $fileName))
					{
						$fileName = $prefix . ++$i . $ext;
					}
					$uploadPath = $uploadDir . DS . $fileName;

					if (!\Filesystem::upload($fileTemp, $uploadPath))
					{
						$this->setError(\Lang::txt('PLG_HUBZERO_COMMENTS_ERROR_MOVING_FILE'));
					}
					else
					{
						$file->set('filename', $fileName);
						$file->store();
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Can't delete what doesn't exist
		if (!$this->exists())
		{
			return true;
		}

		// Remove comments
		foreach ($this->replies('list') as $comment)
		{
			if (!$comment->delete())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		// Remove attachments
		foreach ($this->attachments('list') as $attachment)
		{
			if (!$attachment->delete())
			{
				$this->setError($attachment->getError());
				return false;
			}
		}

		return parent::delete();
	}
}

