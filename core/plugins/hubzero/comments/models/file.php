<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Hubzero\Comments\Models;

use Hubzero\Item\Comment\File as ItemFile;

/**
 * Model class for a forum post attachment
 */
class File extends ItemFile
{
	/**
	 * Does the file exist?
	 *
	 * @return  boolean
	 */
	public function exists()
	{
		return file_exists($this->path());
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		static $path;

		if (!$path)
		{
			$path = $this->getUploadDir();
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				$link = $path . DS . $this->get('comment_id');
			break;

			case 'path':
			case 'filepath':
				$link = $path . DS . $this->get('comment_id') . DS . $this->get('filename');
			break;

			case 'permalink':
			default:
				$link = with(new \Hubzero\Content\Moderator($this->path(), 'public'))->getUrl();
			break;
		}

		return $link;
	}
}
