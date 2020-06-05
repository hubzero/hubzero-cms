<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Hubzero\Database\Table;
use ImagickException;
use Imagick;
use Lang;

require_once dirname(__DIR__) . DS . 'tables' . DS . 'certificate.php';
require_once __DIR__ . DS . 'base.php';

/**
 * Courses model class for a certificate
 */
class Certificate extends Base
{
	/**
	 * Table class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Certificate';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'certificate';

	/**
	 * Properties object
	 * 
	 * @var string
	 */
	protected $_properties = null;

	/**
	 * Base file path
	 * 
	 * @var string
	 */
	protected $_base = null;

	/**
	 * Constructor
	 * 
	 * @param   mixed $oid Integer (ID), string (alias), object or array
	 * @return  void
	 */
	public function __construct($oid=null, $course_id=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof Table))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of Hubzero\\Database\\Table.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of Hubzero\\Database\\Table.'));
			}

			if ($course_id)
			{
				$this->_tbl->load(array('course_id' => $course_id));
			}
			else if (is_numeric($oid) || is_string($oid))
			{
				// Make sure $oid isn't empty
				// This saves a database call
				if ($oid)
				{
					$this->_tbl->load($oid);
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a certificate model
	 *
	 * @param   mixed  $oid ID (int) or alias (string)
	 * @return  object \Components\Courses\Models\Certificate
	 */
	static function &getInstance($oid=0, $course_id=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid . $course_id]))
		{
			$instances[$oid . $course_id] = new self($oid, $course_id);
		}

		return $instances[$oid . $course_id];
	}

	/**
	 * Check if the certificate has the needed file
	 *
	 * @return  boolean
	 */
	public function hasFile()
	{
		$path = $this->path('system');

		if (file_exists($path))
		{
			if (file_exists($path . DS . $this->get('filename', 'certificate.pdf')))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Create images
	 *
	 * @return void
	 */
	public function renderPageImages()
	{
		if (!class_exists('Imagick'))
		{
			// nothing
			$this->setError(Lang::txt('Imagick extension required.'));
			return false;
		}

		try
		{
			if (!$this->exists())
			{
				$this->setError(Lang::txt('No pages exist for nonexistent certificate.'));
				return false;
			}

			$base = $this->path('system');

			if (!file_exists($base))
			{
				if (!\Filesystem::makeDirectory($base))
				{
					$this->setError(Lang::txt('Unable to create directory.'));
					return false;
				}
			}

			$fname = $base . DS . $this->get('filename', 'certificate.pdf');

			// Get the number of images for our for-loop
			$im = new Imagick($fname);
			$num = $im->getNumberImages();

			// Now actually do the image creation and cropping based on min margin
			for ($pages = 0; $pages < $num; ++$pages)
			{
				$im = new Imagick();
				$im->setResolution(300, 300);
				$im->readImage($fname . '[' . ($pages) . ']');
				$im->setImageFormat('png');
				$im->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);

				$im->writeImage($base . DS . ($pages + 1) . '.png');
			}

			return true;
		}
		catch (ImagickException $ex)
		{
			// nothing
			$this->setError($ex->getMessage());
			return false;
		}
	}

	/**
	 * Process the PDF pages to images
	 *
	 * @param   object $fun Closure
	 * @return  void
	 */
	public function eachPage($fun)
	{
		if (!$this->exists())
		{
			\App::abort(422, 'No pages exist for nonexistent certificate.');
			return;
		}

		$base   = $this->path('system');
		$dir    = opendir($base);
		$images = array();

		while ($file = readdir($dir))
		{
			if (preg_match('/^\d+[.]png$/', $file))
			{
				$images[] = $file;
			}
		}

		closedir($dir);
		natsort($images);

		$base  = $this->path('web');
		$token = hash('sha256', \App::get('session')->getId() . ':' . \Config::get('secret'));

		$idx = 0;
		foreach ($images as $img)
		{
			$path = $base . DS . $img . '?token=' . $token;

			$fun($path, ++$idx);
		}
	}

	/**
	 * Build the path to the certificate
	 *
	 * @param   string $type Path type to return
	 * @return  string
	 */
	public function path($type='')
	{
		if (!$this->_base)
		{
			$this->_base = DS . trim($this->config('uploadpath', '/site/courses'), DS) . DS . $this->get('course_id') . DS . 'certificates' . DS . $this->get('id');
		}

		switch ($type)
		{
			case 'sys':
			case 'system':
			case 'upload':
				return PATH_APP . $this->_base;
			break;

			case 'web':
				$base = str_replace('administrator', '', trim(\Request::base(true), '/'));
				return rtrim($base, '/') . substr(PATH_APP, strlen(PATH_ROOT)) . $this->_base;
			break;

			default:
				return $this->_base;
			break;
		}
	}

	/**
	 * Get entry properties
	 * 
	 * Properties are stored as en encoded string. This retrieves the 
	 * string and decodes it or creates an object with base values if
	 * no stored value is found.
	 * 
	 * @return  object
	 */
	public function properties()
	{
		if (!$this->_properties)
		{
			$this->_properties = json_decode($this->get('properties', '{width:900,height:694,elements:[]}'));
			if (!$this->_properties)
			{
				$this->_properties = new \stdClass;
				$this->_properties->width    = 900;
				$this->_properties->height   = 694;
				$this->_properties->elements = array();
			}
		}
		return $this->_properties;
	}

	/**
	 * Render a certificate
	 * 
	 * @param   object  $user  User
	 * @param   string  $path  Path to store rendered file to
	 * @return  boolean True on success, false on error
	 */
	public function render($user=null, $path=null)
	{
		if (!$user)
		{
			$user = \User::getInstance();
		}

		if (!class_exists('\Components\Courses\Models\Course'))
		{
			require_once __DIR__ . DS . 'course.php';
		}
		$course = Course::getInstance($this->get('course_id'));

		require_once __DIR__ . DS . 'certificatepdf.php';

		$img = $this->path('system') . '/1.png';

		list($width, $height) = getimagesize($img);

		// 300 dots per inch
		// 1 inch = 25.4mm
		$w = ($width / 300);
		$h = ($height / 300);
		$mm = 25.4;
		$size = array(
			'width' => ($w * $mm),
			'height' => ($h * $mm)
		);

		$pdf = new CertificatePdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $size, true, 'UTF-8', false);
		$pdf->img_file = $img;

		$pdf->AddPage('L', array($size['height'], $size['width']));

		$pdf->SetFillColor(0, 0, 0);

		foreach ($this->properties()->elements as $element)
		{
			// Convert pixel values to percents
			$element->x = $element->x / $this->properties()->width;
			$element->y = $element->y / $this->properties()->height;

			$element->w = $element->w / $this->properties()->width;
			$element->h = $element->h / $this->properties()->height;

			$val = '';
			switch ($element->id)
			{
				case 'name':
				case 'email':
				case 'username':
					$val = $user->get($element->id);
				break;

				case 'course':
					$val = $course->get('title');
				break;

				case 'offering':
					$val = $course->offering()->get('title');
				break;

				case 'section':
					$val = $course->offering()->section()->get('title');
				break;

				case 'date':
					$val = \Date::of('now')->format(Lang::txt('d M Y'));
				break;
			}

			$pdf->SetFont('Helvetica', '', 30);

			$pdf->SetXY($element->x * $size['width'], $element->y * $size['height']);
			$pdf->Cell($element->w * $size['width'], ($element->h * $size['height']), $val, '', 1, 'C');
		}

		if (!$path)
		{
			$pdf->Output();
			die;
		}

		$pdf->Output($path, 'F');

		return true;
	}

	/**
	 * Store record in the database
	 *
	 * @param   boolean $check Perform data validation?
	 * @return  boolean
	 */
	public function store($check=true)
	{
		if (is_object($this->get('properties')))
		{
			$this->set('properties', json_encode($this->get('properties')));
		}

		return parent::store($check);
	}

	/**
	 * Delete a certificate
	 * 
	 * @return  boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove files
		$path = $this->path('system');
		if (is_dir($path))
		{
			// Attempt to delete the file
			if (!\Filesystem::deleteDirectory($path))
			{
				$this->setError(Lang::txt('Unable to remove upload directory and files for certificate.'));
				return false;
			}
		}

		// Remove this record from the database and log the event
		return parent::delete();
	}
}
