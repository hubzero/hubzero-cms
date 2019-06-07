<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Resources;

use Hubzero\Module\Module;
use App;

/**
 * Module class for com_resources data
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		if (!App::isAdmin())
		{
			return;
		}

		$database = App::get('db');

		$queries = array(
			'unpublished'   => 0,
			'published'     => 1,
			'draftUser'     => 2,
			'pending'       => 3,
			'removed'       => 4,
			'draftInternal' => 5
		);

		foreach ($queries as $key => $state)
		{
			$database->setQuery("SELECT count(*) FROM `#__resources` WHERE published=$state AND standalone=1");
			$this->$key = $database->loadResult();
		}

		// Get the view
		parent::display();
	}
}
