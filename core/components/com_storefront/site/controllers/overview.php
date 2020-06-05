<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Site\Controllers;

use Components\Content\Models\Article;

/**
 * Courses controller class
 */
class Overview extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = Request::getCmd('task', '');

		if (empty($this->_task))
		{
			$this->_task = 'default';
			$this->registerTask('__default', $this->_task);
		}

		parent::execute();
	}

	/**
	 * Display default page
	 *
	 * @return  void
	 */
	public function defaultTask()
	{
		$config = $this->config;

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		$customLandingPage = $this->config->get('landingPage', 0);

		include_once PATH_CORE . DS . 'components' . DS . 'com_content' . DS . 'models' . DS . 'article.php';
		$content = Article::oneOrFail($customLandingPage);

		$this->view->content = $content->toObject();
		$this->view->config = $config;
		$this->view->display();
	}
}
