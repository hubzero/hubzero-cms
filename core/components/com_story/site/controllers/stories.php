<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Story\Models\Manager;
use Components\Story\Models\Story;
use Components\Story\Models\Section;
use Components\Story\Models\Discussion;
use Components\Story\Models\Preference;
use Components\Story\Helpers\Thread;
use Components\Story\Helpers\Context;
use Document;
use Pathway;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * The front page, and a story on its own
 */
class Stories extends SiteController
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
		$this->registerTask('feed', 'feed');
		$this->registerTask('feed.rss', 'feed');

		parent::execute();
	}

	/**
	 * Start the breadcrumbs
	 *
	 * @return  void
	 */
	protected function buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}
	}

	/**
	 * The front page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->buildPathway();

		$filters = array(
			'section_id' => 0,
			'search'     => Request::getString('search', '')
		);

		if ($alias = Request::getString('section', ''))
		{
			$section = Section::oneByAlias($alias);

			if ($section->get('id'))
			{
				$filters['section_id'] = $section->get('id');
				Pathway::append($section->get('title'), 'index.php?option=' . $this->_option . '&section=' . $alias);
			}
		}

		Document::setTitle(isset($section) && $section->get('id') ? $section->get('title') : Lang::txt('COM_STORY'));

		$rows = $this->stories->stories($filters)
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('config', $this->config)
			->display();
	}

	/**
	 * One story, with its prose
	 *
	 * @return  void
	 */
	public function articleTask()
	{
		$this->buildPathway();

		$alias = Request::getString('story', '');
		$on    = array(
			Request::getInt('year', 0),
			Request::getInt('month', 0),
			Request::getInt('day', 0)
		);

		$story = $on[0]
			? $this->stories->story($alias, $on)
			: $this->stories->stories()->whereEquals('alias', $alias)->row();

		if (!$story->get('id'))
		{
			App::abort(404, Lang::txt('COM_STORY_NOT_FOUND'));
		}

		// Counted here rather than in the model: a story is read when it is
		// rendered, not when it is loaded for a listing.
		$story->set('hits', (int) $story->get('hits') + 1);
		$story->save();

		Pathway::append($story->get('title'), $story->link());

		Document::setTitle($story->get('title'));

		$discussion = Discussion::oneOrNew($story->get('discussion_id'));
		$preference = Preference::forUser(User::get('id'));
		$context    = new Context($this->config);

		$this->view
			->set('story', $story)
			->set('text', $story->text())
			->set('discussion', $discussion)
			->set('preference', $preference)
			->set('context', $context)
			->set('thread', $discussion->get('id') ? Thread::build($discussion->get('id'), $preference, $context) : array())
			->set('config', $this->config)
			->setLayout('article')
			->display();
	}

	/**
	 * The feed
	 *
	 * @return  void
	 */
	public function feedTask()
	{
		Document::setType('feed');

		$rows = $this->stories->stories()
			->limit((int) $this->config->get('feed_limit', 20))
			->rows();

		$doc = Document::instance();
		$doc->link = Route::url('index.php?option=' . $this->_option);

		foreach ($rows as $row)
		{
			$text = $row->text();

			$item              = new \Hubzero\Document\Type\Feed\Item();
			$item->title       = $row->get('title');
			$item->link        = Route::url($row->link());
			$item->description = (string) $text->get('intro');
			$item->date        = $row->get('publish_up');
			$item->category    = $row->topic()->get('title');
			$item->author      = '';

			$doc->addItem($item);
		}
	}
}
