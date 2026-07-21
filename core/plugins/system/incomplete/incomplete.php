<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking for missing/required registration fields
 */
class plgSystemIncomplete extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return void
	 */
	public function onAfterRoute()
	{
		if (App::isSite() && !User::isGuest())
		{
			// Essentials only. These are the routes that must bypass the
			// incomplete-registration gate; everything else is deliberately
			// NOT exempt so an incomplete user is held at registration
			// site-wide. In particular the old blanket 'com_content.article'
			// exemption is gone — it let incomplete users roam the entire
			// content site. Add a route here only with a specific reason.
			$exceptions = [
				// Log-out routes — an incomplete user must always be able to
				// leave; redirecting these would trap them with no way out.
				'com_login.logout',
				'com_login.logout.login',
				'com_users.logout',
				'com_users.userlogout',
				'com_users.logout.login',
				// The registration-completion form's own submit/password
				// steps — exempting these prevents an infinite redirect of
				// the completion page back onto itself.
				'com_members.save.profiles',
				'com_members.profiles.save',
				'com_members.profiles.save.profiles',
				'com_members.changepassword',
				// Let an incomplete user read the terms they're being asked
				// to accept (specific path only, not all articles) and file a
				// support ticket, e.g. to report a broken login.
				'/legal/terms',
				'com_support.tickets.new.index',
				'com_support.tickets.save.index'
			];

			if ($allowed = $this->params->get('exceptions', ''))
			{
				$allowed = str_replace("\r", '', trim($allowed));
				$allowed = str_replace('\n', "\n", $allowed);
				$allowed = explode("\n", $allowed);
				$allowed = array_map('trim', $allowed);
				$allowed = array_map('strtolower', $allowed);

				$exceptions = array_merge($exceptions, $allowed);
				$exceptions = array_unique($exceptions);
			}

			$current  = Request::getWord('option', '');
			$current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

			// If exception not found, let's try by raw URL path
			if (!in_array($current, $exceptions))
			{
				$current = Request::path();
			}

			if (!in_array($current, $exceptions) && Session::get('registration.incomplete'))
			{
				// Remember what component was originally requested so we can tell,
				// after the branches below, whether one of them rewrote the request
				// to a registration/link target (and guard against redirect loops).
				$originalOption = Request::getWord('option', '');

				// First check if we're heading to the registration pages, and allow that through
				if (Request::getWord('option') == 'com_members' && (Request::getWord('controller') == 'register' || Request::getWord('view') == 'register'))
				{
					// Set linkaccount far to false at this point, otherwise we'd get stuck in a loop
					Session::set('linkaccount', false);
					$this->event->stop();
					return;
				}

				// Tmp users
				//
				// $target mirrors the in-place rewrite as a real URL. It is only
				// used on the front page (see the redirect below); everywhere else
				// the in-place setVar()/event->stop() swap is what takes effect.
				$target = null;

				if (User::get('tmp_user'))
				{
					Request::setVar('option', 'com_members');
					Request::setVar('controller', 'register');
					Request::setVar('task', 'create');
					Request::setVar('act', '');

					$target = 'index.php?option=com_members&controller=register&task=create';

					$this->event->stop();
				}
				else if (substr(User::get('email'), -8) == '@invalid') // force auth_link users to registration update page
				{
					// Send third-party-auth users straight to the registration
					// form to finish creating their hub account. We used to route
					// them to the "have you logged in before?" account-link page
					// first, but that step confused users, and it can't auto-match
					// an ORCID login anyway (ORCID's public API returns no email).
					// Linking an existing account is now an opt-in action in
					// profile settings rather than a forced first-login step.
					Request::setVar('option', 'com_members');
					Request::setVar('controller', 'register');
					Request::setVar('task', 'update');
					Request::setVar('act', '');

					$target = 'index.php?option=com_members&controller=register&task=update';

					$this->event->stop();
				}
				else // otherwise, send to profile to fill in missing info
				{
					// Does the user even have access to the profile plugin?
					// If not, then we can't redirect them there
					$plugin = Plugin::byType('members', 'profile');

					if (!empty($plugin))
					{
						Request::setVar('option', 'com_members');
						Request::setVar('task', 'view');
						Request::setVar('id', User::get('id'));
						Request::setVar('active', 'profile');

						$target = 'index.php?option=com_members&task=view&id=' . User::get('id') . '&active=profile';

						$this->event->stop();
					}
					else
					{
						// Nothing else we can do, so let them go
						// and mark the incompleteness state so we don't
						// keep checking on every page load
						Session::get('registration.incomplete', false);
					}
				}

				// The site's front-page template only renders the component
				// position on non-default menu items, so the in-place swap above
				// produces no visible output on the home page — the user just sees
				// the normal landing page and is never sent to registration. When
				// we're on the default menu item, do a real redirect to the same
				// target instead so we land on a page whose template shows the
				// component. The $originalOption guard keeps this from firing on
				// the redirect target itself (which is com_users/com_members),
				// so it can't loop.
				if ($target && !in_array($originalOption, array('com_users', 'com_members')))
				{
					$menu = App::get('menu');

					if (is_object($menu->getActive()) && is_object($menu->getDefault())
						&& $menu->getActive()->id == $menu->getDefault()->id)
					{
						App::redirect(Route::url($target));
					}
				}
			}
		}
	}
}
