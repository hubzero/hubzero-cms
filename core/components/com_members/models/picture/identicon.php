<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Picture;

use Hubzero\Image\Identicon as Processor;

/**
 * Identicon User picture
 */
class Identicon
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
	 * @param   string  $email
	 * @param   bool    $thumbnail
	 * @return  string
	 */
	public function picture($email, $thumbnail = true)
	{
		$processor = new Processor();

		$size = $this->pictureSize;
		$file = $this->pictureName;

		if ($thumbnail)
		{
			$size = $this->thumbnailSize;
			$file = $this->thumbnailName;
		}

		$path = $this->path . DIRECTORY_SEPARATOR . $file;

		if (file_exists($path))
		{
			return $path;
		}

		$image = $processor->getImageData($email, $size, $this->color);

		@file_put_contents($path, $image);

		if (!file_exists($path))
		{
			return sprintf('data:image/png;base64,%s', base64_encode($image));
		}

		return $path;
	}
}
