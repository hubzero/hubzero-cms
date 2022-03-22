<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Allow saving of hipaa compliance checkbox
 */
class plgProjectsHipaacompliant extends \Hubzero\Plugin\Plugin
{
	/**
	 * Event call to return data for a specific project
	 *
	 * @param   object  $model   Project model
	 * @param   string  $action  Plugin task
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onProject($model, $action = '', $areas = NULL)
	{
		// Check authorization
		if ($model->exists() && !$model->access('member'))
		{
			return;
		}

		if ($action != 'publish')
		{
			return;
		}

		if (strtolower(Request::method()) != 'post')
		{
			return;
		}

		$compliant = Request::getInt('hipaacompliant', 0, 'post');
		$pid       = Request::getInt('pid', 0);
		$version   = Request::getVar('version', 'dev');

		if ($pid && $version)
		{
			// Load publication
			$pub = new \Components\Publications\Models\Publication($pid, $version);

			// Error loading publication record
			if (!$pub->exists())
			{
				return;
			}

			// Is this pub from this project?
			if (!$pub->belongsToProject($model->get('id')))
			{
				return;
			}

			if (!$compliant)
			{
				App::redirect(
					Route::url($pub->link('editbase')),
					Lang::txt('Please select if the uploaded materials contain potentially sensitive data or not.'),
					'warning'
				);
			}

			if ($compliant == 2)
			{
				if (!$pub->group_owner || !in_array($pub->access, array(4, 5)))
				{
					App::redirect(
						Route::url($pub->link('editbase')),
						Lang::txt('If materials are marked as containing potentially sensitive data, the Publication must be private to a group.'),
						'warning'
					);
				}
			}

			$params = new \Hubzero\Config\Registry($pub->version->params);
			$params->set('hipaacompliant', $compliant);

			$pub->version->params = $params->toString();
			$pub->version->store();
		}
	}
}