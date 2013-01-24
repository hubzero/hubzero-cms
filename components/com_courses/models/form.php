<?php

class PdfForm
{
	private $errors = array(), $fname = NULL, $id = NULL, $pages = NULL, $title = NULL;

	private static function getDbh() {
		static $dbh;
		if (!$dbh) {
			$dbh = JFactory::getDBO();
		}
		return $dbh;
	}

	public static function getActiveList() {
		$dbh = self::getDbh();
		$dbh->setQuery(
			'SELECT pf.id, title, pf.created, (SELECT MAX(created) FROM #__courses_form_questions WHERE form_id = pf.id) AS updated 
			FROM #__courses_forms pf  
			WHERE title IS NOT NULL AND title != \'\' AND active = 1 
			ORDER BY title'
		);
		return $dbh->loadAssocList();
	}

	public static function anyArchived() {
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM #__courses_forms WHERE title IS NOT NULL AND title != \'\' AND active = 0');
		return (bool)$dbh->loadResult();
	}

	public function __construct($id = NULL) {
		$this->id = (int)$id;
	}

	public function isStored() {
		if (!$this->id) {
			return false;
		}
		$dbh = self::getDbh();
		$dbh->setQuery('SELECT 1 FROM #__courses_forms WHERE id = '.$this->id);
		return (bool)$dbh->loadResult();
	}

	public function hasErrors() {
		return (bool)$this->errors;
	}
	
	public function eachPage($fun) {
		if (!$this->id) {
			throw new UnprocessableEntityError('No pages exist for equally nonexistent form');
		}
		// @FIXME: create form base param
		$base = '/www/myhub/site/courses/forms/'.$this->id;
		$dir = opendir($base);
		$images = array();
		while (($file = readdir($dir))) {
			if (preg_match('/^\d+[.]png$/', $file)) {
				$images[] = $file;
			}
		}
		closedir($dir);
		natsort($images);
		$base = preg_replace('#^'.preg_quote(JPATH_BASE).'#', '', $base);
		$idx = 0;
		foreach ($images as $img) {
			$fun($base.'/'.$img, ++$idx);
		}
	}

	public function getErrors() { 
		return $this->errors; 
	}

	public function getId() {
		if ($this->id) {
			return $this->id;
		}
		$dbh = self::getDbh();
		$dbh->execute('INSERT INTO jos_courses_forms() VALUES ()');
		return ($this->id = $dbh->insertid());
	}

	public function renderPageImages() {
		try {
			$fid = $this->getId();
			// @FIXME: clean up folder creation
			mkdir('/www/myhub/site/courses/forms/'.$fid);
			for ($this->pages = 1; ; ++$this->pages) {
				$im = new imagick($this->fname.'['.($this->pages - 1).']');
				$im->setImageFormat('png');
				file_put_contents('/www/myhub/site/courses/forms/'.$fid.'/'.$this->pages.'.png', (string)$im);
			}
		}
		catch (ImagickException $ex) {
		}
	}

	public static function fromPostedFile($name) {
		$pdf = new PdfForm;
		if (!isset($_FILES[$name])) {
			$pdf->errors[] = 'Upload not posted (server error)';
		}
		else if ($_FILES[$name]['error'][0]) {
			switch ($_FILES[$name]['error']) {
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
		else {
			$pdf->fname = $_FILES[$name]['tmp_name'][0];
		}
		return $pdf;
	}

	public function setTitle($title) {
		$dbh = self::getDbh();
		$dbh->execute('UPDATE #__courses_forms SET title = '.$dbh->quote(stripslashes($title)).' WHERE id = '.$this->getId());
		return $this;
	}

	public function getTitle() {
		if ($this->title) {
			return $this->title;
		}
		static $checked;
		if (!$checked) {
			$checked = true;
			$dbh = self::getDbh();
			$dbh->setQuery('SELECT title FROM #__courses_forms WHERE id = '.$this->getId());
			$this->title = $dbh->loadResult();
		}
		return $this->title;
	}

	public function setPageLayout($pages) {
		$dbh = self::getDbh();
		$fid = $this->getId();
		$dbh->setQuery('SELECT MAX(version) FROM #__courses_form_questions WHERE form_id = '.$fid);
		$version = $dbh->loadResult();
		if (!$version) {
			$version = 1;
		}
		else {
			++$version;
		}
		foreach ($pages as $pageNum=>$page) {
			foreach ($page as $groupNum=>$group) {
				$dbh->execute('INSERT INTO #__courses_form_questions(form_id, page, top_dist, left_dist, height, width, version) VALUES ('.$fid.', '.((int)$pageNum).', '.((int)$group['top']).', '.((int)$group['left']).', '.((int)$group['height']).', '.((int)$group['width']).', '.$version.')');
				$groupId = $dbh->insertid();
				foreach ($group['answers'] as $answer) {
					$dbh->execute('INSERT INTO #__courses_form_answers(question_id, top_dist, left_dist, correct) VALUES ('.$groupId.', '.((int)$answer['top']).', '.((int)$answer['left']).', '.($answer['correct'] == 'true' ? 1 : 0).')');
				}
			}
		}
		return $this;
	}

	public function getPageLayout($version = NULL) {
		$dbh = self::getDbh();
		$fid = $this->getId();
		if (isset($date) && !is_null($date)) {
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
		foreach ($dbh->loadAssocList() as $answer) {
			if (!isset($rv[$answer['page']])) {
				$rv[$answer['page']] = array();
			}
			if (!isset($rv[$answer['page']][$answer['question_id']])) {
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

	public function getQuestionAnswerMap($answers) {
		$dbh = self::getDBH();
		$fid = $this->getId();
		$dbh->setQuery(
			'SELECT pfq.id, pfa.id AS answer_id 
			FROM #__courses_form_questions pfq 
			INNER JOIN #__courses_form_answers pfa ON pfa.question_id = pfq.id AND pfa.correct
			WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__courses_form_questions WHERE form_id = '.$fid.')');
		$rv = array();
		$complete = TRUE;
		foreach ($dbh->loadAssocList() as $row) {
			if (isset($answers['question-'.$row['id']])) {
				$rv[$row['id']] = array($answers['question-'.$row['id']], $row['answer_id']);
			}
			else {
				$rv[$row['id']] = array(NULL, $row['answer_id']);
				$complete = FALSE;
			}
		}
		return array($complete, $rv);
	}

	public function getQuestionCount() {
		$dbh = self::getDBH();
		$fid = $this->getId();
		$dbh->setQuery(
			'SELECT COUNT(*) FROM #__courses_form_questions pfq WHERE form_id = '.$fid.' AND version = (SELECT max(version) FROM #__courses_form_questions WHERE form_id = '.$fid.') AND (SELECT 1 FROM #__courses_form_answers WHERE question_id = pfq.id LIMIT 1)'
		);
		return $dbh->loadResult();
	}
}