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

namespace Hubzero\Auth;

/**
 * Authentication statuses
 */
class Status
{
	/**
	 * This is the status code returned when the authentication is success (permit login)
	 *
	 * @const  STATUS_SUCCESS  Successful response
	 */
	const SUCCESS = 1;

	/**
	 * Status to indicate cancellation of authentication (unused)
	 *
	 * @const  STATUS_CANCEL  Cancelled request (unused)
	 */
	const CANCEL = 2;

	/**
	 * This is the status code returned when the authentication failed (prevent login if no success)
	 *
	 * @const  STATUS_FAILURE  Failed request
	 */
	const FAILURE = 4;

	/**
	 * This is the status code returned when the account has expired (prevent login)
	 *
	 * @const  STATUS_EXPIRED  An expired account (will prevent login)
	 */
	const EXPIRED = 8;

	/**
	 * This is the status code returned when the account has been denied (prevent login)
	 *
	 * @const  STATUS_DENIED  Denied request (will prevent login)
	 */
	const DENIED = 16;

	/**
	 * This is the status code returned when the account doesn't exist (not an error)
	 *
	 * @const  STATUS_UNKNOWN  Unknown account (won't permit or prevent login)
	 */
	const UNKNOWN = 32;
}