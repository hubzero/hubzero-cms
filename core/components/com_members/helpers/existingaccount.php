<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

use Hubzero\Config\Registry;
// The class every client's 'Route' alias names. Imported directly because
// tests may alias the global 'Route' to a stand-in for the rest of a run
// (Hubzero\Htmx\Tests\HtmxTest), which would bypass the application router.
use Hubzero\Facades\Route;
use Component;
use Plugin;

/**
 * Helps a third-party login that has no hub account yet find the account
 * its owner already has
 *
 * Such a login lands on the Update Account form (plg_system_incomplete). When
 * the person already has an account, that form is the wrong place: they
 * should sign in to the account and link the login from Account settings.
 */
class ExistingAccount
{
	/**
	 * Whether the person probably has an account they can link this login to:
	 * a confirmed, unblocked account other than $selfId has the address, and
	 * has no link with this provider yet (Account settings only offers
	 * providers an account has not linked)
	 *
	 * This must reveal nothing the Update Account form does not already
	 * reveal. When duplicate addresses are refused (allow_duplicate_emails 0
	 * or 2), saving that form with the provider's address, which it prefills,
	 * already answers whether an account uses it. When duplicates are allowed
	 * (1), the form does not answer, and not every provider verifies the
	 * address it sends, so there is no match at all.
	 *
	 * @param   string   $email     Address asserted by the identity provider
	 * @param   integer  $selfId    The placeholder account of this session
	 * @param   integer  $domainId  The auth domain of this login
	 * @return  boolean
	 */
	public static function matches($email, $selfId, $domainId)
	{
		$email = trim((string) $email);

		// Placeholder accounts carry "-N@invalid"; providers like ORCID send nothing
		if ($email === '' || substr($email, -8) == '@invalid')
		{
			return false;
		}

		if ((int) Component::params('com_members')->get('allow_duplicate_emails', 0) == 1)
		{
			return false;
		}

		// Confirmed only: an account someone registered with this address
		// without ever confirming it may not be this person's
		$users = \Hubzero\User\User::all()
			->whereEquals('email', $email)
			->where('id', '!=', (int) $selfId)
			->whereEquals('block', 0)
			->where('activation', '>', 0)
			->rows();

		foreach ($users as $user)
		{
			$linked = \Hubzero\Auth\Link::all()
				->whereEquals('user_id', (int) $user->get('id'))
				->whereEquals('auth_domain_id', (int) $domainId)
				->total();

			if (!$linked)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Where "Sign in to my existing account" goes: log out (which deletes the
	 * placeholder account, see plg_user_xusers::onLogoutUser), sign in, then
	 * Account settings, where the login can be linked with one click
	 *
	 * - The sign-in page drops a return that points at the login or
	 *   registration pages (com_users displayTask), so the chain cannot end at
	 *   the provider's login URL; Account settings is the nearest allowed
	 *   destination.
	 * - Account settings is /members/myaccount/account. The members router
	 *   builds task=myaccount&active=account as /members/account/myaccount,
	 *   which parses as the member named "account", so the section is appended.
	 * - Naming the authenticator on logout keeps com_users from diverting a
	 *   session whose authenticator cookie is gone to its single-sign-on
	 *   logout page, which would lose the return.
	 * - Base64 can contain "+", which a query string reads as a space.
	 *
	 * @param   string  $authenticator  Plugin name of this login
	 * @return  string  Not HTML-escaped; escape it when printing
	 */
	public static function signInUrl($authenticator)
	{
		$account = Route::url('index.php?option=com_members&task=myaccount', false);
		$account = (strpos($account, '?') === false)
			? rtrim($account, '/') . '/account'
			: $account . '&active=account';

		$login = Route::url('index.php?option=com_users&view=login&return=' . urlencode(base64_encode($account)), false);

		return Route::url(
			'index.php?option=com_users&view=login&task=logout&authenticator=' . urlencode((string) $authenticator)
			. '&return=' . urlencode(base64_encode($login)),
			false
		);
	}

	/**
	 * The name to show for an authentication plugin, as com_users' link page does
	 *
	 * For Shibboleth this is the institution, not what Account settings lists,
	 * so the messages use it only to say where the login came from.
	 *
	 * @param   string  $authenticator  Plugin name
	 * @return  string
	 */
	public static function providerName($authenticator)
	{
		$authenticator = (string) $authenticator;
		$plugin = Plugin::byType('authentication', $authenticator);

		if ($plugin && !empty($plugin->params))
		{
			$params = new Registry($plugin->params);

			if ($name = $params->get('display_name'))
			{
				return (string) $name;
			}
		}

		$class = 'plgAuthentication' . $authenticator;

		if (!class_exists($class))
		{
			Plugin::import('authentication', $authenticator);
		}

		if (class_exists($class) && method_exists($class, 'onGetLinkDescription'))
		{
			return (string) $class::onGetLinkDescription();
		}

		return ucfirst($authenticator);
	}
}
