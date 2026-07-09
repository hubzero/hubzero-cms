<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

/**
 * Helper for serving account flows to XHR/AJAX callers.
 *
 * The account controllers (register, credentials) normally drive a full-page
 * flow of redirects and rendered pages. When a caller adds format=json to the
 * request, they can instead return a small JSON envelope
 * { success, message, redirect } for a front-end to consume without a page
 * reload. This is entirely inert unless format=json is explicitly requested,
 * so default behavior is unchanged.
 */
class Ajax
{
	/**
	 * Did the caller request a JSON response?
	 *
	 * @return  boolean
	 */
	public static function wanted()
	{
		return strtolower(\Request::getWord('format', '')) === 'json';
	}

	/**
	 * Emit a JSON envelope and stop.
	 *
	 * @param   boolean  $success   Whether the action succeeded
	 * @param   string   $message   Human-readable message, or a rendered HTML fragment
	 * @param   string   $redirect  Optional URL the caller should navigate to next
	 * @return  void
	 */
	public static function send($success, $message = '', $redirect = null)
	{
		$payload = array(
			'success' => (bool) $success,
			'message' => $message
		);

		if ($redirect !== null)
		{
			$payload['redirect'] = $redirect;
		}

		if (!headers_sent())
		{
			header('Content-Type: application/json; charset=utf-8');
		}

		echo json_encode($payload);
		exit;
	}
}
