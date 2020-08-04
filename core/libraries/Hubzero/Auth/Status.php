<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
