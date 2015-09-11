<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for resources
 */
class plgCronResources extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = 'resources';

		$obj->events = array(
			array(
				'name'   => 'issueResourceMasterDoi',
				'label'  => Lang::txt('PLG_CRON_RESOURCES_MASTER_DOI'),
				'params' => ''
			)/*,
			array(
				'name'   => 'updateResourceRanking',
				'label'  => Lang::txt('PLG_CRON_RESOURCES_RANKING'),
				'params' => 'ranking'
			)*/
		);

		return $obj;
	}

	/**
	 * Issue master DOI for tool resources if does not exist
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function issueResourceMasterDoi(\Components\Cron\Models\Job $job)
	{
		$database = App::get('db');
		$config   = Component::params('com_publications');

		// Is config to issue master DOI turned ON?
		if (!$config->get('master_doi'))
		{
			return true;
		}

		// Get all tool resources without master DOI
		$sql = "SELECT r.id, r.created_by, v.id as tool_version_id,
				v.toolid, v.toolname, v.title, v.description,
				v.instance, v.revision, v.released
				FROM #__resources AS r, #__tool_version AS v
				WHERE r.published=1
				AND r.type=7
				AND r.standalone=1
				AND r.alias=v.toolname
				AND v.state=1
				AND (r.master_doi IS NULL OR r.master_doi=0)
				GROUP BY r.id
				ORDER BY v.title, v.toolname, v.revision DESC";

		$database->setQuery( $sql );
		if (!($rows = $database->loadObjectList()))
		{
			// No applicable results
			return true;
		}

		// Includes
		require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'doi.php');

		// Get DOI service
		$doiService = new \Components\Publications\Models\Doi();

		// Is service enabled?
		if (!$doiService->on() || !$doiService->_configs->livesite)
		{
			return true;
		}

		// Go through records
		foreach ($rows as $row)
		{
			// Reset metadata
			$doiService->reset();

			// Map data
			$pubYear = $row->released && $row->released != '0000-00-00 00:00:00'
					? gmdate('Y', strtotime($row->released)) : gmdate('Y');
			$doiService->set('pubYear', $pubYear);
			$doiService->mapUser($row->created_by, array(), 'creator');
			$doiService->set('resourceType', 'Software');
			$doiService->set('title', htmlspecialchars(stripslashes($row->title)));
			$doiService->set('url', $doiService->_configs->livesite . DS . 'resources' . DS . $row->toolname . DS . 'main');

			// Register DOI
			$masterDoi = $doiService->register();

			// Save with publication record
			$resource = new \Components\Resources\Tables\Resource($database);
			if ($masterDoi && $resource->load($row->id))
			{
				$resource->master_doi = strtoupper($masterDoi);
				$resource->store();
			}
		}

		return true;
	}

	/**
	 * Issue master DOI for tool resources if does not exist
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function updateResourceRanking(\Components\Cron\Models\Job $job)
	{
		$processed = array();

		$params = $job->get('params');
		$limit  = $params->get('resource_limit', 100);

		if (!is_numeric($limit) || $limit <= 0 || $limit > 1000)
		{
			$limit = 100;
		}

		switch (intval($params->get('resource_frequency', 7)))
		{
			case 7:
				// Once a week, start of the week
				$d = new \DateTime('-' . gmdate('w') . ' days');
			break;

			case 14:
				// Find a start point
				if (!$params->get('start_point'))
				{
					$d = new \DateTime('-' . gmdate('w') . ' days');
					$timestamp = $d->format('Y-m-d') . ' 00:00:00';

					$params->set('start_point', $d->format('Y-m-d') . ' 00:00:00');
				}

				$now = Date::toSql();
				if ($now > $params->get('start_point'))
				{
					$d = new \DateTime($params->get('start_point'));
					$d->modify('+2 week');
					$params->set('start_point', $d->format('Y-m-d') . ' 00:00:00');

					$job->set('params', $params->toString());
					$job->store(false);
					$job->set('params', $params);
				}

				$d = new \DateTime($params->get('start_point'));
			break;

			case 21:
				// Find a start point
				if (!$params->get('start_point'))
				{
					$d = new \DateTime('-' . gmdate('w') . ' days');
					$timestamp = $d->format('Y-m-d') . ' 00:00:00';

					$params->set('start_point', $d->format('Y-m-d') . ' 00:00:00');
				}

				$now = Date::toSql();
				if ($now > $params->get('start_point'))
				{
					$d = new \DateTime($params->get('start_point'));
					$d->modify('+3 week');
					$params->set('start_point', $d->format('Y-m-d') . ' 00:00:00');

					$job->set('params', $params->toString());
					$job->store(false);
					$job->set('params', $params);
				}

				$d = new \DateTime($params->get('start_point'));
			break;

			case 30:
				// Once a week, start of the week
				$d = new \DateTime('first day of this month');
			break;
		}

		$timestamp = $d->format('Y-m-d') . ' 00:00:00';

		$database = App::get('db');

		// Get all resources that haven't been ranked
		$sql = "SELECT r.id, r.ranking, r.ranked
				FROM `#__resources` AS r
				WHERE r.standalone=1
				AND r.state=1
				AND (r.ranked = '0000-00-00 00:00:00' OR r.ranked < " . $database->quote($timestamp) . ")
				ORDER BY r.ranked ASC
				LIMIT {$limit}";

		$database->setQuery($sql);
		$queued = $database->loadObjectList();

		// Loop through each resource and rank it
		foreach ($queued as $item)
		{
			if (in_array($item->id, $processed))
			{
				continue;
			}

			//if ($resource->rank())
			//{
				// mark as sent and save
				$resource->ranked = Date::toSql();
				//$resource->store();
			//}

			$processed[] = $item->id;
		}

		return true;
	}
}
