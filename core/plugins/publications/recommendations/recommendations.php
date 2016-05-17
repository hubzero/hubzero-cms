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
 * Publications Plugin class for recommendations
 */
class plgPublicationsRecommendations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $publication  Current publication
	 * @return  array
	 */
	public function &onPublicationSubAreas($publication)
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
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   integer  $miniview     View style
	 * @return  array
	 */
	public function onPublicationSub($publication, $option, $miniview=0)
	{
		$arr = array(
			'html'    =>'',
			'metadata'=>'',
			'name'    =>'recommendations'
		);

		// Check if our area is in the array of areas we want to return results for
		$areas = array('recommendations');
		if (!array_intersect($areas, $this->onPublicationSubAreas($publication))
		&& !array_intersect($areas, array_keys( $this->onPublicationSubAreas($publication))))
		{
			return false;
		}

		// Get some needed libraries
		include_once(__DIR__ . DS . 'models' . DS . 'recommendation.php');

		// Get recommendations
		$r = Plugins\Resources\Recommendations\Models\Recommendation::find(
			$resource->id,
			$this->params->get('threshold', '0.21')
		);

		$results = $r->limit($this->params->get('display_limit', 10))->rows();

		// Pass the view some info
		$view = $this->view('default', 'browse');

		if ($miniview)
		{
			$view->setLayout('mini');
		}

		$view->set('option', $option);
		$view->set('publication', $publication);
		$view->set('results', $results);
		$view->setErrors($this->getErrors());

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
