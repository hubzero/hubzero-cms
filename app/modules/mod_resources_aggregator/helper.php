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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ResourcesAggregator;

use Hubzero\Module\Module;

/**
 * mod_random_billboard
 */
class Helper extends Module
{
	private function getAll()
	{
		$limit = 16;

		// get the most recent resources
		$db = \App::get('db');

		$query = '	SELECT r.id, r.title, r.publish_up, r.created, t.alias  AS tAlias, t.type as tType, p.uidNumber, p.`name`
			FROM #__resources r LEFT JOIN #__resource_types t ON r.type=t.id
			LEFT JOIN `#__xprofiles` p ON p.`uidNumber` = r.`created_by`
			WHERE r.published=1 AND r.standalone=1 AND r.type!=8 AND (r.access=0 OR r.access=3) ORDER BY publish_up DESC, created DESC LIMIT ' . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();
		//print_r($results); die;

		// get the highest rated resources
		$query = '	SELECT r.id, r.title, r.rating, t.alias AS tAlias, t.type as tType, p.uidNumber, p.`name`
			FROM `#__resources` r
			LEFT JOIN #__resource_types t ON r.type=t.id
			LEFT JOIN `#__xprofiles` p ON p.`uidNumber` = r.`created_by`
			WHERE r.rating >= 4.4 AND r.published=1 AND r.standalone=1 AND r.type!=8 AND (r.access=0 OR r.access=3) ORDER BY rating DESC LIMIT ' . ($limit / 2);

		$db->setQuery($query);
		$rated_results = $db->loadObjectList();

		// combine resources

		// keep track of IDs
		$ids = array();

		// combined resources
		$featured = array();

		foreach($rated_results as $res) {
			$featured[] = array('type' => 'rating', 'res' => $res);
			$ids[] = $res->id;
		}

		foreach($results as $res) {
			if(sizeof($featured) == $limit) {
				break;
			}
			if(!in_array($res->id, $ids)) {
				$featured[] = array('type' => 'new', 'res' => $res);
			}
		}
		shuffle($featured);
		return $featured;
	}

	/**
	 * Display method
	 * 
	 * @return void
	 */
	public function display()
	{
		$this->css()
		     ->js();

		\Document::addScript('/app/templates' . DS . \App::get('template')->template . DS . 'js' . DS . 'masonry.pkgd.min.js');
		\Document::addScript('/app/templates' . DS . \App::get('template')->template . DS . 'js' . DS . 'fit.js');

		$this->featured = $this->getAll();

		parent::display();
	}
}
