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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use ImagickException;
use Component;
use imagick;

class PdfForm
{
	/**
	 * PDF forms errors
	 *
	 * @var array
	 **/
	private $errors = array();

	/**
	 * File name (from which images are generated)
	 *
	 * @var string
	 **/
	private $fname = null;

	/**
	 * Form ID
	 *
	 * @var int
	 **/
	private $id = null;

	/**
	 * Count of pages created
	 *
	 * @var int
	 **/
	private $pages = null;

	/**
	 * Form title
	 *
	 * @var string
	 **/
	private $title = null;

	/**
	 * Base path for form images
	 *
	 * @var string
	 **/
	private $base = null;

	/**
	 * Constructor
	 *
	 * @param   integer  $id
	 * @return  void
	 **/
	public function __construct($id = null)
	{
		$this->id = (int)$id;

		// Get courses config
		$config = Component::params('com_courses');

		// Build the upload path if it doesn't exist
		$coursesPath = trim($config->get('uploadpath', '/site/courses'), DS);

		// If the direcotry doesn't exist, create it
		if (!is_dir(PATH_APP . DS . $coursesPath))
		{
			mkdir(PATH_APP . DS . $coursesPath);
		}

		$this->base = PATH_APP . DS . $coursesPath . DS . 'forms' . DS;

		// If the direcotry doesn't exist, create it
		if (!is_dir($this->base))
		{
			mkdir($this->base);
		}
	}

	/**
	 * Load by asset id
	 *
	 * @param   integer  $asset_id
	 * @return  bool
	 **/
	public static function loadByAssetId($asset_id)
	{
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT `id` FROM `#__courses_forms` WHERE `asset_id` = ' . $asset_id);

		if ($result = $dbh->loadResult())
		{
			$id = (int) $result;
			return new self($id);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get database handle
	 *
	 * @return  object  database object
	 **/
	private static function getDbh()
	{
		static $dbh;

		// If we don't already have one, create it
		if (!$dbh)
		{
			$dbh = \App::get('db');
		}

		return $dbh;
	}

	/**
	 * Get the list of active form questions
	 *
	 * @return  array  Array of active questions
	 **/
	public static function getActiveList()
	{
		$dbh = self::getDbh();

		$dbh->setQuery(
			'SELECT pf.id, title, pf.created, (SELECT MAX(created) FROM `#__courses_form_questions` WHERE form_id = pf.id) AS updated
			FROM `#__courses_forms` pf
			WHERE title IS NOT null AND title != \'\' AND active = 1
			ORDER BY title'
		);

		return $dbh->loadAssocList();
	}

	/**
	 * Check for archived forms
	 *
	 * @return  bool
	 **/
	public static function anyArchived()
	{
		$dbh = self::getDbh();

		$dbh->setQuery('SELECT 1 FROM `#__courses_forms WHERE` title IS NOT null AND title != \'\' AND active = 0');

		return (bool)$dbh->loadResult();
	}

	/**
	 * Verify form was stored
	 *
	 * @return  bool
	 **/
	public function isStored()
	{
		// If no ID, return false
		if (!$this->id)
		{
			return false;
		}

		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM `#__courses_forms` WHERE id = '.$this->id);

		return (bool)$dbh->loadResult();
	}

	/**
	 * Return whether or not there were errors (true or false - not the the errors themselves)
	 *
	 * @return  bool
	 **/
	public function hasErrors()
	{
		return (bool)$this->errors;
	}

	/**
	 * Get errors
	 *
	 * @return  array
	 **/
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Process the PDF pages to images
	 *
	 * @param   object   $fun      Closure
	 * @param   integer  $version
	 * @return  void
	 **/
	public function eachPage($fun, $version=null)
	{
		if (!$this->id)
		{
			\App::abort(422, 'No pages exist for equally nonexistent form');
			return;
		}

		// Assume default base
		$base = $this->base . $this->id;

		// Get latest version if none provided
		if (!isset($version))
		{
			$dbh = self::getDbh();
			$fid = $this->getId();
			$dbh->setQuery('SELECT MAX(version) FROM `#__courses_form_questions` WHERE form_id = '.$fid);
			$version = $dbh->loadResult();
		}

		$versions = array();
		$dirs     = scandir($base);
		foreach ($dirs as $dir)
		{
			if (is_numeric($dir) && is_dir($base . DS . $dir) && $dir != '.' && $dir != '..')
			{
				$versions[] = $dir;
			}
		}

		// Sort
		natsort($versions);

		// If there's a dir for the given version, just use that
		if (in_array($version, $versions))
		{
			$base = $this->base . $this->id . DS . $version;
			$version_dir = $version;
		}
		else // Otherwise, see if there's a version dir for a previous version
		{
			for ($i=0; $i < count($versions); $i++)
			{
				if ($versions[$i] < $version)
				{
					$version_dir = $versions[$i];
				}
			}
		}

		$dir    = opendir($base);
		$images = array();

		while (($file = readdir($dir)))
		{
			if (preg_match('/^\d+[.]png$/', $file))
			{
				$images[] = $file;
			}
		}

		closedir($dir);
		natsort($images);

		$idx = 0;
		foreach ($images as $img)
		{
			$session_id = \App::get('session')->getId();
			$secret     = \Config::get('secret');
			$token      = hash('sha256', $session_id . ':' . $secret);
			$path       = '/api/courses/form/image?id='.$this->getId().'&file='.$img.'&token='.$token;
			$path      .= (isset($version_dir)) ? '&form_version=' . $version_dir : '';
			$fun($path, ++$idx);
		}
	}

	/**
	 * Get the form ID, creating it if it doesn't exist
	 *
	 * @return  integer
	 **/
	public function getId()
	{
		if ($this->id)
		{
			return $this->id;
		}

		$dbh = self::getDbh();
		$dbh->setQuery('INSERT INTO `#__courses_forms` () VALUES ()');
		$dbh->query();

		return ($this->id = $dbh->insertid());
	}

	/**
	 * Set tmp filename/path
	 *
	 * @return void
	 **/
	public function setFname($name)
	{
		$this->fname = $name;
	}

	/**
	 * Create images
	 *
	 * @return  void
	 **/
	public function renderPageImages()
	{
		try
		{
			$fid = $this->getId();

			// Get version number
			$dbh = self::getDbh();
			$fid = $this->getId();
			$dbh->setQuery('SELECT MAX(version) FROM `#__courses_form_questions` WHERE form_id = '.$fid);
			$version = $dbh->loadResult();

			$path = $this->base . $fid . (($version) ? DS . ($version+1) : '');
			if (!is_dir($path))
			{
				mkdir($path);
			}
			elseif (!$version)
			{
				$files = scandir($path);
				if (is_array($files) && count($files) > 0)
				{
					foreach ($files as $file)
					{
						if ($file != '.' && $file != '..' && is_file($path . DS . $file))
						{
							unlink($path . DS . $file);
						}
					}
				}
			}

			// Get the number of images for our for-loop
			$im = new imagick($this->fname);
			$num = $im->getNumberImages();

			// First we need to loop through the images and see what trim is going to do (i.e. figure out the margins)
			for ($this->pages = 0; $this->pages < $num; ++$this->pages)
			{
				$im = new imagick();
				$im->setResolution(300, 300);
				$im->readImage($this->fname.'['.($this->pages).']');
				$im->setImageFormat('png');
				$im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
				$im->borderImage('white', 10, 10);
				$im->trimImage(0);

				// Get the image dimensions and x,y trim points
				$imagePage = $im->getImagePage();

				// Reset page proportions to get new width and height
				$im->setImagePage(0, 0, 0, 0);
				$widths[]                     = $im->width+20;
				$crop[$this->pages]['height'] = $im->height+20;
				$crop[$this->pages]['x']      = $imagePage['x']-10;
				$crop[$this->pages]['y']      = $imagePage['y']-10;
			}

			// Now actually do the image creation and cropping based on min margin
			for ($this->pages = 0; $this->pages < $num; ++$this->pages)
			{
				$im = new imagick();
				$im->setResolution(300, 300);
				$im->readImage($this->fname.'['.($this->pages).']');
				$im->setImageFormat('png');
				$im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
				$im->cropImage(max($widths), $crop[$this->pages]['height'], $crop[$this->pages]['x'], $crop[$this->pages]['y']);
				$im->borderImage('white', 15, 15);
				// paintTransparentImage() was deprecated at some point in favor of transparentPaintImage()
				if (method_exists($im, 'transparentPaintImage'))
				{
					$im->transparentPaintImage($im->getImagePixelColor(0, 0), 0.0, 0, false);
				}
				else
				{
					$im->paintTransparentImage($im->getImagePixelColor(0, 0), 0.0, 0);
				}
				$im->writeImage($path . DS . ($this->pages + 1) . '.png');
			}
		}
		catch (ImagickException $ex)
		{
			// nothing
		}
	}

	/**
	 * Create PdfForm object from posted file
	 *
	 * @param   string  $name
	 * @return  object
	 **/
	public static function fromPostedFile($name)
	{
		$pdf = new PdfForm;

		if (!isset($_FILES[$name]))
		{
			$pdf->errors[] = 'Upload not posted (server error)';
		}
		else if ((is_array($_FILES[$name]['error']) && $_FILES[$name]['error'][0]) || (!is_array($_FILES[$name]['error']) && $_FILES[$name]['error']))
		{
			switch ($_FILES[$name]['error'])
			{
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$pdf->errors[] = 'Upload failed, the file exceeds the maximum allowable size';
				break;
				case UPLOAD_ERR_PARTIAL:
					$pdf->errors[] = 'The upload did not complete. Please try again';
				break;
				case UPLOAD_ERR_NO_FILE:
					$pdf->errors[] = 'Please select a file to upload';
				break;
				case UPLOAD_ERR_NO_TMP_DIR:
				case UPLOAD_ERR_CANT_WRITE:
				case UPLOAD_ERR_EXTENSION:
					$pdf->errors[] = 'Upload failed due to server configuration';
				break;
				default:
					$pdf->errors[] = 'Upload failed for mysterious reasons';
			}
		}
		else
		{
			$pdf->fname = (is_array($_FILES[$name]['tmp_name'])) ? $_FILES[$name]['tmp_name'][0] : $_FILES[$name]['tmp_name'];
		}

		return $pdf;
	}

	/**
	 * Set form title
	 *
	 * @param   string  $title
	 * @return  object
	 **/
	public function setTitle($title)
	{
		$dbh = self::getDbh();
		$dbh->setQuery('UPDATE `#__courses_forms` SET title = '.$dbh->quote(stripslashes($title)).' WHERE id = '.$this->getId());
		$dbh->query();

		if ($id = $this->getAssetId())
		{
			$dbh->setQuery('UPDATE `#__courses_assets` SET title = '.$dbh->Quote(stripslashes($title)).' WHERE id = '.$id);
			$dbh->query();
		}

		return $this;
	}

	/**
	 * Set asset id
	 *
	 * @param   integer  $asset_id
	 * @return  object
	 **/
	public function setAssetId($asset_id)
	{
		$dbh = self::getDbh();
		$dbh->setQuery('UPDATE `#__courses_forms` SET `asset_id` = ' . $dbh->quote($asset_id) . ' WHERE id = ' . $this->getId());
		$dbh->query();

		return $this;
	}

	/**
	 * Get asset id
	 *
	 * @return  object
	 **/
	public function getAssetId()
	{
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT `asset_id` FROM `#__courses_forms` WHERE id = ' . $this->getId());

		return $dbh->loadResult();
	}

	/**
	 * Get form title
	 *
	 * @return  string
	 **/
	public function getTitle()
	{
		if ($this->title)
		{
			return $this->title;
		}

		$dbh = self::getDbh();
		$dbh->setQuery('SELECT title FROM `#__courses_forms` WHERE id = '.$this->getId());
		$this->title = $dbh->loadResult();

		return $this->title;
	}

	/**
	 * Save page layout
	 *
	 * @param   array  $pages
	 * @return  object
	 **/
	public function setPageLayout($pages)
	{
		$dbh = self::getDbh();
		$fid = $this->getId();
		$dbh->setQuery('SELECT MAX(version) FROM `#__courses_form_questions` WHERE form_id = '.$fid);
		$version = $dbh->loadResult();
		if (!$version)
		{
			$version = 1;
		}
		else
		{
			++$version;
		}

		foreach ($pages as $pageNum => $page)
		{
			foreach ($page as $groupNum => $group)
			{
				$dbh->setQuery('INSERT INTO `#__courses_form_questions` (form_id, page, top_dist, left_dist, height, width, version) VALUES ('.$fid.', '.((int)$pageNum).', '.((int)$group['top']).', '.((int)$group['left']).', '.((int)$group['height']).', '.((int)$group['width']).', '.$version.')');
				$dbh->query();
				$groupId = $dbh->insertid();
				foreach ($group['answers'] as $answer)
				{
					$dbh->setQuery('INSERT INTO `#__courses_form_answers` (question_id, top_dist, left_dist, correct) VALUES ('.$groupId.', '.((int)$answer['top']).', '.((int)$answer['left']).', '.($answer['correct'] == 'true' ? 1 : 0).')');
					$dbh->query();
				}
			}
		}

		return $this;
	}

	/**
	 * Get page layout (i.e. qustion locations)
	 *
	 * @param   integer  $version
	 * @return  array
	 **/
	public function getPageLayout($version = null)
	{
		$dbh = self::getDbh();
		$fid = $this->getId();

		if (isset($date) && !is_null($date))
		{
			$date = date('Y-m-d H:i:s', strtotime($date));
		}

		$dbh->setQuery(
			'SELECT pfa.id AS answer_id, question_id, page, correct, pfa.left_dist AS a_left, pfa.top_dist AS a_top, pfq.left_dist AS q_left, pfq.top_dist AS q_top, height, width
			FROM `#__courses_form_questions` pfq
			LEFT JOIN `#__courses_form_answers` pfa ON pfa.question_id = pfq.id
			WHERE form_id = '.$fid.' AND version = '.($version ? (int)$version : '(SELECT MAX(version) FROM `#__courses_form_questions` WHERE form_id = '.$fid.')').'
			ORDER BY page, question_id'
		);

		$rv = array();

		foreach ($dbh->loadAssocList() as $answer)
		{
			if (!isset($rv[$answer['page']]))
			{
				$rv[$answer['page']] = array();
			}

			if (!isset($rv[$answer['page']][$answer['question_id']]))
			{
				$rv[$answer['page']][$answer['question_id']] = array(
					'left'    => $answer['q_left'],
					'top'     => $answer['q_top'],
					'height'  => $answer['height'],
					'width'   => $answer['width'],
					'answers' => array()
				);
			}

			$rv[$answer['page']][$answer['question_id']]['answers'][] = array(
				'left'    => $answer['a_left'],
				'top'     => $answer['a_top'],
				'correct' => (bool)$answer['correct'],
				'id'      => $answer['answer_id']
			);
		}

		return $rv;
	}

	/**
	 * Get question to answers map
	 *
	 * @param   array  $answers
	 * @param   bool   $ended
	 * @return  array
	 **/
	public function getQuestionAnswerMap($answers, $ended=false)
	{
		$dbh = self::getDBH();
		$fid = $this->getId();

		$dbh->setQuery(
			'SELECT pfq.id, pfa.id AS answer_id
			FROM `#__courses_form_questions` pfq
			INNER JOIN `#__courses_form_answers` pfa ON pfa.question_id = pfq.id AND pfa.correct
			WHERE form_id = ' . $fid . ' AND version = (SELECT max(version) FROM `#__courses_form_questions` WHERE form_id = ' . $fid . ')');

		$rv = array();
		$complete = true;

		foreach ($dbh->loadAssocList() as $row)
		{
			if (isset($answers['question-'.$row['id']]))
			{
				$rv[$row['id']] = array($answers['question-' . $row['id']], $row['answer_id']);
			}
			else
			{
				$rv[$row['id']] = array(null, $row['answer_id']);
				$complete = false;
			}
		}

		if ($ended)
		{
			$complete = true;
		}

		return array($complete, $rv);
	}

	/**
	 * Count of questions in this form
	 *
	 * @return  int
	 **/
	public function getQuestionCount()
	{
		$dbh = self::getDBH();
		$fid = $this->getId();

		$dbh->setQuery(
			'SELECT COUNT(*)
			FROM `#__courses_form_questions` pfq
			WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM `#__courses_form_questions` WHERE form_id = '.$fid.') AND (SELECT 1 FROM `#__courses_form_answers` WHERE question_id = pfq.id LIMIT 1)'
		);

		return $dbh->loadResult();
	}

	/**
	 * Copy existing form and data to new form
	 *
	 * @return  mixed
	 **/
	public function copy()
	{
		$title     = $this->getTitle();
		$questions = $this->getPageLayout();
		$oldId     = $this->getId();

		// Save new
		$this->id = null;
		$id = $this->getId();
		$this->setTitle($title);

		$base = $this->base . $oldId;

		// Copy actual files
		if (is_dir($base))
		{
			// Scan for versions
			$versions = array();
			$dirs     = scandir($base);
			foreach ($dirs as $dir)
			{
				if (is_numeric($dir) && is_dir($base . DS . $dir) && $dir != '.' && $dir != '..')
				{
					$versions[] = $dir;
				}
			}

			if (!empty($versions))
			{
				$base .= DS . max($versions);
			}

			\Filesystem::copyDirectory($base, $this->base . $id);
		}

		// Copy questions
		$this->setPageLayout($questions);

		// Copy deployment (only most recent)
		try
		{
			$dep = PdfFormDeployment::latestFromFormId($oldId);
			$dep->setId(null);
			$dep->setFormId($id);
			$dep->genNewCrumb();
			$dep->save();

			// Return the deployment crumb
			return $dep->getCrumb();
		}
		catch (\Hubzero\Error\Exception\RuntimeException $e)
		{
			// Just return the form id
			return $id;
		}
	}
}
