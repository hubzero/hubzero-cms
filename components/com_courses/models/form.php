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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

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
	private $fname = NULL;

	/**
	 * Form ID
	 *
	 * @var int
	 **/
	private $id = NULL;

	/**
	 * Count of pages created
	 *
	 * @var int
	 **/
	private $pages = NULL;

	/**
	 * Form title
	 *
	 * @var string
	 **/
	private $title = NULL;

	/**
	 * Form type
	 *
	 * @var string
	 **/
	private $type = NULL;

	/**
	 * Base path for form images
	 *
	 * @var string
	 **/
	private $base = NULL;

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct($id = NULL)
	{
		$this->id = (int)$id;

		// Get courses config
		$config =& JComponentHelper::getParams('com_courses');

		// Build the upload path if it doesn't exist
		$this->base = JPATH_ROOT . DS . trim($config->get('uploadpath', '/site/courses'), DS) . DS . 'forms' . DS;

		// If the direcotry doesn't exist, create it
		if(!is_dir($this->base))
		{
			mkdir($this->base);
		}
	}

	/**
	 * Load by asset id
	 *
	 * @return bool
	 **/
	public function loadByAssetId($asset_id)
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
	 * @return Joomla database object
	 **/
	private static function getDbh()
	{
		static $dbh;

		// If we don't already have one, create it
		if (!$dbh)
		{
			$dbh = JFactory::getDBO();
		}

		return $dbh;
	}

	/**
	 * Get the list of active form questions
	 *
	 * @return Array of active questions
	 **/
	public static function getActiveList()
	{
		$dbh = self::getDbh();

		$dbh->setQuery(
			'SELECT pf.id, title, pf.created, (SELECT MAX(created) FROM #__courses_form_questions WHERE form_id = pf.id) AS updated 
			FROM #__courses_forms pf  
			WHERE title IS NOT NULL AND title != \'\' AND active = 1 
			ORDER BY title'
		);

		return $dbh->loadAssocList();
	}

	/**
	 * Check for archived forms
	 *
	 * @return bool
	 **/
	public static function anyArchived()
	{
		$dbh = self::getDbh();

		$dbh->setQuery('SELECT 1 FROM #__courses_forms WHERE title IS NOT NULL AND title != \'\' AND active = 0');

		return (bool)$dbh->loadResult();
	}

	/**
	 * Verify form was stored
	 *
	 * @return bool
	 **/
	public function isStored()
	{
		// If no ID, return false
		if (!$this->id)
		{
			return false;
		}

		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM #__courses_forms WHERE id = '.$this->id);

		return (bool)$dbh->loadResult();
	}

	/**
	 * Return whether or not there were errors (true or false - not the the errors themselves)
	 *
	 * @return bool
	 **/
	public function hasErrors()
	{
		return (bool)$this->errors;
	}

	/**
	 * Get errors
	 *
	 * @return array
	 **/
	public function getErrors()
	{
		return $this->errors; 
	}

	/**
	 * Process the PDF pages to images
	 *
	 * @return void
	 **/
	public function eachPage($fun)
	{
		if (!$this->id)
		{
			JError::raiseError(422, 'No pages exist for equally nonexistent form');
			return;
		}

		$base   = $this->base . $this->id;
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

		$base = preg_replace('#^'.preg_quote(JPATH_BASE).'#', '', $base);

		$idx = 0;

		foreach ($images as $img)
		{
			$fun($base.'/'.$img, ++$idx);
		}
	}

	/**
	 * Get the form ID, creating it if it doesn't exist
	 *
	 * @return int
	 **/
	public function getId()
	{
		if ($this->id)
		{
			return $this->id;
		}

		$dbh = self::getDbh();
		$dbh->setQuery('INSERT INTO jos_courses_forms() VALUES ()');
		$dbh->query();

		return ($this->id = $dbh->insertid());
	}

	/**
	 * Create images
	 *
	 * @return void
	 **/
	public function renderPageImages()
	{
		try
		{
			$fid = $this->getId();

			mkdir($this->base . $fid);

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
				$im->trimImage(0);

				// Get the image dimensions and x,y trim points
				$imagePage = $im->getImagePage();

				// Reset page proportions to get new width and height
				$im->setImagePage(0, 0, 0, 0);
				$widths[]                     = $im->width;
				$crop[$this->pages]['height'] = $im->height;
				$crop[$this->pages]['x']      = $imagePage['x'];
				$crop[$this->pages]['y']      = $imagePage['y'];
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
				$im->resizeImage(582,0, Imagick::FILTER_MITCHELL, 1);
				$im->sharpenImage(5, 0.5);
				$im->borderImage('white', 15, 15);
				$im->writeImage($this->base . $fid . DS . ($this->pages + 1) . '.png');
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
	 * @return void
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
				case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE:
					$pdf->errors[] = 'Upload failed, the file exceeds the maximum allowable size'; 
				break;
				case UPLOAD_ERR_PARTIAL:
					$pdf->errors[] = 'The upload did not complete. Please try again';
				break;
				case UPLOAD_ERR_NO_FILE:
					$pdf->errors[] = 'Please select a file to upload';
				break;
				case UPLOAD_ERR_NO_TMP_DIR: case UPLOAD_ERR_CANT_WRITE: case UPLOAD_ERR_EXTENSION:
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
	 * @return object
	 **/
	public function setTitle($title)
	{
		$dbh = self::getDbh();
		$dbh->setQuery('UPDATE #__courses_forms SET title = '.$dbh->quote(stripslashes($title)).' WHERE id = '.$this->getId());
		$dbh->query();

		if ($id = $this->getAssetId())
		{
			$dbh->setQuery('UPDATE #__courses_assets SET title = '.$dbh->Quote(stripslashes($title)).' WHERE id = '.$id);
			$dbh->query();
		}

		return $this;
	}

	/**
	 * Set asset id
	 *
	 * @return object
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
	 * @return object
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
	 * @return string
	 **/
	public function getTitle()
	{
		if ($this->title)
		{
			return $this->title;
		}

		static $checked;

		if (!$checked)
		{
			$checked = true;
			$dbh = self::getDbh();
			$dbh->setQuery('SELECT title FROM #__courses_forms WHERE id = '.$this->getId());
			$this->title = $dbh->loadResult();
		}

		return $this->title;
	}

	/**
	 * Save page layout
	 *
	 * @return object
	 **/
	public function setPageLayout($pages)
	{
		$dbh = self::getDbh();
		$fid = $this->getId();
		$dbh->setQuery('SELECT MAX(version) FROM #__courses_form_questions WHERE form_id = '.$fid);
		$version = $dbh->loadResult();
		if (!$version)
		{
			$version = 1;
		}
		else
		{
			++$version;
		}

		foreach ($pages as $pageNum=>$page)
		{
			foreach ($page as $groupNum=>$group)
			{
				$dbh->setQuery('INSERT INTO #__courses_form_questions(form_id, page, top_dist, left_dist, height, width, version) VALUES ('.$fid.', '.((int)$pageNum).', '.((int)$group['top']).', '.((int)$group['left']).', '.((int)$group['height']).', '.((int)$group['width']).', '.$version.')');
				$dbh->query();
				$groupId = $dbh->insertid();
				foreach ($group['answers'] as $answer)
				{
					$dbh->setQuery('INSERT INTO #__courses_form_answers(question_id, top_dist, left_dist, correct) VALUES ('.$groupId.', '.((int)$answer['top']).', '.((int)$answer['left']).', '.($answer['correct'] == 'true' ? 1 : 0).')');
					$dbh->query();
				}
			}
		}

		// Save form type as well
		$this->setAssetType();

		return $this;
	}

	/**
	 * Save asset type
	 *
	 * @return object
	 **/
	public function setAssetType()
	{
		$dbh = self::getDbh();
		$dbh->setQuery('UPDATE `#__courses_assets` SET `subtype` = ' . $dbh->Quote(JRequest::getWord('type', 'quiz')) . ' WHERE id = ' . $this->getAssetId());

		$dbh->query();
	}

	/**
	 * Get asset type
	 *
	 * @return object
	 **/
	public function getAssetType()
	{
		if (isset($this->type))
		{
			return $this->type;
		}
		if (!$asset_id = $this->getAssetId())
		{
			return false;
		}

		$dbh = self::getDbh();
		$dbh->setQuery('SELECT subtype FROM `#__courses_assets` WHERE id = ' . $asset_id);

		$this->type = $dbh->loadResult();

		return $this->type;
	}

	/**
	 * Get page layout (i.e. qustion locations)
	 *
	 * @return array
	 **/
	public function getPageLayout($version = NULL)
	{
		$dbh = self::getDbh();
		$fid = $this->getId();

		if (isset($date) && !is_null($date))
		{
			$date = date('Y-m-d H:i:s', strtotime($date));
		}

		$dbh->setQuery(
			'SELECT pfa.id AS answer_id, question_id, page, correct, pfa.left_dist AS a_left, pfa.top_dist AS a_top, pfq.left_dist AS q_left, pfq.top_dist AS q_top, height, width
			FROM #__courses_form_questions pfq
			LEFT JOIN #__courses_form_answers pfa ON pfa.question_id = pfq.id
			WHERE form_id = '.$fid.' AND version = '.($version ? (int)$version : '(SELECT MAX(version) FROM #__courses_form_questions WHERE form_id = '.$fid.')').'
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

			$rv[$answer['page']][$answer['question_id']]['answers'][] = array('left' => $answer['a_left'], 'top' => $answer['a_top'], 'correct' => (bool)$answer['correct'], 'id' => $answer['answer_id']);
		}

		return $rv;
	}

	/**
	 * Get question to answers map
	 *
	 * @return array
	 **/
	public function getQuestionAnswerMap($answers)
	{
		$dbh = self::getDBH();
		$fid = $this->getId();

		$dbh->setQuery(
			'SELECT pfq.id, pfa.id AS answer_id 
			FROM #__courses_form_questions pfq 
			INNER JOIN #__courses_form_answers pfa ON pfa.question_id = pfq.id AND pfa.correct
			WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__courses_form_questions WHERE form_id = '.$fid.')');

		$rv = array();
		$complete = TRUE;

		foreach ($dbh->loadAssocList() as $row)
		{
			if (isset($answers['question-'.$row['id']]))
			{
				$rv[$row['id']] = array($answers['question-'.$row['id']], $row['answer_id']);
			}
			else
			{
				$rv[$row['id']] = array(NULL, $row['answer_id']);
				$complete = FALSE;
			}
		}

		return array($complete, $rv);
	}

	/**
	 * Count of questions in this form
	 *
	 * @return int
	 **/
	public function getQuestionCount()
	{
		$dbh = self::getDBH();
		$fid = $this->getId();

		$dbh->setQuery(
			'SELECT COUNT(*) FROM #__courses_form_questions pfq WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__courses_form_questions WHERE form_id = '.$fid.') AND (SELECT 1 FROM #__courses_form_answers WHERE question_id = pfq.id LIMIT 1)'
		);

		return $dbh->loadResult();
	}
}