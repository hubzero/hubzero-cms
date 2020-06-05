<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for publications
 */
class plgCronPublications extends \Hubzero\Plugin\Plugin
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
		$obj->plugin = 'publications';

		$obj->events = array(
			array(
				'name'   => 'sendAuthorStats',
				'label'  => Lang::txt('PLG_CRON_PUBLICATIONS_SEND_STATS'),
				'params' => 'authorstats'
			),
			array(
				'name'   => 'rollUserStats',
				'label'  => Lang::txt('PLG_CRON_PUBLICATIONS_ROLL_STATS'),
				'params' => ''
			),
			array(
				'name'   => 'runMkAip',
				'label'  => Lang::txt('PLG_CRON_PUBLICATIONS_ARCHIVE'),
				'params' => ''
			),
			array(
				'name'   => 'issueMasterDoi',
				'label'  => Lang::txt('PLG_CRON_PUBLICATIONS_MASTER_DOI'),
				'params' => ''
			),
			array(
				'name'   => 'updateFtpLinks',
				'label'  => Lang::txt('PLG_CRON_PUBLICATIONS_UPDATE_FTP_LINKS'),
				'params' => 'ftplinkdates'
			)
		);
		return $obj;
	}

	/**
	 * Send emails to authors with the monthly stats
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function sendAuthorStats(\Components\Cron\Models\Job $job)
	{
		$database = App::get('db');

		$pconfig = Component::params('com_publications');

		// Get some params
		$limit = $pconfig->get('limitStats', 5);
		$image = $pconfig->get('email_image', '');

		Lang::load('com_publications', PATH_APP) ||
		Lang::load('com_publications', Component::path('com_publications') . '/site');

		// Is logging enabled?
		if (is_file(Component::path('com_publications') . '/tables/logs.php'))
		{
			require_once Component::path('com_publications') . '/tables/logs.php';
		}
		else
		{
			$this->setError('Publication logs not present on this hub, cannot email stats to authors');
			return false;
		}

		// Helpers
		require_once Component::path('com_publications') . '/helpers/html.php';

		// Get all registered authors who subscribed to email
		$query  = "SELECT A.user_id ";
		$query .= " FROM `#__publication_authors` as A ";
		$query .= " JOIN `#__users` as P ON A.user_id = P.id ";
		$query .= " WHERE P.block=0 AND P.activation > 0 AND P.sendEmail=1"; // either 1 (set to YES), 0 = NO, or -1 (never set)
		$query .= " AND A.user_id > 0 AND A.status=1 ";

		// If we need to restrict to selected authors
		$params = $job->params;
		if (is_object($params) && $params->get('userids'))
		{
			$apu = explode(',', $params->get('userids'));
			$apu = array_map('trim', $apu);

			if (count($apu) > 0)
			{
				foreach ($apu as $i => $a)
				{
					$apu[$i] = $database->quote($a);
				}

				$query .= " AND A.user_id IN (" . implode(',', $apu) . ") ";
			}
		}

		$query .= " GROUP BY A.user_id ";

		$database->setQuery($query);
		if (!($authors = $database->loadObjectList()))
		{
			return true;
		}

		// Set email config
		$from = array(
			'name'      => Config::get('fromname') . ' ' . Lang::txt('Publications'),
			'email'     => Config::get('mailfrom'),
			'multipart' => md5(date('U'))
		);

		$subject = Lang::txt('Monthly Publication Usage Report');

		$i = 0;
		$mailed = array();
		foreach ($authors as $author)
		{
			// Get the user's account
			$user = User::getInstance($author->user_id);
			if (!$user->get('id'))
			{
				// Skip if not registered
				continue;
			}

			if (in_array($user->get('email'), $mailed))
			{
				// Already did this one
				continue;
			}

			// Get pub stats for each author
			$pubLog = new \Components\Publications\Tables\Log($database);
			$pubstats = $pubLog->getAuthorStats($author->user_id);

			if (!$pubstats || !count($pubstats))
			{
				// Nothing to send
				continue;
			}

			// Plain text
			$eview = new Hubzero\Mail\View(
				array(
					'base_path' => __DIR__,
					'name'      => 'emails',
					'layout'    => 'stats_plain'
				)
			);
			$eview->option     = 'com_publications';
			$eview->controller = 'publications';
			$eview->user       = $user;
			$eview->pubstats   = $pubstats;
			$eview->limit      = $limit;
			$eview->image      = $image;
			$eview->config     = $pconfig;
			$eview->totals     = $pubLog->getTotals($author->user_id, 'author');

			$plain = $eview->loadTemplate();
			$plain = str_replace("\n", "\r\n", $plain);

			// HTML
			$eview->setLayout('stats_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			// Build message
			$message = new \Hubzero\Mail\Message();
			$message->setSubject($subject)
			        ->addFrom($from['email'], $from['name'])
			        ->addTo($user->get('email'), $user->get('name'))
			        ->addHeader('X-Component', 'com_publications')
			        ->addHeader('X-Component-Object', 'publications');

			$message->addPart($plain, 'text/plain');
			$message->addPart($html, 'text/html');

			// Send mail
			if (!$message->send())
			{
				$this->setError(Lang::txt('PLG_CRON_PUBLICATIONS_ERROR_FAILED_TO_MAIL', $user->get('email')));
			}

			$mailed[] = $user->get('email');
		}

		return true;
	}

	/**
	 * Compute unique user stats from text logs
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function rollUserStats(\Components\Cron\Models\Job $job)
	{
		$database = \App::get('db');
		$pconfig  = Component::params('com_publications');

		$numMonths = 1;
		$includeCurrent = false;

		require_once Component::path('com_publications') . '/tables/publication.php';
		require_once Component::path('com_publications') . '/tables/version.php';
		require_once Component::path('com_publications') . '/models/log.php';

		// Get log model
		$modelLog = new \Components\Publications\Models\Log();

		$filters = array();
		$filters['sortby'] = 'date';
		$filters['limit']  = 0;
		$filters['start']  = 0;

		// Instantiate a publication object
		$rr = new \Components\Publications\Tables\Publication($database);

		// Get publications
		$pubs = $rr->getRecords($filters);

		// Compute and store stats for each publication
		foreach ($pubs as $publication)
		{
			$stats = $modelLog->digestLogs($publication->id, 'all', $numMonths, $includeCurrent);
		}

		return true;
	}

	/**
	 * Archive publications beyond grace period
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function runMkAip(\Components\Cron\Models\Job $job)
	{
		$database = \App::get('db');
		$config = Component::params('com_publications');

		require_once Component::path('com_publications') . '/helpers/utilities.php';
		require_once Component::path('com_publications') . '/tables/version.php';
		require_once Component::path('com_projects') . '/helpers/html.php';

		// Check that mkAIP script exists
		if (!\Components\Publications\Helpers\Utilities::archiveOn())
		{
			return;
		}
		// Check for grace period
		$gracePeriod = $config->get('graceperiod', 0);
		if (!$gracePeriod)
		{
			// If no grace period, this cron is unnecessary (archived as approval)
			return;
		}

		$aipBasePath = trim($config->get('aip_path', null), '/');
		$aipBasePath = $aipBasePath && is_dir('/' . $aipBasePath) ? '/' . $aipBasePath : null;

		// Check for base path
		if (!$aipBasePath)
		{
			$this->setError('Missing archival base directory');
			return;
		}

		// Get all unarchived publication versions
		$query  = "SELECT V.*, C.id as id, V.id as version_id ";
		$query .= " FROM `#__publication_versions` as V, `#__publications` as C ";
		$query .= " WHERE C.id=V.publication_id AND V.state=1 ";
		$query .= " AND V.doi IS NOT NULL ";
		$query .= " AND V.accepted IS NOT NULL AND V.accepted !='0000-00-00 00:00:00' ";
		$query .= " AND (V.archived IS NULL OR V.archived ='0000-00-00 00:00:00') ";
		$database->setQuery( $query );
		if (!($rows = $database->loadObjectList()))
		{
			return true;
		}

		// Start email message
		$subject  = Lang::txt('Update on recently archived publications');
		$body     = Lang::txt('The following publications passed the grace period and were archived:') . "\n";
		$aipGroup = $config->get('aip_group');
		$counter  = 0;

		foreach ($rows as $row)
		{
			// Grace period unexpired?
			$monthFrom = Date::of($row->accepted . '+1 month')->toSql();
			if (strtotime($monthFrom) > strtotime(Date::of('now')))
			{
				continue;
			}

			// Load version
			$pv = new \Components\Publications\Tables\Version($database);
			if (!$pv->load($row->version_id))
			{
				continue;
			}

			// Create aip path
			$doiParts = explode('/', $row->doi);
			$aipName = count($doiParts) > 1 ? $doiParts[0] . '__' . $doiParts[1] : '';

			// Archival package exists?
			if ($aipBasePath && $aipName && is_dir($aipBasePath . DS . $aipName))
			{
				// Save approved date and archive date
				$pv->archived = $pv->accepted;
				$pv->store();

				// Do not overwrite existing archives !!
				continue;
			}

			// Run mkAIP and save archived date
			if (\Components\Publications\Helpers\Utilities::mkAip($row))
			{
				$pv->archived = Date::toSql();
				$pv->store();
				$counter++;
				$body .= $row->title . ' v.' . $row->version_label . ' (id #' . $row->id . ')' . "\n";
			}
		}

		// Email update to admins
		if ($counter > 0 && $aipGroup)
		{
			// Set email config
			$from = array(
				'name'      => Config::get('fromname') . ' ' . Lang::txt('Publications'),
				'email'     => Config::get('mailfrom'),
				'multipart' => md5(date('U'))
			);

			$admins = \Components\Projects\Helpers\Html::getGroupMembers($aipGroup);

			// Build message
			if (!empty($admins))
			{
				foreach ($admins as $admin)
				{
					// Get the user's account
					$user = User::getInstance($admin);
					if (!$user->get('id'))
					{
						continue;
					}
					$message = new \Hubzero\Mail\Message();
					$message->setSubject($subject)
					        ->addFrom($from['email'], $from['name'])
					        ->addTo($user->get('email'), $user->get('name'))
					        ->addHeader('X-Component', 'com_publications')
					        ->addHeader('X-Component-Object', 'publications');

					$message->addPart($body, 'text/plain');
					$message->send();
				}
			}
		}

		// All done
		return true;
	}

	/**
	 * Issue master DOI for publications if does not exist
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function issueMasterDoi(\Components\Cron\Models\Job $job)
	{
		$database = \App::get('db');
		$config   = Component::params('com_publications');

		// Is config to issue master DOI turned ON?
		if (!$config->get('master_doi'))
		{
			return true;
		}

		// Get all publications without master DOI
		$sql  = "SELECT V.* FROM `#__publication_versions` as V, `#__publications` as C";
		$sql .= " WHERE C.id=V.publication_id AND (C.master_doi IS NULL OR master_doi=0)";
		$sql .= " AND V.state=1 GROUP BY C.id ORDER BY V.version_number ASC";

		$database->setQuery( $sql );
		if (!($rows = $database->loadObjectList()))
		{
			// No applicable results
			return true;
		}

		include_once Component::path('com_publications') . '/models/publication.php';

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
			// Load publication model
			$model = new \Components\Publications\Models\Publication($row->publication_id, $row->id);

			// Check to make sure we got result
			if (!$model->exists())
			{
				continue;
			}

			// Map publication
			$doiService->mapPublication($model);

			// Build url
			$pointer = $model->publication->alias ? $model->publication->alias : $model->publication->id;
			$url = $doiService->_configs->livesite . '/publications/' . $pointer . '/main';

			// Master DOI should link to /main
			$doiService->set('url', $url);

			// Register DOI
			$masterDoi = $doiService->register();

			// Save with publication record
			if ($masterDoi)
			{
				$model->publication->master_doi = strtoupper($masterDoi);
				$model->publication->store();
			}
		}

		return true;
	}

	/**
	 * Create/Remove ftp links based on embargo date range
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function updateFtpLinks(\Components\Cron\Models\Job $job)
	{
		include_once Component::path('com_publications') . '/models/orm/version.php';
		include_once Component::path('com_publications') . '/models/publication.php';

		$params = $job->params;
		$yesterday = !empty($job->params['startdate']) ? $job->params['startdate'] : Date::of()->subtract('1 day')->format('Y-m-d 00:00:00');
		$today = Date::of()->format('Y-m-d 00:00:00');

		$versions = \Components\Publications\Models\Orm\Version::all()
			->select('#__publication_versions.*')
			->select('t.alias', 'master_alias')
			->join('#__publications p', '#__publication_versions.publication_id', 'p.id')
			->join('#__publication_master_types t', 'p.master_type', 't.id')
			->whereEquals('#__publication_versions.state', 1)
			->where('#__publication_versions.published_up', '>=', $yesterday, 'AND', 1)
			->where('#__publication_versions.published_up', '<=', $today, 'AND', 1)
			->resetDepth()
			->where('#__publication_versions.published_down', '>=', $yesterday, 'OR', 1)
			->where('#__publication_versions.published_down', '<=', $today, 'AND', 1)
			->rows();
		$publicationParams = Component::params('com_publications');
		$typeBlackList = array();
		$typeBlackList = explode(',', $publicationParams->get('sftptypeblacklist'));
		$typeBlackList = array_filter($typeBlackList, 'trim');
		foreach ($versions as $version)
		{
			$publication = new \Components\Publications\Models\Publication($version->publication_id, $version->version_number);
			$publication->setCuration();
			if ($version->isPublished() && !in_array($version->master_alias, $typeBlackList))
			{
				$publication->_curationModel->createSymLink();
			}
			else
			{
				$publication->_curationModel->removeSymLink();
			}
		}
	}
}
