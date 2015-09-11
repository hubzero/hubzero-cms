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
 * @author    Steven Snyder <snyder13@purdue.edu>
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Cookie;

/**
 * Authentication Plugin class for Shibboleth/InCommon
 */
class plgAuthenticationShibboleth extends \Hubzero\Plugin\Plugin
{
	/**
	 * Logs data to the shibboleth debug log
	 *
	 * @param   string         $msg   the message to log
	 * @param   string|object  $data  additional data to log
	 * @return  void
	 **/
	private function log($msg, $data='')
	{
		if ($this->params->get('debug_enabled', false))
		{
			if (!\Log::has('shib'))
			{
				$location = $this->params->get('debug_location', '/var/log/apache2/php/shibboleth.log');
				$location = explode(DS, $location);
				$file     = array_pop($location);

				\Log::register('shib', [
					'path'   => implode(DS, $location),
					'file'   => $file,
					'level'  => 'info',
					'format' => "%datetime% %message%\n"
				]);
			}

			// Create a token to identify related log entries
			if (!$cookie = Cookie::eat('shib-dbg-token'))
			{
				$token = base64_encode(uniqid());
				Cookie::bake('shib-dbg-token', time()+60*60*24, ['shib-dbg-token' => $token]);
			}
			else
			{
				$token = $cookie->{'shib-dbg-token'};
			}

			$toBeLogged = "{$token} - {$msg}";

			if (!empty($data))
			{
				$toBeLogged .= ":\t" . (is_string($data) ? $data : json_encode($data));
			}

			\Log::logger('shib')->info("$toBeLogged");
		}
	}

	/**
	 * Looks up a hostname by ip address to see if we can infer and institution
	 *
	 * We use this instead of standard php function gethostbyaddr because we need
	 * the timeout to prevent load issues.
	 *
	 * @param   string        $ip       the ip address to look up
	 * @param   string|array  $dns      the dns server to use
	 * @param   int           $timeout  the timeout after which requests should expire
	 * @return  string
	 **/
	private static function getHostByAddress($ip, $dns, $timeout=2)
	{
		try
		{
			$resolver = new Net_DNS2_Resolver(['nameservers' => (array) $dns, 'timeout' => $timeout]);
			$result   = $resolver->query($ip, 'PTR');
		}
		catch (Net_DNS2_Exception $e)
		{
			return $ip;
		}

		if ($result
		 && isset($result->answer)
		 && count($result->answer) > 0
		 && isset($result->answer[0]->ptrdname))
		{
			return $result->answer[0]->ptrdname;
		}

		return $ip;
	}

	private static function getInstitutions()
	{
		static $inst = NULL;
		if ($inst === NULL)
		{
			$plugin = Plugin::byType('authentication', 'shibboleth');
			$inst = json_decode(json_decode($plugin->params)->institutions, TRUE);
			$inst = $inst['activeIdps'];
		}
		return $inst;
	}

	public static function getInstitutionByEntityId($eid, $key = NULL)
	{
		foreach (self::getInstitutions() as $inst)
		{
			if ($inst['entity_id'] == $eid)
			{
				return $key ? $inst[$key] : $inst;
			}
		}
		return NULL;
	}

	/**
	 * When linking an account, by default a parameter of the plugin is used to
	 * determine the text "link your <something> account", and failing that the
	 * plugin name is used (eg., "link your Shibboleth account".
	 *
	 * Neither is appropriate here because we want to vary the text based on the
	 * ID provider used. I don't think the average user knows what InCommon or
	 * Shibboleth mean in this context.
	 */
	public static function onGetLinkDescription()
	{
		$sess = App::get('session')->get('shibboleth.session', NULL);
		if ($sess && $sess['idp'] && ($rv = self::getInstitutionByEntityId($sess['idp'], 'label')))
		{
			return $rv;
		}
		// probably only possible if the user abruptly deletes their cookies
		return 'InCommon';
	}

	private static function getLoginParams()
	{
		$service = rtrim(Request::base(),'/');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		$com_user = 'com_users';
		$task     = (User::isGuest()) ? 'user.login' : 'user.link';

		return array($service, $com_user, $task);
	}

	/**
	 * Similar justification to that for onGetLinkDescription.
	 *
	 * We want to show a button with the name of the previously-used ID
	 * provider on it instead of something generic like "Shibboleth"
	 */
	public static function onGetSubsequentLoginDescription($return)
	{
		// look up id provider
		if (isset($_COOKIE['shib-entity-id']) && ($idp = self::getInstitutionByEntityId($_COOKIE['shib-entity-id'], 'label')))
		{
			return '<input type="hidden" name="idp" value="'.$idp['id'].'" />Sign in with '.htmlentities($idp);
		}

		// if we couldn't figure out where they want to go to log in, we can't really help, so we redirect them with ?reset to get the full log-in provider list
		list($service, $com_user, $task) = self::getLoginParams();
		App::redirect($service.'/index.php?reset=1&option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
	}

	private static function htmlify()
	{
		return array(
			function($str) { return str_replace('"', '&quot;', $str); },
			function($str) { return htmlentities($str); }
		);
	}

	public static function onRenderOption($return, $title = 'With an affiliated institution:')
	{
		// hide the login box if the plugin is in "debug mode" and the special key is not set in the request
		$params = Plugin::params('authentication', 'shibboleth');
		if (($testKey = $params->get('testkey', NULL)) && !array_key_exists($testKey, $_GET))
		{
			return '<span />';
		}
		// saved id provider? use it as the default
		$prefill = isset($_COOKIE['shib-entity-id']) ? $_COOKIE['shib-entity-id'] : NULL;
		if (!$prefill && // no cookie
				($host = self::getHostByAddress(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'], $params->get('dns', '8.8.8.8'))) && // can get a host
				preg_match('/[.]([^.]*?[.][a-z0-9]+?)$/', $host, $ma))
		{ // hostname lookup seems php jsonrational (not an ip address, has a few dots in it
			// try to look up a provider to pre-select based on the user's hostname
			foreach (self::getInstitutions() as $inst)
			{
				if (fnmatch('*'.$ma[1], $inst['host']))
				{
					$prefill = $inst['entity_id'];
					break;
				}
			}
		}

		// attach style and scripts
		foreach (array('bootstrap.min.js', 'bootstrap-select.min.js', 'shibboleth.js', 'bootstrap.min.css', 'bootstrap-select.min.css', 'bootstrap-theme.min.css', 'shibboleth.css') as $asset)
		{
			$isJs = preg_match('/[.]js$/', $asset);
			if ($isJs)
			{
				\Hubzero\Document\Assets::addPluginScript('authentication', 'shibboleth', $asset);
			}
			else
			{
				\Hubzero\Document\Assets::addPluginStylesheet('authentication', 'shibboleth', $asset);
			}
		}

		list($a, $h) = self::htmlify();

		// make a dropdown/button combo that (hopefully) gets prettied up client-side into a bootstrap dropdown
		$html = array();
		$html[] = '<form class="shibboleth account incommon-color" action="'.Route::url('index.php?option=com_users&view=login').'" method="get">';
		$html[] = '<div class="default-icon"></div>';
		$html[] = '<select title="'.$a($title).'" name="idp">';
		$html[] = '<option class="placeholder">'.$h($title).'</option>';
		foreach (self::getInstitutions() as $idp)
		{
			$logo = (isset($idp['logo_data']) && $idp['logo_data']) ? '<img src="'.$idp['logo_data'].'" />' : '<span class="logo-placeholder"></span>';
			$html[] = '<option '.($prefill == $idp['entity_id'] ? 'selected="selected" ' : '').'value="'.$a($idp['entity_id']).'" data-content="'.$a($logo.' '.$h($idp['label'])).'">'.$h($idp['label']).'</option>';
		}
		$html[] = '</select>';
		$html[] = '<input type="hidden" name="authenticator" value="shibboleth" />';
		if ($return)
		{
			$html[] = '<input type="hidden" name="return" value="'.$a(preg_replace('/^.return=/', '', $return)).'" />';
		}
		$html[] = '<button class="submit" type="submit">Sign in</button>';
		$html[] = '</form>';
		return $html;
	}

	/**
	 * Actions to perform when logging out a user session
	 *
	 * @return     void
	 */
	public function logout()
	{
		list($service) = self::getLoginParams();

		$return = '/';
		if ($return = Request::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);

			if (!JURI::isInternal($return))
			{
				$return = '';
			}

			$return = '/' . ltrim($return, '/');
		}

		App::redirect($return);
	}

	/**
	 * Check login status of current user
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		if (($sess = App::get('session')->get('shibboleth.session', NULL)))
		{
			$this->log('found resumable session:', $sess);
			return array('username' => $sess['username'], 'eppn' => $sess['eppn'], 'idp' => $sess['idp']);
		}
		return array();
	}

	/**
	 * Actions to perform when logging in a user session
	 *
	 * @param      unknown &$credentials Parameter description (if any) ...
	 * @param      array &$options Parameter description (if any) ...
	 * @return     void
	 */
	public function login(&$credentials, &$options)
	{
		if ($return = Request::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);
			if (!JURI::isInternal($return))
			{
				$return = '';
			}
		}
		$options['return'] = $return;

		// extract variables set by mod_shib, if any
		// https://www.incommon.org/federation/attributesummary.html
		if (($sid = isset($_SERVER['REDIRECT_Shib-Session-ID']) ? $_SERVER['REDIRECT_Shib-Session-ID'] : (isset($_SERVER['Shib-Session-ID']) ? $_SERVER['Shib-Session-ID'] : NULL)))
		{
			$attrs = array(
				'id' => $sid,
				'idp' => $_COOKIE['shib-entity-id']
			);
			foreach (array('email', 'eppn', 'displayName', 'givenName', 'sn', 'email') as $key)
			{
				if (isset($_SERVER[$key]))
				{
					$attrs[$key] = $_SERVER[$key];
				}
				elseif (isset($_SERVER['REDIRECT_'.$key]))
				{
					$attrs[$key] = $_SERVER['REDIRECT_'.$key];
				}
			}
			// normalize things a bit
			if (!isset($attrs['username']) && isset($attrs['eppn']))
			{
				$attrs['username'] = preg_replace('/@.*$/', '', $attrs['eppn']);
			}
			// eppn is sometimes or maybe always in practice an email address
			if (!isset($attrs['email']) && isset($attrs['eppn']) && strpos($attrs['eppn'], '@'))
			{
				$attrs['email'] = $attrs['eppn'];
			}
			if (!isset($attrs['displayName']) && isset($attrs['givenName']) && $attrs['sn'])
			{
				$attrs['displayName'] = $attrs['givenName'].' '.$attrs['sn'];
			}
			$options['shibboleth'] = $attrs;
			$this->log('login from:', $_SERVER);
			$this->log('session attributes: ', $attrs);
			App::get('session')->set('shibboleth.session', $attrs);
		}
	}

	/**
	 * Method to display login prompt
	 *
	 * @access	public
	 * @param   object	$view	view object
	 * @param 	object	$tpl	template object
	 * @return	void
	 */
	public function display($view, $tpl)
	{
		list($service, $com_user, $task) = self::getLoginParams();
		$return = $view->return ? '&return='.$view->return : '';

		// discovery service for mod_shib to feed back the appropriate id provider
		// entityID. see below for more info
		if (array_key_exists('wayf', $_GET))
		{
			if (isset($_GET['return']) && isset($_COOKIE['shib-entity-id']))
			{
				$this->log('wayf passthru', $_COOKIE['shib-entity-id']);
				App::redirect($_GET['return'].'&entityID='.$_COOKIE['shib-entity-id']);
			}
			$this->log('failed wayf');
			// invalid request, back to the login page with you
			App::redirect($service.'/index.php?option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
		}

		// invalid idp in request, send back to login landing
		$eid = isset($_GET['idp']) ? $_GET['idp'] : (isset($_COOKIE['shib-entity-id']) ? $_COOKIE['shib-entity-id'] : NULL);
		if (!isset($eid) || !self::getInstitutionByEntityId($eid))
		{
			$this->log('failed to look up entity id', $eid);
			App::redirect($service.'/index.php?option='.$com_user.'&task=login'.$return);
		}

		// we're about to do at least a few redirects, some of which are out of our
		// control, so save a bit of state for when we get back
		//
		// we don't use the session store because we'd like it to outlive the
		// session so we can suggest this idp next time
		setcookie('shib-entity-id', $_GET['idp'], time()+60*60*24, '/');
		// send the request to mod_shib.
		//
		// this path should be set up in your configuration something like this:
		//
		// <Location /login/shibboleth>
		// 	AuthType shibboleth
		// 	ShibRequestSetting requireSession 1
		// 	Require valid-user
		// 	RewriteRule (.*) /index.php?option=com_users&authenticator=shibboleth&task=user.login [L]
		// </Location>
		//
		// mod_shib protects the path, and in doing so it looks at your SessionInitiators.
		// in shibobleth2.xml. ithis is what we use:
		//
		// <SessionInitiator type="Chaining" Location="/login/shibboleth" isDefault="true" id="Login">
		// 	<SessionInitiator type="SAML2" template="bindingTemplate.html"/>
		// 	<SessionInitiator type="Shib1"/>
		// 	<SessionInitiator type="SAMLDS" URL="https://dev06.hubzero.org/login?authenticator=shibboleth&amp;wayf"/>
		// </SessionInitiator>
		//
		// the important part here is the SAMLDS line pointing mod_shib right back
		// here, but with &wayf in the query string. we look for that a little bit
		// above here and feed the appropriate entity-id back to mod_shib with
		// another redirect. I wouldn't be at all surprised if there is a cleaner
		// way to communicate this that avoids the network hop. pull request, pls
		//
		// (if you are only using one ID provider you can avoid configuring
		// SessionInitiators at all and just define that service like:
		//
		//	<SSO entityID="https://idp.testshib.org/idp/shibboleth">
		// 	SAML2 SAML1
		// </SSO>
		//
		// in which case mod_shib will not need to do discovery, having only one
		// option.
		//
		// either way, the rewrite directs us back here to our login() method
		// where we can extract info about the authn from mod_shib
		$this->log('passing throuugh to shibd');
		App::redirect($service.'/login/shibboleth');
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 * @since 1.5
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		// eppn is eduPersonPrincipalName and is the absolute lowest common
		// denominator for InCommon attribute exchanges. we can't really do
		// anything without it
		if (isset($options['shibboleth']['eppn']))
		{
			$this->log('auth with', $options['shibboleth']);
			$method = (\Component::params('com_users')->get('allowUserRegistration', FALSE)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'shibboleth', $options['shibboleth']['idp'], $options['shibboleth']['eppn']);

			if ($hzal === FALSE)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = 'Unknown user and new user registration is not permitted.';
				return;
			}

			$hzal->email = isset($options['shibboleth']['email']) ? $options['shibboleth']['email'] : NULL;

			$this->log('hzal', $hzal);
			$response->auth_link = $hzal;
			$response->type = 'shibboleth';
			$response->status = \Hubzero\Auth\Status::SUCCESS;
			$response->fullname = isset($options['shibboleth']['displayName']) ? ucwords(strtolower($options['shibboleth']['displayName'])) : $options['shibboleth']['username'];

			if (!empty($hzal->user_id))
			{
				$user = User::getInstance($hzal->user_id); // Bring this in line with the rest of the system

				$response->username = $user->username;
				$response->email    = $user->email;
				$response->fullname = $user->name;
			}
			else
			{
				$response->username = '-' . $hzal->id; // The Open Group Base Specifications Issue 6, Section 3.426
				$response->email    = $response->username . '@invalid'; // RFC2606, section 2

				// Also set a suggested username for their hub account
				App::get('session')->set('auth_link.tmp_username', $options['shibboleth']['username']);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => \Hubzero\User\Profile::getInstance($user->get('id'))->getPicture(0, false),
					'authenticator' => 'shibboleth'
				);
				$this->log('auth cookie', $prefs);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = 'An error occurred verifying your credentials.';
		}
	}

	/**
	 * @access	public
	 * @param   array - $options
	 * @return	void
	 */
	public function link($options = array())
	{
		if (($status = $this->status()))
		{
			$this->log('link', $status);
			// Get unique username
			$username = $status['eppn'];
			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'shibboleth', $status['idp']);

			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				$this->log('already linked', array('domain' => $hzad->id, 'username' => $username));
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					'This account appears to already be linked to a hub account',
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'shibboleth', $status['idp'], $username);
				$hzal->user_id = User::get('id');
				$this->log('setting link', $hzal);
				$hzal->update();
			}
		}
		else
		{
			// User somehow got redirect back without being authenticated (not sure how this would happen?)
			App::redirect(Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'), 'There was an error linking your account, please try again later.', 'error');
		}
	}
}
