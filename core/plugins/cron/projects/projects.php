<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for projects
 */
class plgCronProjects extends \Hubzero\Plugin\Plugin
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
		$obj->plugin = 'projects';

		$obj->events = array(
			array(
				'name'   => 'computeStats',
				'label'  => Lang::txt('PLG_CRON_PROJECTS_LOG_STATS'),
				'params' => ''
			),
			array(
				'name'   => 'googleSync',
				'label'  => Lang::txt('PLG_CRON_PROJECTS_SYNC_GDRIVE'),
				'params' => ''
			),
			array(
				'name'   => 'gitGc',
				'label'  => Lang::txt('PLG_CRON_PROJECTS_GITGC'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Compute and log overall projects usage stats
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function computeStats(\Components\Cron\Models\Job $job)
	{
		$database   = App::get('db');
		$publishing = Plugin::isEnabled('projects', 'publications') ? 1 : 0;

		require_once Component::path('com_projects') . DS . 'models' . DS . 'project.php';
		require_once Component::path('com_projects') . DS . 'tables' . DS . 'stats.php';

		if ($publishing)
		{
			require_once Component::path('com_publications') . DS . 'tables' . DS . 'publication.php';
			require_once Component::path('com_publications') . DS . 'tables' . DS . 'version.php';
		}

		$tblStats = new \Components\Projects\Tables\Stats($database);
		$model = new \Components\Projects\Models\Project();

		// Compute and store stats
		$stats  = $tblStats->getStats($model, true, $publishing);

		return true;
	}

	/**
	 * Auto sync project repositories connected with GDrive
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function googleSync(\Components\Cron\Models\Job $job)
	{
		$database = App::get('db');

		$pconfig = Component::params('com_projects');

		require_once Component::path('com_projects') . DS . 'tables' . DS . 'project.php';
		require_once Component::path('com_projects') . DS . 'tables' . DS . 'owner.php';
		require_once Component::path('com_projects') . DS . 'helpers' . DS . 'connect.php';
		require_once Component::path('com_projects') . DS . 'helpers' . DS . 'html.php';
		require_once Component::path('com_projects') . DS . 'tables' . DS . 'remotefile.php';
		require_once Component::path('com_projects') . DS . 'helpers' . DS . 'remote' . DS . 'google.php';

		require_once Component::path('com_publications') . DS . 'tables' . DS . 'attachment.php';
		require_once Component::path('com_publications') . DS . 'tables' . DS . 'publication.php';
		require_once Component::path('com_publications') . DS . 'tables' . DS . 'version.php';

		// Get all projects
		$obj = new \Components\Projects\Tables\Project($database);
		$projects = $obj->getRecords(array('active' => true), 'admin', 0, 0, ($pconfig->get('confirm_step', 0) ? 3 : 2)); //$obj->getValidProjects(array(), array(), $pconfig, false, 'alias');

		if (!$projects)
		{
			return true;
		}

		$prefix = $pconfig->get('offroot', 0) ? '' : PATH_CORE;
		$webdir = DS . trim($pconfig->get('webpath'), DS);

		Request::setVar('auto', 1);

		foreach ($projects as $project)
		{
			// Load project
			//$project = $obj->getProject($alias, 0);

			$pparams   = new \Hubzero\Config\Registry($project->params);
			$connected = $pparams->get('google_dir_id');
			$token     = $pparams->get('google_token');

			if (!$connected || !$token)
			{
				continue;
			}

			// Unlock sync
			$obj->saveParam($project->id, 'google_sync_lock', '');

			// Plugin params
			$plugin_params = array(
				$project,
				'com_projects',
				true,
				$project->created_by_user,
				null,
				null,
				'sync',
				array('files')
			);

			$sections = Event::trigger('projects.onProject', $plugin_params);
		}

		return true;

	}

	/**
	 * Optimize project repos
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function gitGc(\Components\Cron\Models\Job $job)
	{
		$database = App::get('db');

		$pconfig = Component::params('com_projects');

		require_once Component::path('com_projects') . DS . 'tables' . DS . 'project.php';
		require_once Component::path('com_projects') . DS . 'helpers' . DS . 'githelper.php';
		require_once Component::path('com_projects') . DS . 'helpers' . DS . 'html.php';

		// Get all projects
		$obj = new \Components\Projects\Tables\Project($database);
		$projects = $obj->getValidProjects(array(), array(), $pconfig, false, 'alias');

		if (!$projects)
		{
			return true;
		}

		foreach ($projects as $project)
		{
			$path = \Components\Projects\Helpers\Html::getProjectRepoPath(strtolower($project), 'files');

			// Make sure there is .git directory
			if (!$path || !is_dir($path . DS . '.git'))
			{
				continue;
			}
			$git = new \Components\Projects\Helpers\Git($path);

			$git->callGit('gc --aggressive');
		}

		return true;
	}
}
