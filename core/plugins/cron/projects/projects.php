<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Cron plugin for projects
 */
namespace Plugins\Cron\Projects;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Event;
use Hubzero\Facades\Component;

class Projects extends Plugin
{
    /**
     * Return a list of events
     *
     * @return  array
     */
    public function onCronEvents()
    {
        $this->loadLanguage();

        $obj = new \stdClass();
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
        $publishing = \Hubzero\Facades\Plugin::isEnabled('projects', 'publications') ? 1 : 0;

        if ($publishing) {
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

        // Get all projects
        $obj = new \Components\Projects\Tables\Project($database);
        $projects = $obj->getRecords(
            array('active' => true),
            'admin',
            0,
            0,
            ($pconfig->get('confirm_step', 0) ? 3 : 2)
        );

        if (!$projects) {
            return true;
        }

        $prefix = $pconfig->get('offroot', 0) ? '' : PATH_CORE;
        $webdir = DS . trim($pconfig->get('webpath'), DS);

        Request::setVar('auto', 1);

        foreach ($projects as $project) {
            // Load project
            //$project = $obj->getProject($alias, 0);

            $pparams   = new \Hubzero\Config\Registry($project->params);
            $connected = $pparams->get('google_dir_id');
            $token     = $pparams->get('google_token');

            if (!$connected || !$token) {
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

        // Get all projects
        $obj = new \Components\Projects\Tables\Project($database);
        $projects = $obj->getValidProjects(array(), array(), $pconfig, false, 'alias');

        if (!$projects) {
            return true;
        }

        foreach ($projects as $project) {
            $path = \Components\Projects\Helpers\Html::getProjectRepoPath(strtolower($project), 'files');

            // Make sure there is .git directory
            if (!$path || !is_dir($path . DS . '.git')) {
                continue;
            }
            $git = new \Components\Projects\Helpers\Git($path);

            $git->callGit('gc --aggressive');
        }

        return true;
    }
}
