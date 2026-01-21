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
    public const SUCCESS = 1;

    /**
     * Status to indicate cancellation of authentication (unused)
     *
     * @const  STATUS_CANCEL  Cancelled request (unused)
     */
    public const CANCEL = 2;

    /**
     * This is the status code returned when the authentication failed (prevent login if no success)
     *
     * @const  STATUS_FAILURE  Failed request
     */
    public const FAILURE = 4;

    /**
     * This is the status code returned when the account has expired (prevent login)
     *
     * @const  STATUS_EXPIRED  An expired account (will prevent login)
     */
    public const EXPIRED = 8;

    /**
     * This is the status code returned when the account has been denied (prevent login)
     *
     * @const  STATUS_DENIED  Denied request (will prevent login)
     */
    public const DENIED = 16;

    /**
     * This is the status code returned when the account doesn't exist (not an error)
     *
     * @const  STATUS_UNKNOWN  Unknown account (won't permit or prevent login)
     */
    public const UNKNOWN = 32;
}
