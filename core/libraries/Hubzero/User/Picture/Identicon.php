<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Picture;

use Hubzero\Image\Identicon as Processor;
use Hubzero\Content\Moderator;
use Hubzero\Utility\Str;

/**
 * Identicon User picture
 */
class Identicon implements Resolver
{
	/**
	 * Primary color
	 *
	 * @var  string
	 */
	protected $color = null;

	/**
	 * File path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * File size
	 *
	 * @var  string
	 */
	protected $pictureSize = 200;

	/**
	 * File name
	 *
	 * @var  string
	 */
	protected $pictureName = 'profile.png';

	/**
	 * Thumbnail size
	 *
	 * @var  string
	 */
	protected $thumbnailSize = 50;

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
		if (array_key_exists('pictureSize', $config))
		{
			$this->pictureSize = $config['pictureSize'];
		}

		if (array_key_exists('pictureName', $config))
		{
			$this->pictureName = $config['pictureName'];
		}

		if (array_key_exists('thumbnailSize', $config))
		{
			$this->thumbnailSize = $config['thumbnailSize'];
		}

		if (array_key_exists('thumbnailName', $config))
		{
			$this->thumbnailName = $config['thumbnailName'];
		}

		if (array_key_exists('path', $config))
		{
			$this->path = $config['path'];
		}

		if (array_key_exists('color', $config))
		{
			$this->color = $config['color'];
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
		$processor = new Processor();

		$size = $this->pictureSize;
		$file = $this->pictureName;

		if ($thumbnail)
		{
			$size = $this->thumbnailSize;
			$file = $this->thumbnailName;
		}

		$dir  = $this->path . DIRECTORY_SEPARATOR . Str::pad($id, 5) . DIRECTORY_SEPARATOR;
		$path = $dir . $file;

		if (file_exists($path))
		{
			return with(new Moderator($path, 'public'))->getUrl();
		}

		$image = $processor->getImageData($email, $size, $this->color);

		if (!is_dir($dir))
		{
			@mkdir($dir, 0755, true);
		}
		@file_put_contents($path, $image);

		if (!file_exists($path))
		{
			return sprintf('data:image/png;base64,%s', base64_encode($image));
		}

		return with(new Moderator($path, 'public'))->getUrl();
	}
}
