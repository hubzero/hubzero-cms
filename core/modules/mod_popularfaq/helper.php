<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\PopularFaq;

use Hubzero\Module\Module;
use Components\Kb\Models\Archive;
use Component;
use User;

/**
 * Module class for displaying popular KB articles
 */
class Helper extends Module
{
	/**
	 * Get module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		require_once Component::path('com_kb') . '/models/archive.php';

		$a = new Archive();
		$popular = $a->articles()
			->whereIn('access', User::getAuthorisedViewLevels())
			->whereEquals('state', 1)
			->order('helpful', 'desc')
			->limit(intval($this->params->get('limit', 5)))
			->rows();

		$this->cssId    = $this->params->get('cssId');
		$this->cssClass = $this->params->get('cssClass');

		require $this->getLayoutPath();
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
