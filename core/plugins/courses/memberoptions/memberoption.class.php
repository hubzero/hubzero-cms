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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Description for '"COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION"'
 */
define("COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION", "receive-forum-email");

/**
 * Short description for 'courses_MemberOption'
 *
 * Long description (if any) ...
 */
class courses_MemberOption extends JTable
{
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
		parent::__construct( '#__courses_memberoption', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->gidNumber ) == '')
		{
			$this->setError( Lang::txt('Please provide a gidNumber') );
			return false;
		}

		if (trim( $this->userid ) == '')
		{
			$this->setError( Lang::txt('Please provide a userid') );
			return false;
		}

		if (trim( $this->optionname ) == '')
		{
			$this->setError( Lang::txt('Please provide an optionname') );
			return false;
		}

		if (trim( $this->optionvalue ) == '')
		{
			$this->setError( Lang::txt('Please provide an optionvalue') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'loadRecord'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $gidNumber Parameter description (if any) ...
	 * @param      unknown $userid Parameter description (if any) ...
	 * @param      unknown $optionname Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadRecord($gidNumber=NULL, $userid=NULL, $optionname=NULL)
	{
		if (!$gidNumber)
			$gidNumber = $this->gidNumber;

		if (!$userid)
			$usuerid = $this->userid;

		if (!$optionname)
			$optionname = $this->optionname;

		if (!$gidNumber || !$userid || !$optionname)
			return false;

		$sql = "SELECT * FROM $this->_tbl WHERE userid='$userid' AND gidNumber='$gidNumber' and optionname='$optionname'";

		$this->_db->setQuery($sql);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}

