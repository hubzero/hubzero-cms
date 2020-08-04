<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Picture;

use Hubzero\Content\Moderator;
use Hubzero\Utility\Str;

/**
 * User picture
 */
class File implements Resolver
{
	/**
	 * File path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * File name
	 *
	 * @var  string
	 */
	protected $pictureName = 'profile.png';

	/**
	 * Thumbnail name
	 *
	 * @var  string
	 */
	protected $thumbnailName = 'thumb.png';

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct($config=array())
	{
		if (array_key_exists('pictureName', $config))
		{
			$this->pictureName = $config['pictureName'];
		}

		if (array_key_exists('thumbnailName', $config))
		{
			$this->thumbnailName = $config['thumbnailName'];
		}

		if (array_key_exists('path', $config))
		{
			$this->path = $config['path'];
		}
	}

	/**
	 * Get a path or URL to a user pciture
	 *
	 * @param   integer  $id
	 * @param   string   $name
	 * @param   string   $email
	 * @param   bool     $thumbnail
	 * @return  string
	 */
	public function picture($id, $name, $email, $thumbnail = true)
	{
		$file = $this->pictureName;

		if ($thumbnail)
		{
			$file = $this->thumbnailName;
		}

		$path = $this->path . DIRECTORY_SEPARATOR . Str::pad($id, 5) . DIRECTORY_SEPARATOR . $file;

		if (file_exists($path))
		{
			return with(new Moderator($path, 'public'))->getUrl();
		}

		return '';
	}
}
