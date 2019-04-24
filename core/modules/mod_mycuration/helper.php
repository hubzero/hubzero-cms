<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyCuration;

use Hubzero\Module\Module;
use Publication;
use Component;

/**
 * Module class for displaying a user's publication curation tasks
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$database = \App::get('db');
		$config   = Component::params('com_publications');

		// Get some classes we need
		require_once Component::path('com_publications') . '/tables/publication.php';
		require_once Component::path('com_publications') . '/tables/master.type.php';

		$this->moduleclass = $this->params->get('moduleclass');

		// Build query
		$filters = array(
			'limit'         => intval($this->params->get('limit', 10)),
			'start'         => 0,
			'sortby'        => 'title',
			'sortdir'       => 'ASC',
			'ignore_access' => 1,
			'curator'       => 'owner',
			'dev'           => 1, // get dev versions
			'status'        => array(5, 7) // submitted/pending
		);

		// Instantiate
		$objP = new \Components\Publications\Tables\Publication($database);

		// Assigned curation
		$this->rows = $objP->getRecords($filters);

		require $this->getLayoutPath();
	}
}
