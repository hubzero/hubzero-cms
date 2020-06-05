<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
			),
			array(
				'name'   => 'auditResourceData',
				'label'  => Lang::txt('PLG_CRON_RESOURCES_AUDIT'),
				'params' => 'audit'
			),
			array(
				'name'   => 'emailMemberResources',
				'label'  => Lang::txt('PLG_CRON_RESOURCES_EMAIL_MEMBER_DIGEST'),
				'params' => 'digest'
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
				FROM `#__resources` AS r, `#__tool_version` AS v
				WHERE r.published=1
				AND r.type=7
				AND r.standalone=1
				AND r.alias=v.toolname
				AND v.state=1
				AND (r.master_doi IS NULL OR r.master_doi=0)
				GROUP BY r.id
				ORDER BY v.title, v.toolname, v.revision DESC";

		$database->setQuery($sql);

		if (!($rows = $database->loadObjectList()))
		{
			// No applicable results
			return true;
		}

		// Includes
		require_once Component::path('com_publications') . '/models/doi.php';

		// Get DOI service
		$doiService = new \Components\Publications\Models\Doi();

		// Is service enabled?
		if (!$doiService->on() || !$doiService->_configs->livesite)
		{
			return true;
		}

		require_once Component::path('com_resources') . '/models/entry.php';

		// Go through records
		foreach ($rows as $row)
		{
			// Reset metadata
			$doiService->reset();

			// Map data
			$pubYear = $row->released && $row->released != '0000-00-00 00:00:00'
					? gmdate('Y', strtotime($row->released))
					: gmdate('Y');
			$doiService->set('pubYear', $pubYear);
			$doiService->mapUser($row->created_by, array(), 'creator');
			$doiService->set('resourceType', 'Software');
			$doiService->set('title', htmlspecialchars(stripslashes($row->title)));
			$doiService->set('url', $doiService->_configs->livesite . '/resources/' . $row->toolname . '/main');

			// Register DOI
			$masterDoi = $doiService->register();

			// Save with publication record
			$resource = \Components\Resources\Models\Entry::oneOrNew($row->id);
			if ($masterDoi && $resource->get('id'))
			{
				$resource->set('master_doi', strtoupper($masterDoi));
				$resource->save();
			}
		}

		return true;
	}

	/**
	 * Set rankings for resources that haven't been ranked yet
	 *
	 * NOTE: This method is currently unused and unfinished!
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function updateResourceRanking(\Components\Cron\Models\Job $job)
	{
		$processed = array();

		$params = $job->params;

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
				AND (r.ranked IS NULL OR r.ranked = '0000-00-00 00:00:00' OR r.ranked < " . $database->quote($timestamp) . ")
				ORDER BY r.ranked ASC
				LIMIT {$limit}";

		$database->setQuery($sql);
		$queued = $database->loadObjectList();

		require_once Component::path('com_resources') . '/models/entry.php';

		// Loop through each resource and rank it
		foreach ($queued as $item)
		{
			if (in_array($item->id, $processed))
			{
				continue;
			}

			$resource = \Components\Resources\Models\Entry::oneOrNew($item->id);

			if (!$resource->get('id'))
			{
				continue;
			}

			if ($resource->rank())
			{
				// mark as sent and save
				$resource->set('ranked', Date::toSql());
				$resource->save();
			}

			$processed[] = $item->id;
		}

		return true;
	}

	/**
	 * Audit resource data
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function auditResourceData(\Components\Cron\Models\Job $job)
	{
		$params = $job->params;

		// Determine parameters
		$limit    = intval($params->get('audit_limit', 500));
		$interval = strtoupper($params->get('audit_frequency', '1 MONTH'));
		$now      = Date::toSql();

		// Get records that haven't been processed
		$db = App::get('db');
		$db->setQuery(
			"SELECT r.*
			FROM `#__resources` AS r
			WHERE r.id NOT IN (
				SELECT a.scope_id
				FROM `#__audit_results` AS a
				WHERE a.scope=" . $db->quote('resource') . "
			)
			LIMIT 0," . $limit
		);
		$data = $db->loadAssocList();

		$unprocessed = count($data);

		// If we didn't reach the limit
		// Get records that need to be re-processed
		if ($unprocessed < $limit)
		{
			$nlimit = ($limit - $unprocessed);
			$nlimit = ($nlimit > 0 ? $nlimit : 1);

			$db->setQuery(
				"SELECT r.*
				FROM `#__resources` AS r
				WHERE r.id IN (
					SELECT a.scope_id
					FROM `#__audit_results` AS a
					WHERE a.scope=" . $db->quote('resource') . "
					AND DATE_ADD(a.processed, INTERVAL " . $interval . ") < " . $db->quote($now) . "
				)
				LIMIT 0," . $nlimit
			);
			if ($data2 = $db->loadAssocList())
			{
				$data = $data + $data2;
			}
		}

		// Process records
		if (count($data) > 0)
		{
			include_once Component::path('com_resources') . '/helpers/tests/links.php';

			$auditor = new \Hubzero\Content\Auditor('resource');
			$auditor->registerTest(new \Components\Resources\Helpers\Tests\Links);

			$results = $auditor->check($data);

			// Loop through each record
			foreach ($results as $reportcard)
			{
				// Loop through each test result and save to the database
				foreach ($reportcard['tests'] as $result)
				{
					$prev = Hubzero\Content\Auditor\Result::oneByScope($result->get('scope'), $result->get('scope_id'));

					if ($prev->get('id'))
					{
						$result->set('id', $prev->get('id'));
					}

					$result->save();
				}
			}
		}

		return true;
	}

	/**
	 * Email member activity digest
	 *
	 * Current limitations include a lack of queuing/scheduling. This means that this cron job
	 * should not be set to run more than once daily, otherwise it will continue to send out the
	 * same digest to people over and over again.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function emailMemberResources(\Components\Cron\Models\Job $job)
	{
		$params = $job->params;

		// Determine parameters
		$batch    = intval($params->get('digest_batch', 500));
		$interval = strtoupper($params->get('digest_frequency', '1 month'));

		$now      = Date::of('now')->toSql();
		$previous = Date::of('now')->subtract($interval)->toSql();

		// Get records that haven't been processed
		$db = App::get('db');

		$query = "SELECT DISTINCT(u.id)
				FROM `#__users` AS u
				LEFT JOIN `#__activity_logs` AS l ON l.scope_id=u.id
				AND l.`scope`=" . $db->quote('member.resources') . "
				AND l.`action`=" . $db->quote('emailed') . "
				WHERE u.`block`=0
				AND u.`activation` > 0
				AND u.`approved` > 0
				AND u.`sendEmail`=1
				AND (l.`created` IS NULL OR l.`created` = '0000-00-00 00:00:00' OR l.`created` <= " . $db->quote($previous) . ")
				LIMIT 0," . $batch;
		$db->setQuery($query);
		$users = $db->loadColumn();

		// Loop through members and get their groups that have the digest set
		if ($users && count($users) > 0)
		{
			// Load language files
			Lang::load('plg_cron_resources') ||
			Lang::load('plg_cron_resources', __DIR__);

			require_once Component::path('com_resources') . '/models/entry.php';

			$limit = intval($params->get('digest_limit', 3));

			foreach ($users as $user)
			{
				$query = Components\Resources\Models\Entry::all();

				$r = $query->getTableName();

				$query
					->deselect()
					->select('DISTINCT ' . $r . '.*')
					->whereEquals($r . '.published', Components\Resources\Models\Entry::STATE_PUBLISHED)
					->whereIn($r . '.access', array(0, 1))
					->whereEquals($r . '.standalone', 1);

				$query->whereEquals($r . '.publish_up', '0000-00-00 00:00:00', 1)
					->orWhere($r . '.publish_up', 'IS', null, 1)
					->orWhere($r . '.publish_up', '<=', Date::toSql(), 1)
					->resetDepth();

				$query->whereEquals($r . '.publish_down', '0000-00-00 00:00:00', 1)
					->orWhere($r . '.publish_down', 'IS', null, 1)
					->orWhere($r . '.publish_down', '>=', Date::toSql(), 1)
					->resetDepth();

				$tags = Components\Tags\Models\Objct::all()
					->whereEquals('objectid', $user)
					->whereEquals('tbl', 'xprofiles')
					->rows()
					->fieldsByKey('tagid');

				if (!empty($tags))
				{
					$subquery = "
						(
							(
								SELECT COUNT(DISTINCT tj.tagid)
								FROM `#__tags_object` AS tj
								LEFT JOIN `#__tags` AS t ON t.id=tj.tagid
								WHERE tj.objectid=" . $r . ".id
								AND tj.tbl='resources'
								AND t.id IN (" . implode(',', $tags) . ")
							)
							*
							((90 / ((datediff(NOW(), " . $r . ".created) * 1) + 90)) + 0)
						)
						+
						(LN(LN(1 / (case when datediff(NOW(), " . $r . ".created) >= 1 then datediff(NOW(), " . $r . ".created) else 1 end) + 1)) + 10)
					";

					$query->select('(' . $subquery . ')', 'tag_weight');
					$query->order('tag_weight', 'desc');
				}
				$query->order($r . '.created', 'desc');
				$query->start(0);
				$query->limit($limit);

				$posts = $query->rows();

				// Gather up applicable posts and queue up the emails
				if (count($posts) > 0)
				{
					if ($this->sendEmail($user, $posts, $params))
					{
						// Log activity
						Event::trigger('system.logActivity', [
							'activity' => [
								'action'      => 'emailed',
								'scope'       => 'member.resources',
								'scope_id'    => $user,
								'description' => Lang::txt('PLG_CRON_RESOURCES_EMAILED_DIGEST'),
								'details'     => array(
								)
							],
							'recipients' => [
								//$user
							]
						]);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Handles the actual sending of emails
	 *
	 * @param   int     $user      the user id to send to
	 * @param   array   $posts     the posts to include in the email
	 * @param   object  $params    the cron job params
	 * @return  bool
	 **/
	private function sendEmail($user, $posts, $params)
	{
		$user = User::oneOrNew($user);

		if (!$user->get('id'))
		{
			$this->setError('PLG_CRON_RESOURCES_USER_NOT_FOUND', $user->get('id'));
			return false;
		}

		$eview = new Hubzero\Mail\View(array(
			'base_path' => __DIR__,
			'name'      => 'emails',
			'layout'    => 'digest_plain'
		));
		$eview->member = $user;
		$eview->rows   = $posts;

		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('digest_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build message
		$message = App::get('mailer');
		$message->setSubject($params->get('digest_subject', Lang::txt('PLG_CRON_RESOURCES_EMAIL_DIGEST_SUBJECT')))
				->addFrom(Config::get('mailfrom'), Config::get('sitename'))
				->addTo($user->get('email'), $user->get('name'))
				->addHeader('X-Component', 'com_resources')
				->addHeader('X-Component-Object', 'members_activity_email_digest');

		$message->addPart($plain, 'text/plain');
		$message->addPart($html, 'text/html');

		// Send mail
		if (!$message->send($this->params->get('email_transport_mechanism')))
		{
			$this->setError(Lang::txt('PLG_CRON_RESOURCES_EMAIL_FAILED', $user->get('email')));
			return false;
		}

		return true;
	}
}
