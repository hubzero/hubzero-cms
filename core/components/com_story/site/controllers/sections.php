<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Story\Models\Manager;
use Components\Story\Models\Section;
use Document;
use Pathway;
use Request;
use Lang;
use App;

/**
 * A section's own front page
 */
class Sections extends SiteController
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
	 * List a section's stories, or every section
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}

		$alias   = Request::getString('section', '');
		$section = null;
		$rows    = null;

		if ($alias)
		{
			$section = Section::oneByAlias($alias);

			if (!$section->get('id') || !$section->isPublished())
			{
				App::abort(404, Lang::txt('COM_STORY_SECTION_NOT_FOUND'));
			}

			Pathway::append($section->get('title'), 'index.php?option=' . $this->_option . '&view=sections&section=' . $alias);

			$rows = $this->stories->stories(array('section_id' => $section->get('id')))
				->paginated('limitstart', 'limit')
				->rows();
		}

		Document::setTitle($section->get('id') ? $section->get('title') : Lang::txt('COM_STORY_SECTIONS'));

		$this->view
			->set('section', $section)
			->set('sections', $this->stories->sections()->rows())
			->set('rows', $rows)
			->set('config', $this->config)
			->display();
	}
}
