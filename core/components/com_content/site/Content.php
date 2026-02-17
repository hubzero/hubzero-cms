<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Content extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		// Include dependencies
		require_once dirname(__DIR__) . '/models/article.php';
		require_once __DIR__ . '/helpers/route.php';
		require_once __DIR__ . '/helpers/query.php';
		require_once __DIR__ . '/controllers/articles.php';

		$task = \Request::getCmd('task');
		if ($task) {
		    if (strstr($task, '.')) {
		        $task = explode('.', $task);
		        $task = end($task);
		        \Request::setVar('task', $task);
		    }
		} else {
		    \Request::setVar('task', \Request::getCmd('view', 'article'));
		}

		$controller = new Controllers\Articles();
		$controller->execute();
	}
}
