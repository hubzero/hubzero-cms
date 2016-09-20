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

namespace Components\Support\Models\Orm;

use Hubzero\Database\Relational;
use Filesystem;

/**
 * Support ticket attachment model
 */
class Attachment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support';

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
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'filename' => 'notempty',
		'ticket'   => 'positive|nonzero'
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
		'filename'
	);

	/**
	 * Ensure no invalid characters
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticFilename($data)
	{
		$data['filename'] = preg_replace("/[^A-Za-z0-9.]/i", '-', $data['filename']);

		return $data['filename'];
	}

	/**
	 * Get parent ticket
	 *
	 * @return  object
	 */
	public function ticket()
	{
		return $this->belongsToOne('Ticket', 'ticket');
	}

	/**
	 * Get parent comment
	 *
	 * @return  object
	 */
	public function comment()
	{
		return $this->belongsToOne('Comment', 'comment_id');
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
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function isImage()
	{
		return preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $this->get('filename'));
	}

	/**
	 * Does the file exist on the server?
	 *
	 * @return  boolean
	 */
	public function hasFile()
	{
		return file_exists($this->path());
	}

	/**
	 * File path
	 *
	 * @return  string
	 */
	public function path()
	{
		return PATH_APP . '/site/support/' . $this->get('ticket') . '/' . $this->get('comment_id') . '/' . $this->get('filename');
	}

	/**
	 * Delete record
	 *
	 * @return  boolean  True if successful, False if not
	 */
	public function destroy()
	{
		if ($this->hasFile())
		{
			if (!Filesystem::delete($this->path()))
			{
				$this->addError('Unable to delete file.');

				return false;
			}
		}

		return parent::destroy();
	}

	/**
	 * Load a record by comment ID and filename
	 *
	 * @param   integer  $comment_id
	 * @param   string   $filename
	 * @return  object
	 */
	public static function oneByComment($comment_id, $filename)
	{
		return self::all()
			->whereEquals('comment_id', (int)$comment_id)
			->whereEquals('filename', (string)$filename)
			->row();
	}
}
