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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
