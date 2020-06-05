<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Models;

use Hubzero\Database\Relational;
use Lang;
use Date;
use User;

/**
 * Blog model for a comment
 */
class Comment extends Relational
{
	/**
	 * Database state constants
	 */
	const STATE_FLAGGED = 3;

	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'blog';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'content'  => 'notempty',
		'entry_id' => 'positive|nonzero'
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
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'parent',
		'anonymous',
		'state',
		'modified',
		'modified_by'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'content'
	);

	/**
	 * Generates automatic state field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticState($data)
	{
		if (!isset($data['state']))
		{
			$data['state'] = self::STATE_PUBLISHED;
		}
		return (int)$data['state'];
	}

	/**
	 * Generates automatic state field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAnonymous($data)
	{
		if (!isset($data['anonymous']))
		{
			$data['anonymous'] = 0;
		}
		return (int)$data['anonymous'];
	}

	/**
	 * Generates automatic state field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParent($data)
	{
		if (!isset($data['parent']))
		{
			$data['parent'] = 0;
		}
		return (int)$data['parent'];
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : null);
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		return (isset($data['id']) && $data['id'] ? User::get('id') : 0);
	}

	/**
	 * Parses content string as directed
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		$field = 'content';

		$property = "_{$field}Parsed";

		if (!isset($this->$property))
		{
			$params = array(
				'option'   => $this->get('option', \Request::getCmd('option')),
				'scope'    => $this->get('scope', 'blog'),
				'pagename' => $this->get('alias'),
				'pageid'   => 0,
				'filepath' => $this->get('path'),
				'domain'   => ''
			);

			$this->$property = Html::content('prepare', $this->get($field, ''), $params);
		}

		return $this->$property;
	}

	/**
	 * Return a formatted timestamp for Created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		if (strtolower($as) == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if (strtolower($as) == 'time')
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
	 * Return a formatted timestamp for Modified date
	 * 
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function modified($as='')
	{
		if (strtolower($as) == 'date')
		{
			return Date::of($this->get('modified'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if (strtolower($as) == 'time')
		{
			return Date::of($this->get('modified'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($this->get('modified'))->toLocal($as);
		}

		return $this->get('modified');
	}

	/**
	 * Determine if record was modified
	 * 
	 * @return  boolean  True if modified, false if not
	 */
	public function wasModified()
	{
		return ($this->get('modified') && $this->get('modified') != '0000-00-00 00:00:00');
	}

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
	 * Defines a belongs to one relationship between comment and entry
	 *
	 * @return  object
	 */
	public function entry()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\Entry', 'entry_id');
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
	 * Get either a count of or list of replies
	 *
	 * @param   array  $filters  Filters to apply to query
	 * @return  object
	 */
	public function replies($filters = array())
	{
		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('entry_id');
		}

		$entries = self::blank()
			->including(['creator', function ($creator){
				$creator->select('*');
			}])
			->whereEquals('parent', (int) $this->get('id'));

		if (isset($filters['state']))
		{
			$entries->whereEquals('state', (int) $filters['state']);
		}

		if (isset($filters['entry_id']))
		{
			$entries->whereEquals('entry_id', (int) $filters['entry_id']);
		}

		return $entries;
	}

	/**
	 * Get parent comment
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::oneOrFail($this->get('parent', 0));
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
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

		return parent::destroy();
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
				'com_blog.comment.content',
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
