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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Date;
use User;
use Lang;

require_once __DIR__ . DS . 'attachment.php';
require_once __DIR__ . DS . 'author.php';
require_once __DIR__ . DS . 'license.php';

/**
 * Model class for publication version
 */
class Version extends Relational
{
	/**
	 * State constants
	 *
	 * UNPUBLISHED = 0
	 * PUBLISHED   = 1
	 * DELETED     = 2
	 **/
	const STATE_DRAFT   = 3;
	const STATE_READY   = 4;
	const STATE_PENDING = 5;
	const STATE_WIP     = 7;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'publication';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'publication_id' => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'modified',
		'modified_by'
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
	 * Fields that have content that can/should be parsed
	 *
	 * @var  array
	 **/
	protected $parsed = array(
		'notes',
		'description'
	);

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		if (!isset($data['modified']) || !$data['modified'] || $data['modified'] == '0000-00-00 00:00:00')
		{
			$data['modified'] = Date::of('now')->toSql();
		}
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array  $data  the data being saved
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		if (!isset($data['modified_by']) || !$data['modified_by'])
		{
			$data['modified_by'] = User::get('id');
		}
		return $data['modified_by'];
	}

	/**
	 * Establish relationship to parent publication
	 *
	 * @return  object
	 */
	public function publication()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Publication', 'publication_id');
	}

	/**
	 * Establish relationship to authors
	 *
	 * @return  object
	 */
	public function authors()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Author', 'publication_version_id');
	}

	/**
	 * Establish relationship to attachments
	 *
	 * @return  object
	 */
	public function attachments()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Attachment', 'publication_version_id');
	}

	/**
	 * Establish relationship to license
	 *
	 * @return  object
	 */
	public function license()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\License', 'license_type');
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get the modifier of this entry
	 *
	 * @return  object
	 */
	public function modifier()
	{
		return $this->belongsToOne('Hubzero\User\User', 'modified_by');
	}

	/**
	 * Establish relationship to curator
	 *
	 * @return  object
	 */
	public function curator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'curator');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove authors
		foreach ($this->authors as $author)
		{
			if (!$author->destroy())
			{
				$this->addError($author->getError());
				return false;
			}
		}

		// Remove attachments
		foreach ($this->attachments as $attachment)
		{
			if (!$attachment->destroy())
			{
				$this->addError($attachment->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Check if the resource was deleted
	 *
	 * @return  bool
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_DELETED);
	}

	/**
	 * Check if the draft is ready
	 *
	 * @return  bool
	 */
	public function isReady()
	{
		return ($this->get('state') == self::STATE_READY);
	}

	/**
	 * Check if the resource is pending approval
	 *
	 * @return  bool
	 */
	public function isPending()
	{
		return ($this->get('state') == self::STATE_PENDING);
	}

	/**
	 * Check if the resource is pending author changes
	 *
	 * @return  bool
	 */
	public function isWorked()
	{
		return ($this->get('state') == self::STATE_WIP);
	}

	/**
	 * Is publication unpublished?
	 *
	 * @return  boolean
	 */
	public function isUnpublished()
	{
		return ($this->get('state') == self::STATE_UNPUBLISHED);
	}

	/**
	 * Is this main version
	 *
	 * @return  boolean
	 */
	public function isMain()
	{
		return ($this->get('main') == 1);
	}

	/**
	 * Is this dev version
	 *
	 * @return  boolean
	 */
	public function isDev()
	{
		return ($this->get('state') == self::STATE_DRAFT || $this->get('version_label') == 'dev');
	}

	/**
	 * Is this main published version?
	 *
	 * @return  boolean
	 */
	public function isCurrent()
	{
		return ($this->isMain() && $this->get('state') == self::STATE_PUBLISHED);
	}

	/**
	 * Does publication have future release date?
	 *
	 * @return  boolean
	 */
	public function isEmbargoed()
	{
		if (!$this->get('published_up') || $this->get('published_up') == '0000-00-00 00:00:00')
		{
			return false;
		}

		if (Date::of($this->get('published_up'))->toUnix() > Date::toUnix())
		{
			return true;
		}

		return false;
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function created($as='')
	{
		return $this->_date('created', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function modified($as='')
	{
		return $this->_date('modified', $as);
	}

	/**
	 * Return a formatted published timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function published($as='')
	{
		if ($this->get('accepted')
		 && $this->get('accepted') != '0000-00-00 00:00:00'
		 && $this->get('accepted') > $this->get('published_up'))
		{
			return $this->_date('accepted', $as);
		}
		return $this->_date('published_up', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function unpublished($as='')
	{
		return $this->_date('published_down', $as);
	}

	/**
	 * Return a formatted submitted timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function submitted($as='')
	{
		return $this->_date('submitted', $as);
	}

	/**
	 * Return a formatted accepted timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function accepted($as='')
	{
		return $this->_date('accepted', $as);
	}

	/**
	 * Return a formatted archived timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function archived($as='')
	{
		return $this->_date('archived', $as);
	}

	/**
	 * Return a formatted released timestamp
	 *
	 * @param   string  $as  Format (date, time, datetime, timeago, ...)
	 * @return  string
	 */
	public function released($as='')
	{
		return $this->_date('released', $as);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $key  Field to return
	 * @param   string  $as   What data to return
	 * @return  string
	 */
	protected function _date($key, $as='')
	{
		if ($this->get($key) == '0000-00-00 00:00:00')
		{
			return '';
		}

		$dt = $this->get($key);

		switch (strtolower($as))
		{
			case 'date':
				$dt = Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				break;

			case 'time':
				$dt = Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
				break;

			case 'datetime':
				$dt = $this->_date($key, 'date') . ' &#64; ' . $this->_date($key, 'time');
				break;

			case 'timeago':
				$dt = Date::of($dt)->relative();
				break;

			default:
				break;
		}

		return $dt;
	}

	/**
	 * Get the filespace path
	 *
	 * @return  string
	 */
	public function filespace()
	{
		$pid = String::pad($this->get('publication_id'), 5);
		$vid = String::pad($this->get('id'), 5);
		$sec = $this->get('secret');

		return PATH_APP . '/site/publications/' . $pid . '/' . $vid . '/' . $sec;
	}
}
