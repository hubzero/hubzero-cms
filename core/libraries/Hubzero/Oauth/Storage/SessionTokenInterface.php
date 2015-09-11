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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth\Storage;

/**
 * Session token storage methods
 */
interface SessionTokenInterface
{
	/**
	 * Get session id from cookie
	 * 
	 * @return  bool  Result of test
	 */
	public function getSessionIdFromCookie();

	/**
	 * Get user id via session id
	 * 
	 * @param   string  $session_id  Session identifier
	 * @return  int     User identifier
	 */
	public function getUserIdFromSessionId($session_id);

	/**
	 * Loads client needed for internal requests
	 * 
	 * @return  mixed
	 */
	public function getInternalRequestClient();

	/**
	 * Create internal client. This avoids the issue if the client wasnt 
	 * created or was accidentally delete resulted in both session 
	 * and tool requests failing
	 * 
	 * @return  void
	 */
	public function createInternalRequestClient();
}