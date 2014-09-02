<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'certificate.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

/**
 * Courses model class for a certificate
 */
class CoursesModelCertificate extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableCertificate';

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
		$this->_db = \JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . \JText::_('Table class must be an instance of JTable.')
				);
				throw new \LogicException(\JText::_('Table class must be an instance of JTable.'));
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
	 * @return  object CoursesModelCertificate
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
		try
		{
			if (!$this->exists())
			{
				$this->setError(JText::_('No pages exist for nonexistent certificate.'));
				return false;
			}

			$base = $this->path('system');

			if (!file_exists($base))
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::create($base))
				{
					$this->setError(JText::_('Unable to create directory.'));
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
			JError::raiseError(422, 'No pages exist for nonexistent certificate.');
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
		$token = hash('sha256', JFactory::getSession()->getId() . ':' . JFactory::getConfig()->getValue('secret'));

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
				return JPATH_ROOT . $this->_base;
			break;

			case 'web':
				return str_replace('administrator', '', trim(JURI::base(true), '/')) . $this->_base;
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
				$this->_properties = new stdClass;
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
	 * @param   object  $user JUser
	 * @param   string  $path Path to store rendered file to
	 * @return  boolean True on success, false on error
	 */
	public function render($user=null, $path=null)
	{
		if (!$user)
		{
			$user = JFactory::getUser();
		}

		if (!class_exists('CoursesModelCourse'))
		{
			require_once(__DIR__ . DS . 'course.php');
		}
		$course = CoursesModelCourse::getInstance($this->get('course_id'));

		require_once(JPATH_ROOT . DS . 'libraries' . DS . 'fpdf16' . DS . 'fpdf.php');
		require_once(JPATH_ROOT . DS . 'libraries' . DS . 'fpdi' . DS . 'fpdi.php');

		// Get the pdf and draw on top of it
		$pdf = new FPDI();

		$pageCount = $pdf->setSourceFile($this->path('system') . DS . 'certificate.pdf');
		$tplIdx = $pdf->importPage(1);

		$size = $pdf->getTemplateSize($tplIdx);

		$pdf->AddPage('L', array($size['h'], $size['w']));

		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

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
					$val = JFactory::getDate()->format(JText::_('d M Y'));
				break;
			}

			$pdf->SetFont('Arial', '', 30); //($element->h * $size['h']));

			$pdf->setXY($element->x * $size['w'], $element->y * $size['h']); //  - ($element->h * $size['h'])
			$pdf->Cell($element->w * $size['w'], ($element->h * $size['h']), $val,'',1, 'C');
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
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path))
			{
				$this->setError(JText::_('Unable to remove upload directory and files for certificate.'));
				return false;
			}
		}

		// Remove this record from the database and log the event
		return parent::delete();
	}
}