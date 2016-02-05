<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

		require_once(PATH_CORE . DS . 'components'. DS . 'com_projects' . DS . 'models' . DS . 'project.php');
		require_once(PATH_CORE . DS . 'components'. DS . 'com_projects' . DS . 'tables' . DS . 'stats.php');

		if ($publishing)
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
			require_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'version.php');
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

		require_once(PATH_CORE . DS . 'components'. DS . 'com_projects' . DS . 'tables' . DS . 'project.php');
		require_once(PATH_CORE . DS . 'components'. DS . 'com_projects' . DS . 'tables' . DS . 'owner.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'connect.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php');

		require_once(PATH_CORE . DS . 'components'. DS . 'com_projects' . DS . 'tables' . DS . 'remotefile.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'remote' . DS . 'google.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'attachment.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'version.php');

		// Get all projects
		$obj = new \Components\Projects\Tables\Project($database);
		$projects = $obj->getValidProjects(array(), array(), $pconfig, false, 'alias');

		if (!$projects)
		{
			return true;
		}

		$prefix = $pconfig->get('offroot', 0) ? '' : PATH_CORE;
		$webdir = DS . trim($pconfig->get('webpath'), DS);

		Request::setVar('auto', 1);

		foreach ($projects as $alias)
		{
			// Load project
			$project = $obj->getProject($alias, 0);

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
				NULL,
				NULL,
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

		require_once(PATH_CORE . DS . 'components' . DS .'com_projects' . DS . 'tables' . DS . 'project.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'html.php');

		// Get all projects
		$obj = new \Components\Projects\Tables\Project($database);
		$projects = $obj->getValidProjects(array(), array(), $pconfig, false, 'alias' );

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

