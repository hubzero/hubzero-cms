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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for resources
 */
class plgCronResources extends JPlugin
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
				'name'   => 'issueMasterDoi',
				'label'  => JText::_('PLG_CRON_RESOURCES_MASTER_DOI'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Issue master DOI for tool resources if does not exist
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function issueMasterDoi(CronModelJob $job)
	{
		$database = JFactory::getDBO();
		$config   = JComponentHelper::getParams('com_publications');
		$jconfig  = JFactory::getConfig();
		$juri     = JURI::getInstance();

		// Is config to issue master DOI turned ON?
		if (!$config->get('master_doi'))
		{
			return true;
		}

		// Get all tool resources without master DOI
		$sql  = "SELECT r.id, r.created_by, v.id as tool_version_id,
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

		// Get site url
		$livesite = $jconfig->getValue('config.live_site')
			? $jconfig->getValue('config.live_site')
			: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);
		if (!$livesite)
		{
			return true;
		}

		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_publications' . DS . 'helpers' . DS . 'utilities.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'author.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');

		// Go through records
		foreach ($rows as $row)
		{
			// Get authors
			$objA = new ToolAuthor($database);
			$authors = $objA->getAuthorsDOI($row->id);

			$pubDate = $row->released && $row->released != '0000-00-00 00:00:00'
						? date( 'Y', strtotime($row->released)) : date( 'Y' );

			// Collect metadata
			$metadata = array(
				'title'        => htmlspecialchars(stripslashes($row->title)),
				'pubYear'      => $pubDate,
				'publisher'    => $config->get('doi_publisher'),
				'resourceType' => 'Software',
				'url'          => $livesite . DS . 'resources'
								. DS . $row->toolname . DS . 'main'
			);

			// Register DOI with data from version being published
			$masterDoi = PublicationUtilities::registerDoi(
				$row,
				$authors,
				$config,
				$metadata,
				$doierr,
				1
			);

			// Save with publication record
			$resource = new ResourcesResource($database);
			if ($masterDoi && $resource->load($row->id))
			{
				$resource->master_doi = $masterDoi;
				$resource->store();
			}
		}

		return true;
	}
}
