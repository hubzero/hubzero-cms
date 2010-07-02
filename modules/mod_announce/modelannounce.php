<?php
jimport('joomla.application.component.model');

class ModelAnnouncements extends JModel
{
	private $min, $max, $terms = '', $current_id;

	public function set_current($id) { $this->current_id = intval($id); }
	public function set_search_terms($terms) { $this->terms = $terms; }
	public function get_search_terms() { return $this->terms; }
	public function set_minimum($min) { $this->min = intval($min); }
	public function set_maximum($max) { $this->max = intval($max); }

	private static function get_id_list($ids)
	{
		if (!is_array($ids))
			throw new Exception('remove_in expected an array of ids');
		return join(', ', array_map('intval', $ids));
	}

	public function remove_in($ids)
	{
		$this->_db->execute(
			'DELETE FROM #__announcements WHERE id IN ('.self::get_id_list($ids).')'
		);
	}

	public function archive_in($ids)
	{
		$this->_db->execute(
			'UPDATE #__announcements SET expiration_date = CURRENT_DATE WHERE id IN ('.self::get_id_list($ids).')'
		);
	}

	public function unarchive_in($ids)
	{
		$this->_db->execute(
			'UPDATE #__announcements SET expiration_date = NULL WHERE id IN ('.self::get_id_list($ids).')'
		);
	}

	public function get_published()
	{
		$this->_db->setQuery(
			'SELECT id, link, title, category, active_date FROM #__announcements a1 WHERE active_date <= CURRENT_TIMESTAMP AND (expiration_date IS NULL OR expiration_date > CURRENT_TIMESTAMP)
			ORDER BY (SELECT unix_timestamp(MAX(active_date)) + a2.id FROM jos_announcements a2 WHERE a1.category = a2.category) DESC, active_date DESC
			LIMIT '.$this->max
		);
		$rows = $this->_db->loadAssocList();
		if (($nr = count($rows)) < $this->min)
		{
			$this->_db->setQuery(
				'SELECT id, link, title, category, active_date FROM #__announcements a1 WHERE active_date <= CURRENT_TIMESTAMP AND expiration_date IS NOT NULL AND expiration_date <= CURRENT_TIMESTAMP 
				ORDER BY (SELECT unix_timestamp(MAX(active_date)) + a2.id FROM jos_announcements a2 WHERE a1.category = a2.category) DESC, active_date DESC
				LIMIT '.($this->min - $nr)
			);
			$rows = array_merge($rows, $this->_db->loadAssocList());
			$this->_db->setQuery(
				'SELECT category FROM #__announcements GROUP BY category ORDER BY MAX(active_date) DESC'
			);
			$this->category_priority = array_flip($this->_db->loadResultArray());
			
			usort($rows, array($this, 'sort_category_active_desc'));
		}
		return $rows;
	}

	private function sort_category_active_desc($a, $b)
	{
		if ($a['category'] != $b['category'])
			return $this->category_priority[$a['category']] < $this->category_priority[$b['category']] ? -1 : 1;
		$a_t = strtotime($a['active_date']);
		$b_t = strtotime($b['active_date']);
		return $a_t == $b_t ? 0 : $a_t < $b_t ? 1 : -1;
	}

	public function get_archived()
	{
		$this->_db->setQuery(
			'SELECT id, link, title, category, active_date FROM #__announcements 
			WHERE active_date <= CURRENT_TIMESTAMP AND expiration_date IS NOT NULL AND expiration_date <= CURRENT_TIMESTAMP '.($this->terms ? 'AND title LIKE \'%'.mysql_real_escape_string($this->terms).'%\'' : '' ).'
			ORDER BY category, active_date'
		);
		return $this->_db->loadAssocList();
	}

	public function get_all()
	{
		$this->_db->setQuery(
			'SELECT id, link, title, category, active_date, expiration_date,
				CASE 
					WHEN active_date <= CURRENT_TIMESTAMP AND (expiration_date IS NULL OR expiration_date > CURRENT_TIMESTAMP)
						THEN \'Active\'
					WHEN active_date <= CURRENT_TIMESTAMP AND expiration_date IS NOT NULL AND expiration_date <= CURRENT_TIMESTAMP
						THEN \'Archived\'
					ELSE
						\'Pending\'
				END AS state
			FROM #__announcements
			'.($this->terms ? ' WHERE title like \'%'.mysql_real_escape_string($this->terms).'%\'' : '').'
			ORDER BY active_date DESC, title'
		);
		return $this->_db->loadAssocList();
	}

	public function get_current()
	{
		if (!$this->current_id)
			throw new Exception('No current announcement set');
		$this->_db->setQuery('SELECT id, link, title, category, active_date, expiration_date FROM #__announcements WHERE id = '.$this->current_id);
		return $this->_db->loadAssoc();
	}
}

class TableAnnouncements extends JTable
{
	public $id;
	public $title;
	public $link;
	public $category;
	public $active_date;
	public $expiration_date;

	public function __construct()
	{
		parent::__construct('#__announcements', 'id', JFactory::getDBO());
	}

	public function check()
	{
		if (trim($this->title) == '' || trim($this->category) == '') 
		{
			$this->setError('You must enter both a title and a category');
			return false;
		}
		return true;
	}

	public function store()
	{
		if (!$this->id)
		{
			$this->_db->execute(
				'INSERT INTO #__announcements(title, category, link, active_date, expiration_date) VALUES('.
					$this->_db->quote($this->title).', '.
					$this->_db->quote($this->category).', '.
					$this->_db->quote($this->link).', '.
					($this->active_date ? 'DATE(\''.date('Y/m/d', strtotime($this->active_date)).'\')' : 'CURRENT_DATE').', '.
					($this->expiration_date ? 'DATE(\''.date('Y/m/d', strtotime($this->expiration_date)).'\')' : 'NULL').
				')'
			);
		}
		else
		{
			$this->_db->execute(
				'UPDATE #__announcements SET title = '.$this->_db->quote($this->title).', category = '.$this->_db->quote($this->category).', link = '.$this->_db->quote($this->link).
					', active_date = '.($this->active_date ? 'DATE(\''.date('Y/m/d', strtotime($this->active_date)).'\')' : 'CURRENT_DATE').
					', expiration_date = '.($this->expiration_date ? 'DATE(\''.date('Y/m/d', strtotime($this->expiration_date)).'\')' : 'NULL').'
				WHERE id = '.intval($this->id)
			);
		}
		return true;
	}
}
