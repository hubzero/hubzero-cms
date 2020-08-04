<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth\Storage;

use OAuth2\RequestInterface;

/**
 * Tool token storage interface
 */
interface ToolSessionTokenInterface
{
	/**
	 * Get tool data from request
	 *
	 * @return  bool  Result of test
	 */
	public function getToolSessionDataFromRequest(RequestInterface $request);

	/**
	 * Validate tool session data
	 *
	 * @param   string  $toolSessionId     Tool session id
	 * @param   string  $toolSessionToken  Tool session token
	 * @return  bool    Result of test
	 */
	public function validateToolSessionData($toolSessionId, $toolSessionToken);

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
