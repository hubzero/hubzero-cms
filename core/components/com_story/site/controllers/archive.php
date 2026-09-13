<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Story\Models\Manager;
use Document;
use Pathway;
use Request;
use Lang;

/**
 * What ran on a given day, month or year
 *
 * No routes of its own: a dated address with nothing after the date is an
 * archive, and one with a slug after it is a story. Both fall out of the
 * same parse.
 */
class Archive extends SiteController
{
	/**
	 * Stories in this scope
	 *
	 * @var  object
	 */
	protected $stories = null;

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->stories = new Manager('site', 0);

		$this->registerTask('__default', 'display');

		parent::execute();
	}

	/**
	 * List what ran in the period
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}

		$filters = array(
			'year'  => Request::getInt('year', 0),
			'month' => Request::getInt('month', 0),
			'day'   => Request::getInt('day', 0)
		);

		$label = $this->label($filters);

		Pathway::append($label, 'index.php?option=' . $this->_option . '&view=archive&year=' . $filters['year']);

		Document::setTitle($filters['year'] ? Lang::txt('COM_STORY_ARCHIVE_FOR', $label) : Lang::txt('COM_STORY_ARCHIVE'));

		$rows = $this->stories->stories($filters)
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('label', $label)
			->set('config', $this->config)
			->display();
	}

	/**
	 * Say what period this is, as a reader would
	 *
	 * @param   array  $filters
	 * @return  string
	 */
	protected function label(array $filters)
	{
		if (!$filters['year'])
		{
			return Lang::txt('COM_STORY_ARCHIVE');
		}

		$parts = array($filters['year']);

		if ($filters['month'])
		{
			$parts[] = str_pad($filters['month'], 2, '0', STR_PAD_LEFT);
		}

		if ($filters['day'])
		{
			$parts[] = str_pad($filters['day'], 2, '0', STR_PAD_LEFT);
		}

		return implode('-', $parts);
	}
}
