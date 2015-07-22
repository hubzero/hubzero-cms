<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for recommendations
 */
class plgPublicationsRecommendations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $publication 	Current publication
	 * @return     array
	 */
	public function &onPublicationSubAreas( $publication )
	{
		$areas = array();
		if ($publication->category()->_params->get('plg_recommendations', 1) == 1)
		{
			$areas = array(
				'recommendations' => Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS')
			);
		}
		return $areas;
	}

	/**
	 * Return data on a publication sub view (this will be some form of HTML)
	 *
	 * @param      object  $publication 	Current publication
	 * @param      string  $option    		Name of the component
	 * @param      integer $miniview  		View style
	 * @return     array
	 */
	public function onPublicationSub( $publication, $option, $miniview=0 )
	{
		$arr = array(
			'html'    =>'',
			'metadata'=>'',
			'name'    =>'recommendations'
		);

		// Check if our area is in the array of areas we want to return results for
		$areas = array('recommendations');
		if (!array_intersect( $areas, $this->onPublicationSubAreas( $publication ) )
		&& !array_intersect( $areas, array_keys( $this->onPublicationSubAreas( $publication ) ) ))
		{
			return false;
		}

		// Get some needed libraries
		include_once(PATH_CORE . DS . 'plugins' . DS . 'publications'
			. DS . 'recommendations' . DS . 'publication.recommendation.php');

		// Set some filters for returning results
		$filters = array(
			'id'        => $publication->get('id'),
			'threshold' => $this->params->get('threshold', '0.21'),
			'limit'     => $this->params->get('display_limit', 10)
		);

		// Get recommendations
		$database = App::get('db');
		$r = new PublicationRecommendation($database);
		$results = $r->getResults($filters);

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => 'browse'
		));

		// Instantiate a view
		if ($miniview)
		{
			$view->setLayout('mini');
		}

		// Pass the view some info
		$view->option      = $option;
		$view->publication = $publication;
		$view->results     = $results;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
