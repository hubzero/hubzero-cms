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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Auth;

use Hubzero\Base\Object;

/**
 * Authentication response class, provides an object for storing user and error details
 */
class Response extends Object
{
	/**
	 * Response status (see status codes)
	 *
	 * @var  string
	 */
	public $status = Status::FAILURE;

	/**
	 * The type of authentication that was successful
	 *
	 * @var  string
	 */
	public $type = '';

	/**
	 *  The error message
	 *
	 * @var  string
	 */
	public $error_message = '';

	/**
	 * Any UTF-8 string that the End User wants to use as a username.
	 *
	 * @var  string
	 */
	public $username = '';

	/**
	 * Any UTF-8 string that the End User wants to use as a password.
	 *
	 * @var  string
	 */
	public $password = '';

	/**
	 * The email address of the End User as specified in section 3.4.1 of [RFC2822]
	 *
	 * @var  string
	 */
	public $email = '';

	/**
	 * UTF-8 string free text representation of the End User's full name.
	 *
	 * @var  string
	 *
	 */
	public $fullname = '';

	/**
	 * The End User's date of birth as YYYY-MM-DD. Any values whose representation uses
	 * fewer than the specified number of digits should be zero-padded. The length of this
	 * value MUST always be 10. If the End User user does not want to reveal any particular
	 * component of this value, it MUST be set to zero.
	 *
	 * For instance, if a End User wants to specify that his date of birth is in 1980, but
	 * not the month or day, the value returned SHALL be "1980-00-00".
	 *
	 * @var  string
	 */
	public $birthdate = '';

	/**
	 * The End User's gender, "M" for male, "F" for female.
	 *
	 * @var  string
	 */
	public $gender = '';

	/**
	 * UTF-8 string free text that SHOULD conform to the End User's country's postal system.
	 *
	 * @var  string
	 */
	public $postcode = '';

	/**
	 * The End User's country of residence as specified by ISO3166.
	 *
	 * @var  string
	 */
	public $country = '';

	/**
	 * End User's preferred language as specified by ISO639.
	 *
	 * @var  string
	 */
	public $language = '';

	/**
	 * ASCII string from TimeZone database
	 *
	 * @var  string
	 */
	public $timezone = '';

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
	}
}
