<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for publications
 */
class plgCronPublications extends JPlugin
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
				'label'  => JText::_('PLG_CRON_PUBLICATIONS_SEND_STATS'),
				'params' => 'authorstats'
			),
			array(
				'name'   => 'rollUserStats',
				'label'  => JText::_('PLG_CRON_PUBLICATIONS_ROLL_STATS'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Send emails to authors with the monthly stats
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function sendAuthorStats(CronModelJob $job)
	{
		$database = JFactory::getDBO();
		$juri = JURI::getInstance();

		$jconfig = JFactory::getConfig();
		$pconfig = JComponentHelper::getParams('com_publications');

		// Get some params
		$limit = $pconfig->get('limitStats', 5);
		$image = $pconfig->get('email_image', '');

		$lang = JFactory::getLanguage();
		$lang->load('com_publications', JPATH_BASE);

		// Is logging enabled?
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS .'com_publications' . DS . 'tables' . DS . 'logs.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS .'com_publications' . DS . 'tables' . DS . 'logs.php');
		}
		else
		{
			$this->setError('Publication logs not present on this hub, cannot email stats to authors');
			return false;
		}

		// Helpers
		require_once(JPATH_ROOT . DS . 'components'. DS .'com_members' . DS . 'helpers' . DS . 'imghandler.php');
		require_once(JPATH_ROOT . DS . 'components'. DS .'com_publications' . DS . 'helpers' . DS . 'helper.php');

		// Get publication helper
		$helper = new PublicationHelper($database);

		// Get all registered authors who subscribed to email
		$query  = "SELECT A.user_id, P.picture ";
		$query .= " FROM #__publication_authors as A ";
		$query .= " JOIN #__xprofiles as P ON A.user_id = P.uidNumber ";
		$query .= " WHERE P.mailPreferenceOption != 0 "; // either 1 (set to YES) or -1 (never set)
		$query .= " AND A.user_id > 0 AND A.status=1 ";

		// If we need to restrict to selected authors
		$params = $job->get('params');
		if (is_object($params) && $params->get('userids'))
		{
			$apu    = explode(',', $params->get('userids'));
			$apu    = array_map('trim',$apu);
			$query .= " AND A.user_id IN (";

			$tquery = '';
			foreach ($apu as $a)
			{
				$tquery .= "'".$a."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery . ") ";
		}

		$query .= " GROUP BY A.user_id ";

		$database->setQuery( $query );
		if (!($authors = $database->loadObjectList()))
		{
			return true;
		}

		// Set email config
		$from = array(
			'name'      => $jconfig->getValue('config.fromname') . ' ' . JText::_('Publications'),
			'email'     => $jconfig->getValue('config.mailfrom'),
			'multipart' => md5(date('U'))
		);

		$subject = JText::_('Monthly Publication Usage Report');

		$i = 0;
		foreach ($authors as $author)
		{
			// Get the user's account
			$juser = JUser::getInstance($author->user_id);
			if (!$juser->get('id'))
			{
				// Skip if not registered
				continue;
			}

			// Get pub stats for each author
			$pubLog = new PublicationLog($database);
			$pubstats = $pubLog->getAuthorStats($author->user_id);

			if (!$pubstats || !count($pubstats))
			{
				// Nothing to send
				continue;
			}

			// Plain text
			$eview = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'cron',
					'element' =>'publications',
					'name'    =>'emails',
					'layout'  =>'stats_plain'
				)
			);
			$eview->option     = 'com_publications';
			$eview->controller = 'publications';
			$eview->juser      = $juser;
			$eview->pubstats   = $pubstats;
			$eview->limit      = $limit;
			$eview->image      = $image;
			$eview->helper     = $helper;
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
			        ->addTo($juser->get('email'), $juser->get('name'))
			        ->addHeader('X-Component', 'com_publications')
			        ->addHeader('X-Component-Object', 'publications');

			$message->addPart($plain, 'text/plain');
			$message->addPart($html, 'text/html');

			// Send mail
			if (!$message->send())
			{
				$this->setError(JText::sprintf('PLG_CRON_PUBLICATIONS_ERROR_FAILED_TO_MAIL', $juser->get('email')));
			}
			$mailed[] = $juser->get('email');
		}

		return true;
	}

	/**
	 * Compute unique user stats from text logs
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function rollUserStats(CronModelJob $job)
	{
		$database = JFactory::getDBO();
		$pconfig = JComponentHelper::getParams('com_publications');

		$numMonths = 1;
		$includeCurrent = false;

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'helper.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS .'com_publications' . DS . 'tables' . DS . 'publication.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS .'com_publications' . DS . 'tables' . DS . 'version.php');
		require_once(JPATH_ROOT . DS . 'components'. DS .'com_publications' . DS . 'models' . DS . 'log.php');

		// Get log model
		$modelLog = new PublicationsModelLog();

		$filters = array();
		$filters['sortby'] = 'date';
		$filters['limit']  = 0;
		$filters['start']  = 0;

		// Instantiate a publication object
		$rr = new Publication($database);

		// Get publications
		$pubs = $rr->getRecords($filters);

		// Compute and store stats for each publication
		foreach ($pubs as $publication)
		{
			$stats = $modelLog->digestLogs($publication->id, 'all', $numMonths, $includeCurrent);
		}

		return true;
	}
}
