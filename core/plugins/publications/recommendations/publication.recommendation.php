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

//----------------------------------------------------------
// Recommendation database class
//----------------------------------------------------------

/**
 * Short description for 'PublicationRecommendation'
 *
 * Long description (if any) ...
 */
class PublicationRecommendation extends JTable
{

	/**
	 * Description for 'fromID'
	 *
	 * @var unknown
	 */
	var $fromID       = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'toID'
	 *
	 * @var unknown
	 */
	var $toID         = NULL;  // @var int(11)

	/**
	 * Description for 'contentScore'
	 *
	 * @var unknown
	 */
	var $contentScore = NULL;  // @var float

	/**
	 * Description for 'tagScore'
	 *
	 * @var unknown
	 */
	var $tagScore     = NULL;  // @var float

	/**
	 * Description for 'titleScore'
	 *
	 * @var unknown
	 */
	var $titleScore   = NULL;  // @var float

	/**
	 * Description for 'timestamp'
	 *
	 * @var unknown
	 */
	var $timestamp    = NULL;  // @var datetime (0000-00-00 00:00:00)

	//-----------

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__recommendation', 'fromID', $db );
	}

	/**
	 * Short description for 'getResults'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getResults( $filters=array() )
	{
		$query = "SELECT *, (10*titleScore + 5*contentScore+2*tagScore)/(10+5+2) AS rec_score
		FROM #__recommendation AS rec, #__publications AS P
		WHERE (rec.fromID ='" . $filters['id'] . "' AND P.id = rec.toID)
		OR (rec.toID ='" . $filters['id'] . "' AND P.id = rec.fromID) having rec_score > " . $filters['threshold']."
		ORDER BY rec_score DESC LIMIT " . $filters['limit'];

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
?>