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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Components\Wiki\Helpers\Parser;
use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Request;
use Lang;
use Date;
use User;

/**
 * Wiki model for a comment
 */
class Comment extends Relational
{
	/**
	 * Flagged state
	 *
	 * @var  integer
	 */
	const STATE_FLAGGED = 3;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'page_id' => 'positive|nonzero',
		'ctext'   => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a belongs to one relationship between comment and page
	 *
	 * @return  object
	 */
	public function page()
	{
		return $this->belongsToOne('Components\Wiki\Models\Page', 'page_id');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as
	 * @param   string  $format
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($this->get('created'))->toLocal($as);
		}

		return $this->get('created');
	}

	/**
	 * Was the entry reported?
	 *
	 * @return  boolean  True if reported, False if not
	 */
	public function isReported()
	{
		return ($this->get('state') == self::STATE_FLAGGED);
	}

	/**
	 * Get a list of responses
	 *
	 * @param   array   $filters  Filters to apply to query
	 * @return  object
	 */
	public function replies($filters = array())
	{
		$replies = self::blank()
			->including(['creator', function ($creator){
				$creator->select('*');
			}])
			->whereEquals('parent', $this->get('id'));

		if (isset($filters['version']))
		{
			$replies->whereEquals('version', (int)$filters['version']);
		}

		if (isset($filters['created_by']))
		{
			$replies->whereEquals('created_by', (int)$filters['created_by']);
		}

		if (isset($filters['state']))
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$replies->whereIn('state', $filters['state']);
		}

		if (isset($filters['access']))
		{
			if (!is_array($filters['access']))
			{
				$filters['access'] = array($filters['access']);
			}
			$replies->whereIn('access', $filters['access']);
		}

		return $replies;
	}

	/**
	 * Get parent section
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::oneOrFail($this->get('parent', 0));
	}

	/**
	 * Get the content of the entry
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
				if ($this->get('chtml'))
				{
					return $this->get('chtml');
				}

				$parser = Parser::getInstance();

				$parsed = $parser->parse(stripslashes($this->get('ctext')), array(
					'option'   => Request::getCmd('option', 'com_wiki'),
					'scope'    => Request::getVar('scope'),
					'pagename' => Request::getVar('pagename'),
					'pageid'   => $this->get('page_id'),
					'filepath' => '',
					'domain'   => Request::getVar('group', '')
				));

				$this->set('chtml', $parsed);

				if ($shorten)
				{
					$content = String::truncate($this->get('chtml'), $shorten, array('html' => true));
					return $content;
				}

				return $this->get('chtml');
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = $this->get('ctext');
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
	 * @param   string  $type  The type of link to return
	 * @return  boolean
	 */
	public function link($type='')
	{
		if (!isset($this->base))
		{
			$this->base = $this->page->link() . '&' . ($this->page->get('scope_id') ? 'action' : 'task');
		}

		$link = $this->base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '=editcomment&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '=removecomment&comment=' . $this->get('id');
			break;

			case 'reply':
				$link .= '=addcomment&parent=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=wikicomment&id=' . $this->get('id') . '&parent=' . $this->get('pageid');
			break;

			case 'permalink':
			default:
				$link .= '=comments#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if (!$this->get('id'))
		{
			return true;
		}

		// Remove comments
		foreach ($this->replies()->rows() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->addError($comment->getError());
				return false;
			}
		}

		return parent::delete();
	}

	/**
	 * Validates the set data attributes against the model rules
	 *
	 * @return  bool
	 **/
	public function validate()
	{
		$valid = parent::validate();

		if ($valid)
		{
			$results = \Event::trigger('content.onContentBeforeSave', array(
				'com_wiki.comment.ctext',
				&$this,
				$this->isNew()
			));

			foreach ($results as $result)
			{
				if ($result === false)
				{
					$this->addError(Lang::txt('Content failed validation.'));
					$valid = false;
				}
			}
		}

		return $valid;
	}
}
