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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for adding Dublin Core metadata to the document
 */
class plgPublicationsDublincore extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$view = $this->view();

		$publication->authors();
		$publication->license();

		// Add metadata
		Document::setMetaData('dc.title', $view->escape($publication->title));

		$nullDate = '0000-00-00 00:00:00';

		if ($publication->publish_up && $publication->publish_up != $nullDate)
		{
			Document::setMetaData('dc.date', Date::of($publication->publish_up)->toLocal('Y-m-d'));
		}
		if ($publication->submitted && $publication->submitted != $nullDate)
		{
			Document::setMetaData('dc.date.submitted', Date::of($publication->submitted)->toLocal('Y-m-d'));
		}
		if ($publication->accepted && $publication->accepted != $nullDate)
		{
			Document::setMetaData('dc.date.approved', Date::of($publication->accepted)->toLocal('Y-m-d'));
		}

		if ($doi = $publication->version->get('doi'))
		{
			Document::setMetaData('dc.identifier', $view->escape($doi));
		}

		Document::setMetaData('dcterms.description', $view->escape($publication->abstract));

		$license = $publication->license();
		if (is_object($license))
		{
			Document::setMetaData('dcterms.license', $view->escape($license->title));
		}

		foreach ($publication->_authors as $contributor)
		{
			if (strtolower($contributor->role) == 'submitter')
			{
				continue;
			}

			if ($contributor->name)
			{
				$name = stripslashes($contributor->name);
			}
			else
			{
				$name = stripslashes($contributor->p_name);
			}

			if (!$contributor->organization)
			{
				$contributor->organization = $contributor->p_organization;
			}
			$contributor->organization = stripslashes(trim($contributor->organization));

			Document::setMetaData('dcterms.creator', $view->escape($name . ($contributor->organization ? ', ' . $contributor->organization : '')));
		}
	}
}
