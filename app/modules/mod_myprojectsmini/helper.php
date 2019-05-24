<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyProjectsMini;

use Hubzero\Module\Module;
use Components\Projects\Tables\Project;
use Component;
use User;

/**
 * Module class for displaying a user's projects
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
		$db = \App::get('db');

		// Get the module parameters
		$params = $this->params;
		$this->moduleclass = $params->get('moduleclass', '');
		$limit = intval($params->get('limit', 5));

		// Load component configs
		$config = Component::params('com_projects');

		// Load classes
		require_once Component::path('com_projects') . DS . 'tables' . DS . 'project.php';
		require_once Component::path('com_projects') . DS . 'helpers' . DS . 'html.php';

		// Set filters
		$filters = array(
			'mine'     => 1,
			'start'    => 0,
			'updates'  => 1,
			'sortby'   => 'myprojects',
			'getowner' => 1
		);

		if (!$this->params->get('include_archived', 1))
		{
			$filters['filterby'] = 'active';
		}

		$setup_complete = $config->get('confirm_step', 0) ? 3 : 2;
		$this->filters  = $filters;
		$this->pconfig  = $config;

		// Get a record count
		$obj = new Project($db);
		$this->total = $obj->getCount($filters, false, User::get('id'), 0, $setup_complete);

		// Get records
		$this->rows = $obj->getRecords($filters, false, User::get('id'), 0, $setup_complete);
		
    $projctsorted = $this->rows;
		foreach ($projctsorted as $project) {
			$query = "SELECT lastvisit FROM `#__project_owners` WHERE userid = " . User::get('id') . " AND projectid= " . $project->id;
			$db->setQuery($query);
			$lastvisit= $db->loadResult();
			$project->lastvisit = $lastvisit;
		}

    uasort($projctsorted, function($first, $second) {
    	return $first->lastvisit < $second->lastvisit;
    });
		
    // pass limit to view
    $this->limit = $limit;
		require $this->getLayoutPath();
	}
}
