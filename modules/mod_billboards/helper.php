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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Mod_Billboards helper class, used to query for billboards and contains the display method
 */
class modBillboards extends \Hubzero\Module\Module
{
	/**
	 * Get the list of billboads in the selected collection
	 *
	 * @return retrieved rows
	 */
	private function _getList()
	{
		$db = JFactory::getDBO();

		// Get the correct billboards collection to display from the parameters
		$collection = (int) $this->params->get('collection', 1);

		// Query to grab all the buildboards associated with the selected collection
		// Make sure we only grab published billboards
		$query = 'SELECT b.*, c.*' .
			' FROM #__billboards as b, #__billboard_collection as c' .
			' WHERE c.id = b.collection_id' .
			' AND published = 1' .
			' AND b.collection_id = ' . $collection .
			' ORDER BY `ordering` ASC';
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Display method
	 * Used to add CSS for each slide as well as the javascript file(s) and the parameterized function
	 *
	 * @return void
	 */
	public function display()
	{
		$jdocument = JFactory::getDocument();

		// Check if we have multiple instances of the module running
		// If so, we only want to push the CSS and JS to the template once
		if (!$this->multiple_instances)
		{
			// Push some CSS to the template
			$this->css();
			$this->js();
		}

		// Get the billboard slides
		$this->slides = $this->_getList();

		// Get some parameters
		$transition       = $this->params->get('transition', 'scrollHorz');
		$random           = $this->params->get('random', 0);
		$timeout          = $this->params->get('timeout', 5) * 1000;
		$speed            = $this->params->get('speed', 1) * 1000;
		$this->collection = $this->params->get('collection', 1);
		$this->pager      = $this->params->get('pager', 'pager');

		// Get the billboard background location from the billboards parameters
		$params = JComponentHelper::getParams('com_billboards');
		$image_location = $params->get('image_location', '/site/media/images/billboards/');

		// Add the CSS to the template for each billboard
		foreach ($this->slides as $slide)
		{
			$background = (!empty($slide->background_img)) ? "background: url('$image_location$slide->background_img') no-repeat 0 0;" : '';
			$padding    = (!empty($slide->padding)) ? "padding: $slide->padding;" : '';

			$css =
				"#$slide->alias {
					$background
					}
				#$slide->alias p {
					$padding
					}";
			$this->css($css);
			$this->css($slide->css);
		}

		// Add the CSS to give the pager a unique ID per billboard collection
		// We need this to manage multiple buildboard pagers potentially moving at different speeds
		// @TODO: there should be a better way of doing this
		if ($this->pager != 'null')
		{
			$js_pager    = "'#$this->pager$this->collection'";
			$this->pager = $this->pager . $this->collection;
			$pager =
				".slider #$this->pager a.activeSlide {
					opacity:1.0;
					}";
			$this->css($pager);
		}
		else
		{
			$js_pager = $this->pager;
		}

		// Add the javascript ready function with variables based on this specific billboard
		// Pause: true - means the billbaord stops scrolling on hover
		if (!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$js = '
				var $jQ = jQuery.noConflict();

				$jQ(document).ready(function() {
					$jQ(\'#' . $this->collection . '\').cycle({
						fx: "' . $transition . '",
						timeout: ' . $timeout .',
						pager: ' . $js_pager . ',
						speed: ' . $speed . ',
						random: ' . $random . ',
						cleartypeNoBg: true,
						slideResize: 0,
						pause: true
					});
				});';
		}
		else
		{
			$js = '
				if (!HUB) {
					var HUB = {};
				}

				if (!jq) {
					var jq = $;
				}

				HUB.Billboards = {
					jQuery: jq,

					initialize: function() {
						var $ = this.jQuery;

						$(\'#' . $this->collection . '\').cycle({
							fx: "' . $transition . '",
							timeout: ' . $timeout .',
							pager: ' . $js_pager . ',
							speed: ' . $speed . ',
							random: ' . $random . ',
							cleartypeNoBg: true,
							slideResize: 0,
							pause: true
						});
					}
				}

				jQuery(document).ready(function($){
					HUB.Billboards.initialize();
				});';
		}

		$this->js($js);
	}
}
