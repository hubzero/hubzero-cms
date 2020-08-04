<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
