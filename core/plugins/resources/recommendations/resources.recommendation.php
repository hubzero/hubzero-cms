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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resource recommendation database class
 */
class ResourcesRecommendation extends JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__recommendation', 'fromID', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, False if invalid
	 */
	public function check()
	{
		if (!$this->toID)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_ERROR_NO_ID'));
			return false;
		}
		return true;
	}

	/**
	 * Get a list of recommendations
	 *
	 * @param      array $filters Filters to apply to query
	 * @return     array
	 */
	public function getResults($filters=array())
	{
		$query = "SELECT *, (10*titleScore + 5*contentScore+2*tagScore)/(10+5+2) AS rec_score
		FROM #__recommendation AS rec, #__resources AS r
		WHERE (rec.fromID ='".$filters['id']."' AND r.id = rec.toID AND r.standalone=1)
		OR (rec.toID ='".$filters['id']."' AND r.id = rec.fromID AND r.standalone=1 AND r.published=1) having rec_score > ".$filters['threshold']."
		ORDER BY rec_score DESC LIMIT ".$filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

