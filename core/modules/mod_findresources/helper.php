<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\FindResources;

use Hubzero\Module\Module;
use Components\Tags\Models\Tag;
use Component;

/**
 * Module class for displaying ways to find resources
 */
class Helper extends Module
{
	/**
	 * Generate module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		require_once Component::path('com_tags') . '/models/cloud.php';
		require_once Component::path('com_resources') . '/models/type.php';

		$this->tags = Tag::all()
			->whereEquals('admin', 0)
			->limit((int)$this->params->get('limit', 25))
			->order('objects', 'desc')
			->rows();

		// Get major types
		$this->categories = \Components\Resources\Models\Type::getMajorTypes();

		require $this->getLayoutPath();
	}

	/**
	 * Display module contents
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
