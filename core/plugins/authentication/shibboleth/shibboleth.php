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
	private static function log($msg, $data='')
	{
		static $params;

		if (!isset($params))
		{
			$params = Plugin::params('authentication', 'shibboleth');
		}

		if ($params->get('debug_enabled', false))
		{
			if (!\Log::has('shib'))
			{
				$location = $params->get('debug_location', '/var/log/apache2/php/shibboleth.log');
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
	 * Gets the link domain name
	 *
	 * @param   int  $adid  The auth domain ID
	 * @return  string
	 **/
	public static function getLinkIndicator($adid)
	{
		\Hubzero\Document\Assets::addPluginStylesheet('authentication', 'shibboleth', 'shibboleth.css');
		$dbh = App::get('db');
		$dbh->setQuery('SELECT domain FROM `#__auth_domain` WHERE id = '.(int)$adid);

		// oops ... hopefully not reachable
		if (!($idp = $dbh->loadResult()) || !($label = self::getInstitutionByEntityId($idp, 'label')))
		{
			return 'InCommon';
		}

		return $label;
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
				self::log('getInstitutionByEntityId', array('eid' => $eid, 'key' => $key, 'rv' => $key ? $inst[$key] : $inst));
				return $key ? $inst[$key] : $inst;
			}
		}
		self::log('getInstitutionByEntityId', array('eid' => $eid, 'key' => $key, 'rv' => NULL));
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

		self::log('getLoginParams', array($service, $com_user, $task));
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
		if (isset($_COOKIE['shib-entity-id']) && ($idp = self::getInstitutionByEntityId($_COOKIE['shib-entity-id'])))
		{
			self::log('found eid context', $idp);
			return '<input type="hidden" name="idp" value="'.$idp['entity_id'].'" />Sign in with '.htmlentities($idp['label']);
		}

		// if we couldn't figure out where they want to go to log in, we can't really help, so we redirect them with ?reset to get the full log-in provider list
		list($service, $com_user, $task) = self::getLoginParams();
		self::log('no eid context, redirect', $service.'/index.php?reset=1&option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
		App::redirect($service.'/index.php?reset=1&option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
	}

	private static function htmlify()
	{
		return array(
			function($str) { return str_replace('"', '&quot;', $str); },
			function($str) { return htmlentities($str); }
		);
	}

	public static function onRenderOption($return = NULL, $title = 'With an affiliated institution:')
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
		foreach (array('bootstrap-select.min.js', 'shibboleth.js', 'bootstrap-select.min.css', 'bootstrap-theme.min.css', 'shibboleth.css') as $asset)
		{
			$mtd = 'addPlugin'.(preg_match('/[.]js$/', $asset) ? 'script': 'stylesheet');
			\Hubzero\Document\Assets::$mtd('authentication', 'shibboleth', $asset);
		}

		list($a, $h) = self::htmlify();

		// make a dropdown/button combo that (hopefully) gets prettied up client-side into a bootstrap dropdown
		$html = ['<div class="shibboleth account incommon-color" data-placeholder="'.$a($title).'">'];
		$html[] = '<h3>Select an affiliated institution</h3>';
		$html[] = '<ol>';
		$html = array_merge($html, array_map(function($idp) use($h, $a) {
			return '<li data-entityid="'.$a($idp['entity_id']).'" data-content="'.(isset($idp['logo_data']) ? $a($idp['logo_data']) : '').' '.$h($idp['label']).'"><a href="'.Route::url('index.php?option=com_users&view=login&authenticator=shibboleth&idp='.$a($idp['entity_id'])).'">'.$h($idp['label']).'</a></li>';
		}, self::getInstitutions()));
		$html[] = '</ol></div>';
		return $html;
	}

	/**
	 * Check login status
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		self::log('status');
		$sess = null;
		if (($key = trim(isset($_COOKIE['shib-session']) ? $_COOKIE['shib-session'] : (isset($_GET['shib-session']) ? $_GET['shib-session'] : NULL))))
		{
			self::log('status', $key);
			$dbh = App::get('db');
			$dbh->setQuery('SELECT data FROM `#__shibboleth_sessions` WHERE session_key = '.$dbh->quote($key));
			$dbh->execute();
			$sess = $dbh->loadResult();
		}

		if ($sess)
		{
			$sess = json_decode($sess, TRUE);
			self::log('found resumable session:', $sess);
			return $sess;
		}
		self::log('no shib session', $_GET);
		self::log('no shib session', $_COOKIE);
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
			if (!\Hubzero\Utility\Uri::isInternal($return))
			{
				$return = '';
			}
		}
		$options['return'] = $return;

		// If someone is logged in already, then we're linking an account
		if (!User::get('guest'))
		{
			self::log('already logged in, redirect for link');
			list($service, $com_user, $task) = self::getLoginParams();
			App::redirect($service . '/index.php?option=' . $com_user . '&task=' . $task . '&authenticator=shibboleth&shib-session=' . urlencode($_COOKIE['shib-session']));
		}

		// extract variables set by mod_shib, if any
		// https://www.incommon.org/federation/attributesummary.html
		if (($sid = isset($_SERVER['REDIRECT_Shib-Session-ID']) ? $_SERVER['REDIRECT_Shib-Session-ID'] : (isset($_SERVER['Shib-Session-ID']) ? $_SERVER['Shib-Session-ID'] : NULL)))
		{
			$attrs = array(
				'id' => $sid,
				'idp' => isset($_SERVER['REDIRECT_Shib-Identity-Provider']) ? $_SERVER['REDIRECT_Shib-Identity-Provider'] : $_SERVER['Shib-Identity-Provider']
			);
			foreach (array('email', 'eppn', 'displayName', 'givenName', 'sn', 'mail') as $key)
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
			if (isset($attrs['mail']) && strpos($attrs['mail'], '@'))
			{
				$attrs['email'] = $attrs['mail'];
				unset($attrs['mail']);
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
			self::log('session attributes: ', $attrs);
			self::log('cookie', $_COOKIE);
			self::log('server attributes: ', $_SERVER);
			//JFactory::getSession()->set('shibboleth.session', $attrs);
			$key = trim(base64_encode(openssl_random_pseudo_bytes(128)));
			setcookie('shib-session', $key);
			$dbh = App::get('db');
			$dbh->setQuery('INSERT INTO #__shibboleth_sessions(session_key, data) VALUES('.$dbh->quote($key).', '.$dbh->quote(json_encode($attrs)).')');
			$dbh->execute();
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
				self::log('wayf passthru', $_COOKIE['shib-entity-id']);
				App::redirect($_GET['return'].'&entityID='.$_COOKIE['shib-entity-id']);
			}
			self::log('failed wayf', $service.'/index.php?option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
			// invalid request, back to the login page with you
			App::redirect($service.'/index.php?option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
		}

		// invalid idp in request, send back to login landing
		$eid = isset($_GET['idp']) ? $_GET['idp'] : (isset($_COOKIE['shib-entity-id']) ? $_COOKIE['shib-entity-id'] : NULL);
		if (!isset($eid) || !self::getInstitutionByEntityId($eid))
		{
			self::log('failed to look up entity id, redirect', array('eid' => $eid, 'url' => $service.'/index.php?option='.$com_user.'&task=login'.$return));
			App::redirect($service.'/index.php?option='.$com_user.'&task=login'.$return);
		}

		// we're about to do at least a few redirects, some of which are out of our
		// control, so save a bit of state for when we get back
		//
		// we don't use the session store because we'd like it to outlive the
		// session so we can suggest this idp next time
		if (isset($_GET['idp']))
		{
			setcookie('shib-entity-id', $_GET['idp'], time()+60*60*24, '/');
		}
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
		self::log('passing throuugh to shibd');
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
			self::log('auth with', $options['shibboleth']);
			$method = (\Component::params('com_users')->get('allowUserRegistration', FALSE)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'shibboleth', $options['shibboleth']['idp'], $options['shibboleth']['eppn']);

			if ($hzal === FALSE)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = 'Unknown user and new user registration is not permitted.';
				return;
			}

			$hzal->email = isset($options['shibboleth']['email']) ? $options['shibboleth']['email'] : NULL;

			self::log('hzal', $hzal);
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
					'user_img'      => $user->picture(0, false),
					'authenticator' => 'shibboleth'
				);
				self::log('auth cookie', $prefs);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			self::log('missing eppn in options, return failure', $options);
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
		self::log('do link', $options);
		if (($status = $this->status()))
		{
			self::log('link', $status);
			// Get unique username
			$username = $status['eppn'];
			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'shibboleth', $status['idp']);

			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				self::log('already linked', array('domain' => $hzad->id, 'username' => $username));
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
				$hzal->email   = $status['email'];
				self::log('setting link', $hzal);
				$hzal->update();
			}
		}
		else
		{
			self::log('link failed, bad status, redir');
			// User somehow got redirect back without being authenticated (not sure how this would happen?)
			App::redirect(Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'), 'There was an error linking your account, please try again later.', 'error');
		}
	}
}
